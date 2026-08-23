'use strict';

const state = { books: [], users: [], loans: [], categories: [], authors: [], activity: [], activeView: 'dashboard', modal: null };
const palette = ['#786af0', '#42bc94', '#eead55', '#638bd9', '#da7182', '#8b74b9'];
const viewTitles = { dashboard: 'Dashboard', libros: 'Catálogo de libros', usuarios: 'Lectores', prestamos: 'Préstamos', categorias: 'Categorías y autores', reportes: 'Reportes', bitacora: 'Bitácora' };
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => Array.from(document.querySelectorAll(selector));

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
}

function normalize(value) {
  return String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

function formatDate(value) {
  if (!value) return '—';
  const date = new Date(String(value).replace(' ', 'T'));
  return Number.isNaN(date.getTime()) ? value : new Intl.DateTimeFormat('es-EC', { day: '2-digit', month: 'short', year: 'numeric' }).format(date);
}

function effectiveStatus(loan) {
  if (loan.estado === 'DEVUELTO') return 'DEVUELTO';
  const deadline = String(loan.fecha_devolucion_programada || '').slice(0, 10);
  const today = new Date().toLocaleDateString('en-CA');
  return loan.estado === 'ATRASADO' || (deadline && deadline < today) ? 'ATRASADO' : 'ACTIVO';
}

async function request(endpoint, options = {}) {
  const response = await fetch(`api/${endpoint}`, options);
  const text = await response.text();
  let payload;
  try { payload = JSON.parse(text); } catch { throw new Error('El servidor no devolvió JSON válido. Revisa PHP y la conexión de la base de datos.'); }
  if (!response.ok || payload.success === false) throw new Error(payload.message || `Error HTTP ${response.status}`);
  return payload;
}

async function submit(endpoint, values) {
  const body = new FormData();
  Object.entries(values).forEach(([key, value]) => body.append(key, value ?? ''));
  return request(endpoint, { method: 'POST', body });
}

function toast(message, error = false) {
  const item = document.createElement('div');
  item.className = `toast${error ? ' error' : ''}`;
  item.textContent = message;
  $('#toast-container').appendChild(item);
  window.setTimeout(() => item.remove(), 4000);
}

function empty(title, subtitle = 'No hay información disponible todavía.') {
  return `<div class="empty-state"><strong>${escapeHtml(title)}</strong><span>${escapeHtml(subtitle)}</span></div>`;
}

async function loadData(showMessage = false) {
  const notice = $('#connection-notice');
  try {
    const results = await Promise.allSettled([
      request('get_libros.php'), request('get_usuarios.php'), request('get_prestamos.php'),
      request('get_categorias.php'), request('get_autores.php'), request('get_bitacora.php')
    ]);
    const keys = ['books', 'users', 'loans', 'categories', 'authors', 'activity'];
    results.forEach((result, index) => { if (result.status === 'fulfilled') state[keys[index]] = result.value.data || []; });
    const failures = results.filter((result) => result.status === 'rejected');
    if (failures.length === results.length) throw failures[0].reason;
    if (failures.length) {
      notice.textContent = `Algunos módulos no pudieron cargarse: ${failures.map((item) => item.reason.message).join(' · ')}`;
      notice.classList.remove('hidden');
    } else notice.classList.add('hidden');
    renderAll();
    if (showMessage) toast('Información actualizada correctamente.');
  } catch (error) {
    notice.innerHTML = `<strong>No se pudo conectar con la base de datos.</strong> ${escapeHtml(error.message)} Verifica <strong>config/database.php</strong>, importa el SQL y ejecuta el proyecto con un servidor PHP.`;
    notice.classList.remove('hidden');
    renderAll();
    if (showMessage) toast(error.message, true);
  }
}

function showView(view) {
  const selected = viewTitles[view] ? view : 'dashboard';
  state.activeView = selected;
  $$('.view').forEach((item) => item.classList.toggle('active', item.id === `view-${selected}`));
  $$('.nav-link').forEach((item) => item.classList.toggle('active', item.dataset.view === selected));
  $('#page-title').textContent = viewTitles[selected];
  $('#sidebar').classList.remove('open');
}

function metrics() {
  const active = state.loans.filter((loan) => effectiveStatus(loan) === 'ACTIVO');
  const overdue = state.loans.filter((loan) => effectiveStatus(loan) === 'ATRASADO');
  const returned = state.loans.filter((loan) => effectiveStatus(loan) === 'DEVUELTO');
  const available = state.books.reduce((total, book) => total + Math.max(0, Number(book.cantidad_disponible || 0)), 0);
  const totalCopies = state.books.reduce((total, book) => total + Math.max(Number(book.cantidad_total || 0), Number(book.cantidad_disponible || 0)), 0);
  return { active, overdue, returned, available, totalCopies, lent: Math.max(totalCopies - available, active.length + overdue.length) };
}

function statCard(title, value, note, icon, tone) {
  return `<article class="stat-card"><div class="stat-top"><span>${escapeHtml(title)}</span><span class="stat-icon tone-${tone}">${icon}</span></div><strong class="stat-value">${escapeHtml(value)}</strong><small class="stat-note">${escapeHtml(note)}</small></article>`;
}

function renderStats() {
  const data = metrics();
  $('#dashboard-stats').innerHTML = [
    statCard('Títulos registrados', state.books.length, `${data.totalCopies} ejemplares en inventario`, '▤', 'purple'),
    statCard('Ejemplares disponibles', data.available, 'Listos para un nuevo lector', '✓', 'green'),
    statCard('Lectores registrados', state.users.length, 'Comunidad de la biblioteca', '♙', 'blue'),
    statCard('Préstamos pendientes', data.active.length + data.overdue.length, `${data.overdue.length} con devolución atrasada`, '⇄', data.overdue.length ? 'red' : 'amber')
  ].join('');
  $('#report-stats').innerHTML = [
    statCard('Total de préstamos', state.loans.length, 'Movimientos históricos', '⇄', 'purple'),
    statCard('Devoluciones realizadas', data.returned.length, 'Préstamos completados', '✓', 'green'),
    statCard('Préstamos atrasados', data.overdue.length, 'Requieren seguimiento', '!', data.overdue.length ? 'red' : 'amber'),
    statCard('Disponibilidad', data.totalCopies ? `${Math.round(data.available / data.totalCopies * 100)}%` : '0%', `${data.available} de ${data.totalCopies} ejemplares`, '◷', 'blue')
  ].join('');
  $('#nav-loans').textContent = data.active.length + data.overdue.length;
  $('#loan-summary').innerHTML = `<div class="summary-card"><strong>${data.active.length}</strong><span>Préstamos activos</span></div><div class="summary-card"><strong>${data.overdue.length}</strong><span>Devoluciones atrasadas</span></div><div class="summary-card"><strong>${data.returned.length}</strong><span>Préstamos devueltos</span></div>`;
}

function renderBarChart(target, includeReturns = false) {
  const months = [];
  for (let offset = 5; offset >= 0; offset -= 1) {
    const date = new Date();
    date.setDate(1); date.setMonth(date.getMonth() - offset);
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    months.push({ key, label: new Intl.DateTimeFormat('es', { month: 'short' }).format(date).replace('.', ''), loans: 0, returns: 0 });
  }
  state.loans.forEach((loan) => {
    const loanMonth = months.find((month) => String(loan.fecha_prestamo || '').startsWith(month.key));
    const returnMonth = months.find((month) => String(loan.fecha_devolucion_real || '').startsWith(month.key));
    if (loanMonth) loanMonth.loans += 1;
    if (returnMonth) returnMonth.returns += 1;
  });
  const maximum = Math.max(1, ...months.map((month) => Math.max(month.loans, month.returns)));
  $(target).innerHTML = `<div class="bar-chart">${months.map((month) => `<div class="chart-column"><div class="bar-wrap"><span class="bar" style="height:${Math.max(3, month.loans / maximum * 145)}px" title="${month.loans} préstamos"></span>${includeReturns ? `<span class="bar secondary" style="height:${Math.max(3, month.returns / maximum * 145)}px" title="${month.returns} devoluciones"></span>` : ''}</div><span class="chart-label">${escapeHtml(month.label)}</span></div>`).join('')}</div><div class="chart-legend"><span><i class="legend-dot"></i>Préstamos</span>${includeReturns ? '<span><i class="legend-dot" style="background:#42bc94"></i>Devoluciones</span>' : ''}</div>`;
}

function renderDonut(target, entries, centerLabel) {
  const filtered = entries.filter((entry) => entry.value > 0);
  const total = filtered.reduce((sum, entry) => sum + entry.value, 0);
  if (!total) { $(target).innerHTML = empty('Sin datos para visualizar'); return; }
  let cumulative = 0;
  const segments = filtered.map((entry, index) => {
    const start = cumulative / total * 100;
    cumulative += entry.value;
    return `${entry.color || palette[index % palette.length]} ${start}% ${cumulative / total * 100}%`;
  });
  $(target).innerHTML = `<div class="donut-layout"><div class="donut-chart" style="background:conic-gradient(${segments.join(',')})"><div class="donut-center"><strong>${total}</strong><small>${escapeHtml(centerLabel)}</small></div></div><div class="legend-list">${filtered.slice(0, 6).map((entry, index) => `<div class="legend-item"><span><i class="legend-dot" style="background:${entry.color || palette[index % palette.length]}"></i>${escapeHtml(entry.label)}</span><strong>${entry.value}</strong></div>`).join('')}</div></div>`;
}

function renderCharts() {
  renderBarChart('#activity-chart');
  renderBarChart('#report-activity-chart', true);
  const groups = {};
  state.books.forEach((book) => { const label = book.categoria_nombre || 'Sin categoría'; groups[label] = (groups[label] || 0) + 1; });
  renderDonut('#category-chart', Object.entries(groups).map(([label, value]) => ({ label, value })), 'títulos');
  const data = metrics();
  renderDonut('#availability-chart', [{ label: 'Disponibles', value: data.available, color: '#42bc94' }, { label: 'Prestados', value: data.lent, color: '#786af0' }], 'ejemplares');
}

function bookCard(book, index) {
  const available = Number(book.cantidad_disponible || 0);
  return `<article class="book-card"><div class="book-cover cover-${index % 6}"><strong>${escapeHtml(book.titulo)}</strong></div><div class="book-content"><span class="book-category">${escapeHtml(book.categoria_nombre || 'Sin categoría')}</span><h3>${escapeHtml(book.titulo)}</h3><p class="book-author">${escapeHtml(book.autor_nombre || 'Autor no registrado')}</p><p class="book-code">${escapeHtml(book.codigo)}${book.editorial ? ` · ${escapeHtml(book.editorial)}` : ''}</p><div class="book-footer"><span class="availability${available ? '' : ' empty'}">${available ? `${available} disponibles` : 'Sin ejemplares'}</span><div class="card-actions"><button class="mini-button" data-action="edit-book" data-id="${book.id_libro}" title="Editar libro">✎</button><button class="mini-button" data-action="delete-book" data-id="${book.id_libro}" title="Eliminar libro">×</button></div></div></div></article>`;
}

function renderBooks() {
  const search = normalize($('#book-search').value);
  const category = $('#book-category-filter').value;
  const status = $('#book-status-filter').value;
  const books = state.books.filter((book) => {
    const matchesSearch = normalize([book.titulo, book.autor_nombre, book.categoria_nombre, book.codigo, book.isbn].join(' ')).includes(search);
    const matchesCategory = !category || String(book.id_categoria) === category;
    const matchesStatus = !status || (status === 'available' ? Number(book.cantidad_disponible) > 0 : Number(book.cantidad_disponible) <= 0);
    return matchesSearch && matchesCategory && matchesStatus;
  });
  $('#books-grid').innerHTML = books.length ? books.map(bookCard).join('') : empty('No encontramos libros', 'Prueba con otro filtro o registra un nuevo título.');
  $('#book-count').textContent = `${books.length} ${books.length === 1 ? 'título' : 'títulos'}`;
}

function renderUsers() {
  const search = normalize($('#user-search').value);
  const users = state.users.filter((user) => normalize([user.nombres, user.apellidos, user.cedula, user.correo].join(' ')).includes(search));
  $('#users-grid').innerHTML = users.length ? users.map((user) => {
    const initials = `${String(user.nombres || '?')[0]}${String(user.apellidos || '?')[0]}`.toUpperCase();
    const loans = state.loans.filter((loan) => String(loan.id_usuario) === String(user.id_usuario));
    return `<article class="user-card"><div class="user-head"><span class="reader-avatar">${escapeHtml(initials)}</span><div><h3>${escapeHtml(`${user.nombres} ${user.apellidos}`)}</h3><small>Cédula: ${escapeHtml(user.cedula)}</small></div></div><div class="user-details"><span class="user-detail">✉ ${escapeHtml(user.correo)}</span><span class="user-detail">☎ ${escapeHtml(user.telefono || 'Sin teléfono')}</span><span class="user-detail">⌂ ${escapeHtml(user.direccion || 'Sin dirección')}</span></div><div class="user-footer"><span class="status-pill status-active">${loans.length} ${loans.length === 1 ? 'préstamo' : 'préstamos'}</span><div class="card-actions"><button class="mini-button" data-action="user-history" data-id="${user.id_usuario}" title="Ver historial">☷</button><button class="mini-button" data-action="edit-user" data-id="${user.id_usuario}" title="Editar lector">✎</button></div></div></article>`;
  }).join('') : empty('No encontramos lectores', 'Registra el primer lector o modifica tu búsqueda.');
  $('#user-count').textContent = `${users.length} ${users.length === 1 ? 'lector' : 'lectores'}`;
}

function loanTable(loans, compact = false) {
  if (!loans.length) return empty('Todavía no hay préstamos', 'Los movimientos registrados aparecerán aquí.');
  return `<div class="table-wrap"><table class="data-table"><thead><tr><th>Libro</th><th>Lector</th><th>Fecha préstamo</th><th>Devolución</th><th>Estado</th>${compact ? '' : '<th>Acción</th>'}</tr></thead><tbody>${loans.map((loan) => {
    const status = effectiveStatus(loan);
    const pill = status === 'DEVUELTO' ? 'returned' : status === 'ATRASADO' ? 'overdue' : 'active';
    return `<tr><td><span class="cell-title">${escapeHtml(loan.titulo_libro || 'Libro no disponible')}</span><span class="cell-subtitle">${escapeHtml(loan.codigo_libro || '')}</span></td><td><span class="cell-title">${escapeHtml(`${loan.nombres || ''} ${loan.apellidos || ''}`.trim())}</span><span class="cell-subtitle">${escapeHtml(loan.cedula || '')}</span></td><td>${escapeHtml(formatDate(loan.fecha_prestamo))}</td><td>${escapeHtml(formatDate(loan.fecha_devolucion_programada))}</td><td><span class="status-pill status-${pill}">${status}</span></td>${compact ? '' : `<td>${status === 'DEVUELTO' ? `<span class="cell-subtitle">${escapeHtml(formatDate(loan.fecha_devolucion_real))}</span>` : `<button class="secondary-button" data-action="return-loan" data-id="${loan.id_prestamo}">Devolver</button>`}</td>`}</tr>`;
  }).join('')}</tbody></table></div>`;
}

function renderLoans() {
  const search = normalize($('#loan-search').value);
  const status = $('#loan-status-filter').value;
  const loans = [...state.loans].sort((first, second) => Number(second.id_prestamo) - Number(first.id_prestamo));
  const filtered = loans.filter((loan) => normalize([loan.titulo_libro, loan.codigo_libro, loan.nombres, loan.apellidos, loan.cedula].join(' ')).includes(search) && (!status || effectiveStatus(loan) === status));
  $('#loans-table').innerHTML = loanTable(filtered);
  $('#recent-loans').innerHTML = loanTable(loans.slice(0, 5), true);
}

function renderOrganization() {
  $('#categories-list').innerHTML = state.categories.length ? state.categories.map((category) => `<div class="organization-item"><div><strong>${escapeHtml(category.nombre)}</strong><small>${escapeHtml(category.descripcion || 'Sin descripción')}</small></div><span class="organization-count">${state.books.filter((book) => String(book.id_categoria) === String(category.id_categoria)).length} libros</span></div>`).join('') : empty('Sin categorías registradas');
  $('#authors-list').innerHTML = state.authors.length ? state.authors.map((author) => `<div class="organization-item"><div><strong>${escapeHtml(author.nombre)}</strong><small>${escapeHtml(author.nacionalidad || 'Nacionalidad no registrada')}</small></div><span class="organization-count">${state.books.filter((book) => String(book.id_autor) === String(author.id_autor)).length} libros</span></div>`).join('') : empty('Sin autores registrados');
  const previous = $('#book-category-filter').value;
  $('#book-category-filter').innerHTML = '<option value="">Todas las categorías</option>' + state.categories.map((category) => `<option value="${category.id_categoria}">${escapeHtml(category.nombre)}</option>`).join('');
  $('#book-category-filter').value = previous;
}

function renderReports() {
  const counts = {};
  state.loans.forEach((loan) => { const key = String(loan.id_libro); counts[key] = (counts[key] || 0) + 1; });
  const ranking = state.books.map((book) => ({ ...book, count: counts[String(book.id_libro)] || 0 })).sort((first, second) => second.count - first.count).slice(0, 7);
  $('#popular-books').innerHTML = ranking.length ? ranking.map((book, index) => `<div class="rank-item"><span class="rank-number">${index + 1}</span><div><strong>${escapeHtml(book.titulo)}</strong><small>${escapeHtml(book.autor_nombre || 'Autor no registrado')} · ${escapeHtml(book.categoria_nombre || 'Sin categoría')}</small></div><span class="rank-value">${book.count} ${book.count === 1 ? 'préstamo' : 'préstamos'}</span></div>`).join('') : empty('Todavía no hay libros registrados');
  const events = [...state.activity].sort((first, second) => Number(second.id_bitacora) - Number(first.id_bitacora));
  $('#activity-log').innerHTML = events.length ? events.map((event) => `<article class="timeline-item"><span class="timeline-icon">${String(event.accion || '').includes('DEVOL') ? '↩' : '✓'}</span><div><strong>${escapeHtml(event.accion)}</strong><p>${escapeHtml(event.descripcion || event.tabla_afectada || 'Movimiento del sistema')}</p></div><span class="timeline-date">${escapeHtml(formatDate(event.fecha))}</span></article>`).join('') : empty('La bitácora todavía está vacía', 'Cada nuevo registro, préstamo y devolución aparecerá aquí.');
}

function renderAll() { renderOrganization(); renderStats(); renderBooks(); renderUsers(); renderLoans(); renderCharts(); renderReports(); }

function field(name, label, options = {}) {
  const required = options.required ? ' required' : '';
  const value = escapeHtml(options.value ?? '');
  const full = options.full ? ' full' : '';
  let control;
  if (options.type === 'select') control = `<select id="field-${name}" name="${name}"${required}><option value="">Selecciona una opción</option>${(options.options || []).map((option) => `<option value="${escapeHtml(option.value)}"${String(option.value) === String(options.value) ? ' selected' : ''}>${escapeHtml(option.label)}</option>`).join('')}</select>`;
  else if (options.type === 'textarea') control = `<textarea id="field-${name}" name="${name}" placeholder="${escapeHtml(options.placeholder || '')}"${required}>${value}</textarea>`;
  else control = `<input id="field-${name}" type="${escapeHtml(options.type || 'text')}" name="${name}" value="${value}" placeholder="${escapeHtml(options.placeholder || '')}"${options.min !== undefined ? ` min="${options.min}"` : ''}${required}>`;
  return `<div class="field${full}"><label for="field-${name}">${escapeHtml(label)}${options.required ? ' *' : ''}</label>${control}</div>`;
}

function openModal(type, record = null) {
  state.modal = { type, record };
  let title = ''; let eyebrow = ''; let html = '';
  if (type === 'book') {
    title = record ? 'Editar libro' : 'Registrar nuevo libro'; eyebrow = 'CATÁLOGO BIBLIOGRÁFICO';
    html = field('codigo', 'Código del libro', { value: record?.codigo, placeholder: 'LIB-009', required: true }) + field('titulo', 'Título', { value: record?.titulo, required: true }) + field('id_autor', 'Autor', { type: 'select', value: record?.id_autor, options: state.authors.map((author) => ({ value: author.id_autor, label: author.nombre })), required: true }) + field('id_categoria', 'Categoría', { type: 'select', value: record?.id_categoria, options: state.categories.map((category) => ({ value: category.id_categoria, label: category.nombre })), required: true }) + field('editorial', 'Editorial', { value: record?.editorial }) + field('anio_publicacion', 'Año de publicación', { type: 'number', value: record?.anio_publicacion, min: 1000 }) + field('isbn', 'ISBN', { value: record?.isbn }) + field('cantidad_total', 'Cantidad de ejemplares', { type: 'number', value: record?.cantidad_total || record?.cantidad_disponible || 1, min: 1, required: true }) + field('descripcion', 'Descripción', { type: 'textarea', value: record?.descripcion, full: true });
  } else if (type === 'user') {
    title = record ? 'Editar lector' : 'Registrar nuevo lector'; eyebrow = 'COMUNIDAD LECTORA';
    html = field('cedula', 'Cédula', { value: record?.cedula, required: true }) + field('nombres', 'Nombres', { value: record?.nombres, required: true }) + field('apellidos', 'Apellidos', { value: record?.apellidos, required: true }) + field('correo', 'Correo electrónico', { type: 'email', value: record?.correo, required: true }) + field('telefono', 'Teléfono', { value: record?.telefono }) + field('direccion', 'Dirección', { value: record?.direccion });
  } else if (type === 'loan') {
    title = 'Registrar préstamo'; eyebrow = 'CIRCULACIÓN DE LIBROS';
    const deadline = new Date(); deadline.setDate(deadline.getDate() + 7);
    html = field('cedula_usuario', 'Lector', { type: 'select', options: state.users.filter((user) => user.estado !== 'INACTIVO').map((user) => ({ value: user.cedula, label: `${user.nombres} ${user.apellidos} · ${user.cedula}` })), required: true, full: true }) + field('codigo_libro', 'Libro disponible', { type: 'select', options: state.books.filter((book) => Number(book.cantidad_disponible) > 0).map((book) => ({ value: book.codigo, label: `${book.titulo} · ${book.cantidad_disponible} disponibles` })), required: true, full: true }) + field('fecha_limite', 'Fecha límite de devolución', { type: 'date', value: deadline.toLocaleDateString('en-CA'), required: true }) + field('observacion', 'Observación', { placeholder: 'Opcional' });
  } else if (type === 'category') {
    title = 'Nueva categoría'; eyebrow = 'ORGANIZACIÓN DE LA COLECCIÓN';
    html = field('nombre', 'Nombre de la categoría', { required: true, full: true }) + field('descripcion', 'Descripción', { type: 'textarea', full: true });
  } else if (type === 'author') {
    title = 'Nuevo autor'; eyebrow = 'VOCES DE LA BIBLIOTECA';
    html = field('nombre', 'Nombre completo', { required: true, full: true }) + field('nacionalidad', 'Nacionalidad') + field('fecha_nacimiento', 'Fecha de nacimiento', { type: 'date' });
  }
  $('#modal-title').textContent = title; $('#modal-eyebrow').textContent = eyebrow; $('#modal-fields').innerHTML = html;
  $('#submit-modal').textContent = record ? 'Guardar cambios' : 'Guardar registro';
  $('#modal-backdrop').classList.remove('hidden');
  window.setTimeout(() => $('#modal-fields input, #modal-fields select')?.focus(), 50);
}

function closeModal() { $('#modal-backdrop').classList.add('hidden'); state.modal = null; $('#modal-form').reset(); }

async function handleSubmit(event) {
  event.preventDefault();
  if (!state.modal) return;
  const button = $('#submit-modal'); button.disabled = true; button.textContent = 'Guardando...';
  try {
    const values = Object.fromEntries(new FormData(event.currentTarget).entries());
    const { type, record } = state.modal;
    const endpoints = { book: record ? 'actualizar_libro.php' : 'registrar_libro.php', user: record ? 'actualizar_usuario.php' : 'registrar_usuario.php', loan: 'registrar_prestamo.php', category: 'registrar_categoria.php', author: 'registrar_autor.php' };
    if (record && type === 'book') values.id_libro = record.id_libro;
    if (record && type === 'user') values.id_usuario = record.id_usuario;
    const response = await submit(endpoints[type], values);
    closeModal(); await loadData(); toast(response.message || 'Registro guardado correctamente.');
  } catch (error) { toast(error.message, true); } finally { button.disabled = false; button.textContent = 'Guardar registro'; }
}

async function handleAction(action, id) {
  try {
    if (action === 'open-book') openModal('book');
    else if (action === 'open-user') openModal('user');
    else if (action === 'open-loan') openModal('loan');
    else if (action === 'open-category') openModal('category');
    else if (action === 'open-author') openModal('author');
    else if (action === 'edit-book') openModal('book', state.books.find((book) => String(book.id_libro) === id));
    else if (action === 'edit-user') openModal('user', state.users.find((user) => String(user.id_usuario) === id));
    else if (action === 'user-history') { const user = state.users.find((item) => String(item.id_usuario) === id); location.hash = 'prestamos'; $('#loan-search').value = user?.cedula || ''; renderLoans(); }
    else if (action === 'return-loan') { if (!window.confirm('¿Confirmas que el libro fue devuelto a la biblioteca?')) return; const result = await submit('devolver_prestamo.php', { id_prestamo: id }); await loadData(); toast(result.message); }
    else if (action === 'delete-book') { if (!window.confirm('¿Deseas eliminar este libro? No podrá eliminarse si tiene préstamos registrados.')) return; const result = await submit('eliminar_libro.php', { id_libro: id }); await loadData(); toast(result.message); }
  } catch (error) { toast(error.message, true); }
}

function exportReport() {
  const rows = [['Código', 'Título', 'Autor', 'Categoría', 'Ejemplares totales', 'Disponibles', 'Préstamos']];
  state.books.forEach((book) => rows.push([book.codigo, book.titulo, book.autor_nombre, book.categoria_nombre, book.cantidad_total, book.cantidad_disponible, state.loans.filter((loan) => String(loan.id_libro) === String(book.id_libro)).length]));
  const csv = '\ufeff' + rows.map((row) => row.map((value) => `"${String(value ?? '').replace(/"/g, '""')}"`).join(';')).join('\n');
  const link = document.createElement('a'); link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' })); link.download = 'Reporte_BiblioSystem.csv'; link.click(); URL.revokeObjectURL(link.href);
}

function initialize() {
  $('#current-date').textContent = new Intl.DateTimeFormat('es-EC', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date());
  window.addEventListener('hashchange', () => showView(location.hash.slice(1)));
  showView(location.hash.slice(1));
  document.addEventListener('click', (event) => { const trigger = event.target.closest('[data-action]'); if (trigger) handleAction(trigger.dataset.action, trigger.dataset.id || ''); });
  $('#modal-form').addEventListener('submit', handleSubmit);
  $('#close-modal').addEventListener('click', closeModal); $('#cancel-modal').addEventListener('click', closeModal);
  $('#modal-backdrop').addEventListener('click', (event) => { if (event.target === $('#modal-backdrop')) closeModal(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });
  $('#refresh-button').addEventListener('click', () => loadData(true));
  $('#quick-add').addEventListener('click', () => openModal('loan'));
  $('#menu-toggle').addEventListener('click', () => $('#sidebar').classList.toggle('open'));
  $('#book-search').addEventListener('input', renderBooks); $('#book-category-filter').addEventListener('change', renderBooks); $('#book-status-filter').addEventListener('change', renderBooks);
  $('#user-search').addEventListener('input', renderUsers); $('#loan-search').addEventListener('input', renderLoans); $('#loan-status-filter').addEventListener('change', renderLoans);
  $('#global-search').addEventListener('input', (event) => { location.hash = 'libros'; $('#book-search').value = event.target.value; renderBooks(); });
  $('#export-report').addEventListener('click', exportReport);
  loadData();
}

document.addEventListener('DOMContentLoaded', initialize);

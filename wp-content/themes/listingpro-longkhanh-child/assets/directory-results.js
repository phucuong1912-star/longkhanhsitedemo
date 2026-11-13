(() => {
 'use strict';
 const layout = document.querySelector('.lk-results-layout');
 const api = window.LK_DIRECTORY;
 if (!layout || !api) return;
 const form = document.querySelector('#lk-filter');
 const sort = document.querySelector('.lk-sort select');
 const status = layout.querySelector('.lk-location-status');
 const min = form.elements.min_price, max = form.elements.max_price;
 const money = value => Number(value).toLocaleString('vi-VN') + 'đ';
 function updatePrice(changed) {
  if (Number(min.value) > Number(max.value)) {
   if (changed === min) max.value = min.value; else min.value = max.value;
  }
  form.querySelector('.lk-price-output').textContent = `${money(min.value)} – ${money(max.value)}`;
  const track = form.querySelector('.lk-price-track');
  track.style.setProperty('--price-min', `${Number(min.value) / Number(min.max) * 100}%`);
  track.style.setProperty('--price-max', `${Number(max.value) / Number(max.max) * 100}%`);
 }
 [min,max].forEach(input => input.addEventListener('input', () => updatePrice(input)));
 updatePrice();
 form.addEventListener('change', event => {
  if (event.target === sort) return;
  if (matchMedia('(min-width:992px)').matches) form.requestSubmit();
 });
 function showStatus(message, error = false) {
  status.textContent = message; status.hidden = false;
  status.classList.toggle('is-error', error);
 }
 let locating = false;
 sort.addEventListener('change', async () => {
  if (locating) return;
  if (sort.value !== 'nearest') { form.elements.nearby.value = ''; form.requestSubmit(); return; }
  locating = true; sort.disabled = true;
  showStatus('Đang xin quyền truy cập vị trí của bạn…');
  try { await api.locate(); form.elements.nearby.value = 'me'; sort.disabled = false; form.requestSubmit(); }
  catch (error) { sort.value = 'relevance'; form.elements.nearby.value = ''; showStatus(error.message, true); }
  finally { sort.disabled = false; locating = false; }
 });
 if (layout.dataset.nearby !== '1') return;
 // Rank the entire filtered set, then paginate; never rank only the first page.
 const ids = JSON.parse(layout.dataset.resultIds);
 const matches = (window.LK_SITE?.places || []).filter(place => ids.includes(place.id));
 const cards = layout.querySelector('.lk-results-cards');
 const pagination = layout.querySelector('.lk-pagination');
 const count = layout.querySelector('.lk-result-count strong');
 cards.hidden = true; pagination.hidden = true;
 showStatus('Đang lấy vị trí để tìm các địa điểm gần bạn…');
 api.locate().then(origin => {
  const ranked = matches.filter(api.hasCoordinates).map(place => ({...place, distance:api.distance(origin,place)})).sort((a,b) => a.distance - b.distance || a.id - b.id);
  layout.dataset.mapIds = JSON.stringify(ranked.map(place => place.id));
  count.textContent = ranked.length;
  const missing = matches.length - ranked.length;
  showStatus(`Đã sắp xếp theo khoảng cách từ vị trí của bạn.${missing ? ` ${missing} địa điểm chưa có tọa độ nên chưa thể xếp khoảng cách.` : ''}`);
  function render(page) {
   cards.hidden = false;
   cards.innerHTML = ranked.length ? ranked.slice((page - 1) * 10, page * 10).map(place => api.card(place, 'list')).join('') : '<div class="lk-empty"><h2>Chưa có địa điểm phù hợp có tọa độ</h2><p>Thử bỏ bớt bộ lọc hoặc tìm theo tên và khu vực.</p></div>';
   pagination.replaceChildren(); pagination.hidden = ranked.length <= 10;
   for (let i = 1; i <= Math.ceil(ranked.length / 10); i++) {
    const link = document.createElement('a'), url = new URL(location.href);
    url.searchParams.set('pg',i); link.href = url.href; link.textContent = i;
    if (i === page) link.setAttribute('aria-current','page');
    link.addEventListener('click',event => {event.preventDefault(); history.replaceState(null,'',link.href); render(i); layout.scrollIntoView({block:'start'});});
    pagination.append(link);
   }
   api.savedState();
  }
  const page = Math.min(Math.max(1,Number(new URL(location.href).searchParams.get('pg')) || 1),Math.max(1,Math.ceil(ranked.length / 10)));
  render(page);
 }).catch(error => {
  cards.hidden = false; pagination.hidden = matches.length <= 10;
  layout.dataset.nearby = '0';
  const chip = layout.querySelector('.lk-nearby-chip'); if (chip) chip.hidden = true;
  sort.value = 'relevance'; form.elements.nearby.value = '';
  showStatus(`${error.message} Các kết quả bên dưới chưa được sắp theo khoảng cách.`,true);
  const retry = document.createElement('button'); retry.type = 'button'; retry.className = 'lk-location-retry'; retry.textContent = 'Thử lại';
  retry.addEventListener('click', () => {sort.value = 'nearest'; sort.dispatchEvent(new Event('change'));}); status.append(' ',retry);
 });
})();

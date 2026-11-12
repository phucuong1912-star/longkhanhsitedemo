(() => {
  'use strict';
  if(document.body.classList.contains('elementor-editor-active'))return;
  if (window.lkInitialized) return;
  window.lkInitialized = true;
  const config = window.LK_SITE || { places: [] };
  const places = config.places || [];
  const $ = (s, root = document) => root.querySelector(s);
  const $$ = (s, root = document) => [...root.querySelectorAll(s)];
  if(config.loggedIn)$$('.lk-login-button .elementor-button-text').forEach(label=>label.textContent='Tài khoản');
  $$('.lk-feed-tabs a').forEach(link=>{const active=(new URL(link.href).searchParams.get('lk_feed')||'featured')===(new URL(location.href).searchParams.get('lk_feed')||'featured');if(active)link.setAttribute('aria-current','page');});
  const escape = value => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const read = key => { try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch { return []; } };
  const write = (key, value) => { try { localStorage.setItem(key, JSON.stringify(value)); } catch {} };
  const normalize = s => s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').replace(/\b(coffee|cafe)\b/g,'ca phe');
  const money = n => new Intl.NumberFormat('vi-VN').format(n) + 'đ';
  let lastFocus,restoringFocus=false;
  function openDialog(selector) {
    const dialog = $(selector); if (!dialog) return;
    lastFocus = document.activeElement;
    dialog.showModal(); document.documentElement.classList.add('lk-dialog-open');
    const first = $('input:not([type="hidden"]),button,a[href]', dialog); if (first) first.focus();
  }
  function closeDialog(dialog) {
    if (!dialog || dialog.classList.contains('is-closing')) return;
    const finish=()=>{restoringFocus=true;dialog.close();dialog.classList.remove('is-closing');if (!$('dialog[open]')) document.documentElement.classList.remove('lk-dialog-open');if(lastFocus&&lastFocus.isConnected)lastFocus.focus();requestAnimationFrame(()=>restoringFocus=false);};
    const duration=dialog.classList.contains('lk-drawer')?300:dialog.classList.contains('lk-place-menu')?200:0;
    if(duration&&!matchMedia('(prefers-reduced-motion: reduce)').matches){dialog.classList.add('is-closing');setTimeout(finish,duration);}else finish();
  }
  $$('dialog').forEach(d => {
    d.addEventListener('cancel', e => {e.preventDefault();closeDialog(d);});
    d.addEventListener('click', e => { if (e.target === d && d.id !== 'lk-search-modal') closeDialog(d); });
  });
  function toast(message) {
    const el = $('.lk-toast'); if (!el) return;
    el.textContent = message; el.classList.add('is-visible');
    clearTimeout(el.lkTimer); el.lkTimer = setTimeout(() => el.classList.remove('is-visible'), 2800);
  }
  function savedState() {
    const saved = read('lk-saved');
    $$('[data-save]').forEach(b => { const active = saved.includes(Number(b.dataset.save)); b.setAttribute('aria-pressed', String(active)); });
    $$('.lk-saved-places').forEach(el => {
      const list = places.filter(p => saved.includes(p.id));
      el.innerHTML = list.length ? list.map(p => suggestion(p)).join('') : '<p>Bấm biểu tượng đánh dấu ở địa điểm để lưu vào danh sách của bạn.</p>';
    });
  }
  function suggestion(p) { return `<a class="lk-suggestion" href="${escape(p.url)}"><img src="${escape(p.image)}" alt=""><span><strong>${escape(p.title)}</strong><small>${escape(p.address)}</small></span></a>`; }
  function suggestions(input) {
    const term = normalize(input.value.trim());
    const inModal = input.closest('#lk-search-modal');
    const result = inModal ? $('.lk-search-suggestions', inModal) : $('.lk-suggest', input.closest('form'));
    if (!result) return;
    const found = term ? places.filter(p => normalize(p.title + ' ' + p.address + ' ' + (p.categoryNames || '')).includes(term)).slice(0,5) : places.filter(p => read('lk-recent').includes(p.id)).slice(0,4);
    result.innerHTML = (!term ? '<p class="lk-suggestion-label">Đã xem gần đây</p>' : '') + found.map(suggestion).join('') + (term ? `<a class="lk-all-results" href="${escape(config.search)}?q=${encodeURIComponent(input.value)}">Xem tất cả tìm kiếm cho “${escape(input.value)}”</a>` : '<button type="button" class="lk-nearby">Tìm quanh đây</button>');
    result.hidden = false;
  }
  $$('.lk-search input').forEach(input => {
    input.addEventListener('input', () => suggestions(input));
    input.addEventListener('focus', () => {
      if(restoringFocus)return;
      if (innerWidth < 992 && !input.closest('#lk-search-modal')) {
        openDialog('#lk-search-modal'); const next = $('#lk-search-modal input'); next.value = input.value; next.focus(); suggestions(next);
      } else suggestions(input);
    });
    input.addEventListener('click',()=>{if(!restoringFocus&&innerWidth<992&&!input.closest('#lk-search-modal')&&!$('#lk-search-modal').open){openDialog('#lk-search-modal');const next=$('#lk-search-modal input');next.value=input.value;suggestions(next);}});
  });
  const current = places.find(p => p.id === Number(config.page));
  if (current) write('lk-recent', [current.id, ...read('lk-recent').filter(id => id !== current.id)].slice(0,6));
  function card(p) {
    const reviewLabel=p.reviews ? `${Number(p.reviews)} đánh giá` : 'Chưa có đánh giá';
    return `<article class="lk-place lk-place--grid" data-place-id="${p.id}"><a class="lk-place-photo" href="${escape(p.url)}" tabindex="-1" aria-hidden="true"><img src="${escape(p.image)}" alt="" loading="lazy" width="400" height="300"></a><button class="lk-save" data-save="${p.id}" aria-label="Lưu ${escape(p.title)}" aria-pressed="false"><i class="fas fa-bookmark" aria-hidden="true"></i></button><div class="lk-place-copy"><h3><a href="${escape(p.url)}">${escape(p.title)}</a></h3><div class="lk-list-rating"><span class="lk-stars" aria-label="${p.rating ? `Điểm ${p.rating} trên 5` : reviewLabel}">${p.rating || ''} ☆☆☆☆☆</span><span>${reviewLabel}</span></div><p class="lk-price">${money(p.min)} - ${money(p.max)}</p><p class="lk-address" title="${escape(p.address)}">${escape(p.address)}</p><div class="lk-place-meta"><span class="lk-region"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> ${escape(p.region)}</span><span class="lk-status ${p.opened ? 'is-open' : 'is-closed'}">${p.opened ? 'Đang mở cửa' : 'Đang đóng cửa'}</span></div></div></article>`;
  }
  async function showMap(id) {
    openDialog('#lk-map');
    const listingIds = $$('.lk-results-cards [data-place-id]').map(el => Number(el.dataset.placeId));
    const list = listingIds.length ? places.filter(p => listingIds.includes(p.id)) : places;
    const selected = places.find(p => p.id === Number(id)) || list[0] || places[0];
    $('.lk-map-list').innerHTML = list.map(p => suggestion(p).replace('class="lk-suggestion"', `data-map-place="${p.id}" class="lk-suggestion"`)).join('');
    if (selected) $('#lk-map iframe').src = `https://maps.google.com/maps?q=${selected.lat},${selected.lng}&z=14&output=embed`;
  }
  document.addEventListener('click', e => {
    if (e.target.closest('[data-close]')) { e.preventDefault(); closeDialog(e.target.closest('dialog')); }
    if (e.target.closest('.lk-open-menu')) { e.preventDefault(); openDialog('#lk-menu'); }
    if (e.target.closest('.lk-login-open')) { e.preventDefault(); openDialog('#lk-login'); }
    if (e.target.closest('.lk-open-search')) { e.preventDefault(); openDialog('#lk-search-modal'); suggestions($('#lk-search-modal input')); }
    if (e.target.closest('.lk-detail-links a[href$="/menu/"]')&&$('#lk-place-menu')){e.preventDefault();openDialog('#lk-place-menu');}
    if (e.target.closest('.lk-dismiss-app')) { e.preventDefault(); const banner = $('.lk-app-bar'); if (banner) banner.hidden = true; sessionStorage.setItem('lk-app-dismissed','1'); }
    const save = e.target.closest('[data-save]');
    if (save) { const id = Number(save.dataset.save), saved = read('lk-saved'); const next = saved.includes(id) ? saved.filter(v => v !== id) : [...saved,id]; write('lk-saved',next); savedState(); toast(next.includes(id) ? 'Đã lưu địa điểm' : 'Đã bỏ lưu địa điểm'); }
    const tab = e.target.closest('[data-category]');
    if (tab) { const wrapper = tab.closest('.elementor-widget-container') || tab.parentElement.parentElement; const grid = $('.lk-place-grid', wrapper); const list = places.filter(p => !tab.dataset.category || p.category === tab.dataset.category || (p.categories || []).includes(tab.dataset.category)).slice(0, Number(grid.dataset.limit) || 8); grid.innerHTML = list.length ? list.map(card).join('') : '<p class="lk-empty">Chưa có địa điểm trong danh mục này.</p>'; $$('[role="tab"]',wrapper).forEach(b => b.setAttribute('aria-selected',String(b===tab))); savedState(); }
    if (e.target.closest('.lk-filter-open')) { const wrap = $('.lk-filter-wrap'); wrap.classList.add('is-open'); document.documentElement.classList.add('lk-filter-opened'); $('.lk-filter-close').focus(); }
    if (e.target.closest('.lk-filter-close')) { $('.lk-filter-wrap').classList.remove('is-open'); document.documentElement.classList.remove('lk-filter-opened'); $('.lk-filter-open').focus(); }
    if (e.target.closest('[data-map]')) { e.preventDefault(); showMap(current?.id); }
    const mapLink = e.target.closest('[data-map-place]'); if (mapLink) { e.preventDefault(); const p = places.find(p => p.id === Number(mapLink.dataset.mapPlace)); if(p)$('#lk-map iframe').src=`https://maps.google.com/maps?q=${p.lat},${p.lng}&z=16&output=embed`; }
    if (e.target.closest('.lk-top')) window.scrollTo({top:0,behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});
    const password = e.target.closest('.lk-toggle-password'); if (password) { const input = $('input',password.parentElement); input.type = input.type === 'password' ? 'text' : 'password'; password.setAttribute('aria-label',input.type === 'password'?'Hiện mật khẩu':'Ẩn mật khẩu'); }
    const like = e.target.closest('.lk-like'); if(like){ const active=like.getAttribute('aria-pressed')!=='true';like.setAttribute('aria-pressed',String(active));like.innerHTML=`<i class="fas fa-heart" aria-hidden="true"></i> ${active?'Đã thích':'Thích'}`; }
    const share=e.target.closest('.lk-share');if(share){ if(navigator.share)navigator.share({url:share.dataset.url}).catch(()=>{});else navigator.clipboard?.writeText(share.dataset.url).then(()=>toast('Đã sao chép liên kết')).catch(()=>toast('Bạn có thể sao chép liên kết trên thanh địa chỉ.')); }
    if(!e.target.closest('.lk-search'))$$('.lk-suggest').forEach(el=>el.hidden=true);
    if(e.target.closest('.lk-nearby')){ e.preventDefault(); if(!$('#lk-search-modal').open)openDialog('#lk-search-modal'); $('.lk-search-suggestions').textContent='Đang tìm vị trí của bạn…'; if(!navigator.geolocation){$('.lk-search-suggestions').textContent='Trình duyệt chưa hỗ trợ xác định vị trí. Bạn có thể tìm theo tên hoặc khu vực.';return;} navigator.geolocation.getCurrentPosition(pos=>{places.sort((a,b)=>Math.hypot(a.lat-pos.coords.latitude,a.lng-pos.coords.longitude)-Math.hypot(b.lat-pos.coords.latitude,b.lng-pos.coords.longitude));const input=$('#lk-search-modal input');input.value='';$('.lk-search-suggestions').innerHTML='<h3>Địa điểm quanh bạn</h3>'+places.slice(0,6).map(suggestion).join('');},()=>{$('.lk-search-suggestions').textContent='Chưa lấy được vị trí. Bạn có thể nhập tên địa điểm hoặc chọn khu vực trong bộ lọc.';}); }
  });
  document.addEventListener('keydown',e=>{if(e.key==='Escape'){ const filter=$('.lk-filter-wrap.is-open');if(filter){filter.classList.remove('is-open');document.documentElement.classList.remove('lk-filter-opened');}$$('.lk-suggest').forEach(el=>el.hidden=true);} });
  document.addEventListener('keydown',e=>{
    const tab=e.target.closest('.lk-tabs [role="tab"]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(e.key))return;
    e.preventDefault();const tabs=$$('[role="tab"]',tab.parentElement);let index=tabs.indexOf(tab);
    index=e.key==='Home'?0:e.key==='End'?tabs.length-1:(index+(e.key==='ArrowRight'?1:-1)+tabs.length)%tabs.length;
    tabs[index].focus();tabs[index].click();
  });
  const range=$('#lk-max-price');if(range)range.addEventListener('input',()=>{$('output',range.parentElement).textContent=money(range.value);});
  const path=new URL(location.href).pathname.replace(/\/$/,'');$$('.lk-header-nav a,.lk-bottom-nav a,.lk-drawer a').forEach(a=>{if(new URL(a.href,location.href).pathname.replace(/\/$/,'')===path)a.setAttribute('aria-current','page');});
  try {if(sessionStorage.getItem('lk-app-dismissed')==='1'&&$('.lk-app-bar'))$('.lk-app-bar').hidden=true;}catch{}
  savedState();
})();

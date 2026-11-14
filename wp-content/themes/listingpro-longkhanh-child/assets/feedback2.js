(() => {
 'use strict';
 if(document.body.classList.contains('elementor-editor-active'))return;
 const $=(s,r=document)=>r.querySelector(s), $$=(s,r=document)=>[...r.querySelectorAll(s)];
 const config=window.LK_SITE||{}, places=config.places||[], api=window.LK_DIRECTORY;
 const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const base=new URL(config.base||location.href), url=path=>new URL(path,base).href;
 $$('.lk-bottom-nav a').forEach((a,i)=>{
  if(i===4){a.classList.toggle('lk-login-open',!config.loggedIn);a.href=url(config.loggedIn?'listing-author/':'profile/');}
  if(new URL(a.href).pathname===location.pathname)a.setAttribute('aria-current','page');
 });
 $$('.lk-login-open').forEach(a=>{if(config.loggedIn&&a.tagName==='A')a.href=url('listing-author/');});
 $$('a[href]').forEach(a=>{const destination=new URL(a.href,base);if(destination.origin===base.origin&&destination.pathname===new URL('profile/',base).pathname&&!destination.search){a.classList.add('lk-login-open');if(config.loggedIn)a.href=url('listing-author/');}});
 const current=places.find(p=>p.id===Number(config.page));
 document.addEventListener('keydown',e=>{const save=e.target.closest('a[data-save]');if(save&&e.key===' '){e.preventDefault();save.click();}});
 if(current&&$('.lk-detail-top h1')&&!$('.lk-heading-save')){
  const save=document.createElement('button');save.type='button';save.className='lk-heading-save lk-save';save.dataset.save=current.id;save.setAttribute('aria-label','Lưu địa điểm');save.setAttribute('aria-pressed','false');save.innerHTML='<i class="far fa-bookmark" aria-hidden="true"></i>';$('.lk-detail-top h1').parentElement.append(save);api?.savedState();
 }
 if(document.body.classList.contains('lk-f2-account-page')){const panel=$('.lk-account-panel');panel?.classList.add('lk-login-body');const close=document.createElement('a');close.className='lk-close lk-account-close';close.href=base.href;close.setAttribute('aria-label','Đóng đăng nhập');close.textContent='×';panel?.prepend(close);}
 document.addEventListener('click',e=>{const social=e.target.closest('[data-social]');if(social){const status=$('.lk-social-status',social.parentElement.parentElement);status.hidden=false;status.textContent='Đăng nhập '+social.dataset.social+' sẽ sớm được kết nối. Bạn có thể đăng nhập bằng tài khoản bên trên.';}});
 $$('.lk-filter-group legend').forEach(legend=>{legend.tabIndex=0;legend.setAttribute('role','button');legend.setAttribute('aria-expanded','true');const toggle=()=>{const collapsed=legend.parentElement.classList.toggle('is-collapsed');legend.setAttribute('aria-expanded',String(!collapsed));};legend.addEventListener('click',toggle);legend.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();toggle();}});});
 const category=$('.lk-form select[name=category]');
 if(category){const update=()=>{$$('[data-amenity-category]').forEach(group=>{group.hidden=group.dataset.amenityCategory!==category.value;$$('input',group).forEach(input=>{input.disabled=group.hidden;if(group.hidden)input.checked=false;});});const hint=$('.lk-amenity-hint');if(hint)hint.hidden=!!category.value;};category.addEventListener('change',update);update();}
 $$('.lk-hours-editor').forEach(editor=>{
  const days=$$('.lk-hours-day',editor),refreshers=[];
  days.forEach(day=>{
   day.classList.add('is-folded');const heading=$('.lk-hours-day-heading',day),title=$('strong',heading),mode=$('[data-hours-mode]',day);
   title.tabIndex=0;title.setAttribute('role','button');title.setAttribute('aria-expanded','false');
   const summary=$('.lk-hours-day-summary',heading)||document.createElement('span');summary.className='lk-hours-day-summary';if(!summary.isConnected)heading.append(summary);
   const clear=$('.lk-hours-clear-day',heading)||document.createElement('button');clear.type='button';clear.className='lk-hours-clear-day';clear.setAttribute('aria-label','Xóa lịch '+day.dataset.dayLabel);clear.textContent='×';if(!clear.isConnected)heading.append(clear);
   const refresh=()=>{summary.textContent=mode.value==='closed'?'Nghỉ':mode.value==='24h'?'Mở 24 giờ':$$('.lk-hours-slot',day).map(slot=>`${$('[data-hours-time="open"]',slot).value||'…'} – ${$('[data-hours-time="close"]',slot).value||'…'}`).join(', ');};refreshers.push(refresh);refresh();
   const toggle=()=>{title.setAttribute('aria-expanded',String(!day.classList.toggle('is-folded')));};title.addEventListener('click',toggle);title.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();toggle();}});
   mode.addEventListener('change',()=>{day.classList.remove('is-folded');title.setAttribute('aria-expanded','true');refresh();});
   clear.addEventListener('click',()=>{mode.value='closed';mode.dispatchEvent(new Event('change',{bubbles:true}));day.classList.add('is-folded');title.setAttribute('aria-expanded','false');refresh();});
  });
  const picker=$('.lk-hours-day-picker',editor)||document.createElement('label');picker.className='lk-hours-day-picker';if(!picker.isConnected){picker.innerHTML='<span>Chọn thứ để thêm hoặc sửa giờ</span><select aria-label="Chọn thứ để nhập giờ"><option value="">Chọn thứ</option>'+days.map((day,i)=>`<option value="${i}">${esc(day.dataset.dayLabel)}</option>`).join('')+'</select>';$('.lk-hours-copy',editor).before(picker);} $('select',picker).addEventListener('change',e=>{if(e.target.value==='')return;days.forEach((day,i)=>{const active=i===Number(e.target.value);day.classList.toggle('is-folded',!active);$('.lk-hours-day-heading strong',day).setAttribute('aria-expanded',String(active));if(active)day.scrollIntoView({block:'center',behavior:'auto'});});});
  editor.addEventListener('change',()=>refreshers.forEach(fn=>fn()));editor.addEventListener('click',()=>queueMicrotask(()=>refreshers.forEach(fn=>fn())));
 });
 // Expand invalid day before the browser attempts to focus its hidden time field.
 document.addEventListener('invalid',e=>{const day=e.target.closest('.lk-hours-day');if(day){day.classList.remove('is-folded');$('.lk-hours-day-heading strong',day).setAttribute('aria-expanded','true');}},true);
 let shareDialog,shareOpener;
 document.addEventListener('click',async e=>{
  const shareButton=e.target.closest('[data-place-share]');if(!shareButton)return;shareOpener=shareButton;
  const data={title:current?.title||document.title,url:current?.url||location.href};
  if(!shareDialog){shareDialog=document.createElement('dialog');shareDialog.className='lk-share-dialog';shareDialog.innerHTML='<h2>Chia sẻ địa điểm</h2><div class="lk-share-links"><a target="_blank" rel="noopener">Facebook</a><button type="button">Sao chép liên kết</button></div><p role="status"></p><button type="button" data-close>Đóng</button>';document.body.append(shareDialog);$('a',shareDialog).href='https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(data.url);$('.lk-share-links button',shareDialog).onclick=async()=>{try{await navigator.clipboard.writeText(data.url);$('p',shareDialog).textContent='Đã sao chép liên kết.';}catch{$('p',shareDialog).textContent=data.url;}};}
  if(navigator.share&&!$('[data-system-share]',shareDialog)){const other=document.createElement('button');other.type='button';other.dataset.systemShare='';other.textContent='Ứng dụng khác';other.addEventListener('click',async()=>{try{await navigator.share(data);shareDialog.close();}catch(error){if(error.name!=='AbortError')$('p',shareDialog).textContent='Chọn Facebook hoặc sao chép liên kết để chia sẻ.';}});$('.lk-share-links',shareDialog).append(other);}
  if(!shareDialog.dataset.initialized){shareDialog.dataset.initialized='1';shareDialog.addEventListener('close',()=>{if(!$('dialog[open]'))document.documentElement.classList.remove('lk-dialog-open');shareOpener?.focus();});shareDialog.addEventListener('click',event=>{if(event.target===shareDialog)shareDialog.close();});}
  shareDialog.showModal();document.documentElement.classList.add('lk-dialog-open');
 });
 let map,canvas,mapOpener,markers=new Map();
 $('#lk-map')?.addEventListener('close',()=>mapOpener?.focus());
 function stars(p){return `<span class="lk-star-track" aria-label="${Number(p.rating)||0} trên 5">☆☆☆☆☆<span style="width:${Math.max(0,Math.min(5,Number(p.rating)||0))*20}%">★★★★★</span></span>`;}
 function mapCard(p){return `<button type="button" class="lk-suggestion" data-map-place="${p.id}"><span><strong>${esc(p.title)}</strong><small>${stars(p)} · ${p.reviews?`${p.reviews} đánh giá`:'chưa có đánh giá'}</small><small class="lk-map-card-address">${esc(p.address)}</small><small><span class="${p.opened?'lk-map-open':'lk-map-closed'}">${p.opened?'Đang mở cửa':'Đang đóng cửa'}</span> · ${esc(p.hoursLabel||'')}</small></span><img src="${esc(p.image)}" alt=""></button>`;}
 function scrollMapCard(card){const list=card?.closest('.lk-map-list');if(!list)return;list.scrollTo({left:list.scrollLeft+card.getBoundingClientRect().left-list.getBoundingClientRect().left-(list.clientWidth-card.clientWidth)/2,behavior:matchMedia('(prefers-reduced-motion:reduce)').matches?'auto':'smooth'});}
 function activate(id,pan=true){const marker=markers.get(Number(id));if(!marker)return;if(pan)map.panTo(marker.getLatLng());marker.openPopup();$$('#lk-map [data-map-place]').forEach(card=>card.classList.toggle('is-active',Number(card.dataset.mapPlace)===Number(id)));}
 function showMap(id){
  const dialog=$('#lk-map');if(!dialog)return;
  if(!dialog.open)dialog.showModal();document.documentElement.classList.add('lk-dialog-open');
  const layout=$('.lk-results-layout');let ids=null;try{ids=layout?JSON.parse(layout.dataset.mapIds||layout.dataset.resultIds):null;}catch{}
  const ordered=ids?ids.map(id=>places.find(p=>p.id===Number(id))).filter(Boolean):places;
  const list=ordered.filter(p=>api.hasCoordinates(p));
  $('.lk-map-list',dialog).innerHTML=list.length?list.map(mapCard).join(''):'<p>Chưa có địa điểm có tọa độ trong kết quả này.</p>';
  if(!window.L){$('.lk-map-list',dialog).insertAdjacentHTML('afterbegin','<p role="alert">Chưa tải được bản đồ. Vui lòng thử lại.</p>');return;}
  if(!map){
   canvas=document.createElement('div');canvas.className='lk-map-canvas';canvas.setAttribute('aria-label','Bản đồ các địa điểm');$('.lk-map-layout',dialog).append(canvas);
   map=L.map(canvas,{zoomControl:false}).setView([10.9398,107.2446],13);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors',maxZoom:19}).addTo(map);
   const feedback=document.createElement('p');feedback.className='lk-map-location-status';feedback.setAttribute('role','status');feedback.hidden=true;$('.lk-map-layout',dialog).append(feedback);
   let userMarker;
   const Locate=L.Control.extend({options:{position:'topleft'},onAdd(){const button=L.DomUtil.create('button','lk-map-locate leaflet-bar');button.type='button';button.setAttribute('aria-label','Vị trí của tôi');button.title='Vị trí của tôi';button.innerHTML='<i class="fas fa-crosshairs" aria-hidden="true"></i>';L.DomEvent.disableClickPropagation(button);L.DomEvent.on(button,'click',async e=>{L.DomEvent.preventDefault(e);button.disabled=true;try{const origin=await api.locate();if(userMarker)map.removeLayer(userMarker);userMarker=L.circleMarker([origin.lat,origin.lng],{radius:7,color:'#fff',weight:2,fillColor:'#2979d5',fillOpacity:1}).addTo(map);map.setView([origin.lat,origin.lng],15);feedback.hidden=true;}catch(error){feedback.textContent=error.message;feedback.hidden=false;}finally{button.disabled=false;}});return button;}});
   new Locate().addTo(map);L.control.zoom({position:'topleft'}).addTo(map);
  }
  markers.forEach(marker=>map.removeLayer(marker));markers.clear();
  list.forEach(p=>{const marker=L.marker([p.lat,p.lng],{title:p.title,keyboard:false,icon:L.divIcon({className:'lk-map-pin',html:`<button type="button" class="lk-map-pin-button" data-map-pin="${p.id}" aria-label="${esc(p.title)}"><svg width="24" height="32" viewBox="0 0 24 32" aria-hidden="true"><path d="M12 0C5.4 0 0 5.4 0 12c0 8 12 20 12 20s12-12 12-20C24 5.4 18.6 0 12 0z" fill="currentColor"/><circle cx="12" cy="12" r="4" fill="white"/></svg></button>`,iconSize:[44,44],iconAnchor:[22,40]})}).addTo(map);marker.bindPopup(`<div class="lk-map-popup"><a class="lk-map-popup-photo" href="${esc(p.url)}"><img src="${esc(p.image)}" alt=""></a><div><a href="${esc(p.url)}"><strong>${esc(p.title)}</strong></a><p>${esc(p.address)}</p><p>${stars(p)} ${p.reviews?`${p.reviews} đánh giá`:'chưa có đánh giá'}</p></div></div>`);marker.on('click',()=>{$$('#lk-map [data-map-place]').forEach(card=>{const active=Number(card.dataset.mapPlace)===p.id;card.classList.toggle('is-active',active);if(active)scrollMapCard(card);});});markers.set(p.id,marker);});
  requestAnimationFrame(()=>{map.invalidateSize();if(list.length)map.fitBounds(L.latLngBounds(list.map(p=>[p.lat,p.lng])),{padding:[35,35],maxZoom:16,animate:false});if(list.length)activate(id||list[0].id,false);});
 }
 // Capture before the original single-pin iframe handler.
 document.addEventListener('click',e=>{const trigger=e.target.closest('[data-map], [data-map-place], [data-map-pin]');if(!trigger)return;e.preventDefault();e.stopImmediatePropagation();if(trigger.hasAttribute('data-map-pin')&&map){activate(trigger.dataset.mapPin);scrollMapCard($('#lk-map [data-map-place].is-active'));}else if(trigger.hasAttribute('data-map-place')&&map)activate(trigger.dataset.mapPlace);else{mapOpener=trigger;showMap(trigger.dataset.map);}},true);
})();

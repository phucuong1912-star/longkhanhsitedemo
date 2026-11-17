(() => {
 'use strict';
 if(document.body.classList.contains('elementor-editor-active'))return;
 const $=(s,r=document)=>r.querySelector(s), $$=(s,r=document)=>[...r.querySelectorAll(s)];
 const config=window.LK_SITE||{}, places=config.places||[], api=window.LK_DIRECTORY;
 const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const base=new URL(config.base||location.href), url=path=>new URL(path,base).href;
 const navIcons=[
  '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/>',
  '<path d="M4 4a16 16 0 0 1 16 16M4 10a10 10 0 0 1 10 10"/><circle cx="5" cy="19" r="1.5"/>',
  '<path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.5"/>',
  '<path d="m5 20 14-16"/><circle cx="6.5" cy="6.5" r="3"/><circle cx="17.5" cy="17.5" r="3"/>',
  '<circle cx="12" cy="7" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>'
 ];
 $$('.lk-bottom-nav a').forEach((a,i)=>{
  const icon=$('.elementor-icon-list-icon',a);
  if(icon&&navIcons[i])icon.innerHTML=`<svg class="lk-tab-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${navIcons[i]}</svg>`;
  if(i===4){a.classList.toggle('lk-login-open',!config.loggedIn);}
  const target=new URL(a.href),path=location.pathname.replace(/\/$/,''),destination=target.pathname.replace(/\/$/,'');
  const selected=i===0?path===destination:i===2?path===destination&&new URL(location.href).searchParams.get('nearby')==='me':path===destination||path.startsWith(destination+'/');
  if(selected)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');
 });
 $$('a[href]').forEach(a=>{const destination=new URL(a.href,base);if(!config.loggedIn&&!a.classList.contains('lk-member-saved')&&destination.origin===base.origin&&destination.pathname===new URL('profile/',base).pathname&&!destination.search){a.classList.add('lk-login-open');}});
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
 let map,canvas,mapOpener,activeMapId=null,markers=new Map();
 $('#lk-map')?.addEventListener('close',()=>mapOpener?.focus());
 function stars(p){return `<span class="lk-star-track" aria-label="${Number(p.rating)||0} trên 5">☆☆☆☆☆<span style="width:${Math.max(0,Math.min(5,Number(p.rating)||0))*20}%">★★★★★</span></span>`;}
 function mapCard(p){return `<article class="lk-map-card" data-map-card="${p.id}"><button type="button" class="lk-suggestion" data-map-place="${p.id}" aria-pressed="false"><span><strong>${esc(p.title)}</strong><small>${stars(p)} · ${p.reviews?`${p.reviews} đánh giá`:'chưa có đánh giá'}</small><small class="lk-map-card-address">${esc(p.address)}</small><small><span class="${p.opened?'lk-map-open':'lk-map-closed'}">${p.opened?'Đang mở cửa':'Đang đóng cửa'}</span> · ${esc(p.hoursLabel||'')}</small></span><img src="${esc(p.image)}" alt="" loading="lazy"></button><div class="lk-map-card-footer"><span class="lk-map-selected-label">Đang chọn</span><a href="${esc(p.url)}">Xem địa điểm <span aria-hidden="true">→</span></a></div></article>`;}
 function scrollMapCard(card){
  const list=card?.closest('.lk-map-list');if(!list)return;
  const horizontal=getComputedStyle(list).flexDirection==='row',a=card.getBoundingClientRect(),b=list.getBoundingClientRect();
  list.scrollTo({[horizontal?'left':'top']:horizontal?list.scrollLeft+a.left-b.left-(list.clientWidth-card.clientWidth)/2:list.scrollTop+a.top-b.top-(list.clientHeight-card.clientHeight)/2,behavior:matchMedia('(prefers-reduced-motion:reduce)').matches?'auto':'smooth'});
 }
 function activate(id,animate=true){
  id=Number(id);const marker=markers.get(id);if(!marker)return;
  activeMapId=id;
  markers.forEach((pin,pinId)=>{
   const selected=pinId===id,button=$(`#lk-map [data-map-pin="${pinId}"]`),element=button?.closest('.lk-map-pin');
   pin.setZIndexOffset(selected?1000:0);
   element?.classList.toggle('is-selected',selected);
   button?.setAttribute('aria-pressed',String(selected));
  });
  $$('#lk-map [data-map-card]').forEach(card=>{
   const selected=Number(card.dataset.mapCard)===id;
   card.classList.toggle('is-active',selected);$('[data-map-place]',card)?.setAttribute('aria-pressed',String(selected));
  });
  const motion=animate&&!matchMedia('(prefers-reduced-motion:reduce)').matches,center=marker.getLatLng(),zoom=Math.max(map.getZoom(),14);
  if(typeof map.stop==='function')map.stop();
  if(motion&&typeof map.flyTo==='function')map.flyTo(center,zoom,{animate:true,duration:.65,easeLinearity:.25});
  else if(motion&&map.getZoom()===zoom)map.panTo(center,{animate:true,duration:.55,easeLinearity:.25});
  else map.setView(center,zoom,{animate:motion,pan:{duration:.55,easeLinearity:.25}});
 }
 function showMap(id){
  const dialog=$('#lk-map');if(!dialog)return;
  if(!dialog.open)dialog.showModal();document.documentElement.classList.add('lk-dialog-open');
  const layout=$('.lk-results-layout');let ids=null;try{ids=layout?JSON.parse(layout.dataset.mapIds||layout.dataset.resultIds):null;}catch{}
  const ordered=ids?ids.map(id=>places.find(p=>p.id===Number(id))).filter(Boolean):places;
  const list=ordered.filter(p=>api.hasCoordinates(p));
  const cards=$('.lk-map-list',dialog);cards.setAttribute('aria-label','Các địa điểm trên bản đồ');
  cards.innerHTML=list.length?list.map(mapCard).join(''):'<p class="lk-map-empty">Chưa có địa điểm có tọa độ trong kết quả này.</p>';
  if(!window.L){$('.lk-map-list',dialog).insertAdjacentHTML('afterbegin','<p role="alert">Chưa tải được bản đồ. Vui lòng thử lại.</p>');return;}
  if(!map){
   canvas=document.createElement('div');canvas.className='lk-map-canvas';canvas.setAttribute('aria-label','Bản đồ các địa điểm');$('.lk-map-layout',dialog).append(canvas);
   map=L.map(canvas,{zoomControl:false}).setView([10.9398,107.2446],13);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors',maxZoom:19}).addTo(map);
   const feedback=document.createElement('p');feedback.className='lk-map-location-status';feedback.setAttribute('role','status');feedback.hidden=true;$('.lk-map-layout',dialog).append(feedback);
   let userMarker;
   const Locate=L.Control.extend({options:{position:'topleft'},onAdd(){const button=L.DomUtil.create('button','lk-map-locate leaflet-bar');button.type='button';button.setAttribute('aria-label','Vị trí của tôi');button.title='Vị trí của tôi';button.innerHTML='<i class="fas fa-crosshairs" aria-hidden="true"></i>';L.DomEvent.disableClickPropagation(button);L.DomEvent.on(button,'click',async e=>{L.DomEvent.preventDefault(e);button.disabled=true;try{const origin=await api.locate();if(userMarker)map.removeLayer(userMarker);userMarker=L.circleMarker([origin.lat,origin.lng],{radius:7,color:'#fff',weight:2,fillColor:'#2979d5',fillOpacity:1}).addTo(map);map.setView([origin.lat,origin.lng],15);feedback.hidden=true;}catch(error){feedback.textContent=error.message;feedback.hidden=false;}finally{button.disabled=false;}});return button;}});
   new Locate().addTo(map);L.control.zoom({position:'topleft'}).addTo(map);
  }
  markers.forEach(marker=>map.removeLayer(marker));markers.clear();activeMapId=null;
  list.forEach(p=>{const marker=L.marker([p.lat,p.lng],{title:p.title,keyboard:false,icon:L.divIcon({className:'lk-map-pin',html:`<button type="button" class="lk-map-pin-button" data-map-pin="${p.id}" aria-label="${esc(p.title)}" aria-pressed="false"><svg width="26" height="34" viewBox="-1 -1 26 34" aria-hidden="true"><path d="M12 0C5.4 0 0 5.4 0 12c0 8 12 20 12 20s12-12 12-20C24 5.4 18.6 0 12 0z" fill="currentColor" stroke="white" stroke-width="1.4"/><circle cx="12" cy="12" r="4" fill="white"/></svg></button>`,iconSize:[44,44],iconAnchor:[22,40]})}).addTo(map);marker.on('click',()=>{activate(p.id);scrollMapCard($(`#lk-map [data-map-card="${p.id}"]`));});markers.set(p.id,marker);});
  requestAnimationFrame(()=>{
   map.invalidateSize();
   if(list.length){const first=list.find(p=>p.id===Number(id))||list[0];activate(first.id,false);scrollMapCard($(`#lk-map [data-map-card="${first.id}"]`));}
   else map.setView([10.9398,107.2446],13,{animate:false});
  });
 }
 // Capture before the original single-pin iframe handler.
 document.addEventListener('click',e=>{const trigger=e.target.closest('[data-map], [data-map-place], [data-map-pin]');if(!trigger)return;e.preventDefault();e.stopImmediatePropagation();if((trigger.hasAttribute('data-map-pin')||trigger.hasAttribute('data-map-place'))&&map){activate(trigger.dataset.mapPin||trigger.dataset.mapPlace);if(trigger.hasAttribute('data-map-pin'))scrollMapCard($(`#lk-map [data-map-card="${activeMapId}"]`));}else{mapOpener=trigger;showMap(trigger.dataset.map);}},true);
})();

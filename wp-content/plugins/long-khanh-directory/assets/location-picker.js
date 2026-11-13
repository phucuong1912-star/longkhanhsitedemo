(() => {
 'use strict';
 const config = window.LK_LOCATION || {provider:'openstreet',center:{lat:10.9398,lng:107.2446}};
 let googlePromise;
 function googleMaps() {
  if (window.google?.maps?.importLibrary) return Promise.resolve(window.google.maps);
  if (googlePromise) return googlePromise;
  googlePromise = new Promise((resolve,reject) => {
   if (!config.googleReady || !config.googleScript) {reject(new Error('Google Maps chưa có API key trong cấu hình theme.'));return;}
   const script = document.createElement('script'),url = new URL(config.googleScript);
   const timer = setTimeout(() => reject(new Error('Chưa tải được Google Maps. Bạn vẫn có thể nhập tọa độ thủ công.')),15000);
   window.lkGoogleLocationReady = () => {clearTimeout(timer);resolve(window.google.maps);};
   const previous = window.gm_authFailure;
   window.gm_authFailure = () => {clearTimeout(timer);reject(new Error('Google Maps chưa được cấp quyền. Kiểm tra API key, Billing và tên miền trong cấu hình Google.'));if(previous)previous();};
   url.searchParams.set('callback','lkGoogleLocationReady');script.src = url.href;script.async = true;
   script.onerror = () => {clearTimeout(timer);reject(new Error('Không tải được Google Maps. Bạn có thể nhập tọa độ thủ công.'));};
   document.head.append(script);
  });
  return googlePromise;
 }
 function initialize(root = document) {
  root.querySelectorAll('.lk-address-editor').forEach(editor => {
   if (editor.dataset.locationInitialized) return;editor.dataset.locationInitialized = '1';
   const address = editor.querySelector('[data-location-address]');
   const lat = editor.querySelector('[data-location-coordinate="lat"]'),lng = editor.querySelector('[data-location-coordinate="lng"]');
   const coordinates = editor.querySelector('.lk-address-coordinates'),panel = editor.querySelector('.lk-location-map-panel');
   const canvas = editor.querySelector('.lk-location-canvas'),feedback = editor.querySelector('.lk-location-feedback'),host = editor.querySelector('.lk-google-search');
   let adapter,mapPromise,autocompleteReady = false;
   const read = () => lat.value.trim() !== '' && lng.value.trim() !== '' && Number.isFinite(Number(lat.value)) && Number.isFinite(Number(lng.value)) && Math.abs(Number(lat.value)) <= 90 && Math.abs(Number(lng.value)) <= 180 ? {lat:Number(lat.value),lng:Number(lng.value)} : null;
   function message(text,error = false) {feedback.textContent = text;feedback.hidden = !text;feedback.classList.toggle('is-error',error);}
   function validate() {
    lat.setCustomValidity('');lng.setCustomValidity('');
    if (lat.value && !lng.value) lng.setCustomValidity('Vui lòng nhập kinh độ hoặc chọn ghim trên bản đồ.');
    if (lng.value && !lat.value) lat.setCustomValidity('Vui lòng nhập vĩ độ hoặc chọn ghim trên bản đồ.');
    const point = read();if(point)adapter?.setPosition(point);else if(!lat.value&&!lng.value)adapter?.removeMarker();
   }
   function selectPoint(point) {
    const latitude = typeof point.lat === 'function' ? point.lat() : point.lat;
    const longitude = typeof point.lng === 'function' ? point.lng() : point.lng;
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude)>90 || Math.abs(longitude)>180) return;
    lat.value = latitude.toFixed(7);lng.value = longitude.toFixed(7);validate();
    message(`Đã chọn vị trí: ${lat.value}, ${lng.value}.`);
   }
   async function map() {
    if (mapPromise) return mapPromise;
    mapPromise = (async () => {
     const selected = read(),center = selected || config.center;
     if (config.provider === 'google' && config.googleReady) {
      const maps = await googleMaps();
      const {Map} = await maps.importLibrary('maps'),{AdvancedMarkerElement} = await maps.importLibrary('marker');
      const instance = new Map(canvas,{center,zoom:selected?16:13,mapId:'DEMO_MAP_ID',streetViewControl:false,mapTypeControl:false});
      let marker;
      adapter = {setPosition(point) {if(!marker){marker=new AdvancedMarkerElement({map:instance,position:point,gmpDraggable:true,title:'Kéo ghim để chọn vị trí địa điểm'});marker.addListener('dragend',event=>selectPoint(event.latLng||marker.position));}else{marker.map=instance;marker.position=point;}},removeMarker(){if(marker)marker.map=null;},resize(){maps.event.trigger(instance,'resize');},center(point){instance.setCenter(point);}};
      instance.addListener('click',event=>selectPoint(event.latLng));
     } else {
      const L = window.L;if(!L?.map)throw new Error('Chưa tải được bản đồ. Bạn có thể nhập tọa độ thủ công.');
      const instance = L.map(canvas,{scrollWheelZoom:false}).setView([center.lat,center.lng],selected?16:13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(instance);
      let marker;const icon = L.divIcon({className:'lk-map-marker',html:'<span aria-hidden="true"></span>',iconSize:[28,38],iconAnchor:[14,38]});
      adapter = {setPosition(point){if(!marker){marker=L.marker([point.lat,point.lng],{draggable:true,icon,title:'Kéo ghim để chọn vị trí địa điểm'}).addTo(instance);marker.on('dragend',()=>selectPoint(marker.getLatLng()));}else{marker.setLatLng([point.lat,point.lng]);if(!instance.hasLayer(marker))marker.addTo(instance);}},removeMarker(){if(marker)instance.removeLayer(marker);},resize(){instance.invalidateSize();},center(point){instance.setView([point.lat,point.lng],16);}};
      instance.on('click',event=>selectPoint(event.latlng));
     }
     if(selected)adapter.setPosition(selected);
     return adapter;
    })().catch(error=>{mapPromise=null;message(error.message,true);throw error;});
    return mapPromise;
   }
   async function googleSearch() {
    if(autocompleteReady || !config.googleReady) return;
    try {
     const maps = await googleMaps(),{PlaceAutocompleteElement} = await maps.importLibrary('places');
     const search = new PlaceAutocompleteElement();search.setAttribute('aria-label','Tìm địa chỉ trên Google');host.replaceChildren(search);autocompleteReady = true;
     search.addEventListener('gmp-select',async event=>{
      try {const place=event.placePrediction.toPlace();await place.fetchFields({fields:['formattedAddress','location']});if(!place.location)throw new Error('Địa chỉ này chưa có tọa độ. Hãy chọn lại hoặc thả ghim.');address.value=place.formattedAddress||address.value;selectPoint(place.location);setMode('pin');const instance=await map();instance.center(read());}
      catch(error){message(error.message || 'Chưa lấy được địa chỉ từ Google.',true);}
     });
     search.addEventListener('gmp-error',()=>message('Chưa tìm được địa chỉ Google. Kiểm tra quyền Places API hoặc dùng nhập tọa độ thủ công.',true));
    }catch(error){message(error.message,true);}
   }
   function setMode(mode) {
    editor.querySelectorAll('[data-location-mode]').forEach(button=>{const selected=button.dataset.locationMode===mode;button.setAttribute('aria-selected',String(selected));button.tabIndex=selected?0:-1;});
    coordinates.hidden = mode === 'address';panel.hidden = mode !== 'pin';host.hidden = mode !== 'address' || !config.googleReady;
    if(mode==='pin')map().then(instance=>requestAnimationFrame(()=>instance.resize())).catch(()=>{});
    if(mode==='address'&&config.googleReady)googleSearch();
   }
   editor.querySelectorAll('[data-location-mode]').forEach(button=>{
    button.addEventListener('click',()=>setMode(button.dataset.locationMode));
    button.addEventListener('keydown',event=>{if(!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;event.preventDefault();const tabs=[...editor.querySelectorAll('[data-location-mode]')];let index=tabs.indexOf(button);index=event.key==='Home'?0:event.key==='End'?tabs.length-1:(index+(event.key==='ArrowRight'?1:-1)+tabs.length)%tabs.length;tabs[index].focus();setMode(tabs[index].dataset.locationMode);});
   });
   [lat,lng].forEach(input=>input.addEventListener('input',validate));
   editor.querySelector('.lk-location-my-position').addEventListener('click',()=>{
    if(!navigator.geolocation){message('Trình duyệt không hỗ trợ vị trí. Hãy chọn trên bản đồ hoặc nhập tọa độ.',true);return;}
    message('Đang xin quyền truy cập vị trí của bạn…');
    navigator.geolocation.getCurrentPosition(async position=>{selectPoint({lat:position.coords.latitude,lng:position.coords.longitude});try{(await map()).center(read());}catch{}},()=>message('Chưa lấy được vị trí. Hãy cho phép vị trí hoặc tự chọn ghim trên bản đồ.',true),{timeout:12000,maximumAge:300000});
   });
   window.addEventListener('resize',()=>adapter?.resize());validate();setMode('address');
  });
 }
 initialize();
 if(window.elementorFrontend?.hooks)window.elementorFrontend.hooks.addAction('frontend/element_ready/lk-form.default',scope=>initialize(scope[0]));
})();

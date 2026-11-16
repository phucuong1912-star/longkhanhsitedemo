(() => {
 'use strict';
 document.querySelectorAll('[data-lk-detail-map]').forEach(canvas => {
  if (!window.L || canvas.dataset.initialized) return;
  canvas.dataset.initialized = '1';
  const point = [Number(canvas.dataset.lat), Number(canvas.dataset.lng)];
  const map = L.map(canvas, {scrollWheelZoom:false}).setView(point, 15);
  map.attributionControl.setPosition('topright');
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
   attribution:'&copy; OpenStreetMap contributors', maxZoom:19
  }).addTo(map);
  L.marker(point).addTo(map);
  new ResizeObserver(() => map.invalidateSize()).observe(canvas);
 });
 if (!document.querySelector('.lk-benefits')) {
  document.querySelectorAll('.lk-detail-tabs a[href="#benefits"]').forEach(link => link.closest('li')?.remove());
 }
})();

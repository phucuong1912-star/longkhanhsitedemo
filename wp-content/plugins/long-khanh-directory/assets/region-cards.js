(() => {
 'use strict';
 function init() {
  if (document.body.classList.contains('elementor-editor-active')) return;
  document.querySelectorAll('.lk-region-card').forEach(card => {
   const heading = [...card.querySelectorAll('.elementor-widget-heading a[href]')].find(link => {
    if (link.closest('.lk-region-card') !== card) return false;
    try { const url = new URL(link.href); return url.origin === location.origin && (url.searchParams.has('regions') || url.searchParams.has('regions[]')); }
    catch { return false; }
   });
   if (!heading) return;
   let overlay = card.querySelector(':scope > .lk-region-card-link');
   if (!overlay) {
    overlay = document.createElement('a'); overlay.className = 'lk-region-card-link';
    // The existing heading remains the single keyboard/screen-reader link.
    overlay.tabIndex = -1; overlay.setAttribute('aria-hidden', 'true'); card.append(overlay);
   }
   overlay.href = heading.href;
   ['target', 'rel'].forEach(name => heading.hasAttribute(name) ? overlay.setAttribute(name, heading.getAttribute(name)) : overlay.removeAttribute(name));
  });
 }
 if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
 if (window.jQuery) window.jQuery(window).on('elementor/frontend/init', () => {
  window.elementorFrontend?.hooks.addAction('frontend/element_ready/global', init);
 });
})();

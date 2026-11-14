(() => {
 'use strict';
 const save = document.querySelector('#add-menu-image_btn');
 if (!save) return;
 save.setAttribute('role', 'button');
 save.setAttribute('aria-label', 'Lưu menu ảnh');
 save.title = 'Lưu menu ảnh';
 save.tabIndex = 0;
 save.addEventListener('keydown', event => {
  if (event.key === 'Enter' || event.key === ' ') {
   event.preventDefault();
   save.click();
  }
 });
})();

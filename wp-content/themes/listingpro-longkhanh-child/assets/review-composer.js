(() => {
 'use strict';
 if (document.body.classList.contains('elementor-editor-active')) return;
 const words = ['', 'Rất tệ', 'Chưa tốt', 'Bình thường', 'Hài lòng', 'Tuyệt vời'];
 document.querySelectorAll('.lk-review-composer').forEach(form => {
  const textarea = form.querySelector('[name="review"]'), counter = form.querySelector('.lk-review-counter');
  const submit = form.querySelector('[type="submit"]');
  const select = form.querySelector('[name="place_id"]'), chosen = form.querySelector('.lk-review-chosen');
  const titleInput = form.querySelector('[name="review_title"]');
  function updateValidity() {
   if (submit) submit.disabled = !Array.from(form.elements).every(input => !input.willValidate || input.validity.valid) || Array.from(textarea.value.trim()).length < 10;
  }
  textarea.addEventListener('input', () => {counter.textContent = `${Array.from(textarea.value).length} ký tự (tối thiểu 10 ký tự)`; updateValidity();});
  form.addEventListener('input', updateValidity);
  form.addEventListener('change', updateValidity);
  form.querySelectorAll('.lk-star-row').forEach(row => {
   const group = row.querySelector('.lk-stars-input'), inputs = [...group.querySelectorAll('input')];
   let pointer = null;
   function choose(value) {
    const input = inputs[value - 1]; if (!input) return;
    input.checked = true; row.querySelector('output').textContent = words[value];
    input.dispatchEvent(new Event('change', {bubbles:true}));
   }
   function valueAt(event) {
    const boxes = inputs.map(input => input.parentElement.getBoundingClientRect());
    let nearest = 0, gap = Infinity;
    boxes.forEach((box,i) => {const distance = Math.abs(event.clientX - (box.left + box.width / 2)); if (distance < gap) {gap = distance; nearest = i;}});
    return nearest + 1;
   }
   inputs.forEach(input => input.addEventListener('change', () => {row.querySelector('output').textContent = words[Number(input.value)];}));
   group.addEventListener('pointerdown', event => {
    if (event.button !== 0) return;
    event.preventDefault(); pointer = event.pointerId; group.setPointerCapture(pointer);
    const value = valueAt(event); choose(value); inputs[value - 1].focus({preventScroll:true});
   });
   group.addEventListener('pointermove', event => {if (pointer === event.pointerId) choose(valueAt(event));});
   group.addEventListener('pointerup', event => {if (pointer === event.pointerId) {choose(valueAt(event)); pointer = null;}});
   group.addEventListener('pointercancel', () => {pointer = null;});
   group.addEventListener('lostpointercapture', () => {pointer = null;});
   group.addEventListener('click', event => {if (event.target.tagName !== 'INPUT') event.preventDefault();});
  });
  function showPlace() {
   if (!chosen) return;
   const place = (window.LK_SITE?.places || []).find(p => p.id === Number(select.value));
   chosen.replaceChildren(); chosen.hidden = !place; if (!place) return;
   const img = document.createElement('img'); img.src = place.image; img.alt = '';
   const copy = document.createElement('div'), title = document.createElement('strong'), address = document.createElement('p');
   title.textContent = place.title; address.textContent = place.address; copy.append(title,address); chosen.append(img,copy);
  }
  select.addEventListener('change',showPlace); showPlace();
  const fileInput = form.querySelector('[type="file"]'), error = form.querySelector('.lk-compose-error'), preview = form.querySelector('.lk-review-preview');
  let urls = [];
  function updatePhotos() {
   urls.forEach(url => URL.revokeObjectURL(url)); urls = []; preview.replaceChildren(); error.hidden = true; fileInput.setCustomValidity('');
   const files = Array.from(fileInput.files);
   const invalid = files.length > 5 ? 'Chọn tối đa 5 ảnh.' : files.some(file => file.size > 5 * 1024 * 1024) ? 'Mỗi ảnh cần nhỏ hơn hoặc bằng 5 MB.' : files.some(file => !['image/jpeg','image/png','image/webp'].includes(file.type)) ? 'Chọn ảnh JPG, PNG hoặc WebP.' : '';
   if (invalid) {error.textContent = invalid; error.hidden = false; fileInput.setCustomValidity(invalid);}
   files.forEach((file,index) => {
    const item = document.createElement('div'), remove = document.createElement('button'); item.className = 'lk-review-preview-item';
    if (['image/jpeg','image/png','image/webp'].includes(file.type)) {const img = document.createElement('img'), url = URL.createObjectURL(file); urls.push(url); img.src = url; img.alt = file.name; item.append(img);}
    else {const name = document.createElement('span'); name.textContent = file.name; item.append(name);}
    remove.type = 'button'; remove.textContent = '×'; remove.setAttribute('aria-label',`Bỏ ảnh ${file.name}`);
    remove.addEventListener('click', () => {const transfer = new DataTransfer(); files.filter((_,i) => i !== index).forEach(file => transfer.items.add(file)); fileInput.files = transfer.files; updatePhotos();});
    item.append(remove); preview.append(item);
   });
   updateValidity();
  }
  fileInput.addEventListener('change',updatePhotos);
  form.addEventListener('submit', event => {
   if (textarea.value.trim().length < 10) {event.preventDefault(); textarea.focus(); return;}
   if (submit) {submit.disabled = true; submit.textContent = 'Đang gửi đánh giá…';}
  });
  titleInput.addEventListener('input',updateValidity); updateValidity();
  window.addEventListener('pageshow',updateValidity);
  window.addEventListener('pagehide', () => urls.forEach(url => URL.revokeObjectURL(url)), {once:true});
 });
})();

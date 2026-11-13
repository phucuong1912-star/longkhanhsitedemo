(() => {
 'use strict';
 if (document.body.classList.contains('elementor-editor-active')) return;
 document.querySelectorAll('.lk-hours-editor').forEach(editor => {
  const days = [...editor.querySelectorAll('.lk-hours-day')];
  const error = editor.querySelector('.lk-hours-error');
  function refresh(day) {
   const mode = day.querySelector('[data-hours-mode]').value;
   const container = day.querySelector('.lk-hours-slots'); container.hidden = mode !== 'open';
   const slots = [...container.querySelectorAll('.lk-hours-slot')];
   slots.forEach((slot,index) => {
    slot.querySelectorAll('[data-hours-time]').forEach(input => {
     input.name = `${day.dataset.hoursRoot}[${input.dataset.hoursTime}][${index}]`;
     input.setAttribute('aria-label',`${input.dataset.hoursTime === 'open' ? 'Mở cửa' : 'Đóng cửa'} ${day.dataset.dayLabel} khung ${index + 1}`);
     input.disabled = mode !== 'open'; input.required = mode === 'open';
    });
    const remove = slot.querySelector('.lk-hours-remove');
    remove.disabled = slots.length === 1;
    remove.setAttribute('aria-label',`Xóa khung giờ ${day.dataset.dayLabel}`);
   });
   day.querySelector('.lk-hours-add').hidden = slots.length >= 3;
  }
  function intervals() {
   const result = [];
   days.forEach((day,index) => {
    const mode = day.querySelector('[data-hours-mode]').value;
    if (mode === '24h') result.push([index * 1440,(index + 1) * 1440]);
    if (mode !== 'open') return;
    day.querySelectorAll('.lk-hours-slot').forEach(slot => {
     const minutes = value => {const parts = value.split(':'); return Number(parts[0]) * 60 + Number(parts[1]);};
     const open = minutes(slot.querySelector('[data-hours-time="open"]').value);
     let close = minutes(slot.querySelector('[data-hours-time="close"]').value);
     if (open === close) {result.push([NaN,NaN]); return;}
     if (close < open) close += 1440;
     result.push([index * 1440 + open,index * 1440 + close]);
    });
   });
   return result;
  }
  editor.addEventListener('change',event => {const day = event.target.closest('.lk-hours-day'); if (day) refresh(day); error.hidden = true;});
  editor.addEventListener('click',event => {
   const day = event.target.closest('.lk-hours-day');
   if (event.target.closest('.lk-hours-add') && day) {
    if (day.querySelectorAll('.lk-hours-slot').length >= 3) return;
    const slot = day.querySelector('.lk-hours-slot').cloneNode(true);
    slot.querySelectorAll('input').forEach(input => input.value = '');
    day.querySelector('.lk-hours-add').before(slot); refresh(day); slot.querySelector('input').focus();
   }
   if (event.target.closest('.lk-hours-remove') && day && day.querySelectorAll('.lk-hours-slot').length > 1) {event.target.closest('.lk-hours-slot').remove(); refresh(day);}
   if (event.target.closest('.lk-hours-copy')) {
    const source = days[0], mode = source.querySelector('[data-hours-mode]').value;
    days.slice(1).forEach(target => {
     target.querySelector('[data-hours-mode]').value = mode;
     target.querySelectorAll('.lk-hours-slot').forEach(slot => slot.remove());
     source.querySelectorAll('.lk-hours-slot').forEach(slot => {
      const clone = slot.cloneNode(true);
      [...slot.querySelectorAll('input')].forEach((input,index) => clone.querySelectorAll('input')[index].value = input.value);
      target.querySelector('.lk-hours-add').before(clone);
     });
     refresh(target);
    });
   }
   error.hidden = true;
  });
  editor.closest('form')?.addEventListener('submit',event => {
   const values = intervals(), repeated = [...values,...values.map(([start,end]) => [start + 10080,end + 10080])].sort((a,b) => a[0] - b[0]);
   const invalid = values.some(([start,end]) => !Number.isFinite(start) || !Number.isFinite(end));
   const overlap = repeated.some((value,index) => index > 0 && value[0] < repeated[index - 1][1]);
   if (invalid || overlap) {
    event.preventDefault(); error.hidden = false;
    error.textContent = invalid ? 'Giờ mở và giờ đóng cần khác nhau. Nếu mở cả ngày, hãy chọn Mở 24 giờ.' : 'Các khung giờ đang trùng nhau, kể cả khung giờ qua nửa đêm. Vui lòng kiểm tra lại.';
    error.scrollIntoView({block:'center'});
   }
  });
  days.forEach(refresh);
 });
})();

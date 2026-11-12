(() => {
 'use strict';
 if(document.body.classList.contains('elementor-editor-active'))return;
 const form=document.querySelector('.lk-review-composer');if(!form)return;
 const textarea=form.querySelector('[name="review"]'),counter=form.querySelector('.lk-review-counter');
 textarea.addEventListener('input',()=>counter.textContent=`${Array.from(textarea.value).length} ký tự (tối thiểu 10 ký tự)`);
 const words=['','Rất tệ','Chưa tốt','Bình thường','Hài lòng','Tuyệt vời'];
 form.querySelectorAll('.lk-star-row input').forEach(input=>input.addEventListener('change',()=>input.closest('.lk-star-row').querySelector('output').textContent=words[Number(input.value)]));
 const select=form.querySelector('[name="place_id"]'),chosen=form.querySelector('.lk-review-chosen');
 function showPlace(){
  const place=(window.LK_SITE?.places||[]).find(p=>p.id===Number(select.value));chosen.replaceChildren();chosen.hidden=!place;if(!place)return;
  const img=document.createElement('img');img.src=place.image;img.alt='';
  const copy=document.createElement('div'),title=document.createElement('strong'),address=document.createElement('p');title.textContent=place.title;address.textContent=place.address;copy.append(title,address);chosen.append(img,copy);
 }select.addEventListener('change',showPlace);showPlace();
 const fileInput=form.querySelector('[type="file"]'),error=form.querySelector('.lk-compose-error'),preview=form.querySelector('.lk-review-preview');let urls=[];
 fileInput.addEventListener('change',()=>{
  urls.forEach(url=>URL.revokeObjectURL(url));urls=[];preview.replaceChildren();error.hidden=true;fileInput.setCustomValidity('');
  const files=Array.from(fileInput.files);const invalid=files.length>5?'Chọn tối đa 5 ảnh.':files.some(f=>f.size>5*1024*1024)?'Mỗi ảnh cần nhỏ hơn hoặc bằng 5 MB.':files.some(f=>!['image/jpeg','image/png','image/webp'].includes(f.type))?'Chọn ảnh JPG, PNG hoặc WebP.':'';
  if(invalid){error.textContent=invalid;error.hidden=false;fileInput.setCustomValidity(invalid);return;}
  files.forEach(file=>{const img=document.createElement('img'),url=URL.createObjectURL(file);urls.push(url);img.src=url;img.alt=file.name;preview.append(img);});
 });
 window.addEventListener('pagehide',()=>urls.forEach(url=>URL.revokeObjectURL(url)),{once:true});
})();

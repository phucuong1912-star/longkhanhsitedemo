(() => {
 'use strict';
 if(document.body.classList.contains('elementor-editor-active'))return;
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 const desktop=matchMedia('(min-width: 992px)');
 document.querySelectorAll('[data-lk-typewriter]').forEach(heading=>{
  if(reduced.matches)return;
  const first=heading.textContent.trim();
  const words=[first,...heading.dataset.lkTypewriter.split('\n').map(s=>s.trim()).filter(Boolean)];
  const reader=document.createElement('span');reader.className='lk-sr';reader.textContent=first;
  const visual=document.createElement('span');visual.setAttribute('aria-hidden','true');
  const cursor=document.createElement('span');cursor.className='lk-typewriter-cursor';cursor.textContent='|';cursor.setAttribute('aria-hidden','true');
  heading.replaceChildren(reader,visual,cursor);
  let index=0,characters=0,deleting=false,timer;
  function tick(){
   if(document.hidden){timer=setTimeout(tick,500);return;}
   const word=Array.from(words[index]);
   characters+=deleting?-1:1;visual.textContent=word.slice(0,characters).join('');
   let delay=deleting?50:120+Math.random()*40;
   if(!deleting&&characters===word.length){deleting=true;delay=1500;}
   else if(deleting&&characters===0){deleting=false;index=(index+1)%words.length;}
   timer=setTimeout(tick,delay);
  }
  tick();
  reduced.addEventListener('change',()=>{if(reduced.matches){clearTimeout(timer);visual.textContent=first;cursor.hidden=true;}});
 });

 document.querySelectorAll('.lk-carousel,.lk-banners').forEach(section=>{
  const track=section.querySelector(':scope > .elementor-container');if(!track)return;
  const originals=[...track.children].filter(el=>el.classList.contains('elementor-column'));
  if(originals.length<2)return;
  const promo=section.classList.contains('lk-banners');
  const interval=promo?3000:section.classList.contains('lk-region-row')?5000:4000;
  let timer,frame,settle,hover=false,drag=false,focused=false,animating=false,cycle=0,start=0;
  let gesture=null,moved=false;
  function cancel(){clearTimeout(timer);}
  function enabled(){return !reduced.matches&&!document.hidden&&!hover&&!drag&&!focused&&!(promo?desktop.matches:!desktop.matches);}
  function schedule(){cancel();if(enabled())timer=setTimeout(()=>move(1),interval);}
  function clone(node){
   const copy=node.cloneNode(true);copy.dataset.lkClone='true';copy.setAttribute('aria-hidden','true');
   [copy,...copy.querySelectorAll('[id]')].forEach(el=>el.removeAttribute('id'));
   copy.querySelectorAll('a,button,input,[tabindex]').forEach(el=>el.setAttribute('tabindex','-1'));
   return copy;
  }
  function looped(){return !promo||!desktop.matches;}
  function measure(){
   cancelAnimationFrame(frame);animating=false;cancel();
   track.querySelectorAll(':scope > [data-lk-clone]').forEach(el=>el.remove());
   track.style.scrollSnapType='none';
   if(looped()){
    const before=document.createDocumentFragment(),after=document.createDocumentFragment();
    originals.forEach(el=>{before.append(clone(el));after.append(clone(el));});
    track.prepend(before);track.append(after);
    start=originals[0].offsetLeft-track.firstElementChild.offsetLeft;
    cycle=track.querySelector(':scope > [data-lk-clone]:last-child').offsetLeft-originals.at(-1).offsetLeft;
    track.scrollLeft=start;
   }else{start=0;cycle=0;track.scrollLeft=0;}
   requestAnimationFrame(()=>{track.style.scrollSnapType='';schedule();});
  }
  function normalize(){
   if(!cycle)return;
   let left=track.scrollLeft;
   while(left<start-1)left+=cycle;
   while(left>=start+cycle-1)left-=cycle;
   if(Math.abs(left-track.scrollLeft)>1){track.style.scrollSnapType='none';track.scrollLeft=left;requestAnimationFrame(()=>track.style.scrollSnapType='');}
  }
  function move(direction){
   if(animating)return;cancel();normalize();
   const step=originals[1].offsetLeft-originals[0].offsetLeft;
   if(!step)return;
   const from=track.scrollLeft,to=from+direction*step;
   if(reduced.matches){track.scrollLeft=to;normalize();return;}
   const began=performance.now();animating=true;track.style.scrollSnapType='none';
   function draw(now){
    const t=Math.min((now-began)/500,1);track.scrollLeft=from+(to-from)*(1-Math.pow(1-t,5));
    if(t<1)frame=requestAnimationFrame(draw);
    else{animating=false;normalize();track.style.scrollSnapType='';schedule();}
   }frame=requestAnimationFrame(draw);
  }
  let nav=section.querySelector('.lk-carousel-arrows');
  if(!promo&&!nav){nav=document.createElement('div');nav.className='lk-carousel-arrows';nav.innerHTML='<button aria-label="Xem mục trước">‹</button><button aria-label="Xem mục tiếp theo">›</button>';section.append(nav);}
  nav?.querySelectorAll('button').forEach((button,i)=>button.addEventListener('click',()=>move(i?1:-1)));
  section.addEventListener('mouseenter',()=>{hover=true;cancel();});
  section.addEventListener('mouseleave',()=>{hover=false;schedule();});
  section.addEventListener('focusin',()=>{focused=true;cancel();});
  section.addEventListener('focusout',()=>{focused=false;schedule();});
  track.addEventListener('pointerdown',e=>{drag=true;moved=false;cancel();cancelAnimationFrame(frame);animating=false;gesture={x:e.clientX,left:track.scrollLeft,type:e.pointerType};});
  track.addEventListener('pointermove',e=>{
   if(!gesture||gesture.type!=='mouse')return;
   const delta=e.clientX-gesture.x;if(Math.abs(delta)>8)moved=true;
   if(moved){e.preventDefault();track.style.scrollSnapType='none';track.scrollLeft=gesture.left-delta;}
  });
  function release(){if(!gesture)return;gesture=null;drag=false;track.style.scrollSnapType='';clearTimeout(settle);settle=setTimeout(()=>{normalize();schedule();},160);}
  window.addEventListener('pointerup',release);track.addEventListener('pointercancel',release);
  track.addEventListener('dragstart',e=>e.preventDefault());
  track.addEventListener('click',e=>{if(moved){e.preventDefault();e.stopPropagation();moved=false;}},true);
  track.addEventListener('scroll',()=>{if(animating||drag)return;clearTimeout(settle);settle=setTimeout(()=>{normalize();schedule();},160);},{passive:true});
  document.addEventListener('visibilitychange',schedule);reduced.addEventListener('change',schedule);
  desktop.addEventListener('change',measure);
  let resizeTimer;window.addEventListener('resize',()=>{clearTimeout(resizeTimer);resizeTimer=setTimeout(measure,180);});
  measure();
 });

 const tabs=document.querySelector('.lk-detail-tabs');
 if(tabs){
  const update=()=>{tabs.classList.toggle('is-visible',scrollY>200);};
  window.addEventListener('scroll',update,{passive:true});update();
  const anchors=[...tabs.querySelectorAll('a[href^="#"]')];
  const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{
   if(entry.isIntersecting)anchors.forEach(a=>{if(a.hash==='#'+entry.target.id)a.setAttribute('aria-current','location');else a.removeAttribute('aria-current');});
  }),{rootMargin:'-20% 0px -65% 0px'});
  anchors.forEach(a=>{const target=document.getElementById(a.hash.slice(1));if(target)observer.observe(target);});
 }
})();

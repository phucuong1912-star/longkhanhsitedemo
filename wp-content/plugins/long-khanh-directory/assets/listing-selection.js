(() => {
 'use strict';
 function select(tab,focus=false){
  const root=tab.closest('[data-lk-curated-list]');if(!root)return;
  root.querySelectorAll('[data-lk-curated-tab]').forEach(button=>{
   const active=button===tab;button.setAttribute('aria-selected',String(active));button.tabIndex=active?0:-1;
   const panel=[...root.querySelectorAll('[role=tabpanel]')].find(p=>p.id===button.getAttribute('aria-controls'));if(panel)panel.hidden=!active;
  });
  if(focus)tab.focus();
  window.dispatchEvent(new Event('resize'));
 }
 document.addEventListener('click',event=>{const tab=event.target.closest('[data-lk-curated-tab]');if(tab)select(tab);});
 document.addEventListener('keydown',event=>{
  const tab=event.target.closest('[data-lk-curated-tab]');if(!tab||!['ArrowLeft','ArrowRight','Home','End'].includes(event.key))return;
  const tabs=[...tab.closest('[role=tablist]').querySelectorAll('[data-lk-curated-tab]')],i=tabs.indexOf(tab);
  const next=event.key==='Home'?0:event.key==='End'?tabs.length-1:(i+(event.key==='ArrowRight'?1:-1)+tabs.length)%tabs.length;
  event.preventDefault();select(tabs[next],true);
 });
})();

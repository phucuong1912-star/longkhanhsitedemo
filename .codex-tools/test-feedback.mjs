import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import test from 'node:test';

const siteSource = readFileSync(new URL('../wp-content/themes/listingpro-longkhanh-child/assets/site.js',import.meta.url),'utf8');
const resultsSource = readFileSync(new URL('../wp-content/themes/listingpro-longkhanh-child/assets/directory-results.js',import.meta.url),'utf8');
function directory(geolocation) {
 const values = new Map();
 const storage = {getItem:key=>values.get(key) || null,setItem:(key,value)=>values.set(key,value)};
 const document = {body:{classList:{contains:()=>false}},querySelectorAll:()=>[],querySelector:()=>null,addEventListener:()=>{}};
 const window = {LK_SITE:{places:[]}};
 const context = {document,window,navigator:{geolocation},localStorage:storage,sessionStorage:storage,location:{href:'http://localhost/search/'},URL,JSON,Intl,Date,Number,Math,console};
 vm.runInNewContext(siteSource,context);
 return window.LK_DIRECTORY;
}
test('Haversine distances and invalid coordinates',()=>{
 const api=directory();
 assert.equal(api.distance({lat:10,lng:107},{lat:10,lng:107}),0);
 assert.ok(Math.abs(api.distance({lat:0,lng:0},{lat:1,lng:0})-111.195)<0.01);
 assert.ok(Math.abs(api.distance({lat:0,lng:179},{lat:0,lng:-179})-222.39)<0.01);
 assert.equal(api.hasCoordinates({lat:null,lng:107}),false);
 assert.equal(api.hasCoordinates({lat:91,lng:107}),false);
 assert.equal(api.hasCoordinates({lat:0,lng:0}),true);
});
test('location permission, timeout, unsupported browser and short-lived cache',async()=>{
 let requests=0;
 const api=directory({getCurrentPosition:success=>{requests++;success({coords:{latitude:10.94,longitude:107.24}});}});
 assert.equal((await api.locate()).lat,10.94); await api.locate(); assert.equal(requests,1);
 await assert.rejects(directory({getCurrentPosition:(_,fail)=>fail({code:1})}).locate(),/chưa cho phép/);
 await assert.rejects(directory({getCurrentPosition:(_,fail)=>fail({code:3})}).locate(),/quá thời gian/);
 await assert.rejects(directory().locate(),/chưa hỗ trợ/);
});
function element() {return {hidden:false,textContent:'',innerHTML:'',children:[],classList:{toggle(){}},style:{setProperty(){}},addEventListener(){},append(...items){this.children.push(...items);},replaceChildren(){this.children=[];},setAttribute(){}};}
async function nearbyResults(reject=false,preset=false) {
 const api=directory(); const status=element(),cards=element(),pagination=element(),count=element(),chip=element();
 const min={value:'0',max:'300000',addEventListener(){}},max={value:'300000',max:'300000',addEventListener(){}};
 const output=element(),track=element(),sort={value:'nearest',addEventListener(){}};
 const form={elements:{min_price:min,max_price:max,nearby:{value:'me'}},querySelector:selector=>selector==='.lk-price-output'?output:track,addEventListener(){}};
 const places=Array.from({length:12},(_,i)=>({id:i+1,lat:10+(12-i)/100,lng:107}));
 places.push({id:13,lat:null,lng:null});
 const layout={dataset:{nearby:'1',resultIds:JSON.stringify(places.map(p=>p.id))},querySelector:selector=>({'.lk-location-status':status,'.lk-results-cards':cards,'.lk-pagination':pagination,'.lk-result-count strong':count,'.lk-nearby-chip':chip})[selector]};
 if(preset){layout.dataset.originId='99';places.push({id:99,lat:10,lng:107,title:'Địa điểm đang xem'});}
 let rendered=[],locationRequests=0;
 Object.assign(api,{locate:()=>{locationRequests++;return reject?Promise.reject(new Error('Không có quyền vị trí')):Promise.resolve({lat:10,lng:107});},card:place=>{rendered.push(place.id);return `<article>${place.id}</article>`;},savedState(){}});
 const document={querySelector:selector=>({'.lk-results-layout':layout,'#lk-filter':form,'.lk-sort select':sort})[selector],createElement:()=>element()};
 vm.runInNewContext(resultsSource,{document,window:{LK_DIRECTORY:api,LK_SITE:{places}},location:{href:'http://localhost/search/?nearby=me'},URL,JSON,Number,Math,matchMedia:()=>({matches:true}),history:{replaceState(){}}});
 await new Promise(resolve=>setImmediate(resolve));
 return {layout,status,cards,count,pagination,rendered,chip,sort,locationRequests};
}
test('nearby ranks the entire filtered set before pagination and excludes missing coordinates',async()=>{
 const result=await nearbyResults();
 assert.equal(result.rendered[0],12); assert.equal(result.rendered.length,10);
 assert.equal(result.count.textContent,12); assert.equal(result.pagination.children.length,2);
 assert.match(result.status.textContent,/1 địa điểm chưa có tọa độ/);
 assert.equal(JSON.parse(result.layout.dataset.mapIds).length,12);
});
test('denied location preserves ordinary results and removes misleading nearby state',async()=>{
 const result=await nearbyResults(true);
 assert.equal(result.cards.hidden,false); assert.equal(result.layout.dataset.nearby,'0');
 assert.equal(result.chip.hidden,true); assert.equal(result.sort.value,'relevance');
 assert.match(result.status.textContent,/chưa được sắp theo khoảng cách/);
});

test('star rating supports pointer dragging and native radio changes',()=>{
 const source=readFileSync(new URL('../wp-content/themes/listingpro-longkhanh-child/assets/review-composer.js',import.meta.url),'utf8');
 const listeners={}, output={textContent:''}; let selected=5;
 const inputs=Array.from({length:5},(_,i)=>{
  const handlers={}; return {value:String(i+1),parentElement:{getBoundingClientRect:()=>({left:i*38,width:30})},
   get checked(){return selected===i+1;},set checked(value){if(value)selected=i+1;},
   addEventListener:(type,handler)=>{handlers[type]=handler;},dispatchEvent:event=>handlers[event.type]?.(),focus(){}};
 });
 const group={querySelectorAll:()=>inputs,addEventListener:(type,handler)=>{listeners[type]=handler;},setPointerCapture(){}};
 const row={querySelector:selector=>selector==='.lk-stars-input'?group:output};
 const noop={addEventListener(){}};
 const form={querySelectorAll:()=>[row],querySelector:selector=>({'[name="review"]':{...noop,value:''},'.lk-review-counter':{},'[type="submit"]':null,'[name="place_id"]':noop,'.lk-review-chosen':null,'[name="review_title"]':noop,'[type="file"]':noop,'.lk-compose-error':{},'.lk-review-preview':{}})[selector],addEventListener(){}};
 vm.runInNewContext(source,{document:{body:{classList:{contains:()=>false}},querySelectorAll:()=>[form]},window:{addEventListener(){}},Event:class{constructor(type){this.type=type;}}});
 const event=x=>({button:0,pointerId:1,clientX:x,preventDefault(){}});
 listeners.pointerdown(event(91)); assert.equal(selected,3); assert.equal(output.textContent,'Bình thường');
 listeners.pointermove(event(167)); assert.equal(selected,5);
 listeners.pointerup(event(53)); assert.equal(selected,2); assert.equal(output.textContent,'Chưa tốt');
 inputs[0].checked=true; inputs[0].dispatchEvent({type:'change'}); assert.equal(output.textContent,'Rất tệ');
});


test('listing-origin nearby uses the listing coordinates without requesting visitor location',async()=>{
 const result=await nearbyResults(true,true);
 assert.equal(result.locationRequests,0);
 assert.equal(result.rendered[0],12);
 assert.equal(result.rendered.includes(99),false);
 assert.match(result.status.textContent,/Địa điểm đang xem/);
 assert.equal(JSON.parse(result.layout.dataset.mapIds).length,12);
});


test('share sheet stays usable when native sharing is cancelled or unavailable', async()=>{
 const source=readFileSync(new URL('../wp-content/themes/listingpro-longkhanh-child/assets/feedback2.js',import.meta.url),'utf8');
 const listeners={},opener={focused:false,focus(){this.focused=true;}};
 let sheet,shared=0,copied='';
 const create=tag=>({tag,dataset:{},events:{},hidden:false,open:false,children:[],textContent:'',className:'',setAttribute(){},addEventListener(name,fn){this.events[name]=fn;},append(child){this.children.push(child);},showModal(){this.open=true;},close(){this.open=false;this.events.close?.();},querySelector(selector){return this.parts?.[selector]||null;}});
 const classes=new Set();
 const document={body:{classList:{contains:()=>false},append(el){sheet=el;}},documentElement:{classList:{add:v=>classes.add(v),remove:v=>classes.delete(v)}},querySelector:selector=>selector==='dialog[open]'&&sheet?.open?sheet:null,querySelectorAll:()=>[],addEventListener(type,fn){(listeners[type]??=[]).push(fn);},createElement(tag){const el=create(tag);if(tag==='dialog'){const link=create('a'),copy=create('button'),links=create('div'),status=create('p');links.append(copy);el.parts={'a':link,'.lk-share-links':links,'.lk-share-links button':copy,'p':status};const query=el.querySelector;el.querySelector=selector=>selector==='[data-system-share]'?links.children.find(c=>c.dataset.systemShare!==undefined)||null:query.call(el,selector);}return el;}};
 const canonical='https://example.test/dia-diem/ca-phe-me-bap/';
 const navigator={share:async()=>{shared++;throw Object.assign(new Error('cancel'),{name:'AbortError'});},clipboard:{writeText:async value=>{copied=value;}}};
 vm.runInNewContext(source,{document,window:{LK_SITE:{base:'https://example.test/',page:1,places:[{id:1,title:'Cà Phê Mẹ Bắp',url:canonical}]}},location:{href:canonical+'?tracking=1'},navigator,URL,Map,Number,String,Math,JSON});
 const event={target:{closest:selector=>selector==='[data-place-share]'?opener:null}};
 for(const listener of listeners.click)await listener(event);
 assert.equal(sheet.open,true);assert.equal(shared,0);assert.ok(classes.has('lk-dialog-open'));
 assert.equal(sheet.parts.a.href,'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(canonical));
 await sheet.parts['.lk-share-links button'].onclick();assert.equal(copied,canonical);
 const other=sheet.parts['.lk-share-links'].children.find(c=>c.dataset.systemShare!==undefined);
 await other.events.click();assert.equal(shared,1);assert.equal(sheet.open,true);
 navigator.clipboard.writeText=async()=>{throw new Error('denied');};await sheet.parts['.lk-share-links button'].onclick();assert.equal(sheet.parts.p.textContent,canonical);
 sheet.close();assert.equal(opener.focused,true);assert.equal(classes.has('lk-dialog-open'),false);
});

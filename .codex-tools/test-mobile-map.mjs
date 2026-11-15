import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

const source=readFileSync(new URL('../wp-content/themes/listingpro-longkhanh-child/assets/feedback2.js',import.meta.url),'utf8');
function mapView({reduced=false,vertical=false,ids=[1,2,3]}={}){
 const classes=()=>{const values=new Set();return {contains:v=>values.has(v),toggle(v,on){on?values.add(v):values.delete(v);},add:v=>values.add(v),remove:v=>values.delete(v)};};
 const element=()=>({dataset:{},attrs:{},classList:classes(),setAttribute(k,v){this.attrs[k]=v;},addEventListener(){},append(){},focus(){},getBoundingClientRect:()=>({left:0,top:0,width:300,height:170})});
 const state={cards:new Map(),pins:new Map(),markers:new Map(),moves:[],scrolls:[]};
 const list={...element(),scrollLeft:0,scrollTop:0,clientWidth:375,clientHeight:190,scrollTo:options=>state.scrolls.push(options)};
 Object.defineProperty(list,'innerHTML',{set(html){state.cards.clear();for(const [,id] of html.matchAll(/data-map-card="(\d+)"/g)){const card=element(),button=element();card.dataset.mapCard=id;card.clientWidth=320;card.clientHeight=170;card.closest=()=>list;card.querySelector=selector=>selector==='[data-map-place]'?button:null;state.cards.set(Number(id),card);}}});
 const layout={...element(),dataset:{mapIds:JSON.stringify(ids)}};
 const mapLayout=element(),dialog={...element(),open:false,showModal(){this.open=true;},querySelector:selector=>({'.lk-map-list':list,'.lk-map-layout':mapLayout})[selector]||null};
 const map={zoom:13,setView(center,zoom,options){this.zoom=zoom;state.moves.push({type:'setView',center,zoom,options});return this;},getZoom(){return this.zoom;},panTo(center,options){state.moves.push({type:'panTo',center,options});return this;},removeLayer(){},invalidateSize(){}};
 // This deliberately models the site's Leaflet 0.7/Mapbox API: no getElement,
 // flyTo or map.stop. Newer Leaflet-only calls must not break production.
 const L={map:()=>map,tileLayer:()=>({addTo(){}}),divIcon:options=>options,control:{zoom:()=>({addTo(){}})},Control:{extend:()=>class{addTo(){}}},marker(position,options){
  const id=Number(options.icon.html.match(/data-map-pin="(\d+)"/)[1]);
  const button=element(),icon=element();button.dataset.mapPin=String(id);button.closest=()=>icon;
  state.pins.set(id,{button,icon});
  const marker={getLatLng:()=>position,setZIndexOffset(value){this.z=value;},addTo(){state.markers.set(id,this);return this;},on(){},bindPopup(){throw new Error('Information must stay outside the marker canvas');}};
  return marker;
 }};
 const handlers=[];
 const document={body:{classList:{contains:()=>false}},documentElement:{classList:classes()},createElement:element,
  addEventListener(type,fn,capture){if(type==='click'&&capture)handlers.push(fn);},
  querySelector(selector){if(selector==='#lk-map')return dialog;if(selector==='.lk-results-layout')return layout;const pin=selector.match(/^#lk-map \[data-map-pin="(\d+)"\]$/);if(pin)return state.pins.get(Number(pin[1]))?.button;const card=selector.match(/^#lk-map \[data-map-card="(\d+)"\]$/);if(card)return state.cards.get(Number(card[1]));return null;},
  querySelectorAll:selector=>selector==='#lk-map [data-map-card]'?[...state.cards.values()]:[]};
 const places=[{id:1,title:'Một',lat:10,lng:107},{id:2,title:'Hai',lat:11,lng:108},{id:3,title:'Chưa có tọa độ',lat:null,lng:null}];
 const window={LK_SITE:{base:'https://example.test/',places},LK_DIRECTORY:{hasCoordinates:p=>Number.isFinite(p.lat)&&Number.isFinite(p.lng)},L};
 vm.runInNewContext(source,{document,window,L,location:{href:'https://example.test/search/',pathname:'/search/'},URL,Map,Number,String,Math,JSON,requestAnimationFrame:fn=>fn(),matchMedia:()=>({matches:reduced}),getComputedStyle:()=>({flexDirection:vertical?'column':'row'})});
 const click=(kind,id)=>{const trigger={dataset:{[kind]:String(id??'')},hasAttribute:name=>name===({map:'data-map',mapPin:'data-map-pin',mapPlace:'data-map-place'})[kind],focus(){}};for(const fn of handlers)fn({target:{closest:selector=>selector==='[data-map], [data-map-place], [data-map-pin]'?trigger:null},preventDefault(){},stopImmediatePropagation(){}});};
 return {...state,open:id=>click('map',id),selectCard:id=>click('mapPlace',id),selectPin:id=>click('mapPin',id)};
}
test('selection is exclusive, resets the previous pin and centers the selected location',()=>{
 const view=mapView();view.open(1);
 assert.equal(view.pins.size,2);assert.equal(view.pins.get(1).icon.classList.contains('is-selected'),true);
 view.selectCard(2);
 assert.equal(view.pins.get(1).icon.classList.contains('is-selected'),false);
 assert.equal(view.pins.get(1).button.attrs['aria-pressed'],'false');
 assert.equal(view.pins.get(2).icon.classList.contains('is-selected'),true);
 assert.equal(view.markers.get(2).z,1000);assert.equal(view.cards.get(2).classList.contains('is-active'),true);
 assert.deepEqual(Array.from(view.moves.at(-1).center),[11,108]);assert.equal(view.moves.at(-1).type,'panTo');
 view.selectPin(1);assert.deepEqual(Array.from(view.moves.at(-1).center),[10,107]);assert.ok('left' in view.scrolls.at(-1));
});
test('reduced motion centers without animation and desktop scroll stays inside the card panel',()=>{
 const view=mapView({reduced:true,vertical:true});view.open(1);view.selectPin(2);
 assert.equal(view.moves.at(-1).type,'setView');assert.equal(view.moves.at(-1).options.animate,false);
 assert.ok('top' in view.scrolls.at(-1));assert.equal(view.scrolls.at(-1).behavior,'auto');
});
test('map respects filtered result IDs, skips missing coordinates and tolerates invalid selections',()=>{
 const view=mapView({ids:[2,3]});view.open(1);
 assert.deepEqual([...view.markers.keys()],[2]);assert.equal(view.cards.size,1);
 const moves=view.moves.length;view.selectPin(999);assert.equal(view.moves.length,moves);
});
test('empty filtered results have no selected marker or stale information card',()=>{
 const view=mapView({ids:[]});view.open();assert.equal(view.markers.size,0);assert.equal(view.cards.size,0);
 assert.deepEqual(Array.from(view.moves.at(-1).center),[10.9398,107.2446]);
});

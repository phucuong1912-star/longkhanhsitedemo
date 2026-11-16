(($) => {
 'use strict';
 const places=window.LK_LISTING_PICKER?.places||{};
 function category(select){return $(select).closest('.elementor-repeater-fields').find('select[data-setting="tab_category"]').val()||'';}
 function options(select,prune=false){
  const input=$(select),selected=input.val()||[],slug=category(select);
  const allowed=Object.keys(places).filter(key=>places[key].categories.includes(slug));
  const retained=prune?selected.filter(key=>allowed.includes(key)):selected;
  input.empty();
  // Put selected options first in saved order, then the remaining choices.
  [...new Set([...retained,...allowed])].forEach(key=>{
   const valid=allowed.includes(key),title=places[key]?.title||key;
   const option=new Option(title+(valid?'':' (không còn thuộc danh mục)'),key,false,retained.includes(key));
   option.disabled=!valid;select.add(option);
  });
  if(prune)input.trigger('change');
 }
 $(document).on('select2:opening','select[data-setting="listing_slugs"]',function(){options(this);});
 $(document).on('select2:select select2:clear','select[data-setting="tab_category"]',function(){
  $(this).closest('.elementor-repeater-fields').find('select[data-setting="listing_slugs"]').each(function(){options(this,true);});
 });
 $(document).on('select2:select','select[data-setting="listing_slugs"]',function(event){
  const input=$(this),key=event.params.data.id;
  input.find('option').filter(function(){return this.value===key;}).detach().appendTo(input);
  input.trigger('change');
 });
})(jQuery);

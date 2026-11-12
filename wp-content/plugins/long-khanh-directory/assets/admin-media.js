jQuery(function($){
 'use strict';
 $(document).on('click','.lk-media-choose',function(){
  const field=$(this).closest('.lk-media-field'),input=field.find('input');
  function commit(selection){
   const items=selection.toJSON();input.val(items.map(item=>item.id).join(','));
   const preview=field.find('.lk-media-preview').empty();
   items.forEach(item=>$('<img>',{src:item.sizes?.thumbnail?.url||item.url,alt:item.alt||'',css:{width:80,height:80,objectFit:'cover'}}).appendTo(preview));
  }
  const ids=input.val().split(',').map(Number).filter(Boolean);
  if(ids.length&&wp.media.gallery){
   const gallery=wp.media.gallery.edit('[gallery ids="'+ids.join(',')+'"]');
   gallery.on('update',commit);return;
  }
  const frame=wp.media({title:$(this).data('title'),button:{text:'Dùng các ảnh đã chọn'},library:{type:'image'},multiple:'add'});
  frame.on('open',function(){input.val().split(',').filter(Boolean).forEach(id=>frame.state().get('selection').add(wp.media.attachment(id)));});
  frame.on('select',()=>commit(frame.state().get('selection')));frame.open();
 });
 $(document).on('click','.lk-media-clear',function(){const field=$(this).closest('.lk-media-field');field.find('input').val('');field.find('.lk-media-preview').empty();});
});

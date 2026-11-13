<?php
if(!defined('ABSPATH'))exit;
// ListingPro's legacy fields use the classic meta-box workflow. Keep listing
// data in that editor; Elementor remains the editor for reusable layouts.
add_filter('use_block_editor_for_post_type',function($enabled,$type){return $type==='listing'?false:$enabled;},20,2);
add_action('add_meta_boxes_listing',function($post){
 if(!get_post_meta($post->ID,'_lk_managed',true))return;
 // Avoid two editors submitting different values for the same serialized
 // ListingPro fields. Its original box is kept on unrelated legacy listings.
 remove_meta_box('listing_meta_settings','listing','normal');
 remove_meta_box('postcustom','listing','normal');
 remove_meta_box('lk-data','listing','normal');
 add_meta_box('lk-data','Thông tin địa điểm Long Khánh','lk_meta_box','listing','normal','high');
 global $wp_meta_boxes;
 $boxes=&$wp_meta_boxes['listing']['normal']['high'];
 if(is_array($boxes))$boxes=array_intersect_key($boxes,array_flip(array('lk-data')))+$boxes;
},99);
add_action('save_post_listing',function($id){
 if(!current_user_can('edit_post',$id)||empty($_POST['lk_data_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lk_data_nonce'])),'lk_save_data'))return;
 // That vendor callback expects its legacy form and deletes the Features
 // taxonomy when the form is absent. WordPress saves our taxonomy box itself.
 remove_action('save_post','listingpro_update_features_in_list');
},1);
add_action('admin_post_lk_template_preview',function(){
 if(!current_user_can('edit_pages'))wp_die('Bạn không có quyền sửa template.');
 check_admin_referer('lk_template_preview');$id=absint($_POST['listing_id']??0);
 if(get_post_type($id)==='listing'&&get_post_status($id)==='publish')update_option('lk_listing_preview',$id);
 wp_safe_redirect(admin_url('admin.php?page=lk-directory'));exit;
});
add_action('admin_bar_menu',function($bar){
 if(!is_singular('listing')||!lk_is_directory_listing(get_queried_object_id())||!current_user_can('edit_post',get_queried_object_id()))return;
 $bar->remove_node('elementor_edit_page');
 $bar->add_node(array('id'=>'lk-edit-shared','title'=>'Sửa template địa điểm','href'=>admin_url('post.php?post='.get_option('lk_template_listing').'&action=elementor')));
},999);

add_action('add_meta_boxes_listing',function($post){
 if(!get_post_meta($post->ID,'_lk_managed',true)&&$post->post_status!=='auto-draft')return;
 add_meta_box('lk-listing-media','Ảnh địa điểm và menu','lk_listing_media_box','listing','normal','high');
});
function lk_listing_media_box($post){
 wp_nonce_field('lk_listing_media','lk_media_nonce');
 echo '<p>Ảnh đại diện được chọn ở hộp Ảnh đại diện. Ảnh và menu bên dưới tự hiển thị trong template chung.</p>';
 foreach(array('photo'=>'Ảnh địa điểm','menu'=>'Ảnh menu') as $view=>$label){
  $media=lk_listing_media($post->ID,$view);
  echo '<div class="lk-media-field" style="margin-bottom:20px"><h4>'.esc_html($label).'</h4><input type="hidden" name="lk_media['.$view.']" value="'.esc_attr(implode(',',array_column($media,'id'))).'"><div class="lk-media-preview" style="display:flex;gap:8px;flex-wrap:wrap;margin:10px 0">';
  foreach($media as $m)echo wp_get_attachment_image($m['id'],'thumbnail',false,array('style'=>'width:80px;height:80px;object-fit:cover'));
  echo '</div><button type="button" class="button lk-media-choose" data-title="'.esc_attr($label).'">Chọn / sắp xếp ảnh</button> <button type="button" class="button-link-delete lk-media-clear">Bỏ chọn</button></div>';
 }
}
add_action('admin_enqueue_scripts',function(){
 $screen=get_current_screen();if(!$screen||$screen->post_type!=='listing')return;
 lk_enqueue_location_assets();
 wp_enqueue_media();wp_enqueue_script('lk-admin-media',LK_URL.'assets/admin-media.js',array('jquery','media-views','media-editor'),filemtime(LK_DIR.'assets/admin-media.js'),true);
 wp_enqueue_style('lk-listing-fields',LK_URL.'assets/listing-fields.css',array(),filemtime(LK_DIR.'assets/listing-fields.css'));
 wp_enqueue_script('lk-listing-fields',LK_URL.'assets/listing-fields.js',array(),filemtime(LK_DIR.'assets/listing-fields.js'),true);
});
add_action('admin_notices',function(){if(!empty($_GET['lk_hours_error']))echo '<div class="notice notice-error"><p>Lịch mở cửa chưa được lưu. Vui lòng kiểm tra giờ của từng thứ, tránh khung giờ trùng nhau và chọn Mở 24 giờ nếu mở cả ngày.</p></div>';});
add_action('save_post_listing',function($id){
 if(wp_is_post_autosave($id)||wp_is_post_revision($id)||!current_user_can('edit_post',$id)||empty($_POST['lk_media_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lk_media_nonce'])),'lk_listing_media'))return;
 foreach(array('photo'=>'gallery_image_ids','menu'=>'_lk_menu_ids') as $view=>$key){
  if(!isset($_POST['lk_media'][$view]))continue;
  $ids=array_values(array_filter(array_map('absint',explode(',',sanitize_text_field(wp_unslash($_POST['lk_media'][$view])))),'wp_attachment_is_image'));
  update_post_meta($id,$key,$view==='photo'?implode(',',$ids):$ids);
 }
});

// Approved reviews are the source of the summary; moderation updates it too.
function lk_refresh_rating($post_id){
 if(get_post_type($post_id)!=='listing')return;
 $reviews=get_comments(array('post_id'=>$post_id,'type'=>'lk_review','status'=>'approve'));
 $scores=array_filter(array_map(function($review){$score=(float)get_comment_meta($review->comment_ID,'lk_rating',true);return $score>=1&&$score<=5?$score:0;},$reviews));
 update_post_meta($post_id,'_lk_rating',$scores?round(array_sum($scores)/count($scores),1):0);
}
add_action('transition_comment_status',function($new,$old,$comment){if($comment->comment_type==='lk_review')lk_refresh_rating($comment->comment_post_ID);},10,3);
add_action('deleted_comment',function($id,$comment){if($comment->comment_type==='lk_review')lk_refresh_rating($comment->comment_post_ID);},10,2);
foreach(array('added_comment_meta','updated_comment_meta','deleted_comment_meta') as $hook)add_action($hook,function($meta_id,$comment_id,$key){if($key==='lk_rating'){$comment=get_comment($comment_id);if($comment)lk_refresh_rating($comment->comment_post_ID);}},10,3);

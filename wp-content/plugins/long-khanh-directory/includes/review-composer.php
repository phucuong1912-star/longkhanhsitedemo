<?php
if(!defined('ABSPATH'))exit;
function lk_public_review_meta_query(){return array('relation'=>'OR',array('key'=>'lk_anonymous','compare'=>'NOT EXISTS'),array('key'=>'lk_anonymous','value'=>'1','compare'=>'!='));}
function lk_review_public_name($review){
 $name=$review->comment_author;
 if(get_comment_meta($review->comment_ID,'lk_anonymous',true)){$name=mb_substr($name,0,1).'*****'.(mb_strlen($name)>1?mb_substr($name,-1):'');}
 return $name;
}
function lk_review_photos($review){$ids=get_comment_meta($review->comment_ID,'lk_photo_ids',true);return is_array($ids)?array_values(array_filter(array_map('absint',$ids),'wp_attachment_is_image')):array();}
function lk_render_review_body($review){
 $title=get_comment_meta($review->comment_ID,'lk_review_title',true);if($title)echo '<h4>'.esc_html($title).'</h4>';
 echo '<p>'.nl2br(esc_html($review->comment_content)).'</p>';
 $photos=lk_review_photos($review);if($photos){echo '<div class="lk-review-photos">';foreach($photos as $i=>$id)echo '<a href="'.esc_url(wp_get_attachment_url($id)).'">'.wp_get_attachment_image($id,'medium',false,array('alt'=>'Ảnh trải nghiệm '.($i+1),'loading'=>'lazy')).'</a>';echo '</div>';}
}
function lk_review_composer($settings=array()){
 $places=lk_directory_index();$requested=absint($_GET['place_id']??0);$valid_ids=wp_list_pluck($places,'id');if(!in_array($requested,$valid_ids,true))$requested=0;
 echo '<form class="lk-review-composer" method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lk_review">';wp_nonce_field('lk_review','lk_nonce');
 echo '<div class="lk-compose-main"><h3>'.esc_html($settings['rating_title']??'Xếp hạng của bạn').'</h3><div class="lk-compose-ratings">';
 foreach(lk_criteria_labels() as $key=>$label){echo '<fieldset class="lk-star-row"><legend class="lk-sr">'.esc_html($label).'</legend><span aria-hidden="true">'.esc_html($label).'</span><div class="lk-stars-input">';for($i=1;$i<=5;$i++)echo '<label><input type="radio" name="criteria['.$key.']" value="'.$i.'" '.checked($i,5,false).' required aria-label="'.esc_attr($label.' '.$i.' sao').'"><i class="fas fa-star" aria-hidden="true"></i></label>';echo '</div><output class="lk-score-word">Tuyệt vời</output></fieldset>';}
 echo '</div><h3>'.esc_html($settings['review_heading']??'Đánh giá của bạn').'</h3><label class="lk-sr" for="lk-review-title">Tiêu đề đánh giá</label><input id="lk-review-title" type="text" name="review_title" maxlength="120" placeholder="Nhập tiêu đề đánh giá" required><label class="lk-sr" for="lk-review-copy">Nội dung đánh giá</label><textarea id="lk-review-copy" name="review" rows="5" minlength="10" maxlength="10000" placeholder="Nhập nội dung đánh giá" required aria-describedby="lk-review-counter"></textarea><p id="lk-review-counter" class="lk-review-counter" aria-live="polite">0 ký tự (tối thiểu 10 ký tự)</p>';
 echo '<label class="lk-review-upload"><i class="fas fa-camera" aria-hidden="true"></i><span>Thêm ảnh</span><input type="file" name="review_photos[]" accept="image/jpeg,image/png,image/webp" multiple aria-label="Thêm ảnh đánh giá"></label><p class="lk-upload-help">Tối đa 5 ảnh JPG, PNG hoặc WebP, mỗi ảnh tối đa 5 MB.</p><p class="lk-compose-error" role="alert" hidden></p><div class="lk-review-preview" aria-label="Ảnh đã chọn"></div><div class="lk-anonymous-row"><h3>'.esc_html($settings['anonymous_title']??'Đánh giá ẩn danh').'</h3><label class="lk-switch"><input type="checkbox" name="anonymous" value="1" role="switch" aria-label="Đánh giá ẩn danh"><span aria-hidden="true"></span></label></div><p class="lk-anonymous-help">Tên của bạn sẽ được che và đánh giá không xuất hiện trên bảng tin khi bật ẩn danh.</p>';
 if(is_user_logged_in())echo '<button class="lk-button" type="submit">'.esc_html($settings['button']??'Gửi đánh giá của bạn').'</button>';else echo '<button class="lk-button lk-login-open" type="button">Đăng nhập để gửi đánh giá</button>';
 echo '</div><aside class="lk-compose-aside"><h3>'.esc_html($settings['place_title']??'Địa điểm').'</h3><label class="lk-sr" for="lk-review-place">Chọn địa điểm</label><select id="lk-review-place" name="place_id" required><option value="">📍 Nhấn vào đây để chọn địa điểm</option>';foreach($places as $p)echo '<option value="'.$p['id'].'" '.selected($requested,$p['id'],false).'>'.esc_html($p['title']).'</option>';echo '</select><div class="lk-review-chosen" aria-live="polite" hidden></div><h3>'.esc_html($settings['recent_title']??'Đánh giá gần đây').'</h3>';
 $recent=is_user_logged_in()?get_comments(array('type'=>'lk_review','status'=>'approve','user_id'=>get_current_user_id(),'post__in'=>$valid_ids?:array(0),'number'=>4)):array();
 if(!$recent)echo '<p class="lk-recent-empty">Các đánh giá bạn đã gửi sẽ xuất hiện tại đây.</p>';
 foreach($recent as $review)echo '<article class="lk-compose-recent"><a href="'.esc_url(get_permalink($review->comment_post_ID).'#reviews').'">'.esc_html(get_the_title($review->comment_post_ID)).'</a><p>'.esc_html(wp_trim_words($review->comment_content,20)).'</p></article>';
 echo '</aside></form>';
}
function lk_validate_review_uploads(){
 if(empty($_FILES['review_photos']['name'][0]))return;
 $files=$_FILES['review_photos'];if(!is_array($files['name'])||count($files['name'])>5)wp_die('Vui lòng chọn tối đa 5 ảnh.','Không thể gửi',array('response'=>400));
 require_once ABSPATH.'wp-admin/includes/file.php';
 foreach($files['name'] as $i=>$name){if(($files['error'][$i]??1)!==UPLOAD_ERR_OK||($files['size'][$i]??0)>5*MB_IN_BYTES)wp_die('Ảnh chưa tải lên hoặc lớn hơn 5 MB.','Không thể gửi',array('response'=>400));$type=wp_check_filetype_and_ext($files['tmp_name'][$i],$name,array('jpg|jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'));if(empty($type['type'])||!in_array(wp_get_image_mime($files['tmp_name'][$i]),array('image/jpeg','image/png','image/webp'),true))wp_die('Chỉ hỗ trợ ảnh JPG, PNG hoặc WebP.','Không thể gửi',array('response'=>400));}
}
function lk_save_review_uploads($post_id){
 if(empty($_FILES['review_photos']['name'][0]))return array();
 require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';$files=$_FILES['review_photos'];$ids=array();
 foreach($files['name'] as $i=>$name){$_FILES['lk_review_photo']=array('name'=>sanitize_file_name($name),'type'=>wp_get_image_mime($files['tmp_name'][$i]),'tmp_name'=>$files['tmp_name'][$i],'error'=>0,'size'=>$files['size'][$i]);$attachment=media_handle_upload('lk_review_photo',$post_id);if(!is_wp_error($attachment))$ids[]=$attachment;}
 return $ids;
}

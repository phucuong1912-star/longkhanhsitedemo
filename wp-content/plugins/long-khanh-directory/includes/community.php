<?php
if(!defined('ABSPATH'))exit;
function lk_author_initials($name){
 $parts=preg_split('/\s+/u',trim($name));return mb_strtoupper(mb_substr($parts[0]??'L',0,1).mb_substr(end($parts)?:'K',0,1));
}
function lk_following_ids(){return array_values(array_unique(array_filter(array_map('absint',(array)get_user_meta(get_current_user_id(),'_lk_following',true)))));}
function lk_community_authors(){
 global $wpdb;
 $public_ids=wp_list_pluck(lk_posts(),'ID');
 $rows=$public_ids?$wpdb->get_results($wpdb->prepare("SELECT user_id,COUNT(*) AS review_count FROM {$wpdb->comments} WHERE comment_type='lk_review' AND comment_approved='1' AND user_id>0 AND comment_post_ID IN (".implode(',',array_fill(0,count($public_ids),'%d')).") AND NOT EXISTS (SELECT 1 FROM {$wpdb->commentmeta} WHERE comment_id={$wpdb->comments}.comment_ID AND meta_key='lk_anonymous' AND meta_value='1') GROUP BY user_id ORDER BY review_count DESC LIMIT 5",$public_ids)):array();
 if(!$rows){echo '<div class="lk-community-empty"><span class="lk-avatar">LK</span><p>Chia sẻ đánh giá để cộng đồng có thêm trải nghiệm hữu ích.</p><a class="lk-button lk-outline" href="'.esc_url(lk_link('new-review')).'">Viết đánh giá đầu tiên</a></div>';return;}
 $following=lk_following_ids();
 foreach($rows as $row){$user=get_userdata($row->user_id);if(!$user)continue;
  echo '<div class="lk-author-row"><span class="lk-avatar">'.esc_html(lk_author_initials($user->display_name)).'</span><div><strong>'.esc_html($user->display_name).'</strong><p>'.(int)$row->review_count.' đánh giá</p>';
  if(is_user_logged_in()&&(int)$row->user_id!==get_current_user_id()){
   echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lk_follow"><input type="hidden" name="author_id" value="'.(int)$row->user_id.'">';wp_nonce_field('lk_follow_'.$row->user_id,'lk_nonce');
   echo '<button class="lk-follow" aria-pressed="'.(in_array((int)$row->user_id,$following,true)?'true':'false').'">'.lk_icon('rss').' '.(in_array((int)$row->user_id,$following,true)?'Đang theo dõi':'Theo dõi').'</button></form>';
  }elseif(!is_user_logged_in())echo '<button class="lk-follow lk-login-open">'.lk_icon('rss').' Theo dõi</button>';
  echo '</div></div>';
 }
}
add_action('admin_post_lk_follow',function(){
 if(!is_user_logged_in())wp_die('Vui lòng đăng nhập.','Không thể theo dõi',array('response'=>403));
 $author=absint($_POST['author_id']??0);lk_require_nonce('lk_follow_'.$author);
 if(!$author||$author===get_current_user_id()||!get_userdata($author)||!get_comments(array('type'=>'lk_review','status'=>'approve','user_id'=>$author,'post__in'=>wp_list_pluck(lk_posts(),'ID')?:array(0),'meta_query'=>lk_public_review_meta_query(),'count'=>true)))wp_die('Không tìm thấy thành viên.','Không thể theo dõi',array('response'=>400));
 $ids=lk_following_ids();$ids=in_array($author,$ids,true)?array_diff($ids,array($author)):array_merge($ids,array($author));update_user_meta(get_current_user_id(),'_lk_following',array_values($ids));
 wp_safe_redirect(lk_link('explore'));exit;
});
function lk_feed($settings=array()){
 $limit=max(1,min(20,(int)($settings['limit']??5)));$following=isset($_GET['lk_feed'])&&sanitize_key($_GET['lk_feed'])==='following';
 if($following&&!is_user_logged_in()){echo '<div class="lk-feed-card lk-community-empty"><h2>Đang theo dõi</h2><p>Đăng nhập để xem đánh giá từ những người bạn theo dõi.</p><button class="lk-button lk-login-open">Đăng nhập</button></div>';return;}
 $query=array('type'=>'lk_review','status'=>'approve','post__in'=>wp_list_pluck(lk_posts(),'ID')?:array(0),'meta_query'=>lk_public_review_meta_query(),'number'=>$limit,'orderby'=>'comment_date_gmt','order'=>'DESC');
 if($following){$ids=lk_following_ids();if(!$ids){echo '<div class="lk-feed-card lk-community-empty"><h2>Chưa có bài viết theo dõi</h2><p>Theo dõi thành viên để xem những đánh giá mới của họ tại đây.</p><a class="lk-button lk-outline" href="'.esc_url(lk_link('explore')).'">Xem bài nổi bật</a></div>';return;}$query['user_id']=$ids;}
 $reviews=get_comments($query);$entries=array();
 foreach($reviews as $review){$post=get_post($review->comment_post_ID);if($post&&lk_is_directory_listing($post->ID))$entries[]=array('post'=>$post,'review'=>$review);}
 if(!$entries){echo '<div class="lk-feed-card lk-community-empty lk-feed-welcome"><img src="'.esc_url($settings['empty_image']['url']??lk_asset('review-illustration')).'" alt="" loading="lazy"><h2>'.esc_html($settings['empty_title']??'Chia sẻ trải nghiệm đầu tiên').'</h2><p>'.esc_html($settings['empty_copy']??'Bạn vừa ghé một địa điểm thú vị ở Long Khánh? Viết đánh giá để mọi người cùng khám phá.').'</p><a class="lk-button" href="'.esc_url(lk_link('new-review')).'">Viết Review</a></div>';return;}
 foreach($entries as $entry){
  $post=$entry['post'];$review=$entry['review'];$data=lk_place_data($post);$name=$review?lk_review_public_name($review):'Cộng đồng Long Khánh';$score=$review?(float)get_comment_meta($review->comment_ID,'lk_rating',true):0;
  $time=$review?human_time_diff(strtotime($review->comment_date_gmt.' UTC'),time()).' trước':'Trải nghiệm demo';
  $stars=(int)round(max(0,min(5,$score)));
  echo '<article class="lk-feed-card"><div class="lk-feed-author"><span class="lk-avatar">'.esc_html(lk_author_initials($name)).'</span><div><strong>'.esc_html($name).'</strong><br><a href="'.esc_url($data['url']).'">'.esc_html($data['title']).'</a><small><span class="lk-stars">'.($score?esc_html($score).' ':'').str_repeat('★',$stars).str_repeat('☆',5-$stars).'</span> · '.esc_html($time).'</small></div></div>';
  $text=$review?$review->comment_content:wp_strip_all_tags($post->post_content?:$post->post_excerpt);
  echo '<p class="lk-feed-text">'.nl2br(esc_html(mb_strimwidth($text,0,550,'…'))).'</p>';
  if(!$review)echo '<p class="lk-feed-info">'.lk_icon('map-marker-alt').' '.esc_html($data['address']).'<br>'.lk_icon('dollar-sign').' '.esc_html(lk_money($data['min']).' - '.lk_money($data['max'])).'<br>'.lk_icon('clock').' '.esc_html($data['open'].' - '.$data['close']).'</p>';
  $review_photos=$review?lk_review_photos($review):array();$images=$review_photos?array_map(function($id){return array('id'=>$id,'url'=>wp_get_attachment_url($id));},$review_photos):lk_listing_media($post->ID);$shown=array_slice($images,0,5);
  if($review)echo '<small class="lk-feed-image-label">'.($review_photos?'Ảnh đánh giá':'Ảnh địa điểm').'</small>';
  echo '<div class="lk-feed-photos lk-feed-photos--'.count($shown).'" aria-label="Ảnh địa điểm '.esc_attr($data['title']).'">';
  foreach($shown as $i=>$image)echo '<a href="'.esc_url($review_photos?$image['url']:$data['url'].'photo/').'"><img loading="lazy" src="'.esc_url(wp_get_attachment_image_url($image['id'],'medium_large')?:$image['url']).'" alt="'.esc_attr($data['title'].' - ảnh '.($i+1)).'">'.($i===4&&count($images)>5?'<span class="lk-photo-count">+'.(count($images)-5).'</span>':'').'</a>';
  echo '</div><div class="lk-feed-actions"><button class="lk-like" aria-pressed="false">'.lk_icon('heart').' Thích</button><a href="'.esc_url($data['url'].'#reviews').'">'.lk_icon('comment').' Bình luận</a><button class="lk-share" data-url="'.esc_url($data['url']).'">'.lk_icon('share').' Chia sẻ</button></div></article>';
 }
}

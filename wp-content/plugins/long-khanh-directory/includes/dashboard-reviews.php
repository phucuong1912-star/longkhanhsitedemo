<?php
if(!defined('ABSPATH'))exit;

/** Read existing composer reviews; never mirror them into lp-reviews posts. */
function lk_dashboard_reviews($kind='submitted',$page=1){
 $empty=array('items'=>array(),'total'=>0,'page'=>1,'pages'=>1);
 $user=get_current_user_id();
 if(!$user||!in_array($kind,array('submitted','received'),true))return $empty;
 $places=array('post_type'=>'listing','post_status'=>'publish','has_password'=>false,'posts_per_page'=>-1,'fields'=>'ids');
 if($kind==='received')$places['author']=$user;
 $ids=get_posts($places);if(!$ids)return $empty;
 $args=array('type'=>'lk_review','status'=>$kind==='submitted'?array('approve','hold'):'approve','post__in'=>$ids);
 if($kind==='submitted')$args['user_id']=$user;
 $total=(int)get_comments($args+array('count'=>true));
 $pages=max(1,(int)ceil($total/10));$page=max(1,min($pages,absint($page)));
 $items=$total?get_comments($args+array('number'=>10,'paged'=>$page,'orderby'=>array('comment_date_gmt'=>'DESC','comment_ID'=>'DESC'))):array();
 return array('items'=>$items,'total'=>$total,'page'=>$page,'pages'=>$pages);
}

function lk_dashboard_review_page($key){
 return isset($_GET[$key])&&is_scalar($_GET[$key])?max(1,absint(wp_unslash($_GET[$key]))):1;
}

function lk_dashboard_review_cards($result,$kind){
 $submitted=$kind==='submitted';$key=$submitted?'lk_sent_page':'lk_received_page';
 echo '<section class="lk-dashboard-review-group" aria-labelledby="lk-dashboard-'.$kind.'-title"><h2 id="lk-dashboard-'.$kind.'-title">'.($submitted?'Đánh giá đã gửi':'Đánh giá về địa điểm của bạn').' <span>('.(int)$result['total'].')</span></h2>';
 foreach($result['items'] as $review){
  $link=get_permalink($review->comment_post_ID).'#reviews';
  $rating=(float)get_comment_meta($review->comment_ID,'lk_rating',true);
  echo '<article class="lk-dashboard-review-card" data-lk-comment-id="'.(int)$review->comment_ID.'"><header><h3><a href="'.esc_url($link).'">'.esc_html(get_the_title($review->comment_post_ID)).'</a></h3><span class="lk-dashboard-review-status">'.($review->comment_approved==='1'?'Đã đăng':'Chờ duyệt').'</span></header><p class="lk-dashboard-review-meta">';
  if(!$submitted)echo '<span>'.esc_html(lk_review_public_name($review)).'</span> · ';
  if($rating>=1&&$rating<=5)echo '<span aria-label="'.esc_attr(number_format_i18n($rating,1).' trên 5 sao').'">★ '.esc_html(number_format_i18n($rating,1)).'/5</span> · ';
  echo '<time datetime="'.esc_attr(get_comment_date('c',$review)).'">'.esc_html(get_comment_date(get_option('date_format'),$review)).'</time>';
  if(get_comment_meta($review->comment_ID,'lk_anonymous',true))echo ' · <span>Ẩn danh</span>';
  echo '</p>';lk_render_review_body($review);
  echo '<a class="lk-dashboard-review-link" href="'.esc_url($link).'">Xem tại địa điểm</a></article>';
 }
 if($result['pages']>1){
  $mode=isset($_GET['dashboard'])&&$_GET['dashboard']==='reviews-submited'?'reviews-submited':'reviews';
  $base=add_query_arg('dashboard',$mode,get_permalink(get_queried_object_id()));
  $other=$submitted?'lk_received_page':'lk_sent_page';$other_page=lk_dashboard_review_page($other);
  if($other_page>1)$base=add_query_arg($other,$other_page,$base);
  echo '<nav class="lk-dashboard-review-pages" aria-label="Phân trang '.($submitted?'đánh giá đã gửi':'đánh giá nhận được').'">';
  if($result['page']>1)echo '<a href="'.esc_url(add_query_arg($key,$result['page']-1,$base)).'">Trang trước</a>';
  echo '<span>Trang '.(int)$result['page'].' / '.(int)$result['pages'].'</span>';
  if($result['page']<$result['pages'])echo '<a href="'.esc_url(add_query_arg($key,$result['page']+1,$base)).'">Trang sau</a>';
  echo '</nav>';
 }
 echo '</section>';
}

function lk_dashboard_reviews_panel(){
 if(!is_user_logged_in())return;
 $submitted=lk_dashboard_reviews('submitted',lk_dashboard_review_page('lk_sent_page'));
 $received=lk_dashboard_reviews('received',lk_dashboard_review_page('lk_received_page'));
 if(!$submitted['total']&&!$received['total'])return;
 // Keep native review editing/replies. Hide its empty trophy only when both
 // native collections are empty; the vendor template and data stay untouched.
 $native=function_exists('getAllReviewsArray')&&(array_filter(getAllReviewsArray(true))||array_filter(getAllReviewsArray(false)));
 echo '<section class="lk-dashboard-review-comments panel panel-default col-md-11 align-center" data-lk-native-reviews="'.($native?'1':'0').'" aria-label="Đánh giá của tài khoản">';
 if($submitted['total'])lk_dashboard_review_cards($submitted,'submitted');
 if($received['total'])lk_dashboard_review_cards($received,'received');
 echo '</section>';
}
foreach(array('reviews','reviews-submited') as $part)add_action('get_template_part_templates/dashboard/'.$part,'lk_dashboard_reviews_panel',10,0);

add_action('wp_enqueue_scripts',function(){
 if(!is_user_logged_in()||!isset($_GET['dashboard'])||!in_array($_GET['dashboard'],array('reviews','reviews-submited'),true))return;
 wp_enqueue_style('lk-dashboard-reviews',LK_URL.'assets/dashboard-reviews.css',array(),(string)filemtime(LK_DIR.'assets/dashboard-reviews.css'));
});

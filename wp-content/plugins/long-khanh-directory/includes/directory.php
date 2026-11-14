<?php
if(!defined('ABSPATH'))exit;
function lk_link($slug=''){return home_url('/'.trim($slug,'/').'/');}
function lk_asset($key){static $m; if($m===null)$m=json_decode(file_get_contents(LK_DIR.'assets/images/manifest.json'),true);return isset($m[$key])?LK_URL.'assets/images/'.$m[$key]['filename']:'';}
function lk_money($amount){return number_format_i18n((float)$amount,0).'đ';}
function lk_criteria_labels(){return array('position'=>'Vị trí','space'=>'Không gian','quality'=>'Chất lượng','service'=>'Phục vụ','price'=>'Giá cả');}
function lk_rating_criteria($id){
 if(isset($GLOBALS['lk_criteria_cache'][$id]))return $GLOBALS['lk_criteria_cache'][$id];
 $totals=array_fill_keys(array_keys(lk_criteria_labels()),0);$counts=$totals;
 foreach(get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve')) as $review){$values=get_comment_meta($review->comment_ID,'lk_criteria',true);if(!is_array($values))continue;foreach($totals as $key=>$unused)if(isset($values[$key])&&$values[$key]>=1&&$values[$key]<=5){$totals[$key]+=(float)$values[$key];$counts[$key]++;}}
 foreach($totals as $key=>$value)$totals[$key]=$counts[$key]?round($value/$counts[$key],1):0;
 return $GLOBALS['lk_criteria_cache'][$id]=$totals;
}
function lk_invalidate_public_index(){delete_transient('lk_public_directory_v3');unset($GLOBALS['lk_public_index']);}
function lk_clear_directory_cache($id){unset($GLOBALS['lk_place_cache'][$id],$GLOBALS['lk_criteria_cache'][$id],$GLOBALS['lk_media_cache'][$id]);if(get_post_type($id)==='listing')lk_invalidate_public_index();}
foreach(array('added_post_meta','updated_post_meta','deleted_post_meta') as $hook)add_action($hook,function($meta,$id,$key){if(in_array($key,array('_lk_managed','lp_listingpro_options','_lk_open','_lk_close','_thumbnail_id','_lk_rating','gallery_image_ids','_lk_menu_ids'),true))lk_clear_directory_cache($id);},10,3);
add_action('set_object_terms','lk_clear_directory_cache');add_action('post_updated','lk_clear_directory_cache');
add_action('transition_post_status',function($new,$old,$post){if($new!==$old&&$post->post_type==='listing')lk_clear_directory_cache($post->ID);},10,3);
add_action('edited_term',function($id,$taxonomy_id,$taxonomy){if(in_array($taxonomy,array('listing-category','location','features'),true))lk_invalidate_public_index();},10,3);
add_filter('wp_update_attachment_metadata',function($data){lk_invalidate_public_index();return $data;});
add_action('delete_attachment','lk_invalidate_public_index');
add_action('wp_insert_comment',function($id,$comment){lk_clear_directory_cache($comment->comment_post_ID);},10,2);
add_action('transition_comment_status',function($new,$old,$comment){lk_clear_directory_cache($comment->comment_post_ID);},10,3);
foreach(array('added_comment_meta','updated_comment_meta','deleted_comment_meta') as $hook)add_action($hook,function($meta,$id,$key){if(!in_array($key,array('lk_rating','lk_criteria'),true))return;$comment=get_comment($id);if($comment)lk_clear_directory_cache($comment->comment_post_ID);},10,3);
function lk_icon($icon){return '<i class="fas fa-'.esc_attr($icon).'" aria-hidden="true"></i>';}
function lk_directory_terms($taxonomy){return get_terms(array('taxonomy'=>$taxonomy,'hide_empty'=>false,'meta_query'=>array('relation'=>'OR',array('key'=>'_lk_demo','value'=>1),array('key'=>'_lk_directory','value'=>1))));}
add_action('created_term',function($id,$taxonomy_id,$taxonomy){if(in_array($taxonomy,array('listing-category','location'),true))update_term_meta($id,'_lk_directory',1);},10,3);
function lk_normalize_search($text){return preg_replace('/\b(coffee|cafe)\b/u','ca phe',remove_accents(mb_strtolower($text)));}
function lk_posts(){return get_posts(array('post_type'=>'listing','post_status'=>'publish','has_password'=>false,'posts_per_page'=>-1,'meta_key'=>'_lk_managed','meta_value'=>'1','orderby'=>array('menu_order'=>'ASC','date'=>'DESC','ID'=>'DESC')));}
function lk_public_index(){if(!isset($GLOBALS['lk_public_index'])){$cached=get_transient('lk_public_directory_v3');$GLOBALS['lk_public_index']=is_array($cached)?$cached:array();}return $GLOBALS['lk_public_index'];}
function lk_current_open_status($open,$close){$now=current_time('H:i');return $open===$close||($open<$close?($now>=$open&&$now<$close):($now>=$open||$now<$close));}
function lk_directory_index(){
 $cached=lk_public_index();$index=array();foreach(lk_posts() as $post)$index[$post->ID]=lk_place_data($post);
 if(array_keys($cached)!==array_keys($index))set_transient('lk_public_directory_v3',$index,5*MINUTE_IN_SECONDS);
 $GLOBALS['lk_public_index']=$index;return array_values($index);
}
function lk_place_data($post){
 $p=get_post($post); $id=$p->ID;if(isset($GLOBALS['lk_place_cache'][$id]))return $GLOBALS['lk_place_cache'][$id];
 $index=lk_public_index();if($p->post_status==='publish'&&!$p->post_password&&isset($index[$id])){$data=$index[$id];$data=array_merge($data,lk_schedule_status($data['schedule']));return $GLOBALS['lk_place_cache'][$id]=$data;}
 $o=get_post_meta($id,'lp_listingpro_options',true);if(!is_array($o))$o=array();
 $region=wp_get_post_terms($id,'location');$category=wp_get_post_terms($id,'listing-category');$features=wp_get_post_terms($id,'features',array('fields'=>'slugs'));
 $lat=isset($o['latitude'])&&is_numeric($o['latitude'])&&(float)$o['latitude']>=-90&&(float)$o['latitude']<=90?(float)$o['latitude']:null;
 $lng=isset($o['longitude'])&&is_numeric($o['longitude'])&&(float)$o['longitude']>=-180&&(float)$o['longitude']<=180?(float)$o['longitude']:null;
 $schedule=lk_read_schedule($o,$id);$hours=lk_schedule_status($schedule);$open=$hours['open'];$close=$hours['close'];$opened=$hours['opened'];$price_status=array_key_exists($o['price_status']??'',lk_price_status_labels())?$o['price_status']:'';
 return $GLOBALS['lk_place_cache'][$id]=array('id'=>$id,'title'=>html_entity_decode(wp_strip_all_tags(get_the_title($p)),ENT_QUOTES,'UTF-8'),'url'=>get_permalink($p),'image'=>get_the_post_thumbnail_url($id,'medium_large')?:lk_asset('no-image'),'address'=>$o['gAddress']??'Long Khánh, Đồng Nai','phone'=>$o['phone']??'','priceStatus'=>$price_status,'priceLabel'=>lk_price_status_labels()[$price_status]??'','schedule'=>$schedule,'hoursLabel'=>$hours['hoursLabel'],'min'=>(int)($o['list_price']??0),'max'=>(int)($o['list_price_to']??0),'lat'=>$lat,'lng'=>$lng,'features'=>is_wp_error($features)?array():$features,'open'=>$open,'close'=>$close,'opened'=>$opened,'region'=>!is_wp_error($region)&&$region? $region[0]->name:'Long Khánh','regionSlug'=>!is_wp_error($region)&&$region?$region[0]->slug:'','category'=>!is_wp_error($category)&&$category?$category[0]->slug:'','categories'=>!is_wp_error($category)?wp_list_pluck($category,'slug'):array(),'categoryNames'=>!is_wp_error($category)?implode(' ',wp_list_pluck($category,'name')):'','rating'=>(float)get_post_meta($id,'_lk_rating',true),'reviews'=>(int)get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve','count'=>true)));
}
function lk_rating_stars($score){$score=max(0,min(5,(float)$score));return '<span class="lk-star-track" aria-hidden="true">☆☆☆☆☆<span style="width:'.($score*20).'%">★★★★★</span></span>';}
function lk_card($post,$view='grid'){
 $d=lk_place_data($post);$price=$d['min']?lk_money($d['min']).' - '.lk_money($d['max']):'Đang cập nhật';
 echo '<article class="lk-place lk-place--'.esc_attr($view).'" data-place-id="'.$d['id'].'"><a class="lk-place-photo" href="'.esc_url($d['url']).'" tabindex="-1" aria-hidden="true"><img src="'.esc_url($d['image']).'" alt="" loading="lazy" width="400" height="300"></a><button class="lk-save" data-save="'.$d['id'].'" aria-label="Lưu '.esc_attr($d['title']).'" aria-pressed="false">'.lk_icon('bookmark').'</button><div class="lk-place-copy"><h3><a href="'.esc_url($d['url']).'">'.esc_html($d['title']).'</a></h3><div class="lk-list-rating"><span class="lk-stars" aria-label="'.esc_attr($d['rating']?'Điểm '.$d['rating'].' trên 5':'Chưa có đánh giá').'">'.($d['rating']?esc_html($d['rating']).' ':'').lk_rating_stars($d['rating']).'</span><span>'.($d['reviews']?(int)$d['reviews'].' đánh giá':($d['rating']?'Đánh giá tham khảo':'Chưa có đánh giá')).'</span></div><p class="lk-price">'.lk_icon('dollar-sign').' '.esc_html($price).' '.(!empty($d['priceStatus'])&&$d['priceStatus']!=='notsay'?'<span class="lk-price-level">'.esc_html($d['priceLabel']).'</span>':'').'</p><p class="lk-address" title="'.esc_attr($d['address']).'">'.lk_icon('map-marker-alt').' '.esc_html($d['address']).'</p><div class="lk-place-meta"><span class="lk-region">'.lk_icon('map-marker-alt').' '.esc_html($d['region']).'</span><span class="lk-status '.($d['opened']?'is-open':'is-closed').'">'.($d['opened']?'Đang mở cửa':'Đang đóng cửa').'</span><span class="lk-hours"> - '.esc_html($d['hoursLabel']).'</span></div></div></article>';
}
function lk_search($s=array()){
 $s=wp_parse_args($s,array('placeholder'=>'Tên quán, khu vực, kiểu quán,...','button'=>'Tìm quán','mode'=>'hero'));
 $q=isset($_GET['q'])?sanitize_text_field(wp_unslash($_GET['q'])):'';
 echo '<form class="lk-search lk-search-'.esc_attr($s['mode']).'" action="'.esc_url(lk_link('search')).'" method="get" role="search"><label class="lk-sr" for="lk-query-'.esc_attr($s['mode']).'">Tìm kiếm địa điểm</label><div class="lk-search-field">'.lk_icon('search').'<input id="lk-query-'.esc_attr($s['mode']).'" name="q" type="search" autocomplete="off" placeholder="'.esc_attr($s['placeholder']).'" value="'.esc_attr($q).'"><div class="lk-suggest" hidden></div></div><button type="submit">'.lk_icon('search').' '.esc_html($s['button']).'</button></form>';
}
function lk_listing_grid($s){
 $limit=max(1,(int)($s['limit']??8));$posts=lk_posts();
 if(!empty($s['nearby'])&&lk_context_id()){
  $current=lk_place_data(lk_context_id());
  $posts=array_values(array_filter($posts,function($p)use($current){$data=lk_place_data($p);return $p->ID!==$current['id']&&$current['lat']!==null&&$current['lng']!==null&&$data['lat']!==null&&$data['lng']!==null;}));
  usort($posts,function($a,$b)use($current){$da=lk_place_data($a);$db=lk_place_data($b);$distance=function($d)use($current){return pow(($d['lat']-$current['lat']),2)+pow(($d['lng']-$current['lng'])*cos(deg2rad($current['lat'])),2);};return $distance($da)<=>$distance($db);});
 }
 if(!empty($s['category']))$posts=array_filter($posts,function($p)use($s){return has_term($s['category'],'listing-category',$p);});
 if(!empty($s['tabs'])){echo '<div class="lk-tabs" role="tablist" aria-label="Danh mục địa điểm">';foreach(array(''=>'Mới','an-uong'=>'Ăn uống','khach-san'=>'Khách sạn','giai-tri'=>'Giải trí','mua-sam'=>'Mua sắm') as $slug=>$label)echo '<button role="tab" aria-selected="'.($slug===''?'true':'false').'" data-category="'.esc_attr($slug).'">'.esc_html($label).'</button>';echo '</div>';}
 echo '<div class="lk-place-grid" style="--lk-columns:'.(int)($s['columns']??4).'" data-limit="'.$limit.'">';foreach(array_slice($posts,0,$limit) as $p)lk_card($p);echo '</div>';
 $more_url=lk_link('search');if(!empty($s['nearby'])&&isset($current)&&$current['lat']!==null&&$current['lng']!==null)$more_url=add_query_arg('nearby',get_post_field('post_name',$current['id']),$more_url);
 if(!empty($s['more']))echo '<div class="lk-center"><a class="lk-button lk-outline" href="'.esc_url($more_url).'">'.esc_html($s['more']).'</a></div>';
}
function lk_results($s=array()){lk_render_results($s);}
function lk_reviews($s=array()){
 $id=lk_context_id()?:get_the_ID();$comments=get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve'));echo '<div class="lk-reviews"><div class="lk-review-heading"><h2>'.esc_html($s['title']??'Đánh giá từ cộng đồng').' <span>('.count($comments).')</span></h2>'.'<button type="button" class="lk-button lk-review-open">Viết đánh giá</button>'.'</div>';
 if(!$comments){$image=$s['invite_image']['url']??lk_asset('review-illustration');echo '<div class="lk-review-invite">'.($image?'<img src="'.esc_url($image).'" alt="" loading="lazy">':'').'<div><h3>'.esc_html($s['invite_title']??'Bạn đã từng đến đây?').'</h3><p>'.esc_html($s['invite_copy']??'Chia sẻ trải nghiệm và cảm nhận của bạn cho mọi người cùng biết.').'</p><p>Những review chất lượng sẽ được xuất hiện ở bảng tin đấy!</p></div></div><p class="lk-review-empty">Chưa có đánh giá nào cho '.esc_html(get_the_title($id)).'. Hãy là người đầu tiên chia sẻ trải nghiệm!</p>';}
 foreach($comments as $c){echo '<article class="lk-review"><strong>'.esc_html(lk_review_public_name($c)).'</strong><span class="lk-stars"> '.esc_html(get_comment_meta($c->comment_ID,'lk_rating',true)).'/5 ★</span><time>'.esc_html(get_comment_date('d/m/Y',$c)).'</time>';lk_render_review_body($c);echo '</article>';}
 echo '<dialog id="lk-review-dialog" class="lk-review-dialog" aria-labelledby="lk-review-dialog-title"><div class="lk-review-dialog-header"><h2 id="lk-review-dialog-title">'.esc_html($s['modal_title']??'Đánh giá').' '.esc_html(get_the_title($id)).'</h2><button type="button" class="lk-close" data-close aria-label="Đóng đánh giá">&times;</button></div>';
 lk_review_composer(array_merge($s,array('fixed_place'=>$id,'button'=>$s['review_button']??'Gửi đánh giá')));
 echo '</dialog>';
 echo '</div>';
}

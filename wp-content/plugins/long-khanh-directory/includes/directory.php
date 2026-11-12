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
function lk_invalidate_public_index(){delete_transient('lk_public_directory_v1');unset($GLOBALS['lk_public_index']);}
function lk_clear_directory_cache($id){unset($GLOBALS['lk_place_cache'][$id],$GLOBALS['lk_criteria_cache'][$id],$GLOBALS['lk_media_cache'][$id]);if(get_post_type($id)==='listing')lk_invalidate_public_index();}
foreach(array('added_post_meta','updated_post_meta','deleted_post_meta') as $hook)add_action($hook,function($meta,$id,$key){if(in_array($key,array('_lk_managed','lp_listingpro_options','_lk_open','_lk_close','_thumbnail_id','_lk_rating','gallery_image_ids','_lk_menu_ids'),true))lk_clear_directory_cache($id);},10,3);
add_action('set_object_terms','lk_clear_directory_cache');add_action('post_updated','lk_clear_directory_cache');
add_action('transition_post_status',function($new,$old,$post){if($new!==$old&&$post->post_type==='listing')lk_clear_directory_cache($post->ID);},10,3);
add_action('edited_term',function($id,$taxonomy_id,$taxonomy){if(in_array($taxonomy,array('listing-category','location'),true))lk_invalidate_public_index();},10,3);
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
function lk_public_index(){if(!isset($GLOBALS['lk_public_index'])){$cached=get_transient('lk_public_directory_v1');$GLOBALS['lk_public_index']=is_array($cached)?$cached:array();}return $GLOBALS['lk_public_index'];}
function lk_current_open_status($open,$close){$now=current_time('H:i');return $open===$close||($open<$close?($now>=$open&&$now<$close):($now>=$open||$now<$close));}
function lk_directory_index(){
 $cached=lk_public_index();$index=array();foreach(lk_posts() as $post)$index[$post->ID]=lk_place_data($post);
 if(array_keys($cached)!==array_keys($index))set_transient('lk_public_directory_v1',$index,5*MINUTE_IN_SECONDS);
 $GLOBALS['lk_public_index']=$index;return array_values($index);
}
function lk_place_data($post){
 $p=get_post($post); $id=$p->ID;if(isset($GLOBALS['lk_place_cache'][$id]))return $GLOBALS['lk_place_cache'][$id];
 $index=lk_public_index();if($p->post_status==='publish'&&!$p->post_password&&isset($index[$id])){$data=$index[$id];$data['opened']=lk_current_open_status($data['open'],$data['close']);return $GLOBALS['lk_place_cache'][$id]=$data;}
 $o=get_post_meta($id,'lp_listingpro_options',true);if(!is_array($o))$o=array();
 $region=wp_get_post_terms($id,'location');$category=wp_get_post_terms($id,'listing-category');
 $open=get_post_meta($id,'_lk_open',true)?:'07:00';$close=get_post_meta($id,'_lk_close',true)?:'22:00';$opened=lk_current_open_status($open,$close);
 return $GLOBALS['lk_place_cache'][$id]=array('id'=>$id,'title'=>html_entity_decode(wp_strip_all_tags(get_the_title($p)),ENT_QUOTES,'UTF-8'),'url'=>get_permalink($p),'image'=>get_the_post_thumbnail_url($id,'medium_large')?:lk_asset('no-image'),'address'=>$o['gAddress']??'Long Khánh, Đồng Nai','phone'=>$o['phone']??'','min'=>(int)($o['list_price']??0),'max'=>(int)($o['list_price_to']??0),'lat'=>(float)($o['latitude']??10.9398),'lng'=>(float)($o['longitude']??107.2446),'open'=>$open,'close'=>$close,'opened'=>$opened,'region'=>!is_wp_error($region)&&$region? $region[0]->name:'Long Khánh','regionSlug'=>!is_wp_error($region)&&$region?$region[0]->slug:'','category'=>!is_wp_error($category)&&$category?$category[0]->slug:'','categories'=>!is_wp_error($category)?wp_list_pluck($category,'slug'):array(),'categoryNames'=>!is_wp_error($category)?implode(' ',wp_list_pluck($category,'name')):'','rating'=>(float)get_post_meta($id,'_lk_rating',true),'reviews'=>(int)get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve','count'=>true)));
}
function lk_card($post,$view='grid'){
 $d=lk_place_data($post);$price=$d['min']?lk_money($d['min']).' - '.lk_money($d['max']):'Đang cập nhật';
 echo '<article class="lk-place lk-place--'.esc_attr($view).'" data-place-id="'.$d['id'].'"><a class="lk-place-photo" href="'.esc_url($d['url']).'" tabindex="-1" aria-hidden="true"><img src="'.esc_url($d['image']).'" alt="" loading="lazy" width="400" height="300"></a><button class="lk-save" data-save="'.$d['id'].'" aria-label="Lưu '.esc_attr($d['title']).'" aria-pressed="false">'.lk_icon('bookmark').'</button><div class="lk-place-copy"><h3><a href="'.esc_url($d['url']).'">'.esc_html($d['title']).'</a></h3><div class="lk-list-rating"><span class="lk-stars" aria-label="'.esc_attr($d['rating']?'Điểm '.$d['rating'].' trên 5':'Chưa có đánh giá').'">'.($d['rating']?esc_html($d['rating']).' ':'').'☆☆☆☆☆</span><span>'.($d['rating']?'Đánh giá tham khảo':'Chưa có đánh giá').'</span></div><p class="lk-price">'.lk_icon('dollar-sign').' '.esc_html($price).'</p><p class="lk-address" title="'.esc_attr($d['address']).'">'.lk_icon('map-marker-alt').' '.esc_html($d['address']).'</p><div class="lk-place-meta"><span class="lk-region">'.lk_icon('map-marker-alt').' '.esc_html($d['region']).'</span><span class="lk-status '.($d['opened']?'is-open':'is-closed').'">'.($d['opened']?'Đang mở cửa':'Đang đóng cửa').'</span><span class="lk-hours"> - '.esc_html($d['open'].' - '.$d['close']).'</span></div></div></article>';
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
  $posts=array_values(array_filter($posts,function($p)use($current){return $p->ID!==$current['id'];}));
  usort($posts,function($a,$b)use($current){$da=lk_place_data($a);$db=lk_place_data($b);$distance=function($d)use($current){return pow(($d['lat']-$current['lat']),2)+pow(($d['lng']-$current['lng'])*cos(deg2rad($current['lat'])),2);};return $distance($da)<=>$distance($db);});
 }
 if(!empty($s['category']))$posts=array_filter($posts,function($p)use($s){return has_term($s['category'],'listing-category',$p);});
 if(!empty($s['tabs'])){echo '<div class="lk-tabs" role="tablist" aria-label="Danh mục địa điểm">';foreach(array(''=>'Mới','an-uong'=>'Ăn uống','khach-san'=>'Khách sạn','giai-tri'=>'Giải trí','mua-sam'=>'Mua sắm') as $slug=>$label)echo '<button role="tab" aria-selected="'.($slug===''?'true':'false').'" data-category="'.esc_attr($slug).'">'.esc_html($label).'</button>';echo '</div>';}
 echo '<div class="lk-place-grid" style="--lk-columns:'.(int)($s['columns']??4).'" data-limit="'.$limit.'">';foreach(array_slice($posts,0,$limit) as $p)lk_card($p);echo '</div>';
 if(!empty($s['more']))echo '<div class="lk-center"><a class="lk-button lk-outline" href="'.esc_url(lk_link('search')).'">'.esc_html($s['more']).'</a></div>';
}
function lk_results($s=array()){
 $q=isset($_GET['q'])?sanitize_text_field(wp_unslash($_GET['q'])):'';$cat=isset($_GET['categories'])?sanitize_title(wp_unslash($_GET['categories'])):'';$region=isset($_GET['regions'])?sanitize_title(wp_unslash($_GET['regions'])):'';$open=!empty($_GET['open']);$max=isset($_GET['max_price'])?absint($_GET['max_price']):300000;$sort=isset($_GET['sort'])?sanitize_key($_GET['sort']):'relevance';
 $posts=array_values(array_filter(lk_posts(),function($p)use($q,$cat,$region,$open,$max){$d=lk_place_data($p);$hay=lk_normalize_search($d['title'].' '.$d['address'].' '.$d['region'].' '.($d['categoryNames']??'').' '.wp_strip_all_tags($p->post_content));$needle=lk_normalize_search($q);return (!$q||mb_strpos($hay,$needle)!==false)&&(!$cat||has_term($cat,'listing-category',$p))&&(!$region||has_term($region,'location',$p))&&(!$open||$d['opened'])&&(!$max||!$d['min']||$d['min']<=$max);}));
 if($sort==='rating')usort($posts,function($a,$b){return lk_place_data($b)['rating']<=>lk_place_data($a)['rating'];});
 if($sort==='price')usort($posts,function($a,$b){return lk_place_data($a)['min']<=>lk_place_data($b)['min'];});
 $count=count($posts);$page=max(1,absint($_GET['pg']??1));$per=10;
 echo '<div class="lk-results-layout"><aside class="lk-filter-wrap"><button class="lk-map-preview" data-map> Xem bản đồ</button><form class="lk-filter" method="get" action="'.esc_url(lk_link('search')).'" id="lk-filter"><div class="lk-filter-heading"><h2>'.esc_html($s['filter_title']??'Lọc kết quả').'</h2><button type="button" class="lk-filter-close" aria-label="Đóng bộ lọc">&times;</button></div><input type="hidden" name="q" value="'.esc_attr($q).'">';
 echo '<fieldset><legend>Giờ mở cửa</legend><label><input type="radio" name="open" value="0" '.checked(!$open,true,false).'> Tất cả</label><label><input type="radio" name="open" value="1" '.checked($open,true,false).'> Đang mở cửa</label></fieldset>';
 foreach(array('categories'=>array('Danh mục','listing-category',$cat),'regions'=>array('Khu vực','location',$region)) as $name=>$config){echo '<fieldset><legend>'.esc_html($config[0]).'</legend><label><input type="radio" name="'.esc_attr($name).'" value="" '.checked($config[2],'',false).'> Tất cả</label>';foreach(lk_directory_terms($config[1]) as $term)echo '<label><input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($term->slug).'" '.checked($term->slug,$config[2],false).'> '.esc_html($term->name).'</label>';echo '</fieldset>';}
 echo '<fieldset><legend>Khoảng giá</legend><label for="lk-max-price">Tối đa <output>'.esc_html(lk_money($max)).'</output></label><input id="lk-max-price" type="range" name="max_price" min="10000" max="300000" step="10000" value="'.$max.'"></fieldset><fieldset><legend>Sắp xếp theo</legend><select name="sort" aria-label="Sắp xếp kết quả">';foreach(array('relevance'=>'Đúng nhất','rating'=>'Điểm đánh giá','price'=>'Giá tăng dần') as $k=>$v)echo '<option value="'.$k.'" '.selected($sort,$k,false).'>'.$v.'</option>';echo '</select></fieldset><div class="lk-filter-actions"><button class="lk-button" type="submit">Áp dụng</button><a class="lk-button lk-outline" href="'.esc_url(add_query_arg('q',$q,lk_link('search'))).'">Đặt lại</a></div></form></aside><div class="lk-results"><div class="lk-results-top"><p><strong>'.$count.'</strong> địa điểm'.($q?' cho <strong>“'.esc_html($q).'”</strong>':' khớp với tìm kiếm của bạn').'</p>'.(($cat||$region||$open)?'<a href="'.esc_url(add_query_arg('q',$q,lk_link('search'))).'">Xóa tất cả bộ lọc</a>':'').'</div><div class="lk-results-cards">';
 if(!$count){echo '<div class="lk-empty"><i class="fas fa-search" aria-hidden="true"></i><h2>Chưa tìm thấy địa điểm phù hợp</h2><p>Thử đổi từ khóa hoặc bỏ bớt bộ lọc để xem thêm địa điểm.</p><a class="lk-button" href="'.esc_url(lk_link('search')).'">Xem tất cả địa điểm</a></div>';}
 foreach(array_slice($posts,($page-1)*$per,$per) as $p)lk_card($p,'list');echo '</div>';
 if($count>$per){echo '<nav class="lk-pagination" aria-label="Trang kết quả">';for($i=1;$i<=ceil($count/$per);$i++)echo '<a '.($i===$page?'aria-current="page"':'').' href="'.esc_url(add_query_arg('pg',$i)).'">'.$i.'</a>';echo '</nav>';}
 echo '</div></div><div class="lk-result-tools"><button class="lk-filter-open">'.lk_icon('sliders-h').' Bộ lọc'.(($cat||$region||$open)?' <span class="lk-filter-dot"></span>':'').'</button><button data-map>'.lk_icon('map-marked-alt').' Bản đồ</button></div>';
}
function lk_reviews($s=array()){
 $id=lk_context_id()?:get_the_ID();$comments=get_comments(array('post_id'=>$id,'type'=>'lk_review','status'=>'approve'));echo '<div class="lk-reviews"><div class="lk-review-heading"><h2>'.esc_html($s['title']??'Đánh giá từ cộng đồng').' <span>('.count($comments).')</span></h2>'.(is_user_logged_in()?'<a class="lk-button" href="#lk-review-form">Viết đánh giá</a>':'<button class="lk-button lk-login-open">Viết đánh giá</button>').'</div>';
 if(!$comments){$image=$s['invite_image']['url']??lk_asset('review-illustration');echo '<div class="lk-review-invite">'.($image?'<img src="'.esc_url($image).'" alt="" loading="lazy">':'').'<div><h3>'.esc_html($s['invite_title']??'Bạn đã từng đến đây?').'</h3><p>'.esc_html($s['invite_copy']??'Chia sẻ trải nghiệm và cảm nhận của bạn cho mọi người cùng biết.').'</p><p>Những review chất lượng sẽ được xuất hiện ở bảng tin đấy!</p></div></div><p class="lk-review-empty">Chưa có đánh giá nào cho '.esc_html(get_the_title($id)).'. Hãy là người đầu tiên chia sẻ trải nghiệm!</p>';}
 foreach($comments as $c){echo '<article class="lk-review"><strong>'.esc_html($c->comment_author).'</strong><span class="lk-stars"> '.esc_html(get_comment_meta($c->comment_ID,'lk_rating',true)).'/5 ★</span><time>'.esc_html(get_comment_date('d/m/Y',$c)).'</time><p>'.nl2br(esc_html($c->comment_content)).'</p></article>';}
 if(is_user_logged_in()){
  echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="lk-form" id="lk-review-form"><input type="hidden" name="action" value="lk_review"><input type="hidden" name="place_id" value="'.$id.'">';wp_nonce_field('lk_review','lk_nonce');echo '<fieldset class="lk-review-criteria"><legend>Đánh giá trải nghiệm của bạn</legend>';
  foreach(lk_criteria_labels() as $key=>$label){echo '<label>'.esc_html($label).'<select name="criteria['.$key.']" required><option value="">Chọn điểm</option>';for($i=5;$i>=1;$i--)echo '<option value="'.$i.'">'.$i.' sao</option>';echo '</select></label>';}
  echo '</fieldset><label>Trải nghiệm của bạn<textarea name="review" required minlength="10" rows="4"></textarea></label><button class="lk-button">Gửi đánh giá</button></form>';
 }
 echo '</div>';
}

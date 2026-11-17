<?php
if(!defined('ABSPATH'))exit;

/** Read native ListingPro campaigns; rendering never activates or repairs one. */
function lk_ads_placements(){return array('lp_random_ads'=>'Spotlight','lp_top_in_search_page_ads'=>'Đầu kết quả tìm kiếm','lp_detail_page_ads'=>'Trang chi tiết');}
function lk_ads_mode($meta,$options){return ($meta['ads_mode']??'') ?: (($options['listingpro_ads_campaign_style']??'adsperduration')==='adsperclick'?'perclick':'byduration');}
function lk_ads_eligible($ad_id,$placement){
 if(!isset(lk_ads_placements()[$placement]))return false;
 $options=get_option('listingpro_options',array());if(!is_array($options))return false;
 if(array_key_exists($placement.'_switch',$options)&&empty($options[$placement.'_switch']))return false;
 $post=get_post($ad_id);if(!$post||$post->post_type!=='lp-ads'||$post->post_status!=='publish'||$post->post_password)return false;
 $meta=get_post_meta($ad_id,'lp_listingpro_options',true);if(!is_array($meta))return false;
 if(!is_array($meta['ad_type']??null)||!in_array($placement,$meta['ad_type'],true))return false;
 if(!empty($meta['ad_status'])&&(!is_string($meta['ad_status'])||strtolower($meta['ad_status'])!=='active'))return false;
 if(!is_scalar($meta['ads_listing']??null)||!ctype_digit((string)$meta['ads_listing']))return false;
 $id=absint($meta['ads_listing']);$listing=get_post($id);
 if(!$listing||$listing->post_type!=='listing'||$listing->post_status!=='publish'||$listing->post_password)return false;
 $listing_meta=get_post_meta($id,'lp_listingpro_options',true);
 if(!is_array($listing_meta)||!is_scalar($listing_meta['campaign_id']??null)||(int)$listing_meta['campaign_id']!==(int)$ad_id||get_post_meta($id,'campaign_status',true)!=='active'||get_post_meta($id,$placement,true)!=='active')return false;
 $mode=lk_ads_mode($meta,$options);
 if($mode==='perclick'){
  $price=$options[$placement.'_pc']??null;$balance=$meta['remaining_balance']??null;
  if(!is_numeric($price)||!is_finite((float)$price)||(float)$price<=0||!is_numeric($balance)||!is_finite((float)$balance)||(float)$balance<(float)$price)return false;
 }elseif($mode==='byduration'){
  if(!is_numeric($meta['duration']??null)||!is_finite((float)$meta['duration'])||(float)$meta['duration']<=0)return false;
  // Native payments record an explicit end date; admin-created campaigns may not.
  $end=get_post_meta($ad_id,'campain_expire_date',true);
  if($end){if(!is_string($end))return false;$date=DateTimeImmutable::createFromFormat('!d-m-Y',$end,wp_timezone());if(!$date||$date->format('d-m-Y')!==$end||$date->getTimestamp()<=current_datetime()->getTimestamp())return false;}
 }else return false;
 if(!empty($meta['ad_date'])){if(!is_string($meta['ad_date']))return false;$start=DateTimeImmutable::createFromFormat('!d-m-Y',$meta['ad_date'],wp_timezone());if(!$start||$start->format('d-m-Y')!==$meta['ad_date']||$start->getTimestamp()>current_datetime()->getTimestamp())return false;}
 return array('id'=>(int)$ad_id,'listing'=>$listing,'placement'=>$placement,'mode'=>$mode);
}
function lk_ads_select($placement,$limit=3,$eligible_ids=null,$exclude=0){
 if(!isset(lk_ads_placements()[$placement])||$eligible_ids===array())return array();
 $campaigns=get_posts(array('post_type'=>'lp-ads','post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'rand','update_post_term_cache'=>false));
 $result=array();foreach($campaigns as $campaign){
  $ad=lk_ads_eligible($campaign->ID,$placement);if(!$ad)continue;$id=$ad['listing']->ID;
  if($id===$exclude||isset($result[$id])||($eligible_ids!==null&&!in_array($id,$eligible_ids,true)))continue;
  $result[$id]=$ad;if(count($result)>=max(1,min(6,(int)$limit)))break;
 }
 return array_values($result);
}
function lk_ads_search_ids($posts,$state){
 if(!empty($state['nearby']))$posts=array_filter($posts,function($post){$data=lk_place_data($post);return $data['lat']!==null&&$data['lng']!==null;});
 return array_values(wp_list_pluck($posts,'ID'));
}
function lk_ads_clean_url($url){return remove_query_arg(array('ad_type','lk_ad_ticket'),$url);}
add_filter('post_type_link',function($url,$post){return $post->post_type==='listing'&&function_exists('lk_is_site')&&lk_is_site()?lk_ads_clean_url($url):$url;},100,2);
function lk_ads_signature($id,$placement,$expires,$ticket){return hash_hmac('sha256',implode('|',array($id,$placement,$expires,$ticket)),wp_salt('auth'));}
function lk_ads_url($ad){
 $target=lk_ads_clean_url(get_permalink($ad['listing']));
 if(current_user_can('manage_options')||(is_user_logged_in()&&get_current_user_id()===(int)$ad['listing']->post_author)||is_preview()||isset($_GET['elementor-preview']))return $target;
 $expires=time()+DAY_IN_SECONDS;$ticket=wp_generate_password(24,false,false);
 return add_query_arg(array('lk_ad_visit'=>1,'ad'=>$ad['id'],'placement'=>$ad['placement'],'expires'=>$expires,'ticket'=>$ticket,'signature'=>lk_ads_signature($ad['id'],$ad['placement'],$expires,$ticket)),home_url('/'));
}
function lk_ads_card_html($html,$ad){
 $tags=new WP_HTML_Tag_Processor($html);
 if($tags->next_tag('article')){$tags->add_class('lk-place--ad');$tags->set_attribute('data-ad-id',$ad['id']);}
 return str_replace('<h3>','<span class="lk-ad-label">Được tài trợ</span><h3>',$tags->get_updated_html());
}
function lk_render_ads($placement,$limit=3,$eligible_ids=null,$exclude=0){
 $ads=lk_ads_select($placement,$limit,$eligible_ids,$exclude);if(!$ads)return;
 $kind=array_search($placement,array('spotlight'=>'lp_random_ads','search'=>'lp_top_in_search_page_ads','detail'=>'lp_detail_page_ads'),true);
 echo '<section class="lk-ads lk-ads--'.esc_attr($kind).'" aria-label="Địa điểm được tài trợ" data-ad-placement="'.esc_attr($placement).'"><h2 class="lk-ads-title">Được tài trợ</h2><div class="lk-ads-grid">';
 foreach($ads as $ad)lk_card($ad['listing'],$kind==='search'?'list':'grid',$ad);
 echo '</div></section>';
}
function lk_ads_spotlight($settings){
 if(!is_front_page()||!empty($settings['nearby'])||!empty($GLOBALS['lk_ads_spotlight_rendered'])||!empty($GLOBALS['lk_ads_manual']['lp_random_ads']))return;
 $GLOBALS['lk_ads_spotlight_rendered']=true;lk_render_ads('lp_random_ads');
}

/** A cached card can be clicked once by each account/browser, without storing IPs. */
function lk_ads_visitor(){
 if(is_user_logged_in())return 'user:'.get_current_user_id();
 $cookie=is_string($_COOKIE['lk_ads_visitor']??null)?$_COOKIE['lk_ads_visitor']:'';$parts=explode('.',$cookie,2);
 if(count($parts)===2&&preg_match('/^[a-zA-Z0-9]{24}$/D',$parts[0])&&hash_equals(hash_hmac('sha256',$parts[0],wp_salt('nonce')),$parts[1]))return 'guest:'.$parts[0];
 $id=wp_generate_password(24,false,false);$value=$id.'.'.hash_hmac('sha256',$id,wp_salt('nonce'));$_COOKIE['lk_ads_visitor']=$value;
 if(PHP_SAPI!=='cli'&&!headers_sent())setcookie('lk_ads_visitor',$value,array('expires'=>time()+30*DAY_IN_SECONDS,'path'=>COOKIEPATH?:'/','secure'=>is_ssl(),'httponly'=>true,'samesite'=>'Lax'));
 return 'guest:'.$id;
}
/** Signed visits. Fees/counters remain in ListingPro's native metadata. */
function lk_ads_record_click($ad_id,$placement,$expires,$ticket,$signature){
 if(!is_scalar($expires)||!ctype_digit((string)$expires)||(int)$expires<time()||(int)$expires>time()+DAY_IN_SECONDS+60||!is_string($ticket)||!preg_match('/^[a-zA-Z0-9]{24}$/D',$ticket)||!is_string($signature)||!hash_equals(lk_ads_signature($ad_id,$placement,$expires,$ticket),$signature))return false;
 global $wpdb;$lock='lk-ads-'.substr(hash('sha256',DB_NAME.$wpdb->prefix.$ad_id),0,48);
 if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,2)',$lock))!=='1')return false;
 try{
  wp_cache_delete($ad_id,'post_meta');$ad=lk_ads_eligible($ad_id,$placement);
  if(!$ad||current_user_can('manage_options')||(is_user_logged_in()&&get_current_user_id()===(int)$ad['listing']->post_author))return false;
  $key='lk_ad_visit_'.$ad_id.'_'.hash('sha256',$ticket.'|'.lk_ads_visitor());if(get_transient($key))return false;
  // Refreshing/back/opening the same rendered card cannot consume another click.
  if(!set_transient($key,1,max(1,(int)$expires-time())))return false;
  if($ad['mode']==='perclick'){
   if(!function_exists('lp_count_clicks_for_campaigns'))return false;
   lp_count_clicks_for_campaigns($ad['listing']->ID,$ad_id,$placement.'_pc');
  }else listing_set_metabox('click_performed',max(0,(int)listing_get_metabox_by_ID('click_performed',$ad_id))+1,$ad_id);
  return true;
 }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
function lk_ads_visit(){
 $id=is_scalar($_GET['ad']??null)?absint($_GET['ad']):0;$placement=is_string($_GET['placement']??null)?sanitize_key($_GET['placement']):'';
 $meta=$id?get_post_meta($id,'lp_listingpro_options',true):array();$listing=is_array($meta)?get_post(absint($meta['ads_listing']??0)):null;
 $target=$listing&&$listing->post_type==='listing'&&$listing->post_status==='publish'&&!$listing->post_password?lk_ads_clean_url(get_permalink($listing)):home_url('/');
 nocache_headers();
 $purpose=strtolower(($_SERVER['HTTP_SEC_PURPOSE']??'').' '.($_SERVER['HTTP_PURPOSE']??''));
 if(($_SERVER['REQUEST_METHOD']??'')==='GET'&&strpos($purpose,'prefetch')===false)lk_ads_record_click($id,$placement,$_GET['expires']??null,$_GET['ticket']??null,$_GET['signature']??null);
 wp_safe_redirect($target,302,'LK Ads');exit;
}
// A frontend endpoint also works for subscribers without granting wp-admin access.
add_action('template_redirect',function(){if(isset($_GET['lk_ad_visit']))lk_ads_visit();},1);

/** Add a dynamic widget to a render-only copy of the current detail document. */
function lk_ads_manual_nodes($nodes){
 $found=array();foreach($nodes as $node){$placement=$node['settings']['placement']??'lp_random_ads';if(($node['widgetType']??'')==='lk-ads'&&empty($node['settings']['_lk_automatic_ad'])&&is_string($placement)&&isset(lk_ads_placements()[$placement]))$found[$placement]=true;$found+=lk_ads_manual_nodes($node['elements']??array());}return $found;
}
function lk_ads_detail_nodes($nodes,&$inserted){
 foreach($nodes as &$node){
  $settings=$node['settings']??array();$classes=preg_split('/\s+/',trim(($settings['css_classes']??'').' '.($settings['_css_classes']??'')));
  if(!$inserted&&in_array($node['elType']??'',array('column','container'),true)&&in_array('lk-map-column',$classes,true)){
   foreach($node['elements']??array() as $child)if(($child['widgetType']??'')==='lk-ads'&&($child['settings']['placement']??'')==='lp_detail_page_ads')$inserted=true;
   if(!$inserted)$node['elements'][]=array('id'=>'lkadsdt','elType'=>'widget','widgetType'=>'lk-ads','settings'=>array('placement'=>'lp_detail_page_ads','limit'=>2,'_lk_automatic_ad'=>true),'elements'=>array());$inserted=true;
  }elseif(!empty($node['elements']))$node['elements']=lk_ads_detail_nodes($node['elements'],$inserted);
 }
 return $nodes;
}
add_filter('elementor/frontend/builder_content_data',function($nodes,$document_id){
 $GLOBALS['lk_ads_manual']=($GLOBALS['lk_ads_manual']??array())+lk_ads_manual_nodes($nodes);
 if((int)$document_id!==(int)get_option('lk_template_listing')||get_query_var('lk_view'))return $nodes;
 if(!empty($GLOBALS['lk_ads_manual']['lp_detail_page_ads']))return $nodes;
 $inserted=false;return lk_ads_detail_nodes($nodes,$inserted);
},25,2);
function lk_ads_has_dynamic_nodes($nodes){
 foreach($nodes as $node){$settings=$node['settings']??array();if(in_array($node['widgetType']??'',array('lk-ads','lk-listings','lk-results'),true)||strpos(' '.($settings['css_classes']??'').' '.($settings['_css_classes']??'').' ',' lk-map-column ')!==false||lk_ads_has_dynamic_nodes($node['elements']??array()))return true;}return false;
}
function lk_ads_cache_ttl($value){
 $document=class_exists('Elementor\Plugin')?\Elementor\Plugin::$instance->documents->get_current():null;
 return $document&&lk_ads_has_dynamic_nodes($document->get_elements_data())?'disable':$value;
}
add_filter('option_elementor_element_cache_ttl','lk_ads_cache_ttl',25);add_filter('default_option_elementor_element_cache_ttl','lk_ads_cache_ttl',25);
add_action('elementor/page_templates/canvas/after_content',function(){
 if(is_singular('listing')&&!get_query_var('lk_view')&&lk_is_directory_listing(get_queried_object_id())&&!post_password_required()&&empty($GLOBALS['lk_ads_detail_rendered']))lk_render_ads('lp_detail_page_ads',2,null,get_queried_object_id());
},5);
add_action('wp_enqueue_scripts',function(){
 if(function_exists('lk_is_site')&&lk_is_site())wp_enqueue_style('lk-ads',LK_URL.'assets/ads.css',array('lk-site'),filemtime(LK_DIR.'assets/ads.css'));
},50);

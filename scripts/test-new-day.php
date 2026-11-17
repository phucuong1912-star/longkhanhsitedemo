<?php
/** Local transactional regression checks for new-day.rtf. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only');
function day_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
global $wpdb;
$home=(int)get_option('lk_page_map')['home'];$layout=get_post_meta($home,'_elementor_data',true);$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');
$fingerprint=function()use($wpdb){$out=array();foreach(array($wpdb->posts,$wpdb->postmeta,$wpdb->terms,$wpdb->termmeta,$wpdb->term_taxonomy,$wpdb->term_relationships) as $table)$out[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $out;};
$before=$fingerprint();$post=lk_posts()[0];$get=$_GET;
$wpdb->query('START TRANSACTION');
try{
 $tag=wp_insert_term('cưới hỏi đám cưới lkqa-newday','list-tags');if(is_wp_error($tag))throw new RuntimeException($tag->get_error_message());
 wp_set_object_terms($post->ID,array($tag['term_id']),'list-tags',true);
 foreach(array('cưới hỏi','dam cuoi lkqa-newday') as $q){$_GET=array('q'=>$q);day_check(in_array($post->ID,wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID'),true),'Tag keyword search: '.$q);}
 $data=lk_place_data($post);day_check(str_contains($data['tagNames'],'lkqa-newday'),'Suggestions receive current tag names');
 wp_update_term($tag['term_id'],'list-tags',array('name'=>'dịch vụ cưới lkqa-renamed'));
 $_GET=array('q'=>'lkqa-renamed');day_check(in_array($post->ID,wp_list_pluck(lk_filtered_posts(lk_search_state()),'ID'),true),'Renaming a tag invalidates searchable data');
 $feature=wp_insert_term('QA native icon','features');$fid=$feature['term_id'];wp_set_object_terms($post->ID,array($fid),'features');
 update_term_meta($fid,'_lk_icon','check');update_term_meta($fid,'lp_features_icon','fa fa-motorcycle');
 $bound=lk_resolve_listing_settings('icon-list',array('lk_binding'=>'benefits'),$post->ID);
 day_check($bound['icon_list'][0]['selected_icon']['value']==='fas fa-motorcycle','Detail uses admin-selected native icon instead of old generic tick');
 $widget=\Elementor\Plugin::$instance->elements_manager->create_element_instance(array('id'=>'d17icon','elType'=>'widget','widgetType'=>'icon-list','settings'=>$bound,'elements'=>array()));
 $GLOBALS['lk_listing_context']=$post->ID;ob_start();$widget->print_element();$icon_html=ob_get_clean();unset($GLOBALS['lk_listing_context']);file_put_contents(dirname(__DIR__).'/.codex-tools/new-day-icon.html',$icon_html);day_check(str_contains($icon_html,'motorcycle'),'Native Elementor widget renders the chosen amenity icon');
 update_term_meta($fid,'lp_features_icon','far fa-clock');day_check(lk_feature_icon($fid)['value']==='far fa-clock','Regular icon family is retained');
 update_term_meta($fid,'lp_features_icon','<script>');day_check(lk_feature_icon($fid)['value']==='fas fa-check','Invalid icon has safe fallback');
}finally{$wpdb->query('ROLLBACK');wp_cache_flush();$_GET=$get;unset($GLOBALS['lk_place_cache'],$GLOBALS['lk_public_index']);}
day_check($before===$fingerprint(),'Tag and icon fixtures rolled back; existing content preserved');
$settings=array('link'=>array('url'=>home_url('/bang-dieu-khien/')),'lk_member_profile_link'=>array('url'=>home_url('/ho-so-moi/')),'lk_member_saved_link'=>array('url'=>home_url('/da-luu-moi/')));
$links=lk_menu_member_links($settings);day_check($links['profile']===home_url('/ho-so-moi/')&&$links['saved']===home_url('/da-luu-moi/'),'Menu honors both Elementor URL controls');
unset($settings['lk_member_profile_link'],$settings['lk_member_saved_link']);$links=lk_menu_member_links($settings);day_check(str_starts_with($links['profile'],home_url('/bang-dieu-khien/'))&&$links['saved']===home_url('/bang-dieu-khien/').'#saved-places','Menu defaults follow the configured button URL');
$term=wp_get_post_terms($post->ID,'listing-category')[0];$settings=array('tabs'=>'yes','limit'=>3,'category_groups'=>array(array('tab_category'=>$term->slug,'selection_mode'=>'manual','listing_slugs'=>array($post->post_name),'tab_title'=>'Danh mục giữ nguyên')));
ob_start();lk_render_curated_listings($settings);$html=ob_get_clean();day_check(str_contains($html,'>Mới</button>')&&str_contains($html,'>Danh mục giữ nguyên</button>'),'New tab is restored ahead of unchanged category tabs');
file_put_contents(dirname(__DIR__).'/.codex-tools/new-day-tabs.html',$html);
day_check($layout===get_post_meta($home,'_elementor_data',true)&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Elementor layout and package unchanged');

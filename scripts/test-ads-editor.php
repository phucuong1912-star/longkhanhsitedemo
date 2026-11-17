<?php
/** Editor conversion is read-only; fixture saves are local and rolled back. */
if(PHP_SAPI!=='cli')exit(1);
define('LK_LAYOUT_EXPORTING',true);require dirname(__DIR__).'/wp-load.php';
if(wp_parse_url(home_url(),PHP_URL_HOST)!=='localhost')throw new RuntimeException('Local only.');
add_filter('pre_wp_mail','__return_false');
function ads_editor_check($ok,$label){if(!$ok)throw new RuntimeException($label);echo 'PASS '.$label."\n";}
global $wpdb;$tables=array($wpdb->posts,$wpdb->postmeta,$wpdb->users,$wpdb->usermeta,$wpdb->options);
foreach($tables as $table){$row=$wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS WHERE Name = %s',$table));if(strcasecmp($row->Engine,'InnoDB')!==0)throw new RuntimeException('Need transactional fixtures.');}
$fingerprint=function()use($wpdb,$tables){$data=array();foreach($tables as $table)$data[$table]=hash('sha256',serialize($wpdb->get_results("SELECT * FROM `$table` ORDER BY 1,2",ARRAY_A)));return $data;};$before=$fingerprint();$manifest=hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json');$failed=false;$front=null;
$wpdb->query('START TRANSACTION');
try{
 $uid=wp_insert_user(array('user_login'=>'lk_ads_editor_test_'.wp_generate_password(10,false),'user_pass'=>wp_generate_password(40),'role'=>'administrator'));if(is_wp_error($uid))throw new RuntimeException('Cannot create fixture owner.');wp_set_current_user($uid);
 $id=wp_insert_post(array('post_type'=>'page','post_title'=>'Ads editor fixture','post_status'=>'publish','post_author'=>$uid));update_post_meta($id,'_elementor_edit_mode','builder');
 $front=function()use($id){return $id;};add_filter('pre_option_page_on_front',$front);
 $nodes=array(array('id'=>'section','elType'=>'section','settings'=>array('css_classes'=>'prod-custom-section'),'elements'=>array(array('id'=>'column','elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>array(array('id'=>'heading','elType'=>'widget','widgetType'=>'heading','settings'=>array('title'=>'User title','link'=>array('url'=>'/changed-on-prod/')),'elements'=>array()),array('id'=>'list','elType'=>'widget','widgetType'=>'lk-listings','settings'=>array('limit'=>4,'columns'=>4,'listing_selection'=>'curated','category_groups'=>array(array('tab_title'=>'Custom category','listing_slugs'=>array('user-slug')))),'elements'=>array()))))));
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes)));$raw=get_post_meta($id,'_elementor_data',true);
 $converted=lk_ads_editor_elements($nodes,$id);$children=$converted['elements'][0]['elements'][0]['elements'];
 ads_editor_check($converted['managed']&&count($children)===3&&$children[1]['widgetType']==='lk-ads'&&$children[2]===$nodes[0]['elements'][0]['elements'][1],'Ads becomes a real sibling widget without altering curated listings or user edits');
 ads_editor_check($children[0]===$nodes[0]['elements'][0]['elements'][0]&&$converted['elements'][0]['settings']===$nodes[0]['settings'],'Titles, links, classes and existing node IDs are retained');
 ads_editor_check(lk_ads_editor_elements($converted['elements'],$id)['elements']===$converted['elements'],'Repeated conversion is idempotent and preserves widget IDs');
 $styled=lk_ads_editor_node($id,'lp_random_ads','anchor',array(),array('accent'=>'#143322','type_font_family'=>'Arial','__globals__'=>array('accent'=>'globals/colors?id=primary','unrelated'=>'do-not-copy'),'category_groups'=>array('private-selection')));
 ads_editor_check($styled['settings']['accent']==='#143322'&&$styled['settings']['type_font_family']==='Arial'&&$styled['settings']['__globals__']===array('accent'=>'globals/colors?id=primary')&&!isset($styled['settings']['category_groups']),'New Ads widget retains compatible theme styles without copying listing selection settings');
 $search=$nodes;$search[0]['elements'][0]['elements'][1]['widgetType']='lk-results';$search_nodes=lk_ads_editor_elements($search,$id)['elements'];
 ads_editor_check($search_nodes[0]['elements'][0]['elements'][1]['settings']['placement']==='lp_top_in_search_page_ads','Search converts to a separate search placement widget');
 $detail=array(array('id'=>'detail','elType'=>'column','settings'=>array('css_classes'=>'lk-map-column user-class'),'elements'=>array()));$detail_nodes=lk_ads_editor_elements($detail,(int)get_option('lk_template_listing'))['elements'];
 ads_editor_check($detail_nodes[0]['elements'][0]['widgetType']==='lk-ads'&&empty($detail_nodes[0]['elements'][0]['settings']['_lk_automatic_ad']),'Detail conversion produces an editable widget rather than a frontend-only node');
 wp_set_current_user(0);ads_editor_check(lk_ads_editor_load($nodes,$id)===$nodes,'Guest cannot materialize editor models');wp_set_current_user($uid);
 $plugin=\Elementor\Plugin::$instance;$plugin->editor->set_post_id($id);$plugin->editor->set_edit_mode(true);$doc=$plugin->documents->get($id);
 $editor_nodes=apply_filters('elementor/document/load/data',$nodes,$doc);$config=apply_filters('elementor/document/config',array(),$id);
 ads_editor_check($editor_nodes===$converted['elements']&&($config['settings']['settings']['lk_ads_layout_mode']??'')==='widgets'&&!isset($config['elements']),'Official editor hooks expose widget models and their persisted mode without recursive element merges');
 ads_editor_check(get_post_meta($id,'_elementor_data',true)===$raw&&!get_post_meta($id,'_elementor_page_settings',true),'Opening the editor performs no layout or page setting writes');
 ob_start();lk_render_ads('lp_random_ads',3,array());$placeholder=ob_get_clean();ads_editor_check(strpos($placeholder,'lk-ads-editor-placeholder')!==false,'Editor keeps the widget selectable when no eligible campaign exists');
 $plugin->editor->set_edit_mode(false);ob_start();lk_render_ads('lp_random_ads',3,array());ads_editor_check(ob_get_clean()==='','Empty editor placeholders never appear to frontend visitors');$plugin->editor->set_edit_mode(true);
 // Save a deliberate deletion through Elementor's public document API.
 $doc->save(array('elements'=>$nodes,'settings'=>array('lk_ads_layout_mode'=>'widgets','post_status'=>'publish')));
 unset($GLOBALS['lk_ads_widget_documents'][$id]);$plugin->editor->set_edit_mode(false);
 ads_editor_check((get_post_meta($id,'_elementor_page_settings',true)['lk_ads_layout_mode']??'')==='widgets'&&!lk_ads_auto_enabled($id),'Saving widget deletion disables automatic fallback on the page');
 $plugin->editor->set_edit_mode(true);ads_editor_check(lk_ads_editor_load($nodes,$id)===$nodes,'Reopening a saved page does not recreate the deleted Ads widget');$plugin->editor->set_edit_mode(false);
}catch(Throwable $error){$failed=true;fwrite(STDERR,$error->getMessage()."\n");}
finally{$wpdb->query('ROLLBACK');if($front)remove_filter('pre_option_page_on_front',$front);if(class_exists('Elementor\Plugin'))\Elementor\Plugin::$instance->editor->set_edit_mode(false);wp_set_current_user(0);wp_cache_flush();}
ads_editor_check($before===$fingerprint()&&$manifest===hash_file('sha256',WP_CONTENT_DIR.'/lk-layouts/manifest.json'),'Existing data and the versioned Elementor package remain unchanged');
exit($failed?1:0);

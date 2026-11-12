<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$id=get_option('lk_page_map')['explore'];$data=json_decode(get_post_meta($id,'_elementor_data',true),true);
function lk_explore_widget($id,$type,$settings){return array('id'=>$id,'elType'=>'widget','widgetType'=>$type,'settings'=>$settings,'elements'=>array());}
function lk_explore_row($id,$elements,$classes){return array('id'=>$id,'elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>$classes,'gap'=>'no'),'elements'=>array(array('id'=>substr(md5($id),0,8),'elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>$elements)));}
$sidebar=&$data[0]['elements'][1];
if(!str_contains($sidebar['settings']['css_classes'],'lk-explore-sidebar')){
 $heading=null;$listings=null;foreach($sidebar['elements'] as $node){if(($node['widgetType']??'')==='heading'&&str_contains($node['settings']['title'],'Địa điểm'))$heading=$node;if(($node['widgetType']??'')==='lk-listings')$listings=$node;}
 $sidebar['settings']['css_classes']='lk-explore-sidebar';$listings['settings']['more']='';
 $tabs=array('id'=>'eb562be1','elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>'lk-feed-tabs lk-panel','gap'=>'no'),'elements'=>array());
 foreach(array('featured'=>array('Bài nổi bật','star'),'following'=>array('Đang theo dõi','user-check')) as $mode=>$tab)$tabs['elements'][]=array('id'=>substr(md5($mode),0,8),'elType'=>'column','settings'=>array('_column_size'=>50),'elements'=>array(lk_explore_widget(substr(md5($mode.'button'),0,8),'button',array('text'=>$tab[0],'link'=>array('url'=>$mode==='featured'?lk_link('explore'):add_query_arg('lk_feed','following',lk_link('explore'))),'selected_icon'=>array('value'=>'fas fa-'.$tab[1],'library'=>'fa-solid'),'_css_classes'=>'lk-feed-tab'))));
 $sidebar['elements']=array($tabs,lk_explore_row('eb562be2',array($heading,$listings),'lk-sidebar-panel lk-panel lk-sidebar-list'),lk_explore_row('eb562be3',array(lk_explore_widget('eb562be4','heading',array('title'=>'Người dùng tích cực','header_size'=>'h3')),lk_explore_widget('eb562be5','lk-feed',array('mode'=>'authors'))),'lk-sidebar-panel lk-panel'));
 $data[0]['settings']['css_classes'].=' lk-explore-layout';
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 Elementor\Plugin::$instance->files_manager->clear_cache();
}
function lk_fix_feed_links($nodes){foreach($nodes as &$node){$url=$node['settings']['link']['url']??'';if($url&&wp_parse_url($url,PHP_URL_QUERY)==='feed=following')$node['settings']['link']['url']=add_query_arg('lk_feed','following',remove_query_arg('feed',$url));if(!empty($node['elements']))$node['elements']=lk_fix_feed_links($node['elements']);}return $nodes;}
$data=lk_fix_feed_links($data);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_element_cache');
echo 'Explore sidebar is using editable native sections.',PHP_EOL;

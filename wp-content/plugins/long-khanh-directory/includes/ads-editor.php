<?php
if(!defined('ABSPATH'))exit;

/** Compatibility is read-only until the user saves their current Elementor page. */
function lk_ads_document_mode($id=0,$include_request=true){
 $current=class_exists('Elementor\Plugin')?\Elementor\Plugin::$instance->documents->get_current():null;
 if(!$id&&$current)$id=$current->get_main_id();
 if(!$id)$id=get_queried_object_id();
 if($include_request&&!empty($GLOBALS['lk_ads_widget_documents'][$id]))return 'widgets';
 $settings=$current&&(int)$current->get_main_id()===(int)$id?$current->get_settings():get_post_meta($id,'_elementor_page_settings',true);
 return is_array($settings)&&($settings['lk_ads_layout_mode']??'')==='widgets'?'widgets':'auto';
}
function lk_ads_auto_enabled($id=0){return lk_ads_document_mode($id)==='auto';}
function lk_ads_editor_context($id=0){
 if(!class_exists('Elementor\Plugin'))return false;
 $plugin=\Elementor\Plugin::$instance;
 if(!$plugin->editor->is_edit_mode()&&!$plugin->preview->is_preview_mode())return false;
 $root=(int)$plugin->editor->get_post_id();
 if(!$root)$root=absint($_GET['elementor-preview']??0);
 return $root&&(!$id||$root===(int)$id)&&current_user_can('edit_post',$root);
}
function lk_ads_editor_node($document_id,$placement,$anchor,$used,$source=array()){
 $index=0;do{$id=substr(hash('sha256','lk-ads|'.$document_id.'|'.$placement.'|'.$anchor.'|'.$index++),0,8);}while(isset($used[$id]));
 $style=array();foreach($source as $key=>$value)if(in_array($key,array('accent','radius'),true)||strpos($key,'type_')===0)$style[$key]=$value;
 foreach((array)($source['__globals__']??array()) as $key=>$value)if(in_array($key,array('accent','radius'),true)||strpos($key,'type_')===0)$style['__globals__'][$key]=$value;
 return array('id'=>$id,'elType'=>'widget','widgetType'=>'lk-ads','settings'=>array_merge($style,array('placement'=>$placement,'limit'=>$placement==='lp_detail_page_ads'?2:3,'title'=>'Được tài trợ','_title'=>'Được tài trợ — '.lk_ads_placements()[$placement])),'elements'=>array());
}
function lk_ads_node_ids($nodes){$ids=array();foreach($nodes as $node){if(isset($node['id']))$ids[$node['id']]=true;$ids+=lk_ads_node_ids($node['elements']??array());}return $ids;}
function lk_ads_editor_elements($nodes,$document_id){
 $manual=lk_ads_manual_nodes($nodes);$used=lk_ads_node_ids($nodes);$done=$manual;
 $home=(int)$document_id===(int)get_option('page_on_front');$detail=(int)$document_id===(int)get_option('lk_template_listing');
 $map=get_option('lk_page_map',array());$search=is_array($map)&&(int)$document_id===(int)($map['search']??0);$managed=$home||$detail||$search||!empty($manual);
 $walk=function($items)use(&$walk,&$done,&$managed,&$used,$home,$detail,$document_id){
  $result=array();foreach($items as $node){
   $type=$node['widgetType']??'';$placement='';
   if($home&&$type==='lk-listings'&&empty($node['settings']['nearby']))$placement='lp_random_ads';
   if($type==='lk-results')$placement='lp_top_in_search_page_ads';
   if($placement){$managed=true;if(empty($done[$placement])){$ad=lk_ads_editor_node($document_id,$placement,$node['id']??'list',$used,$node['settings']??array());$result[]=$ad;$used[$ad['id']]=true;$done[$placement]=true;}}
   if(!empty($node['elements']))$node['elements']=$walk($node['elements']);
   $settings=$node['settings']??array();$classes=preg_split('/\s+/',trim(($settings['css_classes']??'').' '.($settings['_css_classes']??'')));
   if($detail&&in_array($node['elType']??'',array('column','container'),true)&&in_array('lk-map-column',$classes,true)){
    $managed=true;if(empty($done['lp_detail_page_ads'])){$ad=lk_ads_editor_node($document_id,'lp_detail_page_ads',$node['id']??'detail',$used);$node['elements'][]=$ad;$used[$ad['id']]=true;$done['lp_detail_page_ads']=true;}
   }
   $result[]=$node;
  }return $result;
 };
 $elements=$walk($nodes);
 return array('elements'=>$elements,'managed'=>$managed);
}
function lk_ads_editor_load($nodes,$document_id){
 if(!lk_ads_editor_context($document_id)||lk_ads_document_mode($document_id,false)==='widgets')return $nodes;
 $result=lk_ads_editor_elements($nodes,$document_id);
 if($result['managed'])$GLOBALS['lk_ads_widget_documents'][$document_id]=true;
 return $result['elements'];
}
add_filter('elementor/document/load/data',function($nodes,$document){return lk_ads_editor_load($nodes,$document->get_main_id());},25,2);
add_filter('elementor/frontend/builder_content_data','lk_ads_editor_load',24,2);
add_filter('elementor/document/config',function($config,$id){
 if(lk_ads_editor_context($id)&&!empty($GLOBALS['lk_ads_widget_documents'][$id]))$config['settings']['settings']['lk_ads_layout_mode']='widgets';
 return $config;
},25,2);
add_action('elementor/documents/register_controls',function($document){
 if(!$document::get_property('has_elements'))return;
 $document->start_controls_section('lk_ads_page',array('label'=>'Quảng cáo','tab'=>\Elementor\Controls_Manager::TAB_SETTINGS));
 $document->add_control('lk_ads_layout_mode',array('label'=>'Bố trí quảng cáo','type'=>'select','options'=>array('auto'=>'Tự động theo vị trí','widgets'=>'Widget riêng (kéo thả / xóa)'),'default'=>'auto','description'=>'Khi dùng widget riêng, xóa widget rồi lưu sẽ bỏ khối đó khỏi trang. Quảng cáo tự động không được chèn lại.'));
 $document->end_controls_section();
});

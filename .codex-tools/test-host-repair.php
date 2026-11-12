<?php
if(PHP_SAPI!=='cli')exit;require dirname(__DIR__).'/wp-load.php';require WP_PLUGIN_DIR.'/long-khanh-host-repair/long-khanh-host-repair.php';
$sample=array('id'=>102,'url'=>'http://localhost:180/longkhanhsite/a/?x=1','external'=>'https://example.com/a','nested'=>array('html'=>'<a href="http://localhost:180/longkhanhsite/blog/">x</a>','number'=>42,'enabled'=>false));
$changed=lkr_rebase($sample,'https://longkhanhsites.site.je/');
$checks=array('nested_urls'=>$changed['url']==='https://longkhanhsites.site.je/a/?x=1'&&strpos($changed['nested']['html'],'localhost')===false,'preserve_other_fields'=>$changed['external']===$sample['external']&&$changed['nested']['number']===42&&$changed['nested']['enabled']===false);
$old=json_decode(get_post_meta(get_option('page_on_front'),'_lk_before_mobile_app_20261112',true),true);
if(!$old)throw new RuntimeException('Legacy app fixture missing');
$upgraded=lkr_app_nodes($old);$art=0;$images=0;foreach($upgraded as $section)if(lkr_has_class($section['settings'],'lk-app-panel'))foreach($section['elements'] as $col)if(lkr_has_class($col['settings'],'lk-app-art')){$art++;$images=count($col['elements']);}
$checks['legacy_app_native_images']=$art===1&&$images===2;$checks['repeat_preserves_edits']=lkr_app_nodes($upgraded)===$upgraded;
echo wp_json_encode($checks),PHP_EOL;exit(in_array(false,$checks,true)?1:0);

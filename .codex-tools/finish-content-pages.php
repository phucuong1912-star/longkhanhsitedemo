<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$page_map=get_option('lk_page_map');$copy=json_decode(file_get_contents(__DIR__.'/content-pages.json'),true);$media=get_option('lk_media_map');
function lk_finish_node($key,$type,$settings){return array('id'=>substr(md5('finished-'.$key),0,8),'elType'=>'widget','widgetType'=>$type,'settings'=>$settings,'elements'=>array());}
function lk_finish_heading($key,$title,$level='h2'){return lk_finish_node($key,'heading',array('title'=>$title,'header_size'=>$level,'typography_typography'=>'custom','typography_font_weight'=>'500'));}
function lk_finish_text($key,$html){return lk_finish_node($key,'text-editor',array('editor'=>$html));}
function lk_finish_col($key,$width,$items,$classes=''){return array('id'=>substr(md5('finished-col-'.$key),0,8),'elType'=>'column','settings'=>array('_column_size'=>$width,'css_classes'=>$classes),'elements'=>$items);}
function lk_finish_section($key,$cols,$classes){return array('id'=>substr(md5('finished-section-'.$key),0,8),'elType'=>'section','settings'=>array('css_classes'=>$classes,'gap'=>'no'),'elements'=>$cols);}
function lk_finish_save($id,$nodes){$old=get_post_meta($id,'_elementor_data',true);if(!get_post_meta($id,'_lk_before_content_20261112',true))update_post_meta($id,'_lk_before_content_20261112',wp_slash($old));update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');echo 'Finished native document '.$id,PHP_EOL;}
foreach(array('privacy','termsofuse') as $slug){
 $c=$copy[$slug];$items=array(lk_finish_heading($slug.'title',$c['title'],'h1'));
 if(!empty($c['intro']))$items[]=lk_finish_text($slug.'intro','<p>'.esc_html($c['intro']).'</p>');
 foreach($c['sections'] as $i=>$section){
  $items[]=lk_finish_heading($slug.$i,$section['title']);
  if(!empty($section['body']))$items[]=lk_finish_text($slug.$i.'body','<p>'.esc_html($section['body']).'</p>');
  if(!empty($section['items']))$items[]=lk_finish_text($slug.$i.'list','<ul><li>'.implode('</li><li>',array_map('esc_html',$section['items'])).'</li></ul>');
  foreach($section['children']??array() as $j=>$child){$items[]=lk_finish_heading($slug.$i.$j,$child['title'],'h3');$items[]=lk_finish_text($slug.$i.$j.'body','<p>'.esc_html($child['body']).'</p>');}
 }
 $items[]=lk_finish_text($slug.'contact','<p><a href="'.esc_url(lk_link('contact')).'">Liên hệ - Góp ý</a> · <a href="'.esc_url(lk_link($slug==='privacy'?'termsofuse':'privacy')).'">'.($slug==='privacy'?'Điều khoản sử dụng':'Chính sách bảo mật').'</a></p>');
 lk_finish_save($page_map[$slug],array(lk_finish_section($slug,array(lk_finish_col($slug,100,$items)),'lk-section lk-legal')));
}
$about=$copy['about'];$paragraphs=array(lk_finish_heading('abouttitle',$about['title'],'h1'));foreach($about['paragraphs'] as $i=>$p)$paragraphs[]=lk_finish_text('about'.$i,'<p>'.esc_html($p).'</p>');
lk_finish_save($page_map['about'],array(lk_finish_section('about',array(lk_finish_col('aboutart',40,array(lk_finish_node('aboutart','image',array('image'=>array('id'=>0,'url'=>LK_URL.'assets/images/about-illustration.svg'),'image_size'=>'full'))),'lk-about-art'),lk_finish_col('aboutcopy',60,$paragraphs,'lk-about-copy')),'lk-section lk-about')));
$stores=array();foreach(array('appstore'=>'https://apps.apple.com/vn/app/id1537080613','playstore'=>'https://play.google.com/store/apps/details?id=com.diadiemlongkhanh.app') as $key=>$url)$stores[]=lk_finish_col('download'.$key,50,array(lk_finish_node('download'.$key,'image',array('image'=>$media[$key],'image_size'=>'full','link_to'=>'custom','link'=>array('url'=>$url,'is_external'=>true)))));
$store_row=lk_finish_section('downloadstores',$stores,'lk-download-stores');$store_row['isInner']=true;
lk_finish_save($page_map['download'],array(lk_finish_section('download',array(lk_finish_col('download',100,array(lk_finish_heading('downloadtitle','Tải app để trải nghiệm và nhận ưu đãi!','h1'),lk_finish_node('downloadqr','image',array('image'=>$media['qrcode'],'image_size'=>'full','_css_classes'=>'lk-download-qr')),$store_row))),'lk-section lk-download-page')));
lk_finish_save($page_map['new-review'],array(lk_finish_section('newreview',array(lk_finish_col('newreview',100,array(lk_finish_heading('newreviewtitle','Viết Review','h1'),lk_finish_node('newreviewform','lk-form',array('kind'=>'review','button'=>'Gửi đánh giá của bạn'))))),'lk-section lk-compose-page')));
Elementor\Plugin::$instance->files_manager->clear_cache();

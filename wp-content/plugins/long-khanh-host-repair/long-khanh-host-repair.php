<?php
/**
 * Plugin Name: Long Khánh - Sửa lỗi chuyển hosting
 * Description: Khôi phục thư viện bị bỏ sót khi deploy và sửa URL/bố cục Elementor sau chuyển host. Chỉ chạy theo thao tác quản trị có nonce, có bản sao và hoàn tác dữ liệu.
 * Version: 1.0.1
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) exit;

function lkr_manifest() {
 $file=__DIR__.'/payload-manifest.json';
 return is_file($file)?json_decode(file_get_contents($file),true):array();
}
function lkr_target_file($entry) {
 $relative=$entry['relative'];
 if(strpos($relative,'..')!==false||strpos($relative,'\\')!==false||substr($relative,0,1)==='/')throw new RuntimeException('Đường dẫn thư viện không hợp lệ.');
 if($entry['group']==='wordpress')return ABSPATH.'wp-includes/js/dist/vendor/'.$relative;
 if($entry['group']==='elementor')return (defined('ELEMENTOR_PATH')?ELEMENTOR_PATH:WP_PLUGIN_DIR.'/elementor/').'vendor/'.$relative;
 throw new RuntimeException('Nhóm thư viện không hợp lệ.');
}
function lkr_library_status() {
 $manifest=lkr_manifest();$missing=array();$blocked=array();
 foreach($manifest['files']??array() as $entry){
  $target=lkr_target_file($entry);
  if(is_file($target)&&filesize($target)>0)continue;
  $missing[]=$entry;
  $version=$entry['group']==='wordpress'?$GLOBALS['wp_version']:(defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'');
  if($version!==$manifest['versions'][$entry['group']])$blocked[]=$entry['group'];
 }
 return array('manifest'=>$manifest,'missing'=>$missing,'blocked'=>array_unique($blocked));
}
function lkr_document_ids() {
 $ids=get_posts(array('post_type'=>array('page','post','elementor_library','listing'),'post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>'_elementor_data','suppress_filters'=>true));
 $ids[]=get_option('elementor_active_kit');
 return array_values(array_unique(array_filter(array_map('absint',$ids))));
}
function lkr_rebase($value,$target) {
 if(is_array($value)){foreach($value as $key=>$item)$value[$key]=lkr_rebase($item,$target);return $value;}
 if(!is_string($value))return $value;
 return str_replace(array('http://localhost:180/longkhanhsite','https://localhost:180/longkhanhsite'),rtrim($target,'/'),$value);
}
function lkr_has_class($settings,$class) {return in_array($class,preg_split('/\s+/',trim($settings['css_classes']??'')),true);}
function lkr_media($key) {
 $map=get_option('lk_media_map',array());$id=absint($map[$key]['id']??0);
 if(!$id||!wp_attachment_is_image($id))throw new RuntimeException('Không tìm thấy ảnh '.$key.'. Chưa thay đổi dữ liệu trang.');
 return array('id'=>$id,'url'=>wp_get_attachment_url($id));
}
function lkr_image_widget($key,$classes,$hide,$context='') {
 return array('id'=>substr(md5('lk-host-image-'.$context.$key),0,8),'elType'=>'widget','widgetType'=>'image','settings'=>array_merge(array('image'=>lkr_media($key),'image_size'=>'full','_css_classes'=>$classes),$hide),'elements'=>array());
}
function lkr_app_nodes($nodes) {
 foreach($nodes as &$node){
  if(($node['elType']??'')==='section'&&lkr_has_class($node['settings'],'lk-app-panel')){
   // Preserve a layout already upgraded or edited on the host.
   $modern=false;foreach($node['elements'] as $col)if(lkr_has_class($col['settings'],'lk-app-art'))$modern=true;
   if(!$modern){
    // Validate media before changing any part of this document.
    lkr_media('app-desktop');lkr_media('app-mobile');
    foreach(array_keys($node['settings']) as $key)if(strpos($key,'background_')===0)unset($node['settings'][$key]);
    $node['settings']['background_background']='classic';$node['settings']['background_color']='#d9ecdc';$node['settings']['gap']='no';
    $copy=&$node['elements'][0];$copy['settings']['css_classes']='lk-app-panel-copy';$copy['settings']['_column_size']=55;
    $copy['settings']['_inline_size_tablet']=$copy['settings']['_inline_size_mobile']=100;
    $copy['settings']['padding']=array('unit'=>'px','top'=>'34','right'=>'40','bottom'=>'34','left'=>'40','isLinked'=>false);
    $copy['settings']['padding_tablet']=$copy['settings']['padding_mobile']=array('unit'=>'px','top'=>'10','right'=>'10','bottom'=>'10','left'=>'10','isLinked'=>true);
    foreach($copy['elements'] as &$widget){
     if(($widget['widgetType']??'')==='heading'){$widget['settings']['typography_typography']='custom';$widget['settings']['typography_font_weight']='700';$widget['settings']['typography_line_height']=array('unit'=>'em','size'=>1.5715);}
     if(lkr_has_class($widget['settings'],'lk-store-links')){
      $widget['elements'][0]['settings']['css_classes']='lk-qr-column';$widget['elements'][0]['settings']['hide_mobile']='hidden-mobile';$widget['elements'][0]['settings']['hide_tablet']='hidden-tablet';
      $widget['elements'][1]['settings']['css_classes']='lk-badges';$widget['elements'][1]['settings']['_inline_size_mobile']=$widget['elements'][1]['settings']['_inline_size_tablet']=100;
     }
    }unset($widget,$copy);
    $context=$node['id'];$node['elements'][1]=array('id'=>substr(md5('lk-host-art-'.$context),0,8),'elType'=>'column','settings'=>array('_column_size'=>45,'_inline_size_mobile'=>100,'_inline_size_tablet'=>100,'css_classes'=>'lk-app-art'),'elements'=>array(lkr_image_widget('app-desktop','lk-app-desktop',array('hide_tablet'=>'hidden-tablet','hide_mobile'=>'hidden-mobile'),$context),lkr_image_widget('app-mobile','lk-app-mobile',array('hide_desktop'=>'hidden-desktop'),$context)));
   }
  }
  if(!empty($node['elements']))$node['elements']=lkr_app_nodes($node['elements']);
 }unset($node);return $nodes;
}
function lkr_clear_cache($ids) {
 foreach($ids as $id){delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');}
 delete_transient('lk_public_directory_v1');
 if(function_exists('lk_invalidate_public_index'))lk_invalidate_public_index();
 if(class_exists('Elementor\\Plugin'))\Elementor\Plugin::$instance->files_manager->clear_cache();
}
function lkr_guard($action) {if(!current_user_can('manage_options'))wp_die('Không có quyền.');check_admin_referer($action);}
function lkr_redirect($message) {update_option('lkr_host_last_result',$message,false);wp_safe_redirect(admin_url('tools.php?page=lk-host-repair'));exit;}

add_action('admin_post_lkr_restore_files',function(){
 lkr_guard('lkr_restore_files');$status=lkr_library_status();
 if(empty($status['manifest']['files'])||$status['blocked'])wp_die('Gói thư viện không khớp phiên bản hiện tại. Không cập nhật WordPress hoặc Elementor.');
 $count=0;$errors=array();
 foreach($status['missing'] as $entry){
  $source=__DIR__.'/payload/'.$entry['group'].'/'.$entry['relative'];$target=lkr_target_file($entry);
  if(!is_file($source)||hash_file('sha256',$source)!==$entry['sha256']){$errors[]=$entry['group'].'/'.$entry['relative'];continue;}
  if(!wp_mkdir_p(dirname($target))||!copy($source,$target)){$errors[]=$entry['group'].'/'.$entry['relative'];continue;}$count++;
 }
 lkr_redirect('Đã khôi phục '.$count.' file bị thiếu, giữ nguyên phiên bản. Số file chưa chép được: '.count($errors).'.');
});
add_action('admin_post_lkr_child_scripts',function(){
 lkr_guard('lkr_child_scripts');
 if(!current_user_can('edit_themes')||get_stylesheet()!=='listingpro-longkhanh-child')wp_die('Chỉ sửa child theme Long Khánh đang hoạt động.');
 $path=get_stylesheet_directory().'/functions.php';$original=file_get_contents($path);
 if(strpos($original,"\$GLOBALS['concatenate_scripts'] = false;")!==false)lkr_redirect('Child theme đã tải riêng thư viện quản trị.');
 if(!is_writable($path))wp_die('Không ghi được child theme. Chưa thay đổi file.');
 update_option('lkr_host_child_backup',array('time'=>current_time('mysql'),'content'=>$original),false);
 $addition="\n// Serve admin dependencies individually. After a host migration, load-scripts.php\n// can keep a year-long cached bundle built before the vendor files were restored.\nadd_action('admin_init', function () {\n \$GLOBALS['concatenate_scripts'] = false;\n});\n";
 // Append within the existing PHP block, or open a new block after a closing tag.
 $tokens=token_get_all($original);$inside=false;
 foreach($tokens as $token)if(is_array($token)){if(in_array($token[0],array(T_OPEN_TAG,T_OPEN_TAG_WITH_ECHO),true))$inside=true;if($token[0]===T_CLOSE_TAG)$inside=false;}
 $updated=$original.($inside?'':"\n<?php\n").$addition;
 if(file_put_contents($path,$updated,LOCK_EX)!==strlen($updated)){file_put_contents($path,$original,LOCK_EX);wp_die('Không ghi đủ file. Đã khôi phục bản cũ.');}
 lkr_redirect('Đã sao lưu functions.php và sửa tải JavaScript quản trị trong child theme. Không sửa theme chính.');
});
add_action('admin_post_lkr_migrate',function(){
 lkr_guard('lkr_migrate');$ids=lkr_document_ids();$target=home_url('/');$prepared=array();$front=(int)get_option('page_on_front');
 foreach($ids as $id){$raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);if(!is_array($nodes))continue;$nodes=lkr_rebase($nodes,$target);if($id===$front)$nodes=lkr_app_nodes($nodes);$prepared[$id]=$nodes;}
 $backup=array('time'=>current_time('mysql'),'site'=>home_url('/'),'posts'=>array(),'options'=>array());
 foreach($ids as $id)foreach(array('_elementor_data','_elementor_page_settings','_elementor_css','_elementor_element_cache') as $key)$backup['posts'][$id][$key]=get_post_meta($id,$key,false);
 foreach(array('lk_media_map','upload_url_path') as $key)$backup['options'][$key]=get_option($key);
 update_option('lkr_host_last_backup',$backup,false);
 foreach($prepared as $id=>$nodes){update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));$settings=get_post_meta($id,'_elementor_page_settings',true);if(is_array($settings))update_post_meta($id,'_elementor_page_settings',wp_slash(lkr_rebase($settings,$target)));}
 foreach($backup['options'] as $key=>$value)if($value!==false)update_option($key,lkr_rebase($value,$target));
 lkr_clear_cache($ids);lkr_redirect('Đã sao lưu và sửa URL trong '.count($prepared).' tài liệu Elementor; khôi phục bố cục tải app trang chủ và tạo lại cache CSS.');
});
add_action('admin_post_lkr_rollback',function(){
 lkr_guard('lkr_rollback');$backup=get_option('lkr_host_last_backup');if(!$backup)wp_die('Chưa có bản sao.');
 foreach($backup['posts'] as $id=>$keys)foreach($keys as $key=>$values){delete_post_meta($id,$key);foreach($values as $value)add_post_meta($id,$key,wp_slash($value));}
 foreach($backup['options'] as $key=>$value){if($value===false)delete_option($key);else update_option($key,$value);}
 lkr_clear_cache(array_keys($backup['posts']));lkr_redirect('Đã hoàn tác dữ liệu về bản sao '.$backup['time'].'. Thư viện cùng phiên bản đã khôi phục được giữ nguyên.');
});
add_action('admin_menu',function(){add_management_page('Sửa chuyển host Long Khánh','Sửa chuyển host Long Khánh','manage_options','lk-host-repair','lkr_admin_page');});
function lkr_admin_page() {
 if(!current_user_can('manage_options'))return;
 $status=lkr_library_status();$ids=lkr_document_ids();$old=0;foreach($ids as $id)if(strpos(get_post_meta($id,'_elementor_data',true),'localhost:180')!==false)$old++;
 echo '<div class="wrap"><h1>Sửa lỗi chuyển hosting Long Khánh</h1><p>WordPress '.esc_html($GLOBALS['wp_version']).' · Elementor '.esc_html(defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'không kích hoạt').'. Công cụ không nâng cấp phiên bản và không sửa theme chính.</p>';
 if(get_option('lkr_host_last_result'))echo '<div class="notice notice-success"><p>'.esc_html(get_option('lkr_host_last_result')).'</p></div>';
 echo '<h2>1. Thư viện bị thiếu khi deploy</h2><p>File còn thiếu: <strong>'.count($status['missing']).'</strong>. Chỉ khôi phục file thiếu/rỗng từ đúng bản đang dùng, không ghi đè file hiện có.</p>';
 if($status['blocked'])echo '<p>Phiên bản không khớp, không chép thư viện.</p>';
 echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lkr_restore_files">';wp_nonce_field('lkr_restore_files');submit_button('Khôi phục thư viện bị thiếu','primary','submit',false,(empty($status['manifest']['files'])||!empty($status['blocked'])||!$status['missing'])?array('disabled'=>'disabled'):array());echo '</form>';
 echo '<p>Tách thư viện quản trị khỏi gói load-scripts.php được cache trước khi khôi phục file. Chỉ thêm cấu hình vào child theme đang hoạt động, có sao lưu functions.php trong database.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lkr_child_scripts">';wp_nonce_field('lkr_child_scripts');submit_button('Sửa tải thư viện quản trị trong child theme','secondary','submit',false);echo '</form>';
 echo '<h2>2. URL và bố cục Elementor</h2><p>Còn <strong>'.$old.'</strong> tài liệu chứa URL localhost. Chuyển sang <strong>'.esc_html(home_url('/')).'</strong>. Chỉ đổi URL nội bộ và nâng khối tải app dạng cũ thành widget ảnh Elementor; giữ nguyên nội dung khác.</p><p>Dữ liệu sắp sửa được sao lưu trong database trước khi ghi và có nút hoàn tác bên dưới.</p>';
 echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lkr_migrate">';wp_nonce_field('lkr_migrate');submit_button('Sao lưu và sửa URL, bố cục tải app','primary','submit',false);echo '</form>';
 $backup=get_option('lkr_host_last_backup');if($backup){echo '<h2>Bản sao dữ liệu</h2><p>'.esc_html($backup['time']).' · '.count($backup['posts']).' tài liệu.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="lkr_rollback">';wp_nonce_field('lkr_rollback');submit_button('Hoàn tác dữ liệu lần sửa gần nhất','secondary','submit',false);echo '</form>';}
 echo '</div>';
}

<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$users=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($users[0]->ID);
$source=json_decode(file_get_contents(LK_DIR.'assets/images/reference-content.json'),true);$media=get_option('lk_media_map');$posts=get_option('lk_blog_ids');
function lk_article_widget($key,$type,$settings){return array('id'=>substr(md5('article-'.$key),0,8),'elType'=>'widget','widgetType'=>$type,'settings'=>$settings,'elements'=>array());}
function lk_article_panel($key,$elements,$classes){return array('id'=>substr(md5($key.'panel'),0,8),'elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>$classes,'gap'=>'no'),'elements'=>array(array('id'=>substr(md5($key.'col'),0,8),'elType'=>'column','settings'=>array('_column_size'=>100),'elements'=>$elements)));}
function lk_article_copy($slug,$index){
 $sets=array(
  'transport'=>array('Trước khi đặt chuyến, hãy xác nhận tuyến đường, giờ xuất phát và điểm đón trả phù hợp với lịch trình của bạn. Hỏi rõ thời gian có mặt để chủ động di chuyển và tránh bỏ lỡ chuyến.','Bạn nên trao đổi trước về loại xe, vị trí ghế và hành lý mang theo. Lưu lại thông tin đặt chỗ, số điện thoại liên hệ và địa điểm đón để thuận tiện kiểm tra khi cần.','Nếu đi cùng gia đình hoặc nhóm bạn, hãy thống nhất giờ tập trung và đặt chỗ sớm. Lịch trình có thể thay đổi vào cuối tuần hoặc dịp lễ, vì vậy nên xác nhận lại trước ngày khởi hành.'),
  'photo'=>array('Chọn thời điểm có ánh sáng dịu để dễ quan sát và chụp ảnh. Chuẩn bị trang phục gọn gàng, thử nhiều góc nhìn và dành thời gian tận hưởng không gian thay vì chỉ dừng lại chụp vội.','Trước khi ghé, hãy kiểm tra đường đi và điều kiện ra vào địa điểm. Tôn trọng không gian chung, xin phép khi cần và tránh để việc chụp ảnh ảnh hưởng tới người xung quanh.','Một chiếc điện thoại đủ pin và vài góc chụp đơn giản đã giúp bạn lưu lại chuyến đi. Giữ gìn vệ sinh, mang theo đồ dùng cần thiết và chọn lịch ghé phù hợp với thời tiết.'),
  'food'=>array('Một buổi hẹn cùng bạn bè sẽ thoải mái hơn khi cả nhóm thống nhất trước ngân sách và khẩu vị. Bạn có thể tham khảo hình ảnh, hỏi thực đơn và xác nhận bàn trống trước khi ghé.','Hãy trao đổi với quán nếu nhóm có yêu cầu riêng về món ăn hoặc chỗ ngồi. Thông tin món, giá và thời gian phục vụ nên được xác nhận trực tiếp để dễ lựa chọn theo nhu cầu.','Sau trải nghiệm, bạn có thể lưu lại địa điểm yêu thích và chia sẻ cảm nhận cụ thể với cộng đồng. Những ghi chú về món ăn, không gian và cách phục vụ sẽ giúp người đến sau có thêm góc nhìn.'),
  'games'=>array('Khi lên lịch chơi cùng bạn bè, hãy hỏi trước tình trạng bàn và thời gian phù hợp. Thống nhất thời lượng, chi phí và các quy định tại địa điểm để buổi gặp gỡ diễn ra thoải mái.','Nếu bạn mới làm quen với bộ môn, có thể chọn buổi chơi nhẹ nhàng để luyện tập và tìm hiểu cách sử dụng dụng cụ. Giữ trật tự, bảo quản thiết bị và tôn trọng người chơi xung quanh.','Bạn nên xác nhận giờ hoạt động trước khi ghé, đặc biệt vào cuối tuần. Sau buổi chơi, hãy lưu lại những điểm phù hợp về vị trí và không gian để dễ lên lịch cho lần tiếp theo.'),
 );
 $topic=str_contains($slug,'nha-xe')?'transport':(str_contains($slug,'chup-hinh')?'photo':(str_contains($slug,'nuong')?'food':'games'));
 return $sets[$topic][$index%3];
}
foreach($posts as $slug=>$id){
 if(get_post_meta($id,'_lk_article_polished',true))continue;
 $data=json_decode(get_post_meta($id,'_elementor_data',true),true);$leading=array_slice($data[0]['elements'][0]['elements'],0,4);
 $leading[1]['settings']['_css_classes']='lk-article-meta';$leading[2]['settings']['_css_classes']='lk-article-intro';
 $headings=$source['blogDetails'][$slug]['blog']['headings'];$items=array();$body=$leading;
 foreach($headings as $i=>$title){
  $anchor='muc-'.($i+1);$title=trim($title);
  $items[]=array('_id'=>substr(md5($slug.$anchor),0,8),'text'=>$title,'link'=>array('url'=>'#'.$anchor),'selected_icon'=>array('value'=>'','library'=>''));
  $body[]=lk_article_widget($slug.$anchor,'heading',array('title'=>$title,'header_size'=>'h2','_element_id'=>$anchor));
  $body[]=lk_article_widget($slug.$anchor.'text','text-editor',array('editor'=>'<p>'.esc_html(lk_article_copy($slug,$i)).'</p>'));
  $key='blog-section-'.$slug.'-'.($i+1);
  if(!empty($media[$key]['id']))$body[]=lk_article_widget($slug.$anchor.'image','image',array('image'=>array('id'=>$media[$key]['id'],'url'=>$media[$key]['url']),'image_size'=>'large'));
 }
 $data[0]['elements'][0]['elements']=$body;
 $toc=array(lk_article_widget($slug.'toc-title','heading',array('title'=>'Nội dung bài viết','header_size'=>'h3')),lk_article_widget($slug.'toc','icon-list',array('icon_list'=>$items)));
 $related=array(lk_article_widget($slug.'related-title','heading',array('title'=>'Bài viết liên quan','header_size'=>'h3')));
 foreach($posts as $other=>$other_id){if($other_id===$id)continue;$key=$slug.'-'.$other;
  $related[]=array('id'=>substr(md5($key.'row'),0,8),'elType'=>'section','isInner'=>true,'settings'=>array('css_classes'=>'lk-related-row','gap'=>'no'),'elements'=>array(
   array('id'=>substr(md5($key.'photo-col'),0,8),'elType'=>'column','settings'=>array('_column_size'=>25,'css_classes'=>'lk-related-photo'),'elements'=>array(lk_article_widget($key.'photo','image',array('image'=>array('id'=>get_post_thumbnail_id($other_id),'url'=>get_the_post_thumbnail_url($other_id,'medium')),'image_size'=>'medium','link_to'=>'custom','link'=>array('url'=>get_permalink($other_id)))))),
   array('id'=>substr(md5($key.'text-col'),0,8),'elType'=>'column','settings'=>array('_column_size'=>75),'elements'=>array(lk_article_widget($key.'title','heading',array('title'=>get_the_title($other_id),'header_size'=>'h4','link'=>array('url'=>get_permalink($other_id)))))),
  ));
 }
 $data[0]['elements'][1]['settings']['css_classes']='lk-article-sidebar';
 $data[0]['elements'][1]['elements']=array(lk_article_panel($slug.'toc-panel',$toc,'lk-panel lk-toc'),lk_article_panel($slug.'related-panel',$related,'lk-panel lk-related'));
 $data[0]['settings']['css_classes'].=' lk-article-layout';
 update_post_meta($id,'_lk_legacy_article_data',get_post_meta($id,'_elementor_data',true));update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));update_post_meta($id,'_lk_article_polished',1);delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
 echo $slug.' '.count($headings).' sections',PHP_EOL;
}
Elementor\Plugin::$instance->files_manager->clear_cache();

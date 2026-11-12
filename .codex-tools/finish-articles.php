<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
$admins=get_users(array('role'=>'administrator','number'=>1));wp_set_current_user($admins[0]->ID);
$source=json_decode(file_get_contents(LK_DIR.'assets/images/reference-content.json'),true);$posts=get_option('lk_blog_ids');
$summaries=array(
 'top-nha-xe-tai-long-khanh-dong-nai'=>array('Kim Mạnh Hùng có nhiều dòng xe phục vụ hành trình giữa Đồng Nai và Sài Gòn.','Võ Cúc Phương là một lựa chọn cho tuyến Long Khánh – Sài Gòn, với xe limousine và ghế ngồi.','Cường Thuỷ có điểm liên hệ trên đường Trần Phú; hãy hỏi chuyến và điểm trả khách khi đặt vé.'),
 'top-4-dia-diem-choi-billiards-tai-long-khanh-dong-nai'=>array('Wukong có không gian chơi billiards và khu vực phục vụ đồ uống trên đường Hồ Thị Hương.','Luxury nằm trên đường Thích Quảng Đức, với không gian dành cho chơi và giao lưu billiards.','X6 tạo điểm nhấn bằng ánh sáng neon, phù hợp những buổi gặp bạn bè sau giờ làm.','LK Pro là lựa chọn trên đường Thích Quảng Đức cho người muốn dành thời gian luyện tập.'),
 'top-nhung-quan-nuong-o-long-khanh'=>array('Nhà Ngọ kết hợp lẩu với các khay nướng dành cho nhóm bạn cùng chia sẻ.','Min Town tập trung món Hoa, dimsum và bò nhúng với nhiều lựa chọn nước dùng.','Chill BBQ & Beer gợi ý một buổi ăn nướng với nhiều món và nước chấm.','Bếp Nhà Leo có các phần lẩu và set nướng cho buổi hẹn nhẹ nhàng.','Cầu Dầu kết hợp không gian bên hồ, ngắm chiều và ăn BBQ ngoài trời.','Nhà Gỗ sử dụng chất liệu gỗ, đèn vàng và chỗ ngồi ngoài trời cho bữa nướng.','Phố Nhật có sushi, sashimi cùng khu vực nướng phong cách Nhật.','Tiệm Nướng Chảo Đường Phố có các phần nướng bơ, bò và hải sản.','BBQ House có thực đơn thịt, hải sản và các món ăn kèm để chọn theo nhóm.','Bò Xèo Cậu Béo gợi ý bò sốt, phần nướng và lẩu cho người thích đổi món.'),
 'top-10-dia-diem-chup-hinh-dep-tai-long-khanh'=>array('Công viên có cây xanh và nhiều khoảng thoáng để chụp ảnh ngoài trời.','Giàn hoa giấy gần tượng đài tạo một góc chụp nhẹ nhàng giữa phố.','Một góc vỉa hè và thảm cỏ trên đường Cách Mạng Tháng Tám cũng có thể thành bối cảnh.','Mặt hồ Cầu Dầu và ánh chiều là điểm nhấn khi chụp ở khu glamping.','Lavin có cổng và không gian mở, phù hợp những tấm ảnh gắn với kiến trúc quán.','Lagom nằm trong hẻm, gợi không khí vintage cho bộ ảnh cà phê.','Highlands có tông đỏ trắng và các góc nhìn ra đường phố Long Khánh.','Sugar ở tầng cao, thuận tiện quan sát phố và ánh hoàng hôn.','Hoa Viên Suối Tre có nhiều cây xanh cho những khung hình gần thiên nhiên.','Khu vực đường Nguyễn Thái Học có kiến trúc cũ, phù hợp ảnh mang sắc thái hoài niệm.')
);
function lk_finish_article_node($key,$html){return array('id'=>substr(md5('finished-article-'.$key),0,8),'elType'=>'widget','widgetType'=>'text-editor','settings'=>array('editor'=>$html),'elements'=>array());}
foreach($posts as $slug=>$id){
 $raw=get_post_meta($id,'_elementor_data',true);$nodes=json_decode($raw,true);$body=&$nodes[0]['elements'][0]['elements'];
 if(!get_post_meta($id,'_lk_before_article_finish_20261112',true))update_post_meta($id,'_lk_before_article_finish_20261112',wp_slash($raw));
 $leading=array_slice($body,0,4);$leading[1]['settings']['editor']='<p>Địa Điểm Long Khánh · '.esc_html(get_the_date('d/m/Y',$id)).'</p>';
 $topic=str_contains($slug,'nha-xe')?'Lịch chạy, giá vé và điểm đón trả cần được xác nhận với nhà xe trước khi đặt chỗ.':(str_contains($slug,'chup-hinh')?'Chọn thời điểm phù hợp, giữ gìn cảnh quan và xin phép khi chụp trong không gian kinh doanh.':'Thực đơn, giờ mở cửa và dịch vụ có thể thay đổi; hãy liên hệ trước khi ghé.');
 $intro='Bốn câu lạc bộ dưới đây giúp bạn tìm chỗ chơi billiards theo vị trí và không gian yêu thích.';
 if(str_contains($slug,'nha-xe'))$intro='Ba nhà xe dưới đây là những địa chỉ để bạn tham khảo khi lên lịch đi từ Long Khánh.';
 elseif(str_contains($slug,'chup-hinh'))$intro='Long Khánh có nhiều bối cảnh từ công viên, đường phố đến những quán cà phê. Danh sách dưới đây gợi ý mười điểm để bạn lên ý tưởng cho bộ ảnh.';
 elseif(str_contains($slug,'nuong'))$intro='Từ những phần nướng theo nhóm đến không gian BBQ ngoài trời, mười quán dưới đây mang lại nhiều cách chọn một bữa hẹn tại Long Khánh.';
 $leading[2]['settings']['editor']='<p>'.esc_html($intro).'</p><p>'.esc_html($topic).'</p>';
 $sections=preg_split('/^##\s+/m',$source['blogDetails'][$slug]['blog']['content']);array_shift($sections);$result=$leading;
 foreach($sections as $i=>$markdown){
  $anchor='muc-'.($i+1);$heading=null;$image=null;
  foreach($body as $n){if(($n['settings']['_element_id']??'')===$anchor)$heading=$n;if(($n['widgetType']??'')==='image'&&($n['id']??'')===substr(md5('article-'.$slug.$anchor.'image'),0,8))$image=$n;}
  if(!$heading)continue;$result[]=$heading;$result[]=lk_finish_article_node($slug.$anchor.'copy','<p>'.esc_html($summaries[$slug][$i]??'').'</p>');if($image)$result[]=$image;
  preg_match_all('/^[\s\-📍📌]*(?:Địa\s*chỉ|Loại xe|Giờ khởi hành|Giá vé|Thông tin liên hệ)\s*[:：].*$/miu',$markdown,$found);
  $facts=array();foreach($found[0] as $line){$line=trim(preg_replace('/^[\s\-📍📌]+/u','',$line));$line=str_replace('**','',$line);if($line)$facts[]=$line;}
  if($facts)$result[]=lk_finish_article_node($slug.$anchor.'facts','<ul class="lk-article-facts"><li>'.implode('</li><li>',array_map('esc_html',$facts)).'</li></ul>');
 }
 $result[]=lk_finish_article_node($slug.'source','<p class="lk-article-source">Thông tin và hình ảnh tham khảo: <a href="'.esc_url('https://diadiemlongkhanh.com/blog/'.$slug).'" target="_blank" rel="noopener">bài gốc trên Địa Điểm Long Khánh</a>. Địa chỉ và mức giá được ghi nhận theo bài gốc, không phải báo giá hiện hành.</p>');
 $body=$result;unset($body);update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($nodes,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');echo 'Finished article '.$id.' ('.count($sections).' entries)',PHP_EOL;
}
// Keep historical promotions factual without presenting them as active offers.
function lk_finish_promo_copy($nodes){foreach($nodes as &$node){if(($node['widgetType']??'')==='text-editor')$node['settings']['editor']=strtr($node['settings']['editor']??'',array('Đã kết thúc · Ưu đãi minh hoạ'=>'Đã kết thúc','Thông tin được giữ để minh hoạ giao diện.'=>'Vui lòng liên hệ địa điểm để hỏi chương trình hiện hành.','Các chương trình bên dưới là dữ liệu minh hoạ đã hết hạn.'=>'Các chương trình bên dưới đã kết thúc.'));if(!empty($node['elements']))$node['elements']=lk_finish_promo_copy($node['elements']);}return $nodes;}
$promo=get_option('lk_page_map')['promo'];$promo_docs=array_merge(array(get_post($promo)),get_posts(array('post_type'=>'page','post_parent'=>$promo,'post_status'=>'publish','posts_per_page'=>-1)));
foreach($promo_docs as $post){$nodes=json_decode(get_post_meta($post->ID,'_elementor_data',true),true);if(!$nodes)continue;update_post_meta($post->ID,'_elementor_data',wp_slash(wp_json_encode(lk_finish_promo_copy($nodes),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));delete_post_meta($post->ID,'_elementor_css');delete_post_meta($post->ID,'_elementor_element_cache');}
Elementor\Plugin::$instance->files_manager->clear_cache();

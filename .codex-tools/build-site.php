<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
set_time_limit(0);
$admins=get_users(array('role'=>'administrator','number'=>1));
if(!$admins)throw new RuntimeException('An existing administrator is required.');
wp_set_current_user($admins[0]->ID);
$media=get_option('lk_media_map',array());
if(count($media)<110)throw new RuntimeException('Finish importing reference media first.');
$source=json_decode(file_get_contents(ABSPATH.'wp-content/plugins/long-khanh-directory/assets/images/reference-content.json'),true);
function ident(){static $n=0;return substr(md5('longkhanh-'.(++$n)),0,8);}
function widget($type,$settings=array(),$class=''){
 if($class)$settings['_css_classes']=$class;
 return array('id'=>ident(),'elType'=>'widget','widgetType'=>$type,'settings'=>$settings,'elements'=>array());
}
function col($elements,$size=100,$class=''){
 return array('id'=>ident(),'elType'=>'column','isInner'=>false,'settings'=>array('_column_size'=>$size,'_inline_size'=>null,'css_classes'=>$class),'elements'=>$elements);
}
function section($columns,$class='',$settings=array(),$inner=false){
 return array('id'=>ident(),'elType'=>'section','isInner'=>$inner,'settings'=>array_merge(array('layout'=>'boxed','content_width'=>array('size'=>1160,'unit'=>'px'),'gap'=>'no','css_classes'=>$class),$settings),'elements'=>$columns);
}
function row($elements,$class='lk-section',$settings=array()){return section(array(col($elements)), $class,$settings);}
function heading($title,$tag='h2',$class='',$url=''){return widget('heading',array('title'=>$title,'header_size'=>$tag,'link'=>array('url'=>$url)),$class);}
function text($html,$class=''){return widget('text-editor',array('editor'=>$html),$class);}
function button($label,$url,$class='',$icon=''){return widget('button',array('text'=>$label,'link'=>array('url'=>$url),'selected_icon'=>$icon?array('value'=>'fas fa-'.$icon,'library'=>'fa-solid'):array(),'icon_align'=>'left'),$class);}
function asset($key,$size='url'){global $media;return $media[$key][$size]??$media[$key]['url']??'';}
function image($key,$class='',$url='',$size='large'){
 global $media;$m=$media[$key]??array('id'=>0,'url'=>'');return widget('image',array('image'=>array('id'=>$m['id'],'url'=>$m['url']),'image_size'=>$size,'link_to'=>$url?'custom':'none','link'=>array('url'=>$url)),$class);
}
function icons($items,$class=''){
 $list=array();foreach($items as $it)$list[]=array('_id'=>ident(),'text'=>$it[0],'link'=>array('url'=>$it[1]??''),'selected_icon'=>array('value'=>($it[3]??'fas').' fa-'.($it[2]??'map-marker-alt'),'library'=>($it[3]??'fas')==='fab'?'fa-brands':'fa-solid'));
 return widget('icon-list',array('icon_list'=>$list),$class);
}
function native_gallery($keys,$class='',$columns=3){global $media;$images=array();foreach($keys as $key)if(!empty($media[$key]['id']))$images[]=array('id'=>$media[$key]['id'],'url'=>$media[$key]['url']);return widget('image-gallery',array('wp_gallery'=>$images,'gallery_columns'=>$columns,'gallery_link'=>'file','gallery_display_caption'=>'none','thumbnail_size'=>'large','open_lightbox'=>'yes'),$class);}
function save_document($id,$elements){
 update_post_meta($id,'_lk_managed',1);update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_template_type',get_post_type($id)==='elementor_library'?'section':'wp-post');update_post_meta($id,'_elementor_version',ELEMENTOR_VERSION);update_post_meta($id,'_wp_page_template','elementor_canvas');
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($elements,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));
 update_post_meta($id,'_elementor_page_settings',array('hide_title'=>'yes','page_layout'=>'elementor_canvas'));
 delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');
}
function entry($key,$title,$slug,$type='page',$parent=0){
 $found=get_posts(array('post_type'=>$type,'post_status'=>'any','meta_key'=>'_lk_key','meta_value'=>$key,'posts_per_page'=>1));
 $id=$found?$found[0]->ID:0;
 if(!$id&&$type==='page'){$old=get_page_by_path($slug,OBJECT,'page');if($old)$id=$old->ID;}
 if($key==='home')$id=(int)get_option('page_on_front');
 $args=array('post_title'=>$title,'post_name'=>$slug,'post_type'=>$type,'post_status'=>'publish','post_parent'=>$parent,'comment_status'=>$type==='listing'?'open':'closed');if($id)$args['ID']=$id;
 $id=wp_insert_post($args,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());update_post_meta($id,'_lk_key',$key);update_post_meta($id,'_lk_managed',1);return $id;
}
function title_section($title,$left=false){return row(array(heading($title,'h2','lk-title'.($left?' lk-title-left':''))));}
function app_block(){
 return section(array(col(array(heading('Tải ứng dụng Địa Điểm Long Khánh'),text('<p>Sử dụng app để nhận các voucher khuyến mãi!</p>'),section(array(col(array(image('qrcode','lk-qr','', 'medium')),25),col(array(image('appstore','', 'https://apps.apple.com/vn/app/id1537080613'),image('playstore','', 'https://play.google.com/store/apps/details?id=com.diadiemlongkhanh.app')),75)),'lk-store-links',array(),true)),55),col(array(),45)),'lk-section lk-app-panel',array('background_background'=>'classic','background_image'=>array('id'=>0,'url'=>asset('app-desktop')),'background_image_mobile'=>array('id'=>0,'url'=>asset('app-mobile')),'background_position'=>'center right','background_repeat'=>'no-repeat','background_size'=>'contain'));
}
$pages=array();foreach(array('home'=>'Trang chủ','search'=>'Tìm kiếm địa điểm','blog'=>'Cẩm nang Long Khánh','explore'=>'Khám phá','promo'=>'Khuyến mãi','profile'=>'Tài khoản','contact'=>'Liên hệ - Góp ý','add-place'=>'Thêm địa điểm','about'=>'Giới thiệu','privacy'=>'Chính sách bảo mật','termsofuse'=>'Điều khoản sử dụng','download'=>'Tải ứng dụng','new-review'=>'Viết đánh giá') as $slug=>$title){$pages[$slug]=entry($slug,$title,$slug);}
update_option('lk_page_map',$pages,false);
$base=home_url('/');$link=function($s='')use($base){return $base.($s?trim($s,'/').'/':'');};

// Native Elementor header, footer and mobile navigation.
$nav=array(array('Blog',$link('blog'),'rss'),array('Khám phá',$link('explore'),'hashtag'),array('Khuyến mãi',$link('promo'),'percentage'));
$header=entry('header','Long Khánh - Header và điều hướng','lk-header','elementor_library');
$banner=section(array(col(array(button('×','#','lk-dismiss-app')),5,'lk-app-close'),col(array(image('app-icon','','','thumbnail')),10,'lk-app-icon'),col(array(text('<p><strong>Ứng dụng ĐịaĐiểmLongKhánh</strong><br>Tải app để nhận muôn vàn ưu đãi!</p>')),60,'lk-app-copy'),col(array(button('Mở ứng dụng',$link('download'))),25,'lk-app-action')),'lk-app-bar');
$desktop=section(array(col(array(image('logo','',$link(), 'full')),14,'lk-header-logo'),col(array(widget('lk-search',array('mode'=>'header','placeholder'=>'Tìm kiếm địa điểm','button'=>'Tìm quán'))),24,'lk-header-search'),col(array(icons($nav)),40,'lk-header-nav'),col(array(button('Viết Review',$link('new-review'),'','pen'),button('Đăng nhập',$link('profile'),'lk-login-open lk-login-button')),22,'lk-header-actions'),col(array(button('Tìm kiếm',$link('search'),'lk-open-search','search'),button('Menu','#','lk-open-menu','bars')),10,'lk-mobile-actions')),'lk-header');
$bottom=row(array(icons(array(array('Trang chủ',$link(),'home'),array('Blog',$link('blog'),'rss'),array('Khám phá',$link('explore'),'fire'),array('Khuyến mãi',$link('promo'),'percentage'),array('Tài khoản',$link('profile'),'user')))),'lk-bottom-nav');
save_document($header,array($banner,$desktop,$bottom));update_option('lk_template_header',$header,false);
$menu=entry('menu','Long Khánh - Menu mobile','lk-menu','elementor_library');save_document($menu,array(row(array(button('Đăng nhập / Đăng ký',$link('profile')),heading('Địa Điểm Long Khánh','h3'),icons(array_merge(array(array('Trang chủ',$link(),'home')),$nav,array(array('Địa điểm mới',$link('add-place'),'map-marker-alt')))),heading('Thông tin','h3'),icons(array(array('Giới thiệu',$link('about'),'info-circle'),array('Điều khoản sử dụng',$link('termsofuse'),'shield-alt'),array('Chính sách bảo mật',$link('privacy'),'lock'),array('Liên hệ - Góp ý',$link('contact'),'envelope'))),heading('Theo dõi chúng tôi','h3'),icons(array(array('Facebook','https://www.facebook.com/DDLongKhanh','facebook','fab'),array('Instagram','https://www.instagram.com/','instagram','fab'),array('Tiktok','https://www.tiktok.com/@diadiemlongkhanh','tiktok','fab')))))));update_option('lk_template_menu',$menu,false);
$footer=entry('footer','Long Khánh - Footer','lk-footer','elementor_library');
save_document($footer,array(section(array(col(array(image('logo','',$link(),'full'),button('Hợp tác với chúng tôi',$link('contact'),'','handshake')),28),col(array(heading('Về chúng tôi','h4'),icons(array(array('Giới thiệu',$link('about'),'angle-right'),array('Liên hệ - góp ý',$link('contact'),'angle-right'),array('Điều khoản sử dụng',$link('termsofuse'),'angle-right'),array('Chính sách bảo mật',$link('privacy'),'angle-right')))),24),col(array(heading('Theo dõi chúng tôi','h4'),icons(array(array('Facebook','https://www.facebook.com/DDLongKhanh','facebook-f','fab'),array('Instagram','https://www.instagram.com/','instagram','fab'),array('Tiktok','https://www.tiktok.com/@diadiemlongkhanh','tiktok','fab')))),24),col(array(heading('Tải ứng dụng','h4'),image('appstore','lk-store','https://apps.apple.com/vn/app/id1537080613'),image('playstore','lk-store','https://play.google.com/store/apps/details?id=com.diadiemlongkhanh.app')),24)),'lk-section lk-footer'),row(array(text('<p>Bản quyền © '.wp_date('Y').' Địa Điểm Long Khánh</p>')),'lk-section lk-copyright')));update_option('lk_template_footer',$footer,false);

// Terms and ListingPro records remain native WordPress data.
$categoryMap=array();foreach($source['home']['categories'] as $c)$categoryMap[$c['_id']]=array($c['slug'],$c['name']);
$categoryMap['629c9fb917dd741c0c5b5d0c']=array('khach-san','Khách Sạn');
$terms=array();foreach($categoryMap as $c){$t=term_exists($c[0],'listing-category');if(!$t)$t=wp_insert_term($c[1],'listing-category',array('slug'=>$c[0]));$terms['cat'][$c[0]]=(int)(is_array($t)?$t['term_id']:$t);update_term_meta($terms['cat'][$c[0]],'_lk_demo',1);}
$regions=array();foreach($source['home']['regions'] as $r)$regions[$r['slug']]=$r['name'];foreach($source['places'] as $p)$regions[$p['region']['slug']]=$p['region']['name'];
foreach($regions as $slug=>$name){$t=term_exists($slug,'location');if(!$t)$t=wp_insert_term($name,'location',array('slug'=>$slug));$terms['region'][$slug]=(int)(is_array($t)?$t['term_id']:$t);update_term_meta($terms['region'][$slug],'_lk_demo',1);}
$placeIDs=array();$routes=array();$index=0;
foreach($source['places'] as $slug=>$p){
 $id=entry('place-'.$slug,$p['name'],$slug,'listing');$placeIDs[$slug]=$id;wp_update_post(array('ID'=>$id,'menu_order'=>$index++,'post_excerpt'=>'Khám phá '.$p['name'].' tại Long Khánh. Xem hình ảnh, địa chỉ, giờ hoạt động và thông tin tham khảo trước khi ghé.'));
 $cat=$categoryMap[$p['category']][0]??'an-uong';wp_set_object_terms($id,array($terms['cat'][$cat]),'listing-category');wp_set_object_terms($id,array($terms['region'][$p['region']['slug']]),'location');
 $price=$p['price']??array('min'=>0,'max'=>0);$hours=$p['openingTime']['now']??'07:00 - 22:00';$split=explode(' - ',$hours);$open=$split[0]??'07:00';$close=$split[1]??'22:00';
 $options=array('gAddress'=>$p['address']['specific'],'phone'=>$p['phone']??'','latitude'=>$p['address']['geo']['lat']??10.9398,'longitude'=>$p['address']['geo']['long']??107.2446,'list_price'=>$price['min']??0,'list_price_to'=>$price['max']??0,'price_status'=>'moderate','business_hours'=>array());foreach(array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') as $day)$options['business_hours'][$day]=array('open'=>$open,'close'=>$close);
 update_post_meta($id,'lp_listingpro_options',$options);update_post_meta($id,'_lk_open',$open);update_post_meta($id,'_lk_close',$close);update_post_meta($id,'_lk_rating',$p['rate']['summary']??0);set_post_thumbnail($id,$media['place-'.$slug]['id']);
 $galleryKeys=array_values(array_filter(array_keys($media),function($k)use($slug){return strpos($k,'gallery-'.$slug.'-')===0;}));if(!$galleryKeys)$galleryKeys=array('place-'.$slug);$galleryIDs=array_map(function($k)use($media){return $media[$k]['id'];},$galleryKeys);update_post_meta($id,'gallery_image_ids',implode(',',$galleryIDs));
 $galleryID=entry('gallery-'.$slug,'Hình ảnh '.$p['name'],'anh-'.$slug);save_document($galleryID,array(row(array(heading($p['name'],'h1','',$link('dia-diem/'.$slug)),text('<p>'.esc_html($p['address']['specific']).'</p>'),button('Quay lại địa điểm',$link('dia-diem/'.$slug),'','arrow-left'),native_gallery($galleryKeys,'lk-gallery-all',4)),'lk-section lk-page')));$routes['dia-diem/'.$slug.'/photo']=$galleryID;
 $menuKeys=array_values(array_filter(array_keys($media),function($k)use($slug){return strpos($k,'menu-'.$slug.'-')===0;}));
 $menuID=entry('menu-'.$slug,'Menu '.$p['name'],'menu-'.$slug);save_document($menuID,array(row(array(heading('Menu của '.$p['name'],'h1'),button('Quay lại địa điểm',$link('dia-diem/'.$slug),'','arrow-left'),$menuKeys?native_gallery($menuKeys,'lk-gallery-all',2):text('<p>Menu đang được cập nhật. Bạn có thể gọi cho địa điểm để hỏi thêm.</p>')),'lk-section lk-page')));$routes['dia-diem/'.$slug.'/menu']=$menuID;
 $directions='https://www.google.com/maps/dir/?api=1&destination='.$options['latitude'].','.$options['longitude'];
 $elements=array(row(array(heading($p['name'],'h1'),icons(array(array($p['address']['specific'],'','map-marker-alt'),array('Hiển thị bản đồ','#location','map-marked-alt'),array('Xem đường đi',$directions,'directions'),array('Xem menu',$link('dia-diem/'.$slug.'/menu'),'utensils')),'lk-detail-links'),native_gallery(array_slice($galleryKeys,0,5),'lk-detail-gallery',3),button('Xem tất cả ảnh ('.count($galleryKeys).')',$link('dia-diem/'.$slug.'/photo'),'lk-gallery-link','images')),'lk-section lk-detail-top lk-panel'));
 $elements[]=section(array(col(array(heading('Đánh giá','h2'),text('<p class="lk-score">'.(($p['rate']['summary']??0)>0?esc_html($p['rate']['summary']):'☆').'</p><p class="lk-rating-message">'.(($p['rate']['summary']??0)>0?'Điểm tham khảo từ bản demo':'Chưa có đánh giá').'</p>'),button('Đánh giá ngay','#reviews','lk-login-open')),33,'lk-panel lk-rating-column'),col(array(heading('Thông tin chi tiết'),icons(array(array(number_format_i18n($price['min']??0).'đ - '.number_format_i18n($price['max']??0).'đ','','dollar-sign'),array($open.' - '.$close,'','clock'),array($p['phone']??'Đang cập nhật',!empty($p['phone'])?'tel:'.$p['phone']:'','phone-alt')))),34,'lk-panel lk-info-column'),col(array(heading('Địa điểm cụ thể'),widget('google_maps',array('address'=>$p['address']['specific'],'zoom'=>array('size'=>15),'height'=>array('size'=>210,'unit'=>'px'))),icons(array(array('Xem đường đi',$directions,'directions')))),33,'lk-panel lk-map-column')),'lk-section lk-detail-info');
 $benefits=array();foreach($p['benefits']??array() as $b){if($b['name']==='Có giao hàng')continue;$icon=str_replace(array('far fa-','fas fa-'),'',$b['icon']);$icon=strtr($icon,array('cars'=>'car','air-conditioner'=>'snowflake','sun-cloud'=>'sun','wifi'=>'wifi','shipping-fast'=>'truck','credit-card'=>'credit-card'));$benefits[]=array($b['name'],'',$icon);}
 if($benefits)$elements[]=row(array(icons($benefits)),'lk-section lk-panel lk-benefits');
 $elements[]=row(array(heading('Giới thiệu'),text('<p>'.esc_html($p['name']).' là một địa điểm tại '.esc_html($p['region']['name']).', Long Khánh. Hình ảnh và thông tin bên trên giúp bạn tham khảo trước khi ghé. Bạn nên liên hệ trực tiếp để xác nhận giờ hoạt động và giá hiện tại.</p>')),'lk-section lk-panel lk-space');
 $elements[]=row(array(widget('lk-reviews',array('title'=>'Đánh giá từ cộng đồng'),'lk-reviews-widget')),'lk-section',array('_element_id'=>'reviews'));
 $elements[]=title_section('Địa điểm lân cận',true);$elements[]=row(array(widget('lk-listings',array('limit'=>4,'columns'=>4,'tabs'=>'','more'=>'Xem thêm địa điểm'))));
 $elements[]=row(array(heading('Đây có phải là địa điểm của bạn không?','h3'),text('<p>Bạn sở hữu hoặc quản lý địa điểm này? Liên hệ với chúng tôi để cập nhật thông tin, hình ảnh và phản hồi cộng đồng.</p>'),button('Xác nhận chủ sở hữu',$link('contact'),'','user-check')),'lk-section lk-panel lk-owner');save_document($id,$elements);
}
update_option('lk_place_ids',$placeIDs,false);

// Home page: native heading/image/button widgets, dynamic listing query only.
$home=array(row(array(heading('Tìm Kiếm Địa Điểm Tại TP.Long Khánh','h1'),text('<p>Địa Điểm Ăn Uống - Mua Sắm - Giải Trí - Du Lịch tại Long Khánh</p>','lk-subtitle'),widget('lk-search',array('placeholder'=>'Tên quán, khu vực, kiểu quán,...','button'=>'Tìm quán','mode'=>'hero'))),'lk-hero',array('background_background'=>'classic','background_image'=>array('id'=>$media['hero']['id'],'url'=>asset('hero')),'background_size'=>'cover','background_position'=>'center center')));
$bannerCols=array();foreach($source['home']['vouchers'] as $v)$bannerCols[]=col(array(image('banner-'.strtolower($v['code']),'',$link('promo/'.strtolower($v['code'])))),33.33);$home[]=section($bannerCols,'lk-section lk-banners');
$home[]=title_section('Danh mục cần tìm ?');$cols=array();foreach($source['home']['categories'] as $c)$cols[]=col(array(image('category-'.$c['slug']),heading($c['name'],'h3','',$link('search').'?categories='.$c['slug'])),25,'lk-category-card');$home[]=section($cols,'lk-section lk-carousel lk-category-row');
$home[]=title_section('Khu vực gần đây');$cols=array();foreach($source['home']['regions'] as $r){$count=count(array_filter($source['places'],function($p)use($r){return $p['region']['slug']===$r['slug'];}));$cols[]=col(array(image('region-'.$r['slug']),heading($r['name'],'h3','',$link('search').'?regions='.$r['slug']),text('<p>'.$count.' địa điểm demo</p>')),20,'lk-region-card');}$home[]=section($cols,'lk-section lk-carousel lk-region-row');
$home[]=title_section('Địa điểm nổi bật');$home[]=row(array(widget('lk-listings',array('limit'=>8,'columns'=>4,'tabs'=>'yes','more'=>'Xem thêm'))));$home[]=app_block();
$home[]=section(array(col(array(image('suggest-place')),25),col(array(heading('Chúng tôi có đang bỏ lỡ địa điểm nào bạn biết ?','h3'),button('Đóng góp địa điểm',$link('add-place'))),75)),'lk-section lk-contribute');save_document($pages['home'],$home);
save_document($pages['search'],array(row(array(widget('lk-results',array('filter_title'=>'Lọc kết quả'))),'lk-section lk-page')));

// Editorial pages and individual articles use editable native widgets.
$blogIDs=array();$blogCards=array();
foreach($source['blogs'] as $i=>$b){
 $slug=$b['slug'];$id=entry('blog-'.$slug,$b['title'],$slug,'post');$blogIDs[$slug]=$id;set_post_thumbnail($id,$media['blog-'.$slug]['id']);
 $date=wp_date('d/m/Y',strtotime($b['createdAt']));$summary=$source['blogDetails'][$slug]['blog']['description']??'Gợi ý địa điểm và trải nghiệm dành cho bạn tại Long Khánh.';
 $sections=array('Thông tin trước khi bạn đi','Gợi ý trải nghiệm tại Long Khánh','Lưu lại địa điểm yêu thích');
 $content=array(heading($b['title'],'h1'),text('<p>'.esc_html($b['author']).' · '.$date.' · Nội dung demo</p>'),text('<p>'.esc_html($summary).'</p>'),image('blog-'.$slug));
 foreach($sections as $j=>$label){$content[]=heading(($j+1).'. '.$label,'h2','');$content[count($content)-1]['settings']['_element_id']='muc-'.($j+1);$content[]=text('<p>'.array('Trước khi lên lịch, bạn hãy kiểm tra địa chỉ, thời gian hoạt động và mức giá phù hợp. Một cuộc gọi ngắn giúp bạn xác nhận thông tin và chọn thời điểm ghé thuận tiện.','Long Khánh có nhiều lựa chọn để ăn uống, vui chơi và gặp gỡ bạn bè. Bạn có thể bắt đầu từ khu vực gần mình, xem hình ảnh thực tế rồi chọn địa điểm phù hợp với nhu cầu.','Dùng biểu tượng đánh dấu để lưu những nơi bạn muốn ghé. Sau trải nghiệm, hãy chia sẻ cảm nhận và hình ảnh của bạn để cộng đồng có thêm thông tin tham khảo.')[$j].'</p>');}
 $content[]=button('Khám phá địa điểm',$link('search'));
 $toc=array(heading('Nội dung bài viết','h3'),icons(array(array('1. '.$sections[0],'#muc-1','angle-right'),array('2. '.$sections[1],'#muc-2','angle-right'),array('3. '.$sections[2],'#muc-3','angle-right'))));
 save_document($id,array(section(array(col($content,66,'lk-panel lk-article'),col($toc,34,'lk-panel lk-toc')),'lk-section lk-page lk-two-columns lk-gradient')));
 $blogCards[]=col(array(image('blog-'.$slug,'',$link('blog/'.$slug)),heading($b['title'],'h3','',$link('blog/'.$slug)),text('<p>'.esc_html($b['author']).' · '.$date.'</p>')),50,'lk-blog-card');
}
update_option('lk_blog_ids',$blogIDs,false);
$blog=array(row(array(image('blog-slogan')),'lk-blog-hero',array('background_background'=>'classic','background_image'=>array('id'=>$media['blog-hero']['id'],'url'=>asset('blog-hero')),'background_size'=>'cover','background_position'=>'center center')),title_section('Các bài viết mới nhất',true),section($blogCards,'lk-section lk-blog-featured'));
foreach(array('Cẩm nang ăn uống'=>array(2),'Cẩm nang du lịch'=>array(0,3),'Vui chơi và giải trí'=>array(1)) as $label=>$indices){$blog[]=title_section($label,true);$items=array();foreach($indices as $i){$c=$blogCards[$i];$c['id']=ident();$items[]=$c;}$blog[]=section($items,'lk-section lk-blog-row');}save_document($pages['blog'],$blog);

// Promo pages preserve reference composition and mark historical offers clearly.
$promos=array();foreach($source['promos'] as $code=>$p){$slug=strtolower($code);$id=entry('promo-'.$slug,$p['title'],$slug,'page',$pages['promo']);$period='Từ '.wp_date('d/m/Y',strtotime($p['startDate'])).' đến '.wp_date('d/m/Y',strtotime($p['endDate']));
 $promos[]=col(array(image('promo-'.$slug,'',get_permalink($id)),heading($p['title'],'h3','',get_permalink($id)),text('<p>'.$period.'<br><strong>Đã kết thúc · Ưu đãi minh hoạ</strong></p>'),heading($p['place']['name'],'h4'),text('<p>'.esc_html($p['place']['address']['specific']).'</p>')),33,'lk-blog-card lk-promo-card');
 save_document($id,array(section(array(col(array(image('promo-'.$slug),heading($p['title'],'h1'),text('<p>'.esc_html($p['content']).'</p><p>'.$period.'</p><p><strong>Chương trình đã kết thúc. Thông tin được giữ để minh hoạ giao diện.</strong></p>'),text('<p>Ưu đãi của <a href="'.$link('search').'">'.esc_html($p['place']['name']).'</a></p>'),section(array(col(array(heading('Khám phá ứng dụng','h3'),text('<p>Xem địa điểm, lưu yêu thích và theo dõi ưu đãi mới.</p>')),60),col(array(image('qrcode')),20),col(array(image('appstore','','https://apps.apple.com/vn/app/id1537080613'),image('playstore','','https://play.google.com/store/apps/details?id=com.diadiemlongkhanh.app')),20)),'lk-promo-ticket',array(),true)),100,'lk-panel')),'lk-section lk-page lk-promo-detail')));
}
save_document($pages['promo'],array(row(array(heading('Cập nhật ưu đãi hiện hành','h1'),text('<p>Bạn đang tìm kiếm một địa điểm để trải nghiệm?<br>Khám phá các chương trình khuyến mãi tại Long Khánh.</p><p><small>Các chương trình bên dưới là dữ liệu minh hoạ đã hết hạn.</small></p>')),'lk-section lk-promo-intro lk-gradient'),section($promos,'lk-section lk-promo-grid lk-page')));
save_document($pages['explore'],array(section(array(col(array(widget('lk-feed',array('limit'=>5))),66),col(array(heading('Bài nổi bật','h3'),text('<p>Những địa điểm và trải nghiệm từ cộng đồng Long Khánh.</p>'),heading('Địa điểm nổi bật','h3'),widget('lk-listings',array('limit'=>5,'columns'=>2,'tabs'=>'','more'=>'Xem thêm'))),34,'lk-panel lk-sidebar-list')),'lk-section lk-page lk-two-columns lk-gradient')));
save_document($pages['contact'],array(row(array(heading('Liên hệ với chúng tôi','h1'),text('<p>Diadiemlongkhanh hoan nghênh mọi ý kiến đóng góp, chia sẻ về mọi vấn đề liên quan.<br>Đừng ngần ngại liên hệ qua email: <a href="mailto:diadiemlongkhanh@gmail.com"><strong>diadiemlongkhanh@gmail.com</strong></a> hoặc SĐT: <a href="tel:0792339233"><strong>07 9233 9233</strong></a>.</p><p>Hoặc điền vào form bên dưới để liên hệ với chúng tôi nhé.</p>'),widget('lk-form',array('kind'=>'contact','button'=>'Gửi'))),'lk-section lk-page lk-contact-layout lk-panel')));
save_document($pages['add-place'],array(row(array(heading('Thêm địa điểm','h1'),text('<p>Những địa điểm yêu thích của bạn chưa có trên DiaDiemLongKhanh.Com? Chia sẻ với cộng đồng ngay!</p>'),widget('lk-form',array('kind'=>'add-place','button'=>'Thêm địa điểm'))),'lk-section lk-page lk-contact-layout lk-panel')));
save_document($pages['profile'],array(row(array(widget('lk-account',array('title'=>'Tài khoản của bạn'))),'lk-section lk-page lk-account-panel')));
save_document($pages['new-review'],array(row(array(heading('Bạn muốn đánh giá địa điểm nào?','h1'),text('<p>Tìm địa điểm bạn đã ghé, mở trang chi tiết rồi chia sẻ trải nghiệm với cộng đồng.</p>'),widget('lk-search',array('mode'=>'hero','placeholder'=>'Nhập tên địa điểm muốn đánh giá','button'=>'Tìm địa điểm')),widget('lk-listings',array('limit'=>8,'columns'=>4,'tabs'=>'yes','more'=>'Xem thêm'))),'lk-section lk-page')));
save_document($pages['download'],array(title_section('Tải ứng dụng Địa Điểm Long Khánh'),app_block(),row(array(text('<p>Chọn App Store hoặc Google Play phù hợp với thiết bị của bạn. Bạn cũng có thể tiếp tục khám phá địa điểm trên website.</p>'),button('Khám phá địa điểm',$link('search'))),'lk-section lk-page')));
foreach(array('about'=>array('Giới thiệu Địa Điểm Long Khánh','Địa Điểm Long Khánh là nơi khám phá quán ăn, quán cà phê, mua sắm, giải trí và du lịch tại Long Khánh.','Bạn có thể tìm địa điểm theo khu vực, xem ảnh, tham khảo khoảng giá và lưu những nơi yêu thích. Các nội dung trong bản dựng này được dùng để trình diễn giao diện và có thể cập nhật bằng Elementor.'),'privacy'=>array('Chính sách bảo mật','Website lưu thông tin bạn chủ động cung cấp khi tạo tài khoản, gửi liên hệ hoặc đóng góp địa điểm.','Danh sách địa điểm yêu thích và địa điểm vừa xem được lưu trên trình duyệt của bạn. Nội dung chính sách này là bản minh hoạ cho dự án local; chủ website cần rà soát trước khi đưa vào sử dụng công khai.'),'termsofuse'=>array('Điều khoản sử dụng','Vui lòng chia sẻ thông tin chính xác và tôn trọng người khác khi sử dụng các tính năng cộng đồng.','Giá, giờ hoạt động và ưu đãi chỉ có tính tham khảo. Hãy xác nhận trực tiếp với địa điểm trước khi sử dụng dịch vụ. Nội dung điều khoản trong dự án local là bản minh hoạ, cần được hoàn thiện trước khi vận hành công khai.')) as $slug=>$copy)save_document($pages[$slug],array(row(array(heading($copy[0],'h1'),text('<p>'.$copy[1].'</p><p>'.$copy[2].'</p>'),button('Liên hệ - Góp ý',$link('contact'))),'lk-section lk-page lk-contact-layout lk-panel')));
update_option('lk_routes',$routes,false);update_option('show_on_front','page');update_option('page_for_posts',0);update_option('page_on_front',$pages['home']);update_option('blogname','Địa Điểm Long Khánh');update_option('blogdescription','Địa điểm ăn uống, mua sắm, giải trí và du lịch tại Long Khánh');update_option('timezone_string','Asia/Ho_Chi_Minh');update_option('elementor_disable_color_schemes','yes');update_option('elementor_disable_typography_schemes','yes');
$support=get_option('elementor_cpt_support',array('post','page'));if(!in_array('listing',$support,true))$support[]='listing';update_option('elementor_cpt_support',$support);update_option('site_icon',$media['favicon']['id']);
$parent_mods=get_option('theme_mods_listingpro',array());
switch_theme('listingpro-longkhanh-child');
if(!get_option('theme_mods_listingpro-longkhanh-child')) update_option('theme_mods_listingpro-longkhanh-child',$parent_mods);
$result=activate_plugin('long-khanh-directory/long-khanh-directory.php');if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
if(function_exists('lk_register_routes'))lk_register_routes();flush_rewrite_rules(false);
if(class_exists('Elementor\\Plugin'))Elementor\Plugin::$instance->files_manager->clear_cache();
echo wp_json_encode(array('pages'=>$pages,'header'=>$header,'footer'=>$footer,'menu'=>$menu,'listings'=>count($placeIDs),'blogs'=>count($blogIDs),'routes'=>count($routes)),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;

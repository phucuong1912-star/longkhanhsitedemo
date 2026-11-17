<?php
if(!defined('ABSPATH'))exit;
abstract class LK_Widget extends \Elementor\Widget_Base {
 protected function is_dynamic_content(): bool {return true;}
 public function get_categories(){return array('long-khanh');}
 public function get_icon(){return 'eicon-map-pin';}
 public function get_style_depends(){return array('lk-site');}
 protected function fields_style(){
  $this->start_controls_section('fields_style',array('label'=>'Ô nhập và biểu mẫu','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  foreach(array('field_text'=>array('Màu chữ ô nhập','--lk-field-text'),'label_color'=>array('Màu nhãn','--lk-label-color'),'field_background'=>array('Nền ô nhập','--lk-field-background'),'field_border'=>array('Viền ô nhập','--lk-field-border')) as $key=>$data)$this->add_control($key,array('label'=>$data[0],'type'=>'color','selectors'=>array('{{WRAPPER}}'=>$data[1].': {{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'field_typography','label'=>'Chữ ô nhập','selector'=>'{{WRAPPER}} input, {{WRAPPER}} select, {{WRAPPER}} textarea'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'label_typography','label'=>'Chữ nhãn và tiêu đề nhóm','selector'=>'{{WRAPPER}} label, {{WRAPPER}} legend, {{WRAPPER}} .lk-hours-day-summary'));
  foreach(array('field_height'=>array('Chiều cao ô nhập','--lk-field-height',32,80),'field_gap'=>array('Khoảng cách các trường','--lk-field-gap',0,60),'map_height'=>array('Chiều cao bản đồ chọn ghim','--lk-map-height',180,600)) as $key=>$data)$this->add_responsive_control($key,array('label'=>$data[0],'type'=>'slider','range'=>array('px'=>array('min'=>$data[2],'max'=>$data[3])),'selectors'=>array('{{WRAPPER}}'=>$data[1].': {{SIZE}}{{UNIT}};')));
  $this->end_controls_section();
 }
 protected function button_style(){
  $this->start_controls_section('button_style',array('label'=>'Nút thao tác','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  foreach(array('button_background'=>array('Nền nút','--lk-button-background'),'button_color'=>array('Chữ nút','--lk-button-color'),'button_hover'=>array('Nền khi rê chuột','--lk-button-hover')) as $key=>$data)$this->add_control($key,array('label'=>$data[0],'type'=>'color','selectors'=>array('{{WRAPPER}}'=>$data[1].': {{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'button_typography','label'=>'Chữ trên nút','selector'=>'{{WRAPPER}} .lk-button, {{WRAPPER}} .lk-search>button'));
  $this->add_responsive_control('button_radius',array('label'=>'Bo góc nút','type'=>'slider','range'=>array('px'=>array('min'=>0,'max'=>40)),'selectors'=>array('{{WRAPPER}}'=>'--lk-button-radius: {{SIZE}}{{UNIT}};')));
  $this->end_controls_section();
 }
 protected function appearance(){
  $this->start_controls_section('appearance',array('label'=>'Giao diện','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Màu chủ đạo','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#52ab5c','selectors'=>array('{{WRAPPER}}'=>'--lk-accent: {{VALUE}};')));
  $this->add_control('radius',array('label'=>'Bo góc','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>32)),'selectors'=>array('{{WRAPPER}}'=>'--lk-radius: {{SIZE}}{{UNIT}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'type','selector'=>'{{WRAPPER}}'));
  $this->end_controls_section();
 }
}
class LK_Widget_Search extends LK_Widget {
 public function get_name(){return 'lk-search';} public function get_title(){return 'Tìm kiếm địa điểm';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Tìm kiếm'));
  $this->add_control('placeholder',array('label'=>'Gợi ý ô tìm kiếm','type'=>'text','default'=>'Tên quán, khu vực, kiểu quán,...'));
  $this->add_control('button',array('label'=>'Chữ nút','type'=>'text','default'=>'Tìm quán'));
  $this->add_control('mode',array('label'=>'Kiểu','type'=>'select','options'=>array('hero'=>'Hero lớn','header'=>'Thanh tìm kiếm header'),'default'=>'hero'));
  $this->end_controls_section();$this->appearance();
 }
 protected function render(){lk_search($this->get_settings_for_display());}
}
class LK_Widget_Listings extends LK_Widget {
 public function get_name(){return 'lk-listings';} public function get_title(){return 'Danh sách địa điểm động';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Danh sách địa điểm'));
  $this->add_control('listing_selection',array('label'=>'Nguồn danh sách','type'=>'select','label_block'=>true,'options'=>array('legacy'=>'Tự động (cấu hình cũ)','curated'=>'Chọn theo từng danh mục'),'default'=>'legacy','description'=>'Chế độ mới chỉ áp dụng khi bạn chủ động chọn; không thay đổi cấu hình đã lưu.'));
  $this->add_control('limit',array('label'=>'Số địa điểm','type'=>'number','default'=>8,'min'=>1,'max'=>50));
  $this->add_control('columns',array('label'=>'Số cột desktop','type'=>'select','options'=>array(2=>'2',3=>'3',4=>'4'),'default'=>4));
  $this->add_control('category',array('label'=>'Slug danh mục (để trống = tất cả)','type'=>'text','condition'=>array('listing_selection'=>'legacy')));
  $this->add_control('nearby',array('label'=>'Địa điểm lân cận bài đang xem','type'=>'switcher','return_value'=>'yes','default'=>'','condition'=>array('listing_selection'=>'legacy')));
  $this->add_control('tabs',array('label'=>'Hiện tab danh mục','type'=>'switcher','return_value'=>'yes','default'=>'yes'));
  $this->add_control('more',array('label'=>'Chữ liên kết xem thêm','type'=>'text','default'=>'Xem thêm'));

  $categories=array(''=>'Chọn danh mục');foreach(lk_directory_terms('listing-category') as $term)$categories[$term->slug]=$term->name;
  $choices=array();foreach(lk_posts() as $post)$choices[$post->post_name]=lk_listing_picker_title($post);
  $row=new \Elementor\Repeater();
  $row->add_control('tab_title',array('label'=>'Tên tab (có thể để trống)','type'=>'text'));
  $row->add_control('tab_category',array('label'=>'Danh mục','type'=>'select2','options'=>$categories,'label_block'=>true));
  $row->add_control('selection_mode',array('label'=>'Địa điểm hiển thị','type'=>'select','options'=>array('auto'=>'Tự động trong danh mục','manual'=>'Chọn thủ công'),'default'=>'auto'));
  $row->add_control('listing_slugs',array('label'=>'Chọn địa điểm trong danh mục','type'=>'select2','multiple'=>true,'options'=>$choices,'label_block'=>true,'condition'=>array('selection_mode'=>'manual'),'description'=>'Chọn danh mục trước. Địa điểm hiển thị theo thứ tự chọn; bỏ chọn rồi chọn lại để đưa xuống cuối.'));
  $this->add_control('category_groups',array('label'=>'Danh mục và địa điểm','type'=>'repeater','fields'=>$row->get_controls(),'default'=>array(),'title_field'=>'{{{ tab_title || tab_category || "Chọn danh mục" }}}','condition'=>array('listing_selection'=>'curated'),'description'=>'Thêm và kéo thả để đổi thứ tự danh mục. Danh mục/bài đã xóa, chưa xuất bản hoặc không còn thuộc danh mục sẽ không được hiển thị.'));
  $this->end_controls_section();$this->appearance();
 }
 public function get_script_depends(){return array('lk-listing-selection');}
 public function get_style_depends(){return array_merge(parent::get_style_depends(),array('lk-listing-selection'));}
 protected function render(){$settings=$this->get_settings_for_display();$settings['lk_widget_id']=$this->get_id();lk_listing_grid($settings);}
}
class LK_Widget_Ads extends LK_Widget {
 public function get_name(){return 'lk-ads';} public function get_title(){return 'Địa điểm được tài trợ (Ads)';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Quảng cáo ListingPro'));
  $this->add_control('placement',array('label'=>'Vị trí quảng cáo','type'=>'select','options'=>lk_ads_placements(),'default'=>'lp_random_ads'));
  $this->add_control('title',array('label'=>'Tiêu đề','type'=>'text','default'=>'Được tài trợ'));
  $this->add_control('show_title',array('label'=>'Hiện tiêu đề','type'=>'switcher','return_value'=>'yes','default'=>'yes'));
  $this->add_control('limit',array('label'=>'Số quảng cáo tối đa','type'=>'number','default'=>3,'min'=>1,'max'=>6));
  $this->add_control('ads_note',array('type'=>'raw_html','raw'=>'Nội dung địa điểm lấy từ các chiến dịch Ads đang hiệu lực. Bạn có thể kéo thả hoặc xóa widget này như các khối Elementor khác.'));
  $this->end_controls_section();$this->appearance();
  $this->start_controls_section('ads_style',array('label'=>'Tiêu đề và thẻ quảng cáo','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('ads_title_color',array('label'=>'Màu tiêu đề','type'=>'color','selectors'=>array('{{WRAPPER}}'=>'--lk-ads-title-color: {{VALUE}};')));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'ads_heading_type','label'=>'Chữ tiêu đề khối','selector'=>'{{WRAPPER}} .lk-ads-title'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'ads_card_type','label'=>'Chữ tên địa điểm','selector'=>'{{WRAPPER}} .lk-place h3'));
  $this->add_control('ads_columns',array('label'=>'Số cột Spotlight trên PC','type'=>'select','options'=>array(1=>'1',2=>'2',3=>'3',4=>'4'),'selectors'=>array('{{WRAPPER}}'=>'--lk-ads-columns: {{VALUE}};')));
  $this->add_responsive_control('ads_gap',array('label'=>'Khoảng cách thẻ','type'=>'slider','range'=>array('px'=>array('min'=>0,'max'=>60)),'selectors'=>array('{{WRAPPER}}'=>'--lk-ads-gap: {{SIZE}}{{UNIT}};')));
  $this->end_controls_section();
 }
 protected function render(){
  $settings=$this->get_settings_for_display();$placement=$settings['placement']??'lp_random_ads';$ids=null;$exclude=0;
  if($placement==='lp_top_in_search_page_ads'){$state=lk_search_state();$ids=lk_ads_search_ids(lk_filtered_posts($state),$state);}
  if($placement==='lp_detail_page_ads'){$exclude=lk_context_id();$GLOBALS['lk_ads_detail_rendered']=true;}
  lk_render_ads($placement,$settings['limit']??3,$ids,$exclude,$settings);
 }
}
class LK_Widget_Results extends LK_Widget {
 public function get_name(){return 'lk-results';} public function get_title(){return 'Kết quả và bộ lọc địa điểm';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Bộ lọc tìm kiếm'));
  $this->add_control('filter_title',array('label'=>'Tiêu đề bộ lọc','type'=>'text','default'=>'Lọc kết quả'));
  $this->add_control('mobile_filter_title',array('label'=>'Tiêu đề bộ lọc mobile','type'=>'text','default'=>'Bộ lọc'));
  foreach(array('categories_title'=>'Danh mục','regions_title'=>'Khu vực','collections_title'=>'Bộ sưu tập','price_tier_title'=>'Mức giá','price_range_title'=>'Khoảng giá','features_title'=>'Tiện ích') as $key=>$label)$this->add_control($key,array('label'=>'Tiêu đề '.$label,'type'=>'text','default'=>$label));
  $this->add_control('help',array('type'=>'raw_html','raw'=>'Dữ liệu lấy từ Địa điểm ListingPro. Các bộ lọc được giữ trong URL khi tải lại hoặc chia sẻ.'));
  $this->end_controls_section();$this->appearance();$this->fields_style();$this->button_style();
  $this->start_controls_section('results_layout',array('label'=>'Bố cục bộ lọc','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('filter_width',array('label'=>'Chiều rộng bộ lọc desktop','type'=>'slider','range'=>array('px'=>array('min'=>200,'max'=>400)),'selectors'=>array('{{WRAPPER}}'=>'--lk-filter-width: {{SIZE}}{{UNIT}};')));
  $this->add_responsive_control('result_gap',array('label'=>'Khoảng cách thẻ kết quả','type'=>'slider','range'=>array('px'=>array('min'=>0,'max'=>50)),'selectors'=>array('{{WRAPPER}}'=>'--lk-result-gap: {{SIZE}}{{UNIT}};')));
  $this->add_control('panel_background',array('label'=>'Nền bộ lọc','type'=>'color','selectors'=>array('{{WRAPPER}}'=>'--lk-panel-background: {{VALUE}};')));
  $this->end_controls_section();
 }
 protected function render(){lk_results($this->get_settings_for_display());}
}
class LK_Widget_Reviews extends LK_Widget {
 public function get_name(){return 'lk-reviews';} public function get_title(){return 'Đánh giá cộng đồng';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Đánh giá'));
  $this->add_control('title',array('label'=>'Tiêu đề','type'=>'text','default'=>'Đánh giá từ cộng đồng'));
  $this->add_control('invite_title',array('label'=>'Tiêu đề mời đánh giá','type'=>'text','default'=>'Bạn đã từng đến đây?'));
  $this->add_control('invite_copy',array('label'=>'Lời mời đánh giá','type'=>'textarea','default'=>'Chia sẻ trải nghiệm và cảm nhận của bạn cho mọi người cùng biết.'));
  $this->add_control('invite_image',array('label'=>'Hình minh họa','type'=>'media','default'=>array('id'=>0,'url'=>lk_asset('review-illustration'))));
  foreach(array('modal_title'=>'Đánh giá','rating_title'=>'Xếp hạng của bạn','review_heading'=>'Đánh giá của bạn','anonymous_title'=>'Đánh giá ẩn danh','review_button'=>'Gửi đánh giá') as $key=>$label)$this->add_control($key,array('label'=>$label,'type'=>'text','default'=>$label));
  $this->end_controls_section();$this->appearance();$this->fields_style();$this->button_style();
  $this->start_controls_section('review_popup',array('label'=>'Popup đánh giá','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  foreach(array('review_width'=>array('Chiều rộng popup','--lk-review-width',360,960),'star_size'=>array('Kích thước sao','--lk-star-size',18,44)) as $key=>$data)$this->add_responsive_control($key,array('label'=>$data[0],'type'=>'slider','range'=>array('px'=>array('min'=>$data[2],'max'=>$data[3])),'selectors'=>array('{{WRAPPER}}'=>$data[1].': {{SIZE}}{{UNIT}};')));
  $this->add_control('panel_background',array('label'=>'Nền popup','type'=>'color','selectors'=>array('{{WRAPPER}}'=>'--lk-panel-background: {{VALUE}};')));
  $this->end_controls_section();
 }
 protected function render(){lk_reviews($this->get_settings_for_display());}
}
class LK_Widget_Form extends LK_Widget {
 // Elementor also asks the unconfigured widget prototype for its dependencies.
 public function get_script_depends(){return array('lk-location-picker','lk-listing-fields');}
 public function get_style_depends(){return array_merge(parent::get_style_depends(),array('lk-location-picker'));}
 public function get_name(){return 'lk-form';} public function get_title(){return 'Biểu mẫu Tiền Hải';}
 public function get_icon(){return 'eicon-form-horizontal';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Biểu mẫu'));
  $this->add_control('kind',array('label'=>'Loại biểu mẫu','type'=>'select','options'=>array('contact'=>'Liên hệ','add-place'=>'Thêm địa điểm','review'=>'Viết review đầy đủ'),'default'=>'contact'));
  foreach(array('basic_title'=>'Thông tin cơ bản','other_title'=>'Thông tin khác','contact_title'=>'Thông tin liên hệ','images_title'=>'Media','video_title'=>'Video giới thiệu (không bắt buộc)','featured_title'=>'Ảnh nổi bật','features_title'=>'Tiện ích','collections_title'=>'Bộ sưu tập','address_title'=>'Thêm Địa Chỉ Tùy Chỉnh','hours_title'=>'Giờ mở cửa theo thứ','price_title'=>'Thông tin giá','add_place_button'=>'Thêm địa điểm') as $key=>$label)$this->add_control($key,array('label'=>$label,'type'=>'text','default'=>$label,'condition'=>array('kind'=>'add-place')));
  $this->add_control('button',array('label'=>'Chữ nút gửi','type'=>'text','default'=>'Gửi'));
  foreach(array('rating_title'=>array('Tiêu đề xếp hạng','Xếp hạng của bạn'),'review_heading'=>array('Tiêu đề nội dung đánh giá','Đánh giá của bạn'),'place_title'=>array('Tiêu đề chọn địa điểm','Địa điểm'),'recent_title'=>array('Tiêu đề lịch sử','Đánh giá gần đây'),'anonymous_title'=>array('Tiêu đề ẩn danh','Đánh giá ẩn danh')) as $key=>$label)$this->add_control($key,array('label'=>$label[0],'type'=>'text','default'=>$label[1],'condition'=>array('kind'=>'review')));
  $this->end_controls_section();$this->appearance();$this->fields_style();$this->button_style();
 }
 protected function render(){lk_form($this->get_settings_for_display());}
}
class LK_Widget_Account extends LK_Widget {
 public function get_name(){return 'lk-account';} public function get_title(){return 'Tài khoản và địa điểm đã lưu';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Tài khoản'));
  $this->add_control('display_mode',array('label'=>'Kiểu hiển thị','type'=>'select','options'=>array('page'=>'Trang tài khoản','login'=>'Biểu mẫu đăng nhập'),'default'=>'page'));
  foreach(array('title'=>'Tài khoản của bạn','login_title'=>'Đăng nhập tài khoản','username_label'=>'Email hoặc tên người dùng','social_divider'=>'hoặc tiếp tục với','facebook_label'=>'Facebook','google_label'=>'Google') as $key=>$value)$this->add_control($key,array('label'=>$value,'type'=>'text','default'=>$value));
  $this->add_control('show_social',array('label'=>'Hiện nút mạng xã hội','type'=>'switcher','return_value'=>'yes','default'=>'yes'));
  $this->end_controls_section();$this->appearance();$this->fields_style();$this->button_style();
  $this->start_controls_section('login_style',array('label'=>'Tiêu đề đăng nhập','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'login_heading','selector'=>'{{WRAPPER}} .lk-login-heading'));
  $this->end_controls_section();
 }
 protected function render(){$s=$this->get_settings_for_display();lk_account($s['display_mode']??'page',$s);}
}
class LK_Widget_Feed extends LK_Widget {
 public function get_name(){return 'lk-feed';} public function get_title(){return 'Bảng tin khám phá';}
 protected function register_controls(){$this->start_controls_section('content',array('label'=>'Bảng tin'));$this->add_control('mode',array('label'=>'Nội dung','type'=>'select','options'=>array('feed'=>'Bảng tin đánh giá','authors'=>'Thành viên đóng góp'),'default'=>'feed'));$this->add_control('limit',array('label'=>'Số bài hiển thị','type'=>'number','default'=>5));$this->add_control('empty_title',array('label'=>'Tiêu đề khi chưa có bài','type'=>'text','default'=>'Chia sẻ trải nghiệm đầu tiên','condition'=>array('mode'=>'feed')));$this->add_control('empty_copy',array('label'=>'Lời mời khi chưa có bài','type'=>'textarea','default'=>'Bạn vừa ghé một địa điểm thú vị ở Tiền Hải? Viết đánh giá để mọi người cùng khám phá.','condition'=>array('mode'=>'feed')));$this->add_control('empty_image',array('label'=>'Hình khi chưa có bài','type'=>'media','default'=>array('id'=>0,'url'=>lk_asset('review-illustration')),'condition'=>array('mode'=>'feed')));$this->end_controls_section();$this->appearance();}
 protected function render(){$settings=$this->get_settings_for_display();if(($settings['mode']??'feed')==='authors')lk_community_authors();else lk_feed($settings);}
}

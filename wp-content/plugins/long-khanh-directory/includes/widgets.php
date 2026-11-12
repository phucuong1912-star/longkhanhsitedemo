<?php
if(!defined('ABSPATH'))exit;
abstract class LK_Widget extends \Elementor\Widget_Base {
 protected function is_dynamic_content(): bool {return true;}
 public function get_categories(){return array('long-khanh');}
 public function get_icon(){return 'eicon-map-pin';}
 public function get_style_depends(){return array('lk-site');}
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
  $this->add_control('limit',array('label'=>'Số địa điểm','type'=>'number','default'=>8,'min'=>1,'max'=>50));
  $this->add_control('columns',array('label'=>'Số cột desktop','type'=>'select','options'=>array(2=>'2',3=>'3',4=>'4'),'default'=>4));
  $this->add_control('category',array('label'=>'Slug danh mục (để trống = tất cả)','type'=>'text'));
  $this->add_control('nearby',array('label'=>'Địa điểm lân cận bài đang xem','type'=>'switcher','return_value'=>'yes','default'=>''));
  $this->add_control('tabs',array('label'=>'Hiện tab danh mục','type'=>'switcher','return_value'=>'yes','default'=>'yes'));
  $this->add_control('more',array('label'=>'Chữ liên kết xem thêm','type'=>'text','default'=>'Xem thêm'));
  $this->end_controls_section();$this->appearance();
 }
 protected function render(){lk_listing_grid($this->get_settings_for_display());}
}
class LK_Widget_Results extends LK_Widget {
 public function get_name(){return 'lk-results';} public function get_title(){return 'Kết quả và bộ lọc địa điểm';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Bộ lọc tìm kiếm'));
  $this->add_control('filter_title',array('label'=>'Tiêu đề bộ lọc','type'=>'text','default'=>'Lọc kết quả'));
  $this->add_control('help',array('type'=>'raw_html','raw'=>'Dữ liệu lấy từ Địa điểm ListingPro. Các bộ lọc được giữ trong URL khi tải lại hoặc chia sẻ.'));
  $this->end_controls_section();$this->appearance();
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
  $this->end_controls_section();$this->appearance();
 }
 protected function render(){lk_reviews($this->get_settings_for_display());}
}
class LK_Widget_Form extends LK_Widget {
 public function get_name(){return 'lk-form';} public function get_title(){return 'Biểu mẫu Long Khánh';}
 public function get_icon(){return 'eicon-form-horizontal';}
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Biểu mẫu'));
  $this->add_control('kind',array('label'=>'Loại biểu mẫu','type'=>'select','options'=>array('contact'=>'Liên hệ','add-place'=>'Thêm địa điểm','review'=>'Viết review đầy đủ'),'default'=>'contact'));
  $this->add_control('button',array('label'=>'Chữ nút gửi','type'=>'text','default'=>'Gửi'));
  foreach(array('rating_title'=>array('Tiêu đề xếp hạng','Xếp hạng của bạn'),'review_heading'=>array('Tiêu đề nội dung đánh giá','Đánh giá của bạn'),'place_title'=>array('Tiêu đề chọn địa điểm','Địa điểm'),'recent_title'=>array('Tiêu đề lịch sử','Đánh giá gần đây'),'anonymous_title'=>array('Tiêu đề ẩn danh','Đánh giá ẩn danh')) as $key=>$label)$this->add_control($key,array('label'=>$label[0],'type'=>'text','default'=>$label[1],'condition'=>array('kind'=>'review')));
  $this->end_controls_section();$this->appearance();
 }
 protected function render(){lk_form($this->get_settings_for_display());}
}
class LK_Widget_Account extends LK_Widget {
 public function get_name(){return 'lk-account';} public function get_title(){return 'Tài khoản và địa điểm đã lưu';}
 protected function register_controls(){$this->start_controls_section('content',array('label'=>'Tài khoản'));$this->add_control('title',array('label'=>'Tiêu đề','type'=>'text','default'=>'Tài khoản của bạn'));$this->end_controls_section();$this->appearance();}
 protected function render(){lk_account('page',$this->get_settings_for_display());}
}
class LK_Widget_Feed extends LK_Widget {
 public function get_name(){return 'lk-feed';} public function get_title(){return 'Bảng tin khám phá';}
 protected function register_controls(){$this->start_controls_section('content',array('label'=>'Bảng tin'));$this->add_control('mode',array('label'=>'Nội dung','type'=>'select','options'=>array('feed'=>'Bảng tin đánh giá','authors'=>'Thành viên đóng góp'),'default'=>'feed'));$this->add_control('limit',array('label'=>'Số bài hiển thị','type'=>'number','default'=>5));$this->add_control('empty_title',array('label'=>'Tiêu đề khi chưa có bài','type'=>'text','default'=>'Chia sẻ trải nghiệm đầu tiên','condition'=>array('mode'=>'feed')));$this->add_control('empty_copy',array('label'=>'Lời mời khi chưa có bài','type'=>'textarea','default'=>'Bạn vừa ghé một địa điểm thú vị ở Long Khánh? Viết đánh giá để mọi người cùng khám phá.','condition'=>array('mode'=>'feed')));$this->add_control('empty_image',array('label'=>'Hình khi chưa có bài','type'=>'media','default'=>array('id'=>0,'url'=>lk_asset('review-illustration')),'condition'=>array('mode'=>'feed')));$this->end_controls_section();$this->appearance();}
 protected function render(){$settings=$this->get_settings_for_display();if(($settings['mode']??'feed')==='authors')lk_community_authors();else lk_feed($settings);}
}

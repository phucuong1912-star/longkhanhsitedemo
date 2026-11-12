<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Promotional_Services extends Widget_Base {

    public function get_name() {
        return 'listingpro-promotional-services';
    }

    public function get_title() {
        return __( 'Promotional Services', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'listing_pro_services_img',
            [
                'label' => __( 'Banner Left Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'listing_pro_services_title',
            [
                'label' => __( 'Element Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'pro_services_desc',
            [
                'label' => __( 'Element Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'elementor-listingpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_transform',
            [
                'label' => __( 'Text Transform', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'None', 'elementor-hello-world' ),
                    'uppercase' => __( 'UPPERCASE', 'elementor-listingpro' ),
                    'lowercase' => __( 'lowercase', 'elementor-listingpro' ),
                    'capitalize' => __( 'Capitalize', 'elementor-listingpro' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_pro_services( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_pro_services')) {
   function listingpro_shortcode_pro_services($atts, $content = null) {
       extract(shortcode_atts(array(
           'listing_pro_services_img'      => get_template_directory_uri()."/assets/images/servcs1.png",
           'listing_pro_services_title'         => '',
           'pro_services_desc' 					=> '',
       ), $atts));

       $output = null;

       $thumbImg = '';
       if (!empty($listing_pro_services_img)) {
           if( is_array( $listing_pro_services_img ) )
           {
               $listing_pro_services_img   =   $listing_pro_services_img['id'];
           }
           $bgImage = wp_get_attachment_image_src( $listing_pro_services_img, 'full' );
           $thumbImg = '<img src="'.$bgImage[0].'" alt="image">';
       }else{
           $thumbImg = '';
       }

       $output .='
	<div class="promotional-service">
		<div class="promotiona-thumb">
			'.$thumbImg.'
		</div>
		<div class="promotiona-text-details">
			<h3>'.$listing_pro_services_title.'</h3>
			<p>'.$pro_services_desc.'</p>
		</div>
	</div>';

       return $output;
   }
}
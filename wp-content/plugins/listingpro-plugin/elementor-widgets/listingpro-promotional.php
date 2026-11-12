<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Promotional_Element extends Widget_Base {

    public function get_name() {
        return 'listingpro-promotional-element';
    }

    public function get_title() {
        return __( 'Promotional Element', 'elementor-listingpro' );
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
            'listing_element_left_img',
            [
                'label' => __( 'Banner Left Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'listing_element_title',
            [
                'label' => __( 'Element Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'element_desc',
            [
                'label' => __( 'Element Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'element_link_title',
            [
                'label' => __( 'Element Link Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'element_link_url',
            [
                'label' => __( 'Element Link URL', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'element_phone_number',
            [
                'label' => __( 'Element Phone Number', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
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
        echo listingpro_shortcode_promotion( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_promotion')) {
    function listingpro_shortcode_promotion($atts, $content = null) {
        extract(shortcode_atts(array(
            'listing_element_left_img'      => get_template_directory_uri()."/assets/images/adss.png",
            'listing_element_title'         => '',
            'element_desc' 					=> '',
            'element_link_title' 		  	=> '',
            'element_link_url' 		  		=> '',
            'element_phone_number' 		  	=> '',
        ), $atts));

        $output = null;

        $leftImg = '';
        if (!empty($listing_element_left_img)) {
            if( is_array( $listing_element_left_img ) )
            {
                $listing_element_left_img   =   $listing_element_left_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $listing_element_left_img, 'full' );
            $leftImg = '<img src="'.$bgImage[0].'"alt="image">';
        }else{
            $leftImg = '';
        }

        $output .='
	<div class="promotional-element">
		<div class="promotional-row">
			<div class="promotiona-col-left">
				'.$leftImg.'
			</div>
			<div class="promotiona-col-right">
				<h3>'.$listing_element_title.'</h3>
				<p>'.$element_desc.'</p>
				<a href="'.$element_link_url.'" class="lp-sent-btn">'.$element_link_title.'</a>
				<p class="phone_content">'.$element_phone_number.'</p>
			</div>
		</div>
	</div>';

        return $output;
    }
}
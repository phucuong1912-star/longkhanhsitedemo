<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Promotional_Support extends Widget_Base {

    public function get_name() {
        return 'listingpro-promotional-support';
    }

    public function get_title() {
        return __( 'Promotional Support', 'elementor-listingpro' );
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
            'support_bg_img',
            [
                'label' => __( 'Support Background Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri()."/assets/images/support.jpg" ]
            ]
        );
        $this->add_control(
            'support_title',
            [
                'label' => __( 'Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'John Doe'
            ]
        );
        $this->add_control(
            'support_designation',
            [
                'label' => __( 'Designation', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'John Doe, CEO Abc Organisation'
            ]
        );
        $this->add_control(
            'support_short_desc',
            [
                'label' => __( 'Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua  eiusmod tempor incididunt ut labore et dolore magna aliqua.'
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
        echo listingpro_shortcode_support( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_support')) {
    function listingpro_shortcode_support($atts, $content = null) {
        extract(shortcode_atts(array(
            'support_bg_img'      	=> get_template_directory_uri()."/assets/images/support.jpg",
            'support_title'         => 'John Doe',
            'support_designation' 	=> 'John Doe, CEO Abc Organisation',
            'support_short_desc' 	=> 'Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua  eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ), $atts));

        $output = null;

        $supportImg = '';
        if (!empty($support_bg_img)) {
            if( is_array( $support_bg_img ) )
            {
                $support_bg_img =   $support_bg_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $support_bg_img, 'full' );
            $supportImg = 'style="background-image: url('.$bgImage[0].');"';
        }else{
            $supportImg = '';
        }

        $output .='
	<div class="promotional-support" '.$supportImg.'>
		<div class="support-section">
			<div class="support-text-details">
				<div class="testi-detail">
					<p>'. $support_short_desc .'</p>
				</div>
				<h3>'.$support_title.'</h3>
				<p>'.$support_designation.'</p>
			</div>
		</div>
	</div>';

        return $output;
    }
}
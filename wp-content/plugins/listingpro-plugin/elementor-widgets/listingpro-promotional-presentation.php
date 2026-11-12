<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Promotional_Presentation extends Widget_Base {

    public function get_name() {
        return 'listingpro-promotional-presentation';
    }

    public function get_title() {
        return __( 'Promotional Presentation', 'elementor-listingpro' );
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
            'presentation_title',
            [
                'label' => __( 'Presentaion Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'presentation_short_desc',
            [
                'label' => __( 'Presentaion Short Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'presentation_title_first',
            [
                'label' => __( 'Presentaion First Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'presentation_designation_first',
            [
                'label' => __( 'Presentaion First Designation', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'presentation_first_img',
            [
                'label' => __( 'Presentaion First Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri()."/assets/images/presentation.png" ]
            ]
        );
        $this->add_control(
            'presentation_title_second',
            [
                'label' => __( 'Presentaion Second Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'presentation_designation_second',
            [
                'label' => __( 'Presentaion Second Designation', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'presentation_second_img',
            [
                'label' => __( 'Presentaion Left Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri()."/assets/images/presentation2.png" ]
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
        echo listingpro_shortcode_presentation( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_presentation')) {
    function listingpro_shortcode_presentation($atts, $content = null) {
        extract(shortcode_atts(array(
            'presentation_title'      				=> '',
            'presentation_short_desc'         		=> '',
            'presentation_title_first' 		  		=> '',
            'presentation_designation_first' 		=> '',
            'presentation_first_img' 		  		=> get_template_directory_uri()."/assets/images/presentation.png",
            'presentation_title_second' 		  	=> '',
            'presentation_designation_second' 		=> '',
            'presentation_second_img' 		  		=> get_template_directory_uri()."/assets/images/presentation2.png",
        ), $atts));

        $output = null;

        $presentationImg1 = '';
        if (!empty($presentation_first_img)) {
            if( is_array( $presentation_first_img ) )
            {
                $presentation_first_img =   $presentation_first_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $presentation_first_img, 'full' );
            $presentationImg1 = '<img src="'.$bgImage[0].'" alt="image">';
        }else{
            $presentationImg1 = '';
        }

        $presentationImg2 = '';
        if (!empty($presentation_second_img)) {
            if( is_array( $presentation_second_img ) )
            {
                $presentation_second_img    =   $presentation_second_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $presentation_second_img, 'full' );
            $presentationImg2 = '<img src="'.$bgImage[0].'" alt="image">';
        }else{
            $presentationImg2 = '';
        }

        $output .='
	<div class="promotional-presentation">
		<div class="top-desc">
			<h2>'.$presentation_title.'</h2>
			<p>'.$presentation_short_desc.'</p>
		</div>
		<div class="presentation-section">
			<div class="presentation-text-details">
				<h3>'.$presentation_title_first.'</h3>
				<p>'.$presentation_designation_first.'</p>
			</div>
			<div class="presentation-thumb">
				'.$presentationImg1.'
			</div>
		</div>
		<div class="presentation-section">
			<div class="presentation-text-details">
				<h3>'.$presentation_title_second.'</h3>
				<p>'.$presentation_designation_second.'</p>
			</div>
			<div class="presentation-thumb">
				'.$presentationImg2.'
			</div>
		</div>
	</div>';

        return $output;
    }
}
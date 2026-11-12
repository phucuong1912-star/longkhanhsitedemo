<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Call_To_Action extends Widget_Base {

    public function get_name() {
        return 'call-to-action';
    }

    public function get_title() {
        return __( 'Call To Action', 'elementor-listingpro' );
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
            'listingpro_calltoaction_style',
            [
                'label' => __( 'Style', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'style1',
                'options' => [
                    'style1' => __("Call to Action with Button", "elementor-listingpro"),
                    "style3" => __("Call to Action Style 3", "elementor-listingpro"),
                    "style2" => __("Call to Action without Button", "elementor-listingpro"),
                ],
            ]
        );
        $this->add_control(
            'calltoaction_title',
            [
                'label' => __( 'Call to Action Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'calltoaction_desc',
            [
                'label' => __( 'Short Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'listingpro_calltoaction_style' => array('style1', 'style2')
                ]
            ]
        );
        $this->add_control(
            'style3_line1',
            [
                'label' => __( 'Line 1', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'daily news &amp; tips',
                'condition' => [
                    'listingpro_calltoaction_style' => 'style3'
                ]
            ]
        );
        $this->add_control(
            'style3_line2',
            [
                'label' => __( 'Line 2', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Read Stories',
                'condition' => [
                    'listingpro_calltoaction_style' => 'style3'
                ]
            ]
        );
        $this->add_control(
            'style3_line2_bg',
            [
                'label' => __( 'Background Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => [
                    'listingpro_calltoaction_style' => 'style3'
                ]
            ]
        );
        $this->add_control(
            'calltoaction_button',
            [
                'label' => __( 'Button Text', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Let\'s Promote Now',
                'condition' => [
                    'listingpro_calltoaction_style' => array( 'style1', 'style3' )
                ]
            ]
        );
        $this->add_control(
            'calltoaction_button_link',
            [
                'label' => __( 'Button Link', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '#',
                'condition' => [
                    'listingpro_calltoaction_style' => array( 'style1', 'style3' )
                ]
            ]
        );
        $this->add_control(
            'calltoaction_phone',
            [
                'label' => __( 'Phone Number', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'or, Call 1800-ListingPro',
                'condition' => [
                    'listingpro_calltoaction_style' => 'style1'
                ]
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
        echo listingpro_shortcode_calltoaction( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}

if(!function_exists('listingpro_shortcode_calltoaction')) {
    function listingpro_shortcode_calltoaction($atts, $content = null) {
        extract(shortcode_atts(array(

            'listingpro_calltoaction_style'      	=> 'style1',
            'calltoaction_title'      	=> "Reach customers with confidence.",
            'calltoaction_desc'         => "Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore",
            'calltoaction_button' 		=> "Let's Promote Now",
            'calltoaction_button_link' 	=> "#",
            'calltoaction_phone' 		=> "or, Call 1800-ListingPro",
            'style3_line1' 		=> "daily news &amp; tips",
            'style3_line2' 		=> "Read Stories",
            'style3_line2_bg' => ''
        ), $atts));

        $output = null;
        if( $listingpro_calltoaction_style == 'style3' )
        {
            if( !empty( $style3_line2_bg ) )
            {
                if( is_array( $style3_line2_bg ) )
                {
                    $style3_line2_bg    =   $style3_line2_bg['id'];
                }
                $style3_line2_bg    =   wp_get_attachment_url( $style3_line2_bg );
            }
            $output .=  '<div class="lp-read-news-overlay"></div>';
            $output .=  '<div class="lp-read-news">';
            if( !empty( $style3_line1 ) )   $output .=  '<p>'. $style3_line1 .'</p>';
            if( !empty( $style3_line2 ) )   $output .=  '<p class="large-size">'. $style3_line2 .'</p>';
            if( !empty( $calltoaction_button ) ) $output .=  '<a href="'. $calltoaction_button_link .'">'. $calltoaction_button .'</a>';
            $output .=  '</div>';
        }
        else if($listingpro_calltoaction_style == 'style1'){
            $output .='
	
	<div class="call-to-action">
		<div class="calltoaction-left-panel">
			<h3>'. $calltoaction_title .'</h3>
			<p>'.$calltoaction_desc.'</p>
		</div>
		<div class="calltoaction-right-panel">
			<a href="'.$calltoaction_button_link.'">'.$calltoaction_button.'</a>
			<p>'.$calltoaction_phone.'</p>
		</div>
	</div>';
        }else{
            $output .='
	
		<div class="call-to-action2 text-center">
			<div class="calltoaction-left-panel2">
				<h3>'. $calltoaction_title .'</h3>
				<h1>'.$calltoaction_desc.'</h1>
				<img src="'.get_template_directory_uri().'/assets/images/banner-arrow.png" alt="banner-arrow" class="banner-arrow">
			</div>
			
		</div>';

        }
        return $output;
    }
}
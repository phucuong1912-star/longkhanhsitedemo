<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Client_Testimonial extends Widget_Base {

    public function get_name() {
        return 'client-testimonial';
    }

    public function get_title() {
        return __( 'Client Testimonial', 'elementor-listingpro' );
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
            'style',
            [
                'label' => __( 'Testimonial Style', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __("Default, on a white background color", "elementor-listingpro"),
                    "inverted" => __("Inverted, on a dark background color", "elementor-listingpro"),
                ],
            ]
        );
        $this->add_control(
            'name',
            [
                'label' => __( 'Client Name', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'avatar',
            [
                'label' => __( 'Client Avatar', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'testimonial_content',
            [
                'label' => __( 'Testimonial Content', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 3,
            ]
        );
        $this->add_control(
            'designation',
            [
                'label' => __( 'Designation', 'elementor-listingpro' ),
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
        echo listingpro_shortcode_testimonial( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_testimonial')) {
    function listingpro_shortcode_testimonial($atts, $content = null) {
        extract(shortcode_atts(array(
            'style'               => '',
            'name'                => '',
            'avatar'              => '',
            'testimonial_content' => '',
            'designation' 		  => '',
        ), $atts));

        $output = null;
        $style_class = null;

        if ( $style == 'inverted' ) $style_class = ' inverted';
        if( is_array( $avatar ) )
        {
            $avatar =   $avatar['id'];
        }

        $output .= '
	<div class="testimonial-box testimonial'. $style_class .'">';
        $output .= '<div class="testimonial-image">
									<img alt="image" src="'. wp_get_attachment_url($avatar) .'" >
				</div>';
        $output .= '<div class="testimonial-msg triangle-isosceles top">
									<div class="testimonial-tit"> 
										<h3>'. esc_attr($name) .'</h3>
										<div class="testimonial-rating">';
        $output .= esc_attr($designation);


        $output .= '						</div>
									</div>
									<div class="testimonial-des">';

        if ( $testimonial_content ) {
            $output .= '
																	<p>'. wp_kses_post($testimonial_content) .'</p>';
        }
        $output .= '					</div>
					</div>';



        $output .= '</div>';


        return $output;
    }
}
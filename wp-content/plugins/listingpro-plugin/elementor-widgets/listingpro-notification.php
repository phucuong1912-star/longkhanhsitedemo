<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Notification extends Widget_Base {

    public function get_name() {
        return 'listingpro-notification';
    }

    public function get_title() {
        return __( 'Listingpro Notification', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages( $args );

        $thnxPage = array();
        foreach($pages as $p) {
            $thnxPage[$p->ID] = $p->post_title;
        }
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'thankyou_img',
            [
                'label' => __( 'Notification Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri()."/assets/images/thankyou.jpg"],
            ]
        );
        $this->add_control(
            'thankyou_title',
            [
                'label' => __( 'Notification Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        $this->add_control(
            'listingpro_notice',
            [
                'label' => __( 'Select Notice', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'success',
                'options' => [
                    'success' => __("Success", "elementor-listingpro"),
                    "failed" => __("Failed", "elementor-listingpro"),
                ],
            ]
        );
        $this->add_control(
            'success_text',
            [
                'label' => __( 'Success Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'An email receipt with detials about your order has been sent to email address provided.please keep it for your record', 'elementor-listingpro' ),
                'condition' => [
                    'listingpro_notice' => 'success'
                ]
            ]
        );
        $this->add_control(
            'success_txt_img',
            [
                'label' => __( 'Icon with description', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri()."/assets/images/email.jpg" ],
                'condition' => [
                    'listingpro_notice' => 'success'
                ]
            ]
        );
        $this->add_control(
            'thankyou_desc',
            [
                'label' => __( 'Notification Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'default', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'thankyou_goto_page',
            [
                'label' => __( 'Redirect to Page', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cat_abstracted',
                'options' => $thnxPage,
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
                'default' => '',
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
        echo listingpro_shortcode_thankyou( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_thankyou')) {
    function listingpro_shortcode_thankyou($atts, $content = null) {
        extract(shortcode_atts(array(
            'thankyou_img'      	=> get_template_directory_uri()."/assets/images/thankyou.jpg",
            'thankyou_title' 		=> '',
            'listingpro_notice' 	=> '',
            'success_text' 			=> esc_html__( 'An email receipt with detials about your order has been sent to email address provided.please keep it for your record', 'js_composer' ),
            'success_txt_img' 		=> get_template_directory_uri()."/assets/images/email.jpg",
            'thankyou_desc' 		=> '',
            'thankyou_goto_page' 	=> '',
        ), $atts));

        $output = null;

        $thnkImg = '';
        if (!empty($thankyou_img)) {
            if( is_array( $thankyou_img ) )
            {
                $thankyou_img   =   $thankyou_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $thankyou_img, 'full' );
            $thnkImg = '<img src="'.$bgImage[0].'" alt="image">';
        }else{
            $thnkImg = '';
        }

        $thnkIcon = '';
        if (!empty($success_txt_img)) {
            if( is_array( $success_txt_img ) )
            {
                $success_txt_img    =   $success_txt_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $success_txt_img, 'full' );
            $thnkIcon = '<img src="'.$bgImage[0].'" alt="image">';
        }else{
            $thnkIcon = '';
        }

        $output .='
	<div class="thankyou-page">
		<div class="thankyou-icon">
			'. $thnkImg .'
		</div>
		<div class="thankyou-panel">
			<h3>'.$thankyou_title.'</h3>';
        if($listingpro_notice == 'success') {
            $output .='
				<div class="success-txt">';
            if(!empty($thnkIcon)) {
                $output .='
						<span>'.$thnkIcon.'</span>';
            }
            $output .='
					<p>'.$success_text.'</p>
				</div>';
        }
        $output .='
			<p>'.$thankyou_desc.'</p>
			<ul>
				<li>
					<a href="'.get_the_permalink($thankyou_goto_page).'">'.get_the_title( $thankyou_goto_page ).'</a>
				</li>
				<li>
					<a href="'.esc_url(home_url('/')).'">'.esc_html__('Home', 'listingpro-plugin').'</a>
				</li>
			</ul>
		</div>
	</div>';

        return $output;
    }
}
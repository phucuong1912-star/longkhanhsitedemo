<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Feature_Box extends Widget_Base {

    public function get_name() {
        return 'feature-box';
    }

    public function get_title() {
        return __( 'Feature Box', 'elementor-listingpro' );
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
            'box_style',
            [
                'label' => __( 'Box Style', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'style1',
                'options' => [
                    'style1' => __( 'Style 1', 'elementor-listingpro' ),
                    'style2' => __( 'Style 2', 'elementor-listingpro' ),
                ],
            ]
        );
        $this->add_control(
            'style_2_title',
            [
                'label' => __( 'Title for Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'box_style' => 'style2'
                ]
            ]
        );
        $this->add_control(
            'style_2_stitle',
            [
                'label' => __( 'Sub Title for Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'box_style' => 'style2'
                ]
            ]
        );
        $this->add_control(
            'feature_image',
            [
                'label' => __( 'Feature Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'fbox_desc',
            [
                'label' => __( 'Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'botton_link1',
            [
                'label' => __( 'Button Link (1)', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'botton_image1',
            [
                'label' => __( 'Button BG Image (1)', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'botton_link2',
            [
                'label' => __( 'Button Link (2)', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'botton_image2',
            [
                'label' => __( 'Button BG Image (2)', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
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
        echo listingpro_shortcode_feature_box( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_feature_box')) {
    function listingpro_shortcode_feature_box($atts, $content = null) {
        extract(shortcode_atts(array(
            'box_style'   => 'style1',
            'feature_image'   => '',
            'style_2_title'   => '',
            'style_2_stitle'   => '',
            'fbox_desc'   => '',
            'botton_link1'   => '',
            'botton_image1'   => '',
            'botton_link2'   => '',
            'botton_image2'   => '',
        ), $atts));

        $output = null;
        $FimageURL=null;
        $bottonImage1=null;
        $bottonImage2=null;

        if ( $feature_image ) {
            if( is_array( $feature_image ) )
            {
                $feature_image  =   $feature_image['id'];
            }
            $imgurl = wp_get_attachment_image_src( $feature_image, 'full');
            $FimageURL = '<img alt="image" src="'. $imgurl[0] .'">';
        }
        if ( $botton_image1 ) {
            if( is_array( $botton_image1 ) )
            {
                $botton_image1  =   $botton_image1['id'];
            }
            $imgurl = wp_get_attachment_image_src( $botton_image1, 'full');
            $bottonImage1 = '<img alt="image" src="'. $imgurl[0] .'">';
        }
        if ( $botton_image2 ) {
            if( is_array( $botton_image2 ) )
            {
                $botton_image2  =   $botton_image2['id'];
            }
            $imgurl = wp_get_attachment_image_src( $botton_image2, 'full');
            $bottonImage2 = '<img alt="image" src="'. $imgurl[0] .'">';
        }

        if ( $box_style == 'style1' ) {

            $output .= '<div class="lp-section-content-container row">';
            $output .= '<div class="col-md-12 text-center">
						<div class="nearby-banner">
							'.$FimageURL.'
						</div>
						<div class="nearby-description">
							<p>
								'.$fbox_desc.'

							</p>
						</div>';
            if(!empty($bottonImage1) || !empty($bottonImage2)) {
                $output .= '
							<ul class="nearby-download nearby-download-about nearby-download-top">
								<li>
									<a href="'.$botton_link1.'">
										'.$bottonImage1.'
									</a>
								</li>
								<li>
									<a href="'.$botton_link2.'">
										'.$bottonImage2.'
									</a>
								</li>
							</ul>';
            }
            $output .='
					</div>';
            $output .= '</div>';

        }elseif( $box_style == 'style2' ){

            $output .= '<div class="lp-section-content-container row">';

            $output .= '	<div class="col-md-6">
						<div class="">
							<Div class="lp-about-section-best-header">
								<h3 class="margin-top-0">'.$style_2_title.'</h3>
								<p>'.$style_2_stitle.'</p>
							</div>
							<div class="lp-about-section-best-description margin-top-45 ">
								<p class="paragraph-small">
								'.$fbox_desc.'
								</p>';
            if(!empty($bottonImage1) || !empty($bottonImage2)) {
                $output .= '
									<ul class="nearby-download nearby-download-about nearby-download-top">
										<li>
											<a href="'.$botton_link1.'">
												'.$bottonImage1.'
											</a>
										</li>
										<li>
											<a href="'.$botton_link2.'">
												'.$bottonImage2.'
											</a>
										</li>
									</ul>';
            }
            $output .='
							</div>
						</div><!-- ../section-content-container-->
					</div>
					<div class="col-md-6">
						<div class="">
							'.$FimageURL.'
						</div><!-- ../section-content-container-->
					</div>';
            $output .= '</div>';
        }

        return $output;
    }
}
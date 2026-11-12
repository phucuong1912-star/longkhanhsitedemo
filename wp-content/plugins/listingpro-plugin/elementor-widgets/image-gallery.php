<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Image_Gallery extends Widget_Base {

    public function get_name() {
        return 'image-gallery';
    }

    public function get_title() {
        return __( 'Image Gallery', 'elementor-listingpro' );
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
            'imagegallerystyle',
            [
                'label' => __( 'Image gallery styles', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'old_style' => __( 'Old Style', 'elementor-listingpro' ),
                    'new_style' => __( 'New Style', 'elementor-listingpro' ),
                ],
                'default' => 'old_style'
            ]
        );
        $this->add_control(
            'gallery_images',
            [
                'label' => __( 'Image for gallery', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::GALLERY,
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
        echo listingpro_shortcode_gallery( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_gallery')) {
    function listingpro_shortcode_gallery($atts, $content = null) {
        extract(shortcode_atts(array(
            'imagegallerystyle' => '',
            'gallery_images' => ''
        ), $atts));

        $output = null;
        $imgIDs=null;
        $screenImage=null;
        $IDs = $gallery_images;


        if(is_array($IDs)) {
            $imgIDs =   array();
            foreach ($IDs as $img_arr) {
                $imgIDs[]   =   $img_arr['id'];
            }
        } else {
            $imgIDs = explode(',',$IDs);
        }

        $count = 1;
        if ($imagegallerystyle == "old_style") {
            $output .= '<div class="about-gallery  lp-section-content-container popup-gallery clearfix">';

            if (!empty($IDs)) {

                foreach($imgIDs as $imgID){

                    if($count == 1){
                        $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-gallery-thumb1');
                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                        $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';

                        $output .= '	<div class="col-md-5 col-sm-5 about-gallery-box">
							<a href="'.$imgFull[0].'" class="image-popup">
								'.$screenImage.'
							</a>
						</div>';
                    }elseif($count == 2){
                        $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-gallery-thumb2');
                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                        $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';

                        $output .= '	<div class="col-md-4 col-sm-4 about-gallery-box">
							<a href="'.$imgFull[0].'" class="image-popup">
								'.$screenImage.'
							</a>
						</div>';
                    }elseif($count == 3){
                        $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-gallery-thumb3');
                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                        $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';

                        $output .= '	<div class="col-md-3 col-sm-3 about-gallery-box">
							<a href="'.$imgFull[0].'" class="image-popup">
								'.$screenImage.'
							</a>
						</div>';
                    }elseif($count == 4){
                        $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-gallery-thumb4');
                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                        $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';
                        $output .= '	<div class="col-md-7 col-sm-7 about-gallery-box">
							<a href="'.$imgFull[0].'" class="image-popup">
								'.$screenImage.'
							</a>
						</div>';
                    }else{
                        $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-gallery-thumb2');
                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                        $screenImage = '<img alt="image" src="'. $imgurl[0] .'">';
                        $output .= '    <div class="col-md-4 col-sm-4 about-gallery-box">                            <a href="' . $imgFull[0] . '" class="image-popup">                                ' . $screenImage . '                            </a>                        </div>';
                    }

                    $count++;
                }
            }

            $output .= '</div>';
        } else {
            $output .= '<div class="about-gallery  popup-gallery clearfix about-gallery-style2">';
            if (!empty($IDs)) {
                foreach ($imgIDs as $imgID) {
                    if ($count == 1) {
                        $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                        $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                        $screenImage = '<img alt="image" src="' . $imgurl[0] . '">';
                        $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                    } elseif ($count == 2) {
                        $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                        $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                        $screenImage = '<img alt="image" src="' . $imgurl[0] . '">';
                        $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                    } elseif ($count == 3) {
                        $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                        $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                        $screenImage = '<img alt="image" src="' . $imgurl[0] . '">';
                        $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                    } elseif ($count == 4) {
                        $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                        $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                        $screenImage = '<img alt="image" src="' . $imgurl[0] . '">';
                        $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                    } else {
                        $imgurl      = wp_get_attachment_image_src($imgID, 'listingpro-gallery-thumb1');
                        $imgFull     = wp_get_attachment_image_src($imgID, 'full');
                        $screenImage = '<img alt="image" src="' . $imgurl[0] . '">';
                        $output .= '    <div class="col-md-2 col-sm-2 padding-0 about-gallery-box">                                    <a href="' . $imgFull[0] . '" class="image-popup">                                        ' . $screenImage . '                                    </a>                                </div>';
                    }

                    $count++;
                }
            }

            $output .= '</div>';
        }

        return $output;
    }
}
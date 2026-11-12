<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_Activities extends Widget_Base {

    public function get_name() {
        return 'listing-activities';
    }

    public function get_title() {
        return __( 'Activities', 'elementor-listingpro' );
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
	 // classic
        $this->add_control(
            'activity_style',
            [
                'label' => __('Select Style', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style1' => __('Style 1', 'elementor-listingpro'),
                    'style2' => __('Style 2', 'elementor-listingpro'),
                    'style3' => __('Style 3', 'elementor-listingpro'),
                ],
                'default' => 'style1'
            ]
        );
        $this->add_control(
            'number_posts',
            [
                'label' => __( 'Number of Activities', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '3' => __( '3', 'elementor-listingpro' ),
                    '4' => __( '4', 'elementor-listingpro' ),
                    '5' => __( '5', 'elementor-listingpro' ),
                ],
                'default' => '5'
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
        echo listingpro_shortcode_lp_activities( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}


    function listingpro_shortcode_lp_activities($atts, $content = null) {
        extract(shortcode_atts(array(
             'number_posts'   => '5',
            'activity_placeholder' => '',
            'activity_style' => ''
        ), $atts));
        require_once (THEME_PATH . "/include/aq_resizer.php");
        $output = null;

        $args   =   array(
            'post_type' => 'lp-reviews',
            'post_status' => 'publish',
            'posts_per_page' => $number_posts,
        );
        $activities  =   new \WP_Query( $args );
        $img_url    = '';
        $img_url2   = '';
        $img_url3   = '';
        $img_url4   = '';
        global $listingpro_options;
        $placeholder_img    =   '';
        $use_listing_img    =   $listingpro_options['lp_review_img_from_listing'];
        if( $use_listing_img == 'off' )
        {
            $placeholder_img    =   $listingpro_options['lp_review_placeholder'];
            $placeholder_img    =   $placeholder_img['url'];
        }

        if( $activities->have_posts() ) :
            $counter    =   1;
		  if ($activity_style == 'style1') {
            $output .=  '<div class="lp-activities"><div class="lp-section-content-container"> ';
            $output .=  '    <div class="row">';
            while ( $activities->have_posts() ) : $activities->the_post();
                $img_url    = '';
                $img_url2   = '';
                $img_url3   = '';
                $img_url4   = '';
                global $post;
                $r_meta     =   get_post_meta( get_the_ID(), 'lp_listingpro_options', true );
                $LID        =   $r_meta['listing_id'];
                $rating     =   $r_meta['rating'];

                $adStatus = apply_filters('lp_get_ad_status', '', $LID);
                $CHeckAd = '';
                $adClass = '';
                if($adStatus == 'active'){
                    $CHeckAd = '<span>'.esc_html__('Ad','listingpro-plugin').'</span>';
                    $adClass = 'promoted';
                }
                $author_avatar_url = get_user_meta( $post->post_author, "listingpro_author_img_url", true);
                $avatar;
                if( !empty( $author_avatar_url ) )
                {
                    $avatar =  $author_avatar_url;

                }
                else
                {
                    $avatar_url = listingpro_get_avatar_url ( $post->post_author, $size = '55' );
                    $avatar =  $avatar_url;
                }
                $interests = '';
                $Lols = '';
                $loves = '';
                $interVal = esc_html__('Interesting', 'listingpro-plugin');
                $lolVal = esc_html__('Lol', 'listingpro-plugin');
                $loveVal = esc_html__('Love', 'listingpro-plugin');

                $interests = listing_get_metabox_by_ID('review_'.$interVal.'',get_the_ID());
                $Lols = listing_get_metabox_by_ID('review_'.$lolVal.'',get_the_ID());
                $loves = listing_get_metabox_by_ID('review_'.$loveVal.'',get_the_ID());


                if( empty( $interests ) )
                {
                    $interests = 0;
                }
                if( empty( $Lols ) )
                {
                    $Lols = 0;
                }
                if( empty( $loves ) )
                {
                    $loves = 0;
                }

                $reacted_msg    =   esc_html__('You already reacted', 'listingpro-plugin');
                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                if( $use_listing_img == 'off' )
                {
                    $img_url    = aq_resize( $placeholder_img, '360', '267', true, true, true);
                    $img_url2   = aq_resize( $placeholder_img, '165', '97', true, true, true);
                    $img_url3   = aq_resize( $placeholder_img, '500', '300', true, true, true);
                    $img_url4   = aq_resize( $placeholder_img, '295', '150', true, true, true);
                }
                else
                {
                    if( has_post_thumbnail( $LID ) )
                    {
                        $thumbnail_url  =   get_the_post_thumbnail_url( $LID );
                        $img_url    = aq_resize( $thumbnail_url, '360', '267', true, true, true);
                        $img_url2   = aq_resize( $thumbnail_url, '165', '97', true, true, true);
                        $img_url3   = aq_resize( $thumbnail_url, '500', '300', true, true, true);
                        $img_url4   = aq_resize( $thumbnail_url, '295', '150', true, true, true);
                    }
                    else
                    {
                        $listing_gallery    =   get_post_meta( $LID, 'gallery_image_ids', true );
                        $listing_imagearray = explode(',', $listing_gallery);
                        $listing_image      = wp_get_attachment_image_src( $listing_imagearray[0], 'full');

                        if ( !empty( $listing_image[0] ) )
                        {
                            $img_url    = aq_resize( $listing_image[0], '360', '267', true, true, true);
                            $img_url2   = aq_resize( $listing_image[0], '165', '97', true, true, true);
                            $img_url3   = aq_resize( $listing_image[0], '500', '300', true, true, true);
                            $img_url4   = aq_resize( $listing_image[0], '295', '150', true, true, true);
                        }
                    }
                }

                if( !empty( $gallery ) )
                {
                    $imagearray = explode(',', $gallery);
                    $image      = wp_get_attachment_image_src( $imagearray[0], 'full');
                    $first_img  =   $imagearray[0];
                    if ( !empty( $image[0] ) )
                    {
                        $img_url    = aq_resize( $image[0], '360', '267', true, true, true);
                        $img_url2   = aq_resize( $image[0], '165', '97', true, true, true);
                        $img_url3   = aq_resize( $image[0], '500', '300', true, true, true);
                        $img_url4   = aq_resize( $image[0], '295', '150', true, true, true);
                    }
                }
                if( empty( $img_url ) )
                {
                    if( empty( $activity_placeholder ) )
                    {
                        $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                    }
                    else
                    {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url    =   aq_resize( $element_placehilder_url, '360', '267', true, true, true);;
                    }
                }
                if( empty( $img_url2 ) )
                {
                    if( empty( $activity_placeholder ) )
                    {
                        $img_url2    =     $listingpro_options['lp_def_featured_image']['url'];
                    }
                    else
                    {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url2   = aq_resize( $element_placehilder_url, '165', '97', true, true, true);

                    }
                }
                if( empty( $img_url3 ) )
                {
                    if( empty( $activity_placeholder ) )
                    {
                        $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                    }
                    else
                    {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url3 = aq_resize( $element_placehilder_url, '500', '300', true, true, true);
                    }
                }
                if( empty( $img_url4 ) )
                {
                    if( empty( $activity_placeholder ) )
                    {
                        $img_url4    =     $listingpro_options['lp_def_featured_image']['url'];
                    }
                    else
                    {
                        $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                        $img_url4 = aq_resize( $element_placehilder_url, '295', '150', true, true, true);
                    }
                }
                $lp_liting_title    =   get_the_title( $LID );
                if( strlen( $lp_liting_title ) > 35 )
                {
                    $lp_liting_title    =   substr( $lp_liting_title, 0, 35 ).'...';
                }

                $rating_num_bg  =   '';
                $rating_num_clr  =   '';

                if( $rating < 2 ){ $rating_num_bg  =   'num-level1'; $rating_num_clr  =   'level1'; }
                if( $rating < 3 ){ $rating_num_bg  =   'num-level2'; $rating_num_clr  =   'level2'; }
                if( $rating < 4 ){ $rating_num_bg  =   'num-level3'; $rating_num_clr  =   'level3'; }
                if( $rating >= 4 ){ $rating_num_bg  =   'num-level4'; $rating_num_clr  =   'level4'; }
                if( $number_posts == 3 )
                {
                    $output .=  '
                <div class="col-md-4"> 
                    <div class="lp-activity">
                        <div class="lp-activity-top">
                            <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" class="lp-activity-author-thumb"><img src="'. esc_attr($avatar) .'" alt="'. get_the_title() .'"></a>
                            <a href="'. get_permalink( $LID ) .'" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="'. $img_url3 .'" alt="'. get_the_title() .'"><img class="hidden-sm hidden-xs" src="'. $img_url .'" alt="'. get_the_title() .'"></a>
                        </div>
                        <div class="lp-activity-bottom">
                            <div class="lp-activity-review-writer">
                                <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">'. get_the_author() .'</a> 
                                <p>'.esc_html__('Wrote a review', 'listingpro-plugin' ).'</p>
                            </div>
                            <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                <span class="lp-star-box ';
                    if( $rating > 0 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 1 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 2  ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 3 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 4 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                <span class="lp-rating-num rating-with-colors '. review_rating_color_class($rating) .'">'. $rating .'</span>
                            </div>
                            <h3><a href="'. get_permalink( $LID ) .'">'. $lp_liting_title .'</a></h3>
                            <strong>'. substr( get_the_title(), 0, 35 ) .'</strong>
                            <div class="lp-activity-description">
                                <p>'. substr( $post->post_content, '0', '100' ) .' <a href="'. get_permalink( $LID ) .'">'.esc_html__('Continue Reading', 'listingpro-plugin' ).'</a></p>
                                <div class="activity-reactions">
                                    <a href="#" data-restype="'.$interVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($interests).'" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>'.esc_html__('Interesting','listingpro-plugin').' <span class="react-count">'. $interests .'</span></a>
                                    <a href="#" data-restype="'.$lolVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($Lols).'" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('LOL','listingpro-plugin').' <span class="react-count">'.$Lols.'</span></a>
                                    <a href="#" data-restype="'.$loveVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($loves).'" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('Love','listingpro-plugin').' <span class="react-count">'.$loves.'</span></a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }

                if( $counter == 1 && $number_posts == 5 )
                {

                    $output .=  '
                <div class="col-md-4">
                    <div class="lp-activity">
                        <div class="lp-activity-top">
                            <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" class="lp-activity-author-thumb"><img src="'. esc_attr($avatar) .'" alt="'. get_the_title() .'"></a>
                            <a href="'. get_permalink( $LID ) .'" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="'. $img_url3 .'" alt="'. get_the_title() .'"><img class="hidden-sm hidden-xs" src="'. $img_url .'" alt="'. get_the_title() .'"></a>
                        </div>
                        <div class="lp-activity-bottom">
                            <div class="lp-activity-review-writer">
                                <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">'. get_the_author() .'</a>
                                <p>'.esc_html__('Wrote a review', 'listingpro-plugin' ).'</p>
                            </div>
                            <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                <span class="lp-star-box ';
                    if( $rating > 0 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 1 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 2  ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 3 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box ';
                    if( $rating > 4 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                <span class="lp-rating-num rating-with-colors '. review_rating_color_class($rating) .'">'. $rating .'</span>
                            </div>
                            <h3><a href="'. get_permalink( $LID ) .'">'. $lp_liting_title .'</a></h3>
                            <strong>'. substr( get_the_title(), 0, 35 ) .'</strong>
                            <div class="lp-activity-description">
                                <p>'. substr( $post->post_content, '0', '130' ) .' <a href="'. get_permalink( $LID ) .'">'.esc_html__('Continue Reading', 'listingpro-plugin' ).'</a></p>
                                <div class="activity-reactions">
                                    <a href="#" data-restype="'.$interVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($interests).'" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>'.esc_html__('Interesting','listingpro-plugin').' <span class="react-count">'. $interests .'</span></a>
                                    <a href="#" data-restype="'.$lolVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($Lols).'" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('LOL','listingpro-plugin').' <span class="react-count">'.$Lols.'</span></a>
                                    <a href="#" data-restype="'.$loveVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($loves).'" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('Love','listingpro-plugin').' <span class="react-count">'.$loves.'</span></a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>';

                }
                if( $counter > 1 && $number_posts == 5 )
                {
                    $bottom_class   =   '';
                    if( $counter == $number_posts-1 || $counter == $number_posts )
                    {
                        $bottom_class   =   'bottom0';
                    }
                    if( $counter == 2 )
                    {
                        $output .=  '<div class="col-md-8">';
                        $output .=  '    <div class="row">';
                    }
                    $output .=  '
                <div class="col-md-6">
                    <div class="lp-activity style2 '. $bottom_class .'">
                        <div class="lp-activity-top">
                            <div class="row">
                                <div class="lp-activity-thumb col-md-6">
                                    <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" class="lp-activity-author-thumb"><img src="'. esc_attr($avatar) .'" alt="image"></a>
                                    <a href="' . get_permalink( $LID ) . '"><img class="hidden-xs hidden-sm" src="'. $img_url2 .'" alt="'. get_the_title() .'"><img class="hidden-md hidden-lg" src="'. $img_url3 .'" alt="'. get_the_title() .'"></a>
                                </div>
                                <div class="lp-activity-top-right col-md-6">
                                    <div class="lp-activity-review-writer">
                                        <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">'. get_the_author() .'</a>
                                <p>'.esc_html__('Wrote a review', 'listingpro-plugin' ).'</p>
                                    </div>
                                    <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                        <span class="lp-star-box ';
                    if( $rating > 0 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 1 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 2  ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 3 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 4 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                        <span class="lp-rating-num rating-with-colors '. review_rating_color_class($rating) .'">'. $rating;
                    if( $rating == '' ){ $output   .=  0; }
                    if( $rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5 ){ $output .= '.0';}
                    $output.= '</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="lp-activity-bottom">
                            <h3><a href="'. get_permalink( $LID ) .'">'. $lp_liting_title .'</a></h3>
                            <strong>'. substr( get_the_title(), 0, 35 ) .'</strong>
                        </div>

                        <div class="lp-activity-description">
                            <p>'. substr( $post->post_content, 0, 80 ) .' <a href="'. get_permalink( $LID ) .'">'.esc_html__('Continue Reading', 'listingpro-plugin' ).'</a></p>
                            <div class="activity-reactions small-btns">
                                <a href="#" data-restype="'.$interVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($interests).'" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>'.esc_html__('Interesting','listingpro-plugin').' <span class="react-count">'. $interests .'</span></a>
                                <a href="#" data-restype="'.$lolVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($Lols).'" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('LOL','listingpro-plugin').' <span class="react-count">'.$Lols.'</span></a>
                                <a href="#" data-restype="'.$loveVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($loves).'" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('Love','listingpro-plugin').' <span class="react-count">'.$loves.'</span></a>
                            </div>
                        </div>
                    </div>
                </div>';
                    if( $counter == $number_posts || $counter == $activities->found_posts )
                    {
                        $output .=  '    </div>';
                        $output .=  '</div>';
                    }
                }
                if( $number_posts == 4 )
                {
                    $output .=  '
                <div class="col-md-6">
                    <div class="lp-activity style2">
                        <div class="lp-activity-top">
                            <div class="row">
                                <div class="lp-activity-thumb col-md-6">
                                    <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" class="lp-activity-author-thumb"><img src="'. esc_attr($avatar) .'"alt="image"></a>
                                    <a href="' . get_permalink( $LID ) . '"><img class="hidden-xs hidden-sm" src="'. $img_url4 .'" alt="'. get_the_title() .'"><img class="hidden-md hidden-lg" src="'. $img_url3 .'" alt="'. get_the_title() .'"></a>
                                </div>
                                <div class="lp-activity-top-right col-md-6">
                                    <div class="lp-activity-review-writer">
                                        <a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">'. get_the_author() .'</a>
                                <p>'.esc_html__('Wrote a review', 'listingpro-plugin' ).'</p>
                                    </div>
                                    <div class="lp-listing-stars clearfix">
                           <div class="lp-rating-stars-outer">
                                        <span class="lp-star-box ';
                    if( $rating > 0 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 1 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 2  ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 3 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        <span class="lp-star-box ';
                    if( $rating > 4 ){ $output .= 'filled'.' '.$rating_num_clr; }
                    $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
</div>
                                        <span class="lp-rating-num rating-with-colors '. review_rating_color_class($rating) .'">'. $rating;
                    if( $rating == '' ){ $output   .=  0; }
                    if( $rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5 ){ $output .= '.0';}
                    $output.= '</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="lp-activity-bottom">
                            <h3><a href="'. get_permalink( $LID ) .'">'. $lp_liting_title .'</a></h3>
                            <strong>'. substr( get_the_title(), 0, 35 ) .'</strong>
                        </div>
                        <div class="lp-activity-description">
                            <p>'. substr( $post->post_content, 0, 120 ) .' <a href="'. get_permalink( $LID ) .'">'.esc_html__('Continue Reading', 'listingpro-plugin' ).'</a></p>
                            <div class="activity-reactions small-btns">
                                <a href="#" data-restype="'.$interVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($interests).'" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span>'.esc_html__('Interesting','listingpro-plugin').' <span class="react-count">'. $interests .'</span></a>
                                <a href="#" data-restype="'.$lolVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($Lols).'" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('LOL','listingpro-plugin').' <span class="react-count">'.$Lols.'</span></a>
                                <a href="#" data-restype="'.$loveVal.'" data-reacted ="'.$reacted_msg.'" data-id="'.get_the_ID().'" data-score="'.esc_attr($loves).'" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span>'.esc_html__('Love','listingpro-plugin').' <span class="react-count">'.$loves.'</span></a>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                $counter++;
            endwhile; wp_reset_postdata();
            $output .=  '   </div></div>';
            $output .=  '</div>';
			      } elseif ($activity_style == 'style2') {
			  wp_enqueue_style('lp-classic-reviews');
			  
                $output .=  '<div class="lp-activities-classicx-style"><div class="lp-section-content-container"> ';
                $output .=  '    <div class="row lp-activities-classic-slider">';
                while ($activities->have_posts()) : $activities->the_post();
                    $img_url    = '';
                    $img_url2   = '';
                    $img_url3   = '';
                    $img_url4   = '';
                    global $post;
                    $r_meta     =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                    $LID        =   $r_meta['listing_id'];
                    $rating     =   $r_meta['rating'];

                    $adStatus = apply_filters('lp_get_ad_status', '', $LID);
                    $CHeckAd = '';
                    $adClass = '';
                    if ($adStatus == 'active') {
                        $CHeckAd = '<span>' . esc_html__('Ad', 'listingpro-plugin') . '</span>';
                        $adClass = 'promoted';
                    }
                    $author_avatar_url = get_user_meta($post->post_author, "listingpro_author_img_url", true);
                    $avatar;
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($post->post_author, $size = '55');
                        $avatar =  $avatar_url;
                    }
                    $interests = '';
                    $Lols = '';
                    $loves = '';
                    $interVal = esc_html__('Interesting', 'listingpro-plugin');
                    $lolVal = esc_html__('Lol', 'listingpro-plugin');
                    $loveVal = esc_html__('Love', 'listingpro-plugin');

                    $interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
                    $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
                    $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());


                    if (empty($interests)) {
                        $interests = 0;
                    }
                    if (empty($Lols)) {
                        $Lols = 0;
                    }
                    if (empty($loves)) {
                        $loves = 0;
                    }

                    $reacted_msg    =   esc_html__('You already reacted', 'listingpro-plugin');
                    $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                    if ($use_listing_img == 'off') {
                        $img_url    = aq_resize($placeholder_img, '360', '267', true, true, true);
                        $img_url2   = aq_resize($placeholder_img, '165', '97', true, true, true);
                        $img_url3   = aq_resize($placeholder_img, '500', '300', true, true, true);
                        $img_url4   = aq_resize($placeholder_img, '295', '150', true, true, true);
                    } else {
                        if (has_post_thumbnail($LID)) {
                            $thumbnail_url  =   get_the_post_thumbnail_url($LID);
                            $img_url    = aq_resize($thumbnail_url, '360', '267', true, true, true);
                            $img_url2   = aq_resize($thumbnail_url, '165', '97', true, true, true);
                            $img_url3   = aq_resize($thumbnail_url, '500', '300', true, true, true);
                            $img_url4   = aq_resize($thumbnail_url, '295', '150', true, true, true);
                        } else {
                            $listing_gallery    =   get_post_meta($LID, 'gallery_image_ids', true);
                            $listing_imagearray = explode(',', $listing_gallery);
                            $listing_image      = wp_get_attachment_image_src($listing_imagearray[0], 'full');

                            if (!empty($listing_image[0])) {
                                $img_url    = aq_resize($listing_image[0], '360', '267', true, true, true);
                                $img_url2   = aq_resize($listing_image[0], '165', '97', true, true, true);
                                $img_url3   = aq_resize($listing_image[0], '500', '300', true, true, true);
                                $img_url4   = aq_resize($listing_image[0], '295', '150', true, true, true);
                            }
                        }
                    }

                    if (!empty($gallery)) {
                        $imagearray = explode(',', $gallery);
                        $image      = wp_get_attachment_image_src($imagearray[0], 'full');
                        $first_img  =   $imagearray[0];
                        if (!empty($image[0])) {
                            $img_url    = aq_resize($image[0], '360', '267', true, true, true);
                            $img_url2   = aq_resize($image[0], '165', '97', true, true, true);
                            $img_url3   = aq_resize($image[0], '500', '300', true, true, true);
                            $img_url4   = aq_resize($image[0], '295', '150', true, true, true);
                        }
                    }
                    if (empty($img_url)) {
                        if (empty($activity_placeholder)) {
                            $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url    =   aq_resize($element_placehilder_url, '360', '267', true, true, true);;
                        }
                    }
                    if (empty($img_url2)) {
                        if (empty($activity_placeholder)) {
                            $img_url2    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url2   = aq_resize($element_placehilder_url, '165', '97', true, true, true);
                        }
                    }
                    if (empty($img_url3)) {
                        if (empty($activity_placeholder)) {
                            $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url3 = aq_resize($element_placehilder_url, '500', '300', true, true, true);
                        }
                    }
                    if (empty($img_url4)) {
                        if (empty($activity_placeholder)) {
                            $img_url4    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url4 = aq_resize($element_placehilder_url, '295', '150', true, true, true);
                        }
                    }
                    $lp_liting_title    =   get_the_title($LID);
                    if (strlen($lp_liting_title) > 35) {
                        $lp_liting_title    =   substr($lp_liting_title, 0, 35) . '...';
                    }

                    $rating_num_bg  =   '';
                    $rating_num_clr  =   '';

                    if ($rating < 2) {
                        $rating_num_bg  =   'num-level1';
                        $rating_num_clr  =   'level1';
                    }
                    if ($rating < 3) {
                        $rating_num_bg  =   'num-level2';
                        $rating_num_clr  =   'level2';
                    }
                    if ($rating < 4) {
                        $rating_num_bg  =   'num-level3';
                        $rating_num_clr  =   'level3';
                    }
                    if ($rating >= 4) {
                        $rating_num_bg  =   'num-level4';

                        $rating_num_clr  =   'level4';
                    }
 					$get_userLocation =  get_user_meta(get_the_author_meta('ID') , 'country', true);
                    $output .= '<div class="col-md-4"> 
                                 <div class="classic-reivew-grid-outer"> 
                                    <div class="classic-reivew-grid-thumbnail"> 
                                        <a href="' . get_permalink($LID) . '" class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="' . $img_url3 . '" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="' . $img_url . '" alt="' . get_the_title() . '"></a>
                                        <div class="classic-reivew-grid-listing-title">    <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3></div>
                                    </div>
                                    <div class="classic-reivew-grid-content"> 
                                        <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                                        <div class="lp-activity-review-writer">
                                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>';
                                             if(!empty($get_userLocation)){
			   $output .= '<p class="lp-classic-boxed-reviews-wrote">' . $get_userLocation . '</p>';	  
			  }
                                        $output .= '  </div>
                                        <div class="lp-activity-review-writer-description">';
			$truncated_content = substr($post->post_content, 0, 115);
			
			 $output .= '<p>' . $truncated_content;
			   if (strlen($post->post_content) > 115) {
					 $output .= '...';
				}	 
		 $output .= ' </p>

                                            <div class="lp-listing-stars clearfix">
                                                <div class="lp-rating-stars-outer">
                                                    <span class="lp-star-box ';
                                                        if ($rating > 0) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 1) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 2) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 3) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 4) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                                <p class="reviews-percentage">';
                                                    $output .= $rating;
                                                    if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                                                        $output .= '.0';
                                                    }
                                                    if ($rating == '') {
                                                        $output   .=  '0';
                                                    }
                                                    $output .= ' <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '"></span>';
                                                    $output .= '
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';

                  
                endwhile;
                wp_reset_postdata();
                $output .=  '   </div></div>';
                $output .=  '</div>';
            }elseif ($activity_style == 'style3') {
			   wp_enqueue_style('lp-classic-reviews');
                $output .=  '<div class="lp-activities-classicx-style-boxed"><div class="lp-section-content-container-boxed"> ';
                $output .=  '    <div class="row lp-activities-classic-slider">';
                while ($activities->have_posts()) : $activities->the_post();
                    $img_url    = '';
                    $img_url2   = '';
                    $img_url3   = '';
                    $img_url4   = '';
                    global $post;
                    $r_meta     =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                    $LID        =   $r_meta['listing_id'];
                    $rating     =   $r_meta['rating'];

                    $adStatus = apply_filters('lp_get_ad_status', '', $LID);
                    $CHeckAd = '';
                    $adClass = '';
                    if ($adStatus == 'active') {
                        $CHeckAd = '<span>' . esc_html__('Ad', 'listingpro-plugin') . '</span>';
                        $adClass = 'promoted';
                    }
                    $author_avatar_url = get_user_meta($post->post_author, "listingpro_author_img_url", true);
                    $avatar;
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($post->post_author, $size = '55');
                        $avatar =  $avatar_url;
                    }
                  
                    $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                    if ($use_listing_img == 'off') {
                        $img_url    = $placeholder_img;
                        $img_url2   = $placeholder_img;
                        $img_url3   = $placeholder_img;
                        $img_url4   = $placeholder_img;
                    } else {
                        if (has_post_thumbnail($LID)) {
                            $thumbnail_url  =   get_the_post_thumbnail_url($LID);
                            $img_url    = $thumbnail_url;
                            $img_url2   =$thumbnail_url;
                            $img_url3   =$thumbnail_url;
                            $img_url4   = $thumbnail_url;
                        } else {
                            $listing_gallery    =   get_post_meta($LID, 'gallery_image_ids', true);
                            $listing_imagearray = explode(',', $listing_gallery);
                            $listing_image      = wp_get_attachment_image_src($listing_imagearray[0], 'full');

                            if (!empty($listing_image[0])) {
                                $img_url    = $listing_image[0];
                                $img_url2   = $listing_image[0];
                                $img_url3   = $listing_image[0];
                                $img_url4   = $listing_image[0];
                            }
                        }
                    }
                    if (empty($img_url)) {
                        if (empty($activity_placeholder)) {
                            $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url    =   $element_placehilder_url;
                        }
                    }
                    if (!empty($gallery)) {
                        $imagearray = explode(',', $gallery);
                        $image      = wp_get_attachment_image_src($imagearray[0], 'full');
                        if (!empty($image[0])) {
                            $img_url3   = $image[0];
                        }
                    }
                    if (empty($img_url3)) {
                        if (empty($activity_placeholder)) {
                            $img_url3    =     $listingpro_options['lp_def_featured_image']['url'];
                        } else {
                            $element_placehilder_url =  wp_get_attachment_url($activity_placeholder);
                            $img_url3 = $element_placehilder_url;
                        }
                    }
                    $lp_liting_title    =   get_the_title($LID);
                    if (strlen($lp_liting_title) > 35) {
                        $lp_liting_title    =   substr($lp_liting_title, 0, 35) . '...';
                    }

                    $rating_num_bg  =   '';
                    $rating_num_clr  =   '';

                    if ($rating < 2) {
                        $rating_num_bg  =   'num-level1';
                        $rating_num_clr  =   'level1';
                    }
                    if ($rating < 3) {
                        $rating_num_bg  =   'num-level2';
                        $rating_num_clr  =   'level2';
                    }
                    if ($rating < 4) {
                        $rating_num_bg  =   'num-level3';
                        $rating_num_clr  =   'level3';
                    }
                    if ($rating >= 4) {
                        $rating_num_bg  =   'num-level4';

                        $rating_num_clr  =   'level4';
                    }
				$lp_liting_review_read = '';
				$lp_liting_review_read_class = '';
			  
			  $get_userLocation =  get_user_meta(get_the_author_meta('ID') , 'country', true);
			
					if (strlen($post->post_content) > 300) {
                        $lp_liting_review_read    =   '... <a href="' . get_permalink($LID) . '">' . esc_html__('Read More', 'listingpro-plugin') . '</a>';
                        $lp_liting_review_read_class    =   'more';
                    }
                    $output .= '<div class="col-md-4"> 
                                 <div class="classic-reivew-grid-outer-boxed"> 
                                 <div  class="lp-activity-thumb"><img class="hidden-md hidden-lg" src="'.$img_url3.'" alt="' . get_the_title() . '"><img class="hidden-sm hidden-xs" src="'.$img_url.'" alt="' . get_the_title() . '"></div>
                                    <div class="classic-reivew-grid-content"> 
                                        <div class="lp-activity-review-writer-description">
                                        <i class="fa-solid fa-quote-left"></i>
                                        <div class="lp-activity-review-writer-content '.$lp_liting_review_read_class.'">
                                            <p class="lp-classic-boxed-reviews-content">' . substr($post->post_content, '0', '300') .' '.$lp_liting_review_read.'</p>
                                        </div>
                                        <div class="lp-activity-review-writer">
                                            <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '" class="lp-activity-author-thumb"><img src="' . esc_attr($avatar) . '" alt="' . get_the_title() . '"></a>
                                                <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a>';
			  if(!empty($get_userLocation)){
			     $output .= '<p class="lp-classic-boxed-reviews-wrote">' . $get_userLocation . '</p>';	  
			  }
                                                
                                        $output .= '   </div>
                                            <div class="lp-listing-stars clearfix">
                                                <div class="lp-rating-stars-outer">
                                                    <span class="lp-star-box ';
                                                        if ($rating > 0) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 1) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 2) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 3) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i></span>
                                                                                        <span class="lp-star-box ';
                                                        if ($rating > 4) {
                                                            $output .= 'filled' . ' ' . $rating_num_clr;
                                                        }
                                                        $output .=  '"><i class="fa fa-star" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                                <p class="reviews-percentage">';
                                                    $output .= $rating;
                                                    if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
                                                        $output .= '.0';
                                                    }
                                                    if ($rating == '') {
                                                        $output   .=  '0';
                                                    }
                                                    $output .= ' <span class="lp-rating-num rating-with-colors ' . review_rating_color_class($rating) . '"></span>';
                                                    $output .= '
                                                </p>
                                            </div>
                                        </div>
                                        <div class="classic-reivew-grid-listing-title-boxed"> <h3><a href="' . get_permalink($LID) . '">' . $lp_liting_title . '</a></h3></div>
                                    </div>
                                </div>
                            </div>';

                  
                endwhile;
                wp_reset_postdata();
                $output .=  '   </div></div>';
                $output .=  '</div>';
            }
			  
        endif;

        return $output;
    }

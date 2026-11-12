<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_Categories extends Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style( 'slick-styles', THEME_DIR . '/assets/lib/slick/slick.css', [ 'elementor-frontend' ], '1.0.0', true );
    }

    public function get_style_depends() {
        return ['slick-styles'];
    }
    public function get_name() {
        return 'listing-categories';
    }

    public function get_title() {
        return __( 'Listing Categories', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $categories = get_terms('listing-category', array('hide_empty' => false));
        $cats = array();
        $cats_parent = array();

        if(!empty($categories)){
            foreach($categories as $category) {
                $cats[$category->term_id] = $category->name;
            }
        }
        if(!empty($categories_parent)){
            foreach($categories_parent as $category_parent) {
                $cats_parent[$category_parent->name] = $category_parent->term_id;
            }
            $first_child_terms = get_terms('listing-category', array('hide_empty' => false, 'parent' => $categories_parent[0]->term_id));
        }
        $first_child_term_Arr   =   array();
        if(!empty($first_child_terms)){
            foreach ( $first_child_terms as $first_child_term )
            {
                $first_child_term_Arr[$first_child_term->name]  =   $first_child_term->term_id;
            }
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );

        $this->add_control(
            'catstyles',
            [
                'label' => __( 'Category Styles', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cat_abstracted',
                'options' => [
                    'cat_abstracted' => __("Abstracted View", "elementor-listingpro"),
                    "cat_abstracted_2" => __("Abstracted View 2", "elementor-listingpro"),
                    "cat_boxed" => __("Boxed View", "elementor-listingpro"),
                    "cat_boxed_2" => __("Boxed View 2", "elementor-listingpro"),
                    "cat_grid_abstracted" => __("Abstracted & Boxed View", "elementor-listingpro"),
                    "cat_ab_grid_abstracted" => __("Grid View", "elementor-listingpro"),
                    "cat_slider_style" =>__("Slider Style", "elementor-listingpro")
                ],
            ]
        );
        $this->add_control(
            'display_sub_cat_box2',
            [
                'label' => __( 'Select Notice', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cat_abstracted',
                'options' => [
                    'show' => __("Show", "elementor-listingpro"),
                    "hide" => __("Hide", "elementor-listingpro"),
                ],
                'condition' => [
                    'catstyles' => 'cat_boxed_2'
                ]
            ]
        );
        $this->add_control(
            'category_ids',
            [
                'label' => __( 'Select Categories', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'multiple' => true,
                'options' => $cats,
            ]
        );
        $this->add_control(
            'cat3_button_text',
            [
                'label' => __( 'Button Text', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'catstyles' => 'cat_slider_style'
                ]

            ]
        );
        $this->add_control(
            'cat3_button_link',
            [
                'label' => __( 'Button Link', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'catstyles' => 'cat_slider_style'
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

        wp_enqueue_style('Slick-css', THEME_DIR . '/assets/lib/slick/slick.css');
        wp_enqueue_script('Slick', THEME_DIR . '/assets/lib/slick/slick.min.js', 'jquery', '', true);
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('.listing-category-slider4').each(function (key, item) {
                    var sliderExtraClassName = 'slider-no-' + key;
                    jQuery(this).addClass(sliderExtraClassName);
                    jQuery(sliderExtraClassName).slick({
                        centerMode: false,
                        centerPadding: '0px',
                        infinite: true,
                        accesibility: false,
                        draggable: true,
                        swipe: true,
                        touchMove: false,
                        autoplaySpeed: 1400,
                        speed: 100,
                        slidesToShow: 4,
                        dots: false,
                        arrows: true,
                        responsive: [
                            {
                                breakpoint: 768,
                                settings: {
                                    arrows: false,
                                    centerMode: false,
                                    centerPadding: '0px',
                                    slidesToShow: 4
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    arrows: false,
                                    centerMode: false,
                                    centerPadding: '0px',
                                    slidesToShow: 1
                                }
                            }
                        ]
                    });
                });
            });
        </script>
        <?php


        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_cats( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listing_cats')) {
    function listingpro_shortcode_listing_cats($atts, $content = null) {
        extract(shortcode_atts(array(
            'category_ids'   => '',
            'catstyles'    => 'cat_grid_abstracted',
            'cat_abstracted_2_btn_text'    => '',
            'cat_abstracted_2_btn_link'    => '',
            'display_sub_cat_box2'    => 'show',
            'cat3_button_link' => '',
            'cat3_button_text'   => 'Explore More',
            'display_main_cats' => ''

        ), $atts));

        if( is_array( $category_ids ) )
        {
            $category_ids   =   implode( ',', $category_ids );
        }

        $has_child_cats ='';
        require_once (THEME_PATH . "/include/aq_resizer.php");
        $output = null;
        global $listingpro_options;
        $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];

        if($listing_mobile_view == 'app_view' && wp_is_mobile() ){
            $output .= '<div class="lp-section-content-container lp-location-slider clearfix">';

            $listingCategories = $category_ids;
            $ucat = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'orderby' => 'count',
                'order' => 'ASC',
                'include'=> $listingCategories
            );
            $allLocations = get_terms( 'listing-category',$ucat);
            $grid = 0;
            foreach($allLocations as $category) {
                $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                $catImg = '';

                $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                if( !empty($cat_image_id) ){
                    $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location270_400', true );
                    $catImg = $thumbnail_url[0];
                }else{
                    $imgurl = aq_resize( $category_image, '270', '400', true, true, true);
                    if(empty($imgurl) ){
                        $catImg = 'https://placehold.co/372x240';
                    }
                    else{
                        $catImg = $imgurl;
                    }
                }

                $output .= '
			
				<div class="slider-for-category-container">
					<div class="">
						<div class="city-girds2">
							<div class="city-thumb2">
								<img alt="image" src="'. $catImg.'" />
								<div class="category-style3-title-outer">
									<h3 class="lp-h3">
										<a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
									</h3>
								</div>
								<a href="'.esc_url( get_term_link( $category )).'" class="overlay-link"></a>
								<div class="location-overlay"></div>
							</div>
							
							
						</div>
					</div>
				</div>
			';
                $grid++;

            }
            $output .= '</div>';
        }else{
            if($catstyles == 'cat_slider_style') {
                $output .= '<div class="lp-section-content-container clearfix listing-category-slider4">';
            }else{
                $output .= '<div class="lp-section-content-container row">';
            }
            $listingCategories = $category_ids;
            $ucat = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'orderby' => 'count',
                'order' => 'ASC',
                'include'=> $listingCategories
            );
            $allLocations = get_terms( 'listing-category',$ucat);
            if( $catstyles == 'cat_abstracted_2' )
            {
                $cat_abstracted_2_btn   =   '';
                if( !empty( $cat_abstracted_2_btn_text ) && !empty( $cat_abstracted_2_btn_link ) )
                {
                    $cat_abstracted_2_btn   =   '<a class="element-inner-button" href="'. $cat_abstracted_2_btn_link .'">'. $cat_abstracted_2_btn_text .'</a>';
                }
                $output .=  '<div class="lp-categories-abs2">';
                $output .=  '   <div class="row">'.$cat_abstracted_2_btn;
                $cat_counter    =   1;
                $total_cats =   count( $allLocations );

                foreach ( $allLocations as $category )
                {
                    $term_children = get_term_children($category->term_id, 'listing-category');
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $imgurl = aq_resize( $category_image, '570', '455', true, true, true);
                    $catImg =   '';

                    $loc_term_children_array = array();
                    $loc_term_children = get_term_children( $category->term_id, 'location' );
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);
                    $term_link  =   esc_url( get_term_link( $category->term_id , 'listing-category'));

                    $has_child_cats =   '';
                    if( $term_children && !empty( $term_children ) )
                    {
                        $has_child_cats =   'has-child-cats';
                    }
                    if( $cat_counter == 1 )
                    {
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/465x375';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg =   aq_resize( $category_image, '478', '375', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-5 lp-category-abs2 abs2-first">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child )
                                {
                                    $output .=  ', ';
                                }
                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';
                    }
                    if( $cat_counter == 2 )
                    {
                        $output .=  '<div class="col-md-7"><div class="row">';

                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/335x390';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg =   aq_resize( $category_image, '331', '375', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-6 lp-category-abs2 abs2-second">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                if( $term_children_counter == 4 ) break;
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child )
                                {
                                    $output .=  ', ';
                                }
                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';

                    }
                    if ( $cat_counter == 3 ){
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/335x190';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg =   aq_resize( $category_image, '336', '182', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-6 lp-category-abs2 abs2-third">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                if( $term_children_counter == 4 ) break;
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child )
                                {
                                    $output .=  ', ';
                                }
                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';

                    }
                    if( $cat_counter == 4 )
                    {
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/585x375';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg =   aq_resize( $category_image, '336', '182', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-6 lp-category-abs2 abs2-third">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                if( $term_children_counter == 4 ) break;
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child )
                                {
                                    $output .=  ', ';
                                }
                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';

                        $output .=  '</div></div>';
                        $output .=  '<div class="clearfix"></div>';
                    }
                    if( $cat_counter == 5 )
                    {

                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/335x190';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg = aq_resize( $category_image, '575', '375', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-6 lp-category-abs2">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child )
                                {
                                    $output .=  ', ';
                                }
                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';
                    }
                    if( $cat_counter == 6 || $cat_counter == 7 )
                    {

                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/292x375';
                            $catImg2 = 'https://placehold.co/500x300';
                        }
                        else{
                            $catImg = aq_resize( $category_image, '288', '375', true, true, true);
                            $catImg2 =   aq_resize( $category_image, '500', '300', true, true, true);
                        }
                        $output .=  '<div class="col-md-3 lp-category-abs2">';
                        $output .=  '    <div class="lp-category-abs2-inner '. $has_child_cats .'">';
                        $output .=  '       <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'"><img class="hidden-sm hidden-xs" src="'. $catImg .'" alt="'.$category->name.'" /><img class="hidden-md hidden-lg" src="'. $catImg2 .'" alt="'.$category->name.'" />';
                        $output .=  '       <span>'. $category->name .'</span></a>';
                        if( $term_children )
                        {
                            $total_child    =   count( $term_children );
                            $term_children_counter  =   1;
                            $output .=  '<div class="lp-category-abs2-inner-sub-cats">';
                            foreach ( $term_children as $term_child )
                            {
                                if( $term_children_counter == 4 ) break;
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $output .=  '    <a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $term_data->name .'</a>';
                                if( $term_children_counter != $total_child && $term_children != 4 )
                                {
                                    $output .=  ', ';
                                }

                                $term_children_counter++;
                            }
                            $output.=   '</div>';
                        }
                        $output .=  '    </div>';
                        $output .=  '</div>';
                    }
                    $cat_counter ++;
                    if ($cat_counter == 8) {
                        $cat_counter = 1;
                    }
                }
                $output .=  '    </div>';
                $output .=  '</div>';

            }
            else if ( $catstyles == 'cat_boxed_2' )
            {
                $listingCategories = $category_ids;
                $ucat = array(
                    'post_type' => 'listing',
                    'hide_empty' => false,
                    'orderby' => 'count',
                    'order' => 'ASC',
                    'parent' => 0,
                    'include'=> $listingCategories
                );

                $allLocations = get_terms( 'listing-category',$ucat);
                $output .=  '<div class="lp-category-boxed2">';
                if( $display_sub_cat_box2 == 'show' )
                {
                    foreach ( $allLocations as $category )
                    {
                        $term_children = get_term_children($category->term_id, 'listing-category');

                        $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                        if( $term_children )
                        {
                            $output .=  '<div class="col-md-3">';
                            $output .=  '   <div class="lp-category-boxed2-inner '. $has_child_cats .'">';
                            $output .=  '       <div class="lp-category-boxed2-inner-top">';
							if( hasFontAwesomeIconClass($category_icon) ){
								$output .=  '            <i class="icon icon-category-element '.$category_icon.'"></i><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
							}else{
								$output .=  '            <img alt="image" src="'. $category_icon .'"><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
							}
                            $output .=  '        </div>';
                            $output .=  '       <div class="lp-category-boxed2-inner-bottom">';
                            $output.=   '           <ul>';
                            foreach ( $term_children as $term_child )
                            {
                                $term_data  =   get_term_by( 'id', $term_child, 'listing-category' );
                                $category_icon = listing_get_tax_meta($term_child,'category','image');
								if( hasFontAwesomeIconClass($category_icon) ){
									$output .=  '<li><i class="icon icon-category-element '.$category_icon.'"></i><a href="'. esc_url( get_term_link( $term_data , 'listing-category')) .'">'. $term_data->name .'</a> </li>';
								}else{
									$output .=  '<li><img alt="image" src="' . $category_icon . '"><a href="'. esc_url( get_term_link( $term_data , 'listing-category')) .'">'. $term_data->name .'</a> </li>';
								}
                                
                            }
                            $output .=  '           </ul>';
                            $output .=  '       </div>';
                            $output .=  '   </div>';
                            $output .=  '</div>';
                        }
                    }
                }

                $output .=  '<div class="clearfix"></div>';
                foreach ( $allLocations as $category )
                {
                    $term_children = get_term_children($category->term_id, 'listing-category');

                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    if( $display_sub_cat_box2 == 'show' )
                    {
                        if( !$term_children )
                        {
                            $output .=  '<div class="col-md-3">';
                            $output .=  '   <div class="lp-category-boxed2-inner '. $has_child_cats .'">';
                            $output .=  '       <div class="lp-category-boxed2-inner-top">';
							if( hasFontAwesomeIconClass($category_icon) ){
								$output .=  '            <i class="icon icon-category-element '.$category_icon.'"></i><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
							}else{
								$output .=  '            <img alt="image" src="'. $category_icon .'"><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
							}
                            $output .=  '        </div>';
                            $output .=  '   </div>';
                            $output .=  '</div>';
                        }
                    }
                    else
                    {
                        $output .=  '<div class="col-md-3">';
                        $output .=  '   <div class="lp-category-boxed2-inner '. $has_child_cats .'">';
                        $output .=  '       <div class="lp-category-boxed2-inner-top">';
						if( hasFontAwesomeIconClass($category_icon) ){
							$output .=  '            <i class="icon icon-category-element '.$category_icon.'"></i><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
						}else{
							$output .=  '            <img alt="image" src="'. $category_icon .'"><h5><a href="'. esc_url( get_term_link( $category->term_id , 'listing-category')) .'">'. $category->name .'</a></h5>';
						}
                        $output .=  '        </div>';
                        $output .=  '   </div>';
                        $output .=  '</div>';
                    }

                }
                $output .=  '</div>';

            }
            else if($catstyles == 'cat_abstracted')
            {
                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';

                    $loc_term_children_array = array();
                    $loc_term_children = get_term_children( $category->term_id, 'location' );
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);

                    if($grid == 0){
                        $gridStyle = 'col-md-6 col-sm-6  col-xs-12';

                        $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                        if( !empty($cat_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location570_455', true );
                            $catImg = $thumbnail_url[0];
                        }else{
                            $imgurl = aq_resize( $category_image, '570', '455', true, true, true);
                            if(empty($imgurl) ){
                                $catImg = 'https://placehold.co/570x455';
                            }
                            else{
                                $catImg = $imgurl;
                            }
                        }


                    }elseif($grid == 1){
                        $gridStyle = 'col-md-6 col-sm-6  col-xs-12';

                        $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                        if( !empty($cat_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location570_228', true );
                            $catImg = $thumbnail_url[0];
                        }else{
                            $imgurl = aq_resize( $category_image, '570', '228', true, true, true);
                            if(empty($imgurl) ){
                                $catImg = 'https://placehold.co/570x228';
                            }
                            else{
                                $catImg = $imgurl;
                            }
                        }

                    }else{
                        $gridStyle = 'col-md-3 col-sm-3 col-xs-12';

                        $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                        if( !empty($cat_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location270_197', true );
                            $catImg = $thumbnail_url[0];
                        }else{
                            $imgurl = aq_resize( $category_image, '270', '197', true, true, true);
                            if(empty($imgurl) ){
                                $catImg = 'https://placehold.co/270x197';
                            }
                            else{
                                $catImg = $imgurl;
                            }
                        }

                    }

                    $output .= '
			<div class="'.$gridStyle.'">
				<div class="city-girds lp-border-radius-8">
					<div class="city-thumb">
						<img alt="image" src="'. $catImg.'" />
						
					</div>
					<div class="city-title text-center">
						<h3 class="lp-h3">
							<a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
						</h3>
						<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
					</div>
					<a href="'.esc_url( get_term_link( $category )).'" class="overlay-link"></a>
				</div>
			</div>';
                    $grid++;
                }

            }elseif($catstyles=="cat_boxed") {

                foreach($allLocations as $cats) {
                    $category_icon = listing_get_tax_meta($cats->term_id,'category','image');
                    $category_image = listing_get_tax_meta($cats->term_id,'category','banner');

                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $cats->term_id);

                    $catImg = '';
                    $cat_image_id = listing_get_tax_meta($cats->term_id,'category','banner_id');
                    if( !empty($cat_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location270_197', true );
                        $catImg = $thumbnail_url[0];
                    }else{
                        $imgurl = aq_resize( $category_image, '270', '197', true, true, true);
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/270x197';
                        }
                        else{
                            $catImg = $imgurl;
                        }
                    }

                    $output .= '
			<div class="col-md-3 col-sm-3 col-xs-12">
				<div class="city-girds lp-border-radius-8">
					<div class="city-thumb">
						<img alt="image" src="'. $catImg.'" />
						
					</div>
					<div class="city-title text-center">
						<h3 class="lp-h3">
							<a href="'.esc_url( get_term_link( $cats->term_id , 'listing-category')).'">'.esc_attr($cats->name).'</a>
						</h3>
						<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>
					</div>
					<a href="'.esc_url( get_term_link( $cats )).'" class="overlay-link"></a>
				</div>
			</div>';
                }
            }elseif($catstyles == 'cat_grid_abstracted'){

                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);

                    if($grid == 0){
                        $gridStyle = 'col-md-6 col-sm-6  col-xs-12';

                        $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                        if( !empty($cat_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_location570_455', true );
                            $catImg = $thumbnail_url[0];
                        }else{
                            $imgurl = aq_resize( $category_image, '570', '455', true, true, true);
                            if(empty($imgurl) ){
                                $catImg = 'https://placehold.co/570x455';
                            }
                            else{
                                $catImg = $imgurl;
                            }
                        }


                    }else{
                        $gridStyle = 'col-md-3 col-sm-3 col-xs-12';

                        $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                        if( !empty($cat_image_id) ){
                            $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_cats270_213', true );
                            $catImg = $thumbnail_url[0];
                        }else{
                            $imgurl = aq_resize( $category_image, '270', '213', true, true, true);
                            if(empty($imgurl) ){
                                $catImg = 'https://placehold.co/270x213';
                            }
                            else{
                                $catImg = $imgurl;
                            }
                        }

                    }

                    $output .= '
			<div class="'.$gridStyle.'">
				<div class="city-girds lp-border-radius-8 city-girds4">
					<div class="city-thumb">
						<img alt="image" src="'. $catImg.'" />
						
					</div>
					<div class="city-title text-center category-style3-title-outer">
						<h3 class="lp-h3">
							<a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
						</h3>
						<label class="lp-listing-quantity">'.esc_attr($totalListinginLoc).' '.esc_html__('Listings', 'listingpro-plugin').'</label>'
                    ;

                    $sub = get_term_children( $category->term_id, 'listing-category' );
                    if(!empty($sub)){
                        $output .= '<ul class="clearfix text-center sub-category-outer lp-listing-quantity">';
                        $counter = 1;
                        foreach ( $sub as $subID ) {
                            if($counter == 1){

                                $categoryTerm = get_term_by( 'id', $subID, 'listing-category' );

                                $output .= '<li><p><a href="'.esc_url( get_term_link( $categoryTerm->term_id , 'listing-category')).'">'.$categoryTerm->name.'</a></p></li>';
                            }
                            $counter ++;
                        }
                        $output .= '</ul>';


                    }

                    $output .='	
					</div>
					<a href="'.esc_url( get_term_link( $category )).'" class="overlay-link"></a>
				</div>
			</div>';
                    $grid++;
                }


            }elseif($catstyles == 'cat_ab_grid_abstracted'){

                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);

                    $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                    if( !empty($cat_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro-blog-grid', true );
                        $catImg = $thumbnail_url[0];
                    }else{
                        $imgurl = aq_resize( $category_image, '372', '240', true, true, true);
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/372x240';
                        }
                        else{
                            $catImg = $imgurl;
                        }
                    }




                    $output .= '
			
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="margin-bottom-30">
						<div class="city-girds2 lp-border-radius-8">
							<div class="city-thumb2">
								<img alt="image" src="'. $catImg.'" />
								
								<div class="category-style3-title-outer">
									<h3 class="lp-h3">
										<a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
									</h3>
								</div>
								<a href="'.esc_url( get_term_link( $category )).'" class="overlay-link"></a>
							</div>
							
						
						</div>
					</div>
				</div>
			';
                    $grid++;

                }

            }else{
                $grid = 0;
                foreach($allLocations as $category) {
                    $category_icon = listing_get_tax_meta($category->term_id,'category','image');
                    $category_image = listing_get_tax_meta($category->term_id,'category','banner');
                    $catImg = '';
                    $totalListinginLoc = lp_count_postcount_taxonomy_term_byID('listing','listing-category', $category->term_id);

                    $cat_image_id = listing_get_tax_meta($category->term_id,'category','banner_id');
                    if( !empty($cat_image_id) ){
                        $thumbnail_url = wp_get_attachment_image_src($cat_image_id, 'listingpro_cats270_150', true );
                        $catImg = $thumbnail_url[0];
                    }else{
                        $imgurl = aq_resize( $category_image, '272', '150', true, true, true);
                        if(empty($imgurl) ){
                            $catImg = 'https://placehold.co/272x150';
                        }
                        else{
                            $catImg = $imgurl;
                        }
                    }




                    $output .= '
			
				<div class="">
					<div class="lp-cat-style4">
						<div class="city-girds2">
							<div class="city-thumb2">
								<div class="lp-cat-image-outer"><img alt="image" src="'. $catImg.'" /></div>
								
								<div class="category-style3-title-outer">
									<h3 class="lp-h3">
										<a href="'.esc_url( get_term_link( $category->term_id , 'listing-category')).'">'.esc_attr($category->name).'</a>
									</h3>
								</div>
								<a href="'.esc_url( get_term_link( $category )).'" class="overlay-link"></a>
							</div>
							
						
						</div>
					</div>
				</div>
			';
                    $grid++;

                }
            }

            if($catstyles == 'cat_slider_style') {
                $output .= '</div>';
            }else{
                $output .= '</div>';
            }

        }
        if($catstyles == 'cat_slider_style' && !empty($cat3_button_link) && !empty( $cat3_button_text ) ) {
            $output .= '<div class="lp-explore-more-text text-center">
            <a href="'.$cat3_button_link.'" class="lp-quote-submit-btn">'.$cat3_button_text.'</a>
        </div>';
        }

        return $output;
    }
}
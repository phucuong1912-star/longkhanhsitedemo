<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Blog_Grids extends Widget_Base {

    public function get_name() {
        return 'blog-grids';
    }

    public function get_title() {
        return __( 'Blog Grids', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {


        $categories = get_terms('category', array('hide_empty' => true));
        $category_arr = array();
        $category_arr[] =   esc_html__('All Categories');
        foreach($categories as $category) {
            $category_arr[$category->term_id] = $category->name.'('.$category->count.' Posts)';
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'blog_style',
            [
                'label' => __( 'Select Blog Element Style', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'style1',
                'options' => [
                    'style1' => __("Blog Grid", "elementor-listingpro"),
                    "style2" => __("Blog Masonery", "elementor-listingpro"),
                    "style3" => __("Blog Circle", "elementor-listingpro"),
					"style4" => __("Classic New", "elementor-listingpro"),
                ],
            ]
        );
        $this->add_control(
            'category',
            [
                'label' => __( 'Select Blog Category', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => '0',
                'options' => $category_arr,
            ]
        );
        $this->add_control(
            'blog_per_page',
            [
                'label' => __( 'No. of posts', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '3',
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
        echo listingpro_shortcode_blog_grids( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}

if(!function_exists('listingpro_shortcode_blog_grids')) {
    function listingpro_shortcode_blog_grids($atts, $content = null) {
        extract(shortcode_atts(array(

            'blog_style'   => 'style1',
            'category'   => '',
            'blog_per_page' => '3',
        ), $atts));

        $output = null;
        $post_count =1;
        if($blog_style == 'style1'){
            $output .= '<div class="lp-section-content-container lp-blog-grid-container row">';

            $type = 'post';
            $args=array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );

            $my_query = null;
            $my_query = new \WP_Query($args);
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'listingpro-blog-grid');
                    if($imgurl){
                        $thumbnail = $imgurl[0];
                    }else{
                        $thumbnail = 'https://placehold.co/372x240';
                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ', ';
                    $catoutput = '';
                    if ( ! empty( $categories ) ) {
                        foreach( $categories as $category ) {
                            $catoutput .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" >' . esc_html( $category->name ) . '</a>' . $separator;
                        }
                    }

                    $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                    $avatar ='';
                    if(!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '51' );
                        $avatar =  $avatar_url;
                    }
                    $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container lp-border lp-border-radius-8">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="'.get_the_permalink().'">
                                                <img src="'.$thumbnail.'" alt="blog-grid-1-410x308" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description text-center">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <img class="avatar" src="'.esc_url($avatar).'" alt="image">
                                                </div>
                                                <div class="lp-blog-grid-category">
                                                    '.trim( $catoutput, $separator ).'
                                                </div>
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="'.get_the_permalink().'">'.get_the_title().'</a>
                                                    </h4>
                                                </div>
                                                <ul class="lp-blog-grid-author">
                                                    <li>
                                                        <a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">
                                                            <i class="fa fa-user"></i>
                                                            <span>'.get_the_author().'</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-calendar"></i>
                                                        <span>'.get_the_date(get_option('date_format')).'</span>
                                                    </li>
                                                </ul><!-- ../lp-blog-grid-author -->
                                        </div>
                                    </div>
                                </div>';

                    if($post_count==3){
                        $output .='<div class="clearfix"></div>';
                        $post_count=1;
                    }
                    else{
                        $post_count++;
                    }
                endwhile;
            }



            $output .= '</div>';
        }elseif($blog_style == 'style2'){

            $output .= '<div class="lp-section-content-container lp-blog-grid-container row lp-blog-grid-container-style2">';

            $type = 'post';
            $args=array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );
            $gridNumber = 1;
            $gridNumber2 = 1;
            $my_query = null;
            $my_query = new \WP_Query($args);
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    if($gridNumber == 1){
                        $imgurl = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'listingpro-blog-grid3');
                        if($imgurl){
                            $thumbnail = $imgurl[0];
                        }else{
                            $thumbnail = 'https://placehold.co/672x430';
                        }
                    }else{
                        $imgurl = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'listingpro-blog-grid');
                        if($imgurl){
                            $thumbnail = $imgurl[0];
                        }else{
                            $thumbnail = 'https://placehold.co/372x240';
                        }



                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ' ';
                    $catoutput = '';
                    if ( ! empty( $categories ) ) {
                        foreach( $categories as $category ) {
                            $catoutput .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" >' . esc_html( $category->name ) . '</a>' . $separator;
                        }
                    }

                    $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                    $avatar ='';
                    if(!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '51' );
                        $avatar =  $avatar_url;
                    }

                    if($gridNumber == 1){
                        $output .= '<div class="lp-blog-grid-box col-md-12">
                                    <div class="lp-blog-grid-box-container lp-blog-grid-box-container-first-post lp-border-radius-8 ">
                                            <div class="col-md-5 lp-blog-style2-outer padding-right-0 lp-border">
                                                <div class="lp-blog-style2-inner">
                                                    <div class="lp-blog-user-thumb2">
                                                        <a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'"><img class="avatar" src="'.esc_url($avatar).'" alt="image"></a>
                                                    </div>
                                                    
                                                    <div class="lp-blog-grid-title">
                                                        <h4 class="lp-h4">
                                                            <a href="'.get_the_permalink().'">'.mb_substr(get_the_title(), 0, 29).'</a>
                                                        </h4>
                                                        <p>'.mb_substr(strip_tags(get_the_content()),0,150).'..</p>
                                                    </div>
                                                    <ul class="lp-blog-grid-author">
                                                        <li>
                                                            
                                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                                <span>'.trim( $catoutput, $separator ).'</span>
                                                            
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-calendar"></i>
                                                            <span>'.get_the_date(get_option('date_format')).'</span>
                                                        </li>
                                                    </ul><!-- ../lp-blog-grid-author -->
                                                    <div class="blog-read-more">
                                                        <a href="'.get_the_permalink().'" class="watch-video">'.esc_html__('Read More', 'listingpro-plugin').'</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lp-blog-grid-box-thumb col-md-7 padding-right-0 padding-left-0">
                                                <a href="'.get_the_permalink().'">
                                                    <img src="'.$thumbnail.'" alt="blog-grid-1-410x308" />
                                                </a>
                                            </div>
                                        
                                        
                                    </div>
                                </div>';
                        if($gridNumber == 1){
                            $output .= '<div class="clearfix"></div>';
                        }
                    }else{

                        $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container lp-blog-grid-box-container-style2 lp-border-radius-8">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="'.get_the_permalink().'">
                                                <img src="'.$thumbnail.'" alt="blog-grid-1-410x308" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description  lp-border lp-blog-grid-box-description2">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'"><img class="avatar" src="'.esc_url($avatar).'" alt="image"></a>
                                                </div>
                                                
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="'.get_the_permalink().'">'.mb_substr(get_the_title(), 0, 29).'</a>
                                                    </h4>
                                                    <p>'.mb_substr(strip_tags(get_the_content()),0,80).'..</p>
                                                </div>
                                                <ul class="lp-blog-grid-author lp-blog-grid-author2">
                                                    <li>
                                                        <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                        <span>'.trim( $catoutput, $separator ).'</span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-calendar"></i>
                                                        <span>'.get_the_date(get_option('date_format')).'</span>
                                                    </li>
                                                </ul><!-- ../lp-blog-grid-author -->
                                                <div class="blog-read-more">
                                                        <a href="'.get_the_permalink().'" class="watch-video">'.esc_html__('Read More', 'listingpro-plugin').'</a>
                                                </div>
                                        </div>
                                    </div>
                                </div>';
                        if($gridNumber2==3){
                            $output .='<div class="clearfix"></div>';

                        }
                        $gridNumber2++;
                    }


                    $gridNumber++;
                endwhile;
            }



            $output .= '</div>';

        }elseif($blog_style == 'style4'){
            ?>
            <?php
			wp_enqueue_style('lp-classic-blog-grid');
            $output .= '<div class="lp-section-content-container lp-blog-grid-container row">';


            $type = 'post';
            $args=array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );


            $my_query = null;
            $my_query = new \WP_Query($args);
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), '100');
                    if($imgurl){
                        $thumbnail = $imgurl[0];
                    }else{
                        $thumbnail = 'https://placehold.co/372x240';
                    }
                    $categories = get_the_category(get_the_ID());
                    $separator = ', ';
                    $catoutput = '';
                    if ( ! empty( $categories ) ) {
                        foreach( $categories as $key => $category ) {
                        	if( $key == 0 ){
                            $catoutput .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" >' . esc_html( $category->name ) . '</a>' . $separator;
                            
                            }
                        }
                    }


                    $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                    $avatar ='';
                    if(!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '51' );
                        $avatar =  $avatar_url;
                    }
                    $output .= '<div class="col-md-4 col-sm-4 lp-blog-grid-box">
                                    <div class="lp-blog-grid-box-container-classic-style">
                                    <div class="lp-blog-grid-box-thumb-posision">
                                    </div>
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="'.get_the_permalink().'">
                                                <img src="'.$thumbnail.'"  />
                                            </a>
                                        </div>
                                       <div class="lp-blog-grid-box-dcontent">
                                        <div class="lp-blog-grid-box-description text-center">
                                                <div class="lp-blog-user-thumb margin-top-subtract-25">
                                                    <img class="avatar" src="'.esc_url($avatar).'" alt="image">
                                                </div>
                                                <div class="lp-blog-grid-category">
                                                   <a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">
                                                        '.get_the_author().'
                                                        </a>
                                                </div>
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="'.get_the_permalink().'">'.get_the_title().'</a>
                                                    </h4>
                                                </div>
                                        </div>
                                         <div class="lp-blog-grid-box-description-bottom">
                                                <ul class="lp-blog-grid-author">
                                                    <li>
                                                     ' .trim( $catoutput, $separator ).  '
                                                    </li>
                                                    <li>
                                                    <i class="fa-regular fa-calendar"></i>
                                                        <span>'.get_the_date(get_option('date_format')).'</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>';


                    if($post_count==3){
                        $output .='<div class="clearfix"></div>';
                        $post_count=1;
                    }
                    else{
                        $post_count++;
                    }
                endwhile;
            }






            $output .= '</div>';
        }else{
            $output .= '<div class="lp-section-content-container lp-section-content-container-style3">';

            $type = 'post';
            $args=array(
                'post_type' => $type,
                'post_status' => 'publish',
                'posts_per_page' => "$blog_per_page",
                'cat' => $category,
            );

            $my_query = null;
            $my_query = new \WP_Query($args);
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    $imgurl = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'listingpro-thumb4');
                    if($imgurl){
                        $thumbnail = $imgurl[0];
                    }else{
                        $thumbnail = 'https://placehold.co/270x270';
                    }



                    $output .= '<div class="col-md-3 col-sm-3 lp-blog-style3">
                                    <div class="lp-blog-grid-box-container">
                                        <div class="lp-blog-grid-box-thumb">
                                            <a href="'.get_the_permalink().'">
                                                <img src="'.$thumbnail.'" alt="blog-grid-1-270x270" />
                                            </a>
                                        </div>
                                        <div class="lp-blog-grid-box-description text-center">
                                                
                                                <div class="lp-blog-grid-title">
                                                    <h4 class="lp-h4">
                                                        <a href="'.get_the_permalink().'">'.get_the_title().'</a>
                                                    </h4>
                                                </div>
                                               
                                        </div>
                                    </div>
                                </div>';
                endwhile;
            }



            $output .= '<div class="clearfix"></div></div>';
        }
        return $output;
    }
}
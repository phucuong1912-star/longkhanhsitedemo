<?php
class listingpro_TWRecentPostWidget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'listingpro_TWRecentPostWidget', 'description' => 'listingpro recent posts.');
        parent::__construct(false, 'listingpro recent posts', $widget_ops);
    }
    function widget($args, $instance) {
        global $post;
        extract(array(
            'title' => '',
            'number_posts' => 2,
            'theme' => 'post_nothumbnailed',
            'post_order' => 'latest',
            'post_type' => 'post'
        ));
        extract($args);
        $title = apply_filters('widget_title', strip_tags($instance['title']));
        $post_count = 2;
        if (isset($instance['number_posts']))
            $post_count = strip_tags($instance['number_posts']);
        $q['posts_per_page'] = $post_count;
        $cats = (array) $instance['post_category'];
        $q['paged'] = 1;
        $q['post_type'] = strip_tags($instance['post_type']);
        if (count($cats) > 0) {
            $typ = 'category';
	    if ($instance['post_type'] != 'post')
		$typ = 'catalog';
            $catq = '';
            $sp = '';
            foreach ($cats as $mycat) {
                $catq = $catq . $sp . $mycat;
                $sp = ',';
            }
            $catq = explode(',', $catq);
            $q['tax_query'] = Array(Array(
                    'taxonomy' => $typ,
                    'terms' => $catq,
                    'field' => 'id'
                )
            );
        }
        if ($instance['post_order'] == 'commented')
            $q['orderby'] = 'comment_count';
        query_posts($q);
        if (isset($before_widget))
            echo wp_kses_post($before_widget);
        if ($title != '')
            echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
        echo '<div class="jw-recent-posts-widget">';
        echo '<ul>';
        while (have_posts ()) : the_post();
            echo '<li>';
                $class = "with-thumb";
                if (isset($instance['theme']) && $instance['theme'] == 'post_thumbnailed') {
                    if (has_post_thumbnail($post->ID)) {
						require_once (THEME_PATH . "/include/aq_resizer.php");
                        $lrg_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'listingpro-author-thumb');
                        $feat_img = $lrg_img[0];
                        $feat_img  = aq_resize( $feat_img, '63', '63', true, true, true);
                        $thumb_width  = 120;
                        $thumb_height = 80;
                        echo '
                        <div class="recent-thumb image">
                            <img src="' . $feat_img . '" alt="' . get_the_title() . '"/>
                        </div>';
                    } else {
                        $class = "no-thumb";
                    }                    
                } else {
                    $format = get_post_format() == "" ? "standard" : get_post_format();
                    echo '<div class="recent-thumb"><span class="post-format '.$format.'"></span></div>';
                }
                echo '<div class="jw-recent-content '.$class.'">';
                    echo '<h3><a href="'.get_the_permalink().'">'.substr(strip_tags(get_the_title()),0,40).'</a></h3>';
                    echo '<p>'.substr(strip_tags(get_the_content()),0,45).'.</p>';
                    echo '<div class="meta">';
                            echo '<span class="date">'.get_the_date( ' j F, Y' ).'</span>';
                            ?>
                            <span class="date"><i class="fa fa-comment-o" aria-hidden="true"></i> <?php comments_number(); ?></span>
                            <?php

                    echo '</div>';
                echo '</div>';
            echo '</li>';
        endwhile;
        echo '</ul>';
        echo '</div>';
        if (isset($after_widget))
            echo wp_kses_post($after_widget);
        wp_reset_query();
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        /* Strip tags (if needed) and update the widget settings. */
        $instance['title'] = strip_tags($new_instance['title']);
        if ($new_instance['post_type'] == 'post') {
	    $instance['post_category'] = $_REQUEST['post_category'];
	} else {
	    $tax = get_object_taxonomies($new_instance['post_type']);
	    $instance['post_category'] = $_REQUEST['tax_input'][$tax[0]];
	}
        $instance['number_posts'] = strip_tags($new_instance['number_posts']);
        $instance['post_type'] = strip_tags($new_instance['post_type']);
        $instance['post_order'] = strip_tags($new_instance['post_order']);
        $instance['theme'] = strip_tags($new_instance['theme']);
        return $instance;
    }

    function form($instance) {
        //Output admin widget options form
        extract(shortcode_atts(array(
                    'title' => '',
                    'theme' => 'post_nothumbnailed',
                    'number_posts' => 2,
                    'post_order' => 'latest',
                    'post_type' => 'post'
                        ), $instance));
        $defaultThemes = Array(
            Array("name" => 'Thumbnailed posts', 'user_func' => 'post_thumbnailed'),
            Array("name" => 'Default posts', 'user_func' => 'post_nonthumbnailed')
        );
        $themes = apply_filters('jw_recent_posts_widget_theme_list', $defaultThemes);
        $defaultPostTypes = Array(Array("name" => 'Post', 'post_type' => 'post')); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e("Title:", "listingpro");?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>"  />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('post_order')); ?>"><?php esc_html_e("Post order:", "listingpro");?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('post_order')); ?>" name="<?php echo esc_attr($this->get_field_name('post_order')); ?>">
                <option value="latest" <?php if ($post_order == 'latest') print 'selected="selected"'; ?>><?php esc_html_e("Latest posts", "listingpro");?></option>
                <option value="commented" <?php if ($post_order == 'commented') print 'selected="selected"'; ?>><?php esc_html_e("Most commented posts", "listingpro");?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('theme')); ?>"><?php esc_html_e("Post theme:", "listingpro");?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('theme')); ?>" name="<?php echo esc_attr($this->get_field_name('theme')); ?>">
                <option value="post_thumbnailed" <?php if ($theme == 'post_thumbnailed') print 'selected="selected"'; ?>><?php esc_html_e("Thumbnail", "listingpro");?></option>
                <option value="post_nothumbnailed" <?php if ($theme == 'post_nothumbnailed') print 'selected="selected"'; ?>><?php esc_html_e("No Thumbnail", "listingpro");?></option>
            </select>
        </p><?php 
        $customTypes = apply_filters('jw_recent_posts_widget_type_list', $defaultPostTypes);
        if (count($customTypes) > 0) { ?>
            <div  id="custom-post-display" style="display:none"><p>
                <label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php esc_html_e("Post from:", "listingpro");?></label>
                <select rel="<?php echo esc_attr($this->get_field_id('post_cats')); ?>" onChange="jw_get_post_terms(this);" class="widefat" id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>"><?php
                    foreach ($customTypes as $postType) { ?>
                        <option value="<?php print $postType['post_type'] ?>" <?php echo selected($post_type, $postType['post_type']); ?>><?php print $postType['name'] ?></option><?php
                    } ?>
                </select>
            </p></div><?php
        } ?>
        <p><?php esc_html_e("If you were not selected for cats, it will show all categories.", "listingpro");?></p>
        <div id="<?php echo esc_attr($this->get_field_id('post_cats')); ?>" class="recent-post-plugins-style"><?php
            $post_type='post';
            $tax = get_object_taxonomies($post_type);

            $selctedcat = false;
            if (isset($instance['post_category']) && $instance['post_category'] != ''){
                $selctedcat = $instance['post_category'];
            }
            wp_terms_checklist(0, array('taxonomy' => $tax[0], 'checked_ontop' => false, 'selected_cats' => $selctedcat)); ?>
        </div>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number_posts')); ?>"><?php esc_html_e("Number of posts to show:", "listingpro");?></label>
            <input  id="<?php echo esc_attr($this->get_field_id('number_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_posts')); ?>" value="<?php echo esc_attr($number_posts); ?>" size="3"  />
        </p><?php
    }
}

add_action('widgets_init', 'listingpro_TWRecentPostWidget_cb');
if(!function_exists('listingpro_TWRecentPostWidget_cb')) {
    function listingpro_TWRecentPostWidget_cb()
    {
        register_widget('listingpro_TWRecentPostWidget');
    }
}
add_action('wp_ajax_themewave_recent_post_terms', 'get_post_type_terms');
if(!function_exists('get_post_type_terms')) {
    function get_post_type_terms()
    {
        $cat = 'post';
        if (isset($_REQUEST['post_format']) && $_REQUEST['post_format'] != '')
            $cat = sanitize_text_field($_REQUEST['post_format']);
        $tax = get_object_taxonomies($cat);
        wp_terms_checklist(0, array('taxonomy' => $tax[0], 'checked_ontop' => false, 'selected_cats' => false));
        die;
    }
}
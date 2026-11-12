<?php
/* ============== Blog Comments Layout ============ */

if(!function_exists('listingpro_comments')) {
    function listingpro_comments($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;

        ?>
        <div class="comments-box" <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
            <div class="comments-thumb">
                <?php echo get_avatar($comment, 85); ?>
            </div>
            <div class="comments-content">
                <div class="comments-meta-box">
                    <div class="comments-author text-left">
                        <div class="comments-name">
                            <?php echo get_comment_author_link(); ?>
                        </div>
                        <div class="comments-date">
                            <?php printf(__('%1$s at %2$s', 'listingpro'), get_comment_date(), get_comment_time()) ?>
                            <?php $rating = get_comment_meta(get_comment_ID(), 'rate', true);
                            ?>
                            <?php if (!empty($rating)) { ?>

                                <div class="post-reviews">
                                    <?php for ($i = 1; $i <= $rating; $i++) { ?>
                                        <i class="fa fa-star"></i>
                                    <?php }
                                    $emptyStars = 5 - $rating;
                                    if ($emptyStars != '0') {
                                        for ($i = 1; $i <= $emptyStars; $i++) { ?>
                                            <i class="fa fa-star-o"></i>
                                        <?php }
                                    }
                                    ?>
                                </div>

                            <?php } ?>
                        </div>

                    </div>
                    <div class="comments-replay text-right">
                        <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply', 'listingpro'), 'depth' => $depth, 'max_depth' => $args['max_depth'])), $comment->comment_ID); ?>
                        <?php edit_comment_link(__('Edit', 'listingpro')); ?>
                    </div>
                </div>
                <div class="comments-description">
                    <?php if ($comment->comment_approved == '0') { ?>
                        <em><i class="icon-info-sign"></i> <?php _e('Comment awaiting approval', 'listingpro'); ?></em>
                        <br/>
                    <?php } elseif ($comment->comment_approved == '1') { ?>
                        <?php comment_text(); ?>
                    <?php } ?>
                </div>
            </div>
        </div><!--../comments-box-->
        <?php
    }
}
	
	
	/* ============== Blog Comments Fields ============ */
	
	add_filter('comment_form_default_fields', 'custom_fields');
    if(!function_exists('custom_fields')) {
        function custom_fields($fields)
        {

            if (is_singular('listing')) {

                $commenter = wp_get_current_commenter();
                $req = get_option('require_name_email');
                $aria_req = ($req ? " aria-required='true'" : '');

                $fields['author'] = '
                <div class="col-md-6 padding-left-0">
                    <div class="form-group clearfix">
                        <label for="inputName">' . esc_html__('Name', 'listingpro') . '</label>' .
                    ($req ? '' : '') .
                    '<input class="form-control" id="inputName"  name="author" type="text" value="' . esc_attr($commenter['comment_author']) .
                    '" size="30" tabindex="1"' . $aria_req . ' />
                    </div>
                </div>
                ';

                $fields['email'] = '
                <div class="col-md-6 padding-right-0">
                    <div class="form-group clearfix">
                        <label for="inputEmail">' . esc_html__('Email', 'listingpro') . '</label>' .
                    ($req ? '' : '') .
                    '<input class="form-control" id="inputEmail" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) .
                    '" size="30"  tabindex="2"' . $aria_req . ' />
                    </div>
                </div>
                  ';

                $fields['url'] = '';

                $fields['comment_field'] =
                    '<div class="form-group">
                        <label for="inputComments">' . esc_html__('Review', 'listingpro') . '</label>
                        <textarea name="comment" class="form-control" rows="8" id="inputComments" aria-required="true" required="required"></textarea>
                    </div>';
            } else {
                $commenter = wp_get_current_commenter();
                $req = get_option('require_name_email');
                $aria_req = ($req ? " aria-required='true'" : '');

                $fields['author'] = '<div class="form-group">
                                        <label for="inputName">' . esc_html__('Name', 'listingpro') . '</label>' .
                    ($req ? '' : '') .
                    '<input class="form-control" id="inputName"  name="author" type="text" value="' . esc_attr($commenter['comment_author']) .
                    '" size="30" tabindex="1"' . $aria_req . ' /></div>';

                $fields['email'] = '<div class="form-group">
                                        <label for="inputEmail">' . esc_html__('Email', 'listingpro') . '</label>' .
                    ($req ? '' : '') .
                    '<input class="form-control" id="inputEmail" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) .
                    '" size="30"  tabindex="2"' . $aria_req . ' /></div>';

                $fields['url'] = '<div class="form-group">
                                        <label for="inputWebsite">' . esc_html__('Website', 'listingpro') . '</label>' .
                    ($req ? '' : '') .
                    '<input class="form-control" id="inputWebsite" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) .
                    '" size="30" tabindex="1"' . $aria_req . ' /></div>';

                $fields['comment_field'] =
                    '<div class="form-group">
                                        <label for="inputComments">' . esc_html__('Comment', 'listingpro') . '</label>
                        <textarea name="comment" class="form-control" rows="8" id="inputComments" aria-required="true" required="required"></textarea>
                    </div>';
            }
            return $fields;
        }
    }
    if(!function_exists('listing_comment_field')) {
        function listing_comment_field($comment_field)
        {
            if (is_singular('listing')) {
                $comment_field =
                    '<div class="form-group">
                        <label for="inputComments">' . esc_html__('Review', 'listingpro') . '</label>
                        <textarea name="comment" placeholder="Listing comment" class="form-control" rows="8" id="inputComments" aria-required="true" required="required"></textarea>
                    </div>';

                return $comment_field;
            } else {
                $comment_field =
                    '<div class="form-group">
                    <textarea placeholder="' . esc_html__("Write your comment here...", "listingpro") . '" name="comment" class="form-control" rows="8" id="inputComments" aria-required="true" required="required"></textarea>
                </div>';

                return $comment_field;
            }
        }
    }
	
	if(is_user_logged_in()){ 
		
			add_filter('comment_form_field_comment', 'listing_comment_field');
	
	}
    if(!function_exists('remove_textarea')) {
        function remove_textarea($defaults)
        {
            $defaults['comment_field'] = '';
            return $defaults;
        }
    }
	if(!is_user_logged_in()){ 
		add_filter( 'comment_form_defaults', 'remove_textarea' );
	}

    if(!function_exists('add_comment_meta_values')) {
        function add_comment_meta_values($comment_id)
        {

            if (isset($_POST['rate'])) {
                $age = wp_filter_nohtml_kses($_POST['rate']);
                add_comment_meta($comment_id, 'rate', $age, false);
            }

        }
    }

	add_action ('comment_post', 'add_comment_meta_values', 1);

    if(!function_exists('remove_comment_fields')) {
        function remove_comment_fields($fields)
        {
            if (is_singular('listing')) {
                unset($fields['url']);
                $commenter = wp_get_current_commenter();
                return $fields;
            } else {
                return $fields;
            }
        }
    }



    if(!function_exists('add_bcw_fields')) {
        function add_bcw_fields()
        {
            if (is_singular('listing')) {
                echo '<div class="form-group margin-bottom-40">
                        <p class="padding-bottom-15">Your Rating for this listing</p>
                        <div class="list-style-none form-review-stars">
                            <input type="hidden" name="rate" class="rating-tooltip" data-filled="fa fa-star fa-2x" data-empty="fa fa-star-o fa-2x" />
                        </div>
                    </div>';
            } else {
            }
        }
    }
    add_action( 'comment_form_logged_in_after', 'add_bcw_fields' );
    add_filter('comment_form_default_fields','remove_comment_fields');
	
		
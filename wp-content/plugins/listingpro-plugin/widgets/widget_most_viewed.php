<?php
/**
 * Extend Recent Posts Widget 
 *
 * Adds different formatting to the default WordPress Recent Posts Widget
 */

class recent_listing_Widget extends WP_Widget{
function __construct() {
	parent::__construct(
		'listingPro_widget', // Base ID
		'ListingPro - Recent Listing Widget', // Name
		array('description' => '' )
   	);
}
function form($instance) {
	if( $instance) {
		$title = esc_attr($instance['title']);
		$numberOfListings = esc_attr($instance['numberOfListings']);
	} else {
		$title = '';
		$numberOfListings = '';
        $video_posts_style = '';
	}
	?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Title', 'listingpro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('numberOfListings')); ?>"><?php echo esc_html__('Number of Listings:', 'listingpro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('numberOfListings'); ?>" name="<?php echo esc_attr($this->get_field_name('numberOfListings')); ?>" type="text" value="<?php echo $numberOfListings; ?>" />
			<!-- <select id="<?php echo $this->get_field_id('numberOfListings'); ?>"  name="<?php echo esc_attr($this->get_field_name('numberOfListings')); ?>">
				<?php for($x=1;$x<=10;$x++): ?>
				<option <?php echo $x == $numberOfListings ? 'selected="selected"' : '';?> value="<?php echo $x;?>"><?php echo $x; ?></option>
				<?php endfor;?>
			</select> -->
		</p>
       
        <!-- <p>
            <label for="<?php echo esc_attr($this->get_field_id('text')); ?>"><?php echo esc_html__('Social Style:', 'listingpro' );?></label>
            <select class='widefat' id="<?php echo $this->get_field_id('video_posts_style'); ?>" name="<?php echo esc_attr($this->get_field_name('video_posts_style')); ?>" type="text">
                <option value='style_one'<?php echo ($video_posts_style=='style_one')?'selected':''; ?>><?php echo esc_html__('Style One', 'listingpro' );?></option>
                <option value='style_two'<?php echo ($video_posts_style=='style_two')?'selected':''; ?>><?php echo esc_html__('Style Two', 'listingpro' );?></option>
                <option value='style_three'<?php echo ($video_posts_style=='style_three')?'selected':''; ?>><?php echo esc_html__('Style Three', 'listingpro' );?></option>
            </select>                
        </p> -->
	<?php
}

function widget($args, $instance) {
	extract( $args );
	$title = apply_filters('widget_title', $instance['title']);
	$numberOfListings = $instance['numberOfListings'];
    $video_posts_style = $instance['video_posts_style'];
	echo $before_widget;

	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	global $post;
	
		$videosPosts = new WP_Query();
		$videosPosts->query('post_type=listing&post_status=publish&posts_per_page=' . $numberOfListings );
		if($videosPosts->found_posts > 0) {
			while ($videosPosts->have_posts()) {
				

				$videosPosts->the_post();
				if(has_post_thumbnail()) {
					$images = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-listing-grid' );
					$image = '<img src="'.$images[0].'"alt="image">';
				}else {
                    global $listingpro_options;
                    $images = $listingpro_options['lp_def_featured_image']['url'];
                    $image = '<img src="'.$images.'" alt="image">';
				}
				$gAddress = listing_get_metabox_by_ID('gAddress',get_the_ID());
				$rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                $rating = apply_filters('lp_rating_number_format', $rating );
                                
				$rate = $rating;
				?>
				<article class="lp-recent-listing-outer">
					<figure>
						<a href="<?php echo get_permalink(); ?>">
							<?php echo $image; ?>
						</a>
					</figure>
					<div class="details">
						<h4><a href="<?php echo get_permalink(); ?>"><?php echo mb_substr(get_the_title(), 0, 20).'...'; ?></a></h4>

                        <?php

                        $NumberRating = listingpro_ratings_numbers($post->ID);
                        if( $NumberRating == 0 )

                        {

                            ?>

                            <span class="lp-rating-num-first"><?php echo esc_html__( 'Be the first to review!', 'listingpro' ); ?></span>

                            <?php

                        }

                        else

                        {
                            $rating = get_post_meta( get_the_ID(), 'listing_rate', true );



                            ?>

                            <div class="lp-listing-stars">
                                <span class="lp-rating-num"><?php echo $rating; ?></span> <span class="lp-rating-num2"><?php esc_html_e('Rating','listingpro'); ?></span>
                            </div>
                            <?php
                        }

                        ?>


                        <div class="clearfix"></div>
						<?php if(!empty($gAddress)) { ?>
							<p><i class="fa fa-map-marker"></i> <?php echo mb_substr( $gAddress, 0, 32 ).'...'; ?></p>
						<?php } ?>
					</div>
					<div class="clearfix"></div>
				</article>
				<?php
			}
		}else{
			echo '<p style="padding:25px;">'.esc_html__('No listing found','listingpro').'</p>';
		}
wp_reset_postdata();
	echo $after_widget;
}



function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['numberOfListings'] = strip_tags($new_instance['numberOfListings']);
    $instance['video_posts_style'] = strip_tags($new_instance['video_posts_style']);
	return $instance;
}
 
 
} //end class recent_listing_Widget
if(!function_exists('recent_listing_widget_registration')) {
    function recent_listing_widget_registration()
    {
        register_widget('recent_listing_Widget');
    }
}
add_action('widgets_init', 'recent_listing_widget_registration');
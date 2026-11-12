<?php
global $post, $listingpro_options;
$popupview          = true;
$video_display_type = $listingpro_options['lp_detail_page_video_display'];
if ($video_display_type == "off") {
	$popupview = false;
}
$plan_id = listing_get_metabox_by_ID('Plan_id', $post->ID);
if ( ! empty($plan_id)) {
	$plan_id = $plan_id;
} else {
	$plan_id = 'none';
}
$video_show = get_post_meta($plan_id, 'video_show', true);
if ($plan_id == "none") {
	$video_show = 'true';
}

$video = listing_get_metabox_by_ID('video', $post->ID);
if ( ! empty($video)) {
	if ($video_show == "true") {
		if ($popupview == true) {
			if (strpos($video, 'youtu.be')) {
				$video = str_replace('youtu.be', 'youtube.com/watch?v=', $video);
			}
			?>
            <div class="video-option  margin-bottom-30">
                <h2>
                    <span><i class="fa fa-play-circle-o"></i></span>
                    <span><?php echo esc_html__('Checkout', 'listingpro'); ?></span>
					<?php echo get_the_title(); ?>
                    <a href="<?php echo esc_url($video); ?>" class="watch-video popup-youtube">
						<?php echo esc_html__('Watch Video', 'listingpro'); ?>
                    </a>
                </h2>
            </div>
			<?php
		} else {
			?>
            <div class="widget-video widget-box widget-bg-color lp-border-radius-5">

				<?php
				$htmlvideocode = wp_oembed_get($video);
				echo($htmlvideocode);
				?>

            </div>

			<?php
		}
	}
}
?>
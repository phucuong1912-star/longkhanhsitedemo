<?php
global $post;
$plan_id = listing_get_metabox_by_ID('Plan_id', $post->ID);
if (empty($plan_id)) {
	$plan_id = 'none';
}
$tags_show = get_post_meta($plan_id, 'listingproc_tag_key', true);
if ($plan_id == "none") {
	$tags_show = 'true';
}
$tags = get_the_terms($post->ID, 'features');
if (! empty($tags)) {
?>
	<div class="post-lp-classic-row ">
		<!-- <div class="post-row-header clearfix margin-bottom-15"><h3><?php echo esc_html__('Features', 'listingpro'); ?></h3></div> -->
		<ul class="features list-style-none clearfix">
			<?php
			foreach ($tags as $tag) {
				$icon = listingpro_get_term_meta($tag->term_id, 'lp_features_icon');
			?>
				<li>
					<a href="<?php echo get_term_link($tag); ?>" class="parimary-link">
						<span class="tick-icon">
							<?php if (! empty($icon)) { ?>
								<i class="fa <?php echo esc_attr($icon); ?>"></i>
							<?php } else { ?>
								<i class="fa fa-check"></i>
							<?php } ?>
						</span>
						<?php echo esc_html($tag->name); ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
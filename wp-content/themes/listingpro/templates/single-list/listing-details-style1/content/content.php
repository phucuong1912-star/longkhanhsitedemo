<?php
	global $post;
	$listingContent = get_the_content($post->ID);
	if ( $listingContent!=="" ) {
?>
		<div class="post-row margin-bottom-30">
			<div class="post-detail-content">
				<?php the_content(); ?>
			</div>
		</div>
<?php
	}
?>
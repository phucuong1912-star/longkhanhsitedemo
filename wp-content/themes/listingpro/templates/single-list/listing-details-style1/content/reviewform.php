<?php
	global $listingpro_options;
	$allowedReviews = $listingpro_options['lp_review_switch'];
	if(!empty($allowedReviews) && $allowedReviews=="1"){
		if(get_post_status($post->ID)=="publish"){
			listingpro_get_reviews_form($post->ID);
			echo '<div class="clearfix"></div>';
		}
	}
?>

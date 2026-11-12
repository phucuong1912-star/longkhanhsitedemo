<?php
	global $listingpro_options;
	$allowedReviews = $listingpro_options['lp_review_switch'];
	if(!empty($allowedReviews) && $allowedReviews=="1"){
		if(get_post_status($post->ID)=="publish"){
			echo '<div class="classic-review-form">';
			listingpro_get_reviews_form($post->ID);
			echo '<div class="clearfix"></div></div>';
		}
	}
?>

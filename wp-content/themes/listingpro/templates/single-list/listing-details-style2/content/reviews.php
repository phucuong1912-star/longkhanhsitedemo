<div class="tab-pane lpreviews" id="reviews">
	<div id="submitreview">
		<?php 
			//getting old reviews
			global $listingpro_options;
			listingpro_get_all_reviews($post->ID);
		?>
	</div>
	<?php
		//comments_template();
		
		$allowedReviews = $listingpro_options['lp_review_switch'];
		if(!empty($allowedReviews) && $allowedReviews=="1"){
			if(get_post_status($post->ID)=="publish"){
				listingpro_get_reviews_form($post->ID);
			}
		}
		
	?>
</div>
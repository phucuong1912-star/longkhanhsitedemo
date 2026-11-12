<?php

/**
 * Listing Ratings
 *
 */

/* ============== ListingPro Ratings (Stars) ============ */
if (!function_exists('listingpro_ratings_stars')) {
	function listingpro_ratings_stars($metaboxID, $postID) {
		$rating = listing_get_metabox_by_ID($metaboxID ,$postID);
		if( !empty($rating) ){
								$blankstars = 5;
								while( $rating > 0 ){
									
													if($rating < 1){
														echo '<i class="fa fa-star lp-star-worst"></i>';
														$rating--;
														$blankstars--;
													}
													
													else if($rating >=1 && $rating < 2){
														echo '<i class="fa fa-star lp-star-bad"></i>';
														$rating--;
														$blankstars--;
													}
													
													else if($rating >=2 && $rating < 3.5){
														echo '<i class="fa fa-star lp-star-satisfactory"></i>';
														$rating--;
														$blankstars--;
													}
													
													else if($rating >=3.5 && $rating <= 5){
														echo '<i class="fa fa-star lp-star-good"></i>';
														$rating--;
														$blankstars--;
													}
									
								}
								while( $blankstars > 0 ){
									echo '<i class="fa fa-star-o"></i>';
									$blankstars--;
								}
								
		}
		else{
			
			$blankstars = 5;
			while( $blankstars > 0 ){
									echo '<i class="fa fa-star-o"></i>';
									$blankstars--;
								}
			
		}
			
		
	}
}

/* ============== ListingPro Ratings (Figure) ============ */
if (!function_exists('listingpro_ratings_figure')) {
	function listingpro_ratings_figure($postID) {
		$print = '';
		$total = '';
		$comment= get_comments( array( 'post_id' => $postID, 'meta_key' => 'rate' ) );
			$count= 0;
			foreach($comment as $com){
				$rating = get_comment_meta($com->comment_ID, 'rate', true);
				if(!empty($rating)){
					 $count++;
				}
				$total+= $rating;
			}
			if($count > 0){
				$sub = $total/$count;
				$afterRound = round($sub);
				for($i=1;$i<=$afterRound;$i++){ 
					$print .='	<i class="fa fa-star"></i>';
				}
				$emptyStars = 5 - $afterRound;
				for($i=1;$i<=$emptyStars;$i++){
					$print .='	<i class="fa fa-star-o"></i>';
				}
			}else{
				for($i=1;$i<=5;$i++){ 
					$print .='<i class="fa fa-star-o"></i>';
				}
			}
			 return $print;
	}
}

/* ============== ListingPro set_listing_ratings ============ */
if (!function_exists('listingpro_set_listing_ratings')) {
	function listingpro_set_listing_ratings($review_id, $listing_id , $new_rating , $action) {

		//new code 2.6.15
		if(empty($listing_id)){
			$listing_id =null;
		}

		if(empty($new_rating)){
			$new_rating = null;
		}

		//end new code 2.6.15
		
		if ($action=="add" || $action=="update"){
			$reviews_ids = listing_get_metabox_by_ID('reviews_ids', $listing_id);
			
			if (!empty($reviews_ids)){
				$reviews_ids = $reviews_ids.','.$review_id;
			}
			else{
				$reviews_ids = $review_id;
			}
			$reviews = array();
			listing_set_metabox('reviews_ids', $reviews_ids, $listing_id);
			$reviews_ids = listing_get_metabox_by_ID('reviews_ids', $listing_id);
			if (strpos($reviews_ids, ',') !== false) {
				$reviews = explode(',', $reviews_ids);
			}
			else{
				$reviews[] = $reviews_ids;
			}
			$reviews = array_unique($reviews);
			
			$totalRating = 0;
			$totalReviews = 0;
			if(!empty($reviews) && !empty($reviews[0])){
				foreach($reviews as $review){
					if(get_post_status($review)=="publish" && !empty($review)){
						if($review==$review_id){
							$rateOfReview = $new_rating;
						}
						else{
							$rateOfReview = listing_get_metabox_by_ID('rating', $review);
                                                        
						}
						if (!empty($rateOfReview)) {
							$totalRating = $totalRating + $rateOfReview;
							$totalReviews++;
						}
                                                $rateOfReview = apply_filters('lp_rating_number_format', $rateOfReview );
						
					}
				}
			}
			if($totalRating != 0 && $totalReviews != 0){
				$afterRound = null;
				if(!empty($totalRating)){
					$sub = $totalRating/$totalReviews;
					$afterRound = $sub;
					$afterRound = number_format($sub, 1);
				}
				
				update_post_meta( $listing_id, 'listing_rate', $afterRound );
				
			}
		}
		
		else if($action=="delete"){
			
			$reviews_ids = listing_get_metabox_by_ID('reviews_ids', $listing_id);
			
			$reviews = array();
			if (strpos($reviews_ids, ',') !== false) {
				$reviews = explode(',', $reviews_ids);
			}
			else{
				$reviews[] = $reviews_ids;
			}
			foreach($reviews as $key=>$review){
				if($review==$review_id){
					unset($reviews[$key]);
				}
			}
			
			$reviews_ids = implode(",",$reviews);
			listing_set_metabox('reviews_ids', $reviews_ids, $listing_id);
			$totalRating = 0;
			$totalReviews = 0;
			foreach($reviews as $review){
				if(get_post_status($review)=="publish"){
					$rateOfReview = listing_get_metabox_by_ID('rating', $review);
					if (is_numeric($rateOfReview)) {
						$totalRating = $totalRating + $rateOfReview;
						$totalReviews++;
					}
					else{
						$totalRating = $totalRating + 0;
						$totalReviews++;
					}
                                        $rateOfReview = apply_filters('lp_rating_number_format', $rateOfReview );
				}
				
			}
			
			$afterRound = null;
			if(!empty($totalRating)){
				$sub = $totalRating/$totalReviews;
				$afterRound = $sub;
				$afterRound = number_format($sub, 1);
			}
			
			if(empty($afterRound)){
				delete_post_meta( $listing_id, 'listing_rate' );
			}else{
				update_post_meta( $listing_id, 'listing_rate', $afterRound );
			}
			
		}
		
	}
}

/* ============== ListingPro get rating number =================== */
if(!function_exists('listingpro_ratings_numbers')){
	function listingpro_ratings_numbers($listing_id){
		
		$rating = 0;
		$reviews_ids = listing_get_metabox_by_ID('reviews_ids', $listing_id);
			
		
		$reviews = array();
		$reviews_ids = listing_get_metabox_by_ID('reviews_ids', $listing_id);
		if (strpos($reviews_ids, ',') !== false) {
			$reviews = explode(',', $reviews_ids);
		}
		else{
			$reviews[] = $reviews_ids;
		}
		$reviews = array_unique($reviews);

		if(!empty($reviews) && !empty($reviews[0])){
			foreach($reviews as $review){
				if(get_post_status($review)=="publish"){
					$rating++;
					
				}
			}
		}
		
		return $rating;
		
	}
}

/* ============== ListingPro highest(avaerage) rating ============ */
if (!function_exists('listingpro_highest_average_rating')) {
	function listingpro_highest_average_rating($listing_id, $new_rate) {

		$listing_rate = get_post_meta( $listing_id, 'listing_reviewed', true );
		if( !empty($listing_rate) ){
			return $listing_rate;
		}
		else{
			return '0';
		}
		
	}
}

/* ============== ListingPro total(reviewes) rating ============ */
if (!function_exists('listingpro_total_reviews_add')) {
	function listingpro_total_reviews_add($listing_id){
		
		$total_reviewed = get_post_meta( $listing_id, 'listing_reviewed', true );
		if ( ! empty( $total_reviewed ) ) {
			$total_reviewed++;
			update_post_meta( $listing_id, 'listing_reviewed', $total_reviewed );
		}
		else{
			update_post_meta( $listing_id, 'listing_reviewed', '1' );
		}
		
	}
}
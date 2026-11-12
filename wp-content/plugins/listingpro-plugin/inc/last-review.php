<?php 
/* ============== Last review by List ID ============ */
	
	if (!function_exists('listingpro_review_by_list_ID')) {
		function listingpro_review_by_list_ID($postid) {
			$output = null;
			$key = 'reviews_ids';
			$review_ids = listing_get_metabox_by_ID($key ,$postid);
			
			$review_ids = explode(",",$review_ids);

			if(!empty($review_ids) && !empty($review_ids[0])){
				$tagline_text = listing_get_metabox_by_ID('tagline_text' ,$postid);
				if(!empty($review_ids)){
					$count = count($review_ids);
					$last = $count - 1;
					$reviewID = $review_ids[$last];

					$author_id = get_post_field( 'post_author', $reviewID );
					$content = get_post_field( 'post_content', $reviewID );
							
					$author_avatar_url = get_user_meta($author_id, "classiads_author_avatar_url", true); 
					$avatar;
					if(!empty($author_avatar_url)) {
						$avatar =  $author_avatar_url;

					} else { 			
						$avatar_url = listingpro_get_avatar_url ( $author_id, $size = '65' );
						$avatar =  $avatar_url;

					}

					$output .= '
					<div class="review">
						<div class="review-post">
							<div class="reviewer-thumb">
								<img src="'.$avatar.'" alt="image">
							</div>
							<div class="reviewer-details">
								<p>'.substr($content, 0, 80).'..</p>
							</div>
						</div>
					</div>';

				}else{
					$output .= '<p>'.$tagline_text.'</p>';
				}
			}
			return $output;
		}
	}
<?php 
/* ============== Last review by List ID ============ */
	
	if (!function_exists('listingpro_last_review_by_list_ID')) {
		function listingpro_last_review_by_list_ID($postid) {
			$key = 'reviews_ids';
			$review_ids = listing_get_metabox_by_ID($key ,$postid);
			$review_ids = explode(",",$review_ids);
			$tagline_text = listing_get_metabox_by_ID('tagline_text' ,$postid);

			if(!empty($review_ids) && !empty($review_ids[0])){
				$count = count($review_ids);
				$last = $count - 1;
				$reviewID = $review_ids[$last];
				if(get_post_status($reviewID)=="publish"){
					$author_id = get_post_field( 'post_author', $reviewID );
					$content = get_post_field( 'post_content', $reviewID );
							
					$author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true); 
					$avatar;
					if(!empty($author_avatar_url)) {
						$avatar =  $author_avatar_url;

					} else { 			
						$avatar_url = listingpro_get_avatar_url ( $author_id, $size = '94' );
						$avatar =  $avatar_url;

					}
					
					?>
					<div class="review">
						<div class="review-post">
							<div class="reviewer-thumb">
								<img src="<?php  echo esc_attr($avatar); ?>" alt="image">
							</div>
							<?php //echo listingpro_last_review_by_list_ID(get_the_title()); ?>
							<div class="reviewer-details">
								<!-- <h4><?php echo get_the_title($reviewID); ?> - <span><?php echo get_the_date('l F j, Y',$reviewID); ?></span></h4> -->
								<p><?php echo substr($content, 0, 95); ?>...</p>
							</div>
						</div>
					</div>
				<?php 
				} else{
					if(!empty($tagline_text)){
						echo '<p><span class="icon"><i class="fa fa-tags"></i></span>'.mb_substr($tagline_text, 0, 100).'</p>';
					}
				} ?>
				
				<?php
			}else{
				if(!empty($tagline_text)){
					echo '<p><span class="icon"><i class="fa fa-tags"></i></span>'.mb_substr($tagline_text, 0, 100).'</p>';
				}
			}
		}
	}
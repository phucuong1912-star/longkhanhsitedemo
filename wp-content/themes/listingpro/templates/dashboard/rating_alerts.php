<ul class="lp-ad-all-alerts clearfix">
						
	<?php
		$recentReviews = array();
		$recentReviews = getAllReviewsArray(false);
		$currentURL = '';
		$perma = '';
		$dashQuery = 'dashboard=';
		$currentURL = listingpro_url('listing-author');
		global $wp_rewrite;
		if ($wp_rewrite->permalink_structure == ''){
			$perma = "&";
		}else{
			$perma = "?";
		}
		if(!empty($recentReviews) && count($recentReviews)>0){
			$args = array(
				'post_type'	=> 'lp-reviews',
				'posts_per_page'	=> 10,
				'post__in'	=> $recentReviews,
				'orderby' => 'date',
				'order'   => 'DESC',
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$author = '';
					$rating = '';
					$content = '';
					
					$rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
					if(!empty($rating)){
					}
					else{
						$rating = 0;
					}
                                        $rating = apply_filters('lp_rating_number_format', $rating );
					
					if($rating <= 3){
						$content = wp_trim_words( get_the_content(), 15, '...' );
					$author = get_the_author();
					$postedtime = get_the_date();
                    $postedtime = get_the_date('j-n-Y');
                    $lptimenow = date('j-n-Y');
                    $datetime1 = new DateTime($postedtime);
                    $datetime2 = new DateTime($lptimenow);
                    $interval = $datetime1->diff($datetime2);
					$timediff = $interval->format('%R%a');
					$timediff = (int)$timediff;
					$timediffString = '';
					if(!empty($timediff)){
						if($timediff > 1){
							$timediffString = esc_html__('Posted', 'listingpro').' '.$timediff.' '.esc_html__('days ago', 'listingpro');
						}else{
							$timediffString = esc_html__('Posted', 'listingpro').' '.$timediff.' '.esc_html__('day ago', 'listingpro');
						}
					}else{
						$timediffString = esc_html__('Posted Today', 'listingpro');
					}
		?>

						<li>
							<a href="<?php echo esc_url($currentURL.$perma.$dashQuery.'reviews#comment-'.get_the_ID()); ?>">
								<span class="lp-star-rating lp-one-star">
									<span></span>
								</span>
								<div class="lp-rating-content">
									<h4><?php echo esc_attr($author).' '.esc_html__('left a', 'listingpro').' '.$rating. esc_html__('star rating.', 'listingpro'); ?></h4>
									<h5><?php echo esc_attr($timediffString); ?></h5>
								</div>
							</a>
						</li>
		<?php
					}
				}
			}
		}else{
		?>
			<li><?php echo esc_html__('Low ratings Notifications will be here.','listingpro'); ?></li>
		<?php
		}
		?>
		
		
		
</ul>
<?php

	global $listingpro_options;
	$current_user = wp_get_current_user();
	$userID = $current_user->ID;
	
	$publish_listings = count_user_posts_by_status('listing', 'publish',$userID);
	$pending_listings = count_user_posts_by_status('listing', 'pending',$userID);
	
	if($pending_listings > 1){
		$notice = esc_html__('You have', 'listingpro').' '.$pending_listings.' '.esc_html__('listings that are almost ready to publish.', 'listingpro');
	}elseif($pending_listings == 1){
		$notice = esc_html__('You have', 'listingpro').' '.$pending_listings.' '.esc_html__('listing that is almost ready to publish.', 'listingpro');
	}

    $published_listings = '';
    $pending_listings='';
    $expired_listings = '';
    $all_listings='';
    $count_listings = wp_count_posts( 'listing', 'readable' );
    $published_listings = count_user_posts_by_status('listing', 'publish',$userID, false);
    $pending_listings = count_user_posts_by_status('listing', 'pending',$userID, false);
    $expired_listings = count_user_posts_by_status('listing', 'expired',$userID, false);
    $all_listings = $published_listings + $pending_listings + $expired_listings;
    $published_campaings = lp_count_user_campaigns($userID);

?>
<div class="panel-dash-views">
    <div class="row">
        <div class="col-md-4 col-xs-4 padding-zero">
            <div class="count-box">
                <?php
                $totalViews = '';
                $totalViews = getAuthorPostsViews();
                ?>
                <p class="views"><?php echo esc_attr($totalViews); ?></p>
                <p><i class="fa fa-eye"></i> <?php esc_html_e('Views','listingpro'); ?></p>

            </div>
        </div>
        <div class="col-md-4 col-xs-4 padding-zero">
            <div class="count-box">
                <?php
                $authorID = get_current_user_id();
                $leadsCount = '';
                $leadsCount = get_user_meta( $authorID, 'leads_count', true );
                if(empty($leadsCount)){
                    $leadsCount = 0;
                }
                ?>
                <p class="views"><?php echo esc_attr($leadsCount); ?></p>
                <p><i class="fa fa-bullseye"></i><?php esc_html_e('Leads','listingpro'); ?></p>
            </div>
        </div>
        <div class="col-md-4 col-xs-4 padding-zero">
            <div class="count-box">
                <?php
                $totalReviews = '';
                $totalReviews = getAuthorTotalViews();
                ?>
                <p class="views"><?php echo esc_attr($totalReviews); ?></p>
                <p><i class="fa fa-commenting-o"></i><?php esc_html_e('Reviews','listingpro'); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="dashboard-panel clearfix">
	<div class="notices-area">
            <?php if($pending_listings == 0 && $publish_listings == 0){ ?>
                <div class="notice success">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-bullhorn"></i>
                    </div>
                    <div class="notice-text">
                        <h2><?php esc_html_e('Welcome! ','listingpro'); ?><span><?php esc_html_e('Get Started by adding a new listing!','listingpro'); ?></span></h2>
                        <p><?php esc_html_e('Lets start by adding your listing. Make sure to provide as much as details as possible.','listingpro'); ?></p>
                    </div>
                </div>
            <?php } ?>
            <?php if($publish_listings != 0){ ?>
                <div class="notice info">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div class="notice-text">
                        <h2><?php esc_html_e('Promote!','listingpro'); ?> <span><?php esc_html_e('Did you know you can generate 10x leads!','listingpro'); ?></span></h2>
                        <p><?php esc_html_e('Start advertising today and reach 100 times more audience which can increase the potential of getting up to 10x leads and double the conversions.','listingpro'); ?></p>
                    </div>

                </div>
            <?php } ?>
            <?php if($pending_listings != 0){ ?>
                <div class="notice warning">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="notice-text">
                        <h2><?php esc_html_e('Listing Pending! ','listingpro'); ?><span><?php esc_html_e('Publish your Listings today and get new customers!','listingpro'); ?></span></h2>
                        <p><strong><?php echo esc_attr($notice); ?></strong><?php esc_html_e(' Please review and submit to get new leads and customers.','listingpro'); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    <div class="user-description-box clearfix">
        <div class="col-sm-6 col-xs-6 description-box description-box-campine description-box-all-listing">
            <p class="count"><?php echo esc_attr($all_listings); ?> <?php echo esc_html__('Items', 'listingpro'); ?></p>
            <p class="count-text"><?php echo esc_html__('All Listing', 'listingpro'); ?></p>
        </div>
        <div class="col-sm-6 col-xs-6 description-box description-box-pending">
            <p class="count"><?php echo esc_attr($pending_listings); ?> <?php echo esc_html__('Items', 'listingpro'); ?></p>
            <p class="count-text"><?php echo esc_html__('Pending', 'listingpro'); ?></p>
        </div>
        <div class="col-sm-6 col-xs-6 description-box description-box-publish">
            <p class="count"><?php echo esc_attr($published_listings); ?> <?php echo esc_html__('Items', 'listingpro'); ?></p>
            <p class="count-text"><?php echo esc_html__('Published', 'listingpro'); ?></p>
        </div>
        <div class="col-sm-6 col-xs-6 description-box description-box-expire">
            <p class="count"><?php echo esc_attr($expired_listings); ?> <?php echo esc_html__('Items', 'listingpro'); ?></p>
            <p class="count-text"><?php echo esc_html__('Expired', 'listingpro'); ?></p>
        </div>
        <div class="col-sm-6 col-xs-6 description-box description-box-campine">
            <p class="count"><?php echo esc_attr($published_campaings); ?> <?php echo esc_html__('Items', 'listingpro'); ?></p>
            <p class="count-text"><?php echo esc_html__('Active Campaigns', 'listingpro'); ?></p>
        </div>
        <div class="clearfix"></div>
    </div>
	
</div>
<div class="panel-recent-activity">
		
		<?php
		
		$lp_recent_activities = get_option( 'lp_recent_activities' );
		
			if( $lp_recent_activities!=false ){
				if (array_key_exists($userID, $lp_recent_activities)) {
					$recent_activities = $lp_recent_activities[$userID];				
					if(!empty($recent_activities)){
						krsort($recent_activities);
						echo '	<div class="section-title">
									<h3>'.esc_html__('Recent Activities','listingpro').'</h3>
								</div>
								<ul>';
						foreach($recent_activities as $key=>$val){
							$currentdate = date("Y-m-d h:i:a");
							$datePosted = $val["time"];
							

							if(!empty($val['type']) && $val['type']=="reaction"){
								echo '
									<li>
										<i class="reaction fa fa-thumbs-o-up"></i>
										<span>
											<strong class="reaction">'.esc_html__('New Reaction!','listingpro').'</strong>
											'.esc_html__('Someone reacted as', 'listingpro').' <strong>'.$val["rating"].'</strong> '.esc_html__('on a comment by ','listingpro').' '.get_the_author_meta('user_login',$val['reviewer']).'.
											<time>'.$val["time"].'</time>
										</span>
									</li>
								';
							}
							
							if(!empty($val['type']) && $val['type']=="lead"){
								echo '
									<li>
										<i class="lead fa fa-bullseye"></i>
										<span>
											<strong class="lead">'.esc_html__('New Lead!','listingpro').'</strong>
											'.$val["actor"].' '.esc_html__('just contacted you.','listingpro').'
											<time>'.$val["time"].'</time>
										</span>
									</li>
								';
							}
							
							if(!empty($val['type']) && $val['type']=="review"){
								echo '
									<li>
										<i class="review fa fa-commenting-o"></i>
										<span>
											<strong class="review">'.esc_html__('New Review!','listingpro').'</strong>
											'.$val["actor"].' '.esc_html__('left a','listingpro').' '.$val["rating"].'/5'.esc_html__(' rating and a review on ','listingpro').'<a href="'.get_the_permalink($val['listing']).'">'.get_the_title($val['listing']).'.</a>
											<time>'.$val["time"].'</time>
										</span>
									</li>
								';
							}
							
							if(!empty($val['type']) && $val['type']=="visit"){
								echo '
									<li>
										<i class="fa fa-globe" aria-hidden="true"></i>
										<span class="simptip-position-top simptip-movable lp-userInfo" data-tooltip="'.$val['reviewer'].'">
											<strong class="review">'.esc_html__("Website visit!","listingpro").'</strong>
											'.$val["actor"].' '.esc_html__("clicked on your website link at ","listingpro").' <a href="'.get_the_permalink($val["listing"]).'">'.get_the_title($val["listing"]).'</a>
											<time>'.$val["time"].'</time>
										</span>
									</li>
								';
							}
							
							if(!empty($val['type']) && $val['type']=="phone"){
								
								echo '
									<li>
										<i class="fa fa-phone" aria-hidden="true"></i>
										<span class="simptip-position-top simptip-movable lp-userInfo" data-tooltip="'.$val['reviewer'].'">
											<strong class="review">'.esc_html__("Phone Number!","listingpro").'</strong>
											'.$val["actor"].' '.esc_html__("clicked on your phone number at ","listingpro").' <a href="'.get_the_permalink($val["listing"]).'">'.get_the_title($val["listing"]).'</a>
											<time>'.$val["time"].'</time>
										</span>
									</li>
								';
							}
							
							
							
						}
						echo '</ul>';
					}else{
						echo '<div class="nothing-inn">'.esc_html__('Recent activities about your listings will be here','listingpro').'</div>';
					}
				}
				else{
					echo '<div class="nothing-inn">'.esc_html__('Recent activities about your listings will be here','listingpro').'</div>';
				}

				
			}
			else{
				echo '<div class="nothing-inn">'.esc_html__('Recent activities about your listings will be here.','listingpro').'</div>';
			}
							
		?>
		
		
	</div>
<div class="dashboard-right-panel">
	<?php 
		/*$addURL = listingpro_url('add_listing_url_mode');
		if(!empty($addURL)){*/
	?>
	<!-- <a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="lp-add-new-listing">
	<?php
		/*} else{*/
	?>
	<a href="#" class="lp-add-new-listing">
	<?php //} ?>
		<i class="fa fa-plus"></i>
		<span>Add New Listing</span>
	</a> -->
	<?php
		$recentReviews = array();
		$recentReviews = getAllReviewsArray(false);
		
	?>
	<div class="lp-dashboard-right-panel-listing">
		
			<?php
			
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
					echo '<h4>'.esc_html__('Following need your urgent Attention','listingpro').'</h4>

							<ul class="clearfix">';
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
						$content = wp_trim_words( get_the_content(), 15, '...' );
						$author = get_the_author();
						if($rating <= 3){
							echo '
								<li>
									<i class="fa fa-commenting-o"></i>
									<strong>'.esc_html__('Attention!','listingpro').'</strong>
									<span><span class="author-name">'.$author.'</span> left '.$rating.'/5 '.esc_html__('rating','listingpro').'</span>
									<a class="reply" href="'.$currentURL.$perma.$dashQuery.'reviews#comment-'.get_the_ID().'">'.esc_html__('Reply', 'listingpro').'</a>
								</li>
							';
						}
					}
					echo '</ul>';
					wp_reset_postdata();
				}
			}else{
				echo '<div class="nothing-inn">'.esc_html__('Low ratings Notifications will be here.','listingpro').'</div>';
			} 
			?>
	</div>
</div>
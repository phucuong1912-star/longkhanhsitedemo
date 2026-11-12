<?php
global $listingpro_options;
$current_user = wp_get_current_user();
$userID = $current_user->ID;

$publish_listings = 0;
$pending_listings = 0;
$pub_switch = lp_theme_option('pub_pend_notice');
$campgn_notice = lp_theme_option('campgn_notice');

$publish_listings = count_user_posts_by_status('listing', 'publish',$userID);
$pending_listings = count_user_posts_by_status('listing', 'pending',$userID);

$lpalertsRatings = getAllReviewsArray(false);
$lptotalClicks = lp_get_total_ads_clicks();
$colCLass = 9;
if( empty($lpalertsRatings) && empty($lptotalClicks) ){
	$colCLass = 9;
}
?>

    <div class="clearfix lp-dashboard-panel-outer col-md-<?php echo esc_attr($colCLass); ?> lp-new-dashboard-panel-outer lp-left-panel-height lp-left-static">
        <div class="notices-area">
            <?php if($publish_listings != 0 && !empty($campgn_notice)){ ?>
                <div class="notice info">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>

                    <div class="notice-text">
                        <h2><span><?php esc_html_e('Did you know you can generate 10x leads!','listingpro'); ?></span></h2>

                    </div>

                </div>
            <?php } ?>
            <?php if($pending_listings != 0 && !empty($pub_switch)){ ?>
                <div class="notice warning">
                    <a href="#" class="close"><i class="fa fa-times"></i></a>
                    <div class="notice-icon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="notice-text">
                        <h2><span><?php esc_html_e('Publish your Listings today and get new customers!','listingpro'); ?></span></h2>

                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="panel-dash-views">
            <div class="row">
				<?php
					
					$yearsStats = get_option('lp_years_stats');
				
					global $wpdb;
					$totalViewsCounts = lp_get_total_count('listing_stats_views', $userID);
					$totalReviewsCounts = lp_get_total_count('listing_stats_reviews', $userID);
					$totalLeadsCounts = lp_get_total_count('listing_stats_leads', $userID);
									
				?>
                <div class="clearfix col-md-12 lp-stats-sorting-outer">
                    <div class="pull-left"><h4><?php echo date_i18n(get_option( 'date_format' )); ?></h4></div>
                    <div class="pull-right">
                        <ul class="lp_stats_duration_filter clearfix" style="display:none">
                            <li>
                                <button type="button" class="lp_stats_duratonBtn active" data-chartduration="weekly" data-label="" data-type=""><?php echo esc_html__('Weekly', 'listingpro'); ?></button>
                            </li>
                            <li>
                                <button type="button" class="lp_stats_duratonBtn" data-chartduration="monthly" data-label="" data-type=""><?php echo esc_html__('Monthly', 'listingpro'); ?></button>
                            </li>
                            <?php
                                if(!empty($yearsStats)){
                            ?>
                                <li>
                                    <button type="button" class="lp_stats_duratonBtn" data-chartduration="yearly" data-label="" data-type=""><?php echo esc_html__('Yearly', 'listingpro'); ?></button>
                                </li>
                                <?php
                                }
                            ?>
                        </ul>
                        <div class="lp-dash-search-stats-outer" style="display:none">
                            <button type="button" class="lp-filter-search-listing"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <div class="lp-dash-search-stats-inner">
                                <div class="lp-dash-search-stats">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <input class="select-dash"  type="text" placeholder="<?php echo esc_html__('Select a Listing', 'listingpro'); ?>" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox">
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xs-12 padding-right-0">
                    <div class="count-box blue-box lp_user_stats_btn lpviewchart" data-chartduration="weekly" data-type="<?php echo esc_html__('view', 'listingpro'); ?>" data-label="view">
                        <div class="clearfix">
                            <p><span><?php esc_html_e('User Views','listingpro'); ?></span></p>
                            <div class="help-text">
                                <a href="#" class="help"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php esc_html_e('View your listings total views from the last Week or Month.','listingpro'); ?></p>
                                </div>
                            </div>

                        </div>
                        <div class="icon-area"><i class="fa fa-eye"></i></div>
                        <div class="dash-right-area">

                            <h3> <p class="views lpstatsnumber"><?php echo esc_attr($totalViewsCounts); ?></p></h3>
                        </div>
						<div class="clearfix"></div>
                        <div class="lp-dash-bottom-area clearfix">
                            <h6 class="lp_status_duration_counter"><?php echo esc_html__('in All Times','listingpro'); ?></h6>
                        </div>
                        <div class="lp-more-insgts-btn clearfix">
                            <button type="button"><?php esc_html_e('more insights','listingpro'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12 padding-0">
                    <div class="count-box orange-box lp_user_stats_btn lpviewleads" data-chartduration="weekly" data-type="<?php echo esc_html__('leads', 'listingpro'); ?>" data-label="leads">
                        <div class="clearfix">
                            <p><span><?php esc_html_e('Customer Leads','listingpro'); ?></span></p>
                            <div class="help-text">
                                <a href="#" class="help"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php esc_html_e('View the total Leads from your listings for the last Week or Month.','listingpro'); ?></p>
                                </div>
                            </div>

                        </div>
                        <div class="icon-area"><i class="fa fa-bullseye"></i></div>
                        <div class="dash-right-area">
                            
                            <h3> <p class="views lpstatsnumber"><?php echo esc_attr($totalLeadsCounts); ?></p></h3>
                        </div>
						<div class="clearfix"></div>
                        <div class="lp-dash-bottom-area clearfix">
                            <h6 class="lp_status_duration_counter"><?php echo esc_html__('in All Times','listingpro'); ?></h6>
                        </div>
                        <div class="lp-more-insgts-btn clearfix">
                            <button type="button"><?php esc_html_e('more insights','listingpro'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12 padding-left-0">
                    <div class="count-box green-box lp_user_stats_btn lpviewreviews" data-chartduration="weekly" data-type="<?php echo esc_html__('reviews', 'listingpro'); ?>" data-label="reviews">
                        <div class="clearfix">
                            <p><span><?php esc_html_e('Customer Reviews','listingpro'); ?></span></p>
                            <div class="help-text">
                                <a href="#" class="help"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php esc_html_e('View the Total Reviews for your listings from the last Week or Month.','listingpro'); ?></p>
                                </div>
                            </div>

                        </div>
                        <div class="icon-area"><i class="fa fa-commenting-o"></i></div>
                        <div class="dash-right-area">
                            
                            <h3> <p class="views lpstatsnumber"><?php echo esc_attr($totalReviewsCounts); ?></p></h3>
                        </div>
						<div class="clearfix"></div>
                        <div class="lp-dash-bottom-area clearfix">
                            <h6 class="lp_status_duration_counter"><?php echo esc_html__('in All Times','listingpro'); ?></h6>
                        </div>
                        <div class="lp-more-insgts-btn clearfix">
                            <button type="button"><?php esc_html_e('more insights','listingpro'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <!--for stats chart-->
            <div class="row">
                <div class="col-md-12">
                    <div class="lpstats background-white">

                        <div id="lpgraph"></div>
                    </div>
                </div>
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
                        ?>
                        <div class="clearfix"></div>
                        <div class="lp-new-activity-container clearfix">
                            <div class="section-title">
                                <h3><?php echo esc_html__('Recent Activities','listingpro'); ?></h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="lp-new-activity-outer">
                                <?php
                                $loopCount = 1;
                                foreach($recent_activities as $key=>$val){
                                    $activiitesclass = '';
                                    if($loopCount > 5){
                                        $activiitesclass = 'lp_hid_this_activity';
                                    }
                                    $currentdate = date_i18n("Y-m-d h:i:a");
                                    $datePosted = $val["time"];
                                    //$lpOnlyDate = date('Y-m-d', strtotime($datePosted));
                                    // change for according to psd.
                                    $lpOnlyDate = date_i18n(get_option( 'date_format' ), strtotime($datePosted));

                                    //$lpOnlyDate = date(get_option( 'date_format' ), strtotime($lpOnlyDate));
                                    $lpOnlyTime = date_i18n('H:i:s', strtotime($datePosted));

                                    $lpOnlyTime = date_i18n(get_option( 'time_format' ), strtotime($lpOnlyTime));


                                    if(!empty($val['type']) && $val['type']=="reaction"){ ?>
                                        <div class="lp-new-activity-inner <?php echo esc_attr($activiitesclass); ?>">
                                            <div class="clearfix">

                                                <div class="lp-activity-image">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <div class="lp-activity-user-img"><img src="<?php echo listingpro_author_image(); ?>" alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></div>
                                                </div>
                                                <div class="lp-activity-content">
																<span><?php echo esc_html__('Someone reacted as','listingpro'); ?>
																<?php 
																	$listingID = $val["listing"];
																	$LPlistingURL = get_the_permalink($listingID);
																?>
                                                                   <a href="<?php echo esc_attr($LPlistingURL )?>" target="_blank"><?php echo wp_kses_post(number_format((float)$val["rating"],1,'.','')).' '. esc_html__('on a comment by ','listingpro').' '.get_the_author_meta('user_login',$val['reviewer']); ?></a></span>
                                                    <div class="lp-new-activity-date">
                                                        <p><?php echo esc_attr($lpOnlyDate); ?></p>
                                                        <p><?php echo esc_attr($lpOnlyTime); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }elseif(!empty($val['type']) && $val['type']=="lead"){
                                        ?>
                                        <div class="lp-new-activity-inner <?php echo esc_attr($activiitesclass); ?>">
                                            <div class="clearfix">

                                                <div class="lp-activity-image">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <div class="lp-activity-user-img"><img src="<?php echo listingpro_author_image(); ?>" alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></div>
                                                </div>
                                                <div class="lp-activity-content">
																<span><?php echo esc_html__($val["actor"],'listingpro'); ?>
																<?php 
																	$listingID = $val["listing"];
																	$LPlistingURL = get_the_permalink($listingID);
																?>
                                                                    <a href="<?php echo esc_attr($LPlistingURL )?>" target="_blank"><?php echo esc_html__('just contacted you.','listingpro'); ?></a></span>
                                                    <div class="lp-new-activity-date">
                                                        <p><?php echo esc_attr($lpOnlyDate); ?></p>
                                                        <p><?php echo esc_attr($lpOnlyTime); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }elseif(!empty($val['type']) && $val['type']=="review"){

                                        $userid = $val['reviewer'];

                                        $author_avatar_url = get_user_meta($userid, "listingpro_author_img_url", true);

                                        if(!empty($author_avatar_url)) {

                                            $avatar =  $author_avatar_url;

                                        } else {

                                            $avatar_url = listingpro_get_avatar_url ( $userid, $size = '94' );
                                            $avatar =  $avatar_url;

                                        }
                                        ?>
                                        <div class="lp-new-activity-inner <?php echo esc_attr($activiitesclass); ?>">
                                            <div class="clearfix">

                                                <div class="lp-activity-image">
                                                    <i class="fa fa-star-o" aria-hidden="true"></i>
                                                    <div class="lp-activity-user-img"><img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></div>
                                                </div>
                                                <div class="lp-activity-content">
																<span><?php echo esc_html__($val["actor"],'listingpro'); ?>
                                                                    <?php echo ' '.esc_html__('left a','listingpro').' '.number_format((float)$val["rating"],1,'.','').'/5'.esc_html__(' rating and a review on ','listingpro').'<a href="'.get_the_permalink($val['listing']).'">'.get_the_title($val['listing']); ?></a></span>
                                                    <div class="lp-new-activity-date">
                                                        <p><?php echo esc_attr($lpOnlyDate); ?></p>
                                                        <p><?php echo esc_attr($lpOnlyTime); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }elseif(!empty($val['type']) && $val['type']=="visit"){ ?>
                                        <div class="lp-new-activity-inner <?php echo esc_attr($activiitesclass); ?>">
                                            <div class="clearfix">

                                                <div class="lp-activity-image">
                                                    <i class="fa fa-globe" aria-hidden="true"></i>
                                                    <div class="lp-activity-user-img"><img src="<?php echo listingpro_author_image(); ?>" alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></div>
                                                </div>
                                                <div class="lp-activity-content">
																<span class="simptip-position-top simptip-movable lp-userInfo" data-tooltip="<?php echo esc_attr($val['reviewer']); ?>">
																<?php echo esc_html__($val["actor"],'listingpro'); ?>
                                                                    <?php echo ' '.esc_html__("clicked on your website link at ","listingpro").' <a href="'.get_the_permalink($val["listing"]).'">'.get_the_title($val["listing"]); ?></a></span>
                                                    <div class="lp-new-activity-date">
                                                        <p><?php echo esc_attr($lpOnlyDate); ?></p>
                                                        <p><?php echo esc_attr($lpOnlyTime); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }elseif(!empty($val['type']) && $val['type']=="phone"){ ?>
                                        <div class="lp-new-activity-inner <?php echo esc_attr($activiitesclass); ?>">
                                            <div class="clearfix">

                                                <div class="lp-activity-image">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <div class="lp-activity-user-img"><img src="<?php echo listingpro_author_image(); ?>" alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></div>
                                                </div>
                                                <div class="lp-activity-content">
																<span class="simptip-position-top simptip-movable lp-userInfo" data-tooltip="<?php echo wp_kses_post($val['reviewer']); ?>">
																<?php echo esc_html__($val["actor"],'listingpro'); ?>
                                                                    <?php echo esc_html__("clicked on your phone number at ","listingpro").' <a href="'.get_the_permalink($val["listing"]).'">'.get_the_title($val["listing"]); ?></a></span>
                                                    <div class="lp-new-activity-date">
                                                        <p><?php echo esc_attr($lpOnlyDate); ?></p>
                                                        <p><?php echo esc_attr($lpOnlyTime); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php
                                    $loopCount++;
                                }
                                ?>
                                <?php
                                if($loopCount > 5){
                                    ?>
                                    <div class="background-white lp-more-activity-btn">
                                        <button class="lp_see_more_activities active" data-replacetext="<?php echo esc_attr__('see less activity', 'listingpro'); ?>"><?php echo esc_html__('see more activity', 'listingpro'); ?></button>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>


                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
    </div>
<?php
set_query_var( 'lptotalClicks', $lptotalClicks );
if( !empty($lpalertsRatings) || !empty($lptotalClicks) ){
    ?>
    <div class="col-md-3 padding-right-0 lp-right-panel-height lp-right-static">
        <div class="lp-ad-click-outer">
            <?php
            if( !empty($lptotalClicks)){ ?>
                <div class="lp-general-section-title-outer">
                    <?php echo get_template_part('templates/dashboard/ads_state'); ?>
                </div>
                <?php
            }
            if( !empty($lpalertsRatings)){
                ?>

                <div class="lp-general-section-title-outer">
                    <p id="reply-title" class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Rating alerts', 'listingpro'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                        <?php echo get_template_part('templates/dashboard/rating_alerts'); ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}else{
	?>
	
	 <div class="col-md-3 padding-right-0 lp-right-panel-height">
        <div class="lp-ad-click-outer">
            <div class="lp-general-section-title-outer">
                    <p id="reply-title" class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Rating alerts', 'listingpro'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                        <?php echo get_template_part('templates/dashboard/rating_alerts'); ?>
                    </div>
                </div>
        </div>
    </div>
	
	<?php
}
?>
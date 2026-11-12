<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        $event_id = get_the_ID();
        $listing_id = get_post_meta($event_id, 'event-lsiting-id', true);
        $event_utilities = get_post_meta($event_id, 'event-utilities', true);
        $timeNow = strtotime("-1 day");
        $event_date = get_post_meta($event_id, 'event-date', true);
        $event_date_end = get_post_meta($event_id, 'event-date-e', true);
        if (!empty($event_date_end) && $event_date_end < $timeNow) return false;
        if (empty($event_date_end) && $timeNow > $event_date) return false;
        $event_time = get_post_meta($event_id, 'event-time', true);
        $event_time_end =   get_post_meta($event_id, 'event-time-e', true);
        
        $event_time =   date_i18n(get_option( 'time_format' ), strtotime($event_time));
        $event_end_time =   date_i18n(get_option( 'time_format' ), strtotime($event_time_end));

        $event_loc = get_post_meta($event_id, 'event-loc', true);
        $event_lat = get_post_meta($event_id, 'event-lat', true);
        $event_lon = get_post_meta($event_id, 'event-lon', true);
        $event_ticket_url = get_post_meta($event_id, 'ticket-url', true);
        $event_img = get_post_meta($event_id, 'event-img', true);
        $event_object = get_post($event_id);
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $attending_users = get_post_meta($event_id, 'attending-users', true);
        $attendies_count = 0;
        if (!empty($attending_users) && is_array($attending_users)) {
            $attendies_count = count($attending_users);
        }
        $lp_map_pin = lp_theme_option_url('lp_map_pin');

        if (has_post_thumbnail($event_id)) {
            $event_img = get_the_post_thumbnail_url($event_id, 'full');
        } else {
            $event_img  =   $event_img;
        }
         ?>
        <section class="lp-event-detail">
            <div class="lp-event-top-title-header">                <?php if (!empty($event_img)) { ?>
                    <div class="lp-event-bgimage"
                         style="background: url(<?php echo esc_url($event_img); ?>);"></div>                <?php
                } ?>
                <div class="lp-event-detail-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-8">                            <?php if (!empty($event_img)) {
                                require_once(THEME_PATH . "/include/aq_resizer.php");
                                $event_img_thumb = aq_resize($event_img, '770', '320', true, true, true); ?>
                                <div class="lp-event-detail-thumbnail"><img src="<?php echo esc_url($event_img_thumb); ?>"
                                                                            alt="<?php echo get_the_title($event_id); ?>">
                                </div>                            <?php
                            } ?>
                            <div class="lp-event-detail-date-title-outer">                                <?php if (!empty($event_date)) { ?>
                                    <div class="lp-event-detail-date"><span
                                                class="event-detil-date"><?php echo date_i18n('d', $event_date); ?></span>
                                        <span><?php echo date_i18n('M', $event_date); ?></span>
                                    </div>                                <?php
                                } ?>
                                <div class="lp-event-detail-title-hosted">
                                    <h1><?php echo get_the_title($event_id); ?></h1>
                                    <h2><?php echo esc_html__('Hosted by:', 'listingpro'); ?> <a
                                                href="<?php echo get_permalink($listing_id); ?>"><?php echo get_the_title($listing_id); ?></a>
                                    </h2>
                                    <div class="lp-share-event-detail"><span class="lp-event-share-btn-st"><i
                                                    class="fa fa-share-alt"
                                                    aria-hidden="true"></i> <?php echo esc_html__('Share :', 'listingpro'); ?></span>
                                        <ul class="lp-event-shar-btn-align-title">
                                            <li><a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>"
                                                   target="_blank">
                                                    <!-- Facebook icon by Icons8 --> <i class="fa fa-facebook"></i> </a>
                                            </li>
                                            <li><a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>"
                                                   target="_blank">
                                                    <!-- twitter icon by Icons8 --> <i class="fa-brands fa-square-x-twitter"></i> </a>
                                            </li>
                                            <li><a href="<?php echo listingpro_social_sharing_buttons('linkedin'); ?>"
                                                   target="_blank">
                                                    <!-- linkedin icon by Icons8 --> <i class="fa fa-linkedin"></i> </a>
                                            </li>
                                        </ul>
                                    </div>
										<?php
								if( class_exists('EventPro') ){
								?>
								<div class="post-meta-right-box ep-event-detail-sidebar-lower lp_older_style">
									<?php $favrt = eventpro_is_favourite($event_id); ?>
									<ul class="post-stat">
										<li id="fav-container">
											<a href="javascript:void(0);" class="ep-event-save-btn <?php if ($favrt == 'yes') {
												echo 'ep-remove-fav';
											} else {
												echo 'ep-add-to-fav';
											} ?>" data-event_id="<?php echo esc_attr($event_id); ?>">
											<?php if ($favrt == 'yes') {
												echo 'saved';
											} else {
												echo 'save';
											} ?>
											
												<i class="<?php if ($favrt == 'yes') {
													echo 'fa fa-bookmark';
												} else {
													echo 'fa-regular fa-bookmark-o';
												} ?>" aria-hidden="true"></i>
											</a>
										</li>
									</ul>
								</div>
								<?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lp-event-detail-side-section">
                                <ul>
                                    <li>
                                        <div id="singlepostmap" class="lp-event-detail-map singlebigmaptrigger"
                                             data-lat="<?php echo esc_attr($event_lat); ?>" data-lan="<?php echo esc_attr($event_lon); ?>"
                                             data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>
                                    </li> <?php if (!empty($event_loc)) { ?>
                                        <li><h3><i class="fa fa-map-marker" aria-hidden="true"></i>
                                                <span><?php echo esc_attr($event_loc); ?></span>
                                            </h3> <?php $event_loc = preg_replace('/\s+/', '+', $event_loc);
                                            $dirURL = "https://maps.google.com?daddr=$event_loc"; ?> <a target="_blank" href="<?php echo esc_url($dirURL); ?>"><?php echo esc_html__('Get Direction', 'listingpro'); ?></a>
                                        </li>                                    
                                    <?php
                                    } ?>
                                    <?php if (!empty($event_time)) { ?>
                                        <li><h3><i class="fa fa-clock-o"
                                                   aria-hidden="true"></i><span><?php echo esc_attr($event_time); ?><?php echo esc_html__('-', 'listingpro'); ?><?php echo date_i18n('l', $event_date); ?></span>
                                            </h3> <?php if (!empty($event_date)) { ?>
                                                <span><?php echo date_i18n(get_option('date_format'), $event_date); ?>                                    </span>
                                            <?php
                                            } ?>
                                        </li>
                                        <li class="lp-event-detail-end-time-outer"><h3><i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <span>
                                                <?php
                                                if(!empty($event_date_end)) {
                                                    echo esc_attr($event_time_end); echo esc_html__('-', 'listingpro');
                                                }
                                                if(!empty($event_date_end)) {
                                                    echo '<span>'.date_i18n('l', $event_date_end).'</span>';
                                                }
                                                ?>
                                            </span>
                                        </h3> <?php if (!empty($event_date_end)) { ?>
                                            <span><?php echo date_i18n(get_option('date_format'), $event_date_end); ?>                                    </span>                                            <?php
                                        } ?>
                                        </li><?php
                                    } ?>                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lp-event-detail-white-bg-section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8">                            <?php if (!empty($event_object)) { ?>
                                <div class="lp-event-detail-content"><p><?php echo wp_kses_post($event_object->post_content); ?></p>
                                </div>                            <?php
                            } ?>
                            <div class="lp-event-viewall-attende">                                <?php if (!isset($event_utilities['guests']) || $event_utilities['guests'] == 'yes') { ?>
                                    <h4><?php echo esc_html__('Attendees', 'listingpro'); ?><?php if (!isset($event_utilities['counter']) || $event_utilities['counter'] == 'yes'): ?><?php echo esc_html__('(', 'listingpro'); ?><?php echo esc_attr($attendies_count); ?><?php echo esc_html__(')', 'listingpro'); ?><?php
                                        endif; ?>                                                                </h4>                            <?php
                                } ?>                            </div>
                            <div class="lp-event-detail-attendes-section">                                <?php if (isset($attending_users)) {
                                    $attendenumber = 0;
                                    if (is_array($attending_users)){
                                        $attendenumber = count($attending_users);
                                    }

                                    ?>
                                    <ul>                                        <?php $count = 1;
                                        if (!empty($attending_users)) {
                                            foreach ($attending_users as $val) {
                                                if (!empty($val)) {
                                                    $user_attende = get_userdata($val);
                                                    $author_avatar_url = get_user_meta($user_attende->ID, "listingpro_author_img_url", true);
                                                    $author_url = get_author_posts_url(get_the_author_meta($user_attende->ID));
                                                    $avatar = '';
                                                    if (!empty($author_avatar_url)) {
                                                        $avatar = $author_avatar_url;
                                                    } else {
                                                        $avatar_url = listingpro_get_avatar_url($user_attende->ID, $size = '90');
                                                        $avatar = $avatar_url;
                                                    }
                                                    ?>
                                                    <li>
                                                        <div class="attende-avtar">
                                                            <img src="<?php echo esc_url($avatar); ?>"
                                                                 alt="<?php echo esc_attr($user_attende->user_nicename); ?>">
                                                        </div>
                                                        <span><?php echo esc_attr($user_attende->user_nicename); ?></span>
                                                    </li>                                                    <?php if ($count == 20) break;
                                                    $count++;
                                                }
                                            }
                                        } ?>
                                    </ul>                                    <?php if ($attendenumber > 20) { ?>
                                        <ul class="lp-attende-extra">                                            <?php $count = 1;
                                            foreach ($attending_users as $val) {
                                                if ($count > 20) {
                                                    $user_attende = get_userdata($val);
                                                    $author_avatar_url = get_user_meta($user_attende->ID, "listingpro_author_img_url", true);
                                                    $author_url = get_author_posts_url(get_the_author_meta($user_attende->ID));
                                                    $avatar = '';
                                                    if (!empty($author_avatar_url)) {
                                                        $avatar = $author_avatar_url;
                                                    } else {
                                                        $avatar_url = listingpro_get_avatar_url($user_attende->ID, $size = '90');
                                                        $avatar = $avatar_url;
                                                    }
                                                    ?>
                                                    <li>
                                                        <div class="attende-avtar"><img src="<?php echo esc_url($avatar); ?>"
                                                                                        alt="<?php echo esc_attr($user_attende->user_nicename); ?>">
                                                        </div>
                                                        <span><?php echo esc_attr($user_attende->user_nicename); ?></span>
                                                    </li>                                                    <?php
                                                }
                                                $count++;
                                            } ?>                                        </ul>
                                        <div class="lp-event-attende-view-all"><span
                                                    data-contract="<?php echo esc_html__('View Less', 'listingpro'); ?>"
                                                    data-expand="<?php echo esc_html__('View More', 'listingpro'); ?>"
                                                    class="lp-event-view-less"><?php echo esc_html__('View More', 'listingpro'); ?></span>
                                        </div>                                    <?php
                                    } ?><?php
                                } ?>                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lp-event-detail-sidebar-area">                             <?php if (!isset($event_utilities['guests']) || $event_utilities['guests'] == 'yes') { ?>
                                    <div class="event-total-going"><span><strong><?php echo esc_html__('Are You going ?', 'listingpro'); ?></strong> <?php if (!isset($event_utilities['counter']) || $event_utilities['counter'] == 'yes'): ?><?php echo esc_attr($attendies_count); ?><?php echo esc_html__('going', 'listingpro'); ?><?php
                                            endif; ?>                                                                        </span>
                                    </div>
                                    <div class="lp-detail-event-going-btn"><input type="hidden" class="going-text"
                                                                                  value="<?php echo esc_html__('Yes! i am going', 'listingpro'); ?>">
                                        <input type="hidden" class="not-going-text"
                                               value="<?php echo esc_html__('I am not going', 'listingpro'); ?>"> <?php if (!isset($event_utilities['guests']) || $event_utilities['guests'] == 'yes') { ?><?php if (is_user_logged_in()):
                                            if (is_array($attending_users) && in_array($user_id, $attending_users)): ?>
                                                <button type="button" data-event="<?php echo esc_attr($event_id); ?>"
                                                        data-uid="<?php echo esc_attr($user_id); ?>"
                                                        class="attend-event not-going"><?php echo esc_html__('I am not going', 'listingpro'); ?></button>                                        <?php
                                            else: ?>
                                                <button type="button" class="attend-event"
                                                        data-event="<?php echo esc_attr($event_id); ?>"
                                                        data-uid="<?php echo esc_attr($user_id); ?>"><?php echo esc_html__('Yes! i am going', 'listingpro'); ?></button>                                        <?php
                                            endif; ?><?php
                                        else: ?>
                                            <button type="button" class="header-login-btn md-trigger app-view-popup-style"
                                                    data-modal="modal-3"><?php echo esc_html__('Yes! i am going', 'listingpro'); ?></button>                                    <?php
                                        endif; ?><?php
                                        } ?>                                    <?php if (!empty($event_ticket_url)):
                                            $tUrl_arr = explode(',', $event_ticket_url);
                                            foreach ($tUrl_arr as $item) {
                                                $item_arr = explode('|', $item);
                                                if (isset($item_arr[0]) && isset($item_arr[1])) { ?>
                                                    <a target="_blank" href="<?php echo esc_url($item_arr[1]); ?>"
                                                       class="lp-event-detail-ticket"><i class="fa fa-tag"
                                                                                         aria-hidden="true"></i><?php echo esc_html__('Get Tickets', 'listingpro'); ?> <?php echo sprintf(esc_html__('%s', 'listingpro'), $item_arr[0]); ?>
                                                    </a>                                                <?php
                                                }
                                            }
                                        endif; ?>
                                    </div>                                                            <?php
                                } ?>
                            </div> <?php if (is_active_sidebar('listing_archive_sidebar')) { ?>
                                <div class="lp-event-detail-dynamic-sidebar">                                    <?php dynamic_sidebar('listing_archive_sidebar'); ?>                                </div>                                <?php
                            } ?>                        </div>
                    </div>
                </div>
            </div>
        </section>        <?php
    }
} //end while}
?>

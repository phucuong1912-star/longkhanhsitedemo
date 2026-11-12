<?php
$currentUserID = get_current_user_id();
$lpAllMessges = get_user_meta($currentUserID, 'lead_messages', true);
//$adminAvatar = listingpro_get_avatar_url($currentUserID, $size = '94');
$adminAvatar = listingpro_author_image();
$leadAvatar = '';
global $current_user;
$loadingIMG = get_template_directory_uri() . '/assets/images/lp-loading.gif';
if (empty($lpAllMessges)) {
?>
    <div class="lp-blank-section">
        <div class="col-md-12 blank-left-side">
            <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
            <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
            <p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. You will see messages here when user will contact you through lead form.', 'listingpro'); ?></p>
        </div>

    </div>
<?php
} else {
?>
    <div class="user-recent-listings-inner tab-pane fade in active" id="inbox">
        <?php

        $latestLead = get_user_meta($currentUserID, 'latest_lead', true);
        $latestLeadArray = array();
        $latestRepliesArray = array();
        $post_id = '';
        $listing_title = '';
        $listing_url = '';
        $listing_data = '';
        $lead_mail = '';
        $name = '';
        $phone = '';
        $extras = array();
        $times = array();
        $replytimes = array();
        $messages = array();
        $replymessages = array();

        if (!empty($latestLead)) {
            foreach ($latestLead as $key => $val) {
                $post_id = $key;
                $lead_mail = $val;
                $listing_title = get_the_title($post_id);
                $listing_url = get_the_permalink($post_id);
                $listing_data = "<a href='$listing_url' class='listingDtlurl'>$listing_title</a>";
            }
            if (!empty($lpAllMessges)) {
                if (isset($lpAllMessges[$post_id][$lead_mail])) {
                    $latestLeadArray = $lpAllMessges[$post_id][$lead_mail]['leads'];
                    if (array_key_exists("replies", $lpAllMessges[$post_id][$lead_mail])) {
                        $latestRepliesArray = $lpAllMessges[$post_id][$lead_mail]['replies'];
                    }

                    $name = $latestLeadArray['name'];
                    $phone = $latestLeadArray['phone'];
                    $times = $latestLeadArray['time'];
                    $latestMessages = $latestLeadArray['message'];
                    $extras = $latestLeadArray['extras'];
                    if (!empty($latestRepliesArray)) {
                        $replytimes = $latestRepliesArray['time'];
                        if (!empty($replytimes)) {
                            if (is_array($replytimes)) {
                                $replytimes = array_reverse($replytimes);
                            }
                        }
                        $replymessages = $latestRepliesArray['message'];
                    }
                }
            }
        } ?>
        <div class="col-md-3 padding-0 background-white lpinboxrightpart">
            <div class="lp-sender-info text-center background-white">
                <?php
                $leadUID = '';
                if (email_exists($lead_mail)) {
                    $leadUser = get_user_by('email', $lead_mail);
                    $leadUID = $leadUser->ID;
                    $author_avatar_url = get_user_meta($leadUID, "listingpro_author_img_url", true);
                    if (!empty($author_avatar_url)) {
                        $leadAvatar =   $author_avatar_url;
                    } else {
                        $leadAvatar = listingpro_get_avatar_url($leadUID, $size = '94');
                    }
                } else {
                    $leadAvatar = listingpro_icons_url('lp_def_author');
                }
                ?>
                <div class="lp-sender-image">
                    <img alt="image" src="<?php echo ($leadAvatar); ?>">
                </div>
                <h6><?php echo esc_attr($name); ?></h6>
                <?php
                if (email_exists($lead_mail)) {
                ?>
                    <p><?php echo esc_html__('Registered User', 'listingpro'); ?></p>
                <?php
                } else {
                ?>
                    <p><?php echo esc_html__('Unregistered User', 'listingpro'); ?></p>
                <?php
                }
                ?>
                <!--<p>Melbourne, Au</p>-->
            </div>
            <div class="lp-ad-click-outer">

                <div class="lp-general-section-title-outer">
                    <p class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Details', 'listingpro'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">

                        <ul class="lp-invoices-all-stats clearfix">
                            <li>
                                <h5><?php echo esc_html__('Listing : ', 'listingpro'); ?> <span><?php echo wp_kses_post($listing_data); ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Email : ', 'listingpro'); ?> <span><?php echo esc_attr($lead_mail); ?></span></h5>
                            </li>
                            <?php if (!empty($phone)): ?>
                                <li>
                                    <h5><?php echo esc_html__('Phone : ', 'listingpro'); ?> <span><?php echo esc_attr($phone); ?></span></h5>
                                </li>
                            <?php endif; ?>

                            <?php
                            if (!empty($extras)) {
                                foreach ($extras as $key => $singleEtr) {
                                    if (!empty($key) && !empty($singleEtr)) {
                            ?>
                                        <li>
                                            <h5><?php echo esc_attr($key); ?> : <span><?php echo esc_attr($singleEtr); ?></span></h5>
                                        </li>
                            <?php
                                    }
                                }
                            }
                            ?>

                        </ul>

                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-3 padding-0 lp-inbox-left-sec">
            <div class="lp-read-messages">
                <?php
                $hasMessages = false;
                if (!empty($lpAllMessges)) {
                    foreach ($lpAllMessges as $key => $singleListingArray) {
                        if (!empty($singleListingArray)) {
                            foreach ($singleListingArray as $emailkey => $singleUserLeads) {
                                $status = $singleUserLeads['status'];
                                $lead_mail  =   $emailkey;
                                $hasMessages = true;
                                $leads  =   array();
                                $name   =   '';
                                $phone  =   '';
                                $latestTime =   '';
                                $latestMessge   =   '';

                                //                                $leads = $singleUserLeads['leads'];
                                //                                $name = $leads['name'];
                                //                                $phone = $leads['phone'];
                                //                                $timess = $leads['time'];
                                //                                $latestTime = end($timess);
                                //                                $messages = $leads['message'];
                                //                                $latestMessge = end($messages);

                                if (isset($singleUserLeads['leads'])) {
                                    $leads = $singleUserLeads['leads'];
                                    $name = $leads['name'];
                                    $phone = $leads['phone'];
                                    $timess = $leads['time'];
                                    $latestTime = end($timess);
                                    $messages = $leads['message'];
                                    $latestMessge = end($messages);
                                }


                                $datatts = '';
                                $datatts = "data-listingid='$key'";
                                $datatts .= " data-email='$emailkey'";
                                $datatts .= " data-loader='$loadingIMG'";

                                $activeClass = $status . ' ';
                                if ($key == $post_id && $emailkey == $lead_mail) {
                                    $activeClass .= 'active';
                                }


                ?>
                                <div class="lp-read-message-inner <?php echo esc_attr($activeClass); ?>" <?php echo wp_kses_post($datatts); ?>>
                                    <h5><?php echo substr($latestMessge, 0, 35) . '...'; ?></h5>
                                    <p class="clearfix">
                                        <?php
                                        $leadUID = '';
                                        if (email_exists($lead_mail)) {
                                            $leadUser = get_user_by('email', $lead_mail);
                                            $leadUID = $leadUser->ID;
                                            $author_avatar_url = get_user_meta($leadUID, "listingpro_author_img_url", true);
                                            if (!empty($author_avatar_url)) {
                                                $leadAvatar =   $author_avatar_url;
                                            } else {
                                                $leadAvatar = listingpro_get_avatar_url($leadUID, $size = '94');
                                            }
                                        } else {
                                            $leadAvatar = listingpro_icons_url('lp_def_author');
                                        }
                                        ?>
                                        <span class="lp-sender-image">
                                            <img alt="image" src="<?php echo ($leadAvatar); ?>">
                                        </span>


                                        <span><?php echo esc_attr($name); ?></span>
                                        <span class="lp-inbox-date"><?php echo date_i18n(get_option('date_format'), strtotime($latestTime)); ?></span>
                                    </p>
                                </div>

                    <?php


                            }
                        }
                    }
                }
                if (empty($hasMessages)) {
                    ?>
                    <div class="lp-read-message-inner"><?php echo esc_html__('There is no replied message', 'listingpro'); ?></div>
                <?php
                }
                ?>
            </div>

        </div>
        <div class="col-md-6 lp-inbox-outer lpinboxmiddlepart">

            <!-- box for internal messages -->
            <div class="row">
                <div class="lp-message-title clearfix text-right">
                    <?php
                    if (!empty($messages)) {
                        if (lp_theme_option('inbox_msg_del') == true) { ?>
                            <button type="button" data-emailid="<?php echo esc_attr($lead_mail); ?>" data-listingid="<?php echo esc_attr($post_id); ?>" class="btn lp-delte-conv"><i class="fa fa-trash" aria-hidden="true"></i> <?php echo esc_html__('Delete', 'listingpro'); ?></button>
                            <span class="lploadingwrap"><i class="lpthisloading fa fa-spinner fa-spin"></i></span>

                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="lp_all_messages_box clearfix">
                <?php

                if (!empty($latestMessages)) {

                    $leadUID = '';
                    if (email_exists($lead_mail)) {
                        $leadUser = get_user_by('email', $lead_mail);
                        $leadUID = $leadUser->ID;
                        $author_avatar_url = get_user_meta($leadUID, "listingpro_author_img_url", true);
                        if (!empty($author_avatar_url)) {
                            $leadAvatar =   $author_avatar_url;
                        } else {
                            $leadAvatar = listingpro_get_avatar_url($leadUID, $size = '94');
                        }
                    } else {
                        $leadAvatar = listingpro_icons_url('lp_def_author');
                    }
                    echo '<div  class="lpsinglemsgbox clearfix">';
                    echo '<div class="lpsinglemsgbox-inner">';
                    /* leads */

                    $msgCount = 1;
                    $outputtMSG = null;
                    $all_messages = $latestMessages + $replymessages;
                    ksort($all_messages);
                    foreach ($all_messages as $key => $singlemessage) {
                        //messages
                        if (isset($latestMessages[$key])) {
                            echo '<div class="lpQest-outer">';
                            echo '<div class="lpQest-img-outer">';
                            echo '<div class="lpQest-image"><img src="' . $leadAvatar . '"></div>';
                            echo '<p>' . $name . '</p>';
                            echo '</div>';
                            echo '<div class="lpQest"><div></div><div class="lp-sec-div"></div><p>' . $singlemessage . '</p></div>';
                            if (isset($times[$key])) {
                                echo '<div class="lpQestdate"><p>' . date_i18n(get_option("date_format"), strtotime($times[$key])) . '</p></div>';
                            }
                            echo '</div>';
                        }
                        /* replies */

                        if (!empty($replymessages)) {

                            if (isset($replymessages[$key])) {
                                echo '<div class="lpQest-outer lpreplyQest-outer">';

                                echo '<div class="lpQest"><div></div><div class="lp-sec-div"></div><p>' . $replymessages[$key] . '</p></div>';

                                echo '<div class="lpQest-img-outer">';
                                echo '<div class="lpQest-image"><img src="' . $adminAvatar . '"></div>';
                                echo '<p>' . $current_user->user_login . '</p>';
                                echo '</div>';
                                if (isset($replytimes[$key])) {
                                    echo '<div class="lpQestdate"><p>' . date_i18n(get_option("date_format"), strtotime($replytimes[$key])) . '</p></div>';
                                }
                                echo '</div>';
                                echo PHP_EOL;
                            }
                        }


                        $msgCount++;
                    }


                    echo wp_kses_post($outputtMSG);

                    echo '</div>';


                    echo '</div>';
                ?>
                    <form id="lp_leadReply" name="lp_leadReply" class="lp_leadReply clearfix" method="POST">
                        <textarea class="lp_replylead" name="lp_replylead" placeholder="<?php echo esc_html__('Reply to this', 'listingpro'); ?>" required></textarea>
                        <div class="pos-relative clearfix">
                            <i class="lpthisloading fa fa-spinner fa-spin"></i>
                            <button type="submit" class="lppRocesesp"><?php echo esc_html__('Send message', 'listingpro'); ?></button>
                        </div>
                        <input type="hidden" name="lpleadmail" value="<?php echo esc_attr($lead_mail); ?>">
                        <input type="hidden" name="lp_listing_id" value="<?php echo esc_attr($post_id); ?>">
                    </form>

                <?php
                }
                ?>
            </div>
            <!-- end box for internal messages -->
        </div>


    </div>
<?php } ?>
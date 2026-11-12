<?php

global $listingpro_options;

$lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];
$plan_id = listing_get_metabox_by_ID('Plan_id', get_the_ID());

$menu_show =   'true';

if (!empty($plan_id)) {
    $plan_id = $plan_id;
} else {
    $plan_id = 'none';
}
if ($plan_id != 'none') {
    $menu_show = get_post_meta($plan_id, 'listingproc_plan_menu', true);
}

if ($menu_show == 'false') return false;

if (isset($listingpro_options['menu_dashoard']) && $listingpro_options['menu_dashoard'] == 0) {
    $menu_show =   false;
}
if ($menu_show == false) return false;

?>



<?php

$lp_listing_menus   =   get_post_meta(get_the_ID(), 'lp-listing-menu', true);

if (is_array($lp_listing_menus) && !empty($lp_listing_menus)):
    $menu_placeholder_img = get_template_directory_uri() . '/assets/images/menu-placeholder.jpg';
    require_once(THEME_PATH . "/include/aq_resizer.php");

    $user_id = get_the_author_meta('ID');
    $ordering_services = array();

    $get_ordering_services = get_user_meta($user_id, 'order_services', [0]);
    if (!empty($get_ordering_services)) {
        $ordering_services = $get_ordering_services;
    }
    if (empty($ordering_services) || count($ordering_services) == 0) {
        echo '<h4 class="lp-detail-section-title">' . esc_html__('Menu', 'listingpro') . '</h4>';
    } else {
?>
        <div class="order_food_online_main-header">
            <div class="clearfix"></div>
            <h4 class="lp-detail-section-title pull-left"><?php echo esc_html__('Menu', 'listingpro'); ?></h4>
            <div class="pull-right order_food_online_container">
                <span class="pull-left order_food_online_text"><?php echo esc_html__('order online', 'listingpro'); ?></span>
                <div class="order_food_online_img pull-right">
                    <?php
                    foreach ($ordering_services as $k => $ordering_service) {
                        if ($ordering_service == esc_html__('Grubhub', 'listingpro')) {
                            echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/grubhub.png" alt="image"></a>';
                        } elseif ($ordering_service == esc_html__('Zomato', 'listingpro')) {
                            echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/zomato.png" alt="image"></a>';
                        } elseif ($ordering_service == esc_html__('Foodpanda', 'listingpro')) {
                            echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/food-panda.png" alt="image"></a>';
                        } elseif ($ordering_service == esc_html__('UberEats', 'listingpro')) {
                            echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/uber-eats.png" alt="image"></a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php
    }
    ?>
    <input type="hidden" id="menu-placeholder-image" value="<?php echo esc_attr($menu_placeholder_img); ?>">
    <input type="hidden" id="menu-chili-image" value="<?php echo get_template_directory_uri() . '/assets/images/chilli.png'; ?>">
    <div class="lp-listing-menuu-wrap" id="lp-listing-menuu-wrap">

        <div class="lp-listing-menuu lp-listing-menuu-slider">

            <?php

            foreach ($lp_listing_menus as $menu_type => $lp_listing_menu):

            ?>

                <div class="lp-listing-menuu-slide">
                    <div class="lp-listing-menu-top">
                        <div class="backg_overlay"></div>
                        <span class="lp-listing-menu-top-content"> <?php echo esc_attr($menu_type); ?></span>

                    </div>


                    <?php

                    echo '<div class="lp-popular-menu-outer clearfix">';
                    $loop_Counter = 0;
                    $title_Loop_Cou = 0;
                    $lp_listing_menu_S = $lp_listing_menu;

                    foreach ($lp_listing_menu_S as $kkkk => $forImgLink) {
                        shuffle($forImgLink);
                        foreach ($forImgLink as $kkkkk => $forImgLinkfstep) {
                            $isPopular = '';
                            if (isset($forImgLinkfstep['popularItem'])) {
                                $isPopular = $forImgLinkfstep['popularItem'];
                            }
                            if ($isPopular == 'mItemPopularTrue') {
                                $img_url_full   =   $forImgLinkfstep['mImage'];
                                //                                echo esc_attr( $img_url_full );
                                if ($img_url_full == '') {
                                    $img_url_full = $menu_placeholder_img;
                                }
                                $price = $forImgLinkfstep['mNewPrice'];
                                if ($price == '') {
                                    $price = $forImgLinkfstep['mOldPrice'];
                                }
                                $title_Loop_Cou++;
                                if ($title_Loop_Cou < 2) {
                                    echo '<h6><i class="fa fa-star" aria-hidden="true"></i> ' . esc_html__("Popular", "listingpro") . '</h6>';
                                }
                                $loop_Counter++;
                                if ($loop_Counter < 4) {
                                    $pricetag = '';
                                    if ($price != '') {
                                        $pricetag = '<span class="lp-pop-menu-detail-pr">' . $price . '</span>';
                                    } else {
                                        $pricetag = '<a target="_blank" href="' . $forImgLinkfstep["mQuoteL"] . '" style="display: inline-block;"><span>' . $forImgLinkfstep["mQuoteT"] . '</span></a>';
                                    }
                                    $menu_image_shorten_url = preg_split("/\,/", $img_url_full);
                                    echo '
                                            <div class="col-md-4">
                                                <div class="lp-pop-men-inner">
                                                    <img alt="image" src="' . $menu_image_shorten_url[0] . '" />
                                                    <div class="lp-pop-menu-detail clearfix">
                                                        ' . $pricetag . '
                                                        <span>' . $forImgLinkfstep['mTitle'] . '</span>
                                                    </div>
                                                </div>
                                            </div>
                                        ';
                                }
                            }
                        }
                    }

                    echo '</div>';

                    ?>



                    <div class="lp-listing-menu-list">

                        <div class="lp-listing-menu-items clearfix">

                            <?php

                            foreach ($lp_listing_menu as $menu_group => $listing_menu):

                                $total_menus    =   count($listing_menu);

                            ?>
                                <div class="lp-listing-menu-item-outer col-md-6">

                                    <?php
                                    $menu_counter   =   0;
                                    $title_counter   =   0;
                                    foreach ($listing_menu as $lp_menu):

                                        $isPopular = '';
                                        if (isset($lp_menu['popularItem'])) {
                                            $isPopular = $lp_menu['popularItem'];
                                        }

                                        if ($isPopular != 'mItemPopularTrue') {

                                            $menu_counter++;

                                            $menu_imgs      =   $lp_menu['mImage'];
                                            $img_url_full   =   $menu_imgs;
                                            $menu_images_arr    =   array();
                                            if (strpos($menu_imgs, ',')) {
                                                $menu_images_arr    =   explode(',', $menu_imgs);
                                                $menu_images_arr    =   array_filter($menu_images_arr);
                                                $img_url    =   $menu_images_arr[0];
                                                $img_url_full   =   $menu_images_arr[0];
                                            } else {
                                                $img_url    =   $menu_imgs;
                                            }
                                            if (empty($img_url)) {
                                                $img_url    =   get_template_directory_uri() . '/assets/images/menu-placeholder.jpg';
                                                $img_url_full   =   get_template_directory_uri() . '/assets/images/menu-placeholder.jpg';
                                            } else {
                                                $img_url  = aq_resize($img_url, '65', '65', true, true, true);
                                            }



                                            $title_counter++;
                                            if ($title_counter < 2) {
                                    ?>
                                                <h6><?php echo esc_attr($menu_group); ?></h6>

                                            <?php } ?>

                                            <div class="lp-listing-menu-item <?php if ($menu_counter == $total_menus) {
                                                                                    echo 'last-item';
                                                                                } ?>">

                                                <div class="lp-menu-item-thumb">
                                                    <?php
                                                    if (is_array($menu_images_arr) && count($menu_images_arr) != 0):
                                                    ?>
                                                        <div class="menu-gallery-pop" style="display: none;">
                                                            <?php
                                                            foreach ($menu_images_arr as $value) {
                                                                echo '<a rel="prettyPhoto[mgallery' . $menu_counter . ']" href="' . $value . '"><img alt="image" src="' . $value . '"></a>';
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?php echo esc_attr($img_url_full); ?>" rel="prettyPhoto[mgallery<?php echo esc_attr($menu_counter); ?>]"><img alt="image" src="<?php echo esc_attr($img_url); ?>"></a>

                                                </div>

                                                <div class="lp-menu-item-detail">

                                                    <a <?php if (!empty($lp_menu['mLink'])) : ?>href="<?php echo esc_url($lp_menu['mLink']); ?>"<?php endif; ?> class="lp-menu-item-title"><?php echo esc_attr($lp_menu['mTitle']); ?></a>

                                                    <?php
                                                    if (!empty($lp_menu['mDetail'])):
                                                    ?>
                                                        <span class="help-text">
                                                            <span class="lp-menu-item-tags help"><?php echo html_entity_decode($lp_menu['mDetail']); ?></span>

                                                            <span class="help-tooltip">
                                                                <span><?php echo html_entity_decode($lp_menu['mDetail']); ?></span>
                                                            </span>


                                                        </span>
                                                        <?php
                                                        $spiceLVL = '';
                                                        if (isset($lp_menu['spiceLVL'])) {
                                                            $spiceLVL = $lp_menu['spiceLVL'];
                                                        }
                                                        if ($spiceLVL == 'spicelvl1') {
                                                            echo '
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                    ';
                                                        }
                                                        if ($spiceLVL == 'spicelvl2') {
                                                            echo '
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                    ';
                                                        }
                                                        if ($spiceLVL == 'spicelvl3') {
                                                            echo '
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                    ';
                                                        }
                                                        if ($spiceLVL == 'spicelvl4') {
                                                            echo '
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                        <img class="spice" alt="image" src="' . get_template_directory_uri() . '/assets/images/chilli.png">
                                                    ';
                                                        }
                                                        ?>


                                                    <?php endif; ?>
                                                    <?php if (!empty($lp_menu['orderU'])) {
                                                        $order_img  =   '';
                                                        if (file_exists(get_template_directory() . '/assets/images/' . $lp_menu['orderP'] . '.png')) {
                                                            $order_img  =   '<img alt="image" src="' . get_template_directory_uri() . '/assets/images/' . $lp_menu['orderP'] . '.png">';
                                                        }
                                                    ?>
                                                        <a href="<?php echo esc_attr($lp_menu['orderU']); ?>" target="_blank" class="lp-menu-order"><?php echo esc_attr($order_img); ?><?php echo esc_html__('Order Now', 'listingpro'); ?></a>

                                                    <?php
                                                    }
                                                    ?>

                                                </div>

                                                <div class="lp-menu-item-price">
                                                    <?php
                                                    if (empty($lp_menu['mQuoteT'])):
                                                        $line_through =   '';
                                                        if ($lp_menu['mNewPrice']) {
                                                            $line_through   =   'line-through';
                                                        }
                                                    ?>
                                                        <?php
                                                        if ($lp_menu['mNewPrice']):
                                                        ?>
                                                            <span class="lp-menu-new-price"><?php echo esc_attr($lp_menu['mNewPrice']); ?></span>
                                                        <?php endif; ?>
                                                        <?php
                                                        if ($lp_menu['mOldPrice']):
                                                        ?>
                                                            <span class="old-price <?php echo esc_attr($line_through); ?>"><?php echo esc_attr($lp_menu['mOldPrice']); ?></span>
                                                        <?php endif; ?>

                                                    <?php
                                                    else:
                                                        $quote_url  =   $lp_menu['mQuoteL'];
                                                        if (empty($quote_url) || $quote_url == '#') {
                                                            $quote_url  =   get_home_url();
                                                        }
                                                    ?>
                                                        <a target="_blank" href="<?php echo esc_url($quote_url); ?>"><?php echo esc_attr($lp_menu['mQuoteT']); ?></a>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="clearfix"></div>

                                            </div>

                                    <?php }
                                    endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach;; ?>

        </div>

    </div>

<?php endif; ?>
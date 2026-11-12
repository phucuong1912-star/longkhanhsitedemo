<?php
$lp_detail_slider_styles = $listingpro_options['lp_detail_slider_styles'];
$IDs = get_post_meta($post->ID, 'gallery_image_ids', true);
if ($lp_detail_slider_styles == 'style1') {
    if (!empty($IDs)) {
        if ($gallery_show == "true") {

            $imgIDs = array();
            $numImages = 0;
            $ximgIDs = explode(',', $IDs);
            if (!empty($ximgIDs)) {
                foreach ($ximgIDs as $value) {
                    if (!empty(get_post_type($value)) && get_post_type($value) == 'attachment') {
                        $imgIDs[] = $value;
                    }
                }

                if (!empty($imgIDs)) {
                    $numImages = count($imgIDs);
                }
            }

            if ($numImages >= 1) { ?>
                <div class="pos-relative">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                    <div class="single-page-slider-container style1">
                        <div class="row">
                            <div class="">
                                <div class="listing-slide img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                    <?php
                                    //$imgSize = 'listingpro-gal';
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    $imgSize = 'listingpro-detail_gallery';

                                    foreach ($imgIDs as $imgID) {

                                        if ($numImages == 3) {
                                            $img_url = wp_get_attachment_image_src($imgID, 'full');
                                            $imgurl = aq_resize($img_url[0], '550', '420', true, true, true);
                                            $imgSrc = $imgurl;
                                        } elseif ($numImages == 2) {
                                            $img_url = wp_get_attachment_image_src($imgID, 'full');
                                            $imgurl = aq_resize($img_url[0], '800', '400', true, true, true);
                                            $imgSrc = $imgurl;
                                        } elseif ($numImages == 1) {
                                            $img_url = wp_get_attachment_image_src($imgID, 'full');
                                            $imgurl = aq_resize($img_url[0], '1170', '400', true, true, true);
                                            $imgSrc = $imgurl;
                                        } elseif ($numImages == 4) {
                                            $img_url = wp_get_attachment_image_src($imgID, 'full');
                                            $imgurl = aq_resize($img_url[0], '400', '400', true, true, true);
                                            $imgSrc = $imgurl;
                                        } else {
                                            /* $imgurl = wp_get_attachment_image_src( $imgID, $imgSize);
                                            $imgSrc = $imgurl[0]; */
                                            $img_url = wp_get_attachment_image_src($imgID, 'full');
                                            $imgurl = aq_resize($img_url[0], '350', '450', true, true, true);
                                            $imgSrc = $imgurl;
                                        }
                                        $imgFull = wp_get_attachment_image_src($imgID, 'full');
                                        if (!empty($imgurl[0])) {
                                            echo '
															<div class="slide">
																<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
																	<img src="' . $imgSrc . '" alt="' . get_the_title() . '" />
																</a>
															</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                if (isset($imgIDs[0])) {
                    $imgurl = wp_get_attachment_image_src($imgIDs[0], 'listingpro-listing-gallery');
                    $imgFull = wp_get_attachment_image_src($imgID, 'full');
                    if (!empty($imgurl[0])) {
                        echo '
                        <div class="slide_ban text-center">
                                <a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
                                        <img src="' . $imgurl[0] . '" alt="' . get_the_title() . '" />
                                </a>
                        </div>';
                    }
                }
            }
        }
    }
} else if ($lp_detail_slider_styles == 'style2') {
    if (!empty($IDs)) {
        if ($gallery_show == "true") {
            $imgIDs = explode(',', $IDs);
            $numImages = count($imgIDs);
            if ($numImages >= 1) { ?>
                <div class="pos-relative">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                    <div class="single-page-slider-container style2">
                        <div class="row">
                            <div class="">
                                <div class="listing-slide img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                    <?php
                                    $slider_height = $listingpro_options['slider_height'];
                                    //$imgSize = 'listingpro-gal';
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    $imgSize = 'listingpro-detail_gallery';
                                    foreach ($imgIDs as $imgID) {
                                        $img_url = wp_get_attachment_image_src($imgID, 'full');
                                        $imgSrc = $img_url;
                                        $imgFull = wp_get_attachment_image_src($imgID, 'full');
                                        $gstyle = 'style="height:' . $slider_height . 'px;object-fit: cover"';
                                        if (!empty($img_url[0])) {
                                            echo '
															<div class="slide">
																<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
																	<img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
																</a>
															</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                $imgurl = wp_get_attachment_image_src($imgIDs[0], 'listingpro-listing-gallery');
                $imgFull = wp_get_attachment_image_src($imgID, 'full');
                if (!empty($imgurl[0])) {
                    echo '
								<div class="slide_ban text-center">
									<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
										<img src="' . $imgurl[0] . '" alt="' . get_the_title() . '" />
									</a>
								</div>';
                }
            }
        }
    }
} elseif ($lp_detail_slider_styles == 'style3') {
    if (!empty($IDs)) {
        if ($gallery_show == "true") {
            $imgIDs = explode(',', $IDs);
            $numImages = count($imgIDs);
            if ($numImages >= 1) { ?>
                <div class="pos-relative">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                    <div class="single-page-slider-container style2">
                        <div class="row">
                            <div class="">
                                <div class="listing-slide img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                    <?php
                                    $slider_height = 354;
                                    //$imgSize = 'listingpro-gal';
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    $imgSize = 'listingpro-detail_gallery';
                                    foreach ($imgIDs as $imgID) {
                                        $img_url = wp_get_attachment_image_src($imgID, 'full');
                                        $imgSrc = $img_url;
                                        $imgFull = wp_get_attachment_image_src($imgID, 'full');
                                        $gstyle = 'style="height:' . $slider_height . 'px;object-fit: cover"';
                                        if (!empty($img_url[0])) {
                                            echo '
                                                            <div class="slide">
                                                                <a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
                                                                    <img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
                                                                </a>
                                                            </div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                $imgurl = wp_get_attachment_image_src($imgIDs[0], 'listingpro-listing-gallery');
                $imgFull = wp_get_attachment_image_src($imgID, 'full');
                if (!empty($imgurl[0])) {
                    echo '
                                <div class="slide_ban text-center">
                                    <a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
                                        <img src="' . $imgurl[0] . '" alt="' . get_the_title() . '" />
                                    </a>
                                </div>';
                }
            }
        }
    }
} elseif ($lp_detail_slider_styles == 'style4') {
    if (!empty($IDs)) {
        if ($gallery_show == "true") {
            $imgIDs = explode(',', $IDs);
            $numImages = count($imgIDs);
            $itemnum = 1;
            $imagesarr = $set = array();
            for ($i = 1; $i <= $numImages; $i++) {
                if ($itemnum == 1) {
                    $imagesarr[] = $imgIDs[$i - 1];
                } else {
                    $set[] = $imgIDs[$i - 1];
                }
                if ($i % 4 == 0 || ($itemnum > 1 && $numImages == $i)) {
                    $itemnum = 0;
                    $imagesarr[] = $set;
                    $set = array();
                }
                $itemnum++;
            }
            if ($numImages > 4 && !wp_is_mobile()) {
            ?>
                <div class="pos-relative bg-transparent container">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                    <div class="single-page-slider-container style4">
                        <div class="single-page-gallery-slider-masonry  listing-slide ">
                            <?php
                            $slider_height = 354;
                            require_once(THEME_PATH . "/include/aq_resizer.php");
                            $images_count = '0';
                            foreach ($imagesarr as $images) {
                                $add_width_class = '';
                                if (is_array($images)) {
                                    if (count($images) == 3) {
                                        $add_width_class = 'variablewidth-three';
                                    }
                                }
                                echo '<div class="listing-image-slide ' . $add_width_class . '">';

                                if (is_array($images)) {
                                    if (count($images) == 3) {

                                        foreach ($images as $key => $image) {
                                            $slider_height = 165;
                            ?>
                                            <div class="listing-slide-new  img_<?php echo esc_attr($numImages); ?> <?php if ($key == 0) {
                                                                                                                        echo 'col-md-12 main-big-image';
                                                                                                                    } else {
                                                                                                                        echo 'col-md-6';
                                                                                                                    } ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                                <?php
                                                $img_url = wp_get_attachment_image_src($image, 'full');
                                                $imgSrc = $img_url;
                                                $imgFull = wp_get_attachment_image_src($image, 'full');
                                                $gstyle = 'style="height:' . $slider_height . 'px;object-fit: cover"';
                                                if (!empty($img_url[0])) {
                                                    echo '<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
												<img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
											</a>';
                                                }
                                                ?>
                                            </div>
                                        <?php
                                        }
                                    } else {
                                        if (count($images) == 2) {
                                            $slider_height = 165;
                                        }
                                        foreach ($images as $key => $image) {
                                        ?>
                                            <div class="listing-slide-new last-images  img_<?php echo esc_attr($numImages); ?> <?php if ($key == 0) {
                                                                                                                                    echo 'main-big-image-first';
                                                                                                                                } elseif ($key == 1) {
                                                                                                                                    echo 'main-big-image-scnd';
                                                                                                                                } ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                                <?php
                                                $img_url = wp_get_attachment_image_src($image, 'full');
                                                $imgSrc = $img_url;
                                                $imgFull = wp_get_attachment_image_src($image, 'full');
                                                $gstyle = 'style="height:' . $slider_height . 'px;object-fit: cover"';
                                                if (!empty($img_url[0])) {
                                                    echo '<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
												<img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
											</a>';
                                                }
                                                ?>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>

                                <?php } else { ?>
                                    <div class="listing-slide-new detail-image-height-set img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                        <?php
                                        $img_url = wp_get_attachment_image_src($images, 'full');
                                        $imgSrc = $img_url;
                                        $imgFull = wp_get_attachment_image_src($images, 'full');
                                        $gstyle = 'style="object-fit: cover"';
                                        if (!empty($img_url[0])) {
                                            echo '<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
										<img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
									</a>';
                                        }
                                        ?>
                                    </div>
                            <?php }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="pos-relative style4-overflow bg-transparent  container">
                    <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                    <div class="single-page-slider-container style4">
                        <div class="row">
                            <div class="">
                                <div class="listing-slide-style4 img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                    <?php
                                    $slider_height = $listingpro_options['slider_height'];
                                    //$imgSize = 'listingpro-gal';
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    $imgSize = 'listingpro-detail_gallery';
                                    foreach ($imgIDs as $imgID) {
                                        $img_url = wp_get_attachment_image_src($imgID, 'full');
                                        $imgSrc = $img_url;
                                        $imgFull = wp_get_attachment_image_src($imgID, 'full');
                                        $gstyle = 'style="height:' . $slider_height . 'px;object-fit: cover"';
                                        if (!empty($img_url[0])) {
                                            echo '
															<div class="slide">
																<a href="' . $imgFull[0] . '" rel="prettyPhoto[gallery1]">
																	<img ' . $gstyle . ' src="' . $img_url[0] . '" alt="' . get_the_title() . '" />
																</a>
															</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
            }
        }
    }
}
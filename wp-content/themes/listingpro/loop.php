<?php
/* The loop starts here. */
$postGridCount = 0;
global $listingpro_options;
$blog_view = isset($listingpro_options['blog_view']) ? $listingpro_options['blog_view'] : '';
$blog_grid_view = isset($listingpro_options['blog_grid_view']) ? $listingpro_options['blog_grid_view'] : '';
$blog_template = isset($listingpro_options['blog_template']) ? $listingpro_options['blog_template'] : '';
$blog_sidebar_style = isset($listingpro_options['blog_sidebar_style']) ? $listingpro_options['blog_sidebar_style'] : '';
$blog_view_class = '4';
if ($blog_view == 'list_view') {
    $blog_view_class = '12';
} elseif ($blog_view == 'grid_view') {
    if ($blog_template == 'right_sidebar' || $blog_template == 'left_sidebar') {
        $blog_view_class = '6';
    } else {
        $blog_view_class = '4';
    }
}

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $postGridCount++;

        $author_avatar_url = get_user_meta(get_the_author_meta('ID'), "listingpro_author_img_url", true);
        $avatar = '';
        if (!empty($author_avatar_url)) {
            $avatar = $author_avatar_url;
        } else {
            $avatar_url = listingpro_get_avatar_url(get_the_author_meta('ID'), $size = '51');
            $avatar = $avatar_url;
        }
?>
        <?php
        if ($blog_view == 'list_view') {
        ?>
            <div class="col-md-<?php echo esc_attr($blog_view_class); ?> col-sm-<?php echo esc_attr($blog_view_class); ?> new-list-style" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="lp-blog-grid-box">
                    <div class="lp-blog-grid-box-container lp-border">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="lp-blog-grid-box-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            require_once(THEME_PATH . "/include/aq_resizer.php");

                                            if ($blog_view == 'list_view') {
                                                //                                                    the_post_thumbnail('full');
                                                $img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                                $img_url = aq_resize($img_url, '305', '200', true, true, true);
                                                echo '<img src="' . $img_url . '" alt="image">';
                                            } else {
                                                //                                                    the_post_thumbnail('full');
                                                $img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                                $img_url = aq_resize($img_url, '305', '223', true, true, true);
                                                echo '<img src="' . $img_url . '" alt="image">';
                                            }
                                        } else {
                                            if ($blog_view == 'list_view') {
                                                echo '<img src="' . esc_html__('https://placehold.co/305x200', 'listingpro') . '" alt="image">';
                                            } else {
                                                echo '<img src="' . esc_html__('https://placehold.co/600x240', 'listingpro') . '" alt="image">';
                                            }
                                        }
                                        ?>
                                    </a>
                                    <div class="lp-blog-grid-date">
                                        <?php echo get_the_date('d F'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="lp-blog-grid-box-description text-left">
                                    <div class="lp-blog-grid-title">
                                        <h4 class="lp-h4">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                    </div>
                                    <ul class="lp-blog-grid-author">
                                        <li class="category-link">
                                            <?php the_category(' ,'); ?>
                                        </li>
                                        <li>
                                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                                <i class="fa fa-user"></i>
                                                <span><?php the_author(); ?></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php the_permalink(); ?>">
                                                <i class="fa fa-comment-o" aria-hidden="true"></i>
                                                <span><?php comments_number(); ?></span>
                                            </a>
                                        </li>
                                    </ul><!-- ../lp-blog-grid-author -->
                                    <?php the_excerpt(); ?>
                                    <a class="lp-blog-grid-link" href="<?php the_permalink(); ?>"><?php echo esc_html__('Read More', 'listingpro'); ?></a>
                                    <div class="lp-blog-grid-shares">
                                        <span><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                                        <a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a>
                                        <a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } else if ($blog_view == 'list_view2') {
        ?>
            <div class="col-md-12 col-sm-12 grid-style3" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="lp-blog-grid-box">
                    <div class="lp-blog-grid-box-container lp-border">
                        <div class="lp-blog-grid-box-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    if ($blog_view == 'list_view2') {
                                        $img_feat_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                        $img_feat_urll = aq_resize($img_feat_url, '770', '460', true, true, true);
                                        echo '<img src="' . $img_feat_urll . '" alt="' . get_the_title() . '">';
                                    } else {
                                        $img_feat_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                        $img_feat_urll = aq_resize($img_feat_url, '368', '220', true, true, true);
                                        echo '<img src="' . $img_feat_urll . '" alt="' . get_the_title() . '">';
                                    }
                                } else {
                                    if ($blog_view == 'list_view2') {
                                        echo '<img src="' . esc_html__('https://placehold.co/770x460', 'listingpro') . '" alt="image">';
                                    } else {
                                        echo '<img src="' . esc_html__('https://placehold.co/368x220', 'listingpro') . '" alt="image">';
                                    }
                                }
                                ?>
                            </a>
                            <div class="lp-blog-grid-title">
                                <h4 class="lp-h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                            </div>
                            <div class="lp-blog-grid-date">
                                <?php echo get_the_date('d F'); ?>
                            </div>
                        </div>
                        <div class="lp-blog-grid-box-description text-left">
                            <ul class="lp-blog-grid-author">
                                <li class="category-link">
                                    <?php the_category(' ,'); ?>
                                </li>
                                <li>
                                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                        <i class="fa fa-user"></i>
                                        <span><?php the_author(); ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php the_permalink(); ?>">
                                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                                        <span><?php comments_number(); ?></span>
                                    </a>
                                </li>
                            </ul><!-- ../lp-blog-grid-author -->
                            <?php the_excerpt(); ?>
                            <a class="lp-blog-grid-link" href="<?php the_permalink(); ?>"><?php echo esc_html__('Read More', 'listingpro'); ?></a>
                            <div class="lp-blog-grid-shares">
                                <span><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                                <a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                <a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a>
                                <a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($blog_grid_view == 'grid_view_style3' && $blog_view == 'grid_view') {
        ?>
            <div class="col-md-<?php echo esc_attr($blog_view_class); ?> col-sm-<?php echo esc_attr($blog_view_class); ?> grid-style3" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="lp-blog-grid-box">
                    <div class="lp-blog-grid-box-container lp-border">
                        <div class="lp-blog-grid-box-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                                    if ($blog_view == 'list_view') {
                                        $img_feat_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                        $img_feat_urll = aq_resize($img_feat_url, '368', '220', true, true, true);
                                        echo '<img src="' . $img_feat_urll . '" alt="' . get_the_title() . '">';
                                    } else {
                                        $img_feat_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                        $img_feat_urll = aq_resize($img_feat_url, '368', '220', true, true, true);
                                        echo '<img src="' . $img_feat_urll . '" alt="' . get_the_title() . '">';
                                    }
                                } else {
                                    if ($blog_view == 'list_view') {
                                        echo '<img src="' . esc_html__('https://placehold.co/1170x440', 'listingpro') . '" alt="image">';
                                    } else {
                                        echo '<img src="' . esc_html__('https://placehold.co/368x220', 'listingpro') . '" alt="image">';
                                    }
                                }
                                ?>
                            </a>
                            <div class="lp-blog-grid-title">
                                <h4 class="lp-h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                            </div>
                            <div class="lp-blog-grid-date">
                                <?php echo get_the_date('d F'); ?>
                            </div>
                        </div>
                        <div class="lp-blog-grid-box-description text-left">
                            <ul class="lp-blog-grid-author">
                                <li class="category-link">
                                    <?php the_category(' ,'); ?>
                                </li>
                                <li>
                                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                        <i class="fa fa-user"></i>
                                        <span><?php the_author(); ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php the_permalink(); ?>">
                                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                                        <span><?php comments_number(); ?></span>
                                    </a>
                                </li>
                            </ul><!-- ../lp-blog-grid-author -->
                            <?php the_excerpt(); ?>
                            <a class="lp-blog-grid-link" href="<?php the_permalink(); ?>"><?php echo esc_html__('Read More', 'listingpro'); ?></a>
                            <div class="lp-blog-grid-shares">
                                <span><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                                <a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                <a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a>
                                <a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($blog_grid_view == 'grid_view_style1') {
        ?>
            <div class="col-md-<?php echo esc_attr($blog_view_class); ?> col-sm-<?php echo esc_attr($blog_view_class); ?> lp-border-radius-8" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="lp-blog-grid-box">
                    <div class="lp-blog-grid-box-container lp-border lp-border-radius-8">
                        <div class="lp-blog-grid-box-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    if ($blog_view == 'list_view') {
                                        the_post_thumbnail('full');
                                    } else {
                                        the_post_thumbnail('full');
                                    }
                                } else {
                                    if ($blog_view == 'list_view') {
                                        echo '<img src="' . esc_html__('https://placehold.co/1170x440', 'listingpro') . '" alt="image">';
                                    } else {
                                        echo '<img src="' . esc_html__('https://placehold.co/600x240', 'listingpro') . '" alt="image">';
                                    }
                                }
                                ?>
                            </a>
                        </div>
                        <div class="lp-blog-grid-box-description text-center">
                            <div class="lp-blog-user-thumb margin-top-subtract-25">
                                <img class="avatar" src="<?php echo esc_url($avatar); ?>" alt="image">
                            </div>
                            <div class="lp-blog-grid-category">
                                <a href="#">
                                    <!-- New update 2.6.10 -->
                                    <?php the_category(', '); ?>
                                    <!-- End New update 2.6.10 -->
                                </a>
                            </div>
                            <div class="lp-blog-grid-title">
                                <h4 class="lp-h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                            </div>
                            <ul class="lp-blog-grid-author">
                                <li>

                                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                        <i class="fa fa-user"></i>
                                        <span><?php the_author(); ?></span>
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-calendar"></i>
                                    <span><?php the_date(get_option('date_format')); ?></span>
                                </li>
                            </ul><!-- ../lp-blog-grid-author -->
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($blog_grid_view == 'grid_view_style2') { ?>
            <div class="col-md-<?php echo esc_attr($blog_view_class); ?> col-sm-<?php echo esc_attr($blog_view_class); ?>" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class=" lp-blog-grid-box">

                    <div class="lp-blog-grid-box-container lp-blog-grid-box-container-style2 lp-border-radius-8">
                        <div class="lp-blog-grid-box-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    if ($blog_view == 'list_view') {
                                        the_post_thumbnail('full');
                                    } else {
                                        the_post_thumbnail('full');
                                    }
                                } else {
                                    if ($blog_view == 'list_view') {
                                        echo '<img src="' . esc_html__('https://placehold.co/1170x440', 'listingpro') . '" alt="image">';
                                    } else {
                                        echo '<img src="' . esc_html__('https://placehold.co/600x240', 'listingpro') . '" alt="image">';
                                    }
                                }
                                ?>
                            </a>

                        </div>
                        <div class="lp-blog-grid-box-description  lp-border lp-blog-grid-box-description2">
                            <div class="lp-blog-user-thumb margin-top-subtract-25">
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><img class="avatar" src="<?php echo esc_url($avatar); ?>" alt="image"></a>
                            </div>

                            <div class="lp-blog-grid-title">
                                <h4 class="lp-h4">
                                    <a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <p><?php echo substr(strip_tags(get_the_content()), 0, 95); ?>..</p>
                            </div>
                            <ul class="lp-blog-grid-author lp-blog-grid-author2">
                                <li>
                                    <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                    <span><?php the_category(', '); ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-calendar"></i>
                                    <span><?php echo get_the_date(get_option('date_format')); ?></span>
                                </li>
                            </ul><!-- ../lp-blog-grid-author -->
                            <div class="blog-read-more">
                                <a href="<?php echo get_the_permalink(); ?>" class="blog-detail-link"><?php echo esc_html__('Read More', 'listingpro'); ?></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        <?php } else { ?>
            <div class="lp-section-content-container-style3 col-md-<?php echo esc_attr($blog_view_class); ?> col-sm-<?php echo esc_attr($blog_view_class); ?>" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="lp-blog-style3">
                    <div class="lp-blog-grid-box-container">
                        <div class="lp-blog-grid-box-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (has_post_thumbnail()) {
                                    if ($blog_view == 'list_view') {
                                        the_post_thumbnail('full');
                                    } else {
                                        the_post_thumbnail('listingpro-thumb4');
                                    }
                                } else {
                                    if ($blog_view == 'list_view') {
                                        echo '<img src="' . esc_html__('https://placehold.co/1170x440', 'listingpro') . '" alt="image">';
                                    } else {
                                        echo '<img src="' . esc_html__('https://placehold.co/270x270', 'listingpro') . '" alt="image">';
                                    }
                                }
                                ?>
                            </a>
                        </div>
                        <div class="lp-blog-grid-box-description text-center">

                            <div class="lp-blog-grid-title">
                                <h4 class="lp-h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>


    <?php
        if ($blog_view == 'list_view') {
            if ($postGridCount % 3 == 0) {
                echo '<div class="clearfix"></div>';
            }
        } elseif ($blog_view == 'grid_view') {
            if ($blog_template == 'right_sidebar' || $blog_template == 'left_sidebar') {
                if ($postGridCount % 2 == 0) {
                    echo '<div class="clearfix"></div>';
                }
            } else {
                if ($postGridCount % 3 == 0) {
                    echo '<div class="clearfix"></div>';
                }
            }
        }
    } // end while
    echo listingpro_pagination();
} else {
    ?>
    <div class="text-center margin-top-80 margin-bottom-100">
        <h2><?php esc_html_e('No Results', 'listingpro'); ?></h2>
        <p><?php esc_html_e('Sorry! There are no posts matching your search.', 'listingpro'); ?></p>
        <p><?php esc_html_e('Try changing your search Keyword', 'listingpro'); ?>
        </p>
    </div>
<?php
}

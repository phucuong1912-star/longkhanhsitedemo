<?php
global $listingpro_options;
$object_id  =   get_queried_object_id();
$u_data =   get_userdata( $object_id );
$joined =   strtotime( $u_data->user_registered );
$u_name =   '';
if( !empty( $u_data->first_name ) || $u_data->last_name )
{
    $u_name =   $u_data->first_name.' '.$u_data->last_name;
}
else
{
	$u_name =   $u_data->display_name;
}
$listing_counters   =   '';
$report_btn         =   '';
if( isset( $listingpro_options['author_counters'] ) )
{
    $listing_counters   =   $listingpro_options['author_counters'];
}
if( isset( $listingpro_options['report_btn'] ) )
{
    $report_btn   =   $listingpro_options['report_btn'];
}
$author_banner  =   $listingpro_options['author_banner'];
$author_avatar_url = get_user_meta($object_id, "listingpro_author_img_url", true);
$avatar =   '';
if( !empty( $author_avatar_url ) )
{
    $avatar =  $author_avatar_url;
}
else
{
    $avatar_url = listingpro_get_avatar_url ( $object_id, $size = '94' );
    $avatar =  $avatar_url;
}
$author_address    =   get_the_author_meta( 'address', $object_id );
?>
<div class="lp-author-banner">
    <div class="lp-banner-top" style="background-image: url(<?php echo esc_url($author_banner['url']); ?>);">
        <div class="lp-header-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="lp-banner-top-thumb">
                    <img alt='image' src="<?php echo esc_url($avatar); ?>">
                </div>
                <div class="lp-banner-top-detail">
                    <h3><?php echo esc_attr($u_name); ?></h3>
                    <p><i class="fa fa-map-marker-alt"></i> <?php echo esc_attr($author_address); ?></p>
                </div>
                <div class="lp-banner-date">
                    <p><?php echo esc_html__( 'Joined In', 'listingpro' ); ?> <?php echo date_i18n( 'M Y', $joined ); ?></p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="lp-banner-bottom">
        <div class="container">
            <div class="row">
                <?php
                if( isset( $listing_counters ) && ( !empty( $listing_counters ) || $listing_counters != 0 ) ):
                    $counter_listing    =   '';
                    if( isset( $listingpro_options['counter_listing'] ) )
                        $counter_listing  =   $listingpro_options['counter_listing'];
                    $counter_photos =   '';
                    if( isset( $listingpro_options['counter_photos'] ) )
                        $counter_photos =   $listingpro_options['counter_photos'];
                    $counter_reviews    =   '';
                    if( isset( $listingpro_options['counter_reviews'] ) )
                        $counter_reviews    =   $listingpro_options['counter_reviews'];
                ?>
                    <div class="lp-banner-bottom-left">
                        <?php
                        if( $counter_listing == 1 || $counter_photos == 1 || $counter_reviews == 1 ):
                        ?>
                        <ul>
                            <?php
                            if( $counter_reviews == 1 ):
                                $reviews_args   =   array(
                                    'post_type' => 'lp-reviews',
                                    'posts_per_page' => -1,
                                    'author' => $object_id
                                );
                                $reviews_query  =   new WP_Query( $reviews_args );
                                $review_count   =   $reviews_query->post_count;
                            ?>
                            <li class="banner-ratings">
                                <i class="fa fa-star"></i>
                                <strong><?php echo esc_attr($review_count); ?></strong> <span><?php echo esc_html__( 'Reviews Submitted', 'listingpro' ); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php
                            if( $counter_photos == 1 ):
                                $photos_count =   count_user_posts_by_status( 'attachment', 'inherit', $object_id );
                            ?>
                            <li class="banner-photos">
                                <i class="fa fa-camera"></i>
                                <strong><?php echo esc_attr($photos_count); ?></strong> <span><?php echo esc_html__( 'Photos', 'listingpro' ); ?></span>
                            </li>
                            <?php endif;; ?>
                            <?php
                            if( $counter_listing == 1 ):
                                $listings_count =   count_user_posts_by_status( 'listing', 'publish', $object_id );
                            ?>
                            <li class="banner-list">
                                <i class="fa fa-list"></i>
                                <strong><?php echo esc_attr($listings_count); ?></strong> <span><?php echo esc_html__( 'Listing', 'listingpro' ); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                    <?php
                        if( isset( $report_btn ) && ( !empty( $report_btn ) || $report_btn != 0 ) ):
                    ?>
                    <?php endif; ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
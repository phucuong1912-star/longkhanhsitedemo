<?php

$type = 'listing';
$defSquery = null;
global $paged;
$postsonpage = '';

if(isset($listingpro_options['my_listing_per_page'])){
    $postsonpage = $listingpro_options['my_listing_per_page'];
}
else{
    $postsonpage = 9;
}


$args=array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'author' => $GLOBALS['authorID'],
);


$listing_query = null;
$listing_query  =   new WP_Query( $args );

$postsonpage = '';

global $listingpro_options;
if(isset($listingpro_options['my_photos_per_page'])){
    $postsonpage = $listingpro_options['my_photos_per_page'];
}
else{
    $postsonpage = 9;
}



?>

<div class="author-photos-wrap">
    <div class="row">
<?php



if( $listing_query->have_posts() ): while ( $listing_query->have_posts() ): $listing_query->the_post();

$listing_title  =   get_the_title();

$listing_url    =   get_permalink( $post->ID );
$query_images_args = array(
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => 5,
    'author' => $GLOBALS['authorID'],
    'post_parent' => $post->ID
);



$my_query = null;
$my_query = new WP_Query($query_images_args);

?>
        <?php
        if( $my_query->have_posts() ):
            echo '<div class="author-photos-wrap-inner"><h4><a href="'.$listing_url.'"><i class="fa fa-link"></i>'.get_the_title().'</a></h4>';
            require_once (THEME_PATH . "/include/aq_resizer.php");
            while ( $my_query->have_posts() ): $my_query->the_post();
            $img_full  =   wp_get_attachment_url( $post->ID );
            $img_thumb = aq_resize( $img_full, '215', '215', true, true, true);
        ?>

            <div class="col-md-2 author-gallery">
                <a href="<?php echo esc_url($img_full); ?>" rel="prettyPhoto[gallery2]"><img alt='image' src="<?php echo esc_url($img_thumb); ?>" ></a>
            </div>
        <?php
        endwhile; wp_reset_postdata(); echo '<div class="clearfix"></div></div>'; 
        endif;
        ?>
        <?php
        endwhile; wp_reset_postdata(); 
        else :    
            echo '<p>' . esc_html__( 'No Image Found.', 'listingpro' ) . '</p>';
        endif;
        ?>
    </div>
    <?php
    echo listingpro_load_more_filter($listing_query, '1', $defSquery);
    ?>
</div>


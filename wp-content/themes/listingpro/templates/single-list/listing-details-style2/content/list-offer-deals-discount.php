<?php
global $listingpro_options;

$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
$discounts_show =   'true';
if(!empty($plan_id)){
    $plan_id = $plan_id;
}else{
    $plan_id = 'none';
}

if( $plan_id != 'none' )
{
    $discounts_show = get_post_meta( $plan_id, 'listingproc_plan_deals', true );
}
// if( $discounts_show == 'false' ) return false;
if( isset( $listingpro_options['discounts_dashoard'] ) && $listingpro_options['discounts_dashoard'] == 0 )

{
    $discounts_show =   'false';
}

// if( $discounts_show == 'false' ) return false;

$post_author_id = get_post_field( 'post_author', get_the_ID() );
$discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );

if( $discount_displayin == 'content' || empty( $discount_displayin ) )
{
    $DDO_design =   $listingpro_options['discount_deals_offers_design'];
}
else
{
    $DDO_design =   $listingpro_options['discount_deals_offers_design_sidebar'];
}

if( isset( $DDO_design ) && !empty( $DDO_design ) )
{
    $DDO_design =   $DDO_design;
}
else
{
    $DDO_design =   1;
}
echo '<div class="code-overlay"></div>';
$lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];


?>
<div class="tab-pane" id="offers_deals">

<?php
if( $DDO_design == 1 )
{
    get_template_part( 'templates/single-list/listing-details-style2/content/list-deals' );
}
if( $DDO_design == 2 )
{
    get_template_part( 'templates/single-list/listing-details-style2/content/list-offers' );
}
if( $DDO_design == 3 )

{
    get_template_part( 'templates/single-list/listing-details-style2/content/list-discount' );
}
?>
</div>


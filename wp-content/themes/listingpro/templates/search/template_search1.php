<?php
global $listingpro_options;

$search_view = $listingpro_options['search_views'];
$search_layout = $listingpro_options['search_layout'];
$alignment = $listingpro_options['search_alignment'];

$alignClass = '';
if ($alignment == 'align_top') {
    $alignClass = 'lp-align-top';
} elseif ($alignment == 'align_middle') {
    $alignClass = 'lp-align-underBanner';
} elseif ($alignment == 'align_bottom') {
    $alignClass = 'lp-align-bottom';
}

$searchViewClass = '';
if ($search_view == 'light') {
    $searchViewClass = 'lp-bg-white';
} elseif ($search_view == 'dark') {
    $searchViewClass = 'lp-bg-black';
} elseif ($search_view == 'grey') {
    $searchViewClass = 'lp-bg-grey';
}

$searchAlignClass = '';
if ($search_layout == 'boxed') {
    $searchAlignClass = 'container';
} elseif ($search_layout == 'fullwidth') {
    $searchAlignClass = esc_attr($searchViewClass);
}
$lp_what_field_switcher = $listingpro_options['lp_what_field_switcher'];
$search_loc_field_hide = $listingpro_options['lp_location_loc_switcher'];
if ((isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) || (isset($search_loc_field_hide) && $search_loc_field_hide == 0)) {
    if ($alignment == 'align_top' || $alignment == 'align_middle') {
        ?>
        <div class="absolute <?php echo esc_attr($alignClass); ?>">
    <?php } ?>
    <div class="<?php echo esc_attr($searchAlignClass); ?> <?php echo esc_attr($alignClass); ?>">
        <div class="row">
            <div class="lp-search-bar-all-demo lp-bottom-with-map-back <?php echo esc_attr($searchViewClass); ?>">
                <?php if ($search_layout == 'fullwidth') { ?>
                <div class="container">
                    <div class="row">
                        <?php } ?>
                        <div class="lp-search-bar">
                            <?php get_template_part('templates/search/home-search'); ?>
                        </div><!-- ../search-bar -->
                        <?php if ($search_layout == 'fullwidth') { ?>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
    <?php if ($alignment == 'align_top' || $alignment == 'align_middle') {
        ?>
        </div>
    <?php }
} ?>
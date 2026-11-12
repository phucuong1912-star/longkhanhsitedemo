<?php
$lp_features    =   wp_get_post_terms( get_the_ID(), 'features' );
if( is_array( $lp_features ) && !empty( $lp_features ) ):
?>
<div class="container">
    <div class="lp-listing-detail-page-content margin-bottom-70">
        <div class="row">
            <div class="pull-left lp-left-title">
                <h2><?php echo esc_html__('Services', 'listingpro'); ?></h2>
            </div>
            <div class="col-md-12 lp-right-content-box">
                <div class=" lp-detail-services-box" id="detail5-services">
                    <ul class="list-style-none">
                        <?php
                        foreach ( $lp_features as $lp_feature ):
                        ?>
                        <li><a href="<?php echo get_term_link($lp_feature); ?>"><?php echo esc_attr( $lp_feature->name ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
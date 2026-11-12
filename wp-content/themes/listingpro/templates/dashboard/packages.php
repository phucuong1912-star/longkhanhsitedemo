<?php
$currencyCode = listingpro_currency_sign();
?>
<?php
global $listingpro_options;
$currency_position = $listingpro_options['pricingplan_currency_position'];
global $wpdb;
$dbprefix = '';
$post_ids = '';
$dbprefix = $wpdb->prefix;
$user_ID = '';
$user_ID = get_current_user_id();
$results = '';
$resultss = '';
$results = $wpdb->get_results( "SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='success'" );
$resultss = $wpdb->get_results( "SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='expired'" );

$plainID = '';
$plainName = '';
$plainType = '';
$plainDate = '';
$plainExpiry = '';
$plainPrice = '';
$plainUsed = '';
$plainRemains = '';
$plainTID = '';
$pendingListings = 0;
$activeListings = 0;

?>
<?php
if(!empty($results) || !empty($resultss)){
    ?>
    <div class="tab-pane fade in active lp-packages" id="lp-listings">

        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 align-center">

            <div class="panel-heading">
				<h5 class="margin-bottom-20"><?php esc_html_e('All Packages', 'listingpro'); ?></h5>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default" data-toggle="tab"><?php esc_html_e('all','listingpro'); ?></a></li>
                    <li><a href="#tab2default" data-toggle="tab"><?php esc_html_e('active','listingpro'); ?></a></li>
                    <li><a href="#tab3default" data-toggle="tab"><?php esc_html_e('inactive','listingpro'); ?></a></li>

                </ul>
            </div>
            <?php
            if(!empty($results) || !empty($resultss) ){
                ?>
                <div class="panel-body lp-new-packages" id="lp-new-invoices">
                    <div class="lp-main-title clearfix">
                        <div class="col-md-2"><p><?php esc_html_e('type','listingpro'); ?></p></div>
                        <div class="col-md-2"><p><?php esc_html_e('trans id','listingpro'); ?></p></div>
                        <div class="col-md-3"><p><?php esc_html_e('trans date','listingpro'); ?></p></div>
                        <div class="col-md-3"><p><?php esc_html_e('expire on','listingpro'); ?></p></div>
                        <div class="col-md-2 text-center"><p><?php esc_html_e('status','listingpro'); ?></p></div>
                    </div>
                    <div class="tab-content clearfix">
                        <div class="tab-pane fade in active" id="tab1default">
                            <?php
                            /* actives packages */
                            if(!empty($results)){
                                foreach( $results as $info ){
                                    $plainTID = $info->order_id;
                                    $plainID = $info->plan_id;
                                    $plainName = $info->plan_name;
                                    $plainType = $info->plan_type;
                                    if( !empty($currency_position) ){
                                        if( $currency_position=="left" ){
                                            $plainPrice = $currencyCode.$info->price;
                                        }else{
                                            $plainPrice = $info->price.$currencyCode;
                                        }

                                    }else{
                                        $plainPrice = $currencyCode.$info->price;
                                    }

                                    if(!empty($info->used)){
                                        $plainUsed = $info->used;
                                        $post_ids = $info->post_id;
                                    }

                                    $activeListingArray = array();
                                    $activeListingArray = explode(',', $post_ids);

                                    if(!empty($activeListingArray)){
                                        foreach($activeListingArray as $pid){
                                            if(get_post_status( $pid )=="pending"){
                                                $pendingListings++;
                                            }
                                            else if(get_post_status( $pid )=="publish"){
                                                $activeListings++;
                                            }
                                        }
                                    }

                                    $plainDate = $info->date;
                                    if(!empty($plainDate)){
                                        $plainDate = date_i18n(get_option('date_format'), strtotime($plainDate));
                                    }
                                    $days = '';
                                    $totalPosts = '';
                                    $planTIme = get_post_meta( $plainID, 'plan_time', true );
                                    if(!empty($planTIme)){
                                        $days = get_post_meta( $plainID, 'plan_time', true );
                                    }

                                    if(!empty($days)){
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainDate. ' + '.$days.' days'));
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainExpiry));
                                    }
                                    else{
                                        $plainExpiry = 'Unlimited';
                                    }
                                    $planText = get_post_meta( $plainID, 'plan_text', true );
                                    if(!empty($planText)){
                                        $totalPosts = get_post_meta( $plainID, 'plan_text', true );
                                        $plainRemains = $totalPosts - $plainUsed;
                                    }
                                    else{
                                        $plainRemains = 'unlimited';
                                        $planText = 'unlimited';
                                    }

                                    ?>
                                    <div class="lp-listing-outer-container clearfix">
                                        <div class="col-md-2">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo wp_kses_post($plainName); ?></p>

                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainTID); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainDate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plainExpiry); ?></p>

                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="lp-invoice-price clerarfix lp-active-plan-btn lp-plane-btn lp-plane-btn-front">

                                                <a> <i class="fa fa-circle" aria-hidden="true"></i> <?php esc_html_e('Active','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                            <?php
                            /* inactive packages */
                            if(!empty($resultss)){
                                foreach( $resultss as $info ){
                                    $plainTID = $info->order_id;
                                    $plainID = $info->plan_id;
                                    $plainName = $info->plan_name;
                                    $plainType = $info->plan_type;
                                    if( !empty($currency_position) ){
                                        if( $currency_position=="left" ){
                                            $plainPrice = $currencyCode.$info->price;
                                        }else{
                                            $plainPrice = $info->price.$currencyCode;
                                        }

                                    }else{
                                        $plainPrice = $currencyCode.$info->price;
                                    }

                                    if(!empty($info->used)){
                                        $plainUsed = $info->used;
                                        $post_ids = $info->post_id;
                                    }

                                    $activeListingArray = array();
                                    $activeListingArray = explode(',', $post_ids);

                                    if(!empty($activeListingArray)){
                                        foreach($activeListingArray as $pid){
                                            if(get_post_status( $pid )=="pending"){
                                                $pendingListings++;
                                            }
                                            else if(get_post_status( $pid )=="publish"){
                                                $activeListings++;
                                            }
                                        }
                                    }

                                    $plainDate = $info->date;
                                    if(!empty($plainDate)){
                                        $plainDate = date_i18n(get_option('date_format'), strtotime($plainDate));
                                    }
                                    $days = '';
                                    $totalPosts = '';
                                    $planTIme = get_post_meta( $plainID, 'plan_time', true );
                                    if(!empty($planTIme)){
                                        $days = get_post_meta( $plainID, 'plan_time', true );
                                    }

                                    if(!empty($days)){
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainDate. ' + '.$days.' days'));
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainExpiry));
                                    }
                                    else{
                                        $plainExpiry = 'Unlimited';
                                    }
                                    $planText = get_post_meta( $plainID, 'plan_text', true );
                                    if(!empty($planText)){
                                        $totalPosts = get_post_meta( $plainID, 'plan_text', true );
                                        $plainRemains = $totalPosts - $plainUsed;
                                    }
                                    else{
                                        $plainRemains = 'unlimited';
                                        $planText = 'unlimited';
                                    }

                                    ?>
                                    <div class="lp-listing-outer-container clearfix">
                                        <div class="col-md-2">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo wp_kses_post($plainName); ?></p>
                                                    
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainTID); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainDate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plainExpiry); ?></p>

                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="lp-invoice-price clerarfix lp-plane-btn lp-plane-btn-front lp-plane-btn-front-inactive">

                                                <a> <i class="fa fa-circle" aria-hidden="true"></i> <?php esc_html_e('Inactive','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>


                        </div>
                        <div class="tab-pane fade" id="tab2default">
                            <?php
                            /* actives */
                            if(!empty($results)){
                                foreach( $results as $info ){
                                    $plainTID = $info->order_id;
                                    $plainID = $info->plan_id;
                                    $plainName = $info->plan_name;
                                    $plainType = $info->plan_type;
                                    if( !empty($currency_position) ){
                                        if( $currency_position=="left" ){
                                            $plainPrice = $currencyCode.$info->price;
                                        }else{
                                            $plainPrice = $info->price.$currencyCode;
                                        }

                                    }else{
                                        $plainPrice = $currencyCode.$info->price;
                                    }

                                    if(!empty($info->used)){
                                        $plainUsed = $info->used;
                                        $post_ids = $info->post_id;
                                    }

                                    $activeListingArray = array();
                                    $activeListingArray = explode(',', $post_ids);

                                    if(!empty($activeListingArray)){
                                        foreach($activeListingArray as $pid){
                                            if(get_post_status( $pid )=="pending"){
                                                $pendingListings++;
                                            }
                                            else if(get_post_status( $pid )=="publish"){
                                                $activeListings++;
                                            }
                                        }
                                    }

                                    $plainDate = $info->date;
                                    if(!empty($plainDate)){
                                        $plainDate = date_i18n(get_option('date_format'), strtotime($plainDate));
                                    }
                                    $days = '';
                                    $totalPosts = '';
                                    $planTIme = get_post_meta( $plainID, 'plan_time', true );
                                    if(!empty($planTIme)){
                                        $days = get_post_meta( $plainID, 'plan_time', true );
                                    }

                                    if(!empty($days)){
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainDate. ' + '.$days.' days'));
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainExpiry));
                                    }
                                    else{
                                        $plainExpiry = 'Unlimited';
                                    }
                                    $planText = get_post_meta( $plainID, 'plan_text', true );
                                    if(!empty($planText)){
                                        $totalPosts = get_post_meta( $plainID, 'plan_text', true );
                                        $plainRemains = $totalPosts - $plainUsed;
                                    }
                                    else{
                                        $plainRemains = 'unlimited';
                                        $planText = 'unlimited';
                                    }

                                    ?>
                                    <div class="lp-listing-outer-container clearfix">
                                        <div class="col-md-2">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo wp_kses_post($plainName); ?></p>
                                                    
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainTID); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainDate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plainExpiry); ?></p>

                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="lp-invoice-price clerarfix lp-active-plan-btn lp-plane-btn lp-plane-btn-front">

                                                <a>  <i class="fa fa-circle" aria-hidden="true"></i>  <?php esc_html_e('Active','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <div class="tab-pane fade" id="tab3default">

                            <?php
                            /* inactive packages */
                            if(!empty($resultss)){
                                foreach( $resultss as $info ){
                                    $plainTID = $info->order_id;
                                    $plainID = $info->plan_id;
                                    $plainName = $info->plan_name;
                                    $plainType = $info->plan_type;
                                    if( !empty($currency_position) ){
                                        if( $currency_position=="left" ){
                                            $plainPrice = $currencyCode.$info->price;
                                        }else{
                                            $plainPrice = $info->price.$currencyCode;
                                        }

                                    }else{
                                        $plainPrice = $currencyCode.$info->price;
                                    }

                                    if(!empty($info->used)){
                                        $plainUsed = $info->used;
                                        $post_ids = $info->post_id;
                                    }

                                    $activeListingArray = array();
                                    $activeListingArray = explode(',', $post_ids);

                                    if(!empty($activeListingArray)){
                                        foreach($activeListingArray as $pid){
                                            if(get_post_status( $pid )=="pending"){
                                                $pendingListings++;
                                            }
                                            else if(get_post_status( $pid )=="publish"){
                                                $activeListings++;
                                            }
                                        }
                                    }

                                    $plainDate = $info->date;
                                    if(!empty($plainDate)){
                                        $plainDate = date_i18n(get_option('date_format'), strtotime($plainDate));
                                    }
                                    $days = '';
                                    $totalPosts = '';
                                    $planTIme = get_post_meta( $plainID, 'plan_time', true );
                                    if(!empty($planTIme)){
                                        $days = get_post_meta( $plainID, 'plan_time', true );
                                    }

                                    if(!empty($days)){
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainDate. ' + '.$days.' days'));
                                        $plainExpiry = date_i18n(get_option('date_format'), strtotime($plainExpiry));
                                    }
                                    else{
                                        $plainExpiry = 'Unlimited';
                                    }
                                    $planText = get_post_meta( $plainID, 'plan_text', true );
                                    if(!empty($planText)){
                                        $totalPosts = get_post_meta( $plainID, 'plan_text', true );
                                        $plainRemains = $totalPosts - $plainUsed;
                                    }
                                    else{
                                        $plainRemains = 'unlimited';
                                        $planText = 'unlimited';
                                    }

                                    ?>
                                    <div class="lp-listing-outer-container clearfix">
                                        <div class="col-md-2">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo wp_kses_post($plainName); ?></p>
                                                   
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainTID); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($plainDate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plainExpiry); ?></p>

                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="lp-invoice-price clerarfix lp-plane-btn lp-plane-btn-front lp-plane-btn-front-inactive">

                                                <a> <?php esc_html_e('Inactive','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

        </div>


    </div>
    <?php
}else{
    ?>
    <div class="lp-blank-section">
        <div class="col-md-12 blank-left-side">
            <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
            <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
        </div>

    </div>
    <?php
}
?>	
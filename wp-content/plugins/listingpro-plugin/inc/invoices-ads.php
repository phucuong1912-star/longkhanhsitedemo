<?php

require_once(ABSPATH . 'wp-admin/includes/screen.php');
/* =========================form action for wire process============================= */

if( !empty($_POST['payment_submit']) && isset($_POST['payment_submit']) ){
    if (!isset($_SESSION)) { session_start(); }
    global $wpdb,$listingpro_options;
    

    $table = 'listing_campaigns';
    $order_id = $_POST['order_id'];
    $mode = $_POST['mode'];
    $duration = $_POST['duration'];
    $budget = $_POST['budget'];
	$price = $budget;
	$pricetotal = $budget;
	
	$postid= $_POST['post_id'];
    $price_packages = listing_get_metabox_by_ID('listings_ads_purchase_packages', $postid);
    $adsType = listing_get_metabox_by_ID('adsType', $postid);
    $ads_duration = listing_get_metabox_by_ID('ads_duration', $postid);
	$tID = $order_id;
	$token = $order_id;
	$payment_method = 'wire';
	$status = 'success';
	$listing_id = $postid;
	
	$taxPrice = 0;
				if(lp_theme_option('lp_tax_swtich')=="1"){
					$taxrate = lp_theme_option('lp_tax_amount');
					if(!empty($taxrate)){
						if($adsType=="byduration"){
							//byduration
							foreach($price_packages as $pkg){
								$pdPrice = lp_theme_option($pkg);
								if(!empty($pdPrice)){
									$pecentPrice = ($taxrate/100)*$pdPrice;
									$pecentPrice = $pecentPrice*$ads_duration;
									$taxPrice = $taxPrice+$pecentPrice;
								}
							}
							// if(!empty($taxPrice)){
							// 	$budget = $budget - $taxPrice;
							// }
							
						}else{
							$taxPrice = ($budget/100)*$taxrate;
							// $budget = $budget - $taxPrice;
						}
					}
				}
	
	
	lp_save_campaign_data($price_packages, $tID, $payment_method, $token, $status, $price, $budget, $listing_id, $adsType, $ads_duration,$pricetotal,$taxPrice);
    $data = array('ad_date' => date(get_option('date_format')));
    $where = array('transaction_id' => $tID);
    lp_update_data_in_db($table, $data, $where);
	
}

/* --------------------delete invoice data------------------- */
if( isset($_POST['delete_invoice_ads']) && !empty($_POST['delete_invoice_ads']) ){

    $main_id = $_POST['main_id'];
    $listId = $_POST['listId'];
	$delteAll = false;
	if(isset($_POST['deletecomplete'])){
		if(!empty($_POST['deletecomplete'])){
			$delteAll = true;
		}
	}
    $listId = listing_get_metabox_by_ID('ads_listing', $listId);
    if(empty($listId)){
        $listId = $_POST['listId'];
    }
    if( !empty($main_id) ){
        $table = 'listing_campaigns';
        $where = array('main_id'=>$main_id);
        lp_delete_data_in_db($table, $where);
		
		if(!empty($delteAll)){
			delete_post_meta( $listId, 'campaign_status');
			$price_packages = array('lp_random_ads', 'lp_detail_page_ads', 'lp_top_in_search_page_ads');
			if( !empty($price_packages) ){
				foreach( $price_packages as $val ){
					delete_post_meta( $listId, $val);
				}
			}
		}

    }

}

/* =========================inovices for ads========================================= */
add_action('admin_menu', 'lp_register_ads_invoice_page');

if(!function_exists('lp_register_ads_invoice_page')) {
    function lp_register_ads_invoice_page() {
        add_submenu_page(
            'lp-listings-invoices',
            'Ads Invoices',
            'Ads Invoices',
            'manage_options',
            'ads-invoices-page',
            'ads_invoices_submenu_page_callback' );
    }
}

if(!function_exists('ads_invoices_submenu_page_callback')) {
    function ads_invoices_submenu_page_callback() {

        global $wpdb;
        $dbprefix = $wpdb->prefix;
        $table = 'listing_campaigns';
        $table_name =$dbprefix.$table;
        ?>


        <div class="wrap listingpro-coupons linvoiceswrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Ads Invoices', 'listingpro-plugin');  ?></h1>


            <div class="clearfix"></div>

            <div class="tablenav top">

                <div class="alignleft actions bulkactions lp_backend_inv_filter_ads">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php echo esc_html__('All Types', 'listingpro-plugin'); ?></label>
                    <select class="lp_invoiceInput_ads">
                        <option value="">
                            <?php echo esc_html__('All Methods', 'listingpro-plugin'); ?>
                        </option>

                        <option value="paypal">
                            <?php echo esc_html__('Paypal', 'listingpro-plugin'); ?>
                        </option>

                        <option value="stripe">
                            <?php echo esc_html__('Stripe', 'listingpro-plugin'); ?>
                        </option>

                        <option value="wire">
                            <?php echo esc_html__('Wire', 'listingpro-plugin'); ?>
                        </option>
                        <?php do_action('lp_invoices_filter') ;?>
                    </select>

                </div>

                <div class="alignleft actions bulkactions lp_backend_inv_filter_ads">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php echo esc_html__('All Status', 'listingpro-plugin'); ?></label>
                    <select  class="lp_invoiceStatusInput_ads" id="bulk-action-selector-top">
                        <option value="">
                            <?php echo esc_html__('All Status', 'listingpro-plugin'); ?>
                        </option>

                        <option value="success">
                            <?php echo esc_html__('Success', 'listingpro-plugin'); ?>
                        </option>

                        <option value="pending">
                            <?php echo esc_html__('Pending', 'listingpro-plugin'); ?>
                        </option>

                        <option value="failed">
                            <?php echo esc_html__('Failed', 'listingpro-plugin'); ?>
                        </option>
                    </select>

                </div>
                <div class="alignright">
                    <p class="search-box">
                        <input type="search" id="lp_adsinvoiceInput" onkeyup="lpSearchDataAdsInInvoice()" class="button" placeholder="<?php echo esc_html__('Search Invoices', 'listingpro-plugin'); ?>">
                    </p>
                </div>

                <br class="clear">
            </div>


            <div class="listingpro_coupon_table">
                <div class="lp_admin_invoice_ajax_result"></div>
                <!--all -->
                <div id="tab-1" class="lp-backendtabs-content current">
                    <?php
                    include_once 'templates/invoice_temp_ads/all.php';
                    ?>

                </div>

                <!--success -->
                <div id="tab-2" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp_ads/success.php';
                    ?>
                </div>


                <!--pending -->
                <div id="tab-3" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp_ads/pending.php';
                    ?>
                </div>

                <!--failed -->
                <div id="tab-4" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp_ads/failed.php';
                    ?>
                </div>

            </div>



        </div>

        <!--search-->
        <script>
            function lpSearchDataAdsInInvoice() {
                var input, filter, table, tr, td, i;
                input = document.getElementById("lp_adsinvoiceInput");
                filter = input.value.toUpperCase();
                table = document.getElementsByClassName("wp-list-table");
                for (j = 0; j < table.length; j++) {
                    tr = table[j].getElementsByTagName("tr");
                    for (i = 0; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[0];
                        if (td) {
                            if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    }
                }
            }



        </script>



        <?php
    }
}

?>
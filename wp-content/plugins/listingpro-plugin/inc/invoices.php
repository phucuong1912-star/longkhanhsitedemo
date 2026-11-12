<?php
require_once(ABSPATH . 'wp-admin/includes/screen.php');
//form submit
if (!empty($_POST['payment_submitt']) && isset($_POST['payment_submitt'])) {

    global $wpdb;
    $dbprefix = '';
    $dbprefix = $wpdb->prefix;
    $table_name = $dbprefix . 'listing_orders';
    $order_id = '';
    $results = '';
    $order_id = $_POST['order_id'];
    $date = date('d-m-Y');
    $update_data = array('date' => $date, 'status' => 'success', 'used' => '1');
    $where = array('order_id' => $order_id);
    $update_format = array('%s', '%s');
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);
    }

    $postid = $_POST['post_id'];
    $my_post;
    $listing_status = get_post_status($postid);
    if ($listingpro_options['listings_admin_approved'] == "no" || $listing_status == "publish") {
        $my_post = array(
            'ID'           => $postid,
            'post_date'  => date("Y-m-d H:i:s"),
            'post_status'   => 'publish',
        );
    } else {
        $my_post = array(
            'ID'           => $postid,
            'post_date'  => date("Y-m-d H:i:s"),
            'post_status'   => 'pending',
        );
    }
    wp_update_post($my_post);
    $ex_plan_id = listing_get_metabox_by_ID('Plan_id', $postid);
    $new_plan_id = listing_get_metabox_by_ID('changed_planid', $postid);
    if (!empty($new_plan_id)) {
        if ($ex_plan_id != $new_plan_id) {
            lp_cancel_stripe_subscription($postid, $ex_plan_id);
            listing_set_metabox('Plan_id', $new_plan_id, $postid);
            listing_set_metabox('changed_planid', '', $postid);
        }
    }

    //if paid claim approval
    $claimOrderNo = get_post_meta($postid, 'claimOrderNo', true);
    if ($order_id == $claimOrderNo) {
        $claimPlan_id = get_post_meta($postid, 'claimPlan_id', true);
        listing_set_metabox('Plan_id', $claimPlan_id, $postid);
    }
    //end if paid claim approval
    $plan_time  = get_post_meta($new_plan_id, 'plan_time', true);
    if (!empty($plan_time)) {
        listing_set_metabox('lp_purchase_days', $plan_time, $postid);
    }



    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $thepost = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $dbprefix . "listing_orders WHERE post_id = %d", $postid));
    }

    $post_author_id = get_post_field('post_author', $postid);
    $user = get_user_by('id', $post_author_id);
    $useremail = $user->user_email;
    $user_name = $user->user_login;

    $admin_email = '';
    $admin_email = get_option('admin_email');

    $listing_id = $postid;
    $listing_title = get_the_title($postid);
    $invoice_no = $thepost->order_id;
    $payment_method = $thepost->payment_method;

    $plan_title = $thepost->plan_name;
    $plan_price = $thepost->price . $thepost->currency;
    $listing_url = get_the_permalink($listing_id);


    //to admin
    $mail_subject = $listingpro_options['listingpro_subject_purchase_activated_admin'];
    $website_url = site_url();
    $website_name = get_option('blogname');

    $formated_mail_subject = lp_sprintf2("$mail_subject", array(
        'website_url' => "$website_url",
        'website_name' => "$website_name",
        'user_name' => "$user_name",
    ));

    $mail_content = $listingpro_options['listingpro_content_purchase_activated_admin'];

    $formated_mail_content = lp_sprintf2("$mail_content", array(
        'website_url' => "$website_url",
        'listing_title' => "$listing_title",
        'plan_title' => "$plan_title",
        'plan_price' => "$plan_price",
        'listing_url' => "$listing_url",
        'invoice_no' => "$invoice_no",
        'website_name' => "$website_name",
        'payment_method' => "$payment_method",
        'user_name' => "$user_name",
    ));


    // to user

    $mail_subject2 = $listingpro_options['listingpro_subject_purchase_activated'];
    $website_url = site_url();

    $formated_mail_subject2 = lp_sprintf2("$mail_subject2", array(
        'website_url' => "$website_url",
        'website_name' => "$website_name",
        'user_name' => "$user_name",
    ));

    $mail_content2 = $listingpro_options['listingpro_content_purchase_activated'];
    $formated_mail_content2 = lp_sprintf2("$mail_content2", array(
        'website_url' => "$website_url",
        'listing_title' => "$listing_title",
        'plan_title' => "$plan_title",
        'plan_price' => "$plan_price",
        'listing_url' => "$listing_url",
        'invoice_no' => "$invoice_no",
        'website_name' => "$website_name",
        'payment_method' => "$payment_method",
        'user_name' => "$user_name",
    ));

    lp_mail_headers_append();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    wp_mail($admin_email, $formated_mail_subject, $formated_mail_content, $headers);
    wp_mail($useremail, $formated_mail_subject2, $formated_mail_content2, $headers);
    lp_mail_headers_remove();
}


/* --------------------delete invoice data------------------- */

if (isset($_POST['delete_invoice']) && !empty($_POST['delete_invoice'])) {

    $main_id = $_POST['main_id'];
    if (!empty($main_id)) {
        $table = 'listing_orders';
        $where = array('main_id' => $main_id);
        lp_delete_data_in_db($table, $where);
    }
}

/* --------------------delete pending invoice data------------------- */



if (isset($_POST['delete_invoicee']) && !empty($_POST['delete_invoicee'])) {

    global $wpdb;
    $dbprefix = '';
    $dbprefix = $wpdb->prefix;
    $table_name = $dbprefix . 'listing_orders';

    $main_id = $_POST['main_id'];
    if (isset($_POST['list_id'])) {
        $listid = $_POST['list_id'];
        $uid = get_post_field('post_author', $listid);
        if (!empty($listid)) {
            delete_post_meta($listid, 'campaign_status');
            $update_data = array(
                'payment_method' => '',
                'status' => 'in progress'
            );
            $where = array(
                'post_id' => $listid,
                'main_id' => $main_id
            );
            $wpdb->update($dbprefix . 'listing_orders', $update_data, $where);
        }
    }
}

/*---------------------------------------------------
				adding invoice page
----------------------------------------------------*/

if (!function_exists('listingpro_register_invocies_page')) {
    function listingpro_register_invocies_page()
    {
        add_menu_page(
            __('Invoices', 'listingpro-plugin'),
            'Invoices',
            'manage_options',
            'lp-listings-invoices',
            'listingpro_invoices_page',
            plugins_url('listingpro-plugin/images/invoices.png'),
            30
        );
        add_submenu_page(
            'lp-listings-invoices',
            'Listing Invoices',
            'Listing Invoices',
            'manage_options',
            'listingpro_invoices_page',
            'listingpro_invoices_page'
        );
        remove_submenu_page('lp-listings-invoices', 'lp-listings-invoices');
        wp_enqueue_style("panel_style", WP_PLUGIN_URL . "/listingpro-plugin/assets/css/custom-admin-pages.css", false, "1.0", "all");
    }
}

add_action('admin_menu', 'listingpro_register_invocies_page');

if (!function_exists('listingpro_invoices_page')) {
    function listingpro_invoices_page()
    {
        //adding css

        global $wpdb;
        $dbprefix = '';
        $dbprefix = $wpdb->prefix;
        $table_name = $dbprefix . 'listing_orders';

?>
        <div class="wrap listingpro-coupons linvoiceswrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Listings Invoices', 'listingpro-plugin');  ?></h1>


            <div class="clearfix"></div>
            <!--
				<ul class="subsubsub lpbackendtabs">
					<li class="all current" data-tab="tab-1"><a><?php //echo esc_html__('All', 'listingpro-plugin'); 
                                                                ?> <span class="count"></span></a> |</li>
					<li class="publish" data-tab="tab-2"><a>Success <span class="count"></span></a> |</li>
					<li class="pending" data-tab="tab-3"><a><?php //echo esc_html__('Pending', 'listingpro-plugin'); 
                                                            ?> <span class="count"></span></a> |</li>
					<li class="failed" data-tab="tab-4"><a><?php //echo esc_html__('Failed', 'listingpro-plugin'); 
                                                            ?> <span class="count"></span></a></li>
				</ul>
			-->


            <div class="tablenav top">

                <div class="alignleft actions bulkactions lp_backend_inv_filter">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php echo esc_html__('All Types', 'listingpro-plugin'); ?></label>
                    <select class="lp_invoiceInput">
                        <option value="">
                            <?php echo esc_html__('All Methods', 'listingpro-plugin'); ?>
                        </option>

                        <option value="paypal">
                            <?php echo esc_html__('Paypal', 'listingpro-plugin'); ?>
                        </option>

                        <option value="stripe">
                            <?php echo esc_html__('Stripe', 'listingpro-plugin'); ?>
                        </option>

                        <option value="2checkout">
                            <?php echo esc_html__('2 Checkout', 'listingpro-plugin'); ?>
                        </option>

                        <option value="wire">
                            <?php echo esc_html__('Wire', 'listingpro-plugin'); ?>
                        </option>
                        <?php do_action('lp_invoices_filter'); ?>
                    </select>

                </div>

                <div class="alignleft actions bulkactions lp_backend_inv_filter">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php echo esc_html__('All Status', 'listingpro-plugin'); ?></label>
                    <select class="lp_invoiceStatusInput" id="bulk-action-selector-top">
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
                        <input type="search" id="lp_invoiceInput" onkeyup="lpSearchDataInInvoice()" class="button" placeholder="<?php echo esc_html__('Search Invoices', 'listingpro-plugin'); ?>">
                    </p>
                </div>

                <br class="clear">
            </div>


            <div class="listingpro_coupon_table">
                <div class="lp_admin_invoice_ajax_result"></div>
                <!--all -->
                <div id="tab-1" class="lp-backendtabs-content current">
                    <?php
                    include_once 'templates/invoice_temp/all.php';
                    ?>

                </div>

                <!--success -->
                <div id="tab-2" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp/success.php';
                    ?>
                </div>


                <!--pending -->
                <div id="tab-3" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp/pending.php';
                    ?>
                </div>

                <!--failed -->
                <div id="tab-4" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/invoice_temp/failed.php';
                    ?>
                </div>

            </div>



        </div>

        <!--search-->
        <script>
            function lpSearchDataInInvoice() {
                var input, filter, table, tr, td, i;
                input = document.getElementById("lp_invoiceInput");
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



        <!--endsearch-->
<?php
    }
}


/* =============== for admin invoice details================ */
add_action('wp_ajax_lp_get_admin_invoice_details', 'lp_get_admin_invoice_details');
if (!function_exists('lp_get_admin_invoice_details')) {
    function lp_get_admin_invoice_details()
    {
        global $wpdb;
        $invoiceid = isset($_POST['invoiceid']) ?  sanitize_text_field($_POST['invoiceid']) : 0;
        $invoiceid = (int) $invoiceid;
        $invoicetype   =   $_POST['invoicetype'];
        $tableName = 'listing_orders';
        if ($invoicetype == "listing") {

            $dbprefix = $wpdb->prefix;
            $myInvoice = $wpdb->get_row("SELECT * FROM " . $dbprefix . $tableName . " WHERE main_id = $invoiceid");

            $firstName = '';
            $lastName = '';
            $listingName = '';
            $price = '';
            $currency = '';
            $paymentMethod = '';
            $transactionID = '';
            $methodImg = '';
            $plan_price = '';
            $plan_title = '';
            $pdays = '';
            $invoicedate = '';
            $currencyCode = (isset($myInvoice->currency) && $myInvoice->currency != '') ? $myInvoice->currency : listingpro_currency_sign();
            $currency_position = lp_theme_option('pricingplan_currency_position');


            if (!empty($myInvoice)) {
                $userID = $myInvoice->user_id;
                $author_obj = get_user_by('id', $userID);
                $firstName = $author_obj->first_name;
                $lastName = $author_obj->last_name;
                $user_phone = get_the_author_meta('phone', $userID);
                $user_email = get_the_author_meta('user_email', $userID);
                $user_address = get_the_author_meta('address', $userID);
                $listingID = $myInvoice->post_id;
                $listingName = get_the_title($listingID);
                $price = $myInvoice->price;
                $paymentMethod = $myInvoice->payment_method;
                $transactionID = $myInvoice->order_id;
                $currency = $myInvoice->currency;
                $invoiceno = $myInvoice->order_id;
                $invoicedate = $myInvoice->date;
                $invoicedate = date(get_option('date_format'), strtotime($invoicedate));


                $plan_priceORG = '';
                if (isset($myInvoice->plan_id) && !empty($myInvoice->plan_id)) {
                    $planID = $myInvoice->plan_id;
                    $plan_title = get_the_title($planID);
                    $plan_price = get_post_meta($planID, 'plan_price', true);
                    $plan_price = $myInvoice->price;
                }

                if (!empty($plan_price)) {
                    if ($currency_position == 'right') {
                        $plan_price .= $currencyCode;
                    } else {
                        $plan_price = $currencyCode . $plan_price;
                    }
                }

                if (isset($myInvoice->days) && !empty($myInvoice->days)) {
                    $pdays = $myInvoice->days;
                }
                if (empty($pdays)) {
                    $pdays = esc_html__('Unlimited', 'listingpro');
                }

                $plan_priceORG = $price;

                $taxPrice = 0;
                $onlyPlanPrice = '';

                if (isset($myInvoice->tax)) {
                    if (!empty($myInvoice->tax)) {
                        $taxPrice = $myInvoice->tax;
                    }
                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                }

                /* if price saved in meta */
                $lp_purchase_price = listing_get_metabox_by_ID('lp_purchase_price', $listId);
                $lp_purchase_tax = listing_get_metabox_by_ID('lp_purchase_tax', $listId);
                if (!empty($lp_purchase_price)) {
                    $onlyPlanPrice = round($lp_purchase_price, 2);
                    $plan_priceORG = $onlyPlanPrice;
                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                }
                if (!empty($lp_purchase_tax)) {
                    $taxPrice = $lp_purchase_tax;
                }
                /* end if price saved in meta */

                if (!empty($taxPrice)) {
                    if ($currency_position == 'right') {
                        $taxPrice .= $currencyCode;
                    } else {
                        $taxPrice = $currencyCode . $taxPrice;
                    }
                }


                if (!empty($plan_priceORG)) {
                    if ($currency_position == 'right') {
                        $plan_priceORG .= $currencyCode;
                    } else {
                        $plan_priceORG = $currencyCode . $plan_priceORG;
                    }
                }


                if (!empty($onlyPlanPrice)) {
                    if ($currency_position == 'right') {
                        $onlyPlanPrice .= $currencyCode;
                    } else {
                        $onlyPlanPrice = $currencyCode . $onlyPlanPrice;
                    }
                }


                $methodImg = get_template_directory_uri() . '/assets/images/' . $paymentMethod . '.png';
                if ($paymentMethod == 'paystack') {
                    $methodImg  =   plugins_url('paystack-for-listingpro/assets/images/paystack.png');
                }
                if ($paymentMethod == 'razorpay') {
                    $methodImg  =   plugins_url('razorpay-for-listingpro/assets/images/logo.png');
                }
            }

            $output = null;
            $output = '
									
									<button style="display:none" type="button" class="btn btn-info btn-lg lpinvoiceadminpop" data-toggle="modal" data-target="#lpinvoiceadminpop"></button>

									<!-- Modal -->
								<div id="lpadmiinvoice">	
									<div id="lpinvoiceadminpop" class="listing-invoices-popup modal fade" role="dialog">
									  <div class="modal-dialog">

										<div class="popup-dialog">
												<div class="md-content">
												
													<div class="modal-header">
														<button type="button" class="close close_invoice_prev" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
													</div>
													
													<div class="modal-body">
																
																<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<div class="margin-bottom-40">
																				<img class="img-responsive" src="' . lp_theme_option_url('invoice_logo') . '" alt="' . esc_attr('logo') . '" />
																			</div>
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			<div class="margin-bottom-40">
																				<span class="lp-infoice-label">
																					' . esc_html__('PAID', 'listingpro') . '
																				</span>
																			</div>
																		</div>
																	</div>
																</div>
																
																<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<div class="margin-bottom-40">
																				<h3 class="modal-titl">' . esc_html__('Invoice#', 'listingpro') . ' <span class="lppopinvoice">' . $invoiceno . '</span></h3>
																			</div>
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			<div class="margin-bottom-40">
																				<p class="lp-invoice-popup-date"><span>' . esc_html__('Date: ', 'listingpro') . '</span><span class="lppopdate">' . $invoicedate . '</span></p>
																			</div>
																		</div>
																	</div>
																</div>
																
																
																<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<span>
																					' . esc_html__('Invoice To: ', 'listingpro') . '
																				</span>
																				
																				
																			<span class="spanblock graycolor">
																					' . $firstName . ' ' . $lastName . '
																				</span>
																				
																				<span class="spanblock maxwidth130">
																					' . $user_address . '
																				</span>
																				
																				
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			
																			<span>
																				' . esc_html__('Pay To: ', 'listingpro') . '
																			</span>
																			
																			<span class="spanblock graycolor">
																					' . esc_html__('Business Name', 'listingpro') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_company_name') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_address') . '
																				</span>
																			
																			
																				
																		</div>
																	</div>
																</div>
																
																<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<span class="spanblock">
																					' . $user_phone . '
																				</span>
																				
																				<span class="spanblock">
																					' . $user_email . '
																				</span>
																				
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			
																			<span class="spanblock graycolor">
																					' . esc_html__('Call Us For Help', 'listingpro') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_phone') . '
																				</span>
																				
																		</div>
																	</div>
																</div>
																
																
																<div class="clearfix"></div>
																<div class="row">
																	<div class="col-md-12">
																			
																			<div class="lp-invoice-description-title">
																					<div class="clearfix lp-invoice-description-title-inner">
																						<h3>
																							<span>' . esc_html__('Invoice Details', 'listingpro') . '</span>
																						</h3>

																					</div>
																					<ul class="clearfix lp-invoice-planinfo-inner">
																						<li>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__('Transaction ID', 'listingpro') . '</span>
																								<p>' . $invoiceno . '</p>
																							</div>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__('Listing Name', 'listingpro') . '</span>
																								<p>' . $listingName . '</p>
																							</div>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__('Pricing Plan', 'listingpro') . '</span>
																								<p class="lp-plan-name lppopplan">' . $plan_title . '</p>
																							</div>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__(' Duration ', 'listingpro') . '</span>
																								<p class="lppopduration">' . $pdays . '</p>
																							</div>
																							<div class="col-md-4 lp-in-trns-id padding-0 text-right">
																								<span class="">' . esc_html__(' Amount ', 'listingpro') . '</span>
																								<p class="lppopamount">' . $plan_price . '</p>
																							</div>
																							

																						</li>

																					</ul>

																				</div>
																				<div class="lp-invoices-other-details margin-bottom-30">
																					<ul class="clearfix">
																						<li>' . esc_html__('Tax', 'listingpro') . ' <span class="lppoptaxprice">' . $taxPrice . '</span></li>
																						<li>' . esc_html__('Plan Price', 'listingpro') . ' <span class="lppopplanprice">' . $onlyPlanPrice . '</span></li>								

																						
																						<li class="lp-invoice-total-amount">' . esc_html__('Total', 'listingpro') . ' <span class="lppopamount">' . $plan_priceORG . '</span></li>
																					</ul>

																				</div>
																				<p class="text-right lp-pay-with">' . esc_html__('Paid with', 'listingpro') . '</br>
																					<img data-srcpaystack="' . plugins_url('paystack-for-listingpro/assets/images/paystack.png') . '" data-srcwire="' . get_template_directory_uri() . '/assets/images/wire.png' . '" data-srcpaypal="' . get_template_directory_uri() . '/assets/images/paypal.png' . '" data-srcstripe="' . get_template_directory_uri() . '/assets/images/stripe.png' . '" alt="image" src="' . $methodImg . '" />

																				</p>
																			
																	</div>
																</div>
																
														
													</div>
												</div>

											</div>
											
											<div class="md-content">
												<div class="modal-footer clearfix">
													<div class="row">
														<div class="col-md-6 text-left">
															<button type="button" class="downloadpdffullinv lp-download-invoice"><i class="fa fa-download" aria-hidden="true"></i> ' . esc_html__('Download PDF', 'listingpro') . '</button>
														</div>
														<div class="col-md-6 text-right">
															<button type="button" class="pull-right printthisinvoice lp-print-invoice">' . esc_html__('Print', 'listingpro') . ' <i class="fa fa-print" aria-hidden="true"></i></button>
														</div>
													</div>

												</div>
											</div>
										  
										</div>

									  </div>
									</div>
								</div>
						';
            //for print
            $output .= '
								<div id="lpinvoiceforpdf" style="display:none">
								<h4>' . esc_html__('Invoice#', 'listingpro') . ' <span class="lppopinvoice">' . $invoiceno . '</span></h4>
																
									<p class="lp-invoice-popup-date"><span>' . esc_html__('Date: ', 'listingpro') . '</span><span class="lppopdate">' . $invoicedate . '</span></p>
																
									<p class="margin-bottom-10">' . esc_html__('Billed To: ', 'listingpro') . '</p>
									<p>' . $firstName . ' ' . $lastName . '</p>
									<p>' . $user_phone . '</p>
									<p class="lp-invoice-email">' . $user_email . '</p>
									<p>' . $user_address . '</p>
									
									<p>' . esc_html__('List Name : ', 'listingpro') . '<span class="lllistname">' . $listingName . '</span></p>
									
									<p class="lp-bill-bold">' . lp_theme_option('invoice_company_name') . '</p>
									<p>' . lp_theme_option('invoice_address') . ' </p>
									<p class="lp-invoice-email">' . get_option('admin_url') . ' </p>
																			
									<p>
										<span>' . esc_html__('Amount', 'listingpro') . '</span>
									</p>

                                    
									<p class="lp-invoice-total-amount">' . esc_html__('Total', 'listingpro') . ' <span class="lppopamount">' . $price . '</span></p>

									<p class="lp-pay-with">' . esc_html__('Paid with', 'listingpro') . '</br>
									<span class="lppopmethod">' . $paymentMethod . '</span>
									</p>
															
							</div>
							';
        } elseif ($invoicetype == 'ads') {
            $output = null;
            $tableName = 'listing_campaigns';

            $dbprefix = $wpdb->prefix;
            $myInvoice = $wpdb->get_row("SELECT * FROM " . $dbprefix . $tableName . " WHERE main_id = $invoiceid");

            $firstName = '';
            $lastName = '';
            $listingName = '';
            $price = '';
            $currency = '';
            $paymentMethod = '';
            $transactionID = '';
            $methodImg = '';
            $plan_price = '';
            $plan_title = '';
            $pdays = '';
            $invoicedate = '';
            $invoiceno = '';
            $adsofType = '';
            $currency_position = lp_theme_option('pricingplan_currency_position');


            if (!empty($myInvoice)) {
                $userID = $myInvoice->user_id;


                $author_obj = get_user_by('id', $userID);
                $firstName = $author_obj->first_name;
                $lastName = $author_obj->last_name;
                $user_phone = get_the_author_meta('phone', $userID);
                $user_email = get_the_author_meta('user_email', $userID);
                $user_address = get_the_author_meta('address', $userID);
                $listingID = $myInvoice->post_id;
                $ads_listing = listing_get_metabox_by_ID('ads_listing', $listingID);
                $listingTtitle = get_the_title($ads_listing);
                if(empty($listingTtitle)){
                    $listingTtitle = get_the_title($listingID);
                }
                $price = $myInvoice->budget;
                $paymentMethod = $myInvoice->payment_method;
                $currency = (isset($myInvoice->currency) && $myInvoice->currency != '') ? $myInvoice->currency : listingpro_currency_sign();
                $invoiceno = $myInvoice->transaction_id;
                $invoicedate = $myInvoice->ad_date;
                $adsofType = $myInvoice->mode;
                $invoicedate = date(get_option('date_format'), strtotime($invoicedate));


                $plan_priceORG = '';
                if (isset($myInvoice->plan_id) && !empty($myInvoice->plan_id)) {
                    $planID = $myInvoice->plan_id;
                    $plan_title = get_the_title($planID);
                    $plan_price = get_post_meta($planID, 'plan_price', true);
                    $plan_price = $myInvoice->price;
                }

                if (isset($myInvoice->days) && !empty($myInvoice->days)) {
                    $pdays = $myInvoice->days;
                }
                if (empty($pdays)) {
                    $pdays = esc_html__('Unlimited', 'listingpro');
                }

                $plan_priceORG = $price;

                $taxPrice = 0;
                $onlyPlanPrice = '';

                if (isset($myInvoice->tax)) {
                    if (!empty($myInvoice->tax)) {
                        $taxPrice = $myInvoice->tax;
                    }
                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                }

                /* if price saved in meta */
                $lp_purchase_price = listing_get_metabox_by_ID('lp_purchase_price', $listId);
                $lp_purchase_tax = listing_get_metabox_by_ID('lp_purchase_tax', $listId);
                if (!empty($lp_purchase_price)) {
                    $onlyPlanPrice = round($lp_purchase_price, 2);
                    $plan_priceORG = $onlyPlanPrice;
                }
                if (!empty($lp_purchase_tax)) {
                    $taxPrice = $lp_purchase_tax;
                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                }
                /* end if price saved in meta */

                //new code for update 2.6.15
                $totalbudget = '';
                $total_price = '';

                if ($adsofType == "perclick") {
                    $totalbudget  = listing_get_metabox_by_ID('budget', $listingID);
                    $onlyPlanPrice = $totalbudget;
                    if (!empty($taxPrice)) {
                        $total_price = $onlyPlanPrice + $taxPrice;
                    } else {
                        $total_price = $onlyPlanPrice;
                    }
                } else {
                    $onlyPlanPrice  = $onlyPlanPrice;
                    $total_price = $price;
                }

                //End new code for update 2.6.15

                if ($currency_position == 'right') {
                    $onlyPlanPrice .= $currency;
                    $taxPrice .= $currency;
                    $price .= $currency;
                    $total_price .= $currency;
                } else {
                    $onlyPlanPrice = $currency . $onlyPlanPrice;
                    $taxPrice = $currency . $taxPrice;
                    $price = $currency . $price;
                    $total_price = $currency . $total_price;
                }



                $methodImg = get_template_directory_uri() . '/assets/images/' . $paymentMethod . '.png';
                if ($paymentMethod == 'paystack') {
                    $methodImg  =   plugins_url('paystack-for-listingpro/assets/images/paystack.png');
                }
                if ($paymentMethod == 'razorpay') {
                    $methodImg  =   plugins_url('razorpay-for-listingpro/assets/images/logo.png');
                }
            }

            $output = null;
            $output = '
									
									<button style="display:none" type="button" class="btn btn-info btn-lg lpinvoiceadminpop" data-toggle="modal" data-target="#lpinvoiceadminpop"></button>
									<div id="lpadmiinvoice">
										<!-- Modal -->
										<div id="lpinvoiceadminpop" class="listing-invoices-popup modal fade" role="dialog">
										  <div class="modal-dialog">

											<div class="popup-dialog">
													<div class="md-content">
														<div class="modal-header">
															<button type="button" class="close close_invoice_prev" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
															
														</div>
														<div class="modal-body">
															<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<div class="margin-bottom-40">
																				<img class="img-responsive" src="' . lp_theme_option_url('invoice_logo') . '" alt="' . esc_attr('logo') . '" />
																			</div>
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			<div class="margin-bottom-40">
																				<span class="lp-infoice-label">
																					' . esc_html__('PAID', 'listingpro') . '
																				</span>
																			</div>
																		</div>
																	</div>
																</div>
															<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<div class="margin-bottom-40">
																				<h3 class="modal-titl">' . esc_html__('Invoice#', 'listingpro') . ' <span class="lppopinvoice">' . $invoiceno . '</span></h3>
																			</div>
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			<div class="margin-bottom-40">
																				<p class="lp-invoice-popup-date"><span>' . esc_html__('Date: ', 'listingpro') . '</span><span class="lppopdate">' . $invoicedate . '</span></p>
																			</div>
																		</div>
																	</div>
																</div>	
															<p class="lp-invoice-popup-date"><span>' . esc_html__('Date: ', 'listingpro') . '</span><span class="lppopdate">' . $invoicedate . '</span></p>
															
															<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<span>
																					' . esc_html__('Invoice To: ', 'listingpro') . '
																				</span>
																				
																				
																			<span class="spanblock graycolor">
																					' . $firstName . ' ' . $lastName . '
																				</span>
																				
																				<span class="spanblock maxwidth130">
																					' . $user_address . '
																				</span>
																				
																				
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			
																			<span>
																				' . esc_html__('Pay To: ', 'listingpro') . '
																			</span>
																			
																			<span class="spanblock graycolor">
																					' . esc_html__('Business Name', 'listingpro') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_company_name') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_address') . '
																				</span>
																			
																			
																				
																		</div>
																	</div>
																</div>
															<div class="row">
																	<!--leftside-->
																	<div class="col-md-6 text-left">
																		<div class="lp-invoice-leftinfo">
																			
																			<span class="spanblock">
																					' . $user_phone . '
																				</span>
																				
																				<span class="spanblock">
																					' . $user_email . '
																				</span>
																				
																		</div>
																	</div>
																	
																	<div class="col-md-6 text-right">
																		<div class="lp-invoice-rightinfo">
																			
																			<span class="spanblock graycolor">
																					' . esc_html__('Call Us For Help', 'listingpro') . '
																				</span>
																				
																				<span class="spanblock">
																					' . lp_theme_option('invoice_phone') . '
																				</span>
																				
																		</div>
																	</div>
																</div>
																
																
																<div class="clearfix"></div>
															<div class="row">
																	<div class="col-md-12">
																			
																			<div class="lp-invoice-description-title">
																					<div class="clearfix lp-invoice-description-title-inner">
																						<h3>
																							<span>' . esc_html__('Invoice Details', 'listingpro') . '</span>
																						</h3>

																					</div>
																					<ul class="clearfix lp-invoice-planinfo-inner">
																						<li>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__('Transaction ID', 'listingpro') . '</span>
																								<p>' . $invoiceno . '</p>
																							</div>
																							<div class="col-md-2 lp-in-trns-id padding-left-0">
																								<span class="">' . esc_html__('Listing Name', 'listingpro') . '</span>
																								<p>' . $listingTtitle . '</p>
																							</div>
																							
																							<div class="col-md-8 lp-in-trns-id padding-0 text-right">
																								<span class="">' . esc_html__(' Amount ', 'listingpro') . '</span>
																								<p class="lppopamount">' . listingpro_currency_sign() . $onlyPlanPrice . '</p>
																							</div>
																							

																						</li>

																					</ul>

																				</div>
																				<div class="lp-invoices-other-details margin-bottom-30">
																					<ul class="clearfix">
																						<li>' . esc_html__('Tax', 'listingpro') . ' <span class="lppoptaxprice">' . $taxPrice . '</span></li>
																						<li>' . esc_html__('Plan Price', 'listingpro') . ' <span class="lppopplanprice">' . $onlyPlanPrice . '</span></li>
																						

																						<li class="lp-invoice-total-amount">' . esc_html__('Total', 'listingpro') . ' <span class="lppopamount">' . $total_price . '</span></li>
																					</ul>

																				</div>
																				<p class="text-right lp-pay-with">' . esc_html__('Paid with', 'listingpro') . '</br>
																					<img data-srcwire="' . get_template_directory_uri() . '/assets/images/wire.png' . '" data-srcpaypal="' . get_template_directory_uri() . '/assets/images/paypal.png' . '" data-srcstripe="' . get_template_directory_uri() . '/assets/images/stripe.png' . '" alt="image" src="' . $methodImg . '" />

																				</p>
																			
																	</div>
																</div>	
															
															
															
														</div>
													</div>

												</div>
											  <div class="md-content">
												<div class="modal-footer clearfix">
													<div class="row">
														<div class="col-md-6 text-left">
															<button type="button" class="downloadpdffullinv lp-download-invoice"><i class="fa fa-download" aria-hidden="true"></i> ' . esc_html__('Download PDF', 'listingpro') . '</button>
														</div>
														<div class="col-md-6 text-right">
															<button type="button" class="pull-right printthisinvoice lp-print-invoice">' . esc_html__('Print', 'listingpro') . ' <i class="fa fa-print" aria-hidden="true"></i></button>
														</div>
													</div>

												</div>
											</div>
											</div>

										  </div>
										</div>
									</div>
						';
            //for print
            $output .= '
								<div id="lpinvoiceforpdf" style="display:none">
								<h4>' . esc_html__('Invoice#', 'listingpro') . ' <span class="lppopinvoice">' . $invoiceno . '</span></h4>
																
									<p class="lp-invoice-popup-date"><span>' . esc_html__('Date: ', 'listingpro') . '</span><span class="lppopdate">' . $invoicedate . '</span></p>
																
									<p class="margin-bottom-10">' . esc_html__('Billed To: ', 'listingpro') . '</p>
									<p>' . $firstName . ' ' . $lastName . '</p>
									<p>' . $user_phone . '</p>
									<p class="lp-invoice-email">' . $user_email . '</p>
									<p>' . $user_address . '</p>
									
									<p>' . esc_html__('List Name : ', 'listingpro') . '<span class="lllistname">' . $listingTtitle . '</span></p>
									
									<p class="lp-bill-bold">' . lp_theme_option('invoice_company_name') . '</p>
									<p>' . lp_theme_option('invoice_address') . ' </p>
									<p class="lp-invoice-email">' . get_option('admin_url') . ' </p>
																			
									<p>
										<span>' . esc_html__('Amount', 'listingpro') . '</span>
									</p>

									<p class="lp-invoice-total-amount">' . esc_html__('Total', 'listingpro') . ' <span class="lppopamount">' . $price . '</span></p>

									<p class="lp-pay-with">' . esc_html__('Paid with', 'listingpro') . '</br>
									<span class="lppopmethod">' . $paymentMethod . '</span>
									</p>
															
							</div>
							';
        }

        exit(json_encode(array($output)));
    }
}

?>
<?php
/* get coupons record*/
if (!function_exists('lp_get_existing_coupons')) {
    function lp_get_existing_coupons()
    {
        $option_name = 'lp_coupons';
        if (get_option($option_name) !== false) {
            $existingCoupons = get_option($option_name);
            return $existingCoupons;
        }
    }
}


/* update coupons record */
if (!function_exists('lp_update_coupons')) {
    function lp_update_coupons($code, $discount, $starts, $ends, $coponlimit, $copontype)
    {
        $option_name = 'lp_coupons';
        $newCoupon['code'] = $code;
        $newCoupon['discount'] = $discount;
        $newCoupon['starts'] = $starts;
        $newCoupon['ends'] = $ends;
        $newCoupon['coponlimit'] = $coponlimit;
        $newCoupon['copontype'] = $copontype;
        $newCoupon['used'] = 0;
        $newCoupon['status'] = '';
        if (get_option($option_name) !== false) {
            $existingCoupons = get_option($option_name);
            $existingCoupons[] = $newCoupon;
            update_option($option_name, $existingCoupons);
        } else {
            update_option($option_name, array($newCoupon));
        }
    }
}



/* submitting coupon */
if (isset($_POST['lp_submit_new_coupon']) && !empty($_POST['lp_submit_new_coupon'])) {
    $couponcode = $_POST['couponcode'];
    $couponpercentage = $_POST['couponpercentage'];
    $couponstarts = $_POST['couponstarts'];
    $couponends = $_POST['couponends'];
    $coponlimit = $_POST['coponlimit'];
    $copontype = '';
    if (isset($_POST['lp-coupon-type'])) {
        $copontype = $_POST['lp-coupon-type'];
    }
    lp_update_coupons($couponcode, $couponpercentage, $couponstarts, $couponends, $coponlimit, $copontype);
}


/* deleting coupon */
if (isset($_POST['lp_delte_coupon_submit']) && !empty($_POST['lp_delte_coupon_submit'])) {
    $option_name = 'lp_coupons';
    $couponIndex = $_POST['couponinxed'];
    $currentCoupons = lp_get_existing_coupons();
    if (!empty($currentCoupons)) {
        if (empty($couponIndex)) {
            unset($currentCoupons[0]);
        } else {
            unset($currentCoupons[$couponIndex]);
        }
    }
    $currentCoupons = array_values($currentCoupons);
    update_option($option_name, $currentCoupons);
}

/* search data in array */
if (!function_exists('lp_search_coupon_in_array')) {
    function lp_search_coupon_in_array($value, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['code'] === $value) {
                return $key;
            }
        }
        return null;
    }
}




/* ===================== function for coupon ajax======================= */
if (!function_exists('listingpro_apply_coupon_code')) {
    function listingpro_apply_coupon_code()
    {
        $response = array();
        $coupon = $_POST['coupon'];
        $listingid = $_POST['listingid'];
        $taxrate = $_POST['taxrate'];
        $price = $_POST['price'];

        if (!empty($coupon) && isset($_POST['notusing'])) {

            exit(json_encode($coupon));
        }
        $existingCoupon = lp_get_existing_coupons();
        if (!empty($existingCoupon)) {
            $returnKey = lp_search_coupon_in_array($coupon, $existingCoupon);
            if (isset($returnKey)) {
                $couponData = $existingCoupon[$returnKey];
                if (!empty($couponData)) {

                    $couponLimit = $couponData['coponlimit'];
                    $couponUsed = $couponData['used'];
                    $couponStarts = $couponData['starts'];
                    $couponEnds = $couponData['ends'];

                    $currentDate = date('Y-m-d');
                    $currentDate = date('Y-m-d', strtotime($currentDate));
                    $couponStarts = date('Y-m-d', strtotime($couponStarts));
                    $couponEnds = date('Y-m-d', strtotime($couponEnds));

                    //New by abbas
                    $currentDate_str = strtotime($currentDate);
                    $couponStarts_str = strtotime($couponStarts);
                    $couponEnds_str = strtotime($couponEnds);
                    $couponType = '';
                    if (isset($couponData['copontype'])) {
                        $couponType = $couponData['copontype'];
                    }

                    if (($currentDate_str >= $couponStarts_str) && ($currentDate_str <= $couponEnds_str)) {
                        /* valid date */
                    } else {
                        $response['msg'] = esc_html__('Sorry! Pending or Expired', 'listingpro');
                        $response['status'] = 'error';
                        exit(json_encode($response));
                    }

                    //End New by abbas


                    if ($couponUsed < $couponLimit) {

                        $response['code'] = $couponData['code'];
                        $response['discount'] = $couponData['discount'];

                        $response['coupontype'] = $couponType;

                        /* for tax */
                        $discount = $couponData['discount'];
                        if (!empty($couponType) && $discount > $price) {
                            $response['msg'] = esc_html__('Sorry! Plan price should be greater than discount price.', 'listingpro');
                            $response['status'] = 'error';
                            exit(json_encode($response));
                        }

                        if (!empty($couponType)) {
                            $price = $price - $discount;
                        } else {
                            $discount = $discount / 100;
                            $discount = $discount * $price;
                            $discount = round($discount, 2);
                            $price = $price - $discount;
                        }

                        if( !empty($taxrate)){
							$priceTax = $taxrate / 100;
							$priceTax = $priceTax * $price;
							$priceTax = round($priceTax, 2);
						}

                        $response['price'] = $price;
                        $response['taxprice'] = $priceTax;

                        /* end of tax */

                        $response['msg'] = esc_html__('Great! Enjoy the discount', 'listingpro');
                        $response['status'] = 'success';
                    } else {
                        $response['msg'] = esc_html__('Sorry! Limit exceeded', 'listingpro');
                        $response['status'] = 'error';
                    }
                }
            } else {
                $response['msg'] = esc_html__('Sorry! Invalid coupon code', 'listingpro');
                $response['status'] = 'error';
            }
        } else {
            $response['msg'] = esc_html__('Sorry! Invalid coupon code', 'listingpro');
            $response['status'] = 'error';
        }
        exit(json_encode($response));
    }
}

add_action('wp_ajax_listingpro_apply_coupon_code', 'listingpro_apply_coupon_code');


/* ===================== function for coupon check after payment======================= */
if (!function_exists('listingpro_apply_coupon_code_at_payment')) {
    function listingpro_apply_coupon_code_at_payment($coupon, $listingid, $taxrate, $price)
    {

        $existingCoupon = lp_get_existing_coupons();
        if (!empty($existingCoupon)) {
            $returnKey = lp_search_coupon_in_array($coupon, $existingCoupon);
            if (isset($returnKey)) {

                $couponData = $existingCoupon[$returnKey];
                if (!empty($couponData)) {

                    $couponLimit = $couponData['coponlimit'];
                    $couponUsed = $couponData['used'];
                    $couponStarts = $couponData['starts'];
                    $couponEnds = $couponData['ends'];

                    $currentDate = date('Y-m-d');
                    $currentDate = date('Y-m-d', strtotime($currentDate));
                    $couponStarts = date('Y-m-d', strtotime($couponStarts));
                    $couponEnds = date('Y-m-d', strtotime($couponEnds));

                    //New by abbas
                    $currentDate_str = strtotime($currentDate);
                    $couponStarts_str = strtotime($couponStarts);
                    $couponEnds_str = strtotime($couponEnds);

                    $couponType = '';
                    if (isset($couponData['copontype'])) {
                        $couponType = $couponData['copontype'];
                    }


                    if (($currentDate_str >= $couponStarts_str) && ($currentDate_str <= $couponEnds_str)) {
                        /* valid date */
                    } else {
                        $response['msg'] = esc_html__('Sorry! Pending or Expired', 'listingpro');
                        $response['status'] = 'error';
                        exit(json_encode($response));
                    }

                     //End New by abbas


                    if ($couponUsed < $couponLimit) {

                        $response['code'] = $couponData['code'];
                        $response['discount'] = $couponData['discount'];

                        /* using credit of coupon limit*/
                        $couponUsed++;
                        $couponData['used'] = $couponUsed;
                        foreach ($existingCoupon as $key => $val) {
                            if ($val['code'] === $couponData['code']) {
                                $existingCoupon[$key] = $couponData;
                            }
                        }
                        $option_name = 'lp_coupons';
                        $user_id = get_current_user_id();
                        $userCoupon = get_user_meta($user_id,  'used_coupons', true);
                        if (!empty($userCoupon)) {
                            if (is_array($userCoupon)) {
                                array_push($userCoupon, $coupon);
                            } else {
                                $userCoupon = array($coupon);
                            }
                        } else {
                            $userCoupon = array($coupon);
                        }
                        update_user_meta($user_id, 'used_coupons', $userCoupon);
                        update_option($option_name, $existingCoupon);
                    }
                }
            }
        }
    }
}




/* if user already used this coupon */
if (!function_exists('lp_check_used_coupon')) {
    function lp_check_used_coupon($code)
    {
        $user_id = get_current_user_id();
        $usedCoupons = get_user_meta($user_id,  'used_coupons', true);
        if (!empty($usedCoupons)) {
            foreach ($usedCoupons as $key => $val) {
                if ($val == $code) {
                    return $val;
                }
            }
        }
        return '';
    }
}



/* get status by coupon code */
if (!function_exists('lp_check_coupon_status')) {
    function lp_check_coupon_status($code)
    {
        $existingCoupons = lp_get_existing_coupons();
        if (!empty($existingCoupons)) {
            foreach ($existingCoupons as $singleCoupon) {
                $singleCOde = $singleCoupon['code'];
                if ($singleCOde == $code) {
                    $cstarts = $singleCoupon['starts'];
                    $cstarts = strtotime(date("d-m-Y", strtotime($cstarts)));
                    $cends = $singleCoupon['ends'];
                    $cends = strtotime(date("d-m-Y", strtotime($cends)));
                    $coponlimit = $singleCoupon['coponlimit'];
                    $used = $singleCoupon['used'];
                    $status = $singleCoupon['status'];
                    $date_now = strtotime(date("d-m-Y"));
                    if ($date_now < $cstarts) {
                        return 'pending';
                    } elseif ($date_now > $cends || $used >= $coponlimit || $status == "expired") {
                        return 'expired';
                    } else {
                        return 'active';
                    }
                }
            }
        }
        return false;
    }
}




/* get coupon status counter by type*/
if (!function_exists('lp_get_coupon_status_counter')) {
    function lp_get_coupon_status_counter($type, $return = null)
    {
        $existingCoupons = lp_get_existing_coupons();
        $count = 0;
        $excount = 0;
        $dataArrayActive = array();
        $dataArrayExpired = array();
        if (!empty($existingCoupons)) {
            if ($type == 'all') {
                if (!empty($return)) {
                    return $existingCoupons;
                } else {
                    $count = count($existingCoupons);
                    return $count;
                }
            } elseif ($type == 'active' || $type == 'expired') {
                foreach ($existingCoupons as $singleCoupon) {
                    $cstarts = $singleCoupon['starts'];
                    $cends = $singleCoupon['ends'];
                    $coponlimit = $singleCoupon['coponlimit'];
                    $used = $singleCoupon['used'];
                    $status = $singleCoupon['status'];
                    $date_now = date("Y-m-d");
                    if ($date_now > $cends || $used >= $coponlimit || $status == "expired") {
                        if (!empty($return)) {
                            $dataArrayExpired[] = $singleCoupon;
                        } else {
                            $excount++;
                        }
                    } else {
                        if (!empty($return)) {
                            $dataArrayActive[] = $singleCoupon;
                        } else {
                            $count++;
                        }
                    }
                }
                if ($type == 'active') {
                    if (!empty($return)) {
                        return $dataArrayActive;
                    } else {
                        return $count;
                    }
                } elseif ($type == 'expired') {
                    if (!empty($return)) {
                        return $dataArrayExpired;
                    } else {
                        return $excount;
                    }
                }
            }
        }
    }
}

if (!function_exists('listingpro_coupons_page')) {
    function listingpro_coupons_page()
    {

        $existingCoupons = lp_get_existing_coupons();
        $allCount        = lp_get_coupon_status_counter('all', null);
        if (empty($allCount)) {
            $allCount = 0;
        }
        $activeCount = lp_get_coupon_status_counter('active', null);
        if (empty($activeCount)) {
            $activeCount = 0;
        }
        $expiredCount = lp_get_coupon_status_counter('expired', null);
        if (empty($expiredCount)) {
            $expiredCount = 0;
        }
        $discountLabel  = esc_html__('Discount( without % sign ) : ', 'listingpro-plugin');
        $discountLabel2 = esc_html__('Discount( fix price ) : ', 'listingpro-plugin');

?>

        <div class="wrap listingpro-coupons">

            <h1 class="wp-heading-inline"><?php echo esc_html__('Listingpro Coupons', 'listingpro-plugin'); ?></h1>

            <button id="show_add_coupons" class="page-title-action">Add New</button>
            <div class="toggle_add_coupons row" style="display: none;">
                <div class="clearfix">
                    <form name="lp_add_coupon_form" method="POST" action="">
                        <br>
                        <div class="form-group col-md-12">
                            <button type="button" id="coponcodebtn" onclick="lprandomString(10)" class="button button-primary button-large lp-secondary-btn btn-first-hover"><?php echo esc_html__('Generate', 'listingpro-plugin'); ?></button>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="text" class="form-control" id="couponcode" placeholder="<?php echo esc_html__('Please click on generate button', 'listingpro-plugin'); ?>" name="couponcode" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="couponpercentage"><?php echo esc_html__('Fix Price Coupon?', 'listingpro-plugin'); ?></label>
                            <label class="switch lp-coupon-type" data-pertext="<?php echo $discountLabel; ?>" data-fixtext="<?php echo $discountLabel2; ?>">
                                <input type="checkbox" name="lp-coupon-type">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="couponpercentage" id="inputLabelCouponType" data-percentprice="<?php echo $discountLabel; ?>" data-fixprice="<?php echo $discountLabel2; ?>"><?php echo $discountLabel; ?></label>
                            <input type="text" class="form-control" id="couponpercentage" placeholder="<?php echo esc_html__('Please add only numeric value', 'listingpro-plugin'); ?>" name="couponpercentage" autocomplete="off" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="couponstarts"><?php echo esc_html__('Start Date : ', 'listingpro-plugin'); ?></label>
                            <input type="date" min="<?php echo date("Y-m-d"); ?>" class="form-control" id="couponstarts" placeholder="<?php echo esc_html__('Coupon Starts', 'listingpro-plugin'); ?>" name="couponstarts" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="couponends"><?php echo esc_html__('End Date : ', 'listingpro-plugin'); ?></label>
                            <input type="date" class="form-control" id="couponends" min="<?php echo date("Y-m-d"); ?>" placeholder="Coupon Ends" name="couponends" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="coponlimit"><?php echo esc_html__('Limit : ', 'listingpro-plugin'); ?></label>
                            <input type="text" class="form-control" id="coponlimit" placeholder="5" name="coponlimit" required>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="hidden" name="lp_submit_new_coupon" value="submit it">
                            <button type="submit" class="button button-primary button-large lp-secondary-btn btn-first-hover"><?php echo esc_html__('Save Coupon', 'listingpro-plugin'); ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="clearfix"></div>
            <ul class="subsubsub lpbackendtabs">
                <li class="all tab-link current" data-tab="tab-1"><a class="current">All <span class="count">(<?php echo $allCount; ?>)</span></a> |
                </li>
                <li class="publish tab-link" data-tab="tab-2"><a>Active <span class="count">(<?php echo $activeCount; ?>)<span></a>
                    |
                </li>
                <li class="expired tab-link" data-tab="tab-3"><a>Expired <span class="count">(<?php echo $expiredCount; ?>)</span></a></li>
            </ul>


            <div id="posts-filter" method="get">


                <?php
                if (!empty($existingCoupons)) {

                ?>
                    <div class="listingpro_coupon_table">
                        <!--all -->
                        <div id="tab-1" class="lp-backendtabs-content current">
                            <?php
                            include_once 'templates/all.php';
                            ?>

                        </div>

                        <!--active -->
                        <div id="tab-2" class="lp-backendtabs-content">
                            <?php
                            include_once 'templates/active.php';
                            ?>
                        </div>


                        <!--expired -->
                        <div id="tab-3" class="lp-backendtabs-content">
                            <?php
                            include_once 'templates/expired.php';
                            ?>
                        </div>

                    </div>
            </div>

        <?php
                } else {
                    /* blank */
        ?>

            <div class="listingpro_coupon_table">
                <!--all -->
                <div id="tab-1" class="lp-backendtabs-content current">
                    <?php
                    include_once 'templates/blank.php';
                    ?>

                </div>

                <div id="tab-2" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/blank-active.php';
                    ?>

                </div>
                <div id="tab-3" class="lp-backendtabs-content">
                    <?php
                    include_once 'templates/blank-expired.php';
                    ?>

                </div>


            </div>
        <?php } ?>


        <script>
            (function() {
                var lprandomString = function(length) {

                    var text = "";

                    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                    for (var i = 0; i < length; i++) {

                        text += possible.charAt(Math.floor(Math.random() * possible.length));

                    }

                    var elem = document.getElementById("couponcode").value = text;
                    //return text;
                };

                // random string length
                //var random = lprandomString(10);
                //alert(random);
                document.getElementById("coponcodebtn").addEventListener("click", lprandomString(10));

            })();

            function lprandomString(length) {
                var text = "";

                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for (var i = 0; i < length; i++) {

                    text += possible.charAt(Math.floor(Math.random() * possible.length));

                }

                var elem = document.getElementById("couponcode").value = text;
            }

            // insert random string to the field
            //var elem = document.getElementById("couponcode").value = random;
        </script>


        </div>

<?php
    }
}

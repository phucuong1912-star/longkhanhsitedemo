<?php
lp_open_chat_box();
function remove_http_from_url($url) {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        return preg_replace('/^https(?=:\/\/)/i','',$url);
    } else {
        return preg_replace('/^http(?=:\/\/)/i','',$url);
    }
}

$env_check  =   get_option('active_env');

$home_url = remove_http_from_url(home_url());
$verification_check =   get_option( 'theme_activation' );
if( $verification_check == 'activated' && $env_check) {
    $active_license_key = get_option('active_license');
    $get_license_api_url = CRIDIO_API_URL.'getlicense/' . $active_license_key;


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $get_license_api_url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $license_response = curl_exec($ch);
    curl_close($ch);

    $license_response = json_decode($license_response);
    $license_data = $license_response->license_data;

    $license_data_arr = json_decode(json_encode($license_data), True);
}

$error_message  =   'SORRY! THIS PURCASE CODE IS INVALID.';

if(isset($_GET['license-res'])) {

    $license_res    =   $_GET['license-res'];

    $res_type       =   'success';
    $license_status =   'active';
    if( $_GET['license-res'] == 'invalid-key' ) {
        $res_type   =   'error';
    }
    if( isset( $_GET['license-status'] ) && $_GET['license-status'] != 'active' ) {
        $license_status =   'inactive';
    }
    if( isset( $_GET['license-status'] ) && $_GET['license-status'] != 'active' ) {
        $license_status =   'inactive';
    }
    if (strpos($license_res, 'Already Active On') !== false) {
        $res_type   =   'already-active';

        $error_message  =   'SORRY! THIS PURCHASE CODE IS ALREADY ACTIVE ON DIFFERENT SITE.';
    }
}

$content = get_option( 'lp_overview_api_content' );
?>
<div class="lp-cc-dashboard-content-container">
    <div class="lp-cc-dashboard-navbar">
        <div class="lp-cc-dashboard-navbar-brand">
            <img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/logo.png" />
        </div>
         <div class="lp-cc-dashboard-navbar-nav">
            <span>Need Customization?<a href="<?php echo isset($content['listingpro_options']['services_link']) ? esc_url($content['listingpro_options']['services_link']) : '#'; ?>" target="_blank" class="lp-need-customization">Request Free Quote</a></span>
			<span class="or"></span>
			<span>Need Help?<a href="https://docs.listingprowp.com/" target="_blank">Go to Help Center</a></span>
         </div>
    </div>
    <div class="lp-cc-dashboard-content-header">
        <p class="lp-cc-dashboard-content-header-title">Welcome to ListingPro Command Center!</p>
    </div>
    <div class="lp-cc-dashboard-content-btn-group">
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=listingpro" class="float-l">Dashboard</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-addons" class="float-l">Add-ons</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-visualizer" class="float-l">Visualizer</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-license" class="active float-l">License</a>
		<?php if( isset($content['listingpro_options']['services_link']) &&  !empty($content['listingpro_options']['services_link']) ){ ?>
		<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-services-link float-l">Services</a>
		<?php } ?>
    </div>
    <div class="license-main-content-box">
        <?php
        if( $env_check && $verification_check == 'activated' && !isset($_GET['license-res']) ) {
            ?>
            <div class="license-view-all-sites-wrapper">
                <input type="hidden" id="cur_active_lic_g" value="<?php echo get_option('active_license'); ?>">
                <div class="license-view-all-sites-header">
                    <span class="license-view-all-sites-header-title">ALL LICENSE ACTIVATION STATUS</span>
                    <span class="license-view-all-sites-header-license-detail">PURCHASE CODE <div id="txtPhoneNo" class="show-in_che"></div> <i class="fa fa-eye input-visibility-toggler"></i></span>
                </div>
                <div class="license-view-all-sites-content">
                    <div class="license-view-all-sites-table">
                        <ul>
                            <li>INSTANCE URL</li>
                            <li>PURCHASE DATE</li>
                            <li>ACTIVATION DATE</li>
                            <li>SUPPORT UNTIL</li>
                            <li>INSTANCE MODE</li>
                            <li>STATUS</li>
                            <li>ACTION</li>
                        </ul>
                        <?php
                        if(!empty($license_data_arr['sandbox']['site_url'])) {
                            ?>
                            <ul>
                                <li><?php echo $license_data_arr['sandbox']['site_url']; ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['purchase-date']['sold_at'])); ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['sandbox']['timestamp'])); ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['purchase-date']['supported_until'])); ?></li>
                                <li>TEST SITE</li>
                                <li><i class="<?php if($license_data_arr['sandbox']['status'] != 'active'){echo 'deactive';} else {echo 'active';} ?> active"></i><?php echo $license_data_arr['sandbox']['status']; ?></li>
                                <?php
                                $record_license_url = remove_http_from_url($license_data_arr['sandbox']['site_url']);
                                if( $record_license_url == $home_url ) {
                                    ?>
                                    <li><div class="site-table-switch"><input id="site-table-switch-2" type="checkbox" checked class="site-table-switch-input"><label for="site-table-switch-2" class="site-table-switch-label"> </label></div></li>
                                    <?php
                                } else {
                                    ?>
                                    <li><i class="fa fa-info sites-table-info-icon"><span>Option to activate and deactivate license is only available on the website where the licence was originally activated.</span></i></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                        <?php
                        if(!empty($license_data_arr['live']['site_url'])) {
                            ?>
                            <ul>
                                <li><?php echo $license_data_arr['live']['site_url']; ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['purchase-date']['sold_at'])); ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['live']['timestamp'])); ?></li>
                                <li><?php echo date_i18n(get_option('date_format'), strtotime($license_data_arr['purchase-date']['supported_until'])); ?></li>
                                <li>LIVE SITE</li>
                                <li><i class="<?php if($license_data_arr['live']['status'] != 'active'){echo 'deactive';} else {echo 'active';} ?> deactive"></i><?php echo $license_data_arr['live']['status']; ?></li>
                                <?php
                                $record_license_url = remove_http_from_url($license_data_arr['live']['site_url']);
                                if( $record_license_url == $home_url ) {
                                    ?>
                                    <li><div class="site-table-switch"><input id="site-table-switch-1" type="checkbox" checked class="site-table-switch-input"><label for="site-table-switch-1" class="site-table-switch-label"> </label></div></li>
                                    <?php
                                } else {
                                    ?>
                                    <li><i class="fa fa-info sites-table-info-icon"><span>Option to activate and deactivate license is only available on the website where the licence was originally activated.</span></i></li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="license-verification-form">
                <div class="colored-top-bar"></div>
                <div class="license-verification-form-header">
                    <span class="license-verification-form-header-title">ListingPro License Activation</span>
                </div>
                <div class="license-verification-form-box-content" <?php if(isset($_GET['license-res']) && ( ($license_status != 'inactive') && ($res_type == 'success' || $res_type == 'error' || $res_type == 'invalid-key' || $res_type == 'already-active' )) ){echo 'style="display: none;"';}?>>
                    <p class="license-verification-form-content-box-des">When you purchase a single license of <a href="http://listingprowp.com" target="_blank">ListingPro WordPress Directory Theme</a>, you are allowed to use the theme on one single finished directory site. Two instances are allowed for activation. One instance for Test Site and another for Live Site. For more information please read our <a href="https://docs.listingprowp.com/knowledge-base/" target="_blank">FAQs</a> below.</p>
                    <form class="license-verification-form-container" id="Listingpro_license_verification_form" action="<?php echo esc_attr('admin-post.php'); ?>" method="post">
                        <img alt="image" class="img-key-bar" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/key-img.png">
                        <input type="hidden" name="action" value="verify_license" />
                        <input type="hidden" name="verifier_redirect_url" value="<?php echo admin_url('admin.php?page=lp-cc-license'); ?>" />
                        <span style="position: relative;">
                        <span class="input-caption-left">ENTER YOUR ITEM PURCHASE CODE (KEY)</span>
                        <span class="input-caption-bottom"><a href="http://bit.ly/activate-listingpro" target="_blank">HOW TO GET PURCHASE CODE (KEY)?</a></span>
                        <input id="title" class="key-enter-bar" required placeholder="E.G : XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" name="key" maxlength="36" autocomplete="off" type="text">
                    </span>
                        <span style="position: relative;">
                        <span class="input-caption-left" style="left: ">INSTANCE MODE</span>
                        <select name="lp_license_env" id="lp_license_env" class="activation-option">
                           <option>Select</option>
                           <option value="live">Live Site</option>
                           <option value="sandbox">Test Site</option>
                        </select>
                    </span>
                        <?php echo wp_nonce_field( 'api_nonce', 'api_nonce_field' ,true, false ); ?>
                        <input type="submit" name="submit" disabled class="button button-success button-hero" value="Activate"/>
                    </form>
                </div>
                <div class="license-activated-successfully-box-content" <?php if(isset($_GET['license-res']) && $res_type == 'success' && $license_status != 'inactive' ){echo 'style="display: block;"';}?>>
                    <img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/success.png">
                    <p class="license-activated-successfully-content-box-des">CONGRATULATIONS! YOUR SITE IS NOW ACTIVATED</p>
                    <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-license">VIEW ALL CURRENTLY ACTIVE SITES</a>
                </div>
                <div class="license-activation-failed-box-content" <?php if(isset($_GET['license-res']) && ($res_type == 'error' || $res_type == 'already-active') ){echo 'style="display: block;"';}?>>
                    <img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/failed.png" class="hide-license-error">
                    <p class="license-activation-failed-content-box-des"><?php echo $error_message; ?></p>
                </div>
            </div>
            <?php
        }
        ?>


    </div>

    <div class="infoVideo">
        <div class="license-verification-form">
            <div class="colored-top-bar"></div>
            <div class="license-verification-form-header">
                <span class="license-verification-form-header-title">Where To Get License</span>
            </div>
            <div class="license-verification-form-box-content-vid">
                <iframe src="https://embed.fleeq.io/l/nvvqj7zqub-8skpshuid0" frameborder="0" allowfullscreen="true"
                        style="width:100%;height:100%"></iframe>
            </div>
        </div>
    </div>

    <div class="license-page-faq">
        <h3><?php echo esc_html_e('Frequently Ask Questions', 'listingpro-plugin') ?></h3>
        <p><a href="https://docs.listingprowp.com/knowledgebase/listingpro-regular-license/" target="_blank"><?php echo esc_html_e('FAQ: How many directories can I build with a single license?', 'listingpro-plugin') ?></a></p>
        <p><a href="https://docs.listingprowp.com/knowledgebase/listingpro-regular-license/#p1" target="_blank"><?php echo esc_html_e('FAQ: Can I monetize (earn) from my directories with a regular license?', 'listingpro-plugin') ?></a></p>
        <p><a href="https://docs.listingprowp.com/knowledgebase/whats-included-in-item-support/" target="_blank"><?php echo esc_html_e('FAQ: What`s included in item support (regular license)?', 'listingpro-plugin') ?></a></p>
    </div>
</div>

<div class="license-page-alert-overlay">
    <form id="Listingpro_license_deactivation_form" action="<?php echo esc_attr('admin-post.php'); ?>" method="post">
        <input type="hidden" name="action" value="deactivate_license" />
        <input type="hidden" name="verifier_redirect_url" value="<?php echo admin_url('admin.php?page=lp-cc-license'); ?>" />
        <input  name="key" value="<?php echo get_option('active_license'); ?>" type="hidden">
        <input name="env" value="<?php echo get_option('active_env'); ?>" type="hidden">
        <?php echo wp_nonce_field( 'api_nonce', 'api_nonce_field_dac' ,true, false ); ?>
        <div class="alert-area">
            <span class="alert-area-head">Alert</span>
            <span class="alert-area-dismiss"><i class="fa fa-times" aria-hidden="true"></i></span>
            <hr>
            <p class="alert-area-des">Are you sure you want to <span class="for-change">deactivate</span> this <a href="">purchase code</a> on the following domain?</p>
            <p class="target-site"><?php echo home_url(); ?></p>
            <hr>
            <span class="btn btn-no"><button>no</button></span>
            <span class="btn btn-yes"><button type="submit">yes</button></span>
        </div>
    </form>
</div>
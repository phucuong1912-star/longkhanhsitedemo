<?php
lp_open_chat_box();
$submit_form_builder_state  =   get_option('listing_submit_form_state');
$form_builder_url       =   admin_url('admin.php?page=lp-submit-form');
$form_builder_active    =   '';
$form_builder_button    =   '<button data-target="' . $form_builder_url . '" class="visualizer-form-builder-button">DISABLED</button>';
$form_builder_checked   =   '';

$lead_form_state        =   get_option('lead-form-active');
$lead_form_url          =   admin_url('admin.php?page=listingpro_visualizer_form_builder');
$lead_form_active       =   '';
$lead_form_button       =   '<button data-target="' . $lead_form_url . '" class="visualizer-lead-form-button">DISABLED</button>';
$lead_form_checked   =   '';

$multi_rating_switch            =   lp_theme_option('lp_multirating_switch');
$multi_rating_switch_option     =   get_option('lp_multirating_switch');
$multi_rating_settings_url      =   admin_url('admin.php?page=reviews_settings');
$multi_rating_active            =   '';
$multi_rating_button            =   '<button data-target="' . $multi_rating_settings_url . '" class="visualizer-reviews-button">DISABLED</button>';
$multi_rating_check             =   '';


if ($submit_form_builder_state == '1') {
    $form_builder_active    =   'active';
    $form_builder_button    =   '<button data-target="' . $form_builder_url . '" class="visualizer-form-builder-button active">EDIT</button>';
    $form_builder_checked   =   'checked="checked"';
}
if ($multi_rating_switch == 1 || $multi_rating_switch_option == 1) {
    $multi_rating_active            =   'active';
    $multi_rating_check             =   'checked="checked"';
    $multi_rating_button            =   '<button data-target="' . $multi_rating_settings_url . '" class="visualizer-reviews-button active">Edit</button>';
}

if ($lead_form_state == 'yes') {
    $lead_form_active   =   'active';
    $lead_form_checked  =   'checked="checked"';
    $lead_form_button   =   '<button data-target="' . $lead_form_url . '" class="visualizer-lead-form-button active">EDIT</button>';
}
$content = get_option('lp_overview_api_content');
?>
<div class="lp-cc-dashboard-content-container">
    <div class="lp-cc-dashboard-navbar">
        <div class="lp-cc-dashboard-navbar-brand">
            <img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/logo.png" />
        </div>
        <div class="lp-cc-dashboard-navbar-nav">
            <span>Need Customization?<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-need-customization">Request Free Quote</a></span>
            <span class="or"></span>
            <span>Need Help?<a href="https://docs.listingprowp.com/" target="_blank">Go to Help Center</a></span>
        </div>
    </div>
    <div class="lp-cc-dashboard-content-header">
        <p class="lp-cc-dashboard-content-header-title">Welcome to ListingPro Command Center!</p>
        <?php
        $verification_check =   get_option('theme_activation');
        if ($verification_check != 'activated') {
        ?>
            <p class="lp-cc-dashboard-content-header-title-des">ListingPro is now installed and ready to use! Get ready to
                built your dream directory. Please <a href="<?php echo admin_url('admin.php?page=lp-cc-license'); ?>">activate your license</a> to get automatic updates, enable
                Visualiz
                <wbr>
                er modules, install free Add-ons and more. We hope you enjoy it
            </p>
        <?php
        }
        ?>
    </div>
    <div class="lp-cc-dashboard-content-btn-group">
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=listingpro" class="float-l">Dashboard</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-addons" class="float-l">Add-ons</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-visualizer" class="active float-l">Visualizer</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-license" class="float-l">License</a>
        <?php if (isset($content['listingpro_options']['services_link']) &&  !empty($content['listingpro_options']['services_link'])) { ?>
            <a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-services-link float-l">Services</a>
        <?php } ?>
    </div>
</div>
<div class="lp-cc-visualizer-content-area">
    <div class="lp-cc-visualizer-content-card-container">
        <div class="lp-cc-visualizer-content-card">
            <div class="lp-cc-visualizer-card-header">
                <p class="lp-cc-visualizer-card-header-heading"> <?php esc_html_e('Multi-Criteria Rating', 'listingpro-plugin') ?> <i class="fa fa-info"><span class="toottip">On this page all the listings will appear post search or by going directly to a location or category.</span></i> </p>
                <label class="switch pull-right <?php echo $multi_rating_active; ?> switch-cc-multi-rating">
                    <input <?php echo $multi_rating_check; ?> value="Yes" class="form-control switch-checkbox lp-cc-visualizer-item-switch cc-multi-rating-switch" type="checkbox" name="">
                    <div class="slider round"></div>
                </label>
            </div>
            <div class="lp-cc-visualizer-card-content">
                <img src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/mockup-multirate.png" alt="image">
                <?php echo $multi_rating_button; ?>
            </div>
        </div>
        <?php
        if (!class_exists('MedicalPro')) {
        ?>
            <div class="lp-cc-visualizer-content-card">
                <div class="lp-cc-visualizer-card-header">
                    <p class="lp-cc-visualizer-card-header-heading"><?php esc_html_e('FES Form Builder', 'listingpro-plugin') ?> <i class="fa fa-info"><span class="toottip">On this page all the listings will appear post search or by going directly to a location or category.</span></i> </p>
                    <label class="switch pull-right switch-cc-form-builder <?php echo $form_builder_active; ?>">
                        <input <?php echo $form_builder_checked; ?> value="Yes" class="form-control switch-checkbox lp-cc-visualizer-item-switch form-builder-switch" type="checkbox" name="">
                        <div class="slider round"></div>
                    </label>
                </div>
                <div class="lp-cc-visualizer-card-content">
                    <img src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/mockup-fes.png" alt="image">
                    <?php echo $form_builder_button; ?>
                </div>
            </div>
        <?php
        }
        ?>
        <?php
        if (class_exists('Listingpro_lead_form') && !class_exists('MedicalPro')) {
        ?>
            <div class="lp-cc-visualizer-content-card">
                <div class="lp-cc-visualizer-card-header">
                    <p class="lp-cc-visualizer-card-header-heading">Lead Form Builder <i class="fa fa-info"><span class="toottip">On this page all the listings will appear post search or by going directly to a location or category.</span></i> </p>
                    <label class="switch pull-right switch-cc-lead-form <?php echo $lead_form_active; ?>">
                        <input <?php echo $lead_form_checked; ?> value="Yes" class="form-control switch-checkbox lp-cc-visualizer-item-switch cc-lead-form-switch" type="checkbox" name="">
                        <div class="slider round"></div>
                    </label>
                </div>
                <div class="lp-cc-visualizer-card-content">
                    <img src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/mockup-lead.png" alt="image">
                    <?php echo $lead_form_button; ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>
<div class="lp-cc-visualizer-form-builder-active-popup">
    <span class="pull-right lp-cc-visualizer-close-form-builder-pop lp-cc-visualizer-close_form_builder"><i class="fa fa-close"></i> </span>
    <div class="lp-cc-visualizer-form-builder-active-popup-container">
        <div class="lp-cc-visualizer-form-builder-active-popup-container-header">ATTENTION!</div>
        <div class="lp-cc-visualizer-form-builder-active-popup-container-content">By activating this form builder you agree to make changes to your Front-End Submission Form for all your business listings and take full responsibility.</div>
        <div class="lp-cc-visualizer-form-builder-active-popup-container-footer">
            <button class="lp-cc-visualizer-btn lp-cc-visualizer-info lp-cc-visualizer-close_form_builder">YES I AGREE</button>
        </div>
    </div>
</div>
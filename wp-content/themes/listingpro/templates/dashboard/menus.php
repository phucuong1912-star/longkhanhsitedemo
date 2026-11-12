<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$m_args = array(
    'post_type' => 'listing',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_id,
    'meta_key' => 'lp-listing-menu',
    'meta_compare' => 'EXISTS'
);
$m_listings = new WP_Query($m_args);
$count_m_listings = $m_listings->found_posts;


$menu_groups_data = get_user_meta($user_id, 'user_menu_groups');
$menu_groups_data = isset($menu_groups_data[0]) ? $menu_groups_data[0] : '';

$menu_types_data = get_user_meta($user_id, 'user_menu_types');
$menu_types_data = isset($menu_types_data[0]) ? $menu_types_data[0] : '';


$argsActive = array(
    'author' => get_current_user_id(),
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_type' => 'listing',
    'post_status' => 'publish',
    'meta_query' =>
        array(
            array(
                'key' => 'menu_listing',
                'compare' => 'EXIST'
            )
        ),
);
$Active_array = get_posts($argsActive);

$currentURL = '';
$perma = '';
$dashQuery = 'dashboard=';
$currentURL = get_permalink();
global $wp_rewrite;
if ($wp_rewrite->permalink_structure == '') {
    $perma = "&";
} else {
    $perma = "?";
}
global $listingpro_options;
$image_gallery = '';
$image_gallery_opt = $listingpro_options['menu_gallery_dashoard'];
if ($image_gallery_opt == 1) {
    $image_gallery = 'data-multiple="true"';
}
$img_menu_dashoard_show = lp_theme_option('img_menu_dashoard');
if ($img_menu_dashoard_show == 0) {
    $img_menu_dashoard_show = false;
} else {
    $img_menu_dashoard_show = true;
}

$ordering_services = array();
$get_ordering_services = get_user_meta($user_id, 'order_services', true);
if (!empty($get_ordering_services)) {
    $ordering_services = $get_ordering_services;
}

?>
<script>
    jQuery(document).ready(function (e) {
        if (jQuery('.menu-tabs').length != 0) {
            jQuery('.menu-tabs').each(function (index) {
                var targetTabID = jQuery(this).attr('id');
                jQuery('#' + targetTabID).tabs();
            });
        }
        if (jQuery('.menu-type-count-val').length != 0) {
            jQuery('.menu-type-count-val').each(function (index) {
                var targetMenuCount = jQuery(this).attr('id'),
                    menuCountVal = jQuery(this).val();

                jQuery('.' + targetMenuCount).text(jQuery('#' + targetMenuCount).val());
            });
        }
        if (jQuery('.menu-group-count-val').length != 0) {
            jQuery('.menu-group-count-val').each(function (index) {
                var targetMenuCount = jQuery(this).attr('id'),
                    menuCountVal = jQuery(this).val();

                jQuery('.' + targetMenuCount).text(jQuery('#' + targetMenuCount).val());
            });
        }
        if (jQuery('.menu-items-count-val').length != 0) {
            jQuery('.menu-items-count-val').each(function (index) {
                var targetMenuCount = jQuery(this).attr('id'),
                    menuCountVal = jQuery(this).val();

                jQuery('.' + targetMenuCount).text(jQuery('#' + targetMenuCount).val());
            });
        }
    });
</script>
<?php
ajax_response_markup();
?>
<!-- Modal -->
<div class="modal fade" id="dashboard-delete-modal2" tabindex="-1" role="dialog"
     aria-labelledby="dashboard-delete-modal2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p><input type="radio" id="delete-group-type-yes" name="delete-group-type" value="1" checked> <label
                            for="delete-group-type-yes"><?php echo esc_html__('Delete all related data', 'listingpro'); ?></label>
                </p>
                <p><input type="radio" id="delete-group-type-no" name="delete-group-type" value="0"> <label
                            for="delete-group-type-no"><?php echo esc_html__('Keep already added data', 'listingpro'); ?></label>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                <button type="button"
                        class="btn btn-primary dashboard-confirm-del-btn"><?php echo esc_html__('Delete', 'listingpro'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dashboard-delete-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-delete-modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo esc_html__('are you sure you want to delete?', 'listingpro'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                <button type="button"
                        class="btn btn-primary dashboard-confirm-del-btn"><?php echo esc_html__('Delete', 'listingpro'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dashboard-delete-modal3" tabindex="-1" role="dialog"
     aria-labelledby="dashboard-delete-modal3" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo esc_html__('are you sure you want to delete all menus of this listing?', 'listingpro'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                <button type="button"
                        class="btn btn-primary dashboard-confirm-del-menu-btn"><?php echo esc_html__('Delete', 'listingpro'); ?></button>
            </div>
        </div>
    </div>
</div>


<div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 aligncenter padding-top-30" id="lp-all-menus">
    <div class="clearfix"></div>
    <div class="lp-add-menu-outer clearfix lp-all-menu-btns">
        <!--        <h5 class="view_all_menu_trigger">--><?php //esc_html_e('All Menus', 'listingpro'); ?><!--</h5>-->
        <!--        <div class="pull-right">-->
        <!--            <a class="online_order_food"><i class="fa fa-truck" aria-hidden="true"></i> -->
        <?php //echo esc_html_e('ONILNE FOOD ORDERING SERVICES', 'listingpro'); ?><!--</a>-->
        <!--            <a class="image_menu_trigger"><i class="fa fa-picture-o" aria-hidden="true"></i> -->
        <?php //echo esc_html_e('upload image menu', 'listingpro'); ?><!-- </a>-->
        <!--            <button data-form="menus" class="lp-add-new-btn add-new-open-form menu-add-new"><span><i class="fa fa-plus" aria-hidden="true"></i></span> -->
        <?php //esc_html_e('Add new', 'listingpro'); ?><!--</button>-->
        <!--        </div>-->

        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#all_menu_wrapper" data-toggle="tab" aria-expanded="true"><?php esc_html_e('ALL Menus', 'listingpro'); ?></a>
                </li>
                <?php
                if ( $img_menu_dashoard_show == 'true'){
                    ?>
                    <li class="">
                        <a href="#image_menu_wrapper" data-toggle="tab" aria-expanded="false"><?php esc_html_e('upload image menu', 'listingpro'); ?></a>
                    </li>
                <?php } ?>
                <li class="">
                    <a href="#ordering_service_wrapper" data-toggle="tab" aria-expanded="false"><?php esc_html_e('ONLINE FOOD ORDERING
                        SERVICES', 'listingpro'); ?></a>
                </li>
                <li class="pull-right">
                    <button data-form="menus" class="lp-add-new-btn add-new-open-form menu-add-new"><span><i
                                    class="fa fa-plus"
                                    aria-hidden="true"></i></span> <?php esc_html_e('Add new', 'listingpro'); ?>
                    </button>
                </li>
            </ul>
        </div>

    </div>


    <div class="tab-content lp-tab-content-outer">


        <div class="tab-pane fade in active all_menu_wrapper" id="all_menu_wrapper">
            <div class="lp-main-title clearfix">
                <div class="col-md-3"><p><?php esc_html_e('menu assigned to', 'listingpro'); ?></p></div>
            </div>
            <div class="tab-content clearfix" style="background: #fff;">
                <div class="tab-pane fade in active" id="tab1default">
                    <?php
                    if ($m_listings->have_posts()): while ($m_listings->have_posts()): $m_listings->the_post();
                        $listing_id = get_the_id();
                        ?>
                        <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                            <div class="col-md-10 lp-content-before-after">
                                <div class="lp-announcement-title">
                                    <a href="<?php echo get_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
                                </div>
                            </div>

                            <div class="col-md-2 text-left lp-content-before-after"
                                 data-content="<?php esc_html_e('On/Off', 'listingpro'); ?>">
                                <div class="clearfix">


                                    <div class="pull-right lp-pull-left-new">
                                        <div class="lp-dot-extra-buttons">
                                            <img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABtSURBVEhLYxgFgwN4R2UKekXl7gJhEBsqTDnwiM4N8YrO/Q/GUTlBUGHKAciVntG5O0DYJTSNHyo8UoFnVI61V0yuFZRLHQAyEBZ5PpHZllBhygHIMKjB/6hqMAiADKS6oUMPjGbpUUANwMAAAIAtN4uDPUCkAAAAAElFTkSuQmCC">
                                            <ul class="lp-user-menu list-style-none">
                                                <li><a class="open-edit-this-menu" href=""
                                                       data-targetID="<?php echo esc_attr($listing_id); ?>"
                                                       data-targettitle="<?php echo the_title(); ?>"
                                                       data-uid="<?php echo esc_attr($user_id); ?>"><i
                                                                class="fa fa-pencil"></i><span><?php esc_html_e('Edit', 'listingpro'); ?></span></a>
                                                </li>
                                                <li><a class="dash-menu-del del-this-menu" href=""
                                                       data-targetID="<?php echo esc_attr($listing_id); ?>"
                                                       data-uid="<?php echo esc_attr($user_id); ?>"><i
                                                                class="fa fa-trash-o"></i><span><?php esc_html_e('Delete', 'listingpro'); ?></span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php endwhile;  else: echo '<span class="margin-left-35">'.esc_html__('No menu items added yet.', 'listingpro').'</span>'; endif; ?>
                </div>
            </div>
        </div>

        <?php
        if ( $img_menu_dashoard_show == 'true'){
            ?>
            <div class="lp-general-section-title-outer image_menu_wrapper tab-pane fade in" id="image_menu_wrapper">
                <p id="reply-title"
                   class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Upload Image Menu', 'listingpro'); ?>
                    <i class="fa fa-angle-down" aria-hidden="true"></i></p>
                <div class="ordering-service-wrap">
                    <ul class="listing_append_img_menu">
                        <?php
                        foreach ($Active_array as $list) {

                            ?>
                            <li class="clearfix">
                                <a class="pull-left online_ordring_list_title" target="_blank"
                                   href="<?php echo get_permalink($list->ID); ?>">
                                    <?php echo esc_attr($list->post_title); ?>
                                </a>
                                <span data-uid="<?php echo esc_attr($list->post_author); ?>" data-target="<?php echo esc_attr($list->ID); ?>"
                                      class="pull-right del_img_menu">
                                        <i class="fa fa-trash fa-spinner"></i>
                                    </span>
                            </li>
                        <?php }
                        if (empty($Active_array) || count($Active_array) == 0) {
                            echo '<li class="no-services no-img_menu">' . esc_html__('No Image Menu Added', 'listingpro') . '</li>';
                        }
                        ?>
                    </ul>
                    <div class="add-new-service_img_menu">
                        <div class="margin-top-0 lp-listing-selecter-drop select_listing_for_img_menu">
                            <div class="lp-pp-noa-tip">
                                <i class="fa fa-exclamation"
                                   aria-hidden="true"></i> <?php echo esc_html__('Menu not allowed with this listing. Please upgrade your plan.', 'listingpro'); ?>
                            </div>
                            <?php
                            echo lp_get_listing_dropdown('add_image_menu', 'select2-ajax', 'add_image_menu', 'menu', null);
                            ?>
                        </div>
                        <div class="form-group add-menu_image">
                            <div class="upload-field img-menu-upload-field" data-multiple="true" style="position: relative">
                                <?php echo do_shortcode('[frontend-button]'); ?>
                                <div class="menu-edit-imgs-wrap image-menu-multiple-wrap">
                                    <input name="frontend-input-multiple" class="frontend-input-multiple"
                                           id="selected_image_menu_url" type="hidden" value="">
                                </div>
                            </div>
                            <i id="add-menu-image_btn" data-uid="<?php echo esc_attr($user_id); ?>"
                               class="fa fa-plus-square"></i>
                            <i id="add-menu-img-spinner" class="fa fa-spinner fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="lp-general-section-title-outer ordering_service_wrapper tab-pane fade in"
             id="ordering_service_wrapper">
            <p id="reply-title"
               class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('ONLINE FOOD ORDERING SERVICES', 'listingpro'); ?>
                <i class="fa fa-angle-down" aria-hidden="true"></i></p>
            <div class="ordering-service-wrap">
                <ul>
                    <?php
                    if (empty($ordering_services) || count($ordering_services) == 0) {
                        echo '<li class="no-services">' . esc_html__('No Service Added', 'listingpro') . '</li>';
                    } else {
                        foreach ($ordering_services as $k => $ordering_service) {
                            echo '<li><a href="' . $k . '" target="_blank">' . $ordering_service . '</a> <span class="del-order-service" data-uid="' . $user_id . '" data-target="' . $k . '"><i class="fa fa-trash"></i></span></li>';
                        }
                    }
                    ?>
                </ul>
                <div class="add-new-service">
                    <select class="online-order-type"
                            placeholder="<?php echo esc_html__('e.g SALAD', 'listingpro'); ?>">
                        <option><?php echo esc_html__('Grubhub', 'listingpro'); ?></option>
                        <option><?php echo esc_html__('Zomato', 'listingpro'); ?></option>
                        <option><?php echo esc_html__('Foodpanda', 'listingpro'); ?></option>
                        <option><?php echo esc_html__('UberEats', 'listingpro'); ?></option>
                    </select>
                    <div class="form-group menu_online_order_url">
                        <input type="url" id="service_url" class="form-control"
                               placeholder="<?php echo esc_html__('Service URL', 'listingpro'); ?>">
                        <i id="add-new-service" data-uid="<?php echo esc_attr($user_id); ?>" class="fa fa-plus-square"></i>
                        <i id="add-new-service-spinner" class="fa fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>


<div class="tab-pane fade in active clearfix lp-dashboard-menu-container" style="display: none;" id="lp-menus" <?php echo 'data-sitem ="'.esc_html__('Item Name','listingpro').'" '.'data-sgroup ="'.esc_html__('Group','listingpro').'" '.'data-sprice ="'.esc_html__('Price','listingpro').'" '.'data-sedit ="'.esc_html__('Edit','listingpro').'" '.'data-sdelete ="'.esc_html__('Delete','listingpro').'" '.'data-squote ="'.esc_html__('Quote Text','listingpro').'" '.'data-slink ="'.esc_html__('Quote Link','listingpro').'" '.'data-srprice ="'.esc_html__('Reg. Price','listingpro').'" '.'data-ssprice ="'.esc_html__('Sale Price','listingpro').'" '.'data-smitem ="'.esc_html__('Menu Item','listingpro').'" '.'data-sdesc ="'.esc_html__('Short Description','listingpro').'" '.'data-spop ="'.esc_html__('Popular Item','listingpro').'" '.'data-sspice ="'.esc_html__('Spice Level','listingpro').'" '.'data-scancel ="'.esc_html__('Cancel','listingpro').'" '.'data-ssave ="'.esc_html__('save','listingpro').'" '.'data-spexquote ="'.esc_html__('Ex: Quote','listingpro').'" '.'data-spprice ="'.esc_html__('Ex: $10','listingpro').'" '.'data-sptitle ="'.esc_html__('Ex: Roasted Chicken','listingpro').'" '.'data-surl ="'.esc_html__('Ex: hht://yourweb.com/page','listingpro').'" ';?>>
    <div class="col-md-9 lp-compaignForm-leftside">
        <div class="clearfix margin-top-20">
            <a href="" class="lp-view-all-btn all-with-refresh"><i class="fa fa-angle-left"
                                                                   aria-hidden="true"></i> <?php echo esc_html__('All Menus', 'listingpro'); ?>
            </a>
            <h5 class="margin-top-0 clearfix margin-bottom-20"><?php echo esc_html__('Create Menu Item(s)', 'listingpro'); ?>
                <a data-imgsrc="<?php echo get_template_directory_uri(); ?>/assets/images/examples/example-menu.jpg"
                   data-expandimage="bird" id="pop" href="" class="lp-view-larg-btn"><i class="fa fa-eye"
                                                                                        aria-hidden="true"></i> <?php echo esc_html__('Full View Example', 'listingpro'); ?>
                </a>
            </h5>

        </div>
        <div class="">
            <div class="lp-coupon-box-row lp-listing-selecter clearfix background-white lp-listing-selecter-outer padding-top-0">
                <div class="row">

                    <div class="col-md-12">
                        <label class="lp-dashboard-left-label"
                               for="announcements-listing"><?php esc_html_e('Choose a listing for the menu item(s) *', 'listingpro'); ?></label>
                    </div>
                    <div class="form-group col-sm-12 margin-top-0 margin-bottom-0">
                        <div class="margin-top-0 lp-listing-selecter-drop">
                            <div class="lp-pp-noa-tip">
                                <i class="fa fa-exclamation"
                                   aria-hidden="true"></i> <?php echo esc_html__('Menu not allowed with this listing. Please upgrade your plan.', 'listingpro'); ?>
                            </div>
                            <?php
                            echo lp_get_listing_dropdown('menu-listing', 'select2-ajax', 'menu-listing', 'menu', null);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height lp-left-panel-height-outer padding-bottom0">

            <div class="lp-menu-step-one margin-top-20">
                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li>
                            <p class="pull-left margin-right-10"><?php esc_html_e('Types', 'listingpro'); ?></p>
                            <div class="help-text margin-0" style="width: 100%;float: none;clear: both;">
                                <a class="help padding-0 margin-0 " target="_blank" style="margin-top: 5px !important;"> <i class="fa fa-info"></i>
                                    <div class="help-tooltip" style="left: -25px !important;">
                                        <p><?php echo esc_html__('Select Menu Type. Add New Menu Type Form Sidebar.', 'listingpro'); ?></p>
                                    </div>
                                </a>
                            </div>
                        </li>
                        <?php
                        if (is_array($menu_types_data) && count($menu_types_data)) {
                            $menu_type_counter = 1;

                            foreach ($menu_types_data as $k => $item) {
                                $menu_type_active = '';
                                if ($menu_type_counter == 1) {
                                    $menu_type_active = 'active';
                                }
                                echo '<li class="' . $menu_type_active . '"><a href="#type-' . str_replace(' ', '-', $item['type']) . '" data-toggle="tab">' . $item['type'] . '</a></li>';
                                $menu_type_counter++;
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="panel-body lp-panel-body-outer lp-menu-panel-body-outer"></div>
            </div>

            <div class="lp-listing-selecter-drop lp-coupon-box-row col-sm-12 clearfix margin-bottom-0">
                <p class="lp-label-menu-group lp-dashboard-left-label pull-left"
                   for="menu-group"><label><?php esc_html_e('GROUPS', 'listingpro'); ?> </label></p>
                <div class="help-text margin-0" style="width: 100%;float: none;clear: both;">
                    <a class="help padding-0 margin-0 " target="_blank" style="margin-top: 15px !important;margin-left: 10px !important;"> <i class="fa fa-info"></i>
                        <div class="help-tooltip" style="left: -25px !important;">
                            <p><?php echo esc_html__('Select Menu Group. Add New Menu Group Form Sidebar.', 'listingpro'); ?></p>
                        </div>
                    </a>
                </div>


                <select multiple id="menu-group" name="menu-group" class="form-control select2">
                    <?php
                    if ($menu_groups_data && is_array($menu_groups_data) && !empty($menu_groups_data)):
                        foreach ($menu_groups_data as $menu_group):
                            echo '<option>' . $menu_group['group'] . '</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div style="display: none;" id="menu-form-toggle" class="lp-menu-step-two margin-top-10">
                <div class="col-md-12 padding-0">
                    <div class="lp-menu-close-outer lp-menu-open">
                        <div class="lp-menu-form-outer background-white">
                            <div class="lp-menu-form-inner">
                                <form class="row">

                                    <div class="col-sm-12">
                                        <div class="lp-menu-form-feilds row">
                                            <div class="margin-bottom-10 col-md-8">
                                                <label class="lp-dashboard-top-label"
                                                       for="menu-title"><?php esc_html_e('Menu Item Name', 'listingpro'); ?>
                                                    <span>*</span></label>
                                                <input name="menu-title" id="menu-title" type="text"
                                                       class=" lp-dashboard-text-field form-control"
                                                       placeholder="<?php echo esc_html__('Ex: Roasted Chicken', 'listingpro'); ?>">
                                            </div>
                                            <div class="menu-price-wrap clearfix">
                                                <div class="col-sm-2 padding-left-0">
                                                    <label class="lp-dashboard-top-label"
                                                           for="menu-old-price"><?php esc_html_e('Reg. Price', 'listingpro'); ?></label>
                                                    <input id="menu-old-price" type="text"
                                                           class=" lp-dashboard-text-field form-control"
                                                           placeholder="<?php echo esc_html__('Ex: $10', 'listingpro'); ?>">
                                                </div>
                                                <div class="col-sm-2 padding-left-0">
                                                    <label class="lp-dashboard-top-label"
                                                           for="menu-new-price"><?php esc_html_e('Sale Price', 'listingpro'); ?></label>
                                                    <input id="menu-new-price" name="menu-new-price" type="text"
                                                           class=" lp-dashboard-text-field form-control"
                                                           placeholder="<?php echo esc_html__('Ex: $7', 'listingpro'); ?>">
                                                </div>
                                            </div>
                                            <div class="margin-bottom-10 col-md-12">
                                                <label class="lp-dashboard-top-label"
                                                       for="menu-detail"><?php esc_html_e('Menu Item Description', 'listingpro'); ?>
                                                    <span>*</span></label>
                                                <textarea name="menu-detail" id="menu-detail" type="text"
                                                          class="form-control lp-dashboard-des-field" rows="3"
                                                          placeholder="<?php echo esc_html__('e.g. This delicious roasted chicken is marinated in...', 'listingpro'); ?>"></textarea>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="clearfix">
                                                <div class="lp-invoices-all-stats clearfix coupons-fields-switch padding-0">
                                                    <div class="lp-invoices-all-stats-on-off lp-invoices-all-stats-on-off-switcher">
                                                        <h5 class="col-md-12 ">
                                                            <label class="switch">
                                                                <input data-target="quote-button"
                                                                       class="form-control switch-checkbox"
                                                                       type="checkbox">
                                                                <div class="slider round"></div>
                                                            </label>
                                                            <?php esc_html_e('External Menu Item URL', 'listingpro'); ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="menu-quote-wrap clearfix margin-bottom-20"
                                                     style="display: none">
                                                    <div class="col-sm-6">
                                                        <label class="lp-dashboard-top-label"
                                                               for="menu-quote-text"><?php esc_html_e('Button Name', 'listingpro'); ?>
                                                            <i class="fa fa-info-circle"></i></label>
                                                        <input id="menu-quote-text" type="text"
                                                               class="lp-dashboard-text-field form-control"
                                                               placeholder="<?php echo esc_html__('e.g. CLICK HERE', 'listingpro'); ?>">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="lp-dashboard-top-label"
                                                               for="menu-quote-link"><?php esc_html_e('Button URL', 'listingpro'); ?>
                                                            <i class="fa fa-info-circle"></i></label>
                                                        <input id="menu-quote-link" type="text"
                                                               class="lp-dashboard-text-field form-control"
                                                               placeholder="<?php echo esc_html__('Ex: hht://yourweb.com/page', 'listingpro'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lp-invoices-all-stats-on-off clearfix margin-bottom-10 Popular_item_container">
                                                <label class="switch">
                                                    <input value="Yes"
                                                           class="form-control switch-checkbox menu_Popular_Item"
                                                           type="checkbox" name="lp_form_fields_inn[235]">
                                                    <div class="slider round"></div>
                                                </label>
                                                <h5 class="margin-left-10"
                                                    style="font-size: 14px!important;display: inline-block;"><?php esc_html_e('Make This Item As Popular Item', 'listingpro'); ?></h5>
                                            </div>
                                            <div class="clearfix margin-bottom-10 menuSpice-control_containter">
                                                <select id="menuSpice-control" class="form-control menuSpice-control"
                                                        style="width: 100%!important;margin-left: 0;">
                                                    <option><?php esc_html_e('Spice Level', 'listingpro'); ?></option>
                                                    <option data-level="1">🌶</option>
                                                    <option data-level="2">🌶🌶</option>
                                                    <option data-level="3">🌶🌶🌶</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="jFiler-input-dragDrop-title lp-dashboard-top-label"><?php esc_html_e('Upload Menu Item Images', 'listingpro'); ?>
                                            <i class="fa fa-info-circle"></i></label>
                                        <div class="jFiler-input-dragDrop pos-relative event-featured-image-wrap-dash">
                                            <div <?php echo esc_attr($image_gallery); ?>
                                                    class="upload-field dashboard-upload-field new-file-upload">
                                                <?php echo do_shortcode('[frontend-button]'); ?>
                                                <div class="menu-edit-imgs-wrap">
                                                    <input class="frontend-input-multiple" type="hidden" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="lp-menu-step-two-btn col-md-6 aligncenter padding-bottom-30">
                        <button id="lp-save-menu" data-uid="<?php echo esc_attr($user_id); ?>"><span><i class="fa fa-plus"
                                                                                                        aria-hidden="true"></i></span> <?php esc_html_e('add menu item', 'listingpro'); ?>
                        </button>
                    </div>
                </div>
                <div class="lp-coupon-box-row lp-save-btn-container">
                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-sm-12 clearfix">
                                <a href=""
                                   class="lp-unsaved-btn"><?php echo esc_html__('Unsaved Event', 'listingpro'); ?></a>
                                <button data-uid="<?php echo esc_attr($user_id); ?>"
                                        class="lp-coupns-btns all-with-refresh lp-view-all-btn pull-right"><?php echo esc_html__('View All Menu', 'listingpro'); ?></button>
                                <button data-cancel="announcements" id="cancelLpAnnouncment"
                                        class="lp-coupns-btns cancel-ad-new-btn pull-right lp-margin-right-10 cancel-add-menu"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 lp-compaignForm-righside lp-right-static">
        <div class="padding-right-0 lp-right-panel-height lp-right-panel-height-outer lp-right-static">
            <div class="lp-ad-click-outer">
                <div class="lp-general-section-title-outer">
                    <p id="reply-title"
                       class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('TYPES AND GROUPS MANAGER', 'listingpro'); ?>
                        <i class="fa fa-angle-down" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="groups-type-manager">
                        <ul class="manange-typs-groups-tabs">
                            <li class="active"
                                id="manage-types-tab"><?php echo esc_html__('ALL TYPES', 'listingpro'); ?></li>
                            <li id="manage-groups-tab"><?php echo esc_html__('ALL GROUPS', 'listingpro'); ?></li>
                        </ul>
                        <div class="manange-typs-groups-tabs-content">
                            <div class="types-group-content active-content" id="manage-types-content">
                                <ul>
                                    <?php
                                    if (is_array($menu_types_data) && count($menu_types_data) > 0) {
                                        foreach ($menu_types_data as $k => $menu_type) {
                                            ?>
                                            <li>
                                                <?php echo esc_attr($menu_type['type']); ?>
                                                <span class="del-group-type del-type del-this"
                                                      data-uid="<?php echo esc_attr($user_id); ?>"
                                                      data-targetid="<?php echo esc_attr($k); ?>"><i
                                                            class="fa fa-trash"></i></span>
                                            </li>

                                            <?php
                                        }
                                    }
                                    ?>


                                </ul>
                                <div class="add-new-type">
                                    <div class="form-group margin-bottom-0">
                                        <input type="text"
                                               placeholder="<?php echo esc_html__('e.g SALAD', 'listingpro'); ?>">
                                        <span id="add-new-type" data-uid="<?php echo esc_attr($user_id); ?>"><i
                                                    class="fa fa-plus-square"></i></span>
                                        <i id="add-new-type-spinner" class="fa fa-spinner fa-spin"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="types-group-content" id="manage-groups-content">
                                <ul>
                                    <?php
                                    if (!empty($menu_groups_data)) {
                                        foreach ($menu_groups_data as $k => $menu_group) {
                                            ?>
                                            <li>
                                                <?php echo esc_attr($menu_group['group']); ?>
                                                <span class="del-group-type del-group del-this"
                                                      data-uid="<?php echo esc_attr($user_id); ?>"
                                                      data-targetid="<?php echo esc_attr($k); ?>"><i
                                                            class="fa fa-trash"></i></span>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>

                                </ul>
                                <div class="add-new-group">
                                    <div class="form-group">
                                        <input type="text"
                                               placeholder="<?php echo esc_html__('e.g SPICY', 'listingpro'); ?>">
                                        <span id="add-new-group" data-uid="<?php echo esc_attr($user_id); ?>"><i
                                                    class="fa fa-plus-square"></i></span>
                                        <i id="add-new-group-spinner" class="fa fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
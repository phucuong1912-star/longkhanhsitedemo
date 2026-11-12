<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;


$menu_groups_data       =   get_user_meta( $user_id, 'user_menu_groups' );
$menu_groups_data       =   $menu_groups_data[0];

$menu_types_data        =   get_user_meta( $user_id, 'user_menu_types' );
$menu_types_data        =   $menu_types_data[0];

$currentURL = '';
$perma = '';
$dashQuery = 'dashboard=';
$currentURL = get_permalink();
global $wp_rewrite;
if ($wp_rewrite->permalink_structure == ''){
	$perma = "&";
}else{
	$perma = "?";
}

?>
<!-- Modal -->
<div class="modal fade" id="dashboard-delete-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-delete-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p><input type="radio" id="delete-group-type-yes" name="delete-group-type" value="1" checked> <label for="delete-group-type-yes"><?php echo esc_html__('Delete all related data', 'listingpro'); ?></label></p>
                <p><input type="radio" id="delete-group-type-no" name="delete-group-type" value="0"> <label for="delete-group-type-no"><?php echo esc_html__('Keep already added data', 'listingpro'); ?></label></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                <button type="button" class="btn btn-primary dashboard-confirm-del-btn"><?php echo esc_html__( 'Delete', 'listingpro' ); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade in active lp-coupns-form lp-manage-types-group">
    <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1default" data-toggle="tab"><?php echo esc_html__('Types', 'listingpro'); ?></a></li>
                <li><a href="#tab2default" data-toggle="tab"><?php echo esc_html__('Groups', 'listingpro'); ?></a></li>
                <button data-form="menu-groups" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('add new groups','listingpro'); ?></button>
                <button data-form="menu-types" class="lp-add-new-btn add-new-open-form "><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('add new types','listingpro'); ?></button>
                <button class="lp-add-new-btn" onclick="window.location.href = '<?php echo esc_url($currentURL . $perma . $dashQuery . 'menus'); ?>';"><span><i class="fa fa-chevron-left" aria-hidden="true"></i></span> <?php esc_html_e('Back to Menu','listingpro'); ?></button>

            </ul>
        </div>
        <div class="panel-body">
            <div class="lp-main-title clearfix">
                <div class="col-md-11"><p><?php echo esc_html__( 'Name', 'listingpro' ); ?></p></div>
                <div class="col-md-1"></div>
            </div>
            <div class="tab-content clearfix">
                <div class="tab-pane fade in active" id="tab1default">
                    <?php
                    if( $menu_types_data && is_array( $menu_types_data ) && !empty( $menu_types_data ) ):
                        foreach ( $menu_types_data as $k => $menu_type ):

                            ?>
                            <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                <div class="col-md-11">
                                    <div class="lp-deal-title">
                                        <p><?php echo esc_attr($menu_type['type']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-1 text-center maring-top8">
                                    <span class="del-group-type del-type del-this" data-uid="<?php echo esc_attr($user_id); ?>" data-targetid="<?php echo esc_attr($k); ?>"><i class="fa fa-trash-o"></i> </span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <div class="tab-pane fade" id="tab2default">
                    <?php
                    if( $menu_groups_data && is_array( $menu_groups_data ) && !empty( $menu_groups_data ) ):
                        foreach ( $menu_groups_data as $k => $menu_group ):
                            ?>
                            <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                <div class="col-md-11">
                                    <div class="lp-deal-title">
                                        <p><?php echo esc_attr($menu_group['group']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-1 text-center maring-top8">
                                    <span class="del-group-type del-group del-this" data-uid="<?php echo esc_attr($user_id); ?>" data-targetid="<?php echo esc_attr($k); ?>"><i class="fa fa-trash-o"></i> </span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>

                </div>
            </div>
        </div>
    </div>
    <div id="menu-types-form-toggle" style="display: none">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height padding-top-0">
            <div class="lp-review-sorting clearfix padding-left-0 padding-top-0">
                <h5 class="margin-top-0"><?php echo esc_html__('Add new type', 'listingpro'); ?></h5>
            </div>
            <div class="lp-coupns-form-outer">
                <div class="lp-voupon-box">
                    <form class="lp-coupons-form-inner">

                        <div class="lp-coupon-box-row">
                            <div class="row">
                                <div class="form-group col-sm-12 ">
                                    <div class="">
                                        <label for="menu-type-new"><?php echo esc_html__('Type Name', 'listingpro'); ?></label>
                                        <input data-err="<?php echo esc_html__( 'Special characters not allowed', 'listingpro' ); ?>" name="menu-type-new" id="menu-type-new" type="text" class="form-control" placeholder="<?php echo esc_html__('e.g. Lunch', 'listingpro'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lp-coupon-box-row">
                            <div class="row">

                                <div class="form-group col-sm-12 clarfix">
                                    <button data-cancel="menu-types" class="lp-coupns-btns cancel-ad-new-btn pull-left"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                    <button data-uid="<?php echo esc_attr($user_id); ?>" class="lp-coupns-btns pull-right manage-type-group-form save-new-type"><?php echo esc_html__('save', 'listingpro'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="menu-groups-form-toggle" style="display: none">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height padding-top-0">
            <div class="lp-review-sorting clearfix padding-left-0 padding-top-0">
                <h5 class="margin-top-0"><?php echo esc_html__('Add new group', 'listingpro'); ?></h5>
            </div>
            <div class="lp-coupns-form-outer">
                <div class="lp-voupon-box">
                    <form class="lp-coupons-form-inner">

                        <div class="lp-coupon-box-row">
                            <div class="row">
                                <div class="form-group col-sm-12 ">
                                    <div class="">
                                        <label for="menu-group-new"><?php echo esc_html__('Group Name', 'listingpro'); ?></label>
                                        <input data-err="<?php echo esc_html__( 'Special characters not allowed', 'listingpro' ); ?>" name="menu-group-new" id="menu-group-new" type="text" class="form-control" placeholder="<?php echo esc_html__('e.g. Spicy', 'listingpro'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lp-coupon-box-row">
                            <div class="row">

                                <div class="form-group col-sm-12 clarfix">
                                    <button data-cancel="menu-groups" class="lp-coupns-btns cancel-ad-new-btn pull-left"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                    <button data-uid="<?php echo esc_attr($user_id); ?>" class="lp-coupns-btns pull-right manage-type-group-form save-new-group"><?php echo esc_html__('save', 'listingpro'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
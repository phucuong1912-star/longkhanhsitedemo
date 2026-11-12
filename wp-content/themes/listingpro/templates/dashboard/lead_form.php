<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$a_args =   array(
    'post_type' => 'listing',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    
    'meta_key' => 'lp_lead_form',
    'meta_compare' => 'EXISTS'
);
$a_listings             =   new WP_Query($a_args);
$count_a_listings       =   $a_listings->found_posts;

?>
<!-- Modal -->

<div class="modal fade" id="dashboard-delete-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-delete-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo esc_html__( 'are you sure you want to delete?', 'listingpro' ); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                <button type="button" class="btn btn-primary dashboard-confirm-form-field-btn"><?php echo esc_html__( 'Delete', 'listingpro' ); ?></button>
            </div>
        </div>
    </div>
</div>
<?php
if( function_exists( 'popup_notification' ) )
{
    echo popup_notification();
}
?>
<div class="tab-pane fade in active" id="lp-leadform">
    <input type="hidden" id="formajaxurl" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
    <div id="lp-lead-form-outer-lead_form">
        <input type="hidden" class="lp-composer-result-lead_form" value="">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-9 lp-left-panel-height">
            <div class="lp-menu-step-one margin-top-10">
                <div class="lp-add-menu-outer clearfix">
                    <h5><?php esc_html_e('All Leads Form','listingpro'); ?></h5>
                    <button data-form="lead-form" class="lp-add-new-btn add-new-lead-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('add new form','listingpro'); ?></button>
                </div>
                <div class="panel-body">
                    <div class="lp-main-title clearfix">
                        <div class="col-md-12"><p><?php esc_html_e('LISTING NAME','listingpro'); ?></p></div>
                    </div>
                    <div class="tab-content clearfix">
                        <div class="tab-pane fade in active" id="tab1default">
                            <?php
                            if( $a_listings->have_posts() ): while ( $a_listings->have_posts() ): $a_listings->the_post();
                                global $post;
                                $lid    =   get_the_ID();
                                $lp_lead_form  =   get_post_meta( $lid, 'lp_lead_form', true );
                            ?>
                            <div class="lp-listing-outer-container clearfix lp-menu-container-outer lead-form-edit-wrap" data-lid="<?php echo esc_attr($lid); ?>">
                                <div class="col-md-11">
                                    <div class="lp-invoice-number lp-listing-form">
                                        <label>
                                            <p><?php echo get_the_title($lid); ?></p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="clearfix">
                                        <div class="pull-right">
                                            <div class="lp-dot-extra-buttons">
                                                <img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABtSURBVEhLYxgFgwN4R2UKekXl7gJhEBsqTDnwiM4N8YrO/Q/GUTlBUGHKAciVntG5O0DYJTSNHyo8UoFnVI61V0yuFZRLHQAyEBZ5PpHZllBhygHIMKjB/6hqMAiADKS6oUMPjGbpUUANwMAAAIAtN4uDPUCkAAAAAElFTkSuQmCC">
                                                <ul class="lp-user-menu list-style-none">
                                                    <li><a href="#" class="edit-lead-form" href="" data-targetid="<?php echo esc_attr($lid); ?>" data-lid="<?php echo esc_attr($lid); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-pencil-square-o"></i><span><?php echo esc_html__( 'Edit', 'listingpro' ); ?></span></a></li>
                                                    <li><a href="#" class="lf-del lp-remove-form-field" data-targetID="<?php echo esc_attr($lid); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-trash-o"></i><span><?php echo esc_html__( 'Delete', 'listingpro' ); ?></span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="display:none;" data-pid="<?php echo esc_attr($lid); ?>" id="update-wrap-<?php echo esc_attr($lid); ?>">
                                    <div class="panel-body margin-top-60 lp-edit-lead-form">
                                        <input type="hidden" class="lp-composer-result-lead_form_<?php echo esc_attr($lid); ?>" value="<?php echo esc_attr($lp_lead_form); ?>">
                                        <div class="lp-menu-step-two margin-top-20">
                                            <div class="fields-list-<?php echo esc_attr($lid); ?>">
                                                <ul class="fields-sroter-<?php echo esc_attr($lid); ?>">
                                                    <?php
                                                    echo do_shortcode( $lp_lead_form );
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="lp-menu-form-outer background-white lp-lead-form-outer-<?php echo esc_attr($lid); ?>" style="display: none">
                                                <div class="">
                                                    <form id="lp-lead-form-form-<?php echo esc_attr($lid); ?>" class="row lp-lead-form">
                                                        <div class="lp-listing-selecter clearfix">

                                                            <div class="form-group col-sm-6 ">
                                                                <div class="lp-listing-selecter-content">
                                                                    <h5><?php echo esc_html__( 'Select a Field Type', 'listingpro' ); ?></h5>
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="lp-listing-selecter-drop">
                                                                    <select data-pid="<?php echo esc_attr($lid); ?>" class="form-control select2 field-type" name="field-type-<?php echo esc_attr($lid); ?>" id="field-type-<?php echo esc_attr($lid); ?>">
                                                                        <option value="text"><?php echo esc_html__( 'Text', 'listingpro' ); ?></option>
                                                                        <option value="email"><?php echo esc_html__( 'Email', 'listingpro' ); ?></option>
                                                                        <option value="tel"><?php echo esc_html__( 'Phone', 'listingpro' ); ?></option>
                                                                        <option value="url"><?php echo esc_html__( 'Url', 'listingpro' ); ?></option>
                                                                        <option value="date"><?php echo esc_html__( 'Date', 'listingpro' ); ?></option>
                                                                        <option value="time"><?php echo esc_html__( 'Time', 'listingpro' ); ?></option>
                                                                        <option value="datetime-local"><?php echo esc_html__( 'Date-Time Local', 'listingpro' ); ?></option>
                                                                        <option value="radio"><?php echo esc_html__( 'Radio', 'listingpro' ); ?></option>
                                                                        <option value="checkbox"><?php echo esc_html__( 'Checkbox', 'listingpro' ); ?></option>
                                                                        <option value="dropdown"><?php echo esc_html__( 'Dropdown', 'listingpro' ); ?></option>
                                                                        <option value="range"><?php echo esc_html__( 'Range', 'listingpro' ); ?></option>
                                                                        <option value="file"><?php echo esc_html__( 'File', 'listingpro' ); ?></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-sm-12">
                                                            <label for="field-label-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Label', 'listingpro'); ?></label>
                                                            <input name="field-label-<?php echo esc_attr($lid); ?>" id="field-label-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('Full Name', 'listingpro'); ?>">
                                                        </div>
                                                        <div class="form-group col-sm-12">
                                                            <label for="field-name-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Name', 'listingpro'); ?></label>
                                                            <input name="field-name-<?php echo esc_attr($lid); ?>" id="field-name-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('Name', 'listingpro'); ?>">
                                                        </div>
                                                        <div class="form-group col-sm-12 options-field" style="display: none;">
                                                            <label for="field-options-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Options', 'listingpro'); ?></label>
                                                            <textarea name="field-options-<?php echo esc_attr($lid); ?>" id="field-options-<?php echo esc_attr($lid); ?>" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group col-sm-12 multiselect-field" style="display: none">
                                                            <label for="field-multi-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Multi Select', 'listingpro'); ?></label>
                                                            <textarea name="field-multi-<?php echo esc_attr($lid); ?>" name="field-multi-<?php echo esc_attr($lid); ?>" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group col-sm-12 field-placeholder">
                                                            <label for="field-placeholder-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Placeholder', 'listingpro'); ?></label>
                                                            <input name="field-placeholder-<?php echo esc_attr($lid); ?>" id="field-placeholder-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('Placeholder', 'listingpro'); ?>">
                                                        </div>
                                                        <div class="clearfix range-extra" style="display: none;">
                                                            <div class="form-group col-sm-6">
                                                                <label for="min-val-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Minimum Value', 'listingpro'); ?></label>
                                                                <input name="min-val-<?php echo esc_attr($lid); ?>" id="min-val-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.1', 'listingpro'); ?>">
                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <label for="max-val-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Maximum Value', 'listingpro'); ?></label>
                                                                <input name="max-val-<?php echo esc_attr($lid); ?>" id="max-val-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.100', 'listingpro'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="clearfix range-extra" style="display: none;">
                                                            <div class="form-group col-sm-6">
                                                                <label for="def-val-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Default Value', 'listingpro'); ?></label>
                                                                <input  name="def-val-<?php echo esc_attr($lid); ?>" id="def-val-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.1', 'listingpro'); ?>">
                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <label for="step-val-<?php echo esc_attr($lid); ?>"><?php echo esc_html__('Step Value', 'listingpro'); ?></label>
                                                                <input name="step-val-<?php echo esc_attr($lid); ?>" id="step-val-<?php echo esc_attr($lid); ?>" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.100', 'listingpro'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="clearfix margin-top-40">
                                                            <div class="form-group col-sm-6 margin-0 padding-top-15">
                                                                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                                                    <span><?php echo esc_html__('required field', 'listingpro'); ?></span>
                                                                    <label class="switch">
                                                                        <input class="form-control switch-checkbox" type="checkbox" id="field-required-<?php echo esc_attr($lid); ?>" name="field-required-<?php echo esc_attr($lid); ?>">
                                                                        <div class="slider round"></div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-6 text-right margin-0">
                                                                <button class="lp-coupns-btns"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                                                <button class="lp-coupns-btns add-form-field form-field-front" data-pid="<?php echo esc_attr($lid); ?>"><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lp-coupon-box-row">
                                            <div class="row">
                                                <div class="form-group col-sm-6 margin-0">

                                                </div>
                                                <div class="form-group col-sm-6 text-right margin-top-20">
                                                    <button class="lp-coupns-btns cancel-update"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                                    <button class="lp-coupns-btns lp-save-template lp-save-template-loop" data-pid="<?php echo esc_attr($lid); ?>"><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            endwhile; wp_reset_postdata(); else:
                            echo esc_html__( 'no form found', 'listingpro' );
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: none;" id="lead-form-form-toggle">
                <div class="lp-menu-step-two margin-top-20">
                    <div class="lp-add-menu-outer clearfix">
                        <h5><?php esc_html_e('Create Form','listingpro'); ?></h5>
                    </div>
                    <div class="fields-list">
                        <ul class="fields-sroter">
                            <li data-name="name" data-shortcode="[lp-customizer-field type='text' name='name7' placeholder='Name:' class='myclass' label='Name']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php esc_html_e('Name','listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="email" data-shortcode="[lp-customizer-field type='email' name='email7' placeholder='Email:' class='myclass' label='Email']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php esc_html_e('Email','listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="phone" data-shortcode="[lp-customizer-field type='text' name='phone7' placeholder='Phone:' class='myclass' label='Phone']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php esc_html_e('Phone','listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="message" data-shortcode="[lp-customizer-field type='textarea' name='message7' placeholder='Message:' class='myclass' label='Message']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php esc_html_e('Message','listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body lp-add-new-filed-container">
                        <div class="lp-menu-form-outer background-white lp-lead-form-outer" style="display: none">
                            <div class="">
                                <form class="row lp-lead-form">
                                    <div class="lp-listing-selecter clearfix">

                                        <div class="form-group col-sm-6 ">
                                            <div class="lp-listing-selecter-content">
                                                <h5><?php echo esc_html__( 'Select a Field Type', 'listingpro' ); ?></h5>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 ">
                                            <div class="lp-listing-selecter-drop">
                                                <select class="form-control select2 field-type" name="field-type" id="field-type">
                                                    <option value="text"><?php echo esc_html__( 'Text', 'listingpro' ); ?></option>
                                                    <option value="email"><?php echo esc_html__( 'Email', 'listingpro' ); ?></option>
                                                    <option value="tel"><?php echo esc_html__( 'Phone', 'listingpro' ); ?></option>
                                                    <option value="url"><?php echo esc_html__( 'Url', 'listingpro' ); ?></option>
                                                    <option value="date"><?php echo esc_html__( 'Date', 'listingpro' ); ?></option>
                                                    <option value="time"><?php echo esc_html__( 'Time', 'listingpro' ); ?></option>
                                                    <option value="datetime-local"><?php echo esc_html__( 'Date-Time Local', 'listingpro' ); ?></option>
                                                    <option value="radio"><?php echo esc_html__( 'Radio', 'listingpro' ); ?></option>
                                                    <option value="checkbox"><?php echo esc_html__( 'Checkbox', 'listingpro' ); ?></option>
                                                    <option value="dropdown"><?php echo esc_html__( 'Dropdown', 'listingpro' ); ?></option>
                                                    <option value="range"><?php echo esc_html__( 'Range', 'listingpro' ); ?></option>
                                                    <option value="file"><?php echo esc_html__( 'File', 'listingpro' ); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="field-label"><?php echo esc_html__('Label', 'listingpro'); ?></label>
                                        <input name="field-label" id="field-label" class="form-control" type="text" placeholder="<?php echo esc_html__('Full Name', 'listingpro'); ?>">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="field-name"><?php echo esc_html__('Name', 'listingpro'); ?></label>
                                        <input name="field-name" id="field-name" class="form-control" type="text" placeholder="<?php echo esc_html__('Name', 'listingpro'); ?>">
                                    </div>
                                    <div class="form-group col-sm-12 options-field" style="display: none;">
                                        <label for="field-options"><?php echo esc_html__('Options', 'listingpro'); ?></label>
                                        <textarea name="field-options" id="field-options" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-sm-12 multiselect-field" style="display: none">
                                        <label for="field-multi"><?php echo esc_html__('Multi Select', 'listingpro'); ?></label>
                                        <textarea name="field-multi" name="field-multi" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-sm-12 field-placeholder">
                                        <label for="field-placeholder"><?php echo esc_html__('Placeholder', 'listingpro'); ?></label>
                                        <input name="field-placeholder" id="field-placeholder" class="form-control" type="text" placeholder="<?php echo esc_html__('Placeholder', 'listingpro'); ?>">
                                    </div>
                                    <div class="clearfix range-extra" style="display: none;">
                                        <div class="form-group col-sm-6">
                                            <label for="min-val"><?php echo esc_html__('Minimum Value', 'listingpro'); ?></label>
                                            <input name="min-val" id="min-val" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.1', 'listingpro'); ?>">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="max-val"><?php echo esc_html__('Maximum Value', 'listingpro'); ?></label>
                                            <input name="max-val" id="max-val" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.100', 'listingpro'); ?>">
                                        </div>
                                    </div>
                                    <div class="clearfix range-extra" style="display: none;">
                                        <div class="form-group col-sm-6">
                                            <label for="def-val"><?php echo esc_html__('Default Value', 'listingpro'); ?></label>
                                            <input  name="def-val" id="def-val" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.1', 'listingpro'); ?>">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="step-val"><?php echo esc_html__('Step Value', 'listingpro'); ?></label>
                                            <input name="step-val" id="step-val" class="form-control" type="text" placeholder="<?php echo esc_html__('eg.100', 'listingpro'); ?>">
                                        </div>
                                    </div>
                                    <div class="clearfix margin-top-40">
                                        <div class="form-group col-sm-6 margin-0 padding-top-15">
                                            <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                                <span><?php echo esc_html__('required field', 'listingpro'); ?></span>
                                                <label class="switch">
                                                    <input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required">
                                                    <div class="slider round"></div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 text-right margin-top-20">
                                            <button class="lp-coupns-btns cancel-new-field"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                            <button class="lp-coupns-btns add-form-field form-field-front" data-pid=""><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="lp-coupon-box-row">
                            <div class="row">
                                <div class="form-group col-sm-6 margin-0">
                                    <label><?php echo esc_html__( 'Select Listing', 'listingpro' ); ?></label>
                                    <select multiple class="form-control select2-ajax" name="lead-form-listing" id="lead-form-listing" data-metakey="leadform">
                                        <option value="0"><?php echo esc_html__('Select Listing', 'listingpro'); ?></option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6 text-right margin-top-20">
                                    <button class="lp-coupns-btns cancel-lead-form"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                    <button class="lp-coupns-btns lp-save-template"><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 padding-right-0 lp-right-panel-height">
            <div class="lp-ad-click-outer">
                <div class="lp-general-section-title-outer">
                    <p class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Default Form Feilds', 'listingpro'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                        <ul class="lp-default-all-stats clearfix default-fields">
                            <li data-name="name" data-shortcode="[lp-customizer-field type='text' name='name7' placeholder='Name:' class='myclass']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php echo esc_html__('Name', 'listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="email" data-shortcode="[lp-customizer-field type='email' name='email7' placeholder='Email:' class='myclass']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php echo esc_html__('Email', 'listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="phone" data-shortcode="[lp-customizer-field type='text' name='phone7' placeholder='Phone:' class='myclass']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php echo esc_html__('Phone', 'listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                            <li data-name="message" data-shortcode="[lp-customizer-field type='textarea' name='message7' placeholder='Message:' class='myclass']">
                                <div class="lp-menu-close-outer lp-leadeform-close-outer">
                                    <div class="lp-menu-closed clearfix ">
                                        <span><i class="fa fa-bars" aria-hidden="true"></i></span>
                                        <span class="lp-right-side-title"><?php echo esc_html__('Message', 'listingpro'); ?></span>
                                        <span class="pull-right lp-remove-form-field"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="button" class="lp-form-feild-btn add-new-form-field front-field-btn"><i class="fa fa-plus" aria-hidden="true"></i><?php echo esc_html__('Add form feild', 'listingpro'); ?> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
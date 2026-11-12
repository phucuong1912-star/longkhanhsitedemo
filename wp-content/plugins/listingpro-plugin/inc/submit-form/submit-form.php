<?php

include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/submit-form/submit-form-functions.php');
if(get_option( 'listing_submit_form_state' ) == '1') {
    add_action( 'admin_enqueue_scripts', 'listingpro_register_form_submit_page' );

    if(!function_exists('listingpro_register_form_submit_page')) {
        function listingpro_register_form_submit_page() {
            wp_enqueue_style("form_builder_style", WP_PLUGIN_URL."/listingpro-plugin/assets/css/form-builder.css", false, "1.0", "all");
            wp_enqueue_script("form_builder_script", WP_PLUGIN_URL."/listingpro-plugin/assets/js/form-builder.js", false, "1.0", "all");
        }
    }

}

if(!function_exists('form_builder_field_options_markup')) {
    function form_builder_field_options_markup($fid) {
        ob_start();
        ?>
        <div class="lp-form-field-options-wrap">
            <div class="form-group">
                <label>Label</label>
                <input type="text" class="form-control field-label" value="">
            </div>
            <div class="form-group">
                <label>Placeholder</label>
                <input type="text" class="form-control field-placeholder" value="">
            </div>

                <div class="form-group">
                    <label>Quick Tip Title</label>
                    <input type="text" class="form-control tip-title" value="">
                </div>
                <div class="form-group">
                    <label>Quick Tip Description</label>
                    <textarea type="text" class="form-control tip-description"></textarea>
                </div>
                <div class="form-group">
                    <label>Quick Tip Image Url</label>
                    <input type="text" class="form-control tip-image" value="">
                </div>

            <?php
            if($fid == 'inputCategory' || $fid == 'inputCity') {
                $multi_label    =   'Multi Category';
                if($fid == 'inputCity') {
                    $multi_label    =   'Multi Location';
                }
                ?>
                <div class="form-group col-sm-12">
                    <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                        <span><?php echo $multi_label; ?></span>
                        <label class="switch">
                            <input value="multi" class="form-control switch-checkbox" type="checkbox" id="taxonomy-multi" name="taxonomy-multi">
                            <div class="slider round"></div>
                        </label>
                    </div>
                </div>
                <?php
            }
            if($fid == 'priceDetails') {
                $priceFromText  =   lp_theme_option('listing_digit_text');
                $priceToText    =   lp_theme_option('listing_price_text');
                ?>
                <div class="form-group col-sm-12">
                    <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                        <span><?php echo $priceFromText; ?></span>
                        <label class="switch">
                            <input value="multi" class="form-control switch-checkbox" type="checkbox" id="price-from" name="price-from">
                            <div class="slider round"></div>
                        </label>
                    </div>
                </div>
                <div class="form-group col-sm-12">
                    <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                        <span><?php echo $priceToText; ?></span>
                        <label class="switch">
                            <input value="multi" class="form-control switch-checkbox" type="checkbox" id="price-to" name="price-to">
                            <div class="slider round"></div>
                        </label>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="form-group"><button class="button button-primary">Save</button></div>
        </div>
        <?php
        $return =   ob_get_contents();
    }
}
if( !function_exists( 'listingpro_submit_form_page' ) )
{
    function listingpro_submit_form_page()
    {
        $submit_form_builder_state  =   get_option( 'listing_submit_form_state' );
        $active_switch_class    =   '';
        if( $submit_form_builder_state == '1' )
        {
            $active_switch_class    =   'active';
        }
        $listing_cat    =   get_terms('listing-category');
        $form_builder_cats_drop =   '<div class="form-group col-sm-12 non-exclusive-wrap">
                        <label for="field-categories">Select Categories</label>
                        <select id="field-categories" multiple>';
        if($listing_cat) {
            foreach ($listing_cat as $item) {
                $form_builder_cats_drop .= '<option value="'.$item->term_id.'">'.$item->name.'</option>';
            }
        }
        $form_builder_cats_drop .=  '</select>
                    </div>';

        $exclusive_field    =   '<div class="form-group col-sm-12">
                                                            <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                                                    <span>'.esc_html( __('Exclusive From Categories', 'listingpro-visualizer')).'</span>
                                                                    <label class="switch">
                                                                        <input class="form-control switch-checkbox" type="checkbox" id="field-exclusive" name="field-exclusive">
                                                                        <div class="slider round"></div>
                                                                    </label>
                                                                </div>
                                                        </div>';
        $exclusive_field    .=  $form_builder_cats_drop;
        $show_in_filter     =   '';
        $enable_extrafields_filter  =   lp_theme_option('enable_extrafields_filter');
        if($enable_extrafields_filter) {
            $show_in_filter .=  '<div class="form-group col-sm-5 margin-0 padding-top-15">
                                                                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                                                    <span>'.esc_html( __('Show in Filter', 'listingpro-visualizer')).'</span>
                                                                    <label class="switch">
                                                                        <input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required">
                                                                        <div class="slider round"></div>
                                                                    </label>
                                                                </div>
                                                            </div>';
        }

        $add_new_fieldmarkup    =   '<div class="lp-submit-form-add-field">
                                                    <form class="row" id="lp-submit-form-add-field">
                                                    <div class="new-field-title">New Field</div>
                                                        <div class="lp-listing-selecter clearfix">
                                                            <div class="form-group col-sm-6 ">
                                                                <label for="field-label">'.esc_html( __('Select a Field Type', 'listingpro-visualizer')).'</label>
                                                                <div class="lp-listing-selecter-drop">
                                                                    <select class="form-control select2 submit-field-type" name="field-type" id="field-type">
                                                                        <option value="text">Text</option>
                                                                        <option value="textarea">Textarea</option>
                                                                        <option value="radio">Radio</option>
                                                                        <option value="check">Checkbox</option>
                                                                        <option value="checkbox">Checkbox (switch on/off)</option>
                                                                        <option value="checkboxes">MultiCheck</option>
                                                                        <option value="select">Dropdown</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        '.$exclusive_field.'
                                                        <div class="form-group col-sm-12">
                                                            <label for="field-label">'.esc_html( __('Label', 'listingpro-visualizer')).'</label>
                                                            <input name="field-label" id="field-label" class="form-control" type="text" placeholder="Field Label">
                                                        </div>
                                                        <div class="form-group col-sm-12 options-field" style="display: none;">
                                                            <label for="field-options">'.esc_html( __('Options', 'listingpro-visualizer')).'</label>
                                                            <textarea name="field-options" id="field-options" class="form-control"></textarea>
                                                        </div>
                                                        <div class="form-group col-sm-12 field-placeholder">
                                                            <label for="field-placeholder">'.esc_html( __('Placeholder', 'listingpro-visualizer')).'</label>
                                                            <input name="field-placeholder" id="field-placeholder" class="form-control" type="text" placeholder="Placeholder">
                                                        </div>
                                                        <div class="clearfix margin-top-40">
                                                            <div class="form-group col-sm-5 margin-0 padding-top-15">
                                                                '.$show_in_filter.'
                                                            </div>
                                                            <div class="form-group col-sm-7 text-right margin-0">
                                                                <button class="cancel-new-field button">Cancel</button>
                                                                <button class="add-submit-form-field button button-primary">Save</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>';
        ?>
        <div id="custom-add-new-field-markup"><?php echo $add_new_fieldmarkup; ?></div>
        <div class="wrap">
            <div id="lp-submit-builder-ovelay">
                <div class="form-builder-msg">
                    <i class="fa fa-spinner fa-spin"></i>
                    <p>Form Saved Successfully</p>
                </div>
            </div>
            <h1>Advanced FES Form Builder</h1>
            <p class="fes-desc">Advance Front-End Submissin(FES) form builder allows you to create and customize the form as you see fit.</p>
            <div class="wrap listingpro-submit-form">
                <div class="form-builder-active-popup">
                    <span class="close-form-builder-pop close_form_builder"><i class="fa fa-close"></i> </span>
                    <div class="form-builder-active-popup-container">
                        <div class="form-builder-active-popup-container-header">POPup on ActIVAting Form BuiLDeR.</div>
                        <div class="form-builder-active-popup-container-content">By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. By activating this form builder you agree to our term's and conditions. </div>
                        <div class="form-builder-active-popup-container-footer"><button class="btn info close_form_builder">CLoSE</button></div>
                    </div>
                </div>

                <div class="form-builder-reset-popup">
                    <div class="form-builder-reset-popup-container">
                        <div class="form-builder-reset-popup-container-header">ARE YOU SURE YOU WANT TO Reset form builder.<span class="close-reset-pop close_reset_popu_p"><i class="fa fa-close"></i> </span></div>
                        <div class="form-builder-reset-popup-container-content">By activating this form builder you agree to our term's and conditions.</div>
                        <div class="form-builder-reset-popup-container-footer"><button class="float-l btn form-builder-confirm-reset">YES</button><button class="float-r btn close_reset_popu_p">NO</button></div>
                    </div>
                </div>
                <div class="form-builder-wrapper">
                    <div class="form-builder-fields-tabs">
                        <?php
                        $submit_form_builder_state  =   get_option( 'listing_submit_form_state' );
                        $hide_builder_tabs  =   'hide-form-builder-tabs';
                        if($submit_form_builder_state == '1') {
                            $hide_builder_tabs  =   '';
                        }
                        ?>
                        <ul class="form-builder-tabs-ul <?php echo $hide_builder_tabs; ?>">
                            <li class="active" id="fes-default-fields">Default Fields</li>
                            <li id="fes-custom-fields">Custom Fields</li>
                        </ul>
<!--                        <a href="#" class="switch-submit-form-builder --><?php //echo $active_switch_class; ?><!--"></a>-->
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-builder-columns">
                        <div class="lp-form-builder-notice" style="<?php if( $submit_form_builder_state == 1 ){ echo 'display:none;'; }; ?>">
                            <div class="lp-customizer-diable">
                                <img alt='image' src="<?php echo WP_PLUGIN_URL ?>/listingpro-plugin/images/sad_smiley.png">
                                <p>Enable form builder to view section</p>
                            </div>
                        </div>
                        <div class="lp-form-builder-inner-wrap" style="<?php if( $submit_form_builder_state == 1 ){ echo 'display:block;'; }; ?>">

                            <div class="form-builder-left">
                                <div class="form-fields-tabs-content">
                                    <div class="fes-default-fields form-builder-tabs-content">
                                        <span class="fes-left-col-title">default available form fields</span>
                                        <p class="fes-left-col-desc">Drag and drop any available fields from below sections to the right to make it active.</p>
                                        <div class="form-fields-list">
                                            <ul class="default-form-fields connected-sortable">
                                                <li class="form-section-wrapper" data-id="primarydetails" data-label="<?php echo esc_html__('Primary Listing Details', 'listingpro-plugin'); ?>">
                                                    <div class="form-section-title">Primary Listing Details <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Title', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Listing Title *', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/title.png"
                                                            data-tip-description="<?php echo esc_html__('Enter your complete business name for when people who know your business by name and are looking you up.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Staple & Fancy Hotel', 'listingpro-plugin'); ?>"
                                                            data-name="postTitle" data-shortcode="[lp-submit-form-field type='postTitle' name='postTitle' placeholder='Staple & Fancy Hotel' class='myclass' label='Listing Title *']">
                                                            <div class="form-field-title"><?php echo esc_html__('Listing Title ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('postTitle'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Tagline', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Tagline Example: Best Express Mexican Grill', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Tagline', 'listingpro-plugin'); ?>"
                                                            data-name="lptagline" data-shortcode="[lp-submit-form-field type='lptagline' name='lptagline' placeholder='Tagline Example: Best Express Mexican Grill' label='Tagline']">
                                                            <div class="form-field-title"><?php echo esc_html__('Tagline ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('lptagline'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Full Address', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Provide your full address for your business to show up on the map and your customer can get direction.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Start typing and find your place in google map', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Full Address', 'listingpro-plugin'); ?>"
                                                            data-name="gAddress" data-shortcode="[lp-submit-form-field type='gAddress' name='gAddress' placeholder='Start typing and find your place in google map' label='Full Address']">
                                                            <div class="form-field-title"><?php echo esc_html__('Full Address ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('gAddress'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('City', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Provide your city name for your business to show up on the map and your customer can get direction.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('select your listing region', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('City', 'listingpro-plugin'); ?>"
                                                            data-multi="no"
                                                            data-name="inputCity" data-shortcode="[lp-submit-form-field type='inputCity' name='inputCity' placeholder='select your listing region' label='City']">
                                                            <div class="form-field-title"><?php echo esc_html__('City ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputCity'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Website', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Its recommended to provide official website url and avoid landing pages designed for a specific campaign', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('http://', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Website', 'listingpro-plugin'); ?>"
                                                            data-name="inputWebsite" data-shortcode="[lp-submit-form-field type='inputWebsite' name='inputWebsite' placeholder='http://' label='Website']">
                                                            <div class="form-field-title"><?php echo esc_html__('Website ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputWebsite'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Phone', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('111-111-1234', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Phone', 'listingpro-plugin'); ?>"
                                                            data-name="inputPhone" data-shortcode="[lp-submit-form-field type='inputPhone' name='inputPhone' placeholder='111-111-1234' label='Phone']">
                                                            <div class="form-field-title"><?php echo esc_html__('Phone ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputPhone'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Whatsapp', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                            data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('111-111-1234', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Whatsapp', 'listingpro-plugin'); ?>"
                                                            data-name="inputWhatsapp" data-shortcode="[lp-submit-form-field type='inputWhatsapp' name='inputWhatsapp' placeholder='111-111-1234' label='Whatsapp']">
                                                            <div class="form-field-title"><?php echo esc_html__('Whatsapp ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputWhatsapp'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="categoryservices" data-label="Category & Services">
                                                    <div class="form-section-title">Category & Services <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Categories', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/cat-sub.png"
                                                            data-tip-description="<?php echo esc_html__('The more specific you get with your categories, the better. You do still want to stay relevant to your business, though. If you ever choose to run ads campaign, your ad will be shown on those categories you select.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Choose Your Business Category', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Category *', 'listingpro-plugin'); ?>"
                                                            data-multi="no"
                                                            data-name="inputCategory" data-shortcode="[lp-submit-form-field type='inputCategory' name='inputCategory' placeholder='Choose Your Business Category' label='Category *']">
                                                            <div class="form-field-title"><?php echo esc_html__('Category ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputCategory'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="pricedetails" data-label="Price Details">
                                                    <div class="form-section-title"><?php echo esc_html__('Price Details ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Price Range|Price From|Price To', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png|https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png|https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png"
                                                            data-placeholder="<?php echo esc_html__('not to say|price from|price to', 'listingpro-plugin'); ?>"
                                                            data-tip-description="<?php echo esc_html__('Setting a price range can help attract the right targeted audience and will avoid any awkward situations for both customers and the owner.|Being honest with your customers can build a strong relationship. Dont hesitate to include.|Being honest with your customers can build a strong relationship. Dont hesitate to include.', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Price Details', 'listingpro-plugin'); ?>"
                                                            data-pricefrom="yes"
                                                            data-priceto="yes"
                                                            data-name="priceDetails" data-shortcode="[lp-submit-form-field type='priceDetails' name='priceDetails' placeholder='not to say,price from,price to' label='Price Details']">
                                                            <div class="form-field-title"><?php echo esc_html__('Price Details ', 'listingpro-plugin'); ?><span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('priceDetails'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="businesshours" data-label="Business Hours">
                                                    <div class="form-section-title">Business Hours <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/biz.png"
                                                            data-placeholder="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                            data-tip-description="<?php echo esc_html__('You dont want your customers to stop by when you are closed so always try to keep your hour up to date. Keeping your store closed when your business indicate its open on the directory could lead to a negative review.', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                            data-name="businessHours" data-shortcode="[lp-submit-form-field type='businessHours' name='businessHours' label='Business Hours']">
                                                            <div class="form-field-title">Business Hours <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('businessHours'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="socialmedia" data-label="Social Media" >
                                                    <div class="form-section-title">Social Media <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/biz.png"
                                                            data-tip-description="<?php echo esc_html__('Being honest with your customers can build a strong relationship. Dont hesitate to include.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                            data-name="socialMedia" data-shortcode="[lp-submit-form-field type='socialMedia' name='socialMedia' label='Social Media']">
                                                            <div class="form-field-title">Social Media <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('socialMedia'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="faq" data-label="Frequently Asked Questions">
                                                    <div class="form-section-title">Frequently Asked Questions <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('FAQ', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/faq.png"
                                                            data-tip-description="<?php echo esc_html__('Share some of the most asked question and answers so they know you are serious about your business and truly care for your customers.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Frequently Asked Questions', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Frequently Asked Questions', 'listingpro-plugin'); ?>"
                                                            data-name="faqs" data-shortcode="[lp-submit-form-field type='faqs' name='faqs' label='Frequently Asked Questions']">
                                                            <div class="form-field-title">Frequently Asked Questions <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('faqs'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="moreinfo" data-label="More Info">
                                                    <div class="form-section-title">More Info <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Description', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/desc.png"
                                                            data-tip-description="<?php echo esc_html__('Tell briefly what your customers what to hear about your business has to offer that is unique and you do better then everyone else.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('Detail description about your listing', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Description', 'listingpro-plugin'); ?>"
                                                            data-name="inputDescription" data-shortcode="[lp-submit-form-field type='inputDescription' placeholder='Detail description about your listing' name='inputDescription' label='Description']">
                                                            <div class="form-field-title">Description <span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputDescription'); ?>
                                                        </li>
                                                        <li data-tip-title=""
                                                            data-tip-image=""
                                                            data-tip-description=""
                                                            data-placeholder=""
                                                            data-label="<?php echo esc_html__('Tags Or Keywords (Comma Separated)', 'listingpro-plugin'); ?>"
                                                            data-name="inputTags" data-shortcode="[lp-submit-form-field type='inputTags' placeholder='' name='inputTags' label='Tags Or Keywords (Comma Separated)']">
                                                            <div class="form-field-title">Tags of Keywords <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('inputTags'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                                <li class="form-section-wrapper" data-id="media" data-label="Media">
                                                    <div class="form-section-title">Media <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span></div>
                                                    <ul class="connected-sortable-inner">
                                                        <li data-tip-title="<?php echo esc_html__('Video', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/video.png"
                                                            data-tip-description="<?php echo esc_html__('Take it to next level and provide more details about what you have to offer. Select all that applies to you.', 'listingpro-plugin'); ?>"
                                                            data-placeholder="<?php echo esc_html__('ex: https://youtu.be/lY2yjAdbvdQ', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Your Business Video', 'listingpro-plugin'); ?>"
                                                            data-name="postVideo" data-shortcode="[lp-submit-form-field type='postVideo' name='postVideo' placeholder='ex: https://youtu.be/lY2yjAdbvdQ' label='Your Business Video']">
                                                            <div class="form-field-title">Business video <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('postVideo'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Gallery', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/gallery.png"
                                                            data-tip-description=""
                                                            data-label="<?php echo esc_html__('Images', 'listingpro-plugin'); ?>"
                                                            data-name="postGallery" data-shortcode="[lp-submit-form-field type='postGallery' name='postGallery' label='Images']">
                                                            <div class="form-field-title">Images <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('postGallery'); ?>
                                                        </li>
                                                        <li data-tip-title="<?php echo esc_html__('Featured Image', 'listingpro-plugin'); ?>"
                                                            data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/gallery.png"
                                                            data-tip-description="<?php echo esc_html__('Quick tip for featured images', 'listingpro-plugin'); ?>"
                                                            data-label="<?php echo esc_html__('Upload Featured Image', 'listingpro-plugin'); ?>"
                                                            data-name="featuredimage" data-shortcode="[lp-submit-form-field type='featuredimage' name='featuredimage' label='Upload Featured Image']">
                                                            <div class="form-field-title">Featured Image <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('featuredimage'); ?>
                                                        </li>
                                                        <li data-tip-title=""
                                                            data-tip-image=""
                                                            data-tip-description=""
                                                            data-label="<?php echo esc_html__('Upload Business Logo', 'listingpro-plugin'); ?>"
                                                            data-name="businessLogo" data-shortcode="[lp-submit-form-field type='businessLogo' name='businessLogo' label='Upload Business Logo']">
                                                            <div class="form-field-title">Business Logo <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                            <?php echo form_builder_field_options_markup('businessLogo'); ?>
                                                        </li>
                                                    </ul>
                                                    <?php echo $add_new_fieldmarkup; ?>
                                                    <div class="form-section-actions">
                                                        <span class="remove-section">Remove Section</span>
                                                        <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="fes-custom-fields form-builder-tabs-content" style="display: none;">
                                        <span class="fes-left-col-title">custom available form fields</span>
                                        <p class="fes-left-col-desc">Drag and drop any available fields from below sections to the right to make it active.</p>
                                        <div class="form-fields-list default-form-fields">
                                            <div class="custom-form-fields-spinner">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-builder-right">
                                <div class="submit-form-buttons">
                                    <span>Active Form Fields</span> <span class="reset-form-builder"><i class="fa fa-refresh"></i></span>
                                    <input type="hidden" class="lp-submit-form-result" value="">
                                    <input type="hidden" id="formajaxurl" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
                                    <button class="button button-primary save-submit-form">Save</button>
                                    <button class="button listing_submit_form_add-new-section">+ add new section</button>
                                    <div class="clearfix"></div>
                                </div>
                                <?php
                                $listing_submit_form_data   =   get_option( 'listing_submit_form_data' );
                                ?>
                                <div class="form-fields-sorter">
                                    <ul class="connected-sortable">
                                        <?php
                                        if( isset( $listing_submit_form_data ) && !empty( $listing_submit_form_data ) )
                                        {
                                            echo do_shortcode( $listing_submit_form_data );
                                        }
                                        else
                                        {
                                            ?>
                                            <li id="primarydetails-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="primarydetails" data-label="Primary Listing Details">
                                                <div class="form-section-title"><span class="title">Primary Listing Details</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li
                                                        data-label="<?php echo esc_html__('Listing Title *', 'listingpro-plugin'); ?>"
                                                        data-tip-title="<?php echo esc_html__('Title', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/title.png"
                                                        data-tip-description="<?php echo esc_html__('Enter your complete business name for when people who know your business by name and are looking you up.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Staple & Fancy Hotel', 'listingpro-plugin'); ?>"
                                                        data-name="postTitle"
                                                        data-shortcode="[lp-submit-form-field type='postTitle' name='postTitle' placeholder='Staple & Fancy Hotel' class='myclass' label='Listing Title *']">
                                                        <div class="form-field-title">Listing Title <span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('postTitle'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Tagline', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Tagline Example: Best Express Mexican Grill', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Tagline', 'listingpro-plugin'); ?>"
                                                        data-name="lptagline" data-shortcode="[lp-submit-form-field type='lptagline' name='lptagline' placeholder='Tagline Example: Best Express Mexican Grill' label='Tagline']">
                                                        <div class="form-field-title">Tagline <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('lptagline'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Full Address', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Provide your full address for your business to show up on the map and your customer can get direction.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Start typing and find your place in google map', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Full Address', 'listingpro-plugin'); ?>"
                                                        data-name="gAddress" data-shortcode="[lp-submit-form-field type='gAddress' name='gAddress' placeholder='Start typing and find your place in google map' label='Full Address']">
                                                        <div class="form-field-title">Full Address <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('gAddress'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('City', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Provide your city name for your business to show up on the map and your customer can get direction.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('select your listing region', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('City', 'listingpro-plugin'); ?>"
                                                        data-multi="no"
                                                        data-name="inputCity" data-shortcode="[lp-submit-form-field type='inputCity' name='inputCity' placeholder='select your listing region' label='City']">
                                                        <div class="form-field-title">City <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputCity'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Website', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Its recommended to provide official website url and avoid landing pages designed for a specific campaign', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('http://', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Website', 'listingpro-plugin'); ?>"
                                                        data-name="inputWebsite" data-shortcode="[lp-submit-form-field type='inputWebsite' name='inputWebsite' placeholder='http://' label='Website']">
                                                        <div class="form-field-title">Website <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputWebsite'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Phone', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('111-111-1234', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Phone', 'listingpro-plugin'); ?>"
                                                        data-name="inputPhone" data-shortcode="[lp-submit-form-field type='inputPhone' name='inputPhone' placeholder='111-111-1234' label='Phone']">
                                                        <div class="form-field-title">Phone <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputPhone'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Whatsapp', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/contact.png"
                                                        data-tip-description="<?php echo esc_html__('Local phone numbers drive 3x more calls than toll-free numbers. Always use a business phone number and avoid personal phone numbers if possible.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('111-111-1234', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Whatsapp', 'listingpro-plugin'); ?>"
                                                        data-name="inputWhatsapp" data-shortcode="[lp-submit-form-field type='inputWhatsapp' name='inputWhatsapp' placeholder='111-111-1234' label='Whatsapp']">
                                                        <div class="form-field-title">Whatsapp <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputWhatsapp'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="categoryservices-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="categoryservices" data-label="Category & Services">
                                                <div class="form-section-title"><span class="title">Category & Services</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Categories', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/cat-sub.png"
                                                        data-tip-description="<?php echo esc_html__('The more specific you get with your categories, the better. You do still want to stay relevant to your business, though. If you ever choose to run ads campaign, your ad will be shown on those categories you select.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Choose Your Business Category', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Category *', 'listingpro-plugin'); ?>"
                                                        data-multi="no"
                                                        data-name="inputCategory" data-shortcode="[lp-submit-form-field type='inputCategory' name='inputCategory' placeholder='Choose Your Business Category' label='Category *']">
                                                        <div class="form-field-title">Category <span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputCategory'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="pricedetails-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="pricedetails" data-label="Price Details">
                                                <div class="form-section-title"><span class="title">Price Details</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Price Range|Price From|Price To', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png|https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png|https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/price.png"
                                                        data-placeholder="<?php echo esc_html__('not to say|price from|price to', 'listingpro-plugin'); ?>"
                                                        data-tip-description="<?php echo esc_html__('Setting a price range can help attract the right targeted audience and will avoid any awkward situations for both customers and the owner.|Being honest with your customers can build a strong relationship. Dont hesitate to include.|Being honest with your customers can build a strong relationship. Dont hesitate to include.', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Price Details', 'listingpro-plugin'); ?>"
                                                        data-pricefrom="yes"
                                                        data-priceto="yes"
                                                        data-name="priceDetails" data-shortcode="[lp-submit-form-field type='priceDetails' name='priceDetails' placeholder='not to say,price from,price to' label='Price Details']">
                                                        <div class="form-field-title">Price Details <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('priceDetails'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="businesshours-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="businesshours" data-label="Business Hours">
                                                <div class="form-section-title"><span class="title">Business Hours</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/biz.png"
                                                        data-placeholder="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                        data-tip-description="<?php echo esc_html__('You dont want your customers to stop by when you are closed so always try to keep your hour up to date. Keeping your store closed when your business indicate its open on the directory could lead to a negative review.', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Business Hours', 'listingpro-plugin'); ?>"
                                                        data-name="businessHours" data-shortcode="[lp-submit-form-field type='businessHours' name='businessHours' label='Business Hours']">
                                                        <div class="form-field-title">Business Hours <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('businessHours'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="socialmedia-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="socialmedia" data-label="Social Media" >
                                                <div class="form-section-title"><span class="title">Social Media</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/biz.png"
                                                        data-tip-description="<?php echo esc_html__('Being honest with your customers can build a strong relationship. Dont hesitate to include.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Social Media', 'listingpro-plugin'); ?>"
                                                        data-name="socialMedia" data-shortcode="[lp-submit-form-field type='socialMedia' name='socialMedia' label='Social Media']">
                                                        <div class="form-field-title">Social Media <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('socialMedia'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="faq-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="faq" data-label="Frequently Asked Questions">
                                                <div class="form-section-title"><span class="title">Frequently Asked Questions</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('FAQ', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/faq.png"
                                                        data-tip-description="<?php echo esc_html__('Share some of the most asked question and answers so they know you are serious about your business and truly care for your customers.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Frequently Asked Questions', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Frequently Asked Questions', 'listingpro-plugin'); ?>"
                                                        data-name="faqs" data-shortcode="[lp-submit-form-field type='faqs' name='faqs' label='Frequently Asked Questions']">
                                                        <div class="form-field-title">Frequently Asked Questions <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('faqs'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="moreinfo-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="moreinfo" data-label="More Info">
                                                <div class="form-section-title"><span class="title">More Info</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Description', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/desc.png"
                                                        data-tip-description="<?php echo esc_html__('Tell briefly what your customers what to hear about your business has to offer that is unique and you do better then everyone else.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('Detail description about your listing', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Description', 'listingpro-plugin'); ?>"
                                                        data-name="inputDescription" data-shortcode="[lp-submit-form-field type='inputDescription' placeholder='Detail description about your listing' name='inputDescription' label='Description']">
                                                        <div class="form-field-title">Description <span class="lp-el-edit"><i class="fa fa-pencil"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputDescription'); ?>
                                                    </li>
                                                    <li data-tip-title=""
                                                        data-tip-image=""
                                                        data-tip-description=""
                                                        data-placeholder=""
                                                        data-label="<?php echo esc_html__('Tags Or Keywords (Comma Separated)', 'listingpro-plugin'); ?>"
                                                        data-name="inputTags" data-shortcode="[lp-submit-form-field type='inputTags' placeholder='' name='inputTags' label='Tags Or Keywords (Comma Separated)']">
                                                        <div class="form-field-title">Tags of Keywords <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('inputTags'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <li id="media-<?php echo rand(123456789,987654321); ?>" class="form-section-wrapper" data-id="media" data-label="Media">
                                                <div class="form-section-title"><span class="title">Media</span> <span class="lp-el-edit"><i class="fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>
                                                <ul class="connected-sortable-inner">
                                                    <li data-tip-title="<?php echo esc_html__('Video', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/video.png"
                                                        data-tip-description="<?php echo esc_html__('Take it to next level and provide more details about what you have to offer. Select all that applies to you.', 'listingpro-plugin'); ?>"
                                                        data-placeholder="<?php echo esc_html__('ex: https://youtu.be/lY2yjAdbvdQ', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Your Business Video', 'listingpro-plugin'); ?>"
                                                        data-name="postVideo" data-shortcode="[lp-submit-form-field type='postVideo' name='postVideo' placeholder='ex: https://youtu.be/lY2yjAdbvdQ' label='Your Business Video']">
                                                        <div class="form-field-title">Business video <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('postVideo'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Gallery', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/gallery.png"
                                                        data-tip-description=""
                                                        data-label="<?php echo esc_html__('Images', 'listingpro-plugin'); ?>"
                                                        data-name="postGallery" data-shortcode="[lp-submit-form-field type='postGallery' name='postGallery' label='Images']">
                                                        <div class="form-field-title">Images <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('postGallery'); ?>
                                                    </li>
                                                    <li data-tip-title="<?php echo esc_html__('Featured Image', 'listingpro-plugin'); ?>"
                                                        data-tip-image="https://classic.listingprowp.com/wp-content/themes/listingpro/assets/images/quick-tip/gallery.png"
                                                        data-tip-description="<?php echo esc_html__('Quick tip for featured images', 'listingpro-plugin'); ?>"
                                                        data-label="<?php echo esc_html__('Upload Featured Image', 'listingpro-plugin'); ?>"
                                                        data-name="featuredimage" data-shortcode="[lp-submit-form-field type='featuredimage' name='featuredimage' label='Upload Featured Image']">
                                                        <div class="form-field-title">Featured Image <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('featuredimage'); ?>
                                                    </li>
                                                    <li data-tip-title=""
                                                        data-tip-image=""
                                                        data-tip-description=""
                                                        data-label="<?php echo esc_html__('Upload Business Logo', 'listingpro-plugin'); ?>"
                                                        data-name="businessLogo" data-shortcode="[lp-submit-form-field type='businessLogo' name='businessLogo' label='Upload Business Logo']">
                                                        <div class="form-field-title">Business Logo <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                                                        <?php echo form_builder_field_options_markup('businessLogo'); ?>
                                                    </li>
                                                </ul>
                                                <?php echo $add_new_fieldmarkup; ?>
                                                <div class="form-section-actions">
                                                    <span class="remove-section">Remove Section</span>
                                                    <span class="listing_submit_form_add-new-field">+ add new field</span>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="submit-form-buttons">
                                    <button class="button button-primary save-submit-form">Save</button>
                                    <button class="button listing_submit_form_add-new-section">+ add new section</button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="lp-submit-form-add-section">
                                    <div class="new-section-title">New Section</div>
                                    <div class="row">
                                        <div class="new-section-inner">
                                            <div class="form-group col-sm-12">
                                                <label for="field-label"><?php echo esc_html( __('Label', 'listingpro-visualizer')); ?></label>
                                                <input name="section-label" id="section-label" class="form-control" type="text" placeholder="section label">
                                            </div>
                                            <div class="form-group col-sm-12 text-right margin-0">
                                                <button class="cancel-new-section button">Cancel</button>
                                                <button class="add-submit-form-section button button-primary">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="lp-layout-builder-ovelay">
            <div class="layout-builder-content">
                <div class="lp-submit-form-add-section">
                    <div class="new-section-title"><?php esc_html_e('Edit Section', 'listingpro-plugin'); ?></div>
                    <div class="row">
                        <div class="new-section-inner">
                            <div class="form-group col-sm-12">
                                <input type="hidden" name="section_id" value="">
                                <label for="section-label"><?php esc_html_e('Section Label', 'listingpro-plugin'); ?></label>
                                <input name="section-label" id="section-label" class="form-control" type="text">
                            </div>
                            <div class="form-group col-sm-12 text-right margin-0">
                                <button class="cancel-new-section button"><?php esc_html_e('Cancel', 'listingpro-plugin'); ?></button>
                                <button class="add-new-section button button-primary"><?php esc_html_e('Save', 'listingpro-plugin'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
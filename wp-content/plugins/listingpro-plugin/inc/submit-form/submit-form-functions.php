<?php

add_action('wp_ajax_lp_reset_form_builder', 'lp_reset_form_builder');

if (!function_exists('lp_reset_form_builder')) {
    function lp_reset_form_builder()
    {
        if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
            $return = array(
                'status'  => 'error',
                'message' => 'Invalid nonce'
            );
            die(json_encode($return));
        }
        if (!current_user_can('edit_posts')) {
            $return = array(
                'status'  => 'error',
                'message' => 'You do not have permission to edit posts.'
            );
            die(json_encode($return));
        }
        delete_option('listing_submit_form_data');
        die(json_encode(array('status' => 'success')));
    }
}

add_action('wp_ajax_lp_get_extra_form_fields_in_tab', 'lp_get_extra_form_fields_in_tab');

if (!function_exists('lp_get_extra_form_fields_in_tab')) {
    function lp_get_extra_form_fields_in_tab()
    {
        if (isset($_REQUEST)) {
?>
            <div class="form-section-wrapper">
                <?php
                $custom_form_fields_args = array(
                    'post_type' => 'form-fields',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                $custom_form_fields = new WP_Query($custom_form_fields_args);
                if ($custom_form_fields->have_posts()) {
                    echo '<ul class="connected-sortable-inner">';
                    while ($custom_form_fields->have_posts()) : $custom_form_fields->the_post();
                        global $post;
                        $field_metas = get_post_meta($post->ID, 'lp_listingpro_options', true);
                        $field_type = $field_metas['field-type'];
                ?>
                        <li data-tip-title="<?php the_title(); ?>" data-tip-image="" data-tip-description="Quick tip for featured images" data-name="<?php echo $post->post_name; ?>" data-shortcode="[lp-submit-form-field type='<?php echo $field_type; ?>' name='<?php echo $post->post_name; ?>' label='<?php the_title(); ?>']">
                            <div class="form-field-title"><?php the_title(); ?> (<?php echo $field_type; ?>)<span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>
                            <?php get_custom_fields_options_markup($post->ID); ?>
                        </li>
                <?php
                    endwhile;
                    wp_reset_postdata();
                    echo '</ul>';
                } else {
                    echo '<p class="fes-left-col-desc">There are no custom form fields added yet.</p>';
                }
                ?>
            </div>

        <?php

            die();
        }
    }
}

if (!function_exists('get_custom_fields_options_markup')) {
    function get_custom_fields_options_markup($fieldID)
    {

        $field_metas = get_post_meta($fieldID, 'lp_listingpro_options', true);


        $field_type = $field_metas['field-type'];
        $my_exclusive_field = '';
        $hide_cats = '';
        $show_in_filter_check = 'checked';
        $selected_cats = array();
        $options_show = 'style="display: none;"';
        $placeholder_show = 'style="display: none;"';
        $my_options = '';

        if ($field_type == 'text' || $field_type == 'textarea' || $field_type == 'email' || $field_type == 'tel' || $field_type == 'url') {
            $placeholder_show = 'style="display: block;"';
        }

        if (isset($field_metas['field-cat'])) {
            $selected_cats = $field_metas['field-cat'];
        }

        if (isset($field_metas['field-type']) && ($field_metas['field-type'] == 'select' || $field_metas['field-type'] == 'radio' || $field_metas['field-type'] == 'checkboxes')) {
            $options_show = 'style="display:block;"';
            if ($field_metas['field-type'] == 'select') {
                $my_options = $field_metas['select-options'];
            }
            if ($field_metas['field-type'] == 'checkboxes') {
                $my_options = $field_metas['multicheck-options'];
            }
            if ($field_metas['field-type'] == 'radio') {
                $my_options = $field_metas['radio-options'];
            }
        }
        if (isset($field_metas['exclusive_field']) && $field_metas['exclusive_field'] == 'Yes') {
            $my_exclusive_field = 'checked';
            $hide_cats = 'style="display:none;"';
        }
        if (isset($field_metas['lp-showin-filter']) && $field_metas['lp-showin-filter'] == 'notshowinfilter') {
            $show_in_filter_check = '';
        }

        $listing_cat = get_terms('listing-category', array('hide_empty' => false));
        $form_builder_cats_drop = '<div class="form-group col-sm-12 non-exclusive-wrap" ' . $hide_cats . '>
                        <label for="field-categories">Select Categories</label>
                        <select id="field-categories" multiple>';
        if ($listing_cat) {
            foreach ($listing_cat as $item) {
                $selected_cat = '';
                if (is_array($selected_cats) && in_array($item->term_id, $selected_cats)) {
                    $selected_cat = 'selected';
                }
                $form_builder_cats_drop .= '<option value="' . $item->term_id . '" ' . $selected_cat . '>' . $item->name . '</option>';
            }
        }
        $form_builder_cats_drop .= '</select>
                    </div>';
        $exclusive_field = '<div class="form-group col-sm-12">
                                    <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                            <span>' . esc_html(__('Exclusive From Categories', 'listingpro-visualizer')) . '</span>
                                            <label class="switch">
                                                <input class="form-control switch-checkbox" type="checkbox" id="field-exclusive" name="field-exclusive" ' . $my_exclusive_field . '>
                                                <div class="slider round"></div>
                                            </label>
                                        </div>
                                </div>';
        $exclusive_field .= $form_builder_cats_drop;
        $show_in_filter = '';
        $enable_extrafields_filter = lp_theme_option('enable_extrafields_filter');
        if ($enable_extrafields_filter) {
            $show_in_filter .= '<div class="form-group col-sm-5 margin-0 padding-top-15">
                                                                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off lp-showin-filter">
                                                                    <span>' . esc_html(__('Show in Filter', 'listingpro-visualizer')) . '</span>
                                                                    <label class="switch">
                                                                        <input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required" ' . $show_in_filter_check . '>
                                                                        <div class="slider round"></div>
                                                                    </label>
                                                                </div>
                                                            </div>';
        }

        ?>
        <div class="lp-form-field-options-wrap">
            <div class="form-group col-sm-6 ">
                <label for="field-label"><?php echo esc_html(__('Select a Field Type', 'listingpro-visualizer')); ?></label>
                <div class="lp-listing-selecter-drop">
                    <select class="form-control select2 submit-field-type" name="field-type" id="field-type">
                        <option value="text" <?php if ($field_type == 'text') {
                                                    echo 'selected';
                                                } ?>>Text
                        </option>
                        <option value="textarea" <?php if ($field_type == 'textarea') {
                                                        echo 'selected';
                                                    } ?>>Textarea
                        </option>
                        <option value="radio" <?php if ($field_type == 'radio') {
                                                    echo 'selected';
                                                } ?>>Radio
                        </option>
                        <option value="check" <?php if ($field_type == 'check') {
                                                    echo 'selected';
                                                } ?>>Checkbox
                        </option>
                        <option value="checkbox" <?php if ($field_type == 'checkbox') {
                                                        echo 'selected';
                                                    } ?>>Checkbox (switch on/off)
                        </option>
                        <option value="checkboxes" <?php if ($field_type == 'checkboxes') {
                                                        echo 'selected';
                                                    } ?>>MultiCheck
                        </option>
                        <option value="select" <?php if ($field_type == 'select') {
                                                    echo 'selected';
                                                } ?>>Dropdown
                        </option>
                    </select>
                </div>
            </div>
            <?php echo $exclusive_field;
            $required = '<div class="clearfix margin-top-40">
				<div class="form-group col-sm-5 margin-0 padding-top-15">
					<div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
						<span>' . esc_html(__('Required', 'listingpro-visualizer')) . '</span>
						<label class="switch">
							<input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required">
							<div class="slider round"></div>
						</label>
					</div>
																
				</div>
			</div>';
            echo $required;
            ?>
            <div class="form-group col-sm-12">
                <label for="field-label"><?php echo esc_html(__('Label', 'listingpro-visualizer')); ?></label>
                <input disabled name="field-label" value="<?php echo get_the_title($fieldID); ?>" id="field-label" class="form-control" type="text" placeholder="Field Label">
            </div>
            <div class="form-group col-sm-12 field-placeholder" <?php echo $placeholder_show; ?>>
                <label for="field-placeholder">Placeholder</label>
                <input name="field-placeholder" id="field-placeholder" class="form-control" type="text" value="<?php echo isset($field_metas['field-placeholder']) ? $field_metas['field-placeholder'] : ''; ?>" placeholder="Placeholder">
            </div>
            <div class="form-group col-sm-12 options-field" <?php echo $options_show; ?>>
                <label for="field-options"><?php echo esc_html(__('Options', 'listingpro-visualizer')); ?></label>
                <textarea name="field-options" id="field-options" class="form-control"><?php echo $my_options; ?></textarea>
            </div>
            <div class="clearfix margin-top-40 lp-showin-filter">
                <div class="form-group col-sm-5 margin-0 padding-top-15">
                    <?php echo $show_in_filter; ?>
                </div>
            </div>
            <div class="form-group">
                <button data-cfid="<?php echo $fieldID; ?>" class="button button-primary update-custom-form-field">
                    Save
                </button>
            </div>
        </div>
        <?php
    }
}


add_action('wp_ajax_lp_save_submit_form', 'lp_save_submit_form');

if (!function_exists('lp_save_submit_form')) {
    function lp_save_submit_form()
    {
        if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
            $return = array(
                'status'  => 'error',
                'message' => 'Invalid nonce'
            );
            die(json_encode($return));
        }
        if (!current_user_can('edit_posts')) {
            $return = array(
                'status'  => 'error',
                'message' => 'You do not have permission to edit posts.'
            );
            die(json_encode($return));
        }
        $submitFormS = wp_unslash($_POST['submitFormS']);
        update_option('listing_submit_form_data', $submitFormS);
    }
}


add_action('wp_ajax_lp_save_extra_form_field', 'lp_save_extra_form_field');

if (!function_exists('lp_save_extra_form_field')) {
    function lp_save_extra_form_field()
    {
        if (isset($_REQUEST)) {
            $return = array();
            if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'Invalid nonce'
                );
                die(json_encode($return));
            }
            if (!current_user_can('edit_posts')) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'You do not have permission to edit posts.'
                );
                die(json_encode($return));
            }
            $field_name = $_REQUEST['field_name'];
            $field_label = $_REQUEST['field_label'];
            $placeholderVal = $_REQUEST['placeholderVal'];
            $field_type = $_REQUEST['field_type'];
            $field_options = $_REQUEST['field_options'];
            $exclusive_val = $_REQUEST['exclusive_val'];
            $exclusive_cats = $_REQUEST['exclusive_cats'];
            $showInFilter = $_REQUEST['showInFilter'];

            $field_data = array(
                'post_title' => $field_label,
                'post_type' => 'form-fields',
                'post_status' => 'publish'
            );
            $field_id = wp_insert_post($field_data);
            if ($field_id) {
                $field_metas = array(
                    'field-type' => $field_type,
                    'field-name' => $field_name,
                    'field-placeholder' => $placeholderVal
                );
                if ($field_type == 'radio') {
                    $field_metas['radio-options'] = $field_options;
                }
                if ($field_type == 'select') {
                    $field_metas['select-options'] = $field_options;
                }
                if ($field_type == 'checkboxes') {
                    $field_metas['multicheck-options'] = $field_options;
                }
                if ($exclusive_val == 'no') {
                    $field_metas['field-cat'] = $exclusive_cats;
                }
                if ($exclusive_val == 'yes') {
                    $field_metas['exclusive_field'] = 'Yes';
                }
                $enable_extrafields_filter = lp_theme_option('enable_extrafields_filter');
                if ($enable_extrafields_filter) {
                    if ($showInFilter == 'yes') {
                        $field_metas['lp-showin-filter'] = 'displaytofilt';
                    }
                    if ($showInFilter == 'no') {
                        $field_metas['lp-showin-filter'] = 'notshowinfilter';
                    }
                }

                foreach ($exclusive_cats as $cat) {
                    $fieldIDs = listingpro_get_term_meta($cat, 'fileds_ids');

                    if (empty($fieldIDs)) {
                        $fieldIDs = array();
                    }

                    if (!in_array($field_id, $fieldIDs)) {
                        array_push($fieldIDs, $field_id);
                        update_term_meta($cat, 'fileds_ids', $fieldIDs);
                    }
                }
                update_post_meta($field_id, 'lp_listingpro_options', $field_metas);
                update_post_meta($field_id, 'lp_field_filter_type', $field_type);
                $get_field_post = get_post($field_id);
                $field_slug = $get_field_post->post_name;
                $return['field_slug'] = $field_slug;
                $return['field_id'] = $field_id;
                $return['status'] = 'success';
            }
            die(json_encode($return));
        }
    }
}


add_action('wp_ajax_lp_update_extra_form_builder_fields', 'lp_update_extra_form_builder_fields');

if (!function_exists('lp_update_extra_form_builder_fields')) {
    function lp_update_extra_form_builder_fields()
    {
        if (isset($_REQUEST)) {
            $return = array();
            if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'Invalid nonce'
                );
                die(json_encode($return));
            }
            if (!current_user_can('edit_posts')) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'You do not have permission to edit posts.'
                );
                die(json_encode($return));
            }
            $exclusive = $_REQUEST['exclusive'];
            $options = $_REQUEST['options'];
            $field_id = $_REQUEST['field_id'];
            $selectedCats = $_REQUEST['selectedCats'];
            $fieldType = $_REQUEST['fieldType'];
            $showInFilter = $_REQUEST['showInFilter'];
            $fieldPlaceholder = isset($_REQUEST['fieldPlaceholder']) ? $_REQUEST['fieldPlaceholder'] : '';
            if ($field_id) {
                //$field_metas = get_post_meta($field_id, 'lp_listingpro_options', true);
                delete_post_meta($field_id, 'lp_listingpro_options');
                $field_metas = array();
                $field_metas['field-type'] = $fieldType;
                $field_metas['field-placeholder'] = $fieldPlaceholder;
                if ($fieldType == 'radio') {
                    $field_metas['radio-options'] = $options;
                }
                if ($fieldType == 'select') {
                    $field_metas['select-options'] = $options;
                }
                if ($fieldType == 'checkboxes') {
                    $field_metas['multicheck-options'] = $options;
                }
                if ($exclusive == 'yes') {
                    $field_metas['exclusive_field'] = 'Yes';
                }
                if ($exclusive == 'no') {
                    $field_metas['field-cat'] = $selectedCats;
                    $field_metas['exclusive_field'] = '';
                }
                $enable_extrafields_filter = lp_theme_option('enable_extrafields_filter');
                if ($enable_extrafields_filter) {
                    if ($showInFilter == 'yes') {
                        $field_metas['lp-showin-filter'] = 'displaytofilt';
                    }
                    if ($showInFilter == 'no') {
                        $field_metas['lp-showin-filter'] = 'notshowinfilter';
                    }
                }
                foreach ($selectedCats as $cat) {
                    $fieldIDs = listingpro_get_term_meta($cat, 'fileds_ids');
                    if (empty($fieldIDs)) {
                        $fieldIDs = array();
                    }
                    if (!in_array($field_id, $fieldIDs)) {
                        array_push($fieldIDs, $field_id);
                        update_term_meta($cat, 'fileds_ids', $fieldIDs);
                    }
                }
                update_post_meta($field_id, 'lp_listingpro_options', $field_metas);
                update_post_meta($field_id, 'lp_field_filter_type', $fieldType);
                $return['field_id'] = $field_id;
            }
            die(json_encode($return));
        }
    }
}


add_action('wp_ajax_lp_remove_extra_form_field', 'lp_remove_extra_form_field');

if (!function_exists('lp_remove_extra_form_field')) {
    function lp_remove_extra_form_field()
    {
        if (isset($_REQUEST)) {
            $return = array();
            if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
                $response = array(
                    'status'  => 'error',
                    'message' => 'Invalid nonce'
                );
                die(json_encode($response));
            }
            if (!current_user_can('edit_posts')) {
                $response = array(
                    'status'  => 'error',
                    'message' => 'You do not have permission to edit posts.'
                );
                die(json_encode($response));
            }
            $target = $_REQUEST['target'];
            $field_post = get_page_by_path($target, OBJECT, 'form-fields');
            $field_id = $field_post->ID;
            if (!current_user_can('delete_post', $field_id)) {
                $response = array(
                    'status'  => 'error',
                    'message' => 'You do not have permission to delete this post.'
                );
                die(json_encode($response));
            }
            wp_delete_post($field_id, true);
            die(json_encode($return));
        }
    }
}


add_shortcode('lp-submit-form', 'lp_submit_form_cb');
add_shortcode('lp-submit-form-field', 'lp_submit_form_field_cb');
add_shortcode('lp-submit-form-sec', 'lp_submit_form_sec_cd');

if (is_admin()) {
    if (!function_exists('field_options_markup')) {
        function field_options_markup($fid, $multi_val)
        {
            $field_options_markup = '<div class="lp-form-field-options-wrap">
                                                <div class="form-group">
                                                    <label>Label</label>
                                                    <input type="text" class="form-control field-label" value="">
                                                </div>
                                                <div class="form-group">
                                                    <label>Placeholder</label>
                                                    <input type="text" class="form-control field-placeholder" value="">
                                                </div>';

            $field_options_markup .= '<div class="form-group">
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
                                                </div>';


            if ($fid == 'inputCategory' || $fid == 'inputCity') {
                $multi_label = 'Multi Category';
                if ($fid == 'inputCity') {
                    $multi_label = 'Multi Location';
                }
                $multi_checked = '';
                if ($multi_val == 'yes') {
                    $multi_checked = 'checked';
                }

                $field_options_markup .= '<div class="form-group col-sm-12">
                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                    <span>' . $multi_label . '</span>
                    <label class="switch">
                        <input ' . $multi_checked . ' value="multi" class="form-control switch-checkbox" type="checkbox" id="taxonomy-multi" name="taxonomy-multi">
                        <div class="slider round"></div>
                    </label>
                </div>
            </div>';
            }
            if ($fid == 'priceDetails') {
                $priceFromText = lp_theme_option('listing_digit_text');
                $priceToText = lp_theme_option('listing_price_text');

                $field_options_markup .= '<div class="form-group col-sm-12">
                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                    <span>' . $priceFromText . '</span>
                    <label class="switch">
                        <input value="multi" class="form-control switch-checkbox" type="checkbox" id="price-from" name="price-from">
                        <div class="slider round"></div>
                    </label>
                </div>
            </div>
            <div class="form-group col-sm-12">
                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                    <span>' . $priceToText . '</span>
                    <label class="switch">
                        <input value="multi" class="form-control switch-checkbox" type="checkbox" id="price-to" name="price-to">
                        <div class="slider round"></div>
                    </label>
                </div>
            </div>';
            }
            if ($fid != 'inputCategory' && $fid != 'postTitle' && $fid != 'inputDescription') {
                $field_options_markup .= '<div class="clearfix margin-top-40">
            <div class="form-group col-sm-5 margin-0 padding-top-15">
                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                    <span>' . esc_html(__('Required', 'listingpro-visualizer')) . '</span>
                    <label class="switch">
                        <input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required">
                        <div class="slider round"></div>
                    </label>
                </div>
                                                            
            </div>
        </div>';
            }
            $field_options_markup .= '<div class="form-group"><button class="button button-primary">Save</button></div>
                                            </div>';

            return $field_options_markup;
        }
    }
    if (!function_exists('lp_submit_form_cb')) {
        function lp_submit_form_cb($atts, $content = null)
        {
            ob_start();
            echo do_shortcode($content);
            return ob_get_clean();
        }
    }
    if (!function_exists('lp_submit_form_sec_cd')) {
        function lp_submit_form_sec_cd($atts, $content = null)
        {
            extract(shortcode_atts(array(
                'id' => 'text',
                'label' => '',
            ), $atts));

            ob_start();

            $listing_cat = get_terms('listing-category', array('hide_empty' => false));

            echo '<li id="' . esc_attr( $id ) . '-' . rand(123456789, 987654321) . '" class="form-section-wrapper" data-id="' . esc_attr( $id ) . '" data-label="' . esc_attr( $label ) . '">
                   <div class="form-section-title"><span class="title">' . esc_html( $label ) . '</span> <span class="lp-el-edit"><i class="fa fa fa-chevron-up"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div>';
            echo '  <ul class="connected-sortable-inner">';
            echo do_shortcode($content);
            echo '  </ul>';
        ?>
            <div class="lp-submit-form-add-field">
                <form class="lp-submit-form-add-field-wrap">
                    <div class="new-field-title">New Field</div>
                    <div class="lp-listing-selecter clearfix">
                        <div class="form-group col-sm-12">
                            <label>Select a Field Type</label>
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
                    <div class="form-group col-sm-12">
                        <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                            <span><?php echo esc_html(__('Exclusive From Categories', 'listingpro-visualizer')); ?></span>
                            <label class="switch">
                                <input class="form-control switch-checkbox" type="checkbox" id="field-exclusive" name="field-exclusive">
                                <div class="slider round"></div>
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 non-exclusive-wrap">
                        <label for="field-categories">Select Categories</label>
                        <select id="field-categories" multiple>
                            <?php
                            if ($listing_cat) {
                                foreach ($listing_cat as $item) {
                                    echo '<option value="' . $item->term_id . '">' . $item->name . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="field-label">Label</label>
                        <input name="field-label" id="field-label" class="form-control" type="text" placeholder="Field Label">
                    </div>
                    <div class="form-group col-sm-12 options-field" style="display: none;">
                        <label for="field-options">Options</label>
                        <textarea name="field-options" id="field-options" class="form-control"></textarea>
                    </div>
                    <div class="form-group col-sm-12 field-placeholder">
                        <label for="field-placeholder">Placeholder</label>
                        <input name="field-placeholder" id="field-placeholder" class="form-control" type="text" placeholder="Placeholder">
                    </div>
                    <div class="clearfix margin-top-40">
                        <?php
                        $enable_extrafields_filter = lp_theme_option('enable_extrafields_filter');
                        if ($enable_extrafields_filter) {
                        ?>
                            <div class="form-group col-sm-5 margin-0 padding-top-15">
                                <div class="lp-invoices-all-stats-on-off lp-form-all-stats-on-off">
                                    <span>Show in Filter</span>
                                    <label class="switch">
                                        <input class="form-control switch-checkbox" type="checkbox" id="field-required" name="field-required">
                                        <div class="slider round"></div>
                                    </label>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="form-group col-sm-7 text-right margin-0">
                            <button class="cancel-new-field button">Cancel</button>
                            <button class="add-submit-form-field button button-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php
            echo '<div class="clearfix"></div>
                                        <div class="form-section-actions">
                                            <span class="remove-section">Remove Section</span>
                                            <span class="listing_submit_form_add-new-field">+ add new field</span>
                                        </div>';
            echo '</li>';

            return ob_get_clean();
        }
    }
    if (!function_exists('lp_submit_form_field_cb')) {
        function lp_submit_form_field_cb($atts, $content = null)
        {
            $data_pricefrom = '';
            $data_priceto = '';
            extract(shortcode_atts(array(
                'type' => 'text',
                'name' => '',
                'placeholder' => '',
                'class' => '',
                'label' => '',
                'options' => '',
                'required' => '',
                'tiptitle' => '',
                'tipimage' => '',
                'tipdesc' => '',
                'multi' => '',
                'pricefrom' => '',
                'priceto' => ''
            ), $atts));

            $attrs = "type='$type' ";
            $attrs .= "class='$class' ";
            $attrs .= "placeholder='$placeholder' ";
            $attrs .= "name='$name' ";
            $attrs .= "label='$label' ";
            $attrs .= "required='$required' ";

            $default_fields_array = array(
                "postTitle",
                "gAddress",
                "inputCity",
                "inputPhone",
                "inputWebsite",
                "inputCategory",
                "priceDetails",
                "businessHours",
                "socialMedia",
                "inputDescription",
                "inputTags",
                "postVideo",
                "postGallery",
                "featuredimage",
                "businessLogo",
                "faqs",
                "lptagline",
                "inputWhatsapp"

            );
            ob_start();
            if (in_array($name, $default_fields_array)) {
                $data_multi = '';
                $multi_val = '';
                if ($name == 'inputCity' || $name == 'inputCategory') {
                    $data_multi = 'data-multi="' . $multi . '"';
                    $multi_val = $multi;
                }
                if ($name == 'priceDetails') {
                    $data_pricefrom = 'data-pricefrom="' . $pricefrom . '"';
                    $data_priceto = 'data-priceto="' . $priceto . '"';
                }
                if ($name == 'postTitle' || $name == 'inputCategory' || $name == 'inputDescription') {
                    echo '<li ' . $data_multi . ' data-label="' . $label . '" data-required="' . $required . '" data-placeholder="' . $placeholder . '" data-tip-title="' . $tiptitle . '" data-tip-image="' . $tipimage . '" data-tip-description="' . $tipdesc . '" data-name="' . $name . '" data-shortcode="[lp-submit-form-field ' . $attrs . ']"><div class="form-field-title">' . $label . ' <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> </div> ' . field_options_markup($name, $multi_val) . '</li>';
                } else {
                    echo '<li data-required="' . $required . '" ' . $data_priceto . ' ' . $data_pricefrom . ' ' . $data_multi . ' data-label="' . $label . '" data-placeholder="' . $placeholder . '" data-tip-title="' . $tiptitle . '" data-tip-image="' . $tipimage . '" data-tip-description="' . $tipdesc . '" data-name="' . $name . '" data-shortcode="[lp-submit-form-field ' . $attrs . ']"><div class="form-field-title">' . $label . ' <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div> ' . field_options_markup($name, $multi_val) . '</li>';
                }
            } else {


                if ($custom_form_field = get_page_by_path($name, OBJECT, 'form-fields')) {
                    if ($custom_form_field->ID) {
                        $field_id = $custom_form_field->ID;
                        $label = get_the_title($field_id);
                        echo '<li data-required="' . $required . '" data-placeholder="' . $placeholder . '" data-tip-title="' . $tiptitle . '" data-tip-image="' . $tipimage . '" data-tip-description="' . $tipdesc . '" data-name="' . $name . '" data-shortcode="[lp-submit-form-field ' . $attrs . ']"><div class="form-field-title">' . $label . ' (' . $atts['type'] . ')<span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div> ';
                        get_custom_fields_options_markup($field_id);
                        echo '</li>';
                    }
                }
            }
            return ob_get_clean();
        }
    }
}
if (!is_admin()) {

    if (!function_exists('lp_submit_form_cb')) {
        function lp_submit_form_cb($atts, $content = null)
        {
            ob_start();
        ?>
            <?php
            $data_pricefrom = '';
            $data_priceto = '';
            echo '<input type="hidden" id="lp-form-builder-is-enabled" value="lp-form-builder-enabled">';
            echo do_shortcode($content);
            ?>
            <?php
            return ob_get_clean();
        }
    }
    if (!function_exists('lp_submit_form_sec_cd')) {
        function lp_submit_form_sec_cd($atts, $content = null)
        {
            extract(shortcode_atts(array(
                'id' => 'text',
                'label' => '',
            ), $atts));

            global $listingpro_options;
            $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

            $price_show = get_post_meta($plan_id, 'listingproc_price', true);
            $hours_show = get_post_meta($plan_id, 'listingproc_bhours', true);
            $social_show = get_post_meta($plan_id, 'listingproc_social', true);
            $faqs_show = get_post_meta($plan_id, 'listingproc_faq', true);

            $ophSwitch = $listingpro_options['oph_switch'];
            $social_show_switch = lp_theme_option('listin_social_switch');
            $faq_switch = $listingpro_options['faq_switch'];


            if ($plan_id == "none") {
                $price_show = 'true';
                $hours_show = 'true';
                $social_show = 'true';
                $faqs_show = 'true';
            }

            ob_start();

            if ($id == 'pricedetails') {
                if ($price_show == "true") {
            ?>
                    <div class="white-section border-bottom lp-style-wrap-border section-id-<?php echo esc_attr($id); ?>">
                        <h4 class="white-section-heading"><?php echo esc_html($label); ?></h4>
                    <?php
                }
            } elseif ($id == 'businesshours') {
                if ($hours_show == "true") {
                    ?>
                        <div class="white-section border-bottom lp-style-wrap-border section-id-<?php echo esc_attr($id); ?>">
                            <h4 class="white-section-heading"><?php echo esc_html($label); ?></h4>
                        <?php
                    }
                } elseif ($id == 'socialmedia') {
                    if ($social_show == "true") {
                        ?>
                            <div class="white-section border-bottom lp-style-wrap-border section-id-<?php echo esc_attr($id); ?>">
                                <h4 class="white-section-heading"><?php echo esc_html($label); ?></h4>
                            <?php
                        }
                    } elseif ($id == 'faq') {
                        if ($faqs_show == "true") {
                            ?>
                                <div class="white-section border-bottom lp-style-wrap-border section-id-<?php echo esc_attr($id); ?>">
                                    <h4 class="white-section-heading"><?php echo esc_html($label); ?></h4>
                                <?php
                            }
                        } else {
                                ?>
                                <div class="white-section border-bottom lp-style-wrap-border section-id-<?php echo esc_attr($id); ?>">
                                    <h4 class="white-section-heading"><?php echo esc_html($label); ?></h4>
                                <?php
                            }
                                ?>
                                <?php
                                echo do_shortcode($content);
                                ?>

                                <?php
                                if ($id == 'pricedetails') {
                                    if ($price_show == "true") {
                                ?>
                                </div>
                            <?php
                                    }
                                } elseif ($id == 'businesshours') {
                                    if ($hours_show == "true") {
                            ?>
                                </div>
                            <?php
                                    }
                                } elseif ($id == 'socialmedia') {
                                    if ($social_show == "true") {
                            ?>
                            </div>
                        <?php
                                    }
                                } elseif ($id == 'faq') {
                                    if ($faqs_show == "true") {
                        ?>
                        </div>
                    <?php
                                    }
                                } else {
                    ?>
                    </div>
                <?php
                                }
                ?>

                <?php
                if ($id == 'categoryservices') {
                    if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {

                        $lp_post = $_GET['lp_post'];
                        $metaFields = get_post_meta($lp_post, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
                        $n = 1;
                        $current_cat_array = get_the_terms($lp_post, 'listing-category');
                        if (!empty($current_cat_array)) {
                            foreach ($current_cat_array as $current_catt) {
                                $current_cat[$n] = $current_catt->term_id;
                                $term_id[$n] = $current_catt->term_id;
                                $n++;
                            }
                        }

                        echo '<div class="white-section border-bottom lp-style-wrap-border">';
                        echo '   <div class="row col-md-12">';
                        echo '<div class="form-group clearfix lpfeatures_fields">';
                        $features;
                        $featuresArr;
                        $nofeatures = true;
                        $fcount = 1;
                        if (!empty($current_cat_array)) {
                            $totalTms = count($current_cat);
                        }
                        $uniqueTermIds = array();
                        if (is_array($current_cat)) {
                            foreach ($current_cat as $cid) {
                                $features = listingpro_get_term_meta($cid, 'lp_category_tags');
                                if (!empty($features)) {
                                    $listing_features_text = lp_theme_option('listing_features_text');

                                    echo '
                                                    <label for="inputTags" class="featuresBycat">' . $listing_features_text . '</label><br>
                                                    <div class="pre-load"></div>
                                                    <div class="featuresDataContainer row clearfix lp-nested" id="tags-by-cat">';

                                    $nofeatures = false;
                                    $cheched = '';
                                    foreach ($features as $feature) {
                                        $terms = get_term_by('id', $feature, 'features');
                                        if (!empty($terms)) {
                                            if (array_key_exists("$terms->term_id", $uniqueTermIds)) {
                                            } else {
                                                $uniqueTermIds[$terms->term_id] = $terms->term_id;
                                                if (!empty($metaFields['lp_feature'])) {
                                                    if (in_array($feature, $metaFields['lp_feature'])) {
                                                        $cheched = "checked";
                                                    } else {
                                                        $cheched = '';
                                                    }
                                                }

                                                echo '<div class="col-md-2 col-sm-4 col-xs-6"><div class="checkbox pad-bottom-10"><input ' . $cheched . '  id="check_' . $terms->term_id . '" type="checkbox" name="lp_form_fields_inn[lp_feature][]" value="' . $terms->term_id . '" ><label for="check_' . $terms->term_id . '">' . $terms->name . '</label></div></div>
                                                            ';
                                            }
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                        }
                        echo '</div>';
                        if ($nofeatures == true) {
                            echo '
                                    <div class="form-group clearfix">
                                        <div class="pre-load"></div>
                                        <div class="featuresDataContainerr lp-nested row" id="tags-by-cat"></div>   
                                        <div class="featuresDataContainer lp-nested row" id="features-by-cat"></div>
                                    </div>';
                        }

                        if (!empty($formFields)) {
                            echo '
                                    <div class="featuresDataContainer row clearfix lp-nested" id="features-by-cat">';
                            echo '<label for="inputTags" class="featuresBycat">' . esc_html__('Additional Business Info', 'listingpro-plugin') . '</label>';
                            echo $formFields;
                            echo '
                                    </div>';
                        }
                        echo '   </div>';
                        echo '</div>';
                    } else {
                ?>

                <?php
                    }
                }
                ?>

                <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_submit_form_field_cb')) {
            if (!function_exists('lp_submit_form_field_cb')) {
                function lp_submit_form_field_cb($atts, $content = null)
                {
                    extract(shortcode_atts(array(
                        'type' => 'text',
                        'name' => '',
                        'placeholder' => '',
                        'class' => '',
                        'label' => '',
                        'options' => '',
                        'required' => '',
                        'multi' => '',
                        'pricefrom' => '',
                        'priceto' => ''
                    ), $atts));


                    if ($required == 'required') {
                        $required_class = 'lp-required-field';
                    } else {
                        $required_class = '';
                    }

                    $atts['required-class'] = $required_class;
                    $listingid = '';

                    $field_id = '';

                    $field_post = get_page_by_path($name, OBJECT, 'form-fields');
                    if ($field_post) {
                        $field_id = $field_post->ID;
                    }


                    $lp_post = '';
                    $current_term = '';
                    $current_fields_style = '';
                    $exclusive = 'no';

                    $field_metas = get_post_meta($field_id, 'lp_listingpro_options', true);
                    $term_dependancy_classes = '';
                    if (isset($field_metas['field-cat']) && is_array($field_metas['field-cat'])) {
                        foreach ($field_metas['field-cat'] as $k => $dtid) {
                            $term_dependancy_classes .= 'lp-form-builder-field-' . $dtid . ' ';
                        }
                    }


                    if (isset($field_metas['exclusive_field']) && $field_metas['exclusive_field'] == 'Yes') {
                        $exclusive = 'yes';
                    }

                    if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                        $lp_post = $_GET['lp_post'];
                        $current_cat_array = get_the_terms($lp_post, 'listing-category');
                        $current_term = $current_cat_array[0]->term_id;
                        if (is_array($current_term)) {
                            if (isset($field_metas['field-cat']) && in_array($current_term, $field_metas['field-cat'])) {
                                $current_fields_style = 'style="display:block"';
                            }
                        }
                    }


                    ob_start();
                    if ($type == 'postTitle') postTitle($atts);
                    if ($type == 'lptagline') lptagline($atts);
                    if ($type == 'gAddress') gAddress($atts);
                    if ($type == 'inputCity') inputCity($atts);
                    if ($type == 'inputPhone') inputPhone($atts);
                    if ($type == 'inputWhatsapp') inputWhatsapp($atts);
                    if ($type == 'inputWebsite') inputWebsite($atts);
                    if ($type == 'inputCategory') inputCategory($atts);
                    if ($type == 'priceDetails') priceDetails($atts);
                    if ($type == 'businessHours') businessHours($atts);
                    if ($type == 'socialMedia') socialMedia($atts);
                    if ($type == 'faqs') faqs_cb($atts);
                    if ($type == 'inputDescription') inputDescription($atts);
                    if ($type == 'inputTags') inputTags($atts);
                    if ($type == 'postVideo') postVideo($atts);
                    if ($type == 'postGallery') postGallery($atts);
                    if ($type == 'featuredimage') featuredimage($atts);
                    if ($type == 'businessLogo') businessLogo($atts);


                    if ($type == 'text') { ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php echo listingpro_field_type_output_text($field_id, $listingid, $atts); ?>
                        </div>
                    <?php
                    }
                    if ($type == 'textarea') {
                        //echo lp_form_textarea_field($name, $placeholder, $class, $required, $label, $exclusive);
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php echo listingpro_field_type_output_textarea($field_id, $listingid, $atts); ?>
                        </div>
                    <?php
                    }
                    if ($type == 'url') {
                        echo lp_form_url_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'tel') {
                        echo lp_form_tel_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'range') {
                        echo lp_form_range_field($name, $placeholder, $class, $required, $min, $max, $step, $def, $label, $exclusive);
                    }
                    if ($type == 'date') {
                        echo lp_form_date_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'time') {
                        echo lp_form_time_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'datetime-local') {
                        echo lp_form_datetime_local_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'file') {
                        echo lp_form_file_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'email') {
                        echo lp_form_email_field($name, $placeholder, $class, $required, $label, $exclusive);
                    }
                    if ($type == 'select') {
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php
                            echo listingpro_field_type_output_select($field_id, $listingid);
                            ?>
                        </div>
                    <?php

                    }
                    if ($type == 'checkboxes') {
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php
                            echo listingpro_field_type_output_checkboxes($field_id, $listingid);
                            ?>
                        </div>
                    <?php
                    }
                    if ($type == 'check') {
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php
                            echo listingpro_field_type_output_check($field_id, $listingid);
                            ?>
                        </div>
                    <?php
                    }
                    if ($type == 'checkbox') {
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php
                            echo listingpro_field_type_output_checkbox($field_id, $listingid);
                            ?>
                        </div>
                    <?php

                    }
                    if ($type == 'radio') {
                    ?>
                        <div <?php echo $current_fields_style; ?> id="lp-form-builder-field-<?php echo $field_id; ?>" class="<?php echo $term_dependancy_classes; ?> lp-form-builder-field lp-form-builder-field-<?php echo $exclusive; ?>">
                            <?php
                            echo listingpro_field_type_output_radio($field_id, $listingid);
                            ?>
                        </div>
                <?php
                    }
                    return ob_get_clean();
                }
            }
        }

        if (!function_exists('postTitle')) {
            function postTitle($atts)
            {
                $enableGoogle = lp_theme_option('lp_listing_listing_by_google');
                $lp_post = '';
                $listing_title = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $listing_title = get_the_title($_GET['lp_post']);
                }
                ?>
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                        <div id="lp_custom_title" class="tab-pane fade in active">
                            <label for="usr"><?php echo $atts['label']; ?></label>
                            <div class="help-text">
                                <a href="#" class="help"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php echo esc_html__('Put your listing title here and tell the name of your business to the world.', 'listingpro-plugin'); ?></p>
                                </div>
                            </div>
                            <input value="<?php echo $listing_title; ?>" data-img_src="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" name="postTitle" class="form-control margin-bottom-10 lp-required-field" id="lptitle" placeholder="<?php echo $atts['placeholder']; ?>">
                            <?php
                            if ($enableGoogle == "yes") {
                                echo '<input data-img_src="' . $atts['tiptitle'] . '" data-quick-tip="<h2>' . esc_html__('Title', 'listingpro-plugin') . '</h2><p>' . esc_html__('Enter your complete business name for when people who know your business by name and are looking you up.', 'listingpro-plugin') . '</p>" type="hidden" id="lptitleGoogle" name="" class="form-control margin-bottom-10 lptitle" placeholder="' . esc_html__('Staple & Fancy Hotel', 'listingpro-plugin') . '" autocomplete="off"><div id="lp_listing_map"></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        if (!function_exists('lptagline')) {
            function lptagline($atts)
            {
                $lp_post = '';
                $listing_tagline = '';

                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $tagline_show = get_post_meta($plan_id, 'listingproc_tagline', true);
                if ($plan_id == "none") {
                    $tagline_show = 'true';
                }


                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {

                    $listing_tagline = listing_get_metabox_by_ID('tagline_text', $_GET['lp_post']);
                }
                if ($tagline_show == 'true') {
                ?>
                    <div class="row">
                        <?php
                        $tagline_cond_class = '';
                        if ($atts['required'] != 'required') {
                            $tagline_cond_class = 'with-title-cond';
                            echo '<div class="lp-tagline-submit-tagline">
                        <label>' . esc_html__('Does Your Business have a tagline?', 'listingpro-plugin') . '
                            <input type="checkbox">
                            <span class="lp-sbt-checkmark"></span>
                        </label>
                    </div>';
                        }

                        echo '<div class="form-group col-md-12 col-xs-12 ' . $tagline_cond_class . '">';
                        echo '            <label for="usr">' . $atts['label'] . '</label>';
                        echo '            <input data-img_src="' . $atts['tipimage'] . '" data-quick-tip="<h2>' . $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" type="text" value="' . $listing_tagline . '" name="tagline_text" class="form-control margin-bottom-10 ' . $atts['required-class'] . '" id="lptagline" placeholder="' . $atts['placeholder'] . '">';
                        echo '        </div>';
                        ?>
                    </div>
                <?php
                }
                ?>

            <?php
            }
        }
        if (!function_exists('gAddress')) {
            function gAddress($atts)
            {
                $lp_post = '';
                $gAddress = '';
                $latitude = '';
                $longitude = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $gAddress = listing_get_metabox_by_ID('gAddress', $lp_post);
                    $latitude = listing_get_metabox_by_ID('latitude', $lp_post);
                    $longitude = listing_get_metabox_by_ID('longitude', $lp_post);
                }
            ?>
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                        <div class="lp-coordinates">
                            <a data-type="gaddress" class="btn-link googleAddressbtn active"><?php echo esc_html__('Search By Google', 'listingpro-plugin'); ?></a>
                            <a data-type="gaddresscustom" class="btn-link googleAddressbtn"><?php echo esc_html__('Manual Coordinates', 'listingpro-plugin'); ?></a>
                            <a data-type="gaddresscustom" class="btn-link googledroppin" data-toggle="modal" data-target="#modal-doppin"><i class="fa fa-map-pin"></i><?php echo esc_html__(' Drop Pin', 'listingpro-plugin'); ?>
                            </a>
                        </div>
                        <label for="inputAddress" class="googlefulladdress"><?php echo $atts['label']; ?></label>

                        <div class="help-text googlefulladdress">
                            <a href="#" class="help"><i class="fa fa-question"></i></a>
                            <div class="help-tooltip">
                                <p><?php echo esc_html__('Start typing and select your google location from google suggestions. This is for the map and also for locating your business.', 'listingpro-plugin'); ?></p>
                            </div>
                        </div>

                        <input data-img_addresssrc="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" value="<?php echo $gAddress; ?>" type="text" class="form-control form-control-st <?php echo $atts['required-class']; ?>" name="gAddress" id="inputAddress" placeholder="<?php echo $atts['placeholder']; ?>" autocomplete="off">
                        <div class="lp-custom-lat clearfix">
                            <label for="inputAddress"><?php echo esc_html__('Add Custom Address', 'listingpro-plugin'); ?></label>
                            <input value="<?php echo $gAddress; ?>" type="text" class="form-control form-control-st <?php echo $atts['required-class']; ?>" name="gAddresscustom" id="inputAddresss" placeholder="<?php echo esc_html__('Add address here', 'listingpro-plugin'); ?>">
                            <div class="row hiddenlatlong">
                                <div class="col-md-6 col-xs-6">
                                    <label for="latitude"><?php echo esc_html__('Latitude', 'listingpro-plugin'); ?></label>
                                    <input value="<?php echo $latitude; ?>" class="form-control" type="hidden" placeholder="40.7143528" id="latitude" name="latitude">
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <label for="longitude"><?php echo esc_html__('Longitude', 'listingpro-plugin'); ?></label>
                                    <input value="<?php echo $longitude; ?>" class="form-control" type="hidden" placeholder="-74.0059731" id="longitude" name="longitude">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        if (!function_exists('inputCity')) {
            function inputCity($atts)
            {
                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $lp_post = '';
                $current_loc = '';
                $locArea = '';

                $locations_type = $listingpro_options['lp_listing_locations_options'];

                if (!empty($locations_type) && $locations_type == "auto_loc") {
                    $locArea = $listingpro_options['lp_listing_locations_range'];
                }

                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $current_loc_array = get_the_terms($lp_post, 'location');
                    if (!empty($current_loc_array)) {
                        foreach ($current_loc_array as $current_locc) {
                            $current_loc = $current_locc->name;
                        }
                    }
                }

                $showLocation = $listingpro_options['location_switch'];
                $location_show = get_post_meta($plan_id, 'listingproc_location', true);

                if ($plan_id == "none") {
                    $location_show = 'true';
                }
                if ($location_show == "true") {
                    $locations_type = $listingpro_options['lp_listing_locations_options'];
                    $locArea = '';
                    if (!empty($locations_type) && $locations_type == "auto_loc") {
                        $locArea = $listingpro_options['lp_listing_locations_range'];
                    }
                    $singleLocMode = true;
                    if ($atts['multi'] == "yes") {
                        $singleLocMode = false;
                    }
                ?>
                    <div class="row">
                        <?php
                        if (!empty($locations_type) && $locations_type == "auto_loc") {
                            if ($singleLocMode == true) {
                        ?>
                                <div class="form-group col-md-12 col-xs-12 lp-new-cat-wrape">
                                    <label for="inputTags"><?php echo $atts['label']; ?></label>
                                    <div class="help-text">
                                        <a href="#" class="help"><i class="fa fa-question"></i></a>
                                        <div class="help-tooltip">
                                            <p><?php echo esc_html__('The city name will help users find you in search filters.', 'listingpro-plugin'); ?></p>
                                        </div>
                                    </div>

                                    <input data-cityimg="<?php echo $atts['tipimage']; ?>'>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" value="<?php echo $current_loc; ?>" id="citiess" name="locationn" data-isseleted="false" class="form-control ostsubmitSelect <?php echo $atts['required-class'];  ?>" autocomplete="off" data-country="<?php echo $locArea; ?>" placeholder="<?php echo $atts['placeholder']; ?>">
                                    <input value="<?php echo $current_loc; ?>" type="hidden" name="location">
                                </div>
                                <?php
                            } else {
                                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                                ?>
                                    <div class="form-group lp-selected-locs clearfix col-md-12">
                                        <?php
                                        $current_loc = array();
                                        $current_loc_array = get_the_terms($lp_post, 'location');
                                        if (!empty($current_loc_array)) {
                                            foreach ($current_loc_array as $current_locc) {
                                                $current_loc[] = $current_locc->term_id;
                                            }
                                        }
                                        $args = array(
                                            'post_type' => 'listing',
                                            'order' => 'ASC',
                                            'parent' => 0,
                                            'hide_empty' => false,
                                        );
                                        $locations = get_terms('location', $args);
                                        foreach ($locations as $location) {

                                            if (!empty($current_loc)) {
                                                foreach ($current_loc as $cloc) {
                                                    if ($location->term_id == $cloc) {
                                        ?>
                                                        <div class="lpsinglelocselected <?php echo $location->name; ?>"><?php echo $location->name; ?>
                                                            <i class="fa fa-times lp-removethisloc"></i><input type="hidden" name="location[]" value="<?php echo $location->name; ?>">
                                                        </div>
                                        <?php
                                                    }
                                                }
                                            }

                                            $argsChild = array(
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'hierarchical' => false,
                                                'parent' => $location->term_id,
                                            );
                                            $childLocs = get_terms('location', $argsChild);
                                            if (!empty($childLocs)) {
                                                foreach ($childLocs as $childLoc) {

                                                    if (!empty($current_loc)) {
                                                        foreach ($current_loc as $cloc) {
                                                            if ($childLoc->term_id == $cloc) {
                                                                $output .= '<div class="lpsinglelocselected ' . $childLoc->name . '">' . $childLoc->name . '<i class="fa fa-times lp-removethisloc"></i><input type="hidden" name="location[]" value="' . $childLoc->name . '"></div>';
                                                            }
                                                        }
                                                    }


                                                    $argsChildof = array(
                                                        'order' => 'ASC',
                                                        'hide_empty' => false,
                                                        'hierarchical' => false,
                                                        'parent' => $childLoc->term_id,
                                                    );
                                                    $childLocsof = get_terms('location', $argsChildof);
                                                    if (!empty($childLocsof)) {
                                                        foreach ($childLocsof as $childLocof) {

                                                            if (!empty($current_loc)) {
                                                                foreach ($current_loc as $cloc) {
                                                                    if ($childLocof->term_id == $cloc) {
                                                                        $output .= '<div class="lpsinglelocselected ' . $childLocof->name . '">' . $childLocof->name . '<i class="fa fa-times lp-removethisloc"></i><input type="hidden" name="location[]" value="' . $childLocof->name . '"></div>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="form-group col-md-6 col-xs-12 lp-new-cat-wrape">
                                            <input id="citiess" name="locationn" class="form-control postsubmitSelect <?php echo $atts['required-class'];  ?>" autocomplete="off" data-country="<?php echo $locArea; ?>" data-country="<?php echo $locArea; ?>" placeholder="<?php echo $atts['placeholder']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p><img alt='image' src='<?php echo $atts['tipimage']; ?>'>" data-isseleted="false">
                                        </div>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="form-group lp-selected-locs clearfix col-md-12"></div>
                                    <div class="form-group col-md-12 col-xs-12 lp-new-cat-wrape">
                                        <label for="inputTags"><?php echo $atts['label']; ?></label>
                                        <div class="help-text">
                                            <a href="#" class="help"><i class="fa fa-question"></i></a>
                                            <div class="help-tooltip">
                                                <p><?php echo esc_html__('The city name will help users find you in search filters.', 'listingpro-plugin'); ?></p>
                                            </div>
                                        </div>
                                        <input id="citiess" name="locationn" data-isseleted="false" data-cityimg="<?php echo $atts['tipimage']; ?>'>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" class="form-control ostsubmitSelect <?php echo $atts['required-class'];  ?>" autocomplete="off" data-country="<?php echo $locArea; ?>" placeholder="<?php echo $atts['placeholder']; ?>">
                                    </div>
                            <?php
                                }
                            }
                        } elseif (!empty($locations_type) && $locations_type == "manual_loc") {

                            ?>
                            <div class="form-group col-md-12 col-xs-12 lp-new-cat-wrape lp-new-cat-wrape">
                                <label for="inputTags"><?php echo $atts['label']; ?></label>
                                <div class="help-text">
                                    <a href="#" class="help"><i class="fa fa-question"></i></a>
                                    <div class="help-tooltip">
                                        <p><?php echo esc_html__('The city name will help users find you in search filters.', 'listingpro-plugin'); ?></p>
                                    </div>
                                </div>
                                <?php
                                if ($singleLocMode == true) {
                                ?>
                                    <select data-cityimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" data-placeholder="<?php echo $atts['placeholder']; ?>" id="inputCity" name="location[]" class="select2 postsubmitSelect <?php echo $atts['required-class']; ?>" tabindex="5">
                                    <?php
                                } else {
                                    $quick_tip_title = 'Full Address';
                                    $quick_tip_desc = 'Provide your full address for your business to show up on the map and your customer can get direction.';
                                    ?>
                                        <select data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p><img alt='image'  src='<?php echo $atts['tipimage']; ?>'>" data-placeholder="<?php echo $atts['placeholder']; ?>" id="inputCity" name="location[]" class="select2 postsubmitSelect <?php echo $atts['required-class']; ?>" tabindex="5" multiple="multiple">
                                        <?php
                                    }
                                        ?>
                                        <option value=""><?php echo $atts['placeholder']; ?></option>
                                        <?php
                                        $current_loc = array();
                                        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                                            $current_loc_array = get_the_terms($lp_post, 'location');
                                            if (!empty($current_loc_array)) {
                                                foreach ($current_loc_array as $current_locc) {
                                                    $current_loc[] = $current_locc->term_id;
                                                }
                                            }
                                        }
                                        $args = array(
                                            'post_type' => 'listing',
                                            'order' => 'ASC',
                                            'hide_empty' => false,
                                            'parent' => 0,
                                        );
                                        $locations = get_terms('location', $args);
                                        if (!empty($locations)) {
                                            foreach ($locations as $location) {
                                                $selected = '';
                                                if (!empty($current_loc)) {
                                                    foreach ($current_loc as $cloc) {
                                                        if ($location->term_id == $cloc) {
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                }
                                                echo '<option ' . $selected . ' value="' . $location->term_id . '">' . $location->name . '</option>';
                                                $argsChild = array(
                                                    'order' => 'ASC',
                                                    'hide_empty' => false,
                                                    'hierarchical' => false,
                                                    'parent' => $location->term_id,
                                                );
                                                $childLocs = get_terms('location', $argsChild);
                                                if (!empty($childLocs)) {
                                                    foreach ($childLocs as $childLoc) {
                                                        $selected = '';
                                                        if (!empty($current_loc)) {
                                                            foreach ($current_loc as $cloc) {
                                                                if ($childLoc->term_id == $cloc) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                        }

                                                        echo '<option ' . $selected . ' value="' . $childLoc->term_id . '">-&nbsp;' . $childLoc->name . '</option>';

                                                        $argsChildof = array(
                                                            'order' => 'ASC',
                                                            'hide_empty' => false,
                                                            'hierarchical' => false,
                                                            'parent' => $childLoc->term_id,
                                                        );
                                                        $childLocsof = get_terms('location', $argsChildof);
                                                        if (!empty($childLocsof)) {
                                                            foreach ($childLocsof as $childLocof) {
                                                                $selected = '';
                                                                if (!empty($current_loc)) {
                                                                    foreach ($current_loc as $cloc) {
                                                                        if ($childLocof->term_id == $cloc) {
                                                                            $selected = 'selected';
                                                                        }
                                                                    }
                                                                }
                                                                echo '<option ' . $selected . ' value="' . $childLocof->term_id . '">--&nbsp;' . $childLocof->name . '</option>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        </select>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('inputPhone')) {
            function inputPhone($atts)
            {
                global $listingpro_options;

                $phone = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $phone = listing_get_metabox_by_ID('phone', $_GET['lp_post']);
                }
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $contact_show = get_post_meta($plan_id, 'contact_show', true);
                if ($plan_id == "none") {
                    $contact_show = 'true';
                }

                if ($contact_show == "true") {
                ?>
                    <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                            <label for="inputPhone"><?php echo $atts['label']; ?></label>
                            <input value="<?php echo $phone; ?>" data-phoneimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" class="form-control <?php echo $atts['required-class']; ?>" name="phone" id="inputPhone" placeholder="<?php echo $atts['placeholder']; ?>">
                        </div>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('inputWhatsapp')) {
            function inputWhatsapp($atts)
            {
                global $listingpro_options;

                $whatsapp = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $whatsapp = listing_get_metabox_by_ID('whatsapp', $_GET['lp_post']);
                }
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                ?>
                <div class="row">
                    <?php
                    echo '
                <div class="form-group col-md-12 col-xs-12 ' . $atts['required-class'] . '">
                    <label for="inputWhatsapp">' . $atts['label'] . '</label>
                    <input value="' . $whatsapp . '" data-whatsappimg="' . $atts['tipimage'] . '" data-quick-tip="<h2>' . $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" type="text" class="form-control ' . $atts['required-class'] . '" name="whatsapp" id="inputWhatsapp" placeholder="' . $atts['placeholder'] . '">
                </div>';
                    ?>
                </div>
                <?php
            }
        }
        if (!function_exists('inputWebsite')) {
            function inputWebsite($atts)
            {
                global $listingpro_options;

                $website = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $website = listing_get_metabox_by_ID('website', $_GET['lp_post']);
                }

                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

                $website_show = get_post_meta($plan_id, 'listingproc_website', true);
                if ($plan_id == "none") {
                    $website_show = 'true';
                }

                if ($website_show == "true") {
                ?>
                    <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                            <label for="inputWebsite"><?php echo $atts['label']; ?></label>
                            <input value="<?php echo $website; ?>" data-webimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" class="form-control <?php echo $atts['required-class']; ?>" name="website" id="inputWebsite" placeholder="<?php echo $atts['placeholder']; ?>">
                        </div>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('inputCategory')) {
            function inputCategory($atts)
            {
                $cat_plan_id = '';
                $lp_post = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $preselctedCat = get_post_meta($lp_post, 'preselected', true);
                }

                if (isset($_POST['plan_id'])) {
                    $plan_id = $_POST['plan_id'];
                    if (isset($_POST['lp_pre_selected_cats'])) {
                        $cat_plan_id = $_POST['lp_pre_selected_cats'];
                    }
                } else {
                    $plan_id = 'none';
                }
                global $listingpro_options;
                $singleCatMode = true;

                if ($atts['multi'] == "yes") {
                    $singleCatMode = false;
                }


                $quicktip_cat = lp_theme_option_url('submit_ad_img_categories');
                ?>
                <div class="row">
                    <div class="form-group clearfix margin-bottom-0 lp-new-cat-wrape col-md-12 lp-form-builder-enabled">
                        <label for="inputCategory"><?php echo $atts['label']; ?></label>

                        <?php
                        if (isset($lp_post) && !empty($lp_post)) {
                            $current_cat_array = get_the_terms($lp_post, 'listing-category');
                            $n = 1;
                            if (!empty($current_cat_array)) {
                                foreach ($current_cat_array as $current_catt) {
                                    $current_cat[$n] = $current_catt->term_id;
                                    $term_id[$n] = $current_catt->term_id;
                                    $n++;
                                }
                            }
                            if ($singleCatMode == true) {
                                echo '<select data-catimg="' . $atts['tipimage'] . '" data-quick-tip="<h2>' . $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" autocomplete="off" data-placeholder="' . $atts['placeholder'] . '" id="inputCategory" name="category[]" class="select2 postsubmitSelect lp-required-field" tabindex="5">';
                            } else {
                                echo '<select data-catimg="' . $atts['tipimage'] . '" data-quick-tip="<h2>' .  $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" autocomplete="off" data-placeholder="' . $atts['placeholder'] . '" id="inputCategory" name="category[]" class="select2 postsubmitSelect lp-required-field" tabindex="5" multiple="multiple">';
                            }
                            if (!empty($preselctedCat)) {
                                //preselected plan based cats
                                $current_cat_objArray = array();
                                $current_cat_obj = get_the_terms($lp_post, 'listing-category');
                                if (!empty($current_cat_array)) {
                                    foreach ($current_cat_array as $snterm) {
                                        $current_cat_objArray[0] = $snterm->term_id;
                                        $current_cat_objArray[1] = $snterm->name;
                                    }

                                    echo '<option value="' . $current_cat_objArray[0] . '">' . $current_cat_objArray[1] . '</option>';
                                }
                            } else {
                                echo '<option value="">' . esc_html__('Select Category', 'listingpro-plugin') . '</option>';
                                $args = array(
                                    'post_type' => 'listing',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => 0,
                                );
                                $categories = get_terms('listing-category', $args);
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        $doAjax = false;
                                        $doAjax = lp_category_has_features($category->term_id);
                                        $selected = '';
                                        foreach ($current_cat as $cid) {
                                            if ($category->term_id == $cid) {
                                                $selected = 'selected';
                                            }
                                        }

                                        echo '<option data-doajax="' . $doAjax . '" ' . $selected . ' value="' . $category->term_id . '">' . $category->name . '</option>';

                                        $argscatChild = array(
                                            'order' => 'ASC',
                                            'hide_empty' => false,
                                            'hierarchical' => false,
                                            'parent' => $category->term_id,

                                        );

                                        $childCats = get_terms('listing-category', $argscatChild);
                                        if (!empty($childCats)) {
                                            foreach ($childCats as $subID) {
                                                $doAjax = false;
                                                $doAjax = lp_category_has_features($subID->term_id);
                                                $selected = '';
                                                foreach ($current_cat as $cid) {
                                                    if ($subID->term_id == $cid) {
                                                        $selected = 'selected';
                                                    }
                                                }
                                                echo '<option ' . $selected . ' data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subID->term_id . '">-&nbsp;&nbsp;' . $subID->name . '</option>';

                                                $childCatsof = array(
                                                    'order' => 'ASC',
                                                    'hide_empty' => false,
                                                    'hierarchical' => false,
                                                    'parent' => $subID->term_id,
                                                );
                                                $childofCats = get_terms('listing-category', $childCatsof);
                                                if (!empty($childofCats)) {
                                                    foreach ($childofCats as $subIDD) {
                                                        $doAjax = false;
                                                        $doAjax = lp_category_has_features($subIDD->term_id);
                                                        $selected = '';
                                                        foreach ($current_cat as $cid) {
                                                            if ($subIDD->term_id == $cid) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        echo '<option ' . $selected . ' data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subIDD->term_id . '">--&nbsp;&nbsp;' . $subIDD->name . '</option>';

                                                        $childCatsoff = array(
                                                            'order' => 'ASC',
                                                            'hide_empty' => false,
                                                            'hierarchical' => false,
                                                            'parent' => $subIDD->term_id,
                                                        );
                                                        $childofCatss = get_terms('listing-category', $childCatsoff);

                                                        if (!empty($childofCatss)) {
                                                            foreach ($childofCatss as $subIDDD) {
                                                                $doAjax = false;
                                                                $doAjax = lp_category_has_features($subIDDD->term_id);
                                                                $selected = '';
                                                                foreach ($current_cat as $cid) {
                                                                    if ($subIDDD->term_id == $cid) {
                                                                        $selected = 'selected';
                                                                    }
                                                                }
                                                                echo '<option ' . $selected . ' data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subIDDD->term_id . '">---&nbsp;&nbsp;' . $subIDDD->name . '</option>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            echo '</select>';
                        } else {
                            if (!empty($cat_plan_id)) {
                        ?>
                                <input type="hidden" name="lppre_plan_cats" value="true" />
                                <select data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p><img alt='image'  src='<?php echo $atts['tipimage']; ?>'>" data-placeholder="<?php echo $atts['placeholder']; ?>" id="inputCategory" name="category[]" class="select2 postsubmitSelect lp-required-field" tabindex="5">
                                    <?php
                                    $selectedCatObj = get_term_by('id', $cat_plan_id, 'listing-category');
                                    $selectedCatName = $selectedCatObj->name;
                                    $doAjax = false;
                                    $doAjax = lp_category_has_features($selectedCatObj->term_id);

                                    ?>
                                    <option data-doajax="' . $doAjax . '" value="<?php echo $cat_plan_id; ?>"><?php echo $selectedCatName; ?></option>
                                    ';
                                </select>
                        <?php
                            } else {
                                if ($singleCatMode == true) {
                                    echo '<select data-catimg="' . $atts['tipimage'] . '" data-quick-tip="<h2>' . $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" data-placeholder="' . $atts['placeholder'] . '" id="inputCategory" name="category[]" class="select2 postsubmitSelect lp-required-field" tabindex="5">';
                                } else {
                                    echo '<select data-catimg="' . $atts['tipimage'] . '" data-quick-tip="<h2>' . $atts['tiptitle'] . '</h2><p>' . $atts['tipdesc'] . '</p>" data-placeholder="' . $atts['placeholder'] . '" id="inputCategory" name="category[]" class="select2 postsubmitSelect lp-required-field" tabindex="5" multiple="multiple">';
                                }
                                echo '<option value="">' . esc_html__('Select Category', 'listingpro-plugin') . '</option>';
                                $args = array(
                                    'post_type' => 'listing',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => 0,
                                );
                                $categories = get_terms('listing-category', $args);
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {

                                        $doAjax = false;
                                        $doAjax = lp_category_has_features($category->term_id);
                                        echo '<option data-doajax="' . $doAjax . '" value="' . $category->term_id . '">' . $category->name . '</option>';

                                        $argscatChild = array(
                                            'order' => 'ASC',
                                            'hide_empty' => false,
                                            'hierarchical' => false,
                                            'parent' => $category->term_id,

                                        );
                                        $childCats = get_terms('listing-category', $argscatChild);
                                        if (!empty($childCats)) {
                                            foreach ($childCats as $subID) {
                                                $doAjax = false;
                                                $doAjax = lp_category_has_features($subID->term_id);
                                                echo '<option data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subID->term_id . '">-&nbsp;&nbsp;' . $subID->name . '</option>';

                                                $childCatsof = array(
                                                    'order' => 'ASC',
                                                    'hide_empty' => false,
                                                    'hierarchical' => false,
                                                    'parent' => $subID->term_id,
                                                );
                                                $childofCats = get_terms('listing-category', $childCatsof);
                                                if (!empty($childofCats)) {
                                                    foreach ($childofCats as $subIDD) {
                                                        $doAjax = false;
                                                        $doAjax = lp_category_has_features($subIDD->term_id);
                                                        echo '<option data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subIDD->term_id . '">--&nbsp;&nbsp;' . $subIDD->name . '</option>';

                                                        $childCatsoff = array(
                                                            'order' => 'ASC',
                                                            'hide_empty' => false,
                                                            'hierarchical' => false,
                                                            'parent' => $subIDD->term_id,
                                                        );
                                                        $childofCatss = get_terms('listing-category', $childCatsoff);
                                                        if (!empty($childofCatss)) {
                                                            foreach ($childofCatss as $subIDDD) {
                                                                $doAjax = false;
                                                                $doAjax = lp_category_has_features($subIDDD->term_id);
                                                                echo '<option data-doajax="' . $doAjax . '"  class="sub_cat" value="' . $subIDDD->term_id . '">---&nbsp;&nbsp;' . $subIDDD->name . '</option>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                echo '</select>';
                            }
                        }
                        ?>
                    </div>
                    <div class="featuresDataContainerOuterSubmit white-section features-container-with-fes">
                        <div class="row">
                            <div class="form-group clearfix lpfeatures_fields col-md-12">
                                <div class="pre-load"></div>
                                <div class="featuresDataContainerr lp-nested row" id="tags-by-cat"></div>
                                <div class="featuresDataContainer lp-nested row" id="features-by-cat"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        if (!function_exists('priceDetails')) {
            function priceDetails($atts)
            {

                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $lp_post = '';
                $price_status = '';
                $listingprice = '';
                $listingptext = '';


                $label1 = esc_html__('Price Details');
                $label2 = esc_html__('Price From');
                $label3 = esc_html__('Price To');

                $place1 = esc_html__('Price Details');
                $place2 = esc_html__('Price From');
                $place3 = esc_html__('Price To');

                $labels_arr = array();
                $palce_arr = array();

                if (isset($atts['label'])) {
                    $labels_arr = explode('|', $atts['label']);
                }
                if (isset($atts['placeholder'])) {
                    $palce_arr = explode('|', $atts['placeholder']);
                }
                if (!empty($labels_arr)) {
                    if (isset($labels_arr[0])) {
                        $label1 = $labels_arr[0];
                    }
                    if (isset($labels_arr[1])) {
                        $label2 = $labels_arr[1];
                    }
                    if (isset($labels_arr[2])) {
                        $label3 = $labels_arr[2];
                    }
                }
                if (!empty($palce_arr)) {
                    if (isset($palce_arr[0])) {
                        $place1 = $palce_arr[0];
                    }
                    if (isset($palce_arr[1])) {
                        $place2 = $palce_arr[1];
                    }
                    if (isset($palce_arr[2])) {
                        $place3 = $palce_arr[2];
                    }
                }

                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];

                    $listingptext = listing_get_metabox_by_ID('list_price_to', $lp_post);
                    $listingprice = listing_get_metabox_by_ID('list_price', $lp_post);
                    $price_status = listing_get_metabox_by_ID('price_status', $lp_post);
                }


                $price_show = get_post_meta($plan_id, 'listingproc_price', true);
                $digitPriceSwitch = $atts['pricefrom'];
                $priceSwitch = $atts['priceto'];
                $currencySwitch = $listingpro_options['currency_switch'];


                if ($plan_id == "none") {
                    $price_show = 'true';
                }

                $lp_priceSymbol = $listingpro_options['listing_pricerange_symbol'];
                $lp_priceSymbol2 = $lp_priceSymbol . $lp_priceSymbol;
                $lp_priceSymbol3 = $lp_priceSymbol2 . $lp_priceSymbol;
                $lp_priceSymbol4 = $lp_priceSymbol3 . $lp_priceSymbol;


                if ($price_show == "true") {
                ?>
                    <div class="row">
                        <div class="col-md-4 clearfix">
                            <label for="price_status"><?php echo $label1; ?></label>

                            <?php
                            if (!isset($lp_post) || empty($lp_post)) {
                                $firtstImage = explode('|', $atts['tipimage'])[0];
                            ?>
                                <select data-priceimg="<?php echo $firtstImage; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" id="price_status" name="price_status" class="chosen-select chosen-select7  postsubmitSelect <?php echo $atts['required-class']; ?>" tabindex="5">
                                    <option value="notsay"><?php echo $place1 ?></option>
                                    <option value="inexpensive"><?php echo $lp_priceSymbol . ' - ' . esc_html__('Inexpensive', 'listingpro-plugin'); ?></option>
                                    <option value="moderate"><?php echo $lp_priceSymbol2 . ' - ' . esc_html__('Moderate', 'listingpro-plugin'); ?></option>
                                    <option value="pricey"><?php echo $lp_priceSymbol3 . ' - ' . esc_html__('Pricey', 'listingpro-plugin'); ?></option>
                                    <option value="ultra_high_end"><?php echo $lp_priceSymbol4 . ' - ' . esc_html__('Ultra High', 'listingpro-plugin'); ?></option>
                                </select>
                            <?php
                            } else {
                                $priceyArray = array(
                                    'notsay' => $place1,
                                    'inexpensive' => esc_html__('Inexpensive', 'listingpro-plugin'),
                                    'moderate' => esc_html__('Moderate', 'listingpro-plugin'),
                                    'pricey' => esc_html__('Pricey', 'listingpro-plugin'),
                                    'ultra_high_end' => esc_html__('Ultra High', 'listingpro-plugin'),
                                );
                            ?>
                                <select placeholder="<?php echo $place1; ?>" data-priceimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" id="price_status" name="price_status" class="chosen-select chosen-select7  postsubmitSelect <?php echo $atts['required-class']; ?>" tabindex="5">

                                    <?php
                                    foreach ($priceyArray as $key => $value) {
                                        if ($price_status == $key) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        if ($key == 'notsay') {
                                            echo '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
                                        } elseif ($key == 'inexpensive') {
                                            echo '<option ' . $selected . ' value="' . $key . '">' . $lp_priceSymbol . ' - ' . $value . '</option>';
                                        } elseif ($key == 'moderate') {
                                            echo '<option ' . $selected . ' value="' . $key . '">' . $lp_priceSymbol2 . ' - ' . $value . '</option>';
                                        } elseif ($key == 'pricey') {
                                            echo '<option ' . $selected . ' value="' . $key . '">' . $lp_priceSymbol3 . ' - ' . $value . '</option>';
                                        } elseif ($key == 'ultra_high_end') {
                                            echo '<option ' . $selected . ' value="' . $key . '">' . $lp_priceSymbol4 . ' - ' . $value . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            <?php
                            }
                            ?>

                        </div>
                        <?php

                        if (isset($atts['tiptitle'])) {
                            $title_arr = explode('|', $atts['tiptitle']);
                        }
                        if (isset($atts['tipdesc'])) {
                            $desc_arr = explode('|', $atts['tipdesc']);
                        }
                        if (isset($atts['tipimage'])) {
                            $img_arr = explode('|', $atts['tipimage']);
                        }
                        if (!empty($title_arr)) {
                            if (isset($title_arr[0])) {
                                $title1 = $title_arr[0];
                            }
                            if (isset($title_arr[1])) {
                                $title2 = $title_arr[1];
                            }
                            if (isset($title_arr[2])) {
                                $title3 = $title_arr[2];
                            }
                        }
                        if (!empty($desc_arr)) {
                            if (isset($desc_arr[0])) {
                                $desc1 = $desc_arr[0];
                            }
                            if (isset($desc_arr[1])) {
                                $desc2 = $desc_arr[1];
                            }
                            if (isset($desc_arr[2])) {
                                $desc3 = $desc_arr[2];
                            }
                        }
                        if (!empty($img_arr)) {
                            if (isset($img_arr[0])) {
                                $img1 = $img_arr[0];
                            }
                            if (isset($img_arr[1])) {
                                $img2 = $img_arr[1];
                            }
                            if (isset($img_arr[2])) {
                                $img3 = $img_arr[2];
                            }
                        }


                        if ($price_show == "true") {

                        ?>
                            <?php
                            if ($atts['pricefrom'] == 'yes') {


                            ?>

                                <div class="col-md-4">
                                    <label for="listingprice"><?php echo $label2; ?></label>
                                    <input value="<?php echo $listingprice; ?>" data-priceimg="<?php echo $img2; ?>" data-quick-tip="<h2><?php echo $title2; ?></h2><p><?php echo $desc2; ?></p>" type="text" name="listingprice" class="form-control <?php echo $atts['required-class']; ?>" id="listingprice" placeholder="<?php echo $place2; ?>">
                                </div>
                            <?php
                            }
                            if ($atts['priceto'] == 'yes') {
                            ?>
                                <div class="col-md-4">
                                    <label for="listingptext"><?php echo $label3; ?></label>
                                    <input value="<?php echo $listingptext; ?>" data-priceimg="<?php echo $img3; ?>'>" data-quick-tip="<h2><?php echo $title3; ?></h2><p><?php echo $desc3; ?></p>" type="text" name="listingptext" class="form-control <?php echo $atts['required-class']; ?>" id="listingptext" placeholder="<?php echo $place3; ?>">
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('businessHours')) {
            function businessHours($atts)
            {
                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

                $ophSwitch = $listingpro_options['oph_switch'];
                $hours_show = get_post_meta($plan_id, 'listingproc_bhours', true);

                if ($plan_id == "none") {
                    $hours_show = 'true';
                }
                if ($hours_show == "true") {
                ?>
                    <div class="row">
                        <div data-hoursimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" class="form-group clearfix margin-bottom-0 col-md-12 bussin-top <?php if (!empty($atts['required-class'])) {
                                                                                                                                                                                                                                                    echo 'required-business-hours';
                                                                                                                                                                                                                                                }; ?>">
                            <?php
                            if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                                echo LP_operational_hours_form($_GET['lp_post'], true);
                            } else {
                                $fakeID = '';
                                echo LP_operational_hours_form($fakeID, false);
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('socialMedia')) {
            function socialMedia($atts)
            {
                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $lp_post = '';
                $twitter = '';
                $facebook = '';
                $linkedin = '';

                $youtube = '';
                $instagram = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];

                    $twitter = listing_get_metabox_by_ID('twitter', $lp_post);
                    $facebook = listing_get_metabox_by_ID('facebook', $lp_post);
                    $linkedin = listing_get_metabox_by_ID('linkedin', $lp_post);

                    $youtube = listing_get_metabox_by_ID('youtube', $lp_post);
                    $instagram = listing_get_metabox_by_ID('instagram', $lp_post);
                }

                $social_show = get_post_meta($plan_id, 'listingproc_social', true);
                $social_show_switch = lp_theme_option('listin_social_switch');

                if ($plan_id == "none") {
                    $social_show = 'true';
                }

                if ($social_show == "true") {
                    $twSwitch = $listingpro_options['tw_switch'];
                    $fbSwitch = $listingpro_options['fb_switch'];
                    $lnkSwitch = $listingpro_options['lnk_switch'];

                    $ytSwitch = $listingpro_options['yt_switch'];
                    $instaSwitch = $listingpro_options['insta_switch'];
                ?>
                    <div class="row <?php if (!empty($atts['required-class'])) {
                                        echo 'required-social-media';
                                    }; ?>">
                        <?php
                        if ($twSwitch == 1) {
                            echo '<input type="hidden" class="form-control" name="twitter" id="inputTwitter" value="' . $twitter . '">';
                        }
                        if ($fbSwitch == 1) {
                            echo '<input type="hidden" class="form-control" name="facebook" id="inputFacebook" value="' . $facebook . '">';
                        }
                        if ($lnkSwitch == 1) {
                            echo '<input type="hidden" class="form-control" name="linkedin" id="inputLinkedIn" value="' . $linkedin . '">';
                        }
                        if ($ytSwitch == 1) {
                            echo '<input type="hidden" class="form-control" name="youtube" id="inputYoutube" value="' . $youtube . '">';
                        }
                        if ($instaSwitch == 1) {
                            echo '<input type="hidden" class="form-control" name="instagram" id="inputInstagram" value="' . $instagram . '">';
                        }
                        if (isset($lp_post) && !empty($lp_post)) {
                        ?>
                            <div class="style2-social-list-section">
                                <?php
                                if ($twSwitch == 1 && !empty($twitter)) {
                                    echo '<div class="social-row social-row-Twitter"><label>' . esc_html__('Twitter', 'listingpro-plugin') . '</label><span>' . $twitter . '</span><a class="remove-social-type" data-social="Twitter"><i class="fa fa-times"></i></a></div>';
                                }
                                if ($fbSwitch == 1 && !empty($facebook)) {
                                    echo '<div class="social-row social-row-Facebook"><label>' . esc_html__('Facebook', 'listingpro-plugin') . '</label><span>' . $facebook . '</span><a class="remove-social-type" data-social="Facebook"><i class="fa fa-times"></i></a></div>';
                                }
                                if ($lnkSwitch == 1 && !empty($linkedin)) {
                                    echo '<div class="social-row social-row-LinkedIn"><label>' . esc_html__('LinkedIn', 'listingpro-plugin') . '</label><span>' . $linkedin . '</span><a class="remove-social-type" data-social="LinkedIn"><i class="fa fa-times"></i></a></div>';
                                }

                                if ($ytSwitch == 1 && !empty($youtube)) {
                                    echo '<div class="social-row social-row-Youtube"><label>' . esc_html__('Youtube', 'listingpro-plugin') . '</label><span>' . $youtube . '</span><a class="remove-social-type" data-social="Youtube"><i class="fa fa-times"></i></a></div>';
                                }
                                if ($instaSwitch == 1 && !empty($instagram)) {
                                    echo '<div class="social-row social-row-Instagram"><label>' . esc_html__('Instagram', 'listingpro-plugin') . '</label><span>' . $instagram . '</span><a class="remove-social-type" data-social="Instagram"><i class="fa fa-times"></i></a></div>';
                                }
                                ?>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="style2-social-list-section"></div>
                        <?php
                        }

                        ?>
                        <div class="style2-add-new-social-sec">
                            <div class="col-md-2"><?php echo $atts['label']; ?></div>
                            <div class="col-md-3">
                                <select data-socialimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" class="select2" id="get_media">
                                    <option><?php echo esc_html__('Please Select', 'listingpro-plugin'); ?></option>
                                    <?php
                                    if ($instaSwitch == 1) {
                                        echo '<option>' . esc_html__('Instagram', 'listingpro-plugin') . '</option>';
                                    }
                                    if ($ytSwitch == 1) {
                                        echo '<option>' . esc_html__('Youtube', 'listingpro-plugin') . '</option>';
                                    }

                                    if ($lnkSwitch == 1) {
                                        echo '<option>' . esc_html__('LinkedIn', 'listingpro-plugin') . '</option>';
                                    }
                                    if ($fbSwitch == 1) {
                                        echo '<option>' . esc_html__('Facebook', 'listingpro-plugin') . '</option>';
                                    }
                                    if ($twSwitch == 1) {
                                        echo '<option>' . esc_html__('Twitter', 'listingpro-plugin') . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input placeholder="<?php echo $atts['placeholder']; ?>" type="text" class="form-control" value="" id="get_media_url">
                            </div>
                            <div class="col-md-1"><a id="add-new-social-url"><i class="fa fa-plus-square"></i></a></div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        if (!function_exists('faqs_cb')) {
            function faqs_cb($atts)
            {

                global $listingpro_options;

                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

                $faqs_show = get_post_meta($plan_id, 'listingproc_faq', true);
                $faq_switch = $listingpro_options['faq_switch'];

                if ($plan_id == "none") {
                    $faqs_show = 'true';
                }
                if ($faqs_show == "true") {
                    $lp_post = '';
                    if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                        $lp_post = $_GET['lp_post'];
                    }
                    $faqs = listing_get_metabox_by_ID('faqs', $lp_post);

                    if (!empty($faqs)) {
                        $faq = $faqs['faq'];
                        $faqans = $faqs['faqans'];
                        $n = count($faq);
                    }
                    $listing_faq_text = $listingpro_options['listing_faq_text'];
                    $listing_faq_tabs_text = $listingpro_options['listing_faq_tabs_text'];

                    $FaqHasData = false;
                    if (!empty($faq) && !empty($faqans)) {
                        foreach ($faq as $faqData) {
                            if ($faqData == "") {
                            } else {
                                $FaqHasData = true;
                            }
                        }
                    }
                    if ($FaqHasData === true) {
                    ?>
                        <div class="row">
                            <div class="form-group clearfix margin-bottom-0">
                                <div id="tabs" class="lsiting-submit-faq-tabs clearfix" data-faqtitle="<?php echo $listing_faq_text ?>">
                                    <?php
                                    $j = 1;
                                    while ($j <= $n) {
                                        $faqQ = $faq[$j];
                                        if (!empty($faqQ)) {
                                    ?>
                                            <div id="tabs-<?php echo $j ?>">
                                                <div class="col-md-2">
                                                    <label for="inpuFaqsLp"><?php echo $atts['label']; ?><?php echo $j ?></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <input data-faqimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" class="form-control" data-faqmaintitle="<?php echo $listing_faq_text; ?>" name="faq[<?php echo $j ?>]" id="inpuFaqsLp<?php echo $j ?>" value="<?php echo $faq[$j] ?>" placeholder="<?php echo $atts['placeholder']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea data-faqimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo esc_html__('FAQ Answers', 'listingpro-plugin'); ?></h2><p><?php echo esc_html__('Share some of the most asked question and answers so they know you are serious about your business and truly care for your customers.', 'listingpro-plugin'); ?></p>" class="form-control" placeholder="<?php echo esc_html__('Answer', 'listingpro-plugin'); ?>" name="faqans[<?php echo $j ?>]" rows="8" id="inputDescriptionFaq<?php echo $j ?>"><?php echo $faqans[$j] ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                        $j++;
                                    }
                                    ?>
                                    <div class="appendother"></div>
                                    <div class="btn-container faq-btns clearfix">
                                        <ul><?php
                                            if (count($faq) > 1) {
                                                $word = preg_replace('/\d/', '', $listing_faq_tabs_text);
                                                $i = 1;
                                                foreach ($faq as $q) {
                                                    if (!empty($q)) {
                                            ?>
                                                        <li>
                                                            <a data-faq-text="<?php echo $listing_faq_tabs_text ?>" href="#tabs-<?php echo $i ?>"><?php echo $word ?><?php echo $i ?></a>
                                                        </li>
                                                <?php
                                                        $i++;
                                                    }
                                                }
                                            } else {
                                                ?>
                                                <li><a href="#tabs-1" data-faq-text="<?php echo $listing_faq_tabs_text ?>"><?php echo $listing_faq_tabs_text ?></a>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                        <a id="tabsbtn" class="lp-secondary-btn btn-first-hover style2-tabsbtn"><i class="fa fa-plus-square"></i><?php echo esc_html__('add new', 'listingpro-plugin'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="row">
                            <div class="form-group clearfix margin-bottom-0 col-md-12">
                                <div id="tabs" class="lsiting-submit-faq-tabs clearfix pos-relative" data-faqtitle="<?php echo $listing_faq_text ?>">
                                    <div id="tabs-1">
                                        <div class="col-md-2">
                                            <label for="inpuFaqsLp"><?php echo $atts['label']; ?></label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <input data-faqimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" class="form-control <?php echo $atts['required-class']; ?>" data-faqmaintitle="<?php echo $listing_faq_text; ?>" name="faq[1]" id="inpuFaqsLp" placeholder="<?php echo $atts['placeholder']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <textarea data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" class="form-control <?php echo $atts['required-class']; ?>" placeholder="<?php echo esc_html__('Answer', 'listingpro-plugin'); ?>" name="faqans[1]" rows="8" id="inputDescriptionFaq"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="appendother"></div>
                                    <div class="btn-container faq-btns clearfix">
                                        <ul>
                                            <li><a href="#tabs-1" data-faq-text="' . $listing_faq_tabs_text . '"><?php echo $listing_faq_tabs_text; ?></a>
                                            </li>
                                        </ul>
                                        <a id="tabsbtn" class="lp-secondary-btn btn-first-hover style2-tabsbtn"><i class="fa fa-plus-square"></i><?php echo esc_html__('add new', 'listingpro-plugin'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
            }
        }

        if (!function_exists('inputDescription')) {
            function inputDescription($atts)
            {
                $lp_post = '';
                $pcontent = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $page_data = get_page($lp_post);
                    $pcontent = $page_data->post_content;
                }
                ?>
                <div class="row">
                    <div class="form-group col-md-12 col-xs-12">
                        <label class="description-tip" for="inputDescription" data-desimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>"><?php echo $atts['label'] ?></label>
                        <?php
                        if (isset($lp_post) && !empty($lp_post)) {
                            $settings = array(
                                'wpautop' => true,
                                'textarea_name' => 'postContent',
                                'editor_class' => 'lp-required-field',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'tinymce' => array(
                                    'theme_advanced_buttons1' => '',
                                    'theme_advanced_buttons2' => false,
                                    'theme_advanced_buttons3' => false,
                                    'theme_advanced_buttons4' => false,
                                ),
                                'quicktags' => false,
                            );
                            wp_editor($pcontent, 'inputDescription', $settings);
                        } else {
                            $settings = array(
                                'wpautop' => true,
                                'textarea_name' => 'postContent',
                                'editor_class' => 'lp-required-field',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'tinymce' => array(
                                    'theme_advanced_buttons1' => '',
                                    'theme_advanced_buttons2' => false,
                                    'theme_advanced_buttons3' => false,
                                    'theme_advanced_buttons4' => false,
                                ),
                                'quicktags' => false,
                            );
                            wp_editor($atts['placeholder'], 'inputDescription', $settings);
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        if (!function_exists('inputTags')) {
            function inputTags($atts)
            {

                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

                $lp_post = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                }

                $tags_show = get_post_meta($plan_id, 'listingproc_tag_key', true);
                $tags_switch = $listingpro_options['tags_switch'];

                if ($plan_id == "none") {
                    $tags_show = 'true';
                }
                if ($tags_show == "true") {
                ?>
                    <div class="row">
                        <div class="form-group col-md-12 col-xs-12 lp-social-area">
                            <div class="form-group col-md-12 col-xs-12" style="padding:0px;">
                                <label for="inputTags"> <?php echo $atts['label']; ?> </label>
                                <div class="help-text">
                                    <a href="#" class="help"><i class="fa fa-question"></i></a>
                                    <div class="help-tooltip">
                                        <p><?php echo esc_html__('These keywords or tags will help your listing to find in search. Add a comma separated list of keywords related to your business.', 'listingpro-plugin'); ?></p>
                                    </div>
                                </div>
                                <?php
                                if (isset($lp_post) && !empty($lp_post)) {
                                ?>
                                    <textarea class="form-control <?php echo $atts['required-class']; ?>" name="tags" id="inputTags inputtagsquicktip" data-tagimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" placeholder="<?php echo $atts['placeholder']; ?>"><?php
                                                                                                                                                                                                                                                                                                                                                            $tags = get_the_terms($lp_post, 'list-tags');
                                                                                                                                                                                                                                                                                                                                                            if ($tags and !is_wp_error($tags)) {
                                                                                                                                                                                                                                                                                                                                                                $names = wp_list_pluck($tags, 'name');
                                                                                                                                                                                                                                                                                                                                                                echo implode(',', $names);
                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                            ?></textarea>
                                <?php
                                } else {
                                ?>
                                    <textarea class="form-control <?php echo $atts['required-class']; ?>" data-tagimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" name="tags" id="inputTags" placeholder="<?php echo $atts['placeholder']; ?>"></textarea>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
        }

        if (!function_exists('postVideo')) {
            function postVideo($atts)
            {
                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';
                $video = '';
                $video_show = get_post_meta($plan_id, 'video_show', true);
                $vdoSwitch = $listingpro_options['vdo_switch'];

                if ($plan_id == "none") {
                    $video_show = 'true';
                }
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $video = listing_get_metabox_by_ID('video', $_GET['lp_post']);
                }

                if ($video_show == "true") {
                ?>
                    <div class="row">
                        <div class="form-group clearfix col-md-12">
                            <label for="postVideo"><?php echo $atts['label']; ?>
                                <span><?php echo esc_html__('(Optional)', 'listingpro-plugin'); ?></span></label>
                            <input value="<?php echo $video; ?>" data-videoimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" type="text" class="form-control <?php echo $atts['required-class']; ?>" name="postVideo" id="postVideo" placeholder="<?php echo esc_html__('ex: https://youtu.be/lY2yjAdbvdQ', 'listingpro-plugin'); ?>">
                        </div>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('postGallery')) {
            function postGallery($atts)
            {
                global $listingpro_options;
                $plan_id = isset($GLOBALS['plan_id_builder']) ? $GLOBALS['plan_id_builder'] : 'none';

                $lp_post = '';
                $galleryImagessize = 0;
                $GalimageCount = 0;

                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                }


                $gallery_show = get_post_meta($plan_id, 'gallery_show', true);
                $fileSwitch = $listingpro_options['file_switch'];

                if ($plan_id == "none") {
                    $gallery_show = 'true';
                }
                if ($gallery_show == 'true') {

                    if (isset($lp_post) && !empty($lp_post)) {
                        $galleryImagesIDS = get_post_meta($lp_post, 'gallery_image_ids', true);
                        $galleryImagesIDS = explode(',', $galleryImagesIDS);
                        if (!empty($galleryImagesIDS) && count($galleryImagesIDS) >= 1) {
                            $GalimageCount = count($galleryImagesIDS);
                            foreach ($galleryImagesIDS as $galID) {
                                $bitesize = filesize(get_attached_file($galID));
                                $galleryImagessize += $bitesize;
                            }
                        }
                    }

                    $upload_icon = '';
                    $upload_icon = '<i class="fa fa-upload" aria-hidden="true"></i>';
                ?>
                    <div class="row">
                        <div class="col-md-12 form-group clearfix margin-bottom-0 lp-img-gall-upload-section lplistgallery" data-featureimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>" data-savedgallerysize="<?php echo $GalimageCount; ?>" data-savedgallweight="<?php echo $galleryImagessize; ?>">
                            <div class="col-sm-12 padding-left-0 padding-right-0">
                                <label for="postVideo"><?php echo $atts['label'] ?></label>
                                <div class="jFiler-input-dragDrop pos-relative">
                                    <div class="jFiler-input-inner">
                                        <div class="jFiler-input-icon">
                                            <i class="icon-jfi-cloud-up-o"></i>
                                        </div>
                                        <div class="jFiler-input-text">
                                            <h3 style="margin:20px 0px;"><?php echo $upload_icon; ?><?php echo esc_html__('Drop files here or click to upload', 'listingpro-plugin'); ?></h3>
                                        </div>
                                        <a class="jFiler-input-choose-btn blue"><?php echo esc_html__('Browse Files', 'listingpro-plugin'); ?></a>
                                        <div class="filediv">
                                            <input type="file" name="listingfiles[]" class="file <?php echo $atts['required-class']; ?>" multiple>
                                        </div>
                                        <?php
                                        if (!empty($galleryImagesIDS) && count($galleryImagesIDS) >= 1) {
                                            $GalimageCount = count($galleryImagesIDS);
                                            $galleryImagessize = 0;
                                            foreach ($galleryImagesIDS as $galID) {
                                                $galleryImagessize = 0;
                                                $bitesize = filesize(get_attached_file($galID));
                                                $sizeinUnits = size_format($bitesize, 4);
                                                $sizedArray = explode(' ', $sizeinUnits);
                                                if (is_array($sizedArray) && isset($sizedArray[1]) && $sizedArray[1] == 'MB') {
                                                    $galleryImagessize += $sizedArray[0] * 1000000;
                                                } elseif (is_array($sizedArray) && isset($sizedArray[1]) && $sizedArray[1] == 'KB') {
                                                    $sizeinmb = $sizedArray[0] * 1000;
                                                    $galleryImagessize += $sizeinmb;
                                                }


                                                $imgFull = wp_get_attachment_image_src($galID, 'thumbnail');
                                                if (!empty($imgFull[0])) {
                                                    echo '      
                                        <div class="filediv" data-savedgallerysize="' . $GalimageCount . '" data-savedgallweight ="' . $galleryImagessize . '">                         
                                            <ul class="jFiler-items-list jFiler-items-grid grid1">
                                                <li class="jFiler-item">    
                                                    <div class="jFiler-item-container">
                                                        <div class="jFiler-item-inner">     
                                                            <div class="jFiler-item-thumb">
                                                                <img src="' . $imgFull[0] . '" alt="post1" />
                                                            </div>      
                                                        </div>      
                                                    </div>
                                                    <a class="icon-jfi-trash jFiler-item-trash-action lpsavedcrossgall"><i class="fa fa-trash"></i></a> 
                                                    <input name="listingfiles[]" calss="file" multiple="multiple" value="' . $galID . '" type="hidden">
                                                    <input name="listingeditfiles[]" calss="file" value="' . $galID . '" type="hidden">
                                                </li>
                                            </ul>
                                        </div>';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
        }
        if (!function_exists('featuredimage')) {
            function featuredimage($atts)
            {

                $lp_post = '';
                $lp_featured_img_url = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $lp_featured_img_url = get_the_post_thumbnail_url($lp_post, array(30, 30));
                }
                ?>
                <div class="form-group clearfix margin-bottom-0 margin-top-10 lp-listing-featuredimage col-md-6">
                    <?php
                    if (isset($lp_featured_img_url) && !empty($lp_featured_img_url)) {
                        echo '<label class="margin-top-10">' . $atts['label'] . '</label>';
                    } else {
                        echo '<label class="margin-top-10">' . $atts['label'] . '</label>';
                    }
                    ?>
                    <div class="custom-file">
                        <input style="display:none;" type="file" name="lp-featuredimage[]" id="lp-featuredimage" class="inputfile inputfile-3 <?php echo $atts['required-class']; ?>" data-multiple-caption="{count} files selected" />
                        <label class="featured-img-label" for="lp-featuredimage" data-featureimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>">
                            <p><?php echo esc_html__('Browse', 'listingpro-plugin'); ?></p>
                            <span><?php echo $atts['placeholder']; ?>&hellip;</span>
                        </label>
                    </div>
                    <?php
                    if (isset($lp_featured_img_url) && !empty($lp_featured_img_url)) {
                        echo '<img class="lp-prevewFeatured lpchangeinstantimg" src = "' . esc_url($lp_featured_img_url) . '" alt="image" />';
                    }
                    ?>
                </div>
            <?php

            }
        }
        if (!function_exists('businessLogo')) {
            function businessLogo($atts)
            {
                global $listingpro_options;
                $lp_business_logo_url = '';
                $lp_post = '';
                if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
                    $lp_post = $_GET['lp_post'];
                    $lp_business_logo_url = listing_get_metabox_by_ID('business_logo', $lp_post);
                }
            ?>
                <div class="form-group clearfix margin-bottom-0 margin-top-10 lp-listing-featuredimage col-md-6">
                    <label class="margin-top-10"><?php echo $atts['label']; ?></label>

                    <div class="custom-file">
                        <input style="display:none;" type="file" name="business_logo[]" id="business_logo" class="inputfile inputfile-4 <?php echo $atts['required-class']; ?>" />
                        <label class="b-logo-img-label" for="business_logo" data-blogoimg="<?php echo $atts['tipimage']; ?>" data-quick-tip="<h2><?php echo $atts['tiptitle']; ?></h2><p><?php echo $atts['tipdesc']; ?></p>">
                            <p><?php echo esc_html__('Browse', 'listingpro-plugin'); ?></p>
                            <span><?php echo esc_html__('Choose a file', 'listingpro-plugin'); ?>&hellip;</span>
                        </label>
                    </div>
                    <?php if (isset($lp_business_logo_url) && !empty($lp_business_logo_url)) { ?>
                        <img style="height:63px; width: 63px;" class="lp-prevewFeatured lpchangeinstantimg" src="<?php echo esc_url($lp_business_logo_url); ?>" alt="image" />
                    <?php } ?>
                </div>
            <?php

            }
        }


        if (!function_exists('lp_form_text_field')) {
            function lp_form_text_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <input type="text" class="form-control <?php echo $class; ?> <?php echo $required; ?>" name="lp_form_fields_inn[<?php echo $name; ?>]" id="field-<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>">
                    <span id="<?php echo $name; ?>"></span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_url_field')) {
            function lp_form_url_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <input type="url" class="form-control <?php echo $class; ?> <?php echo $required; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>">
                    <span id="<?php echo $name; ?>"></span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_tel_field')) {
            function lp_form_tel_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <input type="tel" class="form-control <?php echo $class; ?> <?php echo $required; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>">
                    <span id="<?php echo $name; ?>"></span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_range_field')) {
            function lp_form_range_field($name, $placeholder, $class, $required, $min, $max, $step, $def, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <span class="lp-lead-select-text"><?php echo $label; ?></span>
                    <div class="range-wraper lp-lead-range-wraper">
                        <span class="range-c">Default Range: <?php echo $def; ?></span>
                        <span class="range-start"><?php echo $min; ?></span>
                        <div class="range-slidecontainer">
                            <input value="<?php echo $def; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" type="range" class="<?php echo $required; ?> lp-range-slide range-set form-control <?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>">
                        </div>
                        <span class="range-end"><?php echo $max; ?></span>
                    </div>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_date_field')) {
            function lp_form_date_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field input-group date datetimepicker2">
                    <label><?php echo $label; ?></label>
                    <span class="input-group-addon">
                        <input type="text" class="date1 form-control <?php echo $required; ?> <?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $label; ?>">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_time_field')) {
            function lp_form_time_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field input-group date datetimepicker1">
                    <label><?php echo $label; ?></label>
                    <span class="input-group-addon">
                        <input type="text" class="form-control <?php echo $required; ?> <?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $label; ?>">

                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                    </span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_datetime_local_field')) {
            function lp_form_datetime_local_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field input-group date datetimepicker3">
                    <label><?php echo $label; ?></label>
                    <span class="input-group-addon">
                        <input type="text" class="form-control <?php echo $required; ?> <?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $label; ?>">

                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_file_field')) {
            function lp_form_file_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <span class="lp-lead-select-text"><?php echo $label; ?></span>
                    <div class="custom-file lp-lead-custom-file">
                        <input style="display:none;" type="file" class="<?php echo $required; ?> inputfile inputfile-4 form-control <?php echo $class; ?>" name="<?php echo $name; ?>" id="business_logo" placeholder="<?php echo $placeholder; ?>">
                        <label class="b-logo-img-label" for="business_logo" data-quick-tip="quick tip for business logo">
                            <p><?php echo esc_html(__('Browse', 'listingpro-plugin')); ?></p>
                            <span><?php echo $placeholder; ?></span>
                        </label>
                    </div>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_email_field')) {
            function lp_form_email_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field form-group-icon">
                    <label><?php echo $label; ?></label>
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <input type="email" class="form-control <?php echo $class; ?> <?php echo $required; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>">
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_textarea_field')) {
            function lp_form_textarea_field($name, $placeholder, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field">
                    <label><?php echo $label; ?></label>
                    <textarea class="form-control <?php echo $required; ?> <?php echo $class; ?>" rows="5" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>"></textarea>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_dropdown_field')) {
            function lp_form_dropdown_field($name, $options, $class, $required, $label, $exclusive)
            {
                $options_arr = explode(',', $options);
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field <?php echo $class; ?>">
                    <label><?php echo $label; ?></label>
                    <select class="form-control <?php echo $required; ?>" name="lp_form_fields_inn[<?php echo $name; ?>]" id="field-<?php echo $name; ?>">
                        <option value="0">select otpion</option>
                        <?php
                        foreach ($options_arr as $value) {
                            if (!empty($value)) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_checkbox_field')) {
            function lp_form_checkbox_field($name, $options, $class, $required, $label, $exclusive)
            {
                $options_arr = explode(',', $options);
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field <?php echo $class; ?>" id="<?php echo $name; ?>" <?php echo $required; ?>>
                    <label><?php echo $label; ?></label>
                    <span class="lp-lead-select-text"><?php echo $label; ?></span>
                    <?php
                    $option_coutner = 0;
                    foreach ($options_arr as $value) {
                        $option_coutner++;
                        if (!empty($value)) {
                            echo '<label class="lp-lead-check-container"><input value="' . $value . '" id="field-' . $name . '-' . $option_coutner . '" type="checkbox" name="lp_form_fields_inn[' . $name . '][]" class="form-control ' . $required . ' ' . $class . '"> ' . $value . '<span class="lp-lead-check-checkmark"></span></label>';
                        }
                    }
                    ?>
                </div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_checkbox_switch_field')) {
            function lp_form_checkbox_switch_field($name, $options, $class, $required, $label, $exclusive)
            {
                ob_start();
            ?>
                <div class="clearfix"></div>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field <?php echo $class; ?>" id="<?php echo $name; ?>" <?php echo $required; ?>">
                    <label for="field-<?php echo $name; ?>"><?php echo $label; ?></label>
                    <input value="No" class="form-control switch-checkbox-hidden" type="hidden" name="lp_form_fields_inn[<?php echo $name; ?>]">
                    <label class="switch">
                        <input value="Yes" id="field-<?php echo $name; ?>" class="form-control switch-checkbox" type="checkbox" name="lp_form_fields_inn[<?php echo $name; ?>]">
                        <div class="slider round"></div>
                    </label>
                </div>
                <div class="clearfix"></div>
            <?php
                return ob_get_clean();
            }
        }
        if (!function_exists('lp_form_radio_field')) {
            function lp_form_radio_field($name, $options, $class, $required, $label, $exclusive)
            {
                $options_arr = explode(',', $options);
                ob_start();
            ?>
                <div class="form-group lp-form-builder-field-<?php echo $exclusive; ?> lp-form-builder-field <?php echo $class; ?>" id="<?php echo $name; ?>" <?php echo $required; ?>>
                    <span class="lp-lead-select-text"><?php echo $label; ?></span>
                    <?php
                    $options_counter = 0;
                    foreach ($options_arr as $value) {
                        $options_counter++;
                        if (!empty($value)) {
                            echo '<label class="lp-lead-radio-container"><input id="field-' . $name . '-' . $options_counter . '" type="radio" name="lp_form_fields_inn[' . $name . ']" class="form-control ' . $required . ' ' . $class . '" value="' . $value . '"> ' . $value . '<span class="lp-lead-checkmark"></span></label>';
                        }
                    }
                    ?>
                </div>
    <?php
                return ob_get_clean();
            }
        }
    }

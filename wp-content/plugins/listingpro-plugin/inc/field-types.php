<?php

/* ============== ListingPro get child term (tags) ============ */

if (!function_exists('listingpro_field_type')) {

    function listingpro_field_type($fieldIDs, $listingid = false) {
        $output = '';
        if (!empty($fieldIDs)) {
            foreach ($fieldIDs as $fieldID) {
                $typech = listing_get_metabox_by_ID('field-type', $fieldID);
                if (!empty($typech) && $typech == 'checkbox') {
                    $output .= listingpro_field_type_output_checkbox($fieldID, $listingid);
                }
            }
            $output .= '<div class="clearfix"></div>';
            foreach ($fieldIDs as $fieldID) {
                $typech = listing_get_metabox_by_ID('field-type', $fieldID);
                if (!empty($typech) && $typech == 'check') {
                    $output .= listingpro_field_type_output_check($fieldID, $listingid);
                }
            }

            $output .= '<div class="clearfix"></div>';
            foreach ($fieldIDs as $fieldIDall) {
                $typeall = listing_get_metabox_by_ID('field-type', $fieldIDall);
                if (!empty($typeall) && $typeall != 'checkbox' && $typeall != 'check') {
                    $output .= call_user_func('listingpro_field_type_output_' . $typeall, $fieldIDall, $listingid);
                }
            }
        }

        return $output;
    }

}

/* ============== ListingPro Field (TEXT) ============ */

if (!function_exists('listingpro_field_type_output_text')) {

    function listingpro_field_type_output_text($fieldID, $listingid, $atts = array()) {
        $value = '';
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $value = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($value) && !empty($listingid)) {
            $value = listing_get_fields($slug, $listingid);
        }
        $placeholder = null;
        if (isset($atts['placeholder'])){
            $placeholder = $atts['placeholder'];
        }
        $required = null;
        if (isset($atts['required-class'])){
            $required = $atts['required-class'];
        }

        $field_metas = get_post_meta($fieldID, 'lp_listingpro_options', true);
        $placeholder = isset($field_metas['field-placeholder']) && !empty($field_metas['field-placeholder']) ? $field_metas['field-placeholder'] : $placeholder;
        
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
            <label for="feature_' . $slug . '">' . $title . '</label>
            <input type="text" id="field-' . $slug . '" name="lp_form_fields_inn[' . $slug . ']" class="form-control '. $required .'" value="' . $value . '" placeholder="'. $placeholder .'">					
        </div>';
        return $output;
    }

}

/* ============== ListingPro Field (textarea) ============ */

if (!function_exists('listingpro_field_type_output_textarea')) {

    function listingpro_field_type_output_textarea($fieldID, $listingid, $atts = array()) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $value = listing_get_fields($slug, $_GET['lp_post']);
        }
        
        $field_metas = get_post_meta($fieldID, 'lp_listingpro_options', true);
        $placeholder = isset($field_metas['field-placeholder']) && !empty($field_metas['field-placeholder']) ? $field_metas['field-placeholder'] : $atts['placeholder'];
        
        $output = null;
        $output = '<div class="col-md-12 col-sm-12 col-xs-12 form-group">
            <label for="feature_' . $slug . '">' . $title . '</label>					 
            <textarea id="field-' . $slug . '" class="form-control '. $atts['required-class'] .'" name="lp_form_fields_inn[' . $slug . ']" rows="5" placeholder="'. $placeholder .'">' . $value . '</textarea>
        </div>';
        return $output;
    }

}

/* ============== ListingPro Field (Normal Checkbox) ============ */

if (!function_exists('listingpro_field_type_output_check')) {

    function listingpro_field_type_output_check($fieldID, $listingid) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $value = '';
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $value = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($value) && !empty($listingid)) {
            $value = listing_get_fields($slug, $listingid);
        }

        $output = '<div class="col-md-2 col-sm-4 col-xs-6 form-group">
            <div class="checkbox pad-bottom-10">
                <input value="Yes" ' . checked($value, 'Yes', false) . ' id="check_' . $slug . '" type="checkbox" name="lp_form_fields_inn[' . $slug . ']">
                <label for="check_' . $slug . '">' . $title . '</label>
            </div>
        </div>';
        return $output;
    }

}
/* ============== ListingPro Field (Switch) ============ */

if (!function_exists('listingpro_field_type_output_checkbox')) {

    function listingpro_field_type_output_checkbox($fieldID, $listingid) {
        $postar = get_post($fieldID);
        $value = '';
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $value = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($value) && !empty($listingid)) {
            $value = listing_get_fields($slug, $listingid);
        }
        $output = null;
        $output = '<div class="col-md-3 col-sm-3 col-xs-6 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input value="No" class="form-control switch-checkbox-hidden" type="hidden" name="lp_form_fields_inn[' . $slug . ']">
					<label class="switch">									
					<input value="Yes" ' . checked($value, 'Yes', false) . ' id="field-' . $slug . '" class="form-control switch-checkbox" type="checkbox" name="lp_form_fields_inn[' . $slug . ']">										
					 <div class="slider round"></div>
					</label>					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (wysiwyg) ============ */

if (!function_exists('listingpro_field_type_output_wysiwyg')) {

    function listingpro_field_type_output_wysiwyg($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-12 col-sm-12 col-xs-12 form-group">
            <label for="feature_' . $slug . '">' . $title . '</label>					 
            <textarea id="field-' . $slug . '" class="form-control" name="lp_form_fields_inn[' . $slug . ']" rows="5"></textarea>
        </div>';
        return $output;
    }

}

/* ============== ListingPro Field (text_time) ============ */

if (!function_exists('listingpro_field_type_output_text_time')) {

    function listingpro_field_type_output_text_time($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (select_timezone) ============ */

if (!function_exists('listingpro_field_type_output_select_timezone')) {

    function listingpro_field_type_output_select_timezone($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (text_date_timestamp) ============ */

if (!function_exists('listingpro_field_type_output_text_date_timestamp')) {

    function listingpro_field_type_output_text_date_timestamp($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (text_datetime_timestamp) ============ */

if (!function_exists('listingpro_field_type_output_text_datetime_timestamp')) {

    function listingpro_field_type_output_text_datetime_timestamp($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}
/* ============== ListingPro Field (text_datetime_timestamp_timezone) ============ */

if (!function_exists('listingpro_field_type_output_text_datetime_timestamp_timezone')) {

    function listingpro_field_type_output_text_datetime_timestamp_timezone($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (color) ============ */

if (!function_exists('listingpro_field_type_output_color')) {

    function listingpro_field_type_output_color($fieldID) {
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $output = null;
        $output = '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<input id="field-' . $slug . '" class="form-control" type="text" name="lp_form_fields_inn[' . $slug . ']">					
				</div>'
        ;
        return $output;
    }

}

/* ============== ListingPro Field (checkboxes) ============ */

if (!function_exists('listingpro_field_type_output_checkboxes')) {

    function listingpro_field_type_output_checkboxes($fieldID, $listingid) {
        $output = null;
        $values = '';
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $values = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($values) && !empty($listingid)) {
            $values = listing_get_fields($slug, $listingid);
        }
        $options = listing_get_metabox_by_ID('multicheck-options', $fieldID);

        $options = explode(',', $options);
        $output .= '<div class="col-md-12 col-sm-12 col-xs-12 featuresDataContainer lp-check-custom-wrapp">
				<div class="form-group">
					<label>' . $title . '</label>'
        ;
        $countt = 1;
        foreach ($options as $option) {
            if (!empty($values) && in_array($option, $values)) {
                $cheched = "checked";
            } else {
                $cheched = '';
            }

            $output .= '<div class="radio-inline checkbox"><input ' . $cheched . ' id="field-' . $slug . $countt . '" class="" type="checkbox" name="lp_form_fields_inn[' . $slug . '][]" value="' . $option . '"><label for="field-' . $slug . $countt . '">' . $option . '</label></div>';

            $countt ++;
        }
        $output .= '</div></div>'
        ;
        return $output;
    }

}


/* ============== ListingPro Field (checkboxes) ============ */

if (!function_exists('listingpro_field_type_output_radio')) {

    function listingpro_field_type_output_radio($fieldID, $listingid) {
        $output = null;
        $values = '';
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $values = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($values) && !empty($listingid)) {
            $values = listing_get_fields($slug, $listingid);
        }
        $options2 = listing_get_metabox_by_ID('radio-options', $fieldID);

        $options2 = explode(',', $options2);
        $output .= '<div class="col-md-12 col-sm-12 col-xs-12 form-group">
					<div><label for="feature_' . $slug . '">' . $title . '</label></div>'
        ;
        $lCount = 1;
        foreach ($options2 as $option) {

            $output .= '<div class="radio-inline"><input ' . checked($values, $option, false) . ' style="vertical-align: middle;" id="field-' . $slug . '-' . $lCount . '" class="" type="radio" name="lp_form_fields_inn[' . $slug . ']" value="' . $option . '"><span style="margin-right:20px; vertical-align: middle"><strong>' . $option . '</strong></span></div>';
            $lCount++;
        }
        $output .= '</div>'
        ;
        return $output;
    }

}


/* ============== ListingPro Field (select) ============ */

if (!function_exists('listingpro_field_type_output_select')) {

    function listingpro_field_type_output_select($fieldID, $listingid) {
        $output = null;
        $values = null;
        $postar = get_post($fieldID);
        $slug = $postar->post_name;
        $title = get_the_title($fieldID);
        $options = listing_get_metabox_by_ID('select-options', $fieldID);
        if (isset($_GET['lp_post']) && !empty($_GET['lp_post'])) {
            $values = listing_get_fields($slug, $_GET['lp_post']);
        }
        if (empty($values) && !empty($listingid)) {
            $values = listing_get_fields($slug, $listingid);
        }
        $options = explode(',', $options);
        $output .= '<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<label for="feature_' . $slug . '">' . $title . '</label>
					<select  id="field-' . $slug . '" class="form-control" name="lp_form_fields_inn[' . $slug . ']">
					';
        $output .= '<option value="">' . esc_html__('Select Option', 'listingpro-plugin') . '</option>';
        foreach ($options as $option) {
            if ($option == $values) {
                $selected = "selected";
            } else {
                $selected = '';
            }
            $output .= '<option ' . $selected . ' value="' . $option . '">' . $option . '</option>';
        }
        $output .= '</select>
				</div>
				'
        ;
        return $output;
    }

}

/* for v2 */
add_action('save_post', 'lp_save_formfield_post_callback');
if (!function_exists('lp_save_formfield_post_callback')) {

    function lp_save_formfield_post_callback($post_id) {
        global $post;
        if (isset($post->post_type) && $post->post_type == 'form-fields') {
            if (isset($_POST['lp_field_filter_type'])) {
                $filterType = $_POST['lp_field_filter_type'];
                update_post_meta($post_id, 'lp_field_filter_type', $filterType);
            } else {
                delete_post_meta($post_id, 'lp_field_filter_type');
            }
        }
    }

}
	/* end for v2 */
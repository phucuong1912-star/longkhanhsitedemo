<?php

/**
 * Review ajax
 *
 */

/* ============== ListingPro Review Ajax Init ============ */

add_action('wp_ajax_listingpro_reviews_settings', 'listingpro_reviews_settings');
if (!function_exists('listingpro_reviews_settings')) {
    function listingpro_reviews_settings()
    {
        if (!is_user_logged_in()) {
            $return = array(
                'status'  => 'error',
                'message' => 'Authentication required'
            );
            die(json_encode($return));
        }
        
        if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
            $return = array(
                'status'  => 'error',
                'message' => 'Invalid nonce'
            );
            die(json_encode($return));
        }
        if (!current_user_can('manage_options')) {
            $return = array(
                'status'  => 'error',
                'message' => 'You do not have permission to manage plugin settings.'
            );
            die(json_encode($return));
        }

        if (isset($_POST['ratings_settings_data'])) {
            $return =   array();
            $userID =   get_current_user_id();
            update_option('lp-ratings-settings', $_POST['ratings_settings_data']);
            $return['status']   =   'success';
            $return['data']     =   $_POST['ratings_settings_data'];
            die(json_encode($return));
        }
    }
}

add_action('wp_ajax_listingpro_reviews_default_settings', 'listingpro_reviews_default_settings');
if (!function_exists('listingpro_reviews_default_settings')) {
    function listingpro_reviews_default_settings()
    {
        if (!is_user_logged_in()) {
            $return = array(
                'status'  => 'error',
                'message' => 'Authentication required'
            );
            die(json_encode($return));
        }
        
        if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
            $return = array(
                'status'  => 'error',
                'message' => 'Invalid nonce'
            );
            die(json_encode($return));
        }
        if (!current_user_can('manage_options')) {
            $return = array(
                'status'  => 'error',
                'message' => 'You do not have permission to manage plugin settings.'
            );
            die(json_encode($return));
        }
        if (isset($_POST['ratings_default_settings_data'])) {
            $return =   array();
            update_option('lp-ratings-default-settings', $_POST['ratings_default_settings_data']);
            $return['status']   =   'success';
            $return['data']     =   $_POST['ratings_default_settings_data'];
            die(json_encode($return));
        }
    }
}

add_action('wp_ajax_listingpro_reviews_settings_remove', 'listingpro_reviews_settings_remove');
if (!function_exists('listingpro_reviews_settings_remove')) {
    function listingpro_reviews_settings_remove()
    {
        if (!is_user_logged_in()) {
            $return = array(
                'status'  => 'error',
                'message' => 'Authentication required'
            );
            die(json_encode($return));
        }

        if (isset($_POST)) {
            $return =   array();

            if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'Invalid nonce'
                );
                die(json_encode($return));
            }
            if (!current_user_can('manage_options')) {
                $return = array(
                    'status'  => 'error',
                    'message' => 'You do not have permission to manage plugin settings.'
                );
                die(json_encode($return));
            }

            $target         =   isset($_POST['target']) ? sanitize_text_field($_POST['target']) : '';
            $removeType     =   isset($_POST['removeType']) ? sanitize_text_field($_POST['removeType']) : '';
            $removeDefault  =   isset($_POST['removeDefault']) ? sanitize_text_field($_POST['removeDefault']) : '';

            if ($removeDefault == 'yes') {
                $default_settings   =   get_option('lp-ratings-default-settings');

                if (isset($default_settings['default'][$target])) {
                    unset($default_settings['default'][$target]);
                    $default_settings['default']   =   array_values($default_settings['default']);
                    update_option('lp-ratings-default-settings', $default_settings);
                }

                $return['status']   =   'success';
                die(json_encode($return));
            }
            
            $existing_settings  =   get_option('lp-ratings-settings');

            if ($removeType == 'field') {
                $targetArr  =   explode('-', $target);
                $catIndex   =   isset($targetArr[0]) ? $targetArr[0] : '';
                $fieldIndex =   isset($targetArr[1]) ? $targetArr[1] : '';

                if (isset($existing_settings[$catIndex][$fieldIndex])) {
                    unset($existing_settings[$catIndex][$fieldIndex]);
                    $existing_settings[$catIndex]  =   array_values($existing_settings[$catIndex]);
                }
            }
            if ($removeType == 'group') {
                if (isset($existing_settings[$target])) {
                    unset($existing_settings[$target]);
                }
            }

            update_option('lp-ratings-settings', $existing_settings);
            $return['status']   =   'success';
            die(json_encode($return));
        }
    }
}

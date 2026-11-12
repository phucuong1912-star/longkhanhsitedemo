<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class cridio_Recaptcha_Logic
 */
class cridio_Recaptcha_Logic {
    /**
     * Checks if reCAPTCHA is enabled

     */
    public static function is_recaptcha_enabled($recaptchaType=null) {
        global $listingpro_options;
        $site_key = $listingpro_options['lp_recaptcha_site_key'];
        $secret_key = $listingpro_options['lp_recaptcha_secret_key'];


        $lp_recaptcha_lead = null;
        $lp_recaptcha_reviews = null;
        $lp_lead_form_switch    =   lp_theme_option('lp_lead_form_switch');
        $lp_review_switch   =   lp_theme_option('lp_review_switch');
        if(is_singular('listing') && ($lp_lead_form_switch || $lp_review_switch)){
            $lp_recaptcha_lead = lp_check_receptcha('lp_recaptcha_lead');
            $lp_recaptcha_reviews = lp_check_receptcha('lp_recaptcha_reviews');
        }


        $enableCaptchaRegister = null;
        $enableCaptchaLogin = null;
        if(!is_user_logged_in()){
            $enableCaptchaRegister = lp_check_receptcha('lp_recaptcha_registration');
            $enableCaptchaLogin = lp_check_receptcha('lp_recaptcha_login');
        }


        //for shortcodes
        $template_call = null;
        if(!empty($recaptchaType)){
            $template_call = lp_check_receptcha($recaptchaType);
        }

        if ( ! empty( $site_key ) && ! empty( $secret_key ) ) {
            $handle = 'recaptcha';
            $list = 'enqueued';
            if (wp_script_is( $handle, $list )) {
                return false;
            }

            if( !empty($enableCaptchaRegister) || !empty($enableCaptchaLogin) || !empty($lp_recaptcha_lead) || !empty($lp_recaptcha_reviews) || !empty($template_call) ){
                return true;
            }

        }

        return false;
    }

    /**
     * Checks if reCAPTCHA is valid
     */
    public static function is_recaptcha_valid( $token, $action ) {
        global $listingpro_options;
        $secret_key = $listingpro_options['lp_recaptcha_secret_key'];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'secret' => $secret_key,
                'response' => $token
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $responseKeys = json_decode($response,true);
        if($responseKeys["success"]) {
            if($responseKeys["action"]==$action) {
                if (!empty($responseKeys['score'])) {
                    return true;
                }else{
                    return false;
                }
            }else{
                //something else
                return false;
            }
        }else{
            return false;
        }
    }
}

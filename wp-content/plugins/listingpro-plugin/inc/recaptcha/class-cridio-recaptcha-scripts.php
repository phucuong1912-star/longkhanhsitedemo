<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require 'class-cridio-recaptcha-logic.php';
/**
 * Class cridio_Recaptcha_Scripts
 */
class cridio_Recaptcha_Scripts {
    /**
     * Initialize scripts
     */
    function __construct(){
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_frontend') );
        add_action( 'lp_enqueue_recaptcha_scripts', array($this, 'enqueue_frontend') );
        add_filter( 'cridio_asynchronous_scripts', array($this,'asynchronous_scripts')  );
        $this->get_google_recaptcha_by_page();
    }
    private function get_google_recaptcha_by_page(){
        $lp_recaptcha_lead = lp_theme_option('lp_recaptcha_lead');
        $lp_recaptcha_reviews = lp_theme_option('lp_recaptcha_reviews');
        if(is_singular('listing')){
            if(!empty($lp_recaptcha_lead) || !empty($lp_recaptcha_reviews)){
                do_action( 'wp_enqueue_scripts');
            }
        }
    }
    public function get_google_recaptcha_script(){
        $recaptchaSiteKey = lp_theme_option('lp_recaptcha_site_key');
        return "https://www.google.com/recaptcha/api.js?render=".$recaptchaSiteKey;
    }
    /**
     * Adds JavaScript files to load asynchronously using async defer attributes
     */
    public function asynchronous_scripts( $handles ) {
        $handles[] = 'recaptcha';
        return $handles;
    }
    /**
     * Loads frontend files
     */
    public function enqueue_frontend() {
        if ( cridio_Recaptcha_Logic::is_recaptcha_enabled(null) ) {
            wp_enqueue_script( 'recaptcha', $this->get_google_recaptcha_script(), array(), true, false );
        }
    }
}
if(!empty(lp_theme_option('lp_recaptcha_switch'))){
    $recaptchaOBJ = new cridio_Recaptcha_Scripts();
}
//calling script based on shortcodes
if(!function_exists('lp_enqueue_recaptcha_files')){
    function lp_enqueue_recaptcha_files($posts) {
        $found = false;
        if ( empty($posts) || is_admin() ){
            return $posts;
        }
        $recaptchaType = null;
        $sbmtURL = lp_theme_option('submit-listing');
        $editURL = lp_theme_option('edit-listing');
        if ( $sbmtURL ==  LP_current_location()){
            $recaptchaType = 'lp_recaptcha_listing_submission';
            $found = true;
        }
        if(isset($_GET['lp_post']) && !empty($_GET['lp_post'])){
            $url = strtok(LP_current_location(), '?');
            if ( $editURL ==  $url ){
                $recaptchaType = 'lp_recaptcha_listing_edit';
                $found = true;
            }
        }
        if ($found){
            //calling cridio recaptcha
            if(class_exists('cridio_Recaptcha_Scripts')){
                $objCaptcha = new cridio_Recaptcha_Scripts();
                if ( cridio_Recaptcha_Logic::is_recaptcha_enabled($recaptchaType) ) {
                    wp_enqueue_script( 'recaptcha', $objCaptcha->get_google_recaptcha_script(), array(), true, false );
                }
            }
        }
        return $posts;
    }
    add_action('the_posts', 'lp_enqueue_recaptcha_files' );
}
//calling based on page templates
if(!function_exists('lp_enqueue_template_based')){
    function lp_enqueue_template_based() {
        if ( is_page_template( 'template-contact.php' ) || is_page_template('template-dashboard.php') ) {
            /** Call landing-page-template-one enqueue */
            if(class_exists('cridio_Recaptcha_Scripts')){
                $objCaptcha = new cridio_Recaptcha_Scripts();
                $recaptchaType = null;
                if ( is_page_template( 'template-contact.php' )){
                    $recaptchaType = 'lp_recaptcha_contact';
                }elseif ( is_page_template( 'template-dashboard.php' )){
                    $dashboard = null;
                    if(isset($_GET['dashboard'])){
                        $dashboard = $_GET['dashboard'];
                        if($dashboard == 'update-profile'){
                            $recaptchaType = 'lp_recaptcha_userprofile';
                        }
                    }
                }
                if(!empty($recaptchaType)){
                    if ( cridio_Recaptcha_Logic::is_recaptcha_enabled($recaptchaType) ) {
                        wp_enqueue_script( 'recaptcha', $objCaptcha->get_google_recaptcha_script(), array(), true, false );
                    }
                }
            }
        }
    }
    add_action( 'wp_enqueue_scripts', 'lp_enqueue_template_based' );
}
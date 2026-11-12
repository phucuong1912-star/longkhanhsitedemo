<?php

/**
 * Class Name: cridio reCAPTCHA

 */

if ( ! class_exists( 'cridio_Recaptcha' ) ) {
    /**
     * Class cridio_Recaptcha
     */
    final class cridio_Recaptcha {
        /**
         * Initialize cridio_Recaptcha plugin
         */
        public function __construct() {
            $this->constants();
            $this->includes();
        }

        /**
         * Defines constants
         */
        public function constants() {
            define( 'CRIDIO_RECAPTCHA_URL', 'https://www.google.com/recaptcha/api/siteverify' );
            define( 'CRIDIO_RECAPTCHA_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Include classes         
         */
        public function includes() {            
            require_once CRIDIO_RECAPTCHA_DIR . 'recaptcha/class-cridio-recaptcha-scripts.php';
            require_once CRIDIO_RECAPTCHA_DIR . 'recaptcha/class-cridio-recaptcha-logic.php';
        }

       
       
    }

    new cridio_Recaptcha();
}

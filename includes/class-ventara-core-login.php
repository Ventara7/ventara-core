<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core_Login' ) ) {

    class Ventara_Core_Login {

        public static function init() {
            add_filter( 'login_headerurl', array( __CLASS__, 'alter_login_headerurl' ) );
            add_action( 'login_enqueue_scripts', array( __CLASS__, 'enqueue_login_styles' ) );
            add_action( 'login_header', array( __CLASS__, 'login_left_panel' ) );
            add_filter( 'login_message', array( __CLASS__, 'login_form_logo' ) );
            add_action( 'admin_notices', array( __CLASS__, 'display_admin_notice' ) );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_notice_styles' ) );
        }

        public static function alter_login_headerurl() {
            return home_url();
        }

        public static function enqueue_login_styles() {
            wp_enqueue_style(
                'ventara-core-login',
                VENTARA_CORE_ASSETS_URL . 'css/login.css',
                array(),
                VENTARA_CORE_VERSION
            );
        }

        public static function login_left_panel() {
            include VENTARA_CORE_TEMPLATES . 'login-left-panel.php';
        }

        public static function login_form_logo( $content ) {
            ob_start();
            include VENTARA_CORE_TEMPLATES . 'login-intro.php';
            return ob_get_clean() . $content;
        }

        public static function display_admin_notice() {
            include VENTARA_CORE_TEMPLATES . 'admin-notice.php';
        }

        public static function enqueue_admin_notice_styles() {
            wp_enqueue_style(
                'ventara-core-admin-notice',
                VENTARA_CORE_ASSETS_URL . 'css/admin-notice.css',
                array(),
                VENTARA_CORE_VERSION
            );
        }
    }
}

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
            add_action( 'init', array( __CLASS__, 'register_login_slug_rewrite_rule' ) );
            add_filter( 'query_vars', array( __CLASS__, 'add_login_slug_query_var' ) );
            add_action( 'template_redirect', array( __CLASS__, 'serve_custom_login_slug' ) );
            add_action( 'admin_init', array( __CLASS__, 'redirect_wp_admin_to_custom_login' ) );
            add_filter( 'login_url', array( __CLASS__, 'filter_login_url' ), 10, 3 );
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

        public static function register_login_slug_rewrite_rule() {
            $slug = self::get_custom_login_slug();
            if ( ! empty( $slug ) ) {
                add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '/?$', 'index.php?ventara_login_slug=1', 'top' );
            }
        }

        public static function add_login_slug_query_var( $vars ) {
            $vars[] = 'ventara_login_slug';
            return $vars;
        }

        public static function serve_custom_login_slug() {
            if ( absint( get_query_var( 'ventara_login_slug' ) ) !== 1 ) {
                return;
            }

            require_once ABSPATH . 'wp-login.php';
            exit;
        }

        public static function redirect_wp_admin_to_custom_login() {
            if ( is_user_logged_in() || wp_doing_ajax() || wp_doing_cron() ) {
                return;
            }

            $slug = self::get_custom_login_slug();
            if ( empty( $slug ) ) {
                return;
            }

            $request_path = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
            if ( ! $request_path ) {
                return;
            }

            $request_path = untrailingslashit( $request_path );
            if ( '/wp-admin' !== $request_path && '/wp-admin/' !== $request_path ) {
                return;
            }

            wp_safe_redirect( site_url( '/' . $slug . '/' ) );
            exit;
        }

        public static function filter_login_url( $login_url, $redirect, $force_reauth ) {
            $slug = self::get_custom_login_slug();
            if ( empty( $slug ) ) {
                return $login_url;
            }

            $login_url = site_url( '/' . $slug . '/' );
            if ( ! empty( $redirect ) ) {
                $login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $login_url );
            }

            if ( $force_reauth ) {
                $login_url = add_query_arg( 'reauth', '1', $login_url );
            }

            return $login_url;
        }

        public static function get_custom_login_slug() {
            return sanitize_title( get_option( 'ventara_login_slug', '' ) );
        }

        public static function flush_rewrite_rules() {
            register_shutdown_function( 'flush_rewrite_rules' );
        }
    }
}

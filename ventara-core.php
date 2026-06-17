<?php
/**
 * Plugin Name: Ventara Core
 * Plugin URI:  https://example.com/
 * Description: Ventara administraatori põhifunktsioonid.
 * Version:     1.0.0
 * Author:      Ventara
 * Author URI:  https://example.com/
 * Text Domain: ventara-core
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core' ) ) {

    final class Ventara_Core {

        const VERSION = '1.0.0';
        const MINIMUM_WP_VERSION = '5.0';

        private static $instance = null;

        private function __construct() {
            $this->define_constants();
            $this->load_dependencies();
            $this->init_hooks();
        }

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function define_constants() {
            define( 'VENTARA_CORE_VERSION', self::VERSION );
            define( 'VENTARA_CORE_PLUGIN_FILE', __FILE__ );
            define( 'VENTARA_CORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            define( 'VENTARA_CORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            define( 'VENTARA_CORE_INCLUDES', VENTARA_CORE_PLUGIN_DIR . 'includes/' );
            define( 'VENTARA_CORE_ASSETS_URL', VENTARA_CORE_PLUGIN_URL . 'assets/' );
            define( 'VENTARA_CORE_TEMPLATES', VENTARA_CORE_PLUGIN_DIR . 'templates/' );
        }

        private function load_dependencies() {
            require_once VENTARA_CORE_INCLUDES . 'class-ventara-core-i18n.php';
            require_once VENTARA_CORE_INCLUDES . 'class-ventara-core-admin.php';
            require_once VENTARA_CORE_INCLUDES . 'class-ventara-core-login.php';
            require_once VENTARA_CORE_INCLUDES . 'class-ventara-core-audit.php';
        }

        private function init_hooks() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            add_action( 'plugins_loaded', array( 'Ventara_Core_I18n', 'init' ) );
            add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
            add_action( 'admin_init', array( 'Ventara_Core_Admin', 'register_settings' ) );
            add_action( 'init', array( 'Ventara_Core_Login', 'init' ) );
        }

        public static function activate() {
            Ventara_Core_Login::register_login_slug_rewrite_rule();
            flush_rewrite_rules();
        }

        public function load_textdomain() {
            load_plugin_textdomain( 'ventara-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        public function register_admin_menu() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            add_menu_page(
                esc_html__( 'Ventara', 'ventara-core' ),
                esc_html__( 'Ventara', 'ventara-core' ),
                'manage_options',
                'ventara-core-dashboard',
                array( 'Ventara_Core_Admin', 'render_dashboard_page' ),
                'dashicons-admin-generic',
                2
            );

            add_submenu_page(
                'ventara-core-dashboard',
                Ventara_Core_I18n::t( 'audit' ),
                Ventara_Core_I18n::t( 'audit' ),
                'manage_options',
                'ventara-core-audit',
                array( 'Ventara_Core_Audit', 'render_audit_page' )
            );

            add_submenu_page(
                'ventara-core-dashboard',
                Ventara_Core_I18n::t( 'settings' ),
                Ventara_Core_I18n::t( 'settings' ),
                'manage_options',
                'ventara-core-settings',
                array( 'Ventara_Core_Admin', 'render_settings_page' )
            );
        }
    }
}

register_activation_hook( __FILE__, array( 'Ventara_Core', 'activate' ) );

Ventara_Core::instance();

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core_Admin' ) ) {

    class Ventara_Core_Admin {

        public static function render_dashboard_page() {
            echo '<div class="wrap"><h1>Ventara Core Test</h1><p>Dashboard loaded.</p></div>';
        }

        public static function render_settings_page() {
            echo '<div class="wrap"><h1>Ventara Settings Test</h1><p>Settings loaded.</p></div>';
        }

        public static function render_audit_page() {
            echo '<div class="wrap"><h1>Ventara Audit Test</h1><p>Audit loaded.</p></div>';
        }

        public static function enqueue_assets() {
            // Debug mode: disabled.
        }

        public static function enqueue_settings_assets() {
            // Debug mode: disabled.
        }

        public static function register_settings() {
            // Debug mode: disabled.
        }
    }
}
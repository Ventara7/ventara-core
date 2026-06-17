<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core_Admin' ) ) {

    class Ventara_Core_Admin {

        public static function render_dashboard_page() {
            self::enqueue_assets();
            include VENTARA_CORE_TEMPLATES . 'dashboard.php';
        }

        public static function enqueue_assets() {
            wp_enqueue_style(
                'ventara-core-admin',
                VENTARA_CORE_ASSETS_URL . 'css/admin.css',
                array(),
                VENTARA_CORE_VERSION
            );

            wp_enqueue_script(
                'ventara-core-admin',
                VENTARA_CORE_ASSETS_URL . 'js/admin.js',
                array( 'jquery' ),
                VENTARA_CORE_VERSION,
                true
            );

            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }

            wp_enqueue_script(
                'ventara-core-login-logo',
                VENTARA_CORE_ASSETS_URL . 'js/admin-login-logo.js',
                array( 'jquery' ),
                VENTARA_CORE_VERSION,
                true
            );

            wp_localize_script(
                'ventara-core-login-logo',
                'ventaraLoginLogo',
                array(
                    'i18n' => array(
                        'title'  => esc_html__( 'Choose a login logo', 'ventara-core' ),
                        'button' => esc_html__( 'Use this logo', 'ventara-core' ),
                    ),
                )
            );
        }

        public static function register_settings() {
            register_setting(
                'ventara_core_options_group',
                'ventara_core_options',
                array( __CLASS__, 'sanitize_settings' )
            );

            register_setting(
                'ventara_core_options_group',
                'ventara_logo_mode',
                array(
                    'sanitize_callback' => array( __CLASS__, 'sanitize_logo_mode' ),
                    'default'           => 'automatic',
                    'type'              => 'string',
                )
            );

            register_setting(
                'ventara_core_options_group',
                'ventara_custom_logo',
                array(
                    'sanitize_callback' => array( __CLASS__, 'sanitize_custom_logo' ),
                    'default'           => '',
                    'type'              => 'string',
                )
            );

            add_settings_section(
                'ventara_core_general_section',
                esc_html__( 'Ventara Core Settings', 'ventara-core' ),
                array( __CLASS__, 'render_settings_section' ),
                'ventara_core_settings'
            );

            $fields = array(
                'client_name'         => esc_html__( 'Client name', 'ventara-core' ),
                'client_phone'        => esc_html__( 'Client phone', 'ventara-core' ),
                'client_email'        => esc_html__( 'Client email', 'ventara-core' ),
                'ventara_phone'       => esc_html__( 'Ventara phone', 'ventara-core' ),
                'ventara_email'       => esc_html__( 'Ventara email', 'ventara-core' ),
                'support_url'         => esc_html__( 'Support URL', 'ventara-core' ),
                'left_panel_logo_url' => esc_html__( 'Left panel logo URL', 'ventara-core' ),
            );

            foreach ( $fields as $field => $label ) {
                add_settings_field(
                    $field,
                    $label,
                    array( __CLASS__, 'render_text_field' ),
                    'ventara_core_settings',
                    'ventara_core_general_section',
                    array(
                        'id'    => $field,
                        'label' => $label,
                    )
                );
            }

            add_settings_field(
                'ventara_logo_mode',
                esc_html__( 'Login Logo Mode', 'ventara-core' ),
                array( __CLASS__, 'render_logo_mode_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'ventara_logo_mode',
                    'label' => esc_html__( 'Use the current website logo automatically, or upload a custom login logo.', 'ventara-core' ),
                )
            );

            add_settings_field(
                'ventara_custom_logo',
                esc_html__( 'Custom login logo', 'ventara-core' ),
                array( __CLASS__, 'render_custom_logo_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'ventara_custom_logo',
                    'label' => esc_html__( 'Select a custom logo for the login screen.', 'ventara-core' ),
                )
            );
        }

        public static function render_settings_section() {
            echo '<p>' . esc_html__( 'Configure Ventara Core settings for login branding and support contact information.', 'ventara-core' ) . '</p>';
        }

        public static function render_text_field( $args ) {
            $options = get_option( 'ventara_core_options', array() );
            $value   = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
            printf(
                '<input type="text" id="%1$s" name="ventara_core_options[%1$s]" value="%2$s" class="regular-text" />',
                esc_attr( $args['id'] ),
                esc_attr( $value )
            );
        }

        public static function render_logo_mode_field( $args ) {
            $value = get_option( 'ventara_logo_mode', 'automatic' );
            ?>
            <label>
                <input type="radio" name="ventara_logo_mode" value="automatic" <?php checked( $value, 'automatic' ); ?> />
                <?php esc_html_e( 'Automatic (use website logo)', 'ventara-core' ); ?>
            </label>
            <br />
            <label>
                <input type="radio" name="ventara_logo_mode" value="custom" <?php checked( $value, 'custom' ); ?> />
                <?php esc_html_e( 'Custom', 'ventara-core' ); ?>
            </label>
            <p class="description"><?php echo esc_html( $args['label'] ); ?></p>
            <?php
        }

        public static function render_custom_logo_field( $args ) {
            $value = get_option( 'ventara_custom_logo', '' );
            $logo_mode = get_option( 'ventara_logo_mode', 'automatic' );
            $image_url = '';
            $attachment_id = 0;

            if ( is_numeric( $value ) && intval( $value ) ) {
                $attachment_id = intval( $value );
                $image = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                if ( $image ) {
                    $image_url = $image[0];
                }
            } elseif ( ! empty( $value ) ) {
                $image_url = esc_url( $value );
            }
            ?>
            <div id="ventara_custom_logo_field" style="display: <?php echo 'custom' === $logo_mode ? 'block' : 'none'; ?>;">
                <input type="hidden" id="ventara_custom_logo" name="ventara_custom_logo" value="<?php echo esc_attr( $value ); ?>" />
                <div id="ventara_custom_logo_preview" style="margin-bottom: 8px;">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Custom login logo', 'ventara-core' ); ?>" style="max-width: 200px; height: auto; display: block; margin-bottom: 8px;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="ventara_custom_logo_button">
                    <?php esc_html_e( 'Select logo', 'ventara-core' ); ?>
                </button>
                <button type="button" class="button" id="ventara_custom_logo_remove_button" style="display: <?php echo $image_url ? 'inline-block' : 'none'; ?>; margin-left: 8px;">
                    <?php esc_html_e( 'Remove logo', 'ventara-core' ); ?>
                </button>
                <p class="description"><?php echo esc_html( $args['label'] ); ?></p>
            </div>
            <?php
        }

        public static function sanitize_settings( $input ) {
            $allowed_keys = array(
                'client_name',
                'client_phone',
                'client_email',
                'ventara_phone',
                'ventara_email',
                'support_url',
                'left_panel_logo_url',
            );

            $sanitized = array();

            foreach ( $allowed_keys as $key ) {
                if ( isset( $input[ $key ] ) ) {
                    switch ( $key ) {
                        case 'client_email':
                        case 'ventara_email':
                            $sanitized[ $key ] = sanitize_email( $input[ $key ] );
                            break;
                        case 'support_url':
                        case 'left_panel_logo_url':
                            $sanitized[ $key ] = esc_url_raw( $input[ $key ] );
                            break;
                        default:
                            $sanitized[ $key ] = sanitize_text_field( $input[ $key ] );
                            break;
                    }
                }
            }

            return $sanitized;
        }

        public static function sanitize_logo_mode( $value ) {
            $allowed = array( 'automatic', 'custom' );
            if ( in_array( $value, $allowed, true ) ) {
                return $value;
            }
            return 'automatic';
        }

        public static function sanitize_custom_logo( $value ) {
            if ( is_numeric( $value ) ) {
                return intval( $value );
            }
            return esc_url_raw( $value );
        }

        public static function render_settings_page() {
            self::enqueue_assets();
            include VENTARA_CORE_TEMPLATES . 'settings.php';
        }
    }
}

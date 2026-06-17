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

            wp_enqueue_style(
                'ventara-core-dashboard',
                VENTARA_CORE_ASSETS_URL . 'css/dashboard.css',
                array( 'ventara-core-admin' ),
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

            register_setting(
                'ventara_core_options_group',
                'ventara_login_slug',
                array(
                    'sanitize_callback' => array( __CLASS__, 'sanitize_login_slug' ),
                    'default'           => '',
                    'type'              => 'string',
                )
            );

            register_setting(
                'ventara_core_options_group',
                'ventara_plugin_language',
                array(
                    'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
                    'default'           => 'en',
                    'type'              => 'string',
                )
            );

            add_action( 'update_option_ventara_login_slug', array( __CLASS__, 'maybe_flush_rewrite_rules_on_slug_change' ), 10, 3 );

            add_settings_section(
                'ventara_core_general_section',
                Ventara_Core_I18n::t( 'login_branding' ),
                array( __CLASS__, 'render_settings_section' ),
                'ventara_core_settings'
            );

            add_settings_field(
                'business_name',
                Ventara_Core_I18n::t( 'business_name' ),
                array( __CLASS__, 'render_text_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'business_name',
                    'label' => Ventara_Core_I18n::t( 'business_name_label' ),
                )
            );

            add_settings_field(
                'ventara_logo_mode',
                Ventara_Core_I18n::t( 'login_logo_mode' ),
                array( __CLASS__, 'render_logo_mode_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'ventara_logo_mode',
                    'label' => Ventara_Core_I18n::t( 'logo_mode_label' ),
                )
            );

            add_settings_field(
                'ventara_custom_logo',
                Ventara_Core_I18n::t( 'custom_logo' ),
                array( __CLASS__, 'render_custom_logo_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'ventara_custom_logo',
                    'label' => Ventara_Core_I18n::t( 'custom_logo_label' ),
                )
            );

            add_settings_field(
                'ventara_login_slug',
                Ventara_Core_I18n::t( 'custom_login_url' ),
                array( __CLASS__, 'render_login_slug_field' ),
                'ventara_core_settings',
                'ventara_core_general_section',
                array(
                    'id'    => 'ventara_login_slug',
                    'label' => Ventara_Core_I18n::t( 'login_slug_label' ),
                )
            );

            add_settings_section(
                'ventara_core_language_section',
                'Plugin Settings',
                array( __CLASS__, 'render_language_section' ),
                'ventara_core_settings'
            );

            add_settings_field(
                'ventara_plugin_language',
                Ventara_Core_I18n::t( 'plugin_language' ),
                array( __CLASS__, 'render_language_field' ),
                'ventara_core_settings',
                'ventara_core_language_section',
                array(
                    'id'    => 'ventara_plugin_language',
                    'label' => Ventara_Core_I18n::t( 'plugin_language' ),
                )
            );
        }

        public static function render_settings_section() {
            echo '<p>' . esc_html( Ventara_Core_I18n::t( 'configure_branding' ) ) . '</p>';
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
                <?php esc_html_e( 'Automaatselt saidi logo', 'ventara-core' ); ?>
            </label>
            <br />
            <label>
                <input type="radio" name="ventara_logo_mode" value="custom" <?php checked( $value, 'custom' ); ?> />
                <?php esc_html_e( 'Kohandatud logo', 'ventara-core' ); ?>
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
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Kohandatud sisselogimise logo', 'ventara-core' ); ?>" style="max-width: 200px; height: auto; display: block; margin-bottom: 8px;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="ventara_custom_logo_button">
                    <?php esc_html_e( 'Vali logo', 'ventara-core' ); ?>
                </button>
                <button type="button" class="button" id="ventara_custom_logo_remove_button" style="display: <?php echo $image_url ? 'inline-block' : 'none'; ?>; margin-left: 8px;">
                    <?php esc_html_e( 'Eemalda logo', 'ventara-core' ); ?>
                </button>
                <p class="description"><?php echo esc_html( $args['label'] ); ?></p>
            </div>
            <?php
        }

        public static function sanitize_settings( $input ) {
            $allowed_keys = array(
                'business_name',
            );

            $sanitized = array();

            foreach ( $allowed_keys as $key ) {
                if ( isset( $input[ $key ] ) ) {
                    $sanitized[ $key ] = sanitize_text_field( $input[ $key ] );
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

        public static function sanitize_login_slug( $value ) {
            return sanitize_title( $value );
        }

        public static function render_login_slug_field( $args ) {
            $value = get_option( 'ventara_login_slug', '' );
            printf(
                '<input type="text" id="ventara_login_slug" name="ventara_login_slug" value="%1$s" class="regular-text" />',
                esc_attr( $value )
            );
            printf( '<p class="description">%s</p>', esc_html( $args['label'] ) );
        }

        public static function sanitize_language( $value ) {
            $valid_langs = array( 'en', 'et', 'lv', 'lt', 'fi' );
            if ( in_array( $value, $valid_langs, true ) ) {
                return $value;
            }
            return 'en';
        }

        public static function render_language_section() {
            echo '<p>' . esc_html__( 'Select the plugin interface language.', 'ventara-core' ) . '</p>';
        }

        public static function render_language_field( $args ) {
            $value = get_option( 'ventara_plugin_language', 'en' );
            $languages = Ventara_Core_I18n::get_available_languages();
            ?>
            <select id="ventara_plugin_language" name="ventara_plugin_language">
                <?php foreach ( $languages as $lang_code => $lang_name ) : ?>
                    <option value="<?php echo esc_attr( $lang_code ); ?>" <?php selected( $value, $lang_code ); ?>>
                        <?php echo esc_html( $lang_name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }

        public static function maybe_flush_rewrite_rules_on_slug_change( $old_value, $value, $option ) {
            if ( $old_value !== $value ) {
                Ventara_Core_Login::flush_rewrite_rules();
            }
        }

        public static function render_settings_page() {
            self::enqueue_assets();
            include VENTARA_CORE_TEMPLATES . 'settings.php';
        }

        public static function get_dashboard_data() {
            $theme = wp_get_theme();
            $timezone = get_option( 'timezone_string' );

            if ( empty( $timezone ) ) {
                $timezone_offset = get_option( 'gmt_offset' );
                $timezone = sprintf( 'UTC%+d', $timezone_offset );
            }

            $active_plugins = get_option( 'active_plugins', array() );
            if ( ! function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $all_plugins = get_plugins();
            $inactive_plugins = max( 0, count( $all_plugins ) - count( $active_plugins ) );

            $ssl_enabled = is_ssl();
            $search_visible = (bool) get_option( 'blog_public', 1 );
            $wordfence_active = self::has_active_plugin( $active_plugins, array( 'wordfence/wordfence.php', 'wordfence-waf.php' ) );
            $backup_active = self::has_backup_plugin( $active_plugins );
            $seo_active = self::has_active_plugin( $active_plugins, array( 'rank-math/rank-math.php', 'wordpress-seo/wp-seo.php', 'all-in-one-seo-pack/all_in_one_seo_pack.php', 'aioseo/aioseo.php', 'seo-by-rank-math/rank-math.php' ) );

            return array(
                array(
                    'label' => Ventara_Core_I18n::t( 'website_url' ),
                    'value' => sprintf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">%1$s</a>', esc_url( home_url() ) ),
                ),
                array(
                    'label' => Ventara_Core_I18n::t( 'wordpress_version' ),
                    'value' => esc_html( get_bloginfo( 'version' ) ),
                ),
                array(
                    'label' => Ventara_Core_I18n::t( 'php_version' ),
                    'value' => esc_html( PHP_VERSION ),
                ),
                array(
                    'label' => Ventara_Core_I18n::t( 'active_theme' ),
                    'value' => esc_html( $theme->get( 'Name' ) ) . ' ' . esc_html( $theme->get( 'Version' ) ),
                ),
                array(
                    'label' => Ventara_Core_I18n::t( 'active_plugins' ),
                    'value' => esc_html( number_format_i18n( count( $active_plugins ) ) ),
                ),
                array(
                    'label' => Ventara_Core_I18n::t( 'inactive_plugins' ),
                    'value' => esc_html( number_format_i18n( $inactive_plugins ) ),
                ),
                array(
                    'label' => esc_html__( 'Site Language', 'ventara-core' ),
                    'value' => esc_html( get_locale() ),
                ),
                array(
                    'label' => esc_html__( 'Site Timezone', 'ventara-core' ),
                    'value' => esc_html( $timezone ),
                ),
                array(
                    'label'  => Ventara_Core_I18n::t( 'ssl_status' ),
                    'value'  => esc_html( $ssl_enabled ? esc_html__( 'Enabled', 'ventara-core' ) : esc_html__( 'Disabled', 'ventara-core' ) ),
                    'status' => $ssl_enabled ? 'good' : 'bad',
                ),
                array(
                    'label'  => Ventara_Core_I18n::t( 'search_engine_visibility' ),
                    'value'  => esc_html( $search_visible ? esc_html__( 'Visible', 'ventara-core' ) : esc_html__( 'Discourage search engines', 'ventara-core' ) ),
                    'status' => $search_visible ? 'good' : 'warning',
                ),
                array(
                    'label'  => esc_html__( 'Wordfence’i olek', 'ventara-core' ),
                    'value'  => esc_html( $wordfence_active ? esc_html__( 'Active', 'ventara-core' ) : esc_html__( 'Inactive', 'ventara-core' ) ),
                    'status' => $wordfence_active ? 'good' : 'bad',
                ),
                array(
                    'label'  => esc_html__( 'Varunduspluginna olek', 'ventara-core' ),
                    'value'  => esc_html( $backup_active ? esc_html__( 'Active', 'ventara-core' ) : esc_html__( 'Inactive', 'ventara-core' ) ),
                    'status' => $backup_active ? 'good' : 'bad',
                ),
                array(
                    'label'  => esc_html__( 'SEO pluginna olek', 'ventara-core' ),
                    'value'  => esc_html( $seo_active ? esc_html__( 'Active', 'ventara-core' ) : esc_html__( 'Inactive', 'ventara-core' ) ),
                    'status' => $seo_active ? 'good' : 'warning',
                ),
            );
        }

        private static function has_active_plugin( $active_plugins, $plugin_files ) {
            foreach ( $active_plugins as $plugin ) {
                foreach ( $plugin_files as $file ) {
                    if ( false !== strpos( $plugin, $file ) ) {
                        return true;
                    }
                }
            }
            return false;
        }

        private static function has_backup_plugin( $active_plugins ) {
            $backup_slugs = array(
                'updraftplus/updraftplus.php',
                'backwpup/backwpup.php',
                'backupwordpress/backupwordpress.php',
                'duplicator/duplicator.php',
                'vaultpress/vaultpress.php',
                'backupbuddy/backupbuddy.php',
                'easywp-snapshots/easywp-snapshots.php',
            );

            foreach ( $active_plugins as $plugin ) {
                foreach ( $backup_slugs as $slug ) {
                    if ( false !== strpos( $plugin, $slug ) ) {
                        return true;
                    }
                }
            }

            if ( get_option( 'updraft_backup_time' ) || get_option( 'updraft_backup_db_time' ) || get_option( 'updraft_backup_log' ) ) {
                return true;
            }

            return false;
        }
    }
}

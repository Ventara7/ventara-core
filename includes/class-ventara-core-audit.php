<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core_Audit' ) ) {

    class Ventara_Core_Audit {

        public static function render_audit_page() {
            Ventara_Core_Admin::enqueue_assets();
            $audit_results = self::get_audit_results();
            include VENTARA_CORE_TEMPLATES . 'audit.php';
        }

        public static function get_audit_results() {
            $checks = array(
                self::get_wordpress_version_check(),
                self::get_php_version_check(),
                self::get_ssl_check(),
                self::get_search_visibility_check(),
                self::get_permalink_check(),
                self::get_wordfence_check(),
                self::get_seo_plugin_check(),
                self::get_backup_plugin_check(),
                self::get_debug_mode_check(),
                self::get_xmlrpc_check(),
                self::get_custom_logo_check(),
                self::get_site_title_check(),
                self::get_site_tagline_check(),
                self::get_admin_email_check(),
                self::get_timezone_check(),
            );

            $total_points   = 0;
            $earned_points  = 0;

            foreach ( $checks as $check ) {
                $total_points += $check['points'];
                if ( 'good' === $check['status'] ) {
                    $earned_points += $check['points'];
                } elseif ( 'warning' === $check['status'] ) {
                    $earned_points += round( $check['points'] / 2 );
                }
            }

            $score = 0;
            if ( $total_points > 0 ) {
                $score = round( ( $earned_points / $total_points ) * 100 );
            }

            $score_status = 'critical';
            $summary      = esc_html__( 'Sinu veebisaidil on mitu olulist täiustust saadaval.', 'ventara-core' );

            if ( $score >= 80 ) {
                $score_status = 'good';
                $summary      = Ventara_Core_I18n::t( 'good' );
            } elseif ( $score >= 50 ) {
                $score_status = 'warning';
                $summary      = Ventara_Core_I18n::t( 'warning' );
            }

            return array(
                'checks'        => $checks,
                'score'         => $score,
                'status'        => $score_status,
                'summary'       => $summary,
                'earned_points' => $earned_points,
                'total_points'  => $total_points,
            );
        }

        private static function get_active_plugins() {
            $active_plugins = (array) get_option( 'active_plugins', array() );
            $network_plugins = get_site_option( 'active_sitewide_plugins', array() );

            if ( is_array( $network_plugins ) ) {
                $active_plugins = array_merge( $active_plugins, array_keys( $network_plugins ) );
            }

            return array_map( 'strtolower', $active_plugins );
        }

        private static function has_active_plugin( $plugin_files ) {
            $active_plugins = self::get_active_plugins();
            foreach ( $plugin_files as $plugin_file ) {
                if ( in_array( strtolower( $plugin_file ), $active_plugins, true ) ) {
                    return true;
                }
            }
            return false;
        }

        private static function get_wordpress_version_check() {
            $update_core = get_site_transient( 'update_core' );
            $status      = 'warning';
            $description = esc_html__( 'WordPressi versiooni uuenduse olekut ei õnnestunud tuvastada.', 'ventara-core' );
            $recommendation = esc_html__( 'Mine värskenduste lehele ja kontrolli, et WordPress on ajakohane.', 'ventara-core' );

            if ( is_object( $update_core ) && isset( $update_core->updates ) ) {
                $has_update = false;
                foreach ( $update_core->updates as $update ) {
                    if ( isset( $update->response ) && 'latest' !== $update->response ) {
                        $has_update = true;
                        break;
                    }
                }

                if ( ! $has_update ) {
                    $status      = 'good';
                    $description = esc_html__( 'WordPress töötab viimase installitud uuenduste teabega.', 'ventara-core' );
                    $recommendation = esc_html__( 'Toimingut ei ole vaja, kui välja ei ilmne uus uuendus.', 'ventara-core' );
                } else {
                    $status      = 'critical';
                    $description = esc_html__( 'Saadaval on uuem WordPressi versioon.', 'ventara-core' );
                    $recommendation = esc_html__( 'Uuenda WordPressi tuuma niipea kui võimalik.', 'ventara-core' );
                }
            }

            return array(
                'status'         => $status,
                'title'          => esc_html__( 'WordPressi versioon', 'ventara-core' ),
                'description'    => $description,
                'recommendation' => $recommendation,
                'points'         => 10,
            );
        }

        private static function get_php_version_check() {
            $status = PHP_VERSION_ID >= 80100 ? 'good' : 'critical';
            return array(
                'status'         => $status,
                'title'          => esc_html__( 'PHP versioon', 'ventara-core' ),
                'description'    => PHP_VERSION_ID >= 80100 ? esc_html__( 'Server töötab PHP 8.1 või uuema versiooniga.', 'ventara-core' ) : esc_html__( 'The server PHP versioon is below the recommended minimum.', 'ventara-core' ),
                'recommendation' => PHP_VERSION_ID >= 80100 ? esc_html__( 'No action needed unless your host changes PHP versioons.', 'ventara-core' ) : esc_html__( 'Upgrade the server PHP versioon to 8.1 or higher.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_ssl_check() {
            $enabled = is_ssl();
            return array(
                'status'         => $enabled ? 'good' : 'critical',
                'title'          => esc_html__( 'SSL-i olek', 'ventara-core' ),
                'description'    => $enabled ? esc_html__( 'Veebisait kasutab HTTPS-i.', 'ventara-core' ) : esc_html__( 'Saidile ei pääse hetkel üle HTTPS-i.', 'ventara-core' ),
                'recommendation' => $enabled ? esc_html__( 'Toimingut ei ole vaja, kui HTTPS on juba sundkindel.', 'ventara-core' ) : esc_html__( 'Paigalda ja configureeri SSL-sertifikaat ning sundi HTTPS-i.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_search_visibility_check() {
            $visible = (bool) get_option( 'blog_public', 1 );
            return array(
                'status'         => $visible ? 'good' : 'warning',
                'title'          => esc_html__( 'Otsingumootori nähtavus', 'ventara-core' ),
                'description'    => $visible ? esc_html__( 'Otsingumootoritel on lubatud sait indekseerida.', 'ventara-core' ) : esc_html__( 'Otsingumootori nähtavus is disabled.', 'ventara-core' ),
                'recommendation' => $visible ? esc_html__( 'Toimingut ei ole vaja, välja arvatud juhul kui see on kavatsuslik.', 'ventara-core' ) : esc_html__( 'Luba otsingumootori nähtavus, kui sait peab olema leitav.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_permalink_check() {
            $permalink_structure = get_option( 'permalink_structure', '' );
            $enabled = ! empty( $permalink_structure );
            return array(
                'status'         => $enabled ? 'good' : 'warning',
                'title'          => esc_html__( 'Püsilingi struktuur', 'ventara-core' ),
                'description'    => $enabled ? esc_html__( 'Ilusa püsilingi seaded on lubatud.', 'ventara-core' ) : esc_html__( 'Sait kasutab lihtsaid püsilingi seadeid.', 'ventara-core' ),
                'recommendation' => $enabled ? esc_html__( 'Toimingut ei ole vaja, kui sa ei soovi kohandatud püsilingi struktuuri.', 'ventara-core' ) : esc_html__( 'Switch to a non-plain permalink structure in Seaded → Permalinks.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_wordfence_check() {
            $active = self::has_active_plugin( array( 'wordfence/wordfence.php', 'wordfence-waf.php' ) );
            return array(
                'status'         => $active ? 'good' : 'warning',
                'title'          => esc_html__( 'Wordfence’i olek', 'ventara-core' ),
                'description'    => $active ? esc_html__( 'Wordfence on installitud ja aktiivne.', 'ventara-core' ) : esc_html__( 'Wordfence ei ole hetkel aktiivne.', 'ventara-core' ),
                'recommendation' => $active ? esc_html__( 'Toimingut ei ole vaja, välja arvatud juhul kui valid teise turvapluginna.', 'ventara-core' ) : esc_html__( 'Paigalda ja aktiveeri Wordfence saidi kaitse parandamiseks.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_seo_plugin_check() {
            $active = self::has_active_plugin( array(
                'seo-by-rank-math/rank-math.php',
                'rank-math/rank-math.php',
                'wordpress-seo/wp-seo.php',
                'aioseo/aioseo.php',
            ) );
            return array(
                'status'         => $active ? 'good' : 'warning',
                'title'          => esc_html__( 'SEO pluginna olek', 'ventara-core' ),
                'description'    => $active ? esc_html__( 'Toetatud SEO pluginna on aktiivne.', 'ventara-core' ) : esc_html__( 'Ühtegi toetatud SEO pluginna ei ole aktiivne.', 'ventara-core' ),
                'recommendation' => $active ? esc_html__( 'Hooli, et SEO pluginna oleks ajakohane ja konfigureeritud.', 'ventara-core' ) : esc_html__( 'Paigalda Yoast SEO, Rank Math või AIOSEO parema optimeerimise jaoks.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_backup_plugin_check() {
            $active = self::has_active_plugin( array(
                'updraftplus/updraftplus.php',
                'wpvivid-backuprestore/wpvivid.php',
                'duplicator/duplicator.php',
                'blogvault/backup.php',
            ) );
            return array(
                'status'         => $active ? 'good' : 'warning',
                'title'          => esc_html__( 'Varunduspluginna olek', 'ventara-core' ),
                'description'    => $active ? esc_html__( 'Toetatud varunduspluginna on aktiivne.', 'ventara-core' ) : esc_html__( 'Ühtegi toetatud varunduspluginna ei ole aktiivne.', 'ventara-core' ),
                'recommendation' => $active ? esc_html__( 'Hoolitse regulaarsete varukoopiate eest ja kontrolli varunduse olekut.', 'ventara-core' ) : esc_html__( 'Paigalda UpdraftPlus, WPvivid, Duplicator või BlogVault saidi kaitsmiseks.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_debug_mode_check() {
            $debug_enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
            return array(
                'status'         => $debug_enabled ? 'critical' : 'good',
                'title'          => esc_html__( 'Silumisrežiim', 'ventara-core' ),
                'description'    => $debug_enabled ? esc_html__( 'WP_DEBUG on lubatud tootmissaidil.', 'ventara-core' ) : esc_html__( 'Silumisrežiim is disabled.', 'ventara-core' ),
                'recommendation' => $debug_enabled ? esc_html__( 'Keela WP_DEBUG tootmissaidil.', 'ventara-core' ) : esc_html__( 'Toimingut ei ole vaja, välja arvatud juhul kui silumine on ajutiselt vajalik.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_xmlrpc_check() {
            $xmlrpc_disabled = defined( 'DISABLE_XMLRPC' ) && DISABLE_XMLRPC;
            if ( ! $xmlrpc_disabled ) {
                $xmlrpc_disabled = ! get_option( 'xmlrpc_enabled', false );
            }
            return array(
                'status'         => $xmlrpc_disabled ? 'good' : 'critical',
                'title'          => esc_html__( 'XML-RPC kaitse', 'ventara-core' ),
                'description'    => $xmlrpc_disabled ? esc_html__( 'XML-RPC on keelatud või blokeeritud.', 'ventara-core' ) : esc_html__( 'XML-RPC on lubatud ja võib olla avatud.', 'ventara-core' ),
                'recommendation' => $xmlrpc_disabled ? esc_html__( 'Toimingut ei ole vaja, välja arvatud juhul kui XML-RPC on vajalik.', 'ventara-core' ) : esc_html__( 'Keela XML-RPC või kaitse seda turvapluginna abil.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_custom_logo_check() {
            $has_logo = function_exists( 'has_custom_logo' ) && has_custom_logo();
            return array(
                'status'         => $has_logo ? 'good' : 'warning',
                'title'          => esc_html__( 'Kohandatud logo', 'ventara-core' ),
                'description'    => $has_logo ? esc_html__( 'Saidi jaoks on konfigureeritud kohandatud logo.', 'ventara-core' ) : esc_html__( 'Saidil ei ole konfigureeritud kohandatud logo.', 'ventara-core' ),
                'recommendation' => $has_logo ? esc_html__( 'Kohandatud logo on aktiivne ja valmis kasutamiseks.', 'ventara-core' ) : esc_html__( 'Laadi kohandatud logo üles kohandajas ühtlase brändingu jaoks.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_site_title_check() {
            $title = trim( get_option( 'blogname', '' ) );
            $status = ! empty( $title ) ? 'good' : 'critical';
            return array(
                'status'         => $status,
                'title'          => esc_html__( 'Saidi pealkiri', 'ventara-core' ),
                'description'    => $status === 'good' ? esc_html__( 'Saidi pealkiri on määratud.', 'ventara-core' ) : esc_html__( 'Saidi pealkiri ei ole konfigureeritud.', 'ventara-core' ),
                'recommendation' => $status === 'good' ? esc_html__( 'No action needed.', 'ventara-core' ) : esc_html__( 'Set a site title under Seaded → General.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_site_tagline_check() {
            $tagline = trim( get_option( 'blogdescription', '' ) );
            $status = 'good';
            if ( empty( $tagline ) || 'Just another WordPress site' === $tagline ) {
                $status = 'critical';
            }
            return array(
                'status'         => $status,
                'title'          => esc_html__( 'Saidi alapealkiri', 'ventara-core' ),
                'description'    => $status === 'good' ? esc_html__( 'Alapealkiri on kohandatud.', 'ventara-core' ) : esc_html__( 'Alapealkiri on tühi või endiselt vaikimisi WordPressi tekst.', 'ventara-core' ),
                'recommendation' => $status === 'good' ? esc_html__( 'No action needed.' , 'ventara-core' ) : esc_html__( 'Update the site tagline in Seaded → General.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_admin_email_check() {
            $email = get_option( 'admin_email', '' );
            $status = is_email( $email ) ? 'good' : 'critical';
            return array(
                'status'         => $status,
                'title'          => esc_html__( 'Administraatori e-post', 'ventara-core' ),
                'description'    => $status === 'good' ? esc_html__( 'Administraatori e-post on konfigureeritud.', 'ventara-core' ) : esc_html__( 'Administraatori e-posti aadress ei ole määratud või on kehtetu.', 'ventara-core' ),
                'recommendation' => $status === 'good' ? esc_html__( 'No action needed.' , 'ventara-core' ) : esc_html__( 'Set a valid admin email in Seaded → General.', 'ventara-core' ),
                'points'         => 10,
            );
        }

        private static function get_timezone_check() {
            $timezone = get_option( 'timezone_string', '' );
            $offset = get_option( 'gmt_offset', '' );
            $configured = ! empty( $timezone ) || ( '' !== $offset && 0 !== floatval( $offset ) );
            return array(
                'status'         => $configured ? 'good' : 'warning',
                'title'          => esc_html__( 'Ajavööndi seadistus', 'ventara-core' ),
                'description'    => $configured ? esc_html__( 'Saidi ajavöönd on konfigureeritud.', 'ventara-core' ) : esc_html__( 'Ajavööndi sätted ei ole täielikult konfigureeritud.', 'ventara-core' ),
                'recommendation' => $configured ? esc_html__( 'No action needed.' , 'ventara-core' ) : esc_html__( 'Set a timezone in Seaded → General.', 'ventara-core' ),
                'points'         => 10,
            );
        }
    }
}

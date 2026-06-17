<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Ventara_Core_I18n' ) ) {

    class Ventara_Core_I18n {

        private static $current_language = null;
        private static $translations = array();

        public static function init() {
            self::load_translations();
            self::set_current_language();
        }

        public static function get_current_language() {
            if ( null === self::$current_language ) {
                self::set_current_language();
            }
            return self::$current_language;
        }

        private static function set_current_language() {
            self::$current_language = get_option( 'ventara_plugin_language', 'en' );
            if ( ! self::is_valid_language( self::$current_language ) ) {
                self::$current_language = 'en';
            }
        }

        private static function is_valid_language( $lang ) {
            $valid_langs = array( 'en', 'et', 'lv', 'lt', 'fi' );
            return in_array( $lang, $valid_langs, true );
        }

        private static function load_translations() {
            self::$translations = array(
                'en' => array(
                    'dashboard'              => 'Dashboard',
                    'settings'               => 'Settings',
                    'audit'                  => 'Audit',
                    'website_health'         => 'Website Health',
                    'overall_score'          => 'Overall Audit Score',
                    'run_audit_again'        => 'Run audit again',
                    'good'                   => 'Good',
                    'warning'                => 'Warning',
                    'critical'               => 'Critical',
                    'recommendation'         => 'Recommendation:',
                    'website_url'            => 'Website URL',
                    'wordpress_version'      => 'WordPress Version',
                    'php_version'            => 'PHP Version',
                    'active_theme'           => 'Active Theme',
                    'active_plugins'         => 'Active Plugins Count',
                    'inactive_plugins'       => 'Inactive Plugins Count',
                    'ssl_status'             => 'SSL status',
                    'search_engine_visibility' => 'Search engine visibility',
                    'login_branding'         => 'Login Branding',
                    'login_logo_mode'        => 'Login Logo Mode',
                    'automatic_logo'         => 'Automatic website logo',
                    'custom_logo'            => 'Custom logo',
                    'business_name'          => 'Business name',
                    'custom_login_url'       => 'Custom Login URL',
                    'save_settings'          => 'Save Settings',
                    'plugin_language'        => 'Plugin Language',
                    'configure_branding'     => 'Configure client-specific login branding for Ventara.',
                    'business_name_label'    => 'Business name shown in the login intro text.',
                    'logo_mode_label'        => 'Use the website logo automatically or choose a custom login logo.',
                    'custom_logo_label'      => 'Select a custom logo for the login screen.',
                    'login_slug_label'       => 'Leave empty to use the default WordPress login URL. Enter only a slug, for example: dashboard.',
                ),
                'et' => array(
                    'dashboard'              => 'Juhtpaneel',
                    'settings'               => 'Seaded',
                    'audit'                  => 'Audit',
                    'website_health'         => 'Veebisaidi tervis',
                    'overall_score'          => 'Auditipunktide kokkuvõte',
                    'run_audit_again'        => 'Käivita audit uuesti',
                    'good'                   => 'Hea',
                    'warning'                => 'Hoiatus',
                    'critical'               => 'Kriitiline',
                    'recommendation'         => 'Soovitus:',
                    'website_url'            => 'Veebisaidi URL',
                    'wordpress_version'      => 'WordPressi versioon',
                    'php_version'            => 'PHP versioon',
                    'active_theme'           => 'Aktiivne teema',
                    'active_plugins'         => 'Aktiivse pluginna arv',
                    'inactive_plugins'       => 'Mitteaktiivse pluginna arv',
                    'ssl_status'             => 'SSL olek',
                    'search_engine_visibility' => 'Otsingumootori nähtavus',
                    'login_branding'         => 'Sisselogimise bränding',
                    'login_logo_mode'        => 'Sisselogimise logo režiim',
                    'automatic_logo'         => 'Automaatselt saidi logo',
                    'custom_logo'            => 'Kohandatud logo',
                    'business_name'          => 'Ettevõtte nimi',
                    'custom_login_url'       => 'Kohandatud sisselogimise URL',
                    'save_settings'          => 'Salvesta seaded',
                    'plugin_language'        => 'Pluginna keel',
                    'configure_branding'     => 'Määra Ventara kliendipõhised sisselogimise brändiseaded.',
                    'business_name_label'    => 'Ettevõtte nimi, mida kuvatakse sisselogimise teates.',
                    'logo_mode_label'        => 'Kasuta veebisaidi logo automaatselt või vali kohandatud sisselogimise logo.',
                    'custom_logo_label'      => 'Vali sisselogimise ekraani jaoks kohandatud logo.',
                    'login_slug_label'       => 'Jäta tühjaks, et kasutada vaikimisi WordPressi sisselogimise URL-i. Sisesta ainult lühike osa, näiteks dashboard.',
                ),
                'lv' => array(
                    'dashboard'              => 'Informācijas panelis',
                    'settings'               => 'Iestatījumi',
                    'audit'                  => 'Revīzija',
                    'website_health'         => 'Vietnes veselība',
                    'overall_score'          => 'Kopējais audita rezultāts',
                    'run_audit_again'        => 'Palaist auditu vēlreiz',
                    'good'                   => 'Labi',
                    'warning'                => 'Brīdinājums',
                    'critical'               => 'Kritisks',
                    'recommendation'         => 'Ieteikums:',
                    'website_url'            => 'Vietnes URL',
                    'wordpress_version'      => 'WordPress versija',
                    'php_version'            => 'PHP versija',
                    'active_theme'           => 'Aktīvā tēma',
                    'active_plugins'         => 'Aktīvo spraudņu skaits',
                    'inactive_plugins'       => 'Neaktīvo spraudņu skaits',
                    'ssl_status'             => 'SSL statuss',
                    'search_engine_visibility' => 'Meklēšanas dzinēja redzamība',
                    'login_branding'         => 'Pieslēgšanās zīmolvedība',
                    'login_logo_mode'        => 'Pieslēgšanās logotipa režīms',
                    'automatic_logo'         => 'Automātisks vietnes logotips',
                    'custom_logo'            => 'Pielāgots logotips',
                    'business_name'          => 'Uzņēmuma nosaukums',
                    'custom_login_url'       => 'Pielāgots pieslēgšanās URL',
                    'save_settings'          => 'Saglabāt iestatījumus',
                    'plugin_language'        => 'Spraudņa valoda',
                    'configure_branding'     => 'Konfigurējiet klientam raksturīgu pieslēgšanās zīmolvedību Ventarai.',
                    'business_name_label'    => 'Uzņēmuma nosaukums, kas parādīts pieslēgšanās ievada tekstā.',
                    'logo_mode_label'        => 'Izmantojiet vietnes logotipu automātiski vai atlasiet pielāgotu pieslēgšanās logotipu.',
                    'custom_logo_label'      => 'Atlasiet pieslēgšanās ekrānam pielāgotu logotipu.',
                    'login_slug_label'       => 'Atstājiet tukšu, lai izmantotu noklusējuma WordPress pieslēgšanās URL. Ievadiet tikai fragmentu, piemēram: dashboard.',
                ),
                'lt' => array(
                    'dashboard'              => 'Instrumentų skydelis',
                    'settings'               => 'Nustatymai',
                    'audit'                  => 'Auditas',
                    'website_health'         => 'Svetainės sveikata',
                    'overall_score'          => 'Bendrasis audito rezultatas',
                    'run_audit_again'        => 'Vėl paleisti auditą',
                    'good'                   => 'Gerai',
                    'warning'                => 'Įspėjimas',
                    'critical'               => 'Kritinis',
                    'recommendation'         => 'Rekomendacija:',
                    'website_url'            => 'Svetainės URL',
                    'wordpress_version'      => 'WordPress versija',
                    'php_version'            => 'PHP versija',
                    'active_theme'           => 'Aktyvi tema',
                    'active_plugins'         => 'Aktyvių papildinių skaičius',
                    'inactive_plugins'       => 'Neaktyvių papildinių skaičius',
                    'ssl_status'             => 'SSL būsena',
                    'search_engine_visibility' => 'Paieškos variklio matomumas',
                    'login_branding'         => 'Prisijungimo ženklinimas',
                    'login_logo_mode'        => 'Prisijungimo logotipo režimas',
                    'automatic_logo'         => 'Automatinis svetainės logotipas',
                    'custom_logo'            => 'Pasirinktinis logotipas',
                    'business_name'          => 'Verslo pavadinimas',
                    'custom_login_url'       => 'Pasirinktinis prisijungimo URL',
                    'save_settings'          => 'Išsaugoti nustatymus',
                    'plugin_language'        => 'Papildinio kalba',
                    'configure_branding'     => 'Sukonfigūruokite konkretaus kliento prisijungimo ženklinimą "Ventara".',
                    'business_name_label'    => 'Verslo pavadinimas, rodomas prisijungimo įvado tekste.',
                    'logo_mode_label'        => 'Naudokite svetainės logotipą automatiškai arba pasirinkite pasirinktinį prisijungimo logotipą.',
                    'custom_logo_label'      => 'Pasirinkite pasirinktinį prisijungimo ekrano logotipą.',
                    'login_slug_label'       => 'Palikite tuščią, kad naudotumėte numatytą WordPress prisijungimo URL. Įveskite tik fragmentą, pvz.: dashboard.',
                ),
                'fi' => array(
                    'dashboard'              => 'Kojelauta',
                    'settings'               => 'Asetukset',
                    'audit'                  => 'Tarkastus',
                    'website_health'         => 'Verkkosivuston terveys',
                    'overall_score'          => 'Kokonaisarvosana',
                    'run_audit_again'        => 'Suorita tarkastus uudelleen',
                    'good'                   => 'Hyvä',
                    'warning'                => 'Varoitus',
                    'critical'               => 'Kriittinen',
                    'recommendation'         => 'Suositus:',
                    'website_url'            => 'Verkkosivuston URL-osoite',
                    'wordpress_version'      => 'WordPress-versio',
                    'php_version'            => 'PHP-versio',
                    'active_theme'           => 'Aktiivinen teema',
                    'active_plugins'         => 'Aktiivisten liitännäisten määrä',
                    'inactive_plugins'       => 'Passiivisten liitännäisten määrä',
                    'ssl_status'             => 'SSL-tila',
                    'search_engine_visibility' => 'Hakukoneen näkyvyys',
                    'login_branding'         => 'Sisäänkirjautumisen brändäys',
                    'login_logo_mode'        => 'Sisäänkirjautumisen logon tila',
                    'automatic_logo'         => 'Automaattinen verkkosivuston logo',
                    'custom_logo'            => 'Mukautettu logo',
                    'business_name'          => 'Yrityksen nimi',
                    'custom_login_url'       => 'Mukautettu sisäänkirjautumisen URL-osoite',
                    'save_settings'          => 'Tallenna asetukset',
                    'plugin_language'        => 'Liitännäisen kieli',
                    'configure_branding'     => 'Määritä Ventara-asiakaskohtainen sisäänkirjautumisen brändäys.',
                    'business_name_label'    => 'Yrityksen nimi, joka näkyy sisäänkirjautumisen esittelytekstissä.',
                    'logo_mode_label'        => 'Käytä verkkosivuston logoa automaattisesti tai valitse mukautettu sisäänkirjautumisen logo.',
                    'custom_logo_label'      => 'Valitse mukautettu logo sisäänkirjautumisruutuun.',
                    'login_slug_label'       => 'Jätä tyhjäksi käyttääksesi WordPressin oletussisäänkirjautumisen URL-osoitetta. Kirjoita vain fragmentti, esimerkiksi: dashboard.',
                ),
            );
        }

        public static function t( $key, $fallback = '' ) {
            $language = self::get_current_language();
            
            if ( isset( self::$translations[ $language ][ $key ] ) ) {
                return self::$translations[ $language ][ $key ];
            }

            // Fallback to English
            if ( isset( self::$translations['en'][ $key ] ) ) {
                return self::$translations['en'][ $key ];
            }

            // If no translation found, return fallback or key
            return ! empty( $fallback ) ? $fallback : $key;
        }

        public static function get_available_languages() {
            return array(
                'en' => 'English',
                'et' => 'Eesti',
                'lv' => 'Latvian',
                'lt' => 'Lietuvių',
                'fi' => 'Suomi',
            );
        }
    }
}

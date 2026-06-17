<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$options = get_option( 'ventara_core_options', array() );
?>
<div class="wrap ventara-core-settings">
    <h1><?php echo esc_html( Ventara_Core_I18n::t( 'settings' ) ); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'ventara_core_options_group' );
        do_settings_sections( 'ventara_core_settings' );
        submit_button();
        ?>
    </form>
</div>

<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$dashboard_items = Ventara_Core_Admin::get_dashboard_data();
?>
<div class="wrap ventara-core-dashboard">
    <h1><?php echo esc_html( Ventara_Core_I18n::t( 'dashboard' ) ); ?></h1>
    <p><?php esc_html_e( 'Praeguse saidi keskkonna ja aktiivse WordPressi konfiguratsiooni ülevaade.', 'ventara-core' ); ?></p>

    <div class="ventara-core-dashboard-cards">
        <?php foreach ( $dashboard_items as $item ) : ?>
            <div class="ventara-core-dashboard-card">
                <div class="ventara-core-dashboard-card-header">
                    <h2><?php echo wp_kses_post( $item['label'] ); ?></h2>
                    <?php if ( isset( $item['status'] ) ) : ?>
                        <span class="ventara-core-status-dot ventara-core-status--<?php echo esc_attr( $item['status'] ); ?>"></span>
                    <?php endif; ?>
                </div>
                <p><?php echo wp_kses_post( $item['value'] ); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="ventara-core-dashboard-summary">
        <p><?php esc_html_e( 'See juhtpaneel kasutab WordPressi halduri stiili ja kuvab olulisi saidi üksikasju vastavas kaardipaigutuses.', 'ventara-core' ); ?></p>
    </div>
</div>

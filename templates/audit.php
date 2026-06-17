<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$score = isset( $audit_results['score'] ) ? intval( $audit_results['score'] ) : 0;
$status = isset( $audit_results['status'] ) ? $audit_results['status'] : 'critical';
$summary = isset( $audit_results['summary'] ) ? $audit_results['summary'] : '';
$checks = isset( $audit_results['checks'] ) ? $audit_results['checks'] : array();
?>
<div class="wrap ventara-core-audit">
    <h1><?php echo esc_html( Ventara_Core_I18n::t( 'audit' ) ); ?></h1>

    <div class="ventara-audit-summary-card ventara-audit-status-<?php echo esc_attr( $status ); ?>">
        <div class="ventara-audit-score">
            <span class="ventara-audit-score-value"><?php echo esc_html( $score ); ?></span>
            <span class="ventara-audit-score-label"><?php esc_html_e( 'Auditipunktide kokkuvõte', 'ventara-core' ); ?></span>
        </div>
        <div class="ventara-audit-summary-text">
            <p class="ventara-audit-summary-status"><?php echo esc_html( ucfirst( $status ) ); ?></p>
            <p class="ventara-audit-summary-description"><?php echo esc_html( $summary ); ?></p>
        </div>
        <div class="ventara-audit-actions">
            <a href="<?php echo esc_url( remove_query_arg( 'audit_rerun' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Käivita audit uuesti', 'ventara-core' ); ?></a>
        </div>
    </div>

    <div class="ventara-audit-grid">
        <?php foreach ( $checks as $check ) : ?>
            <div class="ventara-audit-card ventara-audit-card-<?php echo esc_attr( $check['status'] ); ?>">
                <div class="ventara-audit-card-header">
                    <span class="ventara-audit-status-icon ventara-audit-status-icon-<?php echo esc_attr( $check['status'] ); ?>"></span>
                    <h2><?php echo esc_html( $check['title'] ); ?></h2>
                </div>
                <p class="ventara-audit-card-description"><?php echo esc_html( $check['description'] ); ?></p>
                <p class="ventara-audit-card-recommendation"><strong><?php esc_html_e( 'Soovitus:', 'ventara-core' ); ?></strong> <?php echo esc_html( $check['recommendation'] ); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

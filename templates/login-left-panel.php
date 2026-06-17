<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="ventara-login-left">
    <div class="ventara-login-left__inner">
        <div class="ventara-login-left__label"><?php esc_html_e( 'Kodulehe tegi ja hooldab:', 'ventara-core' ); ?></div>

        <div class="ventara-login-left__logo">
            <img src="https://ventara.ee/wp-content/uploads/2026/03/ventara_2-valge.png" alt="Ventara logo">
        </div>

        <div class="ventara-login-left__text">
            <?php esc_html_e( 'See veebileht ei alga disainist, vaid selle külastaja mõistmisest. See on loodud nii, et seda oleks mugav kasutada.', 'ventara-core' ); ?>
        </div>

        <div class="ventara-login-left__spacer"></div>

        <div class="ventara-login-left__support">
            <div class="ventara-login-left__support-title"><?php esc_html_e( 'Lisainfo & klienditugi', 'ventara-core' ); ?></div>

            <div class="ventara-login-left__support-item">
                <span class="ventara-login-left__support-icon">☎</span>
                <span><?php esc_html_e( 'Tel:', 'ventara-core' ); ?> <a href="tel:+37253856265">+372 5385 6265</a></span>
            </div>

            <div class="ventara-login-left__support-item">
                <span class="ventara-login-left__support-icon">✉</span>
                <span>E-post: <a href="mailto:info@buller.ee">info@buller.ee</a></span>
            </div>
        </div>
    </div>
</div>

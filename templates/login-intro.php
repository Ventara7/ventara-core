<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="ventara-login-site-logo">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php
        $logo_mode   = get_option( 'ventara_logo_mode', 'automatic' );
        $custom_logo = get_option( 'ventara_custom_logo', '' );
        $logo_url    = '';

        if ( 'custom' === $logo_mode && ! empty( $custom_logo ) ) {
            if ( is_numeric( $custom_logo ) ) {
                $custom_image = wp_get_attachment_image_src( intval( $custom_logo ), 'full' );
                if ( $custom_image ) {
                    $logo_url = $custom_image[0];
                }
            } else {
                $logo_url = esc_url( $custom_logo );
            }
        }

        if ( empty( $logo_url ) && 'automatic' === $logo_mode ) {
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            if ( $custom_logo_id ) {
                $custom_logo_src = wp_get_attachment_image_src( $custom_logo_id, 'full' );
                if ( $custom_logo_src ) {
                    $logo_url = $custom_logo_src[0];
                }
            }
        }

        if ( ! empty( $logo_url ) ) :
            ?>
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            <?php
        else :
            ?>
            <span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
            <?php
        endif;
        ?>
    </a>
</div>

<?php
$branding_options = get_option( 'ventara_core_options', array() );
$business_name = isset( $branding_options['business_name'] ) ? sanitize_text_field( $branding_options['business_name'] ) : '';
$display_name = $business_name ? $business_name : get_bloginfo( 'name' );
?>
<div class="ventara-login-info">
    <div class="ventara-login-info__message">
        <?php printf(
            esc_html__( 'Oled hetkel sisse logimas kodulehe %s admin keskkonda.', 'ventara-core' ),
            '<span class="website_name">' . esc_html( $display_name ) . '</span>'
        ); ?>
    </div>

    <div class="ventara-login-info__warning">
        <?php esc_html_e( 'Soovitame enne muudatuste tegemist teha varukoopia või veenduda, et sinu tegevus ei muuda veebilehe sisu, väljanägemist ega tehnilist toimimist.', 'ventara-core' ); ?>
    </div>
</div>

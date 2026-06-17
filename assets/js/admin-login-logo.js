( function( $ ) {
    'use strict';

    var frame;

    $( document ).ready( function() {
        // Safely initialize media uploader - only on Settings page with form elements
        if ( typeof wp === 'undefined' || typeof wp.media === 'undefined' ) {
            return;
        }

        if ( typeof ventaraLoginLogo === 'undefined' ) {
            return;
        }

        var $button = $( '#ventara_custom_logo_button' );
        var $remove = $( '#ventara_custom_logo_remove_button' );
        var $input = $( '#ventara_custom_logo' );
        var $preview = $( '#ventara_custom_logo_preview' );

        // Exit if elements don't exist
        if ( ! $button.length ) {
            return;
        }

        $button.on( 'click', function( event ) {
            event.preventDefault();

            if ( frame ) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: ventaraLoginLogo.i18n.title,
                button: {
                    text: ventaraLoginLogo.i18n.button,
                },
                library: {
                    type: 'image',
                },
                multiple: false,
            });

            frame.on( 'select', function() {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                if ( $input.length ) {
                    $input.val( attachment.id );
                }
                if ( $preview.length ) {
                    $preview.html( '<img src="' + attachment.url + '" style="max-width:200px;height:auto;display:block;margin-bottom:8px;" />' );
                }
                if ( $remove.length ) {
                    $remove.show();
                }
            });

            frame.open();
        });

        function toggleCustomLogoField() {
            var logoMode = $( 'input[name="ventara_logo_mode"]:checked' ).val();
            var $field = $( '#ventara_custom_logo_field' );
            if ( ! $field.length ) {
                return;
            }
            if ( logoMode === 'custom' ) {
                $field.show();
            } else {
                $field.hide();
            }
        }

        var $modeInputs = $( 'input[name="ventara_logo_mode"]' );
        if ( $modeInputs.length ) {
            $modeInputs.on( 'change', function() {
                toggleCustomLogoField();
            });
            toggleCustomLogoField();
        }

        if ( $remove.length ) {
            $remove.on( 'click', function( event ) {
                event.preventDefault();
                if ( $input.length ) {
                    $input.val( '' );
                }
                if ( $preview.length ) {
                    $preview.empty();
                }
                $remove.hide();
            });
        }
    });
} )( jQuery );

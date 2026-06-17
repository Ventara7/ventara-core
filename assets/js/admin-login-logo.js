( function( $ ) {
    'use strict';

    var frame;

    $( document ).ready( function() {
        var $button = $( '#ventara_custom_logo_button' );
        var $remove = $( '#ventara_custom_logo_remove_button' );
        var $input = $( '#ventara_custom_logo' );
        var $preview = $( '#ventara_custom_logo_preview' );

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
                $input.val( attachment.id );
                $preview.html( '<img src="' + attachment.url + '" style="max-width:200px;height:auto;display:block;margin-bottom:8px;" />' );
                $remove.show();
            });

            frame.open();
        });

        function toggleCustomLogoField() {
            var logoMode = $( 'input[name="ventara_logo_mode"]:checked' ).val();
            if ( logoMode === 'custom' ) {
                $( '#ventara_custom_logo_field' ).show();
            } else {
                $( '#ventara_custom_logo_field' ).hide();
            }
        }

        $( 'input[name="ventara_logo_mode"]' ).on( 'change', function() {
            toggleCustomLogoField();
        });

        toggleCustomLogoField();

        $remove.on( 'click', function( event ) {
            event.preventDefault();
            $input.val( '' );
            $preview.empty();
            $remove.hide();
        });
    });
} )( jQuery );

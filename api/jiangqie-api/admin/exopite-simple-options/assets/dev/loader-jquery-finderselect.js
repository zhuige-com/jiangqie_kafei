;(function ( $, window, document, undefined ) {

    /*
     * A jQuery Plugin Boilerplate
     *
     * https://github.com/johndugan/jquery-plugin-boilerplate/blob/master/jquery.plugin-boilerplate.js
     * https://john-dugan.com/jquery-plugin-boilerplate-explained/
     */

    var pluginName = "exopiteAttachmentRemover";

    // The actual plugin constructor
    function Plugin( element, options ) {

        this.element = element;
        this._name = pluginName;
        this.$element = $( element );
        this.init();

    }

    Plugin.prototype = {

        init: function() {

            this.bindEvents();

        },

       // Bind events that trigger methods
        bindEvents: function() {
            var plugin = this;

            plugin.$element.find( '.exopite-sof-attachment-media-js' ).on( 'click'+'.'+plugin._name, function( event ) {

                plugin.toggleSelect.call( plugin, $( this ), event );

            });

        },

        toggleSelect: function( $this, event ) {

            if ( event.ctrlKey ) {

                $this.toggleClass( 'selected' );

            } else {

                var already_selected = $this.hasClass( 'selected' );
                this.$element.find( '.exopite-sof-attachment-media-js' ).removeClass( 'selected' );

                if ( ! already_selected ) {

                    $this.addClass( 'selected' );

                }

            }

        },

    };

    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

    $( document ).ready(function() {

        // $( '.exopite-sof-attachment-container' ).exopiteAttachmentRemover();
        var finderSelect = $( '.exopite-sof-attachment-container' ).finderSelect({children:".exopite-sof-attachment-media-js"});

        finderSelect.on( "mousedown",".exopite-sof-attachment-media-delete-js", function(e){
            e.stopPropagation();
        });

        $( '.exopite-sof-attachment-media-delete-js' ).on('click', function(event) {

            var $attachmentContainer = $( this ).closest( '.exopite-sof-attachment-container' );
            var ajaxUrl = $attachmentContainer.data('ajaxurl');
            var attachmentIDs = new Array();

            $attachmentContainer.find( '.selected' ).each(function(index, el) {
                attachmentIDs.push( $( el ).data( 'media-id' ) );
            });

            var confirmDelete = confirm( "Are you sure, you want to delete the selected " + attachmentIDs.length + " media?" );
            if ( confirmDelete == true ) {

                var dataJSON = {
                    'action': 'exopite-sof-file-batch-delete',
                    'media-ids': attachmentIDs
                };

                $.ajax({
                    cache: false,
                    type: "POST",
                    url: ajaxUrl,
                    data: dataJSON,
                    success: function( response ){
                        var ids = jQuery.parseJSON( response );
                        $.each( ids, function(index, item) {
                            $attachmentContainer.find("[data-media-id='" + item + "']").remove();
                        });
                    },
                    error: function( xhr, status, error ) {
                        console.log( 'Status: ' + xhr.status );
                        console.log( 'Error: ' + xhr.responseText );
                    }
                });

            }

        });

    });

})( jQuery, window, document );

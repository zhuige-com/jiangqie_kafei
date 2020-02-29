;(function ( $, window, document, undefined ) {

    /*
     * A jQuery Plugin Boilerplate
     *
     * https://github.com/johndugan/jquery-plugin-boilerplate/blob/master/jquery.plugin-boilerplate.js
     * https://john-dugan.com/jquery-plugin-boilerplate-explained/
     */

    var pluginName = "exopiteFineUploader";

    // The actual plugin constructor
    function Plugin( element, options ) {

        this.element = element;
        this._name = pluginName;
        this.$element = $( element );
        this.init();

    }

    Plugin.prototype = {

        init: function() {

            // console.log( 'maxsize: ' + this.$element.data('maxsize') );
            // console.log( 'allowedExtensions: ' + this.$element.data('mimetypes') );

            var ajaxUrl = this.$element.data('ajaxurl');

            this.$element.fineUploader({
                template: 'qq-template-manual-trigger',
                request: {
                    endpoint: ajaxUrl,
                    // Admin AJAX Param
                    params: {
                        action: 'exopite-sof-file_uploader',
                        postId: this.$element.data('postid')
                    },
                    paramsInBody: true
                },
                deleteFile: {
                    /**
                     * Delete file on AJAX request with qquuid
                     *
                     * @link https://docs.fineuploader.com/features/delete.html
                     * @link https://docs.fineuploader.com/branch/master/api/options.html#deleteFile
                     */
                    method: 'POST',
                    endpoint: ajaxUrl,
                    params: {
                        action: 'exopite-sof-file_uploader',
                    },
                    enabled             : this.$element.data('delete-enabled'),
                    forceConfirm        : this.$element.data('delete-force-confirm')
                },
                retry: {
                    enableAuto          : this.$element.data('retry-enable-auto'),
                    maxAutoAttempts     : this.$element.data('retry-max-auto-attempts'),
                    autoAttemptDelay    : this.$element.data('retry-auto-attempt-delay'),
                },
                validation: {
                    allowedExtensions: this.$element.data('mimetypes').split(','),
                    sizeLimit: this.$element.data('maxsize'),
                    itemLimit: this.$element.data('filecount')
                },
                autoUpload: this.$element.data('auto-upload'),
                debug: true
            });

            this.bindEvents();

        },

       // Bind events that trigger methods
        bindEvents: function() {
            var plugin = this;

            plugin.$element.find( '.trigger-upload' ).on( 'click'+'.'+plugin._name, function() {

                plugin.$element.fineUploader('uploadStoredFiles');

            });

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

        $('.qq-template').exopiteFineUploader();

    });

})( jQuery, window, document );

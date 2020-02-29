
/**
 * Exopite Simple Options Framework Trumbowyg
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSOFColorpicker";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            var plugin = this;

            plugin.$element.find('.colorpicker').each(function (index, el) {

                if ($(el).closest('.exopite-sof-cloneable__item').hasClass('exopite-sof-cloneable__muster')) return;
                if ($(el).hasClass('disabled')) return;

                $(el).wpColorPicker({
                    /**
                     * @param {Event} event - standard jQuery event, produced by whichever
                     * control was changed.
                     * @param {Object} ui - standard jQuery UI object, with a color member
                     * containing a Color.js object.
                     */
                    change: function (event, ui) {
                        plugin.change(event, ui, $(this));
                    },
                });

            });

            plugin.$element.closest('.exopite-sof-wrapper').on('exopite-sof-field-group-item-added-after', function (event, $cloned) {

                $cloned.find('.colorpicker').each(function (index, el) {

                    if ($(el).closest('.exopite-sof-cloneable__item').hasClass('exopite-sof-cloneable__muster')) return;
                    if ($(el).hasClass('disabled')) return;

                    $(el).wpColorPicker({
                        change: function (event, ui) {
                            plugin.change(event, ui, $(this));
                        },
                    });

                });

                console.log('color picker clone');

            });

        },

        change: function (event, ui, $this) {
            var color = ui.color.toString();
            if ($this.hasClass('font-color-js')) {
                console.log('has font-color');
                $this.parents('.exopite-sof-font-field').find('.exopite-sof-font-preview').css({ 'color': color });
            }
        },

    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                    new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);

; (function ($) {
    "use strict";

    $(document).ready(function () {

        $('.exopite-sof-field').exopiteSOFColorpicker();

    });

}(jQuery));

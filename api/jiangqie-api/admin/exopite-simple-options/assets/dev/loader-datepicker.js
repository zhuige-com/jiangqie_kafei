
/**
 * Exopite Simple Options Framework Trumbowyg
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSOFDatepicker";

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

            plugin.$element.find('.datepicker').each(function (index, el) {
                if ($(el).parents('.exopite-sof-cloneable__muster').length) return;
                if ($(el).hasClass('.disabled')) return;
                var dateFormat = $(el).data('format');
                $(el).datepicker({ 'dateFormat': dateFormat });
            });

            plugin.$element.closest('.exopite-sof-wrapper').on('exopite-sof-field-group-item-added-after', function (event, $cloned) {

                $cloned.find('.datepicker').each(function (index, el) {

                    /**
                     * For some reason, datepicker will be attached to muster.
                     * Check if exist before added, if yes, firs tremove it.
                     */
                    if ($(el).closest('.exopite-sof-cloneable__item').hasClass('exopite-sof-cloneable__muster')) return;
                    if ($(el).hasClass('disabled')) return;

                    if ($(el).hasClass('hasDatepicker')) {
                        $(el).datepicker("destroy");
                        $(el).removeClass("hasDatepicker").removeAttr('id');
                    }

                    var dateFormat = $(el).data('format');
                    $(el).datepicker({ 'dateFormat': dateFormat });


                });

            });

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

        $('.exopite-sof-field-date').exopiteSOFDatepicker();

    });

}(jQuery));

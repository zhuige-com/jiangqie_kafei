function updateRangeInput(elem) {
    jQuery(elem).next().val(jQuery(elem).val());
}

function updateInputRange(elem) {
    jQuery(elem).prev().val(jQuery(elem).val());
}

if (typeof throttle !== "function") {
    // Source: https://gist.github.com/killersean/6742f98122d1207cf3bd
    function throttle(callback, limit, callingEvent) {
        var wait = false;
        return function () {
            if (wait && jQuery(window).scrollTop() > 0) {
                return;
            }
            callback.call(undefined, callingEvent);
            wait = true;
            setTimeout(function () {
                wait = false;
            }, limit);
        };
    }
}

// https://stackoverflow.com/questions/24159478/skip-recursion-in-jquery-find-for-a-selector/24215566?noredirect=1#comment37410122_24215566
jQuery.fn.findExclude = function (selector, mask, result) {
    result = typeof result !== 'undefined' ? result : new jQuery();
    this.children().each(function () {
        var thisObject = jQuery(this);
        if (thisObject.is(selector))
            result.push(this);
        if (!thisObject.is(mask))
            thisObject.findExclude(selector, mask, result);
    });
    return result;
}

/**
 * Get url parameter in jQuery
 * @link https://stackoverflow.com/questions/19491336/get-url-parameter-jquery-or-how-to-get-query-string-values-in-js/25359264#25359264
 */
; (function ($, window, document, undefined) {
    $.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        }
        else {
            return decodeURI(results[1]) || 0;
        }
    };
})(jQuery, window, document);

/**
 * Plugin to handle dependencies
 *
 * @link https://github.com/miohtama/jquery-interdependencies
 *
 * CodeStar Framework
 * The code for process dependencies from data attribute
 *
 * @link https://github.com/Codestar/codestar-framework/
 */
; (function ($, window, document, undefined) {
    'use strict';
    /**
     * Dependency System
     *
     * Codestar Framework
     */
    $.fn.exopiteSofManageDependencies = function (param) {
        return this.each(function () {

            var base = this,
                $this = $(this);

            base.init = function () {

                base.ruleset = $.deps.createRuleset();
                base.param = (param !== undefined) ? base.param = param + '-' : '';

                var cfg = {
                    show: function (el) {
                        el.removeClass('hidden');
                    },
                    hide: function (el) {
                        el.addClass('hidden');
                    },
                    log: false,
                    checkTargets: false

                };

                base.dep();

                $.deps.enable($this, base.ruleset, cfg);

            };

            base.dep = function () {

                $this.each(function () {

                    $(this).find('[data-' + base.param + 'controller]').each(function () {

                        var $this = $(this),
                            _controller = $this.data(base.param + 'controller').split('|'),
                            _condition = $this.data(base.param + 'condition').split('|'),
                            _value = $this.data(base.param + 'value').toString().split('|'),
                            _rules = base.ruleset;

                        $.each(_controller, function (index, element) {

                            var value = _value[index] || '',
                                condition = _condition[index] || _condition[0];

                            _rules = _rules.createRule('[data-' + base.param + 'depend-id="' + element + '"]', condition, value);
                            _rules.include($this);

                        });

                    });

                });

            };

            base.init();

        });

    };

})(jQuery, window, document);

/**
 * Exopite SOF Save Options with AJAX
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSaveOptionsAJAX";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.find('.exopite-sof-form-js').on('submit' + '.' + plugin._name, function (event) {
                plugin.submitOptions.call(this, event);
            });

            /**
             * Save on CRTL+S
             * @link https://stackoverflow.com/questions/93695/best-cross-browser-method-to-capture-ctrls-with-jquery/14180949#14180949
             */
            $(window).on('keydown' + '.' + plugin._name, function (event) {

                if (plugin.$element.find('.exopite-sof-form-js').length) {
                    if (event.ctrlKey || event.metaKey) {
                        switch (String.fromCharCode(event.which).toLowerCase()) {
                            case 's':
                                event.preventDefault();
                                var $form = plugin.$element.find('.exopite-sof-form-js');
                                plugin.submitOptions.call($form, event);
                                break;
                        }
                    }
                }
            });

            $(window).on('scroll' + '.' + plugin._name, throttle(plugin.checkFixed, 100, ''));

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        checkFixed: function () {

            var footerWidth = $('.exopite-sof-form-js').outerWidth();
            var bottom = 0;

            if (($(window).scrollTop() > ($('.exopite-sof-header-js').position().top + $('.exopite-sof-header-js').outerHeight(true))) &&
                ($(window).scrollTop() + $(window).height() < $(document).height() - 100)) {
                bottom = '0';
            } else {
                bottom = '-' + $('.exopite-sof-footer-js').outerHeight() + 'px';
            }


            $('.exopite-sof-footer-js').outerWidth(footerWidth);
            $('.exopite-sof-footer-js').css({
                bottom: bottom,
            });

        },

        /**
         * https://thoughtbot.com/blog/ridiculously-simple-ajax-uploads-with-formdata
         * https://stackoverflow.com/questions/17066875/how-to-inspect-formdata
         * https://developer.mozilla.org/en-US/docs/Web/API/FormData/FormData
         * https://developer.mozilla.org/en-US/docs/Web/API/FormData
         * https://stackoverflow.com/questions/2019608/pass-entire-form-as-data-in-jquery-ajax-function
         * https://stackoverflow.com/questions/33487360/formdata-and-checkboxes
         */
        submitOptions: function (event) {

            event.preventDefault();
            var saveButtonString = $(this).data('save');
            var savedButtonString = $(this).data('saved');
            var $submitButtons = $(this).find('.exopite-sof-submit-button-js');
            var currentButtonString = $submitButtons.val();
            var $ajaxMessage = $(this).find('.exopite-sof-ajax-message');
            $submitButtons.val(saveButtonString).attr('disabled', true);

            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }

            var formElement = $(this)[0];
            var formData = new FormData(formElement);

            var formName = $('.exopite-sof-form-js').attr('name');

            /**
             * 2.) Via ajaxSubmit
             */
            var $that = $(this);
            $(this).ajaxSubmit({
                beforeSubmit: function(arr, $form, options) {
                    // The array of form data takes the following form:
                    // [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
                     // https://jsonformatter.curiousconcept.com/

                    $that.find('[name]').not(':disabled').each(function (index, el) {
                        if ($(el).prop('nodeName') == 'INPUT' && $(el).attr('type') == 'checkbox' && !$(el).is(":checked") && !$(el).attr('name').endsWith('[]')) {
                            // not checked checkbox
                            var element = {
                                "name": $(el).attr('name'),
                                "value": "no",
                                "type": "checkbox",
                                // "required":false
                            };
                            arr.push(element);
                        }
                        if ($(el).prop('nodeName') == 'SELECT' && $(el).val() == null) {
                            // multiselect is empty
                            var element = {
                                "name": $(el).attr('name'),
                                "value": "",
                                "type": "select",
                                // "required":false
                            };
                            arr.push(element);
                        }
                    });

                    // return false to cancel submit
                },
                success: function () {
                    $submitButtons.val(currentButtonString).attr('disabled', false);
                    $ajaxMessage.html(savedButtonString).addClass('success show');
                    $submitButtons.blur();
                    setTimeout(function () {
                        // $ajaxMessage.fadeOut( 400 );
                        $ajaxMessage.removeClass('show');
                    }, 3000);
                },

                error: function (data) {
                    $submitButtons.val(currentButtonString).attr('disabled', false);
                    $ajaxMessage.html('Error! See console!').addClass('error show');
                },
            });

            return false;

        }

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

/**
 * Exopite SOF Media Uploader
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteMediaUploader";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);

        this._defaults = $.fn.exopiteMediaUploader.defaults;
        this.options = $.extend({}, this._defaults, options);

        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.find('.button').on('click' + '.' + plugin._name, function (event) {
                // this refer to the "[plugin-selector] .button" element
                plugin.openMediaUploader.call(this, event, plugin);
            });

            if (plugin.options.remove !== undefined && plugin.options.input !== undefined && plugin.options.preview !== undefined) {
                plugin.$element.find(plugin.options.remove).on('click' + '.' + plugin._name, function (event) {
                    // this refer to the "[plugin-selector] .button" element
                    plugin.removePreview.call(this, event, plugin);
                });
            }

        },

        openMediaUploader: function (event, plugin) {

            event.preventDefault();

            /*
             * Open WordPress Media Uploader
             *
             * @link https://rudrastyh.com/wordpress/customizable-media-uploader.html
             */

            var button = $(this),
                parent = button.closest('.exopite-sof-media'),
                isVideo = parent.hasClass('exopite-sof-video'),
                mediaType = (isVideo) ? 'video' : 'image',
                custom_uploader = wp.media({
                    title: 'Insert image',
                    library: {
                        // uncomment the next line if you want to attach image to the current post
                        // uploadedTo : wp.media.view.settings.post.id,
                        type: mediaType
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false // for multiple image selection set to true
                }).on('select', function () { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();

                    if (plugin.options.input !== undefined) {
                        parent.find(plugin.options.input).val(attachment.url);
                    }
                    if (!isVideo && plugin.options.preview !== undefined) {
                        parent.find(plugin.options.preview).removeClass('hidden');
                        parent.find('img').attr({ 'src': attachment.url });
                    }
                    if (isVideo) {
                        parent.find('video').attr({ 'src': attachment.url });
                    }
                    // $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
                    /* if you sen multiple to true, here is some code for getting the image IDs
                    var attachments = frame.state().get('selection'),
                        attachment_ids = new Array(),
                        i = 0;
                    attachments.each(function(attachment) {
                        attachment_ids[i] = attachment['id'];
                        console.log( attachment );
                        i++;
                    });
                    */
                })
                    .open();

        },

        removePreview: function (event, plugin) {

            var parent = plugin.$element;

            var previewWrapper = parent.find(plugin.options.preview);
            var previewImg = parent.find('img');

            if (previewWrapper.css('display') !== 'none' &&
                previewImg.css('display') !== 'none'
            ) {
                previewWrapper.addClass('hidden');
                previewImg.attr({ 'src': '' });
            }

            parent.find(plugin.options.input).val('');
        }

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

/**
 * Exopite SOF Options Navigation
 */
; (function ($, window, document, undefined) {

    /**
     * A jQuery Plugin Boilerplate
     *
     * https://github.com/johndugan/jquery-plugin-boilerplate/blob/master/jquery.plugin-boilerplate.js
     * https://john-dugan.com/jquery-plugin-boilerplate-explained/
     */

    var pluginName = "exopiteOptionsNavigation";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.onLoad.call(plugin);

            plugin.$element.find('.exopite-sof-nav-list-item').on('click' + '.' + plugin._name, function () {

                plugin.changeTab.call(plugin, $(this));

            });

            plugin.$element.find('.exopite-sof-nav-list-parent-item > .exopite-sof-nav-list-item-title').on('click' + '.' + plugin._name, function () {

                plugin.toggleSubMenu.call(plugin, $(this));

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        toggleSubMenu: function (button) {
            // var $parent = button;
            var $parent = button.parents('.exopite-sof-nav-list-parent-item');
            $parent.toggleClass('active').find('ul').slideToggle(200);
        },
        changeTab: function (button) {

            if (!button.hasClass('active')) {

                var section = '.exopite-sof-section-' + button.data('section');

                this.$element.find('.exopite-sof-nav-list-item.active').removeClass('active');

                button.addClass('active');

                this.$element.find('.exopite-sof-section').addClass('hide');
                this.$element.find(section).removeClass('hide');

            }

        },

        onLoad: function () {
            var plugin = this;

            /**
             * "Sanitize" URL
             * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
             */
            var URLSection = encodeURIComponent($.urlParam('section'));

            // If section not exist, then return
            if (!plugin.$element.find('.exopite-sof-section-' + URLSection).length) return false;

            var navList = plugin.$element.find('.exopite-sof-nav-list-item');
            plugin.$element.find('.exopite-sof-section').addClass('hide');
            plugin.$element.find('.exopite-sof-section-' + URLSection).removeClass('hide');
            navList.removeClass('active');
            navList.each(function (index, el) {
                var section = $(el).data('section');
                if (section == URLSection) {
                    $(el).addClass('active');
                }
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

/**
 * Exopite SOF Handle TinyMCE
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSOFTinyMCE";

    // The actual plugin constructor
    function Plugin(element, options) {

        if (typeof tinyMCE == 'undefined') return;

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            var plugin = this;

            tinyMCE.init({
                theme: 'modern',
                plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
                quicktags: true,
                tinymce: true,
                branding: false,
                media_buttons: true,
            });

            plugin.initTinyMCE();

            plugin.$element.on('exopite-sof-accordion-sortstart', function (event, $sortable) {
                $sortable.find('.tinymce-js').not(':disabled').each(function () {
                    tinyMCE.execCommand('mceRemoveEditor', false, $(this).attr('id'));
                });
            });

            plugin.$element.on('exopite-sof-accordion-sortstop', function (event, $sortable) {
                $sortable.find('.tinymce-js').not(':disabled').each(function () {
                    tinyMCE.execCommand('mceAddEditor', true, $(this).attr('id'));
                });
            });

            var $group = plugin.$element.parents('.exopite-sof-field-group');

            plugin.$element.on('exopite-sof-field-group-item-inserted-after', function (event, $cloned) {
                $cloned.find('.tinymce-js').each(function (index, el) {
                    var nextEditorID = plugin.musterID + (parseInt($group.find('.tinymce-js').not(':disabled').length) - 1);
                    $(el).attr('id', nextEditorID);
                    tinyMCE.execCommand('mceAddEditor', true, nextEditorID);
                });

            });

            plugin.$element.on('exopite-sof-field-group-item-cloned-after', function (event, $cloned) {

                $cloned.find('.tinymce-js').each(function (index, el) {
                    var nextEditorID = plugin.musterID + (parseInt($group.find('.tinymce-js').not(':disabled').length) - 1);
                    $(el).attr('id', nextEditorID);
                    $(el).show();
                    $(el).prev('.mce-tinymce').remove();
                    tinyMCE.execCommand('mceAddEditor', true, nextEditorID);
                });

            });

        },

        initTinyMCE: function () {
            var plugin = this;
            plugin.musterID = plugin.$element.find('.exopite-sof-cloneable__muster .tinymce-js').first().attr('id') + '-';

            plugin.$element.find('.tinymce-js').not(':disabled').each(function (index, el) {
                $(this).attr('id', plugin.musterID + index);
                var fullId = $(this).attr('id');

                tinyMCE.execCommand('mceAddEditor', true, fullId);

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

/**
 * Exopite SOF Font Field Preview
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteFontPreview";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.$nav = this.$element.find('.exopite-sof-nav');

        this.init();

    }

    Plugin.prototype = {

        init: function () {
            var plugin = this;

            var $sizeHeightWrapper = this.$element.children('.exopite-sof-typography-size-height');
            var $colorWrapper = this.$element.children('.exopite-sof-typography-color');
            plugin.preview = this.$element.children('.exopite-sof-font-preview');
            plugin.fontColor = $colorWrapper.find('.font-color-js').first();
            plugin.fontSize = $sizeHeightWrapper.children('span').children('.font-size-js');
            plugin.lineHeight = $sizeHeightWrapper.children('span').children('.line-height-js');
            // plugin.lineHeight = this.$element.find( '.line-height-js' );
            plugin.fontFamily = this.$element.children('.exopite-sof-typography-family').children('.exopite-sof-typo-family');
            plugin.fontWeight = this.$element.children('.exopite-sof-typography-variant').children('.exopite-sof-typo-variant');

            // Set current values to preview
            this.loadGoogleFont();
            this.updatePreview();
            this.setColorOnStart();

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.on('change' + '.' + plugin._name, '.font-size-js, .line-height-js, .font-color-js, .exopite-sof-typo-variant', function (e) {
                e.preventDefault();
                plugin.updatePreview();
            });

            plugin.$element.on('change' + '.' + plugin._name, '.exopite-sof-typo-family', function (e) {
                e.preventDefault();
                plugin.loadGoogleFont();
            });


        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },
        // Remove plugin instance completely
        destroy: function() {
            this.unbindEvents();
            this.$element.removeData('plugin_' + this._name);
            // this.element.removeData();
            this.element = null;
            this.$element = null;
        },
        setColorOnStart: function() {
            var plugin = this;
            var color = plugin.fontColor.val();
            plugin.preview.css({ 'color': color });
        },
        updatePreview: function () {
            var plugin = this;
            var fontWeightStyle = plugin.calculateFontWeight(plugin.fontWeight.find(':selected').text());
            // Update preiew
            plugin.preview.css({
                'font-size': plugin.fontSize.val() + 'px',
                'line-height': plugin.lineHeight.val() + 'px',
                'font-weight': fontWeightStyle.fontWeightValue,
                'font-style': fontWeightStyle.fontStyleValue
            });
        },
        updateVariants: function (variants) {
            var plugin = this;
            var variantsArray = variants.split('|');
            var selected = plugin.fontWeight.children('option:selected').val();
            plugin.fontWeight.empty();
            $.each(variantsArray, function (key, value) {
                var $option = $("<option></option>").attr("value", value).text(value);
                plugin.fontWeight.append($option);
                if (value == selected) {
                    $option.attr('selected', 'selected');
                }
            });
            plugin.fontWeight.trigger("chosen:updated");
        },
        loadGoogleFont: function () {
            var plugin = this;
            var variants = plugin.fontFamily.find(":selected").data('variants');

            plugin.updateVariants(variants);

            var font = plugin.fontFamily.val();
            if (!font) return;
            var href = '//fonts.googleapis.com/css?family=' + font + ':' + variants.replace(/\|/g, ',');
            var parentName = plugin.$element.find('.exopite-sof-font-field-js').data('id');
            var html = '<link href="' + href + '" class="cs-font-preview-' + parentName + '" rel="stylesheet" type="text/css" />';

            if ($('.cs-font-preview-' + parentName).length > 0) {
                $('.cs-font-preview-' + parentName).attr('href', href).load();
            } else {
                $('head').append(html).load();
            }

            // Update preiew
            plugin.preview.css('font-family', font).css('font-weight', '400');

        },
        calculateFontWeight: function (fontWeight) {
            var fontWeightValue = '400';
            var fontStyleValue = 'normal';

            switch (fontWeight) {
                case '100':
                    fontWeightValue = '100';
                    break;
                case '100italic':
                    fontWeightValue = '100';
                    fontStyleValue = 'italic';
                    break;
                case '300':
                    fontWeightValue = '300';
                    break;
                case '300italic':
                    fontWeightValue = '300';
                    fontStyleValue = 'italic';
                    break;
                case '500':
                    fontWeightValue = '500';
                    break;
                case '500italic':
                    fontWeightValue = '500';
                    fontStyleValue = 'italic';
                    break;
                case '700':
                    fontWeightValue = '700';
                    break;
                case '700italic':
                    fontWeightValue = '700';
                    fontStyleValue = 'italic';
                    break;
                case '900':
                    fontWeightValue = '900';
                    break;
                case '900italic':
                    fontWeightValue = '900';
                    fontStyleValue = 'italic';
                    break;
                case 'italic':
                    fontStyleValue = 'italic';
                    break;
            }

            return { fontWeightValue, fontStyleValue };
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

/**
 * Exopite SOF Repeater
 */
; (function ($, window, document, undefined) {

    /**
     * A jQuery Plugin Boilerplate
     *
     * https://github.com/johndugan/jquery-plugin-boilerplate/blob/master/jquery.plugin-boilerplate.js
     * https://john-dugan.com/jquery-plugin-boilerplate-explained/
     */

    var pluginName = "exopiteSOFRepeater";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.$sortableWrapper = this.$element.children('.exopite-sof-cloneable__wrapper');
        this.$container = $(element).children('.exopite-sof-accordion__wrapper').first();
        this.sortable = null;

        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.isSortable = (this.$container.data('is-sortable'));
            this.bindEvents();
            this.updateTitle();
            this.setMusterDisabled();
            this.sortableInit();

            /**
             * Access other plugin functions and variables
             */
            // this.$element.data('plugin_exopiteSOFAccordion').showYorself('test')
            // console.log('options: ' + JSON.stringify(this.$element.data('plugin_exopiteSOFAccordion').sortableOptions));

        },

        sortableInit: function() {

            if (this.isSortable) {

                //https://github.com/lukasoppermann/html5sortable
                sortable('.exopite-sof-cloneable__wrapper', {
                    handle: '.exopite-sof-cloneable__title',
                });
            }

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            // Predefinied
            plugin.$element.find('.exopite-sof-cloneable--add').off().on('click' + '.' + plugin._name, function (e) {
                e.preventDefault();
                if ($(this).is(":disabled")) return;
                plugin.addNew.call(plugin, $(this));

            });

            // Dynamically added
            plugin.$element.on('click' + '.' + plugin._name, '.exopite-sof-cloneable--remove:not(.disabled)', function (e) {

                if (e.target != this) return false;
                e.preventDefault();
                plugin.remove($(this));

            });

            plugin.$element.on('click' + '.' + plugin._name, '.exopite-sof-cloneable--clone:not(.disabled)', function (e) {

                // Match only on clicked element
                if (e.target != this) return false;

                e.preventDefault();

                // Stop event bubbling
                e.stopPropagation();

                plugin.addNew($(this));

            });

            plugin.$element.find('.exopite-sof-cloneable__item').on('input change blur', '[data-title=title]', function (event) {

                plugin.updateTitleElement($(this));

            });

            /**
             * Need to "reorder" name elements for metabox,
             * so it is saved in the order of displayed.
             */
            // Call function if sorting is stopped
            plugin.$container.on('sortstart' + '.' + plugin._name, function () {

                plugin.$element.trigger('exopite-sof-accordion-sortstart', [plugin.$container]);

            });


            plugin.$container.on('sortstop' + '.' + plugin._name, function (event, ui) {

                event.stopPropagation();

                plugin.updateName($(this));

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        updateName: function( $element ) {
            var plugin = this;

            var text = $element.closest('.exopite-sof-group').children('.exopite-sof-cloneable--add').text();

            var $wrapper = $element.closest('.exopite-sof-cloneable__wrapper');
            var wrapperName = $wrapper.attr('data-name');
            var baseName = wrapperName.replace(/\[REPLACEME\]$/, '');
            var baseNameRegex = plugin.escapeRegExp(baseName);
            var regexGroupName = new RegExp(baseNameRegex + "\\[(.*?)\\]", "i");

            $wrapper.findExclude('.exopite-sof-cloneable__item', '.exopite-sof-group').each(function(index, el){

                /**
                 * Update data-name for muster element (set parent indexes for cloning)
                 */
                $(el).find('[data-name^="' + baseName + '"]').each(function(indexName, elDataName){
                    var elementName = $(elDataName).attr('data-name');

                    var relpacedName = elementName.replace(regexGroupName, function ($0, $1) {
                        // options[en][group][0][field][]

                        return baseName + '[' + index + ']';
                    });

                    $(elDataName).attr('data-name', relpacedName);

                });

                /**
                 * Update element names (only this parent index) from current level to the last level
                 */
                $(el).find('[name^="' + baseName + '"]').each(function(indexName, elName){

                    var elementName = $(elName).attr('name');
                    var relpacedName = elementName.replace(regexGroupName, function ($0, $1) {
                        // options[en][group][0][field][]

                        return baseName + '[' + index + ']';
                    });

                    $(elName).attr('name', relpacedName);

                });


            });

        },

        remove: function ($button) {

            $wrapper = $button.closest('.exopite-sof-cloneable__wrapper');
            $button.closest('.exopite-sof-cloneable__item').remove();
            this.updateName($wrapper);
            this.checkAmount($wrapper);

            $button.trigger('exopite-sof-field-group-item-removed');

        },

        checkAmount: function ($wrapper) {

            var numItems = $wrapper.children('.exopite-sof-cloneable__item').length;
            var maxItems = $wrapper.data('limit');

            if (typeof maxItems !== 'undefined') {
                return numItems;
            }

            /**
             * Fixme:
             * - This apply to all child, wrong!
             */
            if (maxItems <= numItems) {
                this.$element.find('.exopite-sof-cloneable--add').attr("disabled", true);
                return false;
            } else {
                this.$element.find('.exopite-sof-cloneable--add').attr("disabled", false);
                return numItems;
            }


        },

        setMusterDisabled: function () {

            /**
             * Mainly for nested elements (in our case: tab)
             * This will prevent dinamically added muster elements to save.
             */
            this.$element.find('.exopite-sof-cloneable__muster').find('[name]').prop('disabled', true).addClass('disabled');

        },

        updateTitleElement: function ($element) {

            var $item = $element.closest('.exopite-sof-cloneable__item');
            var title = $item.find('[data-title=title]').first().val();
            $item.children('.exopite-sof-cloneable__title').children('.exopite-sof-cloneable__text').text(title);
            $item.trigger('exopite-sof-field-group-item-title-updated');

        },

        updateTitle: function () {

            this.$element.find('.exopite-sof-cloneable__wrapper').find('.exopite-sof-cloneable__item').find('[data-title=title]').each(function (index, el) {
                var title = $(el).val();
                if (title) {
                    $(el).closest('.exopite-sof-cloneable__item').children('.exopite-sof-cloneable__title').children('.exopite-sof-cloneable__text').text(title);
                }

                $(el).trigger('exopite-sof-field-group-item-title-updated');
            });

        },

        escapeRegExp: function (stringToGoIntoTheRegex) {
            // https://stackoverflow.com/questions/17885855/use-dynamic-variable-string-as-regex-pattern-in-javascript/17886301#17886301
            return stringToGoIntoTheRegex.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        },

        removeIfNested: function(index) {
            // this is the corrent DOM element
            var $this = $(this),
                return_value = false;

            $.each($this.attr('class').split(/\s+/), function(index) {
                if ($this.parents("." + this).length > 0) {
                    return_value = default_value || true;
                }
            });

            return return_value;
        },

        addNew: function ($element) {

            var plugin = this;

            var $group = $element.closest('.exopite-sof-group');

            if ($.fn.chosen) $group.find("select.chosen").chosen("destroy");

            var is_cloned = false;
            var $cloned = null;

            // Decide which element need to clone: the clicked or the muster?
            if ($element.hasClass('exopite-sof-cloneable--clone')) {
                $cloned = $element.parent().parent().parent('.exopite-sof-cloneable__item').clone(true);
                is_cloned = true;
            } else {
                var $muster = $element.parent().children('.exopite-sof-cloneable__muster');
                $cloned = $muster.clone(true);
            }

            /**
             * Get hidden "muster" element and clone it. Remove hidden muster classes.
             * Add trigger before and after (for various programs, like TinyMCE, Trumbowyg, etc...)
             * Finaly append to group.
             */
            $cloned.find('.exopite-sof-cloneable--remove').removeClass('disabled');
            $cloned.find('.exopite-sof-cloneable--clone').removeClass('disabled');
            $cloned.removeClass('exopite-sof-cloneable__muster');
            $cloned.removeClass('exopite-sof-cloneable__muster--hidden');
            $cloned.removeClass('exopite-sof-accordion--hidden');
            $cloned.findExclude('[disabled]', '.exopite-sof-cloneable__muster').attr('disabled', false).removeClass('disabled');;

            plugin.$element.trigger('exopite-sof-field-group-item-added-before', [$cloned, $group]);

            if (is_cloned) {

                // Remove font preview plugin
                $cloned.find('.exopite-sof-font-field').unbind().removeData('plugin_exopiteFontPreview');

                if ($.fn.chosen) $cloned.find('select.chosen').unbind().removeData().next().remove();

                // Insert after clicked element
                $cloned.insertAfter($element.closest('.exopite-sof-cloneable__item'));
                $wrapper = $element.closest('.exopite-sof-cloneable__wrapper');
            } else {
                // Insert after all elements
                $group.children('.exopite-sof-cloneable__wrapper').first().append($cloned);
                $wrapper = $element.closest('.exopite-sof-group').children('.exopite-sof-cloneable__wrapper');
            }

            var numItem = plugin.checkAmount($wrapper);
            if (! numItem) {
                return;
            }

            plugin.setMusterDisabled();
            plugin.updateName($wrapper);

            if ($.fn.chosen) $group.find("select.chosen").chosen({ width: "375px" });

            // If has date picker, initilize it.
            $cloned.find('.datepicker').each(function (index, el) {
                var dateFormat = $(el).data('format');
                $(el).removeClass('hasDatepicker').datepicker({ 'dateFormat': dateFormat });
            });

            plugin.sortableInit();

            // Handle dependencies.
            $cloned.exopiteSofManageDependencies('sub');
            $cloned.find('.exopite-sof-cloneable__content').removeAttr("style").show();

            $cloned.find('.exopite-sof-font-field').each(function(index,el){

                if (!$(el).children('label').children('select').is(":disabled")) {
                    $(el).exopiteFontPreview();
                }

            });

            sortable('.exopite-sof-gallery', {
                forcePlaceholderSize: true,
            });

            plugin.$element.trigger('exopite-sof-field-group-item-added-after', [$cloned, $group]);
            if (is_cloned) {
                plugin.$element.trigger('exopite-sof-field-group-item-cloned-after', [$cloned, $group]);
            } else {
                plugin.$element.trigger('exopite-sof-field-group-item-inserted-after', [$cloned, $group]);
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

/**
 * Exopite SOF Accordion
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSOFAccordion";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.$container = $(element).children('.exopite-sof-accordion__wrapper').first();
        this.allOpen = (this.$container.data('all-open') || typeof this.$container.data('all-open') == 'undefined');
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // showYorself: function(somevar){
        //     console.log('Yeah baby it is me: ' + somevar);
        // },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$container.off().on('click' + '.' + plugin._name, '.exopite-sof-accordion__title', function (e) {
                e.preventDefault();

                if (!$(e.target).hasClass('exopite-sof-cloneable--clone') && !$(this).closest('.exopite-sof-accordion__wrapper').hasClass('exopite-sof-group-compact')) {
                    plugin.toggleAccordion.call(plugin, $(this));
                }

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$container.off('.' + this._name);
        },

        slideUp: function ($element) {
            $element.children('.exopite-sof-accordion__content').slideUp(350, function () {
                $element.addClass('exopite-sof-accordion--hidden');
            });
        },

        slideDown: function ($element) {
            $element.children('.exopite-sof-accordion__content').slideDown(350, function () {
                $element.removeClass('exopite-sof-accordion--hidden');
            });
        },

        toggleAccordion: function ($header) {
            var plugin = this;
            var $this = $header.parent('.exopite-sof-accordion__item');

            /**
             * This is for the accordion field.
             */
            if (!this.allOpen) {

                this.$container.findExclude('.exopite-sof-accordion__item', '.exopite-sof-accordion').each(function (index, el) {

                    if ($(el).is($this)) {
                        plugin.slideDown($this);
                    } else {
                        plugin.slideUp($(el));
                    }

                });

            } else {

                if ($this.hasClass('exopite-sof-accordion--hidden')) {
                    plugin.slideDown($this);
                } else {
                    plugin.slideUp($this);
                }

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


/**
 * Exopite SOF Search In Options
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteSofSearch";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.$nav = this.$element.find('.exopite-sof-nav');

        this.init();

    }

    Plugin.prototype = {

        init: function () {

            $.expr[':'].containsIgnoreCase = function (n, i, m) {
                return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
            };

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.on('keyup' + '.' + plugin._name, '.exopite-sof-search', function (e) {
                e.preventDefault();
                plugin.doSearch.call(plugin, $(this));
            });

            plugin.$element.on('click' + '.' + plugin._name, '.exopite-sof-section-header', function (e) {
                e.preventDefault();
                plugin.selectSection.call(plugin, $(this));
            });

            plugin.$element.on('click' + '.' + plugin._name, '.exopite-sof-nav.search', function (e) {
                e.preventDefault();
                plugin.clearSearch.call(plugin, $(this));
            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },
        clearSearch: function ($clickedElement) {
            var plugin = this;
            plugin.$element.find('.exopite-sof-search').val('').blur();
            plugin.$element.find('.exopite-sof-nav').removeClass('search');
            plugin.$element.find('.exopite-sof-section-header').hide();
            plugin.$element.find('.exopite-sof-field h4').closest('.exopite-sof-field').not('.hidden').removeAttr('style');
            plugin.$element.find('.exopite-sof-field-card').removeAttr('style');
            var activeElement = plugin.$nav.find("ul li.active").data('section');
            plugin.$element.find('.exopite-sof-sections .exopite-sof-section-' + activeElement).removeClass('hide');
        },
        activateSection: function (activeElement) {
            var plugin = this;
            if (plugin.$nav.length > 0) {
                plugin.$element.find('.exopite-sof-section-header').hide();
                var $activeElement = plugin.$element.find('.exopite-sof-nav li[data-section="' + activeElement + '"]');
                $activeElement.addClass('active');
                if ( $activeElement.parents('.exopite-sof-nav-list-parent-item').length > 0  &&
                    ! $activeElement.parents('.exopite-sof-nav-list-parent-item').hasClass('active') ) {
                    $activeElement.parents('.exopite-sof-nav-list-parent-item').addClass('active').find('ul').slideToggle(200);;
                }
                plugin.$element.find('.exopite-sof-nav').removeClass('search');
            }
            plugin.$element.find('.exopite-sof-sections .exopite-sof-section').addClass('hide');
            plugin.$element.find('.exopite-sof-sections .exopite-sof-section-' + activeElement).removeClass('hide');
            plugin.$element.find('.exopite-sof-field h4').closest('.exopite-sof-field').not('.hidden').removeAttr('style');
            plugin.$element.find('.exopite-sof-field-card').removeAttr('style');

        },
        selectSection: function ($sectionHeader) {
            var plugin = this;
            plugin.$element.find('.exopite-sof-search').val('').blur();
            var activeElement = $sectionHeader.data('section');
            plugin.activateSection(activeElement);
        },
        doSearch: function ($searchField) {
            var plugin = this;
            var searchValue = $searchField.val();
            var activeElement = this.$nav.find("ul li.active").data('section');
            if (typeof this.$element.data('section') === 'undefined') {
                this.$element.data('section', activeElement);
            }

            if (searchValue) {
                if (this.$nav.length > 0) {
                    this.$element.find('.exopite-sof-nav-list-item').removeClass('active');
                    this.$element.find('.exopite-sof-nav').addClass('search');
                }
                this.$element.find('.exopite-sof-section-header').show();
                this.$element.find('.exopite-sof-section').removeClass('hide');
                this.$element.find('.exopite-sof-field h4').closest('.exopite-sof-field').not('.hidden').hide();
                this.$element.find('.exopite-sof-field-card').hide();
                this.$element.find('.exopite-sof-field h4:containsIgnoreCase(' + searchValue + ')').closest('.exopite-sof-field').not('.hidden').show();
            } else {
                activeElement = this.$element.data('section');
                this.$element.removeData('section');
                plugin.activateSection(activeElement);
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

/**
 * Exopite SOF Import/Export Options with AJAX
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteImportExportAJAX";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.find('.exopite-sof-import-js').off().on('click' + '.' + plugin._name, function (event) {

                event.preventDefault();
                if ($(this).hasClass('loading')) return;

                swal({

                    // title: "Are you sure?",
                    text: $(this).data('confirm'),
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,

                }).then((willImport) => {

                    if (willImport) {

                        $(this).addClass('loading');
                        $(this).prop("disabled", true);
                        this.disabled = true;
                        plugin.importOptions.call(this, event, plugin);

                    }

                });

                // if ( confirm( $( this ).data( 'confirm' ) ) ) {

                //     plugin.importOptions.call( this, event, plugin );

                // }

            });

            plugin.$element.find('.exopite-sof-reset-js').off().on('click' + '.' + plugin._name, function (event) {

                event.preventDefault();

                swal({

                    // title: "Are you sure?",
                    text: $(this).data('confirm'),
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,

                }).then((willDelete) => {

                    if (willDelete) {

                        $(this).addClass('loading');
                        $(this).prop("disabled", true);
                        plugin.resetOptions.call(this, event, plugin);

                    }

                });

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        importOptions: function (event, plugin) {

            var AJAXData = plugin.$element.find('.exopite-sof--data');

            $.ajax({
                url: AJAXData.data('admin'),
                type: 'post',
                data: {
                    action: 'exopite-sof-import-options',
                    unique: AJAXData.data('unique'),
                    value: plugin.$element.find('.exopite-sof__import').val(),
                    wpnonce: AJAXData.data('wpnonce')
                },
                success: function (response) {

                    if (response == 'success') {

                        plugin.$element.find('.exopite-sof__import').val('');
                        swal({
                            icon: "success",
                        });
                        location.reload();

                    }

                },
                error: function (xhr, status, error) {

                    console.log('Status: ' + xhr.status);
                    console.log('Error: ' + xhr.responseText);
                    swal("Error!", "Check console for more info!", "error");

                }
            });

            return false;

        },

        resetOptions: function (event, plugin) {

            var AJAXData = plugin.$element.find('.exopite-sof--data');

            $.ajax({
                url: AJAXData.data('admin'),
                type: 'post',
                data: {
                    action: 'exopite-sof-reset-options',
                    unique: AJAXData.data('unique'),
                    wpnonce: AJAXData.data('wpnonce')
                },
                success: function (response) {

                    if (response == 'success') {

                        swal({
                            icon: "success",
                        });
                        location.reload();

                    }

                },

                error: function (xhr, status, error) {
                    console.log('Status: ' + xhr.status);
                    console.log('Error: ' + xhr.responseText);
                    swal("Error!", "Check console for more info!", "error");
                }
            });

            return false;

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

/**
 * Exopite SOF Tab Navigation
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopiteTabs";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        /**
         * Looks a little bit strange, but some readon this is the only way
         * what I find, to make this work in group field.
         */
        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;
            plugin.$tabLinks = plugin.$element.children('.exopite-sof-tab-header').children('.exopite-sof-tab-link');
            plugin.$tabContents = plugin.$element.children('.exopite-sof-tab-content');
            plugin.mobileHeader = plugin.$tabContents.children('.exopite-sof-tab-mobile-header');

            plugin.$tabLinks.off().on('click' + '.' + plugin._name, function (event) {

                plugin.changeTabs(event, this);

            });

            plugin.mobileHeader.off().on('click' + '.' + plugin._name, function (event) {

                var index = $(this).parent().index() - 1;
                var that = this;

                // tabLinks
                var $tabLinks = $(that).parent().parent().children('.exopite-sof-tab-header').children('.exopite-sof-tab-link');
                var $tabContents = $(that).parent().parent().children('.exopite-sof-tab-content');

                $tabLinks.removeClass('active');
                $tabLinks.eq(index).addClass('active');
                $tabContents.removeClass('active');
                $(that).parent().addClass('active');

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        changeTabs: function (event, that, index) {
            var plugin = this;
            var index = $(that).index();

            $(that).parent().children('.exopite-sof-tab-link').removeClass('active');
            $(that).parent().parent().children('.exopite-sof-tab-content').removeClass('active');
            $(that).addClass('active');
            $(that).parent().parent().children('.exopite-sof-tab-content').eq(index).addClass('active');

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

; (function ($, window, document, undefined) {

    "use strict";

    var pluginName = "exopiteSOFGallery";

    function Plugin(element, options) {
        this.element = element;
        this._name = pluginName;
        this.meta_gallery_frame = null;
        this.$container = $(element).children('.exopite-sof-gallery').first();

        this.buildCache();
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            var plugin = this;

            plugin.bindEvents();
            plugin.sortableInit();

        },
        sortableInit: function () {
            var plugin = this;

            sortable('.exopite-sof-gallery', {
                forcePlaceholderSize: true,
            });

            // From documentation, not working with dinamically added elements.
            // sortable('.exopite-sof-gallery', {
            //     forcePlaceholderSize: true,
            // })[0].addEventListener('sortstop', function(e) {

            //     console.log('gallery sortstop 0');

            // });

            /**
             * html5sortable - sortupdate event is not triggered on dynamic elements
             * @link https://stackoverflow.com/questions/46211700/html5sortable-sortupdate-event-is-not-triggered-on-dynamic-elements/46212177#46212177
             */
            // if( sortable('.exopite-sof-gallery').length > 0) {
            //     sortable('.exopite-sof-gallery')[sortable('.exopite-sof-gallery').length-1].addEventListener('sortstop', function (e) {

            //         plugin.updateIDs( $(this) );
            //         console.log('gallery sortstop 1');

            //     });
            // }

            // plugin.$galleryList.sortable({
            //     tolerance: "pointer",
            //     cursor: "grabbing",
            //     stop: function( event, ui ) {
            //         console.log('test stop');
            //         let imageIDs = [];
            //         plugin.$galleryList.children('li').each(function( index, el ){
            //             imageIDs.push( $(el).children('img').attr('id') );
            //         });
            //         plugin.$imageIDs.val( imageIDs.join(',') );
            //     }
            // });
        },
        bindEvents: function () {
            var plugin = this;

            plugin.$element.on('click' + '.' + plugin._name, '> .exopite-sof-gallery-add', function (e) {
                e.preventDefault();

                plugin.manageMediaFrame.call( plugin, $(this) );

            });

            plugin.$element.on('click' + '.' + plugin._name, '.exopite-sof-image-delete', function (e) {
                e.preventDefault();

                plugin.deleteImage.call( plugin, $(this) );

            });

            plugin.$container.on('sortstop' + '.' + plugin._name, function (event, ui) {

                // console.log('gallery sortstop 2');

                plugin.updateIDs( $(this) );

            });

        },
        updateIDs: function ( $element ) {

            let imageIDs = [];
            $element.children('span').each(function( index, el ){
                imageIDs.push( $(el).children('img').attr('id') );
            });
            $element.closest('.exopite-sof-gallery').prev().val( imageIDs.join(',') );

        },
        deleteImage: function ($button) {
            var plugin = this;
            if (confirm('Are you sure you want to remove this image?')) {

                let $imageIDs = $button.closest('.exopite-sof-gallery').prev();
                var removedImage = $button.next().attr('id');
                var galleryIds = $imageIDs.val().split(",");
                galleryIds = $( galleryIds ).not([removedImage]).get();
                $imageIDs.val( galleryIds );
                $button.parent().remove();
                plugin.sortableInit();

            }
        },
        manageMediaFrame: function ( $button ) {
            var plugin = this;

            let $imageIDs = $button.prev().prev();
            let $galleryWrapper = $button.prev();

            // If the frame already exists, re-open it.
            plugin.meta_gallery_frame = null;

            let title = plugin.$element.data('media-frame-title') || 'Select Images';
            let button = plugin.$element.data('media-frame-button') || 'Add';
            let image = plugin.$element.data('media-frame-type') || 'image';

            // Sets up the media library frame
            plugin.meta_gallery_frame = wp.media.frames.meta_gallery_frame = wp.media({
                title: title,
                button: {
                    text: button
                },
                library: {
                    type: image
                },
                multiple: true
            });

            // Create Featured Gallery state. This is essentially the Gallery state, but selection behavior is altered.
            plugin.meta_gallery_frame.states.add([
                new wp.media.controller.Library({
                    title: title,
                    priority: 20,
                    toolbar: 'main-gallery',
                    filterable: 'uploaded',
                    library: wp.media.query(plugin.meta_gallery_frame.options.library),
                    multiple: plugin.meta_gallery_frame.options.multiple ? 'reset' : false,
                    editable: true,
                    allowLocalEdits: true,
                    displaySettings: true,
                    displayUserSettings: true
                }),
            ]);

            plugin.meta_gallery_frame.on('open', function () {
                var selection = plugin.meta_gallery_frame.state().get('selection');

                var library = plugin.meta_gallery_frame.state('gallery-edit').get('library');
                var ids = $imageIDs.val();
                if (ids) {
                    let idsArray = ids.split(',');
                    idsArray.forEach(function (id) {
                        let attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }
            });

            // When an image is selected, run a callback.
            plugin.meta_gallery_frame.on('select', function () {
                var imageIDArray = [];
                var imageHTML = '';
                var metadataString = '';
                var images;
                images = plugin.meta_gallery_frame.state().get('selection');
                images.each(function (attachment) {
                    imageIDArray.push(attachment.attributes.id);
                    imageHTML += '<span><span class="exopite-sof-image-delete"></span><img id="' + attachment.attributes.id + '" src="' + attachment.attributes.sizes.thumbnail.url + '"></span>';
                });
                metadataString = imageIDArray.join(",");
                if (metadataString) {
                    $imageIDs.val( metadataString );
                    $galleryWrapper.html( imageHTML );
                    plugin.sortableInit();
                }
            });

            // Finally, open the modal
            plugin.meta_gallery_frame.open();

        },
        destroy: function () {
            this.unbindEvents();
            this.$element.removeData();
        },
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },
        buildCache: function () {
            this.$element = $(this.element);
            this.$imageIDs = this.$element.children('[data-control="gallery-ids"]');
            this.$galleryList = this.$element.children('.exopite-sof-gallery');
        },
    });

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);


/**
 * Exopite SOF - Manage empty checkboxes and selects (multiple)
 *
 * When checkbox is not checked or select with attribute multiple is empty,
 * they are not sent in POST so create a hidden input before the element for that.
 *
 * This is only relevant for metabox, because in the options the POST can be
 * check and add them before being sent.
 */
; (function ($, window, document, undefined) {

    var pluginName = "exopitePrepareForm";

    // The actual plugin constructor
    function Plugin(element, options) {

        this.element = element;
        this._name = pluginName;
        this.$element = $(element);
        this.init();

    }

    Plugin.prototype = {

        init: function () {

            this.bindEvents();

        },

        // Bind events that trigger methods
        bindEvents: function () {
            var plugin = this;

            plugin.$element.find('select[multiple]').not(':disabled').each(function (index, element) {

                var $this = $(this),
                    selected_value = $this.val();

                if (!selected_value) {
                    plugin.addHidden($this.attr('name'), $this);
                }

            });

            plugin.$element.find('select[multiple]').on('change', function () {

                var $this = $(this),
                    selected_value = $this.val();

                if (selected_value) {
                    plugin.removeHidden($this);
                } else {
                    plugin.addHidden($this.attr('name'), $this);
                }

            });

            plugin.$element.find('input:checkbox:not(:checked):not([disabled])').each(function (index, element) {

                var $this = $(this);
                plugin.addHidden($this.attr('name'), $this);

            });

            plugin.$element.find('input[type="checkbox"]').on('change', function () {

                var $this = $(this),
                    checked = $this.prop('checked');

                if (checked) {
                    plugin.removeHidden($this);
                } else {
                    plugin.addHidden($this.attr('name'), $this);
                }

            });

        },

        // Unbind events that trigger methods
        unbindEvents: function () {
            this.$element.off('.' + this._name);
        },

        addHidden: function ( name, $element ) {

            $('<input>').attr({
                'type': 'hidden',
                'name': name,
                'value': 'no'
            }).insertBefore($element);

        },

        removeHidden: function ( $element ) {

            $element.prev('input[type="hidden"]').remove();

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

/**
 * ToDos:
 * - sortable only if data-is-sortable is 1 || true
 * - maybe move sortable to a separate plugin?
 * - clean up
 * - fix name generation (if added or cloned) <- drag&drop is working az expected
 * - replace color picker (
 *      https://acko.net/blog/farbtastic-jquery-color-picker-plug-in/
 *      https://www.jqueryscript.net/other/Color-Picker-Plugin-jQuery-MiniColors.html
 *      https://www.jquery-az.com/a-bootstrap-jquery-color-picker-with-7-demos/
 *   )?
 */
; (function ($) {
    "use strict";

    $(document).ready(function () {

        $('.exopite-sof-wrapper').exopiteSofManageDependencies();
        $('.exopite-sof-wrapper').exopiteSofSearch();
        $('.exopite-sof-sub-dependencies').exopiteSofManageDependencies('sub');

        $('.exopite-sof-wrapper-menu').exopiteSaveOptionsAJAX();

        $('.exopite-sof-media').exopiteMediaUploader({
            input: 'input',
            preview: '.exopite-sof-image-preview',
            remove: '.exopite-sof-image-remove'
        });

        $('.exopite-sof-content-js').exopiteOptionsNavigation();
        $('.exopite-sof-wrapper').find('.exopite-sof-font-field').each(function(index,el){

            if (!$(el).children('label').children('select').is(":disabled")) {
                $(el).exopiteFontPreview();
            }

        });
        $('.exopite-sof-group').exopiteSOFTinyMCE();
        $('.exopite-sof-accordion').exopiteSOFAccordion();
        $('.exopite-sof-group').exopiteSOFRepeater();
        $('.exopite-sof-field-backup').exopiteImportExportAJAX();
        $('.exopite-sof-tabs').exopiteTabs();
        $('.exopite-sof-gallery-field').exopiteSOFGallery();
        $('.exopite-sof-wrapper-metabox').exopitePrepareForm();

    });

}(jQuery));

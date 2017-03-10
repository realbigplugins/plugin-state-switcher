var PluginSS_Switcher;

(function ($) {
    'use strict';

    var l10n = PluginSS_Data['l10n'] || {};

    var api = PluginSS_Switcher = {

        /**
         * jQuery objects for elements.
         *
         * @since 1.0.0
         */
        elements: {},

        /**
         * Initialize the object.
         *
         * @since 1.0.0
         */
        init: function () {

            if (!api.get_elements()) {

                return;
            }

            api.setup_handlers();

            api.set_form_display();
        },

        /**
         * Gets elements.
         *
         * @since 1.0.0
         *
         * @returns {boolean}
         */
        get_elements: function () {

            api.elements.switcher = $('#pluginss-container');

            if (!api.elements.switcher.length) {

                return false;
            }

            api.elements.notices = $('#pluginss-switcher-notices');

            api.elements.fieldset_add = api.elements.switcher.find('fieldset.pluginss-add-state');
            api.elements.fieldset_manage = api.elements.switcher.find('fieldset.pluginss-manage-states');

            api.elements.button_add = api.elements.switcher.find('button[name="add_state"]');
            api.elements.button_delete = api.elements.switcher.find('button[name="delete_state"]');
            api.elements.button_load = api.elements.switcher.find('button[name="load_state"]');

            api.elements.field_name = api.elements.switcher.find('input[name="state_name"]');
            api.elements.field_state = api.elements.switcher.find('select[name="state_selector"]');

            return true;
        },

        /**
         * Sets up event handlers.
         *
         * @since 1.0.0
         */
        setup_handlers: function () {

            api.elements.field_name.keypress(api.add_state_keypress);
            api.elements.field_state.change(api.change_state_selector);

            api.elements.button_add.click(api.save_state);
            api.elements.button_delete.click(api.delete_state);
        },

        /**
         * Fires in pressing a key within the add state text field.
         *
         * @since 1.0.0
         *
         * @param e
         */
        add_state_keypress: function (e) {

            if (e.which === 13) {

                e.preventDefault();
                api.save_state();
            }
        },

        /**
         * Saves a state.
         *
         * @since 1.0.0
         */
        save_state: function (e) {

            var name = api.elements.field_name.val();

            api.set_loading();

            $.post(
                ajaxurl,
                {
                    action: 'pluginss_add_state',
                    name: name,
                    nonce: PluginSS_Data.nonce
                },
                function (response) {

                    var $notice = api.elements.notices.find('.pluginss-notice-dummy').clone();

                    if (typeof response.success == 'undefined' || !response.success) {

                        $notice.addClass('error').find('p').html(response.data.message);

                    } else {

                        $notice.addClass('updated').find('p').html(response.data.message);

                        api.elements.field_state.append(
                            '<option value="' + response.data.id + '" selected data-active="1">' +
                            response.data.name + '</option>'
                        );
                    }

                    api.set_form_display();

                    api.elements.field_name.val('');

                    api.unset_loading();

                    $notice.removeClass('pluginss-notice-dummy').appendTo(api.elements.notices).show();

                    setTimeout(function () {

                        $notice.slideUp(300, function () {

                            $(this).remove();
                        });

                    }, 5000);
                }
            )
        },

        /**
         * Deletes the current state.
         *
         * @since 1.0.0
         */
        delete_state: function () {

            if (!confirm(l10n['delete_state_confirm'])) {

                return;
            }

            var current_state = api.elements.field_state.find('option:selected').val();

            api.set_loading();

            $.post(
                ajaxurl,
                {
                    action: 'pluginss_delete_state',
                    id: current_state,
                    nonce: PluginSS_Data.nonce
                },
                function (response) {

                    var $notice = api.elements.notices.find('.pluginss-notice-dummy').clone();

                    if (typeof response.success == 'undefined' || !response.success) {

                        $notice.addClass('error').find('p').html(response.data.message);

                    } else {

                        $notice.addClass('updated').find('p').html(response.data.message);

                        api.elements.field_state.find('option:selected').remove();
                    }

                    api.set_form_display();

                    api.unset_loading();

                    $notice.removeClass('pluginss-notice-dummy').appendTo(api.elements.notices).show();

                    setTimeout(function () {

                        $notice.slideUp(300, function () {

                            $(this).remove();
                        });

                    }, 5000);
                }
            )
        },

        /**
         * Fires when changing the load state selector.
         *
         * @since 1.0.0
         */
        change_state_selector: function () {

            api.set_form_display();
        },

        /**
         * Sets the form to loading.
         *
         * @since 1.0.0
         */
        set_loading: function () {

            var $inputs = api.elements.switcher.find('input, select, button');

            $inputs.prop('disabled', true);

            api.elements.switcher.find('.pluginss-form').append('<span class="spinner is-active"></span>');
        },

        /**
         * Unsets the form from loading.
         *
         * @since 1.0.0
         */
        unset_loading: function () {

            var $inputs = api.elements.switcher.find('input, select, button'),
                $spinner = api.elements.switcher.find('.spinner');

            $inputs.prop('disabled', false);

            $spinner.remove();
        },

        /**
         * Hides/shows part of the form based on the conditions.
         *
         * @since 1.0.0
         */
        set_form_display: function () {

            if (!api.elements.field_state.find('option').length) {

                api.elements.fieldset_manage.hide();

            } else {

                api.elements.fieldset_manage.show();
            }

            if (api.elements.field_state.find('option[data-active="1"]').length) {

                api.elements.fieldset_add.hide();
                api.elements.fieldset_manage.removeClass('right-padded');

            } else {

                api.elements.fieldset_add.show();
                api.elements.fieldset_manage.addClass('right-padded');
            }

            if (api.elements.field_state.find('option:selected').attr('data-active')) {

                api.elements.button_load.hide();

            } else {

                api.elements.button_load.show();
            }
        }
    };

    $(api.init);

})(jQuery);
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'mage/translate'
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            this._super();
            this.sourceStoreId = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.source_store_id');
            this.reloadOptions();
            if (typeof window.textmasterTemplateId === 'undefined') {
                window.textmasterTemplateId = [];
            }
            if (typeof window.textmasterTemplateId[this.name] === 'undefined') {
                window.textmasterTemplateId[this.name] = this.value();
            }
            this.value(window.textmasterTemplateId[this.name]);
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value)
        {
            if (value != 'undefined')
            {
                window.textmasterTemplateId[this.name] = value;
            }
            return this._super();
        },

        reloadOptions: function ()
        {
            let elementName = this.name;
            let sourceStoreIdName = elementName.replace('template_id', 'target_store_id');
            let sourceStoreId = uiRegistry.get(sourceStoreIdName);
            if (sourceStoreId) {
                let options = [];
                if (
                    typeof window.textmasterApiTemplatesConfig !== 'undefined' &&
                    typeof window.textmasterApiTemplatesConfig[this.sourceStoreId.value()] !== 'undefined' &&
                    typeof window.textmasterApiTemplatesConfig[this.sourceStoreId.value()][sourceStoreId.value()] !== 'undefined'
                ) {
                    _.each(window.textmasterApiTemplatesConfig[this.sourceStoreId.value()][sourceStoreId.value()], function (data) {
                        options.push(data);
                    });
                }
                if (options.length === 0) {
                    options.push({
                        value: '',
                        label: $.mage.__('No API Template available')
                    });
                }
                this.options(options);
            }
        }
    });
});

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
            if (typeof window.textmasterTargetStoreId === 'undefined') {
                window.textmasterTargetStoreId = [];
            }
            if (typeof window.textmasterTargetStoreId[this.name] === 'undefined') {
                window.textmasterTargetStoreId[this.name] = this.value();
            }
            this.value(window.textmasterTargetStoreId[this.name]);
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
                window.textmasterTargetStoreId[this.name] = value;
                let elementName = this.name;
                let templateIdName = elementName.replace('target_store_id', 'template_id');
                let templateId = uiRegistry.get(templateIdName);
                if (templateId) {
                    templateId.reloadOptions();
                }
            }
            return this._super();
        },

        reloadOptions: function ()
        {
            let sourceStoreId = this.sourceStoreId;
            let options = [];
            _.each(sourceStoreId.options(), function (data) {
                if (data.value !== sourceStoreId.value()) {
                    options.push(data);
                }
            });
            if (options.length === 0) {
                options.push({
                    value: '',
                    label: $.mage.__('No target language available')
                });
            }
            this.options(options);
        }
    });
});

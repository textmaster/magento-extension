define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value)
        {
            if (value != 'undefined')
            {
                let targets = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.targets'), targetStoreId;
                _.each(targets.relatedData, function (data) {
                    targetStoreId = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.targets.' + data.record_id + '.target_store_id');
                    if (targetStoreId) {
                        targetStoreId.reloadOptions();
                    }
                });
            }
            return this._super();
        },
    });
});

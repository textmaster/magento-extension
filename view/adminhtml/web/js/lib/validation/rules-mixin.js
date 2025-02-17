define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/lib/validation/utils',
    'moment',
    'mage/translate'
], function ($, _, uiRegistry, utils, moment) {
    'use strict';

    return function (validator) {
        var validators = {
            'textmaster-unique-target-store-id': [
                function (value) {
                    let targets = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.targets'), targetStoreId;
                    let targetsStoreId = [], unique = true;
                    _.each(targets.relatedData, function (data) {
                        targetStoreId = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.targets.' + data.record_id + '.target_store_id');
                        if (targetStoreId) {
                            if (typeof targetsStoreId[targetStoreId.value()] === 'undefined') {
                                targetsStoreId[targetStoreId.value()] = 0;
                            }
                            targetsStoreId[targetStoreId.value()]++;
                        }
                    });

                    return typeof targetsStoreId[value] === 'undefined' || targetsStoreId[value] === 1;
                },
                $.mage.__('Unique Target language')
            ],
            'textmaster-required-document': [
                function (value) {
                    let documentType = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.document_type');
                    let result = true;
                    if (documentType.value() !== 'category' ) {
                        let errorMessage = $('#textmaster_project_' + documentType.value() + 's_error');
                        errorMessage.hide();
                        if ($('#in_textmaster_project_' + documentType.value() + 's').val() === '{}') {
                            result = false;
                            errorMessage.show();
                        }
                    }
                    return result;
                },
                ''
            ],
            'textmaster-category-ids-required-entry': [
                function (value) {
                    let documentType = uiRegistry.get('textmaster_project_form.textmaster_project_form.general.document_type');
                    let result = true;
                    if (documentType.value() === 'category' ) {
                        result = !utils.isEmpty(value);
                    }
                    return result;
                },
                $.mage.__('This is a required field.')
            ]
        };

        validators = _.mapObject(validators, function (data) {
            return {
                handler: data[0],
                message: data[1]
            };
        });

        return $.extend(validator, validators);
    };
});

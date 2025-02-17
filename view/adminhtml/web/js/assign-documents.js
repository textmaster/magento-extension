define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedDocuments = config.selectedDocuments,
            inputId = config.inputId,
            projectDocuments = $H(selectedDocuments),
            gridJsObject = window[config.gridJsObjectName];

        /**
         * Show selected document when edit form in associated document grid
         */
        $(inputId).value = Object.toJSON(projectDocuments);

        /**
         * Register Project Document
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerProjectDocument(grid, element, checked) {
            if (checked) {
                projectDocuments.set(element.value, element.documentName);
            } else {
                projectDocuments.unset(element.value);
            }
            $(inputId).value = Object.toJSON(projectDocuments);
            grid.reloadParams = {
                'selected_documents[]': projectDocuments.keys()
            };
        }

        /**
         * Click on document row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function projectDocumentRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Initialize project document row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function projectProductRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                documentName = $(row).getElementsByClassName('col-document-name')[0];

            if (checkbox && documentName) {
                checkbox.documentName = documentName.innerText;
            }
        }

        gridJsObject.rowClickCallback = projectDocumentRowClick;
        gridJsObject.initRowCallback = projectProductRowInit;
        gridJsObject.checkboxCheckCallback = registerProjectDocument;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                projectProductRowInit(gridJsObject, row);
            });
        }
    };
});

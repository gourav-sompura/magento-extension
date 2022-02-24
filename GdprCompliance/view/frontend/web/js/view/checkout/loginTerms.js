/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'ko'
], function ($, Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Zealousweb_GdprCompliance/checkout/terms'
        },
        showTermsAndConditions: ko.observable(false),
        displayMessage: ko.observable(null),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            
            var currentFromId = this.formId;
            var flag = false;
            
            if(window[this.configSource] && window[this.configSource].termsConfig){
                var termsConfig = window[this.configSource].termsConfig;
                var isActive = termsConfig.isActive;
                this.displayMessage(termsConfig.display_message);
                $.each(termsConfig.forms, function (index, formId) {
                    if(formId == currentFromId && isActive == 1){
                        flag = true;
                    }
                });
            }
            this.showTermsAndConditions(flag);
        }
    });
});

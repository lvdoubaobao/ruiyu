/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'mage/url',
        'Magento_Paypal/js/view/payment/method-renderer/paypal-express-abstract'
    ],
    function (url,Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Paypal/payment/paypal-express'
            },

            getViewImg: function () {
                return url.build('media/wysiwyg/paypal/ppcredit-logo-medium.png');
            }
        });
    }
);

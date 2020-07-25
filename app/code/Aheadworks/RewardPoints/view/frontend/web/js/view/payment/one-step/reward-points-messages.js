/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Aheadworks_OneStepCheckout/js/view/totals/applicable/messages',
    'Aheadworks_RewardPoints/js/model/payment/reward-points-messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});

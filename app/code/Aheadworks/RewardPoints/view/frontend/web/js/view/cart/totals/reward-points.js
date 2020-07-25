/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/*global define*/
define(
    [
        'Aheadworks_RewardPoints/js/view/summary/reward-points'
    ],
    function (Component) {
        "use strict";

        var awRewardPointsRemoveUrl  = window.checkoutConfig.payment.awRewardPoints.removeUrl;

        return Component.extend({
            defaults: {
                template: 'Aheadworks_RewardPoints/cart/totals/reward-points'
            },
            
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return this.getPureValue() != 0;
            },

            /**
             * Retrieve url for remove Reward Points
             *
             * @returns {String}
             */
            getRemoveUrl: function () {
                return awRewardPointsRemoveUrl;
            }
        });
    }
);
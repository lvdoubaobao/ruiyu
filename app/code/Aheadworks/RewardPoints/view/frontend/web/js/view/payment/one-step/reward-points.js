/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define(
    [
        'jquery',
        'ko',
        'Aheadworks_OneStepCheckout/js/view/totals/applicable/abstract-reward',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'Magento_Customer/js/model/customer',
        'Aheadworks_RewardPoints/js/action/apply-reward-points',
        'Aheadworks_RewardPoints/js/action/remove-reward-points',
        'Aheadworks_RewardPoints/js/action/get-customer-reward-points-balance',
        'Aheadworks_RewardPoints/js/model/reward-points-balance',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        totals,
        priceUtils,
        customer,
        applyRewardPointsAction,
        removeRewardPointsAction,
        getCustomerRewardPointsBalanceAction,
        rewardPointsBalance,
        fullScreenLoader,
        $t
    ) {
        'use strict';

        var rewardPoints = totals.getSegment('aw_reward_points');

        return Component.extend({
            defaults: {
                isApplied: (rewardPoints != null && rewardPoints.value != 0)
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();
                if (customer.isLoggedIn()) {
                    getCustomerRewardPointsBalanceAction();
                }
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();
                this.isShown = ko.computed(function () {
                    return customer.isLoggedIn()
                        && rewardPointsBalance.customerRewardPointsOnceMinBalance() == 0
                        && rewardPointsBalance.customerRewardPointsSpendRateByGroup()
                        && rewardPointsBalance.customerRewardPointsSpendRate();
                }, this);
                this.availableLabel = ko.computed(function () {
                    return rewardPointsBalance.customerRewardPointsBalance() +
                        $t(' store reward points available ') +
                        '(' + this.getFormattedPrice(rewardPointsBalance.customerRewardPointsBalanceCurrency()) + ')'
                }, this);
                this.usedLabel = ko.computed(function () {
                    var rewardPoints = totals.getSegment('aw_reward_points');

                    return rewardPoints
                        ? $t('Used ') + rewardPoints.title
                        : '';
                }, this);

                return this;
            },

            /**
             * @inheritdoc
             */
            _getPureTotalValue: function() {
                var rewardPoints = totals.getSegment('aw_reward_points');

                return rewardPoints ? rewardPoints.value : 0;
            },

            /**
             * @inheritdoc
             */
            _apply: function () {
                var isActionComplete = $.Deferred();

                fullScreenLoader.startLoader();
                $.when(isActionComplete).always(function () {
                    fullScreenLoader.stopLoader();
                });

                return applyRewardPointsAction(this.isApplied, ko.observable(true), isActionComplete);
            },

            /**
             * @inheritdoc
             */
            _cancel: function () {
                var isActionComplete = $.Deferred();

                fullScreenLoader.startLoader();
                $.when(isActionComplete).always(function () {
                    fullScreenLoader.stopLoader();
                });

                return removeRewardPointsAction(this.isApplied, ko.observable(true), isActionComplete);
            }
        });
    }
);

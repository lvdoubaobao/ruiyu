/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'mage/url',
        'Aheadworks_RewardPoints/js/model/payment/reward-points-messages',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/get-payment-information',
        'mage/storage',
        'mage/translate'
    ],
    function (
        ko,
        $,
        urlManager,
        messageContainer,
        quote,
        totals,
        errorProcessor,
        getPaymentInformationAction,
        storage,
        $t
    ) {
        'use strict';
        return function (isApplied, isLoading, point, deferred) {
            var quoteId = quote.getQuoteId(),
                url = urlManager.build('aw_rewardpoints/checkout/updateTotal');

            if (typeof deferred == 'undefined') {
                deferred = $.Deferred();
            }

            return $.ajax({
                url: url,
                type: 'POST',
                data: {'reward_points': point,'quote_id': quoteId},
                /*complete: function (data) {
                    var arrDataReward = $.map($.parseJSON(data.responseText), function (value, index) {
                        return [value];
                    });
                    $.dataReward = arrDataReward;
                    var deferred = $.Deferred();
                    getPaymentInformation(deferred);
                    $.when(deferred).done(function () {
                        $.each(listReward, function (key, val) {
                            $('tr.' + val).show();
                            $('tr.' + val + ' td.amount span').text($.dataReward[key]);
                        })
                        totals.isLoading(false);
                    });
                }*/
                success: function (response) {
                    /*console.log(response,
                        Boolean(response.custom_attributes.success),
                        response.custom_attributes.success);*/
                    if (typeof response.custom_attributes.success != 'undefined' && response.custom_attributes.success) {
                        var totalsDeferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        totals.isLoading(true);
                        getPaymentInformationAction(totalsDeferred);
                        $.when(totalsDeferred).done(function () {
                            totals.isLoading(false);
                            deferred.resolve();
                        });
                        messageContainer.addSuccessMessage({'message': response.custom_attributes.message});
                    }
                },
                error: function (response) {
                    isLoading(false);
                    totals.isLoading(false);
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();
                }
            });

            
            /*return storage.post(
                url,
                JSON.stringify({
                    point: point
                }),
                false
            ).done(
                function (response) {
                    if (response[0] != 'undefined' && response[0].success) {
                        var totalsDeferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        totals.isLoading(true);
                        getPaymentInformationAction(totalsDeferred);
                        $.when(totalsDeferred).done(function () {
                            totals.isLoading(false);
                            deferred.resolve();
                        });
                        messageContainer.addSuccessMessage({'message': response[0].message});
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    totals.isLoading(false);
                    errorProcessor.process(response, messageContainer);
                    deferred.reject();
                }
            );*/
        };
    }
);

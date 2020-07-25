/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'Magento_Customer/js/model/customer',
        'Aheadworks_RewardPoints/js/action/apply-reward-points',
        'Aheadworks_RewardPoints/js/action/remove-reward-points',
        'Aheadworks_RewardPoints/js/action/update-reward-points',
        'Aheadworks_RewardPoints/js/action/get-customer-reward-points-balance',
        'Aheadworks_RewardPoints/js/model/reward-points-balance',
        "Aheadworks_RewardPoints/js/ion.rangeSlider",
        'mage/translate'
     ],
    function (
            $, 
            ko, 
            Component, 
            totals, 
            priceUtils,
            customer, 
            applyRewardPoints,
            removeRewardPoints,
            updateRewardPoints,
            getCustomerRewardPointsBalanceAction,
            rewardPointsBalance,
            rangeSlider,
            $t
        ){
        'use strict';
        
        var rewardPoints = totals.getSegment('aw_reward_points');
        var isApplied = ko.observable((rewardPoints != null && rewardPoints.value != 0));
        var isLoading = ko.observable(false);

        function updateTotal(point) {
            isLoading(true);
            if (point == 0) {
                removeRewardPoints(isApplied, isLoading);
            } else if (point == rewardPointsBalance.customerRewardPointsMaxPoints()) {
                applyRewardPoints(isApplied, isLoading);
            } else {
                updateRewardPoints(isApplied, isLoading, point);
            }
        }

        ko.bindingHandlers.spendingPoint = {
            init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
                var usedPoints = 0,//当前使用的积分
                    subtotal = totals.getSegment('subtotal').value,
                    maxUsePoints = rewardPointsBalance.customerRewardPointsMaxPoints();
                if (rewardPoints) {
                    usedPoints = parseInt(rewardPoints.title.replace(' Reward Points', ''));
                }
                if(usedPoints > 0){
                    $('#reward_sales_point').val(usedPoints);
                }
                $(element).ionRangeSlider({
                    grid:true,
                    grid_num:((maxUsePoints < 4) ? maxUsePoints : 4),
                    min:rewardPointsBalance.customerRewardPointsOnceMinBalance(),
                    max:maxUsePoints,
                    step:1,
                    from:usedPoints,
                    onFinish: function (data) {
                        if(usedPoints > 0 && usedPoints == maxUsePoints){
                            $('#reward_max_points_used').attr('checked','checked');
                        }else{
                            $('#reward_max_points_used').removeAttr('checked');
                        }
                        $("#reward_sales_point").val(data.from);
                        updateTotal(data.from);
                    }
                });
            }
        };
        
        return Component.extend({
            defaults: {
                template: 'Aheadworks_RewardPoints/payment/reward-points'
            },
            
            /**
             * Check if reward points is apply
             * 
             * @return {boolean}
             */
            isApplied: isApplied,
            
            /**
             * Is loading
             * 
             * @return {boolean}
             */
            isLoading: isLoading,
            
            /**
             * Check if customer is logged in
             * 
             * @return {boolean}
             */
            isCustomerLoggedIn: function(){
                return customer.isLoggedIn();
            },
            
            /**
             * Is display reward points block
             * 
             * @return {boolean}
             */
            isDisplayed: function() {
                var isDisplayed = false;
                if (this.isCustomerLoggedIn()) {
                    getCustomerRewardPointsBalanceAction();
                    isDisplayed = rewardPointsBalance.customerRewardPointsOnceMinBalance() == 0
                        && rewardPointsBalance.customerRewardPointsSpendRateByGroup()
                        && rewardPointsBalance.customerRewardPointsSpendRate();
                }
                return isDisplayed;
            },
            
            /**
             * Apply reward points
             * 
             * @return {void}
             */
            apply: function() {
                if (this.validate()) {
                    isLoading(true);
                    applyRewardPoints(isApplied, isLoading);
                }
            },
            
            /**
             * Remove reward points
             * 
             * @return {void}
             */
            remove: function() {
                if (this.validate()) {
                    isLoading(true);
                    removeRewardPoints(isApplied, isLoading);
                }
            },

            /**
             * Update reward points
             *
             * @return {void}
             */
            updateTotal: function(point) {
                if (this.validate()) {
                    isLoading(true);
                    if (point == 0) {
                        removeRewardPoints(isApplied, isLoading);
                    } else if (point == rewardPointsBalance.customerRewardPointsMaxPoints()) {
                        applyRewardPoints(isApplied, isLoading);
                    } else {
                        updateRewardPoints(isApplied, isLoading, point);
                    }
                }
            },

            /**
             * Validate
             * 
             * @return {boolean}
             */
            validate: function() {
                return true;
            },
            
            /**
             * Retrieve available points text
             * 
             * @return {String}
             */
            getAvailablePointsText: function() {
                return rewardPointsBalance.customerRewardPointsBalance()
                    + $t(' store reward points available ') 
                    + '(' 
                    + this.getFormattedPrice(rewardPointsBalance.customerRewardPointsBalanceCurrency()) 
                    + ')';
            },
            
            getPointsText: function() {
                var rewardPoints = totals.getSegment('aw_reward_points');
                if(rewardPoints != null){
                    return  $t('you can use:') 
                    + rewardPoints.title;
                }else{
                    return  $t('you can use:') 
                    + rewardPointsBalance.customerRewardPointsMaxPoints()+$t('Reward Points');
                }
                
            },            
            usePoint: function(){
                /*if(rewardPointsBalance.customerRewardPointsBalance()){
                    return rewardPointsBalance.customerRewardPointsBalance();
                }else{
                    return 0;
                }*/
                if (rewardPoints) {
                    return parseInt(rewardPoints.title.replace(' Reward Points', ''));
                } else {
                    return 0;
                }
            },
            oldval: function(){
                if (rewardPoints) {
                    return parseInt(rewardPoints.title.replace(' Reward Points', ''));
                } else {
                    return 0;
                }
            },
            changSpendingPoint:function(){
                var _this = $('#reward_sales_point');
                var val = _this.val();
                if( $.isNumeric(val) ){
                    if(val > rewardPointsBalance.customerRewardPointsMaxPoints()){
                        val = rewardPointsBalance.customerRewardPointsMaxPoints();
                        _this.val(val);
                    }
                    $(_this).data('oldval',val);
                    $(_this).removeAttr('style');
                    var $range = $("#range_reward_point");
                    var slider = $range.data("ionRangeSlider");
                    slider.update({
                        from: val
                    });
                    this.updateTotal(val);
                    if(rewardPointsBalance.customerRewardPointsMaxPoints() == val){
                        $('#reward_max_points_used').attr('checked','checked');
                    }else{
                        $('#reward_max_points_used').removeAttr('checked');
                    }
                }else{
                    $(_this).css({"border":"solid 1px red"});
                    $(_this).val($('#reward_sales_point').data('oldval'));
                }
            },
            maxSpendingPoint:function(){
                var point = 0;
                if ($('#reward_max_points_used').attr('checked')) {
                    point = rewardPointsBalance.customerRewardPointsMaxPoints();
                }
                $('#reward_sales_point').val(point);
                var $range = $("#range_reward_point");
                var slider = $range.data("ionRangeSlider");
                slider.update({
                    from: point
                });
                this.updateTotal(point);
            },
            checkedMax: function () {
                var rewardPoints = totals.getSegment('aw_reward_points');
                if (rewardPoints) {
                    var usedPoints = parseInt(rewardPoints.title.replace(' Reward Points', ''));
                    var maxUsePoints = rewardPointsBalance.customerRewardPointsMaxPoints();
                    if (usedPoints == maxUsePoints) {
                        return true;
                    }
                }
                return false;
            },
            
            /**
             * Retrieve used points text
             * 
             * @return {String}
             */
            getUsedPointsText: function() {
                var rewardPoints = totals.getSegment('aw_reward_points');

                if (rewardPoints) {
                    return $t('Used ') + rewardPoints.title + ' (' + this.getFormattedPrice(rewardPoints.value) + ')';
                } else {
                    return '';
                }
            },
            /**
             * Formated price
             * 
             * @return {String}
             */
            getFormattedPrice: function(price) {
                return priceUtils.formatPrice(price, window.checkoutConfig.priceFormat);
            }
        });
});
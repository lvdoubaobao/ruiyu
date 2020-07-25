/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for share
 *
 * @method click()
 */
define([
    'jquery',
    'facebook'
], function($,FB) {
    "use strict";

    $.widget('awrp.awRewardPointsShare', {
        options: {
            url: '/',
            productId: 0,
            network: ''
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._bind();
        },
        /**
         * Bind event
         */
        _bind: function() {
            this._on({
                click: this.click
            });
        },
        /**
         * Redirect to url with rule id and uenc param
         */
        click: function()
        {
            switch (this.options.network) {
                case 'facebook':
                    this.shareFacebook();
                    break;
                case 'twitter':
                    this.shareTwitter();
                    break;
                case 'google-plus':
                    this.shareGooglePlus();
                    break;
                default:
                    break;
            }
        },
        shareFacebook: function() {
            var _this = this;
            FB.init({
                appId: '855167387977478',
                xfbml: true,
                cookie: true,
                version: 'v2.6'
            });
            FB.ui({
                method: 'share',
                href: $('meta[property="og:url"]').attr('content')
            }, function (response) {
                if (response && !response.error_message) {
                    _this.shareSuccess();
                } else {
                    alert('You have cancelled the share.');
                }
            });
        },
        shareTwitter: function(){
            this.shareSuccess();
        },
        shareGooglePlus: function(){
            this.shareSuccess();
        },
        shareSuccess: function(){
            $.ajax({
                url: this.options.url,
                data: {
                    productId: this.options.productId,
                    network: this.options.network
                },
                type: 'GET',
                cache: false,
                dataType: 'json',
                context: this,
                success: function (response) {
                    console.log(response);
                }
            });
        }
    });

    return $.awrp.awRewardPointsShare;
});

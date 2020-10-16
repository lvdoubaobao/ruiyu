/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'layer'
], function($, $t) {
    "use strict";

    $.widget('mage.catalogAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },

        _create: function() {
            console.log('bindsubmit')
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {Object} form
         */
        submitForm: function (form) {

            //
            var radioval='';
            $('.product-options-wrapper .field.required').find('label.label:first-child').each(function(){

                var labelVal=$(this).attr('for').replace('select_', '');
                if($(".product-options-wrapper .field.required .control input[name='options["+labelVal+"]']").length>0){
                    $(".product-options-wrapper .field.required .control input[name='options["+labelVal+"]']").each(function(){
                        if($(".product-options-wrapper .field.required .control input[name='options["+labelVal+"]']").is(":checked")){
                            radioval=labelVal;
                        }else{
                            radioval=0;
                        }
                    });
                    if(radioval==0){
                        if($('.product-options-wrapper .field.required').find("label.label:first-child[for=select_"+labelVal+"]").parent().find('.mandatory').length>0){
                        }else{
                            $('.product-options-wrapper .field.required').find("label.label:first-child[for=select_"+labelVal+"]").after('<span class="mandatory" style="color:red;">This is mandatory</span>');


                        }
                    }else{
                        $('.product-options-wrapper .field.required').find("label.label:first-child[for=select_"+labelVal+"]").next('span').remove();
                    }
                }

            });
            $('.product-options-wrapper .field.configurable.required').find('label.label:first-child').each(function(){
                var labelVal=$(this).attr('for').replace('attribute', '');
                if($(".product-options-wrapper .field.configurable.required .control select[name='super_attribute["+labelVal+"]']").length>0) {
                    radioval = 1;
                }
            });

            if($('.product-options-wrapper .field.required').find('label.label:first-child').length>0) {
                if (radioval) {
                    //return true;
                } else {

                    $("#buy-now").focus()
                    return false;
                }
            }

            var addToCartButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');

                // disable 'Add to Cart' button
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
            console.log(222);
            layer.open({type: 2,content: 'Adding...'});
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    console.log('cart',res)
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }
                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {

                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    layer.closeAll()

                    //产品弹窗
                   if (res.success){
                        $("#addcart_list1").attr("src",res.success.product.image)
                       $("#gw-product-addcart-qty").html(res.success.itemQty)
                        $("#new-details").html(res.success.product.title)

                        $("#gw-product-detail-ajax-amout").html(res.success.product.qty+'*$'+res.success.product.price)
                        $("#gw-product-detail-subtotal").html('$'+res.success.total_price)
                        $("#tanchuang").show();
                      /*  setTimeout(()=>{
                            $("#addcartbox").hide();
                        },3000)*/
                   }



                    self.enableAddToCartButton(form);
                }
            });
        },

        disableAddToCartButton: function(form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...');
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.mage.catalogAddToCart;
});

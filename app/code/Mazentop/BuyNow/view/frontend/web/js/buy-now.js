define([
    'jquery'
], function ($) {
    "use strict";

    return function (config, element) {

        $(element).click(function () {
            var form = $(config.form);

            // change form action
            var baseUrl = form.attr('action'),
                buyNowUrl = baseUrl.replace('checkout/cart/add', 'buynow/cart/add');
            var radioval='';
            $('.product-options-wrapper .field.required').find('label.label:first-child').each(function(){

                var labelVal=$(this).attr('for').replace('select_', '');
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
            });
            if($('.product-options-wrapper .field.required').find('label.label:first-child').length>0){
                if(radioval){
                    // return true;
                }else{

                    return false;
                }
            }
            form.attr('action', buyNowUrl);

            form.trigger('submit');

            // set form action back
            form.attr('action', baseUrl);

            return false;
        });
    }
});
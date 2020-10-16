require([
    "jquery"
], function ($) {
    $(function () {        
        move_index($(".cms-index-index .customer-review .content"));
        $('.nav_navlist li.parent .plus').click(function () {
            $(this).parents('li.parent').toggleClass('open').siblings().removeClass('open');
        })
        $('.footer-main .links .title').addClass('close');
        $(".nav_navlist li span").delegate(".fa-plus", "click", function () {
            $(this).parent().siblings(".navlist_menu").slideDown("fast");
            $(this).removeClass('fa-plus').addClass('fa-minus');
            $(this).parents("li").siblings().find(".navlist_menu").hide();
            $(this).parents("li").siblings().find(".fa-minus").removeClass('fa-minus').addClass('fa-plus');
        });
        $(".nav_navlist li span").delegate(".fa-minus", "click", function () {
            $(this).parent().siblings(".navlist_menu").slideUp("fast");
            $(this).removeClass('fa-minus').addClass('fa-plus');
            $(this).parent().siblings(".navlist_menu").find(".fa-minus").removeClass('fa-minus').addClass('fa-plus');
            $(this).parent().siblings(".navlist_menu").find("ol").hide();
            $(this).parents("li").siblings().find("ol").hide();
        });
        $(".navlist_menu h3").delegate(".fa-plus", "click", function () {
            $(this).parent().siblings("ol").slideDown("fast");
            $(this).removeClass('fa-plus').addClass('fa-minus');
            $(this).parents(".row").siblings().find("ol").hide();
            $(this).parents(".row").siblings().find(".fa-minus").removeClass('fa-minus').addClass('fa-plus');
        });
        $(".navlist_menu h3").delegate(".fa-minus", "click", function () {
            $(this).parent().siblings("ol").slideUp("fast");
            $(this).removeClass('fa-minus').addClass('fa-plus');
        });
        $(window).scroll(function () {
            if (parseFloat($(window).scrollTop()) >= 160) {
                $(".nav-sections-item-content").addClass("nav_fixed");
                $("#back-top").fadeIn("fast");
            } else {
                $(".nav-sections-item-content").removeClass("nav_fixed");
                $("#back-top").fadeOut("fast");
            }
            /*必填验证*/
            $('.product-options-bottom .box-tocart .actions button.primary').click(function () {
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
                if(radioval){
                    return true;
                }else{
                    $("#buy-now").focus()
                    return false;
                }
            });
            /**/
        });
        $("#back-top").click(function () {
            var speed = 200;
            $("body,html").animate({scrollTop: 0}, speed);
            return false;
        });
        $(".length-help-container .text").click(function(){
            $(".leng-help-img").fadeIn();
        });
        $(".leng-help-img").click(function(){
            $(this).fadeOut();
        });
      
        $(".product-options-wrapper .options-list>.admin__field").click(function(){
            $(this)
                .addClass('outline')
                .siblings().removeClass('outline');                
        });
        var px = "px";
        var _leftHeight = $('.customer-share .bottom .left-img-share').height() + px;
        
        $('.customer-share .bottom .middle-img-share img').css("max-height",_leftHeight);
        $(".show-social-share").click(function(){
            $(this).siblings(".social-addthis").toggleClass("show");
        });       
 
        var myDiv = $(".panel.header .header-account-content .header.links");
        $(".panel.header .header-account-content").delegate(".header-account.active", "click", function (event)
        {
            $(myDiv).show();
            $(this).removeClass('active').addClass('close');
            $(document).one("click", function ()
            {
                $('.panel.header .header-account-content .header-account').removeClass('close').addClass('active');
                $(myDiv).hide();
            });
            event.stopPropagation();
        });
        $(myDiv).click(function (event)
        {
            event.stopPropagation();
        });
        $(".panel.header .header-account-content").delegate(".header-account.close", "click", function () {
            $(this).removeClass('close').addClass('active');
            $(this).siblings('.header.links').hide();
        })
        $(document).find('.countdown').each(function () {
            var _this = jQuery(this);
            var time = _this.data('time');
            _this.text(overtime(time))
            setInterval(function () {
                time = time - 1
                _this.text(overtime(time))
            }, 1000)
            function overtime(time) {
                var day = Math.floor(time / (24 * 60 * 60));
                var hour = Math.floor((time - 24 * 3600 * day) / 3600);
                var min = Math.floor((time - 24 * 3600 * day - 3600 * hour) / 60);
                var s = time - 24 * 3600 * day - 3600 * hour - min * 60;
                var html = '';
                /*if (day > 0) {
                    html += day + 'days';
                }*/
                html += (Array(2).join(0) + hour).slice(-2) + ':' + (Array(2).join(0) + min).slice(-2) + ':' + (Array(2).join(0) + s).slice(-2);
                return (html);
            }
        })
        if ($(window).width() > '768') {
            $(".index_product .product-items  li.product-item").hover(function () {
                $(this).find(".product-item-inner").show();
            }, function () {
                $(this).find(".product-item-inner").hide();
            });
            $("#related-product .product-items  li.product-item,#upsell-product .product-items  li.product-item").hover(function () {
                $(this).find(".product-item-actions").show();
            }, function () {
                $(this).find(".product-item-actions").hide();
            });
            var showdiv = $('#layered-filter-block .filter-options-item');
            if(showdiv.length > 3){
                $('#layered-filter-block .showmore').show();
            }
            showdiv.each(function (index, e) {
                if (index > 3) {
                    $(e).hide();
                }
            })
            $('#layered-filter-block .clickshow').delegate(".thisshow", "click", function () {
                showdiv.show();
                $(this).text('Hide');
                $(this).removeClass('thisshow').addClass('thishide');
            });
            $('#layered-filter-block .clickshow').delegate(".thishide", "click", function () {
                showdiv.each(function (index, e) {
                    if (index > 3) {
                        $(e).hide();
                    }
                })
                $(this).text('Show more');
                $(this).removeClass('thishide').addClass('thisshow');
            });
        }
        $('div.products li.product-item').each(function(){
            var special = $(this).find('.special-price .price-wrapper').data('price-amount');
            var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
            if(special && oldprice && oldprice > special){
                var percent = Math.round((oldprice - special)* 100 /oldprice);
                $(this).find('.product-item-details').append('<div class="prodiscount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>')
            }
        })
        $('.cms-index-index div.item').each(function(){
            var special = $(this).find('.special-price .price-wrapper').data('price-amount');
            var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
            if(special && oldprice && oldprice > special){
                var percent = Math.round((oldprice - special)* 100 /oldprice);
                $(this).append('<div class="prodiscount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>')
            }
        })
        $('div.add-home-5674-topsale .topsale_left').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.img').append('<div class="prodiscount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>');
                 }
             })
         /*****首页折扣显示top-left*******/
         $('div.add-home-5674-topsale .topsale_right').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.img').append('<div class="prodiscount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>');
                 }
        })
        changediscount();
        function changediscount() {
            var prospecial = $('.product-info-main .price-box .special-price .price').text().replace(/[^0-9.]/ig, "");
            var prooldprice = $('.product-info-main .price-box .old-price .price').text().replace(/[^0-9.]/ig, "");
            if (prospecial && prooldprice && prooldprice > prospecial) {
                var propercent = Math.round((prooldprice - prospecial) * 100 / prooldprice);
                $(this).find('.product-info-price .old-price').append('<div class="prodiscount"><span class="num">up to</span><span class="off">'+propercent+'%off</span></div>')
            }
        }    
         if($('.product-info-main .price-box .special-price').length > 0){
              var _price = $('.product-info-main .price-box .special-price .price').eq(0);
         }else{
             var _price = $('.product-info-main .price-box .price').eq(0);
         }
        var _talprice = $('.product-info-main .product-add-form .total .price');
        var _qty = $('#qty')
        function changeprice() {
            if ($('.product-info-main .price-box .special-price').length > 0) {
                var _price = $('.product-info-main .price-box .special-price .price').eq(0);
            } else {
                var _price = $('.product-info-main .price-box .price').eq(0);
            }
            var pricetext = _price.text();
            var talpricetext = _talprice.text();
            var _aprice = pricetext.replace(/[^0-9.]/ig, "");
            if (!talpricetext) {
                _talprice.text(pricetext);
                return;
            }            
            var oldtalprice = talpricetext.replace(/[^0-9.]/ig, "");
            var talprice = (parseFloat(_aprice) * _qty.val()).toFixed(2);
            var reg = new RegExp(oldtalprice, 'i');
            _talprice.text(talpricetext.replace(reg, talprice));
        }
        $('.product-info-main .product-info-price').on('DOMNodeInserted', function () {
            changeprice();
            changediscount();
        })
        _qty.on('input propertychange', function () {           
            changeprice();
        })
        changeprice();  
        $('.qty .control .addQty').on('click', function () {
            var qtyPro = $('#qty')[0];            
            qtyPro.value = parseInt(qtyPro.value) + 1;   
            changeprice();
        })       
        $('.qty .control .reduceQty').on('click', function () {
            var qtyPro = $('#qty')[0];
            if (qtyPro.value < 2) {
                return false;
            } else {
                qtyPro.value = parseInt(qtyPro.value) - 1;
                changeprice();
            }
        })
        $("#form-validate .qty input").on('change', function (e) {
             $('#form-validate').submit();
        });      
        $('.nav_navlist .parent .ptog').click(function(){
            $(this).siblings('.level0').toggleClass('open');
            $(this).siblings('.navlist_menu').toggle();
            $(this).siblings('.navlist_menu').find('h3').click(
                function(){

                    $(this).toggleClass('open');
                    $(this).siblings('.children').toggle();
                   
                });
        });
        //FAQ
        $('dl.faq dt').addClass('cur');
        $('dl.faq').delegate("dt.cur", "click", function () {
            $(this).siblings('dd').slideDown(500);
            $('dl.faq').children('dt.act').removeClass('act').addClass('cur').siblings('dd').slideUp(500);
            $(this).addClass('act').removeClass('cur');
        })
        $('dl.faq').delegate("dt.act", "click", function () {
            console.log('ojoko');
            $(this).siblings('dd').slideUp(500);
            $(this).addClass('cur').removeClass('act');
        })      
    });
    $(document).on("mouseover", '.expander-list .parent .open', function () {
        $(this).text('-');        
        $(this).siblings('a').css({'font-weight': '600'});
        $(this).removeClass('open').addClass('close');
        $(this).parent().siblings('.haschild').slideDown(800);     
    })
    $(document).on('mouseover', '.expander-list .parent .close', function () {
        $(this).text('+');
        $(this).siblings('a').css({'font-weight': 'normal'});   
        $(this).addClass('open').removeClass('close');
        $(this).parent().siblings('.haschild').slideUp(800);
    }) 
    $(window).resize(function () {
        if ($(window).width() > '500') {
            $('.footer-main .links ul').show();
        } else {
            $('.footer-main .links ul').hide();
        }
    });
    /***********设备判断************/
    if (/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))) {
        if (window.location.href.indexOf("?mobile") < 0) {
            try {
                if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                    $(".prev,.next").css({"display": "block"});
                    $(".navlist_menu .row > div").removeClass("nav_col");
                    $(".navlist_menu").removeClass("navpc_menu ");
                    $(".navlist_menu").css({"width": "100%"});
                } else if (/iPad/i.test(navigator.userAgent)) {
                    $(".prev,.next").css({"display": "block"});
                } else {
                    $(".prev,.next").css({"display": "none"});
                    $(".navlist_menu .row > div").addClass("nav_col");
                    $(".navlist_menu").addClass("navpc_menu");
                    nav_show();
                }
            } catch (e) {
            }
        }
    }
    /*****nav*******/
    $(window).load(function () {
        var isIE = /msie/.test(navigator.userAgent.toLowerCase());
        if (isIE) {
            if ($(window).width() <= 771) {
                $(".navlist_menu .row > div").removeClass("nav_col");
                $(".navlist_menu").removeClass("navpc_menu ");
                $(".navlist_menu").css({"width": "100%", "padding-top": "0px", "padding-bottom": "0px"});
            } else if ($(window).width() > 771) {
                $(".navlist_menu .row > div").addClass("nav_col");
                $(".navlist_menu").addClass("navpc_menu");
                nav_show();
            }
            $(window).resize(function () {
                if ($(window).width() <= 771) {
                    $(".navlist_menu .row > div").removeClass("nav_col");
                    $(".navlist_menu").removeClass("navpc_menu ");
                    $(".navigation .nav_navlist i").removeClass('fa-minus').addClass('fa-plus');
                    $(".navigation .nav_navlist i").removeClass('fa-minus').addClass('fa-plus');
                    $(".navlist_menu").css({"width": "100%"});
                    $(".navigation .nav_navlist").css({"display": "none"});
                } else if ($(window).width() > 771) {
                    $(".navlist_menu .row > div").addClass("nav_col");
                    $(".navlist_menu").addClass("navpc_menu");
                    $(".navigation .nav_navlist .navlist_menu").css({"display": "none"});
                    $(".navigation .nav_navlist .navlist_menu ol").css({"display": "none"});
                    $(".navigation .nav_navlist").css({"display": "block"});
                    nav_show();
                }
            });
        } else {
            if (window.matchMedia('(max-width: 771px)').matches) {
                $(".navigation .nav_navlist").css({"display": "block"});
                $(".navlist_menu .row > div").removeClass("nav_col");
                $(".navlist_menu").removeClass("navpc_menu ");
                $(".navlist_menu").css({"width": "100%", "padding-top": "0px", "padding-bottom": "0px"});
            } else if (window.matchMedia('(min-width: 772px)').matches) {
                $(".navlist_menu .row > div").addClass("nav_col");
                $(".navlist_menu").addClass("navpc_menu");
                nav_show();
            }
            $(window).resize(function () {
                if (window.matchMedia('(max-width: 771px)').matches) {
                    $(".navlist_menu .row > div").removeClass("nav_col");
                    $(".navlist_menu").removeClass("navpc_menu ");
                    $(".navigation .nav_navlist i").removeClass('fa-minus').addClass('fa-plus');
                    $(".navigation .nav_navlist i").removeClass('fa-minus').addClass('fa-plus');
                    $(".navigation .nav_navlist").css({"display": "block"});
                    $(".navlist_menu").css({"width": "100%"});
                    $(".navigation .nav_navlist").css({"display": "none"})
                } else if (window.matchMedia('(min-width: 771px)').matches) {
                    $(".navlist_menu .row > div").addClass("nav_col");
                    $(".navlist_menu").addClass("navpc_menu");
                    $(".navigation .nav_navlist .navlist_menu").css({"display": "none"});
                    $(".navigation .nav_navlist .navlist_menu ol").css({"display": "none"});
                    $(".navigation .nav_navlist").css({"display": "block"});
                    nav_show();
                }
            });
        }
    });
    function nav_show() {
        $(".navigation .nav_navlist > li").hover(function () {
            var this_row = $(this).find(".navpc_menu .row > .nav_col");
            var num = $(this).find(".navpc_menu .row").length;
            var navmenu_width = num * (this_row.width()) + 60;
            var maxwidth = $('.container').width();
            $(this).find(".navpc_menu").show();
            $(this).find(".navpc_menu ol").show();
            $(this).find(".navpc_menu").css({"width": navmenu_width, "padding-top": "0px", "padding-bottom": "20px", "max-width": maxwidth});
        }, function () {
            $(this).find(".navpc_menu").hide();
        });
    }
    function move_index(move) {
        var moveLength = move.children('li').eq(0).width(); 
        function gprev() {
            move.stop().animate({left: "-=" + moveLength + "px"}, 300, function () {
                $(this).css({left: "0px"});
                move.children("li:last").after(move.children("li:first"));
            });
        }
        function gnext() {
            move.children("li:first").before(move.children("li:last"));
            move.css({left: "-=" + moveLength + "px"});
            move.stop().animate({left: "0px"}, 300);
        }
        move.siblings('.move').children(".next").click(function (event) {
            gprev();
        });
        move.siblings('.move').children(".prev").click(function (event) {
            gnext();
        });
		
        var isTouch = ('ontouchstart' in window);
        if (isTouch) {
            move.parents(".customer-review").on('touchstart', function (e) {
                var that = $(this);
                var touch = e.originalEvent.changedTouches[0];
                var startX = touch.pageX;
                var startY = touch.pageY;
                $(document).on('touchmove', function (e) {
                    touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
                    var endX = touch.pageX - startX;
                    var endY = touch.pageY - startY;
                    if (Math.abs(endY) < Math.abs(endX)) {
                        if (endX > 10) {
                            $(this).off('touchmove');
                            move.children("li:first").before(move.children("li:last"));
                            move.css({left: "-=" + moveLength + "px"});
                            move.stop().animate({left: "0px"}, 300);
                        } else if (endX < -10) {
                            $(this).off('touchmove');
                            move.stop().animate({left: "-=" + moveLength + "px"}, 300, function () {
                                $(this).css({left: "0px"});
                                move.children("li:last").after(move.children("li:first"));
                            });
                        }
                        return false;
                    }
                });
            });
            $(document).on('touchend', function () {
                $(this).off('touchmove');
            });
        }
    }
});
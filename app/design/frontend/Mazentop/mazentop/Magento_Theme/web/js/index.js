require([
    "jquery"
], function ($) {
    $(function () {        
        move_index($("#hotsale ol"));
        move_index($("#featuredsale ol"));
        move_index($("#bestsale ol"));
        move_index($("#related-product ol"));
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
            if($('.product.data.items').length){
                var Hei = $('.product.data.items').offset().top;

                if($(window).width() > 1280){
                    $('.product.data.items .item.title:first-child').css('left',($(window).width() - 1200)/2 + "px");
                    $('.product.data.items .item.title:nth-child(3)').css('left',($(window).width() - 800)/2 + "px");
                    $('.product.data.items .item.title:nth-child(5)').css('left',($(window).width() - 400)/2 + "px");
                    $('.product.data.items .item.title:nth-child(7)').css('left',($(window).width() - 0)/2 + "px");
                }else{
                    $('.product.data.items .item.title:first-child').css('left',"20px");
                    $('.product.data.items .item.title:nth-child(3)').css('left',"220px");
                    $('.product.data.items .item.title:nth-child(5)').css('left',"420px");
                    $('.product.data.items .item.title:nth-child(7)').css('left',"620px");
                }
                if (parseFloat($(window).scrollTop()) >= Hei) {
                    $('.product.data.items').addClass('fix');
                    $('.product.data.items .item.title').each(function(){
                        $(this).css('position','fixed');
                        $(this).css('top','50px');
                        $(this).css('width','200px');
                    })
                }else{
                    $('.product.data.items').removeClass('fix');
                    $('.product.data.items .item.title').each(function(){
                        $(this).css('position','');
                    })
                }
            }
        });
        $("#back-top").click(function () {
            var speed = 200;/*滑动的速度*/
            $("body,html").animate({scrollTop: 0}, speed);
            return false;
        });
        
        $(".product-options-wrapper .options-list>.admin__field").click(function(){
            $(this)
                .addClass('outline')
                .siblings().removeClass('outline');                
        });
                /*产品倒计时*/
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

/*       产品列表 1加入购物车按钮鼠标移上去显示；2属性筛选*/
        if ($(window).width() > '769') {
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
       /*产品详情中 1加减按钮 数量变化价格也变化 2totalprice*/
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
        /*****详情页折扣显示*******/
         $('div.products li.product-item').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.product-item-details').append('<div class="discount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>')
                 }
             })
         /*****首页折扣显示*******/
         $('div.home-wrapper .item').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.img').append('<div class="discount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>');
                 }
             })
         /*****首页折扣显示top-left*******/
         $('div.add-home-5674-topsale .topsale_left').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.img').append('<div class="discount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>');
                 }
             })
         /*****首页折扣显示top-left*******/
         $('div.add-home-5674-topsale .topsale_right').each(function(){
                var special = $(this).find('.special-price .price-wrapper').data('price-amount');
                var oldprice = $(this).find('.old-price .price-wrapper').data('price-amount');
                if(special && oldprice && oldprice > special){
                     var percent = Math.round((oldprice - special)* 100 /oldprice);
                     $(this).find('.img').append('<div class="discount"><span class="num">'+percent+'%</span><span class="off"> OFF</span></div>');
                 }
        });
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
                return false;
            }
        });
    });
    $(window).resize(function () {
        if ($(window).width() > '500') {
            $('.footer-main .links ul').show();
        } else {
            $('.footer-main .links ul').hide();
        }
    });
    $('.footer-main .links .title').click(function () {
        $(this).toggleClass('active').siblings().removeClass('close');
        $(this).siblings('ul').toggleClass('active').removeClass('close');
    })
    /*pages customer-review-img 左侧分类收缩与展开*/
    $(document).on("mouseover", '.catelog-content-new .parent .open', function () {
        $(this).text('-');        
        $(this).siblings('a').css({'font-weight': '600'});
         $(this).removeClass('open').addClass('close');
        $(this).parent().siblings('.haschild').slideDown(800);
       
    })
    $(document).on('mouseover', '.catelog-content-new .parent .close', function () {
        $(this).text('+');
        $(this).siblings('a').css({'font-weight': 'normal'});   
        $(this).addClass('open').removeClass('close');
        $(this).parent().siblings('.haschild').slideUp(800);
    }) 


    /***********设备判断************/

    if (/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|iPhone|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))) {
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
            /* alert(this_row.width());return*/
            var maxwidth = $('.container').width();
            $(this).find(".navpc_menu").show();
            $(this).find(".navpc_menu ol").show();
            $(this).find(".navpc_menu").css({"width": navmenu_width, "padding-top": "0px", "padding-bottom": "20px", "max-width": maxwidth});
        }, function () {
            $(this).find(".navpc_menu").hide();
        });

    }
    /*图片移动效果,页面加载时触发*/

    function move_index(move) {
        var moveLength = move.children('li').eq(0).width(); /*计算每次移动的长度*/
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

        move.parent('.index_content').siblings(".next").click(function (event) {
            gprev();
        });
        move.parent('.index_content').siblings(".prev").click(function (event) {
            gnext();

        });


        /*移动端触屏*/
        var isTouch = ('ontouchstart' in window);
        if (isTouch) {
            move.parents(".scroll_parents").on('touchstart', function (e) {
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

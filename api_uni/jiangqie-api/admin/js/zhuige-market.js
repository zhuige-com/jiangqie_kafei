/**
 * 追格应用市场
 */

jQuery(document).ready(function ($) {

    /**
         * 加载插件数据
         */
    function load_plugins_market_list() {
        var loading = layer.load();
        $.post("/wp-admin/admin-ajax.php",
            {
                action: 'admin_frontend_call',
                zgaction: 'get_list',
                init: $('.zhuige-param-init').val(),
                cat: $('.zhuige-market-cat.activ').data('id'),
                free: $('.zhuige-market-free.activ').data('free'),
            },
            function (res, status) {
                layer.close(loading);

                if (status != 'success' || !res.success) {
                    return;
                }

                let products = res.data.products;
                let content = '';
                for (let i = 0; i < products.length; i++) {
                    let product = products[i];

                    content += '<div class="zhuige-theme-view">';
                    content += '<div class="zhuige-theme-cover">';
                    content += '<a href="' + product.url + '" target="_blank">';
                    content += '<img src="' + product.cover + '" />';
                    content += '</a>';
                    content += '</div>';
                    content += '<div class="zhuige-theme-info">';
                    content += '<div class="zhuige-theme-text">';
                    content += '<div>';
                    content += '<a href="' + product.url + '" target="_blank" class="theme-title">' + product.title + '</a>';
                    // content += '<text>版本 ' + product.version + '</text>';
                    content += '</div>';
                    content += '<div>';
                    for (let j = 0; j < product['tags'].length; j++) {
                        content += '<span class="zhuige-theme-tag">' + product['tags'][j] + '</span>';
                    }
                    content += '</div>';
                    content += '<div>';

                    if (product['promote'].length > 0) {
                        content += '<text>' + product['promote'] + '</text>';
                    }

                    if (product['free'] == '1') {
                        content += '<strong class="zhui-free">免费</strong>';
                    } else {
                        content += '<text>￥</text>';
                        content += '<strong>' + product['price'] + '</strong>';
                        content += '<cite>原价' + product['original'] + '</cite>';
                    }

                    content += '</div>';
                    content += '</div>';
                    content += '</div>';
                    content += '</div>';
                }

                $('.zhuige-theme-list').append(content);
                $('.zhuige-theme-list').addClass('slide-in');


                if (res.data.categories) {
                    let catlist = '';
                    for (let n = 0; n < res.data.categories.length; n++) {
                        catlist += '<li class="zhuige-market-cat" data-id="' + res.data.categories[n].id + '"><a href="javascript:">' + res.data.categories[n].name + '</a></li>';
                    }
                    $('.zhuige-market-type-ul').append(catlist);
                }

                // if (res.data.notice) {
                //     $('.zhuige-market-ad').html(res.data.notice);
                //     $('.zhuige-market-ad-box').show();
                // }

                if (res.data.ads) {
                    let ads = res.data.ads;
                    content = '';
                    for (let i = 0; i < ads.length; i++) {
                        let element = '';

                        element += '<li style="margin:0;">';
                        element += '<a href="' + ads[i].link + '" target="_blank" title="' + ads[i].title + '">' + ads[i].title + '</a>';
                        element += '</li>';

                        content += element;
                    }
                    $('.zhuige-market-ad-box').show();
                    $('.zhuige-market-ad-ol').append(content);
    
                    if (ads.length > 1) {
                        setInterval(() => {
                            let slideCon = $('.zhuige-market-ad-ol');
                            let slideHeight = slideCon.parent().height();
                            slideCon.stop(true, true).animate({
                                marginTop: (-slideHeight) + "px"
                            },
                            500,
                            function() {
                                $(this).css({
                                    marginTop: "0px"
                                }).find("li:first").appendTo(this);
                            });
                        }, 2000)
                    }
                }
                

            });
    }

    load_plugins_market_list();
    $('.zhuige-param-init').val(0);

    $(document).on('click', '.zhuige-market-cat', function () {
        $('.zhuige-market-cat').removeClass('activ');
        $(this).addClass('activ');

        $('.zhuige-theme-list').empty();
        load_plugins_market_list();
    });

    $(document).on('click', '.zhuige-market-free', function () {
        $('.zhuige-market-free').removeClass('activ');
        $(this).addClass('activ');

        $('.zhuige-theme-list').empty();
        load_plugins_market_list();
    });
});
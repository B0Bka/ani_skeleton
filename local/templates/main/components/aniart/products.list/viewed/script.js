var CatalogProductViewed = (function(App){

    'use strict';

    window.App = App = App || {};

    return {
        //constants
        PARAMS:'',
        PAGINATION:{},

        block:{
            main:'#viewed-items',
            pager:'#catalog_pagination',
            sort:'#catalog_sort',
            product: '.one-news',
            item: '.one-news',
        },
        form:{
        },
        selector:{
            color:'.one-color label',
            size:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            basket: '.one-cat-by'
        },
        favorites: {
            main: '.one-cat-info',
            fav_button_add: '.one-cat-fav',
            product_button_add: '.prod-fav a',
            in_favorite: 'in-favorite',
        },
        events:function(){
            var _app = App,
                _catalog = _app.Catalog,
                _basket = _app.Basket,
                _this = this;

            $(_this.block.pager).on('click', 'a', function(){
                _this.getLoad({
                    sort:$(this).data().sort
                }, function(){
                    _this.initAnimOnScroll();
                });
            });
            $(_app.block.main).on('change', _this.block.sort, function(){
                window.location.href=$(this).find(':selected').val();
            });
            $(_this.block.main).on('click', _this.selector.basket, function(){
                _catalog.getModalSizeContent({
                    id: $(this).closest(_this.block.product).find(_this.selector.color+'[class="checked"]').data().id,
                    elite: $(this).closest(_this.block.product).hasClass('cat-50')
                }, function(){
                    _app.getStyler({object:$(_app.selector.styler)});
                    $(_catalog.modal.size.id).modal('show');
                });
            });
            $(_this.block.main).on('click', _this.selector.color, function(){
                _catalog.selectSibling({
                    id: $(this).data().id,
                    name: $(this).closest(_this.block.product).find('.one-cat-tit a'),
                    img: $(this).closest(_this.block.product).find('.product_img a'),
                    elite: $(this).closest(_this.block.product).hasClass('cat-50')
                });
            });
            $(_app.block.main).on('click', _this.selector.size, function(){

                $(_this.selector.size).removeClass('checked');
                $(this).addClass('checked');

                _catalog.getSize({
                    offer:$(this).data().offer,
                    size:$(this).data().size,
                    inBasket: $(this).data().in_basket,
                    buy:$(_catalog.modal.size.buy)
                });
            });
            $(_app.block.main).on('click', _catalog.modal.size.buy, function(){
                _catalog.getBuy({
                    buy:$(_catalog.modal.size.buy),
                    basket:$(_basket.block.amount),
                    offer:_catalog.CURRENT_OFFER
                });
            });
            //Добавление в избранное

            //избранное в рекомендованых (CatalogProductsListRecomended)
            // $('body').on('click',_this.block.main + ' ' + _this.favorites.fav_button_add, function () {
            //     var __this = this;
            //     var func = '',
            //         product_id = $(__this).closest(_this.block.item).find('.product_list_size label.checked').data('id');
            //
            //     console.log('product_id', product_id);
            //
            //     App.Catalog.getProduct({
            //         id: product_id,
            //         // elite: elite,
            //         handler: 'products.list',
            //         component: _this.PARAMS
            //     }, function () {
            //         if(App.Catalog.SIBLING[product_id].inFavorites == "Y"){
            //             func = 'removeFromFavorite';
            //         } else {
            //             func = 'addToFavorite';
            //         }
            //         App.Catalog.addProduct2FavoriteList( product_id, func, __this);
            //     });
            //
            // });

        },
        init:function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'script.js[CatalogProductViewed] init..'});

            _this.events();
        },
        setTemplate:function(data){
            return this.TEMPLATE = data;
        },
        setParams:function(data){
            return this.PARAMS = data;
        },
        setController:function(data){
            return this.CONTROLLER = data;
        },
        setPagination:function(data){
            return this.PAGINATION = data;
        },
        getPager:function(){
            var _this = this,
                init = _this.PAGINATION.NavRecordCount-_this.PAGINATION.NavPageSize*_this.PAGINATION.NavPageNomer,
                pager = $(_this.block.pager);

            if(init <= 0){
                pager.hide();
                return false;
            }

            if(pager.length){
                pager.find('span').html(_this.PAGINATION.NavPageSize);
                pager.show();
            }
            return true;
        },
        getLoad:function(params, callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main);

            _this.PAGINATION.NavPageNomer++;

            _app.post({
                url:_app.AJAX_DIR+'?'+params.sort,
                data:{
                    handler:'catalog',
                    func:'loadPage',
                    page:_this.PAGINATION.NavPageNomer,
                    componentParams:_this.PARAMS
                }
            }, function(response){
                if(!block.length){
                    alert('block product list not found.. ');
                    return false;
                }
                block.append(response.data.html);
                if(callback && typeof(callback) === 'function'){
                    callback();
                }
                return true;
            });
        },
        getBuy:function(params){
            return params;
        },
        initAnimOnScroll:function(){
            var _this = this;
            if($(_this.block.main).length){
                new AnimOnScroll(document.getElementById('catalog_products_list'), {
                    minDuration : 0.4,
                    maxDuration : 0.7,
                    viewportFactor : 0.2
                });
            }
        }
    };

})(window.App);

$(document).ready(function () {
    CatalogProductViewed.init();
});
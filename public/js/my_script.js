$(document).ready(function () {

    var baseUrl = document.location.origin;
    var token = $('meta[name="csrf-token"]').attr('content');

    function init() {
        $('input[name^=productId]').each(function () {
            totalFunc($(this).val());
        });
        $('#subtotal').text(subtotal());
        //Single product page
        singleTotal(1);
    }

    //Single product page
    $('input#single-productQty').on('change', function () {
        singleTotal($(this).val());
    });

    var singleTotal = function (qty) {
        var total = qty * +$('#single-price').text();
        $('span#single-total').text(Math.round(total * 100) / 100);
    }


    $('input[id^=productQty]').on('change', function () {
        var id = $(this).prop('id').replace("productQty", "");
        totalFunc(id);
        $('#subtotal').text(subtotal());
        $('#nav-total').text(subtotal());
    });

    $(document).on('click', 'button[name^=addFromShop]', function () {
        var id = $(this).val();
        $.post(baseUrl + '/cart/add-to-cart', {
            productId: id,
            isRelated: 0,
            productQty: 1,
            _token: token
        }).done(function (data) {
            $('span#nav-items').text(data.items);
            $('span#nav-total').text(Math.round(100 * parseFloat(data.total)) / 100);
        });
    });

    $('button[name^=delete]').on('click', function () {
        var id = $(this).val();
        var productRow = $(this).closest('div.product-row');
        var isRelated = productRow.find('input[name^=isRelatedProduct]').val();

        productRow.remove();

        var totalAmount = subtotal();
        $.post(baseUrl + '/cart', {
            input: "removeRow",
            productId: id,
            isRelated: isRelated,
            subtotal: totalAmount,
            _token: token
        }).done(function (data) {
            //ToDo redirect to empty Cart
            if (!$('div').is('.product-row')) {
                $.post(baseUrl + '/cart', {input: "emptyCart", _token: token}, function (data) {
                    $("div.product-form").replaceWith(data);
                });
            }
            $('span#nav-items').text(data.items);
            $('span#nav-total').text(data.total);
            $('#subtotal').text(data.total);
        });
    });

    function totalFunc(id) {
        var total = +$('#productQty' + id).val() * (+$('#price' + id).text());
        $('#total' + id).text(Math.round(total * 100) / 100);
    }

    function subtotal() {
        var totalAmount = 0;
        $('span[id^=total]').each(function () {
            totalAmount = Math.round(100 * (totalAmount + parseFloat($(this).text()))) / 100;
        });
        return totalAmount;
    }

    //search
    $('button#nav-search-btn').on('click', function () {

        var keyword = $('input#nav-search').val();
        $.post(baseUrl + '/search', {_token: token, keyword: keyword}, function (data) {

            $('div.modal-body').html(data.html);

            $("#searchModal").modal('toggle');
        });
    });

    //filter
    function propertyFilter() {
        var properties = $('input:checked[id^=filter]').map(function () {
            var dataFilter = {};
            dataFilter.property_value_id = $(this).data('filter');
            return dataFilter;
        }).get();
        var propertiesData =
            $('input[id^=select-property-min-]')
                .filter(function () {
                    return this.value.length !== 0;
                })
                .map(function () {
                    var dataFilter = {};
                    dataFilter.property_id = $(this).data('filter');
                    dataFilter.minValue = $(this).val();
                    return dataFilter;
                }).get();
        if (propertiesData.length > 0) $.merge(properties, propertiesData);

        propertiesData =
            $('input[id^=select-property-max-][value!=""]')
                .filter(function () {
                    return this.value.length !== 0;
                })
                .map(function () {
                    var dataFilter = {};
                    dataFilter.property_id = $(this).data('filter');
                    dataFilter.maxValue = $(this).val();
                    return dataFilter;
                }).get();

        if (propertiesData.length > 0) {
            $.each(properties, function (i, val) {
                var result = $.grep(propertiesData, function (e) {
                    return e.property_id == val.property_id;
                });
                if (result.length > 0) {
                    $.extend(true, val, result[0]);
                    //remove this form propertiesData
                    propertiesData.splice( $.inArray(result[0], propertiesData), 1 );
                }
            });
            $.merge(properties, propertiesData);
        }

        if (properties.length < 1) properties = [];
        var category = $('.active-catalog').data('id');
        $.post(baseUrl + '/filter', {_token: token, properties: properties, category: category}, function (data) {
            var products = $($.parseHTML(data.html)).find('div.product-list');
            console.log($($.parseHTML(data.html)).find('.product-list'));

            $('div.product-list').replaceWith(products);
        });
    }

    $('input[id^=filter], input[id^=select-property-]').on('change', propertyFilter);

    init();
})
;
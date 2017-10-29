$(document).ready(function () {

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
        subtotal();
    });

    $('button[name^=addFromShop]').on('click', function () {
        var CSRF_TOKEN = $('input[name=_token]').val();
        var id = $(this).val();
        console.log(id);
        $.post('cart/add-to-cart', {
            productId: id,
            isRelated: 0,
            productQty: 1,
            _token: CSRF_TOKEN
        }).done(function (data) {
            console.log(data);
            $('span#nav-items').text(data.items);
            $('span#nav-total').text(data.total);
        });
    });

    $('button[name^=delete]').on('click', function () {
        var id = $(this).val();
        var productRow = $(this).closest('div.product-row');
        var isRelated = productRow.find('input[name^=isRelatedProduct]').val();
        productRow.remove();
        var CSRF_TOKEN = $('input[name=_token]').val();
        if ($('div').is('.product-row')) {
            var totalAmount = subtotal();
            $.post('cart/', {
                input: "removeRow",
                productId: id,
                isRelated: isRelated,
                subtotal: totalAmount,
                _token: CSRF_TOKEN
            }).done(function (data) {
                $('span#nav-items').text(data.items);
                $('span#nav-total').text(data.total);
                $('#subtotal').text(data.total);
            });
        } else {
            $.post('cart/', {input: "emptyCart", _token: CSRF_TOKEN}, function (data) {
                $("div.product-form").replaceWith(data);
            });
        }

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

    init();
});
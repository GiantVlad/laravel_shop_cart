$(document).ready(function () {

    function init() {
        $('input[name^=productId]').each(function () {
            totalFunc($(this).val());
        });
        subtotal();
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

    $('button[name^=delete]').on('click', function () {
        var id = $(this).val();
        var productRow = $(this).closest('div.product-row');
        var isRelated = productRow.find('input[name^=isRelatedProduct]').val();
        productRow.remove();
        var CSRF_TOKEN = $('input[name=_token]').val();
        if ($('div').is('.product-row')) {
            if (isRelated > 0) {
                $.post('cart/', {input: "removeRelated", id: id, _token: CSRF_TOKEN});
            }
            subtotal();
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
        $('#subtotal').text('');
        $('span[id^=total]').each(function () {
            $('#subtotal').text(Math.round(100 *
                (+$('#subtotal').text() + parseFloat($(this).text()))
            ) / 100
            );
        });
    }
    init();
});
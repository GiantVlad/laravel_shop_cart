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

    $('button[name^=delete]').on('click', function () {
        var id = $(this).val();
        var productRow = $(this).closest('div.product-row');
        var isRelated = productRow.find('input[name^=isRelatedProduct]').val();
        productRow.remove();
        var CSRF_TOKEN = $('input[name=_token]').val();
        if ($('div').is('.product-row')) {
            var totalAmount = subtotal();
            $('#subtotal').text(totalAmount);
            $.post('cart/', {input: "removeRow", productId: id, isRelated: isRelated, subtotal: totalAmount, _token: CSRF_TOKEN});
            
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
            totalAmount = Math.round( 100 *(totalAmount + parseFloat( $(this).text() )) ) / 100;
        });
        return totalAmount;
    }
    init();
});
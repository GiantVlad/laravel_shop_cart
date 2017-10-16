$(document).ready( function () {

    init ();

    $('input[id^=productQty]').on('change',  function () {
        var id = $( this ).prop('id').replace("productQty", "");
        totalFunc( id );
        subtotal ();
    });

    $('button[name^=delete]').on('click',  function () {
        var id = $( this ).val();
        var productRow = $( this ).closest('div.product-row');
        var isRelated = productRow.find('input[name^=isRelatedProduct]').val();
        productRow.remove();
        var CSRF_TOKEN = $('input[name=_token]').val();
        if ( $('div').is('.product-row') ) {
            if (isRelated > 0) {
                $.post('cart/', {input: "removeRelated", id: id, _token: CSRF_TOKEN});
            }
            subtotal ();
        } else {
            $.post('cart/', { input: "emptyCart", _token: CSRF_TOKEN} , function( data ) {
                $( "div.product-form" ).replaceWith( data );
            });
        }

    });

    function init () {
        $('input[name^=productId]').each( function () {
            totalFunc($(this).val());
        });
        subtotal ();
    }

    function totalFunc( id ) {
        //console.log(key.data);
        var total = +$('#productQty'+id).val() * (+$('#price'+id).text());
        $('#total'+id).text(Math.round(total*100)/100);
    }

    function subtotal () {
        $('#subtotal').text('');
        $('span[id^=total]').each( function () {
            $('#subtotal').text( Math.round( 100 *
                (+$('#subtotal').text() + parseFloat($( this ).text()))
                )/100
            );
        });
    }

});
$(function () {
    var baseUrl = document.location.origin;
    var token = $('meta[name="csrf-token"]').attr('content');
    function readURL(input) {
       if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // We can attach the `fileselect` event to all file inputs on the page
    $(document).on('change', ':file', function () {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        readURL(this);

        input.trigger('fileselect', [label]);
    });

    $(':file').on('fileselect', function (event, label) {
        var input = $(this).parents('.input-group').find(':text');

        if (input.length) input.val(label);
    });


    //For bootstrap confirmation plugin
    $('[data-toggle=confirmation-singleton]').confirmation({
        rootSelector: '[data-toggle=confirmation-singleton]',
        singleton: true,
        container: 'body'
    });

    //******************
    var getProductsInCategory = function (url) {
        //$('div.product-list').css('color', '#dfecf6');
        $('div.product-list').append('<img style="position: absolute; left: 0; top: 0; z-index: 100000;" src="'+baseUrl+'/images/loading.gif" />');

        var id = $('#product-category').val();

        if (!url) url = (baseUrl + '/admin/products/category/' + id);

        $.get(url).done(function (data) {
            $('div.product-list').html(data);
        }).fail(function(data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    }

    $(document).on('change', '#product-category', function() {
        getProductsInCategory(false);
    });
    //Pagination for products with category filter
    $('body').on('click', '.products-pagination .pagination a', function(e) {

        if (!$('#product-category').val()) return;

        e.preventDefault();

        var url = $(this).attr('href');
        getProductsInCategory(url);
        window.history.pushState("", "", url);
    });
    //******************

    //Remove property from Product
    $(document).on('click', '#removeProperty', function() {
        $('div[id^=product-property]').append('<img style="position: absolute; left: 0; top: 0; z-index: 100000;" src="'+baseUrl+'/images/loading.gif" />');

        $.post(baseUrl + '/admin/product/' + $(this).data('product_id') + '/property', {
            value_id: $(this).data('value'),
            _token: token,
            _method: 'delete'
        }).done(function (data) {
            $('div#product-property'+data).remove();
        }).fail(function(data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });

    });
});
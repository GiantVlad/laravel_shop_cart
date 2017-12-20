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
        $('div.product-list').append('<img style="position: absolute; left: 0; top: 0; z-index: 100000;" src="' + baseUrl + '/images/loading.gif" />');

        var id = $('#product-category').val();

        if (!url) url = (baseUrl + '/admin/products/category/' + id);

        $.get(url).done(function (data) {
            $('div.product-list').html(data);
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    }

    $(document).on('change', '#product-category', function () {
        getProductsInCategory(false);
    });
    //Pagination for products with category filter
    $('body').on('click', '.products-pagination .pagination a', function (e) {

        if (!$('#product-category').val()) return;

        e.preventDefault();

        var url = $(this).attr('href');
        getProductsInCategory(url);
        window.history.pushState("", "", url);
    });
    //******************

    //Get product property types modal
    var getPropertyTypes = function () {

        $.get(baseUrl + '/admin/products/property-types/').done(function (data) {
            $('div#propertyModal').html(data);
            $('.selectpicker').selectpicker('refresh');
            $(document).trigger('getPropertyTypesComplete');
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    }

    var getPropertyValues = function (id) {

        $.get(baseUrl + '/admin/products/property/' + id + '/values/').done(function (data) {
            $('div#propertyValues').html(data);
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    }

    //reload property block on edit product page
    var updateProductProperties = function (product_id) {
        $.get(baseUrl + '/admin/product/' + product_id + '/properties/').done(function (data) {
            $('div#propertiesContent').html(data);
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    }

    //Remove property from Product
    $(document).on('click', '#removeProperty', function () {
        $('div[id^=product-property]').append('<img style="position: absolute; left: 0; top: 0; z-index: 100000;" src="' + baseUrl + '/images/loading.gif" />');
        var product_id = $(this).data('product_id');
        $.post(baseUrl + '/admin/product/' + product_id + '/property', {
            value_id: $(this).data('value'),
            _token: token,
            _method: 'delete'
        }).done(function (data) {
            updateProductProperties(product_id);
            getPropertyTypes();
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    });

    $(document).on('click', '#add-property-modal-button', function () {
        var value = $('#propertyTypeValue').val();
        var product_id = $('input#modal-product-id').val();
        var input_type = $('#propertyTypeValue').data('input-type');
        var property_id = $('#propertyTypeValue').data('property-id');
        $.post(baseUrl + '/admin/product/property-type/', {
            property_value: value,
            property_input_type: input_type,
            product_id: product_id,
            property_id: property_id,
            _token: token
        }).done(function (data) {
            $('#propertyModal').modal('hide');
            updateProductProperties(data);
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    });

    //Include property Modal content on Product page
    if ($('#propertyModal').length) {
        getPropertyTypes();
    }

    $(document).on('changed.bs.select', '#propertyType', function () {
        getPropertyValues($(this).val());
    });

    //when modal shown
    $(document).on('show.bs.modal', '#propertyModal', function (e) {
        $('#add-property-modal-button').show();
        var modalProductId = $(e.relatedTarget).data('product-id');
        $(e.delegateTarget).find('div[id^=product-property]').each(function () {
            var removeOptionId = $(this).data('prop-id');
            $('select#propertyType option[value="' + removeOptionId + '"]').remove();
        });
        $('.selectpicker').selectpicker('refresh');
        if ($("select#propertyType option").length > 0) {
            getPropertyValues($('#propertyType').val());
            $(e.currentTarget).find('input#modal-product-id').val(modalProductId);
        } else {
            $('#property-modal-body').html('No available properties');
            $('#add-property-modal-button').hide();
        }
    });

    $(document).on('click', '#new-property-modal-button', function () {
        $('#propertyModal').modal('hide');
        $('#newPropertyModal').modal('show');
    });

    $(document).on('click', '#save-new-property-modal-button', function (e) {
        $.post(baseUrl + '/admin/properties/', {
            property_name: $('input#new-property-name').val(),
            property_priority: $('input#new-property-priority').val(),
            property_type: $('input[type=radio][name=new-property-type]:checked').val(),
            property_value: $('input#new-property-value:required').val(),
            _token: token
        }).done(function (data) {
            $('#newPropertyModal').modal('hide');
            getPropertyTypes();
            $(document).bind('getPropertyTypesComplete', function () {
                $('#propertyModal').modal('show');
            });
        }).fail(function (data) {
            if (data.statusText === "Unauthorized") {
                window.location.href = baseUrl + '/admin/login';
            } else {
                console.log(data);
            }
        });
    });

    $(document).on('click', 'input[type=radio][name=new-property-type]', function () {
        var valueTypeSelector = $('input#new-property-value');
        if (valueTypeSelector.attr('required')) {
            valueTypeSelector.prop('required', false);
            valueTypeSelector.prop('type', 'hidden');
            valueTypeSelector.closest('.form-group').hide();
        } else {
            valueTypeSelector.prop('required', true);
            valueTypeSelector.prop('type', 'text');
            valueTypeSelector.closest('.form-group').show();
        }
    });
});
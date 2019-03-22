$(document).ready(function () {

    var baseUrl = document.location.origin;
    var token = $('meta[name="csrf-token"]').attr('content');

    //search
    /*$('button#nav-search-btn').on('click', function () {

        var keyword = $('input#nav-search').val();
        $.post(baseUrl + '/search', {_token: token, keyword: keyword}, function (data) {

            $('div.modal-body').html(data.html);

            $("#searchModal").modal('toggle');
        });
    });*/

    var orders = {
        $actionSelector: $('select#order-status-actions'),

        init: function () {
            this.$actionSelector.on('change', function (event) {
                event.preventDefault();
                var selector = this;
                if (selector.value) {
                    var id = $('option:selected', selector).data('order-id');
                    $.ajax({
                        method: 'POST',
                        url: baseUrl + '/order/status/',
                        data: {
                            id: id,
                            status: this.value,
                            _token: token,
                            _method: 'PUT'
                        },
                        success: function (data) {
                            if (data === 'redirect_to_cart') {
                                window.location.href = baseUrl + '/cart';
                            }
                            $(selector).closest('.row').find('.order-status').text(selector.value);
                            $(selector).
                                find('option')
                                .remove()
                                .end()
                                .append(
                                    data.html
                                );
                        },
                        error: function (jqXHR, exception) {
                            alert(ajaxError (jqXHR, exception));
                        }
                    });
                }
            })
        }
    }

    orders.init();

    var ajaxError = function (jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connect.\n Verify Network.';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        return msg;
    }

});
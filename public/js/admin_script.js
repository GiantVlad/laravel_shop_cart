$(function () {

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
});
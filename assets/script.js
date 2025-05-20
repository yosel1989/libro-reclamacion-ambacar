jQuery(document).ready(function ($) {
    $('#caldera-form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.post({
            url: caldera_form_ajax.ajax_url,
            data: form.serialize() + '&action=caldera_form_submit',
            success: function (response) {
                alert(response.data);
                form[0].reset();
            }
        });
    });
});

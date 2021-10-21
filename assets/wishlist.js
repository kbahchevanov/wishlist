const $ = require('jquery');

$(document).ready(function() {
    $(document).on('click', '.remove-from-wishlist', function(e) {
        e.preventDefault();
        $('#overlay').show();

        const wishListItemId = $(this).data('id');
        let el = $(this);

        $.ajax({
            url: '/xhr/wishlist_remove',
            data: {id: wishListItemId},
            method: 'POST',
            type: 'json',
            success: function(){
                el.closest('.col').remove();
                $('#overlay').hide();
                $('#info-message-btn').trigger('click');

                if (!$('.col').length) {
                    $('.no-results').removeClass('d-none');
                }
            }
        });
    })
});
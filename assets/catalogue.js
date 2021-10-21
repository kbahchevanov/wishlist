const $ = require('jquery');

$(document).ready(function() {
    let timeout    = null;

    $(document).on('click', '.add-to-wishlist', function (e) {
        e.preventDefault();
        $('#overlay').show();

        const project = $(this).parent().prev().find('h4').text();
        const platform = $(this).prev().find('li').first().text();

        let el = $(this);

        $.ajax({
            url: '/xhr/wishlist_add',
            data: {project: project, platform: platform},
            method: 'POST',
            type: 'json',
            success: function (result) {
                if (result.error) {
                    alert(result.error);
                }

                if (result.item) {
                    $('#overlay').hide();
                    el.prop('disabled', true);
                    $('#info-message-btn').trigger('click');
                }
            }
        });
    });

    $(document).on('click', '.form-check input, .form-check label', function() {
        catalogueSearch(false);
    });

    $(document).on('keyup', '#search', function() {
        window.clearTimeout(timeout);

        timeout = setTimeout(function() {
            catalogueSearch(false);
        }, 500);
    });

    $(document).on('keydown', '#search', function() {
        window.clearTimeout(timeout);
    });

    $(document).on('click', '.page-link', function(e){
        e.preventDefault();

        catalogueSearch($(this).data('page'));
    })

    function catalogueSearch(page) {
        $('#overlay').show();
        $('#results').html('');

        let ajaxData = {};

        const search = $('#search').val().trim();
        if (search.length) {
            ajaxData.search = search;
        }

        const licenses = $("#licenses input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();
        if (licenses.length) {
            ajaxData.licenses = licenses;
        }

        const platforms = $("#platforms input:checkbox:checked").map(function(){
            return $(this).val();
        }).get();
        if (platforms.length) {
            ajaxData.platforms = platforms;
        }

        if (page) {
            ajaxData.page = page;
        }

        $.ajax({
            url: '/xhr/catalogue_search',
            data: ajaxData,
            method: 'POST',
            type: 'json',
            success: function(result){
                $('#results').replaceWith(result.html);
                $('#overlay').hide();
            },
            error: function (){
                console.log('Something is wrong! Please try again!');
            }
        });
    }
});
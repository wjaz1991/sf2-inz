$(function() {
    $(".nav-dropdown").hover(function() {
        $(this).find('ul').stop(true, true).slideDown();
    }, function() {
        $(this).find('ul').stop(true, true).slideUp();
    });
    
    //voting handlers
    $('.vote-up, .vote-down').on('click', function() {
        var that = $(this);
        var type = $(this).parent().find('input[name="type"]').val();
        var entity_type = $(this).parent().find('input[name="entity_type"]').val();
        var entity_id = $(this).parent().find('input[name="entity_id"]').val();

        var request = $.ajax({
            url: Routing.generate('ajax_vote'),
            type: 'post',
            dataType: 'json',
            data: {
                type: type,
                entity_type: entity_type,
                entity_id: entity_id
            }
        });

        request.done(function(data) {
            var element = $('.' + entity_type + '-votes-' + entity_id);
            element.parent().find('.votes-up-count').empty();
            element.parent().find('.votes-down-count').empty();
            
            element.parent().find('.votes-up-count').text(data.up);
            element.parent().find('.votes-down-count').text(data.down);
        })
    });
    
    //comment handling
    $('.comment-trigger').on('click', function(e) {
        e.preventDefault();
        var element = $(this).parent().find('.comments-home');
        if(element.is(':visible')) {
            element.slideUp('slow');
            $(this).text('Show comments');
        } else {
            element.slideDown('slow');
            $(this).text('Hide comments');
        }
    })
});
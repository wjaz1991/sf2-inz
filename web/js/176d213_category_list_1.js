$(function() {
    $('li > ul').each(function() {
        $(this).css('display', 'none');
    });

    $('li').on('click', function(e) {
        e.stopPropagation();

        if($(this).hasClass('expanded')) {
            $(this).find('ul').each(function() {
                $(this).hide('slow');
            });

            $(this).find('li').each(function() {
                $(this).removeClass('expanded');
            });

            $(this).removeClass('expanded');
        } else {
            $(this).find('> ul').each(function() {
                $(this).show('slow');
            });

            $(this).addClass('expanded');
        }
    });
});
$(function() {
    var imagesCount = $('div[id^="auction_images"]').length;

    console.log(imagesCount);

    $('#images-list').find('.form-group div[id^="auction_images_"]').each(function() {
        addDeleteBtn($(this));
    });

    $("#image-add").on('click', function() {
        var imageList = $("#images-list");

        var newWidget = imageList.attr('data-prototype');

        newWidget = $(newWidget.replace(/__name__/g, imagesCount));
        imagesCount++;
        //var newEl = $('<p></p>').html(newWidget);
        imageList.append(newWidget);
        addDeleteBtn(newWidget);
    });

    function addDeleteBtn(element) {
        var button = $('<button class="btn btn-default button-red">Delete</button>');
        element.append(button);

        button.on('click', function() {
            $(this).parent().remove();
        });
    }
});
$(function() {
    $('#auction_endDate').datepicker({
        format: 'dd/mm/yyyy'
    });
    
    $('#auction_startDate').datepicker({
        format: 'dd/mm/yyyy'
    });
    
    tinyMCE.init({
        selector: 'textarea'
    });
    
});
$(function() {
    //if there is a div with the images, initialize gallery
    if($(document).find('.photoset').length) {
        $('.photoset').photosetGrid({
            highresLinks: true,
            gutter: '2px',
            onInit: function(){},
            onComplete: function() {
                var title = $('.auction-title').text();
                $('.photoset').find('a').attr('data-lightbox', 'auction');
                $('.photoset').find('a').attr('data-title', title);
                $('.photoset').find('a').css('display', 'inline-block');
                $('.photoset').attr('style', '');
            }
        });
    }
});
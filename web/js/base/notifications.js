$(function() {
    /*var showPopover = $.fn.popover.Constructor.prototype.show;
    $.fn.popover.Constructor.prototype.show = function() {
        showPopover.call(this);
        if (this.options.showCallback) {
            this.options.showCallback.call(this);
        }
    }*/
    
    //accept and reject friendship ajax actions
    $(document).delegate("button[name='friend_confirm']", 'click', function() {
        var id = $(this).attr('data-id');
        
        var that = $(this);
        
        var request = $.ajax({
            url: Routing.generate('ajax_friend_accept'),
            method: 'post',
            dataType: 'json',
            data: { 
                id: id,
                action: 'accept'
            }
        });
        
        request.done(function(msg) {
            if(msg.error) {
                
            } else {
                $(that).parent().remove();
                
                var reqCount = parseInt($(".requests-count").attr('data-count'));
                
                console.log(reqCount);
                
                if(reqCount >= 1) {
                    $(".requests-count").text(reqCount - 1);
                    $(".requests-count").attr('data-count', reqCount - 1);
                }
                
                if(reqCount - 1 == 0) {
                    $('.pending-requests-trigger').parent().remove();
                }
            }
        })
    });
    
    $(document).delegate("button[name='friend_reject']", 'click', function() {
        var id = $(this).attr('data-id');
        var that = $(this);
        
        var request = $.ajax({
            url: Routing.generate('ajax_friend_accept'),
            method: 'post',
            dataType: 'json',
            data: { 
                id: id,
                action: 'reject'
            }
        });
        
        request.done(function(msg) {
            if(msg.error) {
                
            } else {
                $(that).parent().remove();
                
                var reqCount = parseInt($(".requests-count").attr('data-count'));
                
                console.log(reqCount);
                
                if(reqCount >= 1) {
                    $(".requests-count").text(reqCount - 1);
                    $(".requests-count").attr('data-count', reqCount - 1);
                }
                
                if(reqCount - 1 == 0) {
                    $('.pending-requests-trigger').parent().remove();
                }
            }
        })
    });
    
    //search button click
    $('.search-trigger').on('click', function(event) {
        var element = $('.search-dropdown');
        event.preventDefault();
        if(window.innerWidth > 768) {
            var leftOffset = element.width() / 2 - ($(this).innerWidth() / 2);
            element.css('left', '-' + leftOffset + 'px');
        }
        if(element.is(':visible')) {
            element.addClass('hide');
            $(this).removeClass('nav-btn-clicked');
        } else {
            element.removeClass('hide');
            $(this).addClass('nav-btn-clicked');
        }
        
        $("#nav-searchbar").focus();
    });
    
    //friends request button click
    $('.pending-requests-trigger').on('click', function(event) {
        event.preventDefault();
        var element = $('.requests-dropdown');
        var leftOffset = element.width() / 2 - ($(this).innerWidth() / 2);
        element.css('left', '-' + leftOffset + 'px');
        
        if(element.is(':visible')) {
            $(this).removeClass('nav-btn-clicked');
            element.addClass('hide');
            element.empty();
        } else {
            $(this).addClass('nav-btn-clicked');
            element.removeClass('hide');
            var id = $(this).attr('data-user');
        
            //getting data through ajax
            var request = $.ajax({
                'url': Routing.generate('ajax_pending_friends'),
                'method': 'post',
                'dataType': 'json',
                'data': { 'id': id}
            });

            request.done(function(data){
                var list = $('<ul></ul>', {
                    class: 'list-group requests-results'
                });

                for(var i=0; i<data.length; i++) {
                    var html = '';
                    if(data[i].avatar) {
                        html += "<img class='img-circle' src='" + data[i].avatar + "'>";
                    }
                    html += '<a href="' + data[i].link + '">' + data[i].username + '</a>';
                    html += '<button data-id="' + data[i].id + '" type="submit" name="friend_confirm" class="btn btn-default">Confirm</button>';
                    html += '<button data-id="' + data[i].id + '" type="submit" name="friend_reject" class="btn btn-danger">Reject</button>';

                    $('<li></li>', {
                        class: 'list-group-item',
                        html: html
                    }).appendTo(list);
                }

                list.appendTo('.requests-dropdown');
            });
        }
    });
    
    //searchbar input changes
    $('#nav-searchbar').on('input', function() {
        $('.search-results').empty();
        
        var text = $(this).val();
        
        var request = $.ajax({
            url: Routing.generate('ajax_search'),
            type: 'post',
            dataType: 'json',
            data: {
                text: text
            }
        });
        
        request.done(function(data) {
            $('.search-results').empty();
            
            //if nothing found
            if(!data.users && !data.auctions) {
                $('.search-results').append('<li>No results found.</li>');
            }
            
            //handle found users
            if(data.users) {
                $('.search-results').append('<li><h4>Users</h4></li>');
                for(var i=0; i<data.users.length; i++) {
                    var elem = $('<li class="search-user"></li>');
                    elem.append('<img src="/' + data.users[i].avatar + '">');
                    elem.append('<a href="' + data.users[i].link + '">' + data.users[i].username + '</a>');
                    
                    $('.search-results').append(elem);
                }
            }
            
            //handle found auctions
            if(data.auctions) {
                $('.search-results').append('<li><h4>Auctions</h4></li>');
                for(var i=0; i<data.auctions.length; i++) {
                    var elem = $('<li class="search-auction"></li>');
                    elem.append('<img src="/' + data.auctions[i].image + '">');
                    elem.append('<a href="' + data.auctions[i].link + '">' + data.auctions[i].title + '</a>');
                    
                    $('.search-results').append(elem);
                }
            }
        });
    });
});
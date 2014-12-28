$(function() {
    var showPopover = $.fn.popover.Constructor.prototype.show;
    $.fn.popover.Constructor.prototype.show = function() {
        showPopover.call(this);
        if (this.options.showCallback) {
            this.options.showCallback.call(this);
        }
    }

    //popover for displaying friend requests
    $('.pending-requests').popover({
        'animation': true,
        'content': '<h4>Friend requests</h4>',
        'html': true,
        'placement': 'bottom',
        'trigger': 'click',
        'template': '<div class="popover popover-pending" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
    
    $('.pending-requests').on('show.bs.popover', function() {
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
                class: 'list-group'
            });
            
            for(var i=0; i<data.length; i++) {
                var html = '';
                if(data[i].avatar) {
                    html += "<img class='img-circle' src='" + data[i].avatar + "'>";
                }
                html += data[i].username;
                html += '<button data-id="' + data[i].id + '" type="submit" name="friend_confirm" class="btn btn-default">Confirm</button>';
                html += '<button data-id="' + data[i].id + '" type="submit" name="friend_reject" class="btn btn-danger">Reject</button>';
                
                $('<li></li>', {
                    class: 'list-group-item',
                    html: html
                }).appendTo(list);
            }
            
            list.appendTo('.popover-pending');
        });
    });
    
    //remove popover content on hide
    $('.pending-requests').on('hide.bs.popover', function() {
        $('.popover-pending').find('ul').remove();
    });
    
    //accept and reject friendship ajax actions
    $(document).delegate("button[name='friend_confirm']", 'click', function() {
        var id = $(this).attr('data-id');
        
        var request = $.ajax({
            url: Routing.generate('ajax_friend_accept'),
            method: 'post',
            dataType: 'json',
            data: { id: id }
        });
        
        request.done(function(msg) {
            if(msg.error) {
                
            } else {
                $("button[name='friend_confirm']").parent().remove();
            }
        })
    });
    
    $(document).delegate("button[name='friend_reject']", 'click', function() {
        alert('rejected');
    });
    
    //popover for search function
    $('.popover-search-trigger').popover({
        'animation': true,
        'content': '<h4 class="text-center">Search</h4><select id="e1"><option value="AL">Alabama</option><option value="WY">Wyoming</option></select>',
        'html': true,
        'placement': 'bottom',
        'trigger': 'click',
        showCallback : function() {
            $(".popover-content select").select2({
                //containerCss : {"display":"block"},   
                allowClear: true,
            });
        },
        'template': '<div class="popover popover-search" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content">dsadsadsa</div></div>'
    });
    
    $('#e1').select2();
});
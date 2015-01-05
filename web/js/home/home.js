$(function() {
    function dateToString(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        if(month.toString().length == 1) {
            month = '0' + month;
        }
        var day = date.getDate();
        if(day.toString().length == 1) {
            day = '0' + day;
        }
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        
        var dateString = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
        
        return dateString;
    }
    
    $('.more-trigger').on('click', function() {
        var dateStamp = $('#date-stamp').val();
        var lastCheckDate = new Date(dateStamp);
        var nextLastCheckDate = new Date(dateStamp);
        nextLastCheckDate.setDate(nextLastCheckDate.getDate() - 3);
        
        $('#date-stamp').val(dateToString(nextLastCheckDate));
        var request = $.ajax({
            'url': Routing.generate('home_load_data'),
            type: 'post',
            data: {
                end_date: dateToString(lastCheckDate),
                start_date: dateToString(nextLastCheckDate)
            },
            dataType: 'html'
        });
        
        request.done(function(msg) {
            if(msg && $.trim(msg).length > 0) {
                $('.more-trigger').before(msg);
            } else {
                $('.more-trigger').before('<div class="col-md-12 text-center"><h3>No data left to load</h3></div>');
                $('.more-trigger').fadeOut('slow');
            }
        });
    });
});
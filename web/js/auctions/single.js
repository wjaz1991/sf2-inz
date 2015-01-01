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
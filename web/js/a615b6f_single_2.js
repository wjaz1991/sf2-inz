$(function() {
    $('#auction_endDate').datepicker({
        format: 'dd/mm/yyyy'
    });
    
    tinyMCE.init({
        selector: 'textarea'
    });
});
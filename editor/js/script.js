$(document).ready(function () {
    
    $base = 'https://apps.emcp.com/editor/';
    
    $('select').each(function() {
        
        var selectedValue = $(this).val();

        $(this).html($("option", $(this)).sort(function(a, b) { 
            var arel = parseInt($(a).attr('value'), 10);
            var brel = parseInt($(b).attr('value'), 10);
            return arel == brel ? 0 : arel < brel ? -1 : 1 
        }));
        
        $(this).val(selectedValue);
    });
    
    $('.json-book-selector select').prepend('<option selected="selected">Choose Book</option>');
    $('.json-page-selector select, .html-page-selector select').prepend('<option selected="selected">Choose Page</option>');
    
    $('select').change(function() {
        $(this).parent().parent().submit(); 
    });
    
    $('.json-book-selector').submit(function(event) {
        
        window.location = $base + $('select').val() + "/";
        
        event.preventDefault();
    });
    
    $('.json-page-selector').submit(function(event) {
        
        window.location = $base + $('body').attr('class') + "/json/" + $('.json-page-selector select').val() + "/";
        
        event.preventDefault();
    });
    
    $('.html-page-selector').submit(function(event) {
        
        window.location = $base + $('body').attr('class') + "/html/" + $('.html-page-selector select').val() + "/";
        
        event.preventDefault();
    });
    
});
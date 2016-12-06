$(document).ready(function() {
    
    $deleteCatch = '0';
    
    $(document).on('click', '.delete-link', function(e) {
        
        e.preventDefault();
        
        $id = $(this).attr('id');
        
        if( $deleteCatch == '0' ) {
            $('#deleteLinkModal').modal('show');
            $passedID = $id;
        } else {
            deleteLink($id);
        }
        
    });
    
    $(document).on('click', '.delete-confirm', function() {
        $deleteCatch = '1';
        deleteLink($passedID);
    });
    
    $(document).on('change', '#domain-choice', function() {
        if( $('#domain-choice').val() == '1' ) {
            $('#sub-choice option[value=3]').hide();
        } else {
            $('#sub-choice option[value=3]').show();
        }
    });
    
    $(document).on('click', '.help-redirect-string', function() {
         $('.help-text-redirect-string').fadeIn();
    });
    
    $(document).on('click', '.help-destination-url', function() {
         $('.help-text-destination-url').fadeIn();
    });
    
    $('#string-value').keyup(function() {
        
        $str = $(this).val();
        
        if( $str.match(/ /gi) ){
            $replaced = $(this).val().replace(/ /gi,'');
            $(this).val($replaced);
            
            $('#characterErrorModal').modal('show');
        }
        if( $str.match(/http/gi) ){
            $replaced = $(this).val().replace(/http/gi,'');
            $(this).val($replaced);
            
            $('#characterErrorModal').modal('show');
        }
        if( $str.match(/www./gi) ){
            $replaced = $(this).val().replace(/www./gi,'');
            $(this).val($replaced);
            
            $('#characterErrorModal').modal('show');
        }            
        if( $str.match(/\./gi) ){
            $replaced = $(this).val().replace(/\./gi,'');
            $(this).val($replaced);
            
            $('#characterErrorModal').modal('show');
        }         
        if( $str.match(/#/gi) ){
            $replaced = $(this).val().replace(/#/gi,'');
            $(this).val($replaced);
            
            $('#characterErrorModal').modal('show');
        }

    });
    
    $('[data-toggle="popover"]').popover();

    
});

function deleteLink($id) {
    
    // $('.delete-link#' + $id).parent().parent().fadeOut();
    
    $deleteURL = 'http://apps.emcp.com/redirects/links/delete/' + $id;
    
    $.ajax({
        method: "GET",
        url: $deleteURL,
        async: true
    }).done(function(data) {
        $('.error-handling').html(data);
    }); 
    
}
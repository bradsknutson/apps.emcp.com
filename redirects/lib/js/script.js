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
    
   $('#searchModal form').submit(function(e) {

        e.preventDefault();

        if( isURLP( $('.input-lg').val() ) ) {

            $redir = $('.input-lg').val();
            $redir = $redir.replace(/.*?:\/\//g, "");
            $redir = $redir.replace('www.', "");

            URLprocess($redir);

        } else if ( isURLN( $('.input-lg').val() ) ) {
            
            $redir = $('.input-lg').val();
            $redir = $redir.replace('www.', "");
            URLprocess($redir);

        } else {

            if( $('.input-lg').val() != '' ) {
                window.location.href = '/redirects/search/' + $('.input-lg').val();
            }

        }

    });
    
    $('.toolbox').append('<p class="toolbox-hide">x</p>');
    $(document).on('click','.toolbox-show', function(e) {
        e.preventDefault();
        $('.toolbox').fadeIn();
    });
    
    $(document).on('click','.toolbox-hide', function() {
        $('.toolbox').fadeOut(); 
    });
    
    document.addEventListener("keydown", function(event) {
        if( event.which == '77' ) {
            $('.toolbox').fadeIn();
        }
        if( event.which == '67' ) {
            $('.toolbox').fadeOut();
        }
    });
    
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

function saveIconCheck() {
    if( $('.saveActive').length != '0' ) {
        $('.update-all').fadeIn();
    } else {
        $('.update-all').fadeOut();
    }
}

function updateLink($id,$string,$destination) {
    
    $updateURL = 'http://apps.emcp.com/redirects/includes/update_link.php';
    
    $.ajax({
        method: "POST",
        url: $updateURL,
        async: true,
        data: { 
            id: $id,
            string: $string,
            destination: $destination
        }
    }).done(function(data) {
        $('.error-handling').html(data);
    }); 
    
}

/*********** SEARCH FUNCTIONALITY ***********/
            
function isURLP(s) {
    var regexp = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/
    return regexp.test(s);
}

function isURLN(s) {
    var regexp = /[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/
    return regexp.test(s);
}

function URLprocess(u) {

    $parts = u.split('/');
    $domain_parts = $parts[0];
    $domain_parts = $domain_parts.split('.');
    
    if( $domain_parts.length == 3 ) {
        
        $sub = $domain_parts[0];
        $domain = $domain_parts[1] + '.' + $domain_parts[2];
        $redirect = $parts.join('/').replace($parts[0],"").substring(1);
        
    } else if( $domain_parts.length == 2 ) {
        
        $sub = '';
        $domain = $domain_parts[0] + '.' + $domain_parts[1];
        $redirect = $parts.join('/').replace($parts[0],"").substring(1);
        
    }
    
    window.location.href = '/redirects/find/?sub=' + $sub + '&domain=' + $domain + '&redirect=' + $redirect;

}
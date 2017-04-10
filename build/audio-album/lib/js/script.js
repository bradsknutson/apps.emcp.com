$(document).ready(function() {
    $('.step-2').hide();
    $('.step-3').hide();
    $('.step-4').hide();
    $('.form-step-1 .dialog-options').hide();
    $('.form-step-2 .dialog-options').hide();
    $('.form-step-3 .dialog-options').hide();
});

$(document).on('keypress keyup keydown', 'input#title', function(e) {
    
    if( $('input#ebook').val() != '' ) {
        
        if( e.keyCode != 13 ) {

            $('.step-1-error').fadeOut(function() {
                $(this).remove(); 
            });        

            if( $('input#title').val().length > 0 ) {
                $('.form-step-1 .dialog-options').fadeIn();
            } else {
                $('.form-step-1 .dialog-options').fadeOut();
            }
        }
    } else {
        $('.form-step-1 .dialog-options').fadeOut();
    }
});
$(document).on('keypress keyup keydown', 'input#ebook', function(e) {
    
    if( $('input#title').val() != '' ) {
        
        if( e.keyCode != 13 ) {

            $('.step-1-error').fadeOut(function() {
                $(this).remove(); 
            });        

            if( $('input#ebook').val().length > 0 ) {
                $('.form-step-1 .dialog-options').fadeIn();
            } else {
                $('.form-step-1 .dialog-options').fadeOut();
            }
        }
    } else {
        $('.form-step-1 .dialog-options').fadeOut();
    }
});

$(document).on('submit', '.form-step-1', function(e) {
    e.preventDefault();
    
    $('.ebook-slug').attr('id', $('input#ebook').val() );
    
    $('.step-1-error').fadeOut(function() {
        $(this).remove(); 
    });       
    
    $.ajax({
        method: "POST",
        url: "http://apps.emcp.com/build/audio-album/process-title.php",
        data: { 
            title: $('input[name="title"]').val()
        }
    }).done(function(data) {
        $('.step-1-return').html(data);
    });     
    
})

$(document).on('change', '.form-step-2 input[type="file"]', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

$(document).on('fileselect', '.form-step-2 input[type="file"]', function(event, numFiles, label) {
    var input = $(this).parents('.input-group').find(':text'),
    log = label;

    var extension = label.replace(/^.*\./, '');
    if( extension == 'jpg' || extension == 'png' || extension == 'jpeg' ) {
        if( this.files[0].size < 1000000 ) {
            $('.form-step-2 .dialog-options').fadeIn();
            input.val(log);
        } else {
            $('.form-step-2 .dialog-options').hide();
            input.val('File size too large');
        }
    } else {
        $('.form-step-2 .dialog-options').hide();
        input.val('Must be .jpg or .png');
    }

});

$(document).on('submit', '.form-step-2', function(e) {
    e.preventDefault();
    
    var formData = new FormData();
    formData.append('albumArt', $('input[name="albumArt"]')[0].files[0] );  
    formData.append('albumDirectory', $('input[name="albumDirectory"]').val() );    

    $.ajax({
        url : 'http://apps.emcp.com/build/audio-album/process-image.php',
        type : 'POST',
        data : formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('.loader-container').fadeIn();
        },
        complete: function() {
            
        },
        success: function(data) {
            $('.form-step-2').fadeOut('fast', function() {
                $('.step-2-return').html(data);
            });
            $('.loader-container').fadeOut();
        }
    });
});

$(document).on('click', '.dialog-options .button-submit', function () {
    $(this).parent().parent().find('button[type="submit"]').click();
});

$(document).on('click', '.button-trash', function() {
    $step = $(this).parent().parent().parent().attr('class').split('-')[1];
    $(this).parent().parent().fadeOut('fast', function() {
        if( $step == '2' ) {
            $('.display-album-art-container').empty();   
        } else if( $step == '3' ) {
            $('.display-audio-file-list-container').empty();
        }
        $('.form-step-' + $step).fadeIn();
        $('.form-step-' + $step + ' .dialog-options').hide();
    });
    clearUpload($step);
});

$(document).on('click', '.button-accept', function() {
    $('.step-2').fadeOut('fast', function() {
        $('.step-3').fadeIn(); 
    });
});

function clearUpload($step) {
    $this = $('.form-step-' + $step);
    $this.find('input[type="file"]').val('');
    $this.find('input[type="text"]').val('');
}

$(document).on('click', '.modal-click-style', function() {
    $('#fileDownloadAsModal').modal('show');
});



$(document).on('change', '.form-step-3 input[type="file"]', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

$(document).on('fileselect', '.form-step-3 input[type="file"]', function(event, numFiles, label) {
    var input = $(this).parents('.input-group').find(':text'),
    log = label;

    var extension = label.replace(/^.*\./, '');
    if( extension == 'csv' ) {
        if( this.files[0].size < 500000 ) {
            $('.form-step-3 .dialog-options').fadeIn();
            input.val(log);
        } else {
            $('.form-step-3 .dialog-options').hide();
            input.val('File size too large');
        }
    } else {
        $('.form-step-3 .dialog-options').hide();
        input.val('Must be .csv');
    }

});


$(document).on('submit', '.form-step-3', function(e) {
    e.preventDefault();
    
    var formData = new FormData();
    formData.append('audioFileList', $('input[name="audioFileList"]')[0].files[0] );  
    formData.append('albumDirectory', $('input[name="albumDirectory"]').val() );    

    $.ajax({
        url : 'http://apps.emcp.com/build/audio-album/process-audio-list.php',
        type : 'POST',
        data : formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('.loader-container').fadeIn();
        },
        complete: function() {
            
        },
        success: function(data) {
            $('.form-step-3').fadeOut('fast', function() {
                $('.step-3-return').html(data);
            });
            $('.loader-container').fadeOut();
        }
    });
});

function sortNumber(a,b) {
    return a.sort - b.sort;
}


function processAudioFileList() {
    
    var newJsObj = [];
    
    $('.display-audio-file-list-container .is-table-row').each( function() {
        $sort = $(this).find('div:nth-child(1) input').val();
        $fileName = $(this).find('div:nth-child(2) input').val();
        $trackTitle = $(this).find('div:nth-child(3) input').val();
        
        newJsObj.push({sort : $sort, fileName : $fileName, trackTitle : $trackTitle});
        
    });
    
    newJsObj.sort(sortNumber);
    
    return JSON.stringify( newJsObj );
}



$(document).on('click', '.button-complete', function() {
    $json = processAudioFileList(); 
    $slug = $('.directory-name').attr('id');
    $img = $('.image-name').attr('id');
    $title = $('.album-name').attr('id');
    $ebook = $('.ebook-slug').attr('id');
    
    $.ajax({
        url : 'http://apps.emcp.com/build/audio-album/process-album.php',
        type : 'POST',
        data : {
            albumSlug: $slug,
            albumTitle: $title,
            albumArt: $img,
            albumFiles: $json,
            ebookSlug: $ebook
        },
        beforeSend: function() {
            $('.loader-container').fadeIn();
        },
        complete: function() {
            
        },
        success: function(data) {
            $('.step-3').fadeOut('fast', function() {
                $('.step-4').fadeIn();
                $('.step-4').html(data);
            });
            $('.loader-container').fadeOut();
        }
    });    
    
    
});
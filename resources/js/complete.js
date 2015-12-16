    // AFTER ALL AJAX REQUESTS ARE COMPLETE //
    
    $(document).ajaxStop(function() {
        if( $('body').hasClass('initial') ) {
            
            $('.resource_item.tests, .resource_item.listening_activities_te').each(function() {
                if( $(this).find('.resource-meta-data.book_id').attr('id') == '0' ) {
                    $(this).removeClass('tests listening_activities_te').addClass('textbook_audio');
                }
            });

            $('.resource_item.el_cuarto_misterioso').each(function() {
                $(this).append('<i class="fa fa-play-circle"></i>'); 
            });

            hideLoading();
            $('body').removeClass('initial');
        }
        
    });
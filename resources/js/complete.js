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
            
            new Clipboard('.link-icon');
            
            $favBook = '<li class="favorites_activities collapsed"><div class="resource-cover cover-student_ebook cover-student_ebookl1"><div class="resource_label">Favorites</div></div></li>';            
            
            $('.slice').each(function() {
                
                $lvl = $(this).find('.slice-level').attr('id');
                $unt = $(this).find('.slice-unit').attr('id');
                
                $(this).find('.slidee').prepend('<li class="l' + $lvl + 'u' + $unt + 'favorites favorite-activities-container collapsed"><div class="resource-cover cover-favorites cover-favorites-l' + $lvl + 'u' + $unt + '"><div class="resource_label">Favorites</div></div></li>');
            });
            
            $.each(favArrPre, function(i,v) {
                addToFavorites(v,'1'); 
            });           
            
            $('.frame').sly('reload');
            
        }
        
    });
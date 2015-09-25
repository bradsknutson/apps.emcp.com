$(document).ready(function() {
    
    $('.level-1').addClass('level-selected');
    $('.units-level-1').show();
    $('.slice-level-1').show();
    
    $('.levels>div').click(function() {
        $('.levels>div').removeClass('level-selected');
        $levelSelected = $(this).attr('class').split(' ')[2];
        
        $('.units p').hide();
        $('.units-' + $levelSelected).show();
        
        $('.slice').hide();
        $('.slice-' + $levelSelected).show();
        
        $(this).addClass('level-selected');
        
        $('.frame').sly('reload');
    });
    
    $('.unit-link').click(function(e){
        $unitSelected = $(this).attr('class').split(' ')[1];
        $top = $('.slice.' + $unitSelected).offset().top;
        $("html").stop().scrollTo( { 
            top: $top,
            left: 0
        }, 1000 );
        e.preventDefault();
    });
    
});
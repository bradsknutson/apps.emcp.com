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
    
    $(document).on('click', '.info_icon', function(e) {
        if( $(this).hasClass('toggled') ) {
            $(this).removeClass('toggled');
            $(this).parent().find('.resource_modal_info').hide();
        } else {
            $(this).addClass('toggled');
            $(this).parent().find('.resource_modal_info').show();
        }
        
        e.preventDefault();
    });
    
    $(document).on('click', '.modalClose', function(e) {
        $(this).parent().hide();
        $(this).parent().parent().find('.info_icon').removeClass('toggled');
        e.preventDefault();
    });
    
    $(document).on('click', '.resource_modal_info', function(e) {
        e.preventDefault();
    });

    $(document).scroll(function () {
        var yx = $(this).scrollTop();
        if (yx > 200) {
            $('#totop').css('opacity','1');
        } else {
            $('#totop').css('opacity','0');
        }

    });

    $('#totop').click(function(){$("html").stop().scrollTo( { top:10,left:0} , 1000 );});    
    
});

function lessonRequest(a,b,c,d,e,f) {
    $.ajax({
        url: a,
        async: true,
        type: "POST",
        data: {
            id: b,
            level: c,
            unit: d,
            lesson: f
        }
    }).done(function(data) {
        e.children('.resources').prepend(data); 
    });    
}  
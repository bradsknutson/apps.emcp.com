$(document).ready(function() {
    
    // Constants
    
    $division_all = 'TRUE';
    $division_emc = 'TRUE';
    $division_pes = 'TRUE';
    $division_jist = 'TRUE';

    $emc_link = '<a href="http://www.emcp.com" target="_blank" style="color:#000000;text-decoration:underline;">EMC School</a>';
	// New Brian 8/23/17
	$emc_support_link = '<a href="http://support.emcschool.com" target="_blank" style="color:#000000;text-decoration:underline;">EMC Support</a> 24/7';
	
    $pes_link = '<a href="http://paradigmcollege.com" target="_blank" style="color:#000000;text-decoration:none;">Paradigm Education Solutions</a>';
    $jist_link = '<a href="http://jist.com" target="_blank" style="color:#000000;text-decoration:none;">JIST Career Solutions</a>';
    $all_link = '<a href="http://www.emcp.com" target="_blank" style="color:#000000;text-decoration:none;">EMC School</a>';

    $emc_logo = '<a href="http://www.emcp.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/emc.png" alt="EMC School" /></a>&nbsp;&nbsp;<a href="http://www.emcp.com/zulama" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/zulama.png" alt="EMC School - Zulama" /></a>&nbsp;&nbsp;';
    $pes_logo = '<a href="http://paradigmcollege.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/paradigm.png" alt="Paradigm Education Solutions" /></a>';
    $jist_logo = '<a href="http://jist.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/jist.png" alt="JIST Career Solutions" /></a>';    
    
    $('#division-all').prop('checked', true);
    
    $('#2ndphone').change(function() {
        if ($('#2ndphone').prop('checked')) {
            $('#800num').removeAttr('disabled');   
        } else {
            $('#800num').attr('disabled','disabled');   
        }
    });
    
    $('#division-all').change(function() {
        if ($('#division-all').prop('checked')) {
            $('.uncheck').prop('checked', false);   
        }
    });
    $('#division-emc, #division-pes, #division-jist').change(function() {
        if ($('#division-all').prop('checked')) {  
            $('#division-all').prop('checked', false);   
        }
        if ($('#division-emc').prop('checked') && $('#division-pes').prop('checked') && $('#division-jist').prop('checked') ) {
            $('.uncheck').prop('checked', false);   
            $('#division-all').prop('checked', true);       
        }
    });    
    
    function SelectText(element) {
        var doc = document
            , text = doc.getElementById(element)
            , range, selection
        ;    
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();        
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }  
    
    $('.signature').submit(function(event) {
        
        $salutation = $('#salutation').val();
        $name = '<strong>' + $('#name').val() + '</strong>';
        $title = $('#title').val();
        $email = '<a href="mailto:' + $('#email').val() + '" style="color:#000000;text-decoration:none;">' + $('#email').val() + '</a>';
        $phone = $('#phone').val();
        
        if ($('#2ndphone').prop('checked')) {
            $('.sig-800').html( $('#800num').val() + '<br />').show();               
        } else {
            $('.sig-800').hide();   
        }
        
        if ($('#division-all').prop('checked')) {
            $division_emc = 'TRUE';
            $division_pes = 'TRUE';
            $division_jist = 'TRUE';
            $('.sig-division').html($all_link);
        }        
        
        if ( $('#division-emc').prop('checked') && $('#division-pes').prop('checked') ) {
            $('.sig-division').html($emc_link + ' and ' + $pes_link + '<br />' + $emc_support_link);
            $division_emc = 'TRUE';
            $division_pes = 'TRUE';
            $division_jist = 'FALSE';
        }
        if ( $('#division-emc').prop('checked') && $('#division-jist').prop('checked') ) {
            $('.sig-division').html($emc_link + ' and ' + $jist_link + '<br />' + $emc_support_link);
            $division_emc = 'TRUE';
            $division_pes = 'FALSE';
            $division_jist = 'TRUE';
        }
        if ( $('#division-jist').prop('checked') && $('#division-pes').prop('checked') ) {
            $('.sig-division').html($pes_link + ' and ' + $jist_link);
            $division_emc = 'FALSE';
            $division_pes = 'TRUE';
            $division_jist = 'TRUE';
        }
        
        if ( $('#division-emc').prop('checked') && !$('#division-pes').prop('checked') && !$('#division-jist').prop('checked') ) {
            $('.sig-division').html($emc_link + '<br />' + $emc_support_link);
            $division_emc = 'TRUE';
            $division_pes = 'FALSE';
            $division_jist = 'FALSE';
        }
        if ( $('#division-pes').prop('checked') && !$('#division-emc').prop('checked') && !$('#division-jist').prop('checked') ) {
            $('.sig-division').html($pes_link);
            $division_emc = 'FALSE';
            $division_pes = 'TRUE';
            $division_jist = 'FALSE';
        }
        if ( $('#division-jist').prop('checked') && !$('#division-emc').prop('checked') && !$('#division-pes').prop('checked') ) {
            $('.sig-division').html($jist_link);
            $division_emc = 'FALSE';
            $division_pes = 'FALSE';
            $division_jist = 'TRUE';
        }
        
        
        if ($division_emc == 'FALSE') {
            $('.emc-logo').hide();
        } else {
            $('.emc-logo').show();
        }
        if ($division_pes == 'FALSE') {
            $('.paradigm-logo').hide();
        } else {
            $('.paradigm-logo').show();
        }
        if ($division_jist == 'FALSE') {
            $('.jist-logo').hide();
        } else {
            $('.jist-logo').show();   
        }
        
        $salutation = $salutation.replace(/,/g, '');
        if ($salutation) {
            
            $('.sig-salutation').show();
            $('.sig-salutation').html($salutation + ',<br /><br />');
        } else {
            $('.sig-salutation').hide();
        }
        $('.sig-name').html($name);
        $('.sig-title').html($title);
        $('.sig-email').html($email);
        $('.sig-phone').html($phone);
        
        $('.section-1').hide();
        $('.section-2, .section-3').fadeIn();
        $('table').fadeIn();
        
        SelectText('selectme');
        
        event.preventDefault();
    });
    
    $('.edit').click(function() {
        $('.section-2, .section-3, table').hide();
        $('.section-1').fadeIn(); 
    });
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
});
/**************************************************************/
/***********************DEFAULT VALUES*************************/
/**************************************************************/
var zipcode = '';
var $schoolName = '';
var $issueType = '';
var $issueDescription = '';
var $customerName = '';
var $customerRole = '';
var $customerEmail = '';

/**************************************************************/
/*****************WIZARD PROGRESS NAVIGATION*******************/
/**************************************************************/

$(document).on('click', '.status-step', function() {
    
    if( !$(this).hasClass('disabled') ) {
        $current = $('.form-block').attr('id').split('-')[2];
        $status_step = $(this).attr('class').split(' ')[1].split('-')[2];
        toStep($current,$status_step);        
    }
    
});

/**************************************************************/
/*****************Processing for Step 1************************/
/*******************Organization Type**************************/
/**************************************************************/

$(document).on('click','.type-select',function() {
    $('.type-select').removeClass('selected').addClass('not-selected');
    $(this).addClass('selected').removeClass('not-selected');
    
    $orgType = $(this).attr('id');
    $orgType = $orgType.split('-').join(' ');
    
    $('body').removeClass('no-theme');
    
    if($orgType == 'K 12') {
        $orgType = 'K-12';
        $('body').addClass('emc-theme');
        $('body').removeClass('pes-theme');
        $('body').removeClass('jist-theme');
        $('.org-wording').text('school');
    }
    if($orgType == 'Post Secondary') {
        $('body').addClass('pes-theme');
        $('body').removeClass('emc-theme');
        $('body').removeClass('jist-theme');
        $('.org-wording').text('institution');
    }
    if($orgType == 'Federally Funded') {
        $('body').addClass('jist-theme');
        $('body').removeClass('emc-theme');
        $('body').removeClass('pes-theme');
        $('.org-wording').text('organization');
    }
    
    setFavicon($orgType);
    //console.log('triggered');
    if( $('#to-step-2').hasClass('skip-school') ) {
        $('#back-to-step-3').addClass('halt');
        toStep(1,4);
    } else {
        toStep(1,2);
    }
    
    setTimeout(function() {
        $('.block-step-1 #to-step-2').removeClass('halt');    
    },2000);
    
    loadProductList($orgType, function() {
        loadProductListCallback(function() {
            loadProductListCallbackCallback();
        });
    });
    $('#productInput').val($fieldCustomerPlatform);
    
    $('.status-progress').addClass('progress-1');
    $('.status-step-1').addClass('status-complete');
});


/**************************************************************/
/*****************Processing for Step 2************************/
/************************Zip Code******************************/
/**************************************************************/

// Zip Code API

$(function() {
    // IMPORTANT: Fill in your client key
    var clientKey = 'js-jmeE7RCGsvimtpjTma4b6Jr7U161wS6Qekvmb72DDl06KjZ7t4izNJGHLNIiFP8U';

    var cache = {};
    var container = $('#zip-lookup');
    var errorDiv = container.find('.text-error');

    /** Handle successful response */
    function handleResp(data)
    {
        // Check for error
        if (data.error_msg)
        {
            errorDiv.text(data.error_msg);
            $('.next-step#to-step-3').addClass('halt');   
            $('.form-block').css('height', getBoxHeight(2) ); 

        }
        else 
        {

            $('.next-step#to-step-3').removeClass('halt');  
            $('.form-block').css('height', getBoxHeight(2) ); 

            $zipArray = [];
            $zips = {};
            $zips = data.zip_codes
            var len = $zips.length;


            for( var i = 0; i < len; i++ ) {
                $zipArray[i] = $zips[i].zip_code;
                // console.log($zips[i].zip_code)
            }

            generateSchoolList($zipArray);
        }
    }

    // Set up event handlers
    container.find("input[name='zipcode']").on('keyup', function() {
        errorDiv.empty();
        $('.form-block').css('height', getBoxHeight(2) ); 
        // Get zip code
        zipcode = $(this).val();
        if (zipcode.length == 5 && /^[0-9]+$/.test(zipcode))
        {
            // Clear error
            errorDiv.empty();

            // Check cache
            if (zipcode in cache)
            {
                handleResp(cache[zipcode]);
            }
            else
            {
                // Build url https://www.zipcodeapi.com/rest/<api_key>/radius.<format>/<zip_code>/<distance>/<units>
                var url = "https://www.zipcodeapi.com/rest/"+clientKey+"/radius.json/" + zipcode + "/10/mile";

                // Make AJAX request
                $.ajax({
                    "url": url,
                    "dataType": "json"
                }).done(function(data) {
                    handleResp(data);

                    // Store in cache
                    cache[zipcode] = data;
                }).fail(function(data) {
                    if (data.responseText && (json = $.parseJSON(data.responseText)))
                    {
                        // Store in cache
                        cache[zipcode] = json;

                        // Check for error
                        if (json.error_msg)
                            errorDiv.text(json.error_msg);
                    }
                    else
                        errorDiv.text('Request failed.');
                });
            }
        } else if(zipcode.length < 5 ) {
            $('.next-step#to-step-3').addClass('halt');
        } else {
            $('.next-step#to-step-3').addClass('halt');  
            errorDiv.text('Zip code should be 5 numbers long.');
        }
    }).trigger("change");
});


/**************************************************************/
/*****************Processing for Step 3************************/
/*********************Select School****************************/
/**************************************************************/

// Generates a list of schools that match user entered zip code and organization type
function generateSchoolList($zipArray) {
    
    $('.school-name-container .awesomplete').find('ul').remove();
    $('.school-name-container .awesomplete').find('span').remove();
    
    $('ul#schoolList').html('');
    $schoolList = [];
    
    for(i = 0; i < $schoolJSON.length; i++) { 
        for(x = 0; x < $zipArray.length; x++) {
            if( $schoolJSON[i]['zip'] == $zipArray[x] ) {
                if( $schoolJSON[i]['type'] == $orgType ) {
                    $schoolList.push($schoolJSON[i]['name']);
                }
            }
        }
    }
    
    $schoolList.sort();
    for(y = 0; y < $schoolList.length; y++) {
        $('ul#schoolList').append('<li>' + $schoolList[y] + '</li>');
    }
    
    var input = document.getElementById("schoolInput");
    new Awesomplete(input, {
        list: "#schoolList",
        sort: false
    });  
    
}

$schoolInputCharCount = 0;
$schoolNotFoundInputCharCount = 0;
$(document).on('propertychange change click keyup input paste awesomplete-close', 'input#schoolInput', function(event) {
    
    $schoolInputCharCount++;
    
    $.each( $schoolList, function(i,v) {
        if( v == $('input#schoolInput').val() ) {
            $('#to-step-4').removeClass('halt');
            $schoolName = $('input#schoolInput').val(); 
            return false;
        } else {
            $('#to-step-4').addClass('halt');
            $schoolName = '';
        }
    });
    
    if( $schoolInputCharCount > 10 ) {
        $('.school-not-found').fadeIn(function() {
            $(this).removeClass('halt');
        });
    }
 });
$(document).on('propertychange change click keyup input paste awesomplete-close', 'input#schoolNotFoundInput', function(event) {
    
    $schoolNotFoundInputCharCount++;
    
    $.each( $schoolList, function(i,v) {
        if( v == $('input#schoolNotFoundInput').val() ) {
            $('#to-step-4').removeClass('halt');
            $schoolName = $('input#schoolNotFoundInput').val(); 
            return false;
        } else {
            $('#to-step-4').addClass('halt');
            $schoolName = '';
        }
    });
    
    if( $schoolNotFoundInputCharCount > 10 ) {
        $('.school-still-not-found').fadeIn(function() {
            $(this).removeClass('halt');
        });
    }
 });

$(document).on('click','.school-not-found',function() {
    $('.school-not-found').fadeOut();
    $('.school-name-container').fadeOut(function() {
        $('.school-not-found-info').hide().removeClass('hidden').fadeIn();
        $('.form-block').css('height', getBoxHeight(3) );
    });
    
    
    $('.school-not-found-info .awesomplete').find('ul').remove();
    $('.school-not-found-info .awesomplete').find('span').remove();
    
    $('ul#schoolNotFoundInputList').html('');
    $fullSchoolList = [];
    
    for(i = 0; i < $schoolJSON.length; i++) { 
        $fullSchoolList.push($schoolJSON[i]['name']);
    }
    
    $fullSchoolList.sort();
    for(y = 0; y < $fullSchoolList.length; y++) {
        $('ul#schoolNotFoundInputList').append('<li>' + $fullSchoolList[y] + '</li>');
    }
    
    var inputFull = document.getElementById("schoolNotFoundInput");
    new Awesomplete(inputFull, {
        list: "#schoolNotFoundInputList",
        sort: false
    });     
    
    
});

/**************************************************************/
/*****************Processing for Step 4************************/
/*********************Select Product***************************/
/**************************************************************/
function loadProductList($o,callback) {
    
    //$('#productInput').unbind('change');
    $('#productInput').empty().append('<option value="">Choose Your Product...</option>');
    
    $.getJSON( "/support/lib/js/platforms.json", function( data ) {
        
        $o_id = $o.replace(new RegExp(' ', "g"), '-').toLowerCase();
        $platforms = data;
        
        $.each( data, function( key, val ) {
            $.each( val.types , function( k, v ) {
                // Product Name => val.name
                // Product Type => val.types[k].type
                
                $valNameReadable = val.name.replace(new RegExp(' ', "g"),'-').toLowerCase();
                
                if( val.requireProduct == 'yes' ) {
                    $requireProduct = ' class="require-product" ';
                } else {
                    $requireProduct = '';
                }
                
                if( val.types[k].type == $o ) {
                    if( val.webName == '' ) {
                        $('#productInput').append('<option value="' + val.name + '"' + $requireProduct + '>' + val.name + '</option>');
                    } else {
                        $('#productInput').append('<option value="' + val.name + '"' + $requireProduct + '>' + val.webName + '</option>');
                    }
                }
                
                callback();
                
            });
        });
    });
}

function loadProductListCallback(callback) {
    if( $fieldCustomerPlatform != '' ) {
        $('#productInput').val($fieldCustomerPlatform);
        callback();
    }
}
function loadProductListCallbackCallback() {
    $('#productInput').change();
}

$(document).on('change', 'select#productInput', function(event) {
    
    $drilledProductSelected = '';
    $platformSelected = $('#productInput').val();
    
    if( $platformSelected != '' ) {
        
        if( $('select#productInput option[value="' + $platformSelected + '"]').hasClass('require-product') ) {
            
            $('.next-step#to-step-5').addClass('halt');
            
        } else {
            
            $('.next-step#to-step-5').removeClass('halt');
        }
    
    } else {
        
        $('#productDrilled').parent().fadeOut('fast', function() {
            $('.form-block').css('height', getBoxHeight(4) );
        });
        $('.next-step#to-step-5').addClass('halt');
        
    } 
    
    showProductsFromPlatform($platformSelected, function() {
        resizeAfterProducts();
    }); 
    
    if( $fieldCustomerPlatform == '' ) {
        showTypesByPlatform($platformSelected, function() {
            resizeAfterProducts();
        });
    }
    
});


$(document).on('change', 'select#productDrilled', function(event) {
    
    $drilledProductSelected = $('#productDrilled').val();
    
    if( $('#productInput option:selected').hasClass('require-product') ) {
        if( $('#productDrilled').val() == '' ) {
            $('.next-step#to-step-5').addClass('halt');
        } else {
            $('.next-step#to-step-5').removeClass('halt');
            
            showTypesByPlatform($platformSelected, function() {
                resizeAfterProducts();
            });
               
            toStep('4','5');
        }
    }
    
});

$productsDrilledHTML = '<div class="fa-input preHideFadeIn" style="display:none;"><select id="productDrilled"><option value="">Which of these products are you using...</option></select></div>';

function showProductsFromPlatform($p,callback) {
    
    $.getJSON( "/support/lib/js/products.json", function( data ) {
        
        $('#productDrilled').parent().fadeOut('fast', function() {
            
            $matchingProductCount = 0;
            $(this).remove();
            
            $('.product-container').append($productsDrilledHTML);
        
            $.each( data, function( key, val ) {
                $.each( val.platforms , function( k, v ) {
                    // Product Name => val.name
                    // Product Type => val.type
                    
                    // Product Platforms => val.platforms[k].platform

                    if( val.platforms[k].platform == $p ) {
                        $matchingProductCount++;
                        if( val.webName == '' ) {
                            $('#productDrilled').append('<option value="' + val.name + '">' + val.name + '</option>');
                        } else {
                            $('#productDrilled').append('<option value="' + val.name + '">' + val.webName + '</option>');
                        }
                    }
                });
            });
            
            
            if( $matchingProductCount == 0 ) {
                $('#productDrilled').parent().fadeOut('fast', function() {
                    callback();
                    if( $('#productInput').val() != '' && $fieldCustomerPlatform == '' ) {
                        toStep('4','5');
                    }
                });
            } else {
                $('.preHideFadeIn').fadeIn();
                callback();
            }
        });
        
    });
    
}
function resizeAfterProducts() {
    if( $fieldCustomerPlatform == '' ) {
        $('.form-block').css('height', getBoxHeight(4) );
    }
    
}


/**************************************************************/
/*****************Processing for Step 5************************/
/****************Issue and Contact Info************************/
/**************************************************************/

$(document).on('click', '.cannotFindType', function(e) {
    e.preventDefault();
});

$typeInputHTML = '<div class="fa-input"><select id="typeInput"><option>Select a category...</option></select></div>';
function refreshTypeInput($html) {
    $('.type-container').html($html);
}

$(document).on('click', '.next-step#to-step-5', function() {
    refreshTypeInput($typeInputHTML);
    showTypesByPlatform($('#productInput').val(), function() {
        resizeAfterProducts();
    });
})

function showTypesByPlatform($p,callback) {
    
    refreshTypeInput($typeInputHTML);
    
    $.getJSON( "/support/lib/js/help.json", function( data ) {
        
        $typesJSON = data;
        
        $.each( data, function( key, val ) {
            $.each( val.platforms , function( k, v ) {
                // Issue Type => val.type
                // Issue Platforms => val.platforms[k].platform

                if( val.platforms[k].platform == $p ) {
                    if( val.webName != '' ) {
                        $('#typeInput').append('<option value="' + val.type + '">' + val.webName + '</option>');
                    } else {
                        $('#typeInput').append('<option value="' + val.type + '">' + val.type + '</option>');
                    }
                }

            });
        });
        
        callback();
        
    });
    
}

$(document).on('change', '#typeInput', function() {
    
    step5SubStepsMove('a','b');
    
    $issueType = $(this).val();
});

$(document).on('click', '.cannotFindType', function() {

    $issueType = "I Don't Know/Not Sure";
    
    step5SubStepsMove('a','b');

});

$(document).on('click', '#back-to-step-4', function() {
    
    $currentSubStep = $(this).attr('data-sub-step');
    
    if( typeof $currentSubStep == 'undefined' || $currentSubStep == '' ) {
        $currentSubStep = 'a';
        $nextSubStep = 'back';
    }
    
    if( $currentSubStep == 'a') {
        $nextSubStep = 'back';
    } else if( $currentSubStep == 'b' ) {
        $nextSubStep = 'a';
    } else if( $currentSubStep == 'c' ) {
        $nextSubStep = 'b';
    }
    
    step5SubStepsMove($currentSubStep,$nextSubStep);
    
});
$(document).on('click', '#to-step-6', function() {
    
    $currentSubStep = $('#back-to-step-4').attr('data-sub-step');
    
    if( $currentSubStep == 'a' ) {
        $nextSubStep = 'b';        
    } else if( $currentSubStep == 'b' ) {
        $nextSubStep = 'c';
    } else if( $currentSubStep == 'c' ) {
        $nextSubStep = 'submit';
    }
    
    step5SubStepsMove($currentSubStep,$nextSubStep);
});

function step5SubStepsMove($current,$next) {
    if( $next == 'back' ) {    
        toStep('5','4');
    } else if( $next == 'submit' ) {
        
        $customerName = $('input[name="customerName"]').val();
        $customerRoleVal = $('select.customerRole').val();
        if( $customerRoleVal == 'customerIsEducator' ) { $customerRole = 'Educator'; }
        if( $customerRoleVal == 'customerIsStudent' ) { $customerRole = 'Student'; }
        $customerEmail = $('input[name="customerEmail"]').val();
        
    } else {
        $('.step-5-' + $current).hide().parent().find('.step-5-' + $next).fadeIn(function() {
            $('.form-block').css('height', getBoxHeight(5) );
        });
        $('#back-to-step-4').attr('data-sub-step',$next);
        
        $('.button.submit').addClass('halt')  
        $('#to-step-6').removeClass('halt');  
    }
    
    if( $next == 'c' ) {
        $('#to-step-6').addClass('halt');
        $('.button.submit').removeClass('halt');
    }
    
    if( $current == 'b' ) {
        $issueDescription = $('#issue-customer-text').val();
    }
    
    consoleContactData();
    
}



$(document).on('click', '.button.submit#submit', function() {
        
    $('.comm100-script').html('');
    
    $customerName = $('input[name="customerName"]').val();
    $customerRoleVal = $('select.customerRole').val();
    if( $customerRoleVal == 'customerIsEducator' ) { $customerRole = 'Educator'; }
    if( $customerRoleVal == 'customerIsStudent' ) { $customerRole = 'Student'; }
    $customerEmail = $('input[name="customerEmail"]').val();
    $('.customerEmailInsert').text($customerEmail);
    
    if( $orgType == 'K-12' ) {
        // Chat Channel EMC
        $('.chat-button').attr('id','button-892');
        $('.comm100-script').html($comm100EMC);
    } else {
        // Chat Channel Paradigm (includes JIST)
        $('.chat-button').attr('id','button-891');
        $('.comm100-script').html($comm100Paradigm);
    }
    
    toStep('5','6');
    
});

$(document).on('click', '.chat-button', function() {
    $chatChannel = $(this).attr('id').split('-')[1];
    Comm100API.open_chat_window(event, $chatChannel);
});

/**************************************************************/
/*****************Processing for Step 6************************/
/*****************Chat or Submit Ticket************************/
/**************************************************************/

$(document).on('click', '.submit-options-trigger', function() {
    $(this).fadeOut(function() {
        $('.email-button').addClass('width opacity');
        $('.submit-options').fadeIn(function() {
            $('.form-block').css('height', getBoxHeight(6) ); 
        });
    });
});


$comm100Paradigm = '<!--Begin Comm100 Live Chat Code--><div id="comm100-button-891"></div><script type="text/javascript">var Comm100API=Comm100API||{};(function(t){function e(e){var a=document.createElement("script"),c=document.getElementsByTagName("script")[0];a.type="text/javascript",a.async=!0,a.src=e+t.site_id,c.parentNode.insertBefore(a,c)}t.chat_buttons=t.chat_buttons||[],t.chat_buttons.push({code_plan:891,div_id:"comm100-button-891"}),t.site_id=1000141,t.main_code_plan=891,e("https://ent.comm100.com/chatserver/livechat.ashx?siteId="),setTimeout(function(){t.loaded||e("https://entmax.comm100.com/chatserver/livechat.ashx?siteId=")},5e3)})(Comm100API||{})</script><!--End Comm100 Live Chat Code-->';

$comm100EMC = '<!--Begin Comm100 Live Chat Code--><div id="comm100-button-892"></div><script type="text/javascript">var Comm100API=Comm100API||{};(function(t){function e(e){var a=document.createElement("script"),c=document.getElementsByTagName("script")[0];a.type="text/javascript",a.async=!0,a.src=e+t.site_id,c.parentNode.insertBefore(a,c)}t.chat_buttons=t.chat_buttons||[],t.chat_buttons.push({code_plan:892,div_id:"comm100-button-892"}),t.site_id=1000141,t.main_code_plan=892,e("https://ent.comm100.com/chatserver/livechat.ashx?siteId="),setTimeout(function(){t.loaded||e("https://entmax.comm100.com/chatserver/livechat.ashx?siteId=")},5e3)})(Comm100API||{})</script><!--End Comm100 Live Chat Code-->';





/**************************************************************/
/*********************ON PAGE LOAD*****************************/
/**************************************************************/

$(document).ready(function() {
    
    $('.step-5-b').hide();
    $('.step-5-c').hide();
    
    if( $('.modal-background').attr('data-state') == 'true' ) {
        $('body').attr('id','modal-theme');
    }
    
    // If Organization Type variable is set in URL string - skip step 1
    $fieldOrgType = $('.field-organization-type').attr('id');
    $fieldCustomerSchool = $('.field-customer-school').attr('data-value');
    $fieldCustomerPlatform = $('.field-customer-platform').attr('data-value');
    $fieldCustomerProduct = $('.field-customer-product').attr('data-value');
    $fieldCustomerName = $('.field-customer-name').attr('data-value');
    $fieldCustomerEmail = $('.field-customer-email').attr('data-value');
    $fieldCustomerRole = $('.field-customer-role').attr('data-value');
    
    // Preload Only Org Type Set
    if( $fieldOrgType != '' && $fieldCustomerSchool == '' && $fieldCustomerPlatform == '' ) {
        $orgType = preloadedOrgType($fieldOrgType,'true');
    }
    // Preload Only School Set
    if( $fieldOrgType == '' && $fieldCustomerSchool != '' && $fieldCustomerPlatform == '' ) {
        preloadedSchool($fieldCustomerSchool);
    }
    // Preload Only Platform Set
    if( $fieldOrgType == '' && $fieldCustomerSchool == '' && $fieldCustomerPlatform != '' ) {
        // already handled elsewhere
    }
    
    // Preload Org Type and School Set but not Platform
    if( $fieldOrgType != '' && $fieldCustomerSchool != '' && $fieldCustomerPlatform == '' ) {
        preloadedSchool($fieldCustomerSchool,'true');
        $orgType = preloadedOrgType($fieldOrgType);
    }
    // Preload Org Type and Platform Set but not School
    if( $fieldOrgType != '' && $fieldCustomerSchool == '' && $fieldCustomerPlatform != '' ) {
        $orgType = preloadedOrgType($fieldOrgType);
    }
    // Preload School and Platform Set but not Org Type
    if( $fieldOrgType == '' && $fieldCustomerSchool != '' && $fieldCustomerPlatform != '' ) {
        preloadedSchool($fieldCustomerSchool,'true');
    }
    
    // Preload Org Type, School and Platform Set
    if( $fieldOrgType != '' && $fieldCustomerSchool != '' && $fieldCustomerPlatform != '' ) {
        preloadedSchool($fieldCustomerSchool,'true');
        $orgType = preloadedOrgType($fieldOrgType);
    }
    
    /*
    if( $fieldOrgType != '' ) {
        if( $fieldCustomerSchool != '' ) {
            $orgType = preloadedOrgType($fieldOrgType);
            preloadedSchool($fieldCustomerSchool,'true');
        } else {
            $orgType = preloadedOrgType($fieldOrgType,'true');
        }
    } else {
        if( $fieldCustomerSchool != '' ) {
            preloadedSchool($fieldCustomerSchool,'true');
        } else {
            // Do nothing
        }
    }
    */
    if( $fieldCustomerName != '' ) {
        $('input[name="customerName"]').val($fieldCustomerName);
    }  
    if( $fieldCustomerRole != '' ) {
        $('select.customerRole').val($fieldCustomerRole);
    }  
    if( $fieldCustomerEmail != '' ) {
        $('input[name="customerEmail"]').val($fieldCustomerEmail);
    }
    
    
    
    // Set initial height of modal
    $('.form-block').css('height', getBoxHeight(1) );
    
    $('.school-not-found').addClass('halt');
    $('.school-still-not-found').addClass('halt');
    
});

// Hide loading graphic
$(window).on('load', function() {
    $('.loadingContainer').fadeOut();
});



/**************************************************************/
/**************************TRIGGERS****************************/
/**************************************************************/

// Prevent User From Moving On Without Completing Step
$(document).on('click','.halt',function(e) {
    e.preventDefault();
    return false;
});

// Clicking Next/Back Step
$(document).on('click','.next-step',function(e) {
    
    if( $(this).attr('id') != 'to-step-6' ) {
        if( $(this).hasClass('halt') ) {
            // do nothing
        } else {
            $current = $('.form-block').attr('id').split('-')[2];
            $next = $(this).attr('id').split('-')[2];

            if( $(this).attr('id') == 'to-step-4' ) {
                if( $('#productDrilled > option').length > 1 ) {
                    toStep($current,$next);
                } else {
                    // Skip Step 4
                    toStep('3','5');
                    showTypesByPlatform($platformSelected, function() {
                        resizeAfterProducts();
                    });                    
                }
            } else {
                toStep($current,$next);
            }
        }
    }
    
});
$(document).on('click','.back-step',function(e) {
    if( $(this).attr('id') != 'back-to-step-4' ) {
        $current = $('.form-block').attr('id').split('-')[2];
        $next = $(this).attr('id').split('-')[3];

        toStep($current,$next);
    }
});

// Using Enter Key to Move To Next Step
$(document).keypress(function (e) {
    if (e.which == 13) {
        $current = $('.form-block').attr('id').split('-')[2];
        
        if( $('.block-step-' + $current).find('.button.next-step').hasClass('halt') ) {
            // Do nothing
        } else {
            $('.block-step-' + $current).find('.button.next-step').click();   
        }
    }
});

// Close Modal
$(document).on('click','.close-modal',function() {
    window.parent.postMessage('close', '*'); 
    //window.parent.closeWizard();
});
$(document).on('mousedown','.close-modal', function() {
    $(this).addClass('pressed'); 
});
$(document).on('mouseup','.close-modal', function() {
    $(this).removeClass('pressed'); 
});


/**************************************************************/
/*******************GENERAL USE FUNCTIONS**********************/
/**************************************************************/

// Load schools.json
function loadJSON(callback) {   

    var xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
    xobj.open('GET', '/support/lib/js/schools.json', true); 
    xobj.onreadystatechange = function () {
        if (xobj.readyState == 4 && xobj.status == "200") {
            callback(xobj.responseText);
        }
    };
    xobj.send(null);  
}

// Parse schools.json
function init() {
    loadJSON(function(response) {
        $schoolJSON = JSON.parse(response);
    });
}
init();

// Returns height of elements in modal
function getBoxHeight($step) {
    
    $heightHeader = $('.block-header').outerHeight(true);
    $heightStatus = $('.block-status').outerHeight(true);
    $heightStep = $('.block-step-' + $step).outerHeight(true);
    
    $heightTotal = parseInt($heightHeader) + parseInt($heightStatus) + parseInt($heightStep);
    
    return $heightTotal;
    
}

// Function to transition modal to a specific step
function toStep($current,$next) {
    
    $previous = parseInt($next) - 1;
    $('.status-step-' + $next).removeClass('disabled');
    
    $('.block-step-' + $current).fadeOut(function() {
    
        $('.form-block').css('height', getBoxHeight($next) );
        $('.block-step-' + $next).fadeIn();
        $('.form-block').attr('id', 'current-step-' + $next);
        $('.status-progress').removeClass('progress-1').removeClass('progress-2').removeClass('progress-3').removeClass('progress-4').removeClass('progress-5').addClass('progress-' + $previous);
        
        $('.status-step').removeClass('status-complete')
        
        for(i = $previous; i > 0; i--) {
            $('.status-step-' + i).addClass('status-complete');
            // console.log(i);
        }
        
        $('.block-step-' + $next + ' input').focus();
        
    });
    
    consoleContactData();
    
}

// Set favicon based on Organization Type selected
function setFavicon($o) {
    
    $baseURL = 'http://s2.googleusercontent.com/s2/favicons?domain=';
    
    if( $o == 'Post Secondary' ) {
        $faviconURL = $baseURL + 'paradigmeducation.com';
    } else if ( $o == 'Federally Funded' ) {
        $faviconURL = $baseURL + 'jist.com';
    } else {
        $faviconURL = $baseURL + 'www.emcp.com';
    }
    
    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    link.href = $faviconURL;
    document.getElementsByTagName('head')[0].appendChild(link);
    
}

// JavaScript Client Detection
(function (window) {
    {
        var unknown = '-';

        // screen
        var screenSize = '';
        if (screen.width) {
            width = (screen.width) ? screen.width : '';
            height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }

        // browser
        var nVer = navigator.appVersion;
        var nAgt = navigator.userAgent;
        var browser = navigator.appName;
        var version = '' + parseFloat(navigator.appVersion);
        var majorVersion = parseInt(navigator.appVersion, 10);
        var nameOffset, verOffset, ix;

        // Opera
        if ((verOffset = nAgt.indexOf('Opera')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 6);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Opera Next
        if ((verOffset = nAgt.indexOf('OPR')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 4);
        }
        // Edge
        else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
            browser = 'Microsoft Edge';
            version = nAgt.substring(verOffset + 5);
        }
        // MSIE
        else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(verOffset + 5);
        }
        // Chrome
        else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
            browser = 'Chrome';
            version = nAgt.substring(verOffset + 7);
        }
        // Safari
        else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
            browser = 'Safari';
            version = nAgt.substring(verOffset + 7);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Firefox
        else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
            browser = 'Firefox';
            version = nAgt.substring(verOffset + 8);
        }
        // MSIE 11+
        else if (nAgt.indexOf('Trident/') != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(nAgt.indexOf('rv:') + 3);
        }
        // Other browsers
        else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
            browser = nAgt.substring(nameOffset, verOffset);
            version = nAgt.substring(verOffset + 1);
            if (browser.toLowerCase() == browser.toUpperCase()) {
                browser = navigator.appName;
            }
        }
        // trim the version string
        if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);

        majorVersion = parseInt('' + version, 10);
        if (isNaN(majorVersion)) {
            version = '' + parseFloat(navigator.appVersion);
            majorVersion = parseInt(navigator.appVersion, 10);
        }

        // mobile version
        var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

        // cookie
        var cookieEnabled = (navigator.cookieEnabled) ? true : false;

        if (typeof navigator.cookieEnabled == 'undefined' && !cookieEnabled) {
            document.cookie = 'testcookie';
            cookieEnabled = (document.cookie.indexOf('testcookie') != -1) ? true : false;
        }

        // system
        var os = unknown;
        var clientStrings = [
            {s:'Windows 10', r:/(Windows 10.0|Windows NT 10.0)/},
            {s:'Windows 8.1', r:/(Windows 8.1|Windows NT 6.3)/},
            {s:'Windows 8', r:/(Windows 8|Windows NT 6.2)/},
            {s:'Windows 7', r:/(Windows 7|Windows NT 6.1)/},
            {s:'Windows Vista', r:/Windows NT 6.0/},
            {s:'Windows Server 2003', r:/Windows NT 5.2/},
            {s:'Windows XP', r:/(Windows NT 5.1|Windows XP)/},
            {s:'Windows 2000', r:/(Windows NT 5.0|Windows 2000)/},
            {s:'Windows ME', r:/(Win 9x 4.90|Windows ME)/},
            {s:'Windows 98', r:/(Windows 98|Win98)/},
            {s:'Windows 95', r:/(Windows 95|Win95|Windows_95)/},
            {s:'Windows NT 4.0', r:/(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
            {s:'Windows CE', r:/Windows CE/},
            {s:'Windows 3.11', r:/Win16/},
            {s:'Android', r:/Android/},
            {s:'Open BSD', r:/OpenBSD/},
            {s:'Sun OS', r:/SunOS/},
            {s:'Linux', r:/(Linux|X11)/},
            {s:'iOS', r:/(iPhone|iPad|iPod)/},
            {s:'Mac OS X', r:/Mac OS X/},
            {s:'Mac OS', r:/(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
            {s:'QNX', r:/QNX/},
            {s:'UNIX', r:/UNIX/},
            {s:'BeOS', r:/BeOS/},
            {s:'OS/2', r:/OS\/2/},
            {s:'Search Bot', r:/(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
        ];
        for (var id in clientStrings) {
            var cs = clientStrings[id];
            if (cs.r.test(nAgt)) {
                os = cs.s;
                break;
            }
        }

        var osVersion = unknown;

        if (/Windows/.test(os)) {
            osVersion = /Windows (.*)/.exec(os)[1];
            os = 'Windows';
        }

        switch (os) {
            case 'Mac OS X':
                osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
                break;

            case 'Android':
                osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
                break;

            case 'iOS':
                osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
                break;
        }

        // flash (you'll need to include swfobject)
        /* script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" */
        var flashVersion = 'no check';
        if (typeof swfobject != 'undefined') {
            var fv = swfobject.getFlashPlayerVersion();
            if (fv.major > 0) {
                flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
            }
            else  {
                flashVersion = unknown;
            }
        }
    }

    window.jscd = {
        screen: screenSize,
        browser: browser,
        browserVersion: version,
        browserMajorVersion: majorVersion,
        mobile: mobile,
        os: os,
        osVersion: osVersion,
        cookies: cookieEnabled,
        flashVersion: flashVersion
    };
}(this));

// URL String Preloadd with Organization Type
function preloadedOrgType($orgType,$advance) {

    if( $orgType == 'K-12' || $orgType == 'emc' || $orgType == '1' || $orgType == 'Post-Secondary' || $orgType == 'pes' || $orgType == '2' || $orgType == 'Federally-Funded' || $orgType == 'jist' || $orgType == '3' ) {
        if (typeof $advance !== 'undefined') {
            if( $orgType != '' ) {
                if( $orgType == 'K-12' || $orgType == 'emc' || $orgType == '1' ) {
                    $valOrgType = 'K-12';
                    $('.type-select#K-12').click();
                }
                if( $orgType == 'Post-Secondary' || $orgType == 'pes' || $orgType == '2' ) {
                    $valOrgType = 'Post Secondary';
                    $('.type-select#Post-Secondary').click();
                }
                if( $orgType == 'Federally-Funded' || $orgType == 'jist' || $orgType == '3' ) {
                    $valOrgType = 'Federally Funded';
                    $('.type-select#Federally-Funded').click();
                }
            }       
        } else {
            $('body').removeClass('no-theme');

            $('.type-select').removeClass('selected').addClass('not-selected');
            if( $orgType == 'K-12' || $orgType == 'emc' || $orgType == '1' ) {
                $valOrgType = 'K-12';
                $('.type-select#K-12').click();
            }
            if( $orgType == 'Post-Secondary' || $orgType == 'pes' || $orgType == '2' ) {
                $valOrgType = 'Post Secondary';
                $('.type-select#Post-Secondary').click();
            }
            if( $orgType == 'Federally-Funded' || $orgType == 'jist' || $orgType == '3' ) {
                $valOrgType = 'Federally Funded';
                $('.type-select#Federally-Funded').click();
            }

            loadProductList($orgType, function() {
                loadProductListCallback(function() {
                    loadProductListCallbackCallback();
                });
            });
            $('#productInput').val($fieldCustomerPlatform);

        }
        return $valOrgType;
    } else {
        return;   
    }
}

// URL String Preloaded with School Name
function preloadedSchool($schoolName,$advance) {
    
    $('input#schoolInput').val($fieldCustomerSchool);
    
    $('#to-step-2').addClass('skip-school');
    
    if (typeof $advance !== 'undefined') {
        toStep('1','4')
    }
}

// Print Data to Console for Testing
function consoleContactData() {
    
    $data = 'Contact Data\n--------------\n';
    
    if( $customerName != '' ) {
        $data += 'Name: ' + $customerName + '\n';
        $('input[name="Name"]').val($customerName);
    } else if( typeof $fieldCustomerName !== 'undefined' && $fieldCustomerName != '' ) {
        $data += 'Name: ' + $fieldCustomerName + '\n';
        $('input[name="Name"]').val($fieldCustomerName);
    }
    
    if( $customerRole != '' ) {
        $data += 'Role: ' + $customerRole + '\n';
        $('input[name="Role"]').val($customerRole);
    } else if( typeof $fieldCustomerRole !== 'undefined' && $fieldCustomerRole != '' ) {
        $data += 'Role: ' + $fieldCustomerRole + '\n';
        $('input[name="Role"]').val($fieldCustomerRole);
    }
    
    if( $customerEmail != '' ) {
        $data += 'Email: ' + $customerEmail + '\n';
        $('input[name="Email"]').val($customerEmail);
    } else if( typeof $fieldCustomerEmail !== 'undefined' && $fieldCustomerEmail != '' ) {
        $data += 'Email: ' + $fieldCustomerEmail + '\n';
        $('input[name="Email"]').val($fieldCustomerEmail);
    }
    
    if( $schoolName != '' ) {
        $data += 'School Name: ' + $schoolName + '\n';
        $('input[name="Institute"]').val($schoolName);
    } else if( typeof $fieldCustomerSchool !== 'undefined' && $fieldCustomerSchool != '' ) {
        $data += 'School Name: ' + $fieldCustomerSchool + '\n';
        $('input[name="Institute"]').val($fieldCustomerSchool);
    }
    
    if( typeof $platformSelected != 'undefined' ) {
        $data += '\nProduct Information\n--------------\nPlatform: ' + $platformSelected + '\n';
        $('input[name="Platform"]').val($platformSelected);
    } else if( $fieldCustomerPlatform != '' ) {
        $data += '\nProduct Information\n--------------\nPlatform: ' + $fieldCustomerPlatform + '\n';
        $('input[name="Platform"]').val($fieldCustomerPlatform);
    }
    if( typeof $drilledProductSelected !== 'undefined' && $drilledProductSelected != '' ) {
        $data += 'Product: ' + $drilledProductSelected + '\n';
        $('input[name="Textbook"]').val($drilledProductSelected);
    }
    if( $issueType != '' ) {
        $data += 'Issue Type: ' + $issueType + '\n';
        $('input[name="Type"]').val($issueType);
    }
    if( $issueDescription != '' ) {
        $data += 'Comments: ' + $issueDescription + '\n';
        $('input[name="Description"]').val($issueDescription);
    }
    
    
    $data += '\nUser Machine Information\n--------------\n';
    $data += 'Operating System: ' + jscd.os + ' ' + jscd.osVersion + '\n';
    $data += 'Browser : ' + jscd.browser + ' ' + jscd.browserMajorVersion + '\n';
    
    $data += '\n\n';
    
    console.log($data);
    return $data;
}
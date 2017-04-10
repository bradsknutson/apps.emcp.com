<?php

    $org_type = $_GET['type'];
    $customerName = $_GET['name'];
    $customerEmail = $_GET['email'];
    $customerRole = $_GET['role'];
    $customerSchool = $_GET['school'];
    $customerPlatform = $_GET['platform'];
    $customerProduct = $_GET['product'];
    $modalOption = $_GET['modal'];
    if( $modalOption == 'true' ) {
        $modalValue = 'true';
    } else {
        $modalValue = 'false';
    }
    $baseico = 'http://s2.googleusercontent.com/s2/favicons?domain=';

    if ( $org_type == 'jist' ) {
        $favico = $baseico . 'jist.com';
        $orgSpecificTitle = 'JIST Career Solutions ';
        $orgSpecificTeacher = 'Educator';
    } else if ( $org_type == 'pes' ) {
        $favico = $baseico . 'paradigmeducation.com';
        $orgSpecificTitle = 'Paradigm Education Solutions ';
        $orgSpecificTeacher = 'Instructor';
    } else if ( $org_type == 'emc' ) {
        $favico = $baseico . 'www.emcp.com';
        $orgSpecificTitle = 'EMC School ';
        $orgSpecificTeacher = 'Educator';
    } else {
        $favico = $baseico . 'www.emcp.com';
        $orgSpecificTitle = '';
        $orgSpecificTeacher = 'Educator';
    }
    include('includes/header.php');

?>
<div class="form-container">
    
    <div class="form-block transition col-md-6 col-md-offset-3" id="current-step-1">
        <div class="block-header transition">
            <h4>Contact <span class="organization-specific-title"><?php echo $orgSpecificTitle; ?></span>Technical Support</h4>
            <div class="close-modal">Close</div>
        </div>
        <div class="block-status">
            <div class="status-container">
                <div class="status-line">
                    <span class="status-progress transition"></span>
                </div>
                <div class="status-step status-step-1">
                    <i class="fa fa-home transition" aria-hidden="true"></i>
                </div>
                <div class="status-step status-step-2 disabled">
                    <i class="fa fa-globe transition" aria-hidden="true"></i>
                </div>
                <div class="status-step status-step-3 disabled">
                    <i class="fa fa-graduation-cap transition" aria-hidden="true"></i>
                </div>
                <div class="status-step status-step-4 disabled">
                    <i class="fa fa-info transition" aria-hidden="true"></i>
                </div>
                <div class="status-step status-step-5 disabled">
                    <i class="fa fa-check transition" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        
        
        <!-- STEP 1 - CHOOSE ORGANIZATION TYPE -->
        <div class="block-content block-step-1 transition">
            <div id="org-type-select" class="row-fluid">
                <p class="text-center">Choose your school or organization type.</p>
                
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="type-select transition" id="K-12">K-12</div>
                    </div>
                    <div class="col-md-4">
                        <div class="type-select transition" id="Post-Secondary">Post Secondary</div>
                    </div>
                    <div class="col-md-4">
                        <div class="type-select transition" id="Federally-Funded">Career Services</div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="button next-step halt transition" id="to-step-2">Next</div>
                <div class="clearfix"></div>
            </div>  
        </div>
        
        <!-- STEP 2 - ENTER ZIP CODE -->
        <div class="block-content block-step-2 transition">
            <div id="zip-lookup" class="row-fluid">
                <div class="zip-code-container">
                    <p class="text-center">Enter your zip code.</p>
                    <div class="fa-input">
                        <input type="text" name="zipcode" placeholder="Zip Code" autocomplete="off" >
                    </div>
                    <p class="text-error"></p>
                </div>
                
                <div class="button back-step transition" id="back-to-step-1">Back</div>
                <div class="button next-step halt transition" id="to-step-3">Next</div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- STEP 3 -  -->
        <div class="block-content block-step-3 transition">
            <div id="school-lookup" class="row-fluid">
                <p class="text-center">Enter the name of your <span class="org-wording">school</span>.</p>
                <div class="school-name-container">
                    <div class="fa-input">
                        <input type="text" name="schoolName" placeholder="School/Organization Name" id="schoolInput" />
                    </div>
                    <ul id="schoolList" class="hidden"></ul>
                    <p>Start typing your school name. We've narrowed down our list of schools based on your zip code.</p>
                </div>
                
                <p class="school-not-found text-center cursor-pointer"><i class="fa fa-info transition" aria-hidden="true"></i> Can't find your school?</p>
                
                <div class="school-not-found-info hidden">
                    <div class="fa-input">
                        <input type="text" name="schoolNameNotFound" placeholder="School/Organization Name" id="schoolNotFoundInput" />
                    </div>
                    <ul id="schoolNotFoundInputList" class="hidden"></ul>
                    <p>Try typing in your school name again. We've expanded our search to include more schools.</p>
                    <p class="school-still-not-found text-center cursor-pointer"><i class="fa fa-info transition" aria-hidden="true"></i> Still can't find your school?</p>
                </div>
                
                <div class="school-still-not-found-info hidden">
                    <input type="text" name="schoolStillNotFound" placeholder="School/Organization Name" id="schoolStillNotFoundInput" />
                </div>
                
                <div class="hidden">
                    <input type="text" name="organizationName" id="organizationName" />
                </div>

                <div class="button back-step transition" id="back-to-step-2">Back</div>
                <div class="button next-step halt transition" id="to-step-4">Next</div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- STEP 4 -  -->
        <div class="block-content block-step-4 transition">
            <div id="product-lookup" class="row-fluid">
                <p class="text-center">What product are you using?</p>
                <div class="product-container">
                    <div class="fa-input">
                        <select id="productInput">
                            <option>Choose Your Product...</option>
                        </select>
                    </div>
                    <div class="fa-input hidden">
                        <select id="productDrilled">
                            <option>Which of these products are you using...</option>
                        </select>
                    </div>
                </div>

                <div class="button back-step transition" id="back-to-step-3">Back</div>
                <div class="button next-step halt transition" id="to-step-5">Next</div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- STEP 5 -  -->
        <div class="block-content block-step-5 transition">
            <div id="type-lookup" class="row-fluid">
                <div class="step-5-a">
                    <p class="text-center">What type of help are you looking for? Select from the dropdown list below.</p>

                    <div class="type-container"></div>

                    <p class="text-center"><a href="#" class="cannotFindType">Can't find what you're looking for? <i class="fa fa-question-circle" aria-hidden="true"></i></a></p>
                </div>
                <div class="step-5-b">
                    <p class="text-center">To better assist you, please describe what issue you are having below.</p>
                    
                    <textarea id="issue-customer-text"></textarea>
                    
                </div>
                <div class="step-5-c">
                    <p class="text-center">The last thing we need is your contact information.</p>
                    
                    <div class="customer-name-container">
                        <div class="fa-input">
                            <input type="text" name="customerName" placeholder="First and Last Name" autocomplete="off" >    
                        </div>
                    </div>
                    
                    <div class="customer-role-container">
                        <div class="fa-input">
                            <select class="customerRole">
                                <option value="">Are you a Student or Educator?</option>
                                <option value="Student">Student</option>
                                <option value="Educator">Educator</option>
                            </select>
                        </div>
                    </div>

                    <div class="customer-email-container">
                        <div class="fa-input">
                            <input type="text" name="customerEmail" placeholder="Email Address" autocomplete="off" >
                        </div>
                    </div>
                </div>

                <div class="button back-step transition" id="back-to-step-4">Back</div>
                <div class="button next-step halt transition" id="to-step-6">Next</div>
                <div class="button submit halt transition" id="submit">Submit</div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- STEP 6 -  -->
        <div class="block-content block-step-6 transition">
            <div id="submit-form" class="row-fluid">
                <p class="text-center">How would you like to contact Technical Support?</p>
                
                <div class="chat-button-wrapper">
                    <div class="chat-button transition">
                        <i class="fa fa-comments-o" aria-hidden="true"></i>
                        <p class="chat-button-text">Chat Now</p>
                    </div>
                    <div class="email-button transition">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <p class="chat-button-text">Submit</p>
                    </div>
                    
                    <div class="clearfix"></div>
                    <p class="text-center submit-options-trigger">Other options</p>
                </div>
                
                <div class="submit-options">
                    <p>The fastest way to get help is to use our live chat service.  If you would rather to submit your information, a Technical Support Specialist will contact you at <span class="customerEmailInsert"></span>.  If this is not the best email to contact you at, please <span class="back-step email-go-back" id="back-to-step-5">go back</span> and update it before submitting your information.</p>
                </div>

                <div class="button back-step transition" id="back-to-step-5">Back</div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="hidden">
    <div class="modal-background" data-state="<?php echo $modalValue; ?>"></div>
    <div class="field-organization-type" id="<?php echo $org_type; ?>"></div>
    <div class="field-customer-name" data-value="<?php echo $customerName; ?>"></div>
    <div class="field-customer-email" data-value="<?php echo $customerEmail; ?>"></div>
    <div class="field-customer-role" data-value="<?php echo $customerRole; ?>"></div>
    <div class="field-customer-school" data-value="<?php echo $customerSchool; ?>"></div>
    <div class="field-customer-platform" data-value="<?php echo $customerPlatform; ?>"></div>
    <div class="field-customer-product" data-value="<?php echo $customerProduct; ?>"></div>
</div>

<div class="comm100-vars">
    <input type="text" class="Name" id="Name" name="Name" value="" />
    <input type="text" class="Email" id="Email" name="Email" value="" />
    <input type="text" class="Role" id="Role" name="Role" value="" />
    <input type="text" class="Institute" id="Institute" name="Institute" value="" />
    <input type="text" class="Platform" id="Platform" name="Platform" value="" />
    <input type="text" class="Textbook" id="Textbook" name="Textbook" value="" />
    <input type="text" class="Type" id="Type" name="Type" value="" />
    <input type="text" class="Description" id="Description" name="Description" value="" />
</div>

<div class="comm100-script"></div>

<?php

    include('includes/footer.php');

?>
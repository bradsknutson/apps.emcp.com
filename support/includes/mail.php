<?php

//if "email" variable is filled out, send email
  if (isset($_POST['email']))  {

      //Email information
      $admin_email = "support@emcp.com";

      $email = $_POST['email'];
      $notes = $_POST['notes'];
      $subject = "New Technical Support Form Submittal";
      

      //send email
      mail($admin_email, "$subject", $notes, "From:" . $email);

      //Email response
      echo "Thank you for contacting us!";
  }
  
  //if "email" variable is not filled out, don't send an email
  else  {
      
  }
?>
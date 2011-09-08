<?php

require_once( '../../../wp-load.php' );

$mailto   = (epanel_option('custom_email')) ? epanel_option('custom_email') : epanel_option('custom_email');
$name     = ucwords($_POST['name']); 
$subject  = $_POST['subject'];
$email    = $_POST['email'];
$message  = $_POST['message'];

	if(strlen($_POST['name']) < 1 ){
		echo  'email_error';
	}
	
  else if(strlen($email) < 1 ) {
		echo 'email_error';
	}

  else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", $email)) {
    echo 'email_error';
  }

	else if(strlen($message) < 1 ){
		echo 'email_error';

  } else {

	// NOW SEND THE ENQUIRY

	$email_message="\n\n" .
		"Name: " .
		ucwords($name) .
		"\n" .
		"Email: " .
		$email .
		"\n" .
		"Comments: " .
		"\n" .
		$message .
		"\n" .
		"\n\n" ;

		$email_message = trim(stripslashes($email_message));
		mail($mailto, $subject, $email_message, "From: \"$name\" <".$email.">\nReply-To: \"".ucwords($name)."\" <".$email.">\nX-Mailer: PHP/" . phpversion() );

}
?>
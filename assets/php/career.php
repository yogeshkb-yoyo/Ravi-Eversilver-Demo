<?php
header('Content-type: application/json');
if($_POST)
{
    $to_email       = "connectospark@gmail.com";
   
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
       
        $output = json_encode(array( //create JSON data
            'type'=>'error',
            'text' => 'Sorry Request must be Ajax POST'
        ));
        die($output); //exit script outputting json data
    }
    //Sanitize input data using PHP filter_var().
    $user_name      = filter_var($_POST["name"],    FILTER_SANITIZE_STRING);
    $user_email     = filter_var($_POST["email"],   FILTER_SANITIZE_EMAIL);
    $phone_number   = filter_var($_POST["phone"],   FILTER_SANITIZE_NUMBER_INT);
    $qualifaction   = filter_var($_POST["qualification"],    FILTER_SANITIZE_STRING);
    $college		= filter_var($_POST["college"],    FILTER_SANITIZE_STRING);
    $experience   	= filter_var($_POST["experience"],    FILTER_SANITIZE_STRING);
    $skills        	= filter_var($_POST["skills"], FILTER_SANITIZE_STRING);	

    //additional php validation
    if(strlen($user_name)<4){ // If length is less than 4 it will output JSON error.
        $output = json_encode(array('type'=>'error', 'text' => 'Name is too short or empty!'));
        die($output);
    }

    if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){ //email validation
        $output = json_encode(array('type'=>'error', 'text' => 'Please enter a valid email!'));
        die($output);
    }

    if(!filter_var($phone_number, FILTER_SANITIZE_NUMBER_FLOAT)){ //check for valid numbers in phone number field
        $output = json_encode(array('type'=>'error', 'text' => 'Enter only digits in phone number'));
        die($output);
    }

	$info = pathinfo($_FILES['resume']['name']);
	$ext = $info['extension']; // get the extension of the file
	$newname = $user_name.rand(1,4).'.'.$ext; 
	$path= $_SERVER['DOCUMENT_ROOT'] . '/puvan/resumes/';
		$target = $path.$newname;
        if(!move_uploaded_file( $_FILES['resume']['tmp_name'], $target)) {
            echo "Sussecfully uploaded your file.";
        }
    
    //email subject
    $subject ='New mail via contact form';

    //email body
    $message_body = $skills."\r\n\r\n-".$user_name."\r\n\r\nEmail : ".$user_email."\r\nPhone Number : ". $phone_number
					."\r\n\r\nQualification : ".$qualifaction."\r\n\r\nCollege : ".$college."\r\n\r\nExperience : ".$experience;
   
    //proceed with PHP email.
    $headers = 'From: '.$user_name.'<'.$user_email.'>'."\r\n" .
    'Reply-To: '.$user_name.'<'.$user_email.'>' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
   
    $send_mail = mail($to_email, $subject, $message_body, $headers);
   
    if(!$send_mail)
    {
        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error', 'text' => 'Could not send mail! Please check your PHP mail configuration.'));
        die($output);
    }else{
        $output = json_encode(array('type'=>'success', 'text' => 'Hi '.$user_name .', thank you for your email, we will get back to you shortly.'));
        die($output);
    }
}


?>

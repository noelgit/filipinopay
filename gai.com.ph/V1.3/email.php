<?php

// $email and $message are the data that is being
// posted to this page from our html contact form
$name = $_REQUEST['name'];
$email = $_REQUEST['email'] ;
$message = $_REQUEST['message'] ;

/*if (isset($_POST['name'])) {
		$name = $_POST['name'];
		echo $name;
}
else
{
	echo 'no name';
}	
		if (isset($_POST['email'])) {
		$name = $_POST['email'];
}		
		if (isset($_POST['message'])) {
		$name = $_POST['message'];
}*/

// When we unzipped PHPMailer, it unzipped to
// public_html/PHPMailer_5.2.0
// include our OAuth2 Server object
require_once __DIR__.'/server.php'; 

$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
require 'vendor/autoload.php'; 

//EMAIL
	
	try {
		
		$mail->isSMTP();                               // Set mailer to use SMTP
		$mail->Host = MAIL_HOST;  					   // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                        // Enable SMTP authentication
		$mail->Username = MAIL_USERNAME;               // SMTP username
		$mail->Password = MAIL_PASSWORD;               // SMTP password
		$mail->SMTPSecure = 'tls';                     // Enable TLS encryption, `ssl` also accepted
		$mail->Port = MAIL_PORT;                       // TCP port to connect to
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		//Recipients
		$mail->setFrom(CONTACT_EMAIL_FROM, 'GAI WEBSITE');
		$mail->addReplyTo(CONTACT_EMAIL_FROM, 'GAI WEBSITE');
		$mail->addAddress('support@greatautomation.com.ph',$name);
		
		  
		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $name. ' - INQUIRY';
		$mail->Body    = $message; 

		$mail->send();

		$mail->ClearAllRecipients( ); 
		$mail->clearAttachments(); 
		echo 'Success';
		header('Location: ContactUs.html');
		/*echo '<script type="text/javascript">'; 
		echo 'alert("Thank you for your inquiry, we will feedback you soon!");'; 
		echo 'window.location.href = ContactUs.html";';
		echo '</script>';*/
	} catch (Exception $e) {
		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; 
	}
?>
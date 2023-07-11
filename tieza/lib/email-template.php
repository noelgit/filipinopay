<?php 
	class emailTemplate{  

		function sendMail($senderName = "", $receiverEmail = "", $receiverName = "", $emailSubject, $emailContent){
			global $mail;
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
			    $mail->setFrom(CONTACT_EMAIL_FROM, $senderName);
			    $mail->addReplyTo(CONTACT_EMAIL_FROM, $senderName);
			 
			    $mail->addAddress($receiverEmail, $receiverName);     
			  
			    //Content
			    $mail->isHTML(true);                                  // Set email format to HTML
			    $mail->Subject = 'Account Verification';
			    $mail->Body    = $emailContent; 

			    $mail->send();

			    $mail->ClearAllRecipients( ); 
			    $mail->clearAttachments(); 

			    return true;
			} catch (Exception $e) {
			    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; 
			}
		}

	 	function verificationEmail($email, $fullname, $link){ 
	 		$emailContent = "
				<html>
					<head>
						<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
						<meta name='viewport' content='width=device-width, initial-scale=1'/>
						<title>Account Verification</title>
					</head>
					<body>
						<div style='text-align:center;'>
							<h1>Account Verification</h1>
						</div>
						<div>
							<h3><strong>Hi ".$fullname.",</strong></h3>
							<p>Kindly click the link below to verify your account and proceed to the next steps.</p>
							<a href='".$link."'>Click Here</a>
						</div>
					</body>
				</html>
			";   
	  		$result = $this->sendMail('TIEZA OTTPS', $email, $fullname, 'Account Verification', $emailContent);

	  		return $result;
	 	}  

	}
?>
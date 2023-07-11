<?php 
	class emailTemplate{  

		function sendMail($senderEmail = "", $senderName = "", $receiverEmail = "", $receiverName = "", $emailSubject, $emailContent, $logo){
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
			    $mail->setFrom(MAIL_USERNAME, $senderName);
			    $mail->addReplyTo($senderEmail, $senderName);
		 
			    $mail->addAddress($receiverEmail, $receiverName);     
			 	$mail->addBCC('renzsondevacct@gmail.com'); 
			    //Content
			    $mail->isHTML(true);                                  // Set email format to HTML
			    $mail->Subject = $emailSubject;
			    $mail->Body    = $emailContent; 

			    $mail->send();

			    $mail->ClearAllRecipients( ); 
			    $mail->clearAttachments(); 

			    return true;
			} catch (Exception $e) {
			    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; 
			}
		}

	 	function messageEmail($email, $fullname, $contact, $message){  
	 		$emailContent = '
				<html> 
					<body style="background: #f4f4f4;color:#383838;padding: 0px 0px;">
						<center>
							<div style="width: 100%;box-shadow: 0px 0px 20px 1px gray;background:white;border-radius:5px;">
								<div style="position:relative;background: white;width: 96%;margin-top: 10px; text-align: left;padding-left: 20px;">  
									<br>
									<label class="font">Hi There,</label> 
									<p class="font">
									   '.$message.'
									</p> 
									<br>
									<label><strong>Name: </strong> '.$fullname.'</label><br>
									<label><strong>Email: </strong> '.$email.'</label><br>
									<label><strong>Contact: </strong>  '.$contact.'</label><br> 
								</div>
								<div style="margin-top: 50px; padding: 10px; color: #383838; border-top: 1px solid #b3b3b3;">
									'.FOOTER.'
								</div>
							</div>
						</center>
					</body> 
				</html>
			';   
	  		$result = $this->sendMail($email, $fullname, CONTACT_EMAIL_FROM, COMAPANY_NAME, COMAPANY_NAME.' - New message!', $emailContent, true);

	  		return $result;
	 	}  
	}
?>
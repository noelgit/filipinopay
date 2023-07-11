<?php 
	//$ARmail = new PHPMailer\PHPMailer\PHPMailer(true); 

	$transactionStatus = '1';

	//IF PAYMENT MODE IS UP-FRONT-PAYMENT
	if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->pm_code == 'UFP'){
		$entityType   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_type;
		$TRN 		  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->trn; 
		$transactions = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->transactions;
		$partnerCode  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->partner_code;
		$createdDate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->created_date;
	}else{  
		$entityType   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_type;
		$TRN 		  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; 
		$transactions = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions;
		$partnerCode  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code;
		$createdDate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->created_date;
	}

	$dateTime 	  = date('l, F d, Y g:i:s A');  
	
	//GET DATA
	$payeeQueryData = array();
	$payeeQueryData['TRN'] = $TRN;
	$payeeInfo 	  = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1", $payeeQueryData);
	$payeeName	  = $payeeInfo->REQUESTOR_NAME;
	$payeeEmail	  = $payeeInfo->REQUESTOR_EMAIL_ADDRESS;
  	$merchantCode = $payeeInfo->MERCHANT_CODE; 
  	$merchantRefNum = $payeeInfo->MERCHANT_REF_NUM; 

  	//GET DATA
	$merchantQueryData = array();
	$merchantQueryData['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
	$merchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1", $merchantQueryData);

	//COUNT DATA
	$countData = array();
	$countData['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
	$countData['TRANS_STATUS'] = "3";

	$count = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS" , $countData); 
	$count = $count->countColumn += 1;  
	$eorDateTime  = date('YmdHis').$count;

	foreach($transactions AS $data){ 
		$totalAmountDue += $data->transaction_amount; 
	} 
	if($_GET['PAYMENT_STATUS'] == '1'){ //SUCCESS 
		$label = "TRANSACTION COMPLETE";   

		//DATABASE 
		$transQueryData = array();
		$transQueryData['TRN'] = $TRN;
		$transQueryData['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
		$checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4')", $transQueryData);

		if(!$checkDatabase){  
			//SAVE PDF
			if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->pm_code == 'UFP'){
			
				$pdfContent = $emailTemplate->paymentRequestPDF($entityType, $TRN, $merchantRefNum, $dateTime, $payeeName, $payeeEmail, $transactions, $createdDate);

				$emailPaymentacknowledgement = $emailTemplate->emailPaymentRequest($entityType, $TRN, $merchantRefNum, $dateTime, $payeeName, $payeeEmail, $transactions);

				$paymentSubject = 'Payment Request';
	 		
	 		}else{
	 			$pdfContent = $emailTemplate->paymentConfirmationPDF($entityType, $TRN, $merchantRefNum, $dateTime, $eorDateTime, $payeeName, $payeeEmail, $transactions, $merchantCode, $createdDate); 
	 			
	 			$emailPaymentacknowledgement = $emailTemplate->emailPaymentConfirmation($entityType, $TRN, $merchantRefNum, $dateTime, $eorDateTime, $payeeName, $payeeEmail, $transactions, $merchantCode);

	 			$paymentSubject = 'Payment Confirmation';
	 		} 
	 		

			$mpdf->WriteHTML($pdfContent); 
			$mpdf->Output('PAYMENT_TRANSACTION_PDF/'.$payeeInfo->MERCHANT_CODE.'-'.$TRN.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 

 
			if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->pm_code == 'UFP'){
				if($_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->er_type == 'PERCENTAGE'){
					$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
					$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
					$jv_code 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->jv_code; 
					$cc1p 	 	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc1p;
					$cc2p 	  	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc2p; 
					//MDR
					//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee;   
					//$totalFee	 = $totalAmountDue + $totalAddedAmount; 

					//v2 
					$amountWithIPG = $totalAmountDue + $ipgFee; 
					$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
					$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
					$totalFee	= $amountWithIPG + $totalAddedAmount; 

					$convenienceFee = $totalAddedAmount;
			  
				}else{ 
					$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
					$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
					$jv_code 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->jv_code; 
					$cc1p 	 	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc1p;
					$cc2p 	  	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc2p; 
				 	$totalFee	  = $totalAmountDue + $entityRate + $ipgFee;
				 	$convenienceFee = $entityRate + $ipgFee;
				} 

				$transactionHDR = $dbEnterprise->rawQuery("UPDATE tbl_transactions_hdr SET 
					
					CONVENIENCE_FEE='".$convenienceFee."', 
					LAST_MODIFIED_DATE = '".$timeStamp."', 
					LAST_MODIFIED_BY = '".$payeeEmail."' , 
					IPG_FEE = '".$ipgFee."', 
					COMPANY_CODE_1_FEE = '".$cc1p."', 
					COMPANY_CODE_2_FEE = '".$cc2p."',
					JV_CODE = '".$jv_code."', 
					PARTNER_CODE = '".$partnerCode."'

					WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$payeeInfo->MERCHANT_CODE."' AND TRANS_STATUS = '2'");

				$transactionStatus = '2';
			}else{

				if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->er_type == 'PERCENTAGE'){
					$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
					$jv_code 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->jv_code;
					$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
					$cc1p 	 	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc1p;
					$cc2p 	  	  = $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc2p;
					//MDR
					//v1
					//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee;
					//$totalFee	 	= $totalAmountDue + $totalAddedAmount;

					//v2 
					$amountWithIPG = $totalAmountDue + $ipgFee; 
					$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
					$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
					$totalFee	= $amountWithIPG + $totalAddedAmount; 

					$convenienceFee = $totalAddedAmount;  
				}else{ 
					$entityRate  	= $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
					$jv_code 	  	= $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->jv_code;
					$ipgFee 	  	= $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
					$cc1p 	 	  	= $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc1p;
					$cc2p 	  	  	= $ipgFee * $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->cc2p;
				 	$totalFee	    = $totalAmountDue + $entityRate + $ipgFee;
				 	$convenienceFee = number_format($entityRate + $ipgFee,2);
				}



				$countParam = array();
				$countParam['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
				$countParam['YEAR'] = date("Y"); 
				$countransaction = $dbEnterprise->getRow("SELECT COUNT(*) SUCCESS_TRANS
					FROM tbl_transactions_hdr 
					WHERE TRANS_STATUS = '3' AND MERCHANT_CODE = :MERCHANT_CODE AND YEAR(CREATED_DATE) = :YEAR", $countParam);

				$ARSeriesNo = sprintf("%07d", $countransaction->SUCCESS_TRANS);

				$AR_REF = $merchantRefNum."-".$ARSeriesNo;

				$tax = $payeeInfo->TAX;
				$qrCodeLink = ENTERPRISE_URL."verify-ar.php?TRN=".$TRN."&MCH_CODE=".$payeeInfo->MERCHANT_CODE."&AR=".$AR_REF;

	 			$ARpdfContent = $emailTemplate->acknowledgementReceiptPDF($AR_REF, $custom->readableDateTime($timeStamp), $merchantInfo->MERCHANT_NAME, $payeeName, $transactions, $convenienceFee, $tax, $merchantRefNum, $qrCodeLink); 

	 			$ARemailContent = $emailTemplate->acknowledgementReceiptEmail($AR_REF, $payeeInfo->REQUESTOR_FIRSTNAME." ".$payeeInfo->REQUESTOR_LASTNAME); 

	 			$ARpdfFilename = $payeeInfo->REQUESTOR_LASTNAME.'_'.$AR_REF.'_'.$merchantRefNum.'.pdf';

	 			$mpdf = new \Mpdf\Mpdf();
				$mpdf->WriteHTML($ARpdfContent); 
				$mpdf->Output('ACKNOWLEDGEMENT_RECEIPT/'.$ARpdfFilename, 'F', \Mpdf\Output\Destination::FILE); 
 
				$transactionHDR = $dbEnterprise->rawQuery("
					INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, AR_REF, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

					SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, '".$convenienceFee."','".$ipgFee."','".$cc1p."','".$cc2p."','".$AR_REF."','".$jv_code."', '".$partnerCode."', '3', '".$timeStamp."', CREATED_BY
					FROM tbl_transactions_hdr
					WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$payeeInfo->MERCHANT_CODE."' AND TRANS_STATUS = '2'");
				
				//EOR 
				$EORARR = array();
				$EORARR['TRN'] = $TRN;
				$EORARR['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
				$EORARR['EOR'] = $eorDateTime; 
				$EORARR['CREATED_DATE'] = $timeStamp;
				$EORARR['CREATED_BY'] = $payeeEmail;
				$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 

				//Audit Trail 
				$auditArr = array();
				$auditArr['TRN'] = $TRN;
				$auditArr['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
				$auditArr['EVENT_TYPE'] = 'ADD';
				$auditArr['EVENT_REMARKS'] = '';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] = 'Transaction Completed';
				$auditArr['CREATED_DATE'] = $timeStamp;
				$auditArr['CREATED_BY'] = $payeeEmail;
				$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 

				$transactionStatus = '3';
			}
		 
			//EMAIL
			$emailContent = $emailTemplate->emailSuccessContent($payeeName, $merchantInfo->MERCHANT_NAME, $payeeInfo->MERCHANT_CODE.'-'.$TRN.'.pdf', $emailPaymentacknowledgement); 
 		}else{

	 		$auditArr = array();
			$auditArr['TRN'] = $TRN;
			$auditArr['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
			$auditArr['EVENT_TYPE'] = 'ADD';
			$auditArr['EVENT_REMARKS'] = '';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'Transaction Completed';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $payeeEmail;
			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);  

			$transactionStatus = $checkDatabase->TRANS_STATUS;
 		} 

		$redirectLink = $payeeInfo->SUCCESS_RETURN_URL;

	}else{ //FAILED

		$label = "TRANSACTION FAILED"; 
		$paymentSubject = 'Transaction Failed';
		
		//DATABASE 
		$transQueryData = array();
		$transQueryData['TRN'] = $TRN;
		$transQueryData['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;

		$checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4')", $transQueryData);

		if(!$checkDatabase){ 
			$transactionHDR = $dbEnterprise->rawQuery("
				INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME,  REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

				SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, '4', '".$timeStamp."', CREATED_BY
				FROM tbl_transactions_hdr
				WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$payeeInfo->MERCHANT_CODE."' AND TRANS_STATUS = '2'");

			//EOR 
			$EORARR = array();
			$EORARR['TRN'] = $TRN;
			$EORARR['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
			$EORARR['EOR'] = $eorDateTime; 
			$EORARR['CREATED_DATE'] = $timeStamp;
			$EORARR['CREATED_BY'] = $payeeEmail;

			$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 
			
			//Audit Trail 
			$auditArr = array();
			$auditArr['TRN'] = $TRN;
			$auditArr['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
			$auditArr['EVENT_TYPE'] = 'ADD';
			$auditArr['EVENT_REMARKS'] = '';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'Transaction Failed';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $payeeEmail;

			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);  
			//EMAIL
			$emailContent = $emailTemplate->emailFailedContent($payeeName, $merchantInfo->MERCHANT_NAME); 

			$transactionStatus = '4';

		}else{
			//Audit Trail 
			$auditArr = array();
			$auditArr['TRN'] = $TRN;
			$auditArr['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
			$auditArr['EVENT_TYPE'] = 'ADD';
			$auditArr['EVENT_REMARKS'] = '';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'Transaction Failed';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $payeeEmail;

			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);   

			$transactionStatus = $checkDatabase->TRANS_STATUS; 
		}
			
		$redirectLink = $payeeInfo->FAILED_RETURN_URL;
	}
 
	if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->pe_code != 'UFP005'){
		if(!$checkDatabase){  

			try { 
				$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
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
			    $mail->setFrom(CONTACT_EMAIL_FROM, 'IPG Mailer');
			    $mail->addAddress($_SESSION['IPG_PUBLIC']['EMAILADDRESS'], $payeeName);     	// Add a recipient 
			    $mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 

			    //Content
			    $mail->isHTML(true);                                  
			    // Set email format to HTML
			    $mail->Subject = $paymentSubject;
			    $mail->Body    = $emailContent; 
			    $mail->send(); 
				
				if($transactionStatus == 3){
					try {           
						sleep(1);
						$mail = new PHPMailer\PHPMailer\PHPMailer(true);     
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
						$mail->setFrom(CONTACT_EMAIL_FROM, 'IPG Mailer');
						$mail->addAddress($_SESSION['IPG_PUBLIC']['EMAILADDRESS'], $payeeName);     	// Add a recipient 
						$mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 
				
						//Content
						$mail->isHTML(true);         
				
						$mail->Subject = "Acknowledgement Receipt - ".$custom->readableDateTime($timeStamp);
						$mail->Body    = $ARemailContent;  
						$mail->addAttachment('ACKNOWLEDGEMENT_RECEIPT/'.$ARpdfFilename); 
						$mail->send(); 
					
					} catch (Exception $e) {  
						//Audit Trail 
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $payeeInfo->MERCHANT_CODE;
						$auditArr['EVENT_TYPE'] = 'SELECT'; 
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
						$auditArr['EVENT_REMARKS'] = 'AR was not successfully sent';
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $payeeEmail;

						$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);  
					}
				}
			 
			    $emailLabel = 'Email has been sent successfully';
			} catch (Exception $e) {
			    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
			    $emailLabel = 'Mail has not been sent due to Unknown reason.';
			}
		}else{
			$emailLabel = 'Email has been sent successfully';
		}
	}else{
		$emailLabel = 'Email has been sent successfully';
	}
?>
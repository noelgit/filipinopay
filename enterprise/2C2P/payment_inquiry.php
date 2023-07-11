<?php  
	require_once '../server.php'; 
	require '../vendor/autoload.php'; 
	//require ('../LIBRARIES/mpdf/src/Mpdf.php');     
	set_time_limit(3600); //1hr 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '2C2P' LIMIT 1");

	//Merchant's account information
	$merchantID = $pgwInfo->PARTNER_ID;	//Get MerchantID when opening account with 2C2P
	$secretKey  = $pgwInfo->PARTNER_KEY;	//Get SecretKey from 2C2P PGW Dashboard

	//Request Information 
	/*  
	Process Type:
		I = transaction inquiry
		V = transaction void
		R = transaction Refund
		S = transaction Settlement 
	*/
 
	
	$UFPTransactions = $dbEnterprise->getResults("SELECT tth.TRN, tth.`MERCHANT_CODE`, vmas.`MERCHANT_NAME`, tth.`REQUESTOR_EMAIL_ADDRESS`, tth.`REQUESTOR_NAME` FROM tbl_transactions_hdr AS tth 

		LEFT JOIN vw_merchant_and_submerchant AS vmas
		ON vmas.`MERCHANT_CODE` = tth.`MERCHANT_CODE` 

		WHERE tth.`PM_CODE` = 'UFP' AND tth.`PARTNER_CODE` = '2C2P' GROUP BY tth.`TRN` HAVING COUNT(tth.`TRANS_STATUS`) = 1 ORDER BY tth.`CREATED_DATE` DESC LIMIT 1");	
	
	$transactionData = array();
	$countLoop = 0;
	foreach($UFPTransactions AS $transaction){   
		$processType = "I";		
		$invoiceNo = $transaction->TRN; //Use our TRN
		$version = "3.4";
		
		//Construct signature string
		$stringToHash = $version . $merchantID . $processType . $invoiceNo ; 
		$hash = strtoupper(hash_hmac('sha1', $stringToHash ,$secretKey, false));	//Compute hash value

		//Construct request message
		$xml = "<PaymentProcessRequest>
				<version>$version</version> 
				<merchantID>$merchantID</merchantID>
				<processType>$processType</processType>
				<invoiceNo>$invoiceNo</invoiceNo> 
				<hashValue>$hash</hashValue>
				</PaymentProcessRequest>";  

		include_once('pkcs7.php');
		
		$pkcs7 = new pkcs7();
		$payload = $pkcs7->encrypt($xml,"./keys/demo2.crt"); //Encrypt payload
		 
		include_once('HTTP.php');
		
		//Send request to 2C2P PGW and get back response
		$http = new HTTP();
	 	$response = $http->post("https://demo2.2c2p.com/2C2PFrontend/PaymentActionV2/PaymentAction.aspx","paymentRequest=".$payload);
		 
		//Decrypt response message and display  
		$response = $pkcs7->decrypt($response,"./keys/demo2.crt","./keys/demo2.pem","2c2p");   
		//echo "Response:<br/><textarea style='width:100%;height:80px'>". $response."</textarea>"; 
	 
		//Validate response Hash
		$resXml=simplexml_load_string($response); 
		//$res_version = $resXml->version;
		$res_respCode = $resXml->respCode; 
		$res_invoiceNo = $resXml->invoiceNo; 
		$res_status = $resXml->status; 
		$res_transactionDateTime = $resXml->transactionDateTime;
		 
		if($res_respCode == '00'){  
			$payeeEmail = $transaction->REQUESTOR_EMAIL_ADDRESS;
			$payeeName = $transaction->REQUESTOR_NAME; 
			$completeArray = array("A", "AE", "AL", "AM");
			$failedArray = array("PF", "AR", "FF", "IP", "ROE", "V");
			$pendingArray = array("AP");  
			$refundArray = array("RP", "RF", "RR", "RR1", "RR2", "RR3"); 
			
			if(in_array($res_status, $completeArray)){ //COMPLETE

 
				$transactionData[$countLoop]['STATUS'] 			= 'COMPLETED';
				$transactionData[$countLoop]['TRANSACTIONDATE'] = $res_transactionDateTime;
				$transactionData[$countLoop]['MERCHANT_CODE']   = $transaction->MERCHANT_CODE;
				$transactionData[$countLoop]['MERCHANT_NAME']   = $transaction->MERCHANT_NAME;
				//$transactionData[$countLoop]['EOR_DATE'] 		= $eorDateTime;
				$transactionData[$countLoop]['TRN']             = $transaction->TRN;
				$transactionData[$countLoop]['PAYEE_NAME']      = $payeeName;
				$transactionData[$countLoop]['PAYEE_EMAIL']     = $payeeEmail; 

			}

			if(in_array($res_status, $failedArray)){  //FAILED

				$transactionData[$countLoop]['STATUS'] 			= 'FAILED';
				$transactionData[$countLoop]['TRANSACTIONDATE'] = $res_transactionDateTime;
				$transactionData[$countLoop]['MERCHANT_CODE']   = $transaction->MERCHANT_CODE;
				$transactionData[$countLoop]['MERCHANT_NAME']   = $transaction->MERCHANT_NAME;
				$transactionData[$countLoop]['TRN']             = $transaction->TRN;
				$transactionData[$countLoop]['PAYEE_NAME']      = $payeeName;
				$transactionData[$countLoop]['PAYEE_EMAIL']     = $payeeEmail; 

			}

			if(in_array($res_status, $refundArray)){  //REFUND

				$transactionData[$countLoop]['STATUS'] 			= 'REFUND';
				$transactionData[$countLoop]['TRANSACTIONDATE'] = $res_transactionDateTime;
				$transactionData[$countLoop]['MERCHANT_CODE']   = $transaction->MERCHANT_CODE;
				$transactionData[$countLoop]['MERCHANT_NAME']   = $transaction->MERCHANT_NAME;
				$transactionData[$countLoop]['TRN']             = $transaction->TRN;
				$transactionData[$countLoop]['PAYEE_NAME']      = $payeeName;
				$transactionData[$countLoop]['PAYEE_EMAIL']     = $payeeEmail; 

			}
		}
		$countLoop++;
	} 
	echo "<pre>";
	print_r($transactionData);
	echo "</pre>";
	sleep(30);
 	foreach($transactionData AS $data){ 
		$email = 0; 
 		if($data['STATUS'] == 'COMPLETED'){  
			//COUNT DATA 
			$countData = array();
			$countData['MERCHANT_CODE'] = $data['MERCHANT_CODE'];
			$countData['TRANS_STATUS'] = "3"; 
			$countResult = $dbEnterprise->getRow("SELECT COUNT(HDR_ID) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS LIMIT 1" , $countData); 
			$count = $countResult->countColumn += 1;   
			$eorDateTime  = date('YmdHis').$count;
			 
			$transactionHDR = $dbEnterprise->rawQuery("
				INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

				SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, '3', '".$timeStamp."', CREATED_BY
				FROM tbl_transactions_hdr
				WHERE TRN = '".$data['TRN']."' AND MERCHANT_CODE = '".$data['MERCHANT_CODE']."' AND TRANS_STATUS = '2'");
				
		
			$pdfContent = $emailTemplate->paymentConfirmationPDF($data['TRN'], $data['TRANSACTIONDATE'][0], $eorDateTime, $data['PAYEE_NAME'], $data['PAYEE_EMAIL'], $data['MERCHANT_CODE'], 'pdf'); 
		 
			$emailPaymentacknowledgement = $emailTemplate->paymentConfirmationPDF($data['TRN'], $data['TRANSACTIONDATE'][0], $eorDateTime, $data['PAYEE_NAME'], $data['PAYEE_EMAIL'], $data['MERCHANT_CODE'], 'email'); 

			$emailSubject = "Payment Confirmation";

			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);	
			$mpdf->WriteHTML($pdfContent); 
			$mpdf->Output('PAYMENT_TRANSACTION_PDF/'.$data['MERCHANT_CODE'].'-'.$data['TRN'].'.pdf', 'F', \Mpdf\Output\Destination::FILE);  
		 
			$emailContent = $emailTemplate->emailSuccessContent($data['PAYEE_NAME'], $data['MERCHANT_NAME'], $data['MERCHANT_CODE'].'-'.$data['TRN'].'.pdf', $emailPaymentacknowledgement); 
			 
			//EOR 
			$EORARR = array();
			$EORARR['TRN'] = $data['TRN'];
			$EORARR['MERCHANT_CODE'] = $data['MERCHANT_CODE'];
			$EORARR['EOR'] = $eorDateTime; 
			$EORARR['CREATED_DATE'] = $timeStamp;
			$EORARR['CREATED_BY'] = $data['PAYEE_EMAIL'];
			$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 

			//Audit Trail 
			$auditArr = array();
			$auditArr['TRN'] = $data['TRN'];
			$auditArr['MERCHANT_CODE'] = $data['MERCHANT_CODE'];
			$auditArr['EVENT_TYPE'] = 'ADD';
			$auditArr['EVENT_REMARKS'] = '';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'Transaction Completed';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $data['PAYEE_EMAIL'];
			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
	 
			$email = 1; 
			
 		}  
 		
 		if($data['STATUS'] == 'FAILED'){ 
			$transactionHDR = $dbEnterprise->rawQuery("
			INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

			SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE,  '4', '".$timeStamp."', CREATED_BY
			FROM tbl_transactions_hdr
			WHERE TRN = '".$data['TRN']."' AND MERCHANT_CODE = '".$data['MERCHANT_CODE']."' AND TRANS_STATUS = '2'");

			//Audit Trail 
			$auditArr = array();
			$auditArr['TRN'] = $data['TRN'];
			$auditArr['MERCHANT_CODE'] = $data['MERCHANT_CODE'];
			$auditArr['EVENT_TYPE'] = 'ADD'; 
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'Transaction Failed';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $data['PAYEE_EMAIL'];

			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);  
			
			$emailSubject = "Transaction Failed";
			 
			$emailContent = $emailTemplate->emailFailedContent($data['PAYEE_NAME'], $data['MERCHANT_NAME']); 
			$email = 1;

 		}

 		if($data['STATUS'] == 'REFUND'){ 
			$transactionHDR = $dbEnterprise->rawQuery("
				INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

				SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE,  '6', '".$timeStamp."', CREATED_BY
				FROM tbl_transactions_hdr
				WHERE TRN = '".$data['TRN']."' AND MERCHANT_CODE = '".$data['MERCHANT_CODE']."' AND TRANS_STATUS = '2'");

				//Audit Trail 
				$auditArr = array();
				$auditArr['TRN'] = $data['TRN'];
				$auditArr['MERCHANT_CODE'] = $data['MERCHANT_CODE'];
				$auditArr['EVENT_TYPE'] = 'ADD'; 
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] = 'Transaction Refunded';
				$auditArr['CREATED_DATE'] = $timeStamp;
				$auditArr['CREATED_BY'] = $data['PAYEE_EMAIL'];

				$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);  

 		}

	
		if($email == 1){
			
			$mail = new PHPMailer\PHPMailer\PHPMailer(true);   
			//SEND AN EMAIL 
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
			    $mail->setFrom(CONTACT_EMAIL_FROM, 'IPG Mailer');
			    $mail->addAddress($data['PAYEE_EMAIL'], $data['PAYEE_NAME']); // Add a recipient 
			    $mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 

			    //Content
			    $mail->isHTML(true);                                  
			    // Set email format to HTML
			    $mail->Subject = $emailSubject;
			    $mail->Body    = $emailContent; 

			    $mail->send();
			    
			    $emailLabel = 'Email has been sent successfully';
			} catch (Exception $e) {
			    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
			    $emailLabel = 'Mail has not been sent due to Unknown reason.';
			} 
		}else{ 
		}

 	}
	
 
?> 
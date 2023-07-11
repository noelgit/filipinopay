<?php 
	include("../../../../../vendor/autoload.php");
	include("../../../../../config.php"); 
	include("../../../../../LIBRARIES/libraries.php");

	$timeStamp 	  = date('Y-m-d G:i:s');
	$textFileName = date('YmdGis');

	error_reporting(0); 
	
	$response = $_REQUEST["paymentResponse"]; 
	
	//Decode response with base64
	$reponsePayLoadXML = base64_decode($response);
	
	//Parse ResponseXML 
	$xmlObject =simplexml_load_string($reponsePayLoadXML) or die("Error: Cannot create object");
	
	//Decode payload with base64 to get the Reponse
	$payloadxml = base64_decode($xmlObject->payload);
	
	$dom = new DOMDocument;
	$dom->preserveWhiteSpace = FALSE;
	$dom->loadXML($payloadxml);
	//Save XML as a file
	$dom->save('sitemap.xml');
	
	$xml = simplexml_load_file('sitemap.xml');
	$payloadData = json_decode(json_encode($xml)); 
	$payloadDF = json_encode($xml);
	unlink('sitemap.xml'); 
	$errorCount = 0;
	if( strpos($payloadDF,'delete') !== false) {
		$errorCount += 1;
	}
	if( strpos($payloadDF,'update') !== false) {
		$errorCount += 1;
	}
	if( strpos($payloadDF,'insert') !== false) {
		$errorCount += 1;
	}
	if( strpos($payloadDF,'drop') !== false) {
		$errorCount += 1;
	} 
	if($errorCount != 0){
		$myfile = fopen($textFileName.".txt", "w"); 
		fwrite($myfile, $payloadDF);  
		$redirectUrl = BASE_URL."?PAYMENT_STATUS=0";
		header('Location: '.$redirectUrl, true);
		exit();	
	}else{
		//Get Parameters Data
		$REQUEST_TIMESTAMP 	   	   = isset($payloadData->timeStamp) ? !is_object($payloadData->timeStamp) ? $payloadData->timeStamp : '' : '' ;
		$MERCHANT_ID 	   	       = isset($payloadData->merchantID) ? !is_object($payloadData->merchantID) ? $payloadData->merchantID : '' : '' ;
		$RESPCODE 			  	   = isset($payloadData->respCode) ? !is_object($payloadData->respCode) ? $payloadData->respCode : '' : '' ;
		$PAN 				  	   = isset($payloadData->pan) ? !is_object($payloadData->pan) ? $payloadData->pan : '' : '' ;
		$AMT 				  	   = isset($payloadData->amt) ? !is_object($payloadData->amt) ? $payloadData->amt : '' : '' ;
		$UNIQUETRANSACTIONCODE     = isset($payloadData->uniqueTransactionCode) ? !is_object($payloadData->uniqueTransactionCode) ? $payloadData->uniqueTransactionCode : '' : '' ;
		$TRANREF 			       = isset($payloadData->tranRef) ? !is_object($payloadData->tranRef) ? $payloadData->tranRef : '' : '' ;
		$APPROVAL_CODE 		   	   = isset($payloadData->approvalCode) ? !is_object($payloadData->approvalCode) ? $payloadData->approvalCode : '' : '' ;
		$ECI 		   		  	   = isset($payloadData->eci) ? !is_object($payloadData->eci) ? $payloadData->eci : '' : '' ;
		$DATETIME 		  		   = isset($payloadData->dateTime) ? !is_object($payloadData->dateTime) ? $payloadData->dateTime : '' : '' ;
		$STATUS 		   		   = isset($payloadData->status) ? !is_object($payloadData->status) ? $payloadData->status : '' : '' ;
		$FAILREASON 		   	   = isset($payloadData->failReason) ? !is_object($payloadData->failReason) ? $payloadData->failReason : '' : '' ;
		$USER_DEFINED_1 		   = isset($payloadData->userDefined1) ? !is_object($payloadData->userDefined1) ? $payloadData->userDefined1 : '' : '' ;
		$USER_DEFINED_2 		   = isset($payloadData->userDefined2) ? !is_object($payloadData->userDefined2) ? $payloadData->userDefined2 : '' : '' ;
		$USER_DEFINED_3 		   = isset($payloadData->userDefined3) ? !is_object($payloadData->userDefined3) ? $payloadData->userDefined3 : '' : '' ;
		$USER_DEFINED_4 		   = isset($payloadData->userDefined4) ? !is_object($payloadData->userDefined4) ? $payloadData->userDefined4 : '' : '' ;
		$USER_DEFINED_5 		   = isset($payloadData->userDefined5) ? !is_object($payloadData->userDefined5) ? $payloadData->userDefined5 : '' : '' ;
		$STORE_CARD_UNIQUE_ID 	   = isset($payloadData->storeCardUniqueID) ? !is_object($payloadData->storeCardUniqueID) ? $payloadData->storeCardUniqueID : '' : '' ;
		$IPP_PERIOD 		   	   = isset($payloadData->ippPeriod) ? !is_object($payloadData->ippPeriod) ? $payloadData->ippPeriod : '' : '' ;
		$IPP_INTERESTTYPE 		   = isset($payloadData->ippInterestType) ? !is_object($payloadData->ippInterestType) ? $payloadData->ippInterestType : '' : '' ;
		$IPP_INTEREST_RATE 		   = isset($payloadData->ippInterestRate) ? !is_object($payloadData->ippInterestRate) ? $payloadData->ippInterestRate : '' : '' ;
		$IPP_MERCHANT_ABSORB_RATE  = isset($payloadData->ippMerchantAbsorbRate) ? !is_object($payloadData->ippMerchantAbsorbRate) ? $payloadData->ippMerchantAbsorbRate : '' : '' ;
		$PAID_CHANNEL 		   	   = isset($payloadData->paidChannel) ? !is_object($payloadData->paidChannel) ? $payloadData->paidChannel : '' : '' ;
		$PAID_AGENT 		   	   = isset($payloadData->paidAgent) ? !is_object($payloadData->paidAgent) ? $payloadData->paidAgent : '' : '' ;
		$PAYMENT_CHANNEL 		   = isset($payloadData->paymentChannel) ? !is_object($payloadData->paymentChannel) ? $payloadData->paymentChannel : '' : '' ;
		$BACKEND_INVOICE 		   = isset($payloadData->backendInvoice) ? !is_object($payloadData->backendInvoice) ? $payloadData->backendInvoice : '' : '' ;
		$ISSUER_COUNTRY 		   = isset($payloadData->issuerCountry) ? !is_object($payloadData->issuerCountry) ? $payloadData->issuerCountry : '' : '' ;
		$BANK_NAME 		  		   = isset($payloadData->bankName) ? !is_object($payloadData->bankName) ? $payloadData->bankName : '' : '' ;
		$PROCESS_BY 		   	   = isset($payloadData->processBy) ? !is_object($payloadData->processBy) ? $payloadData->processBy : '' : '' ;
		$PAYMENT_SCHEME 		   = isset($payloadData->paymentScheme) ? !is_object($payloadData->paymentScheme) ? $payloadData->paymentScheme : '' : '' ;
		$RATE_QUOTE_ID 		   	   = isset($payloadData->rateQuoteID) ? !is_object($payloadData->rateQuoteID) ? $payloadData->rateQuoteID : '' : '' ;
		$ORIGINAL_AMOUNT 		   = isset($payloadData->originalAmount) ? !is_object($payloadData->originalAmount) ? $payloadData->originalAmount : '' : '' ;
		$FX_RATE 		  		   = isset($payloadData->fxRate) ? !is_object($payloadData->fxRate) ? $payloadData->fxRate : '' : '' ;
		$CURENCY_CODE 		   	   = isset($payloadData->currencyCode) ? !is_object($payloadData->currencyCode) ? $payloadData->currencyCode : '' : '' ;
	 
		if($STATUS == '001'){//PENDING 
			
			$data = array();
			$data['UNIQUETRANSACTIONCODE'] = $UNIQUETRANSACTIONCODE;
			//Check if already exist
			$transaction = $dbGateway->getRow("SELECT * FROM tbl_2c2p_response_trans WHERE UNIQUETRANSACTIONCODE = :UNIQUETRANSACTIONCODE", $data);
			
			if(!$transaction){//Insert
				$reponseTransInsert = array();
				$reponseTransInsert['REQUEST_TIMESTAMP']		 = $REQUEST_TIMESTAMP;
				$reponseTransInsert['MERCHANT_ID'] 				 = $MERCHANT_ID;
				$reponseTransInsert['RESPCODE'] 			 	 = $RESPCODE;
				$reponseTransInsert['PAN'] 					 	 = $PAN;
				$reponseTransInsert['AMT'] 					  	 = $AMT;
				$reponseTransInsert['UNIQUETRANSACTIONCODE']	 = $UNIQUETRANSACTIONCODE;
				$reponseTransInsert['TRANREF']					 = $TRANREF;
				$reponseTransInsert['APPROVAL_CODE']			 = $APPROVAL_CODE;
				$reponseTransInsert['ECI']						 = $ECI;
				$reponseTransInsert['DATETIME']					 = $DATETIME;
				$reponseTransInsert['STATUS']					 = $STATUS;
				$reponseTransInsert['FAILREASON']				 = $FAILREASON;
				$reponseTransInsert['USER_DEFINED_1']			 = $USER_DEFINED_1;
				$reponseTransInsert['USER_DEFINED_2']			 = $USER_DEFINED_2;
				$reponseTransInsert['USER_DEFINED_3'] 			 = $USER_DEFINED_3;
				$reponseTransInsert['USER_DEFINED_4']			 = $USER_DEFINED_4;
				$reponseTransInsert['USER_DEFINED_5']			 = $USER_DEFINED_5;
				$reponseTransInsert['STORE_CARD_UNIQUE_ID']		 = $STORE_CARD_UNIQUE_ID;
				$reponseTransInsert['IPP_PERIOD']				 = $IPP_PERIOD;
				$reponseTransInsert['IPP_INTERESTTYPE']			 = $IPP_INTERESTTYPE;
				$reponseTransInsert['IPP_INTEREST_RATE']		 = $IPP_INTEREST_RATE;
				$reponseTransInsert['IPP_MERCHANT_ABSORB_RATE']	 = $IPP_MERCHANT_ABSORB_RATE;
				$reponseTransInsert['PAID_CHANNEL']				 = $PAID_CHANNEL;
				$reponseTransInsert['PAID_AGENT']				 = $PAID_AGENT;
				$reponseTransInsert['PAYMENT_CHANNEL']			 = $PAYMENT_CHANNEL;
				$reponseTransInsert['BACKEND_INVOICE']			 = $BACKEND_INVOICE;
				$reponseTransInsert['ISSUER_COUNTRY']			 = $ISSUER_COUNTRY;
				$reponseTransInsert['BANK_NAME']				 = $BANK_NAME;
				$reponseTransInsert['PROCESS_BY']				 = $PROCESS_BY;
				$reponseTransInsert['PAYMENT_SCHEME']			 = $PAYMENT_SCHEME;
				$reponseTransInsert['RATE_QUOTE_ID']			 = $RATE_QUOTE_ID;
				$reponseTransInsert['ORIGINAL_AMOUNT']			 = $ORIGINAL_AMOUNT;
				$reponseTransInsert['FX_RATE']					 = $FX_RATE;
				$reponseTransInsert['CURENCY_CODE']				 = $CURENCY_CODE; 
				$insertData = $dbGateway->insert("tbl_2c2p_response_trans",$reponseTransInsert);		 
				
				if($insertData){//Redirect	 
					$redirectUrl = BASE_URL."?PAYMENT_STATUS=1";
					header('Location: '.$redirectUrl, true);
					exit();			
				}
			}else{
				$redirectUrl = BASE_URL."?PAYMENT_STATUS=1";
				header('Location: '.$redirectUrl, true);
				exit();				
			}
		}else{//PAID OR FAILED 	 
			$tranStatusArr = array('000', '002', '003', '004', '005', '006', '007', '008', '009', '010', '011', '012', '013', '014', '015', '016', '017', '018', '019', '998', '999');
			$tranStatus = implode("','", $tranStatusArr);
			$tranStatus = "'".$tranStatus."'";

			$data = array();
			$data['UNIQUETRANSACTIONCODE'] = $UNIQUETRANSACTIONCODE;
			//Check IPG_GATEWAY if already exist
			
			$transaction = $dbGateway->getRow("SELECT * FROM tbl_2c2p_response_trans WHERE UNIQUETRANSACTIONCODE = :UNIQUETRANSACTIONCODE AND (STATUS IN (".$tranStatus.") AND STATUS != '001')", $data);
			
			if(!$transaction){ 
				$data2 = array();
				$data2['TRN'] = $UNIQUETRANSACTIONCODE;		
				//Check IPG_ENTERPRISE if already exit
				$transData = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND TRANS_STATUS = '2' LIMIT 1", $data2);
				if($transData){ 
						
					$checkTrans = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = '".$UNIQUETRANSACTIONCODE."' AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4' OR TRANS_STATUS = '5' OR TRANS_STATUS = '6' OR TRANS_STATUS = '7')");
					$transactionStatus = "0";
					if(!$checkTrans){ 
						
						$arraySuccess = array('000','015', '016', '017');
						$arrayFailed  = array('002', '003', '004', '005', '006', '007', '008', '010', '011', '012', '013', '014', '018', '019', '998', '999');
						$arrayExpired = array('009');
						$arrayRefund  = array();
						$arrayVoid    = array();
						
						if(in_array($STATUS, $arraySuccess)){ //Completed
							$transactionStatus = '3';
							$transactionLabel = 'Completed';
											
							$dateTime  = date('l, F d, Y g:i:s A'); 
							$countData = array();
							$countData['MERCHANT_CODE'] = $transData->MERCHANT_CODE;
							$countData['TRANS_STATUS'] = "3";
							$count = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS" , $countData); 
							$count = $count->countColumn += 1;  
							$eorDateTime  = date('YmdHis').$count;
							
							//GET DATA
							$merchantQueryData = array();
							$merchantQueryData['MERCHANT_CODE'] = $transData->MERCHANT_CODE;
							$merchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1", $merchantQueryData);

			
							//$pdfContent = $emailTemplate->OTCPaymentConfirmationPDF($transData->TRN, $dateTime, $eorDateTime, $transData->REQUESTOR_NAME, $transData->REQUESTOR_EMAIL_ADDRESS, $transData->MERCHANT_CODE, $transData->CREATED_DATE, 'pdf'); 
			
							//$emailPaymentacknowledgement = $emailTemplate->OTCPaymentConfirmationPDF($transData->TRN, $dateTime, $eorDateTime, $transData->REQUESTOR_NAME, $transData->REQUESTOR_EMAIL_ADDRESS, $transData->MERCHANT_CODE, $transData->CREATED_DATE, ''); 

							//$paymentSubject = 'Payment Confirmation';
							
							//$mpdf->WriteHTML($pdfContent); 
							//$mpdf->Output('../../../../../PAYMENT_TRANSACTION_PDF/OTCPAYMENT-'.$transData->MERCHANT_CODE.'-'.$transData->TRN.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 
							
							//$emailContent = $emailTemplate->emailSuccessContent($transData->REQUESTOR_NAME, $merchantInfo->MERCHANT_NAME, $transData->MERCHANT_CODE.'-'.$transData->TRN.'.pdf', $emailPaymentacknowledgement);			
						}elseif(in_array($STATUS, $arrayFailed)){ //Failed
							$transactionStatus = '4'; 
							$transactionLabel = 'Failed';
							//$paymentSubject = 'Transaction Failed';
							//$emailContent = $emailTemplate->emailFailedContent($transData->REQUESTOR_NAME, $merchantInfo->MERCHANT_NAME); 
							
						}elseif(in_array($STATUS, $arrayExpired)){ //Expired
							$transactionStatus = '5';
							$transactionLabel = 'Expired';
							//$paymentSubject = 'Transaction Expired';
							//$emailContent = $emailTemplate->emailFailedContent($transData->REQUESTOR_NAME, $merchantInfo->MERCHANT_NAME); 
						
						}elseif(in_array($STATUS, $arrayRefund)){ //Refund
							$transactionStatus = '6';
							$transactionLabel = 'Refund';
							//$paymentSubject = 'Transaction Expired';
							//$emailContent = '';
						
						}elseif(in_array($STATUS, $arrayVoid)){ //Void
							$transactionStatus = '7';
							$transactionLabel = 'Void';
							//$paymentSubject = 'Transaction Void';
							//$emailContent = $emailTemplate->emailFailedContent($transData->REQUESTOR_NAME, $merchantInfo->MERCHANT_NAME); 
						} 
						
						//Insert Data 
						if($transactionStatus != '0'){
							$transactionHDR = $dbEnterprise->rawQuery("
								INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

								SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, '".$transactionStatus."', '".$timeStamp."', CREATED_BY
								FROM tbl_transactions_hdr
								WHERE TRN = '".$transData->TRN."' AND MERCHANT_CODE = '".$transData->MERCHANT_CODE."' AND TRANS_STATUS = '2'");
							 
							//EOR 
							$EORARR = array();
							$EORARR['TRN'] = $transData->TRN;
							$EORARR['MERCHANT_CODE'] = $transData->MERCHANT_CODE;
							$EORARR['EOR'] = $eorDateTime; 
							$EORARR['CREATED_DATE'] = $timeStamp;
							$EORARR['CREATED_BY'] = $transData->REQUESTOR_EMAIL_ADDRESS;
							$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 
							
							//Audit Trail 
							$auditArr = array();
							$auditArr['TRN'] = $transData->TRN;
							$auditArr['MERCHANT_CODE'] = $transData->MERCHANT_CODE;
							$auditArr['EVENT_TYPE'] = 'ADD';
							$auditArr['EVENT_REMARKS'] = '';
							$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
							$auditArr['EVENT_REMARKS'] = 'Transaction '.$transactionLabel;
							$auditArr['CREATED_DATE'] = $timeStamp;
							$auditArr['CREATED_BY'] = $transData->REQUESTOR_EMAIL_ADDRESS;
							$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
											
							//Insert Data to IPG_GATEWAY
							$reponseTransInsert  = array();
							$reponseTransInsert['REQUEST_TIMESTAMP']		 = $REQUEST_TIMESTAMP;
							$reponseTransInsert['MERCHANT_ID'] 				 = $MERCHANT_ID;
							$reponseTransInsert['RESPCODE'] 			 	 = $RESPCODE;
							$reponseTransInsert['PAN'] 					 	 = $PAN;
							$reponseTransInsert['AMT'] 					  	 = $AMT;
							$reponseTransInsert['UNIQUETRANSACTIONCODE']	 = $UNIQUETRANSACTIONCODE;
							$reponseTransInsert['TRANREF']					 = $TRANREF;
							$reponseTransInsert['APPROVAL_CODE']			 = $APPROVAL_CODE;
							$reponseTransInsert['ECI']						 = $ECI;
							$reponseTransInsert['DATETIME']					 = $DATETIME;
							$reponseTransInsert['STATUS']					 = $STATUS;
							$reponseTransInsert['FAILREASON']				 = $FAILREASON;
							$reponseTransInsert['USER_DEFINED_1']			 = $USER_DEFINED_1;
							$reponseTransInsert['USER_DEFINED_2']			 = $USER_DEFINED_2;
							$reponseTransInsert['USER_DEFINED_3'] 			 = $USER_DEFINED_3;
							$reponseTransInsert['USER_DEFINED_4']			 = $USER_DEFINED_4;
							$reponseTransInsert['USER_DEFINED_5']			 = $USER_DEFINED_5;
							$reponseTransInsert['STORE_CARD_UNIQUE_ID']		 = $STORE_CARD_UNIQUE_ID;
							$reponseTransInsert['IPP_PERIOD']				 = $IPP_PERIOD;
							$reponseTransInsert['IPP_INTERESTTYPE']			 = $IPP_INTERESTTYPE;
							$reponseTransInsert['IPP_INTEREST_RATE']		 = $IPP_INTEREST_RATE;
							$reponseTransInsert['IPP_MERCHANT_ABSORB_RATE']	 = $IPP_MERCHANT_ABSORB_RATE;
							$reponseTransInsert['PAID_CHANNEL']				 = $PAID_CHANNEL;
							$reponseTransInsert['PAID_AGENT']				 = $PAID_AGENT;
							$reponseTransInsert['PAYMENT_CHANNEL']			 = $PAYMENT_CHANNEL;
							$reponseTransInsert['BACKEND_INVOICE']			 = $BACKEND_INVOICE;
							$reponseTransInsert['ISSUER_COUNTRY']			 = $ISSUER_COUNTRY;
							$reponseTransInsert['BANK_NAME']				 = $BANK_NAME;
							$reponseTransInsert['PROCESS_BY']				 = $PROCESS_BY;
							$reponseTransInsert['PAYMENT_SCHEME']			 = $PAYMENT_SCHEME;
							$reponseTransInsert['RATE_QUOTE_ID']			 = $RATE_QUOTE_ID;
							$reponseTransInsert['ORIGINAL_AMOUNT']			 = $ORIGINAL_AMOUNT;
							$reponseTransInsert['FX_RATE']					 = $FX_RATE;
							$reponseTransInsert['CURENCY_CODE']				 = $CURENCY_CODE; 
							$insertData = $dbGateway->insert("tbl_2c2p_response_trans",$reponseTransInsert);	
							/*
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
								$mail->addAddress($transData->REQUESTOR_EMAIL_ADDRESS, $transData->REQUESTOR_NAME);     	// Add a recipient 
								$mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 

								//Content
								$mail->isHTML(true);                                  
								// Set email format to HTML
								$mail->Subject = $paymentSubject;
								$mail->Body    = $emailContent; 

								$mail->send();
								
								echo 'Email has been sent successfully';
							} catch (Exception $e) { 
								echo 'Mail has not been sent due to Unknown reason.';
							} 
							*/
						}
					}else{
						//Do nothing
					}
				}
			}else{
			 
			}
		
		}
	}
?>
<?php 
	session_start();
	include("../../../../vendor/autoload.php");
	include("../../../../config.php"); 
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);


	$mail = new PHPMailer\PHPMailer\PHPMailer(true); 

	$timeStamp 	  = date('Y-m-d G:i:s');
	$jsonRequest = json_decode(file_get_contents("php://input"), true);

	$id = $jsonRequest['id'];
	$amount = $jsonRequest['amount'];
	$currency = $jsonRequest['currency'];
	$status = $jsonRequest['status'];
	$refNo  = $jsonRequest['requestReferenceNumber'];
 	 
	$response = array();
	if($jsonRequest){
		if($custom->upperCaseString($status) == "PAYMENT_SUCCESS"){

			//Get TRN
			$partnerResponseArr = array();
			$partnerResponseArr['CHECK_OUT_ID'] = $id;
			$partnerResponse = $dbGateway->getRow("SELECT IPG_REF_NUM 
				FROM tbl_paymaya_response_trans 
				WHERE CHECK_OUT_ID = :CHECK_OUT_ID LIMIT 1", $partnerResponseArr);
			
			if($partnerResponse){

				$payeeInfoArr = array();
				$payeeInfoArr['TRN'] = $partnerResponse->IPG_REF_NUM;
				$payeeInfo = $dbEnterprise->getRow("SELECT *
					FROM tbl_transactions_hdr 
					WHERE TRN = :TRN 
					ORDER BY HDR_ID DESC LIMIT 1", $payeeInfoArr);

				if($payeeInfo->TRANS_STATUS == '2'){//On Process

					$TRN	      = $payeeInfo->TRN;
					$merchantCode = $payeeInfo->MERCHANT_CODE;
				  	$merchantRefNum = $payeeInfo->MERCHANT_REF_NUM; 
					$payeeName	  = $payeeInfo->REQUESTOR_NAME;
					$payeeEmail	  = $payeeInfo->REQUESTOR_EMAIL_ADDRESS; 
					$payeeNumber  = $payeeInfo->REQUESTOR_MOBILE_NO;
					$pmCode       = $payeeInfo->PM_CODE;
					$peCode  	  = $payeeInfo->PE_CODE;

					$transDate    = $payeeInfo->CREATED_DATE;
					$newTransDate = date('Y-m-d G:i:s', strtotime($transDate . '+1 sec')); 
					$newTransDateFormat = date('l, F d, Y g:i:s A', strtotime($transDate . '+1 sec')); 
 

					//Set EOR
					$countEORArr = array();
					$countEORArr['MERCHANT_CODE'] = $merchantCode;
					$countEORArr['TRANS_STATUS'] = "3";

					$count = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS" , $countEORArr); 
					$count = $count->countColumn += 1;  
					$eorDateTime  = date('YmdHis').$count;


					//Get Convience Fee
					$entityRatesArr = array();
					$entityRatesArr['MERCHANT_CODE'] = $merchantCode;
					$entityRatesArr['PM_CODE'] = $pmCode;
					$entityRatesArr['PE_CODE'] = $peCode;
					$entityRateData = $dbEnterprise->getRow("SELECT * FROM tbl_payment_entity_rate 
						WHERE MERCHANT_CODE = :MERCHANT_CODE AND PE_CODE = :PE_CODE AND PM_CODE = :PM_CODE LIMIT 1", $entityRatesArr);

					//Get JV rates
					$jvFeeArr = array();
					$jvFeeArr['MERCHANT_CODE'] = $merchantCode;
					$jvFee = $dbEnterprise->getRow("SELECT * FROM tbl_ipg_jv_fees AS TIJF
						WHERE TIJF.`MERCHANT_CODE` = :MERCHANT_CODE LIMIT 1", $jvFeeArr);

					//Get Merchant Data
					$merchantQueryArr= array();
					$merchantQueryArr['MERCHANT_CODE'] = $merchantCode;
					$merchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1", $merchantQueryArr);


					$transactionListArr = array();
					$transactionListArr['TRN'] = $TRN;
					$transactionList = $dbEnterprise->getResults("SELECT * FROM tbl_transactions 
						WHERE TRN = :TRN", $transactionListArr);
			 		


					$totalAmountDue = 0;
					$transactionData = array();
					$count = 0;
					foreach($transactionList as $data){
						$transactionData[$count]['sub_merchant_code'] = $data->SUB_MERCHANT_CODE;
						$transactionData[$count]['transaction_payment_for'] = $data->TRANSACTION_PAYMENT_FOR;
						$transactionData[$count]['transaction_amount'] = $data->TRANSACTION_AMOUNT;

						$totalAmountDue += $data->TRANSACTION_AMOUNT;
						$count++; 
					}

					$transactionDataObject = json_decode(json_encode($transactionData), FALSE);

					if($entityRateData && $jvFee){

						$partnerCode = $entityRateData->PARTNER_CODE;

						$entityRateObj = array();
			 			$entityRateObj['er_type'] 				= $entityRateData->ER_TYPE;    
			 			$entityRateObj['entity_rate_amount'] 	= $entityRateData->ENTITY_RATE_AMOUNT;   
			 			$entityRateObj['ipg_fee'] 				= $entityRateData->IPG_FEE;

			 			$entityRatesJson = json_decode(json_encode($entityRateObj), FALSE);
			 			
			 			$_SESSION['IPG_PUBLIC']['TRANSACTIONS'] = $entityRatesJson; 
	 
						if($entityRateData->ER_TYPE == 'PERCENTAGE'){
							$entityRate   = $entityRateData->ENTITY_RATE_AMOUNT;
							$ipgFee 	  = $entityRateData->IPG_FEE;

							$jvCode 	  = $jvFee->JV_CODE;
							$cc1p 	 	  = $ipgFee * $jvFee->COMPANY_CODE_1_PERCENT;
							$cc2p 	  	  = $ipgFee * $jvFee->COMPANY_CODE_2_PERCENT; 

							//v2 
							$amountWithIPG = $totalAmountDue + $ipgFee; 
							$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
							$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
							
							$totalFee	= number_format($amountWithIPG + $totalAddedAmount,2); 
							$convenienceFee = number_format($totalAddedAmount,2);  
						}else{ 
							$entityRate   = $entityRateData->ENTITY_RATE_AMOUNT;
							$ipgFee 	  = $entityRateData->IPG_FEE;

							$jvCode 	  = $jvFee->JV_CODE;
							$cc1p 	 	  = $ipgFee * $jvFee->COMPANY_CODE_1_PERCENT;
							$cc2p 	  	  = $ipgFee * $jvFee->COMPANY_CODE_2_PERCENT; 

						 	$totalFee	    = number_format($totalAmountDue + $entityRate + $ipgFee,2);
						 	$convenienceFee = number_format($entityRate + $ipgFee,2);
						}


						//Email Content
						$entityType = $entityRateData->ENTITY_TYPE;

			 			$pdfContent = $emailTemplate->paymentConfirmationPDF($entityType, $TRN, $merchantRefNum, $newTransDateFormat, $eorDateTime, $payeeName, $payeeEmail, $transactionDataObject, $merchantCode, $newTransDate); 
			 			$emailPaymentacknowledgement = $emailTemplate->emailPaymentConfirmation($entityType, $TRN, $merchantRefNum, $newTransDateFormat, $eorDateTime, $payeeName, $payeeEmail, $transactionDataObject, $merchantCode);
			 			$paymentSubject = 'Payment Confirmation';

				 		$mpdf = new \Mpdf\Mpdf();
						$mpdf->WriteHTML($pdfContent); 
						$mpdf->Output('../../../../PAYMENT_TRANSACTION_PDF/'.$merchantCode.'-'.$TRN.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 


						$countParam = array();
						$countParam['MERCHANT_CODE'] = $merchantCode;
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
						$mpdf->Output('../../../../ACKNOWLEDGEMENT_RECEIPT/'.$ARpdfFilename, 'F', \Mpdf\Output\Destination::FILE); 
				
		 				//Insert Transaction
						$transactionHDR = $dbEnterprise->rawQuery("
							INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, AR_REF, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

							SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_FIRSTNAME, REQUESTOR_LASTNAME, REQUESTOR_MIDDLENAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, '".$convenienceFee."','".$ipgFee."','".$cc1p."','".$cc2p."','".$AR_REF."','".$jvCode."', '".$partnerCode."', '3', '".$newTransDate."', CREATED_BY
							FROM tbl_transactions_hdr
							WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$merchantCode."' AND TRANS_STATUS = '2'");					


						//EOR 
						$EORArr = array();
						$EORArr['TRN'] = $TRN;
						$EORArr['MERCHANT_CODE'] = $merchantCode;
						$EORArr['EOR'] = $eorDateTime; 
						$EORArr['CREATED_DATE'] = $timeStamp;
						$EORArr['CREATED_BY'] = $payeeEmail;
						$insertEOR = $dbEnterprise->insert("tbl_eor",$EORArr); 

						//Audit Trail 
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $merchantCode;
						$auditArr['EVENT_TYPE'] = 'ADD'; 
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
						$auditArr['EVENT_REMARKS'] = 'Transaction Completed - Reposting';
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $payeeEmail;
						$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
		
	 					//Sent Email
						$emailContent = $emailTemplate->emailSuccessContent($payeeName, $merchantInfo->MERCHANT_NAME, $merchantCode.'-'.$TRN.'.pdf', $emailPaymentacknowledgement); 
	 				
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
						    $mail->addAddress($payeeEmail, $payeeName);     	// Add a recipient 
						    $mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 

						    //Content
						    $mail->isHTML(true);                                  
						    // Set email format to HTML
						    $mail->Subject = $paymentSubject;
						    $mail->Body    = $emailContent; 

						    $mail->send();
						    
						    $addedMessage = 'Email has been sent successfully';
						} catch (Exception $e) { 
						    $addedMessage = 'Mail has not been sent due to Unknown reason.';
						}

						if($insertAudit){
							$response['STATUS'] = 0;
							$response['STATUS_CODE'] = "SUCCESS";
							$response['MESSAGE'] = "Payment Success. (".$addedMessage.")";
	  
						}else{
							$response['STATUS'] = 1;
							$response['STATUS_CODE'] = "ERROR";
							$response['MESSAGE'] = "Something went wrong. Please call the developer";
						}
					}else{
						$response['STATUS'] = 1;
						$response['STATUS_CODE'] = "ERROR";
						$response['MESSAGE'] = "Something went wrong on JV and Entity Rates";
					}
				}else{ 
					$response['STATUS'] = 1;
					$response['STATUS_CODE'] = "ERROR";
					$response['MESSAGE'] = "Something went wrong on transaction status";
				}
			}else{ 
				$response['STATUS'] = 1;
				$response['STATUS_CODE'] = "ERROR";
				$response['MESSAGE'] = "Something went wrong on partner gateway response";
			}



		}else{//Failed Transaction

			//Get TRN
			$partnerResponseArr = array();
			$partnerResponseArr['CHECK_OUT_ID'] = $id;
			$partnerResponse = $dbGateway->getRow("SELECT IPG_REF_NUM 
				FROM tbl_paymaya_response_trans 
				WHERE CHECK_OUT_ID = :CHECK_OUT_ID LIMIT 1", $partnerResponseArr);

			if($partnerResponse){
				$payeeInfoArr = array();
				$payeeInfoArr['TRN'] = $partnerResponse->IPG_REF_NUM;
				$payeeInfo = $dbEnterprise->getRow("SELECT *
					FROM tbl_transactions_hdr 
					WHERE TRN = :TRN 
					ORDER BY HDR_ID DESC LIMIT 1", $payeeInfoArr);
 				
 				if($payeeInfo){
					$TRN = $payeeInfo->TRN;
					$merchantCode = $payeeInfo->MERCHANT_CODE;
				  	$merchantRefNum = $payeeInfo->MERCHANT_REF_NUM; 
					$payeeName	  = $payeeInfo->REQUESTOR_NAME;
					$payeeEmail	  = $payeeInfo->REQUESTOR_EMAIL_ADDRESS; 
					$payeeNumber  = $payeeInfo->REQUESTOR_MOBILE_NO;
					$pmCode       = $payeeInfo->PM_CODE;
					$peCode  	  = $payeeInfo->PE_CODE;

					$countEORArr = array();
					$countEORArr['MERCHANT_CODE'] = $merchantCode;
					$countEORArr['TRANS_STATUS'] = "3";

					$count = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS" , $countEORArr); 
					$count = $count->countColumn += 1;  
					$eorDateTime  = date('YmdHis').$count;

					//Get Merchant Data
					$merchantQueryArr= array();
					$merchantQueryArr['MERCHANT_CODE'] = $merchantCode;
					$merchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1", $merchantQueryArr);


					$transQueryData = array();
					$transQueryData['TRN'] = $TRN;
					$transQueryData['MERCHANT_CODE'] = $merchantCode;
					$checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4')", $transQueryData);

					if(!$checkDatabase){ 

						$transactionHDR = $dbEnterprise->rawQuery("
							INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

							SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, '4', '".$timeStamp."', CREATED_BY
							FROM tbl_transactions_hdr
							WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$payeeInfo->MERCHANT_CODE."' AND TRANS_STATUS = '2'");

						//EOR 
						$EORARR = array();
						$EORARR['TRN'] = $TRN;
						$EORARR['MERCHANT_CODE'] = $merchantCode;
						$EORARR['EOR'] = $eorDateTime; 
						$EORARR['CREATED_DATE'] = $timeStamp;
						$EORARR['CREATED_BY'] = $payeeEmail;

						$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 

						//Audit Trail 
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $merchantCode;
						$auditArr['EVENT_TYPE'] = 'ADD'; 
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
						$auditArr['EVENT_REMARKS'] = 'Transaction Failed - Reposting';
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $payeeEmail;
						$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 	


						$paymentSubject = 'Transaction Failed';
						$emailContent = $emailTemplate->emailFailedContent($payeeName, $merchantInfo->MERCHANT_NAME); 
		
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
						    $mail->addAddress($payeeEmail, $payeeName);        	// Add a recipient 
						    $mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer'); 

						    //Content
						    $mail->isHTML(true);                                  
						    // Set email format to HTML
						    $mail->Subject = $paymentSubject;
						    $mail->Body    = $emailContent; 

						    $mail->send();
						    
						    $addedMessage = 'Email has been sent successfully';
						} catch (Exception $e) {
						    //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
						    $addedMessage = 'Mail has not been sent due to Unknown reason.';
						}
  						
  						if($insertAudit){
							$response['STATUS'] = 0;
							$response['STATUS_CODE'] = "SUCCESS";
							$response['MESSAGE'] = "Payment Success. (".$addedMessage.")";
	  
						}else{
							$response['STATUS'] = 1;
							$response['STATUS_CODE'] = "ERROR";
							$response['MESSAGE'] = "Something went wrong. Please call the developer";
						}

					}else{
						//Audit Trail 
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $merchantCode;
						$auditArr['EVENT_TYPE'] = 'ADD'; 
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
						$auditArr['EVENT_REMARKS'] = 'Transaction Failed - Reposting';
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $payeeEmail;
						$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 

  						if($insertAudit){
							$response['STATUS'] = 0;
							$response['STATUS_CODE'] = "SUCCESS";
							$response['MESSAGE'] = "Reposting Succsss - Payment Failed.";
	  
						}else{
							$response['STATUS'] = 1;
							$response['STATUS_CODE'] = "ERROR";
							$response['MESSAGE'] = "Something went wrong. Please call the developer";
						}					

					}
 				}else{

					$response['STATUS'] = 1;
					$response['STATUS_CODE'] = "ERROR";
					$response['MESSAGE'] = "Something went wrong. No Transaction found";
 				}
 			}else{
				$response['STATUS'] = 1;
				$response['STATUS_CODE'] = "ERROR";
				$response['MESSAGE'] = "Something went wrong on partner gateway response";
			}
		}
	}else{
		$response['STATUS'] = 1;
		$response['STATUS_CODE'] = "ERROR";
		$response['MESSAGE'] = "Parameters Needeed";

	}
	echo json_encode($response); 
?>
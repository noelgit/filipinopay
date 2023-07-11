<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	 
	require 'vendor/autoload.php'; 
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Writer\Csv;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

	$merchantAccount = $dbMerchant->getResults("SELECT MERCHANT_CODE,MERCHANT_EMAIL_ADDRESS FROM tbl_merchant_info");
	
	foreach($merchantAccount as $data){
		
		$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
 		$userEmail 	     = $data->MERCHANT_EMAIL_ADDRESS;
		$merchantCode    = $data->MERCHANT_CODE;
		$dateTimeToday   = date('YmdHsi');
		$currentDateTime = date('M d, Y h:s:i A'); 
		$timeStamp 	  	 = date('Y-m-d G:i:s');
		$dayToday 	     = strtoupper(date("l")); 
		$fileName        = $data->MERCHANT_CODE.'-'.$dateTimeToday; 
		
		$generatedDate = date("Y-m-d G:i:s");
		$generatedDateFormat = date("M d, Y H:i:s");

		$recipientQueryData = array();
		$recipientQueryData['STATUS'] = '1';
		$recipientQueryData['MERCHANT_CODE'] = $merchantCode;
		$merchantRecipient = $dbEnterprise->getRow("SELECT * FROM tbl_merchant_email_recipient WHERE STATUS = :STATUS AND MERCHANT_CODE = :MERCHANT_CODE", $recipientQueryData); 

		$schedQueryData = array();
		$schedQueryData['STATUS'] = '1';
		$schedQueryData['DAY'] = $dayToday;
		$schedQueryData['MERCHANT_CODE'] = $merchantCode;

		$merchantSched = $dbEnterprise->getRow("SELECT GET_TRANS_DAY FROM tbl_merchant_settlement_sched WHERE STATUS = :STATUS AND DAY = :DAY AND MERCHANT_CODE = :MERCHANT_CODE", $schedQueryData);

		
		if($merchantRecipient){
			$emailTO  = explode(";",$merchantRecipient->EMAIL_TO); 
			$emailCC  = explode(";",$merchantRecipient->EMAIL_CC); 
			$emailBCC = explode(";",$merchantRecipient->EMAIL_BCC); 
			 
			if($merchantSched){ 
				$transactionDate = explode(";",$merchantSched->GET_TRANS_DAY); 
				$count = 0;
				$addedCondition = '';
				$settlementDate = '';
				foreach($transactionDate AS $key => $value){
					$transactionDate  = strtoupper($value); 
					$dateofTrasaction = date("Y-m-d", strtotime("previous ".$transactionDate.""));
					$settlementDateformat = date("M d, Y", strtotime("previous ".$transactionDate.""));
					if($count >= 1){
						$settlementDate .= ", ".$settlementDateformat;
						$addedCondition .= " OR DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = '".$dateofTrasaction."' ";
					}else{
						$settlementDate .= $settlementDateformat;
						$addedCondition .= " DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = '".$dateofTrasaction."' ";
					}
					$count++;
				}	  



				$checkReportDate = $dbEnterprise->getRow("SELECT * FROM tbl_generated_documents 
					WHERE MERCHANT_CODE = '".$merchantCode."' AND REPORT_DATE = '".$dateofTrasaction."' AND DOC_TYPE_ID = '2' AND STATUS = '1' AND DELETED != '1'");

				if($checkReportDate){ 
				}else{


					//Report No.  
					$countReport = $dbEnterprise->getRow("SELECT COUNT(*) AS COUNT FROM tbl_generated_documents WHERE DOC_TYPE_ID = '2' AND MERCHANT_CODE = '".$merchantCode."' AND STATUS = '1' AND DELETED != '1' ");
	 
					$reportNo = sprintf("%06d", $countReport->COUNT + 1);
	 
					//Data 
					$infoQueryData = array(); 
					$infoQueryData['MERCHANT_CODE'] = $merchantCode;
					
					$merchantInfo = $dbEnterprise->getRow("SELECT vmas.`MERCHANT_CODE`, vmas.`MERCHANT_NAME` FROM vw_merchant_and_submerchant AS vmas WHERE vmas.`MERCHANT_CODE` = :MERCHANT_CODE", $infoQueryData);

					$merchantName = $merchantInfo->MERCHANT_NAME; 
					$merchantCode = $merchantInfo->MERCHANT_CODE; 

					$transactionData = $dbEnterprise->getResults("SELECT tth.`CREATED_DATE`, tth.`AR_REF`, tth.`REQUESTOR_NAME`, tt.`TRANSACTION_PAYMENT_FOR`, tth.`MERCHANT_REF_NUM`, tt.`TRANSACTION_AMOUNT`,
						(tth.`CONVENIENCE_FEE` + tt.`TRANSACTION_AMOUNT`) AS TOTAL_AMOUNT

						FROM tbl_transactions_hdr AS tth   

						INNER JOIN tbl_transactions AS tt ON tt.`TRN` = tth.`TRN`
						
				        WHERE tth.`MERCHANT_CODE` = :MERCHANT_CODE AND  
					    (".$addedCondition.")
						AND tth.`TRANS_STATUS` = '3' 
						GROUP BY tth.`TRN` 
						ORDER BY tth.`CREATED_DATE` DESC", $infoQueryData);

					//PDF
					$pdfContent = '		
						<html>
							<body>
								<div style="text-align:center;">
									<img src="http://filipinopay.com/paymentportal/IMAGES/DBP-Logo.png" style="width:75px; margin:auto;"><br><br>
									<label>LIST OF DAILY COLLECTIONS</label><br>
									<label>For <strong>'.$merchantName.'</strong></label><br>
									<label>Date: <strong>'.$settlementDate.'</strong></label>
								</div> 
								<br>
								<div style="width:100%; text-align:right;">
									<label>Report No.:'.$reportNo.'</label>
								</div>
								<div>
									<table border="1" style="font-size:12px; width:100%;">
										<tr>
											<th>Date and Time</th>
											<th>AR Number</th>
											<th>Name of Payor</th>
											<th>Particulars</th>
											<th>Reference Number</th>
											<th>Amount</th>
										</tr>
						';  

						$totalAmount = 0;
						foreach($transactionData as $data){  	
							$totalAmount += $data->TRANSACTION_AMOUNT;
							$pdfContent .= '
										<tr>
											<td>'.$data->CREATED_DATE.'</td>
											<td>'.$data->AR_REF.'</td>
											<td>'.$data->REQUESTOR_NAME.'</td>
											<td>'.$data->TRANSACTION_PAYMENT_FOR.'</td> 
											<td>'.$data->MERCHANT_REF_NUM.'</td> 
											<td>P'.number_format($data->TRANSACTION_AMOUNT,2).'</td>  
										</tr>
							'; 
						}  
						$pdfContent .= '
								<tr>
									<th colspan="5" style="font-size:15px; text-align:right;">Total Amount:</th>
									<th style="font-size:15px;">P'.number_format($totalAmount,2).'</th>
								</tr>
								</table>
								<br>
								<div style="text-align:right; width:100%;">
									<label><small>Date and time generated: '.$generatedDateFormat.'</small></label>
								</div>
							</div> 
						</body>
					</html>';
					 
	 

					$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
	    			$mpdf->setFooter('Page No.{PAGENO} of {nb}');
					$mpdf->WriteHTML($pdfContent);  
					$mpdf->Output('ReportFiles/LDC/'.$fileName.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 
					 

					$generatedArr = array();
					$generatedArr['DOC_TYPE_ID'] = '2';
					$generatedArr['MERCHANT_CODE'] = $merchantCode;
					$generatedArr['REPORT_NO'] = $reportNo;
					$generatedArr['REPORT_DATE'] = $dateofTrasaction;
					$generatedArr['GENERATED_DATETIME'] = $generatedDate;
					$generatedArr['FILENAME'] 	  = $fileName.".pdf";
					$generatedArr['FILE_PATH'] 	  = 'ReportFiles/LDC/';
					$generatedArr['STATUS'] 	  = '1';
					$generatedArr['DELETED'] 	  = '0';
					$generatedArr['CREATED_DATE'] = $timeStamp;
					$generatedArr['CREATED_BY']   = "CRON JOB";
					$insertReport = $dbEnterprise->insert("tbl_generated_documents",$generatedArr);


					
					//EMAIL
					$emailContent = $emailTemplate->emailLDCReport($merchantName, $currentDateTime);  
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
					    $mail->addReplyTo(CONTACT_EMAIL_FROM, 'IPG Mailer');
					    foreach($emailTO as $key => $value){
					    	$mail->addAddress($value, $merchantName);     
					    }		
					    foreach($emailCC as $key => $value){
				   	 		$mail->addCC($value); 
					    }
					    foreach($emailBCC as $key => $value){
				   	 		$mail->addBCC($value); 
					    }
					    
					    $mail->addAttachment('ReportFiles/LDC/'.$fileName.'.pdf');  
						  
					    //Content
					    $mail->isHTML(true);                                  // Set email format to HTML
					    $mail->Subject = 'LDC-'.$dateTimeToday;
					    $mail->Body    = $emailContent; 

					    $mail->send();

					    $mail->ClearAllRecipients( ); 
					    $mail->clearAttachments(); 
					} catch (Exception $e) {
					    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; 
					}
				}
			}
		}
	}
?>
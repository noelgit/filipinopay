<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php';  
	require 'vendor/autoload.php';  
 
	$mpdf = new \Mpdf\Mpdf();

	$transactionArr = array();
	$transactionArr['YEAR'] = date("Y");
	$transactionArr["MONTH"] = date("m"); 
	$transactionHDRList = $dbEnterprise->getResults("SELECT HDR.*,
		CONCAT(HDR.`REQUESTOR_LASTNAME`,'_',HDR.`AR_REF`,'_',HDR.`MERCHANT_REF_NUM`,'.pdf') AS AR_FILE,
		VW_MERCH.`MERCHANT_NAME`

		FROM tbl_transactions_hdr AS HDR 

		INNER JOIN vw_merchant_and_submerchant AS VW_MERCH ON VW_MERCH.`MERCHANT_CODE` = HDR.`MERCHANT_CODE`

		WHERE HDR.`TRANS_STATUS` = '3' AND YEAR(HDR.`CREATED_DATE`) = :YEAR AND MONTH(HDR.`CREATED_DATE`) = :MONTH AND HDR.`AR_REF` != '' 
		GROUP BY HDR.`TRN`", $transactionArr);
	
	$TRNArr = array();
	foreach($transactionHDRList as $data){
		if (!in_array($data->TRN, $TRNArr)){ 
	  		array_push($TRNArr,$data->TRN);
	  	} 
  	}

	$transactionList = $dbEnterprise->getResults("SELECT * FROM tbl_transactions AS TRANS WHERE TRANS.`TRN` IN (" . implode(',', $TRNArr) . ")");

	$transactionDataArr = array();  
	foreach($transactionList as $data){ 

		$transactionDataArr[$data->TRN][] = array(
			"TRANS_ID" => $data->TRANS_ID,
			"PAYMENT_FOR" => $data->TRANSACTION_PAYMENT_FOR,
			"TRANSACTION_AMOUNT" => $data->TRANSACTION_AMOUNT,
		);
 
	} 

	foreach($transactionHDRList as $data){ 	 
 
		$qrCodeLink = BASE_URL."verify-ar.php?TRN=".$data->TRN."&MCH_CODE=".$data->MERCHANT_CODE."&AR=".$data->AR_REF;
		$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$qrCodeLink.'\' style=\'margin:auto;\'>'; 

		$totalAmountDue = 0;
		$particulars = ''; 

		$i = 0;
		$len = count($transactionDataArr[$data->TRN]);
 	 
		foreach($transactionDataArr[$data->TRN] AS $trans){    
			$totalAmountDue += $trans['TRANSACTION_AMOUNT'];   
			if ($i == $len - 1) {
				$particulars  .= '<label>'.$trans['PAYMENT_FOR'].' </label>';  
		    }else{
				$particulars  .= '<label>'.$trans['PAYMENT_FOR'].', </label>';  
		    } 
 
		    $i++;
		}   

		$totalAmountPaid = $totalAmountDue + $data->CONVENIENCE_FEE + $data->TAX;

		$ARPDFContent = '
			<div style="font-family:sans-serif;"> 
				<div>
					<img src="http://filipinopay.com/paymentportal/IMAGES/DBP-Logo.png" style="width:75px;">
				</div>
				<div style="width:100%; text-align:center;"> 
					<div style="height:20px;"></div> 
					<h3 style="font-size:22px; text-decoration: underline;">ACKNOWLEDGEMENT RECEIPT</h3>
					<span><strong>AR Number: '.$data->AR_REF.'<strong></span><br>
					<span>'.$custom->readableDateTime($data->CREATED_DATE).'<span>
					<div style="height:50px;"></div> 
				</div>

				<div style="width:100%; font-size:13px;">
					<strong>Agency Name: </strong><span>'.$data->MERCHANT_NAME.'</span><br><br>
					<strong>Name of Payor: </strong><span>'.$data->REQUESTOR_NAME.'</span><br><br>
					<strong>Particulars: </strong><span>'.$particulars.'</span><br><br>
					<strong>Amount: </strong><span>P'.number_format($totalAmountDue,2).'</span><br><br>
					<strong>Service Charge: </strong><span>P'.number_format($data->CONVENIENCE_FEE,2).'</span><br><br>
					<strong>Tax: </strong><span>P'.number_format($data->TAX,2).'</span><br><br>
					<strong>Total Amount: </strong><span>P'.number_format($totalAmountPaid,2).'</span><br><br>
					<strong>Reference No.: </strong><span>'.$data->MERCHANT_REF_NUM.'</span><br><br>
					<div style="height:40px;"></div> 
				</div>

					<div style="height:40px;"></div> 
				<div style="text-align:center;"> 
					<label>This is a system generated receipt. Signature is not required.</label><br><br>

			 		<barcode code="'.$qrCodeLink.'" type="QR" class="barcode" size="1.5" error="M" disableborder="1" />	
					<br><br>
			 		<label><small>Scan this QR Code to validate the authenticity of this receipt</small> </label>
			 	</div> 
			</div>';  
  
		$ARpdfFilename = $data->REQUESTOR_LASTNAME.'_'.$data->AR_REF.'_'.$data->MERCHANT_REF_NUM.'.pdf';

		if(!file_exists("ARFiles/".$ARpdfFilename)){
			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($ARPDFContent); 
			$mpdf->Output('ARFiles/'.$ARpdfFilename, 'F', \Mpdf\Output\Destination::FILE); 
		} 
	} 
	

	$zip = new ZipArchive();
	$zipFile="AR-".date("m")."-".date("Y").".zip";

	if(file_exists("ARFiles/ZIP/".$zipFile)) { 
        unlink("ARFiles/ZIP/".$zipFile);  
	} 
	if ($zip->open("ARFiles/ZIP/".$zipFile, ZIPARCHIVE::CREATE) != TRUE) {
        die ("Could not open archive");
	}

	foreach($transactionHDRList as $data){ 
   		$zip->addFile("ARFiles/".$data->AR_FILE,$data->AR_FILE);
	} 
 
	$zip->close();  
	

	$emailContent = "
		<html>
			<head></head>
			<body> 
				<p>To whom it may concern:</p>
				<p>Attached herewith is a zip file of AR(s) for the month of <strong>".date("F")." ".date("Y")."</strong></p> 
				<p>Thank you,</p>
				<p>Filipinopay</p>

			</body>
		</html>"; 

	$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
		  
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

	    if(COA_AR_EMAILS){
	   		$COAAREmails = explode(",",COA_AR_EMAILS);
	    }
	    if(COA_AR_EMAILS_CC){
	   		$COAAREmailsCC = explode(",",COA_AR_EMAILS_CC);
	    }
	    if(COA_AR_EMAILS_BCC){
	   		$COAAREmailsBCC = explode(",",COA_AR_EMAILS_BCC);
	    }	     

	    if(!empty($COAAREmails)){
		    foreach($COAAREmails as $key => $value){
		    	$mail->addAddress($value);     
		    }		
		}

	    if(!empty($COAAREmailsCC)){
		    foreach($COAAREmailsCC as $key => $value){
	   	 		$mail->addCC($value); 
		    }
		}

	    if(!empty($COAAREmailsBCC)){
		    foreach($COAAREmailsBCC as $key => $value){
	   	 		$mail->addBCC($value); 
		    }
		}
	    
	    $mail->addAttachment('ARFiles/ZIP/'.$zipFile);  
		  
	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Acknowledgement Receipt: '.date("F")." ".date("Y");
	    $mail->Body    = $emailContent; 

	    $mail->send();

	    $mail->ClearAllRecipients( ); 
	    $mail->clearAttachments(); 
	} catch (Exception $e) {
	    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo; 
	} 
	
?>
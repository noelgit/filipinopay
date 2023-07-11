<?php 
	class emailTemplate{

		function acknowledgementReceiptEmail($AR_REF, $payorName){
	 		$emailContent = "
	 		<html>
				<head></head>
				<body> 
					<p>Hi ".$payorName.", </p>
					<p>Attached herewith is your acknowledgement receipt - <strong>".$AR_REF."</strong></p> 
 					<p>Thank you,</p>
 					<p>Filipinopay</p>

				</body>
			</html>
			"; 

			return $emailContent; 
		}

		function acknowledgementReceiptPDF($AR_REF, $createdDate, $merchantName, $payorName, $transactionsData, $convenienceFee, $tax, $merchantRefNum, $qrCodeLink){ 
			
			$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$qrCodeLink.'\' style=\'margin:auto;\'>'; 

			$totalAmountDue = 0;
			$particulars = ''; 

			$i = 0;
			$len = count($transactionsData);
			foreach($transactionsData AS $data){   
				$totalAmountDue += $data->transaction_amount;   
				if ($i == $len - 1) {
					$particulars  .= '<label>'.$data->transaction_payment_for.' </label>';  
			    }else{
					$particulars  .= '<label>'.$data->transaction_payment_for.', </label>';  
			    } 
			    $i++;
			}   
			$totalAmountPaid = $totalAmountDue + $convenienceFee + $tax;
  
			$pdfContent = '
				<div style="font-family:sans-serif;"> 
					<div>
						<img src="'.IMG.'DBP-Logo.png" style="width:75px;">
					</div>
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<h3 style="font-size:22px; text-decoration: underline;">ACKNOWLEDGEMENT RECEIPT</h3>
						<span><strong>AR Number: '.$AR_REF.'<strong></span><br>
						<span>'.$createdDate.'<span>
						<div style="height:50px;"></div> 
					</div>

					<div style="width:100%; font-size:13px;">
						<strong>Agency Name: </strong><span>'.$merchantName.'</span><br><br>
						<strong>Name of Payor: </strong><span>'.$payorName.'</span><br><br>
						<strong>Particulars: </strong><span>'.$particulars.'</span><br><br>
						<strong>Amount: </strong><span>P'.number_format($totalAmountDue,2).'</span><br><br>
						<strong>Service Charge: </strong><span>P'.number_format($convenienceFee,2).'</span><br><br>
						<strong>Tax: </strong><span>P'.number_format($tax,2).'</span><br><br>
						<strong>Total Amount: </strong><span>P'.number_format($totalAmountPaid,2).'</span><br><br>
						<strong>Reference No.: </strong><span>'.$merchantRefNum.'</span><br><br>
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
			return $pdfContent; 
		}


		function paymentRequestPDF($entityType, $TRN, $merchantRefNum, $dateTime, $name, $emailAddress, $transactionsData, $createdDate){ 
			$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.json_encode($transactionsData).'\' >'; 

			foreach($transactionsData AS $data){  
				$totalAmountDue += $data->transaction_amount; 
			}
			if($_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->er_type == 'PERCENTAGE'){
				$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount; 
				$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->ipg_fee;
				
				//MDR
				//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee;  
 				//$totalFee	 = $totalAmountDue + $totalAddedAmount;

				//v2 
				$amountWithIPG = $totalAmountDue + $ipgFee; 
				$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
				$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
				$totalFee	= $amountWithIPG + $totalAddedAmount; 

				$RateDisplay = $totalAddedAmount; 
		 	}else{ 
		 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
				$ipgFee 	 = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->ipg_fee;
			 	$totalFee	 = $totalAmountDue + $entityRate + $ipgFee;
			 	$RateDisplay = number_format($entityRate + $ipgFee,2);
		 	} 

		 	$qrCodeArray = array();
			$qrCodeArray['TRN'] = $TRN;
			$qrCodeArray['TRANSACTION_DETAILS'] = $transactionsData;
			$qrCodeArray['CONVENIENCE_FEE'] 	= $RateDisplay;
			$qrCodeArray['TRANSACTION_DATE'] 	= $createdDate;
			$qrCodeArray['TOTAL_AMOUNT'] 		= $totalFee;

			$pdfContent = ' 
				<div style="font-family:sans-serif;"> 
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<span style="font-size:22px;">Payment Request</h3>
						<div style="height:60px;"></div> 
					</div>
					<div style="width:100%; font-size:13px;">
						<strong>Mode of Payment: </strong><span>'.$entityType.'</span><br><br>
						<strong>TRN: </strong><span>'.$TRN.'</span><br><br>
						<strong>Merchant Ref Number: </strong><span>'.$merchantRefNum.'</span><br><br>
						<strong>Transaction Date and Time: </strong><span>'.$dateTime.'</span><br><br>
						<strong>Payee: </strong><span>'.$name.'</span><br><br>
						<strong>Email Address: </strong><span>'.$emailAddress.'</span><br><br>
						<i style="color:#398eda;">Important reminder: Validity of the payment request is: 24 hours</i> 
					</div> 
					<br><br><br>
					<div style="text-align:center;"> 
					<barcode code="'.str_replace('FP-', '',$TRN).'" type="CODABAR" text="1"/><br>
					'.str_replace('FP-', '',$TRN).'
					<br><br>
			 		<barcode code="'.str_replace('"',"'", json_encode($qrCodeArray)).'" type="QR" class="barcode" size="2" error="M" disableborder="1" />	
				 	</div> 
				</div>';    
			return $pdfContent; 
		}

		function paymentConfirmationPDF($entityType, $TRN, $merchantRefNum, $dateTime, $EOR, $name, $emailAddress, $transactionsData, $merchantCode, $createdDate){ 
			
			$dbEnterprise = new db(DB_USER_ENTERPRISE, DB_PASSWORD_ENTERPRISE, DB_NAME_ENTERPRISE, DB_HOST_ENTERPRISE);

			$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.json_encode($transactionsData).'\' >'; 

			$totalAmountDue = 0;
			$transaction = ''; 
			foreach($transactionsData AS $data){ 
				$subMerchantQueryData = array();
				$subMerchantQueryData['MERCHANT_CODE'] = $merchantCode;
				$subMerchantQueryData['SUB_MERCHANT_CODE'] = $data->sub_merchant_code;

				$SubmerchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE AND SUB_MERCHANT_CODE = :SUB_MERCHANT_CODE LIMIT 1", $subMerchantQueryData);

				$transaction  .= '<tr style="border:1px solid #5b9bd5;">';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$SubmerchantInfo->SUB_MERCHANT_NAME.'</td>';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$data->transaction_payment_for.'</td>';
				$transaction  .= '	<td style="text-align:right; border:1px solid #5b9bd5;">'.number_format($data->transaction_amount,2).'</td>';
				$transaction  .= '</tr>';

				$totalAmountDue += $data->transaction_amount; 
			} 

			if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->er_type == 'PERCENTAGE'){
				$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount; 
				$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
				
				//MDR
				//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee; 
				//$totalFee	 = $totalAmountDue + $totalAddedAmount;

				//v2 
				$amountWithIPG = $totalAmountDue + $ipgFee; 
				$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
				$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
				$totalFee	= $amountWithIPG + $totalAddedAmount; 

				$RateDisplay = $totalAddedAmount;  
		 	}else{ 
		 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
				$ipgFee 	 = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
			 	$totalFee	 = $totalAmountDue + $entityRate + $ipgFee;
			 	$RateDisplay = number_format($entityRate + $ipgFee,2);
		 	}  
 
		 	$qrCodeArray = array();
			$qrCodeArray['TRN'] = $TRN;
			$qrCodeArray['TRANSACTION_DETAILS'] = $transactionsData;
			$qrCodeArray['CONVENIENCE_FEE'] 	= $RateDisplay;
			$qrCodeArray['TRANSACTION_DATE'] 	= $createdDate;
			$qrCodeArray['TOTAL_AMOUNT'] 		= $totalFee;
 
			$pdfContent = '
				<div style="font-family:sans-serif;"> 
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<span style="font-size:22px;">Payment Confirmation Receipt</h3>
						<div style="height:50px;"></div> 
					</div>

					<div style="width:100%; font-size:13px;">
						<strong>Mode of Payment: </strong><span>'.$entityType.'</span><br><br>
						<strong>TRN: </strong><span>'.$TRN.'</span><br><br>
						<strong>Merchant Ref Number: </strong><span>'.$merchantRefNum.'</span><br><br>
						<strong>Transaction Date and Time: </strong><span>'.$dateTime.'</span><br><br>
						<strong>Payee: </strong><span>'.$name.'</span><br><br>
						<strong>Email Address: </strong><span>'.$emailAddress.'</span><br><br>
						<strong>EOR: </strong><span>'.$EOR.'</span>
						<div style="height:40px;"></div> 
					</div>

					<div style="width:80%; padding:0px 10%;"> 
						<table style="text-align: center; width: 100%; border:1px solid #5b9bd5; font-family:sans-serif;"> 
								<tr style="background: #5b9bd5; border:1px solid black;">
									<td style="color: white; padding:10px 0px; ">Merchant Name</td>
									<td style="color: white;">Payment For</td>
									<td style="color: white;">Amount</td>
								</tr>
								'.$transaction.' 
						</table> 
						<div style="height:40px;"></div> 
					</div> 
					
					<div style="width:60%; margin:0px 20%; padding:10px; text-align:center; border:1px solid black;"> 
						<label>Payment Summary</label><br><br>
						<table style="width:100%; font-family:sans-serif;" >
							<tr>
								<td style="padding-bottom:10px;">Amount Due:</td>
								<td style="text-align:right;">PHP '.number_format($totalAmountDue,2).'</td>
							</tr>
							<tr>
								<td style="padding-bottom:10px;">Convenience Fee:</td>
								<td style="text-align:right;">PHP '.number_format($RateDisplay,2).'</td>
							</tr>
							<tr style="border-top:1px solid black;">
								<td style="border-top:1px solid black; padding-top:10px;">Total:</td>
								<td style="text-align:right; border-top:1px solid black;"><strong>PHP '.number_format($totalFee,2).'</strong></td>
							</tr>
						</table> 
					</div>
	 				<div style="height:40px;"></div> 
					<div style="text-align:center;"> 
					<barcode code="'.str_replace('FP-', '',$TRN).'" type="CODABAR" text="1"/><br>
					'.str_replace('FP-', '',$TRN).'
					<br><br>
			 		<barcode code="'.str_replace('"',"'", json_encode($qrCodeArray)).'" type="QR" class="barcode" size="2" error="M" disableborder="1" />	
				 	</div> 
				</div>';  
			return $pdfContent; 
		}

		function OTCPaymentConfirmationPDF($TRN, $dateTime, $EOR, $name, $emailAddress, $merchantCode, $createdDate, $type){ 
			
			$dbEnterprise = new db(DB_USER_ENTERPRISE, DB_PASSWORD_ENTERPRISE, DB_NAME_ENTERPRISE, DB_HOST_ENTERPRISE);
			
			$transactionsDetails = $dbEnterprise->getResults("SELECT SUB_MERCHANT_CODE, TRANSACTION_PAYMENT_FOR, TRANSACTION_AMOUNT FROM tbl_transactions WHERE TRN = '".$TRN."'");
			
			$entityDetails = $dbEnterprise->getRow("SELECT tth.`CREATED_DATE`, tper.`ENTITY_TYPE`, tper.`ER_TYPE`, tper.`ENTITY_RATE_AMOUNT`, tper.`IPG_FEE` FROM tbl_transactions_hdr AS tth 

				INNER JOIN tbl_payment_entity_rate AS tper
				ON tper.`MERCHANT_CODE` = tth.`MERCHANT_CODE` AND tper.`PE_CODE` = tth.`PE_CODE`

				WHERE tth.`TRN` = '".$TRN."' ORDER BY tth.`CREATED_DATE` DESC LIMIT 1");
			
			if($type == "pdf"){
				$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.json_encode($transactionsDetails).'\' >'; 
			}
			
			$totalAmountDue = 0;
			$transaction = ''; 
			foreach($transactionsDetails AS $data){ 
				$subMerchantQueryData = array();
				$subMerchantQueryData['MERCHANT_CODE'] = $merchantCode;
				$subMerchantQueryData['SUB_MERCHANT_CODE'] = $data->SUB_MERCHANT_CODE;

				$SubmerchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE AND SUB_MERCHANT_CODE = :SUB_MERCHANT_CODE LIMIT 1", $subMerchantQueryData);

				$transaction  .= '<tr style="border:1px solid #5b9bd5;">';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$SubmerchantInfo->SUB_MERCHANT_NAME.'</td>';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$data->TRANSACTION_PAYMENT_FOR.'</td>';
				$transaction  .= '	<td style="text-align:right; border:1px solid #5b9bd5;">'.number_format($data->TRANSACTION_AMOUNT).'</td>';
				$transaction  .= '</tr>';

				$totalAmountDue += $data->TRANSACTION_AMOUNT; 
			} 

		
			if($entityDetails->ER_TYPE == 'PERCENTAGE'){
				$entityRate   = $entityDetails->ENTITY_RATE_AMOUNT; 
				$ipgFee 	  = $entityDetails->IPG_FEE;
				
				//MDR
				//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee; 
				//$totalFee	 = $totalAmountDue + $totalAddedAmount;
				
				//v2 
				$amountWithIPG = $totalAmountDue + $ipgFee; 
				$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
				$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
				$totalFee	= $amountWithIPG + $totalAddedAmount; 

				$RateDisplay = $totalAddedAmount;  
		 	}else{ 
		 		$entityRate  = $entityDetails->ENTITY_RATE_AMOUNT;
				$ipgFee 	 = $entityDetails->IPG_FEE;
			 	$totalFee	 = $totalAmountDue + $entityRate + $ipgFee;
			 	$RateDisplay = number_format($entityRate + $ipgFee,2);
		 	}  
 
		 	$qrCodeArray = array();
			$qrCodeArray['TRN'] = $TRN;
			$qrCodeArray['TRANSACTION_DETAILS'] = $transactionsDetails;
			$qrCodeArray['CONVENIENCE_FEE'] 	= $RateDisplay;
			$qrCodeArray['TRANSACTION_DATE'] 	= $entityDetails->CREATED_DATE;
			$qrCodeArray['TOTAL_AMOUNT'] 		= $totalFee;
 
			$pdfContent = '
				<div style="font-family:sans-serif;"> 
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<span style="font-size:22px;">Payment Confirmation Receipt</h3>
						<div style="height:50px;"></div> 
					</div>

					<div style="width:100%; font-size:13px;">
						<strong>Mode of Payment: </strong><span>'.$entityDetails->ENTITY_TYPE.'</span><br><br>
						<strong>TRN: </strong><span>'.$TRN.'</span><br><br>
						<strong>Transaction Date and Time: </strong><span>'.$dateTime.'</span><br><br>
						<strong>Payee: </strong><span>'.$name.'</span><br><br>
						<strong>Email Address: </strong><span>'.$emailAddress.'</span><br><br>
						<strong>EOR: </strong><span>'.$EOR.'</span>
						<div style="height:40px;"></div> 
					</div>

					<div style="width:80%; padding:0px 10%;"> 
						<table style="text-align: center; width: 100%; border:1px solid #5b9bd5; font-family:sans-serif;"> 
								<tr style="background: #5b9bd5; border:1px solid black;">
									<td style="color: white; padding:10px 0px; ">Merchant Name</td>
									<td style="color: white;">Payment For</td>
									<td style="color: white;">Amount</td>
								</tr>
								'.$transaction.' 
						</table> 
						<div style="height:40px;"></div> 
					</div> 
					
					<div style="width:60%; margin:0px 20%; padding:10px; text-align:center; border:1px solid black;"> 
						<label>Payment Summary</label><br><br>
						<table style="width:100%; font-family:sans-serif;" >
							<tr>
								<td style="padding-bottom:10px;">Amount Due:</td>
								<td style="text-align:right;">PHP '.number_format($totalAmountDue,2).'</td>
							</tr>
							<tr>
								<td style="padding-bottom:10px;">Convenience Fee:</td>
								<td style="text-align:right;">PHP '.number_format($RateDisplay,2).'</td>
							</tr>
							<tr style="border-top:1px solid black;">
								<td style="border-top:1px solid black; padding-top:10px;">Total:</td>
								<td style="text-align:right; border-top:1px solid black;"><strong>PHP '.number_format($totalFee,2).'</strong></td>
							</tr>
						</table> 
					</div>';
				if($type == "pdf"){
					$pdfContent .= '
		 				<div style="height:40px;"></div> 
						<div style="text-align:center;"> 
						<barcode code="'.str_replace('FP-', '',$TRN).'" type="CODABAR" text="1"/><br>
						'.str_replace('FP-', '',$TRN).'
						<br><br>
				 		<barcode code="'.str_replace('"',"'", json_encode($qrCodeArray)).'" type="QR" class="barcode" size="2" error="M" disableborder="1" />	
					 	</div>';
				}
			$pdfContent .= '</div>'; 			
			return $pdfContent; 
		}


		
		function emailPaymentRequest($entityType, $TRN, $merchantRefNum, $dateTime, $name, $emailAddress, $transactionsData){ 
			$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.json_encode($transactionsData).'\' >'; 

			$pdfContent = '
				<div style="font-family:sans-serif;"> 
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<span style="font-size:22px;">Payment Request</h3>
						<div style="height:60px;"></div> 
					</div>
					<div style="width:100%; font-size:13px;">
						<strong>Mode of Payment: </strong><span>'.$entityType.'</span><br><br>
						<strong>TRN: </strong><span>'.$TRN.'</span><br><br>
						<strong>Merchant Ref Number: </strong><span>'.$merchantRefNum.'</span><br><br>
						<strong>Transaction Date and Time: </strong><span>'.$dateTime.'</span><br><br>
						<strong>Payee: </strong><span>'.$name.'</span><br><br>
						<strong>Email Address: </strong><span>'.$emailAddress.'</span><br><br>
						<i style="color:#398eda;">Important reminder: Validity of the payment request is: 24 hours</i> 
					</div>  
				</div>';    
			return $pdfContent; 
		}

		function emailPaymentConfirmation($entityType, $TRN, $merchantRefNum, $dateTime, $EOR, $name, $emailAddress, $transactionsData, $merchantCode){ 
			
			$dbEnterprise = new db(DB_USER_ENTERPRISE, DB_PASSWORD_ENTERPRISE, DB_NAME_ENTERPRISE, DB_HOST_ENTERPRISE);

			$code = '<img src=\'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.json_encode($transactionsData).'\' >'; 

			$totalAmountDue = 0;
			$transaction = ''; 
			foreach($transactionsData AS $data){ 

				$subMerchantQueryData = array();
				$subMerchantQueryData['MERCHANT_CODE'] = $merchantCode;
				$subMerchantQueryData['SUB_MERCHANT_CODE'] = $data->sub_merchant_code;

				$SubmerchantInfo = $dbEnterprise->getRow("SELECT * FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE AND SUB_MERCHANT_CODE = :SUB_MERCHANT_CODE LIMIT 1", $subMerchantQueryData);

				$transaction  .= '<tr style="border:1px solid #5b9bd5;">';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$SubmerchantInfo->SUB_MERCHANT_NAME.'</td>';
				$transaction  .= '	<td style="padding:10px 0px; border:1px solid #5b9bd5;">'.$data->transaction_payment_for.'</td>';
				$transaction  .= '	<td style="text-align:right; border:1px solid #5b9bd5;">'.number_format($data->transaction_amount,2).'</td>';
				$transaction  .= '</tr>';

				$totalAmountDue += $data->transaction_amount; 
			} 

			if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->er_type == 'PERCENTAGE'){
				$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
				$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
				
				//MDR
				//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee; 
				//$totalFee	 = $totalAmountDue + $totalAddedAmount;
				
				//v2 
				$amountWithIPG = $totalAmountDue + $ipgFee; 
				$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
				$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
				$totalFee	= $amountWithIPG + $totalAddedAmount; 

				$RateDisplay = $totalAddedAmount;  
		 	}else{ 
		 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
				$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
				
			 	$totalFee	 = $totalAmountDue + $entityRate + $ipgFee;
			 	$RateDisplay = number_format($entityRate + $ipgFee,2);
		 	} 

			$emailAcknowledgementContent = '
				<div style="font-family:sans-serif;"> 
					<div style="width:100%; text-align:center;"> 
						<div style="height:20px;"></div> 
						<span style="font-size:22px;">Payment Confirmation Receipt</h3>
						<div style="height:50px;"></div> 
					</div>

					<div style="width:100%; font-size:13px;">
						<strong>Mode of Payment: </strong><span>'.$entityType.'</span><br><br>
						<strong>TRN: </strong><span>'.$TRN.'</span><br><br>
						<strong>Merchant Ref Number: </strong><span>'.$merchantRefNum.'</span><br><br>
						<strong>Transaction Date and Time: </strong><span>'.$dateTime.'</span><br><br>
						<strong>Payee: </strong><span>'.$name.'</span><br><br>
						<strong>Email Address: </strong><span>'.$emailAddress.'</span><br><br>
						<strong>EOR: </strong><span>'.$EOR.'</span>
						<div style="height:40px;"></div> 
					</div>

					<div style="width:80%; padding:0px 10%;"> 
						<table style="text-align: center; width: 100%; border:1px solid #5b9bd5; font-family:sans-serif;"> 
								<tr style="background: #5b9bd5; border:1px solid black;">
									<td style="color: white; padding:10px 0px; ">Merchant Name</td>
									<td style="color: white;">Payment For</td>
									<td style="color: white;">Amount</td>
								</tr>
								'.$transaction.' 
						</table> 
						<div style="height:40px;"></div> 
					</div> 
					
					<div style="width:60%; margin:0px 20%; padding:10px; text-align:center; border:1px solid black;"> 
						<label>Payment Summary</label><br><br>
						<table style="width:100%; font-family:sans-serif;" >
							<tr>
								<td style="padding-bottom:10px;">Amount Due:</td>
								<td style="text-align:right;">PHP '.number_format($totalAmountDue,2).'</td>
							</tr>
							<tr>
								<td style="padding-bottom:10px;">Convenience Fee:</td>
								<td style="text-align:right;">PHP '.number_format($RateDisplay,2).'</td>
							</tr>
							<tr style="border-top:1px solid black;">
								<td style="border-top:1px solid black; padding-top:10px;">Total:</td>
								<td style="text-align:right; border-top:1px solid black;"><strong>PHP '.number_format($totalFee,2).'</strong></td>
							</tr>
						</table> 
					</div> 
				</div>';  
			return $emailAcknowledgementContent; 
		}

	 	function emailSuccessContent($name, $merchant, $filename, $pdfContent){
	 		$paymentDate  = date('F d, Y');
	 		$emailContent = '
	 		<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>Payment Acknowledgement Receipt.</title>
				</head>
				<body>
					<label>Hi '.ucwords(strtolower($name)).',</label><br>
					
					<p>We are pleased to inform you that your '.$merchant.' payment transaction dated '.$paymentDate.' was successful.</p>

					'.$pdfContent.'

					<p>Click <a href="'.BASE_URL.'PAYMENT_TRANSACTION_PDF/'.$filename.'" download>here</a> to download a copy of payment acknowledgement receipt.</p>
					
					<p>Than you very much.</p>
				</body>
			</html>
			'; 
	  		return $emailContent; 
	 	}
 
	 	function emailFailedContent($name, $merchant){
	 		$paymentDate  = date('F d, Y');
	 		$emailContent = '
	 		<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>Payment Acknowledgement Receipt.</title>
				</head>
				<body>
					<label>Hi '.ucwords(strtolower($name)).',</label><br>
					
					<p>We regret to inform you that your '.$merchant.' payment transaction dated '.$paymentDate.' was not successfully process.</p>
	  
					<p>Kindly contact us at <a href="mailto:'.SUPPORT_EMAIL.'">'.SUPPORT_EMAIL.'</a> for assistance.</p>
					
					<p>Than you very much.</p>
				</body>
			</html>
			'; 
	  		return $emailContent; 
	 	}
	}
?>
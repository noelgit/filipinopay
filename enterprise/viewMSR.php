<?php 

	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	require 'vendor/autoload.php';  

	$getMerchantCode = $_GET['MERCHANT_CODE'];
	$getDate = $_GET['DATE'];

	$merchantData = array();
	$merchantData['MERCHANT_CODE'] = $getMerchantCode;
	$merchantAccount = $dbMerchant->getRow("SELECT MERCHANT_CODE,MERCHANT_EMAIL_ADDRESS FROM tbl_merchant_info WHERE MERCHANT_CODE = :MERCHANT_CODE",$merchantData);
	  
	$userEmail 	     = $merchantAccount->MERCHANT_EMAIL_ADDRESS;
	$merchantCode    = $merchantAccount->MERCHANT_CODE;  

	$date = date_create($getDate);
	$dayToday = date_format($date,"l");     
		
	$schedQueryData = array();
	$schedQueryData['STATUS'] = '1';
	$schedQueryData['DAY'] = $dayToday;
	$schedQueryData['MERCHANT_CODE'] = $merchantCode;

	$merchantSched = $dbEnterprise->getRow("SELECT GET_TRANS_DAY FROM tbl_merchant_settlement_sched WHERE STATUS = :STATUS AND DAY = :DAY AND MERCHANT_CODE = :MERCHANT_CODE", $schedQueryData);
	 
	if($merchantSched){ 
		$transactionDate = explode(";",$merchantSched->GET_TRANS_DAY); 
		$count = 0;
		$addedCondition = '';
		$settlementDate = '';
		$reportDate = $getDate;
		foreach($transactionDate AS $key => $value){
			$transactionDate  = strtoupper($value); 
			$dateofTrasaction = date("Y-m-d", strtotime('previous '.$transactionDate, strtotime($getDate)));
			$settlementDateformat = date('Y-m-d', strtotime('previous '.$transactionDate, strtotime($getDate)));
			if($count >= 1){
				$settlementDate .= ", ".$settlementDateformat;
				$addedCondition .= " OR DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = '".$dateofTrasaction."' ";
			}else{
				$settlementDate .= $settlementDateformat;
				$addedCondition .= " DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = '".$dateofTrasaction."' ";
			}
			$count++;
		}	  

		//Data 
		$infoQueryData = array(); 
		$infoQueryData['MERCHANT_CODE'] = $merchantCode;
		
		$merchantInfo = $dbEnterprise->getRow("SELECT vmas.`MERCHANT_CODE`, vmas.`MERCHANT_NAME` FROM vw_merchant_and_submerchant AS vmas WHERE vmas.`MERCHANT_CODE` = :MERCHANT_CODE", $infoQueryData);

		$merchantName = $merchantInfo->MERCHANT_NAME; 
		$merchantCode = $merchantInfo->MERCHANT_CODE; 

		$transactionData = $dbEnterprise->getResults("SELECT 
			tth.`CREATED_DATE`, tth.`TRN`, tth.`MERCHANT_CODE`,  tth.`CONVENIENCE_FEE`,  
			trpe.`DESCRIPTION` AS MODE_OF_PAYMENT, 
			vmas.`MERCHANT_NAME`,
			trts.`TRANS_DESC`,
		        (SELECT SUM(tt.`TRANSACTION_AMOUNT`)  FROM tbl_transactions AS tt 
		        WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 
		          
		        FROM tbl_transactions_hdr AS tth  

		        LEFT JOIN tbl_ref_payment_entity AS trpe
		        ON trpe.`PE_CODE` = tth.`PE_CODE` 
			
			LEFT JOIN vw_merchant_and_submerchant AS vmas
			ON vmas.`MERCHANT_CODE` = tth.`MERCHANT_CODE`
			
			LEFT JOIN tbl_ref_transaction_status AS trts
			ON trts.`TRANS_STATUS` = tth.`TRANS_STATUS`
			
	        WHERE tth.`MERCHANT_CODE` = :MERCHANT_CODE AND  
		    (".$addedCondition.")
			AND tth.`TRANS_STATUS` = '3' 
			GROUP BY tth.`TRN` 
			ORDER BY tth.CREATED_DATE DESC", $infoQueryData);

			
			//PDF
			$pdfContent = '		
			<html>
				<body>
					<div>
						<label>MERCHANT SETTLEMENT REPORT</label><br><br>
						<label>Merchant Name: '.$merchantName.'</label><br>
						<label>Merchant ID: '.$merchantCode.'</label><br>
						<label>Settlement Date: '.$settlementDate.'</label><br>
						<label>Report Date: '.$reportDate.'</label>
					</div> 
					<div>
						<br><br>
						<table border="1" style="font-size:15px; width:100%;">
							<tr>
								<th>TRANSACTION DATE</th>
								<th>TRANSACTION REFERENCE NUMBER</th>
								<th>MODE OF PAYMENT</th>
								<th>TOTAL AMOUNT</th>
								<th>AMOUNT DUE TO MERCHANT</th>
								<th>CONVENIENCE FEE</th>
								<th>TAX</th> 
								<th>MERCHANT ID</th>
								<th>MERCHANT NAME</th>
								<th>STATUS</th>
							</tr>
			';
			$countTransaction = 0;
			$totalAmountMerchant = 0;
			$totalIPGFee = 0;
			$totalTax = 0; 
			foreach($transactionData as $data){
				$totalAmount = $data->TOTALAMOUNT + $data->CONVENIENCE_FEE;
				$totalAmountMerchant += $data->TOTALAMOUNT;
				$totalIPGFee += $data->CONVENIENCE_FEE;
				$countTransaction++;
				$pdfContent .= '
							<tr>
								<td>'.$data->CREATED_DATE.'</td>
								<td>'.$data->TRN.'</td>
								<td>'.$data->MODE_OF_PAYMENT.'</td>
								<td>'.number_format($totalAmount,2).'</td>
								<td>'.number_format($data->TOTALAMOUNT,2).'</td>
								<td>'.number_format($data->CONVENIENCE_FEE,2).'</td>
								<td> - </td>
								<td>'.$data->MERCHANT_CODE.'</td>
								<td>'.$data->MERCHANT_NAME.'</td>
								<td>'.$data->TRANS_DESC.'</td>
							</tr>
				'; 
			}  
			$pdfContent .= '
					</table>
				</div> 
				<div>
					<br><br>
					<table>
						<tr>
							<td>TOTAL COUNT: </td>
							<td style="text-align:right;">'.$countTransaction.'</td>
						</tr> 
						<tr>
							<td>TOTAL AMOUNT DUE TO MERCHANT: </td>
							<td style="text-align:right;">'.number_format($totalAmountMerchant,2).'</td>
						</tr> 
						<tr>
							<td>TOTAL CONVENIENCE FEE: </td>
							<td style="text-align:right;">'.number_format($totalIPGFee,2).'</td>
						</tr>  
						<tr>
							<td>TOTAL TAX: </td>
							<td style="text-align:right;">'.number_format($totalTax,2).'</td>
						</tr> 
					</table>
				</div>
				
				<div>
					<br><br>
					<table style="width:100%;">
						<tr>
							<td style="width:33%;">
								<strong>Prepared By:</strong><br>
								<label>Name: </label><br>
								<label>Date: </label><br>
								<label>Signature: </label>
							</td>
							
							<td style="width:33%;">
								<strong>Reviewed By:</strong><br>
								<label>Name: </label><br>
								<label>Date: </label><br>
								<label>Signature: </label>
							</td>
							
							<td style="width:33%;">
								<strong>Approved By:</strong><br>
								<label>Name: </label></label><br>
								<label>Date: </label><br>
								<label>Signature: </label>
							</td>
						</tr>
					</table>
				</div>
				
				<div style="text-align:center;">
					<br><br>
					<label>*** NOTHING FOLLOWS ***</label>
				</div>
			</body>
		</html>';
		
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		$mpdf->WriteHTML($pdfContent); 
		$mpdf->Output();
		//$mpdf->Output($filename, 'D');  
		//exit();

		//$mpdf->WriteHTML($pdfContent); 
		//$mpdf->Output('ReportFiles/'.$fileName.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 
		  			
	}
?>
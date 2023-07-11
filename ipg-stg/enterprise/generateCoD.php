<?php 

	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	require 'vendor/autoload.php';  

	$getMerchantCode = $_GET['mchCode'];
	$undepositedDate = $_GET['ud'];
	$undepositedAmount = $_GET['ua'];
	$fundTransferDate = $_GET['ftd'];
	$fundTransferAmount = $_GET['fta'];

	$referenceNumber = date('YmdGis'); 

	$merchantData = array();
	$merchantData['MERCHANT_CODE'] = $getMerchantCode;
	$merchantAccount = $dbMerchant->getRow("SELECT * FROM tbl_merchant_info WHERE MERCHANT_CODE = :MERCHANT_CODE",$merchantData);
	 
	if($merchantAccount){

		$userEmail = $merchantAccount->MERCHANT_EMAIL_ADDRESS;
		$merchantCode = $merchantAccount->MERCHANT_CODE;  
		$merchantName = $merchantAccount->MERCHANT_NAME;  
   
		$collectionDate = date('Y-m-d', strtotime($undepositedDate .' -1 day'));
 	 

 		$transactionParam = array();
 		$transactionParam['MERCHANT_CODE'] = $merchantCode;
 		$transactionParam['COLLECTION_DATE'] = $collectionDate;
 		$transactionParam['TRANS_STATUS'] = '3'; 

		$transactionData = $dbEnterprise->getResults("SELECT 
			TTH.`TRN`, TTH.`MERCHANT_CODE`, TTH.`CONVENIENCE_FEE`, TT.`TRANSACTION_AMOUNT` 
			FROM tbl_transactions_hdr AS TTH 

			INNER JOIN tbl_transactions AS TT ON TT.`TRN` = TTH.`TRN`
			WHERE TTH.`MERCHANT_CODE` = :MERCHANT_CODE AND DATE(TTH.`CREATED_DATE`) = :COLLECTION_DATE AND TTH.`TRANS_STATUS` = :TRANS_STATUS
			GROUP BY TTH.`TRN`", $transactionParam);

		$totalTransactionCount = COUNT($transactionData);
		$totalTransactionAmount = 0;
 
		foreach($transactionData as $data){
			$totalTransactionAmount = $totalTransactionAmount + ($data->CONVENIENCE_FEE + $data->TRANSACTION_AMOUNT);
		}
		
		$undepositedCollection = ($undepositedAmount + $totalTransactionAmount) - $fundTransferAmount;
		//PDF
		$pdfContent = '		
		<html>
			<body>

				<img src="'.PUBLIC_URL.'IMAGES/DBP-Logo.png" style="width:75px; margin:auto;"><br><br>
				<div style="text-align:center;">
					<h3><u>CERTIFICATION OF DEPOSIT</u></h3>
					<h4>SUMMARY</h4><br> 
				</div> 
				<div style="text-align:left;">
					<br><br>
					<table border="0" style="font-size:14px; width:100%;">
						<tr>
							<th colspan="2" style="text-align:left; width:50%;">Undeposited Collections per Last Report dated ('.$undepositedDate.')</th> 
							<th style="text-align:right; width:30%;">Php '.number_format($undepositedAmount,2).'</th> 
						</tr> 
						<tr>
							<th style="text-align:left;">Add: Collections for the day ('.$collectionDate.')</th>
							<td style="width:20%;"></td>
							<td></td> 
						</tr> 
						<tr>
							<td style="text-align:right;">Total Number of Transactions</td>
							<td style="text-align:right;">'.$totalTransactionCount.'</td>
							<td></td> 
						</tr> 
						<tr>
							<td style="text-align:right;">Amount</td>
							<td style="text-align:right;">Php '.number_format($totalTransactionAmount,2).'</td> 
							<td style="text-align:right;">Php '.number_format($totalTransactionAmount,2).'</td> 
						</tr> 
						<tr>
							<th style="text-align:left;">Total Amount of Collection:</th>
							<td></td> 
							<th style="text-align:right;"><hr>Php '.number_format($totalTransactionAmount,2).'</th> 
						</tr> 

						<tr>
							<th style="text-align:left;">Less: Deposit/Fund Transfers</th>
							<td></td>
							<td></td> 
						</tr> 

						<tr>
							<td style="text-align:right;">Date: <strong>'.$fundTransferDate.'</strong></td>
							<td style="text-align:right;">Php '.number_format($fundTransferAmount,2).'</td> 
							<th style="text-align:right;">Php '.number_format($fundTransferAmount,2).'</th> 
						</tr> 
						<tr>
							<td style="text-align:right;">Reference No.: <strong><u>'.$referenceNumber.'</u></strong></td>
							<td></td>
							<td></td> 
						</tr> 
						<tr>
							<th style="text-align:left;"><br><br>Undeposited Collection, this Report</th>
							<td></td>
							<th style="text-align:right; font-size:18px;"><br><br>Php '.number_format($undepositedCollection,2).'</th> 
						</tr>
					</table>
				</div>  
				  
				<div style="text-align:justify;">
					<br><br>
					<p>This is to certify that the above is a true and correct statement. That the amount collected is deposited intact to the <strong>'.$merchantAccount->BANK_NAME.'</strong> bank account of the <strong>'.$merchantName.'</strong> with account number <strong>'.$merchantAccount->BANK_ACCOUNT_NO.'</strong>, and duly supported by the attached proof of deposit. Details of collections can be generated from our online reporting facility or in the attached electronic file of the List of Daily Collections.</p>
				</div>

				<div style="text-align:right;">
					<br><br><br>
					<label><u><strong>'.$merchantAccount->AUTHORIZED_SIGNATORY.'</strong></u></label><br>

					<label><strong>'.$merchantAccount->SIGNATORY_DESIGNATION.'</strong></label>
				</div>
			</body>
		</html>';
		
		$filename = 'certification-of-deposit-'.$referenceNumber.".pdf";	 

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
		$mpdf->WriteHTML($pdfContent); 
		//$mpdf->Output();
		$mpdf->Output($filename, 'D');  
		exit();

		//$mpdf->WriteHTML($pdfContent); 
		//$mpdf->Output('ReportFiles/'.$fileName.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 
		 
	}
?>
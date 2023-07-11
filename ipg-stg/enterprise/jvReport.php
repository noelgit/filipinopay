<?php 
	// include our OAuth2 Server object

	require_once __DIR__.'/server.php'; 

	$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

	$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
	require 'vendor/autoload.php'; 
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Writer\Csv;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
 	
	$companyCode     = $_POST['companyCode'];
	$userEmail		 = $_POST['userEmail'];
	$merchantCode    = $_POST['merchantCode'];

	$dateTimeToday   = date('YmdHsi');
	$currentDateTime = date('M d, Y h:s:i A'); 
	$timeStamp 	  	 = date('Y-m-d G:i:s');
	$dayToday 	     = strtoupper(date("l")); 
	$fileName        = $_POST['companyCode'].'-'.$dateTimeToday; 
	 
	$jvSchedQueryData = array();
	$jvSchedQueryData['STATUS'] = '1';
	$jvSchedQueryData['DAY'] = $dayToday;
	$jvSchedQueryData['COMPANY_CODE'] = $companyCode;

	$jvSched = $dbEnterprise->getRow("SELECT GET_TRANS_DAY FROM tbl_jv_settlement_sched WHERE STATUS = :STATUS AND DAY = :DAY AND COMPANY_CODE = :COMPANY_CODE", $jvSchedQueryData);
	   
	if($jvSched){  
		$transQueryData = array();
		$transactionDate = explode(";",$jvSched->GET_TRANS_DAY); 
		$count = 0;
		$addedCondition = '';
		$settlementDate = '';
		$reportDate = date("l, d M Y G:i:s");
		foreach($transactionDate AS $key => $value){
			$transactionDate  = strtoupper($value); 
			$dateofTrasaction = date("Y-m-d", strtotime("previous ".$transactionDate.""));
			$settlementDateformat = date("l, d M Y", strtotime("previous ".$transactionDate.""));
			if($count >= 1){
				$settlementDate .= ", ".$settlementDateformat;
				$transQueryData['CREATED_DATE'.$count] = $dateofTrasaction;
				$addedCondition .= " OR DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = :CREATED_DATE".$count;
			}else{
				$settlementDate .= $settlementDateformat; 
				$transQueryData['CREATED_DATE'.$count] = $dateofTrasaction;
				$addedCondition .= " DATE_FORMAT(tth.CREATED_DATE,'%Y-%m-%d') = :CREATED_DATE".$count;
			} 
			$count++;
		}	  

		//Data    
		$transQueryData['COMPANY_CODE_1'] = $companyCode;
		$transQueryData['COMPANY_CODE_2'] = $companyCode;
	    
	    if(!empty($merchantCode)){ 
	        $transQueryData['MERCHANT_CODE']   = $merchantCode;
	        $addedConditionMerchant = "AND tth.`MERCHANT_CODE` = :MERCHANT_CODE";
	        $merchantCodeLabel = "<label>Mechant Code: ".$merchantCode."</label><br>";
	    }else{
	    	$addedConditionMerchant = "";
	    	$merchantCodeLabel = "";
	    }

		$transactionData = $dbEnterprise->getResults("SELECT 
		tth.`TRN`, tth.`CREATED_DATE`, tth.`CONVENIENCE_FEE` - tth.`IPG_FEE` AS MDR, tth.`IPG_FEE`, tth.`COMPANY_CODE_1_FEE`, tth.`COMPANY_CODE_2_FEE`, tth.`PM_CODE`, 
		trpe.`DESCRIPTION` AS MODE_OF_PAYMENT, 
		tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_2`, 
		trts.`TRANS_DESC`, 
		vmas.`MERCHANT_CODE`, vmas.`MERCHANT_NAME`,
		(SELECT SUM(tt.`TRANSACTION_AMOUNT`) FROM tbl_transactions AS tt WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 

		FROM tbl_transactions_hdr AS tth 
		 
		LEFT JOIN tbl_ref_payment_entity AS trpe 
		ON trpe.`PE_CODE` = tth.`PE_CODE` 

		LEFT JOIN tbl_ipg_jv_fees AS tijf
		ON tijf.`COMPANY_CODE_1` = :COMPANY_CODE_1 OR tijf.`COMPANY_CODE_2` = :COMPANY_CODE_2
		        
		LEFT JOIN tbl_ref_transaction_status AS trts 
		ON trts.`TRANS_STATUS` = tth.`TRANS_STATUS` 
		
		LEFT JOIN vw_merchant_and_submerchant AS vmas
		ON tth.`MERCHANT_CODE` = vmas.`MERCHANT_CODE`

		WHERE tth.`JV_CODE` = tijf.`JV_CODE` AND

		(".$addedCondition.")

		AND tth.`TRANS_STATUS` = '3' 
		".$addedConditionMerchant."
		GROUP BY tth.`TRN`
		
		ORDER BY tth.CREATED_DATE DESC", $transQueryData);


		if($_POST['fileType'] == 'pdf'){

			$COMPANY_1_NAME = '';
			$COMPANY_2_NAME = '';
			foreach($transactionData as $data){  
				$COMPANY_1_NAME = $data->COMPANY_CODE_1;
				$COMPANY_2_NAME = $data->COMPANY_CODE_2; 
			}
			//PDF
			$pdfContent = '		
			<html>
				<body>
					<div>
						<label>MERCHANT SETTLEMENT REPORT: '.$fileName.'</label><br><br>
						'.$merchantCodeLabel.'
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
								<th>MDR FEE</th>
								<th>UP-FRONT FEE</th>
								<th>IPG FEE</th>
								<th>TAX</th>
								<th>'.$COMPANY_1_NAME.' PROFIT</th>
								<th>'.$COMPANY_2_NAME.' PROFIT</th>
								<th>3RD PARTY FEE</th>
								<th>MERCHANT ID</th>
								<th>MERCHANT NAME</th>
								<th>STATUS</th>
							</tr>
			';

			$countTransaction = 0;
			$totalAmountMerchant = 0;
			$totalMDR = 0;
			$totalConvenienceFee = 0;
			$totalTax = 0;
			$totalCompany1 = 0;
			$totalCompany2 = 0;
			$total3rdPartyFee = 0; 
			foreach($transactionData as $data){   
				$totalAmount = $data->TOTALAMOUNT + $data->IPG_FEE + $data->MDR;
				$totalAmountDueMerchant = $totalAmount - $data->IPG_FEE - $data->MDR;
				$totalAmountMerchant += $totalAmountDueMerchant;
				$totalConvenienceFee += $data->IPG_FEE;
				$totalCompany1 += $data->COMPANY_CODE_1_FEE;
				$totalCompany2 += $data->COMPANY_CODE_2_FEE; 
				$total3rdPartyFee += $data->MDR;

				$countTransaction++;
				$pdfContent .= '
					<tr>
						<td>'.$data->CREATED_DATE.'</td>
						<td>'.$data->TRN.'</td>
						<td>'.$data->MODE_OF_PAYMENT.'</td>
						<td>'.number_format($totalAmount, 2).'</td>
						<td>'.number_format($totalAmountDueMerchant, 2).'</td>';
				if($data->PM_CODE != 'UFP'){ 
					$pdfContent .= '					
						<td>'.number_format($data->MDR, 2).'</td>
						<td></td>';
					$totalMDR += $data->MDR;
				}else{
					$pdfContent .= '					
						<td></td>
						<td>'.number_format($data->MDR, 2).'</td>';
				}
				$pdfContent .= ' 
						<td>'.number_format($data->IPG_FEE, 2).'</td>
						<td> - </td>
						<td>'.number_format($data->COMPANY_CODE_1_FEE, 2).'</td>
						<td>'.number_format($data->COMPANY_CODE_2_FEE, 2).'</td>
						<td>'.number_format($data->MDR,2).'</td>
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
								<td><strong>Total Count: </strong></td>
								<td style="text-align:right;">'.$countTransaction.'</td>
							</tr>
							<tr>
								<td><strong>Total Count Due To Merchant: </strong></td>
								<td style="text-align:right;">'.number_format($totalAmountMerchant, 2).'</td>
							</tr>
							<tr>
								<td><strong>Total MDR: </strong></td>
								<td style="text-align:right;">'.number_format($totalMDR, 2).'</td>
							</tr>
							<tr>
								<td><strong>Total Convenience Fee: </strong></td>
								<td style="text-align:right;">'.number_format($totalConvenienceFee, 2).'</td>
							</tr>
							<tr>
								<td><strong>Total Tax: </strong></td>
								<td style="text-align:right;">'.number_format($totalTax, 2).'</td>
							</tr>
							<tr>
								<td><strong>Total '.$COMPANY_1_NAME.' Profit: </strong></td>
								<td style="text-align:right;">'.number_format($totalCompany1, 2).'</td>
							</tr> 
							<tr>
								<td><strong>Total '.$COMPANY_2_NAME.' Profit: </strong></td>
								<td style="text-align:right;">'.number_format($totalCompany2, 2).'</td>
							</tr> 
							<tr>
								<td><strong>Total 3RD Party Fee: </strong></td>
								<td style="text-align:right;">'.number_format($total3rdPartyFee, 2).'</td>
							</tr>
						</table>
					</div>
					
					<div>
						<br><br>
						<table style="width:100%;">
							<tr>
								<td style="width:33%;">
									<strong>Prepared By:</strong><br>
									<label>Name: '.$userEmail.'</label><br>
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

			$mpdf->WriteHTML($pdfContent); 
			$mpdf->Output('ReportFiles/JV-REPORT/'.$fileName.'.pdf', 'F', \Mpdf\Output\Destination::FILE); 
			$fileNameDownload = $fileName.'.pdf';
		}else{
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', "MERCHANT SETTLEMENT REPORT: ".$fileName.""); 
			$sheet->setCellValue('A3', "Settlement Date:");
			$sheet->setCellValue('A4', "Report Date:");

			$sheet->setCellValue('B3', $settlementDate);
			$sheet->setCellValue('B4', $reportDate);

			//TABLE
			$sheet->setCellValue('C9', "TRANSACTION DATE");
			$sheet->setCellValue('D9', "TRANSACTION REFERENCE NUMBER");
			$sheet->setCellValue('E9', "MODE OF PAYMENT");
			$sheet->setCellValue('F9', "TOTAL AMOUNT");
			$sheet->setCellValue('G9', "AMOUNT DUE TO MERCHANT");
			$sheet->setCellValue('H9', "MDR FEE");
			$sheet->setCellValue('I9', "UP-FRONT FEE");
			$sheet->setCellValue('J9', "IPG FEE");
			$sheet->setCellValue('K9', "TAX");

			$sheet->setCellValue('N9', "3RD PARTY FEE"); 
			$sheet->setCellValue('O9', "MERCHANT ID"); 
			$sheet->setCellValue('P9', "MERCHANT NAME");
			$sheet->setCellValue('Q9', "STATUS"); 

			$countTransaction = 0;
			$totalAmountMerchant = 0;
			$totalMDR = 0;
			$totalConvenienceFee = 0;
			$totalTax = 0;
			$totalCompany1 = 0;
			$totalCompany2 = 0;
			$total3rdPartyFee = 0;
			$count = 10;

			$COMPANY_1_NAME = '';
			$COMPANY_2_NAME = '';
			foreach($transactionData as $data){ 
				$COMPANY_1_NAME = $data->COMPANY_CODE_1;
				$COMPANY_2_NAME = $data->COMPANY_CODE_2;
				$totalAmount = $data->TOTALAMOUNT + $data->IPG_FEE + $data->MDR;
				$totalAmountDueMerchant = $totalAmount - $data->IPG_FEE - $data->MDR;
				$totalAmountMerchant += $totalAmountDueMerchant;
				$totalConvenienceFee += $data->IPG_FEE;
				$totalCompany1 += $data->COMPANY_CODE_1_FEE;
				$totalCompany2 += $data->COMPANY_CODE_2_FEE; 
				$total3rdPartyFee += $data->MDR;

				$countTransaction++;

				$sheet->setCellValue('C'.$count.'', $data->CREATED_DATE);
				$sheet->setCellValue('D'.$count.'', $data->TRN);
				$sheet->setCellValue('E'.$count.'', $data->MODE_OF_PAYMENT);
				$sheet->setCellValue('F'.$count.'', $totalAmount);
				$sheet->setCellValue('G'.$count.'', $totalAmountDueMerchant);
				if($data->PM_CODE != 'UFP'){
					$sheet->setCellValue('H'.$count.'', $data->MDR); 
					$totalMDR += $data->MDR;
				}else{ 
					$sheet->setCellValue('I'.$count.'', $data->MDR);
				}
				$sheet->setCellValue('J'.$count.'', $data->IPG_FEE);
				$sheet->setCellValue('K'.$count.'', ' - ');
				$sheet->setCellValue('L'.$count.'', $data->COMPANY_CODE_1_FEE);
				$sheet->setCellValue('M'.$count.'', $data->COMPANY_CODE_2_FEE);   
				$sheet->setCellValue('N'.$count.'', $data->MDR);   
				$sheet->setCellValue('O'.$count.'', $data->MERCHANT_CODE);   
				$sheet->setCellValue('P'.$count.'', $data->MERCHANT_NAME);   
				$sheet->setCellValue('Q'.$count.'', $data->TRANS_DESC);   
					
					$sheet->getStyle('F'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('G'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('H'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('I'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('J'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('L'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('M'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');
					$sheet->getStyle('N'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');

				$count++;
			}

			$sheet->setCellValue('L9', $COMPANY_1_NAME.' PROFIT');
			$sheet->setCellValue('M9', $COMPANY_2_NAME.' PROFIT');

			$count += 2; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL COUNT');
			$sheet->setCellValue('C'.$count.'', $countTransaction);

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL COUNT DUE TO MERCHANT');
			$sheet->setCellValue('C'.$count.'', $totalAmountMerchant); 
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-'); 

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL MDR');
			$sheet->setCellValue('C'.$count.'', $totalMDR);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');  

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL CONVENIENCE FEE');
			$sheet->setCellValue('C'.$count.'', $totalConvenienceFee);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL TAX');
			$sheet->setCellValue('C'.$count.'', $totalTax);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-'); 

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL '.$COMPANY_1_NAME.' PROFIT');
			$sheet->setCellValue('C'.$count.'', $totalCompany1);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-'); 

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL '.$COMPANY_2_NAME.' PROFIT');
			$sheet->setCellValue('C'.$count.'', $totalCompany2);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-');  

			$count += 1; 
			$sheet->setCellValue('B'.$count.'', 'TOTAL 3RD PARTY FEE');
			$sheet->setCellValue('C'.$count.'', $total3rdPartyFee);
			$sheet->getStyle('C'.$count.'')->getNumberFormat()->setFormatCode('""#,##0.00_-'); 
			

			
			if($_POST['fileType'] == 'excel'){
				$writer = new Xlsx($spreadsheet);
				$writer->save('ReportFiles/JV-REPORT/'.$fileName.'.xlsx');
				$fileNameDownload = $fileName.'.xlsx';
			}
			if($_POST['fileType'] == 'csv'){
				$writer = new Csv($spreadsheet);
				$writer->save('ReportFiles/JV-REPORT/'.$fileName.'.csv');
				$fileNameDownload = $fileName.'.csv';
			}	 
		}

		echo $fileNameDownload;

		/****************************
			INSERT tbl_audit_trail
		*****************************/			 
		$auditArr = array();
		$auditArr['COMPANY_CODE'] 	  = $companyCode; 
		$auditArr['EVENT_TYPE']    	  = 'DOWNLOAD';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'DOWNLOAD SETTLEMENT REPORT '.strtoupper($_POST['fileType']);
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = $custom->upperCaseString($userEmail);

		$insertAudit = $dbJV->insert("tbl_audit_trail",$auditArr);
	}else{
		echo "404";
	}
?>
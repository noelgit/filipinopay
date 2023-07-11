<?php 
	include('../../../config.php');
	include('../../../LIBRARIES/libraries.php');
	session_start();
	error_reporting(0);

	$merchantCode = $_POST['MERCHANT_CODE'];
	$dateFrom = $_POST['dateFrom'];

	$response = array();
	$merchantData = array();

	$addedQuery = '';
	if($merchantCode == 'all' OR $merchantCode == 'ALL'){
		$addedQuery = '';
	}else{
		$merchantData['MERCHANT_CODE'] = $merchantCode;
		$addedQuery = 'WHERE MERCHANT_CODE = :MERCHANT_CODE';
	}	 
	$merchantAccount = $dbMerchant->getResults("SELECT MERCHANT_CODE,MERCHANT_NAME FROM tbl_merchant_info ".$addedQuery, $merchantData);
 	$merchantArr = array();

 	$count1 = 0;
	foreach($merchantAccount as $merchant){

		if(!in_array($merchant->MERCHANT_CODE, $merchantArr)){
			array_push($merchantArr,$merchant->MERCHANT_CODE);
		}

		$dateArray = explode(",",$dateFrom);

		$count2 = 0;
		foreach($dateArray AS $data){  
			$date = date_create($data);
			$formatedDate = date_format($date,"l");  
			$schedQueryData = array();
			$schedQueryData['STATUS'] = '1';
			$schedQueryData['DAY'] = $formatedDate;
			$schedQueryData['MERCHANT_CODE'] = $merchant->MERCHANT_CODE;

			$merchantSched = $dbEnterprise->getRow("SELECT GET_TRANS_DAY FROM tbl_merchant_settlement_sched WHERE STATUS = :STATUS AND DAY = :DAY AND MERCHANT_CODE = :MERCHANT_CODE", $schedQueryData);

			if($merchantSched){  
				
				$response[$count1][$count2]['MERCHANT_CODE'] = $merchant->MERCHANT_CODE;
				$response[$count1][$count2]['MERCHANT_NAME'] = $merchant->MERCHANT_NAME;

				$i = 0;
				$transactionDate = explode(";",$merchantSched->GET_TRANS_DAY); 
				$settlementDate = ''; 			

				foreach($transactionDate AS $key => $value){
					$transactionDate  = strtoupper($value);   

					$settlementDateformat = date('Y-m-d', strtotime('previous '.$transactionDate, strtotime($data)));
					if($i >= 1){
						$settlementDate .= ", ".$settlementDateformat; 
					}else{
						$settlementDate .= $settlementDateformat; 
					}
					$i++;
				}

				$response[$count1][$count2]['SETTLEMENT_DATE'] = $settlementDate;
				$response[$count1][$count2]['REPORT_DATE'] = $data;
				$response[$count1][$count2]['VIEW'] = "<a target='_blank' href='".ENTERPRISE_URL."viewMSR.php?MERCHANT_CODE=".$merchant->MERCHANT_CODE."&DATE=".$data."' class='btn btn-warning full-width'><i class='fas fa-file-pdf'></i> View Details</a>";
			}
			$count2++;
		}
		$count1++;
	}
	 
	echo json_encode($response);	
?>
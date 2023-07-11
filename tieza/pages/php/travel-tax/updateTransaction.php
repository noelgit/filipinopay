<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 

	if($_POST['TYPE'] == 'UPDATE'){
		$IPG_TRN  	  = $_POST['IPG_TRN'];
		$TIEZA_TRN    = $_POST['TIEZA_TRN'];
		$TRANS_ID 	  = $_POST['TRANS_ID'];
		$emailAddress = $_POST['EMAIL'];

		$transData = array();
		$transData['IPG_TRN'] 		  	= $IPG_TRN; 
		$transData['LAST_MODIFIED_DATE']= $timeStamp;
		$transData['LAST_MODIFIED_BY']  = $custom->upperCaseString($emailAddress);
		$result = $dbTieza->update('tbl_transaction', 'ID', $TRANS_ID, $transData);	 

		if($result){
			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['MODULE'] 	  	  = 'TRAVEL-TAX'; 
			$auditArr['TRN']			  = $IPG_TRN;
			$auditArr['EVENT_TYPE']    	  = 'UPDATE';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'UPDATE TRANSACTION IPG_TRN';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

			$insertAudit = $dbTieza->insert("tbl_audit_trail", $auditArr); 

			$response['STATUS']   	  = 'SUCCESS'; 
		}else{
			$response['STATUS']   	  = 'ERROR'; 
		}

		echo json_encode($response);	

	}elseif($_POST['TYPE'] == 'INSERT'){
		$IPG_TRN = $_POST['IPG_TRN'];
		$STATUS  = $_POST['STATUS'];

		$transData = array();
		$transData['IPG_TRN'] = $IPG_TRN;  
		$checkDatabase = $dbTieza->getRow("SELECT * FROM tbl_transaction WHERE IPG_TRN = :IPG_TRN AND (TRANS_STATUS_ID = '2' OR TRANS_STATUS_ID = '3' OR TRANS_STATUS_ID = '4') ", $transData);
		if(!$checkDatabase){ 
			$transData = array();
			$transData['IPG_TRN'] = $IPG_TRN;  
			$transactionData = $dbTieza->getRow("SELECT * FROM tbl_transaction WHERE IPG_TRN = :IPG_TRN ", $transData);
	 	 
			$transactionHDR = $dbTieza->rawQuery("
				INSERT INTO tbl_transaction (TICKET_NO, PASSAGE_ID, DATE_TRAVEL, EXIT_POINT_ID, DESTINATION_ID, CITY_TOWN_PROV, TRANS_AMOUNT, IPG_TRN, TRANS_STATUS_ID, CREATED_DATE, CREATED_BY)
				SELECT TICKET_NO, PASSAGE_ID, DATE_TRAVEL, EXIT_POINT_ID, DESTINATION_ID, CITY_TOWN_PROV, TRANS_AMOUNT, IPG_TRN, '".$STATUS."', '".$timeStamp."', CREATED_BY
				FROM tbl_transaction
				WHERE IPG_TRN = '".$IPG_TRN."' AND TRANS_STATUS_ID = '1'");

			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['MODULE'] 	  	  = 'TRAVEL-TAX'; 
			$auditArr['TRN']			  = $IPG_TRN;
			$auditArr['EVENT_TYPE']    	  = 'INSERT';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'INSERT TRANSACTION STATUS';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($transactionData->CREATED_BY); 

			$insertAudit = $dbTieza->insert("tbl_audit_trail", $auditArr); 

		}else{

		}

	}
?>
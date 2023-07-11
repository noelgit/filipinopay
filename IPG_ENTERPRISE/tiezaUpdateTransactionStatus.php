<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 

	$transactionsData = $dbTieza->getResults("SELECT * FROM tbl_transaction WHERE TRANS_STATUS_ID = '2' ");

	foreach($transactionsData AS $data){
		$transaction = $dbTieza->getRow("SELECT * FROM tbl_transaction WHERE 
			IPG_TRN = '".$data->IPG_TRN."' AND (TRANS_STATUS_ID = '5' OR TRANS_STATUS_ID = '3' OR TRANS_STATUS_ID = '4') LIMIT 1");
			
		if($transaction){

		}else{
			$transactionsHdr = $dbEnterprise->getRow("SELECT TRN, TRANS_STATUS FROM tbl_transactions_hdr WHERE TRN = '".$data->IPG_TRN."' AND TRANS_STATUS != '2' AND TRANS_STATUS != '1' LIMIT 1");

			if($transactionsHdr){
				$insertTransactions = $dbTieza->rawQuery("
					INSERT INTO tbl_transaction (TICKET_NO, PASSAGE_ID, DATE_TRAVEL, EXIT_POINT_ID, DESTINATION_ID, CITY_TOWN_PROV, TRANS_AMOUNT, IPG_TRN, TRANS_STATUS_ID, CREATED_DATE, CREATED_BY)

					SELECT TICKET_NO, PASSAGE_ID, DATE_TRAVEL, EXIT_POINT_ID, DESTINATION_ID, CITY_TOWN_PROV, TRANS_AMOUNT, IPG_TRN, '".$transactionsHdr->TRANS_STATUS."', '".$timeStamp."', CREATED_BY

					FROM tbl_transaction

					WHERE IPG_TRN = '".$data->IPG_TRN."' AND TRANS_STATUS_ID = '2'");

			}else{

			}
			
		}
	}
?>
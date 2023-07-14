<?php  
    $dateTime     = date('D M j g:i A'); 
    $dateToday    = date('Y-m-d');

    //*********************************
    //LINE GRAPH QUERY DATA
    //*********************************    
    $graphQueryData = array();
    $graphQueryData['MERCHANT_CODE'] = $merchantCode; 
    $graphQueryData['TRANS_STATUS']   = '3';

    $getGraphData = $dbEnterprise->getResults("SELECT *, 
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`)  FROM tbl_transactions AS tt
            WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT
        
        FROM tbl_transactions_hdr AS tth

        LEFT JOIN tbl_eor AS te
        ON te.`TRN` = tth.`TRN`

        WHERE tth.`MERCHANT_CODE` = :MERCHANT_CODE AND tth.`TRANS_STATUS` = :TRANS_STATUS", $graphQueryData);
  
    $graphArray = array();  
    if($secondPage == ''){
        $monthToday = date("F Y");
        $firstDayMonth = date('Y-m-01'); 
    }else{
        $date = date_create($secondPage);
        $monthToday = date_format($date,"F Y");  
        $firstDayMonth = date_format($date,"Y-m-01");  
    }
    $lastDayMonth = date("Y-m-t", strtotime($monthToday));

    while (strtotime($firstDayMonth) <= strtotime($lastDayMonth)) {  
        if(!isset($graphArray[$firstDayMonth]['date'])){
            $graphArray[$firstDayMonth]['date'] = $firstDayMonth;
        }

        if(!isset($graphArray[$firstDayMonth]['onlinePayment'])){
            $graphArray[$firstDayMonth]['onlinePayment'] = 0;
        }

        if(!isset($graphArray[$firstDayMonth]['upFrontPayment'])){
            $graphArray[$firstDayMonth]['upFrontPayment'] = 0;
        }

        if(!isset($graphArray[$firstDayMonth]['eWalletPayment'])){
            $graphArray[$firstDayMonth]['eWalletPayment'] = 0;
        }
        
        $graphArray[$firstDayMonth]['date'] = $firstDayMonth;
        $graphArray[$firstDayMonth]['onlinePayment'] += 0;
        $graphArray[$firstDayMonth]['upFrontPayment'] += 0;
        $graphArray[$firstDayMonth]['eWalletPayment'] += 0;       

        $firstDayMonth = date ("Y-m-d", strtotime("+1 day", strtotime($firstDayMonth)));
    }

    foreach($getGraphData AS $data){
        $date = date_create($data->CREATED_DATE);
        $dateFormat = date_format($date,"Y-m-d");   
        $monthFormat = date_format($date,"F Y");
        if($monthToday == $monthFormat){
            if($data->PM_CODE == 'OCP'){    
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += $data->TOTALAMOUNT;
                $graphArray[$dateFormat]['upFrontPayment'] += 0;
                $graphArray[$dateFormat]['eWalletPayment'] += 0;
            }
            elseif($data->PM_CODE == 'UFP'){ 
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += 0;
                $graphArray[$dateFormat]['upFrontPayment'] += $data->TOTALAMOUNT;
                $graphArray[$dateFormat]['eWalletPayment'] += 0; 
            }
            elseif($data->PM_CODE == 'EWP'){ 
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += 0;
                $graphArray[$dateFormat]['upFrontPayment'] += 0;
                $graphArray[$dateFormat]['eWalletPayment'] += $data->TOTALAMOUNT;  
            } 
        } 
    } 
    $jsonGraph = json_encode(array_values($graphArray)); 

    //*********************************
    //COUNT QUERY DATA
    //*********************************
    $dateNow = date('Y-m-d'); 
    $transactionCountPerTRN = $dbEnterprise->getResults("SELECT
          `t`.`MERCHANT_CODE` AS `MERCHANT_CODE`,
          `t`.`TRN`           AS `TRN`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 1)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 1) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 2)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 2) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `IN_PROCESS`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 4)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 4) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `FAILED`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 3)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 3) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `SUCCESSFUL`,
          DATE_FORMAT(`t`.`CREATED_DATE`,'%Y-%m-%d') AS `CREATED_DATE`
        FROM `tbl_transactions_hdr` `t` 

        WHERE (DATE_FORMAT(`t`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND 
        t.`MERCHANT_CODE` = '".$merchantCode."'
        
        GROUP BY `t`.`MERCHANT_CODE`,`t`.`TRN`,DATE_FORMAT(t.`CREATED_DATE`, '%Y-%m-%d')");
        
    $countInProcess = 0;
    $countSuccess   = 0;
    $countFailed    = 0;
    $totalCount     = 0;
    foreach($transactionCountPerTRN AS $data){  
        if($data->IN_PROCESS != 0){
            $countInProcess++;
        }
        if($data->SUCCESSFUL != 0){
            $countSuccess++;
        }
        if($data->FAILED != 0){
            $countFailed++;
        } 
        $totalCount++;
    }
 
    /*
    $countQueryData = array();
    $countQueryData['MERCHANT_CODE'] = $merchantCode; 
    $countQuery = $dbEnterprise->getRow("SELECT *,`IN-PROCESS` AS IN_PROCESS FROM vw_transactions_count_summary WHERE MERCHANT_CODE = :MERCHANT_CODE", $countQueryData);
    */
    //*********************************
    //TABLE QUERY DATA
    //*********************************
    $tableQueryData = array();
    $tableQueryData['MERCHANT_CODE'] = $merchantCode; 

    $dateFrom        =  isset($_POST['dateFrom']) ? $_POST['dateFrom'] : false;  
    $dateToFormat    =  isset($_POST['dateTo']) ? date('Y-m-d', strtotime($_POST['dateTo'] . '+1 day')) : false; 
    $dateTo          =  isset($_POST['dateTo']) ? $_POST['dateTo'] : false; 
    $referenceNumber =  isset($_POST['referenceNumber']) ? $_POST['referenceNumber'] : false; 
    $ORNumber        =  isset($_POST['ORNumber']) ? $_POST['ORNumber'] : false; 

    $additionalQuery = "";
    if(isset($_POST['dateFrom']) AND isset($_POST['dateTo'])){
        $tableQueryData['DATEFROM'] = $dateFrom;
        $tableQueryData['DATETOFORMAT'] = $dateToFormat;
        $additionalQuery .= "(tth.`CREATED_DATE` BETWEEN :DATEFROM AND :DATETOFORMAT) AND "; 
    }
    if(isset($_POST['referenceNumber'])){
        $tableQueryData['MERCHANT_REF_NUM'] = $referenceNumber;
        $additionalQuery .= "tth.`MERCHANT_REF_NUM` = :MERCHANT_REF_NUM AND "; 
    }
    if(isset($_POST['ORNumber'])){
        $tableQueryData['EOR'] = $ORNumber;
        $additionalQuery .= "te.`EOR` = :EOR AND "; 
    } 
    
    $tableData = $dbEnterprise->getResults("SELECT tth.`TRN`, tth.`CREATED_DATE`, tth.`MERCHANT_REF_NUM`, te.`EOR`, tth.`PM_CODE`, tth.`CONVENIENCE_FEE` - tth.`IPG_FEE` AS MDR, tth.`IPG_FEE`, trpe.`DESCRIPTION`,
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`)  FROM tbl_transactions AS tt 
        WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 

        FROM tbl_transactions_hdr AS tth 

        LEFT JOIN tbl_eor AS te 
        ON te.`TRN` = tth.`TRN` 

        LEFT JOIN tbl_ref_payment_entity AS trpe
        ON trpe.`PE_CODE` = tth.`PE_CODE` 

        WHERE tth.`MERCHANT_CODE` = :MERCHANT_CODE AND 
        
        ".$additionalQuery."
        
        tth.`TRANS_STATUS` = '3'
        GROUP BY tth.`TRN` 
        ORDER BY tth.CREATED_DATE DESC", $tableQueryData); 


        $profitData = array();

        foreach($tableData as $data){
            if(!isset($profitData['profitToday'])){
                $profitData['profitToday'] = 0;
            }
            $profitData['profitToday'] += 0;
            $date = date_create($data->CREATED_DATE); 
            $createdDate = date_format($date,"Y/m/d");  
            $createdDateForm = date_format($date,"M, d Y");
            $dateYesterday = date("Y/m/d",strtotime("-1 day"));
            
            if(!isset($profitData['profitYesterday'])){
                $profitData['profitYesterday'] = 0;
            }

            if($createdDate == date("Y/m/d")){ 
                $profitData['profitToday'] += $data->TOTALAMOUNT;
            } 

            if($dateYesterday == $createdDate){
                $profitData['profitYesterday'] += $data->TOTALAMOUNT;
            }
                if(!isset($profitData['highestProfit'][$createdDate])){
                    $profitData['highestProfit'][$createdDate] = 0;
                }
                $profitData['highestProfit'][$createdDate] += $data->TOTALAMOUNT;   
        }
         
        $value = max($profitData['highestProfit']);
        $key = array_search($value, $profitData['highestProfit']);

        $profitDate = date_create($key); 

        $highestProfit = $value; 
        $highestProfitDate = date_format($profitDate,"M, d Y");

?>
<?php  
    $dateTime     = date('D M j g:i A'); 
    $dateToday    = date('Y-m-d'); 
    
    if($thirdPage == ''){ 
        $merchantCodeParam = ""; 
    }else{
        $merchantCodeParam = $thirdPage; 
    }

    $getMerchantData = $dbEnterprise->getResults("SELECT tijf.`MERCHANT_CODE`,vmas.`MERCHANT_NAME` FROM tbl_ipg_jv_fees AS tijf
 
        LEFT JOIN vw_merchant_and_submerchant AS vmas
        ON vmas.`MERCHANT_CODE` = tijf.`MERCHANT_CODE`

        WHERE COMPANY_CODE_1 = '".$companyCode."' OR COMPANY_CODE_2 = '".$companyCode."'

        GROUP BY tijf.`MERCHANT_CODE` ORDER BY vmas.`MERCHANT_NAME` ASC");
    //*********************************
    //LINE GRAPH QUERY DATA
    //*********************************
    $graphQueryData = array();
    $graphQueryData['COMPANY_CODE_1'] = $companyCode;
    $graphQueryData['COMPANY_CODE_2'] = $companyCode;
    $graphQueryData['TRANS_STATUS']   = '3';
    if(!empty($merchantCodeParam)){ 
        $graphQueryData['MERCHANT_CODE']   = $merchantCodeParam;
        $additionalQueryMerchant_1 = "AND tth.`MERCHANT_CODE` = :MERCHANT_CODE";
    }
    $getGraphData = $dbEnterprise->getResults("SELECT tth.`CREATED_DATE`, tth.`PM_CODE`, tth.`JV_CODE`, tth.`COMPANY_CODE_1_FEE`, tth.`COMPANY_CODE_2_FEE`, tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_2`,
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`) 
            FROM tbl_transactions AS tt
            WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT
            
        FROM tbl_transactions_hdr AS tth

        LEFT JOIN tbl_eor AS te
        ON te.`TRN` = tth.`TRN`
        
        LEFT JOIN tbl_ipg_jv_fees AS tijf
        ON tijf.`COMPANY_CODE_1` = :COMPANY_CODE_1 OR tijf.`COMPANY_CODE_2` = :COMPANY_CODE_2
        WHERE tth.`JV_CODE` = tijf.`JV_CODE` AND tth.`TRANS_STATUS` = :TRANS_STATUS ".$additionalQueryMerchant_1, $graphQueryData);
  
    $graphArray = array();  
    if($secondPage == ''){
        $monthToday = date("F Y");
        $firstDayMonth = date('Y-m-01'); 
    }else{
        $date = date_create($secondPage);
        if($date){
            $monthToday = date_format($date,"F Y");  
            $firstDayMonth = date_format($date,"Y-m-01");  
        }else{
            $monthToday = date("F Y");
            $firstDayMonth = date('Y-m-01'); 
        }
    }
    $lastDayMonth = date("Y-m-t", strtotime($monthToday));

    while (strtotime($firstDayMonth) <= strtotime($lastDayMonth)) { 
        $graphArray[$firstDayMonth]['date'] = $firstDayMonth;
        $graphArray[$firstDayMonth]['onlinePayment'] += 0;
        $graphArray[$firstDayMonth]['upFrontPayment'] += 0;
        $graphArray[$firstDayMonth]['eWalletPayment'] += 0;       

        $firstDayMonth = date ("Y-m-d", strtotime("+1 day", strtotime($firstDayMonth)));

    }
    
    foreach($getGraphData AS $data){
    $totalAmount = 0;
        if($data->COMPANY_CODE_1 == $companyCode){
            $totalAmount += $data->COMPANY_CODE_1_FEE;
        }
        if($data->COMPANY_CODE_2 == $companyCode){
            $totalAmount += $data->COMPANY_CODE_2_FEE;
        } 
        $date = date_create($data->CREATED_DATE);
        $dateFormat = date_format($date,"Y-m-d");   
        $monthFormat = date_format($date,"F Y");
        if($monthToday == $monthFormat){
            if($data->PM_CODE == 'OCP'){    
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += number_format($totalAmount,2);
                $graphArray[$dateFormat]['upFrontPayment'] += 0;
                $graphArray[$dateFormat]['eWalletPayment'] += 0;
            }
            elseif($data->PM_CODE == 'UFP'){ 
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += 0;
                $graphArray[$dateFormat]['upFrontPayment'] += number_format($totalAmount,2);
                $graphArray[$dateFormat]['eWalletPayment'] += 0; 
            }
            elseif($data->PM_CODE == 'EWP'){ 
                $graphArray[$dateFormat]['date'] = $dateFormat;
                $graphArray[$dateFormat]['onlinePayment'] += 0;
                $graphArray[$dateFormat]['upFrontPayment'] += 0;
                $graphArray[$dateFormat]['eWalletPayment'] += number_format($totalAmount,2);
            } 
        } 
    } 
    $jsonGraph = json_encode(array_values($graphArray)); 

    //*********************************
    //COUNT QUERY DATA
    //*********************************
    $dateNow = date('Y-m-d');

    if(!empty($merchantCodeParam)){  
        $additionalQueryMerchant_2 = "AND t.`MERCHANT_CODE` = '".$merchantCodeParam."'";
    }else{
        $additionalQueryMerchant_2 = "AND t.`MERCHANT_CODE` = tijf.`MERCHANT_CODE`";
    }  

    $transactionCountPerTRN = $dbEnterprise->getResults("SELECT
          `t`.`MERCHANT_CODE` AS `MERCHANT_CODE`,
          `t`.`TRN`           AS `TRN`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 1)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 1) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 2)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 2) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `IN_PROCESS`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 4)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 4) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `FAILED`,
          (CASE WHEN ((`t`.`TRN` = `t`.`TRN`) AND (MAX(`t`.`TRANS_STATUS`) = 3)) THEN (SELECT COUNT(`tbl_transactions_hdr`.`TRANS_STATUS`) FROM `tbl_transactions_hdr` WHERE ((DATE_FORMAT(`tbl_transactions_hdr`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) AND (`tbl_transactions_hdr`.`TRANS_STATUS` = 3) AND (`tbl_transactions_hdr`.`TRN` = `t`.`TRN`))) ELSE 0 END) AS `SUCCESSFUL`,
          DATE_FORMAT(`t`.`CREATED_DATE`,'%Y-%m-%d') AS `CREATED_DATE`
        FROM `tbl_transactions_hdr` `t`
        
        LEFT JOIN tbl_ipg_jv_fees AS tijf
        ON tijf.`COMPANY_CODE_1` = '".$companyCode."' OR tijf.`COMPANY_CODE_2` = '".$companyCode."' 
        
        WHERE (DATE_FORMAT(`t`.`CREATED_DATE`,'%Y-%m-%d') = DATE_FORMAT('".$dateNow."','%Y-%m-%d')) 
        ".$additionalQueryMerchant_2."

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
 
    //*********************************
    //TABLE QUERY DATA
    //*********************************
    $tableQueryData = array();
    $tableQueryData['COMPANY_CODE_1'] = $companyCode;
    $tableQueryData['COMPANY_CODE_2'] = $companyCode;
    
    $dateFrom        =  $_POST['dateFrom'] ? $_POST['dateFrom'] : false;  
    $dateToFormat    =  $_POST['dateTo'] ? date('Y-m-d', strtotime($_POST['dateTo'] . '+1 day')) : false; 
    $dateTo          =  $_POST['dateTo'] ? $_POST['dateTo'] : false; 
    $referenceNumber =  $_POST['referenceNumber'] ? $_POST['referenceNumber'] : false; 
    $transactionNumber =  $_POST['transactionNumber'] ? $_POST['transactionNumber'] : false; 
    $ORNumber        =  $_POST['ORNumber'] ? $_POST['ORNumber'] : false; 

    $additionalQuery = "";
    if($_POST['dateFrom'] AND $_POST['dateTo']){
        $tableQueryData['DATEFROM'] = $dateFrom;
        $tableQueryData['DATETOFORMAT'] = $dateToFormat;
        $additionalQuery .= "(tth.`CREATED_DATE` BETWEEN :DATEFROM AND :DATETOFORMAT) AND ";
    }
    if($_POST['referenceNumber']){
        $tableQueryData['MERCHANT_REF_NUM'] = $referenceNumber;
        $additionalQuery .= "tth.`MERCHANT_REF_NUM` = :MERCHANT_REF_NUM AND ";
    }
    if($_POST['transactionNumber']){ 
        $tableQueryData['TRN'] = $transactionNumber;
        $additionalQuery .= "tth.`TRN` = :TRN AND ";
    }
    if($_POST['ORNumber']){
        $tableQueryData['EOR'] = $ORNumber;
        $additionalQuery .= "te.`EOR` = :EOR AND ";
    } 
    
    if(!empty($merchantCodeParam)){   
        $tableQueryData['MERCHANT_CODE']   = $merchantCodeParam;
        $additionalQuery .= "tth.`MERCHANT_CODE` = :MERCHANT_CODE AND"; 
    }

    $tableData = $dbEnterprise->getResults("SELECT tth.`TRN`,tth.`CREATED_DATE`, tth.`MERCHANT_REF_NUM`, tth.`PM_CODE`, tth.`CONVENIENCE_FEE` - tth.`IPG_FEE` AS MDR, tth.`IPG_FEE`, tth.`COMPANY_CODE_1_FEE`, tth.`COMPANY_CODE_2_FEE`,
        te.`EOR`, trpe.`DESCRIPTION`, tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_2`,  
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`) FROM tbl_transactions AS tt WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 

        FROM tbl_transactions_hdr AS tth 

        LEFT JOIN tbl_eor AS te 
        ON te.`TRN` = tth.`TRN` 

        LEFT JOIN tbl_ref_payment_entity AS trpe 
        ON trpe.`PE_CODE` = tth.`PE_CODE` 

        LEFT JOIN tbl_ipg_jv_fees AS tijf
        ON tijf.`COMPANY_CODE_1` = :COMPANY_CODE_1 OR tijf.`COMPANY_CODE_2` = :COMPANY_CODE_2
                
        WHERE tth.`JV_CODE` = tijf.`JV_CODE` AND 
        ".$additionalQuery."
        tth.`TRANS_STATUS` = '3' 
        GROUP BY tth.`TRN`
        ORDER BY tth.CREATED_DATE DESC", $tableQueryData); 

    $profitData = array();

    foreach($tableData as $data){
        $profitData['profitToday'] += 0;
        $date = date_create($data->CREATED_DATE); 
        $createdDate = date_format($date,"Y/m/d");  
        $createdDateForm = date_format($date,"M, d Y");
        $dateYesterday = date("Y/m/d",strtotime("-1 day"));
        
        if($createdDate == date("Y/m/d")){ 
            if($data->COMPANY_CODE_1 == $companyCode){
                $profitData['profitToday'] += $data->COMPANY_CODE_1_FEE;
            }
            if($data->COMPANY_CODE_2 == $companyCode){
                $profitData['profitToday'] += $data->COMPANY_CODE_2_FEE;
            } 
        } 

        if($dateYesterday == $createdDate){
            if($data->COMPANY_CODE_1 == $companyCode){
                $profitData['profitYesterday'] += $data->COMPANY_CODE_1_FEE;
            }
            if($data->COMPANY_CODE_2 == $companyCode){
                $profitData['profitYesterday'] += $data->COMPANY_CODE_2_FEE;
            }         
        }
     
        if($data->COMPANY_CODE_1 == $companyCode){
            $profitData['highestProfit'][$createdDate]     += $data->COMPANY_CODE_1_FEE; 
        }
        if($data->COMPANY_CODE_2 == $companyCode){
            $profitData['highestProfit'][$createdDate] += $data->COMPANY_CODE_2_FEE; 
        }  
    }
     
    $value = max($profitData['highestProfit']);
    $key = array_search($value, $profitData['highestProfit']);

    $profitDate = date_create($key); 

    $highestProfit = $value; 
    $highestProfitDate = date_format($profitDate,"M, d Y");


?>
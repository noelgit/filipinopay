<div id="wrapper">
    <?php 
        include('PAGES/REQUIRES/header.php');
        include('PAGES/PHP/DASHBOARD/JVrequire.php'); 
    ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-header">JV Dashboard</h1>
            </div>
            <div class="col-md-4"> 
                <div class="mt30">
                    <label>Select Merchant</label>
                    <select class="form-control selectMerchant">
                        <option value="">All</option> 
                        <?php 
                            foreach($getMerchantData as $data){
                                if($thirdPage == $data->MERCHANT_CODE){
                                    $selected = "selected";
                                }else{
                                    $selected = "";
                                }

                                echo "<option value='".$data->MERCHANT_CODE."' ".$selected.">".$data->MERCHANT_NAME."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"> 
                        &nbsp;
                        <div class="pull-right" style="width: 200px; margin-top:-5px;">
                            <div class="input-group date" >
                                <input type="text" value="<?php echo $monthToday; ?>" class="form-control datepicker">
                                <div class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="panel-body">
                        <ul class="list-graph">
                            <li><span><i class="fa fa-square op"></i> Online Payment</span></li>
                            <li><span><i class="fa fa-square ufp"></i> Up-Front Payment</span></li>
                            <li><span><i class="fa fa-square ep"></i> eWallet Payment</span></li>
                        </ul> 
                        <div id="modePaymentChart" style="height: 250px;"></div>
                    </div> 
                </div>
            </div>
            <?php 
                $dateToday = date('F d, Y'); 
                $dateYesterday = date('F d, Y', strtotime('-1 day', strtotime($dateToday)));
            ?>
            <div class="col-lg-8 col-lg-offset-2 p0">
                <div class="gap gap-md"></div>
                <div class="dateTime"><?php echo $dateTime; ?></div>
                <div class="successTransactions">
                    <label class="currency">PHP</label>
                    <label class="amount"><?php echo number_format($profitData['profitToday'],2); ?></label>
                    <label class="transactionLabel">Profit as of:</label>
                    <label class="transactionLabel"><?php echo $dateToday; ?></label>
                </div>

                <div class="totalTransaction">
                    <label class="collectionsLabel">Highest Profit Dated: <?php echo $highestProfitDate; ?></label>
                    <label class="highestCollection"><?php echo number_format($highestProfit,2); ?></label>
                    <label class="currency">PHP</label>
                    <label class="amount"><?php echo number_format($profitData['profitYesterday'],2); ?></label>
                    <label class="transactionLabel">Profit Collections Dated:</label>
                    <label class="transactionLabel"><?php echo $dateYesterday; ?></label>
                </div>

                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse1" id="viewMoreDetails" style="text-decoration:none; color: #3e3e3e;">View more details <i class="fa fa-arrow-circle-down"></i></a>
                            </h4>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse">
                            <div class="gap"></div>
                            <span class="totalTransactionCount">Total Transaction Count</span>
                            <div class="row transactionCountDiv">
                                <div class="col-lg-12 countTotal">
                                    <span><?php echo $totalCount ? $totalCount : 0 ; ?></span>
                                    <label>Total of Today's Transactions</label>
                                </div>
                                <div class="col-xs-4 countSuccess">
                                    <span><?php echo $countSuccess ? $countSuccess : 0 ; ?></span>
                                    <label>Successful</label>
                                </div>
                                <div class="col-xs-4 countFailed">
                                    <span><?php echo $countFailed ? $countFailed : 0 ; ?></span>
                                    <label>Failed</label>
                                </div>
                                <div class="col-xs-4 countProcess">
                                    <span><?php echo $countInProcess ? $countInProcess : 0 ; ?></span>
                                    <label>In-Process</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="gap gap-lg"></div>
            </div>

            <div class="col-lg-12">
				<!--
                <div class="mb10">
                    <label>Download Settlement Report:</label> 
                    <div class="dropdown inlineBLock">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" class="settlementExportData" id="settlement-download-to-pdf">Download to PDF</a>
                            </li> 
                            <li>
                                <a href="javascript:void(0)" class="settlementExportData" id="settlement-download-to-excel">Download to Excel</a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="settlementExportData" id="settlement-download-to-csv">Download to CSV</a>
                            </li>  
                        </ul>
                    </div>
                </div>
				-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Transaction history 
                        <div class="dropdown dropdownAction">
                          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
                          <span class="caret"></span></button>
                          <ul class="dropdown-menu"> 
                            <li><a href="javascript:void(0)" class="exportData" id="export-to-excel">Export to Excel</a></li>
                            <li><a href="javascript:void(0)" class="exportData" id="export-to-csv">Export to CSV</a></li> 
                          </ul>
                        </div>
                        <a href="<?php echo BASE_URL; ?>DASHBOARD" class="btn btn-default btnRefresh"><i class="fa fa-sync-alt"></i></a>
                    </div>
                        <form action="<?php echo BASE_URL; ?>PAGES/PHP/DASHBOARD/excel.php" method="post" id="export-form">  
                        <input type="hidden" value='' id='hidden-type' name='ExportType'/> 
                        <input type="hidden" value='' id='hidden-dateFrom' name="dateFrom2">
                        <input type="hidden" value='' id='hidden-dateTo' name="dateTo2">
                        <input type="hidden" value='' id='hidden-refNum' name="referenceNumber2">
                        <input type="hidden" value='' id='hidden-transNum' name="transactionNumber2">
                        <input type="hidden" value='' id='hidden-ORNum' name="ORNumber2">
                        <input type="hidden" value='' id='hidden-merchantCode' name="merchantCode2">
                     </form> 

                    <!-- /.panel-heading -->
                    <div class="panel-body"> 
                        <form id="filterDashboard" method="POST" >   
                            <div class="col-md-2 pl0">
                                <div class="form-group">
                                    <label>Payment Date:</label>
                                    <input type="text" name="dateFrom" id="datepicker-from" class="form-control" value="<?php echo $dateFrom; ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2 pl0">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="text" name="dateTo" id="datepicker-to" class="form-control" value="<?php echo $dateTo; ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3 pl0">
                                <div class="form-group">
                                    <label>Merchant Reference Number:</label>
                                    <input type="text" name="referenceNumber" value="<?php echo $referenceNumber; ?>" id="referenceNumber" class="form-control">
                                </div>
                            </div> 
                            <div class="col-md-3 pl0">
                                <div class="form-group">
                                    <label>Transaction Reference Number:</label>
                                    <input type="text" name="transactionNumber" value="<?php echo $transactionNumber; ?>" id="transactionNumber" class="form-control">
                                </div>
                            </div>  
                            <div class="col-md-2 pl0">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="btnSearch" class="btn btn-success full-width">Search <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-striped table-bordered table-hover" id="paymentHistory">
                            <thead>
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Transaction Reference Number</th> 
                                    <th>Merchant Reference Number</th> 
                                    <th>Mode of Payment</th> 
                                    <th>Amount</th>
                                    <th>Convenience Fee/MDR</th> 
                                    <th>GAI Profit</th>
                                    <th>DCI Profit</th>
                                    <th>Total Amount</th>  
                                    <th>Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $counter = 0; 
                                $excelArray = array();
                                foreach($tableData AS $data){
                                    $totalAmount = $data->TOTALAMOUNT + $data->MDR + $data->IPG_FEE;
                                    $date = date_create($data->CREATED_DATE);
                                    $dateFormat = date_format($date,"M d, Y H:i:s");   
                        
                                    echo "
                                        <tr>
                                            <td>".$dateFormat."</td>
                                            <td>".$data->TRN."</td> 
                                            <td>".$data->MERCHANT_REF_NUM."</td> 
                                            <td>".$data->DESCRIPTION."</td>
                                            <td class='text-right'>".number_format($data->TOTALAMOUNT, 2)."</td>
                                            <td class='text-right'>".number_format($data->MDR,2)."</td> 
                                            <td class='text-right'>".number_format($data->COMPANY_CODE_1_FEE,2)."</td>
                                            <td class='text-right'>".number_format($data->COMPANY_CODE_2_FEE,2)."</td>
                                            <td class='text-right'>".number_format($totalAmount,2)."</td>
                                            <td><button data-toggle='modal' data-target='#transactionModal".$counter."' class='btn btn-success'>View Details</a></td>
                                        </tr>  
                                    "; 
                                    $counter++;
                                }
                            ?>
                            </tbody>
                        </table> 

                        <?php                                  
                        $counter = 0;
                        foreach($tableData AS $data){ 
                            $totalAmount = $data->TOTALAMOUNT + $data->MDR + $data->IPG_FEE;

                            $transactionQueryData = array();
                            $transactionQueryData['TRN'] = $data->TRN;
                            $transactions = $dbEnterprise->getResults("SELECT * FROM tbl_transactions AS tt

                            INNER JOIN vw_merchant_and_submerchant AS vmas
                            ON  vmas.`SUB_MERCHANT_CODE` = tt.`SUB_MERCHANT_CODE`

                            WHERE TRN = :TRN", $transactionQueryData); 

                            $date = date_create($data->CREATED_DATE);
                            $dateFormat = date_format($date,"M d, Y H:i:s");  
                            echo '<div id="transactionModal'.$counter.'" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <h4 class="modal-title">Transaction Details</h4>
                                </div>
                                <div class="modal-body pt0">
                                    <table class="full-width mb10">
                                        <tr>
                                            <td><strong>Payment Date:</strong> '.$dateFormat.'</td>
                                            <td><strong>TRN:</strong> '.$data->TRN.'</td>
                                        </tr>
                                        <tr> 
                                            <td><strong>Mode of Payment:</strong> '.$data->DESCRIPTION.'</strong></td>
                                        </tr>
                                    </table>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Merchant Name</th>
                                                <th>Payment For</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                    foreach($transactions as $dataVal){
                                        echo"<tr>
                                            <td>".$dataVal->SUB_MERCHANT_NAME."</td>
                                            <td>".$dataVal->TRANSACTION_PAYMENT_FOR."</td>
                                            <td class='text-right'>".$dataVal->TRANSACTION_AMOUNT."</td>
                                        </tr>";
                                    }
                                echo'</tbody>
                                    </table>
                                    <table class="full-width">
                                        <tr>
                                            <td class="pt0"><strong>Amount: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->TOTALAMOUNT, 2).'</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="pt0"><strong>Convenience Fee/MDR: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->MDR,2).'</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="pt0"><strong>GAI Profit: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->COMPANY_CODE_1_FEE,2).'</strong></td>
                                        </tr>

                                        <tr>
                                            <td class="pt0"><strong>DCI Profit: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->COMPANY_CODE_2_FEE,2).'</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="pt0"><strong>Total Amount: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($totalAmount,2).'</strong></td>
                                        </tr>
                                    </table>

                                    </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                </div>

                                </div>
                            </div>';
                            $counter++;
                            }
                        ?>
                    </div> 
                </div>
            </div>
        </div>
    </div> 
</div>
<!-- /#wrapper --> 
<?php 
    include('PAGES/REQUIRES/resources.php');
?>

<script>
    /* Helper function */
    function download_file(fileURL, fileName) {
        // for non-IE
        if (!window.ActiveXObject) {
            var save = document.createElement('a');
            save.href = fileURL;
            save.target = '_blank';
            var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
            save.download = fileName || filename;
               if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                    document.location = save.href; 
    // window event not working here
                }else{
                    var evt = new MouseEvent('click', {
                        'view': window,
                        'bubbles': true,
                        'cancelable': false
                    });
                    save.dispatchEvent(evt);
                    (window.URL || window.webkitURL).revokeObjectURL(save.href);
                }   
        }

        // for IE < 11
        else if ( !! window.ActiveXObject && document.execCommand)     {
            var _window = window.open(fileURL, '_blank');
            _window.document.close();
            _window.document.execCommand('SaveAs', true, fileName || fileURL)
            _window.close();
        }
    }

    jQuery('.exportData').bind("click", function() {
        var target = $(this).attr('id');
        var dateFrom = $('#datepicker-from').val();
        var dateTo = $('#datepicker-to').val();
        var referenceNumber = $('#referenceNumber').val();
        var transactionNumber = $('#transactionNumber').val();
        var ORNumber = $('#ORNumber').val(); 
        switch(target) {
            case 'export-to-excel' :
                $('#hidden-type').val(target); 
                $('#hidden-dateFrom').val(dateFrom); 
                $('#hidden-dateTo').val(dateTo); 
                $('#hidden-refNum').val(referenceNumber); 
                $('#hidden-transNum').val(transactionNumber); 
                $('#hidden-ORNum').val(ORNumber); 
                $('#hidden-merchantCode').val("<?php echo $merchantCodeParam; ?>"); 

                $('#export-form').submit();
                $('#hidden-type').val('');
            break
            case 'export-to-csv' :
                $('#hidden-type').val(target); 
                $('#hidden-dateFrom').val(dateFrom); 
                $('#hidden-dateTo').val(dateTo); 
                $('#hidden-refNum').val(referenceNumber); 
                $('#hidden-transNum').val(transactionNumber); 
                $('#hidden-ORNum').val(ORNumber); 
                $('#hidden-merchantCode').val("<?php echo $merchantCodeParam; ?>"); 

                $('#export-form').submit();
                $('#hidden-type').val('');
            break
        } 
    });

    jQuery('.settlementExportData').bind("click", function() {
        var target = $(this).attr('id');  
        switch(target) {
            case 'settlement-download-to-pdf' :
                $.ajax({
                    url: ENTERPRISE_URL+"jvReport.php",
                    type:"POST",
                    data: { 
                        fileType: "pdf",
                        userEmail: "<?php echo $_SESSION['JV']['EMAILADDRESS']; ?>",
                        companyCode: "<?php echo $_SESSION['JV']['COMPANY_CODE']; ?>", 
                        merchantCode: "<?php echo $merchantCodeParam; ?>"
                    },
                    cache: false,      
                }).done(function(results){
                    if(results == '404'){
                        alert('No settlement report today.');
                    }else{ 
                        download_file(ENTERPRISE_URL+'ReportFiles/JV-REPORT/'+results, results);  
                    }
                });
            break
            case 'settlement-download-to-excel' : 
                $.ajax({
                    url: ENTERPRISE_URL+"jvReport.php",
                    type:"POST",
                    data: { 
                        fileType: "excel",
                        userEmail: "<?php echo $_SESSION['JV']['EMAILADDRESS']; ?>",
                        companyCode: "<?php echo $_SESSION['JV']['COMPANY_CODE']; ?>", 
                        merchantCode: "<?php echo $merchantCodeParam; ?>"
                    },
                    cache: false,      
                }).done(function(results){
                    if(results == '404'){
                        alert('No settlement report today.');
                    }else{
                        download_file(ENTERPRISE_URL+'ReportFiles/JV-REPORT/'+results, results); 
                        //window.location.href = ENTERPRISE_URL+'ReportFiles/JV-REPORT/'+results; 
                    }
                });
            break
            case 'settlement-download-to-csv' : 
                $.ajax({
                    url: ENTERPRISE_URL+"jvReport.php",
                    type:"POST",
                    data: { 
                        fileType: "csv",
                        userEmail: "<?php echo $_SESSION['JV']['EMAILADDRESS']; ?>",
                        companyCode: "<?php echo $_SESSION['JV']['COMPANY_CODE']; ?>", 
                        merchantCode: "<?php echo $merchantCodeParam; ?>"
                    },
                    cache: false,      
                }).done(function(results){
                    if(results == '404'){
                        alert('No settlement report today.');
                    }else{
                        download_file(ENTERPRISE_URL+'ReportFiles/JV-REPORT/'+results, results);  
                        //window.location.href = ENTERPRISE_URL+'ReportFiles/JV-REPORT/'+results; 
                    }
                });
            break
        } 
    });
    $('#viewMoreDetails').click( function(){
        var isExpanded = $(this).attr("aria-expanded"); 
        if(isExpanded == 'true'){   
            $('#viewMoreDetails').html('View more details <i class="fa fa-arrow-circle-down"></i>'); 
        }else{   
            $('#viewMoreDetails').html('View less details <i class="fa fa-arrow-circle-up"></i>');
        }
    });
    $( "#filterDashboard" ).submit(function( event ) {
          preloader(1); 
    });    
    $('#datepicker-from').datepicker({
        format: 'yyyy-mm-dd',
        endDate: '0d',
    });
    $('#datepicker-to').datepicker({
        startDate: $('#datepicker-from').val(),
        format: 'yyyy-mm-dd',
        endDate: '0d',
    });

    $('#datepicker-from').change(function(){  
        $('#datepicker-to').datepicker('setStartDate', $(this).val()); 
        if($("#datepicker-from").datepicker("getDate") === null) { 
            $("#datepicker-to").prop('required',false);
            $("#datepicker-to").val('');
            $("#datepicker-to").prop('disabled',true);
        }else{
            $("#datepicker-to").prop('required',true);
            $("#datepicker-to").prop('disabled',false);
        }
    });
    if($("#datepicker-from").datepicker("getDate") === null) {
        $("#datepicker-to").prop('required',false);
        $("#datepicker-to").val('');
        $("#datepicker-to").prop('disabled',true);
    }else{
        $("#datepicker-to").prop('required',true);
        $("#datepicker-to").prop('disabled',false);
    }    

    if($('#datepicker-to').datepicker("getDate") === null){
        
    }else{
         $('#datepicker-from').datepicker('setEndDate', $('#datepicker-to').val());
    }
	
    $('#datepicker-to').change(function(){ 
        $('#datepicker-from').datepicker('setEndDate', $(this).val());
    });

    $('.datepicker').change(function() {  
        var dateSelect = $(this).val();
        var datePick = dateSelect.replace(" ","-");

        var merchantCode = $('.selectMerchant').val(); 

        window.location.href = "<?php echo BASE_URL."DASHBOARD/"; ?>"+datePick+"/"+merchantCode; 
    });

    $('.selectMerchant').change(function(){ 
        var merchantCode = $(this).val(); 

        var dateSelect = $('.datepicker').val();
        var datePick = dateSelect.replace(" ","-");

        window.location.href = "<?php echo BASE_URL."DASHBOARD/"; ?>"+datePick+"/"+merchantCode; 
    });

    $('.datepicker').datepicker({
        format: "MM yyyy",
        startView: "months", 
        minViewMode: "months",
        endDate: '+1d', 
        autoclose: true,
    });
    
    new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'modePaymentChart',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: <?php echo $jsonGraph; ?>,
          
      // The name of the data record attribute that contains x-values.
      xkey: 'date',
      // A list of names of data record attributes that contain y-values.
    ykeys: ['onlinePayment','upFrontPayment','eWalletPayment'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Online Payment','Up-Front Payment','eWallet Payment'],
    });
    $('#paymentHistory').DataTable({
        "lengthChange": false,
        "searching": false,
         "order": [ 0, 'desc'],
    });

</script>
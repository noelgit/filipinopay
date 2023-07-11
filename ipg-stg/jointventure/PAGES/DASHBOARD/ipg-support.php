<div id="wrapper">
    <?php 
        include('PAGES/REQUIRES/header.php');
        include('PAGES/PHP/DASHBOARD/ISrequire.php'); 
    ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Support Dashboard</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12"> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Transaction history 
                        <a href="<?php echo BASE_URL; ?>DASHBOARD" class="btn btn-default btnRefresh"><i class="fa fa-sync-alt"></i></a>
                    </div>
                        <form action="<?php echo BASE_URL; ?>PAGES/PHP/DASHBOARD/excel.php" method="post" id="export-form">  
                        <input type="hidden" value='' id='hidden-type' name='ExportType'/> 
                        <input type="hidden" value='' id='hidden-dateFrom' name="dateFrom2">
                        <input type="hidden" value='' id='hidden-dateTo' name="dateTo2">
                        <input type="hidden" value='' id='hidden-refNum' name="referenceNumber2">
                        <input type="hidden" value='' id='hidden-transNum' name="transactionNumber2">
                        <input type="hidden" value='' id='hidden-ORNum' name="ORNumber2">
                     </form> 

                    <!-- /.panel-heading -->
                    <div class="panel-body"> 
                        <form id="filterDashboard" method="POST" > 

                            <div class="col-md-3 pl0">
                                <div class="form-group">
                                    <label>Select Merchant:</label>
                                    <select name="MERCHANT_CODE" class="form-control">
                                        <option value="">Select</option>
                                    <?php 
                                        foreach($merchants AS $merchant){ 
                                            if($merchantCode == $merchant->MERCHANT_CODE){
                                                $selected = 'selected';
                                            }else{
                                                $selected = '';
                                            }

                                            echo "<option value='".$merchant->MERCHANT_CODE."' ".$selected.">".$merchant->MERCHANT_NAME."</option>";
                                        }
                                    ?>
                                    </select>    
                                </div>
                            </div>

                            <div class="col-md-3 pl0">
                                <div class="form-group">
                                    <label>Select Mode of Payment:</label>
                                    <select name="MODE_PAYMENT" class="form-control">
                                        <option value="">Select</option>
                                    <?php 
                                        foreach($paymentMode AS $payment){
                                            if($modePayment == $payment->PE_CODE){
                                                $selected = 'selected';
                                            }else{
                                                $selected = '';
                                            }
                                            echo "<option value='".$payment->PE_CODE."' ".$selected.">".$payment->DESCRIPTION."</option>";
                                        }
                                    ?>
                                    </select>    
                                </div>
                            </div>

                            <div class="col-xs-12"></div>

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
                                    <th>OR Number</th>
                                    <th>Mode of Payment</th> 
                                    <th>Amount</th>
                                    <th>IPG Fee</th> 
                                    <th>Convenience Fee</th>  
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
                                            <td>".$data->EOR."</td>
                                            <td>".$data->DESCRIPTION."</td>
                                            <td class='text-right'>".number_format($data->TOTALAMOUNT, 2)."</td>
                                            <td class='text-right'>".number_format($data->MDR,2)."</td> 
                                            <td class='text-right'>".number_format($data->IPG_FEE,2)."</td>
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
                            if($data->TRANS_STATUS == '3'){
                                $status = '<i class="far fa-check-circle iconSuccess"></i> Successful Transaction';
                            }elseif($data->TRANS_STATUS == '4' OR $data->TRANS_STATUS == '5' OR $data->TRANS_STATUS == '6'){
                                $status = '<i class="far fa-times-circle iconFailed"></i> Failed Transaction';
                            }else{
                                $status = '<i class="fas fa-spinner iconPending fa-spin"></i> Pending Transaction';
                            }
                            $totalAmount = $data->TOTALAMOUNT + $data->MDR + $data->IPG_FEE;

                            $date = date_create($data->CREATED_DATE);
                            $dateFormat = date_format($date,"M d, Y H:i:s");  
                            echo '<div id="transactionModal'.$counter.'" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <h4 class="modal-title pull-left">Transaction Details</h4>
                                    <strong class="pull-right">'.$status.'</strong>  
                                </div>
                                <div class="modal-body pt0">
                                    <table class="full-width mb10">
                                        <tr>
                                            <td><strong>Merchant:</strong> '.$data->MERCHANT_NAME.'</td>
                                        </tr>
                                        <tr>
                                            <td><strong>TRN:</strong> '.$data->TRN.'</td>
                                            <td><strong>Payment Date:</strong> '.$dateFormat.'</td>
                                        </tr>
                                        <tr> 
                                            <td><strong>OR Number:</strong> '.$data->EOR.'</strong></td>
                                            <td><strong>Mode of Payment:</strong> '.$data->DESCRIPTION.'</strong></td>
                                        </tr> 
                                    </table> 
                                    <table class="full-width">
                                        <tr>
                                            <td class="pt0"><strong>Amount: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->TOTALAMOUNT, 2).'</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="pt0"><strong>IPG Fee: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->MDR,2).'</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="pt0"><strong>Convenience FEE: </strong></td>
                                            <td class="text-right pt0"><strong>'.number_format($data->IPG_FEE,2).'</strong></td>
                                        </tr> 
                                        <tr style="border-top: 1px solid #9e9e9e;">
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
        window.location.href = "<?php echo BASE_URL."DASHBOARD/"; ?>"+datePick;  
    });

    $('.datepicker').datepicker({
        format: "MM yyyy",
        startView: "months", 
        minViewMode: "months",
        endDate: '+1d', 
        autoclose: true,
    }); 

    $('#paymentHistory').DataTable({
        "lengthChange": false,
        "searching": false,
         "order": [ 0, 'desc'],
    });

</script>
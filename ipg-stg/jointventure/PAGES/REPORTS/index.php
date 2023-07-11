<div id="wrapper">
    <?php 
        include('PAGES/REQUIRES/header.php'); 

        $merchants = $dbMerchant->getResults("SELECT MERCHANT_CODE, MERCHANT_NAME FROM tbl_merchant_info AS MERCHANT"); 
      
    ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Reports</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12"> 
                <div class="panel panel-default">
                    <div class="panel-heading"> 
                        Merchant Settlement Report  
                        <a href="<?php echo BASE_URL; ?>REPORTS/" class="btn btn-default btnRefresh"><i class="fa fa-sync-alt"></i></a>
                    </div> 

                    <!-- /.panel-heading -->
                    <div class="panel-body"> 
                        <form id="frmMSR" method="POST" > 
                            <div class="col-md-3 pl0">
                                <div class="form-group">
                                    <label>Select Merchant:</label>
                                    <select name="MERCHANT_CODE" class="form-control">
                                        <option value="all">Select</option>
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

                            <div class="col-md-6 pl0">
                                <div class="form-group">
                                    <label>Report Date:</label>
                                    <input type="text" name="dateFrom" id="datepicker-from" class="form-control" autocomplete="off">
                                </div>
                            </div> 
                            <div class="col-md-2 col-md-offset-1 pl0">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="btnSearch" class="btn btn-success full-width">Search <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-striped table-bordered table-hover" id="dataTable">
                            <thead>
                                <tr> 
                                    <th>Merchant ID</th> 
                                    <th>Merchant Name</th> 
                                    <th>Settlement Date</th>
                                    <th>Report Date</th>  
                                    <th>Action</th>  
                                </tr>
                            </thead>
                            <tbody id="tableBody"> 
                            </tbody>
                        </table> 
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
    $('#datepicker-from').datepicker({
        format: 'yyyy-mm-dd',
        endDate: '-1d',
        multidate: true,
        daysOfWeekDisabled: [0,6]
    }); 

    MSRTable = $('#dataTable').DataTable({
        "lengthChange": false,
        "searching": false,
         "order": [ 0, 'desc'],
    });

    $('#frmMSR').validate({
          rules: { 
              dateFrom:{ 
                  required  :   true,   
              },           
          },
          messages: {  
          },
          submitHandler: function(form) {  
            preloader(1);
            var formData = new FormData(form); 
            $.ajax({
                url: BASE_URL+"PAGES/PHP/REPORTS/getMSR.php",
                type:"POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false, 
            }).done(function(results){
                MSRTable.clear();
                var results = JSON.parse(results);  
                for (var i = 0; i < results.length; i++) {
                    for (var i2 = 0; i2 < results[i].length; i2++){ 
                        MSRTable.row.add([
                            results[i][i2]['MERCHANT_CODE'],
                            results[i][i2]['MERCHANT_NAME'],
                            results[i][i2]['SETTLEMENT_DATE'],
                            results[i][i2]['REPORT_DATE'],
                            results[i][i2]['VIEW'],
                        ]);
                    }
                } 
                MSRTable.draw();
                preloader(0);
                return false;
            }); 
            return false; //Not to post the form physically 
        }
    }); 


</script>
<div id="wrapper">
    <?php 
        include('PAGES/REQUIRES/header.php');
    ?>
    <div id="page-wrapper">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Certification of Deposit</h1>
            </div>
        </div>
        
        <br>
        <form id="frmGenerate" method="POST" class="row"> 
            
            <div class="col-md-12">
                <div class="form-group mb0"> 
                    <label class="frmText">Undeposited Collections per last Report:</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group"> 
                    <div class="input-group" >
                        <div class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </div>
                         <input type="text" class="form-control frmField datepicker" placeholder="Date" name="UNDEPOSITED_DATE"> 
                    </div> 
                    <label for="UNDEPOSITED_DATE" generated="true" class="error"></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group" >
                    <div class="input-group-addon"> 
                        <span>&#8369;</span>
                    </div>
                    <input type="text" class="form-control frmField" placeholder="Amount" name="UNDEPOSITED_AMOUNT">
                </div> 
                <label for="UNDEPOSITED_AMOUNT" generated="true" class="error"></label> 
            </div>

            <div class="col-md-12"> 
                <div class="form-group mb0"> 
                    <label class="frmText">Deposit/Fund Transfers:</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group"> 
                    <div class="input-group" >
                        <div class="input-group-addon">
                            <span class="fa fa-calendar"></span>
                        </div>
                         <input type="text" class="form-control frmField datepicker" placeholder="Date" name="FUND_TRANSFER_DATE"> 
                    </div> 
                    <label for="FUND_TRANSFER_DATE" generated="true" class="error"></label> 
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group" >
                    <div class="input-group-addon"> 
                        <span>&#8369;</span>
                    </div>
                    <input type="text" class="form-control frmField" placeholder="Amount" name="FUND_TRANSFER_AMOUNT">
                </div>  
                <label for="FUND_TRANSFER_AMOUNT" generated="true" class="error"></label> 
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="formValidate()">Generate Report</button>  
                </div>
            </div>
        </form> 
    </div> 
</div>
<!-- /#wrapper -->

<div class="modal fade" id="confirmationModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content"> 
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Confirmation</h4>     
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <p class="desc">Are you sure you want to <strong>Generate</strong> this report?</p>
            </div> 

            <div class="modal-footer"> 
                <button class="btn btn-primary" onclick="formGenerate()">Yes</button>
                <button class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>


<?php 
    include('PAGES/REQUIRES/resources.php');
?>

<script>
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd', 
    });

    $('#frmGenerate').validate({
        rules: {     
            UNDEPOSITED_DATE: { 
                required: true,
            },   
            UNDEPOSITED_AMOUNT: { 
                required: true,
                number: true,
            }, 
            FUND_TRANSFER_DATE: { 
                required: true,
            },   
            FUND_TRANSFER_AMOUNT: { 
                required: true,
                number: true,
            },   
        },      
        messages: { 
        },
    });
    
    function formValidate(){ 
        if(!$("#frmGenerate").valid()) { 
            alert('Please fill in correctly the required fields.'); 

        }else{
            $('#confirmationModal').modal('toggle');

        }
    }    

    function formGenerate(){ 
        preloader(1);
        $('#confirmationModal').modal('toggle');
        var fd = new FormData(document.getElementById("frmGenerate")); 

        $.ajax({
            url: BASE_URL+"PAGES/PHP/<?php echo $firstPage."/generateReport.php"; ?>", 
            type:"POST",
            data: fd, 
            cache: false, 
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function(results) {      
                var results = JSON.parse(results);  
                if(results.STATUS == 'SUCCESS'){ 
                    window.open(results.REDIRECT_URL, '_blank');
                    preloader(0);
                }else{
                    alert("Something went wrong. Please try again."); 
                    preloader(0);
                }
            },
            error: function() { 
                alert("Something went wrong. Please try again."); 
                preloader(0);
            }
        }); 
    }     
</script>
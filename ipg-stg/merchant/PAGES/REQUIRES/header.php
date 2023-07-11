<?php 
    if($_SESSION['MERCHANT']['LOGGED'] != 'true'){
        header('Location: '.BASE_URL);  
    } 
    $merchantCode = $_SESSION['MERCHANT']['MERCHANT_CODE'];

    //MERCHANT INFO
    $merchantQueryData = array();
    $merchantQueryData['MERCHANT_CODE'] = $merchantCode;
    $merchantInfo = $dbMerchant->getRow("SELECT tmi.`MERCHANT_NAME`, tml.`IMAGE_PATH` FROM tbl_merchant_info AS tmi

        INNER JOIN tbl_merchant_logo AS tml
        ON tml.`MERCHANT_CODE` = tmi.`MERCHANT_CODE`

        WHERE tmi.`MERCHANT_CODE` = :MERCHANT_CODE LIMIT 1", $merchantQueryData);
?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><?php echo $merchantInfo->MERCHANT_NAME; ?></a>
    </div>

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu"> 
                <li style="padding:10px;">
                    <center>
                        <img src="<?php echo IMG.$merchantInfo->IMAGE_PATH ?>" class="img img-responsive merchantIcon">
                        <h4></h4>
                    </center>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>DASHBOARD/"><i class="fa fa-home fa-fw"></i> Dashboard</a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>CERTIFICATION-DEPOSIT/"><i class="fa fa-file fa-fw"></i> Certification of Deposit</a>
                </li>
              
                <li>
                    <a href="<?php echo BASE_URL; ?>LOGOUT"><i class="fa fa-sign-out-alt fa-fw"></i> Logout</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
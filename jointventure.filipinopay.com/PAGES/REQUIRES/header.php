<?php 
    if($_SESSION['JV']['LOGGED'] != 'true'){
        header('Location: '.BASE_URL);  
    } 
    $companyCode = $_SESSION['JV']['COMPANY_CODE'];
    
    //COMPANY INFO
    $companyQueryData = array();
    $companyQueryData['COMPANY_CODE'] = $companyCode;
    $companyInfo = $dbJV->getRow("SELECT tci.`COMPANY_CODE`, tci.`COMPANY_NAME`, tcl.`IMAGE_PATH` 
                                    FROM tbl_company_info AS tci
                                    LEFT JOIN tbl_company_logo AS tcl
                                    ON tcl.`COMPANY_CODE` = tci.`COMPANY_CODE`

                                    WHERE tci.`COMPANY_CODE` = :COMPANY_CODE LIMIT 1", $companyQueryData);

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
        <a class="navbar-brand" href="#"><?php echo $companyInfo->COMPANY_NAME; ?></a>
    </div>

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu"> 
                <li style="padding:10px;">
                    <center>
                        <img src="<?php echo IMG.$companyInfo->IMAGE_PATH ?>" class="img img-responsive companyIcon">
                        <h4></h4>
                    </center>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>DASHBOARD/"><i class="fa fa-home fa-fw"></i> Dashboard</a>
                </li> 
                <?php 
                    if($_SESSION['JV']['USER_CODE'] == "IS"){
                ?>
                <li>
                    <a href="<?php echo BASE_URL; ?>REPORTS/"><i class="far fa-file-alt"></i> Reports</a>
                </li>
                <?php 
                    }
                ?>
                <li>
                    <a href="<?php echo BASE_URL; ?>LOGOUT"><i class="fa fa-sign-out-alt fa-fw"></i> Logout</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
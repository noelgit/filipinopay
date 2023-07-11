<?php  
	if($_SESSION['TIEZA']['LOGGED'] != 'true'){
		if($firstPage == 'travel-tax'){
			if(isset($_GET['TRN']) AND isset($_GET['STATUS'])){
 				
			}else{
				header('Location: '.BASE_URL);
			}
		}else{
			header('Location: '.BASE_URL);
		}
	}else{ 
		$subscriberID = $_SESSION['TIEZA']['SUBSCRIBERS_ID']; 

		$data = array();
		$data['SUBSCRIBERS_ID'] = $subscriberID;
		$userData = $dbTieza->getRow("SELECT * FROM tbl_account_profile AS tap 
			WHERE tap.`SUBSCRIBERS_ID` = :SUBSCRIBERS_ID LIMIT 1", $data);

		$dataComplete = $userData->IS_DATA_COMPLETE;
		$fullName = $userData->FIRST_NAME.' '.$userData->LAST_NAME; 
	}   	
?> 
<nav class="navbar navigationBackground">
	<div class="container">
		<div class="row full-width">
			<div class="col-sm-8">
		    	<div class="navbar-header"> 
					<a href="<?php echo BASE_URL; ?>" class="navbar-brand">
						<img src="<?php echo IMG; ?>logo.png" style="width:120px;">
						<span>TOURISM INFRASTRUCTURE AND ENTERPRISE ZONE AUTHORITY</span>
					</a> 
		    	</div>
		    </div>
		    <div class="col-sm-4 text-right">
			    <ul class="nav navbar-nav mt20">
					<li class="dropdown headerDropdown">
						<label> <?php echo $custom->upperCaseWords($fullName); ?></label>
						<a class="dropdown-toggle navbarDropdown" data-toggle="dropdown" href="#">
							<i class="fa fa-user-circle"></i>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="<?php echo BASE_URL; ?>profile">Profile</a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="<?php echo BASE_URL; ?>logout">Log out</a>
							</li> 
						</ul>
					</li> 
			    </ul>
			</div>
		</div>
  	</div>

	<div class="line"></div>
</nav>
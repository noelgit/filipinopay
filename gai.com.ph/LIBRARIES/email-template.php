<?php 
	class emailTemplate{  

	 	function emailMerchantReport($merchantName, $currentDateTime){ 
	 		$emailContent = '
	 		<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>Payment Acknowledgement Receipt.</title>
				</head>
				<body>   
					<label>Hi '.$merchantName.',</label><br>

					<p>Attached herewith the settlement report as of '.$currentDateTime.'</p>


					<p>Thank you,</p>

					<p>FilipinoPay</p> 

				</body>
			</html>
			'; 
	  		return $emailContent; 
	 	} 

	 	function emailJvReport($jvName, $currentDateTime){ 
	 		$emailContent = '
	 		<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>Payment Acknowledgement Receipt.</title>
				</head>
				<body>   
					<label>Hi '.$jvName.',</label><br>

					<p>Attached herewith the settlement report as of '.$currentDateTime.'</p>


					<p>Thank you,</p>

					<p>FilipinoPay</p> 

				</body>
			</html>
			'; 
	  		return $emailContent; 
	 	} 

	}
?>
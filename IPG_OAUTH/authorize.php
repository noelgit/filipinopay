<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php';

	$request = OAuth2\Request::createFromGlobals();
	$response = new OAuth2\Response();

	// validate the authorize request
	if (!$server->validateAuthorizeRequest($request, $response)) {
	    $response->send();
	    die;
	}

	// print the authorization code if the user has authorized your client
	$is_authorized = TRUE;
	$server->handleAuthorizeRequest($request, $response, $is_authorized);
	if ($is_authorized) {
	  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
	  $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40); 
	  echo json_encode(array("Status" => "Success", "Code" => "$code"));
	  exit();
	  //exit("SUCCESS! Authorization Code: $code");
	}
	$response->send();
?>
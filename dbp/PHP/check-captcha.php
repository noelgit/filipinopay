<?php

$secret	=	$_REQUEST["secret"];

$gRecaptchaResponse	=	$_REQUEST["response"];

$remoteIp	= $_SERVER['REMOTE_ADDR'];

$recaptchaBase = '../../lib/recaptcha-master/src/ReCaptcha';

require_once $recaptchaBase . '/ReCaptcha.php';

require_once $recaptchaBase . '/RequestMethod.php';

require_once $recaptchaBase . '/RequestParameters.php';

require_once $recaptchaBase . '/Response.php';

require_once $recaptchaBase . '/RequestMethod/Post.php';

require_once $recaptchaBase . '/RequestMethod/Socket.php';

require_once $recaptchaBase . '/RequestMethod/SocketPost.php';
 
$recaptcha = new \ReCaptcha\ReCaptcha($secret);

$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

if ($resp->isSuccess()) {

    $result	= "true";

} else {

    $result = $resp->getErrorCodes();

}



echo $result;



?>
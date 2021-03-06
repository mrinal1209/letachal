<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once('Controllers/MobileVerificationNexmoApi.php');
require_once('Controllers/UserController.php');
require_once('MiddleWare/TokenAuth.php');
$app = new \Slim\App;

$app->get('/',function(Request $request,Response $response){

$data = array("Message"=>"Hello World");
return $response->withJson($data);

});
$app->post('/api/SMS/getcode',\MobileVerificationNexmoApi::class.':sendSmsVerificationCode');

$app->post('/api/SMS/verifycode',\MobileVerificationNexmoApi::class.':verifySmsCode');

$app->post('/api/user/register',\UserController::class.':userRegistration');

$app->put('/api/user/deactivate/{id}',\UserController::class.':userDeactivation')->add(new TokenAuth);

$app->run();

?>

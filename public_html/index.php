<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once('Controllers/MobileVerificationNexmoApi.php');

$app = new \Slim\App;

$app->get('/',function(Request $request,Response $response){

$data = array("Message"=>"Hello World");
return $response->withJson($data);

});
$app->post('/api/SMS/getcode',\MobileVerificationNexmoApi::class.':sendSmsVerificationCode');

$app->post('/api/SMS/verifycode',\MobileVerificationNexmoApi::class.':verifySmsCode');

$app->run();
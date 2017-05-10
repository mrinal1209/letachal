<?php
/**
 * Created by PhpStorm.
 * User: mrinal
 * Date: 4/5/17
 * Time: 12:23 PM
 */
class MobileVerificationNexmoApi{

    private $apiKey = "be346aa7";
    private $apiSecret ="15ea836ec138c37a";

    public function sendSmsVerificationCode($request , $response){
        $number  = filter_var( $request->getParam('number'), FILTER_SANITIZE_NUMBER_INT);
        $url = "https://api.nexmo.com/verify/json";
        $ch = curl_init($url);
# Setup request to send json via POST.
        $data = json_encode( array( "api_key"=> $this->apiKey , "api_secret" => $this->apiSecret , "number" => $number , "brand" => "LetaChal Verify" ) );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
# Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

    }


    public function verifySmsCode($request , $response , $args){

            $requestId = filter_var($request->getParam('request_id'), FILTER_SANITIZE_STRING);
            $code = filter_var($request->getParam('code'), FILTER_SANITIZE_NUMBER_INT);
            $url="https://api.nexmo.com/verify/check/json";
            $ch = curl_init($url);
    # Setup request to send json via POST.
            $data = json_encode( array( "api_key"=> $this->apiKey , "api_secret" => $this->apiSecret , "request_id" => $requestId , "code" => $code ) );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    # Return response instead of printing.
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;

    }
}

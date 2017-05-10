<?php

require_once(__DIR__.'/../Models/UniversalConnect.php');

class UserController{

  private $hookup;

  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }

  public function userRegistration($request , $response)
  {
    $phoneno = filter_var( $request->getParam('user_phone_no'), FILTER_SANITIZE_NUMBER_INT);
    $nickname =filter_var( $request->getParam('user_nickname'), FILTER_SANITIZE_STRING);
    $profilepic=filter_var($request->getParam('user_profile_pic'), FILTER_SANITIZE_STRING);
    $token =md5(md5($nickname)."".md5(microtime()));
    $upload_directory = "Uploads/ProfilePics/";
    $TargetPath=$phoneno;

    if(file_put_contents($upload_directory.$TargetPath.".jpeg",base64_decode($profilepic)) != false)
    {
        $profilepic=$upload_directory.$TargetPath.".jpeg";
    }


    $sql = "INSERT INTO user_account (user_nickname,user_phone_no,user_profile_pic,user_token)
            VALUES(:user_nickname , :user_phone_no , :user_profile_pic , :user_token)";
    try{
    $stmt = $this->hookup->prepare($sql);

    $stmt->bindParam(':user_nickname' , $nickname);
    $stmt->bindParam(':user_phone_no' , $phoneno);
    $stmt->bindParam(':user_profile_pic' , $profilepic);
    $stmt->bindParam(':user_token' , $token);
    // execute the query
    $stmt->execute();
    $id=$this->hookup->lastInsertId();
    $data = array('user_id'=>$id,'user_token'=>$token,'Message'=>'Record Added Successfully');

    }
    catch(PDOException $e)
    {
    $data = array('Message'=>$e->getMessage());
    }

    $newResponse =  $response->withJson($data);
    return $newResponse;

  }

}


 ?>

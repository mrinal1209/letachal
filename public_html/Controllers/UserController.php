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
    $token =md5(md5($phoneno)."".md5(microtime()));
    $upload_directory = "Uploads/ProfilePics/".$phoneno;

    if(file_put_contents($upload_directory.".jpeg",base64_decode($profilepic)) != false)
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
      if($e->errorInfo[1] == 1062)
      {
        $sql="SELECT user_id, user_token , user_is_active FROM user_account WHERE user_phone_no=".$phoneno;
        $stmt = $this->hookup->prepare($sql);
        $stmt->execute();
        // for removing the scaler error
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);
        $data['Message']='Already a existing Member';
        if($data['user_is_active'] == 0)
        {
          $sql="UPDATE user_account SET user_is_active='1' WHERE user_phone_no=".$phoneno;
          $stmt = $this->hookup->prepare($sql);
          // execute the query
          $stmt->execute();
        }
          unset($data['user_is_active']);
      }
      else {
        $data = array('Message'=>$e->getMessage());
      }

    }

    $newResponse =  $response->withJson($data);
    return $newResponse;

  }

  public function userDeactivation($request , $response){
    if($request->hasHeader('Authorization')){
    $token = filter_var($request->getHeader('Authorization')[0],FILTER_SANITIZE_STRING);
    $id = filter_var( $request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT);
  //  try{
    $stmt = $this->hookup->prepare("SELECT user_id FROM user_account WHERE user_token=".$token);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if( ! $row)
    {
        $data = array('Message'=>"No such Token found");
    }
    else if($row['user_id']!=$id)
    {
        $data = array('Message'=>"Token is invalid for this user_id = >".$id);
    }
    else {
      $sql="UPDATE user_account SET user_is_active='0' WHERE user_id=".$id;
      $stmt = $this->hookup->prepare($sql);
      // execute the query
      $stmt->execute();
      $data = array('Message'=>"User Deactivated Successfully");
    }
  /*}catch(PDOException $e)
  {
    $data = array('Message'=>"No such Token found");
  }*/
  }
    else{
      $data = array('Message'=>"Enter a Header Authorization token");
    }

    $newResponse =  $response->withJson($data);
    return $newResponse;
  }

}


 ?>

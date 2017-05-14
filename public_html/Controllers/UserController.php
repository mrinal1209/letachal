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
    //genrating 32bit token for authentication based on unique field PHONENO.
    $token =md5(md5($phoneno)."".md5(microtime()));
    //setting up image location
    $upload_directory = "Uploads/ProfilePics/".$phoneno;
    //inserting image  to sepecifed location
    if(file_put_contents($upload_directory.".jpeg",base64_decode($profilepic)) != false)
    {
        $profilepic=$upload_directory.".jpeg";
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
      //Catching Duplicate records and showing them as registered members.
      if($e->errorInfo[1] == 1062)
      {
        $sql="SELECT user_id, user_token , user_is_active FROM user_account WHERE user_phone_no=".$phoneno;
        $stmt = $this->hookup->prepare($sql);
        $stmt->execute();
        // for removing the scaler error
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);

        // adding custom Message in Response
        $data['Message']='Already a existing Member';

        //Re-activating the deactive user

        if($data['user_is_active'] == 0)
        {
          $sql="UPDATE user_account SET user_is_active='1' WHERE user_phone_no=".$phoneno;
          $stmt = $this->hookup->prepare($sql);
          $stmt->execute();
        }
        //removing data which is not supposed to be seen .
          unset($data['user_is_active']);
      }
      else {
        $data = array('Message'=>$e->getMessage());
      }

    }
//genralized response fetching different results.
    $newResponse =  $response->withJson($data);
    return $newResponse;

  }

  public function userDeactivation($request , $response){
      $user_id = filter_var( $request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT);
      $sql="UPDATE user_account SET user_is_active='0' WHERE user_id=".$user_id;
      $stmt = $this->hookup->prepare($sql);
      $stmt->execute();
      $data = array('Message'=>"User is :- $user_id is deactivated");
      $newResponse =  $response->withJson($data);
      return $newResponse;

  }

}


 ?>

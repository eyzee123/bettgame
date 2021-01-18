<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: content-type, Authorization, x-requested-with');
header('content-type: application/json;charset=utf-8');



include("z_db.php");
date_default_timezone_set('Asia/Manila');
$datedata = date("Y-m-d H:i:sa");



if(@$_GET['action']=='login'){

   //login code
   $uname = @$_GET['username'];
   $pass = @$_GET['password'];
   if($uname!="" && $pass!="")
   {
      $qre = mysqli_query($con,"select * from tempusermaster t where username='$uname' and pass='$pass' and isactive='1'");
      if(mysqli_num_rows($qre)!=0)
      {
            $res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
            session_start();
            $_SESSION['MSRNO']=$res["Msrno"];
            $_SESSION['USERNAME']=$res["username"];
            $_SESSION['FNAME']=$res["name"];
            $_SESSION['RAND_STRING']=$res["rand_string"];
            $_SESSION['MOBILE']=$res["mobile"];
            echo "Success";
      }
      else
      {
         echo "Invalid Username or Password";
      }
   }

}

?>
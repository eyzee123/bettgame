<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: content-type, Authorization, x-requested-with');
header('content-type: application/json;charset=utf-8');



include("z_db.php");
date_default_timezone_set('Asia/Manila');
$datedata = date("Y-m-d H:i:sa");

function RandomString()

{

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

$randstring = '';

for ($i = 0; $i < 10; $i++) {

	$randstring .= $characters[rand(0, strlen($characters))];

}

return $randstring;

}

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

}else if(@$_GET['action']=='register'){
   $username = mysqli_real_escape_string($con,str_replace(' ', '', trim($_GET['username'])));
		/*$name = mysqli_real_escape_string($con,trim($_POST['name']));
		$email = mysqli_real_escape_string($con,trim($_POST['email']));*/
		$pass = mysqli_real_escape_string($con,trim($_GET['password']));
		$mobile = mysqli_real_escape_string($con,trim($_GET['mobile']));
      $sponsor = mysqli_real_escape_string($con,trim($_GET['sponsor']));
		
		$status = "OK";
		$msg = "";
		$dstr = "";
		$sponsor_id = 0;
		
		if(!ctype_digit($sponsor))
		{
			$status = "NOTOK";
			$msg = "Invalid Sponosr-Id";
		}
		if(!ctype_digit($mobile))
		{
			$status = "NOTOK";
			$msg = "Invalid Mobile No.";
		}
		
		
		if($sponsor!="")
		{
			$qre = mysqli_query($con,"select * from tempusermaster where rand_string='$sponsor'");
			if(mysqli_num_rows($qre)>0)
			{
				$res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
				$sponsor_id = $res["Msrno"];
				$dstr = $res["dstr"];
			}
			else
			{
				$status = "NOTOK";
				$msg = "Invalid Refferal Link";
			}
		}
		else
		{
			$status = "NOTOK";
			$msg = "Enter Sponsor-Id";
		}
		
		if($username=="")
		{
			$status = "NOTOK";
			$msg = "Enter Username";
		}
		else
		{
			$qre = mysqli_query($con,"select * from tempusermaster where uname='$username'");
			echo $username;
			if(mysqli_num_rows($qre)>0)
			{
				$status = "NOTOK";
				$msg = "This Username already in use choose another Username";
			}
		}
		if($pass=="")
		{
			$status = "NOTOK";
			$msg = "Enter Password";
		}
		
		if($status=="NOTOK")
		{
			echo "<script>alert('$msg');</script>";
		}
		else if($status=="OK")
		{
			$unstring='';
			$k=1;
			$unstring  = RandomString();
			while($k>0)
			{
				$qre = mysqli_query($con,"select * from tempusermaster where rand_string='$unstring'");
				if(mysqli_num_rows($qre)==0)
				{
					$k=0;
				}
				else
				{
					$unstring  = RandomString();
				}
			}
			mysqli_query($con,"insert into tempusermaster (username,name,pass,email,mobile,ondate,isactive,sponsor,rand_string) VALUES('$username','','$pass','','$mobile','$datedata','1','$sponsor_id','$unstring')");	
			$last_id = mysqli_insert_id($con);
			if($dstr=="")
			{
				$newdstr = ','.$last_id.',';
			}
			else
			{
				$newdstr = $dstr.$last_id.',';
			} 
			
			mysqli_query($con,"update tempusermaster set dstr='$newdstr' where Msrno='$last_id'");
			
			/*$qre = mysqli_query($con,"select * from usertree where userid='$sponsor'");
			$res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
			$newParentstr = '';
			$newParentstr = $res["parentstr"].$last_id.",";
			mysqli_query($con,"insert into usertree (Msrno,userid,fname,isactive,ondate,Itemid,parentstr,sponsor) VALUES('$last_id','$username','$name','1','$datedata','$plan','$newParentstr','$spmsrno')");*/
			
			
			mysqli_query($con,"insert into uwalletstatus (Msrno,cr,dr,isactive,ondate) VALUES('$last_id','0','0','1','$datedata')");
			
			
			
			// echo "<script>alert('Your Account has been created successfully');window.location='login.html';</script>";
			// echo "<script>location.href = 'login.html';</script>";
			echo "Success";
		}
}


?>
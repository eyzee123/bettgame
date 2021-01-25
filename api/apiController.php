<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: content-type, Authorization, x-requested-with');
header('content-type: application/json;charset=utf-8');



include("z_db.php");
date_default_timezone_set('Asia/Manila');
$datedata = date("Y-m-d H:i:sa");

session_start();

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
			if($qre){
				if(mysqli_num_rows($qre)>0)
				{
					$status = "NOTOK";
					$msg = "This Username already in use choose another Username";
				}
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
	}else if(@$_GET['action']=="home"){
		$qre = mysqli_query($con,"select * from uwalletstatus where Msrno='".$_SESSION['MSRNO']."'");
		if(mysqli_num_rows($qre)!=0)
		{
			$res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
			$balance = $res["cr"];
		}	
	
		$qre = mysqli_query($con,"select * from fight_number_master order by id desc LIMIT 1");
		$res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
		$fight_no = $res["fight_no"];

	}else if(@$_GET['action']=="fight_no"){
		$data = "";
		for($j=1;$j<=300;$j++)
		{
			$i=1;
			//and DATE_FORMAT(ondate,'%d-%m-%Y')=DATE_FORMAT('$datedata','%d-%m-%Y')
			$qre = mysqli_query($con,"select * from fight_number_master where fight_status in (1,2,3,4)  and fight_no='$j'");
			if(mysqli_num_rows($qre)>0)
			{
				while($res = mysqli_fetch_array($qre,MYSQLI_ASSOC))
				{
						$color = "";
						$draw = "";
						if($res["fight_status"]=="1")
						{
							$color = "#C81013";
							$draw = "#fff";
						}
						else if($res["fight_status"]=="2")
						{
							$color = "#2122EC";
							$draw = "#fff";
						}
						else if($res["fight_status"]=="3")
						{
							$color = "#FFFF00";
							$draw = "#000";
						}
						else if($res["fight_status"]=="4")
						{
							$color = "#FF9900";
							$draw = "#fff";
						}
						if($j>=1 && $j<=9)
						{
						//border-radius:100% !important;
							$data .="<tr><td class='badge-holder text-center badge_number_automatic_1'>	
								<span class='badge' style='background:".$color.";color:".$draw.";'>&nbsp;&nbsp;</span>	
						</td></tr>";
						}
						else
						{
							$data .="<tr><td class='badge-holder text-center badge_number_automatic_1'>	
								<span class='badge ' style='background:".$color.";color:".$draw.";'>&nbsp;&nbsp;</span>	
						</td></tr>";
						}
						
						
				}	
			}
			else
			{
				$data .="<tr><td class='badge-holder text-center badge_number_automatic_1'>	
								<span class='badge ' style='background:#292B2E;'>&nbsp;&nbsp;</span>	
						</td></tr>";
			}
			if($j%5==0)
			{
				// $data .="</table></td><td><table class='table table-bordered' cellpadding='0' cellpadding='0'>";
			}
	}//for end	
		// echo $draw;
		// echo $color;
		echo $data;
	}else if(@$_GET['action']=="welcome"){
		$qre = mysqli_query($con,"select * from tempadmin");
						$res = mysqli_fetch_array($qre,MYSQLI_ASSOC);
						echo $res["dash_news"];
	}


?>
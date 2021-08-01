<?php
// include_once ('dbconn.php');
require_once ('db_settings.php');
if (isset($_SESSION['user_id']) )
{
	if ($_SESSION['user_id'] <> '')
	{
		header('Location: student.php');
		exit();
	}
	
} 

$errText = '';

// check previous cookie set or not
/*if (isset($_COOKIE['ti_user']) && isset($_COOKIE['ti_pass'])) 
{
    
	$user_name = $_COOKIE['ti_user'];
	$user_pass = $_COOKIE['ti_pass'];
	
	 

	if (validate_username_password($user_name, $user_pass, $errText, true))
	{
		header('Location: student.php');	
		exit();
	}
} */
	



if(isset($_POST['login']))
{
	
	if(!isset($_POST['user']) || empty($_POST['user'])){
		$errText .= '* Please enter a username <br>';
		}
	if(!isset($_POST['pass']) || empty($_POST['pass'])){
		$errText .= '* Please enter a password <br>';
		}
	
	 
	if($errText == '')
	{
		
		$user_name = $_POST['user']; 
		$user_pass =  $_POST['pass'];  
		
		if (validate_username_password($user_name, $user_pass, $errText))
		{
			 
			$sessionTime = 0;
			ob_clean();
			header('Location: student.php');
			exit();			
		}
		 
	}
}

function validate_username_password($user_name, $user_pass, &$errText, $is_cookie = false)
{
	global $conn;
	$user_name	=	$user_name;	
	$user_pass	=	$user_pass;
	$user_pass	=	md5($user_pass);
		
	$sql = "SELECT * FROM pra_users where username = '$user_name' and password = '$user_pass'";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
	  	$_SESSION['user_id']=$row["userID"];
	    $_SESSION['user_name']=$row["username"];
		$_SESSION['eamil']=$row["email"];
		$_SESSION['auth_type']='ADMIN';
		
		return true;
	  }
	} else {
	  return false;
	}	
}

if ($errText != '')
$errText = '<div class="alert alert-danger">' . $errText . '</div>';
?>
<!doctype html>
<html lang="en">
<?php
include ('head.php');
?>

<style>
.login_logo{height:150px;width:300px;max-width:unset;}
</style>

<div id="page-wrapper">
<div id="main-layout">

<div id="logo-area"></div>
 
 <div id="carousel-login" class="carousel slide" data-ride="carousel" data-interval="0">

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <div class="item active">

		<!-- Content Start -->
		<div class="panel panel-default login_panel">
		  <div class="panel-body ">
			<div class="login_logo_container"> <img src="images/logo.png" class="login_logo"> </div>	
			<h2 class="login_title2 color_l"> User Login</h1>
			<?php echo($errText);?>
				<form role="form" action="" method="post" class="color_l ">
				  <div class="form-group">
					<label  class="color_l" for="InputEmail1">User Name</label>
					<input type="text" class="form-control" name="user" id="InputEmail1" placeholder="User Name">
				  </div>
				  <div class="form-group">
					<label  class="color_l" for="InputPassword1">Password</label>
					<input type="password" name="pass" class="form-control" id="InputPassword1" placeholder="Password">
				  </div>
				  <div class="form-group">
					<button type="submit" name="login" value="login" class="btn btn-primary">
					<i class="fa fa-sign-in"></i>  &nbsp; Login &nbsp;</button>
					</div>
				</form>

		  
		  </div><!-- Panel Body End -->
		</div>
		<!-- Content End -->

    </div>
  </div>

</div>

 


</div>
</div>
 



<script type="text/javascript" >

$('.forgot_pass').click(function () {
  $('#carousel-login').carousel('next');
});

$('.back_login').click(function () {
  $('#carousel-login').carousel('prev');
});

$(function()
{
	$("#passRecForm").submit( function(e) 
	{ 
		$('#recover_password_btn').attr('disabled','disabled');
		$('#recover_password_icon').addClass('fa-spinner fa-spin');
		$('#recover_password_icon').removeClass('fa-unlock-alt ');
		
		e.preventDefault();
		dataString=$("#passRecForm").serialize();
		$.ajax(
		{
			type:"POST",
			url:"<?php echo($_SERVER['SCRIPT_NAME']);?>",
			data:dataString,
			dataType:"html",
			success:function(e)
			{
				$("#results").html("<div>"+e+"</div>")
				$('#recover_password_btn').removeAttr('disabled','disabled');
				$('#recover_password_icon').removeClass('fa-spinner fa-spin');
				$('#recover_password_icon').addClass('fa-unlock-alt ');
			},
			error:function(e)
			{
				$("#results").html("<div>Error</div>")
				$('#recover_password_btn').removeAttr('disabled','disabled');
				$('#recover_password_icon').removeClass('fa-spinner fa-spin');
				$('#recover_password_icon').addClass('fa-unlock-alt ');
			}
			
		})

	})

});

</script>
</body>
</html>

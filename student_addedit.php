<?php
require_once ('db_settings.php');
global $conn, $db_error;
$errText = '';
$succText = '';
if (isset($_POST['dkv']))
{
	if(!isset($_POST['name']) || empty($_POST['name'])){
		$errText .= '* Please enter a name <br>';
		}
	if(!isset($_POST['id']) || empty($_POST['id'])){
		$errText .= '* Please enter a student id <br>';
		}

	if(!isset($_POST['age']) || empty($_POST['age'])){
		$errText .= '* Please enter a age <br>';
		}
		
	if(!isset($_POST['subject']) || empty($_POST['subject'])){
		$errText .= '* Please enter a subject <br>';
		}
		
	if(!isset($_POST['grade']) || empty($_POST['grade'])){
		$errText .= '* Please enter a grade <br>';
		}
		
	if(!isset($_POST['average_score']) || empty($_POST['average_score'])){
		$errText .= '* Please enter a average_score <br>';
		}

	if($errText == '')
	{	
						
	$dkv_value		=	$_POST['dkv'];
	if($dkv_value	== '0'){$dkv = 0;}
	else{$dkv = $dkv_value;}
			
	$name = $_POST['name'];
	$id = $_POST['id'];
	$age = $_POST['age'];
	$subject = $_POST['subject'];
	$grade = $_POST['grade'];
	$average_score = $_POST['average_score'];
	
	if ($dkv == 0) 
	{	
		$query = "INSERT INTO pra_students (name,id,age,subject,grade,average_score)
VALUES ('$name',$id,'$age','$subject','$grade','$average_score')";
		
		if ($conn->query($query) === TRUE) {
		  $errText .= '* Student added successfully <br>';
		} else {
		  $errText .= "* Error";
		}

		header('Location: student_addedit.php?msg=success');
		exit();			
	}
	else
	{
		$query = "update pra_students set name = '$name',id= $id,age = '$age', subject = '$subject', average_score = '$average_score' Where studentID = $dkv";
		if ($conn->query($query) === TRUE) {
		  $errText .= '* Student updated successfully <br>';
		} else {
		  $errText .= "* Error";
		}		

		header('Location: student_addedit.php?msg=success&keyid='.$dkv);
			exit();			
	}
	exit();
	}
}
?>

<?php 
require_once ('header.php'); 

$keyid = 0;
$name = '';
$id = '';
$age = '';
$subject = '';
$grade = '';
$average_score = '';

if (isset($_GET["keyid"]))
{
	$keyid = $_GET["keyid"];	
	$query = "select * from pra_students where studentID = $keyid";
	$result = $conn->query($query);
	while($row = $result->fetch_assoc()) {
	  	$studentID=$row["studentID"];
	    $name=$row["name"];
		$age=$row["age"];
		$id=$row["id"];
		$subject=$row["subject"];
		$grade=$row["grade"];
		$average_score=$row["average_score"];
		
	}
}

	
?>

<style>
.isdiscontinued label{margin-top:30px;}
@media  (max-width:767px){.isdiscontinued label{margin-top:0px;}}
.form_tabs .tab-pane{border-bottom:none;}
.table > tbody > tr > td{word-break:break-all;}
</style>
<?php
if ($errText != '')
$errText = '<div class="alert alert-danger">' . $errText . '</div>';
if(!empty($_GET['msg'])){
 $succText = '<div class="alert alert-success" role="alert">Record successfully stored</div>';
}
?>
<div id="frm_inventory_master" class="container-fluid content_container  drop_shadow">
    <div class="title"><?php if($keyid=='0'){echo 'Add New Student';}else{echo 'Update Student';} ?>
		<button class="btn  dt_buttons close_this" data-id="student.php" style="padding:6px;"><i class="fa fa-times fa-md"></i><span>&nbsp; Close </span></button>
		<div id='alert_msg' style="width:40%;"></div> 
	</div>
	<div class="container-fluid">
	<?php echo($errText);?>
	<?php echo($succText);?>
		<form action="student_addedit.php"  method="post" id="student_addedit" class="ae_form" role="form" enctype="multipart/form-data">
			<div class="tab-content form_tabs">
         		<div class="tab-pane active" id="info_tab">
					<div class="row fluid ">
						<div class="col col-xs-12 col-md-7 group_columns form-group ">
							<label class="control-label name" for="name" id="name">Student Name</label>
							<input  type="text" class="form-control"  id="name"  name="name" value= "<?php echo $name; ?>" placeholder="Student Name" data-validation="required"  data-validation-error-msg="" style="text-transform:uppercase;" title="<?php echo $name; ?>" autocomplete="off"> 
						</div>						
						<div class="col col-xs-12 col-md-7 form-group">
							<label class="control-label id" for="id" id="id">Student Roll ID</label>
							<input  type="text" class="form-control"  id="id"  name="id" value= "<?php echo $id; ?>" placeholder="Student ID" data-validation=""  style="text-transform:uppercase;" title="<?php echo $id; ?>">
						</div>
						<div class="col col-xs-12 col-md-7 form-group">
							<label class="control-label age" for="age" id="age">Age</label>
							<input  type="text" class="form-control"  id="age"  name="age" value= "<?php echo $age; ?>" placeholder="Age" data-validation=""  style="text-transform:uppercase;" title="<?php echo $age; ?>">
						</div>
						<div class="col col-xs-12 col-md-7 form-group">
							<label class="control-label subject" for="subject" id="subject">Subjects</label>
							<input  type="text" class="form-control"  id="subject"  name="subject" value= "<?php echo $subject; ?>" placeholder="Subjects" data-validation=""  style="text-transform:uppercase;" title="<?php echo $subject; ?>">
						</div>
						<div class="col col-xs-12 col-md-7 form-group">
							<label class="control-label grade" for="grade" id="grade">Grade</label>
							<input  type="text" class="form-control"  id="grade"  name="grade" value= "<?php echo $grade; ?>" placeholder="Grade" data-validation=""  style="text-transform:uppercase;" title="<?php echo $grade; ?>">
						</div>
						<div class="col col-xs-12 col-md-7 form-group">
							<label class="control-label average_score" for="average_score" id="average_score">Score</label>
							<input  type="text" class="form-control"  id="average_score"  name="average_score" value= "<?php echo $average_score; ?>" placeholder="Score" data-validation=""  style="text-transform:uppercase;" title="<?php echo $average_score; ?>">
						</div>
					</div>
				</div>
				<input name="form_id" value="inventory_master" type="hidden"/>
				<input id="dkv" name="dkv" value="<?php echo $keyid; ?>" type="hidden"/>
				<button id="btn_addnew" class="btn dt_buttons pull-left" style="padding:6px;"><i class="fa fa-plus fa-md"></i><span>&nbsp; <?php if($keyid=='0'){echo 'Add New Student';}else{echo 'Update Student';} ?> </span></button>
			</div>
		</form>
	</div>
</div>	
<?php include ('footer.php'); ?>


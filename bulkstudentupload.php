<?php
require_once ('db_settings.php');
global $conn, $db_error;
$succText = '';
if (isset($_POST['dkv']))
{
$csv = array();

// check there are no errors
if($_FILES['csv']['error'] == 0){
    $name = $_FILES['csv']['name'];
    $ext = @strtolower(end(explode('.', $_FILES['csv']['name'])));
    $type = $_FILES['csv']['type'];
    $tmpName = $_FILES['csv']['tmp_name'];

    // check the file is a csv
    if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
            // necessary if a large csv file
            set_time_limit(0);

            $row = 0;

            while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // number of fields in the csv
                $col_count = count($data);

                // get the values from the csv
                $csv[$row]['col1'] = $data[0];
                $csv[$row]['col2'] = $data[1];

                $name = $data[0];
				$id = $data[1];
				$age = $data[2];
				$subject = $data[3];
				$grade = $data[4];
				$average_score = $data[5];

                $query = "INSERT INTO pra_students (name,id,age,subject,grade,average_score,createdby) VALUES ('$name',$id,'$age','$subject','$grade','$average_score','CSV')";
				
				if ($conn->query($query) === TRUE) {
				  $errText .= '* Student added successfully <br>';
				} else {
				  $errText .= "* Error";
				}

                // inc the row
                $row++;
            }
            fclose($handle);

            header('Location: bulkstudentupload.php?msg=success');
				exit();			
        }
    }
}
}
?>

<?php 
require_once ('header.php'); 
?>

<style>
.isdiscontinued label{margin-top:30px;}
@media  (max-width:767px){.isdiscontinued label{margin-top:0px;}}
.form_tabs .tab-pane{border-bottom:none;}
.table > tbody > tr > td{word-break:break-all;}
</style>
<?php 
if(!empty($_GET['msg'])){
 $succText = '<div class="alert alert-success" role="alert">CSV uploaded successfully</div>';
}
?>
<div id="" class="container-fluid content_container  drop_shadow">
    <div class="title">Bulk Upload Student
		<button class="btn  dt_buttons close_this" data-id="student.php" style="padding:6px;"><i class="fa fa-times fa-md"></i><span>&nbsp; Close </span></button>
		<div id='alert_msg' style="width:40%;"></div> 
	</div>
    <div class="container-fluid">
		<form action="bulkstudentupload.php"  method="post" id="bulkstudentupload" class="ae_form" role="form" enctype="multipart/form-data">
			<?php echo($succText);?>
			<div class="tab-content form_tabs">
         		<input type="file" name="csv" value="" />
			</div>
			<br>
			<input id="dkv" name="dkv" value="0" type="hidden"/>
         	<button id="btn_addnew" class="btn dt_buttons pull-left" style="padding:6px;"><i class="fa fa-plus fa-md"></i><span>&nbsp; Upload CSV</span></button>
		</form>
	</div>
</div>	
<?php include ('footer.php'); ?>


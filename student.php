<?php include ('header.php');
$succText = '';
if(!empty($_GET['msg'])){
 $succText = '<div class="alert alert-success" role="alert">Student deleted successfully</div>';
}
?>
<div class="container-fluid content_container drop_shadow" >
	<?php echo($succText); ?>
	<div class="title">Student List
		<button class="btn  dt_buttons close_this" data-id="student_addedit.php" ><i class="fa fa-times fa-md"></i><span>&nbsp; Add Student </span></button>
	</div>
	<div id="dd_sales_quotes_master" class="df_container">
		<table id="tbl_student_list" class="table table-striped table-bordered datalist" width="100%" cellspacing="0">
			<thead>
				<tr>
					 <th width="13%"> # </th>
					 <th width="13%"> Name </th>
					 <th width="10%"> ID </th>
					 <th width="15%"> Age  </th>
					 <th width="12%"> Subjects  </th>
					 <th width="8%"> Grade </th>
					 <th width="8%"> Average Score </th>
					 <th width="6%"> Status </th>
					 <th>Action</th>
				 </tr>
			</thead>
			<tbody>
			<?php
				global $conn;
				$query 		= "SELECT *	FROM pra_students WHERE status = 'Active' ORDER BY studentID DESC"; 
				$retval = $conn->query($query);
				if ($retval->num_rows < 1)
				{
					$connected = false;
					return -1;
				}
				$i=1;
				while ($datarows = $retval->fetch_object()) {							
					echo "<tr>";
						echo "<td> $i </td>";
						echo "<td>".strtoupper($datarows->name)."</td>";
						echo "<td> $datarows->id </td>";
						echo "<td> $datarows->age </td>";
						echo "<td>".strtoupper($datarows->subject)."</td>";
						echo "<td>".strtoupper($datarows->grade)."</td>";
						echo "<td>".strtoupper($datarows->average_score)."</td>";
						echo "<td> $datarows->status </td>";
						echo "<td> 
							<a href='student_addedit.php?keyid=".$datarows->studentID."'> <i class='fa fa-edit fa-lg' style='color: red !important; background: none !important;'></i> </a>
							<a href='deletestudent.php?keyid=".$datarows->studentID."' onclick='return confirm(`Are you sure, you want to delete it?`)''> <i class='fa fa-trash-o fa-lg' style='color: red !important; background: none !important;'></i> </a>
							</td>";
					echo "</tr>";
				$i++;}
			?>
			</tbody>
		</table>
	</div>
</div>

<style>
.delete_record {cursor: pointer;color: #ff0000 !important;text-align: center;overflow: hidden;display: block;text-align: center;padding: 2px;}
.delete_record:hover {cursor: pointer;color: #fff !important;text-align: center;background: #ff0000 !important;}
.df_container{overflow:scroll !important; overflow: auto !important;}
</style>

<script type="text/javascript" >
$(document).ready(function()
{
	var var_datatable  = $('#tbl_student_list').dataTable({
		dom: 'Zlfrtip',
		stateSave: false,
		"pagingType": "full_numbers",
		"iDisplayLength": 100,
		"aaSorting": [],
		"bPaginate": true,
		"bFilter" : true,
		"responsive" : false,
		"aLengthMenu": [[100, 200, 300, 400, 500,  -1], [100, 200, 300, 400, 500, 'All']],
		"language": {
			"emptyTable": "<span class='text-danger'><b>No Records Found. </b>  </span>"
		}
	});
});

var current_delete_node;
$(document.body).on('click', '.delete_record2', function(event) 
	{
		form_updated = true;
		current_delete_node = $(this).attr('id');
		msg = $(this).attr('data-msg');
		if(msg)
		{
			var current_row = $(this).closest('tr');
			$(current_row).hide('slow');					
			$(current_row).html('');
		}
		else
			$('#confirm-delete-dialog-sale-record').modal("show");
	});
	
	$(document.body).on('click', '.confirm_delete_sale_record', function(event) 
	{
		form_updated = true;
		var del_parm 	= current_delete_node;
		kid_value_arr = del_parm.split("_");
		kid_value = kid_value_arr[0];
		var current_row = $("#" + del_parm).closest('tr');
		var callback = $("#" + del_parm).attr('data-callback');
		if(kid_value == 'NEWRECORD')
		{
			$(current_row).hide('slow');
			$(current_row).html('');
			$('#confirm-delete-dialog-sale-record').modal('hide');
			if(callback)
			executeFunctionByName(callback, window, {'callback' : true});	
		
			callback = '';
			return;
		}
		var current_btn = $(this);
		current_btn.find('i').removeClass('fa-trash-o');
		current_btn.find('i').addClass(' fa-spinner fa-spin ');
		_formurl 		= 'post_process.php';
		_formdata 		= {'mod_name' : 'sale_master_record', 'param' : del_parm};
		$.post( 
             _formurl,
             _formdata,
             function(data) 
			 {
                $('#alert_msg').html(data);
				$('#alert_msg').fadeIn( "slow");
				$('#confirm-delete-dialog-sale-record').modal('hide');
				if(data.indexOf("alert-success") > 0)
				{
					$("#alert_msg").html(data);
					$(current_row).hide('slow');					
					$(current_row).html('');
					current_btn.find('i').addClass('fa-trash-o ');
					current_btn.find('i').removeClass('fa-spinner fa-spin');
					if(callback)
					executeFunctionByName(callback, window, {'callback' : true});	
					callback = '';
				}
				else
				{
					$("#alert_msg").html(data);
					current_btn.find('i').addClass('fa-trash-o ');
					current_btn.find('i').removeClass('fa-spinner fa-spin');
				}
             }

          );
	});

	function ConfirmDelete()
	{
	  var x = confirm("Are you sure you want to delete?");
	  if (x)
	      return true;
	  else
	    return false;
	}
</script>
<?php
include ('footer.php');
?>

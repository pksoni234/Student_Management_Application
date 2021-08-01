<?php 
require_once ('dbconn.php'); 
?>
<style>
.df_container{overflow:scroll !important; overflow: auto !important;}
#inventory_batches_export_excel { float:right;}
</style>
<?php 
require_once ('header.php'); 
only_staff();

$start_date_of_arrival = $_POST['start_date_of_arrival']?$_POST['start_date_of_arrival']:'';
$end_date_of_arrival = $_POST['end_date_of_arrival']?$_POST['end_date_of_arrival']:'';

if(strlen($start_date_of_arrival) > 0) {
	$startDate = date('Y-m-d',strtotime($start_date_of_arrival));
}else {
	$startDate='';
	
} if(strlen($end_date_of_arrival) > 0) {
	$endDate = date('Y-m-d',strtotime($end_date_of_arrival)); 
} else {
	$endDate='';
}
	?>

<div id="this_form_id" class="container-fluid content_container form_max drop_shadow" >
<div class="title">Batch Number Record

<button class="btn  dt_buttons close_this" data-id="inventory_start_receiving.php" ><i class="fa fa-times fa-md"></i>&nbsp; Close </button>
<form action="inventory_batches_export_excel.php"  method="post" id="inventory_batches_export_excel" class="ae_form" data-callback="refresh_masters" role="form" data-refresh="true" target="_blank">
	<input type="hidden" name="startDate" value="<?php echo $startDate;?>" />
	<input type="hidden" name="endDate" value="<?php echo $endDate;?>" />
  	<button id="btn_export_to_excel" class="btn dt_buttons btn-primary"><i id="import_i" class="fa fa-file-excel-o fa-md"></i>&nbsp; Print To Excel </button>
</form>
</div>

<div id='alert_msg'></div>
<div class="container-fluid" id="div_lookup">
<form action="inventory_batches_receiving.php"  method="post" id="inventory_batches_receiving" class="ae_form" data-callback="refresh_masters" role="form" data-refresh="true">
	<div class="row fluid">
		<div class="col col-xs-12 form-group  ">
			<div class="col col-xs-2 form-group  ">
				<input  type="text" class="form-control datepicker" id="start_date_of_arrival"  name="start_date_of_arrival" value= "<?php echo $start_date_of_arrival; ?>" placeholder="Start Date" data-validation="" autocomplete="off"  >
			</div>
			<div class="col col-xs-2 form-group  ">
				<input  type="text" class="form-control datepicker" id="end_date_of_arrival"  name="end_date_of_arrival" value= "<?php echo $end_date_of_arrival; ?>" placeholder="End Date" data-validation="" autocomplete="off"  >
			</div>
			
			<button type="sbumit" class="btn form-btns btn-primary" id="searchfilter" title="Search" data-placement="bottom" >
				<i id="submit_check_icon" class="fa fa-search "></i>  Search  
			</button>
			
			<button type="button" class="btn form-btns btn-warning" id="clear_btn" title="Clear Data" data-placement="bottom" >
				<i id="submit_clear_icon" class="fa fa-refresh  "></i>  Clear  
			</button>
			
		</div>
	</div>
</form>



<div class="title2">
	Batch Number	
</div>
</div>
<div id="dd_lookup" class="df_container">
	<table id="tbl_lookup" class="table table-striped table-bordered datalist" width="100%" cellspacing="0">
		<thead>
			<tr>
				 <th width="15%">BATCH NUMBER</th>
				 <th>PO NUMBERS</th>
				 <th>SUPPLIER</th>
				 <th width="10%"> ARRIVAL DATE</th>
				 <th width="15%"> BATCH CREATED DATE </th>
			 </tr>
		</thead> 
		<tbody id="batchList">		
			<?php
				$query 		= "";
				$rows 		= "";
				$rowcount 	= 0;
				$retval 	= 0;
				$query 		= 	"SELECT batchNumber,GROUP_CONCAT(DISTINCT inventory_stock.po_number SEPARATOR ', ') AS po_number,genarateBatch,createdDate,date_of_arrival,batch_number_gen_date,
										GROUP_CONCAT(DISTINCT supplier_master.supplier_name SEPARATOR ', ') AS supplierName
									FROM inventory_stock 
										LEFT JOIN purchase_order_master ON purchase_order_master.po_number = inventory_stock.po_number
									  	LEFT JOIN supplier_master ON supplier_master.id = purchase_order_master.supplier_id
									WHERE genarateBatch = 1";
				if(strlen($startDate > 0 && $endDate > 0)) {
					$query.=" AND createdDate BETWEEN '$startDate' AND '$endDate'";
				}else if(strlen($startDate) > 0) {
					$query.=" AND DATE(`createdDate`) = '$startDate'";
				}
				$query.=" group by batchNumber";
				$query.=" ORDER by batch_number_gen_date DESC";
				//echo $query;
				$retval 	= 	f_select_query($query, $datarows);
				
				if ($retval == -1 ) 
				{
					$connected = false;
					return -1;
				}
			
				$rowcount =  count($datarows);
				for ($counter = 0; $counter< $rowcount; $counter++)
				{
					$batchNumber		=	 f_htmlspecialchars_decode($datarows[$counter]->batchNumber, 50);
					$po_number			=	 $datarows[$counter]->po_number;
					$createdDate		=	 truncate(f_htmlspecialchars_decode($datarows[$counter]->createdDate , ENT_QUOTES), 50);
					$createdDate		=	 db_to_date($createdDate);
					$date_of_arrival 	=	 truncate(f_htmlspecialchars_decode($datarows[$counter]->date_of_arrival , ENT_QUOTES), 50);
					$date_of_arrival 	=	 db_to_date($date_of_arrival);
					$batch_number_gen_date =  f_htmlspecialchars_decode($datarows[$counter]->batch_number_gen_date , ENT_QUOTES);
					
					if($batch_number_gen_date != 0)
					{
						$batch_time =  date("H:i:s",strtotime($batch_number_gen_date));
					}
					$kid_value			=	 string_encrypt_id('lookup_master', $datarows[$counter]->id);
					
					$row_counter = intval($counter) + 1;
					echo "<tr>";						
					echo "<td> <span class='dt_linkcol wd_datacols' id='kid_$batchNumber'> $batchNumber</span></td>";
					echo "<td>".strtoupper($po_number)."</td>";
					echo "<td>".strtoupper($datarows[$counter]->supplierName)."</td>";
					echo "<td> $date_of_arrival </td>";
					echo "<td>".db_to_date($batch_number_gen_date)."</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
<script type="text/javascript" >
$(document).ready(function() 
{
	$('.xdsoft_datetimepicker').remove();	
	setdate_format();
	
	$('#tbl_lookup').DataTable( {
		"pagingType": "full_numbers",
		"iDisplayLength": 50,
		"bPaginate": true,
		"bFilter" : true,
		"responsive" : true,
		"mark": true,
		"order": [[ 3, "desc" ]]
	});
	
	$( "#clear_btn" ).click(function() {
	 document.getElementById("inventory_batches_receiving").submit();
	});
	
	
	
	/*$(document.body).on('change', '#batchNumber', function(event) 
	{
		var date_of_arrival = $('#date_of_arrival').val();
		var batchNumber = $('#batchNumber').val();
		
		var action="getSearchWiseBatchList";
			$.ajax({
				type:"POST",
				url: "ajax.php",
				data:"date_of_arrival="+date_of_arrival+"&batchNumber="+batchNumber+"&action="+action,
				success: function(result){ 
					$('#batchList').html(result);
				}
				});
				
		
	});*/
	
	/*$(document.body).on('change', '#date_of_arrival', function(event) 
	{
		var date_of_arrival = $('#date_of_arrival').val();
			var action="getBatchNumber";
			$.ajax({
				type:"POST",
				url: "ajax.php",
				data:"date_of_arrival="+date_of_arrival+"&action="+action,
				success: function(result){
					$('#batchNumber').html(result);
					$('#tbl_lookup_info').addClass('hide');
				}
				});
				
	});*/
	$(document.body).on('click', '.dt_linkcol', function(event) 
	{
		var temp_id  = event.target.id;
		kid_value_arr = temp_id.split("_");
		kid_value = kid_value_arr[1];
		
		$.openFormPopup("inventory_batches_item_detail.php",{keyid: kid_value });


		//$.getGo("inventory_batches_item_detail.php", {
		//keyid: kid_value
		
		//});
	
	});
	
	
	
});

</script>
		
	</div>	
</div>	
<?php 
include ('footer.php'); 
?>
<?php
require_once ('dbconn.php'); 
?>
<style>
.df_container{overflow:scroll !important; overflow: auto !important;}
.modal-lg{ width:78%  !important;}
</style>

<div id="this_form_id" class="container-fluid content_container form_max" >
    <div class="title" style="color:#07BEFA;"><b>BATCH DETAILS - <?php echo $_GET["keyid"];?></b>
    <button class="btn  dt_buttons close_this" data-id="inventory_batches_receiving.php" ><i class="fa fa-times fa-md"></i><span>&nbsp; Close </span></button>
	<form action="baches_details_excel.php" method="post">
		<input type="hidden" name="batchNumber" value="<?php echo $_GET['keyid'];?>">
		<button id="btn_export_to_excel" class="btn dt_buttons btn-primary" style="margin-top:-29px;"><i id="import_i" class="fa fa-download fa-md"></i>&nbsp; Export To Excel </button>
	</form>
	<div id='alert_msg'></div>
</div>
<div id="dd_design_master" class="df_container">
	<div id="alert_msg" class="alert_msg"></div>
	<table id="tbl_design_master" class="table table-striped table-bordered datalist" width="100%" cellspacing="0">
		<thead>
				<tr>
					 <th> ItemID </th>
					 <th> Description </th>
					 <th> Quantity </th>
					 <th> Received Quantity </th>
					 <th> Unit Cost </th>
					 <th> Total Cost </th>
					 <th> Po Number </th>
					 <th> Sales Order # </th>
					 <th> Location </th>
				 </tr>
		</thead> 
		<tbody>		
			<?php
				$query 		= "";
				$rows 		= "";
				$rowcount 	= 0;
				$retval 	= 0;
				
				$keyid 		= 	f_htmlspecialchars($_GET["keyid"], ENT_QUOTES);
				
				$query = "SELECT inventory_stock.barcode_number,
								 inventory_stock.po_number,
								 inventory_stock.inventory_master_id,
								 inventory_stock.sales_id,inventory_master.item_id,
								 inventory_master.description_1,
								 inventory_master.description_2,
								 inventory_master.cost,
								 sum(purchase_order_items.quantity) AS quantity,
								 sum(purchase_order_items.received_quantity) AS received_quantity,
								 purchase_order_items.total_cost_amount,
								 purchase_order_items.recd_date,
								 inventory_stock.locationFullName,
								 sales_master.sale_number
						 FROM inventory_stock
							INNER JOIN inventory_master    ON inventory_master.id = inventory_stock.inventory_master_id
							LEFT  JOIN sales_master  	   ON sales_master.id = inventory_stock.sales_id
							LEFT JOIN purchase_order_items ON purchase_order_items.id = inventory_stock.po_detail_id 
						WHERE batchNumber = $keyid 
						GROUP BY unique_po_detail_id
						ORDER BY po_number ";	
				$retval 	= 	f_select_query($query, $datarows);
				
				if ($retval == -1 ) 
				{
					$connected = false;
					return -1;
				}
				$table_name_enc					=	 base64_encode(string_encrypt_key("design_master"));
				
				$rowcount =  count($datarows);
				for ($counter = 0; $counter< $rowcount; $counter++)
				{
					$lint_item_id			=	 f_htmlspecialchars_decode($datarows[$counter]->item_id , ENT_QUOTES);
					$lstr_description_1		=	 f_htmlspecialchars_decode($datarows[$counter]->description_1 , ENT_QUOTES);
					$lstr_description_2		=	 f_htmlspecialchars_decode($datarows[$counter]->description_2 , ENT_QUOTES);
					$lstr_descrition		= 	 $lstr_description_1. ' '.$lstr_description_2;
					$lstr_cost				=	 f_htmlspecialchars_decode($datarows[$counter]->cost , ENT_QUOTES);
					$lstr_total_cost_amount	=	 f_htmlspecialchars_decode($datarows[$counter]->total_cost_amount , ENT_QUOTES);
					$lstr_quantity			=	 f_htmlspecialchars_decode($datarows[$counter]->quantity , ENT_QUOTES);
					$lstr_received_quantity	=	 f_htmlspecialchars_decode($datarows[$counter]->received_quantity , ENT_QUOTES);
					$lstr_po_number			=	 f_htmlspecialchars_decode($datarows[$counter]->po_number , ENT_QUOTES);
					$lstr_barcode_number	=	 f_htmlspecialchars_decode($datarows[$counter]->barcode_number , ENT_QUOTES);
					$lstr_locationFullName	=	 f_htmlspecialchars_decode($datarows[$counter]->locationFullName , ENT_QUOTES);
					$lstr_sale_number		=	 f_htmlspecialchars_decode($datarows[$counter]->sale_number , ENT_QUOTES);
				
					$row_counter = intval($counter) + 1;
					echo "<tr ";
					if($_GET['keyid'] == $kid_value){
						echo "class ='selectedrow' ";
					}
					echo ">";				
					echo "<td>$lint_item_id </td>";
					echo "<td>$lstr_descrition</td>";
					echo "<td>$lstr_quantity </td>";
					echo "<td>$lstr_received_quantity</td>";
					echo "<td>$lstr_cost </td>";
					echo "<td>$lstr_total_cost_amount </td>";
					echo "<td>$lstr_po_number </td>";
					echo "<td>$lstr_sale_number </td>";
					echo "<td>$lstr_locationFullName </td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>	
 
<script type="text/javascript" >
$(document).ready(function() 
{
	
	var var_datatable  = $('#tbl_design_master').dataTable({
		"pagingType": "full_numbers",
		"iDisplayLength": 50,
		"bPaginate": true,
		"bFilter" : true,
		"responsive" : true,
		"aLengthMenu": [[10, 15, 25, 35, 50,  -1], [10, 15, 25, 35, 50, 'All']],
		"responsive": false,
		"language": {
			"emptyTable": "<span class='text-danger'><b>No Records Found. </b>  </span>"
		}
	});
});

</script>

<?php include_once ('footer.php'); ?>
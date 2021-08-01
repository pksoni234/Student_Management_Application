<?php
require_once ('dbconn.php'); 
global $conn, $db_error;
include_once('header.php');
$location_dropdown	= f_get_dropdown("id", "fullLocationName", "aisle_level_bay", $lint_bill_to, "location_id", " type = 'RECEIVING'","Select");
$searchByPoSupplier = 'po_number';

$date_of_arrival 	  = date('m/d/Y');
$temp_date_of_arrival = date('Y-m-d', strtotime($date_of_arrival . ' +1 day'));
$date_of_arrival	  = date('Y-m-d H:i:s', strtotime($date_of_arrival . ' +1 day'));
$date_of_arrival 	  = date('m/d/Y',strtotime($date_of_arrival));
$currentDay 		  =  date("D", strtotime($date_of_arrival));
if($currentDay == 'Sun')
{
	$temp_date_of_arrival = date('Y-m-d', strtotime($date_of_arrival . ' +1 day'));
	$date_of_arrival  	  = date('Y-m-d H:i:s', strtotime($date_of_arrival . ' +1 day'));
	$date_of_arrival 	  = date('m/d/Y',strtotime($date_of_arrival));
}
?>

<style>
	#posupplierlist_body{position:absolute;margin-top:70px;max-height:350px;overflow-y:scroll;overflow-x:hidden;}
	td{padding:2px 7px 2px 5px !important; border-top:none !important;}
	.table > thead > tr > th { border:none;padding:0px 0px 0px 5px !important;border-right:1px solid #dddddd;}
	#receiving_inventory thead { background:#fff; color:#2196f3; font-weight:500;font-size: 14px;}
	form.ae_form .form-control{ height:27px;} 
	.table-striped input[type="checkbox"]:after{margin-right: 0px; border: 1px solid #aeaeae; width:14px; height:14px;}
	.table-striped input[type="checkbox"]:checked:after{background:green; border-color:green;}
	.table-striped input[type="checkbox"]:checked:before{top: -1px; left: 5px; width: 4px; height: 10px;}
	.visible {display: block; }
	input[type="checkbox"]:after, .checkbox input[type="checkbox"]:after, .checkbox-inline input[type="checkbox"]:after{margin-right:0px;}
</style>

<script type="text/javascript">
	$(document).ready(function () {
	$('.chkAllPrepareToReceive').iCheck('destroy');
	$('.chkAllScanPreReceived').iCheck('destroy');
	$('.chkAllScanNotPreReceived').iCheck('destroy');
	});
</script>

<div id="frm_inventory_master" class="container-fluid content_container">
	<div class="title">PreReceive 
		<span id='error_msg' style="color:#FF0000; font-weight:bold;"></span>
		<button class="btn dt_buttons close_this" data-id="inventory_management.php" ><i class="fa fa-times fa-md"></i><span>&nbsp; Close </span></button>
		<button id="data_batches_received" class="btn dt_buttons btn-primary"><i id="submit_check_icon" class="fa fa-send "></i><span>&nbsp; Batches Received </span></button>
		<button type="button" class="btn dt_buttons form-btns btn-info hide" id="btn_remove_all_pre_receive" data-alert="message" data-refresh="true" title="REMOVE PRE RECEIVED ITEMS" data-placement="bottom" style="z-index:1;" onclick="fun_remove_all_pre_receive_Items();" >
			<i id="submit_check_icon" class="fa fa-times fa-md"></i><span> &nbsp; Remove Pre Received Items</span>  
		</button>
		<button type="button" class="btn dt_buttons form-btns btn-primary hide" id="btn_receive_all" data-alert="message" data-refresh="true" title="RECEIVE ITEMS" data-placement="bottom" style="z-index:1;" onclick="fun_receiveAllItems();">
			<i id="submit_check_icon" class="fa fa-get-pocket fa-md"></i><span> &nbsp; Receive Items</span>  
		</button>
		<button type="button" class="btn dt_buttons form-btns btn-success hide" id="pre_receive_table_btn" data-alert="message" data-refresh="true" title="PRE RECEIVE ITEMS" data-placement="bottom" style="z-index:1;" >
			<i id="submit_check_icon" class="fa fa-level-down fa-md"></i><span>&nbsp; Pre Receive</span>  
		</button>
		<label id='alert_msg' style="color:#179017;font-weight:bold;width:34%;"></label>
	</div>
	<div class="container-fluid" id="div_start_receiving">
		<!--  SEARCH AREA DIV [START] -->
		<div class="row fluid">
			<div class="col col-xs-12 col-md-2 form-group">
				<input type="radio" name="searchByPoSupplier" class="rd_option" id="searchByPo" value="po_number" <?php if($searchByPoSupplier=='po_number') { ?> checked="checked"<?php } ?>  />
				<label class="control-label" for="supplier_name" style="width:87%"> Open PO's</label>
				<br />
				<input type="radio" name="searchByPoSupplier" class="rd_option" id="searchBySupplier"   value="supplier_name" <?php if($searchByPoSupplier=='supplier_name') { ?> checked="checked"<?php } ?> /> 
				<label class="control-label" for="supplier_name" style="width:87%">Supplier</label>
				<br />
				<input type="radio" name="searchByPoSupplier" class="rd_option" id="rd_preReceived" value="preReceived/ASN" <?php if($searchByPoSupplier=='preReceived/ASN') { ?> checked="checked"<?php } ?> /> 
				<label class="control-label" for="supplier_name" style="width:87%">PreReceived/ASN</label>
			</div>
			<div class="col col-xs-12 col-md-3 group_columns form-group">
				<div class="col col-xs-12 col-md-12 group_columns form-group">
					<label class="control-label" for="vendor">enter PO # | Supplier Name</label>
					<input  type="text" class="form-control"  id="poSupplierBySearch"  name="poSupplierBySearch" value= "" placeholder="Enter PO # | Supplier Name" data-validation="" onkeyup="fun_searchPoSupplierList(this.value);" style="width:98%;text-transform:uppercase;"  autofocus>
				</div>
				<div class="col col-xs-12 col-md-12 group_columns form-group" id="posupplierlist_body">	
				</div>
			</div>
			<?php /*?><div id="searcharea">
				<div class="table-responsive" id="searcharea2"  style="height: 220px;overflow-y: auto;position: absolute;z-index: 92;top: 160px; width:35%; margin-left:13%;">
					<table id="itemlist-grid" class="table table-striped" style="" width="20%" cellspacing="0">
						<tbody id="posupplierlist_body" >	
							<tr><td align="center" style="display:none;"></td></tr>
						</tbody>
					</table>
				</div>
			</div><?php */?>
			<div class="col col-xs-12 col-md-3 group_columns_last form-group ">
				<label class="control-label" for="email_address"># Of Items To Be Received</label>
				<input  type="text" class="form-control"  id="listOfItemReceived"  name="listOfItemReceived" value= "<?php //echo $lstr_email_address; ?>" placeholder="# Of Items To Be Received" data-validation="" readonly style="width:98%;" >
			</div>
			<div class="col col-xs-12 col-md-2 group_columns form-group ">
				<label class="control-label" for="date_of_arrival">Date Of Arrival</label>
				<input  type="text" class="form-control datepicker"  id="date_of_arrival"  name="date_of_arrival" value= "<?php echo $date_of_arrival; ?>" placeholder="Date Of Arrival" data-validation=""  >
			</div>
			<div class="col col-xs-12 col-md-2 group_columns form-group ">
				<label class="control-label" for="status">Location</label>
				<?php echo $location_dropdown; ?>
			</div>
		</div>
		<!--  SEARCH AREA DIV [END] -->
		
		<!--  SEARCH AREA DATA [START] -->
		<form action="inventory_start_receiving.php"  method="post" id="inventory_start_receiving" class="ae_form" role="form">
			<input type="hidden" name="action" value="saveStartReceivingData" />
			<input type="hidden" name="dateOfArrival" id="dateOfArrival" value="<?php echo $temp_date_of_arrival;?>" />
			<input type="hidden" name="receiving_location" id="receiving_location" value="" />
			<table id="prepare_to_receiv_area_table" class="table table-striped table-bordered datalist" width="100%" cellspacing="0">
				<thead>
					<tr>
						 <th style="line-height:35px; width:5%;" align="center"><input type="checkbox" name="chkPrepareToReceive" id="chkPrepareToReceive" class="chkAllPrepareToReceive" /></th>
						 <th style="line-height:35px; width:10%;" align="center"> Purchase Order # </th>
						 <th style="line-height:35px; width:8%;" align="center"> Item ID</th>
						 <th style="line-height:35px; width:13%;" align="center"> Item Barcode</th>	
						 <th style="line-height:35px; width:15%;" align="center"> Desc-A</th>
						 <th style="line-height:35px; width:7%;" align="center"> Sale Order #</th>					 
						 <th style="line-height:35px; width:10%;" align="center"> Supplier </th>
						 <th style="line-height:35px; width:2%;" align="center"> Qty. </th>
						 <th style="line-height:35px; width:7%;" align="center"> Carton Count </th>
						 <th style="line-height:35px; width:6%;" align="center"> PO Date </th>
						 <th style="line-height:35px; width:7%;" align="center"> Date of Arrival </th>
						 <th style="line-height:35px; width:7%;" align="center"> Location </th>
					 </tr>
				</thead> 
				<tbody>	
					<tbody id="prepare_to_receiv_area" >	
					</tbody>
					<tbody id="loadingDiv" class="hide">
						<tr>
							<td colspan="11" align="center">
								<img src="images/loader.gif"><br><span>Loading Content. Please wait..</span>	
							</td>
						</tr>
					</tbody>
				</tbody>
			</table>
		</form>
		<!--  SEARCH AREA DATA [END] -->
	</div>
</div>

<script type="text/javascript">
	
/*window.setInterval(function(){
	var date_of_arrival_prereceive = $('#date_of_arrival_prereceive').val();
	if(date_of_arrival_prereceive != '')
	{
  		$('#start_receiving_btn').click();
	}
}, 1000);*/


$(document).ready(function (){
	$(document.body).on('click', '#start_receiving_btn', function(event) 
	{
		var action = 'getItemsScannedPreReceived';
		var date_of_arrival_prereceive = $('#date_of_arrival_prereceive').val();
		$.ajax({
		type:"POST",
		url: "inventory_item_scan_pre_receiving.php",
		data:"date_of_arrival_prereceive="+date_of_arrival_prereceive+"&action="+action,
		success: function(result){ 
				$('#items_scanned_prereceived_lable').removeClass('hide');
				$('#ItemsScannedPreReceived').html(result);
				if(result == '') {
					$("#receiv_generate_batchn_btn").addClass('hide');
				}else {
					$("#receiv_generate_batchn_btn").removeClass('hide');
				}
				var checkAllScanItem=$('input:checkbox[name=chkAllScanPreRec]').is(':checked');
				if(checkAllScanItem == true)
				{
					$(".chkScanPreRec").prop('checked', true);
				}
			}
		});
		
		var action = 'getItemsScannedButNotPreReceived';
		$.ajax({
		type:"POST",
		url: "inventory_item_scan_pre_receiving.php",
		data:"date_of_arrival_prereceive="+date_of_arrival_prereceive+"&action="+action,
		success: function(result){ 
				$('#items_scanned_but_not_prereceived_label').removeClass('hide');
				$('#items_scanned_but_not_prereceived_table').removeClass('hide');
				$('#items_scanned_but_not_prereceived').html(result);
				var checkAllScanNotPre=$('input:checkbox[name=chkAllScanNotPreRec]').is(':checked');
				if(checkAllScanNotPre == true)
				{
					$(".chkScanNotPreRec").prop('checked', true);
				}
			}
		});
		
	});
	
	$('.xdsoft_datetimepicker').remove();	
	setdate_format();
 
	$(document.body).on('input', ".pretorec", function (event){
		fun_cntTotalChecked();
	});
	
	$(document.body).on('click', '.chkAllScanPreReceived', function(event) 
	{
		var checkAllScanItem=$('input:checkbox[name=chkAllScanPreRec]').is(':checked');
		if(checkAllScanItem == true)
		{
			$(".chkScanPreRec").prop('checked', true);
		}else {
			$(".chkScanPreRec").prop('checked', true);
		}
	});
	
	$(document.body).on('click', '.chkAllScanNotPreReceived', function(event) 
	{
		var checkAllScanNotPre=$('input:checkbox[name=chkAllScanNotPreRec]').is(':checked');
		if(checkAllScanNotPre == true)
		{
			$(".chkScanNotPreRec").prop('checked', true);
		}else {
			$(".chkScanNotPreRec").prop('checked', true);
		}
	});
	
	$(document.body).on('click', '#receiv_generate_batchn_btn', function(event) 
	{
		var arrival_date = $('#date_of_arrival_prereceive').val();
		var action="updateItemGenerateBatchNumber";
		$.ajax({
			type:"POST",
			url: "ajax.php",
			data:"arrival_date="+arrival_date+"&action="+action,
			success: function(result){
				$("#alert_msg").html("<div class='alert alert-success'>" + result + "</div>");
				$("#receiv_generate_batchn_btn").removeClass('hide');
			}
		});
	});
	
	$(document.body).on('change', '#chkPrepareToReceive', function(event) 
	{
		var checkAllPrepare=$('input:checkbox[name=chkPrepareToReceive]').is(':checked');
		if(checkAllPrepare == true)
		{
			$(".pretorec").prop('checked',true);
			$(".parentPO").prop('checked',true);
		}else{
			$(".pretorec").prop('checked',false);
			$(".parentPO").prop('checked',false);
		}
		fun_cntTotalChecked();
	});
});
		
$('.rd_option').on('ifChanged', function (event) { 
	var seach_val = $("input[name='searchByPoSupplier']:checked"). val();
	if(seach_val == 'preReceived/ASN')
	{
		var searchByPoSupplier = $('input[name=searchByPoSupplier]:checked').val();  
		var location_id	 	   = $('#location_id').val();  
		var dateOfArrival 	   = $('#date_of_arrival').val();  
		var action			   = "getPreReceived_ASNData";
		$.ajax({
			type:"POST",
			url: "inventory_start_receiving_action.php",
			data:"searchByPoSupplier="+searchByPoSupplier+"&location_id="+location_id+"&action="+action+"&dateOfArrival="+dateOfArrival,
			success: function(result){ 
				$("#prepare_to_receiv_area").html(result);
				$('#pre_receive_table_btn').addClass('hide');
				$('#btn_receive_all').addClass('hide');
				$('#btn_remove_all_pre_receive').addClass('hide');
			}
		});
	} else{
		$("#prepare_to_receiv_area").html('');
		$("#btn_remove_all_pre_receive").addClass('hide');
		$('#chkPrepareToReceive').removeAttr('checked','checked');
		$('#chkPrepareToReceive').change();
		fun_clearData();
	}
});
		
	function fun_clearData()
	{
		$("#poSupplierBySearch").val('');
		$("#posupplierlist_body").html('');
		$("#listOfItemReceived").val(0);
	}	
	function fun_searchPoSupplierList(key)
	{
		var searchByPoSupplier = $('input[name=searchByPoSupplier]:checked').val(); 
		var action="getPoOrSupplierList";
		$.ajax({
		type:"POST",
		url: "inventory_start_receiving_action.php",
		data:"searchKey="+key+"&action="+action+"&searchByPoSupplier="+searchByPoSupplier,
		success: function(result){
				$('#posupplierlist_body').html(result);
			}
		});
	}
	
	function fun_add_PoSupplier_Item(po_number)
	{	
		var poSupplierBySearch = po_number; 
		var searchByPoSupplier = $('input[name=searchByPoSupplier]:checked').val();  
		var location_id	 	   = $('#location_id').val();  
		var dateOfArrival 	   = $('#date_of_arrival').val();  
		var action			   = "getStartReceivingData";
		$.ajax({
			type:"POST",
			url: "inventory_start_receiving_action.php",
			data:"searchByPoSupplier="+searchByPoSupplier+"&poSupplierBySearch="+escape(poSupplierBySearch)+"&location_id="+location_id+"&action="+action+"&dateOfArrival="+dateOfArrival,
			success: function(result){ 
				$("[id='cb_"+po_number+"']").html('<input type="checkbox" onclick="fun_remove_PoSupplier_Item(\''+po_number+'\')" checked>');
				$("#prepare_to_receiv_area").append(result);
			}
		});
	}
	
	function fun_remove_PoSupplier_Item(po_number)
	{	
		var poSupplierBySearch = po_number; 
		var searchByPoSupplier = $('input[name=searchByPoSupplier]:checked').val();  
		var location_id 	   = $('#location_id').val();
		var dateOfArrival 	   = $('#date_of_arrival').val();   
		var action			   = "getStartReceivingData";
		$.ajax({
			type:"POST",
			url: "inventory_start_receiving_action.php",
			data:"searchByPoSupplier="+searchByPoSupplier+"&poSupplierBySearch="+poSupplierBySearch+"&location_id="+location_id+"&action="+action+"&dateOfArrival="+dateOfArrival,
			success: function(result){ 
				$("[id='cb_"+po_number+"']").html('<input type="checkbox" name="chkReceive[]" value='+po_number+' onclick="fun_add_PoSupplier_Item(\''+po_number+'\')">');
				$("[class='pr_"+po_number+"']").remove();
			}
		});
	}
	
	$(document).mouseup(function (e){
		var container = $("#poSupplierBySearch,#posupplierlist_body");
		if (!container.is(e.target) && container.has(e.target).length === 0){
			$("#posupplierlist_body").addClass('hide');
		}else {
			$("#posupplierlist_body").removeClass('hide');
		}
	}); 

	$(document.body).on('click', '#data_batches_received', function(event) 
	{
		$.getGo("inventory_batches_receiving.php");
	});

	
	
	$(document.body).on('change', '#location_id', function(event) 
	{
		var locationID = $('#location_id').val();
		$('#receiving_location').val(locationID);
	});
	
	$(document.body).on('click', '#pre_receive_table_btn', function(event) 
	{
		fun_getAllPreReceivedData();
	});

	function fun_getAllPreReceivedData()
	{
	
		location_id = $("#location_id").val();
		var locationID = $('#location_id option:selected').text();
		if(locationID.trim() != 'Select') {
		
		$(".pretorec:checked").each(function () {
			var id = $(this).val();
			$('#recevinglocation_'+id).html(locationID);
		});
		}
					   
		if(location_id==''){$("#location_id").addClass("form_error");}
		if(location_id!=''){$("#location_id").removeClass("form_error");}
		if(location_id !='')
		{
			var form_data = $("#inventory_start_receiving").serialize();
			$("#prepare_to_receiv_area").addClass('hide');
			$("#loadingDiv").removeClass('hide');
			$.ajax({
				type:"POST",
				url: "inventory_start_receiving_action.php",
				data:form_data,
				success: function(result){ 
					$('#alert_msg').html(result);
					$("#prepare_to_receiv_area").removeClass('hide');
					$("#loadingDiv").addClass('hide');
				}
			});
		}
	}
	
	function fun_receiveAllItems()
	{
		var totalCnt  = $('.pretorec:checked').size();
		if(totalCnt == 0)
		{
			$.alert('Please, Select atleast one checkbox.');
		}else{
			var form_data = $("#inventory_start_receiving").serialize();
			$("#prepare_to_receiv_area").addClass('hide');
			$("#loadingDiv").removeClass('hide');
			$.ajax({
				type:"POST",
				url: "inventory_receive_all_items.php",
				data:form_data,
				success: function(result){ 
						$("#prepare_to_receiv_area").removeClass('hide');
						$("#loadingDiv").addClass('hide');
						$('#alert_msg').html(result);
					}
				});
		}	
	}
	
	function fun_remove_all_pre_receive_Items()
	{
		var form_data = $("#inventory_start_receiving").serialize();
		$.ajax({
			type:"POST",
			url: "inventory_remove_all_receiving_data_action.php",
			data:form_data,
			success: function(result){ 
				$('#alert_msg').html(result);
			}
		});
		var searchByPoSupplier = $('input[name=searchByPoSupplier]:checked').val();  
		var location_id	 	   = $('#location_id').val();  
		var dateOfArrival 	   = $('#date_of_arrival').val();  
		var action			   = "getPreReceived_ASNData";
		$.ajax({
			type:"POST",
			url: "inventory_start_receiving_action.php",
			data:"searchByPoSupplier="+searchByPoSupplier+"&location_id="+location_id+"&action="+action+"&dateOfArrival="+dateOfArrival,
			success: function(result){ 
				$("#prepare_to_receiv_area").html(result);
				$('#pre_receive_table_btn').addClass('hide');
				$('#btn_receive_all').removeClass('hide');
				$('#btn_remove_all_pre_receive').removeClass('hide');
				fun_total_piceseCount();
			}
		});
	}
	
	function fun_total_piceseCount()
	{
		var totalPieceCount  = 0;
		$.each($('.pieceCount'), function (index, value)
		   	{
				pieceCount = $(this).text();
				totalPieceCount =  parseInt(totalPieceCount) + parseInt(pieceCount); 
		   });
		$("#listOfItemReceived").val(totalPieceCount);
		$('#pre_receive_table_btn').removeClass('hide');
		$('#btn_receive_all').removeClass('hide'); 
	}
	
	function fun_cntTotalChecked()
	{
		var totalCnt  = $('.pretorec:checked').size();
		var seach_val = $("input[name='searchByPoSupplier']:checked"). val();
		if(parseInt(totalCnt) > 0)
		{
			if(seach_val == 'preReceived/ASN')
			{
				$('#btn_remove_all_pre_receive').removeClass('hide');
				$('#pre_receive_table_btn').addClass('hide');
				$('#btn_receive_all').removeClass('hide');
			}else{
				$('#btn_remove_all_pre_receive').addClass('hide');
				$('#pre_receive_table_btn').removeClass('hide');
				$('#btn_receive_all').removeClass('hide');
			}	
		}else{
			$('#pre_receive_table_btn').addClass('hide');
			$('#btn_receive_all').addClass('hide');
			$('#btn_remove_all_pre_receive').addClass('hide');
		}
		$('#listOfItemReceived').val(parseInt(totalCnt));
	}
	
	function fun_checkAllChild(poNumber)
	{	
		var isParentPoChecked = $('#parentPO_'+poNumber).is(':checked');
		if(isParentPoChecked == true)
		{
			$(".childPo_"+poNumber).prop('checked',true);
		}else{
			$(".childPo_"+poNumber).prop('checked',false);
		}
		fun_cntTotalChecked();
	}
	
</script>

<?php include_once('footer.php'); ?>
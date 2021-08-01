<?php
include_once('dbconn.php');

$keyid	= $_GET['keyid'];

$query 		= 	"SELECT merchandise_transfers_history.*,
						inventory_stock.inventory_master_id,
						inventory_master.item_id,
						inventory_master.description_1,
						inventory_master.description_2
				FROM merchandise_transfers_history
						INNER JOIN inventory_stock ON merchandise_transfers_history.inventory_stock_id = inventory_stock.id 
						INNER JOIN inventory_master ON inventory_stock.inventory_master_id = inventory_master.id
				WHERE merchandise_transfers_history.flag = 'MT' AND mtn = $keyid";

$retval   =  f_select_query($query, $datarows);
$rowcount =  count($datarows);
$itnTableData = '';
if($rowcount > 0)
{
	for ($counter = 0; $counter< $rowcount; $counter++)
	{
		$itnTableData .='<tr>';
			$itnTableData .='<td>'.$datarows[$counter]->item_id.'</td>';
			$itnTableData .='<td>'.f_htmlspecialchars_decode($datarows[$counter]->description_1 , ENT_QUOTES)." - ".f_htmlspecialchars_decode($datarows[$counter]->description_2,ENT_QUOTES).'</td>';
			$itnTableData .='<td>'.$datarows[$counter]->currentLocation.'</td>';
			$itnTableData .='<td>'.$datarows[$counter]->transferLocation.'</td>';
		$itnTableData .='</tr>';
	}
}else{
	$itnTableData .='<tr colspan="4">';
		$itnTableData .='<td>No Records Found !</td>';
	$itnTableData .='</tr>';	
}	

# ITN NUMBER DATA
$query 	  =  "SELECT * FROM itn_number WHERE itn_number = '$keyid'";
$retval   =  f_select_query($query, $datarows);
$rowcount =  count($datarows);	
if($rowcount > 0)
{		
	$itn_date 	=  date('m/d/Y h:i:s A', strtotime($datarows[0]->itn_date));		
	$itn_by   	=  strtoupper($datarows[0]->itn_by); 
	$itn_number =  $datarows[0]->itn_number; 
	$sign    	=  $datarows[0]->sign; 
	$is_agree   =  $datarows[0]->is_agree; 
}else{
	$itn_date 	=  '';		
	$itn_by   	=  ''; 
	$itn_number =  $keyid; 
	$sign    	=  ''; 
	$is_agree	=  0;
}
		
if($is_agree == 1){$chkImg = "images/check_mark.jpg";}
else{$chkImg = "images/invoice_cb.png";}
		
$template   = '';
$template  .= '<html>
					<head></head>
					<body>
						<table class="items" width="100%" style="border-collapse:collapse;margin-top:2px;" cellpadding="8" >
							<tr>
								<td height="1" style="background:#2196f3;text-align:center;color:#fff;font-weight:bold;font-size:18px;">
									ITN - '.$itn_number.'
								</td>
							</tr>
						</table>
						<table class="items" width="100%" style="border-collapse:collapse;margin-top:2px;" cellpadding="8" >
							<thead>
								<tr>
									<td width="20%" style="text-align:left;font-size: 10pt;font-weight: 700;vertical-align:middle;"><strong>ITEM ID</strong></td>
									<td width="40%" style="text-align:left;font-size: 10pt;font-weight: 700;vertical-align:middle;"><strong>DESCRIPTION</strong></td>
									<td width="20%" style="text-align:left;font-size: 10pt;font-weight: 700;vertical-align:middle;"><strong>FROM LOCATION</strong></td>
									<td width="20%" style="text-align:left;font-size: 10pt;font-weight: 700;vertical-align:middle;"><strong>TO LOCATION</strong></td>
								</tr>
							</thead>
							<tbody>
								<tr><td height="1" style="background:#282228;" colspan="6"></td></tr>
								#itnTableData#
							</tbody>
						</table>
						<table class="items" width="100%" style="border-collapse:collapse;margin-top:2px;" cellpadding="8">
							<tr>
								<td>
									<img src="'.$chkImg.'" style="width: 15px;height: 16px;">
									&nbsp;&nbsp;I have received all of the items above in good condition.
								</td>
							</tr>
						</table>
						<table class="items" width="100%" style="border-collapse:collapse;margin-top:2px;" cellpadding="8">
							<tr><td><b><u>SIGNATURE</u></b></td></tr>
							<tr><td><img src="'.$sign.'"></td></tr>
							<tr><td><b>DATE & TIME :&nbsp;</b> '.$itn_date.'</td></tr>
							<tr><td><b>SIGNED BY &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</b>'.$itn_by.'</td></tr>
						</table>
					</body>	
				</html>';
												 
f_replace($template, '#itnTableData#', $itnTableData);
	
define('_MPDF_PATH','./mpdf/');
include("./mpdf/mpdf.php");

$mpdf=new mPDF('c','A4','','',5,5,10,0,0,10);  
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("ITN - ".$keyid);
$mpdf->SetAuthor("ITN - ".$keyid);
$mpdf->showWatermarkText = true;
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($template);
$mpdf->Output();
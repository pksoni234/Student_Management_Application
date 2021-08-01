<?php 
require_once ('dbconn.php'); 

$batchNumber = $_POST['batchNumber']?$_POST['batchNumber']:'';

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

require_once dirname(__FILE__) . '../PHPExcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'BATCH DETAILS - '.$batchNumber);

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A3', 'Item ID')
			->setCellValue('B3', 'Description')
			->setCellValue('C3', 'Quantity')
			->setCellValue('D3', 'Received Quantity')
			->setCellValue('E3', 'Unit Cost($)')
			->setCellValue('F3', 'Total Cost($)')
			->setCellValue('G3', 'Po Number')
			->setCellValue('H3', 'Sale Order#')
			->setCellValue('I3', 'Location');
	
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
						WHERE batchNumber = $batchNumber 
						GROUP BY unique_po_detail_id
						ORDER BY po_number ";
			$retval 		= 	f_select_query($query, $datarows);
			$rowcountBaches =   count($datarows);
			for ($counter = 0; $counter < $rowcountBaches; $counter++)
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
					
				$row_counter = intval($counter) + 4;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$row_counter, $lint_item_id)
					->setCellValue('B'.$row_counter, $lstr_descrition)
					->setCellValue('C'.$row_counter, $lstr_quantity)
					->setCellValue('D'.$row_counter, $lstr_received_quantity)
					->setCellValue('E'.$row_counter, $lstr_cost)
					->setCellValue('F'.$row_counter, $lstr_total_cost_amount)
					->setCellValue('G'.$row_counter, $lstr_po_number)
					->setCellValue('H'.$row_counter, $lstr_sale_number)
					->setCellValue('I'.$row_counter, $lstr_locationFullName);
			}
	
	$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
	$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DCDCDC');
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	
	$objPHPExcel->getActiveSheet()->getStyle('A3:'.'J3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
	$objPHPExcel->getActiveSheet()->getStyle('A3:'.'J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		
	$objPHPExcel->getActiveSheet()->setTitle('BATCH DETAIL - '.$batchNumber);
	$objPHPExcel->setActiveSheetIndex(0);
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="BatchesDetailExcelsheet.xlsx"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
?>
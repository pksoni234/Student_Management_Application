<?php
require_once ('db_settings.php');
if(!(isset($_SESSION['user_id'])))
{
	header("Location: login.php");
}

//latest activity
// $_SESSION['last_action'] = time();

require_once ('head.php');
require_once ('sidebar.php');
?>
<style>
.alert-success{background-color:#FFFFFF;color:#179017}
#alert_msg{display: inline-block;font-size: 12px;margin: 0;}
.editable_table1 tbody > tr > td {padding: 4px !important;}
.table.dataTable.table-striped > tbody > tr.selectedrow.even{ background: rgb(255, 255, 184) none repeat scroll 0% 0%; color: rgb(0, 0, 0);} 
.table.dataTable.table-striped > tbody > tr:nth-of-type(2n+1).selectedrow.odd{ background: rgb(255, 255, 184) none repeat scroll 0% 0%; color: rgb(0, 0, 0);} 
::-webkit-scrollbar {width: 5px;}
::-webkit-scrollbar-track {background: #f1f1f1; }
::-webkit-scrollbar-thumb { background: #c1c1c1;}
::-webkit-scrollbar-thumb:hover {background: #c1c1c1;}
</style>
<div id="page-content-wrapper" class="container-fluid clearfix">  


<?php 
require_once ('header.php'); 

//print_r($_SESSION);
//exit;
if($_SESSION['auth_type'] == 'ADMIN' ) //ADMIN 
{
	require_once ('myaccount.php'); 
}


<?php
require_once ('db_settings.php');
session_destroy();
//header('Location: index.php');
header('Location: login.php');
exit();
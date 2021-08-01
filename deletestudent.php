<?php
require_once ('db_settings.php');
global $conn, $db_error;
if (isset($_GET["keyid"]))
{
$keyid = $_GET["keyid"];    
if($keyid == "")
{
$keyid = 0;    
}
$query = "DELETE FROM pra_students WHERE studentID = $keyid";
                
                if ($conn->query($query) === TRUE) {
                  $errText .= '* Student added successfully <br>';
                } else {
                  $errText .= "* Error";
                }

                header('Location: student.php?msg=success');
                exit();         
}
header('Location: student.php?msg=success');
exit;
?>

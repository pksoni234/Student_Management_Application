<div id="togglemenu" class="toggle_menu" ><i class="fa fa-bars fa-lg menu_toggle_icon"></i></div>
<div id="menu_overlay_section"></div>
<?php
$username = $_SESSION['user_name'];
$slide_toggle = '';
/************************ ADMIN [START] ********************************/
if ($_SESSION["auth_type"] == "ADMIN")
{
$menu = ' 
<div id="sidebar-wrapper">
	<div class="align_center sidebar_logo"><img src="images/logo.png" class="logo2" /></div>
	<ul class="sidebar-nav">
		<li><a class="wow flipInX " href="student.php"><i class="fa fa-database fa-lg "></i> Student</a></li>
		<li><a class="wow flipInX " href="bulkstudentupload.php"><i class="fa fa-database fa-lg "></i>Bulk Student Upload</a></li>
		<li class="wow flipInX logout "><a href="logout.php"><i class="fa fa-power-off fa-lg "></i> Log Out</a></li>
	</ul>
</div>'; 
};
/************************ ADMIN[END] ********************************/
echo $menu;


?>

<script type="text/javascript" >
	function togglemenu() {
		if ($("#togglemenu").is(":visible"))
		{
			$("#sidebar-wrapper").css('width','0');
			$("#menu_overlay_section").hide();		
		}
		else
		{
			$("#sidebar-wrapper").animate({width:"250"},0); 
			$("#menu_overlay_section").show();
		}
	};

	var resizeTimer;
	$(window).resize(function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(togglemenu, 20);
	});
</script>
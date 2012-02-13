<?
		if ($here == "")
		{
?>
<div id="div2" class=div_mainpage>
	<?  include 'main_page_customization.php'; ?>
</div>
<?
		}	//end $here == ""
		foreach ($classes as $class_key => $class_value)
		{
			if($here == $class_value)
			{
?>

<div id="div2" class=div_mainpage>

<? 

// let's create a context sensitive sub menu for each class of the system

				if ($mode == "" || $mode == "confirm_create" || $mode == "confirm_update" || $mode == "confirm_search")
				{
					echo "<p class='p1'>".$class_value.": <a href='". $current_file_name."?here=".$class_value."&mode=create&class_obj=".$class_value."'>create new ".$class_value."</a> ¦ <a href='". $current_file_name."?here=".$class_value."&mode=search&class_obj=".$class_value."'>search ".$class_value."</a> ¦ Help</p>";
				}
				else if ($mode == "create")
				{
					echo "<p class='p1'><a href='". $current_file_name."?here=".$class_value."&class_obj=".$class_value."'>".$class_value."</a>: create new ".$class_value." ¦ <a href='". $current_file_name."?here=".$class_value."&mode=search&class_obj=".$class_value."'>search ".$class_value."</a> ¦ Help</p>";
				}
				else if ($mode == "search")
				{
					echo "<p class='p1'><a href='". $current_file_name."?here=".$class_value."&class_obj=".$class_value."'>".$class_value."</a>: <a href='". $current_file_name."?here=".$class_value."&mode=create&class_obj=".$class_value."'>create new ".$class_value."</a> ¦ search ".$class_value." ¦ Help</p>";
				}
				else if ($mode == "update")
				{
					echo "<p class='p1'><a href='". $current_file_name."?here=".$class_value."&class_obj=".$class_value."'>".$class_value."</a>: <a href='". $current_file_name."?here=".$class_value."&mode=create&class_obj=".$class_value."'>create new ".$class_value."</a> ¦ <a href='". $current_file_name."?here=".$class_value."&mode=search&class_obj=".$class_value."'>search ".$class_value."</a> ¦ Help</p>";
				}
			}
		}
	

	
	
?>	

<!--<div id=div_menu>-->
<ul>
<?

// let's create a context sensitive main menu
	if ($here == "")
	{
		echo "<li class=this_page >Main page</li>";

// ############################### if session is logged in ###################################
		if( isset($_SESSION['user']))
		{
			foreach ($classes as $class_key => $class_value)
			{
				echo "<li><a href=". $current_file_name."?here=".$class_value.">".$class_value."</a></li>";
			}
		// addition menu item which links to "Enter Card" page.
		echo "<li><a href=". $current_file_name."?here=entercard>enter card</a></li>";
		}
	}
	else if ($here != "" )
	{
		// stop drawing the menus if the session isnt logged in.
		if( isset($_SESSION['user']))
		{
			echo "<li ><a href=". $current_file_name.">Main page</a></li>";
			foreach ($classes as $class_key => $class_value)
			{
				if ($class_value != $here)
				{
					echo "<li><a href=". $current_file_name."?here=".$class_value.">".$class_value."</a></li>";
				}
				else
				{
					echo "<li class=this_page>".$here."</li>";
				}
			}
			
			// additional menu item "Enter Card"
			if ($here != "entercard")
			{
				echo "<li><a href=". $current_file_name."?here=entercard>enter card</a></li>";
			}
			else
			{
				echo "<li class=this_page>enter card</li>";
			}
		}
	}
	
?>
</ul>
<!--</div>-->
<?

//	if ($here == "")
	{
?>

<?
	}
?>
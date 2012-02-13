<!--<div id=div_menu>-->
<ul>
<?
// let's create a context sensitive main menu
	if ($here == "")
	{
		echo "<li class=this_page >Main page</li>";
		foreach ($classes as $class_key => $class_value)
		{
			echo "<li><a href=". $current_file_name."?here=".$class_value.">".$class_value."</a></li>";
		}

	}
	else if ($here != "")
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
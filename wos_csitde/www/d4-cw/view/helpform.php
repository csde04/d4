<h3>Looking for help?</h3>
<p>
	<?
	if( isset($_SESSION['user']))
	{
	?>
		Click <a href=<?echo $current_file_name."?here=help";?>><b>here</b></a>
		to go to the help page.
	<?
	}
	else
	{
		echo "Login to access the help section.";
	}
	?>
</p>
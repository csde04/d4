<?
	if ($here == "")
		{
?>
<p class="p1">Welcome to the <? echo $application_title; ?></p>
	<div class=div_image>
		<img src="./include/images/london-olympic-logo.gif"/> 
		<span class="caption">London 2012 Olympics logo © 2012</a></span>
	</div>
	<div class=div_entercardform>
		<? include "view/view_login.php"; ?>
	</div>
<?
		}
?>

<?
	function post_update_message($pino,$classino)
	{
		echo "<p class=p_message>[".date('H:i:s')."] ".$classino." record (id = ".$pino.") has been updated!</p>";
	}
	
	function post_create_message($pino,$classino)
	{
		echo "<p class=p_message>[".date('H:i:s')."] ".$classino." new record (id = ".$pino.") has been created!</p>";
	}

?>
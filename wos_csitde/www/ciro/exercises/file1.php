<html>

<link id="style_link" rel=StyleSheet href="style.css" type="text/css" media=all>
<body>

<?php
  echo "Hello world, world1111!";
  if(isset($_POST['name31']))
  {
  echo "<p>... and hello ";
  echo ("<b>".$_POST['name31']."</b>");
  }
?>

<script>
	function submitta()
	{
		document.form31.submit();
	}
</script>

<form name="form31" action="file1.php" method=post >

<p>What's your name?

<input name="name31" type="text">

<input type = "button" value = "Submit" onClick = "javascript:submitta();">

</form>

</body>
</html>



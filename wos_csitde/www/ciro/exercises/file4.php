<html>
<link id="style_link" rel=StyleSheet href="style.css" type="text/css" media=all>
<body>

<?php
	$name = $_REQUEST['input_name'];
	$surname = $_REQUEST['input_surname'];
	$is_staff = $_REQUEST['input_is_staff'];
	$conn = mysql_connect ('localhost','root','')
              or die("<h1>Cannot connect to mySQL DBMS on this host</h1>");
	$database = mysql_select_db ('policedb1',$conn)
            or die ("<h1>My database inaccessible</h1>");
	$insert_query = "Insert into people (name,surname,is_staff) values ('".$name."','".$surname."','".$is_staff."');";
	echo "<p>".$insert_query;
	mysql_query($insert_query) or die("<p>Query failed: ".mysql_error());
	echo "<p>The following values (".$name.", ".$surname.", ".$is_staff.") have been inserted within the table with the id ".mysql_insert_id();
	mysql_close($conn);
?>

</body>
</html>
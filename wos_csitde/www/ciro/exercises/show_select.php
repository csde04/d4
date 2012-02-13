<html>
<link id="style_link" rel=StyleSheet href="style.css" type="text/css" media=all>
<body>
<?php
//echo $query;
$conn = mysql_connect ('localhost','root','')
              or die("<h1>Cannot connect to mySQL DBMS on this host</h1>");

$database = mysql_select_db ('policedb1',$conn)
            or die ("<h1>My database inaccessible</h1>");

$query = $_REQUEST['query'];
$result = mysql_query ($query) or die("Query failed");

$rows = mysql_num_rows ($result);
$columns = mysql_num_fields($result);

// if $show_columns = array() then all columns are to be shown;

$html_table = "<table id='table1'><tr>";
for($i = 0; $i < $columns; $i++)
	{
		$html_table = $html_table."<th>".mysql_field_name($result,$i);
	}
while ($row = mysql_fetch_array($result))
{
	$html_table = $html_table."<tr>";
	for($i = 0; $i < $columns; $i++)
	{
			if (strstr(mysql_field_flags($result,$i),'primary_key'))
			{
				$html_table = $html_table."<td><a href=''>".$row[$i]."</a>";
			}
			else
			{
				$html_table = $html_table."<td>".$row[$i];
			}
	}
}

$html_table = $html_table . "</table>";
$result_as_html = "<B>".$header."</B> <P>" . $html_table . "<P>The query executed was: <BR><I>".$query;
$shown_columns = $columns;
if (count($show_columns)>0)
{
	$shown_columns = count($show_columns);
}

$result_as_html = $result_as_html."</I><BR>Shown columns: <I>".$shown_columns." out of ".$columns."</I>";

echo $result_as_html;

?>
</body>
</html>
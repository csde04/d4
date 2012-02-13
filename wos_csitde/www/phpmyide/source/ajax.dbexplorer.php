<?php

$request = $_POST['request'];

switch($request)
{
	case 'refresh':
		$db->query("show databases");
		while ($db->fetchRow()) $out[] = $db->row[0];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'gettables':
		$db->query("show tables from {$_POST['dbname']}");
		while ($db->fetchRow()) $out[] = $db->row[0];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'getfields':
		if ($_POST['fields'])
		{
			$oddRow = false;
			$db->query("describe {$_POST['dbname']}.{$_POST['tablename']}");
			$out[] = <<<HTML
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr class="mainfont s10 bold" align="left">
		<td>Field&nbsp;Name</td>
		<td>Kind</td>
		<td>Nulls</td>
		<td>Default</td>
		<td>Extra</td>
	</tr>
	<tr>
		<td colspan="6"><img src="graphics/dot_black.gif" height="1" width="100%"></td>
	</tr>
HTML;
			while ($db->fetchArray())
			{
				$color = ($oddRow) ? '#e4edff' : '#ffffff';
				$oddRow = !$oddRow;
				$out[] = <<<HTML
<tr align="left" bgcolor="$color" class="mainfont s10">
	<td width="10%" nowrap class="bold">{$db->row['Field']}&nbsp;&nbsp;&nbsp;</td>
	<td width="10%" nowrap>{$db->row['Type']}&nbsp;&nbsp;&nbsp;</td>
	<td width="10%" nowrap>{$db->row['Null']}&nbsp;&nbsp;&nbsp;</td>
	<td width="10%" nowrap>{$db->row['Default']}&nbsp;&nbsp;&nbsp;</td>
	<td width="10%" nowrap>{$db->row['Extra']}&nbsp;&nbsp;&nbsp;</td>
	<td width="90%">&nbsp;</td>
</tr>
HTML;
			}
			
			$out[] = '</table>';
		} else {
			$arr = array();
			$db->query("show index from {$_POST['dbname']}.{$_POST['tablename']}");
			while ($db->fetchArray())
			{
				if ($db->row['Key_name'] == 'PRIMARY') $theType = 'PKey';
				else {
					switch($db->row['Index_type'])
					{
						case 'FULLTEXT':
							$theType = 'FullText';
							break;
						default:
							$theType = 'Index';
							break;
					}
				}
				$arr[$db->row['Key_name']]['type'] = $theType;
				$arr[$db->row['Key_name']]['cardinality'] = $db->row['Cardinality'];
				$arr[$db->row['Key_name']]['cols'][] = $db->row['Column_name'];
			}
			

			$oddRow = false;
			$out[] = <<<HTML
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr class="mainfont s10 bold" align="left">
		<td>Index&nbsp;Name&nbsp;&nbsp;&nbsp;</td>
		<td>Type&nbsp;&nbsp;&nbsp;</td>
		<td>Cardinality&nbsp;&nbsp;&nbsp;</td>
		<td>Fields</td>
	</tr>
	<tr>
		<td colspan="5"><img src="graphics/dot_black.gif" height="1" width="100%"></td>
	</tr>
HTML;
			
			foreach($arr as $name=>$data)
			{
				$color = ($oddRow) ? '#e4edff' : '#ffffff';
				$oddRow = !$oddRow;
				$out[] = <<<HTML
	<tr align="left" bgcolor="$color" class="mainfont s10">
		<td width="5%">$name&nbsp;&nbsp;&nbsp;</td>
		<td width="5%">$theType&nbsp;&nbsp;&nbsp;</td>
		<td width="5%" align="right">{$data['cardinality']}&nbsp;&nbsp;&nbsp;</td>
		<td width="95%">&nbsp;</td>
	</tr>
HTML;
			}
			$out[] = '</table>';
		}

		echo implode(chr(10), $out);
		break;
		
	case 'print':
		$classPath = 'source';
		$GLOBALS['fontPath'] = 'source/fonts';
		require("source/class.dbereport.php");

		$dbName = $_POST['dbname'];
		switch($_POST['type'])
		{
			case 'database':
				$job = new dbeDatabaseReport();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;

			case 'table':
				$job = new dbeTableReport();
				$job->dbName = $_POST['dbname'];
				$job->tableName = $_POST['tablename'];
				$job->execute();
				break;
				
			default:
				echo "Where the heck am I?\n\n";
				print_r($_POST);
				break;
				
		}
		break;

	default:
		echo "Unknown Request";
		
}

?>
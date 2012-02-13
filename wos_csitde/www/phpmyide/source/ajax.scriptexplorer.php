<?php

$request = $_POST['request'];

switch($request)
{
	case 'delete':
		$type = $_POST['type'];
		$dbName = $_POST['dbname'];
		$name = $_POST['name'];
		$out = array();
		
		switch($type)
		{
			case 'procedure':
				$db->query("drop procedure if exists $dbName.$name");
				$db->query("show procedure status where db='{$_POST['dbname']}'");
				while ($db->fetchRow()) $out[] = $db->row[1];
				sort($out);
				echo json_encode($out);
				break;				

			case 'function':
				$db->query("drop function if exists $dbName.$name");
				$db->query("show function status where db='{$_POST['dbname']}'");
				while ($db->fetchRow()) $out[] = $db->row[1];
				sort($out);
				echo json_encode($out);
				break;				

			case 'view':
				$db->query("drop view if exists $dbName.$name");
				$db->query("select table_name from information_schema.views where table_schema='{$_POST['dbname']}' order by table_name");
				$out = array();
				while ($db->fetchRow()) $out[] = $db->row[0];
				echo json_encode($out);
				break;
		}
		break;
		
	case 'deletetrigger':
		$dbName = $_POST['dbname'];
		$table = $_POST['table'];
		$timing = $_POST['timing'];
		$db->query("select trigger_name from information_schema.triggers where trigger_schema='$dbName' and event_object_table='$table' and concat(action_timing, '.', event_manipulation)='$timing'");
		if ($db->fetchRow())
			$db->query("drop trigger if exists $dbName.{$db->row[0]}");
		$db->query("delete from {$GLOBALS['pmiDBPrefix']}triggers where dbname='$dbName' and tablename='$table' and timing='$timing'");
		echo buildTriggerTable();
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
		
	case 'getfunctions':
		$db->query("show function status where db='{$_POST['dbname']}'");
		$out = array();
		while ($db->fetchRow()) $out[] = $db->row[1];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'getprocedures':
		$db->query("show procedure status where db='{$_POST['dbname']}'");
		$out = array();
		while ($db->fetchRow()) $out[] = $db->row[1];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'gettriggers':
		echo buildTriggerTable();
		break;
		
	case 'gettriggertables':
		$db->query("show tables from {$_POST['dbname']}");
		while ($db->fetchRow()) $out[] = $db->row[0];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'getviews':
		$db->query("select table_name from information_schema.views where table_schema='{$_POST['dbname']}' order by table_name");
		$out = array();
		while ($db->fetchRow()) $out[] = $db->row[0];
		echo json_encode($out);
		break;
		
	case 'new':
		$type = $_POST['type'];
		$dbName = $_POST['dbname'];
		$newName = $_POST['name'];
		
		switch($type)
		{
			case 'procedure':
				$sql = <<<SQL
CREATE PROCEDURE $dbName.$newName()
BEGIN

	-- Put code here
	
END
SQL;
				break;
				
			case 'function':
				$sql = <<<SQL
CREATE FUNCTION $dbName.$newName() RETURNS NUMERIC(8,2) DETERMINISTIC
BEGIN

	-- Put code here
	
	RETURN(0);
END
SQL;
				break;
				
			case 'view':
				$sql = "CREATE VIEW $dbName.$newName AS SELECT 0";
				break;
				
		}
		$db->query($sql);
		
		$err = $db->error();
		if (!$err)
		{
			echo "$dbName.$newName";
		} else {
			if (preg_match('/exists/', $db->error()))
			{
				$errMsg = "Error: Script already exists\n\n$dbName.$newName";
			} else {
				$errMsg = "Error: {$db->error()}";
			}
			echo $errMsg;
		}
		break;
		
	case 'newtrigger':
		// Note that new triggers are ALWAYS created offline and for each row
		$dbName = $_POST['dbname'];
		$table = $_POST['table'];
		$timing = $_POST['timing'];
		$script = <<<SCRIPT
BEGIN
	-- put trigger code here
END
SCRIPT;
		$db->query("insert into {$GLOBALS['pmiDBPrefix']}triggers(dbname, tablename, timing, script) values('$dbName', '$table', '$timing', '$script')");
		echo "$dbName.$table.$timing";		
		break;
		
	case 'print':
		$classPath = 'source';
		$GLOBALS['fontPath'] = 'source/fonts';
		require("source/class.dbreport.php");

		$dbName = $_POST['dbname'];
		switch($_POST['type'])
		{
			case 'database':
				$job = new reportDBCode();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;

			case 'procedures':
				$job = new reportProcedures();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;
				
			case 'functions':
				$job = new reportFunctions();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;
				
			case 'triggers':
				$job = new reportTriggers();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;

			case 'views':
				$job = new reportViews();
				$job->dbName = $_POST['dbname'];
				$job->execute();
				break;
		}
		break;

	case 'refresh':
		$db->query("show databases");
		while ($db->fetchRow()) $out[] = $db->row[0];
		sort($out);
		echo json_encode($out);
		break;
		
	case 'toggletrigger':
		$dbName = $_POST['dbname'];
		$table = $_POST['table'];
		$tstr = $_POST['timing'];
		$timing = str_replace('.', ' ', $tstr);
		$trgName = $dbName . '.' . ucfirst(strtolower($dbName)) . ucfirst(strtolower($table)) . str_replace(' ', '', ucwords(strtolower($timing)));
		switch($tstr)
		{
			case 'before.insert':
				$tVal = 0;
				break;
			case 'after.insert':
				$tVal = 1;
				break;
			case 'before.update':
				$tVal = 2;
				break;
			case 'after.update':
				$tVal = 3;
				break;
			case 'before.delete':
				$tVal = 4;
				break;
			case 'after.delete':
				$tVal = 5;
				break;
		}
		
		// See if it is online or not because I behave differently based on what I find...
		// Note that I don't rely on the trgName here because it could have been stored by someone other than me
		// and I still need to collected the code to get it to the backup...
		$db->query("select action_statement script, trigger_name from information_schema.triggers where trigger_schema='$dbName' and event_object_table='$table' and concat(action_timing, '.', event_manipulation)='$tstr'");
		if ($db->fetchRow())
		{
			// It's online!
			$script = mysql_escape_string($db->row[0]);
			$db->query("delete from {$GLOBALS['pmiDBPrefix']}triggers where dbname='$dbName' and tablename='$table' and timing='$tstr'");			
			$db->query("insert into {$GLOBALS['pmiDBPrefix']}triggers(dbname,tablename,timing,script) values('$dbName', '$table', '$tstr', '$script')");
			$db->query("drop trigger $trgName");
			echo "$tVal";
		} else {
			// It is not online... take the trigger from the backup table and try to move it live...
			$db->query("select script from {$GLOBALS['pmiDBPrefix']}triggers where dbname='$dbName' and tablename='$table' and timing='$tstr'");
			$db->fetchRow();
			$script = $db->row[0];
			
			$db->query("drop trigger if exists $trgName");
			$sql = <<<SQL
create trigger $trgName $timing on $dbName.$table for each row
$script
SQL;
			$db->query($sql);
			$err = $db->error();
			if ($err)
			{
				echo "{$db->lastQuery()}\n\n";
				echo "Error String:\n{$db->error()}";
			} else {
				echo "$tVal";
			}
		}
		break;
		
	default:
		echo "Unknown Request";
		
}

function buildTriggerTable()
{
	global $db;
	
	$out['before.insert'] = array('exists'=>false, 'online'=>false);
	$out['after.insert'] = array('exists'=>false, 'online'=>false);
	$out['before.update'] = array('exists'=>false, 'online'=>false);
	$out['after.update'] = array('exists'=>false, 'online'=>false);
	$out['before.delete'] = array('exists'=>false, 'online'=>false);
	$out['after.delete'] = array('exists'=>false, 'online'=>false);
	
	$theDB = $_POST['dbname'];
	$theTable = $_POST['tablename'];
	$db->query("select concat(action_timing, '.', event_manipulation) trig from information_schema.triggers where trigger_schema='$theDB' and event_object_table='$theTable'");
	while ($db->fetchRow())
	{
		$out[strtolower($db->row[0])]['exists'] = true;
		$out[strtolower($db->row[0])]['online'] = true;
		$out[strtolower($db->row[0])]['oneshot'] = false; // FIX THIS!
	}
		
	// This checks to see if there are offline triggers...
	$db->query("select timing, oneshot from {$GLOBALS['pmiDBPrefix']}triggers where dbname='$theDB' and tablename='$theTable'");
//echo $db->lastQuery();
	while($db->fetchRow())
	{
		$out[$db->row[0]]['exists'] = true;
		$out[$db->row[0]]['oneshot'] = ($db->row[1] == '1');
	}
	
	return json_encode($out);
}

?>
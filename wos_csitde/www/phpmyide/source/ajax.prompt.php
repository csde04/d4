<?php

/*
	Notes
	
	If the SQL starts with either SELECT or CALL then I need to
	spawn something completely different to handle it, because it
	might be the table of death, or a long running / multiple response
	stored procedure/function.
	
	Otherwise though, I can throw the query and answer it here.
	
	WAIT - perhaps the client can tell me that, then it would know how to behave as well...
	
*/

$ctype = $_POST['ctype'];
$command = $_POST['command'];
	
// It could be a multiple line command...
if (strpos($command, ';')) $ctype = 'long';


switch ($ctype)
{
	case 'short':
		$my = new mysqli($connections[$currCon]['host'], $connections[$currCon]['user'], $connections[$currCon]['password']);
		handleShort();
		break;
		
	case 'long':
		$today = date('Y-m-d', time());
		// NOTE: This is ALWAYS put in the zeroth connection...
		$my = new mysqli($connections[0]['host'], $connections[0]['user'], $connections[0]['password']);
		$my->query("insert into {$GLOBALS['pmiDBPrefix']}long_commands(datestamp, command) values('$today', '$command')");
		$res = $my->query("select LAST_INSERT_ID()");
		$row = $res->fetch_row();
		$id = $row[0];
		
		require "source/class.webrequest2.php";
		$req = new webRequest2();
		$req->domain = $siteHost;
		$req->port = $sitePort;
		$req->url = "{$siteURL}apthread.php";
		$req->addGetParam('conn', $currCon);
		$req->addGetParam('cdate', $today);
		$req->addGetParam('cid', $id);
		$req->addGetParam('dbname', $_POST['dbname']);
		$req->authName = $connections[$currCon]['authname'];
		$req->authPass = $connections[$currCon]['authpass'];
		$req->earlyTermStr = 'OK';
		$req->dispatch();
		echo "OK:$today:$id";
		break;
		
	case 'chunk':
		// NOTE: This is ALWAYS put in the zeroth connection...
		$my = new mysqli($connections[0]['host'], $connections[0]['user'], $connections[0]['password']);
		handleChunk();
		break;
		
	case 'cancel':
		$dateStamp = $_POST['datestamp'];
		$id = $_POST['id'];
		// NOTE: This is ALWAYS put in the zeroth connection...
		$my = new mysqli($connections[0]['host'], $connections[0]['user'], $connections[0]['password']);
		$my->query("delete from {$GLOBALS['pmiDBPrefix']}long_responses where command_datestamp='$dateStamp' and command_id=$id");
		echo "OK";
		break;
		
	default:
		die('Undefined ctype');

}
exit;



function handleChunk()
{
	global $my, $connections, $currCon;
	$dateStamp = $_POST['datestamp'];
	$id = $_POST['id'];
	$res = $my->query("select id, response from {$GLOBALS['pmiDBPrefix']}long_responses where command_datestamp='$dateStamp' and command_id=$id order by id limit 64");
	if ($res->num_rows == 0)
	{
		// Make sure that the command is not gone before I call it "done"...
		$res = $my->query("select id from {$GLOBALS['pmiDBPrefix']}long_commands where datestamp='$dateStamp' and id=$id");
		echo ($res->num_rows == 0) ? 'ALLDONE' : '';
	}
	
	$toDelete = -1;
	while ($row = $res->fetch_row())
	{
		$toDelete = $row[0];
		echo "{$row[1]}\n";
	}
	
	$sql = "delete from {$GLOBALS['pmiDBPrefix']}long_responses where command_datestamp='$dateStamp' and command_id=$id and id<=$toDelete";
	$my->query($sql);
}



function handleShort()
{
	global $my, $connections, $currCon;
	
	$results = array();
	if ($_POST['dbname']) $my->query("use {$_POST['dbname']}");
	
	// Is it a use connection?
	if (preg_match('/use connection[\s]+(.+$)/i', $_POST['command'], $parts))
	{
		// It's a use connection command...
		$newConn = $parts[1];
		if (preg_match('/^[0-9]{1,3}$/', $newConn))
		{
			if ($newConn >= count($connections))
			{
				echo "Error: No connection #$newConn";
				exit;
			}
			file_put_contents('current-connection', $newConn);
			echo "OK: {$connections[$newConn]['name']}:::{$connections[$newConn]['phpmyadmin']}";
			exit;
		}
		
		// OK - they've tried to use the name, try to find it that way...
		$testName = trim(strtolower($newConn));
		for ($i=0; $i<count($connections); $i++)
		{
			if (strtolower($connections[$i]['name']) == $testName)
			{
				file_put_contents('current-connection', $i);
				echo "OK: {$connections[$i]['name']}:::{$connections[$i]['phpmyadmin']}";
				exit;
			}
		}
		
		echo "Error: No connection named \"$newConn\" found";
		exit;
	}
	
	if (strtolower($_POST['command']) == 'show connections')
	{
		for ($i=0; $i<count($connections); $i++)
			echo "$i) {$connections[$i]['name']}\n";
		exit;
	}

	$sql = str_replace("\'", "'", $_POST['command']);
	if (($res = $my->query($sql)) === false) die($my->error);
	
	if (preg_match('/^(insert|update|delete)\s+/im', $sql))
	{
		echo "<i>*** Number of rows affected: {$my->affected_rows}</i>";
		exit;
	}

	if (gettype($res) == 'object')
	{

		// This will create the text box wrapper for the result, whether it's a single row or multiple.
		$rowPtr = 0;
		$output = array();
		$colWidth = array();
		while($row = $res->fetch_array(MYSQLI_ASSOC))
		{
			if ($rowPtr == 0)
			{
				$colPtr = 0;
				foreach($row as $name=>$value)
				{
					$output['header'][$colPtr] = $name;
					$colWidth[$colPtr] = strlen($name);
					$colPtr++;
				}
			}
			
			$colPtr = 0;
			foreach($row as $name=>$value)
			{
				$output['data'][$rowPtr][$colPtr] = $value;
				if (strlen($value) > $colWidth[$colPtr])
					$colWidth[$colPtr] = strlen($value);
					
				$colPtr++;
			}
			$rowPtr++;
		}
		
		$colCount = count($output['header']);
		
		if ($colCount == 0)
		{
			echo "*** <i>No rows returned</i> ***\n";
			exit;
		}
		
		$hsize = 1;
		foreach($colWidth as $wid) $hsize += ($wid + 3);
		$hline = str_pad($hline, $hsize, '-');
		
		$resp = array();
		$resp[] = "$hline\n|";

		for ($i=0; $i<$colCount; $i++)
			$resp[] = ' ' . str_pad($output['header'][$i], $colWidth[$i], '~') . ' |';
		$resp[] = "\n$hline\n";

		$rowCount = count($output['data']);
		for($i=0; $i<$rowCount; $i++)
		{
			$resp[] = '|';
			for($j=0; $j<$colCount; $j++)
			{
				$resp[] = ' ' . str_pad($output['data'][$i][$j], $colWidth[$j], '~') . ' |';
			}
			$resp[] = "\n";
		}
		$resp[] = "$hline\n";
		
		echo str_replace('~', '&nbsp;', implode('', $resp));
		
	} else {
		echo 'OK';
	}
}

?>
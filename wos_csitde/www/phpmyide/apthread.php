<?php

// This script is to be called by the Apache Threader in module.prompt.php only

$debug = false;

// This gets rid of the calling routine straight away and
// lets the apache "thread" part keep going...
if (!$debug)
{
	echo str_pad('OK', 2048, ' ');
	ob_flush();
	flush();
}

if ($_GET['testing']) exit;

// Here we go...
require (is_file('.config.php')) ? '.config.php' : 'config.php';

$currCon = file_get_contents('current-connection') - 0;
$commandDate = $_GET['cdate'];
$commandID = $_GET['cid'];
$dbName = $_GET['dbname'];

// This one is used for updating the output results. Note that it is ALWAYS the zeroth record...
//$upd = new mysqli($connections[$currCon]['host'], $connections[$currCon]['user'], $connections[$currCon]['password']);
$upd = new mysqli($connections[0]['host'], $connections[0]['user'], $connections[0]['password']);

// This one is used for the actual command...
$my = new mysqli($connections[$currCon]['host'], $connections[$currCon]['user'], $connections[$currCon]['password']);

if (($res = $upd->query("select command from {$GLOBALS['pmiDBPrefix']}long_commands where datestamp='$commandDate' and id=$commandID")) === false) 
	die($upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '{$my->error}')"));

$row = $res->fetch_array(MYSQLI_ASSOC);
$command = $row['command'];

if ($dbName) $my->query("use $dbName");
if ($my->error) 
{
	$upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '{$my->error}')");
	exit;
}

if($my->multi_query($command))
{
	do 
	{
		if ($res = $my->store_result())
		{
			buildCurrentResult();
			$res->close();
		} else {
			if ($my->error)
			{
				$err = 'Error: ' . mysql_escape_string($my->error);
				$upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '$err')");
			}
		}
		
	} while($my->next_result());
} else {
	if ($my->error)
	{
		$err = 'Error: ' . mysql_escape_string($my->error);
		$upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '$err')");
	}
}

$my->close();

// OK - now that the responses have all been built, I eliminate the "command" record
// so that the client ajax (after it's gotten all responses) will know that I'm done...
$upd->query("delete from {$GLOBALS['pmiDBPrefix']}long_commands where datestamp='$commandDate' and id=$commandID");
$upd->close;
exit;



function buildCurrentResult()
{
	global $my, $upd, $res, $row, $commandDate, $commandID;
	
	$firstRow = true;
	$colWidths = array();
	while($row = $res->fetch_assoc())
	{
		$colPtr = 0;
		foreach($row as $name=>$value)
		{
			if ($firstRow) $colWidths[$colPtr] = strlen($name);

			$thisLen = $res->lengths[$colPtr] + 3;
			if ($thisLen > 64) $thisLen = 64;
			if ($thisLen > $colWidths[$colPtr]) $colWidths[$colPtr] = $thisLen;
			$colPtr++;
		}
		$firstRow = false;
	}
	
	if (!$res->num_rows)
	{
		$upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '*** <i>No rows returned</i> ***')");
		return;
	}
	
	// Nove the pointer back to 0...
	$res->data_seek(0);

	// Now I have the lengths, build the table...
	$hsize = 1;
	foreach($colWidths as $wid) $hsize += ($wid + 3);
	$hline = str_pad($hline, $hsize, '-');
	
	$resp = array();
	$resp[] = "$hline\n";

	$rowPtr = 0;
	while($row = $res->fetch_assoc())
	{
		if ($rowPtr++ == 0)
		{
			$resp[] = '|';
			$colPtr = 0;
			foreach($row as $name=>$value)
				$resp[] = '~' . str_pad(trim($name), $colWidths[$colPtr++], '~') . '~|';
			$resp[] = "\n$hline\n";
		}
	
		$resp[] = '|';
		$colPtr = 0;
		foreach($row as $value)
		{
			if (strlen($value) > 64) $value = substr($value, 0, 64);
			$value = str_pad(trim($value), $colWidths[$colPtr++], '~');
			$resp[] = "~$value~|";
		}
		$resp[] = "\n";
	}
	$resp[] = "$hline\n";
	$resp[] = "<i>Number of rows returned: {$res->num_rows}</i>\n\n";
	
	$outLines = explode(chr(10), str_replace('~', '&nbsp;', implode('', $resp)));
	foreach($outLines as $line)
	{
		$line = mysql_escape_string($line);
		$upd->query("insert into {$GLOBALS['pmiDBPrefix']}long_responses(command_datestamp, command_id, response) values('$commandDate', $commandID, '$line')");
	}
}

?>
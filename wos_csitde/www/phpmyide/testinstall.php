<?php

echo <<<HTML
<html><head>

<style>
body { font-family: "Trebuchet MS",verdana,arial,sans-serif; font-size: 12px; }
#main { width: 300px; }
.header { font-weight: bold; font-size: 14px; text-align: left; padding-bottom: 10px; padding-top: 40px; }
.question { font-weight: bold; text-align: left; margin-left: 20px; }
.subquestion { font-weight: normal; text-align: left; margin-left: 40px; }
.answer { float: right; font-weight: normal; }
.subanswer { float: right; font-weight: normal; }
.bar { background-image: url("graphics/dot_d0d0d0.gif"); background-position: 0 50%; background-repeat: repeat-x; height: 10px; }
</style>

</head><body>

<center>

<div id="main">
HTML;

$files[] = (is_file('.config.php')) ? '.config.php' : 'config.php';
$files[] = 'source/class.dbconnection.php';
$files[] = 'source/class.webrequest2.php';

$goodToGo = true;
sendHeader('Testing Includes');
foreach($files as $file)
{
	if (is_file($file))
	{
		require $file;
		sendAnswer($file, 'OK');
	} else {
		sendAnswer($file, 'Not Available');
		$goodToGo = false;
	}
}
if (!$goodToGo)
{
	sendHeader('Error: No further tests preformed');
	exit;
}

sendHeader('Testing Functions');
if (testFunctions())
{
	sendHeader('Testing Prompt Handler');
	testWR2();
	
	sendHeader('Testing Connections');
	if (testConnections())
	{
		sendHeader('Testing Privileges');
		testPrivileges();
	} else sendHeader('Error: Privileges not tested');
} else sendHeader('Error: Connections and Privileges not tested');

echo "</div></body></html>";

function testFunctions()
{
	$retVal = true;
	
	if (function_exists('json_encode')) sendAnswer('json_encode', 'OK');
	else { sendAnswer('json_encode', 'Not Available'); $retVal = false; }
	
	if (function_exists('json_decode')) sendAnswer('json_decode', 'OK');
	else { sendAnswer('json_decode', 'Not Available'); $retVal = false; }
	
	if (function_exists('mysql_connect')) sendAnswer('mysql_connect', 'OK');
	else { sendAnswer('mysql_connect', 'Not Available'); $retVal = false; }
	
	if (function_exists('mysqli_connect')) sendAnswer('mysqli_connect', 'OK');
	else { sendAnswer('mysqli_connect', 'Not Available'); $retVal = false; }
	
	return $retVal;
}

function testConnections()
{
	global $connections;
	
	if (file_put_contents('current-connection', '0')) 
		sendAnswer('Writeable current-connection', 'OK');
	else 
		sendAnswer('current-content', 'Error: Cannot be written');
	
	$retVal = true;
	for($i=0; $i<count($connections); $i++)
	{
		$res = mysql_connect($connections[$i]['host'], $connections[$i]['user'], $connections[$i]['password'], $connections[$i]['rootdb']);
		if ($res) sendAnswer($connections[$i]['name'], 'OK');
		else { sendAnswer($connections[$i]['name'], 'Not Available'); $retVal = false; }
	}
	
	return $retVal;
}

function testPrivileges()
{
	global $connections; 
	
	$retVal = true;
	
	for($i=0; $i<count($connections); $i++)
	{
		sendAnswer($connections[$i]['name'], '');
		$db = new dbConnection($connections[$i]['host'], $connections[$i]['user'], $connections[$i]['password'], $connections[$i]['rootdb']);
		@$db->query("show procedure status");
		if ($db->error())
		{
			sendSubAnswer('show procedure status', $db->error);
		} else {
			sendSubAnswer('show procedure status', 'OK');
		}
		
		@$db->query("drop procedure if exists {$connections[$i]['rootdb']}.testtesttesttest");
		if ($db->error())
		{
			sendSubAnswer('drop procedure', $db->error);
		} else {
			sendSubAnswer('drop procedure', 'OK');
		}
		
		$sql = <<<SQL
CREATE PROCEDURE {$connections[$i]['rootdb']}.testtesttesttest()
BEGIN
	-- Dummy Procedure
END
SQL;
		@$db->query($sql);
		if ($db->error())
		{
			sendSubAnswer('create procedure', $db->error);
		} else {
			sendSubAnswer('create procedure', 'OK');
			$db->query("drop procedure if exists {$connections[$i]['rootdb']}.testtesttesttest");
		}
	}
	
	// ToDo: Test writing triggers and views
	
	return $retVal;
}

function testWR2()
{
	global $connections, $siteHost, $siteURL, $sitePort;

	$req = new webRequest2();
	$req->domain = $siteHost;
	$req->port = $sitePort;
	$req->url = "{$siteURL}apthread.php";
	$req->addGetParam('testing', '1');
	$req->authName = $connections[0]['authname'];
	$req->authPass = $connections[0]['authpass'];
	$req->dispatch();
	sendAnswer('Connection', (preg_match('/^OK/', trim($req->getContent()))) ? 'OK' : 'Not Available');
	return true;
}

function sendAnswer($q, $a)
{
	echo <<<HTML
<div class="answer">$a</div><div class="question">Testing: $q</div>
<div class="bar"></div>
HTML;
}

function sendSubAnswer($q, $a)
{
	echo <<<HTML
<div class="subanswer">$a</div><div class="subquestion">$q</div>
<div class="bar"></div>
HTML;
}

function sendHeader($msg)
{
	echo <<<HTML
<div class="header">$msg</div>
HTML;
}

?>

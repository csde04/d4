<?php

$request = $_POST['request'];
if (!$request) die('Hack Attempt');

switch($request)
{
	case 'opentrigger':
		break;
		
	case 'print':
		$classPath = 'source';
		$GLOBALS['fontPath'] = 'source/fonts';

		$type = $_POST['type'];
		$dbName = $_POST['dbname'];
		$name = $_POST['name'];

		require("source/class.printjob.php");
		$job = new printJob();
		$job->dbName = $_POST['dbname'];
		$job->scriptName = $_POST['scriptname'];
		$job->execute($_POST['buffer']);

		break;

	case 'retrieve':
		$type = $_POST['type'];
		$dbName = $_POST['dbname'];
		$name = $_POST['name'];
		
		switch($type)
		{
			case 'procedure':
				$db->query("show create procedure $dbName.$name");
				$db->fetchRow();
				$buff = $db->row[2];
				$ptr = strpos($buff, 'PROCEDURE');
				$buff = substr($buff, $ptr, strlen($buff));
				$buff = str_replace('`', '', $buff);
				echo $buff;
				break;

			case 'function':
				$db->query("show create function $dbName.$name");
				$db->fetchRow();
				$buff = $db->row[2];
				$ptr = strpos($buff, 'FUNCTION');
				$buff = substr($buff, $ptr, strlen($buff));
				$buff = str_replace('`', '', $buff);
				echo $buff;
				break;

			case 'trigger':
				// Dissect the name into the table and timing first...
				preg_match('/([^\.]+)\.(.*$)/', $name, $parts);
				$table = $parts[1];
				$timing = $parts[2];
		
				// First see if there is one online...
				$db->query("select action_statement script from information_schema.triggers where trigger_schema='$dbName' and event_object_table='$table' and concat(action_timing, '.', event_manipulation)='$timing'");
				if ($db->fetchRow())
				{
					// It's online - send it back...					
					echo "TRIGGER $dbName.$name\n{$db->row[0]}";
					exit;
				}
				
				// It's not online, get it from the triggers table...
				$db->query("select script from {$GLOBALS['pmiDBPrefix']}triggers where dbname='$dbName' and tablename='$table' and timing='$timing'");
				$db->fetchRow();
				echo "TRIGGER $dbName.$name\n{$db->row[0]}";
				break;

			case 'view':
				$db->query("show create view $dbName.$name");
				$db->fetchRow();
				$buff = $db->row[1];
				
				$search[] = 'ALGORITHM ';
				$replace[] = "\nALGORITHM ";
				
				$search[] = 'DEFINER=';
				$replace[] = "\nDEFINER=";
				
				$search[] = 'SQL ';
				$replace[]  = "\nSQL ";
				
				$search[] = 'VIEW ';
				$replace[] = "\nVIEW ";
				
				$search[] = '`';
				$replace[] = '';
				
				$buff = str_ireplace($search, $replace, $buff);
				$buff = preg_replace('/VIEW[\s]+([A-Z0-9\.]+)[\s]+AS\s/i', "VIEW $1\nAS ", $buff);
				
				echo $buff;
				break;

			default:
				echo 'What am I doing here?';
		}
		break;

	case 'save':
		$type = $_POST['type'];
		$dbname = $_POST['dbname'];
		$name = $_POST['name'];
		$buffer = str_replace('[%PLUS%]', '+', $_POST['buffer']);

		switch($type)
		{				
			case 'procedure':
				// The first line must look like this: 'PROCEDURE thename()' note that spaces and caps do not matter...
//				$regex = "/^[\s]*PROCEDURE[\s]+{$name}[\s]*\(([^)]*)\)[\s]+(begin.*$)/ims";
				$regex = "/^[\s]*PROCEDURE[\s]+{$name}[\s]*\((.*)\)[\s]+(begin.*$)/ims";
				preg_match($regex, $buffer, $parts);
				if (count($parts) == 0)
					die("Syntax error in header of procedure - script NOT updated");

				$parts[1] = trim($parts[1]);
				$parts[2] = trim($parts[2]);
				$buffer = "create procedure $dbname.$name({$parts[1]})\n{$parts[2]}";

				$sql = "drop procedure if exists $dbname.$name";
				$db->query($sql);

				$sql = str_replace("\'", "'", $buffer);
				$db->query($sql);
				$err = $db->error();
				if ($err)
				{
					// Prepare the backup for insertion...
					$regex = "/PROCEDURE[\s]+{$name}[\s]*\(([^)]*)\)[\s]+(begin.*$)/ims";
					$backup = str_replace('`', '', $backup);
					preg_match($regex, $backup, $parts);

					$parts[1] = trim($parts[1]);
					$parts[2] = trim($parts[2]);
					$backup = "create procedure $dbname.$name({$parts[1]})\n{$parts[2]}";
					$db->query($backup);
					
					// Grab what I want from the error...
					if (preg_match('/error/', $err) and preg_match('/syntax/', $err))
					{
						preg_match("/near '([^']+)[^0-9]+([0-9]{1,6})/im", $err, $matches);
						$lines = explode(chr(10), $sql);
						$showLine = $matches[2];
						$actualLine = $matches[2] - 1;
						echo "Syntax error on line $showLine:\n{$lines[$actualLine]}\n\n Script NOT updated";
					} else {
						echo "MySQL Unhandled Error:\n$err";
					}
				} else {
					$db->query("show create procedure $dbname.$name");
					$db->fetchRow();
					$backup = $db->row[2];
					$bStr = mysql_escape_string($backup);
					$now = date('Y-m-d H:i:s', time());
					$db->query("insert into {$GLOBALS['pmiDBPrefix']}timemachine(descriptor, timestamp, createsql) values('$type.$dbname.$name', '$now', '$bStr')");		
					echo 'OK';
				}
				break;
				
			case 'function':
//				$regex = "/function[\s]+{$name}[\s]*\(([^)]*)\)(.*)(begin.*$)/ims";
				$regex = "/function[\s]+{$name}[\s]*\((.*)\)(.*)(begin.*$)/ims";
				preg_match($regex, $buffer, $parts);
				if (count($parts) == 0)
					die('Syntax error in header of function - script NOT updated');
				
				$parts[1] = trim($parts[1]);
				$parts[2] = trim($parts[2]);
				$parts[3] = trim($parts[3]);
				$buffer = "CREATE FUNCTION $dbname.$name({$parts[1]}) {$parts[2]}\n{$parts[3]}";
			
				// Perform the backup...
				$db->query("show create function $dbname.$name");
				$db->fetchRow();
				$backup = $db->row[2];
				
				$sql = "drop function if exists $dbname.$name";
				$db->query($sql);

				$buffer = str_replace("\'", "'", $buffer);
				$db->query($buffer);
				$err = $db->error();
				if ($err)
				{
					// Prepare the backup for insertion...
					$backup = str_replace('`', '', $backup);
					$regex = '/function[\s]+[A-Z0-9]+[\s]*\(([^)]*)\)(.*)(begin.*$)/ims';
					preg_match($regex, $backup, $parts);
					$parts[1] = trim($parts[1]);
					$parts[2] = trim($parts[2]);
					$parts[3] = trim($parts[3]);
					$backup = "CREATE FUNCTION $dbname.$name({$parts[1]}) {$parts[2]}\n{$parts[3]}";
					$db->query($backup);
					
					// Grab what I want from the error...
					if (preg_match('/error/', $err) and preg_match('/syntax/', $err))
					{
						preg_match("/near '([^']+)[^0-9]+([0-9]{1,6})/im", $err, $matches);
						$lines = explode(chr(10), $buffer);
						$showLine = $matches[2];
						$actualLine = $matches[2] - 1;
						echo "Syntax error on line $showLine:\n{$lines[$actualLine]}\n\n Script NOT updated";
					} else {
						echo "MySQL Unhandled Error:\n$err";
					}
				} else {
					$bStr = mysql_escape_string($buffer);
					$now = date('Y-m-d H:i:s', time());
					$db->query("insert into {$GLOBALS['pmiDBPrefix']}timemachine(descriptor, timestamp, createsql) values('$type.$dbname.$name', '$now', '$bStr')");		
					echo 'OK';
				}
				break;
				
			case 'trigger':
			{
				// Note that there's no change to the ForEachRow or OneShot status by simply saving - 
				// I need to collect that and include it in the create trigger statement if it is online.
				
				$dbName = $_POST['dbname'];
				preg_match('/([^\.]+)\.(.*$)/', $_POST['name'], $parts);
				$table = $parts[1];
				$timing = $parts[2];
				
				// Clear out anything before the first "begin"
				$ptr = stripos($buffer, 'begin');
				$script = substr($buffer, $ptr, strlen($buffer));
				$buffer = mysql_escape_string($script);
				
				
				// First: Is the trigger currently online? If yes, I behave much differently than if no...
				$db->query("select action_statement, trigger_name from information_schema.triggers where trigger_schema='$dbName' and event_object_table='$table' and concat(action_timing, '.', event_manipulation)='$timing'");
				if ($db->fetchRow())
				{
					// First, take a backup of what's there in case I need to restore...
					$oldScript = $db->row[0];
					$triggerName = $db->row[1];
					
					// Now let's attempt to update the trigger...
					$tStr = str_replace('.', ' ', $timing);
					$trgName = $dbName . '.' . ucfirst(strtolower($dbName)) . ucfirst(strtolower($table)) . str_replace(' ', '', ucwords(strtolower($tStr)));
					$sql = <<<SQL
create trigger $trgName $tStr on $dbName.$table for each row
$script
SQL;
					$db->query("drop trigger $dbName.$triggerName");
					$db->query($sql);
					$err = $db->error();
					if ($err)
					{
						// Failed - restore the backup and tell the user...
						$sql = <<<SQL
create trigger $trgName $tStr on $dbName.$table for each row
$oldScript
SQL;
						$db->query(trim($sql));
						echo("Error: Trigger not updated. Message was:\n\n$err");
						exit;
						
					} else {
						// Success!
						
						// Make a backup to the TimeMachine...
						$descriptor = "trigger.$dbName.$table.$timing";
						$now = date('Y-m-d H:i:s', time());
						$db->query("insert into {$GLOBALS['pmiDBPrefix']}timemachine(descriptor, timestamp, createsql) values('$descriptor', '$now', '$buffer')");
				
						echo 'OK';
						exit;
					}

				} else {				
					// OK, it's not - so I simply save it to the backup trigger table...
					$db->query("update {$GLOBALS['pmiDBPrefix']}triggers set script='$buffer' where dbname='$dbName' and tablename='$table' and timing='$timing'");
					echo 'OK';
				}
			}
			break;
			
			case 'view':
				$db->query("show create view $dbname.$name");
				$db->fetchRow();
				$oldView = $db->row[1];
				$oldView = str_replace('`', '', $oldView);
				
				// Kill the old one...
				$db->query("drop view if exists $dbname.$name");
				
				// Attempt to push the new view:
				$db->query($buffer);
				if ($msg = $db->error())
				{
					// reinstate the old view...
					$db->query($oldView);
					
					// Now tell the user what's up...
					echo $msg;
				} else {
					// Push this version to the time machine...
					$buffer = mysql_escape_string($buffer);
					$descriptor = "view.$dbname.$name";
					$now = date('Y-m-d H:i:s', time());
					$db->query("insert into {$GLOBALS['pmiDBPrefix']}timemachine(descriptor, timestamp, createsql) values('$descriptor', '$now', '$buffer')");
					echo 'OK';
				}
				break;

			default:
				echo 'WTF?';
				break;
				
		}
		break;
				
	case 'saveas':
		$type = $_POST['type'];
		$dbname = $_POST['dbname'];
		$name = $_POST['name'];
		$buffer = $_POST['buffer'];
//		$buffer = str_replace("\'", "'", $buffer);
		$buffer = str_replace('[%PLUS%]', '+', $buffer);

		switch($type)
		{				
			case 'function':
			case 'procedure':
				// get everything starting with the left ( of the params and 
				// setup to save the new name...
				preg_match('/\(.*$/ism', $buffer, $matches);
				$buffer = "create $type $dbname.$name{$matches[0]}";
				$db->query($buffer);
				$err = $db->error();
				if ($err)
				{
					// Grab what I want from the error...
					if (preg_match('/error/', $err) and preg_match('/syntax/', $err))
					{
						preg_match("/near '([^']+)[^0-9]+([0-9]{1,6})/im", $err, $matches);
						$lines = explode(chr(10), $sql);
						$showLine = $matches[2];
						$actualLine = $matches[2] - 1;
						echo "Syntax error on line $showLine:\n{$lines[$actualLine]}\n\n Script NOT updated";
					} else {
						echo "MySQL Unhandled Error:\n$err";
					}
				} else {
					echo "$type.$dbname.$name";
				}
				break;
			
			default:
				echo 'WTF?';
				break;
		}
		break;
				
		default:
			alert('Unknown Command');
			
}

?>
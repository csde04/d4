<?php

function microtime_float()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

function microtime_difference($startTime)
{
	return microtime_float() - $startTime;
}

function getDirs($inDir)
{
        $out = array();
        $h = opendir($inDir);
        while (! is_bool($file=readdir($h))) {
                if (! is_dir($inDir . '/' . $file)) { continue; }
                if (( $file=='.') || ($file=='..')) {continue;}
                $out[] = $file;
        }
        closedir($h);
        sort($out);
        return $out;
}

function getFiles($inDir)
{
        $out = array();
        if (!is_dir($inDir)) { die("ERR-01: Invalid Directory '$inDir'"); }
        $h = opendir($inDir);
        while (! is_bool($file=readdir($h))) {
                if (is_dir($inDir . '/' . $file)) {continue;}
				if ($file[0] == '.') { continue; }
                $out[] = $file;
        }
        closedir($h);
        sort($out);
        return $out;
}

function dbToDate($inVal)
{
	$inVal = trim($inVal);
	if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $inVal)) { $inVal .= ' 00:00:00'; }
	preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $inVal, $dparts);
	if (!$dparts[0]) { throw new Exception("dbToDate: Unrecognized date format '$inVal'"); }
	$retVal = mktime($dparts[4], $dparts[5], $dparts[6], $dparts[2], $dparts[3], $dparts[1]);
	return $retVal;
}

function nameCaps($name)
{
	$name = strtolower(trim($name));
	$name = join('-', array_map('ucwords', explode('-', $name)));
	$name = join(';', array_map('ucwords', explode(';', $name)));
	$name = join('|', array_map('ucwords', explode('|', $name)));
	$name = join('\'', array_map('ucwords', explode('\'', $name)));
	$name = join('Mac', array_map('ucwords', explode('Mac', $name)));
	$name = join('Mc', array_map('ucwords', explode('Mc', $name)));
	return $name;
}

function validEMail($email) { return preg_match('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', trim($email)); }

?>
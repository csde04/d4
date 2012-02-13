<?php
/*
	NN: Master/Detail relationship
	Need a way to say that this object is a detail to a master.
	Possibly named values ($this->namedValue('clientid'))
	then used in the sql: select * from atable where clientid=[$clientid]
	The master should have a list of children, (clearable) that, when it moves
	should tell each child to requery with the new value and fetch. Then 
	I (programmer) can simply query each of my db objects for their current
	value, since moving the master would adjust all of the children automatically.
*/

class dbConnection
{
	var $__connected;
	var $__host;
	var $__user;
	var $__password;
	var $__database;
	var $__myConnection;
	var $__lastQuery;
	var $dataSet;
	var $row;
	var $silent;
	
	function dbConnection($host, $user, $password, $database, $doConnect=true)
	{
		$this->__host = $host;
		$this->__user = $user;
		$this->__password = $password;
		$this->__database = $database;
		$this->__connected = false;
		$this->silent = false;
//		if ($doConnect) { $this->connect(); }
	}
	function &cloneConnection()
	{
		if (!$this->__connected) { $this->connect(); }
		return new dbConnection($this->__host, $this->__user, $this->__password, $this->__database);
	}
	function affectedRows()
	{
		if($this->__connected) { return mysql_affected_rows($this->__myConnection); }
		return false;
	}
	function close()
	{
		if($this->__connected) { mysql_close($this->__myConnection); }
	}
	function connect()
	{
		$this->__myConnection = mysql_connect($this->__host, $this->__user, $this->__password, true, 65536);
		if (!$this->__myConnection) { die('class DBConnection cannot connect'); }
		$this->__connected = true;
		mysql_select_db($this->__database, $this->__myConnection);
	}
	function connected() { return $this->__connected; }
	function error()
	{
		if (!$this->__connected) { return ''; }
		return mysql_error($this->__myConnection);
	}
	function fetchArray()
	{
		// Note that I could have just said "return $this->row" and gotten
		// essentially the same result, but I am potentially passing WAY more
		// data back than is necessary - especially if there is a TEXT attached to the row
		$this->row = mysql_fetch_array($this->dataSet);
		if ($this->row) { return true; } else { return false; }
	}
	function fetchAssoc()
	{
		// Note that I could have just said "return $this->row" and gotten
		// essentially the same result, but I am potentially passing WAY more
		// data back than is necessary - especially if there is a TEXT attached to the row
		$this->row = mysql_fetch_assoc($this->dataSet);
		if ($this->row) { return true; } else { return false; }
	}
	function fetchRow()
	{
		$this->row = @mysql_fetch_row($this->dataSet);
		if ($this->row) { return true; } else { return false; }
	}
	function fieldCount() { return mysql_num_fields($this->dataSet); }
	function fieldName($idx) { return mysql_field_name($this->dataSet, $idx); }
	function lastInsertID() { return $this->singleAnswer('select LAST_INSERT_ID(' . $this__myConnection . ')'); }
	function lastQuery() { return $this->__lastQuery; }
	function query($queryStr, $ignore=false)
	{
		if (!$this->__connected) { $this->connect(); }
		$this->__lastQuery = $queryStr;
		$this->__lastError = '';
		$this->dataSet = mysql_query($queryStr, $this->__myConnection) or ($this->__lastError = mysql_error($this->__myConnection));
		if (($this->__lastError > ' ') && (!$ignore))
		{
			if (!$this->silent)
				print "MySQL Error on query('{$this->__lastQuery}') - {$this->__lastError}";
		}
	}
        function rowCount()
        {
                return mysql_num_rows($this->dataSet);
        }
	function rowToXML(&$xml)
	{
		$max = mysql_num_fields($this->dataSet);
		for ($i=0; $i<$max; $i++)
		{
			$xml->addChild(mysql_field_name($this->dataSet, $i), $this->row[$i], false);
		}
	}
	function seek($rowNum)
	{
		return mysql_data_seek($this->dataSet, $rowNum);
	}
	function selectDB($dbName)
	{
		if (!$this->__connected) { $this->connect(); }
		mysql_select_db($dbName, $this->__myConnection);
	}
	function singleAnswer($queryStr)
	{
		if (!$this->__connected) { $this->connect(); }
		$this->query($queryStr);
		$this->fetchRow();
		return $this->row[0];
	}
	function singleArray($queryStr)
	{
		if (!$this->__connected) { $this->connect(); }
		$this->query($queryStr);
		$this->fetchRow($this->__myConnection);
		return $this->row;
	}
	function singleAssoc($queryStr)
	{
		if (!$this->__connected) { $this->connect(); }
		$this->query($queryStr);
		$this->row = mysql_fetch_assoc($this->dataSet);
		return $this->row;
	}
}

?>
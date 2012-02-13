<?php

// These parameters are what configure the entirety of phpMyIDE.
// You may move this file to .config.php if you prefer.

/*
	$siteHost
	This variable is the domain of where <this> instance of phpMyIDE is 
	to be found via a browser. It needs to be a completely qualified domain,
	but nothing else. For example, if you want to go to 
		http://www.mydomain.com/phpmyide/ 
	for this instance, then the $siteHost variable would be "www.mydomain.com"
*/
$siteHost = '42.phpmyide';

$siteHost = 'localhost';

/*
	$siteURL
	This variable is the URL path of where a browser will find <this>
	instance of phpMyIDE. If you want to access phpMyIDE with the URL:
		http://www.mydomain.com/phpmyide/
	then the $siteURL variable should be '/phpmyide/'. If you are pointing
	to the root of a domain like this:
		http://www.mydomain.com/
	then the $siteURL variable should simply be '/'
*/
$siteURL = '/';

/* 
	$sitePort
	This variable should, in most instances, be left alone since more webservers
	server web pages on port 80. If, however, you want to access phpMyIDE on another
	port, then A) you know what the port number is and B) you'll know why to 
	change this. Again, in most circumstances this should be left alone.
*/
$sitePort = 80;

$sitePort = 8080;

/*
	$ajaxLog
	If this variable is set to true the a tab will show up in the bottom area 
	of the IDE called "Ajax Log" - this will display a verbose log of all
	AJAX communications with the server for debugging. In most curcimstances,
	this should be left as false for efficiency.
*/
$ajaxLog = false;

/*
	$pmiDatabase
	$pmiPrefix
	This variable is the database where phpMyIDE can find it's own tables. 
	It can be any database, at all, and in the default configuration phpMyIDE
	expects its own database. If you are using an existing database, then you
	should set the $pmiPrefix variable to something like 'pmi_' so that tables
	made for phpMyIDE will be separate from other tables in your database. In
	the default configuration, phpMyIDE expects a database "phpmyide" and no
	prefix for the table names.
*/
$pmiDatabase = 'phpmyide';
$pmiPrefix = '';

/* 
	Connections
	The $connections array contains all the information phpMyIDE needs to 
	connect to databases and load up phpMyAdmin for you in the correct pane.
	Add connections by creating another block of code like the one below,
	from the $ptr++ line to the // ====== // line. 
	
	['name'] 		This is the caption for the connection. It shows up at the top right
					corner of the IDE as well as when you type "show connections"
	['host']		This is the host address or name where the MySQL instance you are
					connecting to is. In most cases you will connect to 127.0.0.1
					(the local machine) for your primary connection.
	['user']		This is your user name for MySQL login.
	['password']	This is your MySQL password
	['rootdb']		This is a database at <this MySQL instance> that you should
					have pretty full privileges. This is the database that will be
					used for testing the connection and privileges.
	['phpmyadmin']	This is the full URL pointing to a phpMyAdmin instance for <this>
					connection. It will be iFramed in a tab at the bottom of the IDE
					and change as you change connections automatically.
	['authname']	If your web server has a authentication scheme then you'll need 
	['authpass']	to fill these two fields in. In most cases, or if you don't know
					what I'm talking about then DONT FILL THEM IN. If, when you go
					to connection to phpMyIDE you get a Windows or Mac dialog message
					that asks you for your username and password, then these fields
					must be filled in for the IDE to work correctly. This will only
					present itself as a problem if you run a long command from the
					command prompt like, "select * from atable." The testinstall.php
					script will let you know if this is set up correctly.
					
	An important thing to note is that the zeroth connection [0] is very important
	to phpMyIDE. It's the place where the app will expect to find the tables that
	phpMyIDE uses. 
*/

$ptr = -1; // Leave this alone

$ptr++;
$connections[$ptr]['name'] = 'Primary Connection';
$connections[$ptr]['host'] = '127.0.0.1';
$connections[$ptr]['host'] = 'localhost:8080';
$connections[$ptr]['user'] = 'username';
$connections[$ptr]['user'] = 'root';
$connections[$ptr]['password'] = 'password';
$connections[$ptr]['password'] = '';
$connections[$ptr]['rootdb'] = 'phptest';
$connections[$ptr]['rootdb'] = 'cw0910';
$connections[$ptr]['phpmyadmin'] = 'http://www.mydomain.com/phpMyAdmin/';
$connections[$ptr]['phpmyadmin'] = 'http://localhost:8080/phpMyAdmin/';
$connections[$ptr]['authname'] = '';
$connections[$ptr]['authpass'] = '';
// ==================================== //

/* Don't touch this one either... */
$GLOBALS['pmiDBPrefix'] = $pmiDBPrefix = "$pmiDatabase.$pmiPrefix";

?>
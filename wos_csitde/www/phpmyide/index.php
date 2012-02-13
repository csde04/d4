<?php

$GLOBALS['versionNumber'] = '0.5d, June 17, 2008';

$ptr = -1;	
$connections = array();
if (is_file('.config.php')) require '.config.php';
else require "config.php";

require "source/class.dbconnection.php";

error_reporting(E_WARNING);

$currCon = file_get_contents('current-connection') - 0;
$db = new dbConnection($connections[$currCon]['host'], $connections[$currCon]['user'], $connections[$currCon]['password'], $pmiDatabase);
$db->silent = true;
$GLOBALS['utilDB'] = &$db;

// Put Ajax includer here...
if ($request = @$_POST['module'])
{
	$theFile = "source/ajax.{$_POST['module']}.php";
	if (is_file($theFile)) include $theFile;
	exit;
}

/*
	All modules have the opportunity to add to the "include array"
	Looks like this:
	$include[]
		['moduleName']
		['tabBank'] - 0|1|2 - 0 = not a tab, or bank 1 or 2.
		['tabCaption'] - if bank is 1 or 2, then a tab will be created with this name and the modulename above used for referencing GUI components of it
		['css'] - text here will be added to the header CSS
		['initJS'] - text here will be included inside the <script> tags on the body for initialization JS
		['funcJS'] - text here will be included inside the <script> tags in the header and should be functions and classes only
		['onHAdjust'] - this should be the function that is called for each module when the explorer sizer is moved
		['onResize'] - this is a function that will be called when the page is resized
		['div'] - this should be HTML that will be included on the page - like for tabs.
*/

$include = array();

require "source/module.root.php";

require "source/module.scriptfinder.php";
require "source/module.dbexplorer.php";
require "source/module.queries.php";
//require "source/module.templates.php";
require "source/module.preferences.php";

require "source/module.editor.php";
require "source/module.prompt.php";
require "source/module.phpmyadmin.php";
require "source/module.timemachine.php";
//require "source/module.optimizer.php";
//require "source/module.help.php";
if ($ajaxLog) require "source/module.ajaxlog.php";
//require "source/module.debug.php";


// Loop through all the include records and include appropriately...
$css = array();
$jsInit = array();
$jsFunc = array();

foreach($include as $item)
{
	if ($item['css'])
	{
		$css[] = <<<CSS

/*
==================================================================================
   CSS Module: {$item['moduleName']}
==================================================================================
*/
{$item['css']}
CSS;
	}
	
	if ($item['initJS'])
	{
		$jsInit[] = <<<JS
		
/*
==================================================================================
   jsInit Module: {$item['moduleName']}
==================================================================================
*/
{$item['initJS']}
JS;
	}
	
	if ($item['funcJS'])
	{
		$jsFunc[] = <<<JS
		
/*
==================================================================================
   jsFunc Module: {$item['moduleName']}
==================================================================================
*/
{$item['funcJS']}
JS;
	}
}


$cssStr = implode(chr(10), $css);
$jsInitStr = implode(chr(10), $jsInit);
$jsFuncStr = implode(chr(10), $jsFunc);


require "source/html.main.php";

echo $content;

?>
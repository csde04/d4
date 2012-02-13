<?php

$myArr = array();
$myArr['moduleName'] = 'dbexplorer';
$myArr['tabBank'] = 1;
$myArr['tabCaption'] = 'Database&nbsp;Explorer';

$myArr['css'] = <<<CSS
#dbe_Databases		{ height: 15px; width: 150px; border-style: solid; border-width: 1px; border-color: #011743; cursor: pointer; }
#dbe_Tables 		{ height: 15px; width: 200px; overflow: auto; border-style: solid; border-width: 1px; border-color: #011743; cursor: pointer; }
#dbe_Detail 		{ height: 40px; width: 100%; overflow: auto; border-style: solid; border-width: 1px; border-color: #011743; }
.dbe_Radio 		{ cursor: pointer; }
CSS;

$myArr['initJS'] = <<<JS
dbe_Ajax = new ajaxRequestor();
dbe_Ajax.url = 'main.php';
dbe_Ajax.logName = 'DBExplorer';
setTimeout('dbe_initialize()', 50);
setTimeout('dbe_Refresh()', 100);
JS;

$myArr['funcJS'] = <<<JS
function dbe_initialize()
{
	// Note that if ajaxlog window does
	dbe_Ajax.logNode = document.getElementById('ajaxlog_Window');
}

function dbe_Refresh()
{
	document.getElementById('dbe_Selector').className = 'bodyHide';
	dbe_Ajax.onSuccess = dbe_handleRefresh;
	dbe_Ajax.postParam('module', 'dbexplorer');
	dbe_Ajax.postParam('request', 'refresh');
	dbe_Ajax.execute();
}

function dbe_handleRefresh(sender)
{
	var response = eval('(' + sender.lastResponse + ')');
	var max = response.length;
	var target = document.getElementById('dbe_Databases');
	target.options.length = max;
	for (var i=0; i<max; i++)
	{
		target.options[i].text = response[i];
		target.options[i].value = response[i];
	}
	
	dbe_clearTables();
	dbe_clearDetail();
}

function dbe_clearTables() { document.getElementById('dbe_Tables').options.length = 0; }
function dbe_clearDetail() { document.getElementById('dbe_Detail').innerHTML = ''; }

function dbe_selectDatabase()
{
	document.getElementById('dbe_Selector').className = 'bodyHide';
	dbe_clearTables();
	dbe_clearDetail();
	dbe_Ajax.postParam('request', 'gettables');
	dbe_Ajax.postParam('dbname', getSelectValue('dbe_Databases'));
	dbe_Ajax.onSuccess = dbe_handleTables;
	dbe_Ajax.execute();
}
function dbe_handleTables(sender)
{
	var response = eval('(' + sender.lastResponse + ')');
	var max = response.length;
	var target = document.getElementById('dbe_Tables');
	target.options.length = max;
	for (var i=0; i<max; i++)
	{
		target.options[i].text = response[i];
		target.options[i].value = response[i];
	}	
}

function dbe_printReport(reportType)
{
	// This is different - since I want to fire up another window, I can use the ajax handler
	// but send it as a normal post and the target on the form will give me a new window...
	
	document.getElementById('dbe_form_dbname').value = getSelectValue('dbe_Databases');
	document.getElementById('dbe_form_tablename').value = getSelectValue('dbe_Tables');
	document.getElementById('dbe_form_type').value = reportType;
	document.dbe_Report.submit();
}

function dbe_selectTable()
{
	document.getElementById('dbe_Selector').className = 'bodyShow';
	dbe_clearDetail();
	dbe_Ajax.postParam('request', 'getfields');
	dbe_Ajax.postParam('dbname', getSelectValue('dbe_Databases'));
	dbe_Ajax.postParam('tablename', getSelectValue('dbe_Tables'));
	dbe_Ajax.postParam('fields', (document.getElementById('dbe_Fields').checked) ? '1' : '0');
	dbe_Ajax.onSuccess = function(sender) { document.getElementById('dbe_Detail').innerHTML = sender.lastResponse; };
	dbe_Ajax.execute();
}

function dbe_HAdjust()
{
	document.getElementById('dbe_Databases').style.height = (global_explorerHeight - 25) + 'px';
	document.getElementById('dbe_Tables').style.height = (global_explorerHeight - 25) + 'px';
	document.getElementById('dbe_Detail').style.height = (global_explorerHeight - 7) + 'px';
}

JS;

$myArr['onHAdjust'] = 'dbe_HAdjust()';

$myArr['onResize'] = '';

$myArr['div'] = <<<HTML
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="top">
	<td align="center" nowrap>
		<select id="dbe_Databases" size="5" onClick="dbe_selectDatabase()"></select><br><img src="graphics/dot_clear.gif" height="3" width="1"><br>
		<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle">
			<td><input type="button" value="Refresh" onClick="dbe_Refresh()"></td>
			<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
			<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="dbe_printReport('database')"></td>
		</tr></table>
	</td>
	<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
	<td align="center">
		<select id="dbe_Tables" size="5" onClick="dbe_selectTable()"></select><br><img src="graphics/dot_clear.gif" height="3" width="1"><br>
		<table cellpadding="0" cellspacing="0" border="0">
			<tbody id="dbe_Selector" class="bodyHide">
				<tr valign="middle" class="mainfont s10">
					<td><input type="radio" id="dbe_Fields" name="dbe_Mode" class="dbe_Radio" onClick="dbe_selectTable()" CHECKED></td>
					<td><img src="graphics/dot_clear.gif" height="1" width="3"></td>
					<td>Fields</td>
					<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
					<td><input type="radio" name="dbe_Mode" class="dbe_Radio" onClick="dbe_selectTable()"></td>
					<td><img src="graphics/dot_clear.gif" height="1" width="3"></td>
					<td>Indicies</td>
					<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>					
					<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="dbe_printReport('table')"></td>
				</tr>
			</tbody>
		</table>
	</td>
	<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
	<td width="90%"><div id="dbe_Detail"></div></td>
</tr></table>
</div>
<div style="display: none">
<form name="dbe_Report" method="POST" target="_blank">
<input type="hidden" id="dbe_form_request" name="module" value="dbexplorer">
<input type="hidden" id="dbe_form_request" name="request" value="print">
<input type="hidden" id="dbe_form_dbname" name="dbname" value="">
<input type="hidden" id="dbe_form_tablename" name="tablename" value="">
<input type="hidden" id="dbe_form_type" name="type" value="">
</form>
</div>
HTML;

$include[] = $myArr;

?>
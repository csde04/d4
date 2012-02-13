<?php


$myArr = array();
$myArr['moduleName'] = 'prompt';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'Command&nbsp;Prompt';
$myArr['css'] = <<<CSS
#command_History {
	padding-left: 11px;
	overflow: auto;
	height: 1px;
}

.command_HistoryLine {
	font-family: courier;
	font-size: 11px;
	color: #303030;
	white-space: nowrap;
}

.command_HistoryLineBold {
	font-family: courier;
	font-size: 11px;
	font-weight: bold;
	color: #011743;
	white-space: nowrap;
}

#command_Spacer {
	height: 10px;
}

#command_Line {
	height: 50px;
	font-family: courier;
	font-size: 11px;
}

#command_Input {
	font-family: courier;
	font-size: 11px;
	height: 70px;
	border-style: none;
}

#command_Cancel {
	position: absolute;
	top: 100; 
	left: 100;
	z-index: 100;
	display: none;
}

CSS;

$myArr['initJS'] = <<<JS
command_tObj = new tableManager();
command_useDatabase = '';
command_lastDateStamp = '';
command_lastID = '';
command_lastPMA = '{$connections[$currCon]['name']}';
command_cancelFlag = false;

commands = new Array();
command_Ptr = 0;

cp_Ajax = new ajaxRequestor();
cp_Ajax.logName = 'Prompt';
setTimeout('cp_initialize()', 50);

JS;

$myArr['funcJS'] = <<<JS
function cp_initialize()
{
	// Note that if ajaxlog window does
	cp_Ajax.logNode = document.getElementById('ajaxlog_Window');
}

function prompt_onDisplay()
{
	var target = document.getElementById('command_History');
	target.scrollTop = target.scrollHeight;
	command_Focus();
}

function command_addHistory(data, bold, addLine)
{
	if (addLine == 'undefined') addLine = false;
	
	data = data.replace(/\\n/g, '<br>');
	if (addLine) 
		data = data + '<br><img src="graphics/dot_clear.gif" height="7" width="1"><br>';

	var newNode = document.createElement('DIV');
	newNode.className = (bold) ? 'command_HistoryLineBold' : 'command_HistoryLine';
	newNode.innerHTML = data;
	var target = document.getElementById('command_History');
	target.appendChild(newNode);
	
	var maxHeight = ((global_windowHeight - global_explorerHeight) - 230);
	target.style.height = (maxHeight < target.scrollHeight) ? maxHeight + 'px' : (target.scrollHeight + 2) + 'px';	
	target.scrollTop = target.scrollHeight;
}

function command_Focus() { document.getElementById('command_Input').focus(); }

function command_handleShort(sender) 
{ 
	command_addHistory(sender.lastResponse, false, true);
	command_toggleInput(true);
}

function command_handleLong(sender)
{
	var parts = sender.lastResponse.match(/OK:([^:]{10}):([0-9]*$)/);
	command_lastDateStamp = parts[1];
	command_lastID = parts[2];
	var func = 'command_getChunk("' + parts[1] + '", ' + parts[2] + ')';
	setTimeout(func, 100);
}

function command_getChunk(dateStamp, id)
{
	if (command_cancelFlag)
	{
		cp_Ajax.clear();
		cp_Ajax.url = 'main.php';
		cp_Ajax.logNode = document.getElementById('ajaxlog_Window');
		cp_Ajax.onSuccess = function() {};
		cp_Ajax.postParam('module', 'prompt');
		cp_Ajax.postParam('ctype', 'cancel');
		cp_Ajax.postParam('datestamp', dateStamp);
		cp_Ajax.postParam('id', id);
		cp_Ajax.execute();
		
		command_addHistory('<i>*** SQL Cancelled ***</i>', false, true);
		command_toggleInput(true);
		
	} else {
		cp_Ajax.clear();
		cp_Ajax.url = 'main.php';
		cp_Ajax.logNode = document.getElementById('ajaxlog_Window');
		cp_Ajax.onSuccess = command_handleChunk;
		cp_Ajax.postParam('module', 'prompt');
		cp_Ajax.postParam('ctype', 'chunk');
		cp_Ajax.postParam('datestamp', dateStamp);
		cp_Ajax.postParam('id', id);
		cp_Ajax.execute();
	}
}

function command_handleChunk(sender)
{
	if (sender.lastResponse != 'ALLDONE')
	{
		if (sender.lastResponse > ' ') command_addHistory(sender.lastResponse, false, false);
		var func = 'command_getChunk("' + command_lastDateStamp + '", ' + command_lastID + ')';
		setTimeout(func, 50);
	} else {
		command_toggleInput(true);
		command_addHistory('', false, true);
	}
}

function command_handleUse(sender)
{
	// This function interrupts the normal "command response" mechanism
	// to see if we can now start using a particular DB...
	if (sender.lastResponse.match(/OK/))
		command_useDatabase = command_useAttempt;

	command_toggleInput(true);
	command_addHistory(sender.lastResponse, false, true);
}

function command_handleUseConnection(sender)
{
	// If we're here, then we've attempted to change the connection.
	// If it looks like OK: [a name] then we're good to go. If not, just
	// echo (whatever) message I got back and call it a day.
	
	command_toggleInput(true);
	var matches = sender.lastResponse.match(/OK: (.+):::(.*)$/);
	if (matches)
	{
		document.getElementById('connectionName').innerHTML = matches[1];
		dbe_Refresh();
		se_Refresh();
		pma_refresh(matches[2]);
		command_lastPMA = matches[1];
		command_addHistory('OK - connected to "' + matches[1] + '"', false, true);	
	} else {
		command_addHistory(sender.lastResponse, false, true);	
	}
}

function command_Process(data)
{
	data = trim(data);
	command_cancelFlag = false;
			
	if (data <= ' ')
	{
		command_addHistory(' ', false, true);
		return true;
	}
	
	while (commands.length > 128) commands.shift;
	if (commands[commands.length - 1] != data)
		commands.push(data);
	command_Ptr = commands.length;
	
	cp_Ajax.clear();
	cp_Ajax.url = 'main.php';
	cp_Ajax.onSuccess = ((data.match(/^(select|call) /i)) || (data.match(/;/))) ? command_handleLong : command_handleShort;
	cp_Ajax.postParam('module', 'prompt');
	
	// these are simple additions to the prompt's lexicon:
	switch(data.toLowerCase())
	{
		case 'clear':
			var target = document.getElementById('command_History');
			target.innerHTML = '';
			target.style.height = '1px';
			command_Resize();
			return true;
			
		case 'use':
			// It's a "clear database" command...
			command_useDatabase = '';
			command_addHistory(data, true);
			command_addHistory('OK', false, true);
			return true;
			
		case 'show use':
			command_addHistory(data, true);
			command_addHistory('Current Database: ' + command_useDatabase, false, true);
			return true;
			
		case 'show connect':
		case 'show connection':
			command_addHistory(data, true);
			command_addHistory(command_lastPMA, false, true);
			return true;

		case 'history':
		case 'command history':
		case 'show history':
		case 'show command history':
			command_addHistory(data, true);
			var max = commands.length;
			for (var i=0; i<max; i++) command_addHistory(commands[i], false);
			command_addHistory('', false, true);
			return true;
			
		case 'show commands':
		case 'show lexicon':
		case 'lexicon':
			command_addHistory(data, true);
			command_addHistory('clear', false);
			command_addHistory('print [sql statement]', false);
			command_addHistory('report [sql statement]', false);
			command_addHistory('show connect[ion]', false);
			command_addHistory('show connections', false);
			command_addHistory('show history', false);
			command_addHistory('show lexicon', false);
			command_addHistory('show use', false);
			command_addHistory('use', false);
			command_addHistory('use connection [# or name]', false);
			command_addHistory('', false, true);
			return true;
			
	}
	
	if (data.match(/^use connection (.+$)/i))
	{
		cp_Ajax.onSuccess = command_handleUseConnection;
	} else {
		var matches = data.match(/^use (.*$)/i);
		if (matches)
		{
			// This changes things.
			// I need to route the command success to handleUse, because
			// if it worked, then I will store the used database here and
			// throw it up with all future requests.
			command_useAttempt = matches[1];
			cp_Ajax.onSuccess = command_handleUse;
		}
	}
	
	command_toggleInput(false);
	cp_Ajax.postParam('ctype', ((data.match(/^(select|call) /i)) || (data.match(/;/))) ? 'long' : 'short');
	cp_Ajax.postParam('dbname', command_useDatabase);
	cp_Ajax.postParam('command', data);
	cp_Ajax.execute();

	command_addHistory(data, true);
}

function command_keypress(char)
{
	if (char != 13) return true;
	
	var target = document.getElementById('command_Input');
	command_Process(target.value);
	target.value = '';
	target.focus();
	setTimeout('document.getElementById("command_Input").value = ""', 20);
	return true;
}

function command_keydown(event)
{
	keyval = (event.keyCode || event.which);
	switch(keyval)
	{
		case 38:
			command_Ptr--;
			if (command_Ptr <= 0) command_Ptr = 0;
			document.getElementById('command_Input').value = commands[command_Ptr];
			break;
		case 40:
			command_Ptr++;
			if (command_Ptr >= commands.length)
			{
				command_Ptr = commands.length;
				document.getElementById('command_Input').value = '';
			} else document.getElementById('command_Input').value = commands[command_Ptr];
			break;
	}
}

function command_Resize()
{
	document.getElementById('command_History').style.width = (global_windowWidth - 45) + 'px';
	document.getElementById('command_Input').style.width = (global_windowWidth - 45) + 'px';

	var maxHeight = ((global_windowHeight - global_explorerHeight) - 230);
	
	document.getElementById('command_Container').style.height = maxHeight + 'px';
	
	var target = document.getElementById('command_History');
	target.style.height = (maxHeight < target.scrollHeight) ? maxHeight + 'px' : (target.scrollHeight + 2) + 'px';	
	target.scrollTop = target.scrollHeight;
	
	var target = document.getElementById('command_Cancel');
	target.style.left = (global_windowWidth - 80) + 'px';
	target.style.top = (global_windowHeight - 60) + 'px';
}

function command_toggleInput(state)
{
	var inputDiv = document.getElementById('command_LineBody');
	var buttonDiv = document.getElementById('command_Cancel');
	if (state)
	{
		inputDiv.className = 'bodyShow';
		buttonDiv.style.display = 'none';
		document.getElementById('command_Input').value = '';
		setTimeout('command_Focus()', 50);
	} else {
		inputDiv.className = 'bodyHide';
		buttonDiv.style.display = 'block';
	}
}

JS;

$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'command_Resize();';
$myArr['div'] = <<<HTML
<div id="command_Container" onClick="setTimeout('command_Focus()', 10)">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td colspan="2"><div id="command_History"></div></td></tr>
<tbody id="command_LineBody" class="bodyShow">
<tr valign="top">
	<td class="courier s11" style="padding-top: 0px; padding-right: 2px;" align="right">&GT;</td>
	<td><div id="command_Line"><textarea id="command_Input" onKeyPress="command_keypress(event.keyCode || event.which)" onKeyDown="command_keydown(event)"></textarea></div></td>
</tr>
</tbody>
</table>
</div>
<div id="command_Cancel"><input type="button" value="Cancel" onClick="command_cancelFlag = true;"></div>
HTML;

$include[] = $myArr;

?>
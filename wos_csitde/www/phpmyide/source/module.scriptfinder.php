<?php

$myArr = array();
$myArr['moduleName'] = 'scriptexplorer';
$myArr['tabBank'] = 1;
$myArr['tabCaption'] = 'Script&nbsp;Explorer';

$myArr['css'] = <<<CSS
#se_Databases		{ height: 15px; width: 130px; border-style: solid; border-width: 1px; border-color: #011743; cursor: pointer; }
#se_Choices		{ height: 15px; width: 150px; overflow: auto; border-style: solid; border-width: 1px; border-color: #011743 }
/* #se_Detail 		{ height: 40px; width: 100%; overflow: auto; } */
.se_Radio 		{ cursor: pointer; }
#se_normalDetail	{ width: 100%; height: 30px; border-style: solid; border-width: 1px; border-color: #011743; }
#se_triggerDetail	{ width: 100%; height: 30px;}
#se_triggerTables	{ width: 200px; overflow: auto; border-style: solid; border-width: 1px; border-color: #011743; }
#se_triggerSelect	{ width: 50px; overflow: auto; }
#se_triggerGrid		{ border-style: solid; border-color:  #011743; border-width: 1px 0px 0px 1px; }
#se_triggerGrid td	{ border-style: solid; border-color:  #011743; border-width: 0px 1px 1px 0px; }
#se_triggerGrid table td { border-style: none; }

.se_iconOff		{ height: 20px; width: 20px; cursor: default; }
.se_iconOn		{ height: 20px; width: 20px; cursor: pointer; }
.se_switchOff	{ height: 20px; width: 57px; cursor: default; }
.se_switchOn	{ height: 20px; width: 57px; cursor: pointer; }
.se_printIcon	{ height: 20px; width: 20px; cursor: pointer; padding-top: 3px; padding-right: 10px; }

#se_normalDetail	{ padding: 0px; }
.se_NormalCaption	{ padding: 0px 20px 3px 5px; font-family: verdana; font-size: 11px; text-align: left; white-space: nowrap; }
#se_normalHeader	{ background-color: #93b1ed; height: 22px; border-style: solid; border-color: #000000; border-width: 0px 0px 1px 0px; }
#se_normalHeaderFile	{ color: #000000; padding-left: 5px; font-size: 11px; text-align: left; font-weight: bold; }
#se_normalClientArea	{ overflow: auto; padding-top: 5px; }
CSS;

$myArr['initJS'] = <<<JS
se_currentMode = false;
se_Ajax = new ajaxRequestor();
se_Ajax.url = 'main.php';
se_Ajax.logName = 'ScriptExplorer';
setTimeout('se_startLogging()', 50);
setTimeout('se_Refresh()', 100);


itemsPerCol = 0;
itemArr = new Array();

se_normalCurrentName = '';
se_normalCurrentType = '';
se_normalCurrentDB = '';
se_normalCount = 0;
se_currentLatchedRow = null;

se_iconClear = new Image();
se_iconClear.src = 'graphics/dot_clear.gif';
se_iconNewTrigger = new Image();
se_iconNewTrigger.src = 'graphics/newIcon.gif';
se_iconEditTrigger = new Image();
se_iconEditTrigger.src = 'graphics/openIcon.gif';
se_iconDelTrigger = new Image();
se_iconDelTrigger.src = 'graphics/delIcon.gif';
se_iconSwitchLeft = new Image();
se_iconSwitchLeft.src = 'graphics/hSwitchLeft.gif';
se_iconSwitchRight = new Image();
se_iconSwitchRight.src = 'graphics/hSwitchRight.gif';

JS;

$myArr['funcJS'] = <<<JS
function se_HAdjust()
{
	document.getElementById('se_Databases').style.height = (global_explorerHeight - 25) + 'px';
	document.getElementById('se_Choices').style.height = (global_explorerHeight - 7) + 'px';
	document.getElementById('se_normalDetail').style.height = (global_explorerHeight - 7) + 'px';
	document.getElementById('se_normalClientArea').style.height = (global_explorerHeight - 42) + 'px';
	document.getElementById('se_triggerTables').style.height = (global_explorerHeight - 5) + 'px';
	document.getElementById('se_triggerSelect').style.height = (global_explorerHeight - 5) + 'px';
	se_rebuildNormal();
}

//function se_handleOpenTrigger(sender)
//{
//	alert(sender.lastResponse);
//}

function se_handleNew(sender)
{
	var resp = sender.lastResponse;
	if (resp.match(/^Error/))
	{
		alert(resp);
		return false;
	}
	
	var parts = resp.match(/([^\.]+)\.(.*)/);
	se_reloadFiles();
	se_unSelect();
	editor_openFile(se_currentMode, parts[1], parts[2]);
	selectTab(2, 0);
}

function se_handleNewTrigger(sender)
{
	var parts = sender.lastResponse.match(/([^\.]+)\.(.*$)/);
	editor_openFile('trigger', parts[1], parts[2]);
	selectTab(2, 0);
	se_selectTriggerTable();
}

function se_handleNormalFiles(sender)
{
//alert(sender.lastResponse);
	itemArr = eval('(' + sender.lastResponse + ')');
	se_rebuildNormal(true);
}

function se_handleRefresh(sender)
{
//alert(sender.lastResponse);
	var response = eval('(' + sender.lastResponse + ')');
	var max = response.length;
	var target = document.getElementById('se_Databases');
	target.options.length = max;
	for (var i=0; i<max; i++)
	{
		target.options[i].text = response[i];
		target.options[i].value = response[i];
	}
}

function se_handleToggle(sender)
{
	var parts = sender.lastResponse.match(/[0-9]{1}/);
	if (!sender.lastResponse.match(/[0-9]{1}/))
	{
		alert(sender.lastResponse);
		return false;
	}
	
	// The returning value was the 0..5 value of the timing row...
	var target = document.getElementById('se_switchIcon' + sender.lastResponse);
	target.src = (target.src == se_iconSwitchLeft.src) ? se_iconSwitchRight.src : se_iconSwitchLeft.src;
}

function se_handleTriggers(sender)
{
//alert(sender.lastResponse);
	var response = eval('(' + sender.lastResponse + ')');
	var ptr;
	var util = new tableManager();
	
	for(var key in response)
	{
		switch(key)
		{
			case 'before.insert':
				ptr = 0;
				break;
			case 'after.insert':
				ptr = 1;
				break;
			case 'before.update':
				ptr = 2;
				break;
			case 'after.update':
				ptr = 3;
				break;
			case 'before.delete':
				ptr = 4;
				break;
			case 'after.delete':
				ptr = 5;
				break;
		}
		
		var newTarget = document.getElementById('se_newIcon' + ptr);
		var editTarget = document.getElementById('se_openIcon' + ptr);
		var delTarget = document.getElementById('se_delIcon' + ptr);
		var switchTarget = document.getElementById('se_switchIcon' + ptr);
		var iterationTarget = document.getElementById('se_iteration' + ptr);
		var oneShotOffTarget = document.getElementById('se_oneShotOff' + ptr);
		var oneShotOnTarget = document.getElementById('se_oneShotOn' + ptr);

		if (response[key]['exists'])
		{
			newTarget.src = se_iconClear.src;
			newTarget.className = 'se_iconOff';
			util.attribute(newTarget, 'onclick', '');
			
			editTarget.src = se_iconEditTrigger.src;
			editTarget.className = 'se_iconOn';
			util.attribute(editTarget, 'onclick', 'se_triggerEdit(this)');
			
			delTarget.src = se_iconDelTrigger.src;
			delTarget.className = 'se_iconOn';
			util.attribute(delTarget, 'onclick', 'se_triggerDel(this)');
			
			switchTarget.className = 'se_switchOn';
			switchTarget.src = (response[key]['online']) ? se_iconSwitchRight.src : se_iconSwitchLeft.src;
			util.attribute(switchTarget, 'onclick', 'se_triggerToggle(this)');
			
// Until I figure out how to change the iteration, this is shut down...
//			iterationTarget.className = 'bodyShow';
//			oneShotOnTarget.checked = response[key]['oneshot'];
//			oneShotOffTarget.checked = !(response[key]['oneshot']);
			
		} else {
			newTarget.src = se_iconNewTrigger.src;
			newTarget.className = 'se_iconOn';
			util.attribute(newTarget, 'onclick', 'se_triggerNew(this)');
			
			editTarget.src = se_iconClear.src;
			editTarget.className = 'se_iconOff';
			util.attribute(editTarget, 'onclick', '');
			
			delTarget.src = se_iconClear.src;
			delTarget.className = 'se_iconOff';
			util.attribute(delTarget, 'onclick', '');
			
			switchTarget.className = 'se_switchOff';
			switchTarget.src = se_iconClear.src;
			util.attribute(switchTarget, 'onclick', '');
			
			iterationTarget.className = 'bodyHide';
		}		
	}
}

function se_handleTriggerTables(sender)
{
	var response = eval('(' + sender.lastResponse + ')');
	var max = response.length;
	var target = document.getElementById('se_triggerTables');
	target.options.length = max;
	for (var i=0; i<max; i++)
	{
		target.options[i].text = response[i];
		target.options[i].value = response[i];
	}
}

function se_normalDeleteLatched()
{
	if (!confirm('Deleting the selected item is PERMANENT. There is NO UNDO function. Are you CERTAIN that you wish to delete it?'))
		return true;
		
	var cells = se_currentlyLatchedRow.getElementsByTagName('TD');
	var fileName = cells[0].innerHTML;
	
	editor_closeFile(se_currentMode, getSelectValue('se_Databases'), fileName);

	se_Ajax.onSuccess = se_handleNormalFiles;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'delete');
	se_Ajax.postParam('type', se_currentMode);
	se_Ajax.postParam('dbname', getSelectValue('se_Databases'));
	se_Ajax.postParam('name', fileName);
	se_Ajax.execute();
	
	se_unSelect();
}

function se_normalLatch(target)
{
	var util = new tableManager();
	
	// Unlight everything except the new click...
	var base = document.getElementById('se_normalColumnTable');

	var rows = base.getElementsByTagName('TR');
	var max = rows.length;
	for (var i=1; i<max; i++)
	{
		highlightRow(rows[i], '#ffffff');
		util.attribute(rows[i], 'onmouseover', 'highlightRow(this, "#93b1ed")', 'onmouseout', 'highlightRow(this, "#ffffff")', 'onclick', 'se_normalLatch(this)');
	}

	// Highlight the target item...	
	highlightRow(target, "#93b1ed");
	util.attribute(target, 'onmouseover', '', 'onmouseout', '', 'onclick', '');
	target.style.cursor = 'default';
	
	// update the filename area of the header...
	var type = 'Unknown: ';
	switch(se_currentMode)
	{
		case 'procedure':
			type = 'Stored Procedure: ';
			break;
		case 'function':
			type = 'Stored Function: ';
			break;
		case 'view':
			type = 'View: ';
			break;
	}
	var dbStr = getSelectValue('se_Databases');
	var cells = target.getElementsByTagName('TD');
	var fileName = cells[0].innerHTML;
	document.getElementById('se_normalHeaderFile').innerHTML = type + dbStr + '.' + fileName;
	
	document.getElementById('se_normalHeaderSwitch').className = 'bodyShow';
	se_currentlyLatchedRow = target;
}

function se_normalOpenLatched()
{
	var cells = se_currentlyLatchedRow.getElementsByTagName('TD');
	var fileName = cells[0].innerHTML;
	
	var dbName = getSelectValue('se_Databases');

	editor_openFile(se_currentMode, dbName, fileName);

	selectTab(2, 0)
}

function se_normalNew()
{

	var newName = '';
	while(newName <= ' ')
	{
		var newName = prompt('Enter a new ' + se_currentMode + ' name or cancel', 'new' + se_currentMode + 'name');
		if (!newName) return false;
		
		if (newName.match(/[^A-Z0-9_]/i))
		{
			alert('"' + newName + '" is not a valid ' + se_currentMode + ' name - please use only letters and numbers');
			newName = '';
		}
	}
	
	se_Ajax.onSuccess = se_handleNew;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'new');
	se_Ajax.postParam('type', se_currentMode);
	se_Ajax.postParam('dbname', getSelectValue('se_Databases'));
	se_Ajax.postParam('name', newName);
	se_Ajax.execute();	
	
}

function se_printReport(reportType)
{
	// This is different - since I want to fire up another window, I can use the ajax handler
	// but send it as a normal post and the target on the form will give me a new window...
	
	document.getElementById('se_form_dbname').value = getSelectValue('se_Databases');
	document.getElementById('se_form_type').value = reportType;
	document.se_Report.submit();
}

function se_rebuildNormal(force)
{
	if (force == 'undefined') force = false;
	
	var newIPC = Math.floor((global_explorerHeight - 30) / 17);
	if ((newIPC != itemsPerCol) || force)
	{
		var i;
		var j;
		
		itemsPerCol = newIPC;
		
		var outside = new tableManager(0, 0, 0, 10);
		var thisRow = outside.newRow();
		outside.attribute(thisRow, 'valign', 'top');
		
		i = 0;
		rowTemplate = document.getElementById('se_normalRowTemplate');
		spaceTemplate = document.getElementById('se_normalSpaceTemplate');
		
		while (i < itemArr.length)
		{
			var thisCol = new tableManager(0, 0, 0, 10);
			for (j=0; j<itemsPerCol; j++)
			{
				var thisRow = thisCol.newRow();
				thisCol.attribute(thisRow, 'bgcolor', '#ffffff', 'onmouseover', 'highlightRow(this, "#93b1ed")', 'onmouseout', 'highlightRow(this, "#ffffff")', 'onclick', 'se_normalLatch(this)', 'onDblClick', 'se_normalOpenLatched()');
				var thisCell = thisCol.newTextCell(itemArr[i]);
				thisCell.className = 'se_NormalCaption';
				if (++i >= itemArr.length) break;
			}
			var thisCell = outside.newCell();
			thisCol.addTableTo(thisCell);
			
			var thisImg = document.createElement('IMG');
			outside.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '1', 'width', '10');
			outside.newCellContent(thisImg);

		}
			
		var target = document.getElementById('se_normalClientArea');
		target.innerHTML = '';
		var tableHandle = outside.addTableTo(target);
		outside.attribute(tableHandle, 'id', 'se_normalColumnTable');
		se_currentlyLatchedRow = target;

	}
}

function se_Refresh()
{
	se_showChoices(false);
	document.getElementById('se_Databases').options.length = 0;
	document.getElementById('se_triggerTables').options.length = 0;
	document.getElementById('se_triggerGrid').style.display = 'none';

	document.getElementById('se_normalDetail').style.display = 'none';
	document.getElementById('se_triggerDetail').style.display = 'none';
	
	se_Ajax.onSuccess = se_handleRefresh;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'refresh');
	se_Ajax.execute();
}

function se_reloadFiles()
{
	var dbname = getSelectValue('se_Databases');
	switch(se_currentMode)
	{
		case 'procedure':
			se_Ajax.onSuccess = se_handleNormalFiles;
			se_Ajax.postParam('module', 'scriptexplorer');
			se_Ajax.postParam('request', 'getprocedures');
			se_Ajax.postParam('dbname', dbname);
			se_Ajax.execute();
			break;
		
		case 'function':
			se_Ajax.onSuccess = se_handleNormalFiles;
			se_Ajax.postParam('module', 'scriptexplorer');
			se_Ajax.postParam('request', 'getfunctions');
			se_Ajax.postParam('dbname', dbname);
			se_Ajax.execute();
			break;
					
		case 'view':
			se_Ajax.onSuccess = se_handleNormalFiles;
			se_Ajax.postParam('module', 'scriptexplorer');
			se_Ajax.postParam('request', 'getviews');
			se_Ajax.postParam('dbname', dbname);
			se_Ajax.execute();
			break;
			
		case 'trigger':
			se_reloadTriggerTables();
			break;
			
		default:
			alert('Where am I? ' + se_currentMode);
	}
}

function se_reloadTriggerTables()
{
	document.getElementById('se_triggerTables').options.length = 0;
	document.getElementById('se_triggerGrid').style.display = 'none';
	
	var theDB = getSelectValue('se_Databases');
	
	se_Ajax.onSuccess = se_handleTriggerTables;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'gettriggertables');
	se_Ajax.postParam('dbname', theDB);
	se_Ajax.execute();	
}

function se_Resize()
{
	document.getElementById('se_triggerSelect').style.width = (global_windowWidth - 530) + 'px';
	document.getElementById('se_normalDetail').style.width = (global_windowWidth - 328) + 'px';
}

function se_selectDatabase()
{
	document.getElementById('se_normalDetail').style.display = 'none';
	document.getElementById('se_triggerDetail').style.display = 'none';
	document.getElementById('se_normalClientArea').innerHTML = '';

	se_showChoices(true);
	document.getElementById('se_triggerGrid').style.display = 'none';
	document.getElementById('se_triggerTables').options.length = 0;
	for (var i=1; i<5; i++)
		document.getElementById('se_scriptType' + i).checked = false;
}

function se_selectTriggerTable()
{
	document.getElementById('se_triggerGrid').style.display = 'block';
	
	var theDB = getSelectValue('se_Databases');
	var theTable = getSelectValue('se_triggerTables');
	
	se_Ajax.onSuccess = se_handleTriggers;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'gettriggers');
	se_Ajax.postParam('dbname', theDB);
	se_Ajax.postParam('tablename', theTable);
	se_Ajax.execute();	
}

function se_selectTypeManual(typeStr)
{
	for(var i=0; i<5; i++)
		document.getElementById('se_scriptType' + i).checked = false;
	
	var target;
	switch(typeStr)
	{
		case 'procedure':
			target = document.getElementById('se_scriptType1');
			target.checked = true;
			break;
			
		case 'function':
			target = document.getElementById('se_scriptType2');
			target.checked = true;
			break;
			
		case 'view':
			target = document.getElementById('se_scriptType3');
			target.checked = true;
			break;
			
		case 'trigger':
			target = document.getElementById('se_scriptType4');
			target.checked = true;
			break;
	}
	se_selectType(target);
}

function se_selectType(sender)
{
	var theType = sender.value;
	se_currentMode = theType;
	switch(se_currentMode)
	{
		case 'procedure':
			document.getElementById('se_normalDetail').style.display = 'block';
			document.getElementById('se_triggerDetail').style.display = 'none';
			se_reloadFiles();
			break;
		
		case 'function':
			document.getElementById('se_normalDetail').style.display = 'block';
			document.getElementById('se_triggerDetail').style.display = 'none';
			se_reloadFiles();
			break;
			
		case 'view':
			document.getElementById('se_normalDetail').style.display = 'block';
			document.getElementById('se_triggerDetail').style.display = 'none';
			se_reloadFiles();
			break;

		case 'trigger':
			document.getElementById('se_normalDetail').style.display = 'none';
			document.getElementById('se_triggerDetail').style.display = 'block';
			se_reloadTriggerTables();
			break;
	}
}

function se_showChoices(state) { document.getElementById('se_choices').className = (state) ? 'bodyShow' : 'bodyHide'; }

function se_startLogging()
{
	// If the node is null, no logging will occur...
	se_Ajax.logNode= document.getElementById('ajaxlog_Window');
}

function se_triggerTimingConvert(node)
{
	switch(node.id.match(/[0-9]{1}/) - 0)
	{
		case 0: return 'before.insert';
		case 1: return 'after.insert';
		case 2: return 'before.update';
		case 3: return 'after.update';
		case 4: return 'before.delete';
		case 5: return 'after.delete';
	}
}

function se_triggerNew(sender)
{
	var timing = se_triggerTimingConvert(sender);
	se_Ajax.onSuccess = se_handleNewTrigger;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'newtrigger');
	se_Ajax.postParam('dbname', getSelectValue('se_Databases'));
	se_Ajax.postParam('table', getSelectValue('se_triggerTables'));
	se_Ajax.postParam('timing', timing);
	se_Ajax.execute();
}

function se_triggerEdit(sender)
{
	var timing = se_triggerTimingConvert(sender);
	editor_openFile('trigger', getSelectValue('se_Databases'), getSelectValue('se_triggerTables') + '.' + timing);
	selectTab(2, 0);	
}

function se_triggerDel(sender)
{
	if (!confirm('Delete this trigger? There is NO UNDO command for this... are you SURE?')) return false;
	
	var timing = se_triggerTimingConvert(sender);
	editor_closeFile(se_currentMode, getSelectValue('se_Databases'), getSelectValue('se_triggerTables') + '.' + timing);

	se_Ajax.onSuccess = se_handleTriggers;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'deletetrigger');
	se_Ajax.postParam('dbname', getSelectValue('se_Databases'));
	se_Ajax.postParam('table', getSelectValue('se_triggerTables'));
	se_Ajax.postParam('timing', timing);
	se_Ajax.execute();	
	
}

function se_triggerToggle(sender)
{
	var timing = se_triggerTimingConvert(sender);

	// It doesn't matter which state the switch is in, just toggle it...
	se_Ajax.onSuccess = se_handleToggle;
	se_Ajax.postParam('module', 'scriptexplorer');
	se_Ajax.postParam('request', 'toggletrigger');
	se_Ajax.postParam('dbname', getSelectValue('se_Databases'));
	se_Ajax.postParam('table', getSelectValue('se_triggerTables'));
	se_Ajax.postParam('timing', timing);
	se_Ajax.execute();
}

function se_unSelect() 
{
	document.getElementById('se_normalHeaderSwitch').className = 'bodyHide';
}

JS;

$myArr['onHAdjust'] = 'se_HAdjust()';

$myArr['onResize'] = 'se_Resize()';

$myArr['div'] = <<<HTML
<table cellpadding="0" cellspacing="0" border="0"><tr valign="top">
	<td align="center">
		<select id="se_Databases" size="5" onClick="se_selectDatabase()"></select><img src="graphics/dot_clear.gif" height="3" width="1"><br>
		<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle">
			<td><input type="button" value="Refresh" onClick="se_Refresh()"></td>
			<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
			<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="se_printReport('database')"></td>
		</tr></table>
	</td>
	<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
	<td align="left">
		<div id="se_Choices">
			<table cellpadding="0" cellspacing="0" border="0">
				<tbody id="se_choices" class="bodyHide">
					<tr valign="middle" class="mainfont s10">
						<td><input name="se_scriptType" type="radio" id="se_scriptType1" value="procedure" class="se_Radio" onClick="se_selectType(this)"></td>
						<td width="90%" align="left" nowrap>Procedures</td>
						<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="se_printReport('procedures')"></td>
					</tr>
					<tr valign="middle" class="mainfont s10">
						<td><input name="se_scriptType" type="radio" id="se_scriptType2" value="function" class="se_Radio" onClick="se_selectType(this)"></td>
						<td align="left" nowrap>Functions</td>
						<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="se_printReport('functions')"></td>
					</tr>
					<tr valign="middle" class="mainfont s10">
						<td><input name="se_scriptType" type="radio" id="se_scriptType4" value="trigger" class="se_Radio" onClick="se_selectType(this)"></td>
						<td align="left" nowrap>Triggers</td>
						<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="se_printReport('triggers')"></td>
					</tr>
					<tr valign="middle" class="mainfont s10">
						<td><input name="se_scriptType" type="radio" id="se_scriptType3" value="view" class="se_Radio" onClick="se_selectType(this)"></td>
						<td align="left" nowrap>Views</td>
						<td><img src="graphics/icon_Print.gif" class="se_printIcon" onClick="se_printReport('views')"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</td>
	<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
	<td width="90%" align="left">
	
	
		<div id="se_normalDetail" style="display: none">
			<div id="se_normalHeader">
				<table cellpadding="0" cellspacing="0" border="0"><tr valign"middle">
					<td width="10"><img src="graphics/dot_clear.gif" height="22" width="5"></td>
					<td width="16"><img id="se_normalNewDoc" src="graphics/icon_NewDoc.gif" height="16" width="16" onMouseOver="highlightCell(this, '#011743'); Tip('New...')" onMouseOut="highlightCell(this, 'transparent')" onClick="se_normalNew()"></td>
					<td>					
						<table cellpadding="0" cellspacing="0" border="0">
							<tbody id="se_normalHeaderSwitch" class="bodyHide">
							<tr valign="middle">
							<td width="10"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
							<td width="16"><img id="se_normalEdit" src="graphics/icon_edit.gif" height="16" width="16" onMouseOver="highlightCell(this, '#011743'); Tip('Open/Edit Selected')" onMouseOut="highlightCell(this, 'transparent')" onClick="se_normalOpenLatched()"></td>
							<td width="10"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
							<td width="16"><img id="se_normalDelete" src="graphics/icon_delete.gif" height="16" width="16" onMouseOver="highlightCell(this, '#011743'); Tip('Delete Selected')" onMouseOut="highlightCell(this, 'transparent')" onClick="se_normalDeleteLatched()"></td>
							<td width="5"><img src="graphics/dot_clear.gif" height="1" width="5"></td>
							<td width="99%"><div id="se_normalHeaderFile">No File Open</div></td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr></table>
			</div>
			<div id="se_normalClientArea"></div>
		</div>
		
		
		<div id="se_triggerDetail" style="display: none">
			<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="top">
				<td><select id="se_triggerTables" size="5" onClick="se_selectTriggerTable()"></select></td>
				<td><img src="graphics/dot_clear.gif" height="1" width="10"></td>
				<td align="left"><div id="se_triggerSelect">
					<table id="se_triggerGrid" cellpadding="4" cellspacing="0" border="0" style="display: none;">
					
						<tr class="mainFont s11 bold" valign="middle" style="background-color: #011743; color: #ffffff;">
							<td align="left"nowrap>&nbsp;When&nbsp;&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;Off / On</td>
							<td>&nbsp;Iteration</td>
							
						</tr>					
					
						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>Before Insert&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon0"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon0"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon0"></td>
							<td height="20"><img id="se_switchIcon0" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration0" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot0" id="se_oneShotOff0"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot0" id="se_oneShotOn0"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
									<tr><td colspan="7" class="mainfont s11 normal italic" style="color: #909090">Trigger iteration modes are not currently supported</td></tr>
								</table>
							</td>
						</tr>

						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>After Insert&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon1"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon1"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon1"></td>
							<td height="20"><img id="se_switchIcon1" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration1" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot1" id="se_oneShotOff1"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot1" id="se_oneShotOn1"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>Before Update&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon2"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon2"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon2"></td>
							<td height="20"><img id="se_switchIcon2" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration2" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot2" id="se_oneShotOff2"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot2" id="se_oneShotOn2"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>After Update&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon3"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon3"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon3"></td>
							<td height="20"><img id="se_switchIcon3" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration3" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot3" id="se_oneShotOff3"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot3" id="se_oneShotOn3"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						
						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>Before Delete&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon4"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon4"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon4"></td>
							<td height="20"><img id="se_switchIcon4" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration4" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot4" id="se_oneShotOff4"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot4" id="se_oneShotOn4"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						
						<tr valign="middle">
							<td class="mainFont s11 bold" align="left" width="30%" nowrap>After Delete&nbsp;&nbsp;&nbsp;</td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_newIcon5"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_openIcon5"></td>
							<td width="20" height="20"><img src="graphics/dot_clear.gif" class="se_iconOff" id="se_delIcon5"></td>
							<td height="20"><img id="se_switchIcon5" src="graphics/dot_clear.gif" class="se_switchOff"></td>
							<td width="50%" align="left" valign="middle">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody id="se_iteration5" class="bodyHide">
										<tr valign="middle">
											<td><input type="radio" name="se_oneShot5" id="se_oneShotOff5"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>For Each Row</td>
											<td><img src="graphics/dot_clear.gif" height="1" width="20"></td>
											<td><input type="radio" name="se_oneShot5" id="se_oneShotOn5"></td>
											<td><img src="graphics/dot_clear.gif" height="1" width="5"></td>
											<td class="mainfont s10" nowrap>Once Per Event</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						
					</table>
				</div></td>
			</tr></table>
		</div>
	</td>
</tr></table>

<div style="display: none">
<table cellpadding="0" cellspacing="0" border="0">
<tr id="se_normalRowTemplate" valign="middle" onMouseOver="highlightRow(this, '#93b1ed')" onMouseOut="highlightRow(this, 'transparent')" onClick="se_normalLatch(this)">
	<td><div class="se_NormalCaption">AAA</div></td>
</tr>
</table>
</div>
<div style="display: none">
<form name="se_Report" method="POST" target="_blank">
<input type="hidden" id="se_form_request" name="module" value="scriptexplorer">
<input type="hidden" id="se_form_request" name="request" value="print">
<input type="hidden" id="se_form_dbname" name="dbname" value="">
<input type="hidden" id="se_form_type" name="type" value="">
</form>
</div>
HTML;

$include[] = $myArr;

?>
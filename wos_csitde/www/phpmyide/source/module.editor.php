<?php

$myArr = array();
$myArr['moduleName'] = 'editor';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'Editor&nbsp;Window';
$myArr['css'] = <<<CSS
#editor_Window { font-family: courier; }
#editor_Files { width: 250px; overflow: auto; border-style: solid; border-color: #000000; border-width: 0px 0px 0px 1px; padding-left: 5px; }
.editor_FilesCategory { padding-top: 10px; margin-left: 0px; font-weight: bold; font-size: 12px; border-style: solid; border-color: #000000; border-width: 0px 0px 1px 0px; }
.editor_FilesItem { margin-left: 0px; font-weight: normal; font-size: 11px; }
.editor_FilesItemContainer { padding-top: 1px; }
#editor_buttonHeader { border-style: solid; border-width: 1px 2px 2px 1px; border-color: #000000; background-color: #d0d0d0; padding: 3px 0px 3px 0px; }
#editor_FilesSpacer { height: 10px; }
.editor_Icon { height: 20px; width: 20px; }
.editor_IconSpacer { height; 1px; width: 10px; }
CSS;

$myArr['initJS'] = <<<JS
editor_Current = null;

ed_Ajax = new ajaxRequestor();
ed_Ajax.url = 'main.php';
ed_Ajax.logName = 'Editor';
setTimeout('ed_initialize()', 50);

editRecords = new Array();
editor_currentItem = null;
editor_compareBuff = '';
editor_inhibitClick = false;
editor_currentIsDirty = 1;
editor_currentName = '';
editor_scratchPadCount = 0;

setInterval('editor_checkDirty()', 1000);

iconDirty = new Image(6, 6);
iconDirty.src = 'graphics/icon_Dirty.gif';
iconClear = new Image();
iconClear.src = 'graphics/dot_clear.gif';
iconSave = new Image(20, 20);
iconSave.src = 'graphics/icon_Save.gif';
iconRevert = new Image(20, 20);
iconRevert.src = 'graphics/icon_Revert.gif';
iconSaveAs = new Image(20, 20);
iconSaveAs.src = 'graphics/icon_SaveAs.gif';
iconPrint = new Image(20, 20);
iconPrint.src = 'graphics/icon_Print.gif';

JS;

$myArr['funcJS'] = <<<JS
function editor_onDisplay()
{
	document.getElementById('editor_Window').focus();
}

function ed_initialize()
{
	// Note that if ajaxlog window does
	ed_Ajax.logNode = document.getElementById('ajaxlog_Window');
	document.getElementById('editor_Window').value = '';
}

function editor_allowTabs(e)
{
	var key = (e.keyCode || e.which);
	if (key == 9) 
	{ 
		var target = document.getElementById('editor_Window');
		replaceText('\t', target);
		target.focus();
		setTimeout('editor_focus()', 10);
		if (e.returnValue) e.returnValue = false; // ie
		if (e.preventDefault) e.preventDefault(); // other DOM
		return false;
	}
}

function editor_checkDirty()
{
	// If we already know that the current file is dirty then don't worry about it...
	if ((editor_currentIsDirty == 1) || (editor_currentName.match(/^scratchpad\.[0-9]{6}/))) return true;

	if (editor_compareBuff != document.getElementById('editor_Window').value)
	{
		editor_setDirty(editor_currentItem, true);
		editor_currentIsDirty = 1;
	}
}

function editor_close(target)
{
	// if dirty, make sure they want to close first...
	
	// First off... the thing that was clicked here MAY be the close image, not the row-item itself...
	if (target.nodeName == 'IMG')
	{
		while (target.nodeName != 'TR')
			target = target.parentNode;
	}

	// First: is the currently open file the one that I am closing?
	if (target == editor_currentItem)
	{
		if ((editor_currentIsDirty) && (!editor_currentName.match(/scratchpad\.[0-9]{6}/)))
		{
			if (!confirm('Script has not been saved! If you close it now you will lose your changes. Close anyway?'))
				return false;
		}
	
		editor_putAway(target);
		editor_currentItem = null;
		editor_compareBuff = '';
		editor_currentIsDirty = true
		document.getElementById('editor_Window').value = '';
	
		var util = new tableManager();
		
		// If we're shutting down the currently selected ones, it messes with our save icons and I need to address that...
		for(var i=0; i<4; i++)
			editor_setButton(i, false);
		
	}
	
	// Now find the target row's div container...
	while (target.nodeName != 'DIV')
		target = target.parentNode;
		
	// OK - I am pointing that the containing div for <this> item... move upward one more time to get the containing div...
	parent = target.parentNode;
	parent.removeChild(target);
	
	editor_inhibitClick = true;
}

function editor_closeFile(filetype, dbname, filename)
{
	var thisName = filetype + '.' + dbname + '.' + filename;

	// Walk the open files array and see if it is open ... if it is, then close it!
	var tables = document.getElementById('editor_Files').getElementsByTagName('TABLE');
	var max = tables.length;
	for(var i=0; i<max; i++)
	{
		var inputs = tables[i].getElementsByTagName('INPUT');
		if (inputs[0].value == thisName)
		{
			// Gotcha!
			var rows = tables[i].getElementsByTagName('TR');
			editor_close(rows[0]);
			return true;
		}
	}	
}

function editor_execOpenFileGroup()
{
	alert('Sorry - file groups are not supported yet.');
}

function editor_execPrint()
{
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	document.getElementById('editor_form_type').value = inputs[1].value;
	document.getElementById('editor_form_dbname').value = inputs[2].value;
	document.getElementById('editor_form_name').value = inputs[3].value;
	document.getElementById('editor_form_buffer').value = document.getElementById('editor_Window').value;
	document.editor_printJob.submit();
}

function editor_execRevert()
{
	document.getElementById('editor_Window').value = editor_compareBuff;
	editor_setButton(0, false);
	editor_setButton(3, false);
	
	editor_currentIsDirty = false;
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	inputs[6].value = inputs[5].value;
	inputs[4] = 0;
	editor_setDirty(editor_currentItem, false);
}

function editor_execSave()
{
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	var t = inputs[1].value;
	var d = inputs[2].value;
	var n = inputs[3].value;
	
	var buff = document.getElementById('editor_Window').value;
	buff = buff.replace(/\+/g, '[%PLUS%]');
	
	ed_Ajax.postParam('module', 'editor');
	ed_Ajax.postParam('request', 'save');
	ed_Ajax.postParam('type', t);
	ed_Ajax.postParam('dbname', d);
	ed_Ajax.postParam('name', n);
	ed_Ajax.postParam('buffer', buff);
	ed_Ajax.onSuccess = editor_handleSave;
	ed_Ajax.execute();

}

function editor_execSaveAs()
{
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	var t = inputs[1].value;
	var d = inputs[2].value;
	var n = inputs[3].value;
		
	var newName = prompt('Save current ' + t + ' as: ', d + '.' + n);
	if (!newName) return false;
	
	var parts = newName.match(/([^\.]+)\.(.*)/);
	
	var buff = document.getElementById('editor_Window').value;
	buff = buff.replace(/\+/g, '[%PLUS%]');
	
	ed_Ajax.postParam('module', 'editor');
	ed_Ajax.postParam('request', 'saveas');
	ed_Ajax.postParam('type', t);
	ed_Ajax.postParam('dbname', parts[1]);
	ed_Ajax.postParam('name', parts[2]);
	ed_Ajax.postParam('buffer', buff);
	ed_Ajax.onSuccess = editor_handleSaveAs;
	ed_Ajax.execute();
}

function editor_execSaveFileGroup()
{
	alert('Sorry - file groups are not supported yet.');
}

function editor_focus() { document.getElementById('editor_Window').focus(); }

function editor_handleRetrieve(sender)
{
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	inputs[4].value = 0;
	inputs[5].value = sender.lastResponse;
	inputs[6].value = sender.lastResponse;
	document.getElementById('editor_Window').value = sender.lastResponse;
	editor_compareBuff = sender.lastResponse;
	editor_currentIsDirty = 0;
	
	// We need to make sure that the saveas and print images are loaded...
	editor_setButton(1, true);
	editor_setButton(2, true);	
}

function editor_handleSave(sender)
{
//alert(sender.lastResponse);
	if (sender.lastResponse != 'OK')
	{
		alert(sender.lastResponse);
		return false;
	}
	
	var buff = document.getElementById('editor_Window').value;
	var inputs = editor_currentItem.getElementsByTagName('INPUT');
	inputs[5] = buff;
	inputs[6] = buff;
	inputs[4] = 0;
	editor_setButton(0, false);
	editor_setButton(3, false);
	editor_compareBuff = buff;
	document.getElementById('editor_Window').value = buff;
	editor_currentIsDirty = false;
	editor_setDirty(editor_currentItem, false);
}

function editor_handleSaveAs(sender)
{
	var parts = sender.lastResponse.match(/^([A-Z0-9_]+)\.([A-Z0-9_]+)\.([A-Z0-9_]+)$/i);
	if (!parts)
	{
		alert('Error: ' + sender.lastResponse);
		return false;
	}
	
	// Tricky: move the script explorer to what was just saved...
	selectSelectValue(document.getElementById('se_Databases'), parts[2]);
	se_selectDatabase();
	se_selectTypeManual(parts[1]);
	se_unSelect();
	editor_openFile(parts[1], parts[2], parts[3]);
}

function editor_newScratchPad()
{
	var t = 'scratchpad';
	var d = 'none';
	editor_scratchPadCount++;
	var temp = '000000' + editor_scratchPadCount;
	var n = temp.match(/[0-9]{6}$/);
	var thisName = t + '.' + n;
	editor_currentName = thisName;

	var tObj = new tableManager(0, 0, 0, '100%');
	var thisRow = tObj.newRow();
	tObj.attribute(thisRow, 'valign', 'middle');
	
	// This forces the table to be 2 px larger than the delete button...
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '16', 'width', '3');
	var thisCell = tObj.newCellContent(thisImg);
	
	var temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', thisName);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', t);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', d);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', n);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', 0); // dirty
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', ''); // original buffer
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', ''); // working buffer
	thisCell.appendChild(temp);
	
	
	// Now onto the normal stuff.. spacing images and such...
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '6', 'width', '6');
	tObj.newCellContent(thisImg);
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '1', 'width', '5');
	tObj.newCellContent(thisImg);
	
	var thisCell = tObj.newTextCell(thisName);
	tObj.attribute(thisCell, 'width', '99%');
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/icon_SmallClose.gif', 'height', '14', 'width', '14', 'onclick', 'editor_close(this)', 'onmouseover', 'Tip("Close")');
	tObj.newCellContent(thisImg);
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '1', 'width', '3');
	tObj.newCellContent(thisImg);
	
	// Create a new div to contain the table...
	var newDiv = document.createElement('DIV');
	newDiv.className = 'editor_FilesItemContainer';
	tObj.attribute(newDiv, 'width', '100%');
	
	// Add the table to the new div...
	var thisTable = tObj.addTableTo(newDiv);

	var target = document.getElementById('editor_Scratchpads');
	var divs = target.getElementsByTagName('DIV');
	target.appendChild(newDiv);

	editor_putAway(editor_currentItem);
	
	editor_currentItem = thisRow;
	editor_currentIsDirty = 1;
	editor_compareBuff = '';
	document.getElementById('editor_Window').value = '';
	
	for (var i=0; i<4; i++)
		editor_setButton(i, false);
	
	highlightRow(thisRow, "#93b1ed");
}

function editor_openFile(t, d, n)
{

	var thisName = t + '.' + d + '.' + n
	editor_currentName = thisName;
	
	// First - walk the open files array and see if it is already open ... if it is, simply select it...
	var tables = document.getElementById('editor_Files').getElementsByTagName('TABLE');
	var max = tables.length;
	for(var i=0; i<max; i++)
	{
		var inputs = tables[i].getElementsByTagName('INPUT');
		if (inputs[0].value == thisName)
		{
			// Gotcha!
			editor_putAway(editor_currentItem);
			
			var rows = tables[i].getElementsByTagName('TR');
			editor_select(rows[0]);
			return true;
		}
	}

	// Throw the request to open the file...
	ed_Ajax.postParam('module', 'editor');
	ed_Ajax.postParam('request', 'retrieve');
	ed_Ajax.postParam('type', t);
	ed_Ajax.postParam('dbname', d);
	ed_Ajax.postParam('name', n);
	ed_Ajax.onSuccess = editor_handleRetrieve;
	ed_Ajax.execute();
	
	// Create the editorFiles table...
	var tObj = new tableManager(0, 0, 0, '100%');
	var thisRow = tObj.newRow();
	tObj.attribute(thisRow, 'valign', 'middle');
	
	// This forces the table to be 2 px larger than the delete button...
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '16', 'width', '3');
	var thisCell = tObj.newCellContent(thisImg);
	
	/*
		This looks funky, but I am appending a hidden form elements to this cell that contains everything about what <I> am...
		The positions I use for values are:
		0: t.d.n
		1: type
		2: dbname
		3: filename - in the case a trigger, this will be (table).before.update
		4: dirty flag
		5: original buffer
		6: working buffer
	*/
	var temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', thisName);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', t);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', d);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', n);
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', 0); // dirty
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', ''); // original buffer
	thisCell.appendChild(temp);
	temp = document.createElement('INPUT');
	tObj.attribute(temp, 'type', 'hidden', 'value', ''); // working buffer
	thisCell.appendChild(temp);
	
	
	// Now onto the normal stuff.. spacing images and such...
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '6', 'width', '6');
	tObj.newCellContent(thisImg);
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '1', 'width', '5');
	tObj.newCellContent(thisImg);
	
	var thisCell = tObj.newTextCell(d + '.' + n);
	tObj.attribute(thisCell, 'width', '99%');
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/icon_SmallClose.gif', 'height', '14', 'width', '14', 'onclick', 'editor_close(this)', 'onmouseover', 'Tip("Close")');
	tObj.newCellContent(thisImg);
	
	var thisImg = document.createElement('IMG');
	tObj.attribute(thisImg, 'src', 'graphics/dot_clear.gif', 'height', '1', 'width', '3');
	tObj.newCellContent(thisImg);
	
	// Create a new div to contain the table...
	var newDiv = document.createElement('DIV');
	newDiv.className = 'editor_FilesItemContainer';
	tObj.attribute(newDiv, 'width', '100%');
	
	// Add the table to the new div...
	var thisTable = tObj.addTableTo(newDiv);
	
	switch(t)
	{
		case 'procedure':
			var target = document.getElementById('editor_FilesProcedures');
			break;
			
		case 'function':
			var target = document.getElementById('editor_FilesFunctions');
			break;

		case 'trigger':
			var target = document.getElementById('editor_FilesTriggers');
			break;
			
		case 'view':
			var target = document.getElementById('editor_FilesViews');
			break;
			
	}
		
	var divs = target.getElementsByTagName('DIV');
	var max = divs.length;
	var beenInserted = false;
	for (var i=0; i<max; i++)
	{
		var inputs = divs[i].getElementsByTagName('INPUT');
		if (inputs[0].value > thisName)
		{
			// Gotcha! Insert my new node here...
			target.insertBefore(newDiv, divs[i]);
			beenInserted = true;
			break;
		}
	}
	if (!beenInserted)
	{
		// The name is greater than any other in the list, put it at the end...
		target.appendChild(newDiv);
	}
	
	
	editor_putAway(editor_currentItem);
	
	editor_currentItem = thisRow;
	editor_currentIsDirty = 1;
	editor_compareBuff = '';
	document.getElementById('editor_Window').value = '';
	
	editor_setButton(0, false);
	editor_setButton(3, false);
	
	highlightRow(thisRow, "#93b1ed");
}

function editor_putAway(target)
{
	if (!editor_currentItem) return true;
	
	// This grabs the buffer that (I) represent and stores it...
	var tObj = new tableManager();
	highlightRow(target, 'transparent');
	tObj.attribute(target, 'onmouseover', 'highlightRow(this, "#93b1ed")', 'onmouseout', 'highlightRow(this, "#ffffff")', 'onclick', 'editor_select(this)');
	var elements = target.getElementsByTagName('INPUT');
	elements[6].value = document.getElementById('editor_Window').value;
}

function editor_resize()
{
	document.getElementById('editor_Window').style.height = ((global_windowHeight - global_explorerHeight) - 157) + 'px';
	document.getElementById('editor_Window').style.width = (global_windowWidth - 295) + 'px';
	document.getElementById('editor_Files').style.height = ((global_windowHeight - global_explorerHeight) - 194) + 'px';
}

function editor_select(target)
{
	if (editor_inhibitClick)
	{
		editor_inhibitClick = false;
		return true;
	}
	
	var util = new tableManager();
	
	if (editor_currentItem)
	{
		highlightRow(editor_currentItem, 'transparent');
		util.attribute(editor_currentItem, 'onmouseover', 'highlightRow(this, "#93b1ed")', 'onmouseout', 'highlightRow(this, "#ffffff")', 'onclick', 'editor_select(this)');
		editor_putAway(editor_currentItem);
	}
	
	util.attribute(target, 'onmouseover', '', 'onmouseout', '', 'onclick', '');
	highlightRow(target, "#93b1ed");
	
	var inputs = target.getElementsByTagName('INPUT');
	var thisName = inputs[0].value;
	document.getElementById('editor_Window').value = inputs[6].value;
	editor_currentItem = target;
	editor_compareBuff = inputs[5].value;
	editor_currentIsDirty = inputs[4].value;
	editor_currentName = thisName;
	
	if (thisName.match(/scratchpad\.[0-9]{6}/))
	{
		for (var i=0; i<4; i++)
			editor_setButton(i, false);
	} else {
		// Make sure the SaveAs and Print buttons are on...
		editor_setButton(1, true);
		editor_setButton(2, true);
		
		// Turn on/off the "save" button...
		editor_setButton(0, (editor_currentIsDirty == 1));
		editor_setButton(3, (editor_currentIsDirty == 1));	
	}
}

function editor_setButton(which, state)
{
	var tname;
	var source;
	var func;
	var tip;
	var util = new tableManager();

	switch(which)
	{
		case 0:
			tname = 'image_iconSave';
			source = iconSave;
			func = 'editor_execSave()';
			tip = 'Save Current';
			break;
		case 1:
			tname = 'image_iconSaveAs';
			source = iconSaveAs;
			func = 'editor_execSaveAs()';
			tip = 'Save Current As...';
			break;
		case 2:
			tname = 'image_iconPrint';
			source = iconPrint;
			func = 'editor_execPrint()';
			tip = 'Printable...';
			break;
		case 3:
			tname = 'image_iconRevert';
			source = iconRevert;
			func = 'editor_execRevert()';
			tip = 'Revert To Saved';
			break
	}

	var target = document.getElementById(tname);
	if (state)
	{
		target.src = source.src;
		util.attribute(target, 'onclick', func, 'onmouseover', 'Tip("' + tip + '")');
		target.style.cursor = 'pointer';
	} else {
		target.src = iconClear.src;
		util.attribute(target, 'onclick', '', 'onmouseover', '');
		target.style.cursor = 'default';
	}
}

function editor_setDirty(target, state)
{
	if (state)
	{
		var inputs = target.getElementsByTagName('INPUT');
		inputs[4].value = 1;
		var imgs = target.getElementsByTagName('IMG');
		imgs[1].src = iconDirty.src;
		
		editor_setButton(0, true);
		editor_setButton(3, true);
	} else {
		var imgs = target.getElementsByTagName('IMG');
		imgs[1].src = iconClear.src;
	}
}

JS;

$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'editor_resize()';
$myArr['div'] = <<<HTML
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr valign="top">
	<td width="99%"><textarea id="editor_Window" onKeyDown="editor_allowTabs(event)"></textarea></td>
	<td width="5"><img src="graphics/dot_clear.gif" height="1" width="5"></td>
	<td>
		<div id="editor_buttonHeader"><center>
			<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle">
				<td><img id="image_iconSave" src="graphics/dot_clear.gif" class="editor_Icon"></td>
				<td><img src="graphics/dot_clear.gif" class="editor_IconSpacer"></td>
				<td><img id="image_iconSaveAs" src="graphics/dot_clear.gif" class="editor_Icon"></td>
				<td><img src="graphics/dot_clear.gif" class="editor_IconSpacer"></td>
				<td><img id="image_iconPrint" src="graphics/dot_clear.gif" class="editor_Icon"></td>
				<td><img src="graphics/dot_clear.gif" class="editor_IconSpacer"></td>
				<td><img id="image_iconRevert" src="graphics/dot_clear.gif" class="editor_Icon"></td>
				<td><img src="graphics/dot_clear.gif" height="1" width="12"></td>
				<td bgcolor="#000000"><img src="graphics/dot_clear.gif" height="1" width="1"></td>
				<td><img src="graphics/dot_clear.gif" height="1" width="12"></td>
				<td><img src="graphics/icon_OpenFileGroup.gif" class="editor_Icon" style="cursor: pointer" onClick="editor_execOpenFileGroup()" onMouseOver="Tip('Open File Group')"></td>
				<td><img src="graphics/dot_clear.gif" class="editor_IconSpacer"></td>
				<td><img src="graphics/icon_SaveFileGroup.gif" class="editor_Icon" style="cursor: pointer" onClick="editor_execSaveFileGroup()" onMouseOver="Tip('Save File Group')"></td>
			</tr></table></center>
		</div>
		<div id="editor_FilesSpacer"></div>
		<div id="editor_Files">
			<div class="mainfont editor_FilesCategory" style="padding-top: 0px">Procedures</div>
			<div class="mainfont editor_FilesItem" id="editor_FilesProcedures"></div>
			<div class="mainfont editor_FilesCategory">Functions</div>
			<div class="editor_FilesItem" id="editor_FilesFunctions"></div>
			<div class="mainfont editor_FilesCategory">Triggers</div>
			<div class="editor_FilesItem" id="editor_FilesTriggers"></div>
			<div class="mainfont editor_FilesCategory">Views</div>
			<div class="editor_FilesItem" id="editor_FilesViews"></div>
			<div class="mainfont editor_FilesCategory">Stored Queries</div>
			<div class="editor_FilesItem" id="editor_FilesQueries"></div>
			<div class="mainfont editor_FilesCategory">
				<img src="graphics/newIcon.gif" class="editorIcon" style="cursor: pointer;" onClick="editor_newScratchPad()" onMouseOver="Tip('New Scratchpad')">
				<span style="position: relative; top: -5px;">Scratch Pads</span>
			</div>
			<div class="editor_FilesItem" id="editor_Scratchpads"></div>
		</div>
	</td>
</tr></table>
<div style="display: none">
<form name="editor_printJob" method="POST" target="_blank">
<input type="hidden" id=" editor_form_request" name="module" value="editor">
<input type="hidden" id=" editor_form_request" name="request" value="print">
<input type="hidden" id="editor_form_type" name="type" value="">
<input type="hidden" id="editor_form_dbname" name="dbname" value="">
<input type="hidden" id="editor_form_name" name="scriptname" value="">
<input type="hidden" id="editor_form_buffer" name="buffer" value="">
</form>
</div>
HTML;

$include[] = $myArr;

?>
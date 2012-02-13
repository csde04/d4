<?php

$myArr = array();

$myArr['moduleName'] = 'root';
$myArr['tabCaption'] = '';
$myArr['onResize'] = 'root_Resize()';
$myArr['onHAdjust'] = 'root_HAdjust()';
$myArr['css'] = <<<CSS
  body 			{ margin: 5px 5px 0px 5px; background-color: #93B1ED; font-family: verdana, sans-serif; }
 .bold 			{ font-weight: bold; }
 .boxBottom 		{ height: 10px; background-image: url("graphics/bottom.gif"); background-repeat: repeat-x; }
 .boxBR 		{ height: 10px; width: 10px; background-image: url("graphics/br.gif"); background-repeat: no-repeat; }
 .boxLeft 		{ width: 10px; background-image: url("graphics/left.gif"); background-repeat: repeat-y; }
 .boxRight 		{ width: 10px; background-image: url("graphics/right.gif"); background-repeat: repeat-y; }
 .boxTL 		{ height: 10px; width: 10px; background-image: url("graphics/tl.gif"); background-repeat: no-repeat; }
 .boxTop 		{ height: 10px; background-image: url("graphics/top.gif"); background-repeat: repeat-x; }
 .boxTR 		{ height: 10px; width: 10px; background-image: url("graphics/tr.gif"); background-repeat: no-repeat; }
 .boxBL 		{ height: 10px; width: 10px; background-image: url("graphics/bl.gif"); background-repeat: no-repeat; }
 #clientMain	{ height: 50px; width: 100%; }
 #explorer 		{ height: 50px; background-color: #ffffff; }
 #footer 		{ height: 40px; background-color: #ffffff; }
 #hAdjustHandle		{ position: absolute; z-index: 100; top: 230px; left: 600px; height: 16px; width: 47px; cursor: move; background-image: url('graphics/hKnuckles.gif'); }
 #header 		{ height: 40px; background-color: #ffffff; }
 #headerContent 	{ height: 20px; }
 .italic 		{ font-style: italic; }
 #leftPanel 		{ height: 200px; background-color: #ffffff; }
 #logo 			{ position: absolute; z-index: 10; top: 11px; left: 20px; }
 #versionNumber		{ position: absolute; z-index: 10; top: 12px; right: 25px; font-family: verdana, arial, sans-serif; font-size: 11px; font-weight: bold; font-style: italic; }
 #main 			{ }
 .mainfont 		{ font-family: verdana, sans-serif; color: #000000; }
A.mainfont		{ font-weight: bold; text-decoration: none; color: #000000; }
A.mainfont:HOVER 	{ text-decoration: underline; }
 .courier		{ font-family: courier; }
 #rightPanel 		{ width: 250px; height: 200px; background-color: #ffffff; }
 .s9  			{ font-size: 9px; }
 .s10 			{ font-size: 10px; }
 .s11 			{ font-size: 11px; }
 .s12 			{ font-size: 12px; }
 .s14 			{ font-size: 14px; }
 .s18 			{ font-size: 18px; }
 .s20 			{ font-size: 20px; }
 .s24 			{ font-size: 24px; }
 .spacer 		{ height: 6px; }
 .tabOffMiddle 		{ background-color: #345496; }
 .tabOnMiddle 		{ background-color: #011743; }
 .tabText 		{ font-family: verdana, sans-serif; font-size: 10px; font-weight: bold; color: #ffffff; }
A.tabText 		{ text-decoration: none; }
A.tabText:HOVER 	{ color: #ffff00; }
 .underline 		{ text-decoration: underline; }
 
tbody.bodyShow { display: block; display: table-row-group; }
tbody.bodyHide { display: none }

.tmFooter { text-align: center; font-family: verdana,arial,sans-serif; font-size: 10px; color: #011743; }
A.tmFooter { font-weight: bold; text-decoration: none; }
A.tmFooter:HOVER { background-color: #011743; color: #ffffff; }

CSS;

$myArr['initJS'] = <<<JS
storage = new localStorage();
storage.fileName = 'main';

global_explorerHeight = storage.retrieveItem('explorerHeight');
if (isNaN(global_explorerHeight) || (global_explorerHeight == null))
{
	global_explorerHeight = 150;
	storage.storeItem('explorerHeight', global_explorerHeight);
}

setTimeout('masterInit()', 100);

isIE = ((document.all) && (document.getElementById));



// Attach an onResize event to the main window that is browser agnostic...
if (typeof window.addEventListener != 'undefined')
{
	window.addEventListener("resize", masterResize, false); 
}
else if (typeof document.addEventListener != 'undefined') 
{
	document.addEventListener("resize", masterResize, false); 
}
else if (typeof window.attachEvent != 'undefined') 
{
	window.attachEvent("onresize", masterResize); 
}

// Setup the mouse tracker and prep for the resizeer
document.onmousemove = _mouseMove;
if (document.captureEvents) document.captureEvents(Event.MOUSEMOVE);
document.onmouseup = _mouseUp;
if (document.captureEvents) document.captureEvents(Event.MOUSEUP);

dragObject = null;
mousePos = null;
mouseOffset = null;

// Load tab graphics...
tabOnLeft = new Image();
tabOnLeft.src = 'graphics/tabOnLeft.gif';
tabOnRight = new Image();
tabOnRight.src = 'graphics/tabOnRight.gif';
tabOffLeft = new Image();
tabOffLeft.src = 'graphics/tabOffLeft.gif';
tabOffRight = new Image();
tabOffRight.src = 'graphics/tabOffRight.gif';

// Select the first tab on both banks:
setTimeout('selectTab(1, 0)', 100);
setTimeout('selectTab(2, 0)', 150);

JS;

$myArr['funcJS'] = <<<JS
function addEventSimple(obj,evt,fn) {
	if (obj.addEventListener)
		obj.addEventListener(evt,fn,false);
	else if (obj.attachEvent)
		obj.attachEvent('on'+evt,fn);
}

function removeEventSimple(obj,evt,fn) {
	if (obj.removeEventListener)
		obj.removeEventListener(evt,fn,false);
	else if (obj.detachEvent)
		obj.detachEvent('on'+evt,fn);
}

function masterInit()
{
	div_clientMain = document.getElementById('clientMain');
	div_explorer = document.getElementById('explorer');
	div_hAdjustHandle = document.getElementById('hAdjustHandle');
	global_windowHeight = browserWindowHeight();
	global_windowWidth = browserWindowWidth();
	masterHAdjust();
}

function root_HAdjust()
{
	div_explorer.style.height = global_explorerHeight + 'px';	
	div_hAdjustHandle.style.top = ((global_explorerHeight - 0) + 80) + 'px';
	storage.storeItem('explorerHeight', global_explorerHeight);
	masterResize();
}

function root_Resize()
{
	global_windowHeight = browserWindowHeight();
	global_windowWidth = browserWindowWidth();
//	div_clientMain.style.height = ((global_windowHeight - global_explorerHeight) - 143) + 'px';
	div_clientMain.style.height = ((global_windowHeight - global_explorerHeight) - 157) + 'px';
	div_hAdjustHandle.style.left = (Math.round(global_windowWidth / 2) - 23) + 'px';
}

function _mouseMove(ev)
{
	if (ev == null) ev = window.event;
		
	if (dragObject)
	{
		ev = ev || window.event;
		mousePos = _mouseCoords(ev);

//		debug('dragging: x=' + mousePos.x + ', y=' + mousePos.y);
	
		document.body.focus();
	    document.onselectstart = function () { return false; };
		global_explorerHeight = (mousePos.y - 85);
		if (global_explorerHeight <= 100) global_explorerHeight = 100;
		if (global_explorerHeight >= (global_windowHeight - 240)) global_explorerHeight = global_windowHeight - 240;
		masterHAdjust();
		return false;
	}
}

function _mouseCoords(ev)
{
	if (ev.pageX || ev.pageY) return { x:ev.pageX, y:ev.pageY };
	return { x:ev.clientX + document.body.scrollLeft - document.body.clientLeft, y:ev.clientY + document.body.scrollTop  - document.body.clientTop };
}

function _mouseUp(ev) 
{
	if (dragObject)
	{
//		debug('mouseUp');
		dragObject = null;
		document.onselectstart = null;
		document.onselect = null;
		return false;
	}
}

function _startDrag(target)
{
//	debug('startDrag');
	dragObject = target;
	mouseOffset = mousePos;
    document.onselectstart = function() { return false; };
    document.onselect = function() { return false; };
	document.body.focus();
	return false;
}

function _handleResize()
{
	var leftPanel = document.getElementById('leftPanel');
	var rightPanel = document.getElementById('rightPanel');
	leftPanel.style.height = (browserWindowHeight() - 292) + 'px';
	leftPanel.style.width = (browserWindowWidth() - 282) + 'px';
	rightPanel.style.height = (browserWindowHeight() - 292) + 'px';
}

function selectTab(theBand, theTab)
{
	var max = (theBand == 1) ? band1Count : band2Count;
	
	for (var i=0; i<max; i++)
	{
		var thisLeft = 'tabLeft' + theBand + i;
		var thisRight = 'tabRight' + theBand + i;
		var thisCenter = 'tabCenter' + theBand + i;
		var thisDiv = 'tab' + theBand + i;
		
		if (i == theTab)
		{
			document.getElementById(thisLeft).src = tabOnLeft.src;
			document.getElementById(thisRight).src = tabOnRight.src;
			document.getElementById(thisCenter).style.backgroundColor = '#011743';
			document.getElementById(thisDiv).style.display = 'block';
		} else {
			document.getElementById(thisLeft).src = tabOffLeft.src;
			document.getElementById(thisRight).src = tabOffRight.src;
			document.getElementById(thisCenter).style.backgroundColor = '#345496';
			document.getElementById(thisDiv).style.display = 'none';
		}
	}

	var funcName = modList[theBand][theTab] + '_onDisplay';
	if (eval("typeof " + funcName + " == 'function'"))
		setTimeout(funcName + '()', 10);
	
}



// ----------------------------------------------------------- //
//                      ajaxRequestor                          //
// ----------------------------------------------------------- //
function ajaxRequestor() 
{ 
	this.clearAll(); 
	this.logNode = false;
	this.logName = 'Unnamed';
}

ajaxRequestor.prototype.__defaultError = function(sender)
{
	var tempStr = "ajaxRequestor Error:\\n" +
                  "status: " + this.requestor.status + "\\n" +
	 	          "headers: " + this.requestor.getAllResponseHeaders();
	alert(tempStr);
}
ajaxRequestor.prototype.__defaultSuccess = function(sender)
{
	alert("ajaxRequestor successfully returned from a request - but there is no handler assigned to receive it");
}
ajaxRequestor.prototype.__decodeString = function(inputStr)
{
	var decoded = unescape(inputStr);
	decoded = decoded.replace(/\%2F/g, "/"); 
	decoded = decoded.replace(/\%3F/g, "?");
	decoded = decoded.replace(/\%3D/g, "=");
	decoded = decoded.replace(/\%26/g, "&");
	decoded = decoded.replace(/\%40/g, "@");
	return decoded;
}
ajaxRequestor.prototype.__encodeString = function(inputStr)
{
	var encoded = escape(inputStr);
	encoded = encoded.replace(/\//g,"%2F");
	encoded = encoded.replace(/\?/g,"%3F");
	encoded = encoded.replace(/=/g,"%3D");
	encoded = encoded.replace(/&/g,"%26");
	encoded = encoded.replace(/@/g,"%40");
	return encoded;
}
ajaxRequestor.prototype.__getParams = function()
{
	if (this.getNames.length == 0) { return ""; }
	var out = (this.url.indexOf('?') == -1) ? '?' : '&';
	for (var i=0; i<this.getNames.length; i++)
	{
		out += this.getNames[i] + '=' + this.getValues[i];
		if (i < (this.getNames.length - 1)) { out += '&'; }
	}
	return out;
}
ajaxRequestor.prototype.__getRequestor = function()
{
	if ((this.requestor != null) && (!this.reqIsIE)) { return true; }
	
	try {
		this.requestor = new XMLHttpRequest();
		this.reqIsIE = false;
		return; true;
	} catch(e) {}
	
	try {
		this.requestor = new ActiveXObject("Msxml2.XMLHTTP.6.0");
		this.reqIsIE = true;
		return; true;
	} catch(e) {}

	try {
		this.requestor = new ActiveXObject("Msxml2.XMLHTTP.3.0");
		this.reqIsIE = true;
		return; true;
	} catch(e) {}

	try {
		this.requestor = new ActiveXObject("Msxml2.XMLHTTP");
		this.reqIsIE = true;
		return; true;
	} catch(e) {}

	try {
		this.requestor = new ActiveXObject("Microsoft.XMLHTTP");
		this.reqIsIE = true;
		return; true;
	} catch(e) {}
	
	alert('ajaxRequestor Fatal Error: Cannot instantiate an XMLHTTP Object');
}
ajaxRequestor.prototype.__onRTS = function()
{
	this.log('__onRTS - readyState=' + this.requestor.readyState);

	if ((this.requestor.readyState >= 2) && (this.timeoutHandle))
	{
		this.log('__onRTS - clearing timeout');
		clearTimeout(this.timeoutHandle);
		this.timeoutHandle = false;
	}
	
    if (this.requestor.readyState == 4)
	{
		if (this.masterStatus) { this.masterStatus.handleChange(false); }
		if ((this.requestor.status==200) || (this.requestor.status==0))
		{
			this.log('__onRTS: status=' + this.requestor.status);
			this.lastResponse = this.__decodeString(this.requestor.responseText);
			if (!this.lastResponse) 
			{ 	
				this.log('__onRTS: no lastResponse, returning false');
				return false;
			}
			this.log('__onRTS - received ' + this.lastResponse);
			this.log('__onRTS - firing onSuccess');
			this.onSuccess(this);
		} else {
			switch(this.requestor.status)
			{
				case 12029:
				case 12030:
				case 12031:
				case 12152:
				case 12159:
					this.log('__onRTS - untrapped error ' + this.request.status);
					alert('Untrapped error: ' + this.request.status);
					break;
					
				default:
					this.onError(this);
			}
		}
		this.busy = false;
	}
}
ajaxRequestor.prototype.__postParams = function()
{
	var out = "";
	var varNames = '';
	for (var i=0; i<this.postNames.length; i++)
	{
		if (i > 0) { varNames += '|'; }
		varNames += this.postNames[i];
		if (i > 0) { out += '&'; }
		out += this.postNames[i] + '=' + this.__encodeString(this.postValues[i]);
	}
	if (out) { out += '&' + 'ajax_var_names=' + varNames; }
	return out;
}
ajaxRequestor.prototype.abort = function()
{
	if (this.busy)
	{
		this.log('abort');
		// clear timeout as well
		this.requestor.abort();
		clearTimeout(this.timeoutHandle);
		this.timeoutHandle = false;
		this.busy = false;
	}
}

ajaxRequestor.prototype.clear = function()
{
	this.methodPost = true;
	this.__transStatus = 0;
	this.__transBusy = false;
    this.lastResponse = new String();
	this.selfReference = null;
    this.newRequest();
	this.timeoutHandle = false;
	this.timeoutMS = 5000;
}
ajaxRequestor.prototype.clearAll = function()
{
    this.xmlHandler = null;
    this.masterStatus = null;
    this.onUnrecognized = new String();

    this.onError = this.__defaultError;
    this.onSuccess = this.__defaultSuccess;
    
    this.clear();
}
ajaxRequestor.prototype.execute = function(timeoutVal)
{
	this.log('execute starts');
	
	if (this.busy)
	{
		this.log('busy, aborting');
		// clear timeout as well
		this.requestor.abort();
		this.busy = false;
	}
	
	var thisTimeoutVal = this.timeoutMS;
	if (timeoutVal != undefined) { thisTimeoutVal = timeoutVal; }
	this.log('execute sets TTL of ' + thisTimeoutVal);
	
	this.__getRequestor();

	if (!this.requestor) 
	{
		this.log('Fail - no XMLHTTPRequestor');
		alert("You cannot dispatch a request on this machine (no viable XMLHTTPRequestor)");
		return "";
	}
	if (!this.url) 
	{
		this.log('Fail - no destination URL');
		alert("You must supply a URL to ajaxRequestor to process a request");
		return "";
	}

	this.busy = true;
	var httpMethod = (this.methodPost) ? 'POST' : 'GET';

	var theURL = this.url;
	theURL += this.__getParams();
	this.lastRequest = theURL;
	this.log('Target URL: ' + theURL);
	
	var loader = this;
	this.requestor.onreadystatechange = function() { loader.__onRTS.call(loader); }
	if (this.masterStatus) { this.masterStatus.handleChange(true); }

	// Set a callback to <me> in case the request takes to long...
	this.timeoutHandle = setTimeout( function() { loader.__handleTimeout.call(loader); }, this.timeoutMS);
	
	this.requestor.open('POST', theURL, true);
	this.requestor.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	if ((document.all) && (document.getElementById))
	{
		// IE
		setTimeout( function() { loader.__executeSend.call(loader) }, 10);
		this.log('Delay execution for IE');
	} else {
		var pp = this.__postParams();
		if (pp > ' ')  this.log('Posting ' + pp);
		this.log('dispatching');
		this.requestor.send(pp);
	}
}
ajaxRequestor.prototype.__executeSend = function() 
{ 
	var pp = this.__postParams();
	if (pp > ' ')  this.log('Posting ' + pp);
	this.log('dispatching');	
	this.requestor.send(); 
}

ajaxRequestor.prototype.__handleAbort = function()
{
	this.log('__handleAbort()');
	if (this.masterStatus) { this.masterStatus.handleChange(false); }
	this.requestor.abort();
}
ajaxRequestor.prototype.__handleTimeout = function()
{
	this.log('__handleTimeout()');
	this.__handleAbort();
	var loader = this;
	setTimeout(function() { loader.execute.call(loader); }, 100);
}
ajaxRequestor.prototype.getParam = function(key, value)
{
	var ptr = this.getNames.length;
	for (var i=0; i<this.getNames.length; i++)
	{
		if (this.getNames[i] == key) { ptr = i; }
	}
	this.getNames[ptr] = key;
	this.getValues[ptr] = value;
}
ajaxRequestor.prototype.log = function(msg)
{
	if (this.logNode)
	{
		var newNode = document.createElement('DIV');
		newNode.innerHTML = this.logName + ': ' + msg;
		this.logNode.appendChild(newNode);
	}
}
ajaxRequestor.prototype.method = function(doPost)
{
	this.methodPost = (doPost);
}
ajaxRequestor.prototype.newRequest = function()
{
	this.getNames = new Array();
	this.getValues = new Array();
	this.postNames = new Array();
	this.postValues = new Array();
	this.url = '';
}
ajaxRequestor.prototype.postParam = function(key, value)
{
	var ptr = this.postNames.length;
	for (var i=0; i<this.postNames.length; i++)
	{
		if (this.postNames[i] == key) { ptr = i; }
	}
	this.postNames[ptr] = key;
	this.postValues[ptr] = value;
}

// ----------------------------------------------------------- //
//                       localStorage                          //
// ----------------------------------------------------------- //
function localStorage() { this.clear(); }
localStorage.prototype.clear = function()
{
	this.fileName = new String();
}
localStorage.prototype._getRaw = function()
{
	var rawBuff = document.cookie;
	var cookieRegExp = new RegExp("\\\b" + this.fileName + "=([^;]*)");
	theValue = cookieRegExp.exec(rawBuff);
	if (theValue != null) { theValue = theValue[1]; }
	return theValue;
}
localStorage.prototype.asArray = function()
{
	var outArr = new Object();
	var rawBuff = this._getRaw(this.fileName);
	if (rawBuff == undefined) { return false; }
	var tempArr = rawBuff.match(/([^&]+)/g);
	for (var i=0; i<tempArr.length; i++)
	{
		var parts = tempArr[i].match(/([^=]+)=(.*$)/);
		var varName = parts[1];
		var varValue = parts[2];
		outArr[varName] = unescape(varValue);
	}
	return outArr;
}
localStorage.prototype.dropFile = function()
{
	if (this.fileName)
	{
		var expiredDate = new Date();
		expiredDate.SetMonth(-1);
		var writeBuff = this.fileName + "=";
		writeBuff += "expires=" + expiredDate.toGMTString();
		document.cookie = writeBuff;
	}
}
localStorage.prototype.dropItem = function(theName)
{
	var rawBuff = readUnEscapedCookie(this.fileName);
	if (rawBuff)
	{
		var stripAttributeRegExp = new RegExp("(^|/&)" + theName + "=[^&]*&?");
		rawBuff = rawBuff.replace(stripAttributeRegExp, "$1");
		if (rawBuff.length != 0)
		{
			var newBuff = this.fileName + "=" + rawBuff;
			document.cookie = newBuff
		} else { this.dropFile(); }
	}
}
localStorage.prototype.enabled = function()
{
	var cookiesEnabled = window.navigator.cookieEnabled;
	if (!cookiesEnabled)
	{
		document.cookie = "cookiesEnabled=True";
		cookiesEnabled = new Boolean(document.cookie).valueOf();
	}
	return cookiesEnabled;
}
localStorage.prototype.retrieveItem = function(theName)
{
	var rawBuff = this._getRaw(this.fileName);
	var extractMultiValueCookieRegExp = new RegExp("\\\b" + theName + "=([^;&]*)");
	resValue = extractMultiValueCookieRegExp.exec(rawBuff);
	if (resValue != null) { resValue = unescape(resValue[1]); }
	return resValue;
}
localStorage.prototype.storeItem = function(theName, theValue)
{
	var rawBuff = this._getRaw(this.fileName);
	if (rawBuff)
	{
		var stripAttributeRegExp = new RegExp("(^|&)" + theName + "=[^&]*&?");
		rawBuff = rawBuff.replace(stripAttributeRegExp, "$1");
		if (rawBuff.length != 0) { rawBuff += "&"; }
	} else rawBuff = "";
	
	rawBuff += theName + "=" + escape(theValue);
	document.cookie = this.fileName + "=" + rawBuff;
}


// ----------------------------------------------------------- //
//                       Table Manager                         //
// ----------------------------------------------------------- //
// This object is used to manage the creation of tables - not that
// you'd really need it if it wasn't for IE's quirkiness...
function tableManager(cellPadding, cellSpacing, border, width) 
{ 
	this.clear();
	if (cellPadding) { this.cellPadding = cellPadding; }
	if (cellSpacing) { this.cellSpacing = cellSpacing; }
	if (border) { this.border = border; }
	if (width) { this.width = width; }
}
tableManager.prototype.clear = function()
{
	this.cellPadding = 0;
	this.cellSpacing = 0;
	this.border = 0;
	this.width = '100%';
	this.sizingGraphicURL = 'graphics/dot_clear.gif';
	
	this.currentRow = null;
	this.currentCell = null;
	
	this.isIE = ((document.all) && (document.getElementById));
	if (this.isIE) {
		this.mainNode = document.createElement('TBODY');
	} else {
		this.mainNode = document.createElement('TABLE');
	}
}
tableManager.prototype.addTableTo = function(newOwner)
{
	if (this.isIE)
	{
		var tableNode = document.createElement('TABLE');
		this.attribute(tableNode, 'cellpadding', this.cellPadding, 'cellspacing', this.cellSpacing, 'border', this.border, 'width', this.width);
		tableNode.appendChild(this.mainNode);
		newOwner.appendChild(tableNode);
		return tableNode;
	} else {
		this.attribute(this.mainNode, 'cellpadding', this.cellPadding, 'cellspacing', this.cellSpacing, 'border', this.border, 'width', this.width);
		newOwner.appendChild(this.mainNode);
		return this.mainNode;
	}
}
tableManager.prototype.attribute = function(theNode)
{
	for(var i=1; i<arguments.length; i+=2) 
	{ 
		if (this.isIE) { 
			// NN2 attribute the object the old way...
			switch(arguments[i].toLowerCase())
			{
				case 'align' : theNode.align = arguments[i+1]; break;
				case 'bgcolor' : theNode.bgColor = arguments[i+1]; break;
				case 'border' : theNode.border = arguments[i+1]; break;
				case 'cellpadding' : theNode.cellPadding = arguments[i+1]; break;
				case 'cellspacing' : theNode.cellSpacing = arguments[i+1]; break;
				case 'class' : theNode.className = arguments[i+1]; break;
				case 'colspan' : theNode.colSpan = arguments[i+1]; break;
				case 'enctype' : theNode.encoding = arguments[i+1]; break;
				case 'height' : theNode.height = arguments[i+1]; break;
				case 'href' : theNode.setAttribute('href', arguments[i+1]); break;
				case 'id' : theNode.id = arguments[i+1]; break;
				case 'method' : theNode.method = arguments[i+1]; break;
				case 'name' : theNode.name = arguments[i+1]; break;
				case 'nowrap' : theNode.noWrap = 'nowrap'; break;
				case 'onclick' : theNode['onclick'] = new Function(arguments[i+1]); break;
				case 'onblur' : theNode['onblur'] = new Function(arguments[i+1]); break;
				case 'onchange' : theNode['onchange'] = new Function(arguments[i+1]); break;
				case 'onclick' : theNode['onclick'] = new Function(arguments[i+1]); break;
				case 'ondblclick' : theNode['ondblclick'] = new Function(arguments[i+1]); break;
				case 'onfocus' : theNode['onfocus'] = new Function(arguments[i+1]); break;
				case 'onkeypress' : theNode['onkeypress'] = new Function(arguments[i+1]); break;
				case 'onmouseover' : theNode['onmouseover'] = new Function(arguments[i+1]); break;
				case 'onmouseout' : theNode['onmouseout'] = new Function(arguments[i+1]); break;
				case 'rowspan' : theNode.rowSpan = arguments[i+1]; break;
				case 'size' : theNode.size = arguments[i+1]; break;
				case 'src' : theNode.src = arguments[i+1]; break;
				case 'type' : theNode.setAttribute('type', arguments[i+1]); break;
				case 'valign' : theNode.vAlign = arguments[i+1]; break;
				case 'value' : theNode.value = arguments[i+1]; break;
				case 'visibility' : theNode.visibility = arguments[i+1]; break;
				case 'width' : theNode.width = arguments[i+1]; break;
			}
		} else { theNode.setAttribute(arguments[i], arguments[i+1]); }
	}
	return theNode;
}
tableManager.prototype.newCell = function(rowNode)
{
	if (!rowNode) { rowNode = this.currentRow; }
	if (!rowNode) { rowNode = this.newRow(); }
	this.currentCell = document.createElement('TD');
	rowNode.appendChild(this.currentCell);
	return this.currentCell;
}
tableManager.prototype.newCellContent = function(cellContent, rowNode)
{
	if (!rowNode) { rowNode = this.currentRow; }
	this.newCell();
	this.currentCell.appendChild(cellContent);
	return this.currentCell;
}
tableManager.prototype.newFirstCell = function(content)
{
	this.newRow();
	this.newCell();
	if (content) { this.currentCell.appendChild(content); }
	return this.currentCell;
}
tableManager.prototype.newRow = function() 
{ 
	this.currentRow = document.createElement('TR');
	this.mainNode.appendChild(this.currentRow);
	return this.currentRow; 
}
tableManager.prototype.newTextCell = function(theText, theClass)
{
	return this.textCell(theText, theClass);
}
tableManager.prototype.sizingCell = function(height, width, colSpan) { return this._sizingCell(this.currentRow, height, width, colSpan); }
tableManager.prototype._sizingCell = function(theRow, height, width, colSpan)
{
	if (!theRow) { theRow = this.newRow(); }
	var thisContent = document.createElement('img');
	thisContent.src = this.sizingGraphicURL;
	this.attribute(thisContent, 'height', height, 'width', width);
	this.newCell(theRow).appendChild(thisContent);

	if (colSpan) { this.attribute(this.currentCell, 'colSpan', colSpan); }
	return this.currentCell;
}
tableManager.prototype.sizingRow = function(height, colSpan)
{
	this.newRow();
	this.sizingCell(height, 1, colSpan);
	return this.currentCell;
}
tableManager.prototype.textCell = function(theText, textClass)
{
	this.newCell();
	if (textClass) { this.attribute(this.currentCell, 'class', textClass); }
	this.currentCell.appendChild(document.createTextNode(theText));
	return this.currentCell;
}


// ----------------------------------------------------------- //
//                     General Functions                       //
// ----------------------------------------------------------- //
function browserWindowHeight()
{
	if (isIE)
	{
		return document.documentElement.clientHeight;
	} else {
		return window.innerHeight;
	}
}
function browserWindowWidth()
{
	if (isIE)
	{
		return document.documentElement.clientWidth;
	} else {
		return window.innerWidth;
	}
}
function getSelectValue(theSelect)
{
	var temp = document.getElementById(theSelect);
	var idx = temp.selectedIndex;
	return temp.options[idx].value;
}
function selectSelectValue(theSelect, theValue)
{
	// Very simply - select the option in the list that has a <value> matching what was passed...
	theValue = theValue.toLowerCase();
	var max = theSelect.options.length;
	for (var i=0; i<max; i++)
	{
		if (theValue == theSelect.options[i].value.toLowerCase())
		{
			theSelect.selectedIndex = i;
			return true;
		}
	}
	return false;
}
function highlightRow(theRow, theRowColor, theTextColor)
{
    if (typeof(document.getElementsByTagName) != 'undefined')
        { var theCells = theRow.getElementsByTagName('td'); }
    else if (typeof(theRow.cells) != 'undefined')
        { var theCells = theRow.cells; }
    else { return false; }

    var rowCellsCnt = theCells.length;
    for (var c=0; c<rowCellsCnt; c++)
    {
        theCells[c].style.backgroundColor=theRowColor;
        if (theTextColor) { theCells[c].style.color=theTextColor; }
        theCells[c].style.cursor='pointer';
    }
    return true;
}
function highlightCell(theCell, theColor, theTextColor)
{
    theCell.style.backgroundColor=theColor;
    if (theTextColor) { theCell.style.color=theTextColor; }
    theCell.style.cursor='pointer';
    return true;
}
function dumpArray(arr, level) 
{
	var dumped_text = "";
	if (!level) { level = 0; }

	// The padding given at the beginning of the line.
	var level_padding = "";
	for (var j=0; j<level; j++) { level_padding += '   '; }

	if (typeof(arr) == 'object') 
	{ 
		// Array, Hashes & Objects
		for (var item in arr) 
		{
			var value = arr[item];
 
			if (typeof(value) == 'object') 
			{ 
				// If it is an array...
				dumped_text += level_padding + "['" + item + "']\\n";
				dumped_text += dumpArray(value,level+1);
			} else {
				vSurround = (typeof(value) == 'string') ? "'" : '';
				nSurround = (item.match(/^[0-9]{1,10}$/)) ? '' : "'";
				dumped_text += level_padding + "[" + nSurround + item + nSurround + "] => " + vSurround + value + vSurround + "\\n";
			}
		}
	} else { 
		// Stings, Chars & Numbers etc.
		dumped_text = "(" + typeof(arr) + ") " + arr + "\\n";
	}
	
	return dumped_text;
}
function replaceText(text, textarea)
{
	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
	}
	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;

		if (textarea.setSelectionRange)
		{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put it on the end.
	else
	{
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
	}
}

function trim(theStr) { return theStr.replace(/^\s+/g, '').replace(/\s+$/g, ''); }

JS;

$myArr['onHAdjust'] = 'root_HAdjust()';

$myArr['onResize'] = 'root_Resize()';

$myArr['div'] = <<<HTML
HTML;

$include[] = $myArr;

?>
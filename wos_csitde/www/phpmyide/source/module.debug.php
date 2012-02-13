<?php

$myArr = array();
$myArr['moduleName'] = 'debug';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'Debug';
$myArr['css'] = "#debug_Window	{ overflow: auto; width: 100%; font-family: courier; font-size: 11px; font-weight: normal; background-color: #e0e0e0; }";
$myArr['initJS'] = '';
$myArr['funcJS'] = <<<JS
function debug(msg)
{
	var node = document.createElement('div');
	node.innerHTML = msg;
	document.getElementById('debug_Window').appendChild(node);
}
function debug_Resize()
{
	var newHt = ((global_windowHeight - global_explorerHeight) - 158) + 'px';
	document.getElementById('debug_Window').style.height = newHt;
}

JS;
$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'debug_Resize()';
$myArr['div'] = <<<HTML
<div id="debug_Window">
</div>
HTML;

$include[] = $myArr;

?>
<?php

$myArr = array();
$myArr['moduleName'] = 'timemachine';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'Time Machine';
$myArr['css'] = "#debug_Window	{ overflow: auto; width: 100%; font-family: courier; font-size: 11px; font-weight: normal; background-color: #e0e0e0; }";
$myArr['initJS'] = '';
$myArr['funcJS'] = <<<JS
function tm_Resize()
{
	document.getElementById('tm_Window').style.height = ((global_windowHeight - global_explorerHeight) - 158) + 'px';
}

JS;
$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'tm_Resize()';
$myArr['div'] = <<<HTML
<div id="tm_Window">
</div>
HTML;

$include[] = $myArr;

?>
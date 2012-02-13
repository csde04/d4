<?php

$myArr = array();
$myArr['moduleName'] = 'ajaxlog';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'Ajax Log';
$myArr['css'] = "#ajaxlog_Window	{ overflow: auto; width: 100%; font-family: courier; font-size: 11px; font-weight: normal; background-color: #e0ffe0; padding: 5px; }";
$myArr['initJS'] = '';
$myArr['funcJS'] = <<<JS
function ajaxlog_Resize() 
{ 
	document.getElementById('ajaxlog_Window').style.height = ((global_windowHeight - global_explorerHeight) - 165) + 'px'; 
	document.getElementById('ajaxlog_Window').style.width = (global_windowWidth - 40) + 'px';
}
JS;

$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'ajaxlog_Resize()';
$myArr['div'] = <<<HTML
<div id="ajaxlog_Window">
</div>
HTML;

$include[] = $myArr;

?>
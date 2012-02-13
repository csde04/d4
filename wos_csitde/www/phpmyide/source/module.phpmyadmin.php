<?php

$myArr = array();
$myArr['moduleName'] = 'pmaadmin';
$myArr['tabBank'] = 2;
$myArr['tabCaption'] = 'phpMyAdmin';
$myArr['css'] = <<<CSS
#phpMyAdminWindow { width: 100%; }
#pma_Refresh { position: absolute; top: 10px; left: 25px; width: 180px; z-index: 100; }
CSS;
$myArr['initJS'] = <<<JS
setTimeout('pma_refresh()', 60);
JS;

$myArr['funcJS'] = <<<JS
function pma_Resize()
{
	document.getElementById('phpMyAdminWindow').style.height = ((global_windowHeight - global_explorerHeight) - 160) + 'px';
	document.getElementById('phpMyAdminWindow').style.width = (global_windowWidth - 40) + 'px';
	document.getElementById('pma_Refresh').style.top = ((global_explorerHeight - 0) + 132) + 'px';
}

function pma_refresh(newTarget)
{
	var target = document.getElementById('phpMyAdminWindow');
	if (newTarget == undefined)
	{
		target.src = target.src;
	} else {
		target.src = newTarget;
	}
}

JS;

$myArr['onHAdjust'] = '';
$myArr['onResize'] = 'pma_Resize()';

$myArr['div'] = <<<HTML
<div id="pma_Container">
<iframe id="phpMyAdminWindow" src ="{$connections[$currCon]['phpmyadmin']}"></iframe>
<input id="pma_Refresh" type="button" value="Refresh" onClick="pma_refresh()">
</div>
HTML;

$include[] = $myArr;

?>
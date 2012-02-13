<?php

// Build the tabs HTML...

$resizeArr = array();
$hAdjustArr = array();
$bank1 = array();
$bank2 = array();
$div1 = array();
$div2 = array();
$bank1Ptr = 0;
$bank2Ptr = 0;
$modList = array();

if ($item['tabBank'] == 1)
{
	$modList[] = "modList[1][$bank1] = '{$item['moduleName']}'";
	$bank1++;
} else {
	$modList[] = "modList[2][$bank2] = '{$item['moduleName']}'";
	$bank2++;
}



foreach($include as $item)
{
	switch(@$item['tabBank'])
	{
		case 0 : break;
		
		case 1 :
			$bankPtr = count($bank1);
			$modList[] = "modList[1][$bankPtr] = '{$item['moduleName']}'";
			$bank1[] = <<<HTML
<td><img id="tabLeft1$bank1Ptr" src="graphics/tabOffLeft.gif" height="20" width="10"></td>
<td id="tabCenter1$bank1Ptr" class="tabOffMiddle tabText"><a href="javascript:void(0)" class="tabText" onClick="selectTab(1, $bank1Ptr)">{$item['tabCaption']}</a></td>
<td><img id="tabRight1$bank1Ptr" src="graphics/tabOffRight.gif" height="20" width="10"></td>
HTML;
			$div1[] = <<<HTML
			
<!-- ------------------------------------------------------------
   HTML Module: {$item['moduleName']}
------------------------------------------------------------- -->

<div id="tab1$bank1Ptr" style="display: none">
{$item['div']}
</div>
HTML;
			$bank1Ptr++;
			break;
			
		case 2 :
			$bankPtr = count($bank2);
			$modList[] = "modList[2][$bankPtr] = '{$item['moduleName']}'";
			$bank2[] = <<<HTML
<td><img id="tabLeft2$bank2Ptr" src="graphics/tabOffLeft.gif" height="20" width="10"></td>
<td id="tabCenter2$bank2Ptr" class="tabOffMiddle tabText"><a href="javascript:void(0)" class="tabText" onClick="selectTab(2, $bank2Ptr)">{$item['tabCaption']}</a></td>
<td><img id="tabRight2$bank2Ptr" src="graphics/tabOffRight.gif" height="20" width="10"></td>
HTML;
			$div2[] = <<<HTML
			
<!-- ------------------------------------------------------------
   HTML Module: {$item['moduleName']}
------------------------------------------------------------- -->

<div id="tab2$bank2Ptr" style="display: none">
{$item['div']}
</div>
HTML;
			$bank2Ptr++;
			break;
	}
	
	if ($item['onResize'])
		$resizeArr[] = "	{$item['onResize']};";
	
	if ($item['onHAdjust'])
		$hAdjustArr[] = "	{$item['onHAdjust']};";
	
}

$bank1Str = implode(chr(10), $bank1);
$bank2Str = implode(chr(10), $bank2);
$div1Str = implode(chr(10), $div1);
$div2Str = implode(chr(10), $div2);
$modListStr = implode(chr(10), $modList);

$jsResizeStr = implode(chr(10), $resizeArr);
$jsHAdjustStr = implode(chr(10), $hAdjustArr);

$nowYear = date('Y', time());
if (!isset($headerContent)) $headerContent = '';

$startupConnection = $connections[$currCon]['name'];


$content = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="description" content="phpMyIDE: Integrated Development Environment for MySQL"/>
<link rel="shortcut icon" href="graphics/favicon.ico" type="image/vnd.microsoft.icon">
<link rel="icon" href="graphics/favicon.ico" type="image/vnd.microsoft.icon">
	
<style>
$cssStr

</style>
	
<script>
// These are enormously special because it must be created AFTER all other modules have been created...
function masterResize()
{
$jsResizeStr
}

function masterHAdjust()
{
$jsHAdjustStr
}

$jsFuncStr

</script>
	
<title>phpMyIDE</title>

</head><body>

<script src="resources/wz_tooltip.js"></script>

<script>
band1Count = $bank1Ptr;
band2Count = $bank2Ptr;

modList = new Array();
modList[1] = new Array();
modList[2] = new Array();
$modListStr

$jsInitStr

</script>


<table cellpadding="0" cellspacing="0" border="0" width="100%">

<tr><td><div id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="10"><div class="boxTL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxTop"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxTR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
		<tr>
			<td width="10" class="boxLeft"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
			<td width="100%"><div id="headerContent">
				$headerContent
			</div></td>
			<td width="10" class="boxRight"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
		</tr>
		<tr>
			<td width="10"><div class="boxBL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxBottom"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxBR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
	</table>
</div></td></tr>

<tr><td><div class="spacer"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td></tr>

<tr><td align="left">
	<table cellpadding="0" cellspacing="0" border="0"><tr>
		<td><img src="graphics/dot_clear.gif" height="1" width="15"></td>
$bank1Str
	</tr></table>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffffff">
		<tr>
			<td width="10"><div class="boxTL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxTop"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxTR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
		<tr>
			<td width="10" class="boxLeft"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
			<td width="100%">
				<div id="explorer">
$div1Str
				</div>
			</td>
			<td width="10" class="boxRight"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
		</tr>
		<tr>
			<td width="10"><div class="boxBL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxBottom"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxBR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
	</table>
</td></tr>

<tr><td><div class="spacer"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td></tr>

<tr><td><div id="main">
	<table cellpadding="0" cellspacing="0" border="0"><tr>
		<td><img src="graphics/dot_clear.gif" height="1" width="15"></td>
$bank2Str
	</tr></table>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffffff">
		<tr>
			<td width="10"><div class="boxTL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxTop"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxTR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
		<tr>
			<td width="10" class="boxLeft"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
			<td width="100%">
				<div id="clientMain">
$div2Str
				</div>
			</td>
			<td width="10" class="boxRight"><img src="graphics/dot_clear.gif" height="1" width="10"></td>
		</tr>
		<tr>
			<td width="10"><div class="boxBL"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="100%"><div class="boxBottom"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
			<td width="10"><div class="boxBR"><img src="graphics/dot_clear.gif" height="1" width="1"></div></td>
		</tr>
	</table>
</div></td></tr>

</table>
<div style="height: 3px;"></div>
<div class="tmFooter"><a href="http://phpmyide.com" class="tmFooter" target="_blank">phpMyIDE</a>&#174; is a trademark of <a href="http://me3inc.com/" class="tmFooter" target="_blank">ME3 Inc.</a> All portions of this website are copyright &copy; 2007-$nowYear <a href="http://me3inc.com/" class="tmFooter" target="_blank">ME3 Inc.</a></div>
<div id="logo"><a href="http://www.phpmyide.com/"><img src="graphics/logo.jpg" height="30" width="142" border="0"></a></div>
<div id="versionNumber" style="text-align: right;">Ver. {$GLOBALS['versionNumber']}<br><div style="font-weight: normal">Connection: <span id="connectionName">$startupConnection</span></div></div>
<div id="hAdjustHandle" onmousedown="_startDrag(this);"><img src="graphics/dot_clear.gif" height="1" width="1"></div>

</body>
</html>
HTML;

?>
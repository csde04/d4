<?php 
	$content = $_REQUEST['content'];
	$folder1 = $_REQUEST['folder'];
?>
<!--
BSD Licence: http://www.opensource.org/licenses/bsd-license.php

<OWNER> v.v.
<ORGANISATION> University of Hertfordshire
<YEAR> 2009

Copyright (c) <YEAR>, <OWNER>
All rights reserved.
Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the <ORGANISATION> nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
-->
<html>
<link id="style_link" rel=StyleSheet href="style1.css" type="text/css" media=all>
<body id=body_local>
<p><b><?php echo $content; ?></b>
<?php 
	$myFile = $folder1."/".$content;
	
	if (isset($_REQUEST['save']))
	{
		$fw = fopen($myFile, 'w') or die("can't open file");
		$stringData = $_POST['T1'];;
		fwrite($fw, $stringData);
		fclose($fw);
		echo " <b>Saved</b>";
	}

?>

<div id=main>
<form name=f1 action="show_code.php?content=<?php echo $content; ?>&folder=<? echo $folder1; ?>&save=yes" method=post>
<textarea name="T1" style="width: 100%; height: 100%; font-size:11px; font-family: Arial;" wrap="physical" onkeydown="return catchTab(this,event);">
<?php 
		$fh = fopen($myFile, 'r');
		$theData = fread($fh,filesize($myFile));
		echo $theData;
?>
</textarea>
</div>
<script>
function setSelectionRange(input, selectionStart, selectionEnd) {
	if (input.setSelectionRange) {
    input.focus();
    input.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (input.createTextRange) {
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character', selectionEnd);
    range.moveStart('character', selectionStart);
    range.select();
  }
}

function replaceSelection (input, replaceString) {
	if (input.setSelectionRange) {
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;

// Hack by kennethburgener to avoid Firefox scrolling to top after using tab key when textarea has been scrolled downwards.
// magistus 2006-11-06 Sadly enough, this doesn't seem to work in Firefox 1.5.0.7 or 2.0 and even creates more problems like double TABS!
//
//		var scrollTop = input.scrollTop; // fix scrolling issue with Firefox
//		input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
//		input.scrollTop = scrollTop;
// End of hack by kennethburgener    

		input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
    
		if (selectionStart != selectionEnd){ 
			setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
		}else{
			setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
		}

	}else if (document.selection) {
		var range = document.selection.createRange();

		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			range.text = replaceString;

			 if (!isCollapsed)  {
				range.moveStart('character', -replaceString.length);
				range.select();
			}
		}
	}
}

// We are going to catch the TAB key so that we can use it, Hooray!
function catchTab(item,e){
	if(navigator.userAgent.match("Gecko")){
		c=e.which;
	}else{
		c=e.keyCode;
	}
	if(c==9){
		replaceSelection(item,String.fromCharCode(9));

// magistus 2006-11-06 Comment out the timeout as advised by kennethburgener to avoid IE jumping to top of page, works OK!
//		setTimeout("document.getElementById('"+item.id+"').focus();",0);	

		return false;
	}
		    
}

	
	function saveFile()
	{
		//alert(parent.cirotitle.document.getElementById('pippo').checked);
		//parent.ciroframe2.location.href = "http://www.google.com";
		document.f1.submit();
		alert('Saving!');
		if (parent.cirotitle.document.getElementById('pippo').checked == false)
		{
			parent.ciroframe2.location.replace('<? echo $folder1 ?>/'+'<? echo $content; ?>');
			//parent.ciroframe2.location.href = '<? echo $folder1 ?>/'+'<? echo $content; ?>';
		}
	}
</script>
<p id="p1"><input type=button name="button_save" value="Save" onclick="javascript:saveFile();"/>
</form>
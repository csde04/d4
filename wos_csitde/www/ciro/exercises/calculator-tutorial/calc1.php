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
<head>
	<title>Calculator</title>
	<script src="XXXXX.js"></script>
	<link id="style_link" rel=StyleSheet href="XXXXX.css" type="text/css" media=all>
</head>

<body onload="javascript:cancel();">
	<div id="main_div">
		<form>
		<input type=text id=display name=display readonly=true value="0">
		<input type=text id=display1 name=display1 disabled=true>
		
		<div id="digit-keyboard">
			<input type=button class="bcalc" id=bmplus name=bmplus value="m+" disabled=true>
			<input type=button class="bcalc" id=bmmenus name=bmmenus value="m-" XXXXX=true>
			<input type=button class="bcalc" id=bmr name=bmr value="mr" disabled=true>	
			<input type=button class="bcalc" id=b7 name=b7 value=7 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b8 name=b8 value=8 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b9 name=b9 value=9 onClick="javascript:makeNumber(this);">			
			<input type=button class="bcalc" id=b4 name=b4 value=4 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b5 name=b5 value=5 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b6 name=b6 value=6 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b1 name=b1 value=1 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b2 name=b2 value=2 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b3 name=b3 value=3 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=b0 name=b0 value=0 onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=bdot name=bdot value="." onClick="javascript:makeNumber(this);">
			<input type=button class="bcalc" id=bcanc name=bcanc value="c" onClick="javascript:cancel();">
		</div>
		<div id="operator-keyboard">
			<input type=button class="bcalc boper" id=bdivided name=bdivided value="/" onClick="javascript:makeOperation(this)">	
			<input type=button class="bcalc boper" id=btimes name=btimes value="*" onClick="javascript:makeOperation(this)">
			<input type=button class="bcalc boper" id=bminus name=bminus value="-" onClick="javascript:makeOperation(this)">
			<input type=button class="bcalc boper" id=bplus name=bplus value="+" onClick="javascript:makeOperation(this)">
			<input type=button class="bcalc boper" id=bequal name=bequal value="=" onClick="javascript:makeOperation(this)">
		</div>
		</form>
	</div>
</body>
</html>
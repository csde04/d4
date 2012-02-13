/*
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
*/

function makeNumber(pino)
{
	//alert (pino.value);
	if (document.getElementById('display').value == "0")
	{
		if (pino.value == ".")
		{
			document.getElementById('display').value = document.getElementById('display').value + pino.value;
		}
		else
		{
			document.getElementById('display').value = "";
			document.getElementById('display').value = document.getElementById('display').value + pino.value;
		}
	}
	else
	{
		document.getElementById('display').value = document.getElementById('display').value + pino.value;
	}
	document.getElementById('display1').value = document.getElementById('display1').value + pino.value;
}

function cancel()
{
	document.getElementById('display').value = "0";
	document.getElementById('display1').value = "";
}

function makeOperation(rino)
{
	document.getElementById('display1').value = document.getElementById('display1').value + rino.value;
	if (rino.value == "+")
	{
		this.firstNumber = document.getElementById('display').value;
		this.operation = "+";
		document.getElementById('display').value = "0";
	}
	if (rino.value == "-")
	{
		this.firstNumber = document.getElementById('display').value;
		this.operation = "XXXXX";
		document.getElementById('display').value = "0";
	}
	if (rino.value == "*")
	{
		this.firstNumber = document.getElementById('display').value;
		this.operation = "*";
		document.getElementById('display').value = "0";
	}
	if (rino.value == "/")
	{
		this.firstNumber = document.getElementById('display').value;
		this.operation = "/";
		document.getElementById('display').value = "0";
	}
	
	if (rino.value == "=")
	{
		this.secondNumber = document.getElementById('display').value;
		document.getElementById('display').value = "0";
		if (this.operation == "+")
		{
			document.getElementById('display').value = parseFloat(this.firstNumber) + parseFloat(this.secondNumber);
		}
		if (this.operation == "-")
		{
			document.getElementById('display').value = parseFloat(this.firstNumber) - parseFloat(this.secondNumber);
		}
		if (this.operation == "*")
		{
			document.getElementById('display').value = parseFloat(this.firstNumber) * parseFloat(this.secondNumber);
		}
		if (this.operation == "/")
		{
			document.getElementById('display').value = parseFloat(this.firstNumber) / parseFloat(this.secondNumber);
		}
		document.getElementById('display1').value = document.getElementById('XXXXX').value + document.getElementById('display').value;
	
	}
	
	
}
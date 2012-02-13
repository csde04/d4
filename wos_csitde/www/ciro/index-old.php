<?
	$folder = "../ciro/exercises";
	if (isset($_REQUEST['folder']))
	{
		$folder = $_REQUEST['folder'];
	}
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
<body>
<h2>CIRO <i>(v. 2.1)</i>. Elements of this directory: <i><? echo $folder; ?> </i></h2> 

<script>
	function double_load(pino)
	{
		alert(pino);
		if (document.getElementById('pippo').checked == false)
		{
		parent.ciroframe1.location.href = 'show_code.php?content='+pino+'&folder='+'<? echo $folder ?>';
		parent.ciroframe2.location.replace('<? echo $folder; ?>/'+pino);
		}
		else
		{
		parent.ciroframe1.location.href = 'show_code.php?content='+pino+'&folder='+'<? echo $folder ?>';
		}
	}
	
	function goDir(pinuzzo)
	{
		document.form2.folder.value = pinuzzo;
		document.form2.submit();
	}
</script>
<form name=form2 id=form2 action=index-old.php method=post>
<input type=hidden name=folder value="<?php echo $folder ?>">
<table border=0 >
<tr>
<td>
<table>
<tr><td class=td_link>(<a href="../phpmyadmin" target="ciroframe2">phpmyadmin</a>) 
<tr><td class="td_last">Display code only <input type=checkbox id=pippo >
</table>
<td ><table><tr>
<?php
	$i = 0;
	foreach(scandir($folder) as $value)
	{
		if ((fmod(($i+9),9)==0) && ($i > 0))
		{
			echo "<tr>";
		}
		if ($folder == "../ciro/exercises")
		{
			if ((is_dir($folder."/".$value) == false ) && $value != "Thumbs.db")
			{
				echo "<td><a href=javascript:double_load('" . $value . "')>". $value ."</a> ";
				$i++;
			}
			else
			{
				if ($value != "." && $value != ".." && $value != "Thumbs.db") 
				{
					echo "<td><a href=javascript:goDir('".$folder."/".$value."')><img src='folder_1.bmp'> ". $value."</a>";
					$i++;
				}
			}
			
		}
		else
		{
			if ((is_dir($folder."/".$value) == false ) && $value != "Thumbs.db")
			{
				echo "<td><a href=javascript:double_load('" . $value . "')>". $value ."</a> ";
				$i++;
			}
			else
			{
				$prefolder = $folder;
				
				if(is_dir($folder."/".$value) && ($value != "."))
				{
					if($value=="..")
					{
						while ($prefolder[strlen($prefolder)-1] != "/")
						{
							$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						}
						$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						echo "<td><a href=javascript:goDir('".$prefolder."')><img src='folder_su.bmp'> ". $value."</a>";
						$i++;
					}
					else
					{
						echo "<td><a href=javascript:goDir('".$folder."/".$value."')><img src='folder_1.bmp'> ". $value."</a>";
						$i++;
					}
				}
				/*
				if ($value != "." && $value != ".." && $value != "Thumbs.db") 
				{
					echo "<td><a href=javascript:goDir('".$folder."/".$value."')><img src='folder_1.bmp'> ". $value."</a>";
					$i++;
				}
				*/
			}
		}
	}


/*
$folder = "../ciro/exercises";

$dossier = opendir($folder);
	
	$i = 0;
	
	while ($fichier = readdir($dossier)) 
	{
		if ((fmod(($i+9),9)==0) && ($i > 0))
			{
				echo "<tr>";
			}
			
		if ($folder == "../ciro/exercises")
		{
			if ($fichier != "." && $fichier != ".." && $fichier != "Thumbs.db") 
			{
		//    if(is_dir($Fichier)) { // Do not always works
				if(is_dir($folder."/".$fichier)) // Works always
				{
					echo "<td><a href=javascript:goDir('".$folder."/".$fichier."')><img src='folder_1.bmp'> ". $fichier."</a>";
				} 
				else
				{
					//echo "<li><a href=javascript:double_load('" . $fichier . "')>". $fichier ."</a></li> ";
				}
			}
		}
		else
		{
			if ($fichier != "Thumbs.db" && $fichier != ".") 
			{
		//    if(is_dir($Fichier)) { // Do not always works
				//if(is_dir($folder."/".$fichier)) // Works always
				
				//echo "CURRENT FOLDER = ".$currentfolder."<BR>";
				
				$prefolder = $folder;
				
				if(is_dir($folder."/".$fichier))
				{
					if($fichier=="..")
					{
						while ($prefolder[strlen($prefolder)-1] != "/")
						{
							$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						}
						$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						echo "<td><a href=javascript:goDir('".$prefolder."')><img src='folder_su.bmp'> ". $fichier."</a>";
					}
					else
					{
						echo "<td><a href=javascript:goDir('".$folder."/".$fichier."')><img src='folder_1.bmp'> ". $fichier."</a>";
					}
				} 
				else
				{
					//echo "<li><a href=javascript:double_load('" . $fichier . "')>". $fichier ."</a></li> ";
				}
			}
		} 
		$i++;
	} 
	
	closedir($dossier);
	
	$dossier = opendir($folder);
	
	$i = 0;
	
	while ($fichier = readdir($dossier)) 
	{
		if ((fmod(($i+9),9)==0) && ($i > 0))
			{
				echo "<tr>";
			}
			
		if ($folder == "../ciro/exercises")
		{
			if ($fichier != "." && $fichier != ".." && $fichier != "Thumbs.db") 
			{
		//    if(is_dir($Fichier)) { // Do not always works
				if(is_dir($folder."/".$fichier)) // Works always
				{
					//echo "DIR: <a href=javascript:goDir('".$folder."/".$fichier."')>". $fichier."</a><BR>";
				} 
				else
				{
					echo "<td><a href=javascript:double_load('" . $fichier . "')>". $fichier ."</a> ";
				}
			}
		}
		else
		{
			if ($fichier != "Thumbs.db" && $fichier != ".") 
			{
		//    if(is_dir($Fichier)) { // Do not always works
				//if(is_dir($folder."/".$fichier)) // Works always
				
				//echo "CURRENT FOLDER = ".$currentfolder."<BR>";
				
				$prefolder = $folder;
				
				if(is_dir($folder."/".$fichier))
				{
					if($fichier=="..")
					{
						while ($prefolder[strlen($prefolder)-1] != "/")
						{
							$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						}
						$prefolder = substr($prefolder, 0, (strlen($prefolder) -1));
						//echo "DIR: <a href=javascript:goDir('".$prefolder."')>". $fichier."</a><BR>";
					}
					else
					{
						//echo "DIR: <a href=javascript:goDir('".$folder."/".$fichier."')>". $fichier."</a><BR>";
					}
				} 
				else
				{
					echo "<td><a href=javascript:double_load('" . $fichier . "')>". $fichier ."</a> ";
				}
			}
		} 
		$i++;
	} 
	
	closedir($dossier);
*/
?>
</table>

</table>
</form>
</body>
</html>
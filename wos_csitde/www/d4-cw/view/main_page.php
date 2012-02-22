<?
	foreach ($classes as $class_key => $class_value)
	{
		if ($here == $class_value)
		{
	
?>

<?

	if (($mode != "confirm_update") && ($mode != "confirm_create")) 
	{
		include "controller/controller_retrieve.php";
	}
?>

</div>


<?
	if ($mode == "create")
{
	echo "<div id='div_right'>";
	//echo "<script>document.getElementById('div2').style.width = '60%';</script>";
	echo "<script>display_div_right();</script>"; 
	include "view_create.php";
	echo "</div>";
	
}

if ($mode == "confirm_create")
{
	include "controller/controller_create.php";
	// echo "<script>alert('".$here." ".$_REQUEST['input_referred_as']." has been created');</script>";
	echo "<script>this.location = '".$current_file_name."?here=".$here."&mode=create&class_obj=".$here."&post_create=".$last_inserted_record."';</script>";
}

if ($mode == "update")
{
	echo "<div id='div_right'>";
	//echo "<script>document.getElementById('div2').style.width = '60%';</script>";
	echo "<script>display_div_right();</script>"; 
	
	include "view_update.php";
	echo "</div>";
}

if ($mode == "confirm_update")
{
	
	include "controller/controller_update.php";
	//echo "<script>alert('".$here." id ".$class_obj_id." (".$_REQUEST['input_referred_as'].") has been updated');</script>";
	echo "<script>this.location = '".$current_file_name."?here=".$here."&mode=update&class_obj_id=".$class_obj_id."&post_update=".$class_obj_id."';</script>";
	
}

if ($mode == "confirm_search")
{
	$mode = "search";
}

if ($mode == "search")
{
	echo "<div id='div_right'>";
	//echo "<script>document.getElementById('div2').style.width = '60%';</script>";
	echo "<script>display_div_right();</script>"; 
	include "view_retrieve.php";
	echo "</div>";
}



/*

if ($mode == "search" || $mode == "create" || $mode == "update")
{
	//echo "<p>HGAJSHGJSHGDJSGH";
	foreach ($join_tables as $jt_key => $jt_value)
	{
		$pos = strpos($jt_value,$here);
		if($pos === false) {
						// string needle NOT found in haystack
		}
		else {		// string needle found in haystack
						
			
			$there = str_replace("_"," ",$jt_value);
			$there = str_replace($here,"",$there);
			
			echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			include "view_displayjt.php";
			echo "</div>";
		}
	}
}

*/
?>

<?
		}		//end $here == "class_value
		
	}	// end foreach ($classes as $class_key => $class_value)
?>

<div id=bottom_div><img  src="include/images/vf1_logo.png" class="img_vf1"><p class="licence_disclaimer"><b>This program has been developed by using <a href="http://homepages.stca.herts.ac.uk/~comqvv/vf1">VF1</a>, possibly the simplest open-source free PHP development framework in the world! :-) You can redistribute VF1 and/or modify it under the terms of the <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a> as published by the Free Software Foundation. VF1 is distributed in the hope that it will be useful, but comes WITHOUT ANY WARRANTY. See the GNU General Public License for more details.</b>
</div>
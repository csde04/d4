<?
	// this is a hybrid controller/view file
	// to be amended in future versions of VF1
	
	
	
	if ($mode != "confirm_search") 
	{
?>	
	<table class=table1>
		<tr>
<?

	foreach (MyActiveRecord::Columns($class_value) as $class_attribute => $class_attr_value)
	{
		if (in_array($class_attribute,$foreign_keys))
		{
			foreach ($foreign_keys as $fk_key => $fk_value)
			{
				if ($class_attribute == $fk_value)
				{
					echo "<th><!--class_attribute=".$class_attribute." fk_key=".$fk_key." -->".name_child_relationship($class_value,$fk_value);
				}
			}
			//echo "<th>owned by";
		}
		else
		{
			echo "<th>".$class_attribute;
		}
	}
	
	
	foreach ($join_tables as $jt_key => $jt_value)
	{
		$pos = strpos($jt_value,$here);
		if($pos === false) {
						// string needle NOT found in haystack
		}
		else {		// string needle found in haystack
						
			$there = str_replace("_","",$jt_value);
			$there = str_replace($here,"",$there);
			
			echo "<th>associated ".$there;
			//echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			//include "view_displayjt.php";
			//echo "</div>";
		}
	}
	
	
	
	

	$obj_class = MyActiveRecord::FindBySql($class_value, 'SELECT * FROM '.$class_value.' WHERE id > -1 ORDER BY id');
	
	foreach ($obj_class as $obj_key => $obj_value)
	{
		echo "<tr>";
		foreach (MyActiveRecord::Columns($class_value) as $obj_attribute => $obj_attr_value)
		{
			if ($obj_attribute=="id")
			{
				// ####################################### if here = access do not allow update ################################# //
				if( $here == "access")
				{
					echo "<td>".$obj_value->$obj_attribute;
				}
				else
				{
					echo "<td><a href=javascript:update_obj('".$current_file_name."','".$class_value."',".$obj_value->$obj_attribute.");>".$obj_value->$obj_attribute."</a>";
				};
			}
			else if (strlen($obj_attribute)> 2 && !(strpos($obj_attribute,"_id")===false))
			{
				//$related_class = substr($obj_attribute, 0, -3);
				$related_class = find_relatedclass($obj_attribute,$foreign_keys);
				//echo "<td>related_class = ".$related_class;
				echo "<td>".$obj_value->$obj_attribute.". ".$obj_value->find_parent($related_class,$obj_attribute)->referred_as;
				
				/*
				if($obj_attribute == "from_location_id")
				{
					echo "CIAO!!!! ".$related_class." - ".$obj_value->find_parent($related_class,$obj_attribute)->referred_as;
				}
				*/
				
			}
			else
			{
				echo "<td>".$obj_value->$obj_attribute;
			}
		}
		
		//////
		
		foreach ($join_tables as $jt_key => $jt_value)
	{
		$pos = strpos($jt_value,$here);
		if($pos === false) {
						// string needle NOT found in haystack
		}
		else {		// string needle found in haystack
						
			$there = str_replace("_","",$jt_value);
			$there = str_replace($here,"",$there);
			
			echo "<td>";
			$i = 0;
			foreach ($obj_value->find_attached($there) as $_fakey => $_favalue)
			{
				if ($i == 0)
				{
				echo " ".$_favalue->referred_as;
				$i++;
				}
				else
				{
				echo ", ".$_favalue->referred_as;
				$i++;
				}
			}
			
			//echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			//include "view_displayjt.php";
			//echo "</div>";
		}
	}
		
		///////
		
	}
	
?>
	</table>
<?
	} // end $mode != "confirm_search"
	
	
	else      //  if $mode is equal to "confirm_search"!!!
	{
	
?>
<table class=table1>
		<tr>
<?
	
	$class_obj = $_REQUEST['class_obj'];
	
	$search_operator = $_REQUEST['search_operator'];
	
	foreach (MyActiveRecord::Columns($class_value) as $class_attribute => $class_attr_value)
	{
		if (in_array($class_attribute,$foreign_keys))
		{
			foreach ($foreign_keys as $fk_key => $fk_value)
			{
				if ($class_attribute == $fk_value)
				{
					echo "<th><!--class_attribute=".$class_attribute." fk_key=".$fk_key." -->".name_child_relationship($class_value,$fk_value);
				}
			}
			//echo "<th>owned by";
		}
		else
		{
			echo "<th>".$class_attribute;
		}
	}
	
	foreach ($join_tables as $jt_key => $jt_value)
	{
		$pos = strpos($jt_value,$here);
		if($pos === false) {
						// string needle NOT found in haystack
		}
		else {		// string needle found in haystack
						
			$there = str_replace("_","",$jt_value);
			$there = str_replace($here,"",$there);
			
			echo "<th>associated ".$there;
			//echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			//include "view_displayjt.php";
			//echo "</div>";
		}
	}
	
	
	
	$strSQLsearch = 'Select * from '.strtolower($class_obj).' where id>=0 ';  // the search query has been initialised
	$strSQLor = 'Select * from '.strtolower($class_obj).' where id<0 ';
	$strSQLor_mod = 0;
	
	$pino = array();		// this is a local array, unneeded here: it should have been commented: but since it is not used, it doesn't cause too many troubles
	
	// by means of the following loop, the search query is developed (each loop adds a condition after the "where")
	
	foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
	{
		if (substr($key_REQUEST,0,6) == 'input_')
		{
			$local_attrib = substr($key_REQUEST,6);
			if ($value_REQUEST != "")
			{
				if ($search_operator == 'AND')
				{
				$strSQLsearch = $strSQLsearch." ".$search_operator." ".$local_attrib." = '".$value_REQUEST."' ";    // the search query gets incremented by "and columnX = 'valueX.'.. and columnY = 'valueY'
				}
				else
				{
				$strSQLor = $strSQLor." ".$search_operator." ".$local_attrib." = '".$value_REQUEST."' "; 
				$strSQLor_mod = 1;
				}
			}							// in mySQL, also integers can be "quoted"... This is a loop to create a very basic search query			
		}
	}
	
	if ($search_operator == 'OR')
	{
		if ($strSQLor_mod == 1)
		{
			$strSQLsearch = $strSQLor;
		}
		else
		{
			$strSQLsearch = $strSQLsearch;
		}
	}
	else
	{
		$strSQLsearch = $strSQLsearch;
	}	
	
	//here the search criteria include any further filter based on the related join_table(s) 
	$relation_class = '';
	$relation_name = $_REQUEST['jt_name'];
	$relation_class = $_REQUEST['jt_class'];
	
	if($relation_class != '')
	{
		$sqlSQLmod_rel = 0;
		
		if ($search_operator == 'OR')
		{
			$innerSelect = "Select ".$class_value."_id from ".$relation_name." where false ";
			foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
			{
				if (substr($key_REQUEST,0,9) == 'jt_input_')
				{
					$that_id = $value_REQUEST;
					$innerSelect = $innerSelect." or ".$relation_class."_id = ".$that_id;
				
					$sqlSQLmod_rel = 1;
				}
			}
		}
		else   //search_operator = 'AND'
		{
			$innerSelect = "Select ".$class_value."_id from ".$relation_name." where true ";
			$innerSelect1 = "Select ".$class_value."_id from ".$relation_name." where true ";      //da sistemare...
			foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
			{
				if ($sqlSQLmod_rel == 0)
				{
					if (substr($key_REQUEST,0,9) == 'jt_input_')
					{
						$that_id = $value_REQUEST;
						$innerSelect = $innerSelect." and ".$relation_class."_id = ".$that_id;
						$sqlSQLmod_rel = 1;
					}
				}
				if($sqlSQLmod_rel >= 1)
				{
					if (substr($key_REQUEST,0,9) == 'jt_input_')
					{
						$that_id = $value_REQUEST;
						$innerSelect = $innerSelect." and ".$class_value."_id in (".$innerSelect1." and ".$relation_class."_id = ".$that_id.") ";
						$sqlSQLmod_rel ++;
					}
				}
			}
		}
		
		if ($sqlSQLmod_rel == 0)
		{
			$strSQLsearch = $strSQLsearch;
		}
		else
		{
			if ($search_operator == 'OR')
			{
				$strSQLsearch = $strSQLor;
			}
			$strSQLsearch = $strSQLsearch." ".$search_operator." id in (".$innerSelect.")";
		}
	}
	
	
	//////////
	
	//echo "<p>".$strSQLsearch;
	
	$obj_class = MyActiveRecord::FindBySql($class_value, $strSQLsearch);
	
	foreach ($obj_class as $obj_key => $obj_value)
	{
		echo "<tr>";
		foreach (MyActiveRecord::Columns($class_value) as $obj_attribute => $obj_attr_value)
		{
			if ($obj_attribute=="id")
			{
				echo "<td><a href=javascript:update_obj('".$current_file_name."','".$class_value."',".$obj_value->$obj_attribute.");>".$obj_value->$obj_attribute."</a>";
			}
			else if (strlen($obj_attribute)> 2 && !(strpos($obj_attribute,"_id")===false))
			{
				//$related_class = substr($obj_attribute, 0, -3);
				$related_class = find_relatedclass($obj_attribute,$foreign_keys);
				
				//echo "<td>".$obj_value->$obj_attribute.". ".$obj_value->find_parent($related_class)->referred_as;
				echo "<td>".$obj_value->$obj_attribute.". ".$obj_value->find_parent($related_class,$obj_attribute)->referred_as;
			}
			else
			{
				echo "<td>".$obj_value->$obj_attribute;
			}
		}
		
				//////
		
		foreach ($join_tables as $jt_key => $jt_value)
	{
		$pos = strpos($jt_value,$here);
		if($pos === false) {
						// string needle NOT found in haystack
		}
		else {		// string needle found in haystack
						
			$there = str_replace("_","",$jt_value);
			$there = str_replace($here,"",$there);
			
			echo "<td>";
			$i = 0;
			foreach ($obj_value->find_attached($there) as $_fakey => $_favalue)
			{
				if ($i == 0)
				{
				echo " ".$_favalue->referred_as;
				$i++;
				}
				else
				{
				echo ", ".$_favalue->referred_as;
				$i++;
				}
			}
			
			//echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			//include "view_displayjt.php";
			//echo "</div>";
		}
	}
		
		///////
		
		
		
		
		
	}
	
	
	//echo $strSQLsearch."<br>";  Check the SQl string has been properly formed
	
?>

	</table>

<?
	}   //end else ($mode is equal to "confirm_search)" !!!
?>
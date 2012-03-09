<?
	// this is a hybrid controller/view file
	// to be amended in future versions of VF1
	
	// Set the status of the card if the mode==cancel_card using the card id
	if( $mode == "cancel_card")
	{
		$id = $_REQUEST[ 'card_id'];
		$toCancel = MyActiveRecord::FindById( 'card', $id);
		
		// set the status id to 3 (cancelled)
		$toCancel->status_id = 3; 
		$toCancel->save();
	}
	
	
	//generate a page selection form
		if( isset(  $_REQUEST[ 'page']))
		{
			$pageNumber = $_REQUEST[ 'page'];
		}
		else
		{
			$pageNumber = 0;
		}
		
		if( isset(  $_REQUEST[ 'rows']))
		{
			$rowsPerPage = $_REQUEST[ 'rows'];
		}
		else
		{
			$rowsPerPage = 10;
		}
	
	$pageOffset = $rowsPerPage * $pageNumber;
	
	// get the number of rows in the table so we can calculate how many pages we can have.
	$rowCount = MyActiveRecord::Count($class_value);
	$totPages = $rowCount / $rowsPerPage;
	
	/*
	echo " page ".$pageNumber;
	echo " perpage ".$rowsPerPage;
	echo " offset ".$pageOffset;
	echo " rows ".$rowCount;
	echo " tot ".$totPages;
	*/
?>
	<form name="pagecontrol">
	
		<input type="hidden" name="here" value="<? echo $here; ?>"/>
		
		<?
		// these essentially just forward the values on when the page reloads
		if( isset( $_REQUEST[ 'mode']))
		{
			echo "<input type=hidden name='mode' value='" .$mode. "'/>";
		}
		
		if(  isset( $_REQUEST[ 'class_obj']))
		{
			echo "<input type=hidden name='class_obj' value='" .$_REQUEST[ 'class_obj']. "'/>";
		}
		
		if(  isset( $_REQUEST[ 'class_obj_id']))
		{
			echo "<input type=hidden name='class_obj_id' value='" .$_REQUEST[ 'class_obj_id']. "'/>";
		}
		
		
		?>
		<table name="pageselect" border=0>
			<tr>
				<td>Page:
					<select name="page">
						<?
							for( $i = 0; $i < $totPages; $i++)
							{
								if( $i == $pageNumber)
								{
									echo "<option  selected='selected' value='".$i."' >".$i."</option>";
								}
								else
								{
									echo "<option value='".$i."'>".$i."</option>";
								}
							}
						?>
					</select>
					<?
						// if on the first page only draw the next link.
						if( $pageNumber == 0)
						{
							
						}
						// if on the last page ... 
						else if( $pageNumber == $totPages)
						{
							
						}
						else
						{
							
						}
					?>
				</td>
				<td>Records Per Page:
					<select name="rows" >
						<?
							// Needs a JS action to set the "page" select to 0 when this is changed.
							for( $i = 1; $i <= 50; $i++)
							{
								if( $i == $rowsPerPage)
								{
									echo "<option  selected='selected' value='".$i."' >".$i."</option>";
								}
								else
								{
									echo "<option value='".$i."'>".$i."</option>";
								}
							}
						?>
					</select>
				</td>
				<td>
					<input type="submit" value="Go"/>
				</td>
			</tr>
		</table>
	</form>
<?
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
	
	$obj_class = MyActiveRecord::FindBySql($class_value, 'SELECT * FROM '.$class_value.' WHERE id > -1 ORDER BY id LIMIT '.$pageOffset.' , '.$rowsPerPage );
	
	foreach ($obj_class as $obj_key => $obj_value)
	{
		// Restricts the rows being drawn to the specified page.
		//$rowCount++;
		//if( $rowCount <= $pageOffset + $rowsPerPage && $rowCount > $pageOffset)
		//{
			echo "<tr>";
			// this draws the rows in the table
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
						
						// ################################### Add a cancel button if here == card ################################# //
						if( $here == "card" && $obj_value->status_id != 3)
						{
							// Make the "C" image link
							echo " - <a href=javascript:confirm_cancel_card('".$current_file_name."',".$obj_value->$obj_attribute."); title='Cancel this card'><img src='/d4-cw/include/images/cancel.png' /></a>";
						}
					}
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
		//}
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
	
	//echo "<p>".$strSQLsearch ."LIMIT '".$pageOffset."' , '".$rowsPerPage;
	
	// searches the DB with the correct limits according to the page and number of rows.
	$obj_class = MyActiveRecord::FindBySql($class_value, $strSQLsearch .'LIMIT '.$pageOffset.' , '.$rowsPerPage);
	//$obj_class = MyActiveRecord::FindBySql($class_value, $strSQLsearch ."LIMIT 0 , 2");
	
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
					
					// ################################### Add a cancel button if here == card ################################# //
						if( $here == "card" && $obj_value->status_id != 3)
						{
							// Make the "C" image link
							echo " - <a href=javascript:confirm_cancel_card('".$current_file_name."',".$obj_value->$obj_attribute."); title='Cancel this card'><img src='/d4-cw/include/images/cancel.png' /></a>";
						}
				}
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
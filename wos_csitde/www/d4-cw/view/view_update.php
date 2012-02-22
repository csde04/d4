<?

	$class_obj_id=$_REQUEST['class_obj_id'];
	
	$class_obj = MyActiveRecord::FindById($here,$class_obj_id);

	
	echo "<p class=p1>UPDATE ".$here."</p>";
	
	if (isset($_REQUEST['post_update']))
	{
		post_update_message($_REQUEST['post_update'],$here);
	}
	
	//echo "</p>";
	echo "<table class=table1><form id='update_".$here."' action=".$current_file_name."?here=".$here."&mode=confirm_update&class_obj=".$here."&class_obj_id=".$class_obj_id." method=post>";
	
	$w_columns = MyActiveRecord::Columns($class_obj);
	foreach($w_columns as $wcolumns_key => $wcolumns_value)
	{
		if ($wcolumns_key == "id")
		{
			echo "<tr><td>".$wcolumns_key."<td><input type=text name='input_".$wcolumns_key."' value='".$class_obj->$wcolumns_key."' readonly=true>";
		}
		
		else
		{
			if(MyActiveRecord::GetType($class_obj,$wcolumns_key) == 'date')
			{
				echo "<tr><td>".$wcolumns_key."<td><input type=text id='input_".$wcolumns_key."' name='input_".$wcolumns_key."' value='".$class_obj->$wcolumns_key."'>";
				echo "<td><input type=button value='Set Date' onclick=displayDatePicker('input_".$wcolumns_key."',false,'ymd','-'); >";
				
				//echo "<tr id='arow'><td>".$wcolumns_key."<td><input type=text id='input_".$wcolumns_key."' name='input_".$wcolumns_key."' value='' datepicker='true' datepicker_format='DD/MM/YYYY'>";
				//echo "<tr><td>".$wcolumns_key."<td><input type=text id='input_".$wcolumns_key."' name='input_".$wcolumns_key."' value='' datepicker='true' datepicker_format='DD/MM/YYYY'><td><div id='aaa'>&nbsp;</div><script>var b = new free_date_picker('b', 'aaa', 'input_".$wcolumns_key."', 1, true, 'en');</script>";
				
				
			}
			elseif (strlen($wcolumns_key)> 2 && !(strpos($wcolumns_key,"_id")===false))
			{
				//$related_class = substr($wcolumns_key, 0, -3);
				$related_class = find_relatedclass($wcolumns_key,$foreign_keys);
				echo "<tr><td>".$wcolumns_key."<td><input type=text id='input_".$wcolumns_key."' name='input_".$wcolumns_key."' value='".$class_obj->$wcolumns_key."'><td><select id='select_".$wcolumns_key."' onChange=javascript:change_obj('".$wcolumns_key."')>";
				
				foreach ($obj_class = MyActiveRecord::FindBySql($related_class, 'SELECT * FROM '.$related_class.' WHERE id > -1 ORDER BY referred_as') as $obj_attribute => $obj_attr_value)
				{
					// echo "<option>".$obj_attribute." - ".$obj_attr_value->referred_as;    // it works, but...
					
					echo "<option ";
					if ($obj_attr_value->id == $class_obj->$wcolumns_key)
					{
						echo "selected";
					}
					
					echo "  value='".$obj_attr_value->id."' >".$obj_attr_value->id." - ".$obj_attr_value->referred_as;
					
					//echo "(".$wcolumns_key.")";
					
					if (strlen($wcolumns_key)> 2 && !(strpos($wcolumns_key,"_id")===false))
					{
						//$related_superclass = substr($wcolumns_key, 0, -3);
						$related_superclass = find_relatedclass($wcolumns_key,$foreign_keys);
						foreach ($super_obj = MyActiveRecord::Columns($related_superclass) as $super_obj_attribute => $super_obj_value)
						{
							if (strlen($super_obj_attribute)> 2 && !(strpos($super_obj_attribute,"_id")===false))
							{
								//$related_supersuperclass = substr($super_obj_attribute, 0, -3);
								$related_supersuperclass = find_relatedclass($super_obj_attribute,$foreign_keys);
								
								//$related_superobj = $obj_attr_value->find_parent($related_supersuperclass)->referred_as;
								$related_superobj = $obj_attr_value->find_parent($related_supersuperclass,$super_obj_attribute)->referred_as;
								
								//echo "<td>".$obj_value->$obj_attribute.". ".$obj_value->find_parent($related_class,$obj_attribute)->referred_as;
								
								echo " (".$related_superobj.")";
							}
						}
					}
				}
				echo "</select>";
			}
			else
			{
				echo "<tr><td>".$wcolumns_key."<td><input type=text name='input_".$wcolumns_key."' value='".$class_obj->$wcolumns_key."'>";
			}
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
			
			//echo "<script>document.getElementById('div_right').style.height = '230px';document.getElementById('div_right').style.border = 'none';</script><div id=div3>";
			//echo "<p class=p1>manage the ".$jt_value." relationship by the following criterion: ";
			include "view_displayjt.php";
			//echo "</div>";
		}
	}
	
	echo "<tr><td><td><input type=button value='Update this ".$here."' onClick=javascript:confirm_update('update_".$here."')><td><input type=reset></table></form>";
	
?>
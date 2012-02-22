<?
	//echo " - jt_value = ".$jt_value." - here = ".$here;
	//echo "</i>".$here."</i> (other class: ".$there.")";
	
	if ($mode == 'create')
	{

	echo "<tr><th>associated ".$there." <th>(mode: ".$mode.")";
	$i = 0;
	foreach ($obj_class = MyActiveRecord::FindBySql($there, 'SELECT * FROM '.$there.' WHERE id > -1 ORDER BY referred_as') as $obj_attribute => $obj_attr_value)
				{
					// echo "<option>".$obj_attribute." - ".$obj_attr_value->referred_as;    // it works, but...
					
					echo "<tr><td><td>".$obj_attr_value->referred_as." <td><input type=checkbox id='jt_input_".$there."_".$i."' name='jt_input_".$there."_".$i."' value='".$obj_attr_value->id."'>";
					$i++;
					
				}
	echo "<input type=hidden name='jt_name' value='".$jt_value."'><input type=hidden name='jt_class' value='".$there."'>";
	
	}
	
	if ($mode == 'update')
   	{
		echo "<tr><th>associated ".$there." <th>(mode: ".$mode.")";
	$i = 0;
	foreach ($obj_class = MyActiveRecord::FindBySql($there, 'SELECT * FROM '.$there.' WHERE id > -1 ORDER BY referred_as') as $obj_attribute => $obj_attr_value)
				{
					// echo "<option>".$obj_attribute." - ".$obj_attr_value->referred_as;    // it works, but...
					
					echo "<tr><td><td>".$obj_attr_value->referred_as." <td><input type=checkbox id='jt_input_".$there."_".$i."' name='jt_input_".$there."_".$i."' value='".$obj_attr_value->id."'";
					foreach ($class_obj->find_linked($there) as $pp_key => $pp_value)
					{
					//echo " xxx: ".$obj_attr_value->find_attached($there)->id;
						// echo " xxx: ".$pp_value->id;
						if ($pp_value->id == $obj_attr_value->id)
						{
							echo " checked=true ";
						}
					}
					echo ">";
					
					$i++;
					
				}
	echo "<input type=hidden name='jt_name' value='".$jt_value."'><input type=hidden name='jt_class' value='".$there."'>";
	}
	
	
	if($mode == 'search')
	{
		echo "<tr><th>associated ".$there." <th>(mode: ".$mode.")";
	$i = 0;
	foreach ($obj_class = MyActiveRecord::FindBySql($there, 'SELECT * FROM '.$there.' WHERE id > -1 ORDER BY referred_as') as $obj_attribute => $obj_attr_value)
				{
					// echo "<option>".$obj_attribute." - ".$obj_attr_value->referred_as;    // it works, but...
					
					echo "<tr><td><td>".$obj_attr_value->referred_as." <td><input type=checkbox id='jt_input_".$there."_".$i."' name='jt_input_".$there."_".$i."' value='".$obj_attr_value->id."'";
					
					if ($_REQUEST['jt_input_'.$there.'_'.$i] == $obj_attr_value->id)
					{
						echo " checked ";
					}
					
					echo ">";
					$i++;
					
				}
	echo "<input type=hidden name='jt_name' value='".$jt_value."'><input type=hidden name='jt_class' value='".$there."'>";
	}

?>
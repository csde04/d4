<?
 $class_obj=$_REQUEST['class_obj'];
	
	$pino = array();	// this is a local array used to store retrieved attributes of selected objects
	
	foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
	{
		if (substr($key_REQUEST,0,6) == 'input_')
		{
			if ($key_REQUEST != "input_id")
			{
			$pino = $pino + array(substr($key_REQUEST,6) => $value_REQUEST);
			}
		}
	}
	//echo "<P>.".print_r($pino);
   $this_obj = MyActiveRecord::Create($class_obj, $pino );
   
   $this_obj->save();			// crucial command: disactivate  only if you don't want to save... 
   
   $last_inserted_record = $this_obj->id;
   
   $relation_name = $_REQUEST['jt_name'];
   $relation_class = $_REQUEST['jt_class'];
   
   
  // echo "<p>relation_name = ".$relation_name." - strpos = ".strpos ($relation_name,$class_obj)."";
  
 
   echo "<p>";
   
   foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
	{
		if (substr($key_REQUEST,0,9) == 'jt_input_')
		{
			//$pino = (substr($key_REQUEST,9) => $value_REQUEST);
				
				$that_id = $value_REQUEST;
				 //echo " that_id = ".$that_id;
				 //echo " key = ".$key_REQUEST;


				if (strpos($relation_name,$class_obj)>0)
				{
					$obj2 = $this_obj;
					//$obj1 = $that_id;
					$obj1 = MyActiveRecord::FindById($relation_class, $that_id);
				}
				else
				{
					$obj1 = $this_obj;
					$obj2 = MyActiveRecord::FindById($relation_class, $that_id);
					//$obj2 = $that_id;
				}
				//MyActiveRecord::Link($obj1,$obj2);
				MyActiveRecord::Link($obj1,$obj2);
				//echo "rel_name = ".$relation_name." - class = ".$class_obj." pos = ".strpos($relation_name,$class_obj)." obj1 = ".$obj1->id." - obj2 = ".$obj2->id."; ";

		}
	}
	
?>
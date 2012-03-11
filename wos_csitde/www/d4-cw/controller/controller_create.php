<?

	/**TODO:	
	secure card state creation
	if date is valid
	AND date != current date
	card state = 2
	
	deletion of records
	
	cancellation 
	
	check returned record set for clashing dates
	note: some sort of array functionallity
	**/


 $class_obj=$_REQUEST['class_obj'];
	
	$pino = array();	// this is a local array used to store retrieved attributes of selected objects
	

	/**if the input fields are null: error and prompt user for correct input**/
	
	foreach($_REQUEST as $key_REQUEST => $value_REQUEST)
	{
		if($value_REQUEST == "")
		{		
			$bad_fields = 1;	
		}
	}
	
	if($bad_fields == 1)
	{
		?>
		<script type="text/javascript">
		alert( "Check field entry and try again, all fields must be entered");
		</script>
		<?
	}
	
	if($bad_fields != 1)
	{
		/**get time input fields**/
		
		foreach($_REQUEST as $key_REQUEST => $value_REQUEST)
		{
			if($key_REQUEST == 'input_startdate')
			{
				$temp_start_db = $value_REQUEST;
				$temp_start = MyActiveRecord::TimeStamp($value_REQUEST);
				$checkDate = 1;
			}
			else if($key_REQUEST == 'input_expirydate')
			{
				$temp_end_db = $value_REQUEST;
				$temp_end = MyActiveRecord::TimeStamp($value_REQUEST);
				$checkDate = 1;
			}
		}


		/**compare input date constraints with current time and other boundary controls**/
		if($checkDate == 1 && ($temp_start > $temp_end || $temp_end < $temp_start || strtotime(date("Y-m-d")) > $temp_start || strtotime(date("Y-m-d")) > $temp_end))
		{
			?>
			<script type="text/javascript">
			alert( "Please Enter a correct card validity period");
			</script>
			<?
			$bad_date = 1;
		}
		
		/**if date is bad don't run save/create commands**/
		
		if($bad_date != 1)
		{
	
			foreach($_REQUEST as $key_REQUEST => $value_REQUEST)
			{
				if($key_REQUEST == 'input_staff_id')
				{
					$temp_id = $value_REQUEST;
					$card_obj2 = MyActiveRecord::FindBySQL("card", "SELECT staff_id,startdate,expirydate FROM card WHERE staff_id='".$temp_id."' AND status_id!=3 AND 
					(('".$temp_start_db."' >= startdate AND '".$temp_start_db."' <= expirydate) OR ('".$temp_end_db."' >= startdate AND '".$temp_start_db."' <= expirydate))");
					if(count($card_obj2) != 0)
					{
						?>
						<script type="text/javascript">
						alert( "Staff member already has an active card for that date period");
						</script>
						<?
						
						$bad_card = 1;
						break;
					} 
				}
			}
		
			if($bad_card != 1)
			{
				/**check current database for card records and test constraints**/
				foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
				{
					if (substr($key_REQUEST,0,6) == 'input_')
					{
						if ($key_REQUEST != "input_id")
						{
							
							if($here == 'card' && $mode == 'confirm_create' && $key_REQUEST == '')
							{
								
							}
							
							$pino = $pino + array(substr($key_REQUEST,6) => $value_REQUEST);
							
						}
					}
				}
				
				/**if the card has violated its constraints then the save/create methods will not be executed**/
				
				if($bad_card != 1)
				{
					/*create status_id field*/
				
					if(strtotime(date("Y-m-d")) == $temp_start)
					{
						$pino = $pino + array("status_id" => 1);
					}
					else
					{
						$pino = $pino + array("status_id" => 2);
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
				}	
			}
		}
	}
?>
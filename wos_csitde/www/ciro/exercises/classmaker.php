<html>
<link id="style_link" rel=StyleSheet href="style.css" type="text/css" media=all>
<body>
<P><a href="classmaker.php">REFRESH</a>

<?php

define('MYACTIVERECORD_CONNECTION_STR', 'mysql://root@localhost/cw0910');
include 'MyActiveRecord.0.4.php';


class Driver extends MyActiveRecord 
{
	function destroy()
	{
		// clean up attached People (drivers) on destroy
		//foreach( $this->find_attached('Driver') as $driver ) $this->detach($driver);
		//return parent::destroy();
	}	
}

//class FemaleDriver extends Driver {}
//class MaleDriver extends Driver {}

class Car extends MyActiveRecord 
{
	function destroy()
	{

	}	
}


?>

<? $mode = $_REQUEST['mode'];?>

<div id="classmakerlist_sx"> 
<?
echo "<P id='p1'>Drivers:</P>\n";

$driver = MyActiveRecord::FindBySql('Driver', 'SELECT * FROM driver WHERE id > 1 ORDER BY last');

$car = MyActiveRecord::FindBySql('Car', 'SELECT * FROM car WHERE id > 1 ORDER BY make');

foreach($driver as $driver_key => $driver_value)
{
	
	$driver_value->drives = $driver_value->find_attached('Car');
	
	echo "<P> - ".$driver_value->first." ".$driver_value->last." (id: ".$driver_value->id.", class: ".$driver_value->class.")";
}

echo "<P id='p1'>Cars:</P>\n";

foreach ($car as $car_key => $car_value)
{
	$car_value->owned = $car_value->find_parent('Driver');
	
	echo "<P> - ".$car_value->make." ".$car_value->model." (id: ".$car_value->id.", owner: ". $car_value->owned->first." ".$car_value->owned->last.")";
}


/*

$ka = MyActiveRecord::FindBySql('Car', 'SELECT * FROM car WHERE id > 1 ORDER BY model');
foreach( $ka as $ka_item ) print $ka_item->model;
echo "<BR>";
print_r($ka);

foreach(MyActiveRecord::Columns('car') as $key => $value)
{
	//echo "<BR>".$key ." -> ".$value;
	echo "<BR>"."$ka->$key = ".$ka_item->$key." - ". "$ka->model ".$ka_item->model ; 
}

echo "<HR>"; */

?>
</div>

<div id="classmakerlist_dx"> 

<?
echo "<P id='p1'>Relationships by drivers:</P>\n";


foreach($driver as $driver_key => $driver_value)
{
	/*
	$driver->owns = $driver->find_children('Car');
	$driver->drives  = $driver->find_attached('Car');
	print_r($driver);
	*/
	
	$driver_value->drives = $driver_value->find_attached('Car');
	
	echo "<P> - ".$driver_value->first." ".$driver_value->last." drives ";
	
	foreach ($driver_value->drives as $downs_key => $downs_value)
	{
		echo ($downs_value->make). " ";
	}
	
	$driver_value->owns = $driver_value->find_children('Car');
	
	echo "<P> ".$driver_value->first." ".$driver_value->last." owns ";
	
	foreach ($driver_value->owns as $downs_key => $downs_value)
	{
		echo ($downs_value->make). " ".$downs_value->model;
	}
	
	//echo "<P>Driver ".$driver->first." owns " .print_r($driver);	
	//$driver->drives  = $driver->find_attached('Car');
	//echo "<P>".print_r($driver);
}

echo "<P id='p1'>Relationships by cars:</P>\n";

foreach ($car as $car_key => $car_value)
{
	$car_value->owned = $car_value->find_parent('Driver');
	
	echo "<P> - ".$car_value->make." ".$car_value->model." owned by ";
	echo $car_value->owned->first;
	
	foreach ($car_value->owned as $cowns_key => $cowns_value)
	{
		echo ($cowns_value->first). " ";
	}
}

foreach ($car as $car_key => $car_value)
{
	$car_value->driven_by = $car_value->find_attached('Driver');
	
	echo "<P> - ".$car_value->make." ".$car_value->model." driven by ";

	
	foreach ($car_value->driven_by as $cowns_key => $cowns_value)
	{
		echo ($cowns_value->first). " ";
	}
}
?>
</div>

<div id="classmaker_bottom"> 
<a href="classmaker.php?mode=create&class_obj=Driver">Create new driver</a> ¦ 
<a href="classmaker.php?mode=create&class_obj=Car">Create new car</a> ¦
<a href="classmaker.php?mode=create_relationship&class1=Car&class2=Driver">Create new driving relationship</a>

<?
if ($mode == "create")
{
	$class_obj=$_REQUEST['class_obj'];
	
	echo "<table><form action=classmaker.php?mode=confirm_create&class_obj=".$class_obj." method=post>";
	
	$w_columns = MyActiveRecord::Columns($class_obj);
	foreach($w_columns as $wcolumns_key => $wcolumns_value)
	{
		if ($wcolumns_key != "id")
		{
			echo "<tr><td>".$wcolumns_key."<td><input type=text name='input_".$wcolumns_key."' value=''>";
		}
	}
	
	echo "</table><input type=submit></form>";
}


if ($mode == "confirm_create")
{
	$class_obj=$_REQUEST['class_obj'];
	
	$pino = array();
	
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
	echo "<P>.".print_r($pino);
   $john = MyActiveRecord::Create($class_obj, $pino );
   $john->save();
	echo "<script>this.location = 'classmaker.php';</script>";
   
}


if ($mode == "create_relationship")
{

	echo "<P><i>Drives</i> relationship";
	echo "<table id='table_relationship'><form action=classmaker.php?mode=confirm_relationship&class1=Driver&class2=Car method=post>";
	echo "<tr><td>";
	foreach($driver as $driver_key => $driver_value)
	{
		$driver_value->drives = $driver_value->find_attached('Car');
		//echo "<P> - ".$driver_value->first." ".$driver_value->last." (id: ".$driver_value->id.", class: ".$driver_value->class.")";
		echo "<br><input type=radio name='input_driver' value='".$driver_value->id."' > ".$driver_value->first." ".$driver_value->last;
	}
	echo "<td>";
	foreach ($car as $car_key => $car_value)
	{
		$car_value->owned = $car_value->find_parent('Driver');
		//echo "<P> - ".$car_value->make." ".$car_value->model." (id: ".$car_value->id.", owner: ". $car_value->owned->first." ".$car_value->owned->last.")";
		echo "<br><input type=checkbox name='input_car_".$car_value->id."' value='".$car_value->id."'> ".$car_value->make." ".$car_value->model;
	}
	echo "<td><input type=submit>";
	echo "</table></form>"; 
}


if ($mode == "confirm_relationship")
{
	$pino = array();
	$gino = 0;
	
	foreach ($_REQUEST as $key_REQUEST => $value_REQUEST)
	{
		//echo "<P>key = ".$key_REQUEST."; value = ".$value_REQUEST;
		
		if (substr($key_REQUEST,0,12) == 'input_driver')
		{
			if ($key_REQUEST != "input_id")
			{
			$gino = $value_REQUEST;
			}
		}
		
		if (substr($key_REQUEST,0,9) == 'input_car')
		{
			if ($key_REQUEST != "input_id")
			{
			$pino = $pino + array(substr($key_REQUEST,10) => $value_REQUEST);
			}
		}

	}
	
	foreach ($pino as $pino_key => $pino_value)
	{
		echo "<P>Driver: ".$gino." drives now Car: ".$pino_value."";
		
		$driver_obj = MyActiveRecord::Create('Driver', array('id'=>$gino));
		$car_obj = MyActiveRecord::Create('Car', array('id'=>$pino_value));
		
		MyActiveRecord::unlink($driver_obj, $car_obj);
		MyActiveRecord::link($driver_obj, $car_obj);
		
		echo "<script>this.location = 'classmaker.php';</script>";
	}
	//echo "<P>.".print_r($pino);
	
}
?>


</div>


</body>
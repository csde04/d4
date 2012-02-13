<?
	
	// The application title is defined here 
	$application_title = "SAMS-Prototype";
	
	// The application database and the connection string are defined here
	// syntax is: 'username@database.server/database_name IDENTIFIED BY PASSWORD ' 
	define('MYACTIVERECORD_CONNECTION_STR', 'mysql://root@localhost/samsp');
	
	// includes used implementation of MyActiveRecord class 
	include './include/MyActiveRecord.0.4.php';
	
	//in this array we list all and only those classes we like to CRUD manage from the main menu 
	$classes = array('staff','card','venue','agency','access');  
	
	// in this array we list all join tables which hold many to many relationships between two given classes of objects
	$join_tables = array('card_venue');	
	
	// in this array below we list all foreign keys: this array MUST EXIST: if empty then uncomment line below (and comment the following one!)
	//foreign_keys=array();
	$foreign_keys = array('card_id','staff_id','venue_id','agency_id'); 
	
	// relationships between entities/classes are named below: if no name has
	// been given to a certain relationship, the bare foreign key would be displayed
	function name_child_relationship($class_name,$foreign_key)
	{
		if ($class_name == 'access' && $foreign_key == 'card_id')
		{
			return " card ";
		}
		else if ($class_name == 'card' && $foreign_key == 'staff_id')
		{
			return " staff ";
		}
		else if ($class_name == 'staff' && $foreign_key == 'agency_id')
		{
			return " agency ";
		}
	}
	
	// this array has been initiated, but its usage will be defined in future versions of VF1
	$objects = array();
	
	// classes are defined below as extensions of MyActiveRecord class
	class agency extends MyActiveRecord{
			function destroy(){
			}	
		}
		
	class staff extends MyActiveRecord{
			function destroy(){
			}	
		}
		
	class card extends MyActiveRecord{
			function destroy(){
			}	
		}
		
	class access extends MyActiveRecord{
			function destroy(){
			}	
		}
	
	class venue extends MyActiveRecord{
			function destroy(){
			}	
		}
	class card_venue extends MyActiveRecord{
			function destroy(){
			}	
		}
?>
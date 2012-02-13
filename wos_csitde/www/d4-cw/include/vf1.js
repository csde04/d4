function update_car (file_name,car_id)
{
	document.location = file_name+"?"+"&here=Cars&class_obj=car&class_obj_id="+car_id+"&mode=update";
}

function update_obj(file_name,class_name,obj_id)
{
	/* alert("CIAO"); */
	document.location = file_name+"?"+"&here="+class_name+"&class_obj="+class_name+"&class_obj_id="+obj_id+"&mode=update";
}

function change_obj(obj_name)
{
	/* alert(obj_name); */ 
	var selobj = document.getElementById('select_'+obj_name);
	document.getElementById('input_'+obj_name).value = selobj.options[selobj.selectedIndex].value;
}


function update_driver(file_name,driver_id)
{
	document.location = file_name+"?"+"&here=Drivers&class_obj=driver&class_obj_id="+driver_id+"&mode=update";
}

function change_driver_id(driver_id)
{
	document.getElementById('input_driver_id').value = driver_id;
}

function showAuthorisation(driver_id)
{
	alert('This JS function shows which cars driver '+ driver_id +' can currently drive');
	
	document.getElementById('submit_authorisation').disabled = false;
	
	var all=document.getElementsByTagName("*");
	for(var i=0; i<all.length; i++)
	{
		if (all[i].id.indexOf("input_car_")!=-1)
		{
			all[i].checked = false;
		}
	}
	
	var x, y;
	for (x = 0; x < myDrivers.length; x ++)
	{
		if (myDrivers[x] == driver_id)
		{
			for (y = 0; y < myRides[x].length; y++)
			{
				document.getElementById('input_car_'+myRides[x][y]).checked = true;
			}
		}
	}
}
function display_div_right()
{
	/* alert('ciao'); this function displays the form to create, update or search existing records on the right of the main listings */
	
	document.getElementById('div2').style.width = '60%';
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

function confirm_create(form_id)
{
	if(confirm('Would you like to create this new record?'))
	{
		document.getElementById(form_id).submit();
	}
}

function confirm_update(form_id)
{
	if(confirm('Would you like to update this record?'))
	{
		document.getElementById(form_id).submit();
	}
}




var base_url="../";

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	if ( form.name.value == "" ) {
		alert( submit_MustName );
	} else {
		submitform( pressbutton );
	}
}
function showImport()
{
    var mydiv = document.getElementById('dialog_import');
	if (mydiv.style.display== 'none')
	{
		mydiv.style.display = 'block';
	}
	else
	{
		mydiv.style.display = 'none';
	}
    return false;  
}



/******* Add Row in Detail table ***********/
function addRow(imgtype, libtype, idtype, id, text)
{
	var nbnew = document.getElementById("nbnew");
    n=0;
    n=nbnew.value;
    n++;
	
	tab=document.getElementById("detail");
    tabBody=tab.getElementsByTagName("TBODY").item(0);
	
    row=document.createElement("TR");
    cell0 = document.createElement("TD");
    cell1 = document.createElement("TD");
    cell2 = document.createElement("TD");
    
	chknode = document.createElement("input");
    
    chknode.name= 'suppressnew'+n;
    chknode.id= 'suppressnew'+n;
    chknode.value=0;
    chknode.type="checkbox";
	
    img = document.createElement("img");
    img.src=base_url+"components/com_hecmailing/images/"+imgtype;
    textnode1=document.createTextNode(libtype);
    textnode2=document.createTextNode(text);
    hid = document.createElement("input");
    hid.name= 'new'+n;
    hid.id= 'new'+n;
    hid.type="hidden";
    hid.value= idtype+";"+id;
    
	cell0.appendChild(chknode);
    cell1.appendChild(img);
    cell1.appendChild(textnode1);
    cell2.appendChild(textnode2);
    cell0.appendChild(hid);
    
	row.appendChild(cell0);
    row.appendChild(cell1);
    row.appendChild(cell2);
    tabBody.appendChild(row);
	nbnew.value=n;
	return n
}
/****** Add User ******/
function showAddNewUser()
{
   	$( "#dialogUser" ).dialog({
				resizable: false,
				height:250,
				width : 300,
				modal: true,
				draggable: true, 
				
	});
	return false;
}

function addUser()
{
	$( "#dialogUser" ).dialog("close");
    user=document.getElementById("newuser");
	addRow("user16.png", text_user, 2, user.value, user.options[user.selectedIndex].text);
}
function cancelUser()
{
	$( "#dialogUser" ).dialog("close");
}
/********** Add Group ************/
function showAddNewGroupe()
{
	$( "#dialogGroup" ).dialog({
				resizable: false,
				height:250,
				width : 300,
				modal: true,
				draggable: true, 
				
			});
	return false;
   
}
function addGroupe()
{
	$( "#dialogGroup" ).dialog("close");
	
	grp = document.getElementById("newgroupe");
	typegrp = document.getElementById("typegroupe");
	
	switch(typegrp.value)
	{
		case '3':
			textgroup=text_joomlagroup;
			break;
		case '5':
			textgroup=text_hecmailinggroup;
			break;
	}
	
	addRow("group16.png", textgroup,typegrp.value , grp.value, grp.options[grp.selectedIndex].text);
}
function cancelGroup()
{
	$( "#dialogGroup" ).dialog("close");
}

/**************** Add Mail **********************/
function showAddNewMail()
{
    $( "#dialogMail" ).dialog({
				resizable: false,
				height:200,
				width : 300,
				modal: true,
				draggable: true, 
				
			});
	return false;
}

function addMail()
{
	$( "#dialogMail" ).dialog("close");
	var mail=document.getElementById("newmail");
	addRow("email16.png", text_mail,4,mail.value, mail.value);
}

function cancelMail()
{
	$( "#dialogMail" ).dialog("close");
}

/*************** Remove Entry *************************/
function showDeleteEntry()
{
    var table = document.getElementById("detail"); 
    var rowCount = 0;
    rowCount=table.rows.length;  
    nb=0;
    for(var i=0; i<rowCount; i++) 
    {  
        var row = table.rows[i]; 
        try
        {
	        var chkbox = row.cells[0].childNodes[0]; 
	        if(null != chkbox && true == chkbox.checked) 
	        {
	            nb++;         
	        }
        }
        catch(err)
        { }
    }
    
    if (nb>0)
    {
		var msgdelitem = document.getElementById("dialogDelEntry_msg");
        if (msgdelitem!=null)
	    	  msgdelitem.innerHTML = text_wantremove+' '+nb+' '+text_items; 
	      
			$( "#dialogDelEntry" ).dialog({
				resizable: false,
				height:200,
				width : 300,
				modal: true,
				draggable: true, 
				
			});
	      
	      
    }
    else
    {
      alert(text_noitem);
    }
    return false;  
}    

function deleteRows() {  
	try {  
		$( "#dialogDelEntry" ).dialog("close");
		var todel = document.getElementById('todel');
		var table = document.getElementById("detail");  
		var rowCount = table.rows.length;  
        for(var i=0; i<rowCount; i++) {  
        	var row = table.rows[i];
        	try
        	{
	            var chkbox = row.cells[0].childNodes[0];  
	            if(null != chkbox && true == chkbox.checked) {
	            	todel.value+=chkbox.value+';';
	                table.deleteRow(i);  
	                rowCount--;  
	                i--;  
	            }
        	}
        	catch(err) {}
      }  
   }catch(e) {  
     alert(e);  
   }  
}  

function cancelDelEntry()
{
	$( "#dialogDelEntry" ).dialog("close");
}


/************************************************************
*                        Permissions
*************************************************************/
function addRowPerm (imgtype, libtype, idtype, id, text , perm_send, perm_manage, perm_grant)
{
	var nbnew = document.getElementById("nbnewperm");
	n=0;
	n=nbnew.value;
	n++;
	
	tab=document.getElementById("permissions");
	tabBody=tab.getElementsByTagName("TBODY").item(0);
	row=document.createElement("TR");
	cell0 = document.createElement("TD");
	cell1 = document.createElement("TD");
	cell2 = document.createElement("TD");
	cell3 = document.createElement("TD");
	cell4 = document.createElement("TD");
	cell5 = document.createElement("TD");
	chknode = document.createElement("input");
	

	chknode.name= 'suppressnewperm'+n;
	chknode.id= 'suppressnewperm'+n;
	chknode.value=0;
	chknode.type="checkbox";
	
	chksend = document.createElement("input");
	chksend.name= 'newperm_send'+n;
	chksend.value=1;
	chksend.id= 'newperm_send'+n;
	chksend.checked=perm_send;
	chksend.type="checkbox";
	
	chkmanage = document.createElement("input");
	chkmanage.name= 'newperm_manage'+n;
	chkmanage.value=1;
	chkmanage.id= 'newperm_manage'+n;
	chkmanage.checked=perm_manage;
	chkmanage.type="checkbox";
	
	chkgrant = document.createElement("input");
	chkgrant.name= 'newperm_grant'+n;
	chkgrant.id= 'newperm_grant'+n;
	chkgrant.value=1;
	chkgrant.checked=perm_grant;
	chkgrant.type="checkbox";
	
	
	img = document.createElement("img");
	img.src=base_url+"components/com_hecmailing/images/"+imgtype;
	textnode1=document.createTextNode(libtype);
	textnode2=document.createTextNode(text);
	hid = document.createElement("input");
	hid.name= 'newperm'+n;
	hid.id= 'newperm'+n;
	hid.type="hidden";
	hid.value= idtype+";"+id
	
	cell0.appendChild(chknode);
	cell1.appendChild(img);
	cell1.appendChild(textnode1);
	cell2.appendChild(textnode2);
	cell0.appendChild(hid);
	cell3.appendChild(chksend);
	cell4.appendChild(chkmanage);
	cell5.appendChild(chkgrant);
	row.appendChild(cell0);
	row.appendChild(cell1);
	row.appendChild(cell2);
	row.appendChild(cell3);
	row.appendChild(cell4);
	row.appendChild(cell5);
	tabBody.appendChild(row);
	nbnew.value=n;
}

/**************** User permission ***************************/
function showAddNewUserPerm()
{
  	$( "#dialogUserPerm" ).dialog({
		resizable: false,
		height:300,
		width : 320,
		modal: true,
		draggable: true, 
	});
	return false;
}


function addUserPerm()
{
	$( "#dialogUserPerm" ).dialog("close");
	var user = document.getElementById('newuserperm');
	var perm_send = document.getElementById('right_send').checked;
	var perm_manage = document.getElementById('right_manage').checked;
	var perm_grant = document.getElementById('right_grant').checked;
	addRowPerm ("user16.png", text_user, 2, user.value, user.options[user.selectedIndex].text , perm_send, perm_manage, perm_grant);
	
}

function cancelUserPerm()
{
	$( "#dialogUserPerm" ).dialog("close");
}
/**************** Group permission ***************************/
function showAddNewGroupePerm()
{

    $( "#dialogGroupPerm" ).dialog({
		resizable: false,
		height:300,
		width : 320,
		modal: true,
		draggable: true, 
	});
	return false;
}
function addGroupePerm()
{
	$( "#dialogGroupPerm" ).dialog("close");
	grp = document.getElementById('newgroupperm');
	var perm_send = document.getElementById('rightg_send').checked;
	var perm_manage = document.getElementById('rightg_manage').checked;
	var perm_grant = document.getElementById('rightg_grant').checked;
	addRowPerm ("group16.png", text_joomlagroup, 3, grp.value, grp.options[grp.selectedIndex].text , perm_send, perm_manage, perm_grant);
}

function cancelGroupPerm()
{
	$( "#dialogGroupPerm" ).dialog("close");
}

/************************ Delete permission *****************************/
function showDeletePermEntry()
{
    
    var table = document.getElementById("permissions");  
    var rowCount = table.rows.length;  
    nb=0;
    for(var i=0; i<rowCount; i++) 
    {  
        var row = table.rows[i];  
        try
        {
	        var chkbox = row.cells[0].childNodes[0];  
	        if(null != chkbox && true == chkbox.checked) 
	        {
	            nb++;         
	        }  
        }
        catch(err) {}
        
    }
    if (nb>0)
    {
    	var msg = document.getElementById("dialogDelPerm_msg").innerHTML = text_wantremove+' '+nb+' '+text_perms; 
		$( "#dialogDelPerm" ).dialog({
			resizable: false,
			height:200,
			width : 300,
			modal: true,
			draggable: true, 
		});
     
    }
    else
    {
      alert(text_noperm);
    }
    return false;  
}    
function deleteRowsPerm() {  
	try {  
        $( "#dialogDelPerm" ).dialog("close");
        var todel = document.getElementById('todelperm');
        var table = document.getElementById("permissions");  
        var rowCount = table.rows.length;  
        for(var i=0; i<rowCount; i++) {  
			 var row = table.rows[i]; 
			 try
			 {
				 var chkbox = row.cells[0].childNodes[0];  
				 if(null != chkbox && true == chkbox.checked) {
					 todel.value+=chkbox.value+';';
					 table.deleteRow(i);  
					 rowCount--;  
	     			i--;  
	            }
			 } catch(err) {}
        }  
     }catch(e) {  
       alert(e);  
    }  
}  
function cancelDelPerm()
{
	$( "#dialogDelPerm" ).dialog("close");
}


/*************** WebService ****************************/
function appendOptionLast(elSel,id,name)
{
  var elOptNew = document.createElement('option');
  elOptNew.text = name;
  elOptNew.value = id;
  
  try {
    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
  }
  catch(ex) {
    elSel.add(elOptNew); // IE only
  }
}

function changeType(baseurl,grouptype, groupid)
{
	var url = baseurl+"&groupid="+groupid+"&grouptype="+grouptype;
	try {
		$.getJSON( url, {
			groupid: groupid,
			grouptype: grouptype,
			format: "json"
		})
		.done(function( data ) {
			var cboValues = document.getElementById("newgroupe");
			cboValues.options.length = 0;
			$.each( data, function( i, item ) {
				appendOptionLast(cboValues, item[0],item[1]);
			});
		});
	}
	catch(ex) {
		alert ("Error retrieving group info :" + ex);
	}
}
	
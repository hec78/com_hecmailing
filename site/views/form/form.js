// @version 1.8.0
// @package hecMailing for Joomla
// @module views.form.tmpl.default.php (associated javascript module)
// @subpackage : View Form (Sending mail form)
// @copyright Copyright (C) 2008-2013 Hecsoft All rights reserved.
// @license GNU/GPL
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//////////////////////////////////////////////////////////////////////////////

function loadTemplate()
{
	
	iIdTemplate=document.getElementById("idTemplate");
	var s=document.getElementById("saved");
	selected = s.options[s.selectedIndex].value;
	if (selected>0)
	{
		$( "#loadtmpl" ).dialog('close');
  		iIdTemplate.value=selected;
  		Joomla.submitbutton('form.load');
	}
}

function saveTemplate()
{
	
	iSaveTemplate=document.getElementById("saveTemplate");
	val=document.getElementById("tmplName").value;
	if (val=='')
	{
		alert (text_msg_tmplname_empty);
	}
	else
	{
		$( "#savetmpl" ).dialog('close');
		iSaveTemplate.value=val;
		Joomla.submitbutton('save');
		
	}
	
}

function showLoadTemplate()
{
   	$( "#loadtmpl" ).dialog({
		resizable: false,
		height:250,
		width : 300,
		modal: true,
		draggable: true 
		});
  return false;  
}

function showSaveTemplate()
{
	$( "#savetmpl" ).dialog({
		resizable: false,
		height:250,
		width : 330,
		modal: true,
		draggable: true 
		});
   return false;  
}

function cancelSaveTmpl()
{
	$( "#savetmpl" ).dialog("close");
}

function cancel()
{
	$( "#loadtmpl" ).dialog("close");
   
}

function showBrowse()
{
	$( "#browseDlg" ).dialog({
		resizable: false,
		height:600,
		width : 400,
		modal: true,
		draggable: true 
		});
  	fillList('');
    return false;  
}

function hideBrowse()
{
	$( "#browseDlg" ).dialog('close');
  	return false;  
}

// Add attachment row (file input)
function addAttachment()
{
 	var count = document.getElementById("attachcount");
 	var tab=document.getElementById("attachmentbody");
    var row=document.createElement("tr");
    var cell0 = document.createElement("td");
    n=0;
    n=count.value;
    n++;
    var iFile = document.createElement("input");
    iFile.name= 'attach'+n;
    iFile.id= 'attach'+n;
    iFile.type="file";
    iFile.style.width="100%";
    tab.appendChild(row);
    row.appendChild(cell0);
    cell0.appendChild(iFile);
 		count.value=n;
}



function selectFile()
{
		var count = document.getElementById("localcount");
 		var tab=document.getElementById("attachmentbody");
 		var dlgBrowse = document.getElementById("browseDlg");
 		var dlgBrowseList = document.getElementById("browseListe");
 		sel = getSelected(dlgBrowseList);
 		if (curdir.length>0) ledir=curdir+'/';
 		else ledir='/';
 		for (i=0;i<sel.length;i++)
 		{
	      row=document.createElement("TR");
	      cell0 = document.createElement("TD");
	      n=0;
	      n=count.value;
	      n++;
	      var iFile = document.createElement("input");
	      iFile.name= 'local'+n;
	      iFile.id= 'local'+n;
	      iFile.type="hidden";
	      iFile.value = base_dir+ledir+sel[i];
	      cell0.appendChild(iFile);
	      iChk=document.createElement('input');
	      iChk.name= 'chklocal'+n;
	      iChk.id= 'chklocal'+n;
	      iChk.checked=true;
	      iChk.type='checkbox';
	      iTxt = document.createTextNode('  ' + base_dir + ledir +  sel[i]);
	      cell0.appendChild(iChk);
	      cell0.appendChild(iTxt);
	      tab.appendChild(row);
	      row.appendChild(cell0);
   		  count.value=n;
   		}
   hideBrowse();
}

function addElement(divListe,path, isfolder)
{
	var a = document.createElement('a');
	num=divListe.childNodes.length;
  if (isfolder) // Dir
  {
  	a.id = 'folder'+num; 
  	a.ondblclick= function() { dbclick(this);return false; }
  	img = document.createElement('img');
  	img.src=baseURI+"/components/com_hecmailing/images/folder.gif";
  	img.width=16;
  	img.height=16;
  	a.appendChild(img);
  	txt=document.createTextNode("  " + path);
  	a.appendChild(txt);
  }
  else
  {
  	a.id = 'file'+num;
  	a.onclick = function() { select(this);return false; }
  	a.ondblclick= function() { selectandclose(this);return false; }
  	chk = document.createElement('input');
  	chk.type='Checkbox';
  	chk.id= 'chk'+ a.id;
  	chk.name = path;
  	a.appendChild(chk);
  	txt=document.createTextNode(path);
  	a.appendChild(txt);
  }
  a.name=path;
  divListe.appendChild(a);
}

function resetList(divListe)
{
	obj=divListe;
	try {
    if(obj.hasChildNodes() && obj.childNodes) {
      while(obj.firstChild) {
        obj.removeChild(obj.firstChild);
      }
    }
  }
  catch(e) {
    //  do nothing, or uncomment the error message
    //alert(e.message);
  }
}

function getSelected(divListe)
{
	ret=new Array();
	lesCheck = divListe.getElementsByTagName('input');
	for (i=0;i< lesCheck.length;i++)
	{
		chk=lesCheck[i];
		if (chk.type=='checkbox')
		{
			if (chk.checked)
				ret.push(chk.name);
		}
	}
	return ret;
}

function dbclick(param)
{
	if (param.name=='..')
	{
			k=-1;
			for(i=curdir.length-1;i>0;i--)
			{
				if (curdir.substr(i,1)=='/')
				{
						k=i;
						break;
				}
			}
			newdir=curdir.substr(0,k);
	}
	else
	{
  		newdir=curdir+'/'+ param.name;			
  }
  fillList( newdir);
}

function select(elmt)
{
	var chk = elmt.getElementsByTagName('input');
	chk=chk[0];
	chk.checked=!chk.checked;
}
function selectandclose(elmt)
{
	var chk = elmt.getElementsByTagName('input');
	chk=chk[0];
	chk.checked=!chk.checked;
	selectFile();
}

var curdir='';



function fillList(dirname)
{
		var dlg = document.getElementById('browseDlg');
		var lst = document.getElementById('browseListe');
		var btn = document.getElementById('browseButtons');
		resetList(lst);
		
		lst.style.width="100%";
		lst.style.height=dlg.clientHeight - 50;
		btn.style.top = dlg.clientHeight - 50;
		img = document.createElement('img');
  		img.src=baseURI+"/components/com_hecmailing/images/ajax-loader.gif";
	  	l = (dlg.clientWidth-100)/2;
	  	h = (dlg.clientHeight-100)/2;
	  	img.style.marginLeft= l+"px";
	  	img.style.marginTop= h+"px";
	  	
	  	lst.appendChild(img);
	  	if (dirname==undefined)
	  	{
	  		dirname='';
	  	}

	  var url = currentURI+'?task=getdir&format=json&option=com_hecmailing&dir=' + encodeURI(  dirname ) ;
		
		try {
			$.getJSON( url, {
				format: "json"
			})
			.done(function( data ) {
				resetList(lst);
				curdir = data['dir'];
				items= data['list'];
				
				$.each( items, function( i, item ) {
					var isfolder=false;
	  				if (item.substr(0,1) == '@')
	  				{
	  					item = item.substr(1);
	  					isfolder=true;
	  				}	
	  			
	  				addElement(lst,item, isfolder);
				
				});
			});
		}
		catch(ex) {
			alert ("Error retrieving group info :" + ex);
		}
		if (dirname=='')
		{
			document.getElementById('browseCurrentDir').innerHTML=dirname;
			document.getElementById('browsePath').innerHTML = '/';
		}
		else
		{
			document.getElementById('browseCurrentDir').innerHTML=dirname;
			document.getElementById('browsePath').innerHTML = dirname;
		}
	
	  
}

function checksend()
{
	var grp = document.getElementById("groupe");
	val = grp.options[grp.selectedIndex].value;
	if (val<0)
	{
		alert(text_msg_select_group);
		return false;
	}
	var subject = document.getElementById("subject").value;
	if (subject.length==0)
	{
		alert(text_msg_empty_subject);
		return false;
	}
	submitbutton('form.send');
	return true;
}

var current_group=0;

function showManageButton(selectBox)
{
	var btn = document.getElementById("manage_button");
	current_group=selectBox.options[selectBox.options.selectedIndex].value;
	flag=rights[current_group];
	if ((flag & 6)>0)
	{
		btn.style.visibility = 'visible';
	}
	else
	{
		btn.style.visibility = 'hidden';
	}
}
function manage_group()
{
	window.open("index2.php?option=com_hecmailing&task=manage_group&tmpl=component&idgroup='; ?>"+current_group,text_manage,"directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=yes,toolbar=no,width=800,height=600");
}

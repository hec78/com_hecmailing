<?php 
/**
 * @version 1.8.2
 * @package hecmailing
 * @copyright 2009-2013 Hecsoft.net
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomla.hecsoft.net
 * @author H Cyr
 **/
 
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
JHTML::_('behavior.tooltip');
/*$cpath="components/com_hecmailing/libraries/crypt";
$cryptinstall=$cpath."/cryptographp.fct.php";
include $cryptinstall; */
require_once('components/com_hecmailing/libraries/recaptcha/recaptchalib.php');

// Get a key from http://recaptcha.net/api/getkey
//$publickey = "6LdY6AsAAAAAACn2_Mq5qa7Ro_E3UaScqIv5iysB ";
//$privatekey = "6LdY6AsAAAAAAK6GF1sb7WpqBc61Xb4cpNE57hRO";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;


// Modif Joomla 1.6+
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $mainframe->addCustomHeadTag en   $document->addCustomTag
$document->addCustomTag('<link rel="stylesheet" href="components/com_hecmailing/css/toolbar.css" type="text/css" media="screen" />');
$document->addCustomTag ('<link rel="stylesheet" href="components/com_hecmailing/css/dialog.css" type="text/css" media="screen" />');
?>

<script language="javascript" type="text/javascript">

<!--

function submitbutton2(pressbutton) {
    var myform = document.getElementById("adminForm");
    var mytask = document.getElementById("task");
    if (myform==null)
    {
        alert ("Error : Can't get Form 'adminForm'");
    }
    if (pressbutton) {
      mytask.value=pressbutton;
    }
     if (typeof myform.onsubmit == "function") {
        myform.onsubmit();
    }
    if (pressbutton == 'cancel') {
            myform.submit();
            return;
    }
    
    <?php
           // echo $editor->save( 'body' );
    ?>
    alert('submit task='+document.getElementById("task").value);
    myform.submit();
}
var oldid=-1;
function showInfo(id)
{
	
	if (oldid>=0)
	{
		var infodiv = document.getElementById("info"+oldid);
		infodiv.style.display = "none";
	}
	
 	if (id!="0")
 	{   
 	 	var infodiv = document.getElementById("info"+id);
 		infodiv.style.display = "block";
	    
	   
 	}
 	oldid=id;
    return true;  

}   

/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck(str) {

		var at="@";
		var dot=".";
		var lat=str.indexOf(at);
		var lstr=str.length;
		var ldot=str.indexOf(dot);
		if (str.indexOf(at)==-1){
		   return false;
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
  	   return false;
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		   
		    return false;
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		   		    return false;
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		       return false;
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    return false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		   return false;
		 }

 		 return true;					
	}



function checksend()
{
	var grp = null;
  grp= document.getElementById("contact");
  
	if (grp.type!='hidden')
	{
  	val = grp.options[grp.selectedIndex].value;
  	if (val<=0)
  	{
  		alert('<?php echo JText::_('COM_HECMAILING_MSG_SELECT_GROUP'); ?>');
  		grp.focus();
  		return false;
  	}
  }
	var email = document.getElementById("email").value;
	if (email.length==0 || email==null || email=="")
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_MAIL'); ?>');
		email.focus();
		return false;
	}
	if (echeck(email)==false)
	{
	    alert('<?php echo JText::_('COM_HECMAILING_MSG_BAD_EMAIL'); ?>');
		  email.focus();
		  return false;
  }
	var name = document.getElementById("name").value;
	if (name.length==0 || name=="" || name==null)
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_NAME'); ?>');
    name.focus();
		return false;
	}
	var subject = document.getElementById("subject").value;
	if (subject.length==0 || subject=="" || subject==null)
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_SUBJECT'); ?>');
		subject.focus();
		return false;
	}
	var body = document.getElementById("body").value;
	if (body.length==0 || body=="" || body==null)
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_BODY'); ?>');
		body.focus();
		return false;
	}	
  //submitbutton('sendContact');
  var myform = document.getElementById("adminForm");
  var mytask = document.getElementById("task");
  mytask.value = 'contact.send';
  myform.submit();
}
 

//-->
<?php
if ($this->lang != '')
{
	$lang_tab = split('-',$this->lang);
	$lang = $lang_tab[0];
	$theme = $this->captcha_theme;
	/* Theme : red , white, blackglass, clean */
	echo "var RecaptchaOptions = {   lang : '".$lang."', theme:'".$theme."' };";	
}
$help_url = 'http://joomla.hecsoft.net/index.php?option=com_content&view=article&id=55&Itemid=65';
?>

</script>


<form action="index.php?option=com_hecmailing&task=contact.send" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data" >
<input type="hidden" id="required" name="required" value="uword">
<div class="componentheading"><?php echo $this->title; ?></div>
<div id="component-hecmailing">
</div>
<?php echo $this->msg; ?>
<hr><br>

<table width="100%" class="admintable" style="valign:top">
<?php if (count($this->names)>1)
{ ?>
<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_CONTACT_WHO'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_CONTACT_WHO_HELP'), JText::_('COM_HECMAILING_CONTACT_WHO'), 'tooltip.png', '', 
               $help_url));
 ?> :</td><td><?php echo $this->contacts; ?></td></tr>

<tr valign="top"><td class="key"></td><td></td><td><div id="info" name="info"></div>
<?php 
  $i=0;
  foreach($this->infolist as $info)
  {
  	echo "<div id=\"info" . $this->infoid[$i] . "\" style=\"display:none;background-color:lightgrey;padding:3px;\" >".$info. "</div>";
  	//echo "<input type=\"hidden\" id=\"info" . $this->infoid[$i] . "\" name=\"info" . $this->infoid[$i] . "\" value=\"".$info."\" >";
  	$i++; 
  }
  echo "</td></tr>";
} 
else
 {
   	echo '<tr valign="top"><td class="key"></td><td></td><td><div id="info' . $this->infoid[0] . '" style=\"display:bloc;background-color:lightgrey;padding:3px;" >'.$this->infolist[0]. '</div></td></tr>';
    echo $this->contacts;
 }
?>


<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_YOUR_EMAIL'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_YOUR_EMAIL_HELP'), JText::_('COM_HECMAILING_YOUR_EMAIL'), 'tooltip.png', '', 
               $help_url)); ?>:</td><td><input type="text" id="email" name="email" value="<?php echo $this->email; ?>" /> </td></tr>
<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_YOUR_NAME'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_YOUR_NAME_HELP'), JText::_('COM_HECMAILING_YOUR_NAME'), 'tooltip.png', '', 
               $help_url)); ?> :</td><td><input type="text" id="name" name="name" value="<?php echo $this->name; ?>" /> </td></tr>


<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_CONTACT_SUBJECT'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_CONTACT_SUBJECT_HELP'), JText::_('COM_HECMAILING_CONTACT_SUBJECT'), 'tooltip.png', '', 
               $help_url)); ?> :</td><td><input style="width:100%"  type="text" id="subject" name="subject" value="<?php echo $this->subject; ?>" /></td></tr>
<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_CONTACT_BODY'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_CONTACT_BODY_HELP'), JText::_('COM_HECMAILING_CONTACT_BODY'), 'tooltip.png', '', 
               $help_url)); ?> :</td><td>



<textarea id="body" width="300px" cols="50"  name="body" rows="4"><?php echo $this->body; ?> </textarea>
<?php
/*
$editor =& JFactory::getEditor();

echo $editor->display('body', $this->body, $this->width, $this->height, '60', '20', true);
*/
?>
</td></tr>
<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_CONTACT_BACKUP_MAIL'); ?></td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_CONTACT_BACKUP_MAIL_HELP'), JText::_('COM_HECMAILING_CONTACT_BACKUP_MAIL'), 'tooltip.png', '', 
               $help_url)); ?> :</td><td><input type="checkbox" name="backup_mail" value="1" id="backup_mail" <?php echo $this->backup_mail; ?> /> </td></tr>
<?php
	if ($this->captcha_show_logged=='1')
	{
		?>
		<tr valign="top"><td class="key"><?php echo JText::_('COM_HECMAILING_ANTISPAM'); ?> </td><td>
<?php echo (JHTML::tooltip(JText::_('COM_HECMAILING_ANTISPAM_HELP'), JText::_('COM_HECMAILING_ANTISPAM'), 'tooltip.png', '', 
              $help_url)); ?>:</td><td><?php echo JText::_("COM_HECMAILING_COPY_CODE"); ?><br><?php echo recaptcha_get_html($this->publickey, $error); ?>
	<input type="hidden" name="check_captcha" id="check_captcha" value="1"></td></tr>
	<?php
}
else
{
	?>
	<input type="hidden" name="check_captcha" id="check_captcha" value="0">
	<?php
}
?>
<tr><td colspan="3" align="center"><input type="button" name="sendContact" value="<?php echo JText::_('COM_HECMAILING_SEND_CONTACT'); ?>" onclick="javascript: checksend();return false;"></input>
</td></tr></table>


<?php echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="option" id="option" value="com_hecmailing">
<!-- <input type="hidden" name="view" id="view" value="contact">  -->
<input type="hidden" name="task" id="task" value="contact.send">
<div><?php echo JText::sprintf("COM_HECMAILING_VERSION_FOOTER",$this->version); ?></div>
</form>


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

//require_once('components/com_hecmailing/libraries/recaptcha/recaptchalib.php');

// Get a key from http://recaptcha.net/api/getkey

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

// Modif Joomla 1.6+
$app = JFactory::getApplication();
$document = JFactory::getDocument();
// Modif pour J1.6+ : change $mainframe->addCustomHeadTag en   $document->addCustomTag
$document->addStyleSheet("components/com_hecmailing/assets/css/item.css",array(),array());
$document->addStyleSheet("components/com_hecmailing/assets/css/contact.css",array( "media"=>"screen"),array());

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
    
    /*        $editor = JEditor::getInstance();
            echo $editor->save( 'body' );
            */
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
    grp= document.getElementById("jform_contact");
  
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
	var email = document.getElementById("jform_email").value;
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
	var name = document.getElementById("jform_name").value;
	if (name.length==0 || name=="" || name==null)
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_NAME'); ?>');
    name.focus();
		return false;
	}
	var subject = document.getElementById("jform_subject").value;
	if (subject.length==0 || subject=="" || subject==null)
	{
		alert('<?php echo JText::_('COM_HECMAILING_MSG_EMPTY_SUBJECT'); ?>');
		subject.focus();
		return false;
	}
	var body = document.getElementById("jform_body").value;
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
//if ($this->lang != '')
// {
// 	$lang_tab = split('-',$this->lang);
// 	$lang = $lang_tab[0];
// 	$theme = $this->captcha_theme;
// 	/* Theme : red , white, blackglass, clean */
// 	echo "var RecaptchaOptions = {   lang : '".$lang."', theme:'".$theme."' };";	
// }*/
$help_url = 'http://joomla.hecsoft.net/index.php?option=com_content&view=article&id=55&Itemid=65';
?>

</script>


<form action="index.php?option=com_hecmailing&task=send_contact" method="post" name="adminForm" id="adminForm" ENCTYPE="multipart/form-data" >
<input type="hidden" id="required" name="required" value="uword">
<div class="componentheading"><?php echo $this->title; ?></div>
<div id="component-hecmailing">
</div>

<hr><br>
<div id="contactForm" >
	<?php
// Iterate through the fields and display them.
	foreach($this->form->getFieldset('basic') as $field):
    // If the field is hidden, only use the input.
	    if ($field->hidden):
	        echo $field->input;
	    else:
	    ?>
	    <dl class="dl-horizontal">
	    <dt><?php echo $field->label; ?></dt>
	    <dd><?php echo $field->input ?></dd>
	    </dl>
	    <?php
	    endif;
	endforeach;
	?>
	</div>


<?php
	if ($this->captcha_show_logged=='1')
	{
		?>
		
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
<div class="center"><input type="button" name="sendContact" value="<?php echo JText::_('COM_HECMAILING_SEND_CONTACT'); ?>" onclick="javascript: checksend();return false;"></input>
</div>


<?php echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="option" id="option" value="com_hecmailing">
<!-- <input type="hidden" name="view" id="view" value="contact">  -->
<input type="hidden" name="task" id="task" value="send_contact">
<div><?php echo JText::sprintf("COM_HECMAILING_VERSION_FOOTER",""); ?></div>
</form>


<?php /** * @version 1.8.2 * @package hecmailing * @copyright 2009-2013 Hecsoft.net * @license http://www.gnu.org/licenses/gpl-3.0.html * @link http://joomla.hecsoft.net * @author H Cyr **/
defined('_JEXEC') or die ('restricted access'); 

jimport('joomla.application.component.view'); 
jimport('joomla.html.toolbar');
class hecMailingViewLogDetail extends JView { 	function display ($tpl=null) 	{ 
      // Modif Joomla 1.6+		$mainframe = JFactory::getApplication();		$currentuser= &JFactory::getUser();		$pparams = &$mainframe->getParams();		$model = & $this->getModel(); 		$groupe=0; 
		$height = $pparams->get('edit_width','400');		$width = $pparams->get('edit_height','400');		$idlog = JRequest::getInt('idlog', 0);
		if ($idlog>0)		{			$infomsg = $model->getLogDetail($idlog);			if (!$infomsg)			{				$data=false; $att=array();				$this->assignRef('data', false);				$this->assignRef('attachment',$att);			}			else			{				$this->assignRef('data', $infomsg);				$this->assignRef('attachment',$infomsg->attachment);			}		}		else 		{			$data=false;			$att=array();			$this->assignRef('data', $data);			$this->assignRef('attachment',$att);		}		$msg="";		$this->assignRef('msg', $msg);
  		$this->assignRef('idlog', $idlog);		$this->assignRef('saved', $saved);		$this->assignRef('height', $height);		$this->assignRef('width', $width);		$viewLayout = JRequest::getVar( 'layout', 'default' );		$this->_layout = $viewLayout;		parent::display($tpl); 	} } 
?> 

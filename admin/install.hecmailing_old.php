<?php

defined('_JEXEC') or die('Restricted access');

/**
* @version 1.7.8
* @package hecMailing for Joomla
* @copyright Copyright (C) 2008-2011 Hecsoft All rights reserved.
* @license GNU/GPL
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


function com_install()
{
	$Component_Version="1.8.3";
    // First make sure that this version of Joomla is 1.5 or greater
    $version = new JVersion();
    if ( (real)$version->RELEASE < 1.5 ) {
        echo "<h1 style=\"color: red;\">The 'hecMailing' package will only work on Joomla version 1.5 or later!</h1>";
        return false;
    }

    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hecmailing'.DS.'update.php');
    HecMailingUpdate::update_hecmailing_table();

    echo '<div class="header">Le composant HEC Mailing '.$Component_Version.' a &eacute;t&eacute; install&eacute; avec succ&egrave;s! </div>';
    echo '<div class="header">HEC Mailing '.$Component_Version.' component succesfully installed! </div>';
  	echo '<div class="header">HEC Mailing '.$Component_Version.'  ist erfolgreich installiert worden</div>';
	  echo '<div class="header">HEC Mailing '.$Component_Version.'  werd met successen geplaatst</div><br>';
	  echo '<hr>';
   
    ?>

    </div>

    <h2>DESCRIPTION:</h2>
    <p>
      Ce composant pour joomla 1.5+ (compatible 1.6, 1.7, 2.5) permet l'envoi de mail a une liste de diffusion par des utilisateurs autoris&eacute;s &acute; partir du frontend.<br>
      Vous pouvez cr&eacute;er plusieurs groupes. Chaque groupe peut contenir des utilisateur joomla, des groupes joomla ou des adresses e-mail<br>
      Ce composant utilise l'&eacute;diteur par d&eacute;faut et peut envoyer des images (images stock&eacute;es sur votre site ou attach&eacute;es).<br>
      Il est aussi capable d'envoyer des pi&egrave;ces jointes (&agrave; partir de votre disque ou du site)&lt;br&gt;
      Un syst&egrave;me de template est disponible (sauvegarde d'un mail).
    </p>
    <hr>
    <p>
    	This joomla 1.5+ (1.6, 1.7 and 2.5 compliant) extension allow authorized user to send html mail to distribution lists (groups) from the frontend.<br>
			You can create many groups from backend. A group can contain joomla user, joomla group or other e-mail address.<br>
			This component use default editor and is able to send images (remote image with link or attached!) and have a template system.
			It can also send attachment (from your local disk or from website)<br>
    </p>
    
    <br>
    <h2>PARAMETRAGE:</h2>
    <p>Si vous utilisez le module de contact, apr&egrave;s l'installation, vous devez vous rendre sur le site de reCaptcha (<a href="http://recaptcha.net/whyrecaptcha.html" >http://recaptcha.net/whyrecaptcha.html</a>) afin d'obtenir un jeu de cl&eacute; (priv&eacute; et public) que vous saisirez dans l'&eacute;cran des param&egrave;tres.<br>
    	<ul><li>Cr&eacute;er un compte</li>
    		<li>A partir de votre compte, ajouter votre site puis 'Create Key'</li>
    		<li>Ouvrir l'interface d'administration puis ouvrir le composant hecMailing</li>
    		<li>Cliquer sur Param&egrave;tres</li>
    		<li>Copier/Coller la cl&eacute; priv&eacute;e dans le champ 'ReCaptcha private Key'</li>
    		<li>Copier/Coller la cl&eacute; public dans le champ 'ReCaptcha public Key'</li>
    		<li>Sauver. Votre module Contact est pr&ecirc;t &agrave; fonctionner</li>
    	</ul>
    </p>
    		<p><i>If you use Contact module, after install, you need to go to reCaptcha website (<a href="http://recaptcha.net/whyrecaptcha.html" >http://recaptcha.net/whyrecaptcha.html</a>) to get keys (private and public). Theses keys will be put in hecMailing parameter screen.<br>
    			How to :<br>
    	<ul><li>Create an account on reCaptcha website</li>
    		<li>Click on 'MY ACCOUNT', 'Add a New Site'and 'Create Key'</li>
    		<li>Open admin backend of your website and choose hecMailing from component menu</li>
    		<li>Click on Parameters</li>
    		<li>Copy/Paste the  private key in 'ReCaptcha private Key' field</li>
    		<li>Copy/Paste the  public key in 'ReCaptcha private Key' field</li>
    		<li>Save. Your Contact module is ready</li>
    	</ul>
    	</i><p>
    <h2>Change Log</h2>
    <p>
	 1.8.3   Correction du probl&egrave;me de log lors d'envoi en group
	1.8.2 Correction du probl&egrave;me de mise &agrave; jour<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Permet la mise &agrave; jour avec la version de test<br>
	1.8.1 Correction du probl&egrave;me javascript (Fichier form.js manquant dans le package)<br>
	1.8.0 Utilisation de jQuery pour les services web et les dialogues<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possibilit&eacute; d'ajouter un group HECMailing a un autre groupe<br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Modification ajout de groupe (admin) avec selection du type de groupe (joomla ou hecmailing) et service web pour obtenir le d&eacute;tail du groupe s&eacute;lectionn&eacute;<br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Simplification du code javascript<br>
	1.7.8 Corrige le problem d'Import<br>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ajout du support de l'import des fichiers MAC<br>
    1.7.7 Corrige le probl&egrave;me d'envoi de mail avec Joomla 2.5 (Nom r&eacute;el)<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Corrige le probl&egrave; d'installation avec des bases de donn&eacute; non UTF8<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Corrige le probl&egrave; lors de l'obtention de l'information si le user courant est admin en joomla 1.6<br>
	1.7.6   Corrige le probleme d'envoi aux utilisateurs bloqu&eacute;s<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ajoute le nom du destinataire<br>
	1.7.5 Corige le probleme de doublonage du message lors de l'envoie d'une demance de contact<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Corrige le problem de traduction des menus administrateur
	1.7.4  Corrige le probl&egrave;me d'affichage de la page de param&eacute;trage avec Joomla 1.5.x<br>
	   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Corrige le probl&egrave;me de cochage des droits des groupes avec joomla 1.5.x<br>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Corrige le probl&egrave;me de droit d'utilisation du composant (frontend) avec joomla 1.5.x<br>
	1.7.3 Corrige le probl&egrave;me lors de l'utilisation des groupes joomla<br>
	1.7.2 Langage Allemend corrig&eacute; et mis &grave; jour par Georg Straube <br>
	1.7.1 Correction des probl&egrave;mes de gestion des groupes<br>
           Ajout gestion de template de contact<br>
			Correction probl&egrave;mes menu d'administration<br>

	1.7.0   Version compatible avec joomla 1.6/1.7 et +<br>

    </p>

	<p><i>
	 1.8.3   Fix group email log problem<br>
	1.8.2 Fixe Updade problem<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Allow to Update with Stable or Test version  <br>
	1.8.1 Fixe javascript problem (form.js file missing in package)<br>
	1.8.0 Use of JQuery for Webservice and Dialogs<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add HECMailing group use in a group (group of group)<br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Change group add (admin). We use a web service for list group detail (joomla or hecmailing)<br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Simplify javascript code<br>
	1.7.8 Fixe Import problem<br>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add support for Mac File import<br>
    
	1.7.7 Fixe Joomla 2.5 send mail problem (real name)<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fixe Install problem when MySQL is not UTF8 charset<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fixe IsAdmin get info on Joomla 1.6+<br>
	1.7.6   Fixe Can't send mail to blocked users<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Feature Add recipient Name <br>
	1.7.5   Fixe double message when send Contact<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fixe Admin Menu Translation Problem with joomla 1.5<br>
	1.7.4  Fixe component admin parameter problem on Joomla 1.5.x<br>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fixe Group right checkbox problem on joomla 1.5.x<br>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fixe use authorization issue on joomla 1.5.x<br>
	1.7.3 Correct use of joomla group problem<br>
	1.7.2 de-DE language corrected by Georg Straube <br>
	1.7.1  Correct Group Management problems<br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add template for contact<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Correct Admin menu problem<br>
	1.7.0 Joomla 1.6/1.7 compliant<br>
	</i></p>



    <p>
    		Vous pouvez utiliser le gestionnaire de bug disponible <a href="http://sourceforge.net/tracker/?group_id=281865&atid=1195720">ici</a> pour decrire vos problemes et envoyer vos suggestions.<br>
				
        Please report bugs and suggestions with <a href="http://sourceforge.net/tracker/?group_id=281865&atid=1195720">HEC Mailing tracker</a>.

    </p>

    <h2>Contributors :</h2>
    &nbsp;<i>Herv&eacute; CYR</i>: Project leader and main developper<br>
    &nbsp;<i>Arjan Mels</i>: Project contributor (version 0.9.1, many bug/feature correction and dutch translation)<br>  
    &nbsp;<i>Ingo</i>: Project contributor (1st deutch translation)<br>
	&nbsp;<i>Georg Straube</i>: Project contributor (last deutch translation)<br>
    <?php



    return true;

}

?>
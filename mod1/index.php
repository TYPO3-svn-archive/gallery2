<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Philipp Mueller <philipp.mueller@xeiro.ch>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Gallery2' for the 'gallery2' extension.
 *
 * @author	Philipp Mueller <philipp.mueller@xeiro.ch>
 * @package TYPO3
 * @subpackage gallery2
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   71: class tx_gallery2_module1 extends t3lib_SCbase
 *
 *              SECTION: mainfunctions
 *   93:     function init()
 *  115:     function main()
 *  133:     function jumpToUrl(URL)
 *
 *              SECTION: output
 *  189:     function printContent()
 *
 *              SECTION: galleryfunctions
 *  205:     function runGallery()
 *  273:     function initGallery()
 *  327:     function getSessionData()
 *  341:     function setSessionData($data)
 *  352:     function clearSessionData()
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

// Modulconfiguration
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:gallery2/mod1/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
// don't change the following two lines, else the module don't works!!
global $TYPO3_CONF_VARS;
$MCONF['name']=$TYPO3_CONF_VARS['EXTCONF']['gallery2']['g2mainMod'].'_txgallery2M1';
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
require_once(t3lib_extMgm::extPath('gallery2').'lib/class.tx_gallery2_api.php');

class tx_gallery2_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $gallery;
	var $extKey = 'gallery2';

	var $conf = array();
	var $g2RelPath;
	var $g2Path;
	var $g2DocPath;
	var $sessionName = 'gallery2typo3';

	/**************************************************************************************************************************
	 *
	 * mainfunctions
	 *
	 **************************************************************************************************************************/

	/**
	 * Initializes the Module
	 *
	 * @return	void		..
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS,$_EXTKEY;

		parent::init();

		$this->api = t3lib_div::makeInstance('tx_gallery2_api');
		$this->api->makeDummyFiles();
		$this->api->setConf();
		$this->conf = $this->api->conf;

		$this->g2Path = '../../../../'.$this->api->g2RelPath;

		$this->api->loadGallery($this->g2Path);
		$this->initGallery();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose 'web' as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	void		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		$data = $this->runGallery();

		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->docType = 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';
		$this->doc->JScode .= $data['javascript'];
		$this->doc->JScode .= $data['css'];

		$this->doc->postCode='
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
		';

		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content .= $data['bodyHtml'];

		$backArgs = t3lib_div::_GET('extg2_backArgs');
		if($backArgs) {
			if($backArgs['link']) {
				$backLink = '<div style="margin-top: 10px;"><a href="'.$backArgs['link'].'" style="font-weight: bold; font-size: 120%;">';

				if($_REQUEST['extg2_backArgs']['label']) {
					$backLink .= $backArgs['label'];
				} else {
					$backLink .= '&lt;&lt; back';
				}

				$backLink .= '</a></div>';
				$this->setSessionData(array('backlink' => $backLink));
			}
		}

		$sessionData = $this->getSessionData();
		if($sessionData['backlink']) {
			$this->content .= $sessionData['backlink'];
		}

		// ShortCut
		if ($BE_USER->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}
	}

	/**************************************************************************************************************************
	 *
	 * output
	 *
	 **************************************************************************************************************************/

	/**
	 * Prints out the module HTML
	 *
	 * @return	void		..
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**************************************************************************************************************************
	 *
	 * galleryfunctions
	 *
	 **************************************************************************************************************************/

	/**
	 * Returns the gallery-code
	 *
	 * @return	array		gallery-data
	 */
	function runGallery() {
		$data = array();

	    // initiate G2
	    $ret = $this->gallery;

	    if ($ret) {
	      $data['bodyHtml'] = $ret->getAsHtml();
	      return $data;
	    }

	    // handle the G2 request
	    $g2moddata = GalleryEmbed::handleRequest();

	    // show error message if isDone is not defined
	    if (!isset($g2moddata['isDone'])) {
	      $data['bodyHtml'] = 'isDone is not defined, something very bad must have happened.';
	      return $data;
	    }

	    // exit if it was an immediate view / request (G2 already outputted some data)
	    if ($g2moddata['isDone']) {
		exit;
	    }

	    // put the body html from G2 into the xaraya template
	    $data['bodyHtml'] = isset($g2moddata['bodyHtml']) ? $g2moddata['bodyHtml'] : '';

	    // get the page title, javascript and css links from the <head> html from G2
	    $title = ''; $javascript = array();	$css = array();

	    if (isset($g2moddata['headHtml'])) {
	      list($data['title'], $css, $javascript) = GalleryEmbed::parseHead($g2moddata['headHtml']);
		  $data['headHtml'] = $g2moddata['headHtml'];
	    }


	    /* Add G2 javascript  */
	    $data['javascript'] = '';
	    if (!empty($javascript)) {
	      foreach ($javascript as $script) {
		     $data['javascript'] .= "\n".$script;
	      }
	    }

	    /* Add G2 css  */
	    $data['css'] = '';
	    if (!empty($css)) {
	      foreach ($css as $style) {
		     $data['css'] .= "\n".$style;
	      }
	    }

		$data['css'] .= "\n".'<style type="text/css">div.giAlbumCell, div.giItemCell { width: 250px; height: 350px; }</style>';

	    // sidebar block
	    if (isset($g2moddata['sidebarBlocksHtml']) && !empty($g2moddata['sidebarBlocksHtml'])) {
	      $data['sidebarHtml'] = $g2moddata['sidebarBlocksHtml'];
	    }

	    return $data;
	}

	/**
	 * initialisiert die GalleryAPI
	 *
	 * @return	void
	 */
	function initGallery(){
		require_once($this->g2Path.'/embed.php');
		global $BE_USER,$GLOBALS;

		$uc = unserialize($BE_USER->user['uc']);
		$user = Array(
			'username' => 'be_'.$BE_USER->user['username'],
			'email' => $BE_USER->user['email'],
			'fullname' => $BE_USER->user['realName'],
			'language' => $uc['lang'],
			'hashedpassword' => $BE_USER->user['password'],
			'hashmethod' => 'md5',
			'creationtimestamp' => time()
		);

		$embedUri = $GLOBALS['_SERVER']['SCRIPT_NAME'];
	    $ret = GalleryEmbed::init(array(
					'g2Uri' => $this->api->g2DocPath.'/'.$this->api->g2RelPath.'/',
					'embedUri' => $embedUri,
					'activeUserId' => $user['username']
					));


		if ($ret) {
		     $ret2 = GalleryEmbed::isExternalIdMapped($user['username'], 'GalleryUser');

		     if ($ret2 && $ret2->getErrorCode() & ERROR_MISSING_OBJECT) {
		         $ret = GalleryEmbed::createUser($user['username'], $user);

		         if ($ret) {
		             print 'An error occurred during the on-the-fly user creation <br>';
		             print $ret->getAsHtml();
		             exit;
		         }
		     } else {

		         if ($ret2) {
		             print 'An error occurred while checking if a user already exists<br>';
		             print $ret2->getAsHtml();
		         }

		         print 'An error occurred while trying to initialize G2<br>';
		         print $ret->getAsHtml();
		         exit;
		     }
		}
		$this->gallery = $ret;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function getSessionData(){
		global $BE_USER;

		$sesData = $BE_USER->getSessionData($this->sessionName);

		return $sesData;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$data: ...
	 * @return	[type]		...
	 */
	function setSessionData($data){
		global $BE_USER;
		$BE_USER->setAndSaveSessionData($this->sessionName,$data);
		return;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function clearSessionData(){
		$this->setSessionData(array());
		return;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_gallery2_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
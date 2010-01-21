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
 * Class 'ajaxSingleView' for the 'gallery2' extension.
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
 *   64: class tx_gallery2_ajaxSingleView
 *   77:     function init($g2_itemId)
 *   97:     function main()
 *  154:     function printContent()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

error_reporting (E_ALL);

// Set standard-configurations
setlocale(LC_ALL, 'de_DE');
$BACK_PATH = '../../../../';

// Load TYPO3-Configuration
require_once($BACK_PATH.'typo3conf/localconf.php');
require_once($BACK_PATH.'t3lib/class.t3lib_div.php');
require_once($BACK_PATH.'t3lib/class.t3lib_extmgm.php');
require_once($BACK_PATH.'t3lib/class.t3lib_ajax.php');

// Load Gallery2-Configuration
$g2Conf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['gallery2']);
require_once($BACK_PATH.$g2Conf['g2RelPath'].'/modules/core/classes/Gallery.class');
$gallery =& new Gallery();
require_once($BACK_PATH.$g2Conf['g2RelPath'].'/config.php');

class tx_gallery2_ajaxSingleView {
	var $content = '';
	var $dblink;
	var $g2_itemId;
	var $showTitle = 0;
	var $showDescr = 0;

	/**
	 * Init-Function
	 *
	 * @param	integer		$g2_itemID: Id of Gallery-Item (only pictures)
	 * @return	void		..
	 */
	function init($g2_itemId){
		global $storeConfig;

		$this->g2_itemId = $g2_itemId;
		$this->showTitle = intval($_GET['showTitle']);
		$this->showDescr = intval($_GET['showDescr']);

		// Connect to gallery2-db-server
		$this->dblink = mysql_connect($storeConfig['hostname'],$storeConfig['username'],$storeConfig['password']);
		if (!$this->dblink) die('<b>Keine Verbindung möglich:</b> ' . mysql_error());

		// Select gallery2-db
		mysql_select_db($storeConfig['database']);
	}

	/**
	 * Mainfunction
	 *
	 * @return	void		..
	 */
	function main(){
		global $gallery,$g2Conf;

		$query = "SELECT g_id
			FROM g2_Derivative
			WHERE g_derivativeSourceId = ".$this->g2_itemId."
			AND g_derivativeOperations LIKE ('scale|%')
			LIMIT 1;";

		$res = mysql_query($query);

		if($res) {
			$idData = mysql_fetch_assoc($res);
			$g2_itemIdDisplay = $idData['g_id'];
			$query	= 'SELECT g2_DerivativeImage.g_width, g2_DerivativeImage.g_height FROM g2_DerivativeImage WHERE g2_DerivativeImage.g_id = '.$g2_itemIdDisplay;
			$res	= mysql_query($query);
			$sizesData = mysql_fetch_assoc($res);
			$width	= $sizesData['g_width'];
			$height	= $sizesData['g_height'];
		} else {
			$g2_itemIdDisplay = $this->g2_itemId;
			$query = 'SELECT g2_PhotoItem.g_width, g2_PhotoItem.g_height FROM g2_PhotoItem WHERE g2_PhotoItem.g_id = '.$g2_itemIdDisplay;
			$res = mysql_query($query);
			$data = mysql_fetch_assoc($res);
			$width = $data['g_width'];
			$height = $data['g_height'];
		}

		if(!$g2_itemIdDisplay) {
			$g2_itemIdDisplay = $this->g2_itemId;
		}

		$query = 'SELECT g2_Item.g_title, g2_Item.g_description FROM g2_Item WHERE g2_Item.g_id = '.$this->g2_itemId;
		$res = mysql_query($query);
		$data = mysql_fetch_assoc($res);
		$title = $data['g_title'];
		$description = $data['g_description'];

		$link = t3lib_div::locationHeaderUrl('/'.$g2Conf['g2RelPath'].'/main.php?g2_view=core.DownloadItem&g2_itemId='.$g2_itemIdDisplay);
		$out .= '<img src="'.$link.'" title="'.$title.'" alt="'.$title.'"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' />';

		if($title AND $this->showTitle) {
			$out .= '<span class="ajaxGalleryTitle">'.$title.'</span>';
		}

		if($description AND $this->showDescr) {
			$out .= '<span class="ajaxGalleryDescription">'.$description.'</span>';
		}

		$this->content .= $out;
	}

	/**
	 * Returns the xml-code
	 *
	 * @return	void		..
	 */
	function printContent(){
		$content =	'<data>';
		$content .=	'<htmlcode><![CDATA['.$this->content.']]></htmlcode>';
		$content .=	'</data>';

		header('Content-Type: text/xml');
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
<t3ajax>'.utf8_encode($content).'</t3ajax>';
		echo $xml;

		mysql_close($this->dblink);
	}
}

// Start the script
$object = t3lib_div::makeInstance('tx_gallery2_ajaxSingleView');
$object->init(intval($_GET['g2_itemId']));
$object->main();
$object->printContent();

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/scripts/tx_gallery2_ajaxsingleview.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/scripts/tx_gallery2_ajaxsingleview.php']);
}

// remove access-variables
unset($typo_db_host,$typo_db_username,$typo_db_password,$typo_db,$storeConfig);
?>
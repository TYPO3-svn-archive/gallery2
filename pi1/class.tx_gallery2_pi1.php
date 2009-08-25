<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Philipp Mueller <pmueller@juhui.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Plugin 'Gallery2' for the 'gallery2' extension.
 *
 * @author	Philipp Mueller <pmueller@juhui.ch>
 * @package TYPO3
 * @subpackage gallery2
 *
 */
#error_reporting (E_ALL);

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('gallery2').'lib/class.tx_gallery2_api.php');

// Load Gallery2-Configuration (includes database-access for gallery2)
$g2Conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gallery2']);
require_once($g2Conf['g2RelPath'].'/modules/core/classes/Gallery.class');
$gallery =& new Gallery();
require_once($g2Conf['g2RelPath'].'/config.php');

class tx_gallery2_pi1 extends tslib_pibase {
	var $prefixId = 'tx_gallery2_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_gallery2_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'gallery2';	// The extension key.

	var $g2Relpath;
	var $g2Path;
	var $g2DocPath;

	var $excludeIds='';

	/**
	 * Mainfunction
	 *
	 * @param	string		$content: ..
	 * @param	array		$conf: Configuration
	 * @return	string		HTML-Code
	 */
	function main($content,$conf) {
		$this->pi_setPiVarDefaults(); # Set piVars
		$this->pi_loadLL();	# Load Locallang

		// Set configuration with setup and flexform
		$this->pi_initPIflexForm();
		$T3FF = $this->cObj->data['pi_flexform'];
		$conf = $this->merge_conf($conf,$T3FF);
		$this->conf=$conf;

		$this->api = t3lib_div::makeInstance('tx_gallery2_api');
		$this->api->setConf();
		$this->api->loadGallery();

		// Link all items to a gallerypage
		if($conf['galleryPage']) $url = intval($conf['galleryPage']);
		else $url = $GLOBALS['TSFE']->id;

		$this->initGallery($url);

		$out = '';
		switch($conf['view']) { // configurate this option in flexform or typoscript
			case 'ajaxGallery':
				require_once(t3lib_extMgm::extPath('gallery2').'lib/class.tx_gallery2_ajax.php');
				$ajaxObj = t3lib_div::makeInstance('tx_gallery2_ajax');
				$ajaxObj->init($this);

				$out .= $ajaxObj->getAjaxGallery();
				break;
			case 'ajaxSingleview':
				require_once(t3lib_extMgm::extPath('gallery2').'lib/class.tx_gallery2_ajax.php');
				$ajaxObj = t3lib_div::makeInstance('tx_gallery2_ajax');
				$ajaxObj->init($this);

				$out .= $ajaxObj->getAjaxSingleview();
				break;
			case 'recentAlbumsSelf':
				$out .= '<div class="recentrandom">';
				$out .= $this->getRecentAlbumsSelf();
				$out .= '</div>';
				break;
			case 'recentAlbum':
			case 'recentImage':
				$out .= $this->getRecentBlock($conf['view']);
				break;
			case 'randomAlbum':
			case 'randomImage':
				$out .= $this->getMoreRandoms($conf['view']);
				break;
			default: # gallery
				$out .= $this->gallery();
				break;
		}

		return $out;
	}

	/**************************************************************************************************************************
	 *
	 * views
	 *
	 **************************************************************************************************************************/

	function checkGPvar(){
		$status = false;

		if(t3lib_div::_GET()) {
			foreach(t3lib_div::_GET() as $k=>$v) {
				if(preg_match('/g2_/',$k)) $status = true;
			}
		}

		if(t3lib_div::_POST()) {
			foreach(t3lib_div::_POST() as $k=>$v) {
				if(preg_match('/g2_/',$k)) $status = true;
			}
		}

		return $status;
	}

	/**
	 * Shows the gallery2 on the page
	 *
	 * @return	string		HTML-Code of G2
	 */
	function gallery() {
		GalleryCapabilities::set('showSidebar', false);

		if ($this->conf['showItem'] AND !$this->checkGPvar()) {
			$params = array('g2_itemId'=>$this->conf['showItem']);
			$typolink = $this->cObj->getTypoLink_URL($GLOBALS['TSFE']->id,$params);
			$locationHeaderURL = t3lib_div::locationHeaderURL($typolink);

			header('Location: '.$locationHeaderURL);
			exit;
		}

		$g2moddata = GalleryEmbed::handleRequest();

	    // put the body html from G2 into the template
	    $data['bodyHtml'] = isset($g2moddata['bodyHtml']) ? $g2moddata['bodyHtml'] : '';

		$this->api->writeGalleryHeader($data,$g2moddata);

		$out = $data['bodyHtml'];
		if($this->piVars['backlink']) {
			$url = t3lib_div::locationHeaderUrl(urldecode($this->piVars['backlink']));
			$out .= '<div class="backlink">'.$this->cObj->gettypolink($this->pi_getLL('backlink'),$url,array(),'_self').'</div>';
		}

		return $out;

	}

	/**
	 * Display random images of recent albums (use "count")
	 *
	 * @return	string		HTML-Code of G2
	 */
	function getRecentAlbumsSelf(){
		global $storeConfig;
		$conf = $this->conf;

		$gI		= 'g2_Item';
		$gAI	= 'g2_AlbumItem';
		$gCE	= 'g2_ChildEntity';

		$parentId	= $conf['showItem'];
		$select		= "$gI.g_id,$gI.g_title";
		$from		= "($gI INNER JOIN $gAI ON $gI.g_id = $gAI.g_id) INNER JOIN $gCE ON $gI.g_id = $gCE.g_id";

		if($parentId) {
			$where	= "$gCE.g_parentId = $parentId";
		} else {
			$where	= '';
		}

		$orderBy	= "$gI.g_originationTimestamp DESC";
		$limit		= ($conf['count']?$conf['count']:3);

		$galleryDB	= $GLOBALS['TYPO3_DB'];
		$galleryDB->sql_pconnect($storeConfig['hostname'], $storeConfig['username'], $storeConfig['password']);
		$galleryDB->sql_select_db($storeConfig['database']);

		$data	= $galleryDB->exec_SELECTgetRows($select,$from,$where,'',$orderBy,$limit);

		if($data) {
			$galleryURL = $this->cObj->gettypolink_URL($conf['galleryPage']);
			if(substr($thisURL,0,1)=='/') $thisURL = substr($thisURL,1);

			foreach($data as $k=>$row) {
				$this->conf['showItem'] = $row['g_id'];
				$imageBlock = $this->getRandomBlock('randomImage');
				$regex = '/(<a href=")(.+)(g2_itemId=)([0-9]+)(")(.+)/';
				$imageBlock = preg_replace($regex,'\1'.$galleryURL.(strpos($galleryURL,'?')?'&':'?').'\3 '.$row['g_id'].'\5 title="'.utf8_encode($row['g_title']).'" target="_top">',$imageBlock);
				$out .= '<div class="recentrandomitem recentrandomitem-'.$k.'">';
				$out .= str_replace(' '.$row['g_id'],$row['g_id'],$imageBlock);
				$out .= '<div class="recentrandom-title">'.utf8_encode($row['g_title']).'</div>';
				$out .= '</div>';
			}
		}

		return $out;
	}

	/**
	 * return random images or albums (use the configuration "count" for the number items)
	 *
	 * @param	string		$view: ...
	 * @return	string		HTML-Code of G2
	 */
	function getMoreRandoms($view){
		$out = '';

		if($this->conf['count'] < 1) $count = 1;
		else $count = $this->conf['count'];

		if($this->conf['fails']) $fails = $this->conf['fails'];
		else $fails = 5;

		if($this->conf['randomAlbumLink']) {
			global $storeConfig;

			$gI		= 'g2_Item';
			$gAI	= 'g2_AlbumItem';
			$gCE	= 'g2_ChildEntity';

			$select		= "$gI.g_id,$gI.g_title";
			$from		= "($gI INNER JOIN $gAI ON $gI.g_id = $gAI.g_id) INNER JOIN $gCE ON $gI.g_id = $gCE.g_parentId";
			$galleryDB	= $GLOBALS['TYPO3_DB'];
			$galleryDB->sql_pconnect($storeConfig['hostname'], $storeConfig['username'], $storeConfig['password']);
			$galleryDB->sql_select_db($storeConfig['database']);
		}

		$ids = array();
		$galleryURL = $this->cObj->gettypolink_URL($this->conf['galleryPage']);
		for($i=0;$i<$count;$i++) {
			$add = $this->getRandomBlock($view);
			preg_match('/(\?g2\_itemId\=)([0-9]+)(\">)/',$add,$matches);
			if(!in_array($matches[2],$ids)) {
				if($this->conf['dontlink']) {
					$add = preg_replace('/(<a\ href\=)(.+)/','',$add);
					$add = preg_replace('/(<\/a>)/','',$add);
				} elseif($this->conf['randomAlbumLink']) {
					$data = $galleryDB->exec_SELECTgetRows($select,$from,"$gCE.g_id = ".$matches[2],'','','1');
					$regex = '/(<a href=")(.+)(g2_itemId=)([0-9]+)(")(.+)/';
					$add = preg_replace($regex,'\1'.$galleryURL.'?\3 '.$data[0]['g_id'].'\5 title="'.utf8_encode($data[0]['g_title']).'" target="_top">',$add);
					$add = str_replace(' '.$data[0]['g_id'],$data[0]['g_id'],$add);
				}

				$out .= '<div class="randomItem randomItem'.($i+1).'">';
				$out .= $add;
				$out .= '</div>';

				$ids[] = $matches[2];
			} else {
				if($fails > 0) {
					$fails--;
					$i--;
				} else {
					break;
				}
			}
		}

		return $out;
	}

	/**
	 * returns a random album or image
	 *
	 * @param	string		$blocks: commalist of all blocks (see G2-Embed-Documentation)
	 * @return	string		HTML-Code of G2
	 */
	function getRandomBlock($blocks) {
		$conf=$this->conf;

		$params = array('blocks'=>$blocks,'show'=>$conf['show']);
		if($conf['showItem']) {
			$params['itemId']=$conf['showItem'];
		}

		if($conf['maxSize']) {
			$params['maxSize']=$conf['maxSize'];
		}

		$ret = GalleryEmbed::getImageBlock($params);

		if($ret[1]) {
			$out = $ret[1];
		} else {
			$out = '';
		}

		return $out;
	}

	/**
	 * returns a recent album or image
	 *
	 * @param	string		$blocks: commalist of all blocks (see G2-Embed-Documentation)
	 * @return	string		HTML-Code of G2
	 */
	function getRecentBlock($blocks){
		$conf=$this->conf;

		$params = array('blocks'=>$blocks,'show'=>$conf['show']);

		if($conf['showItem']) $params['itemId']=$conf['showItem'];
		if($conf['maxSize']) $params['maxSize']=$conf['maxSize'];

		$ret = GalleryEmbed::getImageBlock($params);

		$out = $ret[1];

		return $out;
	}

	/**************************************************************************************************************************
	 *
	 * helpers
	 *
	 **************************************************************************************************************************/

	/**
	 * initialize gallery2
	 *
	 * @param	string		$url: URL to an other page
	 * @return	void		...
	 */
	function initGallery($urlId=0){
		if($this->piVars) {
			$params = array();
			foreach($this->piVars as $k=>$v) {
				$params[$this->prefixId.'['.$k.']'] = $v;
			}
		}

		if(is_int($urlId)) {
			$url = $this->cObj->gettypolink_URL($urlId,$params);
		} else {
			$url = $this->cObj->gettypolink_URL($GLOBALS['TSFE']->id,$params);
		}

		if(substr($url,0,1)!='/') $url = '/'.$url;
		preg_match_all('/('.urlencode($GLOBALS['_SERVER']['REDIRECT_URL']).')(.+)/',urlencode($url),$regs);
		if($regs[2][0]) {
			$url = $GLOBALS['_SERVER']['REDIRECT_URL'].($regs[2][0]?urldecode($regs[2][0]):'');
		}

		$g2uri = $this->api->g2DocPath.'/'.$this->api->g2RelPath.'/';
		$this->g2url = $url;
		$g2config = array(
			'g2Uri'			=> $g2uri,
			'embedUri'		=> $url,
			'activeUserId'	=> '',
			'fullInit'		=> true,
		);

		$ret = GalleryEmbed::init($g2config);
	}

	/**
	 * Writes the configuration-array
	 *
	 * @param	array		$conf: typoscript-configuration
	 * @param	array		$ffconf: FlexForm-configuration
	 * @return	array		Merged Conf-Array
	 */
	function merge_conf($conf,$ffconf){
		$niceFFconf = array();
		if($ffconf['data']) {
			foreach($ffconf['data'] as $key=>$data) {
				foreach($data['lDEF'] as $k=>$v) {
					if($v['vDEF']) $niceFFconf[$k] = $v['vDEF'];
				}
			}
		}

		$mergedConf = t3lib_div::array_merge_recursive_overrule($conf,$niceFFconf);
		return $mergedConf;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/pi1/class.tx_gallery2_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/pi1/class.tx_gallery2_pi1.php']);
}

?>
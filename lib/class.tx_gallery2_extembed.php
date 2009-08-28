<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Philipp Mueller <pmueller@juhui.ch>
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
 *
 * @author	Philipp Mueller <pmueller@juhui.ch>
 * @company Jud Grafik+Internet, www.juhui.ch
 * @package TYPO3
 * @subpackage gallery2
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   71: class tx_gallery2_extembed extends tx_gallery2_api
 *   90:     function init($pObj)
 *
 *              SECTION: Actions
 *  115:     function login($user)
 *  126:     function checkActiveUser($activeUserId)
 *  136:     function logout()
 *  149:     function createAlbum($form,$uid,$parentId)
 *  171:     function updateAlbum($album,$g2form,$user='')
 *  191:     function deleteAlbum($uid)
 *
 *              SECTION: Views
 *  216:     function showAlbumOnly($id)
 *  239:     function getUploadForms($id)
 *
 *              SECTION: Option-Handling
 *  271:     function disableOptions()
 *  310:     function enableOptions($fdata=array())
 *
 *              SECTION: Gallery-Handling
 *  333:     function makeGallery()
 *  349:     function initGallery()
 *
 *              SECTION: Helper-Functions
 *  411:     function isAlbum($uid)
 *  422:     function getAlbumId($uid)
 *  433:     function getAlbumObject($albumId)
 *
 * TOTAL FUNCTIONS: 16
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('gallery2').'lib/class.tx_gallery2_api.php');

class tx_gallery2_extembed extends tx_gallery2_api {
	var $prefixId = 'tx_gallery2_extembed';        // Same as class name
    var $scriptRelPath = 'lib/class.tx_gallery2_extembed.php';    // Path to this script relative to the extension dir.
    var $extKey = 'gallery2';    // The extension key.

	var $cObj;
	var $conf = array();

	var $pObj;
	var $pPrefixId;
	var $pExtKey;
	var $pScriptRelPath;

	/**
	 * Init from extension
	 *
	 * @param	cObj		$pObj: Parent Object
	 * @return	void		..
	 */
	function init($pObj){
		$this->pObj=$pObj;
		$this->cObj=$pObj->cObj;
		$this->pPrefixId = $pObj->prefixId;
    	$this->pScriptRelPath = $pObj->scriptRelPath;
    	$this->pExtKey = $pObj->extKey;

		$this->makeDummyFiles();
		$this->setConf();
		$this->loadGallery();
		$this->initGallery();
	}

	/**************************************************************************************************************************
	 *
	 * Actions
	 *
	 **************************************************************************************************************************/

	/**
	 * login user
	 *
	 * @param	string		$user: username
	 * @return	boolean		...
	 */
	function login($user){
		$ret = GalleryEmbed::login($user);
		return ($ret?false:true);
	}

	/**
	 * check if user active
	 *
	 * @param	integer		$id: ..
	 * @return	boolean		..
	 */
	function checkActiveUser($activeUserId){
		$ret = GalleryEmbed::checkActiveUser($activeUserId);
		return ($ret?false:true);
	}

	/**
	 * logout user
	 *
	 * @return	boolean		...
	 */
	function logout(){
		$ret = GalleryEmbed::logout();
		return ($ret?false:true);
	}

	/**
	 * Create an album
	 *
	 * @param	array		$form: Form-Data
	 * @param	integer		$uid: ID of entry
	 * @param	integer		$parentId: Id of Parent G2-Album
	 * @return	integer		Album-Id
	 */
	function createAlbum($form,$uid,$parentId){
		if($form AND $uid AND $parentId) {
			$data = GalleryCoreApi::createAlbum($parentId,$form['pathComponent'],$form['title'],$form['summary'],$form['description'],$form['keywords']);
			if(!$data[0] AND $data[1]) {
				$ret = GalleryCoreApi::addMapEntry('ExternalIdMap',array('externalId' => $uid,'entityType' => 'GalleryAlbumItem', 'entityId' => $data[1]->id));
				if(!$ret) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Update an album
	 *
	 * @param	object		$album: Album-Object
	 * @param	array		$g2form: Form-Data
	 * @param	string		$user: Username
	 * @return	void		..
	 */
	function updateAlbum($album,$g2form,$user=''){
		if($album AND $g2form) {

			$settitle = $album->settitle($g2form['title']);
			$album->setkeywords($g2form['keywords']);
			$album->setdescription($g2form['description']);
			$album->setpathcomponent($g2form['pathComponent']);
			GalleryCoreApi::acquireWriteLock($album->getId());
			$save = $album->save();
		}

		return false;
	}

	/**
	 * Remove an album and the ext MapEntry
	 *
	 * @param	integer		$uid: ID of ext-entry
	 * @return	void		..
	 */
	function deleteAlbum($uid){
		if(intval($uid)) {
			require_once($this->g2Path.'/modules/core/classes/helpers/GalleryEntityHelper_simple.class');
			$albumId = $this->getAlbumId($uid);
			$ret = GalleryCoreApi::removeMapEntry('ExternalIdMap', array('externalId' => $uid,'entityType' => 'GalleryAlbumItem', 'entityId' => $albumId));
			$ret2 = GalleryCoreApi::deleteEntityById($albumId);

			return true;
		}

		return false;
	}

	/**************************************************************************************************************************
	 *
	 * Views
	 *
	 **************************************************************************************************************************/

	/**
	 * Display an album
	 *
	 * @param	integer		$id: ID
	 * @return	string		HTML-Code
	 */
	function showAlbumOnly($id){
		$showItem = GalleryUtilities::getRequestVariables('view');
		$itemId = GalleryUtilities::getRequestVariables('itemId');

		if (empty($showItem) AND empty($itemId)) {
			GalleryUtilities::putRequestVariable('view', 'core:ShowItem');
			GalleryUtilities::putRequestVariable('itemId', $id);
		}
		$fdatatemp = GalleryFactoryHelper_simple::_getFactoryData();

		$fdataAll = $this->disableOptions();
		$out = $this->makeGallery();
		$this->enableOptions($fdataAll);

		return $out;
	}

	/**
	 * get the uploadforms back
	 *
	 * @param	integer		$id: id of album
	 * @return	string		HTML-Code
	 */
	function getUploadForms($id){
		global $gallery;

		$view = GalleryUtilities::getRequestVariables('view');
		$subView = GalleryUtilities::getRequestVariables('subView');
		$itemId = GalleryUtilities::getRequestVariables('itemId');

		if (empty($view) AND empty($itemId) AND empty($subView)) {
			GalleryUtilities::putRequestVariable('view', 'core.ItemAdmin');
			GalleryUtilities::putRequestVariable('subView', 'core.ItemAdd');
			GalleryUtilities::putRequestVariable('addPlugin', 'ItemAddUploadApplet');
			GalleryUtilities::putRequestVariable('itemId', $id);
		}

		$fdata = $this->disableOptions();
		$out = $this->makeGallery();
		$this->enableOptions($fdata);

		return $out;
	}

	/**************************************************************************************************************************
	 *
	 * Option-Handling
	 *
	 **************************************************************************************************************************/

	/**
	 * Disable Options
	 *
	 * @return	array		$fdata: disabled options
	 */
	function disableOptions(){
		$fdata = array();
		$optionData = array('ItemEditPlugin'=> array('ItemEditItem','ItemEditRotateAndScalePhoto','ItemEditPhotoThumbnail'),
							'ItemEditOption'=> array(),
							'ItemAddPlugin'	=> array('ItemAddUploadApplet'));

		foreach($optionData AS $plugin => $options) {
			if($plugin) {
				$fdatatemp = GalleryFactoryHelper_simple::_getFactoryData();

				if($fdatatemp[1]['implementations'][$plugin]) {
					foreach($fdatatemp[1]['implementations'][$plugin] as $k=>$v) {
						$fdata[$plugin][$k]['className'] = $k;
						$fdata[$plugin][$k]['implId'] = $k;
						$fdata[$plugin][$k]['implPath'] = $v;
						$fdata[$plugin][$k]['implModuleId'] = $fdatatemp[1]['pluginIds'][$plugin][$k];
					}
				}
				if($fdata[$plugin]) {
					foreach($fdata[$plugin] as $k=>$v) {
						if(in_array($k,$options)) {
							unset($fdata[$plugin][$k]);
						} else {
							GalleryCoreApi::unregisterFactoryImplementation($plugin, $k);
						}
					}
				}
			}
		}

		return $fdata;
	}

	/**
	 * Enable options
	 *
	 * @param	array		$fdata: options to enable
	 * @return	void		..
	 */
	function enableOptions($fdata=array()){
		if($fdata) {
			foreach($fdata as $plugin => $row) {
				if($plugin AND $row) {
					foreach($row as $k=>$v) {
						GalleryCoreApi::registerFactoryImplementation($plugin, $v['className'], $v['implId'], $v['implPath'],$v['implModuleId'], '');
					}
				}
			}
		}
	}

	/**************************************************************************************************************************
	 *
	 * Gallery-Handling
	 *
	 **************************************************************************************************************************/

	/**
	 * Create the gallery
	 *
	 * @return	string		bodyhtml
	 */
	function makeGallery(){
		$this->login($this->pObj->conf['gallery2.']['user']);

		GalleryCapabilities::set('showSidebarBlocks', false);
		$data = GalleryMain(true);
		$g2moddata = GalleryEmbed::handleRequest();
		$this->writeGalleryHeader($data, $g2moddata);

		return $data['bodyHtml'];
	}

	/**
	 * initialize the gallery
	 *
	 * @return	void
	 */
	function initGallery(){
		$lConf = $this->pObj->conf['gallery2.'];
		global $GLOBALS;

		$user = Array(
			'username' => $lConf['user'],
			'email' => $lConf['email'],
			'fullname' => 'News-Administrator',
			'hashedpassword' => md5(time()),
			'hashmethod' => 'md5',
			'creationtimestamp' => time()
		);

	    $ret = GalleryEmbed::init(array(
					'g2Uri' => $this->g2DocPath.'/'.$this->g2RelPath.'/',
					'embedUri' => ($this->pObj->url?$this->pObj->url:$GLOBALS['_SERVER']['REQUEST_URI']),
					'activeUserId' => $user['username']
					));

		#debug($ret);
		if ($ret) {
		     # Error! Did we get an error because the user doesn't exist in g2 yet?
		     $ret2 = GalleryEmbed::isExternalIdMapped($user['username'], 'GalleryUser');

		     if ($ret2 && $ret2->getErrorCode() & ERROR_MISSING_OBJECT) {
				 /* The user does not exist in G2 yet. Create in now on-the-fly */
		         $ret = GalleryEmbed::createUser($user['username'], $user);

		         if ($ret) {
		             /* An error during user creation. Not good, print an error or do whatever is appropriate
		              * in your emApp when an error occurs */
		             print 'An error occurred during the on-the-fly user creation <br>';
		             print $ret->getAsHtml();
		             exit;
		         }
		     } else {
		         # The error we got wasn't due to a missing user, it was a real error
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

	/**************************************************************************************************************************
	 *
	 * Helper-Functions
	 *
	 **************************************************************************************************************************/

	/**
	 * Show if an album is available
	 *
	 * @param	integer		$uid: ID of News-Entry
	 * @return	boolean		If available or not
	 */
	function isAlbum($uid){
		$ret = GalleryEmbed::isExternalIdMapped($uid, 'GalleryAlbumItem');
		return !($ret && $ret->getErrorCode() & ERROR_MISSING_OBJECT);
	}

	/**
	 * Show if an album-id
	 *
	 * @param	integer		$uid: ID of News-Entry
	 * @return	boolean		If available or not
	 */
	function getAlbumId($uid){
		$ret = GalleryEmbed::getExternalIdMap('externalId');
		return $ret[1][$uid]['entityId'];
	}

	/**
	 * Get album-id
	 *
	 * @param	integer		$albumId: Albumid
	 * @return	array		Album-Data
	 */
	function getAlbumObject($albumId){
		list ($ret, $rootAlbum) = GalleryCoreApi::loadEntitiesById($albumId);

		if(!$ret AND $rootAlbum) {
			return $rootAlbum;
		}

		return false;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/lib/class.tx_gallery2_extembed.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/lib/class.tx_gallery2_extembed.php']);
}
?>
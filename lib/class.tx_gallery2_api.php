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
 * class 'tx_gallery2_api' for the 'gallery2' extension.
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
 *   55: class tx_gallery2_api
 *
 *              SECTION: filefunctions (obsolete)
 *   70:     function makeDummyFiles()
 *  116:     function mkdir_recursive($path)
 *
 *              SECTION: gallery2 functions
 *  146:     function loadGallery($g2path='')
 *  158:     function writeGalleryHeader($data,$g2moddata)
 *
 *              SECTION: helpers
 *  205:     function setConf()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_gallery2_api {
	var $extKey = 'gallery2';

	/**************************************************************************************************************************
	 *
	 * filefunctions (obsolete)
	 *
	 **************************************************************************************************************************/

	/**
	 * Write Dummy file because a bug of the G2-upload-applet!
	 * (obsolete)
	 *
	 * @return	void		..
	 */
	function makeDummyFiles(){
		global $TYPO3_CONF_VARS;
		if($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['g2FEedit']) {
			$dummyfilearray = array(
				'com/gallery/GalleryRemote/resources' => array(	'GRResources.class',
																'GRResources.class',
																'GRResources_de.class',
																'GRResources_de.class',
																'GRResources_de_CH.class',
																'GRResources_de_CH.properties',
																'GRResources_de_CH.class',
																'GRResources_de_DE.class',
																'GRResources_de_DE.class',
																'GRResources_de_DE.properties'),

				'com/gallery/GalleryRemote/util'	  => array(	'OsShutdown.class'),

				'com/drew/imaging/jpeg'			  	  => array(	'JpegProcessingException.class'),
			);

			foreach($dummyfilearray as $k=>$v) {
				$dummypath = $GLOBALS['_SERVER']['DOCUMENT_ROOTDOCUMENT_ROOT'].'/'.$k;
				if(!is_dir($dummypath)) {
					if(!$this->mkdir_recursive($dummypath)) {
						die('<b>Fatal Error:</b> can\'t create folders: <b>'.$dummypath.'</b>');
					}
				}

				foreach($v as $file) {
					$dummyfile = $dummypath.'/'.$file;
					if(!is_file($dummyfile)) {
						$handle = fopen($dummyfile,'x');
						fclose($handle);
						if(!is_file($dummyfile)) die('<b>Fatal Error:</b> can\'t create file: '.$dummyfile);
					}
				}
			}
		}
	}

	/**
	 * Creates folders recursive
	 *
	 * @param	string		$path: Path of directory
	 * @return	boolean		Has created or not
	 */
	function mkdir_recursive($path) {
		$pathElements = explode('/',$path);
		$tPath = '';

		foreach ($pathElements AS $k => $v) {
	    	if(!$v) unset($addElements[$k]);
			else {
				$tPath .=  '/'.$v;
				if (!is_dir($tPath)) {
				    mkdir($tPath);
				}
			}
		}

		if(is_dir($path)) return true;
		else return false;
	}

	/**************************************************************************************************************************
	 *
	 * gallery2 functions
	 *
	 **************************************************************************************************************************/

	/**
	 * Load gallery2-data
	 *
	 * @param	string		$g2path: Gallery2-Path
	 * @return	void		..
	 */
	function loadGallery($g2path=''){
		if(!$g2path) $g2path = $this->g2Path;
		require_once($g2path.'/embed.php');
	}

	/**
	 * writes the a gallery-header
	 *
	 * @param	array		$data: ..
	 * @param	array		$g2moddata: ..
	 * @return	void		...
	 */
	function writeGalleryHeader($data,$g2moddata){
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
	    if(!$this->conf['dontIncludeCSS']) {
		    if (!empty($css)) {
		      foreach ($css as $style) {
			     $data['css'] .= "\n".$style;
		      }
		    }
	    }

		if (!$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId]) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = $data['javascript']."\n";

			if(!$this->conf['dontIncludeCSS']) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] .= $data['css']."\n";
			}
		}
	}

	/**************************************************************************************************************************
	 *
	 * helpers
	 *
	 **************************************************************************************************************************/

	/**
	 * Set the configuration-data
	 *
	 * @return	void		..
	 */
	function setConf(){
		$this->conf = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey];
		$this->g2RelPath = $this->conf['g2RelPath'];
		$this->g2Path = $this->conf['g2RelPath'];
		ereg('(.+)(/typo3)(.+)',$GLOBALS['_SERVER']['SCRIPT_NAME'],$regs);
		$this->g2DocPath = ($regs[1]?$regs[1]:'');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/lib/class.tx_gallery2_api.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/lib/class.tx_gallery2_api.php']);
}
?>
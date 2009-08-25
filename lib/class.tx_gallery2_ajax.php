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
 * AJAX for the 'gallery2' extension.
 *
 * @author	Philipp Mueller <pmueller@juhui.ch>
 * @company Jud Grafik+Internet, www.juhui.ch
 * @package TYPO3
 * @subpackage gallery2
 */

require_once(PATH_t3lib.'class.t3lib_ajax.php');

class tx_gallery2_ajax {
	var $extKey = 'gallery2';
	var $pObj;

	/**
	 * Init-Function
	 *
	 * @param	object		$pObj: Parent Object
	 * @return	void		..
	 */
	function init(&$pObj){
		$this->pObj = $pObj;
	}

	/**
	 * returns the Ajax-Gallery
	 *
	 * @return	string		HTML-Code of the Gallery
	 */
	function getAjaxGallery(){
		$out = '';

		$out .= $this->getAjaxFunctions('getSingleview_ajax');
		$out .= $this->getGalleryCode();

		return $out;
	}

	/**
	 * returns the Gallery-Code with replaced Image-Links
	 *
	 * @return	string		HTML-Code
	 */
	function getGalleryCode(){
		$out = '';

		// Create Gallery-HTML
		$gallery = $this->pObj->gallery();

		// Replace all Image-Links with javascript-links
		$regex = '/(\<div class\=\"giItemCell\"\>)([[:space:]]+)(\<a href\=\")(.+)(\?g2_itemId\=)([0-9]+)("\>)/';
		preg_match_all($regex,$gallery,$matches);
		$gallery = preg_replace($regex,'\1\2\3javascript:getSingleview(\'\6\');\7',$gallery);

		$out .= $gallery;
		return $out;
	}

	/**
	 * returns the single-view-layer
	 *
	 * @return	string		HTML-Code
	 */
	function getAjaxSingleview(){
		return '<div id="gallerySingleView"></div>';
	}

	/**
	 * Returns the javascript-functions for AJAX
	 *
	 * @param	string		$function: Name of Javascript-Function
	 * @return	string		JavaScript-Code inkl. HTML-Wrap
	 */
	function getAjaxFunctions($function){
		if($this->pObj->conf['showAjaxTitle']) {
			$showTitle=' + \'&showTitle=1';
		} else {
			$showTitle=' + \'&showTitle=0';
		}

		if($this->pObj->conf['showAjaxDescription']) {
			$showDescr='&showDescr=1\'';
		} else {
			$showDescr='&showDescr=0\'';
		}

		$jsHeader = '
		function getSingleview(g2itemId)	{
			var url = \''.t3lib_extMgm::siteRelPath('gallery2').'scripts/tx_gallery2_ajaxsingleview.php?g2_itemId=\' + g2itemId'.$showTitle.$showDescr.';
			ajax_doRequest(url);
		}

		function getSingleview_ajax(t3ajax) {
			if (t3ajax.getElementsByTagName("data")[0])	{
				var content	= t3ajax.getElementsByTagName("data")[0].getElementsByTagName("htmlcode")[0].firstChild.data;
			} else {
				var content = \'\';
			}

			document.getElementById(\'gallerySingleView\').innerHTML = content;
		}';

		$jsHeader .= t3lib_ajax::getJScode($function);
		$out .= t3lib_div::wrapJS($jsHeader);
		$out .= '<noscript><span class="error">Bitte aktivieren Sie die Javascript-Unterstützung, damit die Gallery korrekt dargestellt werden kann.</span></noscript>';

		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/gallery2/lib/class.tx_gallery2_ajax.php'])    {
    include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/gallery2/lib/class.tx_gallery2_ajax.php']);
}
?>
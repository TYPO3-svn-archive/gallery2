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
 * Class 'albumview' for the 'gallery2' extension.
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
 *   47: class tx_gallery2_albumview
 *   62:     function displayAlbumTree($PA,$fobj)
 *   88:     function getSelectorArray($albums,$prefix='')
 *  103:     function getSelectForm($albums)
 *  133:     function getAlbumArray($parentid=0)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_gallery2_albumview {
	var $g2table=array(	'album'			=> 'g2_AlbumItem',
						'childentity'	=> 'g2_ChildEntity',
						'item'			=> 'g2_Item');
	var $selectorArray=array();
	var $selectorPrefix = '';
	var $TSConfig = array();

	/**
	 * Returns the selectorfield
	 *
	 * @param	array		$PA: ..
	 * @param	array		$fobj: ..
	 * @return	string		HTML-Code
	 */
	function displayAlbumTree($PA,$fobj){
		global $BE_USER;

		$this->PA = $PA;
		$this->pObj = &$PA['pObj'];

		$this->TSConfig = $BE_USER->getTSConfig('gallery2');

		if($this->TSConfig['properties']['parentId']) $parentid = $this->TSConfig['properties']['parentId'];
		else $parentid = 0;

		$albums = $this->getAlbumArray($parentid);
		$this->getSelectorArray($albums);

		$out = $this->getSelectForm($this->selectorArray);

		return $out;
	}

	/**
	 * Format the array to use with selectorbox
	 *
	 * @param	array		$albums: Albumarray
	 * @param	string		$prefix: Prefix for albumname
	 * @return	void		Write a global variable
	 */
	function getSelectorArray($albums,$prefix=''){
		foreach($albums as $k=>$v) {
			$this->selectorArray[$k]=$prefix.'-&nbsp;'.$v['g_title'];
			if($v['sub']) {
				$this->getSelectorArray($v['sub'],$prefix.'&nbsp;&nbsp;');
			}
		}
	}

	/**
	 * Get a selector-box for G2-Albums
	 *
	 * @param	array		$albums: Dataarray
	 * @return	string		HTML-Code of Selectorbox
	 */
	function getSelectForm($albums){
		$config = $this->PA['fieldConf']['config'];
		$c=0;
		$sI=0;

		foreach($albums as $value=>$label) {
			$sM = ($this->PA['itemFormElValue']==$value?' selected="selected"':'');
			if ($sM) $sI = $c;

			$options[] = '<option value="'.htmlspecialchars($value).'"'.$sM.'>'.utf8_encode($label).'</option>';
			$c++;
		}

		#$sOnChange = 'if (this.options[this.selectedIndex].value==\'--div--\') {this.selectedIndex='.$sI.';} '.implode('',$this->PA['fieldChangeFunc']);
		$sOnChange = $this->PA['fieldConf']['onChange'];

		$out = '';
		$out .= '<select name="'.$this->PA['itemFormElName'].'" size="'.$c.'"'.$this->pObj->insertDefStyle('select').' onchange="'.htmlspecialchars($sOnChange).'">';
		$out .= implode('',$options);
		$out .= '</select>';

		return $out;
	}

	/**
	 * Returns the albums of G2-DB
	 *
	 * @param	integer		$parentid: ParentId of album
	 * @return	array		Alben
	 */
	function getAlbumArray($parentid=0){
		$select = 'a.g_id,i.g_title';
		$from = '('.$this->g2table['album'].' a INNER JOIN '.$this->g2table['childentity'].' c ON a.g_id=c.g_id) INNER JOIN '.$this->g2table['item'].' i ON c.g_id=i.g_id';
		$where = 'c.g_parentid='.$parentid;
		$orderBy = 'c.g_id';

		$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where,$groupBy,$orderBy,$limit,'g_id');
		$out = array();

		if($data) {
			foreach($data as $k=>$v) {
				$out[$k]=$v;
				$out[$k]['sub']=$this->getAlbumArray($v['g_id']);
				unset($out[$k]['g_id']);
			}
		}

		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/class.tx_gallery2_albumview.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/gallery2/class.tx_gallery2_albumview.php']);
}
?>
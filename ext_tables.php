<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

# PI 1
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPlugin(Array('LLL:EXT:gallery2/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:gallery2/flexform_ds_pi1.xml');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Gallery2');

# Backend
if (TYPO3_MODE=='BE')	{
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_gallery2_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_gallery2_pi1_wizicon.php';
	t3lib_extMgm::addModule($TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['g2mainMod'],'txgallery2M1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_gallery2_albumview.php');
}
?>
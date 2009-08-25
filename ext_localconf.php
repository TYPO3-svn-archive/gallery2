<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['g2RelPath'] 		= $_EXTCONF['g2RelPath'] ? $_EXTCONF['g2RelPath'] : 'gallery2';
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['g2FEedit'] 		= 0; // no more used
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['g2mainMod'] 		= $_EXTCONF['g2mainMod'] ? $_EXTCONF['g2mainMod'] : 'user';
$TYPO3_CONF_VARS['EXTCONF'][$_EXTKEY]['dontIncludeCSS']	= $_EXTCONF['dontIncludeCSS'] ? $_EXTCONF['dontIncludeCSS'] : 0;

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_gallery2_pi1 = < plugin.tx_gallery2_pi1.CSS_editor
',43);

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_gallery2_pi1.php','_pi1','list_type',0);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_gallery2_pi2 = < plugin.tx_gallery2_pi2.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_gallery2_pi2.php','_pi2','list_type',0);


  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_gallery2_pi3 = < plugin.tx_gallery2_pi3.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi3/class.tx_gallery2_pi3.php','_pi3','list_type',0);

t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/res/pageTSConfig.txt">');
t3lib_extMgm::addUserTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/res/userTSConfig.txt">');

?>
<?php
global $TYPO3_CONF_VARS;

define('TYPO3_MOD_PATH', '../typo3conf/ext/gallery2/mod1/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']=$TYPO3_CONF_VARS['EXTCONF']['gallery2']['g2mainMod'].'_txgallery2M1'; // must be set in index.php too!!
$MCONF['access']='user,group';
$MCONF['script']='index.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:gallery2/mod1/locallang_mod.xml';
?>
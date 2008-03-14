<?php
if (!defined ("TYPO3_MODE")) {
	die ("Access denied.");
}

//$TYPO3_CONF_VARS["FE"]["XCLASS"]["tslib/class.tslib_fe.php"]=t3lib_extMgm::extPath("loginusertrack")."class.ux_tslib_fe.php";

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/class.tx_loginusertrack_tsfehook.php:tx_loginusertrack_tsfehook';

?>
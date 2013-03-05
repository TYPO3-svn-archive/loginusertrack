<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "loginusertrack".
 *
 * Auto generated 05-03-2013 23:28
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Login User Tracking',
	'description' => 'Logs in a separate table each time a frontend user logs in and further the timespan of the session and viewed pages. Backend module provides statistics over the data.',
	'category' => 'module',
	'shy' => 0,
	'version' => '2.0.8',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Kasper Skaarhoj',
	'author_email' => 'extensions@netcreators.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.4.0-0.0.0',
			'typo3' => '4.1.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"2fa6";s:36:"class.tx_loginusertrack_tsfehook.php";s:4:"d60a";s:12:"ext_icon.gif";s:4:"0bef";s:17:"ext_localconf.php";s:4:"e15d";s:14:"ext_tables.php";s:4:"2003";s:14:"ext_tables.sql";s:4:"402c";s:16:"locallang_db.xml";s:4:"0e8f";s:7:"tca.php";s:4:"ae12";s:14:"doc/manual.sxw";s:4:"a0e2";s:42:"mod1/class.tx_loginusertrack_lastlogin.php";s:4:"f1ec";s:42:"mod1/class.tx_loginusertrack_pagestats.php";s:4:"fab0";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"2c0e";s:14:"mod1/index.php";s:4:"6bd8";s:18:"mod1/locallang.xml";s:4:"0ead";s:22:"mod1/locallang_mod.xml";s:4:"ec16";s:19:"mod1/moduleicon.gif";s:4:"0bef";}',
	'suggests' => array(
	),
);

?>
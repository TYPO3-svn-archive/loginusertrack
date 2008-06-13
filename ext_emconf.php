<?php

########################################################################
# Extension Manager/Repository config file for ext: "loginusertrack"
#
# Auto generated 25-04-2008 12:49
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Login User Tracking',
	'description' => 'Logs in a separate table each time a fronend user logs in and further the timespan of the session and viewed pages. Backend module provides statistics over the data.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Kasper Skårhøj',
	'author_email' => 'kasper@typo3.com',
	'author_company' => 'Curby Soft Multimedia',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.1.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.1.0-0.0.0',
			'php' => '4.4.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:18:{s:9:"ChangeLog";s:4:"8a7b";s:36:"class.tx_loginusertrack_tsfehook.php";s:4:"e2fe";s:21:"class.ux_tslib_fe.php";s:4:"2e88";s:12:"ext_icon.gif";s:4:"0bef";s:17:"ext_localconf.php";s:4:"9936";s:14:"ext_tables.php";s:4:"2003";s:14:"ext_tables.sql";s:4:"10c0";s:16:"locallang_db.xml";s:4:"2b7d";s:7:"tca.php";s:4:"53a5";s:14:"doc/manual.sxw";s:4:"a0e2";s:42:"mod1/class.tx_loginusertrack_lastlogin.php";s:4:"ba94";s:42:"mod1/class.tx_loginusertrack_pagestats.php";s:4:"fab0";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"2c0e";s:14:"mod1/index.php";s:4:"2840";s:18:"mod1/locallang.xml";s:4:"2fd4";s:22:"mod1/locallang_mod.xml";s:4:"ec16";s:19:"mod1/moduleicon.gif";s:4:"0bef";}',
	'suggests' => array(
	),
);

?>
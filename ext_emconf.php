<?php

########################################################################
# Extension Manager/Repository config file for ext: "loginusertrack"
#
# Auto generated 13-03-2008 12:48
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
	'version' => '1.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:13:{s:21:"class.ux_tslib_fe.php";s:4:"2e88";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"f416";s:14:"ext_tables.php";s:4:"0469";s:14:"ext_tables.sql";s:4:"aa68";s:14:"doc/manual.sxw";s:4:"a0e2";s:42:"mod1/class.tx_loginusertrack_lastlogin.php";s:4:"c425";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"fdff";s:14:"mod1/index.php";s:4:"79f7";s:18:"mod1/locallang.php";s:4:"4734";s:22:"mod1/locallang_mod.php";s:4:"2461";s:19:"mod1/moduleicon.gif";s:4:"8074";}',
);

?>
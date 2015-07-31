<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "loginusertrack".
 *
 * Auto generated 30-07-2015 23:50
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Login User Tracking',
	'description' => 'Logs in a separate table each time a frontend user logs in and further the timespan of the session and viewed pages. Backend module provides statistics over the data.',
	'category' => 'module',
	'version' => '2.1.0',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Daniel Minder',
	'author_email' => 'typo3@minder.de',
	'author_company' => '',
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '6.2.0-6.2.99',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);


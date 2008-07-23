<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2002 Kasper Sk�rh�j (kasper@typo3.com)
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
 * Module 'User Track' for the 'loginusertrack' extension.
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 */

// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require('conf.php');
require($BACK_PATH.'init.php');
require($BACK_PATH.'template.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]

require_once(t3lib_extMgm::extPath('loginusertrack', 'mod1/class.tx_loginusertrack_lastlogin.php'));
require_once(t3lib_extMgm::extPath('loginusertrack', 'mod1/class.tx_loginusertrack_pagestats.php'));

class tx_loginusertrack_module1 extends t3lib_SCbase {
	var $pageinfo;

	function tx_loginusertrack_module1() {
		$GLOBALS['LANG']->includeLLfile('EXT:loginusertrack/mod1/locallang.php');
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('function1'),
	#			'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
				'4' => $LANG->getLL('function4'),
				'5' => $LANG->getLL('function5'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module.
	 * Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$HTTP_GET_VARS,$HTTP_POST_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="index.php" method="post">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript">
					script_ended = 1;
					if (top.theMenu) top.theMenu.recentuid = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->middle();
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content with a switch-construct
	 *
	 * @return	void
	 */
	function moduleContent()	{
		global $LANG;

		$userId = intval(t3lib_div::GPvar('useruid'));
		$sessId = intval(t3lib_div::_GP('sessid'));
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
				$list = array();
				$query='SELECT tx_loginusertrack_stat.*,fe_users.username,fe_users.name,fe_users.uid AS user_uid FROM tx_loginusertrack_stat,fe_users WHERE fe_users.uid=tx_loginusertrack_stat.fe_user'.
						($userId ? ' AND fe_users.uid='.intval($userId) : '').
						($sessId ? ' AND tx_loginusertrack_stat.uid=' . $sessId : '') .
						' AND fe_users.pid = '.intval($this->id).
						t3lib_BEfunc::deleteClause('fe_users').
						' ORDER BY session_login DESC LIMIT 200';
				$res = $GLOBALS['TYPO3_DB']->sql_query($query);
				while(($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
					$extra = '';
					if ($userId) {
						$extra .= '&sessid=' . $row['uid'];
					}
					$list[]='<tr bgcolor="'.$this->doc->bgColor4.'">
						<td nowrap>'.date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'].' '.$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],$row['session_login']).'</td>
						<td nowrap>'.t3lib_BEfunc::calcAge(time()-$row['session_login'],$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')).'</td>
						<td nowrap><a href="index.php?id='.$this->id.'&useruid='.$row['user_uid'].$extra.'">'.$row['username'].'</a></td>
						<td nowrap>'.$row['name'].'</td>
						<td nowrap>'.t3lib_BEfunc::calcAge($row['last_page_hit']-$row['session_login'],$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')).'</td>
						<td>'.$row['session_hit_counter'].'</td>
					</tr>
					';
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);

				$content='<table border="0" cellpadding="1" cellspacing="1" width="100%">
				<tr bgcolor="'.$this->doc->bgColor5.'">
					<td><strong>'.$LANG->getLL('header_datetime').'</strong></td>
					<td><strong>'.$LANG->getLL('header_age').'</strong></td>
					<td><strong>'.$LANG->getLL('header_username').'</strong></td>
					<td><strong>'.$LANG->getLL('header_name').'</strong></td>
					<td><strong>'.$LANG->getLL('header_session_lgd').'</strong></td>
					<td><strong>'.$LANG->getLL('header_pagehits').'</strong></td>
				</tr>
				'.implode('',$list).'</table>';

				if ($userId > 0) {
					$inst = t3lib_div::makeInstance('tx_loginusertrack_pagestats');
					/* @var $inst tx_loginusertrack_pagestats */
					$content = '<a href="index.php?id=' . $this->id . ($sessId ? '&useruid=' . $userId : '') .
								'"><strong>'.$LANG->getLL($sessId ? 'modulecont_listAllSessions' : 'modulecont_listAllUsers').
								'</strong></a><br /> <br />'.$content.
								$this->doc->section($LANG->getLL('header_pagestats'),
								$sessId ?
									$inst->getPageStatsForSession($this->doc, $sessId) :
									$inst->getPageStats($this->doc, $userId)) .
								'<br /> <br />';
				}

				$this->content.=$this->doc->section($LANG->getLL('mainheader_log'),$content,0,1);
			break;
			case 3:
				$times=array();
				$times[0]=time();
				for ($a=1;$a<=12;$a++)	{
					$times[$a]=mktime (0,0,0,date('m')+1-$a,1);
				}

				$content='';
				for ($a=0;$a<12;$a++)	{
					$list = array();
					$query='SELECT tx_loginusertrack_stat.*,fe_users.username,fe_users.name,fe_users.uid AS user_uid, count(*) AS counter, max(session_login) AS last_login FROM tx_loginusertrack_stat,fe_users WHERE fe_users.uid=tx_loginusertrack_stat.fe_user'.
							($userId>0 ? ' AND fe_users.uid='.intval($userId) : '').
							' AND fe_users.pid = '.intval($this->id).
							' AND session_login<'.intval($times[$a]).' AND session_login>='.intval($times[$a+1]).
							t3lib_BEfunc::deleteClause('fe_users').
							' GROUP BY fe_users.uid ORDER BY counter DESC LIMIT 200';
					$res = $GLOBALS['TYPO3_DB']->sql_query($query);
					while (($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
						$list[]='<tr bgcolor="'.$this->doc->bgColor4.'">
							<td nowrap>'.date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'].' '.$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],$row['last_login']).'</td>
							<td nowrap>'.t3lib_BEfunc::calcAge(time()-$row['last_login'],$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')).'</td>
							<td nowrap><a href="index.php?id='.$this->id.'&useruid='.$row['user_uid'].'">'.$row['username'].'</a></td>
							<td nowrap>'.$row['name'].'</td>
							<td>'.$row['counter'].'</td>
						</tr>
						';
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($res);

					$content.='
					<BR>
					'.$LANG->getLL('period').' <strong>'.date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$times[$a+1]).' - '.date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$times[$a]-1).'</strong><BR>
					<table border=0 cellpadding=1 cellspacing=1>
					<tr bgColor="'.$this->doc->bgColor5.'">
						<td><strong>'.$LANG->getLL('header_datetime').'</strong></td>
						<td><strong>'.$LANG->getLL('header_age').'</strong></td>
						<td><strong>'.$LANG->getLL('header_username').'</strong></td>
						<td><strong>'.$LANG->getLL('header_name').'</strong></td>
						<td><strong>'.$LANG->getLL('header_logins').'</strong></td>
					</tr>

					'.implode('',$list).'</table>';
				}

				if ($userId>0)	{
					$content='<a href="index.php?id='.$this->id.'"><strong>'.$LANG->getLL('modulecont_listAllUsers').'</strong></a><br>'.$content;
				}

				$this->content.=$this->doc->section($LANG->getLL('mainheader_monthly'),$content,0,1);
			break;
			case 4:
				$inst = t3lib_div::makeInstance('tx_loginusertrack_lastlogin');
				/* @var $inst tx_loginusertrack_lastlogin */
				$content = $inst->main($this->id,$this,'');
			break;
			case 5:
				$inst = t3lib_div::makeInstance('tx_loginusertrack_lastlogin');
				/* @var $inst tx_loginusertrack_lastlogin */
				$content = $inst->main($this->id,$this,'active');
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginusertrack/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginusertrack/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_loginusertrack_module1');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();

?>
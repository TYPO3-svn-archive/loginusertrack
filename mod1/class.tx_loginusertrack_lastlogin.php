<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Kasper Sk�rh�j (kasper@typo3.com)
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
 * Detection of old logins and deletion of such users
 *
 * @author	Kasper Sk�rh�j <kasper@typo3.com>
 */


require_once(PATH_t3lib.'class.t3lib_tcemain.php');

class tx_loginusertrack_lastlogin {
	var $daySpanBetweenCrAndLogin = 20;
	var $subject = 'Login User Notice!';
	var $headers = '';

	/**
	 * Main function for both "Active users" and "Inactive users"
	 *
	 * @param	integer		$id: The current page id of the module. This is where the users are sought for
	 * @param	object		$pObj: Reference to the parent object of the module ($this)
	 * @param	string		$mode: "active": Shows active users. Default: Shows in active.
	 * @return	void		Sets the content in $pObj->content
	 */
	function main($id,&$pObj,$mode)	{
		global $LANG;
		$content='';

		// Get days back.
		$version = class_exists('t3lib_utility_VersionNumber')
			? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
			: t3lib_div::int_from_ver(TYPO3_version);
		if ($version >= 4006000) {
			$daysBack = t3lib_utility_Math::forceIntegerInRange(t3lib_div::_GP('daysBack'),-1,1000);
		}
		else {
			$daysBack = t3lib_div::intInRange(t3lib_div::_GP('daysBack'),-1,1000);
		}

		$content.= '
			'.$LANG->getLL('lastlogin_main_enterTheDaysSince','1').':<br>
			<input type="text" name="daysBack" value="'.htmlspecialchars($daysBack?$daysBack:100).'">
			<input type="hidden" name="id" value="'.htmlspecialchars($id).'">
			<input type="submit" name="_" value="'.$LANG->getLL('lastlogin_main_setDaysSinceLast','1').'">
			<br>
		';

			// Total number of users:
		list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(*) AS t', 'fe_users',
						'pid='.intval($id) . t3lib_BEfunc::deleteClause('fe_users'));
		$content.='<strong>'.$LANG->getLL('lastlogin_main_totalNumberOfUsers','1').':</strong> '.$row['t'];

		$pObj->content.=$pObj->doc->section($LANG->getLL('Last_logins'),$content,0,1);

			//
		if ($daysBack)	{
			switch($mode)	{
				case 'active':
					$content = $this->showActive($id,$daysBack);
					$pObj->content.=$pObj->doc->section(sprintf($LANG->getLL('lastlogin_main_activeUsersLoggedIn','1'),$daysBack),$content,0,1);
				break;
				default:
					$content = $this->removeOld($id,$daysBack);
					$pObj->content.=$pObj->doc->section(sprintf($LANG->getLL('lastlogin_removeold_usersWithLastLogin','1'),$daysBack),$content,0,1);
				break;
			}
		}
	}

	/**
	 * Code for the "inactive" function, enabling us to remove users and send them warning emails.
	 *
	 * @param	integer		$id: Page id, see main
	 * @param	integer		$daysBack: The number of days to use as limit. Coming from input-field.
	 * @return	string		HTML content
	 */
	function removeOld($id,$daysBack)	{
		global $LANG;
		$content='';

		$tcemain_cmd=array();
		$testUsername = trim(t3lib_div::_GP('test_username'));
		$emailMsg = trim(t3lib_div::_GP('email_msg'));
		$action = t3lib_div::_GP('sendWarningEmail') ? (t3lib_div::_GP('_DELETE') ? 'delete' : 'email') : '';	// Set to blank, "delete" or "email"
		$this->subject = $LANG->getLL('lastlogin_removeold_subject');
		$this->headers = 'From: '.t3lib_div::_GP('header_name').' <'.t3lib_div::_GP('header_email').'>';


			// old users:
		$query = 'SELECT uid,username,email,name,lastlogin,password FROM fe_users WHERE pid='.intval($id).
			($testUsername ? ' AND username="'.addslashes($testUsername).'"' : ' AND lastlogin < '.(time()-$daysBack*24*3600)).
			t3lib_BEfunc::deleteClause('fe_users').
			' ORDER BY name';
		$res = $GLOBALS['TYPO3_DB']->sql_query($query);

		$tRows[]='<tr bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor5.'">
			<td nowrap><strong>'.$LANG->getLL('lastlogin_removeold_username','1').'</strong></td>
			<td nowrap><strong>'.$LANG->getLL('lastlogin_removeold_name','1').'</strong></td>
			<td nowrap><strong>'.$LANG->getLL('lastlogin_removeold_email','1').'</strong></td>
			<td nowrap><strong>'.$LANG->getLL('lastlogin_removeold_lastLogin','1').'</strong></td>
		</tr>';

		$emailsAcc=array();
		while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)))	{
			$tRows[]='<tr bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor4.'">
				<td nowrap>'.htmlspecialchars($row['username']).'</td>
				<td nowrap>'.htmlspecialchars($row['name']).'</td>
				<td nowrap>'.htmlspecialchars($row['email']).'</td>
				<td nowrap>'.htmlspecialchars(date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$row['lastlogin'])).'</td>
			</tr>';

			if (trim($row['email'])) {
				$emailsAcc[] = preg_replace('/--/','&#45;&#45;',trim($row['email']));
			}

			if ($action == 'email')	{
				$this->sendWarningEmail($row,$emailMsg);
			} elseif ($action == 'delete')	{
				$tcemain_cmd['fe_users'][$row['uid']]['delete']=1;
				if ($emailMsg) {
					$this->sendWarningEmail($row,$emailMsg);
				}
			}
		}
		$num_rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

		if ($action == 'delete') {
			$content .= '<span style="color:red;">'.sprintf($LANG->getLL('lastlogin_removeold_deletedSUsers','1'),$num_rows).'</span><br>';
			if ($emailMsg)	{
				$content.='<span style="color:red;">'.sprintf($LANG->getLL('lastlogin_removeold_sentDeletedEmails','1'),$num_rows).'</span><br>';
			}

			$tce = t3lib_div::makeInstance("t3lib_TCEmain");
			$tce->start(array(),$tcemain_cmd);
			$tce->process_cmdmap();
		} else {
			if ($action=='email')	{
				$content.='<span style="color:red;">'.sprintf($LANG->getLL('lastlogin_removeold_sentSWarningEmails','1'),$num_rows).'</span><br>';
			}

			$content.='
			<strong>'.$LANG->getLL('lastlogin_removeold_numberOfInactiveUsers','1').'</strong> '.$num_rows.'<br>
			('.sprintf($LANG->getLL('lastlogin_removeold_didNotLoginDuring','1'),'<strong>'.$daysBack.'</strong>').')<br>
			<table border="0" cellpadding="1" cellspacing="2">'.implode('
			',$tRows).'</table>';

			$msg = $emailMsg?$emailMsg:sprintf($LANG->getLL('lastlogin_removeold_hiNameYouAre','1'),t3lib_div::getIndpEnv('TYPO3_SITE_URL'),$daysBack,t3lib_div::getIndpEnv('TYPO3_SITE_URL'));

			$delMsg = sprintf($LANG->getLL('lastlogin_removeold_hiNameYouWere','1'),t3lib_div::getIndpEnv('TYPO3_SITE_URL'));

			$content.='
<br>
<strong>'.$LANG->getLL('lastlogin_removeold_sendingAWarningEmail','1').'</strong><br>
<br>
<textarea name="email_msg" rows="20" '.$GLOBALS['TBE_TEMPLATE']->formWidthText(48,'').'>'.t3lib_div::formatForTextarea($msg).'</textarea><br>
<strong>'.$LANG->getLL('lastlogin_removeold_senderName','1').'</strong><br>
<input type="text" name="header_name" value="'.htmlspecialchars(t3lib_div::_GP('header_name')?t3lib_div::_GP('header_name'):$GLOBALS['BE_USER']->user['realName']).'"><br>
<strong>'.$LANG->getLL('lastlogin_removeold_senderEmail','1').'</strong><br>
<input type="text" name="header_email" value="'.htmlspecialchars(t3lib_div::_GP('header_email')?t3lib_div::_GP('header_email'):$GLOBALS['BE_USER']->user['email']).'"><br>
'.$LANG->getLL('lastlogin_removeold_sendATestTo','1').' <input type="text" name="test_username" value="'.htmlspecialchars(t3lib_div::_GP('test_username')).'"><br>
<input type="submit" name="sendWarningEmail" value="'.$LANG->getLL('lastlogin_removeold_sendWarningEmail','1').'"> - <input type="submit" name="_" value="'.$LANG->getLL('lastlogin_removeold_sendUpdate','1').'"><br>

<!--

'.implode(', ',$emailsAcc).'

-->

<div style="background-color: red; color:white; padding-left: 5px;">
<input type="checkbox" name="_DELETE" value="" onclick="
	document.forms[0].sendWarningEmail.value=this.checked?\''.$LANG->getLL('lastlogin_removeold_deleteUsers','1').'\':\''.$LANG->getLL('lastlogin_removeold_sendWarningEmail','1').'\';
	if (this.checked)	{
		this.value = document.forms[0].email_msg.value;
		document.forms[0].email_msg.value=unescape(\''.rawurlencode(trim($delMsg)).'\');
	} else {
		document.forms[0].email_msg.value = this.value;
	}
	"> <strong>'.$LANG->getLL('lastlogin_removeold_delete','1').'</strong><br>
'.$LANG->getLL('lastlogin_showactive_ifYouCheckThis').'
</div>


		';
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $content;
	}

	/**
	 * Shows active users in a table
	 *
	 * @param	integer		$id: Page id, see main function
	 * @param	integer		$daysBack: Number of days to use as limit for active users. See description in the output in the module.
	 * @return	string		HTML output
	 */
	function showActive($id,$daysBack)	{
		global $LANG;

			// Total number of 'active' users were created more than XX days ago and having login within the last XX days
		$orderBy = t3lib_div::_GP('orderby');
		$query = 'SELECT uid,username,email,name,lastlogin FROM fe_users WHERE pid='.intval($id).
			' AND lastlogin > '.(time() - $daysBack*24*3600).
			' AND crdate < '.(time() - $daysBack*24*3600).
			' AND lastlogin-crdate > '.(24*3600*$this->daySpanBetweenCrAndLogin).
			t3lib_BEfunc::deleteClause('fe_users').
			' ORDER BY '.(t3lib_div::inList('username,name,email,lastlogin',$orderBy)?$orderBy.($orderBy=='lastlogin'?' DESC':''):'name');
		$res = $GLOBALS['TYPO3_DB']->sql_query($query);
		$tRows = array();

		$tRows[]='<tr bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor5.'">
			<td nowrap><strong><a href="index.php?id='.$id.'&daysBack='.$daysBack.'&orderby=username">'.$LANG->getLL('lastlogin_showactive_username','1').'</a></strong></td>
			<td nowrap><strong><a href="index.php?id='.$id.'&daysBack='.$daysBack.'&orderby=name">'.$LANG->getLL('lastlogin_showactive_name','1').'</a></strong></td>
			<td nowrap><strong><a href="index.php?id='.$id.'&daysBack='.$daysBack.'&orderby=email">'.$LANG->getLL('lastlogin_showactive_email','1').'</a></strong></td>
			<td nowrap><strong><a href="index.php?id='.$id.'&daysBack='.$daysBack.'&orderby=lastlogin">'.$LANG->getLL('lastlogin_showactive_lastLogin','1').'</a></strong></td>
		</tr>';

		$emailsAcc = array();
		while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
			if (trim($row['email'])) {
				$emailsAcc[] = preg_replace('/--/','&#45;&#45;',trim($row['email']));
			}

			$tRows[] = '<tr bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor4.'">
				<td nowrap>'.htmlspecialchars($row['username']).'</td>
				<td nowrap>'.htmlspecialchars($row['name']).'</td>
				<td nowrap>'.htmlspecialchars($row['email']).'</td>
				<td nowrap>'.htmlspecialchars(date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$row['lastlogin'])).'</td>
			</tr>';
		}
		$content .= '
		<strong>'.$LANG->getLL('lastlogin_showactive_numberOfActiveUsers','1').'</strong> '.$GLOBALS['TYPO3_DB']->sql_num_rows($res).'<br>
'.sprintf($LANG->getLL('lastlogin_sendwarnin_usersAreShownHere'), $daysBack, $daysBack, $this->daySpanBetweenCrAndLogin).'<br>

<!--

'.implode(', ',$emailsAcc).'

-->


		<table border="0" cellpadding="1" cellspacing="2">'.implode('
		',$tRows).'</table>';

		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $content;
	}

	/**
	 * Sends a warning email to the fe_users record being input /
	 *
	 * @param	array		$row: A fe_users row with fields like uid, username, email, name, password
	 * @param	string		$emailMsg: The message to send. Can contain markers, ###USERNAME###, ###NAME###, ###PASSWORD###
	 * @return	void
	 */
	function sendWarningEmail($row,$emailMsg)	{
		$email = trim($row['email']);
		if ($email)	{
			$subject = $this->subject;
			$markers = array('###USERNAME###', '###NAME###', '###PASSWORD###');
			$subst = array($row['username'], $row['name'], $row['password']);

			$message = str_replace($markers, $subst, $emailMsg);

#debug(array($email,$subject,$message));
#$email='kasper@typo3.com';
			t3lib_div::plainMailEncoded($email,$subject,$message,$this->headers);
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginusertrack/mod1/class.tx_loginusertrack_lastlogin.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginusertrack/mod1/class.tx_loginusertrack_lastlogin.php']);
}
?>

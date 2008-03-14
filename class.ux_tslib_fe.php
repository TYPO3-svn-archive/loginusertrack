<?
class ux_tslib_fe extends tslib_fe {
	function getConfigArray()	{
		parent::getConfigArray();
		
		if ($this->config["config"]["tx_loginusertrack_enable"])	{
			if (is_array($this->fe_user->user))	{
				if (t3lib_div::GPvar("logintype")=="login")	{
					$this->ext_addNewEntry();
				} else {
					$this->ext_updateEntry();
				}		
			}
		}
	}

	
	function ext_addNewEntry()	{
		$query="INSERT INTO tx_loginusertrack_stat (fe_user,session_login,last_page_hit,session_hit_counter,page_id) 
				VALUES (".intval($this->fe_user->user["uid"]).",".time().",".time().",1,".intval($this->id).")";
				
		$res=mysql(TYPO3_db,$query);
		echo mysql_error();
	}
	function ext_updateEntry()	{
		$query = "SELECT * FROM tx_loginusertrack_stat WHERE fe_user=".intval($this->fe_user->user["uid"])." ORDER BY session_login DESC LIMIT 1";
		$res = mysql(TYPO3_db,$query);
		$row=mysql_fetch_assoc($res);
		if (is_array($row))	{
			$query = "UPDATE tx_loginusertrack_stat SET last_page_hit=".time().", session_hit_counter=".intval($row["session_hit_counter"]+1)." WHERE uid=".intval($row["uid"]);
			$res = mysql(TYPO3_db,$query);
		}
	}
}
?>
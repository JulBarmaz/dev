<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

Class Polls extends BaseObject{
	public function __construct() {
	}
	public function getPollByItem($item_id=0) {
		$sql_txt="SELECT p.*,pi.pi_poll_id FROM #__polls AS p, #__poll_items AS pi";
		$sql_txt.=" WHERE pi.pi_poll_id=p.p_id"
		 				." AND pi.pi_id=".$item_id
		 				." AND p.p_enabled=1"
		 				." AND p.p_deleted=0"
		 				." AND p.p_startdate<=NOW()"
		 				." AND p.p_enddate>=NOW()";
		Database::getInstance()->setQuery($sql_txt);
		Database::getInstance()->loadObject($result);
		return $result;
	}
	public function getItems($poll_id=0) {
		$sql_txt="SELECT pi.* FROM #__poll_items AS pi, #__polls AS p";
		$sql_txt.=" WHERE pi.pi_poll_id=p.p_id"
		 				." AND pi.pi_poll_id=".$poll_id
		 				." AND p.p_enabled=1"
		 				." AND p.p_deleted=0"
		 				." AND p.p_startdate<=NOW()"
		 				." AND p.p_enddate>=NOW()"
		 				." AND pi.pi_deleted=0";
		 				$sql_txt.=" ORDER BY pi.pi_ordering";
		Database::getInstance()->setQuery($sql_txt);
		$result=Database::getInstance()->loadObjectList();
		return $result;
	}

	public function getPoll($poll_id=0) {
		$sql_txt="SELECT p.* FROM #__polls AS p";
		$sql_txt.=" WHERE p.p_id=".$poll_id
		 				." AND p.p_enabled=1"
		 				." AND p.p_deleted=0"
		 				." AND p.p_startdate<=NOW()"
		 				." AND p.p_enddate>=NOW()";
		Database::getInstance()->setQuery($sql_txt);
		Database::getInstance()->loadObject($result);
		return $result;
	}
	public function allreadyVoted($poll){
		if (isset($_COOKIE["poller".$poll->p_id])) return true;
		if (isset($_SESSION["poller".$poll->p_id])) {
			if ((time()-$poll->p_lag) < $_SESSION["poller".$poll->p_id]) return true;
		}
		return false;
	}
	public function setCookieLag($poll){		
		Session::getInstance()->setcookie('poller'.$poll->p_id, '', time() - $poll->p_lag,"/");
		$_SESSION["poller".$poll->p_id]=time();
	}
	public function votePoll($psid){
		if($psid) {
			$sql_txt="UPDATE #__poll_items SET pi_hits=pi_hits+1 WHERE pi_id=".$psid;
			Database::getInstance()->setQuery($sql_txt);
			if (Database::getInstance()->query()) {
				return true; 
			} else { 
				return false;
			}
		} else return false;
	}
}



?>
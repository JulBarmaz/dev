<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsModelpolls extends SpravModel {

	public function getPollsCount() {
		$query = "SELECT COUNT(*) FROM #__polls ";
		$query .= "WHERE p_deleted=0"; 
		$this->_db->setQuery($query);
		return intval($this->_db->loadResult());
	}
	public function getPolls() {
		$query = "SELECT *, ";
		$query .= " (SELECT SUM(pi_hits) FROM #__poll_items WHERE pi_poll_id=p_id GROUP BY pi_poll_id) as p_total_voted";
		$query .= " FROM #__polls";
		$query .= " WHERE p_deleted=0 "; 
		$query .= "";
		$query .= $this->getAppendix();
		$this->_db->setQuery($query);
		$polls = $this->_db->loadObjectList();
		return $polls;
	}
	public function getPoll($psid) {
		$query = "SELECT *, ";
		$query .= " (SELECT SUM(pi_hits) FROM #__poll_items WHERE pi_poll_id=p_id GROUP BY pi_poll_id) as p_total_voted";
		$query .= " FROM #__polls";
		$query .= " WHERE p_deleted=0 ";
		$query .= " AND p_id=".$psid;
		$this->_db->setQuery($query);
		$poll = false;
		$this->_db->loadObject($poll);
		return $poll;
	}
	public function getItems($polls) {
		if (count($polls)>0) {
			foreach($polls as $poll) {	$ids[]=$poll->p_id;	}
			$instr="(".implode(",",$ids).")";
			$query = "SELECT * FROM #__poll_items";
			$query .= " WHERE pi_deleted=0 AND pi_poll_id IN ".$instr; 
			$query .= " ORDER BY pi_ordering";
			$this->_db->setQuery($query);
			$items = $this->_db->loadObjectList();
			return $items;
		} else return false;
	}
}

?>
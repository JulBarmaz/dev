<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class mailModelletter extends Model {

	public function getLetterCount($inbox=true,$unreadOnly=true) {
		if ($inbox) {
			$side = "r"; $dest = "reciever";
		} else {
			$side = "s"; $dest = "sender";
		}
		
		$uid = User::getInstance()->getId();
		$query = "SELECT COUNT(*) FROM #__mail WHERE (l_deleted_".$side."=0) AND (l_".$dest."_id=".$uid.")";
		if ($unreadOnly)	$query .= " AND (l_read=0)";

		$this->_db->setQuery($query);
		return intval($this->_db->loadResult());
	}

	public function getMail($inbox=true,$unreadOnly=true) {
		if ($inbox) {
			$side = "r"; $dest = "reciever";
		} else {
			$side = "s"; $dest = "sender";
		}

		$uid = User::getInstance()->getId();
		$query = "SELECT * FROM #__mail WHERE (l_deleted_".$side."=0) AND (l_".$dest."_id=".$uid.")";
		if ($unreadOnly) $query .= " AND (l_read=0)"; 
		$query .= " ORDER BY l_date";
		$query .= $this->getAppendix();

		$this->_db->setQuery($query);
		$letters = $this->_db->loadObjectList();
		foreach ($letters as $letter) {
			$letter->sender = User::getNicknameFor($letter->l_sender_id);
			$letter->reciever = User::getNicknameFor($letter->l_reciever_id);
		}

		return $letters;
	}

	public function getLetter($lid) {
		$letter=false;
		$uid = User::getInstance()->getId();
		$query = "SELECT * FROM #__mail WHERE (l_id=".intval($lid).") AND ((l_reciever_id=".$uid.") OR (l_sender_id=".$uid."))";
		$this->_db->setQuery($query);
		$this->_db->loadObject($letter);
		return $letter;
	}

	public function setRead($lid) {
		$query = "UPDATE #__mail SET l_read=1 WHERE l_id=".intval($lid);
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	public function writeLetter($recieverId,$theme,$text) {
		$uid = User::getInstance()->getId();
		$query = "INSERT INTO #__mail VALUES(0,".$uid.",".$recieverId.",'".$theme."','".$text."',NOW(),0,0,0)";
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	public function deleteLetter($lid,$side) {
		$query = "UPDATE #__mail SET l_deleted_".$side."=1 WHERE l_id=".intval($lid);
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	public function getCountUnreadLetter($userid) {
		$side = "r";
		$role = "reciever";		
	  $query = "SELECT count(*) FROM #__mail WHERE (l_deleted_".$side."=0) AND (l_".$role."_id=".$userid.")";
		$query .= " AND (l_read=0)";
		$this->_db->setQuery($query);
		return $this->_db->LoadResult();
	}
}
?>
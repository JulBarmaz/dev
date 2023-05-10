<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Exporter extends BaseObject {

	//---------- Singleton implementation ------------
	private static $_instance = null;
	private $replace_prefix = true;
	
	public static function createInstance($replace_prefix=true) {
		if (self::$_instance == null) {
			self::$_instance = new self($replace_prefix);
		}
	}

	public static function getInstance($replace_prefix=true) {
		self::createInstance($replace_prefix);
		return self::$_instance;
	}
	//------------------------------------------------

	private $_db = null;

	private function __construct($replace_prefix) {
		$this->replace_prefix=$replace_prefix;
		$this->_db = Database::getInstance();
	}

	public function getTablesList($like='') {
		$sql="SHOW FULL TABLES";
		if($like) $sql="SHOW FULL TABLES LIKE '%".$like."%'";
		$this->_db->setQuery($sql);
		$tbd = $this->_db->loadResultArray();
		return $tbd;
	}

	public function getCreateTable($table,$delimiter='') {
		if (!$delimiter) $delimiter=$this->_db->getDelimiter(); 
		$this->_db->setQuery('SHOW CREATE TABLE '.$table);
		$tbd = $this->_db->loadAssocList();
		$tbq = $tbd[0]['Create Table'].$delimiter."\n";
		if ($this->replace_prefix) return str_replace('CREATE TABLE `'.$this->_db->getPrefix(),'CREATE TABLE `#__', $tbq);
		else return $tbq;
	}

	public function getInserts($table,$delimiter='') {
		if (!$delimiter) $delimiter=$this->_db->getDelimiter();
		$this->_db->setQuery('SELECT * FROM `'.$table.'`');
		if ($this->replace_prefix) $table = str_replace($this->_db->getPrefix(), '#__', $table);
		$data = $this->_db->loadAssocList();
		$query = "";
		foreach ($data as $row) {
			$fields = ""; $vals = "";
			foreach ($row as $f=>$v) {
				if (is_null($v)) $v="NULL";
				else $v = "'".addslashes($v)."'";
				if ($fields != "") $fields .= ",";
				$fields .= "`".$f."`";
				if ($vals != "") $vals .= ",";
				$vals .= $v;
			}
			$query .= "INSERT INTO `".$table."`(".$fields.") VALUES(".$vals.")".$delimiter."\n";
		}
		return $query;
	}

}

?>
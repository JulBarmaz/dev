<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Database extends BaseObject {
	//---------- Singleton implementation ------------
	private static $_instance = null;
	public static function createInstance($critFail=true) {
		if (self::$_instance == null) {
			self::$_instance = new self($critFail);
		}
	}
	public static function getInstance($critFail=true) {
		self::createInstance($critFail);
		return self::$_instance;
	}
	//------------------------------------------------
	private $_link		= '';
	private $_connected		= false;
	private $_connect_timeout	= 20;
	private $_connect_timeout_default = 20;
	private $_dbHost		= '';
	private $_dbPort		= '';
	private $_dbName		= '';
	private $_dbUser		= '';
	private $_dbPrefix		= '';
	private $_sql			= '';
	private $_cursor		= null;
	private $_limit			= 0;
	private $_offset		= 0;
	private $_errorNum      = '';
	private $_errorMsg      = '';
	private $_delimiter ="###qb_delimiter###";
	private $in_trasaction=false;

	public function setDebugMode(){
		$this->_debugger=siteConfig::$debugMode;
	}
	private function __construct($critFail) {
		$this->initObj();
		$this->reconnect(DatabaseConfig::$dbHost, DatabaseConfig::$dbPort, DatabaseConfig::$dbName, DatabaseConfig::$dbUser, DatabaseConfig::$dbPassword, DatabaseConfig::$dbPrefix, $critFail);
		$this->milestone("DB constructed", __FUNCTION__);
	}
	private function replacePrefix($sql,$prefix="#__") {
		$sql = trim($sql);
		$escaped = false;
		$quoteChar = '';
		$n = strlen($sql);
		$startPos = 0;
		$literal = '';
		while($startPos < $n) {
			$ip = strpos($sql,$prefix,$startPos);
			if ($ip === false) break;
			$j = strpos($sql,"'",$startPos);
			$k = strpos($sql,'"',$startPos);
			if (($k !== false) && (($k < $j) || $j == false)) {
				$quoteChar = '"';
				$j = $k;
			}	else {
				$quoteChar = "'";
			}
			if ($j === false) $j = $n;
			$literal .= str_replace($prefix,$this->_dbPrefix,substr($sql,$startPos,$j - $startPos));
			$startPos = $j;
			$j = $startPos + 1;
			if ($j >= $n) break;
			while (true) {
				$k = strpos($sql,$quoteChar,$j);
				$escaped = false;
				if ($k === false) break;
				$l = $k - 1;
				while ($l >= 0 && $sql[$l] == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j = $k + 1;
					continue;
				}
				break;
			}
			if ($k == false) { // error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr($sql,$startPos,$k - $startPos + 1);
			$startPos = $k + 1;
		}
		if ($startPos < $n) $literal .= substr($sql,$startPos,$n - $startPos);
		return $literal;
	}

	public function isConnected() {
		return $this->_connected;
	}
	public function getPrefix() {
		return $this->_dbPrefix;
	}
	public function getDBName() {
		return $this->_dbName;
	}
	public function getDelimiter() {
		return $this->_delimiter;
	}
	public function getErrorNum() {
		return $this->_errorNum;
	}
	public function getErrorMsg() {
		return $this->_errorMsg;
	}
	
	public function reconnect($dbhost, $dbport, $dbname, $dbuser, $dbpass, $dbprefix, $critFail=true) {
		$this->_connected = false;
		if ($this->_link) mysqli_close($this->_link);
		$this->_dbHost = $dbhost;
		$this->_dbPort = $dbport;
		$this->_dbName = $dbname;
		$this->_dbUser = $dbuser;
		$this->_dbPrefix = $dbprefix;
		if (!($this->_link = @mysqli_connect($this->_dbHost, $this->_dbUser, $dbpass, $this->_dbName, $this->_dbPort))) {
			if ($critFail) $this->criticalFail("DB connect failed");
			else {
				$this->_connected = false;
				return false;
			}
		}
		if (!mysqli_select_db($this->_link, $this->_dbName)) {
			if ($critFail) $this->criticalFail("DB selection failed");
			else {
				$this->_connected = false;
				return false;
			}
		}
		// mysqli_options ( $this->_link ,MYSQLI_OPT_CONNECT_TIMEOUT, $this->_connect_timeout ); 
		$this->milestone("DB selected `".$dbname."`", __FUNCTION__);
		@mysqli_query($this->_link, "SET NAMES 'UTF8'");
		$this->_connected = true;
	}
	
	protected function criticalFail($text) {
		@ob_end_clean();
		ini_set('display_errors', 'Off');
		ini_set('log_errors', 'On');
		Util::sendHeader(503);
		echo "Service unavailable. Refer logs.";
		$text.= ": ".mysqli_connect_error();
		throw new Exception($text);
//		Util::halt($text);
	}
	// запускаем профайлинг
	public function startProfiling($count_query=15)
	{
		$this->_sql ="set profiling=1";
		$this->query();
		// ограничим число запросов 
		if($count_query>99) $count_query=99;
		$this->_sql ="set profiling_history_size=".(int)$count_query;
		$this->query();
		
	}
	public function getProfilingMessages()
	{
		$this->_sql ="show profiles";
		return $this->loadObjectList();
	}
	public function getProfilingMessagesByID($id)
	{
		$this->_sql ="show profile for query ".(int)$id;
		return $this->loadObjectList();
	}
	public function stopProfiling()
	{
		$this->_sql ="set profiling=0";
		$this->query();
	}
	
	
	//mysql> show profiles;
	
	public function getNumRows( $cursor=null ) {
		return mysqli_num_rows( $cursor ? $cursor : $this->_cursor );
	}
	
	public function getAffectedRows( $cursor=null ) {
		return mysqli_affected_rows($this->_link);
	}
	
	public function setQuery($sql, $offset = 0, $limit = 0, $prefix='#__') {
		$this->_sql = $this->replacePrefix($sql, $prefix);
		$this->_limit = intval($limit);
		$this->_offset = intval($offset);
	}
	public function getQuery($format=false) {
		if ($format) return '<pre>'.htmlspecialchars($this->_sql).'</pre>';
		else return $this->_sql;
	}
	public function query() {
		if ($this->_limit > 0 && $this->_offset == 0) {
			$this->_sql .= "\n LIMIT ".$this->_limit;
		}	else if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql .= "\n LIMIT ".$this->_offset.", ".$this->_limit;
		}
		if ($this->_debugger) $_startTime = microtime(1);
		$this->_cursor = @mysqli_query($this->_link, $this->_sql);
		if ($this->_debugger){
			$tr='';	if($this->in_trasaction) $tr='TR-';
			Debugger::getInstance()->sqlQuery((microtime(1) - $_startTime)." :".$tr." ".$this->_sql);
		}
		if (!$this->_cursor) {
			$this->error(Text::_("DB error")." => query ".$this->_sql." -:- ".mysqli_error($this->_link));
			$this->_errorNum .= mysqli_errno( $this->_link ) . " ";
			$this->_errorMsg .= mysqli_error( $this->_link )." SQL=".$this->_sql."<br />";
			return false;
		}
		return $this->_cursor;
	}
	public function loadResult() {
		if (!($cursor = $this->query())) return null;
		if ($row = @mysqli_fetch_row($cursor)) {
			$ret = $row[0];
		} else $ret = null;
		@mysqli_free_result($cursor);
		return $ret;
	}
	public function loadAssocList($key='') {
		if (!($cursor = $this->query())) return null;
		$array = array();
		while ($row = @mysqli_fetch_assoc($cursor)) {
			if ($key) $array[$row[$key]] = $row;
			else $array []= $row;
		}
		@mysqli_free_result($cursor);
		return $array;
	}
	/* Load an array of single field results into an array */
	function loadResultArray($numinarray = 0) {
		if (!($cursor = $this->query())) return null;
		$array = array();
		while ($row = mysqli_fetch_row( $cursor )) {
			$array[] = $row[$numinarray];
		}
		mysqli_free_result( $cursor );
		return $array;
	}
	public function loadObject(&$object) {
		if ($object != null) {
			if (!($cursor = $this->query())) return false;
			if ($array = mysqli_fetch_assoc($cursor)) {
				@mysqli_free_result($cursor);
				foreach ($array as $key=>$value) {
					$object->{$key} = $value;
				}
				return true;
			} else return false;
		} else {
			if ($cursor = $this->query()) {
				if ($object = @mysqli_fetch_object($cursor)) {
					@mysqli_free_result($cursor);
					return true;
				} else {
					$object = null;
					return false;
				}
			} else return false;
		}
	}
	public function loadObjectList($key='') {
		if (!($cursor = $this->query())) return null;
		$array = array();
		while ($row = @mysqli_fetch_object($cursor)) {
			if ($key) $array[$row->{$key}] = $row;
			else $array[] = $row;
		}
		@mysqli_free_result($cursor);
		return $array;
	}

	public function insertid() {
		return mysqli_insert_id( $this->_link );
	}
	
	public function getListFieldfromTable($table, $full=false) {
		if ($full) $this->setQuery("SHOW FULL COLUMNS FROM #__".$table);
		else $this->setQuery("DESCRIBE #__".$table);
		return $this->LoadObjectList('Field');
	}

	public function checkTableExists($table){
		$table=str_replace("#__", $this->_dbPrefix, $table);
		$sql = "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_schema='".$this->_dbName."' AND table_name ='".$table."'";
		$this->setQuery($sql);
		return Util::toBool($this->loadResult());
	}

	
	public function doubleBaks($text){
		return str_replace("$$", urlencode("$$"), $text);
	}
	/* begin transaction */
	public function startTransaction($p_transaction_safe = false){
		$this->in_trasaction=true;
		if ($this->_debugger) $_startTime = microtime(1);
		mysqli_autocommit($this->_link, FALSE);
		mysqli_begin_transaction($this->_link);//, MYSQLI_TRANS_START_READ_ONLY);
		if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : START TRANSACTION");
	}
	/* successful end transaction */
	public function endTransaction(){
		$this->in_trasaction=false;
		if ($this->_debugger) $_startTime = microtime(1);
		$res_commit=mysqli_commit($this->_link);
		if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : COMMIT TRANSACTION");
	}
	/* rollback transaction */
	public function rollbackTransaction(){
		$this->in_trasaction=false;
		if ($this->_debugger) $_startTime = microtime(1);
		mysqli_rollback($this->_link);
		if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : ROLLBACK TRANSACTION");
	}
	
	
	public function query_batch( $abort_on_error=true, $p_transaction_safe = false,$delimiter='') {
		
		if (!$delimiter) $delimiter=$this->_delimiter;
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe&&PHP_VERSION<'5.5') {
			$this->_sql = 'START TRANSACTION'.$delimiter. $this->_sql .$delimiter.'COMMIT'.$delimiter;
		}
		
		$query_split = preg_split ("/(".$delimiter.")/", $this->_sql,-1,PREG_SPLIT_NO_EMPTY);
		$error = 0;
		if ($p_transaction_safe&&PHP_VERSION>'5.5') {
			//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			if ($this->_debugger) $_startTime = microtime(1);
			mysqli_autocommit($this->_link, FALSE);
			mysqli_begin_transaction($this->_link);//, MYSQLI_TRANS_START_READ_ONLY);
			if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : START TRANSACTION");
		}
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			
			if ($command_line != '') {
				if ($this->_debugger) $_startTime = microtime(1);
				$this->_cursor = mysqli_query($this->_link, $command_line );
		//		var_dump($this->_cursor);
				if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : ".$command_line);
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysqli_errno( $this->_link ) . " ";
					$this->_errorMsg .= 'TR - '.mysqli_error( $this->_link )." SQL=".$command_line."<br />";
					if ($abort_on_error) return $this->_cursor;
				}
				
			}
		}
		if ($p_transaction_safe&&PHP_VERSION>'5.5'&&$error==0) {
			$res_commit=mysqli_commit($this->_link);
			if($res_commit) {
				if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : SUCCESSFUL FINISHED TRANSACTION");
				return true;
			}else{
				if ($this->_debugger) Debugger::getInstance()->sqlQuery('TR - '.(microtime(1) - $_startTime)." : FAIL TRANSACTION");
				mysqli_rollback($this->_link);
				return false;
			}
		}
		
		return $error ? false : true;
	}
	
	public function getLastError() {
		return mysqli_error($this->_link);
	}
	public function getLastConnectError() {
		return mysqli_connect_error();
	}	
	
	public function getNextAutoinc($table=""){
		$this->setQuery("SELECT AUTO_INCREMENT FROM information_schema.TABLES  WHERE TABLE_SCHEMA = '".$this->_dbName."' AND TABLE_NAME ='".$table."'");
		return $this->loadResult();
	}
	public function setNextAutoinc($table="",$inc=0){
		$this->setQuery("ALTER TABLE ".$table." AUTO_INCREMENT = ".(int)$inc);
		return $this->loadResult();
	}
	public function getEscaped($text) {
		return mysqli_real_escape_string($this->_link, $text);
	}
}
?>
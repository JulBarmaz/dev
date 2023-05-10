<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

define('DE_MESSAGE','message');
define('DE_WARNING','warning');
define('DE_ERROR','error');
define('DE_MILESTONE','milestone');

class Debugger {
	//---------- Singleton implementation ------------
	private static $_instance = null;

	public static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
			self::$_instance->_enabled = siteConfig::$debugMode;
		}
	}
	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	//------------------------------------------------
	private $_enabled 		= false;
	private $_events 		= array();
	private $_sqlQueries	= array();
	private $_translations	= array();
	private $_startTime		= 0;
	private $_lastTime		= 0;
	private $_lastMemory	= 0;
	private $_threshold		= 5; // в процентах
	private $_mem_threshold = 250000 ; // в байтах
	private function __construct() {
		$this->_startTime = microtime(1);
		$this->_lastTime = microtime(1);
	}
	private function passEvent($type,$text) {
		if($this->_enabled){
			$event = new stdClass();
			if($type==DE_MILESTONE){
				$newtime=microtime(1);
				$event->time = $newtime-$this->_lastTime;
				$event->ttime = $newtime-$this->_startTime;
				$this->_lastTime=$newtime;
				$newmem=$this->getMemory(); 
				$event->memory = $newmem-$this->_lastMemory;
				$this->_lastMemory=$newmem;
			} else {
				$event->time = 0; $event->memory = 0;
			}
			$event->type = $type;
			$event->text = $text;
			$this->_events []= $event;
		}
	}
	public function getTime() {
		$execTime = microtime(1) - $this->_startTime;
		return $execTime;
	}
	public function message($text) {
		$this->passEvent(DE_MESSAGE,$text);
	}
	public function warning($text) {
		$this->passEvent(DE_WARNING,$text);
	}
	public function error($text,$isFatal=false, $codeerror="503") {
		$this->passEvent(DE_ERROR,$text);
		if ($isFatal) Util::fatalError($text, $codeerror); // This is main debugger fatal.
	}
	public function milestone($text) {
		$this->passEvent(DE_MILESTONE,$text);
	}
	public function translation($txt) {
		if($this->_enabled) $this->_translations []= $txt;
	}
	public function sqlQuery($query,$prefix="") {
		if($this->_enabled)	$this->_sqlQueries[]= $prefix.htmlspecialchars($query);
	}
	public function dumpSystemInfo() {
		$this->message("Memory limit = ".ini_get("memory_limit"));
		$this->message("Max execution time = ".ini_get("max_execution_time"));
	}
	public function dump($divId='dbg',$divClass='debug',$extDiv=true) {
		$dbg_div = "";
		if ($this->_enabled) {
			if ($extDiv) {
				$dbg_div .= "<div id=\"debugger\"><div class=\"".$divClass."\" id=\"".$divId."\">\n";
			}

			// Debug log
			$dbg_div .= "<div class=\"".$divClass."\" id=\"".$divId."_log\"><br /><b>Debug messages:</b><br /><br />\n";
			$dbg_div .= "<table>";

			$execTime = $this->getTime();
			$dbg_div .= "<tr><td>-</td><td>Common execution time:&nbsp;".$execTime." sec.</td></tr>";
			$dbg_div .= "<tr><td>-</td><td>Max execution time = ".ini_get("max_execution_time")." sec.</td></tr>";
			$dbg_div .= "<tr><td>-</td><td>Time threshold:&nbsp;".$this->_threshold." %.</td></tr>";
			$dbg_div .= "<tr><td>-</td><td>Memory threshold: ".number_format($this->_mem_threshold,0)." bytes</td></tr>";
			$dbg_div .= "<tr><td>-</td><td>".$this->getMemoryString(true)." sec.</td></tr>";
			$dbg_div .= "<tr><td>-</td><td>Memory limit = ".ini_get("memory_limit")."</td></tr>";
			$time_threshold=($this->_threshold * $execTime)/100;
			foreach ($this->_events as $event) {
				switch ($event->type) {
					case DE_ERROR:
						$dbg_div .= "<tr><td class=\"debug-error\">!!!</td><td><span class=\"debug-error\">".Text::_("Error").":</span>&nbsp;".$event->text."</td></tr>\n";
						break;

					case DE_WARNING:
						$dbg_div .= "<tr><td class=\"debug-warning\">*</td><td><span class=\"debug-warning\">".Text::_("Warning").":</span>&nbsp;".$event->text."</td></tr>\n";
						break;

					case DE_MILESTONE:
						if ($event->memory>0) $plus="+"; else $plus="-";
						$mem=$plus.number_format($event->memory,0," "," ");
						if ($event->memory > $this->_mem_threshold) {	$mem = "<span class=\"red\">".$mem."</span>"; }
						if ($event->time > $time_threshold) {
							$cur_time = "<span class=\"red\">".number_format($event->time,6)."</span>";
						} else {
							$cur_time = number_format($event->time,6);
						}
						$total_time="<span class=\"blue\">".number_format($event->ttime,6)."</span>";
						$dbg_div .= "<tr><td>-</td><td>".$event->text." => <span class=\"debug-milestone\">Time: ".$cur_time." sec, Total time ".$total_time." sec, Memory: ".$mem." bytes</span></td></tr>\n";
						break;

					case DE_MESSAGE:
					default:
						$dbg_div .= "<tr><td>-</td><td>".$event->text."</td></tr>\n";
						break;
				}
			}

			$dbg_div .= "</table><br />";
			$dbg_div .= "</div>\n";
			if(siteConfig::$debugMode>1){
				// Translation log
				$_npp=0;
				$trans=array_unique($this->_translations);
				$dbg_div .= "<div class=\"".$divClass."\" id=\"".$divId."_trans\"><br /><b>Translation errors log (".count($trans)."):</b><br /><br />\n";
				if (count($trans)>0) {
					foreach ($trans as $trans_text) {
						$_npp++;
						$dbg_div .= $trans_text."=\"".$trans_text."\"<br />";
					}
				} else { $dbg_div.="No errors found."; }
				$dbg_div .= "</div>";
			}
			// SQL log
			$dbg_div .= "<div class=\"".$divClass."\" id=\"".$divId."_sql\"><br /><b>SQL queries:</b><br /><br />\n";
			$_npp=0;
			foreach ($this->_sqlQueries as $sqlQuery) {
				$_npp++;
				$dbg_div .= $_npp.") ".$sqlQuery."<br />";
			}
			$dbg_div .= "</div>";

			if ($extDiv) {
				$dbg_div .= "</div></div>";
			}
		}

		return $dbg_div;
	}
	public function getMemory($peak = false) {
		static $isWin;
		if (($peak) && (function_exists( 'memory_get_peak_usage' ))) {
			return memory_get_peak_usage( true );
		} elseif (function_exists ( 'memory_get_usage' )) {
			return memory_get_usage();
		} else {
			// Determine if a windows server
			if (is_null ( $isWin )) {
				$isWin = (substr ( PHP_OS, 0, 3 ) == 'WIN');
			}
			// Initialize variables
			$output = array();
			$pid = getmypid();
			if ($isWin) {
				// Windows workaround
				@exec ( 'tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output );
				if (! isset ( $output [5] )) {
					$output [5] = null;
				}
				return $this->get_digits( $output [5] ) * 1024;
			} else {
				@exec ( "ps -o rss -p $pid", $output );
				return $output [1] * 1024;
			}
		}
	}
	public function getMemoryString($peak = false) {
		$pid = getmypid ();
		$byte_uses = $this->getMemory( $peak );
		$mb = intval ( $byte_uses / 1048576 );
		$kb = intval ( ($byte_uses - ($mb * 1048576)) / 1024 );
		$byte = intval ( $byte_uses - ($mb * 1048576) - ($kb * 1024) );
		return "PID - $pid .:" . strval ( $mb ) . ' mb. ' . strval ( $kb ) . ' kb. ' . strval ( $byte ) . ' b.';
	}
}
?>
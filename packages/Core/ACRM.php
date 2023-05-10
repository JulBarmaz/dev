<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class ACRM extends BaseObject{
	private static $impression_lag=86400;
//	private static $impression_lag=10;
	
	public function __construct() {
	}
	public function getItems($client=0,$cat=0,$quantity=0,$randomize=0) {
		if ($randomize)	$sql_txt="SELECT b.*,RAND() AS randomize";
		else $sql_txt="SELECT b.*";

		$sql_txt.=" FROM #__banners AS b";
		$sql_txt.=" LEFT JOIN #__banners_clients AS cl ON cl.bcl_id=b.b_client_id";
		$sql_txt.=" LEFT JOIN #__banners_categories AS cats ON cats.bc_id=b.b_cat_id";
		$sql_txt.=" WHERE b.b_enabled=1 AND b.b_publish_up<=NOW() AND b.b_publish_down>=NOW()";
		if ($client) $sql_txt.=" AND b.b_client_id=".$client;
		if ($cat) $sql_txt.=" AND b.b_cat_id=".$cat;
		$sql_txt.=" AND cl.bcl_enabled=1 AND cl.bcl_deleted=0 AND cats.bc_published=1 AND cats.bc_deleted=0";
		$sql_txt.=" AND (b.b_show_total=0 OR (b.b_show_total-b.b_show_made)>0)";

		if ($randomize)	$sql_txt.=" ORDER BY randomize";
		else $sql_txt.=" ORDER BY b.b_sticky DESC, b.b_ordering";
		
		if ($quantity) $sql_txt.=" LIMIT ".$quantity;
		Database::getInstance()->setQuery($sql_txt);
		$result=Database::getInstance()->loadObjectList();
		return $result;
	}

	public static function clickACRM($psid){
		if (Portal::getLicenseType()=="DEMO") return "ACRM restriction";
		else {
			if($psid) {
				$sql_txt="UPDATE #__banners SET b_clicks=b_clicks+1 WHERE b_id=".$psid;
				Database::getInstance()->setQuery($sql_txt);
				if (Database::getInstance()->query()) return "OK"; else return "FALSE";
			} else return "FALSE";
		}
	}
	
	public static function displayExecuted($arr){ 
		if (Portal::getLicenseType()=="DEMO") return "ACRM restriction";
		else {
			if(count($arr)) {
				$items=implode(",",$arr);
				$sql_txt="UPDATE #__banners SET b_show_made=b_show_made+1 WHERE b_id IN(".$items.")";
				Database::getInstance()->setQuery($sql_txt);
				Database::getInstance()->query();
				return "OK";
			} else return "Nothing for update";
		}
	}
	
	public static function checkShown($arr){
		$acrm_arr=array();
		$curr_adds_arr=array();
		if (is_array($arr) && count($arr)){
			$curr_adds_arr  = json_decode(Request::get("BARMAZ_acrm", "{}", "cookie"), true);
			if(!is_array($curr_adds_arr)) $curr_adds_arr=array();
			foreach($arr as $itemid){
				if (!array_key_exists($itemid, $curr_adds_arr)) {
					echo "1";
					$acrm_arr[]=$itemid;
					$curr_adds_arr[$itemid]=time();
				} elseif (intval($curr_adds_arr[$itemid]) + self::$impression_lag<time()){
					echo "2";
					$acrm_arr[]=$itemid;
					$curr_adds_arr[$itemid]=time();
				}
			}
			if (count($curr_adds_arr)) {
				Session::getInstance()->setcookie("BARMAZ_acrm", json_encode($curr_adds_arr), 0,"/");
			}
		}
		return $acrm_arr;
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogHelperPost {
	public function getDates($psid,$postDates){
		if(isset($_COOKIE["BARMAZ_blog_".$psid])){
			$dates_arr=explode("#",$_COOKIE["BARMAZ_blog_".$psid]);
			if(count($dates_arr)==2) {
				$postStartDate=$dates_arr[0];
				if(preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $postStartDate) && $postStartDate!="0000-00-00"){
					$postDates["postStartDate"]=$postStartDate." 00:00:00";
				}
				$postEndDate=$dates_arr[1];
				if(preg_match ("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $postEndDate) && $postEndDate!="0000-00-00"){
					$postDates["postEndDate"]=$postEndDate." 23:59:59";
				}
			} 
		}
		return $postDates;	
	}
	public function resetDates($psid){
		unset($_COOKIE["BARMAZ_blog_".$psid]);
	}
	public function getPostsDates($blog_id, $year=0, $month=0, $with_time=false){
		$result=array();
		$sql="SELECT p_date FROM #__blogs_posts WHERE p_deleted=0 AND p_enabled=1 AND p_blog_id=".$blog_id;
		if ($month && $year){
			$sql.=" AND YEAR(p_date)=".$year." AND MONTH(p_date)=".$month;
		}
		Database::getInstance()->setQuery($sql);
		$tmp=Database::getInstance()->loadAssocList();
		if ($with_time) return $tmp;
		if (count($tmp)){
			foreach($tmp as $key=>$dt){
				$d=substr(strval($dt["p_date"]), 0, 10);
				$result[]=$d;
			}
		}
		return $result;
	}
} 
?>

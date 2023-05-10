<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Bonus extends BaseObject {
	
	static function addUserBonus($oper_id){
		$u_id=User::getInstance()->u_id;
		$db=Database::getInstance();
		$sql="SELECT * FROM #__bonus_type WHERE b_id='".$oper_id."' AND b_deleted=0 AND b_enabled=1";
		$db->setQuery($sql);
		$res=false;
		$db->loadObject($res);
		if($res){
			$sql_add ="INSERT INTO #__bonus_list (b_id,b_uid,b_oper,b_date,b_sum) VALUES(NULL,'".$u_id."','".$res->b_id."',NOW(),'".$res->b_price."')".$db->getDelimiter();
			$sql_add.="UPDATE #__users SET u_points=u_points+".$res->b_price." WHERE u_id='".$u_id."'".$db->getDelimiter();
			$db->setQuery($sql_add);
			return $db->query_batch(true,true);		   		  	
		}
		return false;		
	}
	
}
?>


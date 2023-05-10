<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelpaymenttypes extends SpravModel {
	public function getParamsMask($elem) {
		$obj=false;
		$path=PATH_FRONT_MODULES."catalog".DS."payments".DS.$elem->pt_file.DS."default.php";
		$_class=$elem->pt_file."PaymentClass";
		if (is_file($path)){
			require_once $path;
			if (class_exists($_class)) $obj=new $_class($elem);
		}
		if (!$obj) $obj=new catalogPayment($elem);
		return $obj->getParamsMask();
	}
	public function getTemplates(){
		// @TODO может в базе хранить ?
		return Files::getFolders(PATH_FRONT_MODULES."catalog".DS."payments".DS,array(".","..",".svn"),false);
	}
	public function saveParams($id,$def_params,$params) {
		$_params = Params::intersect($params, $def_params);
		$query = "UPDATE #__goods_pts SET pt_params='".$_params."' WHERE pt_id=".$id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	public function saveNewPTS($tmpl_name){
		$db=Database::getInstance();
		$sql_txt = "INSERT INTO #__goods_pts (pt_name, pt_price, pt_currency, pt_file, pt_params, pt_enabled, pt_deleted )	
					VALUES('', 0, ".catalogConfig::$default_currency.", '".$tmpl_name."', '', 0, 0)";
		$db->setQuery($sql_txt);
		if ($db->query()) $res=$db->insertid(); else $res=false;
		return $res;
	}
}
?>
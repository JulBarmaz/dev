<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModeldeliverytypes extends SpravModel {
	public function getParamsMask($elem) {
		$obj=false;
		$path=PATH_FRONT_MODULES."catalog".DS."deliveries".DS.$elem->dt_file.DS."default.php";
		$_class=$elem->dt_file."DeliveryClass";
		if (is_file($path)){
			require_once $path;
			if (class_exists($_class)) $obj=new $_class($elem);
		}
		if (!$obj) $obj=new catalogDelivery($elem);
		return $obj->getParamsMask();
	}
	public function getElementClone($psid=0,$fillempty=false) {
		$row = $this->getElement($psid, $fillempty);
		if (is_object($row)){
			$meta = $this->meta;
			foreach($meta->field as $ind=>$field)	{
				if ($field == $meta->keystring) $row->{$field}=0;
				if($meta->input_type[$ind]=="image" || $meta->input_type[$ind]=="file") $row->{$field}="";
			}
		}
		return $row;
	}
	public function getTemplates(){
		// @TODO может в базе хранить ?
		return Files::getFolders(PATH_FRONT_MODULES."catalog".DS."deliveries".DS,array(".","..",".svn"),false);
	}
	public function saveParams($id,$def_params,$params) {
		$_params = Params::intersect($params, $def_params);
		$query = "UPDATE #__goods_dts SET dt_params='".$_params."' WHERE dt_id=".$id;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	public function saveNewDTS($tmpl_name){
		$db=Database::getInstance();
		$sql_txt = "INSERT INTO #__goods_dts (dt_name, dt_price, dt_currency, dt_file, dt_params, dt_enabled, dt_deleted )	
					VALUES('', 0, ".catalogConfig::$default_currency.", '".$tmpl_name."', '', 0, 0)";
		$db->setQuery($sql_txt);
		if ($db->query()) $res=$db->insertid(); else $res=false;
		return $res;
	}
	public function updateOrdering() {
		if (is_null($this->meta)) $this->loadMeta();
		$meta = $this->meta;
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$ordering=$reestr->get("ordering");
		$psid=$reestr->get("psid");
		$multy_code = $reestr->get("multy_code");
		if ($meta->ordering_field) {
			$sql="UPDATE #__".$meta->tablename." SET ".$meta->ordering_field."=".(int)$ordering." WHERE ".$meta->keystring."='".$psid."'";
			$this->_db->setQuery($sql);
			return $this->_db->query();
		} else return false;
	}
}
?>
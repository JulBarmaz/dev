<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Profile extends BaseObject {

	private static $_instances = array();
	private $uid 		 = false;
	private $profile = false;
	
	public static function getInstance($uid) {
		if (!isset(self::$_instances[$uid])) self::$_instances[$uid] = new self($uid);
		return self::$_instances[$uid];
	}
	private function __construct($uid) {
		$this->initObj();
		$this->_db = Database::getInstance();
		if($uid) {
			$this->uid=$uid;
			$this->setProfile();
		}
	}
	
	public static function addProfile($uid){
		$reg_data = array("uid"=>$uid);
		Event::raise("user.before_addProfile", array("module"=>false), $reg_data);
		$db = Database::getInstance();
		$sql="INSERT INTO #__profiles (`pf_id`) VALUES (".$uid.")";
		$db->setquery($sql);
		return $db->query();
	}
	
	public function getProfile()	{
		if (!$this->profile) $this->setProfile();
		return $this->profile;
	}
	
	private function setProfile(){
		$model = new SpravModel('user');
		$meta = new SpravMetadata('user','panel','defl');   
		$model->meta=$meta;
		$el=$model->getElement($this->uid);
		// заглушка для версий без профиля
		if($this->uid && !$el) { 
			$el= self::addProfile($this->uid);
			$el=$model->getElement($this->uid);
		} 
		foreach ($meta->field as $key=>$field) { 
			if ($meta->view[$key]&&$meta->input_type[$key]!="hidden") {
				if($el) $cur_val = $el->{$field}; else $cur_val=0;
				if($meta->ck_reestr[$key]) $current_value = SpravView::getValueFromCKArray($meta->ck_reestr[$key],$cur_val);
				else {
					switch($meta->val_type) {
						default:
							if($el) { $current_value = $el->{$field}; }
							else  { $current_value = ""; }
						break;
					}
				}
				$this->profile[$field]["title"]=Text::_($meta->field_title[$key]);
				$this->profile[$field]["val"]=$current_value;
			}
		}
		
	}
}
?>
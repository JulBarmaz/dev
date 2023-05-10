<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Userdata extends BaseObject {

	private static $_instance = null;
	private static $org_types = array( "0"=>"Private person",1=>"Organization",2=>"Private businessman");
	private static $tmpl = array( "org_type"=>"",
									"fullname"=>"",
									"shortname"=>"",
									"surname"=>"",
									"firstname"=>"",
									"patronymic"=>"",
									"phone"=>"",
									"fax"=>"",
									"phone_mobile"=>"",
									"phone_mobile_2"=>"",
									"doc_type"=>"",
									"doc_num"=>"",
									"doc_series"=>"",
									"doc_department"=>"",
									"doc_date"=>"",
									"inn"=>"",
									"kpp"=>"",
									"ogrn"=>"",
									"boss"=>"",
									"chief_accountant"=>""
									);
	
	
	private $uid 	 = false;
	private $crypted = false;
	private $vendors = false;
	private $company = false;
	private $banks	 = false;
	private $address = false;
	private $keyfile_path = false;
	private $keyfile = false;
	
	public static function getInstance($uid=0, $technical=false) {
		// При пустом вызове сразу лесом
		if (!$uid) Util::redirect("/index.php",Text::_("User undefined"));	
		if (!$technical && !ACLObject::getInstance('maintenanceUserdata')->canAccess()) {
			$uid	= User::getInstance()->getId();
			if (self::$_instance == null) self::$_instance = new self($uid);
		} else {
			self::$_instance = new self($uid);
		}
		return self::$_instance;
	}
	private function __construct($uid) {
		$this->initObj();
		$this->_db = Database::getInstance();
		// формируем имя файла в котором хранятся записи (примерно по 1000 на файл)
		$this->uid=$uid;
		if (backofficeConfig::$cryptoUserData) {
			$this->crypta= new Crypta();
			$this->crypted = true;
			$fn="00000000000".ceil($this->uid/1000);
			$fn=substr($fn,strlen($fn)-11);
			$this->keyfile_path = str_replace(DS.DS, DS, str_replace("/", DS, backofficeConfig::$cryptoPath).DS);
			$this->keyfile = $this->keyfile_path."f".$fn.".php";
		}
	}
	
	public function getVendors()	{
		if(!catalogConfig::$multy_vendor) return false;
		if (!$this->vendors) $this->setVendors();
		return $this->vendors;
	}
	public function setVendors()	{
		$sql="SELECT * FROM #__vendors as v, #__users_vendors as l WHERE v.v_id=l.uv_vid AND l.uv_uid>0 AND l.uv_uid=".$this->uid;
		$this->_db->setQuery($sql);
		$this->vendors=$this->_db->loadObjectList();
	}
	public function getCompany()	{
		if (!$this->company) $this->setCompany();
		return $this->company;
	}

	public function getDefaultAddress($type_id)	{
		$this->setAddresses();
		if($this->address){
		foreach($this->address as $addr){
			if ($addr['type_id']==$type_id && $addr['use_as_default']) return $addr;
		}
		}
		return Address::getTmpl();
	}
	public function getAddress($addr_id)	{
		if (!isset($this->address[$addr_id])) $this->setAddress($addr_id);
		return $this->address[$addr_id];
	}

	public function getBank($bank_id)	{
		if (!isset($this->banks[$bank_id])) $this->setBank($bank_id);
		return $this->banks[$bank_id];
	}

	public function getAddresses()	{
		//if (!is_array($this->address)) 
		$this->setAddresses();
		return $this->address;
	}
	
	public function getBanks()	{
		//if (!is_array($this->banks)) 
		$this->setBanks();
		return $this->banks;
	}
	
	private function setCompany(){
		$sql="SELECT * FROM #__users_company WHERE c_id=".$this->uid;
		$this->_db->setQuery($sql);
		$addr_rows=$this->_db->loadObjectList();
		if (count($addr_rows)==1) {
			foreach ($addr_rows as $row) {
				$data=$this->decode($row->c_data);
				if (is_array($data)){
					foreach(self::$tmpl as $key=>$val){
						if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val; 
					}
					$this->company=$current;
				} else $this->company=self::$tmpl;
			}
		} else $this->company=self::$tmpl;
	}
	public function saveCompany() {
		foreach(self::$tmpl as $key=>$val){
			$data[$key]=stripslashes(Request::getSafe($key,"")); 
		}
		$data=$this->encode($data);
		$sql="INSERT INTO #__users_company VALUES (".$this->uid.",'".$data."') ON DUPLICATE KEY UPDATE c_data='".$data."'";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}  
	private function setAddress($addr_id){
		$address = Address::getTmpl();
		$sql="SELECT * FROM #__users_addr WHERE a_id=".$addr_id." AND a_uid=".$this->uid." ORDER BY a_type";
		$this->_db->setQuery($sql);
		$addr_rows=$this->_db->loadObjectList();
		if (count($addr_rows)==1) {
			foreach ($addr_rows as $row) {
				$data=$this->decode($row->a_data);
				if (is_array($data)){
					foreach($address as $key=>$val){
						if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val; 
					}
					$this->address[$row->a_id]=$current;
				} else $this->address[$row->a_id]=$address;
				$this->address[$row->a_id]["psid"]=$row->a_id;
				$this->address[$row->a_id]["type_id"]=$row->a_type;
				$this->address[$row->a_id]["use_as_default"]=$row->a_default;
			}
		} else $this->address[]=$address;
	}
	private function setAddresses(){
		$address = Address::getTmpl();
		$sql="SELECT * FROM #__users_addr WHERE a_uid=".$this->uid." ORDER BY a_type";
		$this->_db->setQuery($sql);
		$addr_rows=$this->_db->loadObjectList();
		if (count($addr_rows)) {
			foreach ($addr_rows as $row) {
				$data=$this->decode($row->a_data);
				if (is_array($data)){
					foreach($address as $key=>$val){
						if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val; 
					}
					$this->address[$row->a_id]=$current;
				} else $this->address[$row->a_id]=$address;
				$this->address[$row->a_id]["psid"]=$row->a_id;
				$this->address[$row->a_id]["type_id"]=$row->a_type;
				$this->address[$row->a_id]["use_as_default"]=$row->a_default;
			}
		} else $this->address=false;
	}
	public function saveAddress($psid=0) {
		$address = Address::getTmpl();
		foreach($address as $key=>$val){
			$address[$key]=stripslashes(Request::getSafe($key,$val)); 
		}
		if(!$psid) $psid=Request::getInt("psid",0);
		$type_id=Request::getInt("type_id",0);
		$use_as_default=Request::getInt("use_as_default",0);
		// Cleaning an filling fullinfo
		$address['fullinfo']=trim($address['fullinfo'],",");
		$address['fullinfo'] = preg_replace('/\,+/', ',', $address['fullinfo']);
		if($address['fullinfo']=="" || $address['fullinfo']==",") $address['fullinfo'] = Text::_(Address::$address_types[$type_id]);
		$address=$this->encode($address);
		if ($use_as_default) {
			$sql="UPDATE #__users_addr SET a_default=0 WHERE a_id<>".$psid." AND a_type=".$type_id." AND a_uid=".$this->uid;
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		if ($psid) $sql="UPDATE #__users_addr SET a_data='".$address."', a_type=".$type_id.",a_default=".$use_as_default." WHERE a_id=".$psid." AND a_uid=".$this->uid;
		else $sql="INSERT INTO #__users_addr VALUES (NULL,".$this->uid.",".$type_id.",".$use_as_default.",'".$address."')";
		$this->_db->setQuery($sql);
		$res=$this->_db->query();
		if($res) return $address; else return false;
	}  
	public function deleteAddress() {
		$psid=Request::getInt("psid",0);
		if ($psid) {
			$sql="DELETE FROM #__users_addr WHERE a_id=".$psid." AND a_uid=".$this->uid;
			$this->_db->setQuery($sql);
			$res=$this->_db->query();
			return $res;
		}
	}  
	private function setBank($bank_id){
		$tmpl = Bank::$tmpl;
		$sql="SELECT * FROM #__users_bank WHERE b_id=".$bank_id." AND b_uid=".$this->uid;
		$this->_db->setQuery($sql);
		$rows=$this->_db->loadObjectList();
		if (count($rows)==1) {
			foreach ($rows as $row) {
				$data=$this->decode($row->b_data);
				if (is_array($data)){
					foreach($tmpl as $key=>$val){
						if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val; 
					}
					$this->banks[$row->b_id]=$current;
				} else $this->banks[$row->b_id]=$tmpl;
				$this->banks[$row->b_id]["psid"]=$row->b_id;
				$this->banks[$row->b_id]["use_as_default"]=$row->b_default;
			}
		} else $this->banks[]=$tmpl;
	}
	private function setBanks(){
		$tmpl = Bank::$tmpl;
		$sql="SELECT * FROM #__users_bank WHERE b_uid=".$this->uid;
		$this->_db->setQuery($sql);
		$rows=$this->_db->loadObjectList();
		if (count($rows)) {
			foreach ($rows as $row) {
				$data=$this->decode($row->b_data);
				if (is_array($data)){
					foreach($tmpl as $key=>$val){
						if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val; 
					}
					$this->banks[$row->b_id]=$current;
				} else $this->banks[$row->b_id]=$tmpl;
				$this->banks[$row->b_id]["psid"]=$row->b_id;
				$this->banks[$row->b_id]["use_as_default"]=$row->b_default;
			}
		} else $this->banks=false;
	}
	
	public function saveBank() {
		$data = Bank::$tmpl;
		foreach($data as $key=>$val){
			$data[$key]=stripslashes(Request::getSafe($key,"")); 
		}
		$psid=Request::getInt("psid",0);
		$use_as_default=Request::getInt("use_as_default",0);
		$data=$this->encode($data);
		if ($use_as_default) {
			$sql="UPDATE #__users_bank SET b_default=0 WHERE b_id<>".$psid." AND b_uid=".$this->uid;			
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		if ($psid) $sql="UPDATE #__users_bank SET b_data='".$data."',b_default=".$use_as_default." WHERE b_id=".$psid." AND b_uid=".$this->uid;
		else $sql="INSERT INTO #__users_bank VALUES (NULL,".$this->uid.",".$use_as_default.",'".$data."')";
		$this->_db->setQuery($sql);
		$res=$this->_db->query();
		return $res;
	}  
	
	public function deleteBank() {
		$psid=Request::getInt("psid",0);
		if ($psid) {
			$sql="DELETE FROM #__users_bank WHERE b_id=".$psid." AND b_uid=".$this->uid;
			$this->_db->setQuery($sql);
			$res=$this->_db->query();
			return $res;
		}
	}  
	
	public function decode($data){
		if ($this->crypted) $data=$this->crypta->user_decrypt(base64_decode($data),$this->getUserKey());
		else $data=base64_decode($data);
		$result=json_decode($data,true); 
		return $result;
	}
	public function encode($data){
		$result=json_encode($data);
		if ($this->crypted) $result=base64_encode($this->crypta->user_encrypt($result, $this->getUserKey()));
		else $result=base64_encode($result);
		return $result;
	}
	private function addUserKey()	{
		$id=$this->uid;
		// формируем хеш для пользователя
		$key=md5($id.time());
		$str=$id."=".$key.CR_LF;
		if ($this->keyfile_path && is_dir($this->keyfile_path)) {
			$f=fopen($this->keyfile,"a");
			if (is_resource($f)) {
				fwrite($f,$str);
				fclose($f);
				return $key;
			} else {
				$this->error("Error add key file".$this->keyfile);
				return false;
			}
		} else {
			$this->error("Error user key add ".$this->keyfile);
			return false;
		}
	}

	private function getUserKey() {
		$id=$this->uid;
		if(file_exists($this->keyfile)) {
			$res=parse_ini_file($this->keyfile);
			if(!isset($res[$id])) {
				return $this->addUserKey($id);
			}	else {
				return $res[$id];
			}
		} else return $this->addUserKey($id);
		return false;
	}	
	public static function renderOrgTypeSelector($type_id){
		foreach (self::$org_types as $key=>$val){
			$org_types[$key]=Text::_($val);
		}
		$html=HTMLControls::renderSelect("org_type", "", "", "", $org_types,$type_id,false);
		return $html;
		
	}
	public static function getOrgType($type_id){
		if (isset(self::$org_types[$type_id])) return self::$org_types[$type_id]; else return "";
	}
}
?>
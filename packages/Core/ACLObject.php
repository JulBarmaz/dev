<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class ACLObject extends BaseObject {

	private static $_aclObjects = array();
	private static $_aoData = array();
	private static $_roles = null;
	private static $s_allowedByDefault = array("helpModule");

	private $_allowedByDefault = array();
	private $_id 			= 0;
	private $_description	= '';
	private $_moduleName	= '';
	private $_access 		= 0;
	
	
	public static function initialize() {
		$db = Database::getInstance();
		$isAdminMode = 0;
		if (defined('_ADMIN_MODE')) $isAdminMode = 1;
		$db->setQuery("SELECT * FROM `#__acl_objects` WHERE `ao_is_admin`=".$isAdminMode);
		self::$_aoData = $db->loadObjectList("ao_name");
		Debugger::getInstance()->milestone("ACLObject initialized");
	}

	public static function clearACL() {
		self::$_aclObjects = array();
	}
	
	public static function getRoles() {
		if (self::$_roles == null) {
			$query = "SELECT * FROM #__acl_roles WHERE ar_deleted=0";
			$db = Database::getInstance();
			$db->setQuery($query);
			self::$_roles = $db->loadObjectList();
		}
		return self::$_roles;
	}
	
	public static function createACLObject($moduleName, $objectName, $objectDescription="", $isAdmin=0) {
		if ($objectDescription == "") $objectDescription = $objectName;
		$db = Database::getInstance();
		$query = "SELECT COUNT(ao_id) FROM #__acl_objects WHERE ao_name='".$objectName."' AND ao_module_name='".$moduleName."' AND ao_is_admin=".$isAdmin;
		$db->setQuery($query);
		if (!$db->loadResult()) {
			$qIsAdmin = 0;
			if ($isAdmin) $qIsAdmin = 1;
			$query="INSERT INTO #__acl_objects(ao_name, ao_module_name, ao_description, ao_is_admin)
					VALUES('".$objectName."','".$moduleName."','".$objectDescription."','".$qIsAdmin."')";
			$db->setQuery($query);
			$db->query();
			return $db->insertid();
		}	else {
			Debugger::getInstance()->error(Text::_("ACL object already exists").": ".$objectName);
			return false;
		}
	}
	public static function getInstance($aclObjectName,$critical=true) {
		if (array_key_exists($aclObjectName, self::$_aclObjects) == false) {
			if (array_key_exists($aclObjectName, self::$_aoData)) {
				$aclObjectRow = self::$_aoData[$aclObjectName];
				$aclObject = new ACLObject($aclObjectRow->ao_id,$aclObjectRow->ao_name,$aclObjectRow->ao_description,$aclObjectRow->ao_module_name);
			} else {
				if(!in_array($aclObjectName, self::$s_allowedByDefault) && $critical == true) Util::fatalError(Text::_('ACL object not exists').": ".$aclObjectName); // Realy fatal. Method is static.
				$aclObject = new ACLObject(0, $aclObjectName, '', '');
			}
			self::$_aclObjects[$aclObjectName] = $aclObject;
		}
		return self::$_aclObjects[$aclObjectName];
	}
	private function __construct($aclObjectId, $aclObjectName, $aclObjectDescription, $aclObjectModuleName) {
		Debugger::getInstance()->milestone("ACLObject constructor => ".$aclObjectName);
		$this->initObj("ACLObject_".$aclObjectName);
		$this->_id = $aclObjectId;
		$this->_moduleName = $aclObjectModuleName;

		$this->_allowedByDefault = self::$s_allowedByDefault;

		if ($aclObjectDescription == '') {
			$aclObjectDescription = $aclObjectName;
		}
		$this->_description = $aclObjectDescription;

		if (!in_array($aclObjectName,$this->_allowedByDefault)) {
			$db = Database::getInstance();
			$user = User::getInstance();
			$query = "SELECT `acl_access` FROM `#__acl_rules` WHERE (`acl_object_id`=".$this->_id.") AND (`acl_role_id`=".$user->getRole().")";
			$db->setQuery($query);
			$this->_access = intval($db->loadResult());
		}
		else { $this->_access = 1;
		}
		Debugger::getInstance()->milestone("ACLObject created => ".$aclObjectName);
	}

	public function getId() {
		return $this->_id;
	}

	public function getDescription() {
		return $this->_description;
	}

	public function getModuleName() {
		return $this->_moduleName;
	}

	public function canAccess() {
		return ($this->_access == 1);
	}

}

?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aclmgrHelperAcl{
	public function __construct() {
		$this->_db=Database::getInstance();
	}
	public function checkModulesAccess(&$message) {
		$modules=Module::getInstalledModules();
		foreach($modules as $module) {
			$sql = "SELECT COUNT(ao_id) FROM #__acl_objects WHERE ao_name='".$module."Module' AND ao_module_name='".$module."' AND ao_is_admin=1";
			$this->_db->setQuery($sql);
			if(!$this->_db->LoadResult()) {
				$message[]=Text::_('Admin module access rule for module')." ".$module." ".Text::_("is absent");
				$sql = "INSERT INTO `#__acl_objects`
						(`ao_name`,`ao_module_name`,`ao_description`,`ao_is_admin`)
						VALUES ('".$module."Module','".$module."','Module access',1)";
				$this->_db->setQuery($sql);
				if(!$this->_db->query()) {
					$message[]=Text::_('Adding admin module access rule for module')." ".$module." ".Text::_("failed");
					return false;
				} else {
					$message[]=Text::_('Admin module access rule for module')." ".$module." ".Text::_("added");
				}
			} elseif(ACLObject::getInstance($module."Module", false)->canAccess() != 1) {
				$message[]=Text::_('Your role have no access for module')." ".$module;
			}
		}
		if(count($message)){
			$message[]=Text::_('Check your role module access rules');
			return false;
		} else return true;
		
	}
	
	public function checkModulesAcl(&$message) {
		$sides=array(0, 1);
		$result = true;
		$modules=Module::getInstalledModules();
		foreach($modules as $module) {
			foreach($sides as $side) {
				$sql = "SELECT ao_id, ao_name FROM #__acl_objects WHERE ao_module_name='".$module."' AND ao_is_admin=".$side;
				$this->_db->setQuery($sql);
				$res_db=$this->_db->loadAssocList("ao_name");
				$res_core=Module::getInstance($module)->getACLTemplate($side);
				if(count($res_core)) {
					$counter=0;
					foreach($res_core as $acl) 	{
						$counter=$counter+10;
						if(!array_key_exists($acl['ao_name'], $res_db)) {
							if($side) $message[]=sprintf(Text::_('Admin rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("is absent");
							else $message[]=sprintf(Text::_('Front rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("is absent");
							$sql = "INSERT INTO `#__acl_objects` (`ao_name`,`ao_module_name`,`ao_description`,`ao_ordering`,`ao_is_admin`)
									VALUES ('".$acl['ao_name']."','".$module."','".$acl['ao_description']."',".$counter.",".$side.")";
							$this->_db->setQuery($sql);
							if(!$this->_db->query()) {
								$result = false;
								if($side) $message[]=sprintf(Text::_('Adding admin rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("failed");
								else $message[]=sprintf(Text::_('Adding front rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("failed");
							} else {
								if($side) $message[]=sprintf(Text::_('Admin rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("added");
								else $message[]=sprintf(Text::_('Front rule %s for module'),$acl['ao_name'])." ".$module." ".Text::_("added");
							}
						} else {
							$key = $res_db[$acl['ao_name']]['ao_id'];
							$sql = "UPDATE `#__acl_objects` SET `ao_ordering`=".$counter.", `ao_description`='".$acl['ao_description']."' WHERE ao_id=".$key;
							$this->_db->setQuery($sql);
							if(!$this->_db->query()) {
								$result = false;
								if($side) $message[]=sprintf(Text::_('Admin rule update %s for module'),$acl['ao_name'])." ".$module." ".Text::_("failed");
								else $message[]=sprintf(Text::_('Front rule update %s for module'),$acl['ao_name'])." ".$module." ".Text::_("failed");
							}
						}
					}
				}
			}
		}
		return $result;
	}
}
?>
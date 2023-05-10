<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class ConfigTMPL {
	public static $_tabs= array("1"=>array("site","Site settings"),
			"2"=>array("backoffice","Security settings"),
			"3"=>array("admin","Admin panel settings"),
			"4"=>array("catalog","Catalog settings"),
			"5"=>array("gallery","Gallery settings"),
			"6"=>array("mailer","Mail settings"),
			"7"=>array("so","Site owner settings"),
			"8"=>array("seo","Seo settings")
	);
	public static function _loadConfigs() {
		foreach(self::$_tabs as $config) {
			require_once PATH_CONFIG.$config[0].'.php';
		}
	}
	public static function getConfigVar($className, $property) {
		if(!class_exists($className)) return null;
		if(!property_exists($className, $property)) return null;
		if(isset($className::$$property)) return $className::$$property;
		else return null;
		/* Old variant */
		// $vars = get_class_vars($className);
		// return $vars[$property];
	}
	public static function setConfigVar($className, $property, $value) {
		if(!class_exists($className)) return false;
		if(!property_exists($className, $property)) return false;
		$className::$$property=$value;
		return true;
	}
	public static function _loadVars() {
		// подбираем все из базы
		$db=Database::getInstance();
		$sql="SELECT * from #__config ORDER BY cfg_section, cfg_key";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		if (count($res)>0) {
			foreach($res as $row) {
				self::setConfigVar($row->cfg_section.'Config', $row->cfg_key, $row->cfg_value);
			}
		}
		$db->setDebugMode();
		Debugger::getInstance()->milestone("First possible milestone.Configs updated. db ".$db->getDBName());
	}
}
/* 0) тип свойства, 
 * 1) значение по умолчанию, пока не используется нигде, кроме формы настроек для типов string, берется из самих файлов настроек 
 * 2) source, 
 * 3) показывать ли подсказку вида (имя параметра description - прописывается в ini файлах перевода),
 * 4) требуется перевод 
 * */
class siteConfigTMPL extends ConfigTMPL {
	public $props = array(
			"metaTitle"					=>	array("string","BARMAZ-CMS"),
			"siteDisabled"				=>	array("boolean",false),
			"metaDescription"			=>	array("string",""),
			"metaKeywords"				=>	array("string",""),
			"siteDomain"				=>	array("string","BARMAZ-cms.web",false,true),
			"sitePort"					=>	array("integer","80",false,true),
			"siteSSLPort"				=>	array("integer","443",false,true),
			"enableGeneratorMetaTag"	=>	array("boolean",false,false,false),
//			"gzipMode"					=>	array("boolean",true,false,true),
			"allowedType"				=>	array("multiselect_method","","Mime::getMimeKeys"),
			"cssOverride"				=>	array("boolean",false),
			"loadBootstrap"				=>	array("boolean",true),
			"useJQueryUIMini"			=>	array("boolean",true),
			"debugMode"					=>	array("select","0",array("0"=>"Disabled","1"=>"Except CSS and translation - Admin only","2"=>"Except CSS - Admin only","100"=>"Full - Admin only","101"=>"Except CSS and translation - All users","102"=>"Except CSS - All users","200"=>"Full - All users")),
			"siteMemoryLimit"			=>	array("select","20",array("20"=>"20","25"=>"25","30"=>"30","35"=>"35","40"=>"40","50"=>"50","60"=>"60","80"=>"80","100"=>"100","120"=>"120","140"=>"140","160"=>"160","190"=>"190"), false, false),
			"siteTimeLimit"				=>	array("select","30",array("30"=>"30","45"=>"45","60"=>"60","90"=>"90","120"=>"120","180"=>"180","240"=>"240"), false, false),
			"cookieLifeTime"			=>	array("integer",1440),
			"cacheLife"					=>	array("integer",0),
			"defaultLanguage"			=>	array("folder","ru", "/language/common"),
			"site_rules_article"		=>	array("table_select",2,"SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY a_ordering, fld_name"),
			"privacy_policy_article"	=>	array("table_select",2,"SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY a_ordering, fld_name"),
			"defaultModule"				=>	array("folder","site", "/modules"),
			"defaultMenuID"				=>	array("table_select", 32, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name", true),
			"pagesPerPanel"				=>	array("integer",10),
			"recordsPerPage"			=>	array("integer",10),
			"shortTextLength"			=>	array("integer",350),
			"searchTargetBlank"			=>	array("boolean",true),
			"searchMenuID"				=>	array("table_select", 0, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name"),
			"treeDepth"					=>	array("integer",20),
			"sendFeedbacksByMail"		=>	array("boolean",true),
			"siteTemplate"				=>	array("folder","html5","/templates"),
			"siteTemplatesByMenu"		=>	array("boolean", false),
			"use_referral_system"		=>	array("boolean", true),
			"use_points_system"			=>	array("boolean", true),
			"useMultiVote"				=>	array("boolean", false),
			"def_account_bonus"			=>	array("integer", 0),
			"useTextAddress"			=>	array("boolean", true,false,true),
			"intervalNumFilter"			=>	array("boolean", false)
			/* , "useTranslateSystem"		=>	array("boolean", false) */
	);
	public function __construct() {
		$_source=array();
		$front_module_folders=Files::getFolders(PATH_FRONT.DS.'modules',array(".svn",".",".."));
		$en_modules=Module::getInstalledModules(false,true);
		foreach($en_modules as $key=>$val){
			if(array_key_exists($val, $front_module_folders)){
				$_source[$val]=$val;
			}	
		}
		$this->props["defaultModule"]=array("select","0", $_source);
	}
}
class backofficeConfigTMPL extends ConfigTMPL {
	public $props = array(
			"noRegistration"			=>	array("boolean",false),
			"floodDelay"				=>	array("integer",5),
			"cryptoUserData"			=>	array("boolean",true,false,true),
			"cryptoPath"				=>  array("string", "", false, true),
			"backupPath"				=>  array("string", "", false, true),
			"regConfirmation"			=>	array("select","1", array(0=>"Not required",1=>"Confirmation by E-mail",2=>"Confirmation by admin")),
			"sameSitecookie"      =>	array("select","none", array('none'=>"Absent",'Strict'=>"Strict",'Lax'=>"Lax"),true),
			"secureCoockie"				=>	array("boolean",false,false,true),			
			"allowSNLogin"				=>	array("boolean",false,false,true),
			"allowEmailLogin"			=>	array("boolean",false),
			"defaultUserRole"			=>	array("table_select", 2,"SELECT ar_id AS fld_id, ar_title AS fld_name FROM #__acl_roles ORDER BY fld_name"),
			"allowAutoExchange"			=>	array("select", "0", array(0=>"Disabled", 1=>"Allowed by cookie", 2=>"Allowed by cookie and sesssion id in request", 3=>"Allowed by cookie and ID in request")),
			"autoExchangeLogin"			=>	array("string","autoExchangeLogin"),
			"secretCode"				=>  array("string", "Secret Code",false,true),
			"unlockKey"					=>  array("string", "",false,true),
			"updatesBetaChannel"		=>	array("boolean",false,false,true)
	);
	public function __construct() {
		$this->props["cryptoPath"][1] = str_replace(DS,"/", PATH_FRONT).".crypto/";
		$this->props["backupPath"][1] = str_replace(DS,"/", PATH_FRONT).".backup/";
	}
}
class catalogConfigTMPL extends ConfigTMPL {
	public $props = array(
			"catalogTitle"				=>	array("string",""),
			"catalogDescription"		=>	array("string",""),
			"catalogKeywords"			=>	array("string",""),
			"catalogDisabled"			=>	array("boolean", false),
			"catalogDisabledMsg"		=>	array("text", "Catalog is temporary disabled.<br />Please visit us later."),
			"ordersDisabled"			=>	array("boolean", false),
			"ordersWithoutRegistration"	=>	array("boolean", false, false, true),
			"catalogAdminEmail"			=>	array("string", "admin@localhost"),
			"catalog_rules_article"		=>	array("table_select",2,"SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY fld_name"),
			"default_currency"			=>	array("table_select", "0","SELECT c_id AS fld_id, c_name AS fld_name FROM #__currency ORDER BY fld_name"),
			"default_measure"			=>	array("table_select", "0","SELECT meas_id AS fld_id, meas_full_name AS fld_name FROM #__measure ORDER BY fld_name"),
			"thousandSeparator"			=>	array("string", ""),
			"price_digits"				=>	array("integer", 2),
			"quantity_digits"			=>	array("integer", 4),
			"default_wmeasure"			=>	array("table_select", "0","SELECT meas_id AS fld_id, meas_full_name AS fld_name FROM #__measure WHERE meas_type=4 ORDER BY fld_name"),
			"weight_digits"				=>	array("integer", 3),
			"default_size_measure"		=>	array("table_select", "0","SELECT meas_id AS fld_id, meas_full_name AS fld_name FROM #__measure WHERE meas_type=2 ORDER BY fld_name"),
			"size_digits"				=>	array("integer", 3),
			"default_vol_measure"		=>	array("table_select", "0","SELECT meas_id AS fld_id, meas_full_name AS fld_name FROM #__measure WHERE meas_type=1 ORDER BY fld_name"),
			"volume_digits"				=>	array("integer", 3),
			"size4volume_measure"		=>	array("table_select", "0","SELECT meas_id AS fld_id, meas_full_name AS fld_name FROM #__measure WHERE meas_type=2 ORDER BY fld_name", true),
			"hide_prices"				=>	array("boolean", false),
			"basket_fullview"			=>	array("boolean", true),
			"default_order_status"		=>	array("table_select", 1,"SELECT os_id AS fld_id, os_name AS fld_name FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY fld_id"),
			"default_order_taxes"		=>	array("table_select", 1,"SELECT t_id AS fld_id, t_name AS fld_name FROM #__taxes ORDER BY fld_id"),
			"ggr_thumb_AutoResize"		=>	array("boolean",true),
			"ggr_thumb_width"			=>	array("integer", 250),
			"ggr_thumb_height"			=>	array("integer", 250),
			"thumbAutoCreate"			=>	array("boolean",true),
			"thumbAutoResize"			=>	array("boolean",true),
			"thumb_width"				=>	array("integer", 250),
			"thumb_height"				=>	array("integer", 250),
			"mediumImgAutoCreate"		=>	array("boolean",true),
			"mediumImgAutoResize"		=>	array("boolean",true),
			"mediumImgWidth"			=>	array("integer", 350),
			"mediumImgHeight"			=>	array("integer", 350),
			"listImageLink"				=>	array("select","0",array("0"=>"Disabled","1"=>"Popup","2"=>"Flypage")),
			"show_stock"				=>	array("boolean", false),
			"check_stock"				=>	array("boolean", false),
			"show_base_price"			=>	array("boolean", false),
			"show_pack_price"			=>	array("boolean", false),
			"complectPriceAsGoodsSum"	=>	array("boolean", false),
			"multy_vendor"				=>	array("boolean", false,false,true),
			"default_vendor"			=>	array("table_select", "0","SELECT v_id AS fld_id, v_name AS fld_name FROM #__vendors ORDER BY fld_name"),
			"default_manufacturer"		=>	array("table_select", 1,"SELECT mf_id AS fld_id, mf_name AS fld_name FROM #__manufacturers ORDER BY fld_id")
	);
	public function __construct() {
		$_source=SpravStatic::getCKArray("thousand_separator_index");
		$this->props["thousandSeparator"]=array("select","1", $_source, false, false);
	}
}
class adminConfigTMPL extends ConfigTMPL {
	public $props = array(
			"adminTemplate"				=>	array("folder","space","/administrator/templates"),
			"cssOverride"				=>	array("boolean",false),
			"loadBootstrap"				=>	array("boolean",true),
			"useJQueryUIMini"			=>	array("boolean",true),
			"adminDefaultModule"		=>	array("folder","help", "/administrator/modules"),
			"adminMemoryLimit"			=>	array("select","100",array("20"=>"20","25"=>"25","30"=>"30","35"=>"35","40"=>"40","50"=>"50","60"=>"60","80"=>"80","100"=>"100","120"=>"120","140"=>"140","160"=>"160","190"=>"190"), false, false),
			"adminTimeLimit"			=>	array("select","60",array("30"=>"30","45"=>"45","60"=>"60","90"=>"90","120"=>"120","180"=>"180","240"=>"240"), false, false),
			"adminPagesPerPanel"		=>	array("integer",20),
			"adminRecordsPerPage"		=>	array("integer",30),
			"adminCleanRowsPerQuery" 	=> 	array("integer",20),
			"adminSelectorAsTree"		=>	array("boolean", false,false,true),
			"adminFltIncCustomFld"		=>	array("boolean",false)
	);
}
class soConfigTMPL extends ConfigTMPL {
	public $props = array(
			"showOnFeedbackPage"		=>	array("boolean",false),
			"firmName"					=>	array("string", ""),
			"fullFirmName"				=>	array("string", ""),
			"OGRN"						=>	array("string", ""),
			"INN"						=>	array("string", ""),
			"KPP"						=>	array("string", ""),
			"Address"					=>	array("string", ""),
			"Addressadd"				=>	array("string", ""),
			"siteEmail"					=>	array("string","admin@localhost"),
			"contactName"				=>	array("string", ""),
			"Timework"					=>	array("string", ""),
			"Phone"						=>	array("string", ""),
			"Phone2"					=>	array("string", ""),
			"Fax"						=>	array("string", ""),
			"Viber"						=>	array("string", ""),
			"WhatsApp"					=>	array("string", ""),
			"Telegram"					=>	array("string", ""),
			"Skype"						=>	array("string", ""),
			"ICQ"						=>	array("string", ""),
			"Jabber"					=>	array("string", "")
	);
}
class mailerConfigTMPL extends ConfigTMPL {
	public $props = array(
			"robotNotifier"				=>	array("select","0",array("0"=>"Disabled","1"=>"Internal","2"=>"External")),
			"robotNotifierMessages"		=>	array("integer", 10),
			"robotEmail" 				=>	array("string", "admin@localhost",false,true),
			"useSMTP"					=>	array("boolean", true),
			"logErrors"					=>	array("boolean", true),
			"smtpServer" 				=>	array("string", "127.0.0.1"),
			"smtpPort" 					=>	array("integer", 25,false,false),
			"smtpUser" 					=>	array("string", "guest"),
			"smtpPassword" 				=>	array("password", ""),
			"exchangeEmail"				=>  array("string", "admin@localhost"),
//			"exchangePassword"			=>  array("password", ""),
			"pop3Server" 				=>	array("string", "127.0.0.1"),
			"pop3Port" 					=>	array("integer", 110),
			"pop3User" 					=>	array("string", "guest"),
			"pop3Password"				=>	array("password", ""),
			"smsEnabled"				=>	array("boolean", false),
			"smsProvider"				=>	array("filenames","","/packages/tools/SMSProviders"),
			"smsUser" 					=>	array("string", ""),
			"smsPassword"				=>	array("password", ""),
			"smsSender"					=>	array("string", "")
	);
}
class galleryConfigTMPL extends ConfigTMPL {
	public $props = array(
			"ggr_thumbAutoResize"			=>	array("boolean",true),
			"ggr_thumbWidth"			=>	array("integer",200),
			"ggr_thumbHeight"			=>	array("integer",200),
			"gal_thumbAutoResize"			=>	array("boolean",true),
			"gal_thumbWidth"			=>	array("integer",200),
			"gal_thumbHeight"			=>	array("integer",200),
			"thumbAutoCreate"			=>	array("boolean",true),
			"thumbAutoResize"			=>	array("boolean",true),
			"thumbWidth"				=>	array("integer",100),
			"thumbHeight"				=>	array("integer",100),
			"thumbTitleDelta"			=>	array("integer",30),
			"slideshow_timeout"			=>	array("integer",3),
			"showParentDescr"			=>	array("boolean",true)
	);
}
class seoConfigTMPL extends ConfigTMPL {
	public $props = array(
			"sefMode"					=>	array("boolean",false,false,true),
			"enableMetaKeywords"		=>	array("boolean",false,false,false),
//			"strictSefMode"				=>	array("boolean",false,false,true),
			"stop404"					=>	array("boolean",false,false,true),
			"activeMidFromTable"		=>	array("boolean",true),
			"useMidInMenuLinks"		    =>	array("boolean",true,false,true),
			"saveOriginalImageName"		=>	array("boolean",false,false,true),
			"alwaysAbsoluteLinks"		=>	array("select", "2", array("0"=>"Disabled", "1"=>"Without protocol", "2"=>"With current protocol")),
			"tmplCSSBackCompatibility"	=>	array("boolean",false,false,true)
	);
}

?>
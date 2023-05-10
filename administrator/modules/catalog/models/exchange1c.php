<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelexchange1c extends Model {
	private $work_dir=PATH_TMP;
	private $tmp_dir=PATH_TMP;
	private $helper = false;
	private $cml_version_in_min = "";
	private $cml_version_in_max = "";
	private $cml_version_out = "";
	private $changes_only = null;
	private $log_level=0;
	private $log_file="exchange1c.log";
	private $autoexchange=false;
	private $start_date=false;
	private $end_date=false;
	private $offers_mode=0;
	private $export_system="";
	
	public function __construct($module) {
		parent::__construct($module);
		$this->work_dir = PATH_TMP."exchange1c".DS;
		$this->tmp_dir = PATH_TMP."exchange1c_tmp".DS;
		$cml_version = explode("-", $this->getParam("1c_version_in"));
		$this->cml_version_in_min=$cml_version[0];
		$this->cml_version_in_max=$cml_version[1];
		$this->cml_version_out=$this->getParam("1c_version_out");
		$this->helper=$this->getModule()->getHelper("exchange1c");
		$this->log_level=intval($this->getParam("1c_log_level"));
		$this->setLogfile();
		$this->setOffersMode($this->getParam("1c_goods_offers_mode"));
		$this->setExportSystem($this->getParam("1c_goods_export_system"));
	}
	public function dump2screen($var, $stop_executing=false){
		$this->helper->dump2screen($var, $stop_executing);
	}
	public function setExportSystem($mode=""){
		$this->export_system = $mode;
		$this->helper->setExportSystem($mode);
	}
	public function setOffersMode($mode=0){
		$this->offers_mode = $mode;
		$this->helper->setOffersMode($mode);
	}
	public function setAutoexchange($autoexchange=false){
		$this->autoexchange = $autoexchange;
		$this->helper->setAutoexchange($autoexchange);
	}
	public function setLogfile($suffix=""){
		if(!$suffix) $suffix=Request::getSafe("type");
		if($suffix) $this->log_file="exchange1c-".$suffix.".log";
		$this->helper->setLogLevel($this->log_level);
		$this->helper->setLogFile($this->log_file);
	}
	public function clearLog(){
		Util::writeLog("CLEARED LOG FILE", $this->log_file, true, true);
	}
	private function log($message, $level=0, $with_date=true){
		if($level<=$this->log_level){
			Util::writeLog($message, $this->log_file, $with_date);
		}
	}
	private function logTitle($message, $level=1){
		$this->log(str_repeat("#", 100), $level, false);
		$this->log(mb_strtoupper($message, DEF_CP), $level);
		$this->log(str_repeat("#", 100), $level, false);
	}
	private function logError($message, $level=1){
		$this->log("[ERROR] ".$message, $level);
	}
	private function logWarning($message, $level=1){
		$this->log("[WARNING] ".$message, $level);
	}
	private function logInfo($message, $level=2){
		$this->log("[INFO] ".$message, $level);
	}
	private function logDebug($message, $level=3){
		$this->log("[DEBUG] ".$message, $level);
	}
	private function cleanFolders(){
		$result=true;
		if(Files::checkFolder($this->work_dir, false)) $result=Files::removeFolder($this->work_dir, true);
		if(!$result) return $result;
		if(Files::checkFolder($this->tmp_dir, false)) $result=Files::removeFolder($this->tmp_dir, true);
		return $result;
	}
	public function resetVars($mode="all"){
		switch ($mode){
			case "all":
			default:
				// Settings::setVar($name, $val);
				Session::getInstance()->saveUserVar("exchange1c_init_ok", false);
				Session::getInstance()->saveUserVar("exchange1c_zip", false);
				Session::getInstance()->saveUserVar("exchange1c_last_zip_file", "");
				Session::getInstance()->saveUserVar("exchange1c_classifier", json_encode(false));
				break;
		}
	}
	public function initRemote(&$messages){
		$this->setAutoexchange(true);
		if($this->getParam("1c_log_always_clean")) $this->clearLog();
		$this->resetVars();
		if($this->cleanFolders()){
			if(Files::checkFolder($this->work_dir, true)){
				$remote_version=Request::getSafe("version");
				if($remote_version && !$this->checkCMLVersion($remote_version)){
					$messages[]=Text::_("Incompatible version").": ".$remote_version.", ".Text::_("waiting").": ".$this->cml_version_in_min."-".$this->cml_version_in_max;
					$this->logError("Incompatible version: ".$remote_version.", waiting: ".$this->cml_version_in_min."-".$this->cml_version_in_max);
				} else {
					$messages[]="zip=".(intval($this->getParam("1c_zip")) ? "yes":"no");
					$messages[]="file_limit=".(intval($this->getParam("1c_filesize"))*1024);
					$this->logInfo("Init OK");
					Session::getInstance()->saveUserVar("exchange1c_init_ok", true);
					return true;
				}
			}
		}
		return false;
	}
	private function getXMLFilesList(){
		// Get with default sorting. May be we need some mask for sorting ?
		$xml_files=array();
		$all_files=Files::getFiles($this->work_dir);
		if(count($all_files)){
			foreach($all_files as $file){
				if(!$file["folder"] && Files::getExt($file["filename"])=="xml"){
					$xml_files[]=$file["filename"];
				}
			}
		}
		return $xml_files;
	}
	public function manualUpload(&$messages, &$files) {
		$result = false;
		$allowed_keys=preg_split("/(\;)/",siteConfig::$allowedType);
		if(!in_array("xml", $allowed_keys) || !in_array("zip", $allowed_keys)){
			$this->logError("File type not allowed: xml, zip");
			$messages[]=Text::_("File type not allowed").": xml, zip. ".Text::_("Check main settings").".";
			return false;
		}
		if(Files::checkFolder($this->work_dir, false)) Files::removeFolder($this->work_dir, true);
		Files::checkFolder($this->work_dir, true);
		if(Files::checkFolder($this->tmp_dir, false)) Files::removeFolder($this->tmp_dir, true);
		Files::checkFolder($this->tmp_dir, true);
		$res=Files::uploadTempFile("import_file", "exchange1c_tmp");
		if(!$res || !(isset($res["file"]) && $res["file"] && is_file($res["file"]))) {
			$this->logError("File upload error");
			$messages[]=Text::_("File upload error");
		} else {
			$ext=Files::getExt($res["file"]);
			if($ext == "zip"){
				if(Files::unzip($res["file"], $this->work_dir)){
					$files = $this->getXMLFilesList();
					$this->logInfo("File uploaded and unpacked: ".$res["original_filename"]." (".$res["file"].")");
					$messages[]=Text::_("File uploaded and unpacked").": ".$res["original_filename"];
					$result = true;
				} else {
					$this->logError("Unzip error: ".$res["file"]);
					$messages[]=Text::_("Unzip error");
				}
			} elseif($ext == "xml") {
				$original_filename = Files::getName($res["original_filename"]);
				$original_filename=Translit::_($original_filename).".".$ext;
				if($original_filename){
					if(copy($res["file"], $this->work_dir.$original_filename)) {
						$files[]=$original_filename;
						$this->logInfo("File uploaded: ".$original_filename);
						$messages[]=Text::_("File uploaded").": ".$original_filename;
						$result = true;
					} else {
						$this->logError("Copying of files failed");
						$messages[]=Text::_("Copying of files failed");
					}
				} else {
					$this->logError("Copying of files failed (x02)");
					$messages[]=Text::_("Copying of files failed"). " (x02)";
				}
			} else{
				$this->logError("Unsupported file type");
				$messages[]=Text::_("Unsupported file type");
			}
			Files::delete($res["file"], true);
		}
		Files::removeFolder($this->tmp_dir, true);
		return $result;
	}
	public function autoUpload(&$messages) {
		$this->setAutoexchange(true);
		$result = false;
		$filename = Request::getSafe("filename");
		if($filename){
			$this->logDebug("Uploading ".$filename);
		} else {
			$this->logError("No filename in request");
			return $result;
		}
		if (strpos($filename, "import_files") !== false) {
			$slash = strrpos($filename, "/");
			$folder=substr($filename, 0, $slash);
			if($folder) Files::checkFolder($this->work_dir.$folder, true);
		}
		$dest = $this->work_dir.$filename;
		$data = file_get_contents("php://input");
		if ($data !== false) {
			$handle = fopen($dest, "ab");
			if(Files::getExt($dest)=="zip"){
				Session::getInstance()->saveUserVar("exchange1c_zip", true);
				Session::getInstance()->saveUserVar("exchange1c_last_zip_file", $filename);
			}
			if ($handle) {
				$filesize = fwrite($handle, $data);
				if ($filesize) {
					chmod($dest, 0664);
					// Not everybody wants message. IP waits only one string: success, so "Keep silent"
					// $messages[]="The file " . $filename . " has been successfully uploaded, filesize: " . $filesize;
					$this->logInfo("The file (or part) " . $filename . " has been successfully uploaded, filesize: " . $filesize);
					$result = true;
				} else {
					$messages[]="Error writing file: ".$filename;
					$this->logError("Error writing file: ".$dest);
				}
				fclose($handle);
			} else {
				$messages[]="Error while opening file for write: ".$filename;
				$this->logError("Error while opening file for write: ".$dest);
			}
		} else {
			$messages[]="Data is empty: ".$filename;
			$this->logError("Data is empty: ".$filename);
		}
		return $result;
	}
	public function autoUploadUnzip(&$messages) {
		$result = false;
		if(Util::toBool(Session::getInstance()->getUserVar("exchange1c_zip"))===true){
			$last_zip_file=Session::getInstance()->getUserVar("exchange1c_last_zip_file");
			if($last_zip_file){
				$source =  $this->work_dir.$last_zip_file;
				if(is_file($source)){
					if(Files::unzip($source, $this->work_dir)){
						// Not everybody wants message. IP waits only one string: success, so "Keep silent"
						// $messages[]="The file " . $last_zip_file . " has been successfully unzipped.";
						$this->logInfo("The file " . $last_zip_file . " has been successfully unzipped.");
						Session::getInstance()->saveUserVar("exchange1c_last_zip_file", "");
						$result = true;
					} else {
						$this->logError("Unzip error: ".$last_zip_file);
						$messages[]=Text::_("Unzip error");
					}
					Files::delete($source, true);
				} else {
					$this->logError("Zip file absent: ".$source);
					$messages[]=Text::_("Zip file absent").": ".$source;
				}
			} else {
				// May be trading system ignore zip flag and sends files unpacked
				$result = true;
			}
		} else {
			$result = true;
		}
		return $result;
	}
	public function processExport(&$messages, $autoexchange=false, $start_date="", $end_date=""){
		$this->setAutoexchange($autoexchange);
		$result = false;
		if($this->autoexchange){
			$this->start_date = Settings::getVar("1c_exchange_orders_success_date");
			if(!$this->start_date) $this->start_date = false;
			else $this->start_date = Date::toSQL($this->start_date);
			$this->end_date = Date::nowSQL();
			Settings::setVar("1c_exchange_orders_start_date", $this->start_date, "datetime");
			Settings::setVar("1c_exchange_orders_end_date", $this->end_date, "datetime");
		} else {
			$this->start_date = $start_date;
			$this->end_date = $end_date;
		}
		if($this->prepareOrders($messages)){
			$result["filename"]="orders.xml";
			/*
			// It seems nobody support zip on export, even 1C. Don't delete this comment, please.
			if(intval($this->getParam("1c_zip"))) {
				if(Files::zipSingle($this->work_dir."orders.xml", $this->work_dir."orders.zip")) $result["filename"]="orders.zip";
			} else $result["filename"]="orders.xml";
			*/
		}
		return $result;
	}
	public function processDownload($filename){
		Util::download($this->work_dir, $filename);
	}
	private function setChangesOnly($val){
		$this->changes_only = $val;
		$this->helper->setChangesOnly($val);
	}
	// @TODO Сделать разбивку. Пока что все сразу. (Lone Russian Comment)
	public function processImport($filename, &$messages, $autoexchange=false) {
		$this->setAutoexchange($autoexchange);
		$result = false;
		if($filename){
			$source = $this->work_dir.$filename;
			if(is_file($source)){
				$this->logInfo("Importing data from: ".$source);
				libxml_use_internal_errors(true);
				$xml = @simplexml_load_file($source);
				if (!$xml) {
					$this->logError("The file is not an XML standard: ".$filename);
					$this->logError(implode(CR_LF, libxml_get_errors()));
					$messages[]=Text::_("The file is not an XML standard").": ".$filename;
					return false;
				}
				$my_version=$this->getParam("1c_version");
				if($this->checkCMLVersionInXML($xml, $messages)){
					$this->logTitle("Data parsing started");
					if (BaseCML::checkNode($xml, "Metadata")) {
						// 2.04 - present, 2.05 - present
						$this->logTitle("Loading classifier");
						$classifier = $this->parseClassifier(BaseCML::getNode($xml, "Metadata"), $messages);
						if($classifier===false) {
							$this->logError("Classifier parse common error (".__CLASS__."->".__FUNCTION__.")");
							$messages[]=Text::_("Classifier parse common error");
							return false;
						}
						$this->logDebug("Saving classifier to session var (".__CLASS__."->".__FUNCTION__.")");
						Session::getInstance()->saveUserVar("exchange1c_classifier", json_encode($classifier));
						BaseCML::dropNode($xml, "Metadata");
					} else {
						// 2.09 - it seems that absent
						$classifier = array();
					}
					if (BaseCML::checkNode($xml, "Catalog")) {
						$this->logTitle("Loading catalog");
						if (!count($classifier)) {
							$this->logWarning("EMPTY CLASSIFIER !!! USING DEFAULT CMS SETTINGS !!!");
						}
						$goods = $this->parseCatalog(BaseCML::getNode($xml, "Catalog"), $classifier, $messages);
						if($goods===false) {
							$this->logError("Goods parse common error (".__CLASS__."->".__FUNCTION__.")");
							$messages[]=Text::_("Goods parse common error");
							return false;
						}
						BaseCML::dropNode($xml, "Catalog");
						if(is_array($goods) && count($goods)){
							// Let's process data read form XML
							if(!$this->processGoods($goods, $classifier, $messages)){
								$this->logError("Goods processing common error (".__CLASS__."->".__FUNCTION__.")");
								$messages[]=Text::_("Goods processing common error");
								return false;
							}
							if(!$this->changes_only && $this->getParam("1c_goods_disable_absent")) {
								$this->helper->disableAllGoods();
								if($this->helper->isError()){
									// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
									$messages[]=Text::_("Goods disabling common error");
									return false;
								}
							}
							$this->helper->addGoodsArr($goods, $this->getParam("1c_goods_create_new"), $this->getParam("1c_goods_update_found"), $this->getParam("1c_goods_enable"), $this->getParam("1c_goods_restore_deleted"));
							if($this->helper->isError()){
								// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
								$messages[]=Text::_("Goods adding common error");
								return false;
							}
							// Let's set goods for offers to classifier
							$classifier["goods"]=array();
							if(count($goods)){
								foreach($goods as $gk=>$gvar){
									$classifier["goods"][$gk]=$gvar["psid"];
								}
							}
//							$this->dump2screen($classifier["goods"],true);
							// Let's remember updated classifier
							$this->logDebug("Again saving classifier to session var (".__CLASS__."->".__FUNCTION__.")");
							Session::getInstance()->saveUserVar("exchange1c_classifier", json_encode($classifier));
						}
					}
					// @TODO May be here we insert goods and make return 2
					// $messages[]="Working"; return 2;
					if (BaseCML::checkNode($xml, "OffersList")) {
						$this->logTitle("Loading offers");
						if(!count($classifier)){
							$classifier = json_decode(Session::getInstance()->getUserVar("exchange1c_classifier"), true);
							if(is_null($classifier)) $classifier=array();
						}
						if(!isset($classifier["price_types"])){
							if (BaseCML::checkNode(BaseCML::getNode($xml, "OffersList"), "PriceTypes")) {
								$this->logInfo("Parsing price types in root of offers list...");
								$classifier["price_types"] = $this->parseClassifierPriceTypes(BaseCML::getNode(BaseCML::getNode($xml, "OffersList"), "PriceTypes"), $messages);
								if($classifier["price_types"]===false) return false;
								BaseCML::dropNode($xml, "PriceTypes");
							}
						}
						if(!isset($classifier["measures_by_code"])) $classifier["measures_by_code"]=array();
						$this->logTitle("Loading offers");
						$offers=$this->parseOffersPack(BaseCML::getNode($xml, "OffersList"), $classifier, $messages);
						if($offers===false) {
							$this->logError("Offers parse common error (".__CLASS__."->".__FUNCTION__.")");
							$messages[]=Text::_("Offers parse common error");
							return false;
						}
						if(is_array($offers) && count($offers)){
							$offers_data = $this->processOffers($offers, $classifier, $messages);
							if($offers_data===false){
								$this->logError("Offers processing common error (".__CLASS__."->".__FUNCTION__.")");
								$messages[]=Text::_("Offers processing common error");
								return false;
							}
							// Let's update goods and options
							if($this->offers_mode==1 || $this->offers_mode==2 || $this->offers_mode==3){
								$this->helper->addCharacteristics($offers_data);
								if($this->helper->isError()){
									$messages[]=Text::_("Offers adding common error");
									return false;
								}
							}
						}
						BaseCML::dropNode($xml, "OffersList");
					}

//					$messages[]="Still working !!!"; return 2;
					if (BaseCML::checkNode($xml, "testCML_Document")) {
						$this->logTitle("Loading docs");
						foreach (BaseCML::getNode($xml, "testCML_Document") as $doc) {
							if(!$this->parseOrder($doc, $messages)){
$this->dump2screen("IT'S MY STOP DUMP FOR SCREEN WITH FAILED PARSING ORDER IN ".__FUNCTION__, true);
								$messages[]=Text::_("Orders import common error");
								return false;
							}
						}
						BaseCML::dropNode($xml, "testCML_Document");
					}
					$this->logTitle("Not processed data");
					$this->logDebug(print_r($xml, true));
					$messages[]=Text::_("Operation complete").": ".$filename;
					$this->logTitle("Data parsing stopped");
					$result = true;
				}
			} else {
				$this->logError("File absent: ".$source);
				$messages[]=Text::_("File absent");
			}
		} else {
			$this->logError("Filename absent");
			$messages[]=Text::_("Filename absent");
		}

$this->dump2screen($messages);
$this->dump2screen("IT'S MY STOP DUMP FOR SCREEN", true);

		return $result;
	}
	private function checkCMLVersion($version){
// $this->dump2screen($version);
// $this->dump2screen($this->cml_version_in_min."-".$this->cml_version_in_max);
// $this->dump2screen(version_compare($version, $this->cml_version_in_min, "ge") && version_compare($version, $this->cml_version_in_max, "le"));
// $this->dump2screen(version_compare($version, $this->cml_version_in_min, "ge")); $this->dump2screen(version_compare($version, $this->cml_version_in_max, "le"));
		return (version_compare($version, $this->cml_version_in_min, "ge") && version_compare($version, $this->cml_version_in_max, "le"));
	}
	private function checkCMLVersionInXML($xml, &$messages) {
		if (BaseCML::checkAttr($xml, "SchemaVersion")) {
			$version=(string)BaseCML::getAttr($xml, "SchemaVersion");
			if(!$this->checkCMLVersion($version)){
				$this->logError("Incompatible version: ".$version.", waiting: ".$this->cml_version_in_min."-".$this->cml_version_in_max);
				$messages[]=Text::_("Incompatible version").": ".$version.", ".Text::_("waiting").": ".$this->cml_version_in_min."-".$this->cml_version_in_max;
				return false;
			}
			$this->logInfo("XML version: ".$version);
		} else {
			$this->logError("The file is not an XML standard");
			$messages[]=Text::_("The file is not an XML standard");
			return false;
		}
		return true;
	}
	public function processOrdersChangeStatus(&$messages, $autoexchange=false){
		$this->setAutoexchange($autoexchange);
		$result=false;
		$start_date = Settings::getVar("1c_exchange_orders_start_date");
		$end_date = Settings::getVar("1c_exchange_orders_end_date");
		if($start_date) $start_date = Date::toSQL($start_date);
		if($end_date) $end_date = Date::toSQL($end_date);
		if(Date::isSQLDate($end_date)){ // First time start_date may be empty
			$orders_model = $this->_module->getModel("orders");
			$orders = $orders_model->getOrdersByDate($start_date, $end_date, true, array($this->getParam("1c_order_status_for_export")));
			if(count($orders)){
				foreach($orders as $order){
					if(!$orders_model->setStatus($order->o_id, $this->getParam("1c_order_status_after_export"))) {
						$messages[]=Text::_("Failed to change status for order with ID")."=".$order->o_id;
						$this->logError("Failed to change status for order with ID=".$order->o_id);
						return $result; // Better to return here, as a result or "false"
					}
				}
				$result = true;
			} else {
				$result = true;
			}
			if($result && $this->autoexchange) Settings::setVar("1c_exchange_orders_success_date", Date::fromSQL($end_date), "datetime");
		} else {
			$messages[]=Text::_("Unknown exchage date for orders");
			$this->logError("Unknown exchage date for orders");
		}
		return $result;
	}
	private function prepareOrders(&$messages){
		$result=false; 
		$orders_model = $this->_module->getModel("orders");
		$orders = $orders_model->getOrdersByDate($this->start_date, $this->end_date, true, array($this->getParam("1c_order_status_for_export")));
		$docs = $this->helper->prepareOrders($orders);
//$this->dump2screen($this->start_date); $this->dump2screen($this->end_date);
		if($this->helper->isError()){
			// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
			$messages[]=Text::_("Orders preparing failed");
			return $result;
		}
		// Making file
		// If auto-exchange is full, then it will be erased when loading goods
		$this->logTitle("Preparing orders");
		$root = BaseCML::getRoot($this->cml_version_out);
		$xml = BaseCML::xmlData(BaseCML::array2xml($docs, $root));
		// @TODO May be only in autoexchange mode ???
		if($this->getParam("1c_order_convert_to_cp1251")){
			$xml = str_replace('utf-8', 'windows-1251', $xml);
			$xml = mb_convert_encoding($xml, 'cp1251', 'utf-8');
		}
		$dest = $this->work_dir."orders.xml";
		$orders_file = @fopen($dest, "w");
		if (!$orders_file) {
			$messages[]=Text::_("Unable to write file").": "."orders.xml";
			$this->logError("Unable to write file: " . $dest);
		} else {
			fwrite($orders_file, $xml);
			fclose($orders_file);
			$this->logDebug("Prepared file: ".$dest);
			$result=true;
		}
		return $result;
	}
	private function parseClassifierGoodsGroups($xml, &$data, &$messages) {
		if (!$xml) return;
		if(BaseCML::checkNode($xml, "Section")){
			foreach (BaseCML::getNode($xml, "Section") as $node) {
				$category["guid"]	= (string)BaseCML::getNode($node, "Id");
				$category["title"]	= (string)BaseCML::getNode($node, "Title");
				$category["code"]	= (string)BaseCML::getNode($node, "Code");
				$category["image"] = (string)BaseCML::getNode($node, "Picture"); // Not in standart, but useful
				if($category["image"]) $category["image_path"]=$this->work_dir.str_replace("/", DS, $category["image"]);
				else $category["image_path"]=false;
				$category["image_title"] = "";
				$category["description"] = (string)BaseCML::getNode($node, "Description");
				$category["properties"]	= $this->parseProperties(BaseCML::getNode($xml, "Properties"), $messages); // For future
				$category["children"]=array();
				if (BaseCML::checkNode($node, "Sections")) {
					$this->parseClassifierGoodsGroups(BaseCML::getNode($node, "Sections"), $category["children"], $messages);
				}
				$data[$category["guid"]]=$category;
			}
		}
		return true;
	}
	// May be merge with parseClassifierGoodsGroups ?
	private function parseClassifierGoodsCategories($xml, &$data, &$messages) {
		if (!$xml) return false;
		if(BaseCML::checkNode($xml, "testCML_Category")){
			foreach (BaseCML::getNode($xml, "testCML_Category") as $node) {
				$category["guid"] = (string)BaseCML::getNode($node, "Id");
				$category["title"] = (string)BaseCML::getNode($node, "Title");
				$category["code"] = (string)BaseCML::getNode($node, "Code");
				$category["image"] = (string)BaseCML::getNode($node, "Picture"); // Not in standart, but useful
				if($category["image"]) $category["image_path"]=$this->work_dir.str_replace("/", DS, $category["image"]);
				else $category["image_path"]=false;
				$category["image_title"] = "";
				$category["description"] = (string)BaseCML::getNode($node, "Description");
				$category["properties"]	= $this->parseProperties(BaseCML::getNode($xml, "Properties"), $messages); // For future
				$category["children"]=array();
				if (BaseCML::checkNode($node, "testCML_Categories")) {
					$this->parseClassifierGoodsGroups(BaseCML::getNode($node, "testCML_Categories"), $category["children"], $messages);
				}
				$data[$category["guid"]]=$category;
			}
		}
		return true;
	}
	private function parseAddress($xml, &$messages) {
		if (!$xml) return "";
		$data=Address::getTmpl(true);
		$locality=array(); $house=array();
		if(BaseCML::checkNode($xml, "testCML_Address_field")){
			foreach(BaseCML::getNode($xml, "testCML_Address_field") as $node){
				$_type = (string)BaseCML::getNode($node, "Type");
				$_value = (string)BaseCML::getNode($node, "Value");
				switch ($_type){
					case BaseCML::_("testCML_Address_post_code"): $data["zipcode"]=$_value; break;
					case BaseCML::_("testCML_Address_country"): $data["country"]=$_value; break;
					case BaseCML::_("testCML_Address_region"): $data["region"]=$_value; break;
					case BaseCML::_("testCML_Address_state"): $locality[1]=$_value; break;
					case BaseCML::_("testCML_Address_small_city"): $locality[2]=$_value; break;
					case BaseCML::_("testCML_Address_city"): $data["district"]=$_value; break;
					case BaseCML::_("testCML_Address_street"): $data["street"]=$_value; break;
					case BaseCML::_("testCML_Address_house"): $house[1]=$_value; break;
					case BaseCML::_("testCML_Address_building"): $house[2]=$_value; break;
					case BaseCML::_("testCML_Address_flat"): $data["apartment"]=$_value; break;
					default: break;
				}
			}
		}
		$data["locality"]=trim(implode(" ", $locality));
		$data["house"]=trim(implode(" ", $house));
		$data["fullinfo"]=(string)BaseCML::getNode($xml, "View");
		return $data;
	}
	private function parseBank($xml, &$messages) {
		if (!$xml) return "";
		return array(
				"corr_account"	=> (string)BaseCML::getNode($xml, "testCML_CorrespondentAccount"),
				"title"			=> (string)BaseCML::getNode($xml, "Title"),
				"bik"			=> (string)BaseCML::getNode($xml, "testCML_BIK"),
				"address"		=> $this->parseAddress(BaseCML::getNode($xml, "Address"), $messages)
		);
	}
	private function parseAccount($xml, &$messages) {
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "testCML_Account")){
			foreach (BaseCML::getNode($xml, "testCML_Account") as $obj) {
				$data[]	= array(
						"number" => (string)BaseCML::getNode($obj, "testCML_AccountNumber"),
						"bank"   => $this->parseBank(BaseCML::getNode($obj, "testCML_Bank"), $messages)
				);
			}
		}
		return $data;
	}
	private function parseMeasure($xml, &$classifier, &$messages){
		// Here we using $classifier["measures_by_code"], not $classifier["measures"] !!!
		if (!$xml) return "";
		$unit=array();
		$unit["code"]	= trim((string)BaseCML::getAttr($xml, "Code"));
		$unit["code"] = trim(mb_substr($unit["code"], 0, 10, DEF_CP), ". \t\n\r\0\x0B"); // Some trading systems don't use real codes, they use their fantasy codes, with dots, in national characters.
		$unit["short_name"]	= (string)BaseCML::getAttr($xml, "ShortName");
		$unit["full_name"]	= (string)BaseCML::getAttr($xml, "FullName"); // It is here for error description, when code is empty
		if(!$unit["short_name"]) $unit["short_name"] = (string)$xml;
		if(mb_strlen($unit["short_name"], DEF_CP) > mb_strlen($unit["full_name"], DEF_CP) && $unit["full_name"]) $unit["short_name"] = "";
		if($this->getParam("1c_measures_search")==2 && !$unit["short_name"]) $unit["short_name"]=$unit["full_name"];
		$search_value="";
// $this->dump2screen($unit);
		if($this->getParam("1c_measures_search")==1) $search_value=$unit["code"];
		elseif($this->getParam("1c_measures_search")==2) $search_value=$unit["short_name"];
		elseif($this->getParam("1c_measures_search")==3) $search_value=$unit["full_name"];
		if($search_value){
			if(array_key_exists($search_value, $classifier["measures_by_code"])) {
				if(isset($classifier["measures_by_code"][$search_value]["psid"]) && $classifier["measures_by_code"][$search_value]["psid"]){
					return $classifier["measures_by_code"][$search_value]["psid"];
				} else {
					$messages[]=Text::_("Measure exists without psid").": ".$search_value;
					$this->logError("Measure exists: ".$search_value." but psid absent");
					return false;
				}
			}
			$unit["international_abbreviation"]	= (string)BaseCML::getAttr($xml, "IntlAbbreviation");
			if(BaseCML::checkNode($xml, "testCML_Ratio")){
				$parent = BaseCML::getNode($xml, "testCML_Ratio");
				if(BaseCML::checkNode($parent, "Measure")){
					$unit["base_code"] = (string)BaseCML::getNode($parent, "Measure");
				} else {
					$unit["base_code"]=0;
				}
				if(BaseCML::checkNode($parent, "Rate")){
					$unit["base_coeff"] = (string)BaseCML::getNode($parent, "Rate");
				} else {
					$unit["base_coeff"] = 1;
				}
			}
			$unit["psid"]=$this->helper->addMeasureWoGUID($unit);
			if(!$unit["psid"]) {
				$this->logError("Error parsing measure: code=`".$unit["code"]."`, full_name=`".$unit["full_name"]."` (".__CLASS__."->".__FUNCTION__.")");
				return false;
			}
			$classifier["measures_by_code"][$search_value] = $unit;
		} else {
			$messages[]=Text::_("Measure search value empty").": ".BaseCML::_("FullName")."=`".$unit["full_name"]."`";
			$this->logError("Measure search value empty: ".BaseCML::_("FullName")."=`".$unit["full_name"]."`"." (".__CLASS__."->".__FUNCTION__.")");
			return false;
		}
		return $unit["psid"];
	}
	private function parseClassifierMeasures($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		foreach(BaseCML::getNode($xml, "testCML_UnitOfMeasurement") as $node){
			$unit = array();
			$unit["guid"]	= (string)BaseCML::getNode($node, "Id");
			$unit["deleted"] =(Util::toBool(BaseCML::getNode($node, "MarkedForDeletion"))===true ? true : false);
			$unit["short_name"]	= (string)BaseCML::getNode($node, "ShortName");
			$unit["code"]	= trim((string)BaseCML::getNode($node, "Code"));
			$unit["code"] = trim(mb_substr($unit["code"], 0, 10, DEF_CP));
			$unit["full_name"]	= (string)BaseCML::getNode($node, "FullName");
			$unit["international_abbreviation"]	= (string)BaseCML::getNode($node, "IntlAbbreviation");
			if($this->getParam("1c_measures_search")==2 && !$unit["short_name"]) $unit["short_name"]=$unit["full_name"];
			if(BaseCML::checkNode($xml, "testCML_Ratio")){
				$parent = BaseCML::getNode($xml, "testCML_Ratio");
				if(BaseCML::checkNode($parent, "Measure")){
					$unit["base_code"] = (string)BaseCML::getNode($parent, "Measure");
				} else {
					$unit["base_code"]=0;
				}
				if(BaseCML::checkNode($parent, "Rate")){
					$unit["base_coeff"] = (string)BaseCML::getNode($parent, "Rate");
				} else {
					$unit["base_coeff"] = 1;
				}
			}
			$unit["psid"]=$this->helper->addMeasure($unit);
			if(!$unit["psid"]) {
				$this->logError("Error parsing measure in classifier: ".$unit["guid"]." (".$unit["short_name"].")");
				/*
				if($this->helper->isError()){
					// $this->logError($this->helper->getErrorText(false));  // The message already has been logged in by helper
				}
				*/
				return false;
			}
			// May be by code ? Not by GUID ? But may be repeats !!!
			$data[$unit["guid"]]=$unit;
		}
		return $data;
	}
	private function getMeasureFromClassifier($code, &$classifier, &$messages){
		$psid=0;
		if(isset($classifier["measures"])) {
			foreach($classifier["measures"] as $guid=>$unit){
				if($code==$unit["code"]) {
					$psid=$unit["psid"];
					break;
				}
			}
		}
		if(!$psid){
			$messages[]=Text::_("Measure code absent in classifier");
			$this->logError("Measure code absent in classifier");
		}
		return $psid;
	}
	private function getFieldsFromProperties(&$properties, $field_name, $param_name){
		$data = array();
		$param_arr = explode(",", $this->getParam($param_name));
		if(is_array($properties)){
			foreach($properties as $pk=>$property){
				if(in_array($property["title"], $param_arr)){
					if(isset($property["choices"]) && is_array($property["choices"])){
						foreach($property["choices"] as $pc_key=>$property_choice){
							$data[$pc_key]=$property_choice;
						}
					} else {
						$data[$pk]=$property;
					}
					$properties[$pk]["skip_on_add"]	= true;
					$properties[$pk]["skip_reason"]	= $field_name;
				}
			}
		}
		return $data;
	}
	private function parsePropertyChoices($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "List")){
			foreach(BaseCML::getNode($xml, "List") as $node){
				$property_val=array();
				$property_val["guid"]	= (string)BaseCML::getNode($node, "ValueId");
				$property_val["title"]	= (string)BaseCML::getNode($node, "Value");
				$data[$property_val["guid"]]=$property_val;
			}
		}
		return $data;
	}
	private function parseProperties($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "testCML_PropertyNomenclatures")) $property_name="testCML_PropertyNomenclatures";
		else $property_name="Property";
		foreach(BaseCML::getNode($xml, $property_name) as $node){
			$property = array();
			$property["guid"]	= (string)BaseCML::getNode($node, "Id");
			$property["skip_on_add"]	= false;
			$property["skip_reason"]	= "";
			$property["title"]	= (string)BaseCML::getNode($node, "Title");
			$property["multiple"]	= (BaseCML::checkNode($node, "Multiple") ? Util::toBool(BaseCML::getNode($node, "Multiple")) : false);
			$property["required"]	= (BaseCML::checkNode($node, "testCML_Required") ? Util::toBool(BaseCML::getNode($node, "testCML_Required")) : false);
			$property["use4goods"]	= (BaseCML::checkNode($node, "testCML_forProducts") ? Util::toBool(BaseCML::getNode($node, "testCML_forProducts")) : false);
			$property["use4offers"]	= (BaseCML::checkNode($node, "testCML_forOffers") ? Util::toBool(BaseCML::getNode($node, "testCML_forOffers")) : false);
			$property["use4docs"]	= (BaseCML::checkNode($node, "testCML_forDocuments") ? Util::toBool(BaseCML::getNode($node, "testCML_forDocuments")) : false);
			$property["table"]	= ""; // May be check use4goods, use4offers, use4docs ?
			$property["type"]	= (string)BaseCML::getNode($node, "ValuesType");
			switch($property["type"]){
				case BaseCML::_("List"):
// $this->dump2screen($property["guid"]);
					$property["type"] = 5; // "list";
					if($property["multiple"]) $property["type"] = 8;  // "list"; // multiselect
					if (BaseCML::checkNode($node, "ChoiceValues")) {
						$property["choices"] = $this->parsePropertyChoices(BaseCML::getNode($node, "ChoiceValues"), $messages);
					} else {
						$property["choices"] = array();
					}
					break;
				case BaseCML::_("testCML_DateTime"):
					$property["type"] = 12; // "datetime";
					break;
				case BaseCML::_("Number"):
					$property["type"] = 3; // "number";
					break;
				case BaseCML::_("String"):
				default:
					$property["type"] = 1; // "string";
					break;
			}
			$data[$property["guid"]]=$property;
		}
		return $data;
	}
	private function parseAttributes($xml, &$classifier, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "ItemAttribute")) {
			foreach(BaseCML::getNode($xml, "ItemAttribute") as $node){
				$attr = array();
				$attr["guid"] = (string)BaseCML::getNode($node, "Id");
				$attr["title"] = (string)BaseCML::getNode($node, "Title");
				$attr["value"] = (string)BaseCML::getNode($node, "Value");
				
				$data[]=$attr;
			}
		} else {
			$this->logDebug("Tags `".BaseCML::_("ItemAttribute")."` absent in list (".__CLASS__."->".__FUNCTION__.")");
		}
		return $data;
	}
	private function parsePrices($xml, &$classifier, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "Price")) {
			foreach(BaseCML::getNode($xml, "Price") as $node){
				$price = array();
				$price["type_guid"] = (string)BaseCML::getNode($node, "PriceTypeId");
				if($price["type_guid"] && isset($classifier["price_types"]) && isset($classifier["price_types"][$price["type_guid"]]) && $classifier["price_types"][$price["type_guid"]]["compliant"]){
					$price["title"] = (string)BaseCML::getNode($node, "View");
					$price["value"] = floatval(str_replace(",", ".", strval((string)BaseCML::getNode($node, "PriceForOne"))));
					if(!$this->parseCurrency($node, $price, $messages)){
						$this->logError("Currency absent: ".$element["currency"]." (".__CLASS__."->".__FUNCTION__.")");
						return false;
					}
					$price["coeff"] = (string)BaseCML::getNode($node, "Rate");
					$price["measure_name"] = (string)BaseCML::getNode($node, "Measure");
					$price["compliant"] = $classifier["price_types"][$price["type_guid"]]["compliant"];
					$data[$price["compliant"]]=$price;
				}
			}
		}
		return $data;
	}
	private function parseGoodsTaxes($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "testCML_TaxRate")){
			foreach(BaseCML::getNode($xml, "testCML_TaxRate") as $node){
				$element = array();
				$element["title"]	= (string)BaseCML::getNode($node, "Title");
				if(BaseCML::checkNode($node, "testCML_Rate")) $element["value"]	= (string)BaseCML::getNode($node, "testCML_Rate");
				else  $element["value"] = false;
				$data[]=$element;
			}
		} else {
			$this->logDebug("Tags `".BaseCML::_("testCML_TaxRate")."` absent in list (".__CLASS__."->".__FUNCTION__.")");
		}
		return $data;
	}
	private function parseGoodsPropertiesValues($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "PropertyValues")){
			foreach(BaseCML::getNode($xml, "PropertyValues") as $node){
				$element = array();
				$element["guid"]	= (string)BaseCML::getNode($node, "Id");
				$element["value"]	= (string)BaseCML::getNode($node, "Value");
				$data[]=$element; 
			}
		} else {
			$this->logDebug("Tags `".BaseCML::_("PropertyValues")."` absent in list (".__CLASS__."->".__FUNCTION__.")");
		}
		return $data;
	}
	private function parseRequisites($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "testCML_TraitValue")){
			foreach(BaseCML::getNode($xml, "testCML_TraitValue") as $node){
				$element = array();
				$element["title"]	= (string)BaseCML::getNode($node, "Title");
				$element["value"]	= (string)BaseCML::getNode($node, "Value");
				$data[]=$element;
			}
		} else {
			$this->logDebug("Tags `".BaseCML::_("testCML_TraitValue")."` absent in list (".__CLASS__."->".__FUNCTION__.")");
		}
		return $data;
	}
	private function parseGoodsGoodsGoups($xml, &$messages){
		if (!$xml) return "";
		$data = array();
		if(BaseCML::checkNode($xml, "Id")){
			foreach(BaseCML::getNode($xml, "Id") as $node){
				$element = array();
				$element["guid"]	= (string)$node;
				if($element["guid"]) $data[]=$element;
				else $this->logDebug("Tag `".BaseCML::_("Id")."` empty in list (".__CLASS__."->".__FUNCTION__.")");
			}
		} else {
			$this->logDebug("Tags `".BaseCML::_("Id")."` absent in list (".__CLASS__."->".__FUNCTION__.")");
		}
		return $data;
	}
	private function parseCurrency($node, &$element, &$messages){
		// if (!$node) return false; // If not then default, not false
		if(BaseCML::checkNode($node, "Currency")){
			$element["currency"]	= (string)BaseCML::getNode($node, "Currency");
			$element["currency_id"] = Currency::getIdByCode($element["currency"]);
			if($element["currency_id"]==0){
				if($this->getParam("1c_currency_absent")==1){
					$messages[]=Text::_("Currency absent").": ".$element["currency"];
					return false;
				} else {
					$element["currency_id"] = catalogConfig::$default_currency;
					$element["currency"]	= Currency::getCode($element["currency_id"]);
				}
			}
		} else {
			$element["currency_id"] = catalogConfig::$default_currency;
			$element["currency"]	= Currency::getCode($element["currency_id"]);
		}
		return true;
	}
	private function parseClassifierPriceTypes($xml, &$messages){
		// Let's make price types accordance
		$price_types_accordance=array();
		for($price_index=1; $price_index<=5; $price_index++){
			$price_types_accordance["price_".$price_index] = $this->getParam("1c_price_".$price_index);
		}
		$price_types = array();
		// Check performed when call this function
		foreach(BaseCML::getNode($xml, "PriceType") as $node)  {
			$price_type=array();
			$price_type["guid"]	= (string)BaseCML::getNode($node, "Id");
			$price_type["title"]	= (string)BaseCML::getNode($node, "Title");
			if(!$this->parseCurrency($node, $price_type, $messages)){
				$this->logError("Currency absent: ".$element["currency"]." (".__CLASS__."->".__FUNCTION__.")");
				return false;
			}
			if(BaseCML::checkNode($node, "Code")){ // May be unuseful
				$price_type["code"]	= (string)BaseCML::getNode($node, "Code");
			} else {
				$price_type["code"]	= $price_type["currency"];
			}
			$key = array_search($price_type["title"], $price_types_accordance);
			if($key) $price_type["compliant"] = $key;
			else $price_type["compliant"] = false;
			$price_types[$price_type["guid"]]=$price_type;
			if(!$this->autoexchange && $price_type["compliant"]===false) $messages[]=Text::_("Price compliance absent").": ".$price_type["title"]." (".$price_type["guid"]."). ".Text::_("Will be skipped");
		}
		if($this->autoexchange){
			$found=false;
			foreach($price_types as $pk=>$pv){
				if($pv["compliant"]) $found=true;
			}
			if($found===false){
				$messages[]=Text::_("Compliant prices absent");
				return false;
			}
		}
		return $price_types;
	}
	private function parseOwner($xml, &$messages){
		if (!$xml) return "";
		return array(
				"guid"			=> (string)BaseCML::getNode($xml, "Id"),
				"title"			=> (string)BaseCML::getNode($xml, "Title"),
				"full_title"	=> (string)BaseCML::getNode($xml, "FullTitle"),
				"official_title"=> (string)BaseCML::getNode($xml, "OfficialTitle"),
				"inn"			=> (string)BaseCML::getNode($xml, "testCML_INN"),
				"kpp"			=> (string)BaseCML::getNode($xml, "testCML_KPP"),
				"okpo"			=> (string)BaseCML::getNode($xml, "testCML_OKPO"),
				"account"		=> $this->parseAccount(BaseCML::getNode($xml, "testCML_Accounts"), $messages),
				"post_address"	=> $this->parseAddress(BaseCML::getNode($xml, "Address"), $messages),
				"legal_address"	=> $this->parseAddress(BaseCML::getNode($xml, "testCML_LegalAddress"), $messages)
		);
	}
	private function parseClassifier($xml, &$messages){
		$data = array();
		$data["guid"] = (string)BaseCML::getNode($xml, "Id");
		$data["name"] = (string)BaseCML::getNode($xml, "Title");
		/* Owner (Vendor) */
		if (BaseCML::checkNode($xml, "Owner")) {
			$this->logInfo("Parsing owner (They call him OWNER. We call him VENDOR.)");
			$data["owner"] = $this->parseOwner(BaseCML::getNode($xml, "Owner"), $messages);
			$data["owner"]["psid"] = $this->helper->addVendor($data["owner"], $this->getParam("1c_vendors_create_new"), $this->getParam("1c_vendors_update_found"), $this->getParam("1c_vendors_enable"), $this->getParam("1c_vendors_restore_deleted"));
			if($this->helper->isError()){
				// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
				$messages[]=Text::_("Parsing owner failed");
				return false;
			}
			BaseCML::dropNode($xml, "Owner");
		}
		// Warehouses
		if (BaseCML::checkNode($xml, "Warehouses")) { // Absent in 2.04 - 2.07
			// We don't have warehouses
			$this->logInfo("Parsing warehouses in classifier (dummy)...");
			BaseCML::dropNode($xml, "Warehouses");
		}
		// PriceTypes
		if (BaseCML::checkNode($xml, "PriceTypes")) {
			$this->logInfo("Parsing price types in classifier...");
			$data["price_types"] = $this->parseClassifierPriceTypes(BaseCML::getNode($xml, "PriceTypes"), $messages);
			if($data["price_types"]===false) return false;
			BaseCML::dropNode($xml, "PriceTypes");
		}
		// Measures
		$data["measures_by_code"]=array();
		if (BaseCML::checkNode($xml, "UnitsOfMeasurement")) { // Absent in 2.04 - 2.07
			$this->logInfo("Parsing measures in classifier...");
			$data["measures"] = $this->parseClassifierMeasures(BaseCML::getNode($xml, "UnitsOfMeasurement"), $messages);
			if(!is_array($data["measures"])) return false;
			BaseCML::dropNode($xml, "UnitsOfMeasurement");
		}
		// Properties
		if (BaseCML::checkNode($xml, "Properties")) {
			// Just read
			$this->logInfo("Parsing properties in classifier...");
			$data["properties"]	= $this->parseProperties(BaseCML::getNode($xml, "Properties"), $messages);
			$data["manufacturers"] = $this->getFieldsFromProperties($data["properties"], "manufacturers", "1c_manufacturers_names");
			$data["weight_properties"] = $this->getFieldsFromProperties($data["properties"], "weight", "1c_goods_weight_names");
			$data["width_properties"] = $this->getFieldsFromProperties($data["properties"], "width", "1c_goods_width_names");
			$data["length_properties"] = $this->getFieldsFromProperties($data["properties"], "length", "1c_goods_length_names");
			$data["height_properties"] = $this->getFieldsFromProperties($data["properties"], "height", "1c_goods_height_names");
			if(count($data["manufacturers"])) $this->helper->addManufacturers($data["manufacturers"], $this->getParam("1c_manufacturers_create_new"), $this->getParam("1c_manufacturers_update_found"), $this->getParam("1c_manufacturers_enable"), $this->getParam("1c_manufacturers_restore_deleted"));
			// May be here place to add properties to database ?
			// @TODO Decide, already, finally, where to add
			if(count($data["properties"])) $this->helper->addProperties($data["properties"]);
			/*** ^^^^^^^^^^^^ Decide, already, finally, where to add ^^^^^^^^^^^^ ***/
			
			if($this->helper->isError()){
				// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
				$messages[]=Text::_("Adding properties failed");
				return false;
			}
			// Or may be later, while adding goods and offers ?
			BaseCML::dropNode($xml, "Properties");
		}
		// Goods groups
		$data["categories"] = array();
		if ($this->getParam("1c_groups_load")) {
			if (BaseCML::checkNode($xml, "testCML_Categories")) {
				$this->logInfo("Parsing categories in classifier...");
				$this->parseClassifierGoodsCategories(BaseCML::getNode($xml, "testCML_Categories"), $data["categories"], $messages);
				BaseCML::dropNode($xml, "testCML_Categories");
			} elseif (BaseCML::checkNode($xml, "Sections")) {
				$this->logInfo("Parsing sections in classifier...");
				$this->parseClassifierGoodsGroups(BaseCML::getNode($xml, "Sections"), $data["categories"], $messages);
				BaseCML::dropNode($xml, "Sections");
			}
			if(count($data["categories"])){
				// Let's insert groups !!!
				$this->helper->addGoodsGroups($data["categories"], $this->getParam("1c_groups_create_new"), $this->getParam("1c_groups_update_found"), $this->getParam("1c_groups_enable"), $this->getParam("1c_groups_restore_deleted"));
				if($this->helper->isError()){
					// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
					$messages[]=Text::_("Adding goods groups failed");
					return false;
				}
				// Let's transform from tree to line array
				$data["categories_line"] = $this->helper->transformTreeToLineArray($data["categories"]);
			}
		}
// $this->dump2screen($data);
		return $data;
	}
	private function checkCatalogOwner(&$xml, &$classifier, &$messages){
		if (!$xml) return false;
		if ( !count($classifier) || !(isset($classifier["owner"]) && count($classifier["owner"])) ) {
			if (BaseCML::checkNode($xml, "Owner")) {
				$this->logInfo("Parsing owner from catalog (They call him OWNER. We call him VENDOR.)");
				$classifier["owner"] = $this->parseOwner(BaseCML::getNode($xml, "Owner"), $messages);
				$classifier["owner"]["psid"] = $this->helper->addVendor($classifier["owner"], $this->getParam("1c_vendors_create_new"), $this->getParam("1c_vendors_update_found"), $this->getParam("1c_vendors_enable"), $this->getParam("1c_vendors_restore_deleted"));
				if($this->helper->isError()){
					// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
					$messages[]=Text::_("Parsing owner in catalog failed")."(catalog)";
					return false;
				}
				// If classifier absent then we get catalog id and title
				// Is it right ???
				$classifier["guid"] = (string)BaseCML::getNode($xml, "Id");
				$classifier["name"] = (string)BaseCML::getNode($xml, "Title");
				return true;
			}
		} else {
			$classifier_guid = (string)BaseCML::getNode($xml, "MetadataId"); // Classifier id
			if(!$classifier_guid || $classifier["guid"] !== $classifier_guid){
				$this->logError("The loading catalog does not match the classifier");
				$messages[]=Text::_("The loading catalog does not match the classifier");
				return false;
			}
			return true;
		}
		return false;
	}
	private function checkOffersPackOwner(&$xml, &$classifier, &$messages){
		if (!$xml) return false;
		if ( !count($classifier) || !(isset($classifier["owner"]) && count($classifier["owner"])) ) {
			if (BaseCML::checkNode($xml, "Owner")) {
				$this->logInfo("Parsing owner from offers pack (They call him OWNER. We call him VENDOR.)");
				$classifier["owner"] = $this->parseOwner(BaseCML::getNode($xml, "Owner"), $messages);
				$classifier["owner"]["psid"] = $this->helper->addVendor($classifier["owner"], $this->getParam("1c_vendors_create_new"), $this->getParam("1c_vendors_update_found"), $this->getParam("1c_vendors_enable"), $this->getParam("1c_vendors_restore_deleted"));
				if($this->helper->isError()){
					// $this->logError($this->helper->getErrorText(false)); // The message already has been logged in by helper
					$messages[]=Text::_("Parsing owner in offers pack failed")."(catalog)";
					return false;
				}
				// If classifier absent then we get offers pack id and title
				// Is it right ???
				$classifier["guid"] = (string)BaseCML::getNode($xml, "Id");
				$classifier["name"] = (string)BaseCML::getNode($xml, "Title");
				return true;
			}
		} else {
			$classifier_guid = (string)BaseCML::getNode($xml, "MetadataId"); // Classifier id
			$catalog_guid = (string)BaseCML::getNode($xml, "CatalogId"); // Catalog id
			if(!$classifier_guid || $classifier["guid"] !== $classifier_guid){
				$this->logError("The loading offers pack does not match the classifier");
				$messages[]=Text::_("The loading offers pack does not match the classifier");
				return false;
			}
			return true;
		}
		return false;
	}
	private function parseCatalog($xml, &$classifier, &$messages){
		if (!$xml) return false;
		if(is_null(BaseCML::getAttr($xml, "ChangesOnly", null))) return false;
		$this->setChangesOnly(Util::toBool((string)BaseCML::getAttr($xml, "ChangesOnly")));
		if(!$this->checkCatalogOwner($xml, $classifier, $messages)) return false;
		if(BaseCML::checkNode($xml, "Elements")){
			return $this->parseGoods(BaseCML::getNode($xml, "Elements"), $classifier, $messages);
		}
		return array();
	}
	private function parseGoods($xml, &$classifier, &$messages){
		$measures_in_classifier = isset($classifier["measures"]); // Measures were parsed while parsing classifier
		if (!$xml) return false;
		$data = array();
		if(BaseCML::checkNode($xml, "Element")){
			foreach (BaseCML::getNode($xml, "Element") as $node) {
				$goods=array();
				$goods["guid"]	= (string)BaseCML::getNode($node, "Id");
				$this->logDebug("Parsing goods with guid: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
				if(count(explode("#", $goods["guid"]))>1){
					$messages[]=Text::_("Unsupported feature with multiply `#` in GUID");
					$this->logError("Unsupported feature with multiply `#` in GUID: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
					return false;
				}
//$this->dump2screen($goods["guid"]);
				$goods["title"] = (string)BaseCML::getNode($node, "Title");
				$goods["full_title"] = "";
				$goods["type"] = 1;
				$goods["sku"] = (string)BaseCML::getNode($node, "Article");
				$goods["image"] = (string)BaseCML::getNode($node, "Picture");
				if($goods["image"]) $goods["image_path"]=$this->work_dir.str_replace("/", DS, $goods["image"]);
				else $goods["image_path"]=false;
				$goods["image_title"] = "";
				$goods["description"] = Text::toHtml((string)BaseCML::getNode($node, "Description"));
				if($measures_in_classifier){
					if(BaseCML::nodeHasChilds($node, "BaseUnit")){ // @TODO Unknown variant. But may be...
						$messages[]=Text::_("Multiply measures in BaseUnit");
						$this->logError("Multiply measures in BaseUnit (".__CLASS__."->".__FUNCTION__.")");
						return false;
					} else {
						// just code as string
						$goods["measure"] = $this->getMeasureFromClassifier((string)BaseCML::getNode($node, "BaseUnit"), $classifier, $messages);
						if(!$goods["measure"]) {
							return false;
						}
					}
				} else {
					// node
					$goods["measure"] = $this->parseMeasure(BaseCML::getNode($node, "BaseUnit"), $classifier, $messages);
					if(!$goods["measure"]) {
						return false;
					}
				}
				$goods["manufacturer"] = 0;
				$goods["df_fields"]=array();
				$goods["links"]=array();
				$goods["properties"]	= $this->parseGoodsPropertiesValues(BaseCML::getNode($node, "PropertiesValues"), $messages);
				$goods["taxes"]	= $this->parseGoodsTaxes(BaseCML::getNode($node, "testCML_TaxRates"), $messages);
				$goods["requisites"]	= $this->parseRequisites(BaseCML::getNode($node, "TraitsValues"), $messages);
				$goods["attributes"] = $this->parseAttributes(BaseCML::getNode($node, "ItemAttributes"), $classifier, $messages);
				if(!count($goods["requisites"])) $this->parseRequisites($node, $messages); // Different versions of CML
				$goods["goods_groups"] = $this->parseGoodsGoodsGoups(BaseCML::getNode($node, "Sections"), $messages);
				if(!count($goods["goods_groups"])) $this->parseGoodsGoodsGoups(BaseCML::getNode($node, "testCML_Categories"), $messages); // Different versions of CML
				if(BaseCML::checkNode($node, "MarkedForDeletion")){
					$goods["deleted"]=(Util::toBool(BaseCML::getNode($node, "MarkedForDeletion"))===true ? true : false);
				} elseif(BaseCML::checkAttr($node, "Status")){
					$goods["deleted"]=(BaseCML::getAttr($node, "Status")==BaseCML::_("Deleted") ? true : false);
				} else {
					$goods["deleted"]=false;
				}
				$data[$goods["guid"]]=$goods;
			}
		}
		return $data;
	}
	private function processGoods(&$data, &$classifier, &$messages){
		$goods_types=SpravStatic::getCKArray("goods_type");
		$manufacturers_is_loaded = (bool)(isset($classifier["manufacturers"]) && count($classifier["manufacturers"]));
		$weight_in_properties = (bool)(isset($classifier["weight_properties"]) && count($classifier["weight_properties"]));
		$width_in_properties = (bool)(isset($classifier["width_properties"]) && count($classifier["width_properties"]));
		$length_in_properties = (bool)(isset($classifier["length_properties"]) && count($classifier["length_properties"]));
		$height_in_properties = (bool)(isset($classifier["height_properties"]) && count($classifier["height_properties"]));
		$unique_sku=array();
		foreach($data as $gk=>&$goods){
			if($goods["sku"]){
				if(in_array($goods["sku"], $unique_sku)){
					if($this->getParam("1c_goods_duplicate_sku")==1){
						$messages[]=Text::_("Repeated SKU").": ".$goods["sku"];
						$this->logError("Repeated SKU: ".$goods["sku"]." for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						return false;
					} else {
						$this->logDebug("Repeated SKU: ".$goods["sku"]." for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						$goods["sku"]="";
					}
				} else {
					$unique_sku[]=$goods["sku"]; 
				}
			}
			if(is_array($goods["requisites"])){
				foreach ($goods["requisites"] as $rk=>&$requisite) {
					switch($requisite["title"]){
						case BaseCML::_("FileDescription"):
							$arr=explode("#", $requisite["value"]);
							if(count($arr)==2){
								if($arr[0]==$goods["image"]){
									$goods["image_title"] = $arr[1];
									$requisite="processed";
								}
							}
						break;
						case BaseCML::_("HTMLDescription"):
							if($this->getParam("1c_goods_html_descr")){
								$goods["description"]=$requisite["value"];
								$requisite="processed";
							}
						break;
						case BaseCML::_("testCML_Full_title"):
							$goods["full_title"]=$requisite["value"];
							$requisite="processed";
						break;
						case BaseCML::_("testCML_VidOfNomenclature"):
							$type=array_search($requisite["value"], $goods_types);
							if($type){
								$goods["type"] = $type;
								$requisite="processed";
							} else {
								$this->logDebug("Goods type present, but not found: ".$requisite["value"]);
							}
							break;
						default:
							$processed=false;
							if(!$manufacturers_is_loaded){
								$param_arr = explode(",", $this->getParam("1c_manufacturers_names"));
								if(in_array($requisite["title"], $param_arr)){
									if(!$requisite["value"]){
										$goods["manufacturer"] = 0;
										$this->logDebug("Manufacturer empty in requsite for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
									} else {
										$goods["manufacturer"] = $this->helper->addManufacturerWoGUID(array("title"=>$requisite["value"]), $this->getParam("1c_manufacturers_create_new"), $this->getParam("1c_manufacturers_update_found"), $this->getParam("1c_manufacturers_enable"), $this->getParam("1c_manufacturers_restore_deleted"));
										if(!$goods["manufacturer"]){
											$messages[]=Text::_("Error adding manufacturer from requisite").": ".$requisite["value"];
											$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
											$this->logError("Error adding manufacturer from requisite: ".$requisite["value"]." (".__CLASS__."->".__FUNCTION__.")");
											return false;
										}
									}
									$processed = true;
								}
							}
							if(!$weight_in_properties){
								$param_arr = explode(",", $this->getParam("1c_goods_weight_names"));
								if(in_array($requisite["title"], $param_arr)){
									$goods["weight"] = $requisite["value"];
									$processed = true;
								}
							}
							if(!$width_in_properties){
								$param_arr = explode(",", $this->getParam("1c_goods_width_names"));
								if(in_array($requisite["title"], $param_arr)){
									$goods["width"] = $requisite["value"];
									$processed = true;
								}
							}
							if(!$length_in_properties){
								$param_arr = explode(",", $this->getParam("1c_goods_length_names"));
								if(in_array($requisite["title"], $param_arr)){
									$goods["length"] = $requisite["value"];
									$processed = true;
								}
							}
							if(!$height_in_properties){
								$param_arr = explode(",", $this->getParam("1c_goods_height_names"));
								if(in_array($requisite["title"], $param_arr)){
									$goods["height"] = $requisite["value"];
									$processed = true;
								}
							}
							if(!$processed) $this->logDebug("Unsupported requisite: ".$requisite["title"]." (".__CLASS__."->".__FUNCTION__.")");
						break;
					}
				}
			}
			if(is_array($goods["properties"])){
				foreach($goods["properties"] as $gpk=>&$property){
					if(isset($classifier["properties"][$property["guid"]])){
						$prop = $classifier["properties"][$property["guid"]];
						if($prop["skip_on_add"] && $prop["skip_reason"]){
							switch($prop["skip_reason"]){
								case "manufacturers":
									if($prop["type"]==5){
										if(!$property["value"]){
											$goods["manufacturer"]=0;
											$this->logDebug("Manufacturer empty in property for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
										} elseif(isset($classifier["manufacturers"][$property["value"]]) && isset($classifier["manufacturers"][$property["value"]]["psid"])){
											$goods["manufacturer"]=$classifier["manufacturers"][$property["value"]]["psid"];
										} else {
											$messages[]=Text::_("Manufacturer absent in classifier");
											$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
											$this->logError("Manufacturer absent in classifier: ".$property["value"]." (".__CLASS__."->".__FUNCTION__.")");
											return false;
										}
									} else {
										// Manufacturer is a string ?????
										if(!$property["value"]){
											$goods["manufacturer"]=0;
											$this->logDebug("Manufacturer empty in property for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
										} else {
											$goods["manufacturer"] = $this->helper->addManufacturerWoGUID(array("title"=>$property["value"]), $this->getParam("1c_manufacturers_create_new"), $this->getParam("1c_manufacturers_update_found"), $this->getParam("1c_manufacturers_enable"), $this->getParam("1c_manufacturers_restore_deleted"));
											if(!$goods["manufacturer"]){
												$messages[]=Text::_("Error adding manufacturer from property").": ".$property["value"];
												$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
												$this->logError("Error adding manufacturer from property: ".$property["value"]." (".__CLASS__."->".__FUNCTION__.")");
												return false;
											}
										}
									}
									break;
								case "weight":
									$goods["weight"]=$property["value"];
									break;
								case "width":
									$goods["width"]=$property["value"];
									break;
								case "length":
									$goods["length"]=$property["value"];
									break;
								case "height":
									$goods["height"]=$property["value"];
									break;
								default :
									$messages[]=Text::_("Property not loaded with unknown reason");
									$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
									$this->logError("Property not loaded with unknown reason: ".$prop["skip_reason"]." (".$property["guid"].":".$property["value"].") (".__CLASS__."->".__FUNCTION__.")");
									return false;
									break;
							}
						} else {
							$df_field="df_".DBUtil::cleanNameForDB($property["guid"]);
							if($prop["type"]==5 && $property["value"]){
								if(isset($prop["choices"][$property["value"]]) && isset($prop["choices"][$property["value"]]["psid"])){
									$goods["df_fields"][$df_field] = $prop["choices"][$property["value"]]["psid"];
								} else {
									$messages[]=Text::_("Property choice absent in classifier");
									$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
									$this->logError("Property choice absent in classifier ".$property["guid"].":".$property["value"]." (".__CLASS__."->".__FUNCTION__.")");
									return false;
								}
							} else {
								$goods["df_fields"][$df_field] = $property["value"];
							}
						}
					} else {
						$messages[]=Text::_("Property absent in classifier").": ".$property["guid"];
						$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						$this->logError("Property absent in classifier: ".$property["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						return false;
					}
				}
			}
			if(is_array($goods["goods_groups"])){
				foreach($goods["goods_groups"] as $ggr=>&$group){
					if(isset($classifier["categories_line"][$group["guid"]])){
						$goods["links"][]=$classifier["categories_line"][$group["guid"]];
					} else {
						$messages[]=Text::_("Category absent in classifier").": ".$group["guid"];
						$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						$this->logError("Category absent in classifier: ".$group["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						return false;
					}
				}
			}
			if(is_array($goods["taxes"])){
				$goods["tax"] = $this->helper->addTaxWoGUID($goods["taxes"][0], $this->getParam("1c_manufacturers_create_new"), $this->getParam("1c_manufacturers_update_found"), $this->getParam("1c_manufacturers_enable"), $this->getParam("1c_manufacturers_restore_deleted"));
				if(!$goods["tax"]){
					$messages[]=Text::_("Error adding tax")." ".$goods["taxes"][0]["title"].": ".$goods["taxes"][0]["value"];
					$this->logDebug("Current goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
					$this->logError("Error adding tax ".$goods["taxes"][0]["title"].": ".$goods["taxes"][0]["value"]." (".__CLASS__."->".__FUNCTION__.")");
					return false;
				}
			} else {
				$this->logDebug("Taxes absent for goods: ".$goods["guid"]." (".__CLASS__."->".__FUNCTION__.")");
			}
			if(is_array($goods["attributes"])){
				// @TODO Atributes in goods, not in offers
			}
			if(isset($classifier["owner"]) && isset($classifier["owner"]["psid"]) && $classifier["owner"]["psid"]) $goods["vendor"]=$classifier["owner"]["psid"];
		}
// $this->dump2screen($data, true);
		return true;
	}
	private function parseOffers($xml, &$classifier, &$messages){
		$measures_in_classifier = isset($classifier["measures"]); // Measures were parsed while parsing classifier
		if (!$xml) return false;
		$data = array();
		if(BaseCML::checkNode($xml, "Offer")){
			foreach (BaseCML::getNode($xml, "Offer") as $node) {
				$offer=array();
				$offer["guid"] = (string)BaseCML::getNode($node, "Id");
				$this->logDebug("Parsing offer with guid: ".$offer["guid"]." (".__CLASS__."->".__FUNCTION__.")");
				$guid = explode("#", $offer["guid"]);
				$offer["goods_guid"] = $guid[0];
				$offer["feature_guid"] = isset($guid[1]) ? $guid[1] : "";
				$offer["title"] = (string)BaseCML::getNode($node, "Title");
				$offer["barcode"] = (string)BaseCML::getNode($node, "Barcode");
				$offer["quantity"] = floatval(str_replace(",", ".", strval((string)BaseCML::getNode($node, "Amount"))));
				if($measures_in_classifier){
					if(BaseCML::nodeHasChilds($node, "BaseUnit")){ // @TODO Unknown variant. But may be...
						$messages[]=Text::_("Multiply measures in BaseUnit");
						$this->logError("Multiply measures in BaseUnit (".__CLASS__."->".__FUNCTION__.")");
						return false;
					} else {
						// just code as string
						$offer["measure"] = $this->getMeasureFromClassifier((string)BaseCML::getNode($node, "BaseUnit"), $classifier, $messages);
						if(!$offer["measure"]) {
							return false;
						}
					}
				} else {
					// node
					$offer["measure"] = $this->parseMeasure(BaseCML::getNode($node, "BaseUnit"), $classifier, $messages);
					if(!$offer["measure"]) {
						return false;
					}
				}
				$offer["attributes"] = $this->parseAttributes(BaseCML::getNode($node, "ItemAttributes"), $classifier, $messages);
				$offer["prices"] = $this->parsePrices(BaseCML::getNode($node, "Prices"), $classifier, $messages);
// $this->dump2screen($offer);
// $this->dump2screen($node);
				if($offer["feature_guid"] && isset($data[$offer["goods_guid"]][$offer["feature_guid"]])){
					$messages[]=Text::_("Multiply offers in goods with same offer guid");
					$this->logError("Multiply offers in goods with same offer guid.".$offer["guid"]." (".__CLASS__."->".__FUNCTION__.")");
					return false;
				} elseif(!$offer["feature_guid"] && isset($data[$offer["goods_guid"]]) && count($data[$offer["goods_guid"]])){
					$messages[]=Text::_("Multiply offers in goods without offer guid");
					$this->logError("Multiply offers in goods without offer guid.".$offer["guid"]." (".__CLASS__."->".__FUNCTION__.")");
					return false;
				}
				if(BaseCML::checkNode($node, "MarkedForDeletion")){
					$offer["deleted"]=(Util::toBool(BaseCML::getNode($node, "MarkedForDeletion"))===true ? true : false);
				} elseif(BaseCML::checkAttr($node, "Status")){
					$offer["deleted"]=(BaseCML::getAttr($node, "Status")==BaseCML::_("Deleted") ? true : false);
				} else {
					$offer["deleted"]=false;
				}
				if($offer["feature_guid"]) $data[$offer["goods_guid"]][$offer["feature_guid"]]=$offer;
				else  $data[$offer["goods_guid"]]["EMPTY_FEATURE_GUID"]=$offer;
			}
		}
// $this->dump2screen($classifier, true);
		return $data;
	}
	private function getOptionValueNameFromOffer(&$offer){
		$offer_name = "";
		if($this->offers_mode==1){
			// Run through attributes
			if(is_array($offer["attributes"])){
				foreach($offer["attributes"] as $attribute){
					$offer_name.=($offer_name ? ", ": "").$attribute["title"].": ".$attribute["value"];
				}
			}
		} elseif($this->offers_mode==2){
			// Parse title
			$offer_name = $offer["title"];
// $this->dump2screen($offer_name);
			$l_bracket=mb_strrpos($offer_name, "(", 0, DEF_CP);
			if($l_bracket !==false){
				$offer_name=trim(mb_substr($offer_name, $l_bracket), "() \t\n\r\0\x0B");
			}
// $this->dump2screen($l_bracket);
// $this->dump2screen($offer_name);
		}
		return $offer_name;
	}
	private function getOptionPricesFromOffer(&$offer, &$current_goods, &$classifier, &$messages){
		if(!$current_goods->g_currency) return false;
		$data=array();
// $this->dump2screen($offer, true);
// $this->dump2screen($offer["quantity"]);
// $this->dump2screen($offer["prices"]);
		for($price_index=1; $price_index<=5; $price_index++){
			if(isset($offer["prices"]) && isset($offer["prices"]["price_".$price_index])){
				$offer_price = $offer["prices"]["price_".$price_index];
// $this->dump2screen($offer["prices"]["price_".$price_index], true);
				$data["price_".$price_index]=Currency::getInstance()->convert($offer_price["value"], $offer_price["currency_id"], $current_goods->g_currency);
			} else {
				$data["price_".$price_index]=0;
			}
		}
		return $data;
	}
	private function processOffers(&$data, &$classifier, &$messages){
		$new_offers = array();
		if($this->offers_mode==1 || $this->offers_mode==2 || $this->offers_mode==3){
			$goods_fields_list=array("g_id", "g_name", "g_currency", "g_measure", "g_extcode");
			$_goods=array();
			$check_options_arr=array();
			foreach($data as $goods_guid=>&$offers){
				$new_offers[$goods_guid]["base"]=array();
				$new_offers[$goods_guid]["options"]=array();
				//// NEED TO SET PSID !!!!
// $this->dump2screen("TOVAR with GUID = ".$goods_guid);
				if(count($offers)==0){
					// No offers for goods. Impossible, but let's check.
					$this->dump2screen("NOTHING !!!");
				} else {
					if(!array_key_exists($goods_guid, $_goods)) $_goods[$goods_guid]=$this->helper->getGoodsFields($goods_fields_list, "g_extcode", $goods_guid);
// $this->dump2screen($_goods[$goods_guid]);
					$check_options_arr[$goods_guid]=array();
					$check_options_arr[$goods_guid]["option_values"]=array();
					foreach($offers as $offer_key=>&$offer){
// $this->dump2screen("OFFER with GUID = ".$offer_key." [ ".$offer["feature_guid"]." ]");
						if($offer["deleted"]) {
							$offer["option_value"]=false;
							$offer["option_quantity"]=false;
							$offer["option_prices"]=false;
							$this->logWarning("Offer deleted. Skip offer: ".$offer["guid"]);
							continue;
						}
						if(is_object($_goods[$goods_guid])){
							$offer["option_value"]="";
							if($offer_key=="EMPTY_FEATURE_GUID"){
								// It's goods
							} else {
								$offer["option_value"] = $this->getOptionValueNameFromOffer($offer);
							}
							// Don't collect if not empty or if exists
							if($offer["option_value"] && in_array($offer["option_value"], $check_options_arr[$goods_guid]["option_values"])) $offer["option_value"]=false;
							// Don't collect if empty
							if($offer["option_value"]) $check_options_arr[$goods_guid]["option_values"][]=$offer["option_value"];
							// Warn and debug empty and false
							if($offer["option_value"]===false){
								$offer["option_quantity"]=false;
								$offer["option_prices"]=false;
								$this->logWarning("Repeated option. Skip offer: ".$offer["guid"]);
							} elseif(!$offer["option_value"] && $offer["option_value"]!==false) {
								$this->logDebug("Empty option for offer: ".$offer["guid"]);
							}
							if($offer["option_value"]!==false){
								// Recalc all features, empty option_value may be goods
								if(isset($offer["quantity"]) && $offer["quantity"]){
// $this->dump2screen($offer["quantity"]."=>".$offer["measure"]."=>".$_goods[$goods_guid]->g_measure);
									$offer["option_quantity"]=Measure::getInstance()->convert($offer["quantity"], $offer["measure"], $_goods[$goods_guid]->g_measure);
								} else {
									if(!isset($offer["option_quantity"])){
										$this->logWarning("Quantity absent for offer: ".$offer["guid"]);
									} else {
										$this->logDebug("Zero quantity for offer: ".$offer["guid"]);
									}
									$offer["option_quantity"]=0;
								}
								$offer["option_prices"]=$this->getOptionPricesFromOffer($offer, $_goods[$goods_guid], $classifier, $messages);
								if($offer["option_prices"]===false){
									$messages[]=Text::_("Error converting prices");
									$this->logError("Error converting prices: ".$offer["guid"]." (".__CLASS__."->".__FUNCTION__.")");
									return false;
								}
								if($offer_key=="EMPTY_FEATURE_GUID"){
									// It's goods
									$new_offers[$goods_guid]["base"]["quantity"]=$offer["option_quantity"];
									$new_offers[$goods_guid]["base"]["prices"]=$offer["option_prices"];
									$new_offers[$goods_guid]["base"]["psid"]=$_goods[$goods_guid]->psid;
								} else {
									if($this->offers_mode==1 || $this->offers_mode==2){
										$new_offers[$goods_guid]["options"][$offer["guid"]]=array();
										$new_offers[$goods_guid]["options"][$offer["guid"]]["offer_key"]=$offer_key;
										$new_offers[$goods_guid]["options"][$offer["guid"]]["name"]=$offer["option_value"];
										$new_offers[$goods_guid]["options"][$offer["guid"]]["quantity"]=$offer["option_quantity"];
										$new_offers[$goods_guid]["options"][$offer["guid"]]["prices"]=$offer["option_prices"];
									}
								}
							}
						} else {
							$offer["option_value"]=false;
							$offer["option_quantity"]=false;
							$offer["option_prices"]=false;
							$this->logWarning("Goods absent or deleted. Offer skipped: ".$offer["guid"]);
						}
// $this->dump2screen("option_value=".$offer["option_value"]);
// $this->dump2screen("option_quantity=".$offer["option_quantity"]);
// $this->dump2screen($offer["option_prices"]);
					} // foreach $offers
				} // count($offers)>0
// $this->dump2screen($offers);
			}
// $this->dump2screen($new_offers);
			// @FIXME What about common quantity as sum of offers quantities ???
		} else {
			$messages[]=Text::_("Unsupported offers mode");
			$this->logError("Unsupported offers mode (".__CLASS__."->".__FUNCTION__.")");
			return false;
		}
// $this->dump2screen($classifier["goods"]);
		return $new_offers;
	}
	private function parseOffersPack($xml, &$classifier, &$messages){
		if (!$xml) return false;
		if(is_null(BaseCML::getAttr($xml, "ChangesOnly", null))) return false;
		$this->setChangesOnly(Util::toBool((string)BaseCML::getAttr($xml, "ChangesOnly")));
		if(!$this->checkOffersPackOwner($xml, $classifier, $messages)) return false;
		if(BaseCML::checkNode($xml, "Offers")){
			return $this->parseOffers(BaseCML::getNode($xml, "Offers"), $classifier, $messages);
		}
		return array();
	}
	private function parseOrder($xml, &$messages){
		// @TODO IT'S DUMMY - parseOrder
		$data = array();
		if(BaseCML::checkNode($xml, "testCML_Contractors")){
			$contra=array();
			$_node = BaseCML::getNode($xml, "testCML_Contractors");
			if(BaseCML::checkNode($_node, "testCML_Contractor")){
				foreach (BaseCML::getNode($_node, "testCML_Contractor") as $node) {
					$element = array();
					$element["title"] = (string)BaseCML::getNode($node, "Title");
					$element["full_title"] = (string)BaseCML::getNode($node, "FullTitle");
					$element["role"] = (string)BaseCML::getNode($node, "testCML_Role");
					$contra[] = $element;
// $this->dump2screen($node);
				}
			}
			$data["contra"] = $contra;
		}
		if(BaseCML::checkNode($xml, "testCML_Taxes")){
			$taxes=array();
			$_node = BaseCML::getNode($xml, "testCML_Taxes");
			if(BaseCML::checkNode($_node, "Tax")){
				foreach (BaseCML::getNode($_node, "Tax") as $node) {
					$tax=array();
					$tax["title"]=(string)BaseCML::getNode($node, "Title");
					$tax["in_sum"]=(string)BaseCML::getNode($node, "InSum");
					$tax["summa"]=floatval(str_replace(",", ".", strval((string)BaseCML::getNode($node, "testCML_Sum"))));
					$taxes[] = $tax;
 // $this->dump2screen($node);
				}
			}
			$data["taxes"] = $taxes;
		}
		if(BaseCML::checkNode($xml, "Elements")){
			$goods=array();
			$_node = BaseCML::getNode($xml, "Elements");
			if(BaseCML::checkNode($_node, "Element")){
				foreach (BaseCML::getNode($_node, "Element") as $node) {
					$element = array();
					$element["guid"]	= (string)BaseCML::getNode($node, "Id");
					$this->logDebug("Parsing goods with guid: ".$element["guid"]." (".__CLASS__."->".__FUNCTION__.")");
					if(count(explode("#", $element["guid"]))>1){
						$messages[]=Text::_("Unsupported feature with multiply `#` in GUID");
						$this->logError("Unsupported feature with multiply `#` in GUID: ".$element["guid"]." (".__CLASS__."->".__FUNCTION__.")");
						return false;
					}
					$element["title"] = (string)BaseCML::getNode($node, "Title");
					$element["sku"] = (string)BaseCML::getNode($node, "Article");
					$goods[] = $element;
// $this->dump2screen($node);
				}
			}
			$data["goods"] = $goods;
		}
		if(BaseCML::checkNode($xml, "TraitsValues")){
			$data["requisites"] = $this->parseRequisites(BaseCML::getNode($xml, "TraitsValues"), $messages);
		}
// $this->dump2screen($data);
		return $this->processOrder($data, $messages);
	}
	private function processOrder(&$data, &$messages){
		// @TODO IT'S DUMMY - processOrder
		
		
		$this->dump2screen($data);
		return true;
	}
}
?>
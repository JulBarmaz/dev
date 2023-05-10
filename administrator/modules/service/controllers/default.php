<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceControllerdefault extends SpravController {

	private $start_path=BARMAZ_UF_PATH;
	private $start_link=BARMAZ_UF;
	private static $_hiddenfiles=array('.', '..',	'.svn', 'index.php', 'index.html',	'.htaccess','.htpasswd','web.config');
//	private $memory_limit=196;
	private $max_execution_time=300;
	
	// @TODO Придумать что-то другое когда будут не только виджеты
	public function showCachemanager(){
		$this->checkACL("viewCacheManager");
		$folders=Files::getFolders(PATH_CACHE, self::$_hiddenfiles, false);
		$view=$this->getView();
		$view->assign("folders", $folders);
		if ($folders)	{
			foreach($folders as $folder) {
				$files=Files::getFiles(PATH_CACHE.$folder["filename"].DS, self::$_hiddenfiles);
				$view->assign($folder["filename"]."_files", $files);
			}
		}
	}
	public function showUserfilter() {
		$view=$this->getView();
		$model=$this->getModel('cachemanager');
		$filtercount=$model->getFilterCount();
		$view->assign("filtercount", $filtercount);
	}
	public function clearUserFilter(){
		$view=$this->getView();
		$model=$this->getModel('cachemanager');
		$model->clearUserFilter(0);
		$this->setRedirect("index.php",Text::_('Cleared all filter records'));
	}

	public function deleteCacheFolder(){
		$this->checkACL("viewCacheManager");
		$folder=Request::getSafe("folder","");
		$files=Files::getFiles(PATH_CACHE.$folder.DS, self::$_hiddenfiles);
		if ($files){
			foreach ($files as $file){
				Files::delete(PATH_CACHE.$folder.DS.$file["filename"], true);
			}
		}
		$msg=Text::_("Operation complete");
		$this->setRedirect("index.php?module=service&view=cachemanager", $msg);
	}
	public function deleteCacheFile(){
		$this->checkACL("viewCacheManager");
		$folder=Request::getSafe("folder","");
		$file=Request::getSafe("file","");
		if (Files::delete(PATH_CACHE.$folder.DS.$file, true)) $msg=Text::_("Operation complete");	
		else $msg=Text::_("File delete error");
		$msg=Text::_("Operation complete");
		$this->setRedirect("index.php?module=service&view=cachemanager", $msg);
	}
	/* Резервное копирование таблиц БД */
	public function showDb(){
		$this->checkACL("viewDatabase");
		$backup_path = str_replace(DS.DS, DS, str_replace("/", DS, backofficeConfig::$backupPath).DS);
		if(!is_dir($backup_path)) $backup_path=PATH_FRONT.".backup".DS;
		$view=$this->getView();
		switch($this->get('layout')) {
			case "export":
				$tables = Exporter::getInstance()->getTablesList();
				$view->assign("tables",$tables);
				$view->assign("backup_path", $backup_path);
				$file="dbexport_".date("Ymd")."_".date("Gi").".sql";
				$view->assign("file", $file);
			break;
			default:
				$files=Files::getFiles($backup_path);
				$view->assign("files", $files);
				$view->assign("backup_path", $backup_path);
			break;
 		}
		
	}
	public function exportTables() {
		// FIXME говорят что phpmyadmin не поднимает
		$this->checkACL("viewDatabase");
		$backup_path = str_replace(DS.DS, DS, str_replace("/", DS, backofficeConfig::$backupPath).DS);
		if(!is_dir($backup_path)) $backup_path=PATH_FRONT.".backup".DS;
		$file=Request::getSafe("file","");
		$model=Module::getInstance()->getModel('db');
		$model->backupDB($backup_path,$file);
		$msg=Text::_("Operation complete");
		$this->setRedirect("index.php?module=service&view=db", $msg);
	}
	public function deleteBackup() {
		$this->checkACL("viewDatabase");
		$backup_path = str_replace(DS.DS, DS, str_replace("/", DS, backofficeConfig::$backupPath).DS);
		if(!is_dir($backup_path)) $backup_path=PATH_FRONT.".backup".DS;
		$file=Request::getSafe("file","");
		if (Files::delete($backup_path.$file, true)) $msg=Text::_("Operation complete");	
		else $msg=Text::_("File delete error");
		$this->setRedirect("index.php?module=service&view=db", $msg);
	}
	/* Медиа менеджер */
	public function startMediamanager($ajax=true) {
		if ($ajax) Portal::getInstance()->disableTemplate();
		$this->checkACL("viewMediamanager");
		$this->set('view','mediamanager',true);
		$model=Module::getInstance()->getModel();
		$return_element=Request::getSafe("ret_elem",""); // ид элемента в который вернуть значение
		$nfl=Request::getInt("nfl"); // 0 - относительный путь файла
		$folder=Session::getVar("mediamanager_last_folder");
		if(!is_null($folder)) $folder=$model->parsePath($folder,"",0);
		else $folder=$model->parsePath("","",0);
		$files=$model->getFiles($folder);
		if($files === false) $folder="";
		$files=$model->getFiles($folder);
		$view=$this->getView();
		$view->assign("folder",$folder);
		$view->assign("files",$files);
		$filepath = BARMAZ_UF_PATH.str_replace("/",DS,$folder);
		$view->assign("info_message","");
		$view->assign("error_class","");
		$view->assign("filepath",$filepath);
		$view->assign("is_ajax",$ajax);
		$view->assign("return_element",$return_element);
		$view->assign("nfl",$nfl);
		$view->renderFull ();
		$this->haltView();
	}
	public function showMediamanager() {
		$this->startMediamanager(false);
	}
	/* Медиа менеджер */
	public function startEditorFileBrowser($ajax=true) {
		if ($ajax) Portal::getInstance()->disableTemplate();
		if($this->checkACL("viewMediamanager",false)){
			$data=array();
			Event::raise("editor.mediamanager_params", array("file_browser_mode"=>"browser"), $data);
			if(array_key_exists("file_browser_path", $data) && is_file($data["file_browser_path"])){
				require_once($data["file_browser_path"]);
			}
		}
	}
	public function getLink() {
		$this->checkACL("viewMediamanager");
		Portal::getInstance()->disableTemplate();
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$ret_elem=Request::getSafe("ret_elem","");
		$folder=$model->parsePath($folder,$goto,$up);
		$file=Request::getSafe("file","");
		$nfl=Request::getInt("nfl"); // "0" - относительный путь файла, "-1" - относительный путь файла без userfiles
		if (Files::validFileName($file)) {
			$filelink=$model->getFileLink($folder,$file,$nfl);
			if(!$filelink) echo Text::_("Link error"); 
			else {
				if (!$ret_elem) {
					if ($nfl) echo "<b>".Text::_("Full file link").": </b>";
					else  echo "<b>".Text::_("Relative file link").": </b>";
				}
				echo $filelink;
			}
		} else echo Text::_("Wrong file name");
		Util::halt();
	}
	public function downloadBackup(){
		$this->checkACL("viewDatabase");
		$model=Module::getInstance()->getModel('db');
		$backup_path = str_replace(DS.DS, DS, str_replace("/", DS, backofficeConfig::$backupPath).DS);
		if(!is_dir($backup_path)) $backup_path=PATH_FRONT.".backup".DS;
		$file=Request::getSafe("file","");
		$href=Router::_("/administrator/index.php?module=service&view=db");
		if (Files::validFileName($file)) {
			$model->downloadFile($backup_path, $file, $href);
		} else {
			$this->setRedirect($href, Text::_("Wrong file name"));
		}
		
	}
	public function downloadFile() {
		$this->checkACL("viewMediamanager");
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$folder=$model->parsePath($folder,$goto,$up);
		$file=Request::getSafe("file","");
		$href="index.php?module=service&view=mediamanager&folder=".$folder;
		if (Files::validFileName($file)) {
			$model->downloadFile($folder,$file,$href);
		} else {
			$this->setRedirect($href, Text::_("Wrong file name"));
		}
	}
	public function showMailerlog(){
		$this->showData();
	}
	public function cleanMailerlog(){
		$this->checkACL("viewServiceMailerlog");
		$model=Module::getInstance()->getModel("mailerlog");
		$model->cleanMailerlog();
		$this->showMailerlog();
	}
/**************************************************/
/*************** Ajax media manager ***************/	
/**************************************************/
	public function ajaxgetMediaContent($info_message="",	$is_error=false) {
		Portal::getInstance()->disableTemplate();
		$this->checkACL("viewMediamanager");
		$this->set('view','mediamanager',true);
		$model=Module::getInstance()->getModel();
		$folder=Request::getSafe("folder","");
		$goto=Request::getSafe("go_to","");
		$up=Request::getInt("is_up",0);
		$folder=$model->parsePath($folder,$goto,$up);
		$files=$model->getFiles($folder);
		if($files === false) $folder="";
		$files=$model->getFiles($folder);
		$view=$this->getView();
		$view->assign("folder",$folder);
		$view->assign("files",$files);
		$filepath = $this->start_path.str_replace("/",DS,$folder);
		$view->assign("filepath",$filepath);
		$view->assign("info_message",$info_message);
		if ($is_error) $view->assign("error_class","error_message");
		else $view->assign("error_class","");
		$view->render();
	}
	public function ajaxdeleteMediaFolder() {
		$is_error=true;
		$this->checkACL("useMediamanager");
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$folder=$model->parsePath($folder,$goto,$up);
		$dir=Request::getSafe("oldfolder","");
		$href="index.php?module=service&view=mediamanager&folder=".$folder;
		$error_message="";
		if (Files::validFolderName($dir)) {
			$folder=str_replace("/",DS,$folder);
			if (!$dir) $error_message = Text::_("Folder delete error")." : ".$dir;
			else {
				if ($folder) $_dir = $this->start_path.DS.$folder.DS.$dir;
				else $_dir = $this->start_path.DS.$dir;
				if(is_dir($_dir)){
					if (Files::folderIsEmpty($_dir)) {
						if(rmdir($_dir)) {
							$error_message = Text::_("Folder deleted")." : ".$dir;
							$is_error=false;
						}	else $error_message = Text::_("Folder not deleted")." : ".$dir;
					} else $error_message = Text::_("Folder not empty")." : ".$dir;
				} else 	$error_message = Text::_("Folder not exists")." : ".$dir;
			}	
		} else {
			$error_message=Text::_("Wrong folder name");
		}
		$this->ajaxgetMediaContent($error_message,$is_error);
	}
	public function ajaxcreateMediaFolder() {
		$is_error=true;
		$this->checkACL("useMediamanager");
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$folder=$model->parsePath($folder,$goto,$up);
		$dir=Request::getSafe("newfolder","");
		if (Files::validFolderName($dir)) {
			$folder=str_replace("/",DS,$folder);
			if ($folder) $_dir = $this->start_path.DS.$folder.DS.$dir;
			else $_dir = $this->start_path.DS.$dir;
			if(!is_dir($_dir)){
				if(!mkdir($_dir, 0755, true)) $error_message=Text::_("Folder not created")." : ".$dir;
			} else 	$error_message=Text::_("Folder already exists")." : ".$dir;
			$error_message=Text::_("Folder created")." : ".$dir;
			$is_error=false;
		} else {
			$error_message=Text::_("Wrong folder name");
		}
		$this->ajaxgetMediaContent($error_message,$is_error);
	}
	public function ajaxdeleteMediaFile() {
		$is_error=true;
		$this->checkACL("useMediamanager");
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$folder=$model->parsePath($folder,$goto,$up);
		$file=Request::getSafe("file","");
		if (Files::validFileName($file)) {
			if (in_array($file, self::$_hiddenfiles)) Util::redirect($href, Text::_("File not exists")." : ".$file);
			$folder=str_replace("/",DS,$folder);
			if (!$file) $error_message = Text::_("File delete error")." : ".$file;
			if ($folder) $fullname = $this->start_path.DS.$folder.DS.$file;
			else $fullname = $this->start_path.DS.$file;
			if(is_file($fullname)){
				if(unlink($fullname)) {
					$error_message = Text::_("File deleted")." : ".$file;
					$is_error=false;
				}
				else $error_message = Text::_("File not deleted")." : ".$file;
			} else $error_message = Text::_("File not exists")." : ".$file;
		} else {
			$error_message = Text::_("Wrong file name");
		}
		$this->ajaxgetMediaContent($error_message,$is_error);
	}
	
	public function ajaxcreateMedia() {
		$is_error=true;
		$this->checkACL("useMediamanager");
		$model=Module::getInstance()->getModel('mediamanager');
		$folder=Request::getSafe("folder",""); $goto=""; $up=0;
		$folder=$model->parsePath($folder,$goto,$up);
		
		$fieldname="up_file";
		if(isset($_FILES[$fieldname]['tmp_name'])) {
			Debugger::getInstance()->message('File uploaded. Type:'.$_FILES[$fieldname]['type'].'.  Name: '.$_FILES[$fieldname]['name']);
			if(is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
				if ($folder) $upload_dir = $this->start_path.DS.$folder;
				else $upload_dir = $this->start_path;
				if (is_dir($upload_dir)) {
					$file=$_FILES[$fieldname]['name'];
					if (Files::validFileName($file)) {
						if (in_array($file, self::$_hiddenfiles)) $error_message = Text::_("File allready exists")." : ".$file;
						$fullpath=$upload_dir.DS.$file;
						if (file_exists($fullpath)) {
							$error_message = Text::_("File allready exists")." : ".$file;
						} else {
							$tmp_name=$_FILES[$fieldname]['tmp_name'];
							$flag = copy($tmp_name,$fullpath);
							if($flag) { $error_message = Text::_("File uploaded")." : ".$file; $is_error=false; }
							else  $error_message = Text::_("Error copying file")." : ".$file;
						}
					} else $error_message = Text::_("Wrong file name");
				} else $error_message = Text::_("Folder not exists");
			} else $error_message = Text::_("File upload failed");
		} else $error_message = Text::_("File upload failed");
		$this->ajaxgetMediaContent($error_message,$is_error);
	}
	
	/* updater */
	public function showUpdater() {
		$this->checkACL("viewUpdater");
		$model=Module::getInstance()->getModel();
		switch($this->get('layout')) {
			case "restructure":
				$version=intval(Settings::getVar("restruct_version"));
				$current_version = Portal::getInstance()->getVersionRevision();
				break;
			default:
				$version=$model->getVersion();
				if($version=='0') $current_version=$version;
				else $current_version=$model->checkNewVersion();
				break;
		}
		$view=$this->getView();
		$view->assign("current_version",$current_version);
		$view->assign("version",$version);
	}
	public function restructureDB(){
		$this->checkACL("viewUpdater");
		$model=Module::getInstance()->getModel();
		$version=intval(Settings::getVar("restruct_version"));
		$current_version = Portal::getInstance()->getVersionRevision();
		if ($current_version > $version) {
			$view=$this->getView();
			$view->setLayout('restructure_result');
			$view->assign('result', $model->proceedDbRestructure($current_version));
			$view->assign('packagelog', $model->getLog());
			$view->render();
		} else {
			$this->setRedirect("index.php", Text::_("Your database does not need restructuring"));
		}
	}
	public function downloadUpdates() {
//		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$this->checkACL("viewUpdater");
		$model=Module::getInstance()->getModel();
		$current_version=$model->checkNewVersion();
		$version=$model->getVersion();
		if ($current_version > $version) {
			$view=$this->getView();
			if ($model->processPackage()) $view->setLayout('confirm');
			else $view->setLayout('updated');
			$view->assign('packagelog',$model->getLog());
			$view->render();
		} else {
			$this->setRedirect("index.php", Text::_("Your version is up-to-date"));
		}
	}
	public function applyUpdates() {
//		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$this->checkACL("viewUpdater");
		$model=Module::getInstance()->getModel();
		$current_version=$model->checkNewVersion();
		if ($current_version > $model->getVersion()) {
			$result=$model->processUpdates($current_version);
			$view=$this->getView();
			$view->setLayout('updated');
			$view->assign('packagelog',$result);
			$view->render();
		} else {
			$this->setRedirect("index.php", Text::_("Your version is up-to-date"));
		}
	}
	// редактирование дополнительных меню
	public function showAddMenu(){
		// @todo - дописать  управление доп элементами меню из админки
		$this->showData();
	}
	public function showAclRules(){
		$model=$this->getModel();
		$view=$this->getView();
		$message=array();
		// проверка доступов к модулям системы для админа
		if($model->checkModulesAccess($message)){
			$model->checkModulesAcl($message);
		}
		$view->assign('message',$message);		
	}
	public function showImageprocessor(){
		$model=$this->getModel();
		$view=$this->getView();
		$message=array();
		$objects = $model->getImageObjects();
		$view->assign('objects', $objects);
		$view->assign('message', $message);
	}
	public function ajaxprocessResize(){
		$this->processResize(true);
	}
	/**
	 * отображение на форме интерфейса для выбора данных, подлежащих обработке 
	 */
	public function ajaxsetSelectField()
	{
		$data=Request::getSafe('lim');
		$html='';
		// разбираем ключ на составляющие catalog#goodsgroup#defl#ggr_thumb
		$confdata=explode("#",$data);
		// конструкция у нас жесткая  и все заранее прописано :	
		$module=$confdata[0];
		$view=$confdata[1];
		$layout=$confdata[2];
		$fld=$confdata[3];
		$s_name="wcp_sel";
		$html.='<div>';
		switch($module){
			case 'catalog':
				//switch($view){
				//	case 'goodsgroup':
				$html.="<span> Группа ,начиная с которой(включительно) будет выполняться 
				преобразование содержания поля ".$fld."</span>";
				$grpTree=Module::getHelper('groupsTree','catalog');
				$html.=$grpTree->getTreeHTML(0, "select", $s_name);
				// добавляем сведения по таблице и полю
				$html.=HTMLControls::renderHiddenField('wcp_module',$module);
				$html.=HTMLControls::renderHiddenField('wcp_view',$view);
				$html.=HTMLControls::renderHiddenField('wcp_layout',$layout);
				$html.=HTMLControls::renderHiddenField('wcp_fld',$fld); 
				break;
						
				//}
				break;
		}
		
		
		echo $html;
		
	}
	
	/**
	 * оптимизация изображений - преобразование в webp
	 * @param string $ajax
	 */
	public function startOptimize($ajax=false){
		
		/*
		 * // Image
$dir = 'img/countries/';
$name = 'brazil.png';
$newName = 'brazil.webp';

// Create and save
$img = imagecreatefrompng($dir . $name);
imagepalettetotruecolor($img);
imagealphablending($img, true);
imagesavealpha($img, true);
imagewebp($img, $dir . $newName, 100);
imagedestroy($img);
		 * */
		
	}
	
	public function processResize($ajax=false){
		$image_object = false;
		$error_message = "";
		$total_records = 0;
		$status = array("status"=>"unknown", "message"=>"", "error_message"=>"", "next_start"=>0);
		$records_per_pass=intval($this->getConfigVal("process_records_per_pass"));
		
		$start=Request::getInt("start", 1);
		$field_key=Request::getSafe("field_key");
		$enabled_only=Request::getInt("enabled_only");
		$skip_deleted=Request::getInt("skip_deleted");
		$force_from_source=Request::getInt("force_from_source");
		$i_understand=Request::getInt("i_understand");
		
		if($this->checkACL("useImageProcessor", false)){
			if(Request::getMethod()=="POST"){
				if($i_understand){
					$model=$this->getModel();
					$image_object=$model->getImageObject($field_key);
					if($image_object){
						$total_records=$model->getTotalRecords($image_object, $enabled_only, $skip_deleted);
						if($ajax){
							// proceed resize
							if($start > $total_records){
								$status["status"]="finished";
								$status["message"]=Text::_("Operation complete").". <br />".Text::_("Records processed").": ".$total_records;
							} else {
								$status_arr=$model->resizeRecords($image_object, $start, $records_per_pass, $enabled_only, $skip_deleted, $force_from_source);
								$status["status"]="processing";
								$status["next_start"]=$start + $records_per_pass;
								$status["message"]=Text::_("Processing")." ".$start." ".Text::_("from")." ".$total_records;
								foreach($status_arr as $sk=>$sv){
									if($sv["status"]=="ERROR"){
										$status["error_message"].=($status["error_message"] ? "<br />" : "").$sv["message"];
									}
								}
							}
						}
					} else {
						$error_message=Text::_("Error while fetching image object data");
					}
				} else {
					$error_message=Text::_("Operation aborted");
				}
			} else {
				$error_message=Text::_("Operation aborted");
			}
		} else {
			$error_message=Text::_("Accees denied");
		}
		if($ajax){
			if($error_message) {
				// JSON error response
				$status["status"]="aborted";
				$status["message"]=$error_message;
			}
			echo json_encode($status);
		} else {
			$view=$this->getView();
			$view->setLayout("resize");
			$view->assign("image_object", $image_object);
			$view->assign("start", $start);
			$view->assign("field_key", $field_key);
			$view->assign("enabled_only", $enabled_only);
			$view->assign("skip_deleted", $skip_deleted);
			$view->assign("force_from_source", $force_from_source);
			$view->assign("i_understand", $i_understand);
			$view->assign("total_records", $total_records);
			$view->assign("records_per_pass", $records_per_pass);
			$view->assign("error_message", $error_message);
			$view->render();
		}
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class installerControllerdefault extends Controller {
	private $redirectHref="index.php?module=installer";
	
	public function ajaxreadLicense(){
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$psid=Request::getInt("psid",0);
		$license=$model->getLicense($psid);
		if ($license) echo $license; else echo Text::_("License not found");
	}
	public function showInstall(){
		$this->checkACL("viewInstaller");
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$packages = $model->getInstalledPackages();
		$view = $this->getView();		
		$view->assign("packages",$packages);
	}
	public function installFromFile() {
		$redirectHref=$this->redirectHref;
		$fieldname="packageFile";
		$cur_update="install".time();
		$upload_dir = PATH_TMP;
		$folder			=	PATH_TMP.$cur_update;
		$fullpath		=	PATH_TMP.$cur_update.".zip";
		if (mkdir($folder, 0755, false)) {
			if(isset($_FILES[$fieldname]['tmp_name'])) {
				Debugger::getInstance()->message('File uploaded. Type:'.$_FILES[$fieldname]['type'].'.  Name: '.$_FILES[$fieldname]['name']);
				if(is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
					if (is_dir($upload_dir)) {
						$tmp_name=$_FILES[$fieldname]['tmp_name'];
						$flag = copy($tmp_name,$fullpath);
						if($flag) {
							Debugger::getInstance()->message('File downloaded to: '.$fullpath);
							if ($this->unzip($fullpath,PATH_TMP.$cur_update,true,$redirectHref)) {
								$this->install(PATH_TMP.$cur_update);
							}
						}	else  $this->setRedirect($redirectHref, Text::_("Error copying file")." : ".$fullpath);
					} else $this->setRedirect($redirectHref, Text::_("Upload dir not exists"));
				} else $this->setRedirect($redirectHref, Text::_("File upload failed"));
			} else $this->setRedirect($redirectHref, Text::_("File upload failed")." empty FILES array");
		} else $this->setRedirect($redirectHref,Text::_("Temporary folder is not writable"));
	}
	
	public function installFromURL() {
		$redirectHref=$this->redirectHref;
		$packageURL = Request::getSafe("packageURL","");
		if ($packageURL) {
			$cur_update="install".time();
			$filename=PATH_TMP.$cur_update.".zip";
			if (mkdir(PATH_TMP.$cur_update, 0755, false)) {
				$package = fopen($filename, 'wb');  
				$ch = curl_init();  
				curl_setopt($ch, CURLOPT_URL, $packageURL); 
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);  
				curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
				curl_setopt($ch, CURLOPT_FILE, $package);  
				curl_setopt($ch, CURLOPT_HEADER, 0); // allow redirects  
				curl_setopt($ch, CURLOPT_TIMEOUT, 7);  
				if(curl_exec($ch)) { // Загрузили 
					Debugger::getInstance()->message('File downloaded to: '.$filename);
					curl_close($ch); fclose($package);
					if ($this->unzip($filename,PATH_TMP.$cur_update,true,$redirectHref)) {
						$this->install(PATH_TMP.$cur_update);
					}				
				} else { 
					curl_close($ch);	fclose($package);
					$this->setRedirect($redirectHref,Text::_("Error at file loading with code")." : ".curl_error($ch));
				}
			} else $this->setRedirect($redirectHref,Text::_("Temporary folder is not writable"));
		} else $this->setRedirect($redirectHref,Text::_("URL is empty"));
	}
	public function installFromFolder() {
		$packagePath = Request::getSafe("packagePath","");
		$packagePath = str_replace("/", DS, $packagePath);
		if ($packagePath[0] == DS) { $packagePath = substr($packagePath,1);	} 
		$packagePath = PATH_TMP.$packagePath;
		$this->install($packagePath);
	}
	
	public function install($packagePath="") {
		$this->checkACL("viewInstaller");
		if ($packagePath&&is_dir($packagePath)){
			if (ComponentInstaller::getInstance()->install($packagePath)) {
				$result=true;
				$msg=Text::_("Successfully installed");
			}	else {
				$result=false;
				$msg=Text::_("Installation failed");
			}
			$view=$this->getView();
			$view->setLayout("results");
			$view->assign("log",ComponentInstaller::getInstance()->getLog());
			$view->assign("mode","install");
			$view->assign("msg",$msg);
			$view->assign("result",$result);
			if ($result) Files::removeFolder($packagePath, 1);
			$view->render();
		} else {
			$msg=Text::_("Path is absent");
			$this->setRedirect($this->redirectHref,$msg);
		}
	}
	public function unzip($fromfile,$tofolder,$delete=true,$redirectHref=""){
		$zip = new ZipArchive;
		if ($zip) {
			$res = $zip->open($fromfile);
			if ($res === TRUE) {
		    $zip->extractTo($tofolder);
	  	  $zip->close();
	  	  if ($delete) {
	  	  	unlink($fromfile);
	  	  }
				Debugger::getInstance()->message('File unzipped to : '.$tofolder);
				return true;
			} else {
				if ($redirectHref) $this->setRedirect($redirectHref, Text::_("Package open error")." : ".$res);
				else return false;
			}
		} else {
			if ($redirectHref) $this->setRedirect($redirectHref, Text::_("ZipArchive object error"));			
			else return false;
		}
	}
	public function uninstall() {
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$psid=Request::getInt("psid",0);
		$packageFile=PATH_TMP."uninstall".time().".xml";
		$xmlData=$model->getXMLData($psid);
		if ($xmlData) {
			if ($handle = fopen($packageFile, 'a')){
				if (fwrite($handle, $xmlData) != FALSE) {
					fclose($handle);
					if (ComponentInstaller::getInstance()->uninstall($packageFile,$psid)) {
						$result=true;
						$msg=Text::_("Successfully uninstalled");
					}	else {
						$result=false;
						$msg=Text::_("Uninstallation failed");
					}
					$view=$this->getView();
					$view->setLayout("results");
					$view->assign("log",ComponentInstaller::getInstance()->getLog());
					$view->assign("mode","install");
					$view->assign("msg",$msg);
					$view->assign("result",$result);
					unlink($packageFile);
					$view->render();
				} else {
					$msg=Text::_("Unable to write uninstall file");
					$this->setRedirect($this->redirectHref,$msg);
				}
			} else {
				$msg=Text::_("Unable to create uninstall file");
				$this->setRedirect($this->redirectHref,$msg);
			}
		} else {
			$msg=Text::_("Uninstall impossible");
			$this->setRedirect($this->redirectHref,$msg);
		}
	}
}
?>
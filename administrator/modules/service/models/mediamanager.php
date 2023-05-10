<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelmediamanager extends Model {
	private $start_path=BARMAZ_UF_PATH;
	private $_hiddenfiles=array('.','.svn','resources','index.php','index.html','.htaccess','.htpasswd','web.config');
	private $start_link=BARMAZ_UF;
	
	public function parsePath($folder,$goto,$up){
		$folder = str_replace(DS,"/",$folder);
		$folder_arr=preg_split('/[\/]/',$folder."/".$goto);
		if (count($folder_arr)>0) {
			foreach($folder_arr as $single_name) {
				$single_name=trim($single_name);
				if ((!$single_name)||($single_name=="..")||($single_name==".")) continue;
				$result[]=$single_name;
			}
			if ((isset($result))&&(count($result))) {
				if ($up) {
					$result = array_slice($result,0,count($result)-1);
					if (count($result)) {
						$new_folder=implode("/", $result);
					} else $new_folder="";
				} else {
					$new_folder=implode("/", $result);
				}
			} else $new_folder="";
		} else $new_folder="";
		Session::setVar("mediamanager_last_folder", $new_folder);
		return $new_folder;
	}

	public function getFiles($folder=""){
		$folder=str_replace("/",DS,$folder);
		$filepath = $this->start_path.DS.$folder;
		if (!is_dir($filepath)) return false;
		$folders=Files::getFolders($filepath,$this->_hiddenfiles);
		$files=Files::getFiles($filepath,$this->_hiddenfiles);
		if ($folders && $files) return array_merge($folders,$files);
		elseif ($folders) return $folders;
		elseif ($files) return $files;
		else return false;
	}
	public function getFileLink($folder,$file,$need_fulllink=1){
		if ($folder) $fulllink = "/".$folder."/".$file;
		else $fulllink = "/".$file;
		if ($need_fulllink!=-1){
			$fulllink=$this->start_link.Files::pathURLEncode($fulllink);
			if ($need_fulllink==0) $fulllink = parse_url($fulllink, PHP_URL_PATH);
		} else{
			$fulllink=Files::pathURLEncode($fulllink);
		}
		$folder=str_replace("/", DS, $folder);
		if ($folder) $fullname = $this->start_path.DS.$folder.DS.$file;
		else $fullname = $this->start_path.DS.$file;
		if ((file_exists($fullname))&&(is_file($fullname))) {
			return $fulllink;
		} else return false;
	}
	
	public function downloadFile($folder,$file,$href){
		if (in_array($file, $this->_hiddenfiles)) Util::redirect($href, Text::_("File not exists")." : ".$file);
		$folder=str_replace("/",DS,$folder);
		if ($folder) $fullname = $this->start_path.$folder.DS.$file;
		else $fullname = $this->start_path.$file;
		if ((file_exists($fullname))&&(is_file($fullname))) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header("Content-Length: ".filesize($fullname));
			ob_end_clean();
			readfile($fullname);
			Util::halt();
		}
	}
	
/************************************************************************************************/
/*	
	public function createFolder($folder,$dir,$href){
		$folder=str_replace("/",DS,$folder);
		if ($folder) $_dir = $this->start_path.DS.$folder.DS.$dir;
		else $_dir = $this->start_path.DS.$dir;
		if(!is_dir($_dir)){
	  	if(!mkdir($_dir, 0755, true)) Util::redirect($href, Text::_("Folder not created")." : ".$dir);
		} else 	Util::redirect($href, Text::_("Folder already exists")." : ".$dir);
		Util::redirect($href, Text::_("Folder created")." : ".$dir);
	}

	public function deleteFolder($folder,$dir,$href){
		$folder=str_replace("/",DS,$folder);
		if (!$dir) Util::redirect($href, Text::_("Folder delete error")." : ".$dir);
		if ($folder) $_dir = $this->start_path.DS.$folder.DS.$dir;
		else $_dir = $this->start_path.DS.$dir;
		if(is_dir($_dir)){
	  	if (Files::folderIsEmpty($_dir)) {
				if(rmdir($_dir)) Util::redirect($href, Text::_("Folder deleted")." : ".$dir);
		  	else Util::redirect($href, Text::_("Folder not deleted")." : ".$dir);
	  	} else Util::redirect($href, Text::_("Folder not empty")." : ".$dir);
		} else 	Util::redirect($href, Text::_("Folder not exists")." : ".$dir);
	}
	

	public function deleteFile($folder,$file,$href){
		if (in_array($file, $this->_hiddenfiles)) Util::redirect($href, Text::_("File not exists")." : ".$file);
		$folder=str_replace("/",DS,$folder);
		if (!$file) Util::redirect($href, Text::_("File delete error")." : ".$file);
		if ($folder) $fullname = $this->start_path.DS.$folder.DS.$file;
		else $fullname = $this->start_path.DS.$file;
		if(is_file($fullname)){
			if(unlink($fullname)) Util::redirect($href, Text::_("File deleted")." : ".$file);
	  	else Util::redirect($href, Text::_("File not deleted")." : ".$file);
		} else 	Util::redirect($href, Text::_("File not exists")." : ".$file);
	}

	public function uploadFile($folder,$href) {
		$fieldname="up_file";
		if(isset($_FILES[$fieldname]['tmp_name'])) {
			Debugger::getInstance()->message('File uploaded. Type:'.$_FILES[$fieldname]['type'].'.  Name: '.$_FILES[$fieldname]['name']);
			if(is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
				if ($folder) $upload_dir = $this->start_path.DS.$folder;
				else $upload_dir = $this->start_path;
				if (is_dir($upload_dir)) {
					$file=$_FILES[$fieldname]['name'];
					if (Files::validFileName($file)) {
						if (in_array($file, $this->_hiddenfiles)) Util::redirect($href, Text::_("File allready exists")." : ".$file);
						$fullpath=$upload_dir.DS.$file;
						if (file_exists($fullpath)) {
							Util::redirect($href, Text::_("File allready exists")." : ".$file);
						} else {
							$tmp_name=$_FILES[$fieldname]['tmp_name'];
							$flag = copy($tmp_name,$fullpath);
							if($flag) Util::redirect($href, Text::_("File uploaded")." : ".$file);
							else  Util::redirect($href, Text::_("Error copying file")." : ".$file);
						}
					} else Util::redirect($href, Text::_("Wrong file name"));
				} else Util::redirect($href, Text::_("Folder not exists"));
			} else Util::redirect($href, Text::_("File upload failed"));
		} else Util::redirect($href, Text::_("File upload failed"));
	}
*/
}

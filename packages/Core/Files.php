<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO


defined('_BARMAZ_VALID') or die("Access denied");

class Files {
	private static $_hiddenfiles=array('.svn', 'index.php', 'index.html', '.htaccess', '.htpasswd', 'web.config');
	private static $_image_types = array("gif","jpg","jpeg","png");
	private static $_flash_types = array("swf","swc");

	public static function checkUserDir($dir_name, $modname='') {
		$dir_name=str_replace("/", DS, $dir_name);
		if($modname) $new_dir=$modname.DS.$dir_name;
		else $new_dir=$dir_name;
		if (self::checkFolder(BARMAZ_UF_PATH.$new_dir,true)) {
			self::checkIndexFile(BARMAZ_UF_PATH.$new_dir, true);
			return BARMAZ_UF_PATH.$new_dir;
		}
		Portal::getInstance()->fatalError(Text::_("Folder not created")." : ".$new_dir);
	}
	public static function checkFolder($path, $force=false, $force_index=true){
		if(is_dir($path)) return true;
		else {
			if ($force) {
				if(!mkdir($path, 0755, true)) return false;
				if($force_index) self::checkIndexFile($path,true);
				return true;
			} else return false;
		}
	}
	public static function checkIndexFile($path, $create=false, $content=""){
		return self::touchFile($path, "index.html", $content, $create, false);
	}
	public static function touchFile($path, $file, $content="", $overwrite=true, $binary=true){
		$file=$path.DS.$file;
		if (!is_file($file) || $overwrite) {
			if (!$handle = fopen($file, 'w+'.($binary ? "b" : "t"))) return false;
			fwrite($handle, $content);
			fclose($handle);
			return true;
		} else return true;
	}
	/**
	 * Gets the extension of a file name
	 * @param string $file The file name
	 * @return string The file extension
	 */
	public static function getExt($file) {
		$dot = strrpos($file, '.') + 1;
		return substr($file, $dot);
	}
	public static function getName($file) {
		$dot = strrpos($file, '.');
		return substr($file, 0, $dot);
	}
	public static function makeSafe($file) {
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
		return preg_replace($regex, '', $file);
	}
	public static function check_type($type) 	{
		if(!siteConfig::$allowedType) return false;
		$type=str_replace("\"", "", $type);
		$allowed_keys=preg_split("/(\;)/",siteConfig::$allowedType);
		$allowed_types=Mime::getTypes($allowed_keys);
		if (is_array($allowed_types))	{
			foreach($allowed_types as $key=>$value)	{
				if($type==$value) return true;
			}
		}
		return false;
	}
	public static function uploadXML($fieldname, $filename, $dir_name, $unzip=false, $convert=false, $in='windows-1251', $out='UTF-8') {
		return self::uploadDataFile("XML", $fieldname, $filename, $dir_name, $unzip, $convert, $in, $out);
	}
	public static function uploadDataFile($type="CSV", $fieldname, $filename, $dir_name, $unzip=false, $convert=false, $in='windows-1251', $out='UTF-8') {
		$result=false;
		if(isset($_FILES[$fieldname]['tmp_name'])){
			if($_FILES[$fieldname]['error']==0){
				if(is_uploaded_file($_FILES[$fieldname]['tmp_name']) && self::check_type($_FILES[$fieldname]['type'])) {
					if (!is_dir($dir_name)) {
						Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name']." : Directory ".$dir_name." absent");
						return false;
					}
					$rash=strtolower(strrchr(strval($_FILES[$fieldname]['name']),'.')); //забираем раширение файла
					$m_link=strval($dir_name.$filename.$rash); //формируем ссылку на файл
					$data_link=strval($dir_name.$filename.".".strtolower($type)); //формируем ссылку на файл
					$files_name=$_FILES[$fieldname]['tmp_name'];
					if(copy($_FILES[$fieldname]['tmp_name'], $m_link)) {
						if ($rash==".zip" && $unzip){ // распаковываем
							if (is_file($data_link)) unlink($data_link);
							self::unzipSingle($m_link, $dir_name, $data_link);
						} else $data_link=$m_link;
						if($convert) $files_name=self::file_iconv($data_link, $in, $out);
						$result['file']=$data_link;
					} else {
						Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name']." : Error while copying.");
					}
				} else {
					Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name']." : Wrong type or not uploaded.");
				}
			} else {
				$fileUploadErrors = SpravStatic::getCKArray("upload_result_codes");
				Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name'].' :'.	(isset($fileUploadErrors[$_FILES[$fieldname]['error']]) ? $fileUploadErrors[$_FILES[$fieldname]['error']] : "Unknown error"));
			}
		} else {
			Debugger::getInstance()->warning('Error while uploading file. Field '.$fieldname.' absent.');
		}
		return $result;
	}
	public static function zipSingle($source, $dest, $delete=true){
		$zip = new ZipArchive;
		if ($zip) {
			if ($zip->open($dest, ZipArchive::CREATE) === TRUE) {
				$dest_filename=pathinfo($source, PATHINFO_BASENAME);
				$result = $zip->addFile($source, $dest_filename);
				$zip->close();
				if ($delete) unlink($source);
				Debugger::getInstance()->message('File zipped to : '.$dest);
				if($result && is_file($dest)) return true;
				return false;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public static function unzipSingle($fromfile,$tofolder,$tofile,$delete=true){
		$zip = new ZipArchive;
		if ($zip) {
			$res = $zip->open($fromfile);
			if ($res === TRUE) {
				$filedata=$zip->statIndex(0);
				$tmpfile=$tofolder.DS.$filedata["name"];
				$zip->extractTo($tofolder);
				$zip->close();
				copy($tmpfile,$tofile); unlink($tmpfile);
				if ($delete) {
					unlink($fromfile);
				}
				Debugger::getInstance()->message('File unzipped to : '.$tofolder);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public static function unzip($fromfile,$tofolder,$delete=true){
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
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Транилитерирует русские имена файлов
	 */
	public static function clearName($filename)	{
		return Translit::_($filename);
	}
	public static function uploadTempFile($fieldname, $dir_name="", $max_size=0) {
		$result=false;
		if(isset($_FILES[$fieldname]['tmp_name'])){
			if($_FILES[$fieldname]['error']==0){
				if(!self::check_type($_FILES[$fieldname]['type'])) { // проверка на тип файла
					Debugger::getInstance()->warning('File not saved. Type not correct:'.$_FILES[$fieldname]['type']);
					return false;
				}
				if($max_size && $_FILES[$fieldname]['size']>(1024*1024*$max_size+1)) { // проверка на размер
					Debugger::getInstance()->warning('Too large file:'.$_FILES[$fieldname]['size']);
					return false;
				}
				if(is_uploaded_file($_FILES[$fieldname]['tmp_name']) && self::check_type($_FILES[$fieldname]['type']))	{
					$filename=md5($dir_name.basename($_FILES[$fieldname]['name']).$_FILES[$fieldname]['size'].Session::getInstance()->getKey());
					$md5dir=substr($filename,0,3);
					$upload_dir=PATH_TMP.($dir_name ? $dir_name.DS : "").substr($filename,0,3);
					if(self::checkFolder($upload_dir, true, true)) {
						$rash=strtolower(strrchr(strval($_FILES[$fieldname]['name']),'.')); //забираем раширение файла
						$new_file_path=strval($upload_dir.DS.$filename.$rash); //формируем ссылку на файл
						$tmp_name=$_FILES[$fieldname]['tmp_name'];
						if(copy($tmp_name, $new_file_path)) {
							$result['file']=$new_file_path;
							$result['link']="";
							$result['filename']=$filename.$rash;
							$result["original_filename"]=$_FILES[$fieldname]['name'];
						}
						Debugger::getInstance()->message('File uploaded. Type:'.$_FILES[$fieldname]['type'].'.  download name: '.$_FILES[$fieldname]['name']);
					}
				}
			} else {
				$fileUploadErrors = SpravStatic::getCKArray("upload_result_codes");
				Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name'].' :'.	(isset($fileUploadErrors[$_FILES[$fieldname]['error']]) ? $fileUploadErrors[$_FILES[$fieldname]['error']] : "Unknown error"));
			}
		} else {
			Debugger::getInstance()->warning('Error while uploading file. Field '.$fieldname.' absent.');
		}
		return $result;
	}
	public static function uploadUserFile($fieldname, $modname, $sub_dir, $max_size=0) {
		if (!$modname) return false;
		$result=false;
		if(isset($_FILES[$fieldname]['tmp_name'])){
			if($_FILES[$fieldname]['error']==0){ // есть файл и загружен без ошибок
				if(seoConfig::$saveOriginalImageName) {
					$rash=strrchr(strval($_FILES[$fieldname]['name']), '.');//забираем раширение файла
					$filen=substr(basename($_FILES[$fieldname]['name'], $rash), 0, 40);
					if(!self::validFolderName($filen))
					$filen=self::clearName($filen);
					$filename=$filen."_".User::getInstance()->getID()."_".time();
				} else
					$filename=md5(time().User::getInstance()->getID().$modname.basename($_FILES[$fieldname]['tmp_name'])).time();
				$md5dir=substr($filename,0,3);
				$sub_dir=$sub_dir."/".$md5dir;
				if(!self::check_type($_FILES[$fieldname]['type'])) { // проверка на тип файла
					Debugger::getInstance()->warning('File not saved. Type not correct:'.$_FILES[$fieldname]['type']);
					return false;
				}
				if($max_size && $_FILES[$fieldname]['size']>(1024*1024*$max_size+1)) { // проверка на размер
					Debugger::getInstance()->warning('Too large file:'.$_FILES[$fieldname]['size']);
					return false;
				}
				if(is_uploaded_file($_FILES[$fieldname]['tmp_name']) && self::check_type($_FILES[$fieldname]['type']))	{
					$upload_dir=self::checkUserDir($sub_dir, $modname);
					$rash=strtolower(strrchr(strval($_FILES[$fieldname]['name']),'.')); //забираем раширение файла
					$new_file_path=strval($upload_dir.DS.$filename.$rash); //формируем ссылку на файл
					$tmp_name=$_FILES[$fieldname]['tmp_name'];
					if(copy($tmp_name, $new_file_path)) {
						$download_link=strval(BARMAZ_UF.'/'.$modname.'/'.$sub_dir.'/'.$filename.$rash);
						$result['file']=$new_file_path;
						$result['link']=$download_link;
						$result['filename']=$filename.$rash;
					}
					Debugger::getInstance()->message('File uploaded. Type:'.$_FILES[$fieldname]['type'].'.  download name: '.$_FILES[$fieldname]['name'].' linkname in system '.$download_link);
				}
			} else {
				$fileUploadErrors = SpravStatic::getCKArray("upload_result_codes");
				Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name'].' :'.	(isset($fileUploadErrors[$_FILES[$fieldname]['error']]) ? $fileUploadErrors[$_FILES[$fieldname]['error']] : "Unknown error"));
			}
		} else {
			Debugger::getInstance()->warning('Error while uploading file. Field '.$fieldname.' absent.');
		}
		return $result;
	}
	public static function uploadUserFileArray($fieldname, $modname, $_dir) {
		$result=array();
		if (!$modname) return $result;
		// есть файл и загружен без ошибок
		if(isset($_FILES[$fieldname]['tmp_name']) && is_array($_FILES[$fieldname]['tmp_name'])){
			$countf=count($_FILES[$fieldname]['error']);
			for ($i = 0; $i < $countf; $i++) {
				$result['file'][$i]="";
				$result['link'][$i]="";
				$result['filename'][$i]="";
				if($_FILES[$fieldname]['error'][$i]==0){
					$filename=md5(time().User::getInstance()->getID().$modname.basename($_FILES[$fieldname]['tmp_name'][$i])).$i.time();
					$md5dir=substr($filename,0,3);
					$sub_dir=$_dir."/".$md5dir;
					if(!self::check_type($_FILES[$fieldname]['type'][$i]))	{
						Debugger::getInstance()->warning('File not saved. Type not correct:'.$_FILES[$fieldname]['type'][$i]);
					} else {
						if(is_uploaded_file($_FILES[$fieldname]['tmp_name'][$i]) && self::check_type($_FILES[$fieldname]['type'][$i]))	{
							$upload_dir=self::checkUserDir($sub_dir, $modname);
							$rash=strtolower(strrchr(strval($_FILES[$fieldname]['name'][$i]),'.'));//забираем раширение файла
							$new_file_path=strval($upload_dir.DS.$filename.$rash);//формируем ссылку на файл
							$tmp_name=$_FILES[$fieldname]['tmp_name'][$i];
							if(copy($tmp_name, $new_file_path)) {
								$download_link=strval(BARMAZ_UF.'/'.$modname.'/'.$sub_dir.'/'.$filename.$rash);
								$result['file'][$i]=$new_file_path;
								$result['link'][$i]=$download_link;
								$result['filename'][$i]=$filename.$rash;
								Debugger::getInstance()->message('file uploaded. Type:'.$_FILES[$fieldname]['type'][$i].'.  download name: '.$_FILES[$fieldname]['name'][$i].' linkname in system '.$download_link);
							} else {
								Debugger::getInstance()->warning('File not saved. Copy error:'.$_FILES[$fieldname]['name'][$i]);
							}
						}
					}
				} else {
					$fileUploadErrors = SpravStatic::getCKArray("upload_result_codes");
					Debugger::getInstance()->warning('Error while uploading file '.$_FILES[$fieldname]['name'][$i].' :'.	(isset($fileUploadErrors[$_FILES[$fieldname]['error'][$i]]) ? $fileUploadErrors[$_FILES[$fieldname]['error'][$i]] : "Unknown error"));
				}
			}
		} else {
			Debugger::getInstance()->warning('Error while uploading file. Field '.$fieldname.' absent.');
		}
		return $result;
	}

	public static function splitAppendix($filename, $path=false){
		if (mb_strlen($filename)>3) {
			$appendix = mb_substr($filename,0,3);
			if ($path) return $appendix.DS.$filename;
			else return $appendix."/".$filename;
		} else return "";
	}
	public static function getAppendix($filename){
		if (mb_strlen($filename)>3) {
			$appendix = mb_substr($filename,0,3);
			return $appendix;
		}
		return "";
	}
	public static function detect_encoding($string) {
		static $list = array('utf-8', 'windows-1251');
		foreach ($list as $item) {
			$sample = iconv($item, $item, $string);
			if (md5($sample) == md5($string))	return $item;
		}
		return null;
	}
	/**
	 * Удаляет файл по URL или по относительной ссылке
	 * @param string $filename
	 */
	public static function delete($filename, $abs_path=false) {
		if (!$abs_path){
			$filename=str_replace(Portal::getURI(),"",$filename);
			$filename=SITE_PATH . DS .str_replace("/",DS,$filename);
		}
		if(is_file($filename)) {
			return unlink($filename);
		}
		return false;
	}
	/**
	 *
	 * Конвертирование полученного текстового файла в другую кодировку
	 * @param string $file - путь к файлу
	 * @param string $in  - входящая кодировка
	 * @param string $out - исходящая кодировка
	 */
	public static function file_iconv($filename,$in,$out) {
		$text=file_get_contents($filename);
		if($text===false) {
			echo "Failed open file (".$filename.")";  exit;
		}	else	{
			$somecontent = iconv($in,$out,$text);
			//echo $somecontent;
			// Вначале давайте убедимся, что файл существует и доступен для записи.
			if (is_writable($filename)) {
				if (!$handle = fopen($filename, 'wb')) {
					echo "2.Не могу открыть файл ($filename)";  exit;
				}
				// Записываем $somecontent в наш открытый файл.
				if (fwrite($handle, $somecontent) === FALSE) {
					echo "Не могу произвести запись в файл ($filename)";
					exit;
				}
				fclose($handle);
				return $filename;

			} else {
				echo "Файл $filename недоступен для записи";
			}
		}
	}
	public static function getFiles($folder="", $hiddenfiles="", $fullinfo=true){
		if (!$hiddenfiles) $hiddenfiles = self::$_hiddenfiles;
		clearstatcache();
		if(!is_dir($folder)) return false;
		$allfiles = scandir($folder);
		foreach($allfiles as $file) {
			if (in_array($file, $hiddenfiles))  continue;
			if(is_file($folder.DS.$file)) {
				$files[$file]["folder"] = 0;
				$files[$file]["filename"] = $file;
				if ($fullinfo){
					$files[$file]["filesize"] = round(filesize($folder.DS.$file)/1024,3);
					$files[$file]["filedate"] = date ("d.m.Y H:i", filemtime($folder.DS.$file));
				}
			}
		}
		if (!isset($files)) return array();
		if (count($files)) ksort($files, SORT_REGULAR);
		return $files;
	}
	public static function folderIsEmpty($folder){
		if (!$folder) return false;
		$hiddenfiles = array(	'.', '..');
		$folders=Files::getFolders($folder, $hiddenfiles,false);
		if (($folders)&&(count($folders)>0)) return false;
		else {
			$files=Files::getFiles($folder, $hiddenfiles,false);
			if (($files)&&(count($files)>0)) return false;
		}
		return true;
	}
	public static function getFolders($folder="", $hiddenfiles="", $fullinfo=true){
		if (!$hiddenfiles) $hiddenfiles = self::$_hiddenfiles;
		clearstatcache();
		if(!is_dir($folder)) return false;
		$allfiles = scandir($folder);
		foreach($allfiles as $file) {
			if (in_array($file, $hiddenfiles))  continue;
			if(is_dir($folder.DS.$file)) {
				$folders[$file]["folder"] = 1;
				$folders[$file]["filename"] = $file;
				if ($fullinfo){
					$folders[$file]["filesize"] = round(filesize($folder.DS.$file)/1024,3);
					$folders[$file]["filedate"] = date ("d.m.Y H:i", filemtime($folder.DS.$file));
				}
			}
		}
		if (isset($folders)>0)	ksort($folders, SORT_REGULAR); else return false;
		return $folders;
	}
	public static function validFolderName($name){
		if (preg_match("/^[\sa-zA-Z0-9_!~=+-]+$/",$name)) return true; else return false;
	}
	
	public static function validFileName($name){
		if (preg_match("/^[\sa-zA-Z0-9_!~=+-]+\.[a-zA-Z0-9_!~]+$/",$name)) return true; else return false;
	}
	public static function getImageInfo($file = null) {
		if(!is_file($file)) return false;
		/* собака для подавления внутренних сообщений об ошибках getimagesize */
		if(!$filesize = filesize($file) or !$data = @getimagesize($file)) return false;
		$extensions = array(1 => 'gif',		2 => 'jpg',		3 => 'png',		4 => 'swf',
				5 => 'psd',		6 => 'bmp',		7 => 'tiff',	8 => 'tiff',
				9 => 'jpc',		10 => 'jp2',	11 => 'jpx',  12 => 'jb2',
				13 => 'swc',	14 => 'iff',	15 => 'wbmp',	16 => 'xbmp');
		$result = array('width'			=>	$data[0],
				'height'			=>	$data[1],
				'type'				=>	$data[2],
				'extension'	=>	$extensions[$data[2]],
				'size'				=>	$filesize,
				'mime'				=>	$data['mime']);
		return $result;
	}
	public static function isImage($name){
		$arr=self::getImageInfo($name);
		if($arr){
			if (in_array($arr['extension'],self::$_image_types)) return true;	 else return false;
		}
		else return false;
	}
	public static function isFlash($name){
		$arr=self::getImageInfo($name);
		if($arr){
			if (in_array($arr['extension'],self::$_flash_types)) return true;	 else return false;
		}
		else return false;
	}
	public static function getFolderContent($path,$subpath,$hiddenfiles,$recursive,&$result,&$counter){
		if(!$counter) clearstatcache();
		if (!$hiddenfiles) $hiddenfiles = self::$_hiddenfiles;
		if($subpath) $fullpath=$path.DS.$subpath.DS;
		else $fullpath=$path.DS;
		$folders=self::getFolders($fullpath, $hiddenfiles, !$recursive);
		if (($folders)&&(count($folders)>0)){
			foreach($folders as $folder) {
				$counter++;
				$result[$counter]["subpath"]=$subpath;
				$result[$counter]["filename"]=$folder["filename"];
				$result[$counter]["folder"]=$folder["folder"];
				if($recursive) {
					if($subpath) $new_subpath=$subpath.DS.$folder["filename"];
					else $new_subpath=$folder["filename"];
					self::getFolderContent($path,$new_subpath,$hiddenfiles,$recursive,$result,$counter);
				}
			}
		}
		$files=self::getFiles($fullpath, $hiddenfiles, !$recursive);
		if(($files)&&(count($files)>0)) {
			foreach($files as $file) {
				$counter++;
				$result[$counter]["subpath"]=$subpath;
				$result[$counter]["filename"]=$file["filename"];
				$result[$counter]["folder"]=$file["folder"];
			}
		}
		return true;
	}
	public static function copyFolder($src,$dest,$force=false){
		$src = rtrim($src, DS);
		$dest = rtrim($dest, DS);
		if (!is_dir($src)) return false;
		if (is_dir($dest) && !$force) return false;
		if (!self::checkFolder($dest,$force)) return false;
		$files=scandir($src);
		if($files && count($files)){
			foreach ($files as $file)	{
				$sfid = $src.DS.$file;
				$dfid = $dest.DS.$file;
				switch (filetype($sfid)) {
					case 'dir':
						if ($file != '.' && $file != '..')	{
							$ret = self::copyFolder($sfid, $dfid, $force);
							if (!$ret) return false;
						}
						break;
					case 'file':
						if (!@copy($sfid, $dfid)) return false;
						break;
				}
			}
		}
		return true;
	}
	public static function canWrite($path,$is_folder,$nested=0){
		$nested++;
		if ($nested>20) return false;
		$path = rtrim($path, DS);
		if($is_folder) {
			if(is_dir($path)){
				if (is_writable($path)) $result=true;
				else $result=false;
			} else {
				$parent = dirname($path);
				if($parent==$path) $result=false;
				else $result=self::canWrite($parent, 1,$nested);
			}
		} else {
			if(file_exists($path)){
				if (is_writable($path)) $result=true;
				else $result=false;
			} else {
				$parent = dirname($path);
				if($parent==$path) $result=false;
				else $result=self::canWrite($parent, 1,$nested);
			}
		}
		$nested--;
		return $result;
	}
	public static function removeFolder($src,$force=0) {
		$src = rtrim($src, DS);
		$result = true;
		$files=scandir($src);
		foreach ($files as $file)	{
			$sfid = $src.DS.$file;
			switch (filetype($sfid)) {
				case 'dir':
					if ($file != '.' && $file != '..')	{
						if ($force)	$result = self::removeFolder($sfid, $force);
						else $result = false;
					}
					break;
				case 'file':
					if ($force) $result = unlink($sfid);
					else $result = false;
					break;
			}
		}
		if ($result) $result=rmdir($src);
		return $result;
	}
	public static function mime_content_type($filename) {
		if (function_exists('mime_content_type')){
			return mime_content_type($filename);
		}	else {
			if (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$type = finfo_file($finfo, $filename);
				finfo_close($finfo);
				return $type;
			} else {
				return false;
			}
		}
	}
	public static function getMemoryRequired4image($imagePath, $params=array()) {
		if(!count($params)) $params = getimagesize($imagePath);
		if(isset($params['channels'])) {
			return round( ( $params[0] * $params[1] * $params['bits'] * $params['channels'] / 8 + Pow(2, 16) ) * 1.65 );
		} else {
			return round( $params[0] * $params[1] * $params['bits'] );
		}
	}
	public static function resizeImage($_src, $_dest, $_width=100, $_height=100){
		$params = getimagesize($_src);
		// if (extension_loaded('imagick')) echo 'Supported';
		// else echo 'Not supported';
		if ( $params[0] > $_width || $params[1] > $_height ) {
			$memory_usage = memory_get_usage(true);
			$memory_required = self::getMemoryRequired4image($_src, $params);
			$memory_limit = ini_get('memory_limit');
			$memory_limit_int = intval($memory_limit)*1024*1024;
			// Util::pre("memory_usage=".$memory_usage); Util::pre("memory_required=".$memory_required); Util::pre("memory_limit=".$memory_limit); Util::pre("memory_limit=".$memory_limit_int);
			if($memory_limit_int - $memory_usage - $memory_required < 0) {
				Debugger::getInstance()->warning("Not enough memory in ".__CLASS__.":".__FUNCTION__." (memory_limit=".number_format($memory_limit_int, 0, ".", " ").", memory_usage=".number_format($memory_usage, 0, ".", " ").", memory_required=".number_format($memory_required, 0, ".", " ").")");
				return false;
			}
			switch ( $params[2] ) {
				case IMAGETYPE_GIF: $source = imagecreatefromgif($_src); break;
				case IMAGETYPE_JPEG: $source = imagecreatefromjpeg($_src); break;
				case IMAGETYPE_PNG: $source = imagecreatefrompng($_src); break;
				default: 
					Debugger::getInstance()->warning("Usupported format in ".__CLASS__.":".__FUNCTION__." (".$params[2].")");
					return false; 
				break;
			}
			$k_width = $params[0] / $_width;
			$k_height = $params[1] / $_height;
			if($k_width > $k_height) $k_size = $k_width; // коэффициент по ширине больше
			else $k_size = $k_height; //коэффициент по высоте больше
			$resource_width = floor($params[0] / $k_size);
			$resource_height = floor($params[1] / $k_size);
			$resource = imagecreatetruecolor($resource_width, $resource_height);
			if($params[2]==3) {
				imagealphablending($resource, false);
				imagesavealpha($resource, true);
			}
			imagecopyresampled($resource, $source, 0, 0, 0, 0, $resource_width, $resource_height, $params[0], $params[1]);
			imagedestroy($source);
			switch ( $params[2] ) {
				case IMAGETYPE_GIF: return imagegif($resource, $_dest); break;
				case IMAGETYPE_JPEG: return imagejpeg($resource, $_dest); break;
				case IMAGETYPE_PNG: return imagepng($resource, $_dest); break;
				default:
					Debugger::getInstance()->warning("Usupported format in ".__CLASS__.":".__FUNCTION__." (".$params[2].")");
					return false; 
				break;
			}
		} else {
			if($_src != $_dest) {
				if(!Files::checkFolder(pathinfo($_dest, PATHINFO_DIRNAME), true)) {
					Debugger::getInstance()->warning("Check folder error in ".__CLASS__.":".__FUNCTION__." (".pathinfo($_dest, PATHINFO_DIRNAME).")");
					return false;
				}
				if(!copy($_src, $_dest)) {
					Debugger::getInstance()->warning("Copy error in ".__CLASS__.":".__FUNCTION__." (".$_src." : ".$_dest.")");
					return false;
				}
			}
		}
		return true;
	}
	/*
	public static function resizeImage($_src,$_dest,$_width=100,$_height=100){
		$params = getimagesize($_src);
		switch ( $params[2] ) {
			case 1: $source = imagecreatefromgif($_src); break;
			case 2: $source = imagecreatefromjpeg($_src); break;
			case 3: $source = imagecreatefrompng($_src); break;
			default: return false; break;
		}
		if ( $params[0]>$_width || $params[1]>$_height ) {
			$k_width=$params[0]/$_width;
			$k_height=$params[1]/$_height;
			if($k_width>$k_height) $k_size = $k_width; // коэффициент по ширине больше
			else $k_size = $k_height; //коэффициент по высоте больше
			$resource_width = floor($params[0] / $k_size);
			$resource_height = floor($params[1] / $k_size);
			$resource = imagecreatetruecolor($resource_width, $resource_height);
			if($params[2]==3) {
				imagealphablending( $resource, false );
				imagesavealpha( $resource, true );
			}
			imagecopyresampled($resource, $source, 0, 0, 0, 0, $resource_width, $resource_height, $params[0], $params[1]);
		} else $resource = $source;
		switch ( $params[2] ) {
			case 1: return imagegif($resource, $_dest); break;
			case 2: return imagejpeg($resource, $_dest); break;
			case 3: return imagepng($resource, $_dest); break;
			default: return false; break;
		}
	}
	*/
	public static function pathURLEncode($path,$islink=true){
		if (!$islink) $path = str_replace(DS,"/",$path);
		$path_arr=preg_split('/[\/]/',$path);
		if(count($path_arr)){
			$str=array();
			foreach($path_arr as $key=>$val){
				$str[]=rawurlencode($val);
			}
			$path=implode("/",$str);
		}
		if (!$islink) $path = str_replace("/",DS,$path);
		return $path;
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModeldb extends Model {
	private $_hiddenfiles=array('.','.svn','resources','index.php','index.html','.htaccess','.htpasswd','web.config');
	
	public function backupDB($path,$file){
		$exporter = Exporter::getInstance(false);
		$tables = $exporter->getTablesList();
		$fpsys = fopen($path.$file,"wt");
		//$delimiter=$this->_db->getDelimiter();
		$delimiter=";\n";
		$setnames = "SET NAMES 'UTF8'".$delimiter."\n";
		fputs($fpsys, $setnames, mb_strlen($setnames,DEF_CP));
		foreach ($tables as $t) {
			$expMode = Request::getInt($t.'_mode',0);
			if ($expMode) {
				$createTable = "DROP TABLE IF EXISTS `".$t."`".$delimiter."\n";
				$createTable .= $exporter->getCreateTable($t, $delimiter)."\n";
				fputs($fpsys, $createTable);
				$inserts = $exporter->getInserts($t, $delimiter)."\n";
				fputs($fpsys, $inserts);
			}
		}
		fclose($fpsys);
	}
	public function downloadFile($folder,$file,$href){
		if (in_array($file, $this->_hiddenfiles) || !$folder) Util::redirect($href, Text::_("File not exists")." : ".$file);
		$fullname = $folder.DS.$file;
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
}
?>
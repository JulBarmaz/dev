<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Mime {
	private static $mimes = array(
			'cdr' => array('image/x-coreldraw', 'application/octet-stream'),
			'hqx' => 'application/mac-binhex40',
			'cpt' => 'application/mac-compactpro',
			'csv' => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
			'bin' => 'application/macbinary',
			'dms' => 'application/octet-stream',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'exe' => array('application/octet-stream', 'application/x-msdownload'),
			'class' => 'application/octet-stream',
			'psd' => 'application/x-photoshop',
			'so' => 'application/octet-stream',
			'sea' => 'application/octet-stream',
			'dll' => 'application/octet-stream',
			'oda' => 'application/oda',
			'pdf' => array('application/pdf', 'application/x-download'),
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'mif' => 'application/vnd.mif',
			'xls' => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel', 'application/vnd.ms-office'),
			'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
			'wbxml' => 'application/wbxml',
			'wmlc' => 'application/wmlc',
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'dxr' => 'application/x-director',
			'dvi' => 'application/x-dvi',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'php' => 'application/x-httpd-php',
			'php4' => 'application/x-httpd-php',
			'php3' => 'application/x-httpd-php',
			'phtml' => 'application/x-httpd-php',
			'phps' => 'application/x-httpd-php-source',
			'js' => 'application/x-javascript',
			'swf' => 'application/x-shockwave-flash',
			'sit' => 'application/x-stuffit',
			'tar' => 'application/x-tar',
			'tgz' => array('application/x-tar', 'application/x-gzip-compressed'),
			'xhtml' => 'application/xhtml+xml',
			'xht' => 'application/xhtml+xml',
			'zip' =>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mpga' => 'audio/mpeg',
			'mp2' => 'audio/mpeg',
			'mp3' => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
			'aif' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'ra' => 'audio/x-realaudio',
			'rv' => 'video/vnd.rn-realvideo',
			'wav' => array('audio/x-wav', 'audio/wave', 'audio/wav'),
			'bmp' => array('image/bmp', 'image/x-windows-bmp'),
			'gif' => 'image/gif',
			'jpeg' => array('image/jpeg', 'image/pjpeg'),
			'jpg' => array('image/jpeg', 'image/pjpeg'),
			'jpe' => array('image/jpeg', 'image/pjpeg'),
			'png' => array('image/png',  'image/x-png'),
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'css' => 'text/css',
			'html' => 'text/html',
			'htm' => 'text/html',
			'shtml' => 'text/html',
			'txt' => 'text/plain',
			'text' => 'text/plain',
			'log' => array('text/plain', 'text/x-log'),
			'rtx' => 'text/richtext',
			'rtf' => 'text/rtf',
			'xml' => 'text/xml',
			'xsl' => 'text/xml',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'mp4' => 'video/mp4',
			'ogg' => 'video/ogg',
			'ogv' => 'video/ogg',
			'webm' => 'video/webm',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'avi' => 'video/x-msvideo',
			'movie' => 'video/x-sgi-movie',
			'doc' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'word' => array('application/msword', 'application/octet-stream'),
			'xl' => 'application/excel',
			'eml' => 'message/rfc822',
			'json' => array('application/json', 'text/json')
	);

	public static function getTypeByKey($key) {
		if(isset(self::$mimes[$key])) {
			if(is_array(self::$mimes[$key])) return self::$mimes[$key];
			else return array(self::$mimes[$key]);
		} else	return array();
	}
	public static function getTypes($keys) {
		$result=array();
		if (count($keys)){
			foreach($keys as $key){
				$result=array_merge($result, self::getTypeByKey($key));
			}
		}
		return $result;
	}
	public static function getMimeKeys() {
		ksort(self::$mimes);
		$arr = array_keys(self::$mimes);
		return array_combine($arr, $arr);
	}
}

<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

final class Translit{
	private static $in = false;
	private static $out = false;
	private static $in_default = array(
									'&', '?',  '!', '.', ',', ':',  ';',  '*', '(', ')',
									'{', '}',  '%', '#', '№', '@',  '$',  '^', '_', '+',
									'/', '\\', '=', '|', '"', '\'', '\"', ' ', '`'
									);
	private static $out_default = array(
										'-', '-',  '-', '-', '-', '-',  '-',  '-',  '-',  '-',
										'-', '-',  '-', '-', '-', '-',  '-',  '-',  '_',  '-',
										'-', '-',  '-', '-', '-', '-',  '-',  '-',  '-',
										);
	/*
	// "translit_input_array" and "translit_output_array" vars must be set in main.ini lang file
	// Example how to get strings via vars. Returns array with keys "translit_input_array" and "translit_output_array"
	// $arr = Translit::makeString($in_array, $out_array);
	// Russian source vars example
	 $in_array=array(
					'а', 'б',  'в', 'г', 'д', 'е',  'ё',  'з', 'и', 'й',
					'к', 'л',  'м', 'н', 'о', 'п',  'р',  'с', 'т', 'у',
					'ф', 'х',  'ъ', 'ы', 'э', 'ж',  'ц',  'ч', 'ш', 'щ',
					'ь', 'ю',  'я'
					);
	$out_array=array(
					'a', 'b',  'v', 'g', 'd', 'e',  'e',  'z',  'i',  'y',
					'k', 'l',  'm', 'n', 'o', 'p',  'r',  's',  't',  'u',
					'f', 'h',  'j', 'i', 'e', 'zh', 'ts', 'ch', 'sh', 'shch',
					'',  'yu', 'ya'
					);
	*/
	public static function makeString($arr_in, $arr_out){
		if(count($arr_in)){
			foreach ($arr_in as $key_in=>$str_in){
				$arr_in[$key_in] = urlencode($str_in);
			}
		}
		if(count($arr_out)){
			foreach ($arr_out as $key_out=>$str_out){
				$arr_out[$key_out] = urlencode($str_out);
			}
		}
		$result["translit_input_array"]=implode(",", $arr_in);
		$result["translit_output_array"]=implode(",", $arr_out);
		return $result;
	}
	public static function init(){
		if(!is_array(self::$in) || !is_array(self::$in)) {
			$in_str = Text::_("translit_input_array");
			if($in_str!=="translit_input_array") self::$in=explode(',', $in_str);
			if(self::$in && count(self::$in)){
				foreach (self::$in as $key_in=>$str_in){
					self::$in[$key_in] = urldecode($str_in);
				}
				self::$in = array_merge(self::$in_default, self::$in);
			}
			$out_str = Text::_("translit_output_array");
			if($out_str!=="translit_output_array") self::$out=explode(',', $out_str);
			if(self::$out && count(self::$out)){
				foreach (self::$out as $key_out=>$str_out){
					self::$out[$key_out] = urldecode($str_out);
				}
				self::$out = array_merge(self::$out_default, self::$out);
			}
			if(!is_array(self::$in) || !is_array(self::$in)) {
				self::$in=self::$in_default;
				self::$out=self::$out_default;
			}
		}
	}
	public static function _($string, $coder=DEF_CP, $trim_all=true) {
		self::init();
		$string=htmlentities(html_entity_decode($string, ENT_QUOTES, DEF_CP), ENT_QUOTES, DEF_CP);
		$_html_entities_arr_source=array_keys(array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
		$_html_entities_arr_dest=array_fill(0,count($_html_entities_arr_source),"_");
		$string = mb_strtolower($string, $coder);
		$string = str_replace($_html_entities_arr_source,$_html_entities_arr_dest,$string);
		$string = str_replace(self::$in, self::$out, $string);
		$string = preg_replace("/[^a-z0-9_-]/i", "", $string); // case independent
		if($trim_all) {
			$string = trim($string, '-');
			$string = preg_replace("/_{2,}/", "-", $string);
			$string = preg_replace('/\-+/', '-', $string); // replaces multipe dashes
			$string = trim($string, '-_');
		}
		return $string;
	}
	public static function getDefaultArrays(){
		return array("in_default"=>self::$in_default, "out_default"=>self::$out_default);
	}
}
?>
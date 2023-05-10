<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

/**
 ПЕРЕИМЕНОВАТЬ В metadata_u.php
 В этот файл складывать классы возвращающие ассоциативные массивы, которые
 планируется использовать в основном справочнике системы  в качестве
 статичных списков, которые имеют приоритет над metadata.php
 В качестве возвращаемого значения - всегда ассоциативный массив

 Имя функции не менять,
 Имя файла не менять.
 Начинка по желанию.

 Reserved keys are "sex", "side", "rating", "link_target", "extendable_tables","template_zones"
*/

final class userCKArray	{
	public static function getArray($ck_label="") {
		switch($ck_label){
			// Пример получения значений
			case "extendable_tables":
				$ck_array=customCKArray::getArray($ck_label, true);
				// Здесь можно обработать массив
				return $ck_array;
				break;
			default:
				return false;
				break;
		}
	}
}
?>
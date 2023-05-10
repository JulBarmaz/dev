<?php
/*!
 * BARMAZ-CMS
 * Copyright (c) BARMAZ Group
 * Web: https://BARMAZ.ru
 * Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
 * Revision: 1713 (2019-11-05 13:35:32)
 */
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
			case "objects_type_placement":
				return array(1=>Text::_("Placement"), 2=>Text::_("Trade placement"), 3=>Text::_("Depo placement"), 4=>Text::_("Common placement"),5=>Text::_("Service placement"));
			case "soc_role_snt":
				return array(1=>"член СНТ", 2=>"член правления", 3=>"председатель", 4=>"не член СНТ");
			case "object_type_counter":
				return array(1=>"Электроэнергия",2=>"Газ", 3=>"ГВС", 4=>"ХВС", 5=>"Тепло");
			case "object_kol_tariff":
				return array(1=>"1",2=>"2", 3=>"3");
			//case "extendable_tables":
				//$ck_array=customCKArray::getArray($ck_label, true);
				//array_push($ck_array,"article");
				// Здесь можно обработать массив
				//return $ck_array;
				
			case "extendable_tables":
				return array("feedback"=>"feedback","goods"=>"goods","goods_group"=>"goods_group","profiles"=>"profiles","articles"=>"article");
				break;
				
				// бухгалтерская подсистема
			case "type_spc":
				return array( 'A'=>"Активный",'P'=>"Пассивный",'AP'=>"Активно-Пассивный",'Z'=>"Забаланс");
				break;
			case "ps_an":
				$snt_buh= new snt_buh();
				return $snt_buh->getArrayAnalitic();
				break;
				
				
			default:
				return false;
				break;
		}
	}
}
?>
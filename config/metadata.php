<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

/**
 В этот файл складывать классы возвращающие ассоциативные массивы, которые
 планируется использовать в основном справочнике системы  в качестве
 статичных списков, которые могут быть override в файле пользователя metadata_u.php
 metadata_u.php - не выгружается в базовой поставке.
 его надо создавать самому.
 
 
 Идет проверка по имени файла, если он есть
 то он приоритетен
 и вызывается эта функция.
 Имя функции не менять,  
 Имя файла не менять.
 Начинку не трогать
 
 Reserved keys are "sex", "side", "rating", "link_target", "extendable_tables"
*/

final class customCKArray	{
	public static function getArray($ck_label="", $skip_user_mdconfig=false) {
		if(!$skip_user_mdconfig) {
			$userMDConfig=PATH_CONFIG.'metadata_u.php';
			if(file_exists($userMDConfig)) {
				require_once($userMDConfig);
				if (class_exists("userCKArray",false)){
					$userCKArray=userCKArray::getArray($ck_label);
					if(is_array($userCKArray)) return $userCKArray;
				}
			}
		}
		switch($ck_label){
			case "title_tags":
				return array("div"=>"DIV", "p"=>"P", "h1"=>"H1", "h2"=>"H2", "h3"=>"H3", "h4"=>"H4", "h5"=>"H5", "h6"=>"H6");
			case "bs_elements_in_row":
				return array("12"=>"1", "6"=>"2", "4"=>"3", "3"=>"4", "2"=>"6");
			case "quadro_by_row":
				return array("2"=>"2", "3"=>"3", "4"=>"4", "6"=>"6");
			case "bs_quadro_by_row":
				return array("6"=>"2", "4"=>"3", "3"=>"4", "2"=>"6");
			case "thousand_separator_index":
				return array("0"=>Text::_("None symbol"), "1"=>Text::_("Comma symbol"), "2"=>Text::_("Space symbol"),  "3"=>Text::_("Apostrophe symbol") );
			case "thousand_separator_value":
				return array("0"=>"", 	"1"=>",", "2"=>" ",  "3"=>"'" );
			case "admin_goods_pans_titles":
				return array(1=>"Main data", 2=>"Images", 3=>"Description", 4=>"Additional");
			case "admin_goodsgroup_pans_titles":
				return array(1=>"Main data", 2=>"Images", 3=>"Description", 4=>"Additional");
			case "goods_list_tmpl":
				return array('defl'=>'default','special'=>'special');
			case "fcats_list_tmpl":
				return array('defl'=>'default');
			case "goods_flypage_tmpl":
				return array('info'=>'info');
			case "goods_default_sorting":
				$_asc_text = " (".mb_strtolower(Text::_("Ascending")).")";
				$_desc_text = " (".mb_strtolower(Text::_("Descending")).")";
				return array('g_name.ASC'=>Text::_('Name').$_asc_text, 'g_name.DESC'=>Text::_('Name').$_desc_text, 'g_fullname.ASC'=>Text::_('Full name').$_asc_text, 'g_fullname.DESC'=>Text::_('Full name').$_desc_text, 'g_change_date.ASC'=>Text::_('Change date').$_asc_text, 'g_change_date.DESC'=>Text::_('Change date').$_desc_text, 'ordering.ASC'=>Text::_('Ordering field').$_asc_text, 'ordering.DESC'=>Text::_('Ordering field').$_desc_text);
			case "goods_order_tmpl":
				return array('new'=>'new');
			case "bp_sort":
				return array('p_ordering'=>Text::_('Ordering'),'p_theme'=>Text::_('Theme'), 'p_date'=>Text::_('Post date'), 'p_comments'=>Text::_('Comments quantity'), 'p_rating'=>Text::_('Rating'));
			case "a_childs_sort":
				return array('a_ordering'=>Text::_('Ordering'), 'a_date'=>Text::_('Date'), 'a_title'=>Text::_('Title'), 'a_rating'=>Text::_('Rating'));
			case "ym_markers":
				return array("workshop.png"=>"Мастерская", "keyMaster.png"=>"Ключ", "camping.png"=>"Вигвам", "theater.png"=>"Театр", "buildings.png"=>"Здание",
							"house.png"=>"Дом", "mushroom.png"=>"Гриб", "truck.png"=>"Грузовик", "storehouse.png"=>"Хранилище", "factory.png"=>"Фабрика",
							"attention.png"=>"Внимание");
				break;
			case "extendable_tables":
				return array("feedback"=>"feedback","goods"=>"goods","goods_group"=>"goods_group","profiles"=>"profiles");
				break;
			case "catalog_search_places":
				return array('0'=>Text::_("Disabled"), '1'=>Text::_('SKU').", ".Text::_('Title'), '2'=>Text::_('SKU'),'3'=>Text::_('Title'));
			default:
				return false;
				break;
		}
	}
}
?>
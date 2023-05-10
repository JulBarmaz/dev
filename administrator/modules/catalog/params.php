<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModuleParams {
	public static function _proceed(&$module){
		$module->addParam("tree_skip_deleted", "boolean", 0);
		$module->addParam("reverse_analog_link", "boolean", 1);
		$module->addParam("default_goods_currency", "select", DEFAULT_CURRENCY, false, Currency::getList());
		$module->addParam("generate_sku_automaticly", "boolean", 0);
		$module->addParam("automatic_sku_prefix", "string", "G-");
		$module->addParam("frontend_title", "title", Text::_("Frontend"));
		$module->addParam("quadro_by_row", "select", "3", false, SpravStatic::getCKArray("bs_quadro_by_row"));
		$module->addParam("video_width", "integer", 320);
		$module->addParam("video_height", "integer", 190);
		$module->addParam("breadcrumb_lenght", "integer", 0);
		$module->addParam("breadcrumb_start", "string", "catalog");
		$module->addParam("breadcrumb_start_link", "string", "index.php?module=catalog");
		$module->addParam("Default_ext_filter", "boolean", 0);
		$module->addParam("show_filter_button", "boolean", 1);
		$module->addParam("show_sort_links", "boolean", 1);
		$module->addParam("default_goods_sorting", "select", "", false, Params::transformParamsSource(SpravStatic::getCKArray("goods_default_sorting")));
		$module->addParam("show_goods_from_subgroups", "boolean", 0);
		$module->addParam("show_kits_on_info_page", "boolean", 0);
		$module->addParam("reset_filter_on_category_changed", "boolean", 1);
		$module->addParam("filter_vendors_as_list", "boolean", 1);
		$module->addParam("filter_manufacturers_as_list", "boolean", 1);
		$module->addParam("enable_favourites_goods", "boolean", 1);
		$module->addParam("enable_compare_goods", "boolean", 1);
		$module->addParam("orders_tab", "tab", Text::_("Orders"));
		$module->addParam("sms2admin", "boolean", 0);
		$module->addParam("sms2user", "boolean", 0);
		$module->addParam("sms2vendor", "boolean", 0);
		$module->addParam("require_person", "boolean", 1);
		$module->addParam("require_email", "boolean", 1);
		$module->addParam("require_phone", "boolean", 1);
		$module->addParam("search_tab", "tab", Text::_("Search"));
		$module->addParam("search_mode", "select", "0", false, Params::transformParamsSource(SpravStatic::getCKArray("catalog_search_places")));
		$module->addParam("minimum_search_length", "integer", 3, true);
		$module->addParam("live_search_title", "title", Text::_("Live search"));
		$module->addParam("live_search_show_categories", "integer", 5);
		$module->addParam("live_search_show_goods", "integer", 5);
		$module->addParam("live_search_show_more_goods", "boolean", 1);
		if(defined("_ADMIN_MODE")){
			$module->addParam("1c_exchange_settings_tab", "tab", Text::_("1C exchange settings"));
			$module->addParam("1c_version_in", "ro_string", "2.04-2.07", true);
			$module->addParam("1c_version_out", "ro_string", "2.04", true);
			$module->addParam("1c_goods_export_system", "select", "ut103", true, Params::transformParamsSource(array("ut103"=>Text::_("1C UT103"), "ut11"=>Text::_("1C UT11"), "unf16"=>Text::_("1C UNF16"), "ip"=>Text::_("Infop"))));
			$module->addParam("1c_messages_cp1251", "boolean", 0);
			$module->addParam("1c_log_level", "select", "0", true, Params::transformParamsSource(array("0"=>Text::_("Disabled"),"1"=>Text::_("Errors"), "2"=>Text::_("Errors and messages"), "3"=>Text::_("Debug info"))));
			$module->addParam("1c_log_always_clean", "boolean", 1);
			$module->addParam("1c_zip", "boolean", 0);
			$module->addParam("1c_filesize", "integer", 10000);
			$module->addParam("1c_groups_title", "title", Text::_("Goods groups"));
			$module->addParam("1c_groups_load", "boolean", 1);
			$module->addParam("1c_groups_create_new", "boolean", 1);
			$module->addParam("1c_groups_update_found", "boolean", 1);
			$module->addParam("1c_groups_enable", "boolean", 1);
			$module->addParam("1c_groups_restore_deleted", "boolean", 1);
			$module->addParam("1c_groups_update_images", "boolean", 1);
			$module->addParam("1c_goods_title", "title", Text::_("Goods"));
			$module->addParam("1c_goods_create_new", "boolean", 1);
			$module->addParam("1c_goods_duplicate_sku", "select", "1", true, Params::transformParamsSource(array("1"=>Text::_("Show error"), "2"=>Text::_("Nullify"))));
			$module->addParam("1c_goods_update_found", "boolean", 1);
			$module->addParam("1c_goods_enable", "boolean", 1);
			$module->addParam("1c_goods_restore_deleted", "boolean", 1);
			$module->addParam("1c_goods_disable_absent", "boolean", 0);
			$module->addParam("1c_goods_update_groups", "boolean", 0);
			$module->addParam("1c_goods_update_images", "boolean", 1);
			$module->addParam("1c_goods_html_descr", "boolean", 1);
			$module->addParam("1c_goods_weight_names", "string", BaseCML::_("Weight"), true, null, Text::_("Names in the trading system for weight property")."<br />".Text::_("Separate by comma"));
			$module->addParam("1c_goods_width_names", "string", BaseCML::_("testCML_Width"), true, null, Text::_("Names in the trading system for width property")."<br />".Text::_("Separate by comma"));
			$module->addParam("1c_goods_height_names", "string", BaseCML::_("testCML_Height"), true, null, Text::_("Names in the trading system for height property")."<br />".Text::_("Separate by comma"));
			$module->addParam("1c_goods_length_names", "string", BaseCML::_("testCML_Length"), true, null, Text::_("Names in the trading system for length property")."<br />".Text::_("Separate by comma"));
			$module->addParam("1c_goods_offers_mode", "select", "1", true, Params::transformParamsSource(array("1"=>Text::_("Characteristics")." (".Text::_("mode 1").")", "2"=>Text::_("Characteristics")." (".Text::_("mode 2").")", "3"=>Text::_("Goods only")." (".Text::_("without options").")")));
			$module->addParam("1c_goods_offers_mode_description", "ro_string", Text::_("Offers mode description"));
			$module->addParam("1c_prices_title", "title", Text::_("Price types"));
			$module->addParam("1c_price_1", "string", "", false, null, Text::_("Name in the trading system for price type").": ".Text::_("Price 1"));
			$module->addParam("1c_price_2", "string", "", false, null, Text::_("Name in the trading system for price type").": ".Text::_("Price 2"));
			$module->addParam("1c_price_3", "string", "", false, null, Text::_("Name in the trading system for price type").": ".Text::_("Price 3"));
			$module->addParam("1c_price_4", "string", "", false, null, Text::_("Name in the trading system for price type").": ".Text::_("Price 4"));
			$module->addParam("1c_price_5", "string", "", false, null, Text::_("Name in the trading system for price type").": ".Text::_("Price 5"));
			$module->addParam("1c_prices_reset_absent", "boolean", 1);
			// Currency search by short code is very bad idea, but i just left it here commented
			// $module->addParam("1c_currency_search", "select", "1", true, Params::transformParamsSource(array("1"=>Text::_("By code"), "2"=>Text::_("By short name"))));
			$module->addParam("1c_currency_absent", "select", "1", true, Params::transformParamsSource(array("1"=>Text::_("Show error"), "2"=>Text::_("Set default"))));
			$module->addParam("1c_measures_title", "title", Text::_("Measures"));
			$module->addParam("1c_measures_search", "select", "1", true, Params::transformParamsSource(array("1"=>Text::_("By code"), "2"=>Text::_("By short name"), "3"=>Text::_("By full name"))));
			$module->addParam("1c_measures_default_type", "select", "0", true, Params::transformParamsSource(SpravStatic::getCKArray("measure_type")), Text::_("Will be created with this type and koeff equal 1"));
			$module->addParam("1c_orders_title", "title", Text::_("Orders"));
			$module->addParam("1c_orders_reserve", "boolean", 1);
			$module->addParam("1c_order_status_for_export", "table_select", "0", false, "SELECT os_id AS fld_id, os_name AS fld_name FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY fld_id");
			$module->addParam("1c_order_status_after_export", "table_select", "0", false, "SELECT os_id AS fld_id, os_name AS fld_name FROM #__orders_status WHERE os_enabled=1 AND os_deleted=0 ORDER BY fld_id");
			$module->addParam("1c_order_convert_to_cp1251", "boolean", 0);
			$module->addParam("1c_vendors_title", "title", Text::_("Vendors"));
			$module->addParam("1c_vendors_create_new", "boolean", 1);
			$module->addParam("1c_vendors_update_found", "boolean", 1);
			$module->addParam("1c_vendors_enable", "boolean", 1);
			$module->addParam("1c_vendors_restore_deleted", "boolean", 1);
			$module->addParam("1c_manufacturers_title", "title", Text::_("Manufacturers"));
			$module->addParam("1c_manufacturers_create_new", "boolean", 1);
			$module->addParam("1c_manufacturers_names", "string", BaseCML::_("ManufacturerProperty").",".BaseCML::_("Manufacturer"), true, null, Text::_("Names in the trading system for manufacturer property")."<br />".Text::_("Separate by comma"));
			$module->addParam("1c_manufacturers_update_found", "boolean", 1);
			$module->addParam("1c_manufacturers_enable", "boolean", 1);
			$module->addParam("1c_manufacturers_restore_deleted", "boolean", 1);
		}
	}
}
?>
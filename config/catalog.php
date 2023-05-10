<?php
//BARMAZ_COPYRIGHT_TEMPLATE
defined('_BARMAZ_VALID') or die("Access denied");

class catalogConfig  {
	public static	$catalogDisabled		= false;
	public static	$catalogAdminEmail		= "admin@localhost";
	public static	$catalogDisabledMsg	= '';
	public static	$thousandSeparator = 1;
	public static	$price_digits	= 2;
	public static	$quantity_digits	= 4;
	public static	$weight_digits	= 3;
	public static	$size_digits	= 3;
	public static	$volume_digits	= 3;
	public static	$default_order_status	= 1;
	public static	$default_currency = 275;
	public static	$default_measure = 0;
	public static	$default_wmeasure = 0;
	public static	$default_size_measure = 0;
	public static	$default_vol_measure = 0;
	public static	$size4volume_measure = 0;
	public static	$ordersDisabled		= false;
	public static	$ordersWithoutRegistration	= false;
	public static	$default_order_taxes	= 0;
	public static	$ggr_thumb_AutoResize = true;
	public static	$ggr_thumb_width=300;
	public static	$ggr_thumb_height=300;
	public static	$catalog_rules_article	= 2;
	public static	$thumbAutoCreate=true;
	public static	$thumbAutoResize=true;
	public static	$thumb_width=300;
	public static	$thumb_height=300;
	public static	$mediumImgAutoCreate=true;
	public static	$mediumImgAutoResize=true;
	public static	$basket_fullview=true;
	public static	$mediumImgWidth=450;
	public static	$mediumImgHeight=450;
	public static	$show_base_price=false;
	public static	$show_pack_price=false;
	public static	$complectPriceAsGoodsSum=false;
	public static	$hide_prices=false;
	public static	$show_stock=false;
	public static	$check_stock=false;
	public static	$listImageLink=0;
	public static	$multy_vendor		= true;
	public static	$default_vendor = 0;
	public static	$default_manufacturer = 0;
	public static	$catalogTitle="";
	public static	$catalogDescription="";
	public static	$catalogKeywords="";	
}
?>
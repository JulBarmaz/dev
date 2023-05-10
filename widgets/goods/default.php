<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class goodsWidget extends Widget {
	protected $_requiredModules = array("catalog");
	protected $skip_fields=array("g_id", "g_name","g_sku", "g_thumb", "g_alias", "g_type","g_fullname");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("ShowDopTitle", "boolean", 0);
		$this->addParam("DopTitle", "string", "");
		$this->addParam("LinkDopTitle", "string", "");
		$this->addParam("goodsGroupid", "integer", 0);
		$this->addParam("goodsQuantity", "integer", 4);
		$this->addParam("goods_in_row", "select", "3", false, SpravStatic::getCKArray("bs_elements_in_row"));
		$this->addParam("showSKU", "boolean", 0);
		$this->addParam("showCustomProperties",	"boolean", 0);
		$this->addParam("onlyNew", "boolean", 0);
		$this->addParam("onlyHit", "boolean", 0);
		$this->addParam("orderBy", "select", "g_name.ASC", false, SpravStatic::getCKArray("goods_default_sorting"));
		$this->addParam("showCartButton", "boolean", 0);
		$this->addParam("showCatalogButton", "boolean", 0);
		$this->addParam("showFavouritesButton", "boolean", 0);
		$this->addParam("showCompareButton", "boolean", 0);
		$this->addParam("catalogButtonText", "string", Text::_("Go to catalog"));
	}
	public function prepare() {
		Portal::getInstance()->addScript("modules/catalog.js");
		/*
		$widgetID=$this->getParam("Widget_ID");
		if($widgetID){
			$script="
					$(window).on('load',function() {
						equalizeDivHeight('#".$widgetID." .g_thumb a');
						equalizeDivHeight('#".$widgetID." .g_title');
						equalizeDivHeight('#".$widgetID." .g_custom_properties');
						equalizeDivHeight('#".$widgetID." .g_quantity');
						equalizeDivHeight('#".$widgetID." .g_price');
					});
					";
			Portal::getInstance()->addScriptDeclaration($script);
		}
		*/
	}
	public function render() {
		$grpsid = $this->getParam('goodsGroupid');
		$new =  $this->getParam('onlyNew');
		$hit =  $this->getParam('onlyHit');
		$showSKU =  $this->getParam('showSKU');
		$showCustomProperties = $this->getParam('showCustomProperties');
		$showCatalogButton = $this->getParam('showCatalogButton');
		$catalogButtonText = $this->getParam('catalogButtonText');
		$quantity = $this->getParam('goodsQuantity');
		$Doptitle = $this->getParam('DopTitle');
		$LinkDoptitle = $this->getParam('LinkDopTitle');
		$show_Doptitle = $this->getParam('ShowDopTitle');
		$goods_in_row = $this->getParam('goods_in_row');
		$show_cart_button = $this->getParam('showCartButton');
		$enable_favourites = Module::getInstance("catalog")->getParam("enable_favourites_goods") && $this->getParam('showFavouritesButton');
		$enable_compare = Module::getInstance("catalog")->getParam("enable_compare_goods") && $this->getParam('showCompareButton');
		if($enable_favourites) $favourites = Module::getHelper("favourites", "catalog")->getFavourites(); else $favourites = array();
		if($enable_compare) $compare = Module::getHelper("compare", "catalog")->getCompare(); else $compare = array();
		
		switch($goods_in_row){
			case 12: // 1 quadro
				$r_class="col-xss-12 col-xs-12 col-sm-12 col-md-".$goods_in_row;
				break;
			case 6: // 2 quadro
				$r_class="col-xss-12 col-xs-6 col-sm-6 col-md-".$goods_in_row;
				break;
			case 4: // 3 quadro
				$r_class="col-xss-12 col-xs-6 col-sm-4 col-md-".$goods_in_row;
				break;
			case 3: // 4 quadro
			default:
				$r_class="col-xss-12 col-xs-6 col-sm-3 col-md-".$goods_in_row;
				break;
			case 2: // 6 quadro
				$r_class="col-xss-12 col-xs-6 col-sm-3 col-md-".$goods_in_row;
				break;
		}
		
		$sorting =  $this->getParam('orderBy');
		$order_by="g_name"; $order_dir="ASC";
		if($sorting) {
			$_default_sorting = explode(".", $sorting);
			if(count($_default_sorting)==2){
				$order_by=$_default_sorting[0]; $order_dir=$_default_sorting[1];
			}
		}
		$widgetHTML="";
		$helper=Module::getHelper("goods","catalog");
		$show_goods_from_subgroups=intval(Module::getInstance("catalog")->getParam('show_goods_from_subgroups'));
		if($show_goods_from_subgroups) $goods = $helper->getGoodsWithSubCats($grpsid, true, $quantity, $new, $hit, $order_by, $order_dir);
		else $goods = $helper->getGoods($grpsid, $quantity, $new, $hit, $order_by, $order_dir);
		if (count($goods)) {
			if($show_Doptitle&&$Doptitle)	{
				$widgetHTML.="<div class=\"w_goods_toplabel toplabel row\"><div class=\"col-xs-12\">";
				if ($LinkDoptitle) $widgetHTML.="<a class=\"linkButton\" href=\"".Router::_($LinkDoptitle)."\">".$Doptitle."</a>";
				else $widgetHTML.=$Doptitle;
				$widgetHTML.="</div></div>";
			}
			$widgetHTML .= "<div class=\"w_goods row\">";
			$tp=User::getInstance()->u_pricetype;
			$field_price="g_price_".$tp;
			$gids=array();
			foreach($goods as $gds)	$gids[$gds->g_id]=$gds->g_id;
			$options_gids=Module::getHelper("goods","catalog")->haveOptions($gids);
			$discounts=Module::getHelper("goods","catalog")->getDiscounts($gids);
			$model = Module::getInstance("catalog")->getModel('goods');
			$model->meta=new SpravMetadata("catalog", "goods", 'default', true, true, 0);
			$g_name_index = $model->meta->getFieldIndex("g_name");
			$g_fullname_index = $model->meta->getFieldIndex("g_fullname");
			foreach($goods as $gds){
				$widgetHTML1="";
				if($showCustomProperties) {
					$gds = $model->getElementData($gds->g_id);
					foreach($model->meta->field as $key=>$val){
						$field = $model->meta->field[$key];
						$str_table = "";
						$field_tbl= $model->meta->field[$key];
						$code=$gds->{$field};
						if($model->meta->view[$key] && !in_array($model->meta->field[$key], $this->skip_fields) && $gds->{$model->meta->field[$key]}!=""){
							if($model->meta->ck_reestr[$key]) {
								if (is_array($model->meta->ck_reestr[$key])) $key_arr=$model->meta->ck_reestr[$key];
								else $key_arr=SpravStatic::getCKArray($model->meta->ck_reestr[$key]);
								if(is_array($key_arr) && $model->meta->input_type[$key] == "multiselect"){
									$code = explode(";", trim($code, ";"));
									$ms_code = array();
									if(is_array($code)){
										foreach($code as $ms_key=>$ms_val){
											if(array_key_exists($ms_val, $key_arr)){
												$ms_code[]=$key_arr[$ms_val];
											}
										}
										$gds->{$field} = implode(", ", $ms_code);
									} else $gds->{$field}="";
								} elseif(is_array($key_arr) && array_key_exists($code, $key_arr)) {
									$gds->{$field}=$key_arr[$code];
								} else {
									$gds->{$field}="";
								}
							} elseif ($model->meta->ch_table[$key]) {
								if ($model->meta->ch_table[$key]) {
									$field_tbl=$model->meta->field[$key]."_sql_replace";
								} else {
									$field_tbl=$model->meta->field[$key];
								}
								$str_table=$gds->$field_tbl;
							}
							if ($model->meta->val_type[$key]=='date') {
								$str_table=Date::fromSQL($row->{$field_tbl}, true);
							} elseif ($model->meta->val_type[$key]=='datetime') 	{
								if($gds->{$field_tbl} != '0000-00-00 00:00:00') $str_table=date("d.m.y H:i",Date::GetTimestamp($gds->{$field_tbl}, 1)); else $str_table = '';
							} elseif ($model->meta->val_type[$key]=='timestamp') {
								$str_table=Date::fromSQL($gds->{$field_tbl}, false, false);
							} elseif ($model->meta->val_type[$key]=='text') {
								$str_table=html_entity_decode($gds->{$field_tbl}, ENT_COMPAT,DEF_CP);
							} elseif ($model->meta->val_type[$key]=='string') {
								$str_table=$gds->{$field_tbl};
							} elseif ($model->meta->val_type[$key]=='constanta') {
									$str_table=Text::_($gds->{$field_tbl});
							} elseif ($model->meta->val_type[$key]=='currency'){
								if($model->meta->keycurrency) $currency_val=$row->{$model->meta->keycurrency}; else $currency_val="";
								$str_table=number_format(Currency::getInstance()->convert($row->{$field_tbl}, $currency_val), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
							}
							if($str_table){
								$widgetHTML1.="<div class=\"char_element row char_element-".$field."\">";
								$widgetHTML1.="	<div class=\"char_label col-xss-12 col-xs-5\">".HTMLControls::renderLabelField(false, Text::_($model->meta->field_title[$key]))."</div>";
								$widgetHTML1.="	<div class=\"char_value col-xss-12 col-xs-7\" id=\"".$field."_val\">".$str_table."</div>";
								$widgetHTML1.="</div>";
							}
						}
					}
				}
				$sell_measure=$gds->g_measure;
				switch($gds->g_selltype){
					case 1:
						$sell_measure=$gds->g_pack_measure;
						break;
					case 2:
						$sell_measure=$gds->g_wmeasure;
						break;
					case 3:
						$sell_measure=$gds->g_vmeasure;
						break;
					case 4:
					case 5:
					case 0:
					default:
						break;
				}
				
				$widgetHTML .= "<div class=\"w_goods_element ".$r_class."\">";
				$widgetHTML .= "<div class=\"quadro-wrapper float-fix\">";
				if ($gds->g_hit) $widgetHTML .= "<div class=\"ishit\"></div>";
				if ($gds->g_new) $widgetHTML .= "<div class=\"isnew\"></div>";
				$thumb=$helper->getImage($gds->g_thumb,1);
				if (!$thumb) $thumb = $helper->getEmptyImage();
				$thumb=HTMLControls::renderImage($thumb,false,catalogConfig::$thumb_width,0,"",$gds->g_id.")".$gds->g_name);
				$widgetHTML.= "<div class=\"g_thumb\"><a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$gds->g_id."&alias=".$gds->g_alias)."\">".$thumb."</a></div>";
				if($model->meta->view[$g_name_index]) $gds_title = $gds->g_name;
				elseif($model->meta->view[$g_fullname_index]) $gds_title = $gds->g_fullname;
				else $gds_title = "";
				$widgetHTML.="<div class=\"g_title\">";
				if($gds_title) $widgetHTML.="<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$gds->g_id."&alias=".$gds->g_alias)."\">".$gds_title."</a>";
				$widgetHTML.="</div>";
				if($showCustomProperties) $widgetHTML.="<div class=\"g_custom_properties\">".$widgetHTML1."</div>";
				if($showSKU) $widgetHTML.="<div class=\"g_sku\">".Text::_("sku").": ".$gds->g_sku."</div>";
				if(!catalogConfig::$hide_prices && $gds->{$field_price}>0) {
					$_old_val = Currency::getInstance()->convert($gds->{$field_price}, $gds->g_currency);
					$_val = Module::getHelper("goods","catalog")->applyDiscounts($gds->g_id, $_old_val, $discounts);
					$widgetHTML.="<div class=\"g_price\">";
					if($_old_val!=$_val) $widgetHTML.= "	<span class=\"old_price\">".number_format($_old_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
					$widgetHTML.=Text::_("Price").": ".number_format($_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY);
					$widgetHTML.="</div>";

					$_widgetButtonsHTML = "";
					if($enable_favourites && array_key_exists($gds->g_id, $favourites)) $favourites_class = "inFavourites"; else $favourites_class = "notInFavourites";
					if($enable_compare && array_key_exists($gds->g_id, $compare)) $compare_class = "inCompare"; else $compare_class = "notInCompare";
					if($enable_favourites){
						$_widgetButtonsHTML.= "<div class=\"favouritesButtons ".$favourites_class."\">";
						$_widgetButtonsHTML.= "<a title=\"".Text::_("Add to favourites")."\" onclick=\"addToFavourites(this, '".$gds->g_id."');\" class=\"addToFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart-empty\"></i></a>";
						$_widgetButtonsHTML.= "<a title=\"".Text::_("Remove from favourites")."\" onclick=\"removeFromFavourites(this, '".$gds->g_id."');\" class=\"removeFromFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart\"></i></a>";
						$_widgetButtonsHTML.= "</div>";
					}
					if($enable_compare){
						$_widgetButtonsHTML.= "<div class=\"compareButtons ".$compare_class."\" >";
						$_widgetButtonsHTML.= "<a title=\"".Text::_("Add to compare")."\" onclick=\"addToCompare(this, '".$gds->g_id."');\" class=\"addToCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>";
						$_widgetButtonsHTML.= "<a title=\"".Text::_("Remove from compare")."\" onclick=\"removeFromCompare(this, '".$gds->g_id."');\" class=\"removeFromCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>";
						$_widgetButtonsHTML.= "</div>";
					}
					if($show_cart_button){
						if (!catalogConfig::$ordersDisabled){
							$widgetHTML.="<div class=\"g_quantity\">";
							if(intval($gds->g_vendor)){
								$widgetHTML.="	<div class=\"row\">";
								if(!array_key_exists($gds->g_id, $options_gids)){
									$widgetHTML.="		<div class=\"col-xs-6\">".HTMLControls::renderLabelField("quant_".$gds->g_id, Text::_("quantity")." (".Measure::getInstance()->getShortName($sell_measure).")")."</div>";
									$widgetHTML.="		<div class=\"col-xs-6\">".HTMLControls::renderInputText("quant_".$gds->g_id,"1","5")."</div>";
								}
								$widgetHTML.="	</div>";
							}
							$widgetHTML.="</div>";
						}
						$widgetHTML.="<div class=\"g_basket\">";
						if(catalogConfig::$ordersDisabled || !intval($gds->g_vendor) || array_key_exists($gds->g_id, $options_gids)){
							$widgetHTML.="<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$gds->g_id)."\" class=\"linkButton btn btn-info button-add2basket button-add2basket-readmore\" >".Text::_('Read more')."</a>";
						} else {
							$widgetHTML.="<a onclick=\"javascript:addToBasket".($gds->g_is_single ? "Single" : "")."('".$gds->g_id."');\" class=\"linkButton btn btn-info button-add2basket button-add2basket-act\" >".Text::_('Add to basket')."</a>";
						}
						$widgetHTML.= $_widgetButtonsHTML;
						$widgetHTML.="</div>";
					} else {
						$widgetHTML.="<div class=\"g_basket\">";
						if($_widgetButtonsHTML) $widgetHTML.= $_widgetButtonsHTML;
						$widgetHTML.="</div>";
					}
				}
				$widgetHTML.="</div>";
				$widgetHTML.="</div>";
			}
			if($showCatalogButton){
				$widgetHTML.="<div class=\"buttons col-xs-12\">";
				if($grpsid) {
					$alias=Module::getHelper("goods","catalog")->getAliasByGroupId($grpsid);
					$catalog_link = Router::_("index.php?module=catalog&view=goods&psid=".$grpsid.($alias ? "&alias=".$alias : ""));
				} else $catalog_link = Router::_("index.php?module=catalog");
				$widgetHTML.="<a href=\"".$catalog_link."\" class=\"linkButton btn btn-info\" >".$catalogButtonText."</a>";
				$widgetHTML.="</div>";
			}
			$widgetHTML.="</div>";
		}	else  $widgetHTML = "";
		return $widgetHTML;
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$spr_tmpl_overrided=1;
switch($this->quadro_by_row){
	case 6: // 2 quadro
		$r_class="col-xss-12 col-xs-6 col-sm-6 col-md-".$this->quadro_by_row;
		break;
	case 4: // 3 quadro
		$r_class="col-xss-12 col-xs-6 col-sm-4 col-md-".$this->quadro_by_row;
		break;
	case 3: // 4 quadro
	default:
		$r_class="col-xss-12 col-xs-6 col-sm-3 col-md-".$this->quadro_by_row;
		break;
	case 2: // 6 quadro
		$r_class="col-xss-12 col-xs-6 col-sm-3 col-md-".$this->quadro_by_row;
		break;
}
$list_mode = Request::getSafe("BARMAZ_goods_mode","","cookie");
if($list_mode && $list_mode == "list") {
	$list_mode_class=" goods-list-list";
} else {
	$list_mode_class = "";
}
?>
<h1 class="title no_border"><?php echo Text::_("Favourites list"); ?></h1>
<div class="catalog_list">
<?php
echo "<div class=\"goods-list-mode-switcher\" style=\"display:none;\">";
echo "	<a rel=\"nofollow\" onclick=\"toggleGoodsListMode(this);\" class=\"switch-layout switch-list".($list_mode=="list" ? " active" :"")."\" title=\"".Text::_("List")."\"></a>";
echo "	<a rel=\"nofollow\" onclick=\"toggleGoodsListMode(this);\" class=\"switch-layout switch-grid".($list_mode=="list" ? "" :" active")."\" title=\"".Text::_("Tile")."\"></a>";
echo "</div>";
$ggr_link_tail = "";
$tp=User::getInstance()->u_pricetype;
$field_price="g_price_".$tp;
$arr_setfields=array($field_price,"g_wmeasure",'g_currency',"g_thumb","g_medium_image","g_image","g_name","g_fullname","g_comments","g_new","g_hit","g_file_demo","g_file");
$gids=array();
if(is_array($_table_body_arr)&&count($_table_body_arr)) {
	echo "<div class=\"goods-list row".$list_mode_class."\">";
	foreach($_table_body_arr as $row) {	?>
		<div id="favourites_<?php echo $row['g_id']['value']; ?>" class="goods-element<?php echo ($r_class ? " ".$r_class : ""); ?>">		
			<div class="quadro-wrapper">
				<?php 
				if ($row['g_hit']['value']) echo "<div class=\"ishit\"></div>";
				if ($row['g_new']['value'])  echo "<div class=\"isnew\"></div>";
				?>
				<div class="g_thumb"><?php
					$thumb=$this->getImage($row['g_thumb']['value'],1);
					if (!$thumb) $thumb = $this->getEmptyImage();
					$fullimg=$this->getImage($row['g_image']['value']);
					if($row['g_thumb']['hidden']!=1){
						$thumb=HTMLControls::renderImage($thumb, false, catalogConfig::$thumb_width, 0, "", $row['g_id']['value'].")".$row['g_name']['value']);
					}
					switch(catalogConfig::$listImageLink) {
						case "1":
							if($fullimg) echo "<a class=\"relpopup g_thumb_link\" href=\"".$fullimg."\">".$thumb."</a>";
							else echo "<div class=\"g_thumb_link\">".$thumb."</div>";
							break;
						case "2":
							echo "<a class=\"g_thumb_link\" href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$row['g_id']['value']."&amp;alias=".$row['g_alias']['value'].$ggr_link_tail)."\">".$thumb."</a>";
							break;
						case "0":
						default:
					  	echo $thumb;
					  break;
					}
				?>
				</div> <!-- g_thumb -->
				
				<?php if(!$row['g_name']['hidden']) {?>
					<div class="g_title"><?php echo "<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$row['g_id']['value']."&amp;alias=".$row['g_alias']['value'].$ggr_link_tail)."\">".$row['g_name']['value']."</a>"; ?></div>
				<?php } elseif(!$row['g_fullname']['hidden']) { ?>
					<div class="g_title"><?php echo "<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$row['g_id']['value']."&amp;alias=".$row['g_alias']['value'].$ggr_link_tail)."\">".$row['g_fullname']['value']."</a>"; ?></div>
				<?php } ?>
				
				<?php
					if($row['g_comments']['hidden']!=1 && $row['g_comments']['html']){
					$text=$row['g_comments']['html'];
					echo "<div class=\"g_comment\">";
					$first_hr=mb_strpos($text,'<hr id="system-readmore"',0);
					if ($first_hr) echo mb_substr($text,0,min($first_hr,300));
					else echo $text;
					echo "</div>";
					}
				?>
				<div class="g_custom_properties">
				<?php
				foreach($_table_header_arr as $key=>$val)	{
					if($key=="checkbox") continue;
					if(!in_array($key, $arr_setfields) && $row[$key]['hidden']!=1 && $row[$key]["html"]) {
						echo "<div class=\"char_element row char_element-".$key."\">";
						echo "	<div class=\"char_label col-xs-5\">".HTMLControls::renderLabelField(false, $_table_header_arr[$key]["html"])."</div>";
						echo "	<div class=\"char_value col-xs-7\" id=\"".$key."_val\">".$row[$key]["html"]."</div>";
						echo "</div>";
					}
				}
				?>
				</div>
				<div class="g_basket_wrapper">
				<?php
				$sell_measure=$row['g_measure']['value'];
				switch($row['g_selltype']['value']){
					case 1:
						$sell_measure=$row['g_pack_measure']['value'];
						break;
					case 2:
						$sell_measure=$row['g_wmeasure']['value'];
						break;
					case 3:
						$sell_measure=$row['g_vmeasure']['value'];
						break;
					case 4:
					case 5:
					case 0:
					default:
						break;
				}
				if(!catalogConfig::$hide_prices && $row[$field_price]['hidden']!=1) {
					if(floatval($row[$field_price]['value'])>0) {
						$_old_val = Currency::getInstance()->convert($row[$field_price]['value'], $currency_val);
					} else {
						$_old_val = 0;
					}
					$_val = $this->applyDiscounts($row['g_id']['value'], $_old_val, $this->discounts);
					if($_val < 0) $_val = 0;
					echo "<div class=\"g_price\">";
					if($_old_val || $_val){
						if($_old_val!=$_val) echo "	<span class=\"old_price\">".number_format($_old_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						if(array_key_exists($row['g_id']['value'], $this->options_gids)){
							echo "	<span class=\"measure_val\">".Text::_("Price from")."</span> <span class=\"price_val\">".number_format($_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span> <span class=\"measure_val\">".Text::_("per")." ".Measure::getInstance()->getShortName($sell_measure)."</span></span>";
						} else {
							echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($sell_measure)."</span> <span class=\"price_val\">".$_val." <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						}
					}
					echo "</div>";
				}
				/*************************************************************************************/
				if (!catalogConfig::$ordersDisabled){
					echo "<div class=\"g_quantity\">";
					if(intval($row['g_vendor']['value'])){
						echo "<div class=\"row\">";
						if(!array_key_exists($row['g_id']['value'], $this->options_gids)){
							echo "<div class=\"col-xs-6\">".HTMLControls::renderLabelField("quant_".$row['g_id']['value'],Text::_("quantity")."&nbsp;(".Measure::getInstance()->getShortName($sell_measure).")")."</div>";
							echo "<div class=\"col-xs-6\">".HTMLControls::renderInputText("quant_".$row['g_id']['value'],"1","5","","","form-control numeric quantity-field")."</div>";
						}
						echo "</div>";
					}
					echo "</div>";
				}
				echo "<div class=\"add2basket\">";
				if(catalogConfig::$ordersDisabled || !intval($row['g_vendor']['value']) || array_key_exists($row['g_id']['value'], $this->options_gids)){
					echo "<a  href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$row['g_id']['value']."&amp;alias=".$row['g_alias']['value'].$ggr_link_tail)."\" class=\"linkButton btn btn-info button-add2basket button-add2basket-readmore\" >".Text::_('Read more')."</a>";
				} else {
					echo "<a onclick=\"javascript:addToBasket".($row['g_is_single']['value'] ? "Single" : "")."('".$row['g_id']['value']."');\" class=\"linkButton btn btn-info button-add2basket button-add2basket-act\" >".Text::_('Add to basket')."</a>";
				}
				$favourites_class = "inFavourites";
				$onclick = "if(confirm('".Text::_("Are you sure")." ?')){removeFromFavourites(this, '".$row['g_id']['value']."', '#favourites_".$row['g_id']['value']."');}";
				echo "<div class=\"favouritesButtons ".$favourites_class."\"><a title=\"".Text::_("Remove from favourites")."\" onclick=\"".$onclick."\" class=\"removeFromFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-remove\"></i></a></div>";
				echo "</div>";
				/*************************************************************************************/
				?>
				</div>
			</div>	
		</div><!-- конец блока данных-->
<?php
	}
	echo "</div>";
} else {
	echo "<div class=\"no_data\">".Text::_("Data absent")."</div>";
}

echo $_html_footer;
?>
</div>
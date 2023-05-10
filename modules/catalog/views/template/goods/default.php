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
	$list_mode_class = " goods-list-grid";
}

$tp=User::getInstance()->u_pricetype;
$field_price="g_price_".$tp;
?>
<div class="catalog_list">
<?php
if($this->grp) $ggr_link_tail=($this->grp->ggr_alias ? "&ggr_alias=".$this->grp->ggr_alias : "").($this->grp->ggr_alias ? "&ggr_id=".$this->grp->ggr_id : "");
else $ggr_link_tail = "";
if ($consider_parents && $this->grp){
	echo "<h1 class=\"title\">".$this->grp->ggr_name."</h1>";
	if ($this->grp->ggr_image || strlen($this->grp->ggr_comment)>15) {
		echo "<div class=\"group-short\"><div class=\"row\">";
		$filelink="";
		if ($this->grp->ggr_image) {
			$filename=BARMAZ_UF_PATH."catalog".DS."ggr".DS."i".DS.Files::splitAppendix($this->grp->ggr_image,true);
			if (Files::isImage($filename))	{
				$filelink=BARMAZ_UF."/catalog/ggr/i/".Files::splitAppendix($this->grp->ggr_image);
			}
		} 
		if ($filelink)	{
			echo "<div class=\"col-sm-4 group-img\"><img width=\"100%\" alt=\"".$this->grp->ggr_name."\" src=\"".$filelink."\" /></div>";
			echo "<div class=\"col-sm-8\">".$this->grp->ggr_comment."</div>";
		} else echo "<div class=\"col-sm-12\">".$this->grp->ggr_comment."</div>";
		echo "</div></div>";
	}
}

if ((isset($this->filter_vendor) && $this->filter_vendor) || (isset($this->filter_manufacturer) && $this->filter_manufacturer)){
	echo "<h2 class=\"title subtitle\">";
	if (isset($this->filter_vendor) && $this->filter_vendor) echo Text::_("Vendor").": <a class=\"customFilterLink\" onclick=\"resetCustomGoodsFilter('vendor')\" title=\"".Text::_("Reset filter")."\">".$this->filter_vendor->v_store_name."</a>";
	if (isset($this->filter_manufacturer) && $this->filter_manufacturer) echo Text::_("Manufacturer").": <a class=\"customFilterLink\" onclick=\"resetCustomGoodsFilter('manufacturer')\" title=\"".Text::_("Reset filter")."\">".$this->filter_manufacturer->mf_name."</a>";
	echo "</h2>";
}

if (isset($this->kwds) && $this->kwds){
	echo "<h2 class=\"title subtitle\">";
	echo Text::_("Search results for the query")." \"".$this->kwds."\"";
	echo "</h2>";
}
if ($consider_parents&&count($this->childs)>0) {
	echo "<div class=\"groups-list row\">";
	foreach($this->childs as $group) {
		$href="index.php?module=catalog&amp;view=goods&amp;psid=".$group->ggr_id."&amp;alias=".$group->ggr_alias;
		if (isset($this->kwds) && $this->kwds){
			$href .= "&kwds=".urlencode($this->kwds);
		}
		echo "<div class=\"goodsgroup ".$r_class."\"><div class=\"quadro-wrapper\">";
		$filelink="/images/blank.gif";
		$group_class="group-img group-img-empty";
		if (isset($group->ggr_thumb)) {
			$thumb=BARMAZ_UF_PATH."catalog".DS."ggr".DS.Files::splitAppendix($group->ggr_thumb,true);
			if (Files::isImage($thumb))	{
				$thumb=BARMAZ_UF."/catalog/ggr/".Files::splitAppendix($group->ggr_thumb);
				$group_class="group-img";
			} else $thumb="";
		} else $thumb = $this->getEmptyImage();
		if (!$thumb) $thumb = $this->getEmptyImage();
		$thumb=HTMLControls::renderImage($thumb, false, "100%", 0, "", $group->ggr_id.") ".$group->ggr_name);
		echo "<div class=\"".$group_class."\"><a class=\"group-title\" href=\"".Router::_($href)."\">".$thumb."</a></div>";
		echo "<div class=\"group-link\"><a class=\"group-title\" href=\"".Router::_($href)."\">".$group->ggr_name."</a></div>";
		echo "</div></div>";
	}
	echo "</div>";
}

echo "<div id=\"goods-list-controls-block\" class=\"float-fix\">";
$sortBlockClass = "";
$filterBlockClass = "";
$switcherBlockClass = " hidden-xs";

echo "<div class=\"goods-list-mode-switcher".$switcherBlockClass."\"><div class=\"goods-list-mode-switcher-wrapper float-fix\">";
echo "<a rel=\"nofollow\" onclick=\"toggleGoodsListMode(this);\" class=\"switch-layout switch-list".($list_mode=="list" ? " active" :"")."\" title=\"".Text::_("List")."\"></a>";
echo "<a rel=\"nofollow\" onclick=\"toggleGoodsListMode(this);\" class=\"switch-layout switch-grid".($list_mode=="list" ? "" :" active")."\" title=\"".Text::_("Tile")."\"></a>";
echo "</div></div>";

if($this->show_filter_button) { // id="filterBlock" is necessary for widget
	echo "<div id=\"filterBlock\" class=\"filter-block".$filterBlockClass."\"><div class=\"filter-button\">";
	echo "<a title=\"".Text::_("Filter")."\" class=\"btn btn-info\" onclick=\"javascript:showFilter('catalog','goods','','".$this->multy_code."','".Text::_("Filter")."', 0, '".$this->get("controller")."'); return false;\" class=\"linkButton\" rel=\"nofollow\"><i class=\"glyphicon glyphicon-filter\" aria-hidden=\"true\"></i></a>";
	echo "</div></div>";
}
/******************** Sorting settings start ********************/
$sort_block_layout = 1;
$sort_only_arr = array();
// $sort_only_arr = array("g_name", "g_ordering", $field_price);
/******************** Sorting settings stop ********************/
if($this->show_sort_links && is_array($_table_body_arr) && count($_table_body_arr)>0) {
	echo "<div class=\"sort-block".$sortBlockClass."\">";
	if(!$sort_block_layout){
		echo "<ul class=\"nav nav-pills\">";
		foreach ($_table_header_arr as $fld_name=>$sorting){
			if(count($sort_only_arr) && !in_array($fld_name, $sort_only_arr)) continue;
			$sorting_class=$sorting["orderby_class"];
			$sorting_icon="";
			if($sorting_class && $sorting_class != "no-sort"){
				if($sorting["orderby_class"]=="order-up"){
					$sorting_icon="<i class=\"glyphicon glyphicon-chevron-up\" aria-hidden=\"true\"></i>";
					$sorting_class.=" active";
				} elseif($sorting["orderby_class"]=="order-down"){
					$sorting_icon="<i class=\"glyphicon glyphicon-chevron-down\" aria-hidden=\"true\"></i>";
					$sorting_class.=" active";
				}
				if ($sorting["onclick"]) echo "<li class=\"".$sorting_class."\"><a  ".$sorting["onclick"].">".$sorting["html"].$sorting_icon."</a></li>";
			}
		}
		echo "</ul>";
	} else {
		$current_ref=$sort_base_ref;
		echo "<div class=\"sorting-select\">";
		echo "<span class=\"label\">".Text::_("Ordering")."</span>";
		$sorter = "<select onchange=\"document.location.href=this.value\">";
		foreach ($_table_header_arr as $fld_name=>$sorting){
			if($fld_name == "g_fullname" && $this->g_name_visible) continue;
			if(count($sort_only_arr) && !in_array($fld_name, $sort_only_arr)) continue;
			$sorting_class = $sorting["orderby_class"];
			$sorting_class_asc = "";
			$sorting_class_desc = "";
			$sort_option_selected_asc = "";
			$sort_option_selected_desc = "";
			if($sorting_class && $sorting_class != "no-sort" && $sorting["onclick"]){
				if($sorting_class == "order-up"){ // DESC
					$sorting_class_desc = "active";
					$sort_option_selected_desc = " selected=\"selected\"";
				} elseif($sorting_class == "order-down"){ // ASC
					$sorting_class_asc = "active";
					$sort_option_selected_asc = " selected=\"selected\"";
				}
				$sorter.= "<option class=\"".$sorting_class_asc."\" ".$sort_option_selected_asc." value=\"".Router::_($sort_base_ref."&sort=".$fld_name."&orderby=ASC")."\">".$sorting["html"]." (".mb_strtolower(Text::_("Ascending")).")"."</option>";
				$sorter.= "<option class=\"".$sorting_class_desc."\" ".$sort_option_selected_desc." value=\"".Router::_($sort_base_ref."&sort=".$fld_name."&orderby=DESC")."\">".$sorting["html"]." (".mb_strtolower(Text::_("Descending")).")"."</option>";
			}
		}
		$fld_name = "ordering";
		$sorting_class_asc = "";
		$sorting_class_desc = "";
		$sort_option_selected_asc = "";
		$sort_option_selected_desc = "";
		if($sort == $fld_name){
			if($orderby == "DESC"){ // DESC
				$sorting_class_desc = "active";
				$sort_option_selected_desc = " selected=\"selected\"";
			} elseif($orderby == "ASC"){ // ASC
				$sorting_class_asc = "active";
				$sort_option_selected_asc = " selected=\"selected\"";
			}
		}
		$sorter.= "<option class=\"".$sorting_class_asc."\" ".$sort_option_selected_asc." value=\"".Router::_($sort_base_ref."&sort=".$fld_name."&orderby=ASC")."\">".Text::_("Sort order")." (".mb_strtolower(Text::_("Ascending")).")"."</option>";
		$sorter.= "<option class=\"".$sorting_class_desc."\" ".$sort_option_selected_desc." value=\"".Router::_($sort_base_ref."&sort=".$fld_name."&orderby=DESC")."\">".Text::_("Sort order")." (".mb_strtolower(Text::_("Descending")).")"."</option>";
		$sorter.= "</select>";
		echo $sorter; 
		echo "</div>";
	}
	echo "</div>";
}

echo "</div>"; // #goods-list-controls-block

if ($filtered) echo "<div class=\"filtered\"><div class=\"row\"><div class=\"col-sm-12\">".Text::_("Is filtered").":</b>&nbsp;".$filtered."</div></div></div>";

// данные которые известны, и сверстаны на основании метадаты
//$_form, $_html_pan, $_table_class, $filtered, $_table_header_arr, $_table_body_arr, $_html_footer
$arr_setfields=array($field_price,"g_wmeasure",'g_currency',"g_thumb","g_medium_image","g_image","g_name","g_fullname","g_comments","g_new","g_hit","g_file_demo","g_file");
$gids=array();
if(is_array($_table_body_arr) && count($_table_body_arr)) {
	echo "<div class=\"goods-list row midi-gutter".$list_mode_class."\">";
	foreach($_table_body_arr as $row) { ?>
		<div class="goods-element<?php echo ($r_class ? " ".$r_class : ""); ?>">		
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
				if($meta->keycurrency) $currency_val=$row[$fldcurrency]["value"]; else $currency_val="";
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
				$favourites_block = "";
				$compare_block = "";
				if($this->enable_favourites){
					if(array_key_exists($row['g_id']['value'], $this->favourites)) $favourites_class = "inFavourites"; else $favourites_class = "notInFavourites";
					$favourites_block.= "<div class=\"favouritesButtons ".$favourites_class."\">";
					$favourites_block.= "<a title=\"".Text::_("Add to favourites")."\" onclick=\"addToFavourites(this, '".$row['g_id']['value']."');\" class=\"addToFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart-empty\"></i></a>";
					$favourites_block.= "<a title=\"".Text::_("Remove from favourites")."\" onclick=\"removeFromFavourites(this, '".$row['g_id']['value']."');\" class=\"removeFromFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart\"></i></a>";
					$favourites_block.= "</div>";
				}
				if($this->enable_compare){
					if(array_key_exists($row['g_id']['value'], $this->compare)) $compare_class = "inCompare"; else $compare_class = "notInCompare";
					$compare_block.= "<div class=\"compareButtons ".$compare_class."\" >";
					$compare_block.= "<a title=\"".Text::_("Add to compare")."\" onclick=\"addToCompare(this, '".$row['g_id']['value']."');\" class=\"addToCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>";
					$compare_block.= "<a title=\"".Text::_("Remove from compare")."\" onclick=\"removeFromCompare(this, '".$row['g_id']['value']."');\" class=\"removeFromCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>";
					$compare_block.= "</div>";
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
				echo $favourites_block;
				echo $compare_block;
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
	if ($filtered && $consider_parents && !count($this->childs)) echo "<div class=\"no_data\">".Text::_("Data absent")."</div>";
}

echo $_html_footer;
?>
</div>
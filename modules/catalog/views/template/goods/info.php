<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$spr_tmpl_overrided=1;
$_base_koeff = 0;
$_pack_koeff = 0;
$_old_val_price = 0;
$tp=User::getInstance()->u_pricetype;
$field_price="g_price_".$tp;
$arr_setfields=array($field_price,"g_wmeasure",'g_currency',"g_thumb","g_medium_image","g_image","g_name","g_fullname","g_comments","g_new","g_hit","g_file_demo","g_file");
$thumb="";
$_html="";
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
?>
<div class="catalog_info"><form id="goods_info" name="goods_info"  enctype="multipart/form-data">
	<?php echo Event::raise("share.goods"); ?>
	<?php if(!$_info_arr['g_name']['hidden']) { ?>
		<h1 class="title"><?php echo htmlspecialchars($_info_arr['g_name']['value'],NULL,NULL,FALSE);?></h1>
	<?php } elseif(!$_info_arr['g_fullname']['hidden']) { ?>
		<h1 class="title"><?php echo htmlspecialchars($_info_arr['g_fullname']['value'],NULL,NULL,FALSE);?></h1>
	<?php } ?>
	<div class="row">
		<div class="col-sm-6">
			<?php 
			if ($_info_arr['g_hit']['value']) echo "<div class=\"ishit\"></div>";
			if ($_info_arr['g_new']['value'])  echo "<div class=\"isnew\"></div>";
			$medium=$this->getImage($_info_arr['g_medium_image']['value'],2);
			if (!$medium) { $thumb = $this->getImage($_info_arr['g_thumb']['value'],1); }
			if (!$thumb) { $thumb = $this->getEmptyImage(); }
			$fullimg=$this->getImage($_info_arr['g_image']['value']);
			if($_info_arr['g_medium_image']['hidden']!=1 && $medium){
				$thumb=HTMLControls::renderImage($medium,false,0,0,"",$_info_arr['g_name']['value']);
				$_html.="<div class=\"g_thumb\">";
				if($fullimg) $_html.="<a class=\"relpopup\" href=\"".$fullimg."\">".$thumb."</a>";
				else $_html.="<span class=\"nolink\">".$thumb."</span>";
				$_html.= "</div>";
			} else {
				if($_info_arr['g_thumb']['hidden']!=1){
					$thumb=HTMLControls::renderImage($thumb,false,0,0,"",$_info_arr['g_name']['value']);
					$_html.="<div class=\"g_thumb\">";
					if($fullimg) $_html.="<a class=\"relpopup\" href=\"".$fullimg."\">".$thumb."</a>";
					else $_html.="<span class=\"nolink\">".$thumb."</span>";
					$_html.= "</div>";
				}
			}
			?>
			<div class="row"><div class="col-sm-12"><?php  echo $_html; ?></div></div>
			<?php if (count($this->images)) { ?>
			<div class="row g_mini_thumbs row-cells-autoheight mini-gutter">
			<?php 
				$add_img_counter=0;
				foreach($this->images as $add_img){
					$add_img_counter++;
					$thumb=$this->getImage($add_img->i_thumb,1);
					if (!$thumb) $thumb = $this->getEmptyImage();
					$fullimg=$this->getImage($add_img->i_image);
					$thumb=HTMLControls::renderImage($thumb,false,catalogConfig::$thumb_width,0,$add_img->i_title,$add_img_counter.".".$_info_arr['g_name']['value']);
					echo "<div class=\"g_mini_thumb col-xs-3 row-cell-wrapper\">";
					if($fullimg) echo "<a title=\"".$add_img->i_title."\" class=\"relpopupwt\" href=\"".$fullimg."\">".$thumb."</a>";
					else echo $thumb;
					echo "</div>";
				}
			?>
			</div>
			<?php } ?>
			
			<?php if($_info_arr['g_comments']['hidden']!=1 && $_info_arr['g_comments']['html']){ ?>
			<div class="row"><div class="col-sm-12">
				<?php 
				echo "<div class=\"g_comment\">";
				echo $_info_arr['g_comments']['html'];
				echo "</div>";
				?>
			</div></div>
			<?php } ?>
		</div>
		<div class="col-sm-6">
			<?php
			if($this->enable_favourites || $this->enable_compare){
				if(array_key_exists($_info_arr['g_id']['value'], $this->favourites)) $favourites_class = "inFavourites"; else $favourites_class = "notInFavourites";
				if(array_key_exists($_info_arr['g_id']['value'], $this->compare)) $compare_class = "inCompare"; else $compare_class = "notInCompare";
				echo "<div class=\"actionsButtons\">";
				if($this->enable_favourites){
					echo "	<div class=\"favouritesButtons ".$favourites_class."\">
								<a title=\"".Text::_("Add to favourites")."\" onclick=\"addToFavourites(this, '".$_info_arr['g_id']['value']."');\" class=\"addToFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart-empty\"></i></a>
								<a title=\"".Text::_("Remove from favourites")."\" onclick=\"removeFromFavourites(this, '".$_info_arr['g_id']['value']."');\" class=\"removeFromFavourites linkButton btn btn-info\"><i class=\"glyphicon glyphicon-heart\"></i></a>
							</div>";
				}
				if($this->enable_compare){
					echo "	<div class=\"compareButtons ".$compare_class."\" >
								<a title=\"".Text::_("Add to compare")."\" onclick=\"addToCompare(this, '".$_info_arr['g_id']['value']."');\" class=\"addToCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>
								<a title=\"".Text::_("Remove from compare")."\" onclick=\"removeFromCompare(this, '".$_info_arr['g_id']['value']."');\" class=\"removeFromCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-stats\"></i></a>
							</div>";
				}
				echo "</div>";
			}
			// Цена для продажной единицы
			// Скидка для продажной единицы
			// Вес для базовой единицы
			// Размер для базовой единицы
			$sell_measure=$base_measure=$_info_arr['g_measure']['value'];
			$pack_measure=$_info_arr['g_pack_measure']['value'];
			switch($_info_arr['g_selltype']['value']){
				case 1: // Ед.изм.упаковки
					$sell_measure=$_info_arr['g_pack_measure']['value'];
					break;
				case 2: // Ед.изм.веса
					$sell_measure=$_info_arr['g_wmeasure']['value'];
					break;
				case 3: // Ед.изм.объема
					$sell_measure = $_info_arr['g_vmeasure']['value'];
					break;
				case 4:
				case 5:
				case 0:
				default: // Базовая ед.изм
					break;
			}
			if(!catalogConfig::$hide_prices && $_info_arr[$field_price]['hidden']!=1){
				//if(floatval($_info_arr[$field_price]['value'])>0){ 
					$_val=floatval($_info_arr[$field_price]['value']);
					$_base_val=0;
					$_pack_val=0;
					$volume = 0;
					switch($_info_arr['g_selltype']['value']){
						case 1: // Ед.изм.упаковки
							if ($_info_arr['g_pack_koeff']['value']) $_base_val=round($_val / $_info_arr['g_pack_koeff']['value'], 2);
							$_pack_val=$_base_val;
							break;
						case 2: // Ед.изм.веса
							if ($_info_arr['g_weight']['value']) $_base_val=round($_val * $_info_arr['g_weight']['value'],2);
							$_pack_val=$_base_val * $_info_arr['g_pack_koeff']['value'];
							break;
						case 3: // Ед.изм.объема
							$height = Measure::getInstance()->convert($_info_arr['g_height']['value'], $_info_arr['g_size_measure']['value'], catalogConfig::$size4volume_measure);
							$width = Measure::getInstance()->convert($_info_arr['g_width']['value'], $_info_arr['g_size_measure']['value'], catalogConfig::$size4volume_measure);
							$length = Measure::getInstance()->convert($_info_arr['g_length']['value'], $_info_arr['g_size_measure']['value'], catalogConfig::$size4volume_measure);
							$_volume = $height * $width * $length;
							if($_volume){
								$volume = Measure::getInstance()->convert($_volume, catalogConfig::$default_vol_measure, $_info_arr['g_vmeasure']['value']);
								$_base_val = $_val * $volume;
								$_pack_val = $_base_val * $_info_arr['g_pack_koeff']['value'];
							}
							break;
						case 4:
						case 5:
						case 0:
						default: // Базовая ед.изм
							$_base_val=$_val;
							$_pack_val=$_base_val * $_info_arr['g_pack_koeff']['value'];
							break;
					}
					
					if($meta->keycurrency) $currency_val=$_info_arr[$meta->keycurrency]['value']; else $currency_val=0;
					if(floatval($_info_arr[$field_price]['value'])>0){
						$_old_val_price = Currency::getInstance()->convert($_val, $currency_val);
					} else {
						$_old_val_price = 0;
					}
					$_val_price = $this->applyDiscounts($_info_arr['g_id']['value'], $_old_val_price, $this->discounts);
					if($_val_price < 0) $_val_price = 0;
					$_base_koeff = 0;
					$_pack_koeff = 0;
					
					if (($base_measure!=$sell_measure) && catalogConfig::$show_base_price && $_base_val && $_old_val_price){
						$_old_base_val = Currency::getInstance()->convert($_base_val, $currency_val);
						// $_base_val = $this->applyDiscounts($_info_arr['g_id']['value'], $_old_base_val, $this->discounts);
						$_base_koeff = $_old_base_val / $_old_val_price;
						$_base_val = $_val_price * $_base_koeff;
						echo "<div class=\"g_price\">";
						//if($_old_base_val != $_base_val) echo "	<span class=\"old_price\"><span class=\"old_base_price_val\">".number_format($_old_base_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						//echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($base_measure)."</span> <span class=\"price_val\"><span id=\"current_base_price_val\" class=\"current_base_price_val\">".number_format($_base_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						if($_old_base_val != $_base_val) echo "	<span class=\"old_price\"><span class=\"old_base_price_val\"></span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($base_measure)."</span> <span class=\"price_val\"><span id=\"current_base_price_val\" class=\"current_base_price_val\"></span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						echo "</div>";
					} else {
						$_old_base_val = $_old_val_price;
					}
					if (($pack_measure != $sell_measure) && catalogConfig::$show_pack_price && $_pack_val && $_old_val_price){
						$_old_pack_val = Currency::getInstance()->convert($_pack_val, $currency_val);
						// $_pack_val = $this->applyDiscounts($_info_arr['g_id']['value'], $_old_pack_val, $this->discounts);
						$_pack_koeff = $_old_pack_val / $_old_val_price;
						$_pack_val= $_val_price * $_pack_koeff;
						echo "<div class=\"g_price\">";
						//if($_old_pack_val!=$_pack_val) echo "	<span class=\"old_price\"><span class=\"old_pack_price_val\">".number_format($_old_pack_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						//echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($pack_measure)."</span> <span class=\"price_val\"><span id=\"current_pack_price_val\" class=\"current_pack_price_val\">".number_format($_pack_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						if($_old_pack_val!=$_pack_val) echo "	<span class=\"old_price\"><span class=\"old_pack_price_val\"></span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($pack_measure)."</span> <span class=\"price_val\"><span id=\"current_pack_price_val\" class=\"current_pack_price_val\"></span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						echo "</div>";
					} else {
						$_old_pack_val= $_old_val_price;
					}
					//if($_old_val_price || $_val_price){
						echo "<div class=\"g_price g_price_4\">";
						//if($_old_val_price!=$_val_price) echo "	<span class=\"old_price\"><span class=\"old_price_val\">".number_format($_old_val_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						//echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($sell_measure)."</span> <span class=\"price_val\"><span id=\"current_price_val\" class=\"current_price_val\">".number_format($_val_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						if($_old_val_price!=$_val_price) echo "	<span class=\"old_price\"><span class=\"old_price_val\"></span> ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
						echo "	<span class=\"measure_val\">".Text::_("Price for")." ".Measure::getInstance()->getShortName($sell_measure)."</span> <span class=\"price_val\"><span id=\"current_price_val\" class=\"current_price_val\"></span> <span class=\"currency_val\">".Currency::getShortName(DEFAULT_CURRENCY)."</span></span>";
						echo "</div>";
					//}
				//}
			}
			if (!catalogConfig::$ordersDisabled && intval($_info_arr['g_vendor']['value'])){
				echo "<div class=\"r_basket row\">";
				echo "	<div class=\"r_quantity_label col-xs-3\">";
				echo HTMLControls::renderLabelField("quant_".$_info_arr['g_id']['value'],Text::_("quantity")." (".Measure::getInstance()->getShortName($sell_measure).")");
				echo "	</div>";
				echo "	<div class=\"r_quantity col-xs-4\">";
				echo HTMLControls::renderInputText("quant_".$_info_arr['g_id']['value'],"1","5","","quant_".$_info_arr['g_id']['value'],"form-control numeric quantity-field",false,true,"",array("onchange"=>"recalcPrice()"));
				echo "	</div>";
				echo "	<div class=\"add2basket col-xs-5\">
								<input class=\"linkButton btn btn-info\" type=\"submit\" value=\"".Text::_('Add to basket')."\" />
								<input type=\"hidden\" value=\"".$_info_arr['g_id']['value']."\" name=\"g_id\" id=\"g_id\" />
							</div>";
				echo "</div>";
			}
			?>
			<div class="characteristics">
				<?php 
				foreach($_info_arr as $key=>$val)	{
					if(!in_array($key, $arr_setfields) && $val['hidden']!=1 && $val["html"]) {
						if ($val['input_type']=="filepath"){
							if($this->canDownloadFiles && substr($key, 0, 3)=="df_"){
								echo "<div class=\"char_element row mini-gutter char_element-".$key."\">";
								echo "	<div class=\"char_label col-xs-5\">".HTMLControls::renderLabelField(false, $val["title"])."</div>";
								echo "	<div class=\"char_value col-xs-7\" id=\"".$key."_val\">";
								echo "		<a rel=\"nofollow\" href=\"".Router::_("index.php?module=catalog&task=downloadFile&psid=".$_info_arr['g_id']['value'])."&file=".substr($key,3)."\" class=\"downloadButton\" alt=\"".Text::_('Download file')."\">".Text::_('Download file')."</a>";
								echo "	</div>";
								echo "</div>";
							}
						} elseif ($val['input_type']=="image" && !$val['value']) {
							continue;
						} else {
							echo "<div class=\"char_element row mini-gutter char_element-".$key."\">";
							echo "	<div class=\"char_label col-xs-5\">".HTMLControls::renderLabelField(false, $val["title"])."</div>";
							echo "	<div class=\"char_value col-xs-7\" id=\"".$key."_val\">".$val["html"]."</div>";
							echo "</div>";
						}
					}
				}
				if ($_info_arr['g_file_demo']['value']&&($_info_arr['g_type']['value']==3 ||$_info_arr['g_type']['value']==4)){
					echo "<div class=\"char_element r_demo row mini-gutter char_element-g_file_demo\">";
					echo "	<div class=\"char_label col-xs-5\"></div>";
					echo "	<div class=\"char_value col-xs-7\" id=\"g_file_demo_val\">";
					if ($this->canDownloadDemo) echo "<a href=\"".Router::_("index.php?module=catalog&task=downloadDemo&psid=".$_info_arr['g_id']['value'])."\" class=\"btn btn-info downloadButton\" alt=\"".Text::_('Download file')."\">".Text::_('Download file')."</a>";
					else echo "<a onclick=\"alert('".Text::_("Download denied").". ".Text::_("You are not authorized")."');\" class=\"btn btn-info downloadButton\" alt=\"".Text::_('Download file')."\">".Text::_('Download file')."</a>";
					echo "	</div>";
					echo "</div>";
				}
				?>
			</div>
			<?php if(count($this->complect)){ ?>
			<div class="complect-set">
				<h4 class="title"><?php echo Text::_("Kit contents"); ?></h4>
				<?php foreach($this->complect as $kit_item){ 
					echo "<div class=\"char_element row mini-gutter char_element-".$key."\">";
					echo "	<div class=\"char_label col-xss-9 col-xs-9\">".HTMLControls::renderLabelField(false, $kit_item->g_name)."</div>";
					echo "	<div class=\"char_value col-xss-3 col-xs-3\" id=\"".$key."_val\">";
					echo $kit_item->s_quantity;
					switch($kit_item->g_selltype){
						case 1: // Ед.изм.упаковки
							$kit_item_measure=$kit_item->g_pack_measure;
							break;
						case 2: // Ед.изм.веса
							$kit_item_measure=$kit_item->g_wmeasure;
							break;
						case 3: // Ед.изм.объема
							$kit_item_measure = $kit_item->g_vmeasure;
							break;
						case 4:
						case 5:
						case 0:
						default: // Базовая ед.изм
							$kit_item_measure=$kit_item->g_measure;
							break;
					}
					echo "&nbsp;";
					echo Measure::getInstance()->getShortName($kit_item_measure);
					echo "</div>";
					echo "</div>";
				} ?>
			</div>
			<?php } ?>
			<?php if(count($this->options)) { ?>
			<div class="options">
			<?php 
				foreach ($this->options as $ok=>$oval){
					$title_prefix=($oval->o_required  ? "* " : "");
					echo"<div class=\"option_row row mini-gutter\">";
					switch($oval->t_input_type){
						case "file":
							echo"<div class=\"col-xs-12\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
							echo"<div class=\"option_el col-xs-12\"><div class=\"fileselector\">";
							echo HTMLControls::renderInputFile('option_'.$oval->od_id, "", 20, 'option_'.$oval->od_id, 'form-control', $oval->o_required);
							echo HTMLControls::renderButton('option_'.$oval->od_id."_clear", "", "button", "", "clrfile","clearfieldVal('"."option_".$oval->od_id."')", Text::_("Clear"));
							echo "</div></div>";
						break;
						case "textarea":
							echo"<div class=\"col-xs-12\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
							echo"<div class=\"option_el col-xs-12\">";
							echo HTMLControls::renderBBCodeEditor('option_'.$oval->od_id, 'option_'.$oval->od_id, '', 35, 3, 'form-control', false, $oval->o_required);
							echo "</div>";
						break;
						case "text":
							echo"<div class=\"col-xs-5\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
							echo"<div class=\"option_el col-xs-7\">";
							$js_arr=array();
							if(in_array($oval->t_val_type, array("date", "time", "datetime"))){
								echo HTMLControls::renderDateTimeSelector('option_'.$oval->od_id,"", ($oval->t_val_type!="date"), ($oval->t_val_type!="time"), $oval->o_required);
							} else {
								echo HTMLControls::renderInputText(
									'option_'.$oval->od_id, 
									($oval->t_val_type=="int" || $oval->t_val_type=="float" ? 0 : ""), 
									"", 
									"", 
									'option_'.$oval->od_id, 
									'form-control'.($oval->t_val_type=="int" ? " numeric" : "").($oval->t_val_type=="float" ? " decimal" : ""), 
									false, 
									$oval->o_required, 
									'', 
									$js_arr);
							}
							echo "</div>";
							break;
						case "select":
							$selected = " selected=\"selected\"";
							echo"<div class=\"col-xs-5\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
							echo"<div class=\"option_el option_select col-xs-7\">";
							$_zero_fill=!$oval->o_required;
							$js="recalcPrice()";
							$_arr=array();
							$_sel_val=0;
							$sel_image="";
							echo "<select class=\"singleSelect form-control\" name=\"option_".$oval->od_id."\" id=\"option_".$oval->od_id."\"".($oval->o_required ? " required=\"required\"" : "")." onchange=\"changeOptionImage(".$oval->od_id."); recalcPrice();\">";
							if(!$oval->o_required) {
								echo "<option value=\"\"".$selected.">".Text::_("Not selected")."</option>";
								$selected="";
							}
							foreach ($oval->optionsData as $od_key=>$od_val){
								if($selected && $oval->haveImage) $sel_image="<div id=\"option_select_image_".$oval->od_id."\" class=\"select_image\">".HTMLControls::renderImage($od_val->ovd_thumb, false, "100%", "", $od_val->ov_name , "")."</div>";
								echo "<option value=\"".$od_key."\"".$selected.">".$od_val->ov_name."</option>";
								$selected="";
							}
							echo "</select>";
							echo $sel_image;
							if($oval->o_is_quantitative) echo "	<input value=\"1\" name=\"option_quant_".$oval->od_id."\" id=\"option_quant_".$oval->od_id."\" class=\"form-control quantity-control\" onchange=\"recalcPrice()\" type=\"text\" />";
							echo "</div>";
						break;
						case "radiogroup":
							$checked = " checked=\"checked\"";
							$js="recalcPrice()";
							if($oval->haveImage || $oval->o_is_quantitative){
								echo"<div class=\"col-xs-12\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
								echo"<div class=\"option_el col-xs-12\">";
								if(count($oval->optionsData)<3) $opt_cols=2; else $opt_cols=3;
								$opt_counter=0;
								foreach ($oval->optionsData as $od_key=>$od_val){
									$opt_counter++;
									if($opt_counter % $opt_cols == 1) echo "<div class=\"row\">";
									echo "<div class=\"col-xs-".(12/$opt_cols)." radio".($oval->haveImage ? "_image" : "")."\">";
									if(!$oval->haveImage) echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"radio\" onchange=\"recalcPrice()\" type=\"radio\" />";
									echo "	<label class=\"label\" for=\"option_".$oval->od_id."_".$od_key."\">";
									if($oval->haveImage && $od_val->ovd_thumb) echo HTMLControls::renderImage($od_val->ovd_thumb, false, "100%", "", $od_val->ov_name , "");
									echo "		<span class=\"option_title\">".$od_val->ov_name."</span>";
									echo "	</label>";
									if($oval->haveImage) echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"radio\" onchange=\"recalcPrice()\" type=\"radio\" />";
									if($oval->o_is_quantitative) echo "	<input value=\"1\" name=\"option_quant_".$oval->od_id."_".$od_key."\" id=\"option_quant_".$oval->od_id."_".$od_key."\" class=\"form-control quantity-control\" onchange=\"recalcPrice()\" type=\"text\" />";
									echo "</div>";
									if($opt_counter % $opt_cols == 0) echo "</div>";
									$checked="";
								}
								if($opt_counter % $opt_cols != 0) echo "</div>";
							} else {
								echo"<div class=\"col-xs-5\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
								echo"<div class=\"option_el col-xs-7\">";
								foreach ($oval->optionsData as $od_key=>$od_val){
									echo "<div class=\"radio\">";
									echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"radio\" onchange=\"recalcPrice()\" type=\"radio\" />";
									echo "	<label class=\"label\" for=\"option_".$oval->od_id."_".$od_key."\">";
									echo "		<span class=\"option_title\">".$od_val->ov_name."</span>";
									echo "	</label>";
									echo "</div>";
									$checked="";
								}
							}
							echo "</div>";
						break;
						case "checkbox":
							// $checked = " checked=\"checked\"";
							$checked="";
							$js="recalcPrice()";
							if($oval->haveImage || $oval->o_is_quantitative){
								echo"<div class=\"col-xs-12\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $oval->o_title)."</div>";
								echo"<div class=\"option_el col-xs-12\">";
								if(count($oval->optionsData)<3) $opt_cols=2; else $opt_cols=3;
								$opt_counter=0;
								foreach ($oval->optionsData as $od_key=>$od_val){
									$opt_counter++;
									if($opt_counter % $opt_cols == 1) echo "<div class=\"row\">";
//									$checked = " checked=\"checked\"";
									echo "<div class=\"col-xs-".(12/$opt_cols)." checkbox".($oval->haveImage ? "_image" : "")."\">";
									if(!$oval->haveImage) echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."[".$od_key."]\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"checkbox\" onchange=\"recalcPrice()\" type=\"checkbox\" />";
									echo "	<label class=\"label\" for=\"option_".$oval->od_id."_".$od_key."\">";
									if($oval->haveImage && $od_val->ovd_thumb) echo HTMLControls::renderImage($od_val->ovd_thumb, false, "100%", "", $od_val->ov_name , "");
									echo "		<span class=\"option_title\">".$od_val->ov_name."</span>";
									echo "	</label>";
									if($oval->haveImage) echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."[".$od_key."]\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"checkbox\" onchange=\"recalcPrice()\" type=\"checkbox\" />";
									if($oval->o_is_quantitative) echo "	<input value=\"1\" name=\"option_quant_".$oval->od_id."_".$od_key."\" id=\"option_quant_".$oval->od_id."_".$od_key."\" class=\"form-control quantity-control\" onchange=\"recalcPrice()\" type=\"text\" />";
									echo "</div>";
									if($opt_counter % $opt_cols == 0) echo "</div>";
//									$checked="";
								}
								if($opt_counter % $opt_cols != 0) echo "</div>";
							} else {
								echo"<div class=\"col-xs-5\">".HTMLControls::renderLabelField('option_'.$oval->od_id, $title_prefix.$oval->o_title)."</div>";
								echo"<div class=\"option_el col-xs-7\">";
								foreach ($oval->optionsData as $od_key=>$od_val){
									echo "<div class=\"checkbox\">";
									echo "	<input value=\"".$od_val->ovd_id."\" name=\"option_".$oval->od_id."[".$od_key."]\" id=\"option_".$oval->od_id."_".$od_key."\"".$checked." class=\"checkbox\" onchange=\"recalcPrice()\" type=\"checkbox\" />";
									if(count($oval->optionsData)>1) {
										echo "	<label class=\"label\" for=\"option_".$oval->od_id."_".$od_key."\">";
										echo "		<span class=\"option_title\">".$od_val->ov_name."</span>";
										echo "	</label>";
									}
									echo "</div>";
//									$checked="";
								}
							}
							echo "</div>";
						break;
					}
					echo "</div>";
				}
			?>
			</div>
			<?php } ?>
		</div>
	</div>
</form>	
</div>
<?php if (count($this->videos)) { ?>
<div class="videoset">
	<h4 class="title"><?php echo Text::_("Goods videos"); ?></h4>
	<div class="row">
		<?php foreach($this->videos as $video) {?>
			<div class="col-sm-6">
				<div class="goods-video">
					<?php	echo $this->renderPlayer($video, "100%", "auto"); ?>
					<p class="video-title"><?php echo addslashes($video->v_title); ?></p>
				</div>
			</div>
		<?php }?>
	</div>
</div>
<?php }?>
<?php if (count($this->analogs)) { ?>
<div class="analogs">
	<h4 class="title"><?php echo Text::_("Analogs"); ?></h4>
	<div class="row">
		<?php $thumb_width=catalogConfig::$thumb_width+10;
		$gids=array();
		foreach($this->analogs as $gds)	$gids[$gds['id']]=$gds['id'];
		$discounts=Module::getHelper("goods","catalog")->getDiscounts($gids);
		foreach($this->analogs as $analog) {?>
			<div class="goods-analog <?php echo $r_class ?>">
				<div class="quadro-wrapper"><?php
					$thumb=$this->getImage($analog['thumb'],1);
					if (!$thumb) $thumb = $this->getEmptyImage();
					$thumb=HTMLControls::renderImage($thumb,false,$thumb_width,0,"",$analog['id'].")".$analog['title']);
					echo "<div class=\"g_thumb\"><a class=\"analog-thumb\" href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$analog['id']."&amp;alias=".$analog['alias'])."\">".$thumb."</a></div>";
					echo "<div class=\"g_title\"><a class=\"analog-title\" href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$analog['id']."&amp;alias=".$analog['alias'])."\">".$analog["title"]."</a></div>";
					$_old_val = Currency::getInstance()->convert($analog[$field_price],$analog["g_currency"]);
					echo "<div class=\"g_price\">";
					$_val = Module::getHelper("goods","catalog")->applyDiscounts($analog['id'], $_old_val, $discounts);
					if($_old_val!=$_val) echo "<span class=\"old_price\">".number_format($_old_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
					echo Text::_("Price").": ".number_format($_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY);
					echo "</div>";
					?>
				</div>
			</div>
		<?php }?>
	</div>
</div>
<?php }?>
<?php if (count($this->additionals)) { ?>
<div class="additionals">
	<h4 class="title"><?php echo Text::_("Additional goods"); ?></h4>
	<div class="row">
		<?php $thumb_width=catalogConfig::$thumb_width+10; 
		$gids=array();
		foreach($this->additionals as $gds)	$gids[$gds['id']]=$gds['id'];
		$discounts=Module::getHelper("goods","catalog")->getDiscounts($gids);
		foreach($this->additionals as $additional) {?>
			<div class="goods-additional <?php echo $r_class ?>">
				<div class="quadro-wrapper"><?php
					$thumb=$this->getImage($additional['thumb'],1);
					if (!$thumb) $thumb = $this->getEmptyImage();
					$thumb=HTMLControls::renderImage($thumb,false,$thumb_width,0,"",$additional['id'].")".$additional['title']);
					echo "<div class=\"g_thumb\"><a class=\"additional-thumb\" href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$additional['id']."&amp;alias=".$additional['alias'])."\">".$thumb."</a></div>";
					echo "<div class=\"g_title\"><a class=\"additional-title\" href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$additional['id']."&amp;alias=".$additional['alias'])."\">".$additional["title"]."</a></div>";
					$_old_val = Currency::getInstance()->convert($additional[$field_price],$additional["g_currency"]);
					echo "<div class=\"g_price\">";
					$_val = Module::getHelper("goods","catalog")->applyDiscounts($additional['id'], $_old_val, $discounts);
					if($_old_val!=$_val) echo "<span class=\"old_price\">".number_format($_old_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY)."</span>";
					echo Text::_("Price").": ".number_format($_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY);
					echo "</div>";
					?>
				</div>
			</div>
		<?php }?>
	</div>
</div>
<?php }?>
<div class="comments_list"><!-- Блок комментариев -->
<?php if ($this->comm->commentsEnabled()&&$this->comm->checkACL("read")){
	$comments=$this->comm->renderComments();
	if($comments) {
		echo "<h4 class=\"titleBlockComment\">".Text::_('Comments').":</h4>";
		echo $comments;
	}
	echo $this->comm->renderCommentForm();	
}	?>
</div><!-- Конец блока комментариев -->

<!-- Блок расчетов -->
<?php require $this->getCustomLayoutPath("info_calc_unit");?>
<!-- Конец блока расчетов -->
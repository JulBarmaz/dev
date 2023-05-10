<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$baseWeight = round($_info_arr['g_weight']['value'],3);
$baseWeightMeasure = Measure::getInstance()->getShortName($_info_arr['g_wmeasure']['value']);
$baseHeight = round($_info_arr['g_height']['value'],3);
$baseWidth = round($_info_arr['g_width']['value'],3);
$baseLength = round($_info_arr['g_length']['value'],3);
$baseSizeMeasure = Measure::getInstance()->getShortName($_info_arr['g_size_measure']['value']);
$basePoints = round($_info_arr['g_points']['value'],0);
/****************** OPTIONS JS ARRAY FORMING ******************/
$opt_js="";
//$opt_js.="\n var basePrice = ".$_old_base_val.";";
//$opt_js.="\n var packPrice = ".$_old_pack_val.";";

$opt_js.="\n var base_koeff = ".$_base_koeff.";";
$opt_js.="\n var pack_koeff = ".$_pack_koeff.";";

$opt_js.="\n var sellPrice = ".$_old_val_price.";";
$opt_js.="\n var baseHeight = ".$baseHeight.";";
$opt_js.="\n var size_digits = ".catalogConfig::$size_digits.";";
$opt_js.="\n var weight_digits = ".catalogConfig::$weight_digits.";";
$opt_js.="\n var baseWidth = ".$baseWidth.";"; 
$opt_js.="\n var baseLength = ".$baseLength.";";
$opt_js.="\n var baseSizeMeasure = '".$baseSizeMeasure."';";
$opt_js.="\n var baseWeight = ".$baseWeight.";";
$opt_js.="\n var baseWeightMeasure = '".$baseWeightMeasure."';";
$opt_js.="\n var basePoints = ".$basePoints.";";
$opt_js.="\n var optionsData = new Array();";
$field_opt_price="ovd_price_".$tp;
if(count($this->options)){
	$i=0;
	foreach ($this->options as $js_opt_key=>$js_opt_val){
		$opt_js.="\n optionsData['".$i."'] = new Object();";
		$opt_js.="\n optionsData['".$i."'].optID = '".$js_opt_key."';";
		$opt_js.="\n optionsData['".$i."'].optType = '".$js_opt_val->t_input_type."';";
		$opt_js.="\n optionsData['".$i."'].optHaveImage = '".$js_opt_val->haveImage."';";
		$opt_js.="\n optionsData['".$i."'].optHaveQuantity = '".$js_opt_val->o_is_quantitative."';";
		$opt_js.="\n optionsData['".$i."'].optRequired = '".$js_opt_val->o_required."';";
		$opt_js.="\n optionsData['".$i."'].optDataCount = ".count($js_opt_val->optionsData).";";
		$opt_js.="\n optionsData['".$i."'].optData = new Array();";
		if(count($js_opt_val->optionsData)){
			$j=0;
			foreach($js_opt_val->optionsData as $js_ov_key=>$js_ov_val){
				$opt_js.="\n optionsData['".$i."'].optData['".$j."']= new Object();";
				$opt_js.="\n optionsData['".$i."'].optData['".$j."']=
					{
					'id' : ".$js_ov_val->ovd_id.",
					'val_id' : ".$js_ov_val->ovd_val_id.",
					'opt_id' : ".$js_opt_key.",
					'name' : '".$js_ov_val->ov_name."',
					'price_sign' : '".$js_ov_val->ovd_price_sign."',
					'price' : ".Currency::getInstance()->convert($js_ov_val->{$field_opt_price}, $_info_arr["g_currency"]["value"]).",
					'weight_sign' : '".$js_ov_val->ovd_weight_sign."',
					'weight' : ".$js_ov_val->ovd_weight.",
					'points_sign' : '".$js_ov_val->ovd_points_sign."',
					'points' : ".$js_ov_val->ovd_points.",
					'length_sign' : '".$js_ov_val->ovd_length_sign."',
					'length' : ".$js_ov_val->ovd_length.",
					'width_sign' : '".$js_ov_val->ovd_width_sign."',
					'width' : ".$js_ov_val->ovd_width.",
					'height_sign' : '".$js_ov_val->ovd_height_sign."',
					'height' : ".$js_ov_val->ovd_height.",
					'decrease_stock' : ".$js_ov_val->ovd_check_stock.",
					'stock' : ".$js_ov_val->ovd_stock.",
					'thumb' : '".$js_ov_val->ovd_thumb."'
					};";
				$j++;
			}
		}
		$i++;
	}
}
$opt_js.="\n var discountsData = new Array();";
$gid = $_info_arr['g_id']['value'];
$i=0;
if(array_key_exists($gid, $this->discounts) && count($this->discounts[$gid])){
	foreach ($this->discounts[$gid] AS $discount){
		$opt_js.="\n discountsData['".$i."'] = new Object();";
		$opt_js.="\n discountsData['".$i."'].d_sign = '".$discount["d_sign"]."';";
		$opt_js.="\n discountsData['".$i."'].d_value = '".$discount["d_value"]."';";
		$opt_js.="\n discountsData['".$i."'].d_stop = '".$discount["d_stop"]."';";
		$i++;
		if($discount["d_stop"]) break;
	}
}
$opt_js.="\n var extPricesData = new Array();";
if(array_key_exists($gid, $this->extendedPrices) && count($this->extendedPrices[$gid])){
	$i=0;
	foreach ($this->extendedPrices[$gid] AS $extendedPrice){
		$opt_js.="\n extPricesData['".$i."'] = new Object();";
		$opt_js.="\n extPricesData['".$i."'].p_quantity = ".$extendedPrice["p_quantity"].";";
		$opt_js.="\n extPricesData['".$i."'].p_price_1 = ".$extendedPrice["p_price_1"].";";
		$opt_js.="\n extPricesData['".$i."'].p_price_2 = ".$extendedPrice["p_price_2"].";";
		$opt_js.="\n extPricesData['".$i."'].p_price_3 = ".$extendedPrice["p_price_3"].";";
		$opt_js.="\n extPricesData['".$i."'].p_price_4 = ".$extendedPrice["p_price_4"].";";
		$opt_js.="\n extPricesData['".$i."'].p_price_5 = ".$extendedPrice["p_price_5"].";";
		$i++;
	}
}
$opt_js.="
		function debug_info_calc_unit(message){
			".(siteConfig::$debugMode ? "" : "return;")."
			console.log(message);
		}
		function applyCurrentDiscounts(curPrice){
			discountsData.forEach(function(item, i, arr) {
				if(item.d_sign=='+'){
					curPrice = curPrice + item.d_value;
				} else if(item.d_sign=='-') {
					curPrice = curPrice - item.d_value;
				} else if(item.d_sign=='*') {
					curPrice = curPrice * item.d_value;
				} else if(item.d_sign=='=') {
					curPrice = item.d_value;
				}
			});
			if(curPrice < 0) {
				debug_info_calc_unit('applyCurrentDiscounts - Found negative price:' + curPrice);
				curPrice = 0;
				debug_info_calc_unit('applyCurrentDiscounts - Reset negative price to ' + curPrice);
			}
			return VtoFloat(curPrice, 2);
		}
		function applyExtendedPrices(cPrice){
			var currentQuantity=parseFloat($('#quant_".$gid."').val());
			var newCurrentPrice=cPrice;
			$.each(extPricesData, function( index, item ) {
				if(currentQuantity>=item.p_quantity) {
					newCurrentPrice=VtoFloat(item.p_price_".$tp.");
					return false;
				}
			});								
			return newCurrentPrice;
		}
		function updateCurrentPrice(curPrice, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.price_sign=='+'){
				curPrice = curPrice + (vopt.price * quant);
			} else if(vopt.price_sign=='-') {
				curPrice = curPrice - (vopt.price * quant);
			} else if(vopt.price_sign=='*') {
				curPrice = curPrice * (Math.pow(vopt.price, quant));
			}
			if(curPrice < 0) {
				debug_info_calc_unit('updateCurrentPrice - Found negative price:' + curPrice);
				curPrice = 0;
				debug_info_calc_unit('updateCurrentPrice - Reset negative price to ' + curPrice);
			}
			curPrice = VtoFloat(curPrice, 2);
			return curPrice;
		}
		function updateCurrentPoints(currentValue, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.points_sign=='+'){ currentValue = currentValue + (vopt.points * quant); } 
			else if(vopt.points_sign=='-') { currentValue = currentValue - (vopt.points * quant); } 
			else if(vopt.points_sign=='*') { currentValue = currentValue * (Math.pow(vopt.points, quant)); }
			currentValue = VtoFloat(currentValue, 3);
			return currentValue;
		}
		function updateCurrentWeight(currentValue, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.weight_sign=='+'){ currentValue = currentValue + (vopt.weight * quant); } 
			else if(vopt.weight_sign=='-') { currentValue = currentValue - (vopt.weight * quant); } 
			else if(vopt.weight_sign=='*') { currentValue = currentValue * (Math.pow(vopt.weight, quant)); }
			currentValue = VtoFloat(currentValue, 3);
			return currentValue;
		}
		function updateCurrentHeight(currentValue, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.height_sign=='+'){ currentValue = currentValue + (vopt.height * quant); } 
			else if(vopt.height_sign=='-') { currentValue = currentValue - (vopt.height * quant); } 
			else if(vopt.height_sign=='*') { currentValue = currentValue * (Math.pow(vopt.height, quant)); }
			currentValue = VtoFloat(currentValue, 3);
			return currentValue;
		}
		function updateCurrentWidth(currentValue, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.width_sign=='+'){ currentValue = currentValue + (vopt.width * quant); } 
			else if(vopt.width_sign=='-') { currentValue = currentValue - (vopt.width * quant); } 
			else if(vopt.width_sign=='*') { currentValue = currentValue * (Math.pow(vopt.width, quant)); }
			currentValue = VtoFloat(currentValue, 3);
			return currentValue;
		}
		function updateCurrentLength(currentValue, vopt, quant){
			quant=VtoFloat(quant,siteConfig['quantity_digits']);
			if(vopt.length_sign=='+'){ currentValue = currentValue + (vopt.length * quant); } 
			else if(vopt.length_sign=='-') { currentValue = currentValue - (vopt.length * quant); } 
			else if(vopt.length_sign=='*') { currentValue = currentValue * (Math.pow(vopt.length, quant)); }
			currentValue = VtoFloat(currentValue, 3);
			return currentValue;
		}
		function recalcPrice(){
			ajaxShowActivity();
			var currentPrice=applyExtendedPrices(sellPrice);
//			var currentBasePrice=applyExtendedPrices(basePrice);
//			var currentPackPrice=applyExtendedPrices(packPrice);

			var oldPrice=currentPrice;
//			var oldBasePrice=currentBasePrice;
//			var oldPackPrice=currentPackPrice;

			currentPrice=applyCurrentDiscounts(currentPrice);
//			currentBasePrice=applyCurrentDiscounts(currentBasePrice);
//			currentPackPrice=applyCurrentDiscounts(currentPackPrice);

			var currentHeight = baseHeight;
			var currentWidth = baseWidth; 
			var currentLength = baseLength;
			var currentWeight = baseWeight;
			var currentPoints = basePoints;
			optionsData.forEach(function(item, i, arr) {
				var currentQuantity=1;
				var haveQuantity=item.optHaveQuantity;
				if(item.optDataCount>0){
					if(item.optType=='select'){
						selected_id=$('#option_' + item.optID + ' option:selected').val();
						item.optData.forEach(function(val, j) {
							if(val.id==selected_id){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID).val())
								} else {
									currentQuantity=1;
								}
								currentPrice=updateCurrentPrice(currentPrice, val, currentQuantity);
//								currentBasePrice=updateCurrentPrice(currentBasePrice, val, currentQuantity);
//								currentPackPrice=updateCurrentPrice(currentPackPrice, val, currentQuantity);
								
								oldPrice=updateCurrentPrice(oldPrice, val, currentQuantity);
//								oldBasePrice=updateCurrentPrice(oldBasePrice, val, currentQuantity);
//								oldPackPrice=updateCurrentPrice(oldPackPrice, val, currentQuantity);

								currentPoints=updateCurrentPoints(currentPoints, val, currentQuantity);
								currentWeight=updateCurrentWeight(currentWeight, val, currentQuantity);
								currentHeight=updateCurrentHeight(currentHeight, val, currentQuantity);
								currentWidth=updateCurrentWidth(currentWidth, val, currentQuantity);
								currentLength=updateCurrentLength(currentLength, val, currentQuantity);
							}
						});
					} else if(item.optType=='radiogroup') {
						selected_id=$('input[name=option_' + item.optID + ']:checked').val();
						item.optData.forEach(function(val, j) {
							if(val.id==selected_id){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID + '_' + val.id).val())
								} else {
									currentQuantity=1;
								}
								currentPrice=updateCurrentPrice(currentPrice, val, currentQuantity);
//								currentBasePrice=updateCurrentPrice(currentBasePrice, val, currentQuantity);
//								currentPackPrice=updateCurrentPrice(currentPackPrice, val, currentQuantity);

								oldPrice=updateCurrentPrice(oldPrice, val, currentQuantity);
//								oldBasePrice=updateCurrentPrice(oldBasePrice, val, currentQuantity);
//								oldPackPrice=updateCurrentPrice(oldPackPrice, val, currentQuantity);

								currentPoints=updateCurrentPoints(currentPoints, val, currentQuantity);
								currentWeight=updateCurrentWeight(currentWeight, val, currentQuantity);
								currentHeight=updateCurrentHeight(currentHeight, val, currentQuantity);
								currentWidth=updateCurrentWidth(currentWidth, val, currentQuantity);
								currentLength=updateCurrentLength(currentLength, val, currentQuantity);
							}
						});
					} else if(item.optType=='checkbox') {
						item.optData.forEach(function(val, j) {
							if($('#option_' + val.opt_id + '_' + val.id).attr('checked')){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID + '_' + val.id).val())
								} else {
									currentQuantity=1;
								}
								currentPrice=updateCurrentPrice(currentPrice, val, currentQuantity);
//								currentBasePrice=updateCurrentPrice(currentBasePrice, val, currentQuantity);
//								currentPackPrice=updateCurrentPrice(currentPackPrice, val, currentQuantity);

								oldPrice=updateCurrentPrice(oldPrice, val, currentQuantity);
//								oldBasePrice=updateCurrentPrice(oldBasePrice, val, currentQuantity);
//								oldPackPrice=updateCurrentPrice(oldPackPrice, val, currentQuantity);

								currentPoints=updateCurrentPoints(currentPoints, val, currentQuantity);
								currentWeight=updateCurrentWeight(currentWeight, val, currentQuantity);
								currentHeight=updateCurrentHeight(currentHeight, val, currentQuantity);
								currentWidth=updateCurrentWidth(currentWidth, val, currentQuantity);
								currentLength=updateCurrentLength(currentLength, val, currentQuantity);
							}
						});
					}
				}
			});

//			$('.current_base_price_val').text(formatFloat(currentBasePrice, siteConfig['price_digits']));
//			$('.old_base_price_val').text(formatFloat(oldBasePrice, siteConfig['price_digits']));

//			$('.current_pack_price_val').text(formatFloat(currentPackPrice, siteConfig['price_digits']));
//			$('.old_pack_price_val').text(formatFloat(oldPackPrice, siteConfig['price_digits']));

			$('.current_base_price_val').text(formatFloat(currentPrice * base_koeff, siteConfig['price_digits']));
			$('.old_base_price_val').text(formatFloat(oldPrice * base_koeff, siteConfig['price_digits']));

			$('.current_pack_price_val').text(formatFloat(currentPrice * pack_koeff, siteConfig['price_digits']));
			$('.old_pack_price_val').text(formatFloat(oldPrice * pack_koeff, siteConfig['price_digits']));

			$('.current_price_val').text(formatFloat(currentPrice, siteConfig['price_digits']));
			$('.old_price_val').text(formatFloat(oldPrice, siteConfig['price_digits']));

			$('#g_points_val').text(formatFloat(currentPoints, 0));						
			$('#g_weight_val').text(formatFloat(currentWeight, weight_digits)+' '+baseWeightMeasure);
			$('#g_height_val').text(formatFloat(currentHeight, size_digits)+' '+baseSizeMeasure);
			$('#g_width_val').text(formatFloat(currentWidth, size_digits)+' '+baseSizeMeasure);
			$('#g_length_val').text(formatFloat(currentLength, size_digits)+' '+baseSizeMeasure);
			ajaxHideActivity();
		}
		function getOptionsForBasket(){
			var fd = new FormData;
			var optArr=new Array();
			optionsData.forEach(function(item, i, arr) {
				var currentQuantity=1;
				var haveQuantity=item.optHaveQuantity;
				if(item.optDataCount>0){
					if(item.optType=='select'){
						selected_id=$('#option_' + item.optID + ' option:selected').val();
						item.optData.forEach(function(val, j) {
							if(val.id==selected_id){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID).val())
								} else {
									currentQuantity=1;
								}
								optArr.push({'opt_id':item.optID, 'val_id':val.id, 'quantity':currentQuantity});
							}
						});
					} else if(item.optType=='radiogroup') {
						selected_id=$('input[name=option_' + item.optID + ']:checked').val();
						item.optData.forEach(function(val, j) {
							if(val.id==selected_id){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID + '_' + val.id).val())
								} else {
									currentQuantity=1;
								}
								optArr.push({'opt_id':item.optID, 'val_id':val.id, 'quantity':currentQuantity});
							}
						});
					} else if(item.optType=='checkbox') {
						item.optData.forEach(function(val, j) {
							if($('#option_' + val.opt_id + '_' + val.id).attr('checked')){
								if (haveQuantity==1){
									currentQuantity=parseFloat($('#option_quant_' + item.optID + '_' + val.id).val())
								} else {
									currentQuantity=1;
								}
								optArr.push({'opt_id':item.optID, 'val_id':val.id, 'quantity':currentQuantity});
							}
						});
					}
				} else { // here is options without values
					if(item.optType=='text'){
						optArr.push({'opt_id':item.optID, 'value': $('#option_' + item.optID).val(), 'quantity':currentQuantity});
					} else if(item.optType=='textarea') {
						optArr.push({'opt_id':item.optID, 'value': $('#option_' + item.optID).val(), 'quantity':currentQuantity});
					} else if(item.optType=='file'){
						fd.append('option_' + item.optID, $('#option_' + item.optID).prop('files')[0]);
					}
				}
			});
			fd.append('optArr', JSON.stringify(optArr));
			return fd;
//			return optArr;
		}
		function changeOptionImage(id){
			selected_id=$('#option_' + id + ' option:selected').val();
			src=$('#option_select_image_' + id + ' img').attr('src');
			optionsData.forEach(function(item, i) {
				if(item.optID==id && item.optDataCount>0){
					item.optData.forEach(function(val, j) {
						if(val.id==selected_id){
							src=val.thumb;
						}
					});
				}
			});
			$('#option_select_image_' + id + ' img').attr('src', src);
		}
		$(document).on('submit','#goods_info',function (e) {
			e.preventDefault();
			var theForm = $('#goods_info');
			if (( typeof(theForm[0].checkValidity) == 'function' ) && !theForm[0].checkValidity()) {
				return false;
			} else {
				addToBasket".($_info_arr['g_is_single']['value'] ? "Single" : "")."($('#g_id').val());
			}
			return false;
		})
		$(document).ready(function(){
			recalcPrice();
		});
		";
Portal::getInstance()->addScriptDeclaration($opt_js);
?>
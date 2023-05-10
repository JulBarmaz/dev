<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewprice extends View {
	public function renderGroupGoods($psid,$params){
		$model=Module::getInstance()->getModel();
		$goods=$model->getGoods($psid,$params);
		if(count($goods)){
			foreach($goods as $g){
				$this->renderGoodsRow($g,$params);
			}
		}
	}	
	public function renderGoodsRow($row,$params){
		echo "<tr>";
		if ($params["show_thumbs"]) {
			$thumb=$this->getImage($row->g_thumb,1);
			if ($thumb) $thumb=HTMLControls::renderImage($thumb,false,catalogConfig::$thumb_width,0,"",$row->g_id.")".$row->g_name);
			echo "<td align=\"center\">".$thumb."</td>";
		}
		echo "<td>".$row->g_sku."</td>";
		echo "<td>".$row->g_name."</td>";
		$price_field="g_price_".$params["price_type"];
		$pack_measure_id=$row->g_pack_measure;
		$volume_measure_id=$row->g_vmeasure;
		$weight_measure_id=$row->g_wmeasure;
		$pack_price=""; $volume_price=""; $weight_price="";
		// приводим к метрам, считаем что кубометр и метр имеют коэффициент 1
		// Считаем что размеры указаны в "Единица измерения размеров по умолчанию" настроек
		$koef=Measure::getInstance()->getKoeff(catalogConfig::$default_size_measure);
		$koef3=Measure::getInstance()->getKoeff($volume_measure_id);
		$height=$row->g_height*$koef;
		$width=$row->g_width*$koef;
		$length=$row->g_length*$koef;
		if ($koef3) $volume=$height*$width*$length/$koef3;
		else $volume=0;
		$weight=floatval($row->g_weight); 
		$_val=$row->{$price_field};
		if($params["discount"])
		{
			$_val=round($_val*(100+floatval($params["discount"]))/100,0);
		}		
		$base_price=0;
		switch($row->g_selltype){
			case 1: // упаковка
				$measure_id=$row->g_pack_measure;
				$pack_price=$_val;
				if ($row->g_pack_koeff) $base_price=round($_val/$row->g_pack_koeff, 2);
				if ($weight) $weight_price=round($base_price/$weight, 2);
				if ($volume) $volume_price=round($base_price/$volume, 2);
				break;
			case 2: // вес 
				$measure_id=$row->g_wmeasure;
				$weight_price=$_val;
				if ($weight) $base_price=round($weight_price*$weight, 2);
				$pack_price=round($base_price*$row->g_pack_koeff, 2);
				if ($volume) $volume_price=round($base_price/$volume, 2);
				break;
			case 3: // объем
				$measure_id=$row->g_vmeasure;
				$volume_price=$_val;
				if ($volume_measure_id!=$measure_id) {
					if ($volume) $base_price=round($volume_price*$volume, 2);
				} else { $base_price=$volume_price; }
				if ($volume_measure_id!=$pack_measure_id) {
					$pack_price=round($base_price*$row->g_pack_koeff, 2);
					} // else { $pack_price=$volume_price; }
				if ($weight) $weight_price=round($base_price/$weight, 2);
				break;
			case 4:
				//						break;
			case 5:
				//						break;
			case 0:
			default:
				$measure_id=$row->g_measure;
				$pack_price=round($_val*$row->g_pack_koeff, 2);
				if ($weight) $weight_price=round($_val/$weight, 2);
				if ($volume) $volume_price=round($_val/$volume, 2);
				break;
		}
		if ($pack_price) {
			$pack_price=number_format($pack_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); 
			$pack_measure_txt=Measure::getInstance()->getShortName($pack_measure_id);
		}	else {
			$pack_measure_txt="";
			$pack_price="";
		}
		if ($weight_price) {
			$weight_price=number_format($weight_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
			$weight_measure_txt=Measure::getInstance()->getShortName($weight_measure_id);
		}  else {
			$weight_measure_txt="";
			$weight_price="";
		}
		if ($volume_price) {
			$volume_price=number_format($volume_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
			$volume_measure_txt=Measure::getInstance()->getShortName($volume_measure_id);
		} else {
			$volume_measure_txt="";
			$volume_price="";
		}
		
		
		echo "<td align=\"center\">".Measure::getInstance()->getShortName($measure_id)."</td>";
		if($params["show_dimensions"]){
			echo "<td align=\"right\">".($row->g_length>0 ? $row->g_length : "")."</td>";
			echo "<td align=\"right\">".($row->g_width>0 ? $row->g_width :"")."</td>";
			echo "<td align=\"right\">".($row->g_height>0 ? $row->g_height :"") ."</td>";
		}
		if ($params["show_weight"]){
			echo "<td align=\"right\">";
			if($row->g_weight>0){ echo number_format($row->g_weight, catalogConfig::$weight_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".$weight_measure_txt; 	}
			echo "</td>";
		}
		echo "<td align=\"right\">".number_format($_val, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."</td>";
		if($params["show_pack_price"]){
			echo "<td align=\"center\">".$pack_measure_txt."</td>";
			echo "<td align=\"right\">".$pack_price."</td>";
		}	
		if($params["show_volume_price"]){
			echo "<td align=\"center\">".$volume_measure_txt."</td>";
			echo "<td align=\"right\">".$volume_price."</td>";
		}	
		if($params["show_weight_price"]){
			echo "<td align=\"center\">".$weight_measure_txt."</td>";
			echo "<td align=\"right\">".$weight_price."</td>";
		}
		echo "</tr>";
	}
	public function getImage($img,$image_state=0) {
		$imgpath=BARMAZ_UF_PATH.'catalog'.DS.'i'.DS;
		$imgurl="";
		if($image_state==1) $imgpath.='thumbs'.DS.Files::splitAppendix($img,true);
		elseif($image_state==2) $imgpath.='medium'.DS.Files::splitAppendix($img,true);
		else $imgpath.='fullsize'.DS.Files::splitAppendix($img,true);
		if((file_exists($imgpath))&&(is_file($imgpath))) {
			$imgurl=BARMAZ_UF.'/catalog/i/';
			if($image_state==1) { $imgurl.='thumbs/'.Files::splitAppendix($img); }
			elseif($image_state==2) { $imgurl.='medium/'.Files::splitAppendix($img); }
			else { $imgurl.='fullsize/'.Files::splitAppendix($img); }
		}
		return $imgurl;
	}
	
}
?>
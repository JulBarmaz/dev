<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogHelperGoods {
	public function getMainGroupAliasByGoodsId($psid){
		$sql="SELECT g_main_grp FROM `#__goods` WHERE g_id=".$psid;
		Database::getInstance()->setQuery($sql);
		$ggr_id = intval(Database::getInstance()->loadResult());
		if(!$ggr_id){ // случай когда не указана основная группа
			$sql="SELECT parent_id FROM `#__goods_links` WHERE g_id=".$psid." ORDER BY `parent_id` LIMIT 1";
			Database::getInstance()->setQuery($sql);
			$ggr_id = intval(Database::getInstance()->loadResult());
		}
		if($ggr_id){
			$sql="SELECT ggr_alias FROM #__goods_group WHERE ggr_id='".$ggr_id."'";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->loadResult();
		} else return "";
	}
	public function getManufacturerIdByAlias($alias){
		$sql="SELECT mf_id FROM #__manufacturers WHERE mf_alias='".$alias."'";
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
	public function getVendorIdByAlias($alias){
		$sql="SELECT v_id FROM #__vendors WHERE v_alias='".$alias."'";
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
	public function getGroupIdByAlias($alias){
		$sql="SELECT ggr_id FROM #__goods_group WHERE ggr_alias='".$alias."'";	
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());	
	}
	public function getGoodsIdByAlias($alias){
		$sql="SELECT g_id FROM #__goods WHERE g_alias='".$alias."'";
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());	
	}
	public function getAliasByGroupId($id){
		$sql="SELECT ggr_alias FROM #__goods_group WHERE ggr_id=".(int)$id;
		Database::getInstance()->setQuery($sql);
		return strval(Database::getInstance()->loadResult());
	}
	public function getAliasByGoodsId($id){
		$sql="SELECT g_alias FROM #__goods WHERE g_id=".(int)$id;
		Database::getInstance()->setQuery($sql);
		return strval(Database::getInstance()->loadResult());
	}
	public function getRandomGoods($grp_id=0,$g_id=0,$new=0,$hit=0, $rg_referer="") {
		$goods=array();
		if($g_id) { // есть ид товара - просто не морочимся, а его отдаем
			$sql='SELECT * FROM #__goods WHERE g_id='.(int)$g_id;
			Database::getInstance()->setQuery($sql);
			$result=Database::getInstance()->loadObject($goods[0]);
		} else {
			$sql_count="SELECT count(g_id) FROM #__goods";
			$sql_select="SELECT * FROM #__goods";
			$sql_where=" WHERE g_enabled=1 AND g_deleted=0";
			if($grp_id) $sql_where.=" AND g_id IN (SELECT l.g_id FROM #__goods_links AS l WHERE l.parent_id=".(int)$grp_id.")";
			if($new) $sql_where.=" AND g_new=1";
			if($hit) $sql_where.=" AND g_hit=1";
			$sql_count.=$sql_where;
			Database::getInstance()->setQuery($sql_count);
			$count=Database::getInstance()->loadResult();
			if ($count){
				$ind=rand(0,$count-1);
				$sql_select.=$sql_where;
				$sql_select.=" LIMIT ".$ind.",1";	
				Database::getInstance()->setQuery($sql_select);
				$result=Database::getInstance()->loadObject($goods[0]);
			}
		}
		if (count($goods)){
			foreach($goods as $g) {
				$g->g_thumb_url=$this->getThumbImage($g->g_thumb);
				$g->g_goods_url=Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$g->g_id."&amp;alias=".$g->g_alias.($rg_referer ? "&rg_referer=".$rg_referer : ""));
			}
		}
		return $goods;
	}
	public function getGoods($grp_id=0, $quantity=0, $new=0, $hit=0, $order_by="g_name", $order_dir="") {
		$sql_select = "SELECT g.* FROM #__goods AS g";
		if($grp_id) {
			$sql_select.= " LEFT JOIN #__goods_links AS l ON l.g_id=g.g_id";
			$sql_where=" WHERE l.parent_id=".(int)$grp_id." AND g.g_enabled=1 AND g.g_deleted=0";
		} else {
			$sql_where=" WHERE g.g_enabled=1 AND g.g_deleted=0";
		}
		if($new) $sql_where.=" AND g.g_new=1";
		if($hit) $sql_where.=" AND g.g_hit=1";
		$sql_select.=$sql_where." ORDER BY ".$order_by.($order_dir ? " ".$order_dir : "")." LIMIT ".$quantity;
		Database::getInstance()->setQuery($sql_select);
		return Database::getInstance()->loadObjectList();
	}
	public function getGoodsWithSubCats($grp_id=0, $include_main=false, $quantity=0, $new=0, $hit=0, $order_by="g_name", $order_dir="") {
		$sql_select = "SELECT g.* FROM #__goods AS g";
		if($grp_id) {
			$grp_id_arr=$this->getAllChildGroups($grp_id, $include_main);
			$sql_select.= " LEFT JOIN #__goods_links AS l ON l.g_id=g.g_id";
			$sql_where=" WHERE l.parent_id IN (".implode(", ", $grp_id_arr).") AND g.g_enabled=1 AND g.g_deleted=0";
		} else {
			$sql_where=" WHERE g.g_enabled=1 AND g.g_deleted=0";
		}
		if($new) $sql_where.=" AND g.g_new=1";
		if($hit) $sql_where.=" AND g.g_hit=1";
		$sql_select.=$sql_where." ORDER BY ".$order_by.($order_dir ? " ".$order_dir : "")." LIMIT ".$quantity;
		Database::getInstance()->setQuery($sql_select);
		return Database::getInstance()->loadObjectList();
	}
	public function getChildGroupsIds($grp_ids=array()){
		if(count($grp_ids)){
			$sql="SELECT ggr_id FROM #__goods_group WHERE ggr_id_parent IN (".implode(", ", $grp_ids).") AND ggr_enabled=1 AND ggr_deleted=0";
			Database::getInstance()->setQuery($sql);
			$res = Database::getInstance()->loadAssocList("ggr_id");
			if(count($res)) return array_keys($res);
		} else {
			return array();
		}
	}
	public function getAllChildGroups($grp_id=0, $include_main=false){
		$all_ids = array();
		$first_arr = array();
		$first_arr[] = $grp_id;
		$current_childs = $this->getChildGroupsIds($first_arr);
		while($current_childs && count($current_childs)){
			$all_ids = array_merge($all_ids, $current_childs);
			$current_childs=$this->getChildGroupsIds($current_childs);
		}
		if($include_main) $all_ids[]=$grp_id;
		return $all_ids;
	}
	public function getEmptyImage() {
		$imgurl="";
		$imgpath_c=PATH_IMAGES.DS."nophoto.png";
		$imgpath_t=PATH_TEMPLATES.DS.Portal::getInstance()->getTemplate().DS.'images'.DS."nophoto.png";
		if((file_exists($imgpath_t))&&(is_file($imgpath_t))) {
			$imgurl=Portal::getURI()."templates/".Portal::getInstance()->getTemplate()."/images/nophoto.png";
		} elseif((file_exists($imgpath_c))&&(is_file($imgpath_c))) {
			$imgurl=Portal::getURI()."images/nophoto.png";
		} else $imgurl="";
		return  $imgurl;
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
	public function getThumbImage($img, $url="") {
		if (!$url) {
			$imgpath=BARMAZ_UF_PATH."catalog".DS."i".DS.'thumbs'.DS.Files::splitAppendix($img,true);
			if((file_exists($imgpath))&&(is_file($imgpath))) {
				$imgurl=BARMAZ_UF."/catalog/i/thumbs/".Files::splitAppendix($img);
			} else $imgurl="";
		} else {
			$imgurl=$url."/catalog/i/thumbs/".Files::splitAppendix($img);
		}
		return $imgurl;
	}
	public function getParentGroup($psid){
		$sql="SELECT parent_id FROM #__goods_links WHERE g_id=".$psid." ORDER BY ordering LIMIT 1";
		Database::getInstance()->setQuery($sql);
		return floatval(Database::getInstance()->loadResult());
	}
	public function getMaxPriceInGroup($group_id, $field){
		$sql="SELECT MAX(".$field.") FROM #__goods as g";
		$sql.=" LEFT JOIN #__goods_links as gg ON gg.g_id=g.g_id";
		if($group_id){
			$show_goods_from_subgroups=intval(Module::getInstance("catalog")->getParam('show_goods_from_subgroups'));
			if($show_goods_from_subgroups){
				$childs = $this->getAllChildGroups($group_id, true);
				$sql.=" WHERE gg.parent_id IN (".implode(",", $childs).")";
			} else {
				$sql.=" WHERE gg.parent_id=".$group_id;
			}
		}
		Database::getInstance()->setQuery($sql);
		return floatval(Database::getInstance()->loadResult());
	}
	public function haveOptions(&$gids){
		$sql = "SELECT `od_obj_id`, COUNT(`od_opt_id`) AS count_options FROM `#__goods_options_data` WHERE `od_obj_id` IN(".implode(",", $gids).") GROUP BY `od_obj_id`";
		Database::getInstance()->setQuery($sql);
		$result=Database::getInstance()->loadAssocList('od_obj_id');
		if(!is_array($result)) $result=array();
		return $result;		
	}
	public function getOptions($psid){
		$sql="SELECT od.*, o.*, t.*
				FROM `#__goods_options_data` AS od, `#__goods_options` AS o, `#__goods_opt_types` AS t
				WHERE od.od_obj_id=".$psid." AND od.od_opt_id=o.o_id AND o.o_type=t.t_id
				AND od.od_enabled=1
				AND o.o_deleted=0 AND o.o_enabled=1
				ORDER BY od.od_ordering, o.o_ordering";
		Database::getInstance()->setQuery($sql);
		$options=array();
		$options = Database::getInstance()->loadObjectList('od_id');
		if(is_array($options)){
			foreach ($options as $ok=>$ov) {
				$options[$ok]->o_title = trim(preg_replace("#\[(.+?)\]#is", "", $options[$ok]->o_title));
				$options[$ok]->optionsData=array();
				$options[$ok]->haveImage=0;
			}
			$optionsData=$this->getOptionsData(array_keys($options));
			foreach ($optionsData as $od_key=>$od_val){
				$img=0;
				if(array_key_exists($od_val->ovd_od_id, $options)) {
					$options[$od_val->ovd_od_id]->optionsData[$od_val->ovd_id]=$od_val;
					$img = $this->getOptionThumb($od_val->ovd_thumb, $od_val->ov_thumb);
					if($img) $options[$od_val->ovd_od_id]->haveImage=1;
					else $img=$this->getEmptyImage();
					$options[$od_val->ovd_od_id]->optionsData[$od_val->ovd_id]->ovd_thumb=$img;
				}
			}
		}
		return $options;
	}
	public function getOptionsData($ids){
		if(count($ids)){
			$sql="SELECT ovd.*, ov.ov_name, ov.ov_thumb, ov.ov_extcode
				FROM `#__goods_opt_vals_data` AS ovd, `#__goods_options_data` AS od, `#__goods_opt_vals` AS ov
				WHERE ovd.ovd_od_id IN(".implode(",",$ids).") AND ovd.ovd_od_id=od.od_id
				AND ovd.ovd_val_id=ov.ov_id
				AND ov.ov_enabled=1 AND ov.ov_deleted=0
				AND od.od_enabled=1 AND ovd.ovd_enabled=1
				ORDER BY ovd.ovd_ordering, ov.ov_ordering";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->loadObjectList();
		} else {
			return array();
		}
	}
	public function getOptionThumb($ovd_thumb, $ov_thumb){
		$imgpath=""; $imgurl="";
		if($ovd_thumb) $imgpath=BARMAZ_UF_PATH."catalog".DS."i".DS."opt_vals_data".DS.'thumbs'.DS.Files::splitAppendix($ovd_thumb,true);
		if($imgpath && file_exists($imgpath) && is_file($imgpath)) {
			$imgurl=BARMAZ_UF."/catalog/i/opt_vals_data/thumbs/".Files::splitAppendix($ovd_thumb);
		} elseif($ov_thumb){
			$imgpath=BARMAZ_UF_PATH."catalog".DS."i".DS."opt_vals".DS.'thumbs'.DS.Files::splitAppendix($ov_thumb,true);
			if($imgpath && file_exists($imgpath) && is_file($imgpath)) {
				$imgurl=BARMAZ_UF."/catalog/i/opt_vals/thumbs/".Files::splitAppendix($ov_thumb);
			}
		}
		return $imgurl;
	}
	public function getExtendedPrices($ids){
		if(!count($ids)) return array();
		$res=array();
		$sql = "SELECT * FROM #__goods_prices WHERE p_g_id IN(".implode(",", $ids).")  AND p_enabled=1 ORDER BY p_quantity DESC";
		Database::getInstance()->setQuery($sql);
		$d = Database::getInstance()->loadAssocList();
		if(count($d)){
			foreach($d AS $row){
				$res[$row["p_g_id"]][]=$row;
			}
		}
		return $res;
	}
	public function applyExtendedPrices($gid, $price, $quantity, $extPrices){
		$tp=User::getInstance()->u_pricetype;
		if(array_key_exists($gid, $extPrices) && count($extPrices[$gid])){
			foreach ($extPrices[$gid] AS $extPrice){
				if($quantity>=$extPrice["p_quantity"]) return $extPrice["p_price_".$tp];
			}
		}
		if($price < 0) $price = 0;
		return $price;
	}
	public function applyDiscounts($gid, $price, $discounts){
		$new_price=$price;
		if(array_key_exists($gid, $discounts) && count($discounts[$gid])){
			foreach ($discounts[$gid] AS $discount){
				if($discount["d_sign"]=='+')	$new_price = $new_price + $discount["d_value"];
				elseif($discount["d_sign"]=='-') $new_price = $new_price - $discount["d_value"];
				elseif($discount["d_sign"]=='*') $new_price = $new_price * $discount["d_value"];
				elseif($discount["d_sign"]=="=") $new_price = $new_price;
				if($discount["d_stop"]) break;
			}
		}
		if($new_price < 0) $new_price = 0;
		return $new_price;
	}
	public function getDiscounts($ids){
		if(!count($ids)) return array(); 
		$res=array();
		if(User::getInstance()->u_discount){
			$personal_discount = array("g_id"=>0, "d_id"=>0, "d_name"=>Text::_("Personal discount"), "d_sign"=>"*", "d_value"=>(100-User::getInstance()->u_discount)/100, "d_period_unlimited"=>1, "d_start_date"=>"", "d_end_date"=>"", "d_stop"=>0, "d_comment"=>"", "d_ordering"=>0, "d_enabled"=>1, "d_deleted"=>0);
			foreach($ids as $gid){
				$personal_discount["g_id"]=$gid;
				$res[$gid][]=$personal_discount;
			}
		}
		$sql = "SELECT gd.g_id, d.* FROM `#__discounts` AS d, `#__goods_discounts` AS gd";
		$sql.= " WHERE d.d_id=gd.d_id AND d.d_enabled=1 AND d.d_deleted=0 AND gd.g_id IN(".implode(",", $ids).")";
		$sql.= " AND (d.d_period_unlimited=1 OR (d.d_start_date<=NOW() AND d.d_end_date>=NOW()))";
		$sql.= " ORDER BY d.d_ordering";
		Database::getInstance()->setQuery($sql);
		$d = Database::getInstance()->loadAssocList();
		if(count($d)){
			foreach($d AS $row){
				$res[$row["g_id"]][]=$row;
			}
		}
		return $res;
	}
} 
?>

<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('goodsgroup');
	}
	public function getLinksArray(&$i, &$_arr) {
		$db = Database::getInstance ();
		$module = $this->getName ();
		$arr_grp = array ();
		$arr_goods = array ();
		$arr_grp_img = array ();
		$arr_goods_img = array ();
		$sql = 'SELECT ggr_id, ggr_name, ggr_alias,ggr_id_parent,ggr_thumb,ggr_title_thm,ggr_image,ggr_title_img FROM #__goods_group WHERE ggr_deleted=0 AND ggr_enabled=1';
		$db->setQuery ( $sql );
		$res = $db->loadObjectList ( 'ggr_id' );
		// пути к изображениям группы
		$imgpath_ggr_thm = BARMAZ_UF_PATH . 'catalog' . DS . 'ggr' . DS;
		$imgpath_ggr_img = BARMAZ_UF_PATH . 'catalog' . DS . 'ggr' . DS . 'i' . DS;
		
		if (count ( $res )) {
			foreach ( $res as $val ) {
				if (! $val->ggr_id_parent || array_key_exists ( $val->ggr_id_parent, $res )) {
					$i ++;
					$ci = 0; // итератор картинок группы
					$arr_grp [$val->ggr_id] = $val->ggr_id;
					$_arr [$module] [$i] ['link'] = Router::_ ( "index.php?module=catalog&view=goods&psid=" . $val->ggr_id . "&alias=" . $val->ggr_alias, true );
					$_arr [$module] [$i] ['name'] = $val->ggr_name;
					$_arr [$module] [$i] ['fullname'] = $val->ggr_name;
					// добавляем сюда картинки если они есть по этой группе
					if ($val->ggr_thumb) {
						
						$ci ++;
						$img = $val->ggr_thumb;
						$imgpath = $imgpath_ggr_thm . Files::splitAppendix ( $img, true );
						if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
							$imgurl = BARMAZ_UF . '/catalog/ggr/' . Files::splitAppendix ( $img );
							if ($val->ggr_title_thm) $title = $val->ggr_title_thm;
							else $title = $val->ggr_name." ".mb_strtolower(Text::_("Thumb"), DEF_CP);
							$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
							$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
						}
					}
					if ($val->ggr_image) {
						$ci ++;
						$img = $val->ggr_image;
						$imgpath = $imgpath_ggr_img . Files::splitAppendix ( $img, true );
						if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
							$imgurl = BARMAZ_UF . '/catalog/ggr/i/' . Files::splitAppendix ( $img );
							if ($val->ggr_title_img) $title = $val->ggr_title_img;
							else $title = $val->ggr_name." ".mb_strtolower(Text::_("Image"), DEF_CP);
							$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
							$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
						}
					}
				}
			}
		}
		
		$sql2 = 'SELECT g.g_id, g.g_name, g.g_fullname, g.g_alias, g.g_change_date, l.parent_id,
			g.g_image,g.g_medium_image,g.g_thumb,g.g_title_img,g.g_title_med,g.g_title_thm FROM #__goods as g
			LEFT JOIN #__goods_links as l ON l.g_id=g.g_id
			WHERE g_deleted=0 AND g_enabled=1 AND g_type<100 ';
		$db->setQuery ( $sql2 );
		$res2 = $db->loadObjectList ();
		if (count ( $res2 )) {
			foreach ( $res2 as $val2 ) {
				if (array_key_exists ( $val2->parent_id, $arr_grp ) && ! array_key_exists ( $val2->g_id, $arr_goods )) {
					$i ++;
					$ci = 0;
					$arr_goods [$val2->g_id] = $val2->g_id;
					$_arr [$module] [$i] ['link'] = Router::_ ( "index.php?module=catalog&view=goods&layout=info&psid=" . $val2->g_id . "&alias=" . $val2->g_alias, true );
					$_arr [$module] [$i] ['name'] = $val2->g_name;
					$_arr [$module] [$i] ['fullname'] = $val2->g_fullname;
					$_arr [$module] [$i] ['date_change'] = $val2->g_change_date;
					// основные изображения товара
					$ci ++;
					$img = $val2->g_thumb;
					$imgpath = BARMAZ_UF_PATH . 'catalog' . DS . 'i' . DS . 'thumbs' . DS . Files::splitAppendix ( $img, true );
					if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
						$imgurl = BARMAZ_UF . '/catalog/i/thumbs/' . Files::splitAppendix ( $img );
						if ($val2->g_title_thm) $title = $val2->g_title_thm;
						else $title = $val2->g_name." ".mb_strtolower(Text::_("Thumb"), DEF_CP);
						$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
						$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
					}
					$ci ++;
					$img = $val2->g_medium_image;
					$imgpath = BARMAZ_UF_PATH . 'catalog' . DS . 'i' . DS . 'medium' . DS . Files::splitAppendix ( $img, true );
					if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
						$imgurl = BARMAZ_UF . '/catalog/i/medium/' . Files::splitAppendix ( $img );
						
						if ($val2->g_title_med)
							$title = $val2->g_title_med;
							else
								$title = $val2->g_name." ".mb_strtolower(Text::_("Med.image"), DEF_CP);
								$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
								$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
					}
					$ci ++;
					$img = $val2->g_image;
					$imgpath = BARMAZ_UF_PATH . 'catalog' . DS . 'i' . DS . 'fullsize' . DS . Files::splitAppendix ( $img, true );
					if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
						$imgurl = BARMAZ_UF . '/catalog/i/fullsize/' . Files::splitAppendix ( $img );
						
						if ($val2->g_title_img) $title = $val2->g_title_img;
						else $title = $val2->g_name." ".mb_strtolower(Text::_("Image"), DEF_CP);
						$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
						$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
					}
					
					// доп. изображения
					$sql_im = "select i_image,i_thumb,i_title_thm,i_title_img from #__goods_img where i_gid=" . $val2->g_id;
					$db->setQuery ( $sql_im );
					$img_b = $db->loadObjectList ();
					if (count ( $img_b )) {
						foreach ( $img_b as $val ) {
							$ci ++;
							$img = $val->i_thumb;
							$imgpath = BARMAZ_UF_PATH . 'catalog' . DS . 'i' . DS . 'thumbs' . DS . Files::splitAppendix ( $img, true );
							if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
								$imgurl = BARMAZ_UF . '/catalog/i/thumbs/' . Files::splitAppendix ( $img );
								if ($val->i_title_thm) $title = $val->i_title_thm;
								else $title = $val2->g_name." ".mb_strtolower(Text::_("Add.thumb"), DEF_CP).$ci;
								$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
								$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
							}
							$ci ++;
							$img = $val->i_image;
							$imgpath = BARMAZ_UF_PATH . 'catalog' . DS . 'i' . DS . 'fullsize' . DS . Files::splitAppendix ( $img, true );
							if ((file_exists ( $imgpath )) && (is_file ( $imgpath ))) {
								$imgurl = BARMAZ_UF . '/catalog/i/fullsize/' . Files::splitAppendix ( $img );
								if ($val->i_title_img) $title = $val->i_title_img;
								else $title = $val2->g_name." ".mb_strtolower(Text::_("Add.image"), DEF_CP)." ".$ci;
								$_arr [$module] [$i] ['img'] [$ci] ['image'] = $imgurl;
								$_arr [$module] [$i] ['img'] [$ci] ['title'] = $title;
							}
						}
					}
				}
			}
		}
		return true;
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='catalogModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewCatalogOrders'; $acl[$i]['ao_description']='Orders';
			$i++;$acl[$i]['ao_name']='viewCatalogOrderItems'; $acl[$i]['ao_description']='Order items';
			$i++;$acl[$i]['ao_name']='viewCatalogManufacturer_cats'; $acl[$i]['ao_description']='Manufacturer categories';
			$i++;$acl[$i]['ao_name']='deleteCatalogManufacturers_cats'; $acl[$i]['ao_description']='Finally delete manufactures categories';
			$i++;$acl[$i]['ao_name']='viewCatalogManufacturers'; $acl[$i]['ao_description']='Manufacturers';
			$i++;$acl[$i]['ao_name']='deleteCatalogManufacturers'; $acl[$i]['ao_description']='Finally delete manufactures';
			$i++;$acl[$i]['ao_name']='viewCatalogVendor_cats'; $acl[$i]['ao_description']='Vendor categories';
			$i++;$acl[$i]['ao_name']='deleteCatalogVendor_cats'; $acl[$i]['ao_description']='Finally delete vendor categories';
			$i++;$acl[$i]['ao_name']='viewCatalogVendors'; $acl[$i]['ao_description']='Vendors';
			$i++;$acl[$i]['ao_name']='deleteCatalogVendors'; $acl[$i]['ao_description']='Finally delete vendors';
			$i++;$acl[$i]['ao_name']='viewCatalogGoodsgroup'; $acl[$i]['ao_description']='Goods groups';
			$i++;$acl[$i]['ao_name']='deleteCatalogGoodsgroup'; $acl[$i]['ao_description']='Finally delete goods groups';
			$i++;$acl[$i]['ao_name']='viewCatalogGoods'; $acl[$i]['ao_description']='Goods';
			$i++;$acl[$i]['ao_name']='deleteCatalogGoods'; $acl[$i]['ao_description']='Finally delete goods';
			$i++;$acl[$i]['ao_name']='viewCatalogMeasures'; $acl[$i]['ao_description']='Measures';
			$i++;$acl[$i]['ao_name']='viewCatalogPaymenttypes'; $acl[$i]['ao_description']='Payment types';
			$i++;$acl[$i]['ao_name']='deleteCatalogPaymenttypes'; $acl[$i]['ao_description']='Finally delete payment types';
			$i++;$acl[$i]['ao_name']='viewCatalogDeliverytypes'; $acl[$i]['ao_description']='Delivery types';
			$i++;$acl[$i]['ao_name']='deleteCatalogDeliverytypes'; $acl[$i]['ao_description']='Finally delete delivery types';
			$i++;$acl[$i]['ao_name']='viewCatalogOptions'; $acl[$i]['ao_description']='Goods options';
			$i++;$acl[$i]['ao_name']='deleteCatalogOptions'; $acl[$i]['ao_description']='Finally delete goods options';
			$i++;$acl[$i]['ao_name']='viewCatalogTaxes'; $acl[$i]['ao_description']='List of taxes';
			$i++;$acl[$i]['ao_name']='deleteCatalogTaxes'; $acl[$i]['ao_description']='Finally delete taxes';
			$i++;$acl[$i]['ao_name']='viewCatalogDiscounts'; $acl[$i]['ao_description']='List of discounts and surcharges';
			$i++;$acl[$i]['ao_name']='deleteCatalogDiscounts'; $acl[$i]['ao_description']='Finally delete discounts and surcharges';
			$i++;$acl[$i]['ao_name']='viewCatalogCurrency'; $acl[$i]['ao_description']='Currencies';
			$i++;$acl[$i]['ao_name']='viewCatalogCurrency_rate'; $acl[$i]['ao_description']='Currency rates';
			$i++;$acl[$i]['ao_name']='viewCatalogFeedbacks'; $acl[$i]['ao_description']='Feedbacks'; // @FIXME Remove ???
			$i++;$acl[$i]['ao_name']='viewCatalogUsers'; $acl[$i]['ao_description']='Vendors link';
			$i++;$acl[$i]['ao_name']='viewCatalogExchange1c'; $acl[$i]['ao_description']='Data exchange in 1C format';
			$i++;$acl[$i]['ao_name']='viewCatalogImport'; $acl[$i]['ao_description']='Import data';
			$i++;$acl[$i]['ao_name']='viewCatalogExport'; $acl[$i]['ao_description']='Export data';
//			$i++;$acl[$i]['ao_name']='deleteCatalogFields'; $acl[$i]['ao_description']='Finally delete group fields';
			$i++;$acl[$i]['ao_name']='viewCatalogGoods_stat'; $acl[$i]['ao_description']='Transition statistics from other sites';
			$i++;$acl[$i]['ao_name']='deleteCatalogGoods_stat'; $acl[$i]['ao_description']='Finally delete goods statistics';
		} else {
			$i++;$acl[$i]['ao_name']='catalogModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewCatalogGoods'; $acl[$i]['ao_description']='View goods';
			$i++;$acl[$i]['ao_name']='viewCatalogDownloadDemo'; $acl[$i]['ao_description']='Download demo files';
			$i++;$acl[$i]['ao_name']='viewCatalogDownloadFiles'; $acl[$i]['ao_description']='Download files';
			$i++;$acl[$i]['ao_name']='viewCatalogVendors'; $acl[$i]['ao_description']='Vendors';
			$i++;$acl[$i]['ao_name']='viewCatalogManufacturers'; $acl[$i]['ao_description']='Manufacturers';
		}
		return 	$acl;
	}
}
?>
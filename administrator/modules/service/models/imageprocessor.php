<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelimageprocessor extends Model {
	private $_hiddenfiles=array('.','.svn','resources','index.php','index.html','.htaccess','.htpasswd','web.config');
	public function getImageFields(){
		$res=array();
		// в целом может имеет смысл сделать метод внутри модулей , который возвращает свои ресурсы, подлежащие обработке
		// но пока сложим тут 
		// статьи 
		$res[]=array("module"=>"article","type"=>"file","view"=>"items", "layout"=>"defl", "field"=>"a_thumb", "source"=>"a_thumb", "width"=>$this->getModule()->getController()->getConfigVal("thumb_width"), "height"=>$this->getModule()->getController()->getConfigVal("thumb_height"), "checkACL"=>"viewArticleItems");
		$res[]=array("module"=>"article","type"=>"text","view"=>"items", "layout"=>"defl", "field"=>"a_text", "source"=>"a_thumb", "width"=>false, "height"=>false, "checkACL"=>"viewArticleItems");
		// блоги
		$res[]=array("module"=>"blog","type"=>"file","view"=>"post", "layout"=>"defl", "field"=>"p_thumb", "source"=>"p_thumb", "width"=>$this->getModule()->getController()->getConfigVal("thumb_width"), "height"=>$this->getModule()->getController()->getConfigVal("thumb_height"), "checkACL"=>"viewBlogPost");
		$res[]=array("module"=>"blog","type"=>"text","view"=>"post", "layout"=>"defl", "field"=>"p_text", "source"=>"p_text", "width"=>false, "height"=>false, "checkACL"=>"viewBlogPost");
    // каталог 		
		$res[]=array("module"=>"catalog","type"=>"file","view"=>"goodsgroup", "layout"=>"defl", "field"=>"ggr_thumb", "source"=>false, "width"=>catalogConfig::$ggr_thumb_width, "height"=>catalogConfig::$ggr_thumb_height, "checkACL"=>"viewCatalogGoodsgroup");
		$res[]=array("module"=>"catalog","type"=>"file","view"=>"goods", "layout"=>"defl", "field"=>"g_image", "source"=>"g_image", "width"=>false, "height"=>false, "checkACL"=>"viewCatalogGoods");
		$res[]=array("module"=>"catalog","type"=>"file","view"=>"goods", "layout"=>"defl", "field"=>"g_medium_image", "source"=>"g_image", "width"=>catalogConfig::$mediumImgWidth, "height"=>catalogConfig::$mediumImgHeight, "checkACL"=>"viewCatalogGoods");
		$res[]=array("module"=>"catalog","type"=>"file","view"=>"goods", "layout"=>"defl", "field"=>"g_thumb", "source"=>"g_image", "width"=>catalogConfig::$thumb_width, "height"=>catalogConfig::$thumb_height, "checkACL"=>"viewCatalogGoods");
		$res[]=array("module"=>"catalog","type"=>"file","view"=>"images", "layout"=>"defl", "field"=>"i_thumb", "source"=>"i_image", "width"=>catalogConfig::$thumb_width, "height"=>catalogConfig::$thumb_height, "checkACL"=>"viewCatalogGoods");
		// галерея
		$res[]=array("module"=>"gallery","type"=>"file","view"=>"groups", "layout"=>"defl", "field"=>"gr_thumb", "source"=>"gr_thumb", "width"=>galleryConfig::$ggr_thumbWidth, "height"=>galleryConfig::$ggr_thumbHeight, "checkACL"=>"viewGalleryGroups");
		$res[]=array("module"=>"gallery","type"=>"file","view"=>"items", "layout"=>"defl", "field"=>"g_thumb", "source"=>"g_thumb", "width"=>galleryConfig::$gal_thumbWidth, "height"=>galleryConfig::$gal_thumbHeight, "checkACL"=>"viewGalleryItems");
		$res[]=array("module"=>"gallery","type"=>"file","view"=>"images", "layout"=>"defl", "field"=>"gi_thumb", "source"=>"gi_image", "width"=>galleryConfig::$thumbWidth, "height"=>galleryConfig::$thumbHeight, "checkACL"=>"viewGalleryImages");
		$res[]=array("module"=>"gallery","type"=>"file","view"=>"images", "layout"=>"defl", "field"=>"gi_image", "source"=>"gi_image", "width"=>false, "height"=>false, "checkACL"=>"viewGalleryImages");
		return $res;
	}
	public function getImageObject($key){
		$objects = $this->getImageObjects();
		if(array_key_exists($key, $objects)) return $objects[$key];
		else return false;
	}
	public function getImageObjects(){
		$image_fields=$this->getImageFields();
		$images=array();
		foreach($image_fields as $k=>$field_array){
			Text::parseModule($field_array["module"]);
			$key=$field_array["module"]."#".$field_array["view"]."#".$field_array["layout"]."#".$field_array["field"];
			$meta = new SpravMetadata($field_array["module"], $field_array["view"], $field_array["layout"], true, true, 0);
			//Util::showArray($meta);
			$field_index=-1;
			foreach($meta->field as $kf=>$kv){
				if($kv==$field_array["field"]){
					$field_index=$kf;
				}
			}
			if($field_index>-1){
				if (ACLObject::getInstance($field_array["checkACL"], false)->canAccess()){
					$images[$key]["checkACL"]=$field_array["checkACL"];
					$images[$key]["field_title"]=Text::_($meta->field_title[$field_index]);
					$images[$key]["field_name"]=$field_array["field"];
					$images[$key]["keystring"]=$meta->keystring;
					$images[$key]["namestring"]=$meta->namestring;
					$images[$key]["deleted"]=$meta->deleted;
					$images[$key]["enabled"]=$meta->enabled;
					$images[$key]["module"]=$meta->module;
					$images[$key]["type"]=$field_array["type"];
					$images[$key]["view"]=$field_array["view"];
					$images[$key]["width"]=$field_array["width"];
					$images[$key]["height"]=$field_array["height"];
					$images[$key]["layout"]=$field_array["layout"];
					$images[$key]["title"]=Text::_($meta->module).": ".Text::_($meta->title);
					$images[$key]["tablename"]=$meta->tablename;
					$images[$key]["dest_path"]=$path = str_replace("/", DS, BARMAZ_UF_PATH.$meta->module.DS.$meta->upload_path[$field_index]).DS;
					if($field_array["source"] && $field_array["source"] != $field_array["field"]){
						$images[$key]["source_field_name"]=$field_array["source"];
						$field_index=-1;
						foreach($meta->field as $kf=>$kv){
							if($kv==$field_array["source"]){
								$field_index=$kf;
							}
						}
						if($field_index>-1){
							$images[$key]["source_path"]=str_replace("/", DS, BARMAZ_UF_PATH.$meta->module.DS.$meta->upload_path[$field_index]).DS;
						} else {
							unset($images[$key]);
						}
					} else {
						$images[$key]["source_field_name"]=false;
						$images[$key]["source_path"]=false;
					}
				}
			}
			unset($meta);
		}
		return $images;
	}
	public function getTotalRecords($image_object, $enabled_only, $skip_deleted) {
		$sql = "SELECT COUNT('".$image_object["keystring"]."') FROM #__".$image_object["tablename"];
		if($enabled_only || $skip_deleted){
			$where_sql="";
			if($enabled_only) $where_sql.=($where_sql ? " AND " : " ").$image_object["enabled"]."=1";
			if($skip_deleted) $where_sql.=($where_sql ? " AND " : " ").$image_object["deleted"]."=0";
			$sql.=($where_sql ? " WHERE".$where_sql : "");
		}
		$this->_db->setquery($sql);
		return $this->_db->loadResult();
	}
	public function resizeRecords($image_object, $start, $records_per_pass, $enabled_only, $skip_deleted, $force_from_source){
		$result=array();
		$sql = "SELECT ".$image_object["keystring"]." AS ind, ".$image_object["namestring"]." AS title, ".($image_object["source_field_name"] ? $image_object["source_field_name"] : "''")." AS src, ".$image_object["field_name"]." AS dest FROM #__".$image_object["tablename"];
		if($enabled_only || $skip_deleted){
			$where_sql="";
			if($enabled_only) $where_sql.=($where_sql ? " AND " : " ").$image_object["enabled"]."=1";
			if($skip_deleted) $where_sql.=($where_sql ? " AND " : " ").$image_object["deleted"]."=0";
			$sql.=($where_sql ? " WHERE".$where_sql : "");
		}
		$sql.=" ORDER BY ".$image_object["keystring"]." LIMIT ".($start-1).", ".$records_per_pass;
		$this->_db->setquery($sql);
//		Util::logFile($sql);
		$records = $this->_db->loadObjectList('ind');
		if(count($records)){
			foreach($records AS $id=>$rec){
				$error=false;
				$src_file=""; $dest_file=""; $dest_filename="";
				if($rec->dest){ // есть имя файла назначения
					$dest_file = $image_object['dest_path'].Files::splitAppendix($rec->dest, true);
					if(is_file($dest_file) && !$force_from_source ){ // есть файл назначения, перегенерация не нужна, значит он же файл источника
						$src_file=$dest_file;
					} elseif(is_file($dest_file) && $force_from_source){ // есть файл назначения, нужна перегенерация
						/************************************************************/
						if($rec->src) { // есть имя источника
							$src_file = $image_object['source_path'].Files::splitAppendix($rec->src, true);
							if(!is_file($src_file)){ // нет файла источника
								$error = true;
								$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Source file absent").": ".$src_file.", ".Text::_("Destination file present").": ".$dest_file;
							}
						} else {
							$error = true;
							$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Destination file present").": ".$dest_file.", ".Text::_("Source filename absent");
						}
						/************************************************************/
					} elseif($rec->src) { // нет файла назначения, есть имя источника
						$src_file = $image_object['source_path'].Files::splitAppendix($rec->src, true);
						if(!is_file($src_file)){ // нет файла источника
							$error = true;
							$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Source file absent").": ".$src_file.", ".Text::_("Destination file absent").": ".$dest_file;
						} else {
							$dest_filename=$rec->src;
							$dest_file = $image_object['dest_path'].Files::splitAppendix($dest_filename, true);
							if(is_file($dest_file)){ // уже есть такой файл назначения, генерируем новое имя
								$rash=strrchr(strval($dest_filename), '.'); //забираем раширение файла
								$dest_filename=md5(time().$rec->ind.$image_object['module'].$dest_filename).time().$rash;
								Files::checkFolder($image_object['dest_path'].Files::getAppendix($dest_filename, true), true);
								$dest_file = $image_object['dest_path'].Files::splitAppendix($dest_filename, true);
							}
						}
					} else {
						$error = true;
						$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Destination file absent").": ".$dest_file.", ".Text::_("Source filename absent");
					}
				} elseif($rec->src){ // есть имя файла источника, нет имени файла назначения
					$src_file = $image_object['source_path'].Files::splitAppendix($rec->src, true);
					if(!is_file($src_file)){ // нет файла источника
						$error = true;
						$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Source file absent").": ".$src_file.", ".Text::_("Destination filename absent").": ".$dest_file;
					} else {
						$dest_filename=$rec->src;
						$dest_file = $image_object['dest_path'].Files::splitAppendix($dest_filename, true);
						if(is_file($dest_file)){ // уже есть такой файл назначения, генерируем новое имя
							$rash=strrchr(strval($dest_filename), '.'); //забираем раширение файла
							$dest_filename=md5(time().$rec->ind.$image_object['module'].$dest_filename).time().$rash;
							Files::checkFolder($image_object['dest_path'].Files::getAppendix($dest_filename, true), true);
							$dest_file = $image_object['dest_path'].Files::splitAppendix($dest_filename, true);
						}
					}
				} else {
					// нет имени файла источника, нет имени файла назначения
					$error = true;
					$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Source and destinatinations filenames absent");
				}
				if(!$error){
					if($src_file && $dest_file){ // все нормально, делаем ресайз
						$resize_result=@Files::resizeImage($src_file, $dest_file, $image_object["width"], $image_object["height"]);
//						Util::logFile("[ID#".$rec->ind."] ".$rec->title.": ".$src_file."=>".$dest_file."=>".$image_object["width"]."=>".$image_object["height"]);
						if($resize_result){
							if($dest_filename){ // апдейтим поле в базе
								$sql="UPDATE #__".$image_object["tablename"]." SET ".$image_object["field_name"]."='".$dest_filename."' WHERE ".$image_object["keystring"]."=".$rec->ind;
								$this->_db->setQuery($sql);
								if($this->_db->query()){
									// EVERYTHING OK
								} else { // не удалось обновить поле в базе
									$error = true;
									$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Failed updating database")." (".$dest_filename.")";
									if(is_file($dest_file)) Files::delete($dest_file, true);
								}
							} else {
								// EVERYTHING OK
							}
						} else {
							$error = true;
							$error_message="[ID#".$rec->ind."] ".$rec->title.": ".Text::_("Failed resizing file").": ".$src_file." => ".$dest_file;
						}
					}
				}
				if($error){
					$result[$rec->ind]["status"]="ERROR";
					$result[$rec->ind]["message"]=$error_message;
				} else {
					$result[$rec->ind]["status"]="OK";
					$result[$rec->ind]["message"]="";
				}
			}
		}
		return $result;
	}
	
	public static function webpImage($source, $quality = 100, $removeOld = false)
	{
		$dir = pathinfo($source, PATHINFO_DIRNAME);
		$name = pathinfo($source, PATHINFO_FILENAME);
		$destination = $dir . DIRECTORY_SEPARATOR . $name . '.webp';
		$info = getimagesize($source);
		$isAlpha = false;
		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($source);
			elseif ($isAlpha = $info['mime'] == 'image/gif') {
				$image = imagecreatefromgif($source);
			} elseif ($isAlpha = $info['mime'] == 'image/png') {
				$image = imagecreatefrompng($source);
			} else {
				return $source;
			}
			if ($isAlpha) {
				imagepalettetotruecolor($image);
				imagealphablending($image, true);
				imagesavealpha($image, true);
			}
			imagewebp($image, $destination, $quality);
			
			if ($removeOld)
				unlink($source);
				
				return $destination;
	}
}
?>
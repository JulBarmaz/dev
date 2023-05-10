<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class contentPlugingallery extends Plugin {
	protected $_events=array("content.prepare");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("using_plugin", "ro_string", Text::_("content_gallery_description"));
		$this->addParam("plugin_use_in_article", "boolean", 1);
		$this->addParam("plugin_use_in_blogs", "boolean", 0);
		$this->addParam("plugin_class_prefix", "string", "content_gallery");
		$this->addParam("quadro_by_row", "select", "3", false, SpravStatic::getCKArray("bs_elements_in_row"));
	}
	protected function onRaise($event, &$data) {
		$plugin_use_in_article = $this->getParam("plugin_use_in_article");
		$plugin_use_in_blogs = $this->getParam("plugin_use_in_blogs");
		$quadro_by_row_default = $this->getParam("quadro_by_row");
		$html="";
		$used_in_module=$this->getParam("used_in_module"); // set when called
		$clean_all=$this->getParam("clean_all"); // set when called
		switch($used_in_module){
			case "article":
				if (!$plugin_use_in_article) return false;
				break;
			case "blog":
				if (!$plugin_use_in_blogs) return false;
				break;
			default:
				if(!$clean_all) return false;
				break;
		}
		$gallery_regex = "#{gallery(.*?)}(.*?){/gallery}#s";
		preg_match_all( $gallery_regex, $data, $matches );
		$count = count( $matches[0] );
		if ( $count ) {
			if($clean_all) $this->processCleaning( $data, $matches, $count, $gallery_regex);
			else $this->processImages( $data, $matches, $count, $gallery_regex, $quadro_by_row_default);
		}
		return $html;
	}
	private function processCleaning(&$data, $matches, $count, $gallery_regex){
		for ( $i=0; $i < $count; $i++ ) {
			$data = str_replace( $matches[0][$i], "", $data );
		}
	}

	private function processImages(&$data, $matches, $count, $gallery_regex, $quadro_by_row_default){
		$bs_elements_in_row=array_flip(SpravStatic::getCKArray("bs_elements_in_row"));
		for ( $i=0; $i < $count; $i++ ) {
			if (isset($matches[1][$i])) {
				$html="";
				$gallery_id = 0;
				$quadro_by_row=0;
				$use_popup=0;
				$template='';
				$thumb_limit=0;
				$thumb_height="";
				$thumb_width="";
				$thumb_titles_height="";
				if (isset($matches[2][$i]))$title = $matches[2][$i]; else $title = '';
				$gallery_params = $matches[1][$i];

				$id_matches = array();
				preg_match( "#gallery_id=\|(.*?)\|#s", $gallery_params, $id_matches );
				if (isset($id_matches[1])) $gallery_id = intval($id_matches[1]);

				if($gallery_id){

					$popup_matches = array();
					preg_match( "#popup=\|(.*?)\|#s", $gallery_params, $popup_matches );
					if (isset($popup_matches[1])) $use_popup=1;
					
					$template_matches = array();
					preg_match( "#template=\|(.*?)\|#s", $gallery_params, $template_matches );
					if (isset($template_matches[1])) $template=(string)$template_matches[1];
						
					$thumb_titles_matches = array();
					preg_match( "#thumb_titles_height=\|(.*?)\|#s", $gallery_params, $thumb_titles_matches );
					if (isset($thumb_titles_matches[1])) $thumb_titles_height=$thumb_titles_matches[1];

					$thumb_limit_matches = array();
					preg_match( "#thumb_limit=\|(.*?)\|#s", $gallery_params, $thumb_limit_matches );
					if (isset($thumb_limit_matches[1])) $thumb_limit=intval($thumb_limit_matches[1]);

					$quadro_by_row_matches = array();
					preg_match( "#quadro_by_row=\|(.*?)\|#s", $gallery_params, $quadro_by_row_matches );
					if (isset($quadro_by_row_matches[1])) $quadro_by_row=intval($quadro_by_row_matches[1]);
					
					if(array_key_exists($quadro_by_row, $bs_elements_in_row)) $quadro_by_row = $bs_elements_in_row[$quadro_by_row];
					else $quadro_by_row = $quadro_by_row_default;

					$thumb_height_matches = array();
					preg_match( "#thumb_height=\|(.*?)\|#s", $gallery_params, $thumb_height_matches );
					if (isset($thumb_height_matches[1])) $thumb_height=$thumb_height_matches[1];

					$thumb_width_matches = array();
					preg_match( "#thumb_width=\|(.*?)\|#s", $gallery_params, $thumb_width_matches );
					if (isset($thumb_width_matches[1])) $thumb_width=$thumb_width_matches[1];

					$images=$this->getImages($gallery_id, $thumb_limit);
					$class_prefix = $this->getParam("plugin_class_prefix");
					if($template){
						// если темплейта нет - идем по обычному пути - иначе шаблон должен находиться по пути
						// подключенный темплейт / plugins / content/ gallery / имя темплейта php
						// содержит в себе две функции и в нем функцию рендера галереи renderGallery и функция рендера изображения 
						$templatePath = Portal::getInstance()->getTemplatePath().'plugins'.DS.'content'.DS.'gallery'.DS.$template.'.php';
						$this->message(Text::_("Looking for gallery template").": ".$templatePath, __FUNCTION__);
						
						if ((file_exists($templatePath)) && (is_file($templatePath))){
						  require_once $templatePath;
						  $templ_plug_css=Portal::getURI()."/templates/".Portal::getInstance()->getTemplate()."/css/plugins/content/gallery_".$template.".css";
						  Portal::getInstance()->addStyleSheet($templ_plug_css, !seoConfig::$tmplCSSBackCompatibility);
						  $html.=renderGallery($title,$class_prefix,$gallery_id,$images, $use_popup, $thumb_width, $thumb_height, $thumb_titles_height);
						} else {	
						  $html.="fail show template attempt ".$template;
						}
					} else {	 
						$html.="<div class=\"".$class_prefix." row\" id=\"plug_gal_".$gallery_id."\">";
						if($title) $html.="<h3 class=\"".$class_prefix."_title\">".$title."</h3>";
						$html.=$this->renderThumbs($images, $quadro_by_row, $use_popup, $thumb_width, $thumb_height, $thumb_titles_height, $gallery_id);
						$html.="</div>";
					}
					$data = str_replace( $matches[0][$i], $html, $data );
				}
			}
		}
	}
	private function renderThumbs($images, $quadro_by_row, $use_popup, $thumb_width, $thumb_height, $thumb_titles_height, $gallery_id){
		$html="";
		$class_prefix = $this->getParam("plugin_class_prefix");
		$group_attr=$this->getParam("group_attribute", false, "data-gg-attr");
		if(count($images)){
			$thumb_height_str="auto";
			$thumb_width_str="100%";
			$thumb_titles_height_str="";
			if($thumb_titles_height){
				if(strtolower(substr($thumb_titles_height, strlen($thumb_titles_height)-2, 2))=="px"){
					$thumb_titles_height_str = intval(substr($thumb_titles_height, 0, strlen($thumb_titles_height)-2))."px";
				} elseif(intval($thumb_height)>0) {
					$thumb_titles_height_str = intval($thumb_titles_height)."px";
				}
			}
			if($thumb_height){
				if(strtolower(substr($thumb_height, strlen($thumb_height)-2, 2))=="px"){
					$thumb_height_str = intval(substr($thumb_height, 0, strlen($thumb_height)-2))."px";
				} elseif(intval($thumb_height)>0) {
					$thumb_height_str = intval($thumb_height)."px";
				}
			}
			if($thumb_width){
				if(strtolower(substr($thumb_width, strlen($thumb_width)-2, 2))=="px"){
					$thumb_width_str = intval(substr($thumb_width, 0, strlen($thumb_width)-2))."px";
				}elseif(strtolower(substr($thumb_width, strlen($thumb_width)-1, 1))=="%"){
					$thumb_width_str = intval(substr($thumb_width, 0, strlen($thumb_width)-1))."%";
				} elseif(intval($thumb_width)>0) {
					$thumb_width_str = intval($thumb_width)."px";
				}
			}
			
			if($use_popup){
				$script="$(document).ready(function() {
							$('a.".$class_prefix."_thumb_link').fancybox({
								'hideOnOverlayClick': true,
								'cyclic'			: true,
								'titlePosition'		: 'over', // 'float', 'outside', 'inside' or 'over'
								'hideOnContentClick': false,
								'rel'				: '".$group_attr."',
								'transitionIn'		: 'elastic',
								'transitionOut'		: 'elastic',
								'speedIn'			: 200,
								'speedOut'			: 100,
								'autoDimensions' 	: true,
								'scrolling'			: 'no',
								'centerOnScroll'	: true,
								'titleShow'			: ".($thumb_titles_height ? "true" : "false").",
								'enableNavArrows'	: true,
								'showNavArrows'		: true
							});
						});";
				Portal::getInstance()->addScriptDeclaration($script);
			}
			
			$automax=" style=\"max-width:".$thumb_width_str."; max-height:".$thumb_height_str.";\"";
			if($thumb_height_str) $minheight="style=\"min-height:".$thumb_height_str.";\"";
			
			foreach($images as $image) {
				$thumb_file=BARMAZ_UF_PATH.'gallery'.DS.'i/thumbs'.DS.Files::getAppendix($image->gi_thumb).DS.$image->gi_thumb;
				$thumb=file_exists($thumb_file);
				$img_file=BARMAZ_UF_PATH.'gallery'.DS.'i'.DS.Files::getAppendix($image->gi_image).DS.$image->gi_image;
				$img=file_exists($img_file);
				$title=addslashes($image->gi_title);
				if ((!$thumb)&&(!$img)) {
					$thumb_link="";
					$image_link="";
				} elseif(!$thumb) {
					$image_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
					$thumb_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
				} elseif(!$img) {
					$image_link="";
					$thumb_link=BARMAZ_UF.'/gallery/i/thumbs/'.Files::getAppendix($image->gi_thumb)."/".$image->gi_thumb;
				} else {
					$image_link=BARMAZ_UF.'/gallery/i/'.Files::getAppendix($image->gi_image)."/".$image->gi_image;
					$thumb_link=BARMAZ_UF.'/gallery/i/thumbs/'.Files::getAppendix($image->gi_thumb)."/".$image->gi_thumb;
				}
				$html.= "<div class=\"col-xss-12 col-xs-6 col-sm-".$quadro_by_row."\">";
				$html.= "<div class=\"".$class_prefix."_image\">";

				if ($use_popup && $image_link) $html.= "<a ".$group_attr."=\"gallery_".$gallery_id."\" title=\"".$title."\" class=\"".$class_prefix."_thumb_link\" href=\"".$image_link."\">";
				else $html.= "<div class=\"".$class_prefix."_thumb_div\">";
				$html.= "<span ".$minheight." class=\"".$class_prefix."_thumb_wrapper\">";
				if ($thumb_link) $html.= "<img".$automax." title=\"".$image->gi_title_img."\" alt=\"".$image->gi_alt_img."\" class=\"".$class_prefix."_thumb\" src=\"".$thumb_link."\" />";
				else $html.= "<img width=\"1\" height=\"1\"".$automax." alt=\"\" class=\"".$class_prefix."_empty_thumb\" src=\"/images/blank.gif\" />";
				$html.= "</span>";
				if($thumb_titles_height_str) $html.= "<span style=\"height:".$thumb_titles_height_str.";\" class=\"".$class_prefix."_title\" title=\"".$title."\">".$title."</span>";
				if ($use_popup && $image_link) $html.= "</a>";
				else $html.= "</div>";
				$html.= "</div></div>";
			}
		}
		return $html;
	}
	private function getImages($gallery_id, $thumb_limit){
		$sql="SELECT * FROM #__gallery_images WHERE gi_gallery_id=".$gallery_id." AND gi_published=1 AND gi_deleted=0 ORDER BY gi_ordering";
		if($thumb_limit) $sql.=" LIMIT ".$thumb_limit;
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}
}
?>
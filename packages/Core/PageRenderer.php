<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class PageRenderer extends HTMLRenderer {

	private $_scripts		= array();
	private $_scriptDeclarationsHeader	= array();
	private $_scriptDeclarationsFooter	= array();

	private $_styleSheets	= array();
	private $_styles		= array();

	private $_unreg_rendered 	= false;
	private $_templateName	= '';
	private $_charset		= "utf-8";
	private $_head_tag		= '<head>';
	private $_title			= '';
	private $_description	= '';
	private $_metas			= array();
	private $_custom_tags	= array();
	private $_subroot		= '/';
	protected $_printMode = false;
	public function render() {
		ob_start();
		parent::render();
		$html=ob_get_contents();
		ob_end_clean();
		// Template stylesheet
		$this->addStyleSheet("main.css", !seoConfig::$tmplCSSBackCompatibility);
		if($this->_printMode) $this->addStyleSheet("print.css", !seoConfig::$tmplCSSBackCompatibility);
		// Main module stylesheet
		$this->addStyleSheet("modules/".$this->get("module").".css", !seoConfig::$tmplCSSBackCompatibility);
		if (!Portal::getInstance()->isDisabled() && !Portal::getInstance()->inPrintMode()) $this->addScript("template.js");
		$this->milestone("PageRenderer rendered.Fetching footer (No more milestones expected)", __FUNCTION__);
		$unregistered_1=$this->fetchUnregistered();
		$unregistered_2=$this->fetchUnregistered();
		$html=$this->fetchHead().$unregistered_1.$html.$unregistered_2.$this->fetchFooter();
		Event::raise("system.renderPageRendererBeforeOutput", array(), $html);
		echo $html;
	}
	protected function fetchHead() {
		$headCustomHtml=Event::raise("system.fetchHeadBefore", array(), $this);
		if(!is_null($headCustomHtml)) return $headCustomHtml;
		
		if (defined('_BARMAZ_HTML5')){
			$headHtml = "<!DOCTYPE html>";
			$headHtml.= "<html lang=\"".Text::getLanguage()."\">\n";
			$headHtml.= $this->_head_tag."\n";
		} else {
			$headHtml = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"".Text::getLanguage()."-".Text::getLanguage()."\" lang=\"".Text::getLanguage()."-".Text::getLanguage()."\">\n";
			$headHtml.= $this->_head_tag."\n";
		}
		
		if (siteConfig::$debugMode>0) { // Полное отключение кэширования страниц
			$this->setMeta("Pragma","no-cache",true);
			$this->setMeta("cache-control","no-cache",true);
			$this->setMeta("Expires","0",true);
		}
		
		foreach ($this->_metas as $meta) {
			if($meta->name && $meta->content && !$meta->is_property){
				if ($meta->httpEquiv == true) {	
					$headHtml .= "<meta http-equiv=\"".$meta->name."\" content=\"".$meta->content."\" />\n";
				}
			}
		}

		$headHtml .= "<title>".$this->_title."</title>\n";

		foreach ($this->_metas as $meta) {
			if($meta->name && !$meta->is_property){
				// по заветам Ашмановцев - кейворды вредят продвижению.
//				if(!seoConfig::$enableMetaKeywords && $meta->name=="keywords") continue; // Вариант когда вообще не выводим
				if(!seoConfig::$enableMetaKeywords && $meta->name=="keywords") $meta->content=""; // Вариант когда выводим пустой
				if ($meta->httpEquiv != true) {
					$headHtml .= "<meta name=\"".$meta->name."\" content=\"".$meta->content."\" />\n";
				}
			}
		}
		
		foreach ($this->_metas as $meta) {
			if($meta->name && $meta->content && $meta->is_property){
				$headHtml .= "<meta property=\"".$meta->name."\" content=\"".$meta->content."\" />\n";
			}
		}
		foreach ($this->_custom_tags as $_custom_tag_key=>$_custom_tag) {
			$headHtml .= $_custom_tag."\n";
		}
// C base не работает якорь как его ставят обычно		
//		$headHtml .= "<base href=\"".Portal::getInstance()->getURI()."\" />";

		$favicon=Portal::getURI().LINK_TEMPLATES."/".$this->_templateName."/images/favicon.ico";
		$headHtml .="<link type=\"image/ico\" href=\"".$favicon."\" rel=\"icon\" />";
		// Stylesheets
		$headHtml .= "<!-- Stylesheets -->\n";
		foreach ($this->_styleSheets as $strSrc => $strAttr ) {
			if (Router::isAbsoluteLink($strSrc)) {
				$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$strSrc."\"";
				if (!is_null($strAttr['media'])) { $headHtml .= " media=\"".$strAttr['media']."\""; }
				$headHtml .= " />\n";
				if(siteConfig::$debugMode>2) Debugger::getInstance()->message("CSS load attempt (Strict path): ".$source_link." (".$source_path.")");
			} elseif ($strSrc[0] == "/") {
				$source_path = PATH_FRONT.Util::dsPath($strSrc);
				if (file_exists($source_path)) {
					if($strAttr['inc2body']){
						$headHtml .= "<style>";
						$headHtml .= file_get_contents($source_path);
						$headHtml .= "</style>\n";
					} else {
						$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$this->appendBuildVersion($strSrc)."\"";
						if (!is_null($strAttr['media'])) {	$headHtml .= " media=\"".$strAttr['media']."\""; }
						$headHtml .= " />\n";
					}
				}
			} else {
				if (defined("_ADMIN_MODE")) $override=adminConfig::$cssOverride; else $override=siteConfig::$cssOverride;
				if ($override){
					$source_path = PATH_TEMPLATES.$this->_templateName.DS."css".DS.Util::dsPath($strSrc);
					$source_link = LINK_TEMPLATES."/".$this->_templateName."/css/".$strSrc;
					if (!file_exists($source_path)) {
						if(siteConfig::$debugMode>2) Debugger::getInstance()->message("CSS load attempt (Base path): ".$source_link." (".$source_path.")");
						$source_path = PATH_CSS.Util::dsPath($strSrc);
						$source_link = LINK_CSS."/".$strSrc;
						if (file_exists($source_path)) {
							if(siteConfig::$debugMode || !$strAttr['inc2body']) {
								if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link;
								$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$this->appendBuildVersion($source_link)."\"";
								if (!is_null($strAttr['media'])) {	$headHtml .= " media=\"".$strAttr['media']."\""; }
								$headHtml .= " />\n";
							} else {
								$headHtml .= "<style>";
								$headHtml .= file_get_contents($source_path);
								$headHtml .= "</style>\n";
							}
						} else {
							if(siteConfig::$debugMode>2) Debugger::getInstance()->warning("CSS load attempt failed (Base path): ".$source_link." (".$source_path.")");
						}
					} else {
						if(siteConfig::$debugMode>2) Debugger::getInstance()->message("CSS load attempt (Template path): ".$source_link." (".$source_path.")");
						if(siteConfig::$debugMode || !$strAttr['inc2body']) {
							if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link;
							$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$this->appendBuildVersion($source_link)."\"";
							if (!is_null($strAttr['media'])) {	$headHtml .= " media=\"".$strAttr['media']."\""; }
							$headHtml .= " />\n";
						} else {
							$headHtml .= "<style>";
							$headHtml .= file_get_contents($source_path);
							$headHtml .= "</style>";
						}
					}
				} else {
					$source_path = PATH_CSS.Util::dsPath($strSrc);
					$source_link = LINK_CSS."/".$strSrc;
					if(siteConfig::$debugMode>2) Debugger::getInstance()->message("CSS load attempt (Base path): ".$source_link." (".$source_path.")");
					if (file_exists($source_path)) {
						if(siteConfig::$debugMode || !$strAttr['inc2body']) {
							if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link;
							$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$this->appendBuildVersion($source_link)."\"";
							if (!is_null($strAttr['media'])) {	$headHtml .= " media=\"".$strAttr['media']."\""; }
							$headHtml .= " />\n";
						} else {
							$headHtml .= "<style>";
							$headHtml .= file_get_contents($source_path);
							$headHtml .= "</style>";
						}
					}
					$source_path = PATH_TEMPLATES.$this->_templateName.DS."css".DS.Util::dsPath($strSrc);
					$source_link = LINK_TEMPLATES."/".$this->_templateName."/css/".$strSrc;
					if(siteConfig::$debugMode>2) Debugger::getInstance()->message("CSS load attempt (Template path): ".$source_link." (".$source_path.")");
					if (file_exists($source_path)) {
						if(siteConfig::$debugMode || !$strAttr['inc2body']) {
							if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link; 
							$headHtml .= "<link rel=\"stylesheet\" type=\"".$strAttr['mime']."\" href=\"".$this->appendBuildVersion($source_link)."\"";
							if (!is_null($strAttr['media'])) $headHtml .= " media=\"".$strAttr['media']."\""; 
							$headHtml .= " />\n";
						} else {
							$headHtml .= "<style>";
							$headHtml .= file_get_contents($source_path);
							$headHtml .= "</style>";
						}
					}
				}
			}
		}

		// Styles
		$headHtml .= "<!-- Styles -->\n";
		foreach ($this->_styles as $style) {
			$headHtml .= "<style type=\"text/css\">\n".$style."\n</style>\n";
		}
		$headHtml .='
<!--[if IE 8]>
	<link href="'.$this->getTemplateURI().'css/ie8only.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if IE 9]>
	<link href="'.$this->getTemplateURI().'css/ie9only.css" rel="stylesheet" type="text/css" />
<![endif]-->';

		defined("_BARMAZ_IE_DROP") OR define("_BARMAZ_IE_DROP", 6);
		$headHtml .='<!--[if lte IE '._BARMAZ_IE_DROP.']><script src="'.$this->getTemplateURI().'js/ie'._BARMAZ_IE_DROP.'.js" /><![endif]-->';

		if (defined('_BARMAZ_HTML5')){
			$headHtml .='<!--[if lt IE 9]>
	<script src="/redistribution/html5_css3/'.$this->appendBuildVersion("html5shiv.min.js").'"></script>
<![endif]-->';
		}
		if (defined('_BARMAZ_HTML5')){
			if((siteConfig::$loadBootstrap && !defined("_ADMIN_MODE")) || (adminConfig::$loadBootstrap && defined("_ADMIN_MODE"))){
				$headHtml .='<!--[if lt IE 9]>
	<script src="/redistribution/bootstrap/js/'.$this->appendBuildVersion("respond.min.js").'"></script>
<![endif]-->';
			}
		}
		
		// Script file links
		$headHtml .= "<!-- Scripts -->\n";
		$headHtml .= "<!--noindex-->\n";
		foreach ($this->_scripts as $strSrc => $params) {
			if($params["put2header"]){
				if (Router::isAbsoluteLink($strSrc)) {
					$headHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$strSrc."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
				} elseif ($strSrc[0] == "/") {
					$headHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$this->appendBuildVersion($strSrc)."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
				} else {
					// template file check for override
					$source_path = PATH_TEMPLATES.$this->_templateName.DS."js".DS.Util::dsPath($strSrc);
					$source_link = LINK_TEMPLATES."/".$this->_templateName."/js/".$strSrc;
					if (!file_exists($source_path)) {
						$source_path = PATH_JS.Util::dsPath($strSrc);
						$source_link = LINK_JS."/".$strSrc;
					}
					if (file_exists($source_path)) {
						if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link;
						$headHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$this->appendBuildVersion($source_link)."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
					}
				}
				$this->_scripts[$strSrc]["rendered"]=true;
			}
		}
		// Script declarations
		$headHtml .= "<!-- Script declarations -->\n";
		foreach ($this->_scriptDeclarationsHeader as $scriptType=>$scriptDeclaration) {
			$headHtml .= "<script".($scriptType=="none" ? "" : " type=\"".$scriptType."\"").">//<![CDATA[\n";
			$headHtml .= $scriptDeclaration;
			$headHtml .= "\n // ]]>\n</script>\n";
		}
		$headHtml .= "<!--/noindex-->\n";
		$headHtml .="</head><body class=\"".$this->get("module")."-".$this->get("view")."-".$this->get("layout").($this->isMainpage(1) ? " is-main-page" : "")."\">";
		Event::raise("system.fetchHeadAfter", array(), $headHtml);
		return $headHtml;
	}

	protected function getCopyright($class="",$style="") {
		if ($class) $class=" class=\"".$class."\"";
		if ($style) $style=" style=\"".$style."\"";
		return "Powered by <a ".$class.$style." href=\"https://barmaz.ru/\" target=\"_blank\">BARMAZ-CMS</a> &copy; 2022-".date("Y").". All rights reserved.";
	}
	
	protected function fetchUnregistered() {
		if (Portal::getInstance()->getLicenseType()=="DEMO") {
			if ($this->_unreg_rendered) return "";
			if (rand(0,1)) return "";
			if (rand(0,1)) $message=$this->getCopyright();
			else $message=Text::utf8_unicode(html_entity_decode(strip_tags($this->getCopyright()),ENT_COMPAT,DEF_CP));
			return $this->fetchRandomMessage($message);
		}
		return "";
	}
	protected function fetchRandomMessage($message, $color="#AAAAAA") {
		if($message){
			$size_delta=rand(1,200);
			$margin_delta=intval($size_delta/2);
			$hash=hash("md5",time().rand(555555,777777),false); $pattern="(\d+)"; $replacement=""; 
			$myrandom_id = substr(preg_replace($pattern,$replacement,$hash),1,10); 
			$style ="div#".$myrandom_id."{ width:".(400 + $size_delta)."px; position:fixed; text-align:center; z-index:".(99999 + rand(444,555555))."; bottom:0px; left:50%; margin-left:-".(200 + $margin_delta)."px !important; margin-bottom: 0 !important; background-color:#FFFFFF; border:1px solid; height:14px; line-height:12px; font-size:10px; color:".$color."; display:block !important;}";
			$style.="div#".$myrandom_id." a{ color:".$color."; }";
			$this->addStyle($style);
			$this->_unreg_rendered=true;
			return "<div id=\"".$myrandom_id."\">".$message."</div>";
		} 
		return "";
	}
	protected function fetchFooter() {
		$footerCustomHtml=Event::raise("system.fetchFooterBefore", array(), $this);
		if(!is_null($footerCustomHtml)) return $footerCustomHtml;
		
		$footerHtml ="<!--noindex-->\n";
		$footerHtml.="<div style=\"display:none;\">".Debugger::getInstance()->getTime()."</div>";
		$footerHtml.="<div id=\"barmaz-loading\" title=\"".Text::_("Click to cancel")."\" style=\"display:none;\"><p id=\"loading-text\">".Text::_("Loading")."...</p></div>";
		$footerHtml.="<div id=\"barmaz-overlay\" style=\"display:none;\"></div>";
		foreach ($this->_scripts as $strSrc => $params) {
			if(!$params["rendered"]){
				if (Router::isAbsoluteLink($strSrc)) {
					$footerHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$strSrc."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
				} elseif ($strSrc[0] == "/") {
					$footerHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$strSrc."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
				} else {
					// template file check for override
					$source_path = PATH_TEMPLATES.$this->_templateName.DS."js".DS.Util::dsPath($strSrc);
					$source_link = LINK_TEMPLATES."/".$this->_templateName."/js/".$strSrc;
					if (!file_exists($source_path)) {
						$source_path = PATH_JS.Util::dsPath($strSrc);
						$source_link = LINK_JS."/".$strSrc;
					}
					if (file_exists($source_path)) {
						if ($source_link[0] != "/") $source_link = $this->_subroot.$source_link;
						$footerHtml .= "<script ".($params['type']=="none" ? "" : " type=\"".$params['type']."\"")." src=\"".$source_link."\"".($params['defer'] ? " defer" : "").($params['async'] ? " async" : "")."></script>\n";
					}
				}
			}
		}
		foreach ($this->_scriptDeclarationsFooter as $scriptType=>$scriptDeclaration) {
			$footerHtml .= "<script".($scriptType=="none" ? "" : " type=\"".$scriptType."\"").">//<![CDATA[\n";
			$footerHtml .= $scriptDeclaration;
			$footerHtml .= "\n // ]]>\n</script>\n";
		}
		$footerHtml.="<!--/noindex-->";
		Event::raise("system.fetchFooterAfter", array(), $footerHtml);
		// Debugger dump
		if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $footerHtml = $footerHtml.Debugger::getInstance()->dump();
		$footerHtml .= "</body></html>";
		return $footerHtml;
	}
	//--------------------------- Head control -------------------------------------
	public function setTitle($title='') {
		$this->_title = $title;
		if (siteConfig::$enableGeneratorMetaTag) $this->setMeta("generator","Barmaz erp");
	}
	public function getTitle() {
		return $this->_title ;		
	}
	public function getMeta($name,$httpEquiv=false, $is_property=false) {
		if ($httpEquiv == true) $metaKey = $name."_httpequiv";
		elseif ($is_property == true) $metaKey = $name."_is_property";
		else $metaKey = $name;
		if (array_key_exists($metaKey, $this->_metas) == true)	return $this->_metas[$metaKey];
		else return false;
		
	}
	public function isMainpage($skipDefaultID=false) {
		// return (!(Request::getSafe("module","") || Request::getSafe("option",""))||(!$skipDefaultID && $_SESSION['active_menu_id']==siteConfig::$defaultMenuID));
		return (
				!(
						(
								Request::getSafe("task","")=="search"
								&&
								!Request::getSafe("module","")
								&&
								!Request::getSafe("option","")
								)
						||
						Request::getSafe("module","")
						||
						Request::getSafe("option","")
						)
				||
				(
						!$skipDefaultID
						&&
						$_SESSION['active_menu_id']==siteConfig::$defaultMenuID
						)
				);
	}
	public function setCustomHeadTag($head_tag='') {
		$this->_head_tag = $head_tag;
	}
	public function setCharset($type = 'utf-8') {
		$this->setMeta("Content-Type","text/html; charset=".$type,true);
	}
	public function setDescription($description) {
		$this->setMeta("description",$description);
	}
	public function setMeta($name, $content, $httpEquiv=false, $is_property=false) {
		if ($httpEquiv == true) $metaKey = $name."_httpequiv";
		elseif ($is_property == true) $metaKey = $name."_is_property";
		else $metaKey = $name;

		if (array_key_exists($metaKey, $this->_metas) == true) {
			$this->_metas[$metaKey]->name = $name;
			$this->_metas[$metaKey]->content = $content;
		}	else {
			$meta = new stdClass();
			$meta->name			= $name;
			$meta->content		= $content;
			$meta->httpEquiv	= $httpEquiv;
			$meta->is_property	= $is_property;
			$this->_metas[$metaKey] = $meta;
		}
	}
	public function addCustomTag($content) {
		$this->_custom_tags[] = $content;
	}
	public function getTemplate() {
		return $this->_templateName;
	}
	public function isDisabled() {
		return (siteConfig::$siteDisabled == true && User::getInstance()->isLoggedIn() == false);
	}
	public function setTemplate($templateName, $critical=true) {
		$templateDir = PATH_TEMPLATES.$templateName.DS;
		// проверим наличие директории шаблона - а то падает где-то ниже
		if(!is_dir($templateDir))		die('Template dir for '.$templateName. ' not found. Correct it');
		$this->setTemplatePath($templateDir);
		if ($this->isDisabled()) {
			if(defined("_ADMIN_MODE")) Util::redirect("/");
			else $templateFile=$templateDir."disabled.php";
		}
		else $templateFile=$templateDir."default.php"; 
		if (is_file($templateFile)){
			$this->setTemplateFile($templateFile); 
			$this->_templateName = $templateName;
			return true;
		} else {
			if($critical) $this->fatalError(Text::_("Template not found")." : ".$templateName); // Realy fatal, if critical flag true.
			else $this->error("PageRenderer failed to set template: ".$templateName);
			return false;	
		}
	}
	public function setSubroot($subroot='') {
		if ($subroot)	$this->_subroot = "/".$subroot."/";
	}
	
	public function addScript($url, $defer=false, $async=false, $put2header=false, $type="none") {
		if (array_key_exists($url, $this->_scripts) == true) {
			$this->warning(Text::_("Resource overwrite attempt")." ".Text::_("for script")." \"".$url."\"(defer=>".$defer.", async=>".$async.", put2header=>".$put2header.", type=>".$type.")");
		} else {
			$this->_scripts[$url] = array("defer"=>$defer, "async"=>$async, "put2header"=>$put2header, "type"=>$type, "rendered"=>false);
		}
	}
	public function addScriptDeclaration($content, $put2header=false, $type="none") {
		if($put2header){
			if(array_key_exists($type, $this->_scriptDeclarationsHeader) == false){
				$this->_scriptDeclarationsHeader[$type] = $content;
			} else {
				$this->_scriptDeclarationsHeader[$type] .= chr(13).$content;
			}
		} else {
			if(array_key_exists($type, $this->_scriptDeclarationsFooter) == false){
				$this->_scriptDeclarationsFooter[$type] = $content;
			} else {
				$this->_scriptDeclarationsFooter[$type] .= chr(13).$content;
			}
		}
	}
	// $inc2body works only for full relative paths started with slash
	// global CSS files are always included in body
	public function addStyleSheet($url, $inc2body=false, $type="text/css", $media=null) {
		if (array_key_exists($url,$this->_styleSheets) == true) {
			$this->warning(Text::_("Resource overwrite attempt")." ".Text::_("for stylesheet")." \"".$url."\"(".$type.")");
		}	else {
			$this->_styleSheets[$url]['mime'] = $type;
			$this->_styleSheets[$url]['media'] = $media;
			$this->_styleSheets[$url]['inc2body'] = $inc2body;
		}
	}

	public function addStyle($style) {
		$this->_styles []= $style;
	}
	public function appendBuildVersion($strSrc){
		return $strSrc."?v=".Portal::getVersionRevision();
	}
	//------------------------------------------------------------------------------
	public function getVarsArr(){
		return array("_styles"=>&$this->_styles, "_styleSheets"=>&$this->_styleSheets, "_scripts"=>&$this->_scripts, "_scriptDeclarationsHeader"=>&$this->_scriptDeclarationsHeader, "_scriptDeclarationsFooter"=>&$this->_scriptDeclarationsFooter, "_metas"=>&$this->_metas, "custom_tags"=>&$this->_custom_tags);
	}
}
?>
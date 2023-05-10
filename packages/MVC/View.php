<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class View extends HTMLRenderer {
	private $_showPagePanel		= false;
	private $_breadCrumb		= array();
	protected $_isCanonicalToMainpage = false;
	protected $_rendered = false;
	private $_layoutFile = "";
	public function __construct($name) {
		$this->initObj($name);
		// @TODO Canonical links
		/*
		echo "<pre>";
		echo "<br />Module=".Module::getInstance()->getName();
		echo "<br />Viewname=".$this->getName();
		echo "<br />Default Viewname=".Module::getInstance()->getDefaultView();
		echo "<br />Layout=".$this->getLayout();
		echo "<br />Psid=".Module::getInstance()->getController()->getPsid();
		echo "</pre>";
		*/
		if(!defined("_ADMIN_MODE")){
			if(Module::getInstance()->getName()==siteConfig::$defaultModule){
				if($this->getName()==Module::getInstance()->getDefaultView()){
					if($this->getLayout()=="default"){
						if(Module::getInstance()->getController()->getPsid()==Module::getInstance()->getParam("Default_item_ID") && !Portal::getInstance()->isMainpage()){
							$this->_isCanonicalToMainpage=true;
//							Portal::getInstance()->addCustomTag("<link rel=\"canonical\" href=\"".Portal::getInstance()->getURI()."\" />");
						}
					}
				}
			}
		}
	}
	public function setMeta($metatag, $metavalue) {
		if (($metatag)&&($metavalue)) {
			switch ($metatag) {
				case "title":
					$metavalue=mb_substr($metavalue,0,250,DEF_CP);
					Portal::getInstance()->setTitle($metavalue);
					break;
				case "keywords":
					$metavalue=mb_substr($metavalue,0,250,DEF_CP);
					Portal::getInstance()->setMeta("keywords", $metavalue);
					break;
				case "description":
					$metavalue=mb_substr($metavalue,0,250,DEF_CP);
					Portal::getInstance()->setDescription($metavalue);
					break;
			}
		}
	}
	/**
	 * название заменяемого штатного модуля, для определения пути к файлам  
	 * @param string $module_name - имя штатного модуля
	 * @return NULL|string - пусто или имя заменяемого модуля
	 */
	public function getpath_name($module_name='')
	{
		if($module_name){
			Database::getInstance()->setQuery("select m_replace_name from #__modules where m_name='".$module_name."'");
			$pathl=Database::getInstance()->loadResult();
			if($pathl) return $pathl;
		}
		return $module_name;
	}
	
	public function getLayout() {
		return $this->get('layout');
	}
	public function setLayout($layout) {
		$_layout=$this->get('layout');
		$pathr=$this->getpath_name($this->get('module'));
		if (defined('_ADMIN_MODE')) $lPath = PATH_TEMPLATES.adminConfig::$adminTemplate.DS.'html'.DS.'modules'.DS.$pathr.DS.$this->getName().DS.$layout.'.php';
		else $lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'modules'.DS.$pathr.DS.$this->getName().DS.$layout.'.php';
		if (!is_file($lPath)) {
			$lPath = PATH_MODULES.$pathr.DS.'views'.DS.'template'.DS.$this->getName().DS.$layout.'.php';
			if (!is_file($lPath) && defined('_ADMIN_MODE')) {
				$lPath = PATH_FRONT_MODULES.$pathr.DS.'views'.DS.'template'.DS.$this->getName().DS.$layout.'.php';
			}
		}
		if (is_file($lPath)) {
			$this->set('layout', $layout, true);
			$this->_layoutFile = $lPath;
		} else {
			if ($_layout!=$layout) {
				$this->error(Text::_("Layout change error")." : |".$_layout."| => |".$layout."|");
			} else {
				if($layout == "default"){
					$this->fatalError(Text::_("Layout not found")." : ".$lPath, ($this->_debugger==0 ? "404" : "503")); // Realy fatal if default, else redirect already set in next lines.
				} else {
					if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) $this->fatalError(Text::_("Layout not found")." : ".$lPath, defined("_ADMIN_MODE") ? "503" : "404"); // Render fatal for admin debug.
					else Util::redirect(Router::_("index.php"), Text::_("Layout not found")." : ".$lPath, "404");
				}
			}
		}
	}
	public function getCustomLayoutPath($layout) {
		if (defined('_ADMIN_MODE')) $lPath = PATH_TEMPLATES.adminConfig::$adminTemplate.DS.'html'.DS.'modules'.DS.$this->get('module').DS.$this->getName().DS.$layout.'.php';
		else $lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'modules'.DS.$this->get('module').DS.$this->getName().DS.$layout.'.php';
		if (!is_file($lPath)) {
			$lPath = PATH_MODULES.$this->get('module').DS.'views'.DS.'template'.DS.$this->getName().DS.$layout.'.php';
			if (!is_file($lPath) && defined('_ADMIN_MODE')) {
				$lPath = PATH_FRONT_MODULES.$this->get('module').DS.'views'.DS.'template'.DS.$this->getName().DS.$layout.'.php';
			}
		}
		if (is_file($lPath)) return $lPath;
		return false;
	}
	public function includeLayout($layout) {
		$lPath = $this->getCustomLayoutPath($layout);
		if ($lPath) include($lPath);
	}
	public function resetRenderFlag() {
		$this->_rendered = false;
	}
	public function render() {
		$this->prepare();
		$layout = $this->get('layout');
		$this->setLayout($layout);

		if (!is_file($this->_layoutFile)) {
			$this->fatalError(Text::_("Layout not found")." : ".$this->_layoutFile, "404"); // Realy fatal. This is main view render.
		}	else {
			if (!$this->_rendered){ 
				if (Portal::getInstance()->noTemplate()) $class_postfix="_nt"; else $class_postfix="";
				echo "<div class=\"moduleBody".$class_postfix." ".$this->get("module")."Module\"><div class=\"content".$class_postfix."\">";
				include_once $this->_layoutFile;
				echo "</div>";
				echo $this->renderPagePanel();
				echo "</div>";
			}
		}
		$this->milestone("View rendered => ".$this->get('module').".".$this->getName().".".$layout, __FUNCTION__);
	}
	private function renderShortcut($shortcut) {
		$scHTML = "";
		if ($shortcut["text"]) {
			$scHTML .= "<a href=\"#\" class=\"linkButton btn btn-info \" rel=\"nofollow\"";
			if ($shortcut["id"]) $scHTML .= " id=\"".$shortcut['id']."\"";
			$buttonWidth = intval($shortcut['width']);
			if ($buttonWidth) $scHTML .= " style=\"width:".$buttonWidth."px\"";
			$scHTML .= " onclick=\"".$shortcut["onclick"]."\">".$shortcut['text']."</a>";
				
		}
		return $scHTML;
	}
	public function getBreadCrumbArray() { 
		return $this->_breadCrumb;
	}
	public function setBreadCrumbArray($array) {
		$this->_breadCrumb=$array;
	}

	public function renderBreadCrumb() { 
		if (Portal::getInstance()->inPrintMode()) return "";
		$headerHTML = "<div class=\"moduleBreadcrumb\">";
		foreach ($this->_breadCrumb as $bc) {
			if(defined("_ADMIN_MODE")) $headerHTML .= "<img class=\"breadcrumb\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" alt=\"\" />";
			else $headerHTML .= "<img class=\"breadcrumb\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" alt=\"\" />";
			if ($bc->link=="#")	$headerHTML .= "<a class=\"breadcrumb\">".$bc->text."</a>";
			else $headerHTML .= "<a class=\"breadcrumb\" href=\"".$bc->link."\">".$bc->text."</a>";
		}
		$headerHTML .= "</div>";
		return $headerHTML;
	}
	public function renderPagePanel() {
		if ($this->_showPagePanel == true) {
			if (defined('_ADMIN_MODE')) $lPath = PATH_TEMPLATES.adminConfig::$adminTemplate.DS.'html'.DS.'modules'.DS.'paginator.php';
			else $lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'modules'.DS.'paginator.php';
			if (is_file($lPath)) {
				ob_start();
				include $lPath;
				$_html = ob_get_contents();
				ob_end_clean();
			} else { 
				$_html="<div class=\"navigator\">";
				$_html.="<div class=\"navigator_pages\">";
				$_html.="<ul class=\"pagination\">";
				if ($this->firstPageLink) $_html.="<li class=\"page-item\"><a class=\"pageLink firstPageLink\" href=\"".$this->firstPageLink."\">".Text::_("First page")."</a></li>";
				else $_html.="<li class=\"page-item\"><span class=\"pageLink firstPageLink\">".Text::_("First page")."</span></li>";
				if ($this->prevPageLink) $_html.="<li class=\"page-item\"><a class=\"pageLink prevPageLink\" href=\"".$this->prevPageLink."\">".Text::_("Prev.page")."</a></li>";
				else $_html.="<li class=\"page-item\"><span class=\"pageLink prevPageLink\">".Text::_("Prev.page")."</span></li>";
				echo $this->pageLinks;
				if ($this->nextPageLink) $_html.="<li class=\"page-item\"><a class=\"pageLink nextPageLink\" href=\"".$this->nextPageLink."\">".Text::_("Next page")."</a></li>";
				else $_html.="<li class=\"page-item\"><span class=\"pageLink nextPageLink\">".Text::_("Next page")."</span></li>";
				if ($this->lastPageLink) echo "<li class=\"page-item\"><a class=\"pageLink lastPageLink\" href=\"".$this->lastPageLink."\">".Text::_("Last page")."</a></li>";
				else $_html.="<li class=\"page-item\"><span class=\"pageLink lastPageLink\">".Text::_("Last page")."</span></li>";
				$_html.="</ul></div>";
				$_html.="<div class=\"navigator_records\">"; 
				$_html.=Text::_("Records")."&nbsp;".$this->pageRange."&nbsp;".Text::_("of")."&nbsp;".$this->recordsTotal;
				$_html.="</div>";				
				$_html.="</div>";				
			}
		} else $_html=""; 
		return $_html;
	}
	public function showPagePanel($show=true) {
		$this->_showPagePanel = $show;
	}
	public function addBreadcrumb($text,$link) {
		$bc = new stdClass();
		$bc->text = $text;
		if ($link=="#")	$bc->link = $link;
		else $bc->link = Router::_($link);
		$this->_breadCrumb []= $bc;
	}
	public function setBreadcrumb($text,$link) {
		$bc = new stdClass();
		$bc->text = $text;
		if ($link=="#")	$bc->link = $link;
		else $bc->link = Router::_($link);
		$this->_breadCrumb = array($bc);
	}
	public function prepare() {} // For overriding
}
?>
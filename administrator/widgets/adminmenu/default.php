<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class adminmenuWidget extends Widget {
    protected $bootstrape5 = true;     //  css rules - from bootstrape 5.3
	protected $tree_level = 0;
	protected $disabled_modules_style = array();
	protected $max_levels = 5;

	public function render() {
		foreach (Portal::getInstance()->getDisabledModules() as $d_module){
			$this->disabled_modules_style[$d_module]=" style=\"color: #999999 !important;\"";
		}
		if (siteConfig::$treeDepth) $this->max_levels = (int)siteConfig::$treeDepth;
		if($this->bootstrape5){
		  $wraper_div_class=" class=\"container-fluid\" ";
		  $navbar_header=" class=\"collapse navbar-collapse\" ";
		}else{
		  $wraper_div_class=" class=\"\" ";
		  $navbar_header=" class=\"collapse navbar-collapse\" ";
		}
		$this->tree_level = 0;
		$parents_arr=preg_split('/(\,)/', 0);
		
		$menuHTML = "<div ".$wraper_div_class." id=\"".$this->getParam('menu_divId', false)."\">";
		//$menuHTML = "<div class=\"d-block d-sm-none\">";
		
		$menuHTML.= "<a class=\"navbar-brand d-sm-none\" href=\"#\">".Text::_("Navigation")."</a>";
		$menuHTML.= "<button class=\"navbar-toggler d-sm-none\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"".Text::_("Navigation swith")."\">";
 
		$menuHTML.= "<span class=\"navbar-toggler-icon d-sm-none\"></span>";
		$menuHTML.= "</button>";
		//$menuHTML = "</div>";
		
		//old - $menuHTML.= "	<div class=\"navbar-header admin-top-menu-header\"><span class=\"topmenu_label visible-xs\">".Text::_("Navigation")."</span>";
		//old -  $menuHTML.= "		<button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"collapse\" data-target=\"#admin_top_menu\"><i class=\"glyphicon glyphicon-menu-hamburger\"></i></button>";
		//old -  $menuHTML.= "	</div>";
		$menuHTML.= "	<div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent\">";
		// old - $menuHTML.= "	<div id=\"admin_top_menu\" class=\"collapse navbar-collapse menu\">";
		$menuHTML.= "		<ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">";
		// old $menuHTML.= "		<ul class=\"nav navbar-nav\">";
		

		$menu_arr = $this->getBaseMenuArray();
		Event::raise("widget.adminmenu.base_data.prepared", array(), $menu_arr);
		$this->appendCustomMenuArray($menu_arr);
		Event::raise("widget.adminmenu.custom_data.prepared", array(), $menu_arr);
		foreach($menu_arr as $m_key=>$m_arr){
			$menuHTML.= "		<li class=\"nav-item admin-menu-".$m_key." dropdown\">";
			//<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					//</a>
			
			$menuHTML.= "<a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\"  >".Text::_($m_arr["title"])." <span class=\"caret\"></span></a>";
			$menuHTML.= $this->renderSubmenuUl($m_arr);
			$menuHTML.= "		</li>";
		}
		$menuHTML.= "		</ul>";
		$menuHTML.= "	</div>";
		$menuHTML.= "</div>";
		
		$script="$(document).ready(function() {
					$('ul.dropdown-menu [data-toggle=dropdown-submenu]').on('click', function(event) {
						event.preventDefault();
						event.stopPropagation();
						if($(this).parent().hasClass('open')) {
							$(this).parent().removeClass('open');
							$(this).closest('ul').find('li').removeClass('open');
						} else {
							$(this).parent().addClass('open');
						}
					});
				});";
		Portal::getInstance()->addScriptDeclaration($script);
		return $menuHTML;
	}
	protected function getBaseMenuArray(){
			$menu_arr = array();
		/***********************************************************************************/
		$mm_name = "service";
		$menu_arr[$mm_name]=array("title"=>"Service menu", "link"=>"#", "module"=>"", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Cache manager", "link"=>"index.php?module=service&view=cachemanager", "module"=>"service", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Clear users filters", "link"=>"index.php?module=service&view=userfilter", "module"=>"service", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Check ACL objects", "link"=>"index.php?module=service&view=aclrules", "module"=>"service", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Prepare metadata fields", "link"=>"index.php?module=conf&task=prepareFields", "module"=>"conf", "acl"=>"", "childs"=>array());
		if (defined("_BARMAZ_TRANSLATE")) {
			$divider++;
			$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Check Translate module", "link"=>"index.php?module=conf&task=checkTranslateList", "module"=>"conf", "acl"=>"", "childs"=>array());
		}
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"DB Service", "link"=>"index.php?module=service&view=db", "module"=>"service", "acl"=>"", "childs"=>array());
		if(intval(Settings::getVar("restruct_version")) < Portal::getInstance()->getVersionRevision()){
			$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Database restructuring", "link"=>"index.php?module=service&&view=updater&&layout=restructure", "module"=>"service", "acl"=>"", "childs"=>array());
		} else {
			$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Check for updates", "link"=>"index.php?module=service", "module"=>"service", "acl"=>"", "childs"=>array());
		}
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Installer", "link"=>"index.php?module=installer", "module"=>"installer", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"PHP Info", "link"=>"index.php?module=conf&task=pi", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Mailer log", "link"=>"index.php?module=service&view=mailerlog", "module"=>"service", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Site map create", "link"=>"index.php?module=conf&task=createmap", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Site map show", "link"=>"index.php?module=conf&view=sitemap", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Site map manual editor", "link"=>"index.php?module=conf&view=mansitemap", "module"=>"conf", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Media manager", "link"=>"index.php?module=service&view=mediamanager", "module"=>"service", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Image processor", "link"=>"index.php?module=service&view=imageprocessor", "module"=>"service", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "config";
		$menu_arr[$mm_name]=array("title"=>"Configuration", "link"=>"#", "module"=>"", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Settings", "link"=>"index.php?module=conf&view=config", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Menu editor", "link"=>"index.php?module=menus", "module"=>"menus", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Modules", "link"=>"index.php?module=conf&view=modules", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Plugins", "link"=>"index.php?module=conf&view=plugins", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Widgets", "link"=>"index.php?module=conf&view=widgets", "module"=>"conf", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Countries, regions, cities", "link"=>"index.php?module=conf&view=cladr", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Currencies", "link"=>"index.php?module=catalog&view=currency", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Measures", "link"=>"index.php?module=catalog&view=measures", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Prepare visio", "link"=>"index.php?module=conf&task=selectVisio", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Additional fields", "link"=>"index.php?module=conf&view=dopfields_groups", "module"=>"conf", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Template zones", "link"=>"index.php?module=conf&view=tmplzones", "module"=>"conf", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Redirect links", "link"=>"index.php?module=conf&view=redirectlinks", "module"=>"conf", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "acl";
		$menu_arr[$mm_name]=array("title"=>"ACL", "link"=>"#", "module"=>"", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Users", "link"=>"index.php?module=user", "module"=>"user", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Roles", "link"=>"index.php?module=aclmgr&view=roles", "module"=>"aclmgr", "acl"=>"", "childs"=>array());
		//$divider++;
		//$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Auth providers", "link"=>"index.php?module=user&view=auth_providers", "module"=>"user", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Blacklist", "link"=>"index.php?module=user&view=blacklist", "module"=>"user", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "content";
		$menu_arr[$mm_name]=array("title"=>"Common content", "link"=>"#", "module"=>"", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Articles", "link"=>"index.php?module=article", "module"=>"article", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Blogs categories", "link"=>"index.php?module=blog", "module"=>"blog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"All blogs", "link"=>"index.php?module=blog&view=list&layout=all", "module"=>"blog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Rights map by roles", "link"=>"index.php?module=blog&view=rights&layout=rolesmap", "module"=>"blog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Rights map by users", "link"=>"index.php?module=blog&view=rights&layout=usersmap", "module"=>"blog", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "multimedia";
		$menu_arr[$mm_name]=array("title"=>"Multimedia", "link"=>"#", "module"=>"", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Image gallery", "link"=>"index.php?module=gallery", "module"=>"gallery", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Video gallery", "link"=>"index.php?module=videoset", "module"=>"videoset", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Banners categories", "link"=>"index.php?module=acrm&view=cats", "module"=>"acrm", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Banners clients", "link"=>"index.php?module=acrm&view=clients", "module"=>"acrm", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"ACRM", "link"=>"index.php?module=acrm&view=items&layout=all", "module"=>"acrm", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "forum";
		$menu_arr[$mm_name]=array("title"=>"Forum", "link"=>"#", "module"=>"forum", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Forum sections", "link"=>"index.php?module=forum", "module"=>"forum", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Rights map by roles", "link"=>"index.php?module=forum&view=rights&layout=rolesmap", "module"=>"forum", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Rights map by users", "link"=>"index.php?module=forum&view=rights&layout=usersmap", "module"=>"forum", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "comments";
		$menu_arr[$mm_name]=array("title"=>"Comments", "link"=>"#", "module"=>"comments", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Comments groups", "link"=>"index.php?module=comments", "module"=>"comments", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Rights map by roles", "link"=>"index.php?module=comments&view=rights", "module"=>"comments", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "feedback";
		$menu_arr[$mm_name]=array("title"=>"Feedback", "link"=>"#", "module"=>"feedback", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Messages from site", "link"=>"index.php?module=feedback&view=messages", "module"=>"feedback", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Polls", "link"=>"index.php?module=polls", "module"=>"polls", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		$mm_name = "catalog";
		$menu_arr[$mm_name]=array("title"=>"Catalog", "link"=>"#", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider = 0;
		$menu_arr[$mm_name]["childs"][$divider] = array();
		/***********************************************************************************/
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Goods groups and goods", "link"=>"index.php?module=catalog&view=goodsgroup", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Goods without groups", "link"=>"index.php?module=catalog&view=goods", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Price list", "link"=>"index.php?module=catalog&view=price", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Orders", "link"=>"index.php?module=catalog&view=orders", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Vendors categories", "link"=>"index.php?module=catalog&view=vendor_cats", "module"=>"catalog", "acl"=>"", "childs"=>array());
		if(catalogConfig::$multy_vendor) {
			$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Vendors links", "link"=>"index.php?module=catalog&view=users", "module"=>"catalog", "acl"=>"", "childs"=>array());
		}
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Manufacturers categories", "link"=>"index.php?module=catalog&view=manufacturer_cats", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Delivery types", "link"=>"index.php?module=catalog&view=deliverytypes", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Payment types", "link"=>"index.php?module=catalog&view=paymenttypes", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Goods options", "link"=>"index.php?module=catalog&view=options", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"List of taxes", "link"=>"index.php?module=catalog&view=taxes", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Discounts and surcharges", "link"=>"index.php?module=catalog&view=discounts", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Data exchange in 1C format", "link"=>"index.php?module=catalog&view=exchange1c", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Export catalog data as CSV", "link"=>"index.php?module=catalog&view=export", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Import catalog data as CSV", "link"=>"index.php?module=catalog&view=import", "module"=>"catalog", "acl"=>"", "childs"=>array());
		$divider++;
		$menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"Transition statistics from other sites", "link"=>"index.php?module=catalog&view=goods_stat", "module"=>"catalog", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		// $menu_arr[$mm_name]["childs"][$divider][] = array("title"=>"", "link"=>"", "module"=>"", "acl"=>"", "childs"=>array());
		/***********************************************************************************/
		return $menu_arr;
	}
	protected function renderSubmenuUl($m_arr){
		$result = "";
		if(count($m_arr["childs"])) {
			$result = "<ul class=\"dropdown-menu\">";
			$divider = 0;
			foreach($m_arr["childs"] as $ms_key=>$ms_arr){
				if($ms_key != $divider){
					$result.= "<li class=\"divider\"></li>";
					$divider = $ms_key;
				}
				foreach($ms_arr as $_key=>$_arr){
					$style=(isset($this->disabled_modules_style[$_arr["module"]]) ? $this->disabled_modules_style[$_arr["module"]] : "");
					if(count($_arr['childs'])){
						$result.= "<li".$style." class=\"dropdown-submenu\"><a".$style." class=\"dropdown-toggle\" data-toggle=\"dropdown-submenu\" href=\"#\">".Text::_($_arr["title"])."</a>";
					} else {
						if(isset($this->disabled_modules_style[$_arr["module"]])) $link = "#"; else $link=Router::_($_arr["link"]);
						
						$result.= "<li class=\"nav-item\" ".$style." ><a class=\"nav-link\" ".$style." href=\"".$link."\">".Text::_($_arr["title"])."</a>";
					}
					$result.= $this->renderSubmenuUl($_arr);
					$result.= "</li>";
				}
			}
			$result.= "</ul>";
		}
		return $result;
	}
	protected function appendCustomMenuArray(&$menu_arr){
		$sql = "SELECT am.*, COUNT(amc.mnu_id) AS mnu_childs FROM #__admin_menus AS am
				LEFT JOIN #__admin_menus AS amc ON amc.mnu_parent_id=am.mnu_id
				WHERE am.mnu_id>0
				GROUP BY am.mnu_id
				ORDER BY am.mnu_order,am.mnu_id";
		Database::getInstance()->setQuery($sql);
		$res=Database::getInstance()->loadObjectList();
		if(count($res)){
			$childs = array();
			foreach($res as $k=>$v){
				if(!isset($childs[$v->mnu_parent_id])) $childs[$v->mnu_parent_id] = array();
				$childs[$v->mnu_parent_id][] = $v;
			}
			$this->tree_level++;
			foreach($res as $k=>$v){
				if($v->mnu_parent_id == 0){
					$mm_name = "admin-menu-".$v->mnu_id;
					$menu_arr[$mm_name]=array("title"=>$v->mnu_name, "link"=>"#", "module"=>$v->mnu_module, "acl"=>"", "childs"=>array());
					$menu_arr[$mm_name]["childs"] = $this->appendCustomMenuChilds($v->mnu_id, $childs);
				}
			}
		}
	}
	protected function appendCustomMenuChilds($mnu_id, $childs){
		$menu_arr = array();
		if($this->tree_level > $this->max_levels) return $menu_arr;
		$this->tree_level++;
		if(array_key_exists($mnu_id, $childs)){
			$divider = 0;
			foreach($childs[$mnu_id] as $k=>$v){
				if($v->mnu_parent_id == $mnu_id){
					if($v->mnu_name === "-") {
						$divider++;
						continue;
					}
					$menu_arr[$divider][] = array("title"=>$v->mnu_name, "link"=>$v->mnu_link, "module"=>$v->mnu_module, "acl"=>"", "childs"=>$this->appendCustomMenuChilds($v->mnu_id, $childs));
				}
			}
		}
		$this->tree_level--;
		return $menu_arr;
	}
}
?>
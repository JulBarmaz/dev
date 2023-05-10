<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SearchMachine extends BaseObject {

	//---------- Singleton implementation ------------
	private static $_instance = null;

	private $whereInputs=array();
	private $defSorting=array();
	private $sorting=array();
	private $orderBy="ttl";
	private $keywords=array();
	private $stype="any";
	private $swhere="";
	private $page=1;
	private $link="index.php?task=search";
	private $pagesize=3;
	private $view=null;
	private $paginator=null;
	
	public static function createInstance($critFail=true) {
		if (self::$_instance == null) {
			self::$_instance = new self($critFail);
		}
	}
	public static function getInstance($critFail=true) {
		self::createInstance($critFail);
		return self::$_instance;
	}
	public function addWhereInputs($inputs){
		if (count($inputs)){
			foreach ($inputs as $fld=>$text){
				$this->whereInputs[$fld]=$text;
			}
		}
	}
	public function getWhereInputs(){
		return $this->whereInputs;
	}
	
	public function getWhere(){
		return $this->swhere;
	}

	public function getType(){
		return $this->stype;
	}

	public function getLink(){
		return $this->link;
	}
	
	public function getPageSize(){
		return $this->pagesize;
	}
	
	public function getWords(){
		return $this->keywords;
	}
	public function addSorting($arrs){
		if (count($arrs)){
			foreach ($arrs as $fld=>$text){
				$this->sorting[$fld]=$text;
			}
		}
	}
	
	public function addDefaultSorting($arrs){
		if (count($arrs)){
			foreach ($arrs as $fld=>$text){
				$this->defSorting[$fld]=$text;
			}
		}
	}
	public function getOrderBy(){
		if (in_array($this->orderBy, $this->sorting[$this->getWhere()])) return str_replace("-", " ", $this->orderBy);
		else return str_replace("-", " ", $this->defSorting[$this->getWhere()][0]);
	}
	
	public function getOrderByNR(){
		if (in_array($this->orderBy, $this->sorting[$this->getWhere()])) return $this->orderBy;
		else return $this->defSorting[$this->getWhere()][0];
	}
	
	public function buildRoute($options, $force_fronte, $absolute_link, $force_protocol) {
		$url = "";
		if(!count($this->getWhereInputs())) Event::raise("search.renderForm");
		if(isset($options["task"]) && $options["task"]=="search"){
			if( (isset($options["searchtype"]) && in_array($options["searchtype"],array("any", "all", "exact")))
				&& (isset($options["where_search"]) && array_key_exists($options["where_search"],$this->getWhereInputs()))
			) {
				$url = $options["task"]."/".$options["searchtype"]."/".$options["where_search"].".html";
				if(isset($options["kwds"])) {
					$url.= "?kwds=".$options["kwds"];
					if(isset($options["page"]) && $options["page"]>1) $url.= "&page=".$options["page"];
					if(isset($options["orderby"]) && $options["orderby"]) $url.= "&orderby=".$options["orderby"];
				}
			} else {
				$url = "search.html".(seoConfig::$useMidInMenuLinks ? "?mid=".siteConfig::$searchMenuID : "");
			}
		}
		return Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
	}
	
/*	
	public function parseRequest(){
		$this->pagesize=Request::get("rpp",siteConfig::$recordsPerPage);
		$this->stype=Request::getSafe("searchtype","any"); 
		$keywords = trim(Request::getSafe("keywords",""));
		if (!$keywords) $keywords=trim(urldecode(Request::getSafe("kwds","")));
		$kwds=explode(" ",$keywords);
		if(!count($this->getWhereInputs())) Event::raise("search.renderForm");
		if (count($kwds)){
			foreach($kwds as $kw) {
				$kw=trim($kw);		if ($kw) $this->keywords[]=$kw;
			}
		}
		$this->swhere=Request::getSafe("where_search",false);
		$this->page=Request::getInt("page",1);
		$this->orderBy=Request::getSafe("orderby","");
		$this->link="index.php?task=search&amp;searchtype=".$this->stype."&amp;where_search=".$this->swhere."&amp;kwds=".urlencode(implode(" ",$kwds));
	}
*/	
	public function parseRequest(){
		$_404=false;
		$kwds=array();
		$request_count=4;
		$keywords = trim(Request::getSafe("keywords","")); // это поле из формы
		if(!$keywords) $keywords=trim(urldecode(Request::getSafe("kwds",""))); // это параметр из строки запроса GET
		if ($keywords) $kwds=explode(" ",$keywords);
		if(Request::getMethod()=="GET"){
			if(Request::getSafe("task","")=="search"){
				if (count($_REQUEST)>2){ // task и mid это 2 
					// тут надо проверок всяких чтобы 404 если что
					if(in_array(Request::getSafe("searchtype",""), array("any", "all", "exact"))) {
						if(!count($this->getWhereInputs())) Event::raise("search.renderForm");
						if(array_key_exists(Request::getSafe("where_search",""), $this->getWhereInputs())) {
							if(count($kwds)){
								if(Request::getInt("page")) $request_count++; 
								if(Request::getInt("mid")) $request_count++; 
								if(Request::getSafe("orderby","")) $request_count++;
								//	@TODO тут бы конечно проверить еще возможные сортировки
								if (count($_REQUEST)!=$request_count) $_404=true;
							} else $_404=true;
						} else $_404=true;
					} else $_404=true;
				} elseif(count($_REQUEST)==2 && !Request::getInt("mid","")) $_404=true; 
			} else $_404=true;
		}
		if($_404) Util::redirect(Portal::getURI(1), Text::_("Page not found"), 404);
		else {
			$this->stype=Request::getSafe("searchtype","any");
			if(count($kwds)){
				foreach($kwds as $kw) {
					$kw=trim($kw); 
					if($kw) $this->keywords[]=$kw;
				}
			}
			$this->swhere=Request::getSafe("where_search",false);
			$this->page=Request::getInt("page",1);
			$this->orderBy=Request::getSafe("orderby","");
			$this->link="index.php?task=search&amp;searchtype=".$this->stype."&amp;where_search=".$this->swhere."&amp;kwds=".urlencode(implode(" ",$kwds));
		}
	}
	
	public function renderForm(){
		$module="";
		$view="";
		$task="search";
		Portal::getInstance()->setTitle(Text::_("Search in site"));
		echo "<div class=\"search-machine\">";
		echo "<h1 class=\"title\">".Text::_("Search in site")."</h1>";
		echo "<form action=\"".Router::_("index.php")."\" method=\"post\" name=\"frmSearch\">";
		echo "<div class=\"searchform row\">";
		echo "	<div class=\"col-sm-4\">".HTMLControls::renderLabelField("keywords","Search phrase",1)."</div>";
		echo "	<div class=\"col-sm-8\">".HTMLControls::renderInputText("keywords",implode(" ",$this->keywords),80)."</div>";
		echo "</div>";
		echo "<div class=\"searchform_radio row\">";
		echo "	<div class=\"col-sm-4\">".HTMLControls::renderRadio("searchtype", "searchtype_any", "any",Text::_("Any word"),"", $this->stype=="any")."</div>";
		echo "	<div class=\"col-sm-4\">".HTMLControls::renderRadio("searchtype", "searchtype_all", "all",Text::_("All words"),"", $this->stype=="all")."</div>";
		echo "	<div class=\"col-sm-4\">".HTMLControls::renderRadio("searchtype", "searchtype_exact", "exact",Text::_("Exact phrase"),"", $this->stype=="exact")."</div>";
		echo "</div>";
		$inputs=$this->getWhereInputs();
		if (count($inputs)) {
			echo "<div class=\"searchform_selector row\">";
			echo "	<div class=\"col-sm-12\">".HTMLControls::renderSelect("where_search","" , "", "", $inputs,$this->swhere,0)."</div>";
			echo "</div>";
		}
		echo "<div class=\"buttons\">";
		echo HTMLControls::renderButton("submit", $val=Text::_("Start search"),  "submit");
		echo HTMLControls::renderHiddenField("module", $module);
		echo HTMLControls::renderHiddenField("view", $view);
		echo HTMLControls::renderHiddenField("task", $task);
		echo "</div>";
		echo "</form>";
		echo "</div>";
	}
	public function renderResult() {
		if (!count($this->keywords)) return false;
		$this->view=new View("SearchMachine");
		Event::raise("search.renderResult",array(),$data);
		$filename=PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS."html".DS."search.php";
		if (count($data)) Portal::getInstance()->setTitle(Text::_("Search results").": ".implode(" ",$this->keywords));
		if (file_exists($filename)) {
			require_once($filename);
		} else {
			echo "<hr />";
			if (count($data)) {
				echo $this->renderSortPanel(); 
				foreach($data as $row){
					echo "<div class=\"search-results\">";
					echo "<div class=\"row\">";
					echo "<div class=\"searchRowTitle col-sm-12\"><h4 class=\"title\"><a rel=\"nofollow\" href=\"".$row["link"]."\" class=\"articleTitle\">".$row["ttl"]."</a></h4></div>";
					if($row["cdate"] || $row["img"]){
						echo "<div class=\"col-sm-2\"><div class=\"row\">";
						if ($row["cdate"]) {
							echo "<div class=\"searchRowDate col-xs-6 col-sm-12\">".$row["cdate"]."</div>";
						}
						if ($row["img"]) {
							echo "<div class=\"searchRowShort col-xs-6 col-sm-12\">".$row["img"]."</div>";
						}
						echo "</div></div>";
						$col_class="col-sm-10";
					} else {
						$col_class="col-sm-12";
					}
					echo "<div class=\"searchRowShort ".$col_class."\">".$row["txt"]."</div>";
					echo "</div>";
					echo "</div>";
				}
			} else { echo "<p class=\"searchResult\">".Text::_("Records not found")."</p>"; }
		}
		echo $this->view->renderPagePanel(); 
	}
	public function alterPaginator($resultsCount=0) {
		$this->paginator=new Paginator($this->view, $resultsCount, $this->pagesize);
		$this->paginator->buildPagePanel($this->link, "");
		return $this->paginator->getAppendix();
	}	
	public function renderSortPanel(){
		if (count($this->sorting)>1){
			echo "<div class=\"search-sort row\"><div class=\"col-sm-12\"><ul class=\"nav nav-pills\">";
			$activeSort=$this->getOrderByNR();
			$baselink=$this->getLink().($this->page>1 ? "&amp;page=".$this->page : "");
			foreach($this->sorting[$this->getWhere()] as $key=>$sort){
				if ($activeSort==$sort) $class="class=\"active\""; else $class="";
				echo "<li ".$class."><a href=\"".Router::_($baselink."&amp;orderby=".$sort)."\">".Text::_($sort)."</a></li>";
			}
			echo "</ul></div></div>";
		}
	}
}
?>
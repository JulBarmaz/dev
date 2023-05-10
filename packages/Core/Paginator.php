<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Paginator extends BaseObject {

	private $_view			= null;
	private $_itemCount		= 0;
	private $_pageSize		= 0;
	private $_page				= 1;
	
	public function __construct($view, $itemCount, $pageSize, $is_last_page=false) {
		$this->initObj('Paginator');
		$this->_view = $view;
		$this->_itemCount = $itemCount;
		$this->_pageSize = $pageSize;
		if ($is_last_page) $this->_page=intval(ceil($this->_itemCount / $this->_pageSize));
		else $this->_page = Request::getInt('page',1);
		$this->_view->assign('page',$this->_page);
	}

	public function getAppendix() {
		$startItem = ($this->_page - 1) * $this->_pageSize;
		return " LIMIT ".$startItem.", ".$this->_pageSize;
	}

	public function buildPagePanel($pageLink,$pageLinkTail='') {
		// Count pages
		$pageCount = intval(ceil($this->_itemCount / $this->_pageSize));
/*
		if (($pageCount * $this->_pageSize) < $this->_itemCount) {
			$pageCount++;
		}
*/
		// pagesOnPanel - non power of 2!
		$pagesOnPanel = defined("_ADMIN_MODE") ? adminConfig::$adminPagesPerPanel : siteConfig::$pagesPerPanel;
		$pageLinks = "";

		// First and last page link
		//echo $pageLink."&amp;page=1".$pageLinkTail;
		$firstPageLink = "";
		$lastPageLink = "";

		$pageRadius = ceil(($pagesOnPanel - 1) / 2);
		$startPage = $this->_page - $pageRadius;
		if ($startPage < 1) {
			$startPage = 1;
		}
		$endPage = $startPage + $pagesOnPanel - 1;
		if ($endPage > $pageCount) {
			$endPage = $pageCount;
		}

		// Place first page link if need one
		if ($startPage > 1) {
//			$firstPageLink = Router::_($pageLink."&amp;page=1".$pageLinkTail);
			$firstPageLink = Router::_($pageLink.$pageLinkTail);
		}

		$prevPageLink="";
		$nextPageLink="";
		for ($p = $startPage; $p <= $endPage; $p++) {
			if ($p == $this->_page) {
				$pageLinks .= "<li class=\"page-item active\"><span class=\"active-navigator\">".$p."</span></li>";
				if ($p>1) $prevPageLink = Router::_($pageLink.($p>2 ? "&amp;page=".($p-1):"").$pageLinkTail); 
				if ($p<$endPage) $nextPageLink = Router::_($pageLink.($p>0 ? "&amp;page=".($p+1):"").$pageLinkTail);
			} else {
				$currentPageLink = Router::_($pageLink.($p<>1 ? "&amp;page=".$p : "").$pageLinkTail);
				$pageLinks .= "<li class=\"page-item\"><a class=\"pageLink\" href=\"".$currentPageLink."\">".$p."</a></li>";
			}
		}

		// Place last page link if need one
		if ($endPage < $pageCount) {
			$lastPageLink = Router::_($pageLink.($pageCount>1 ? "&amp;page=".$pageCount : "").$pageLinkTail);
		}

		if ($pageCount > 1) {
			$this->_view->showPagePanel();
		}
		$this->_view->assign('pageLinks', $pageLinks);
		$this->_view->assign('firstPageLink', $firstPageLink);
		$this->_view->assign('firstPageJS', "");
		$this->_view->assign('lastPageLink', $lastPageLink);
		$this->_view->assign('lastPageJS', "");
		$this->_view->assign('prevPageLink', $prevPageLink);
		$this->_view->assign('prevPageJS', "");
		$this->_view->assign('nextPageLink', $nextPageLink);
		$this->_view->assign('nextPageJS', "");
		$fromRecord = $this->_pageSize*($this->_page-1)+1;
		$toRecord 	= $this->_pageSize*($this->_page);
		if ($this->_itemCount<$toRecord) $toRecord=$this->_itemCount;
		$this->_view->assign('pageRange',$fromRecord."-".$toRecord);
		$this->_view->assign('recordsTotal',$this->_itemCount);
	}

}

?>
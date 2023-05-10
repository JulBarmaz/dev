<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="navigator">
	<div class="navigator_pages"><ul class="pagination"><?php 
	if ($this->firstPageLink) echo "<li class=\"page-item\"><a class=\"pageLink firstPageLink\" href=\"".$this->firstPageLink."\">".Text::_("First page")."</a></li>"; 
//	else echo "<li class=\"page-item\"><span class=\"pageLink firstPageLink\">".Text::_("First page")."</span></li>"; 	
	if ($this->prevPageLink) echo "<li class=\"page-item\"><a class=\"pageLink prevPageLink\" href=\"".$this->prevPageLink."\">".Text::_("Prev.page")."</a></li>"; 	
	else echo "<li class=\"page-item\"><span class=\"pageLink prevPageLink\">".Text::_("Prev.page")."</span></li>";
	echo $this->pageLinks;
	if ($this->nextPageLink) echo "<li class=\"page-item\"><a class=\"pageLink nextPageLink\" href=\"".$this->nextPageLink."\">".Text::_("Next page")."</a></li>"; 
	else echo "<li class=\"page-item\"><span class=\"pageLink nextPageLink\">".Text::_("Next page")."</span></li>";
	if ($this->lastPageLink) echo "<li class=\"page-item\"><a class=\"pageLink lastPageLink\" href=\"".$this->lastPageLink."\">".Text::_("Last page")."</a></li>"; 
//	else echo "<li class=\"page-item\"><span class=\"pageLink lastPageLink\">".Text::_("Last page")."</span></li>";
	?></ul></div>
	<div class="navigator_records"><?php 
	echo Text::_("Records")."&nbsp;".$this->pageRange."&nbsp;".Text::_("of")."&nbsp;".$this->recordsTotal;
	?>
	</div>
</div>
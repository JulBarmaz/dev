<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

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
?>

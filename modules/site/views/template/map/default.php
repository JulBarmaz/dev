<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<h1 class="title"><?php echo Text::_("Site map"); ?></h1>
<?php 
if (count($this->links)>0) {
	echo '<div class="site-map">';
	echo '<div class="row">';
	foreach($this->links as $module=>$data) {
		if ($data["html"]) {
			echo '<div class="col-sm-6"><div class="map_block map_'.$module.'">';
			echo '<h2 class="title">';
			if($data["title_link"]) echo '<a href="'.Router::_("index.php?module=".$module).'">';
			echo Text::_($module);
			if($data["title_link"]) echo '</a>';
			echo '</h2>';
			echo $data["html"];
			echo '</div></div>';
		}
	}
	echo '</div>';
	echo '</div>';
}	
?>
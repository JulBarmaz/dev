<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Export file");?></h4>
	<div class="row buttons"><div class="col-md-12">
		<?php 
		if($this->res){
			echo "<a class=\"btn btn-info\" href=\"".$this->res."\">".Text::_("Download file")."</a>";
		} else echo Text::_("Error");
		?>
	</div></div>
</div></div></div></div>
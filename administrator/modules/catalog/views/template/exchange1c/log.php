<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager catalog-exchange1c-log">
	<h4 class="title"><?php echo Text::_("Data exchange in 1C format");?>. <?php echo Text::_("Report");?>.</h4>
	<div id="modify-wrapper">
		<div class="row"><div class="col-sm-12">
			<?php echo str_replace(str_repeat(" ", 2), str_repeat("&nbsp;", 2),Text::toHtml($this->log)); ?>
		</div></div>
	</div>
</div></div></div></div>

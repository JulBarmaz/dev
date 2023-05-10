<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

//$spr_tmpl_overrided= 1;
?>
<h1 class="title"><?php echo Text::_("Vendor info"); ?></h1>
<?php if(is_object($row)){ ?>
	<div class="row buttons-top">
		<div class="col-sm-12">
			<a class="linkButton btn btn-info" onclick="setCustomGoodsFilter('vendor',<?php echo $psid; ?>,'<?php echo Router::_("index.php?module=catalog");?>');"><?php echo Text::_("Show goods by vendor"); ?></a>
		</div>
	</div>
<?php } else {
	echo "<div class=\"spravInfo row\"><div class=\"col-xs-12\">".Text::_("Does not contain the data")."</div></div>";
}
?>
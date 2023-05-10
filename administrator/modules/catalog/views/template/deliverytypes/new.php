<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Select delivery type template"); ?></h4>
	<form id="someTypeSelector" action="index.php" method="post">
		<div class="row"><div class="col-sm-12">
			<?php echo $this->selector; ?>
		</div></div>
		<div class="buttons">
			<input type="hidden" id="task" value="SaveNewDTS" name="task" />
			<input type="hidden" id="module" value="catalog" name="module" />
			<input type="hidden" id="view" value="deliverytypes" name="view" />
			<input type="hidden" id="layout" value="" name="modify" />
			<input type="button" class="commonButton btn btn-info" onclick="javascript:history.back(); ; return true" value="<?php echo Text::_("Close"); ?>" name="cancel" />
			<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_("Next"); ?>" name="save" />
		</div>
	</form>
</div></div></div></div>
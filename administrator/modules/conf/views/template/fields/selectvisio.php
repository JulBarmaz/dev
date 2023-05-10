<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="conf-manager rounded-pan rounded-pan-mini">
	<h4 class="title">
		<?php echo Text::_("Prepare visio"); ?>
		<!-- 
		<input type="image" src="<?php echo Portal::getInstance()->getTemplateUri(); ?>images/refresh.png" onclick="javascript:document.location.href='index.php?module=conf&amp;task=prepareFields'" name="prepare" title="<?php echo Text::_("Prepare metadata fields"); ?>" />
		 -->
	</h4>
	<div id="selectVisio">
		<form method="post" action="index.php">
			<div class="prepare-visio">
				<div class="row"><div class="col-md-6"><?php echo HTMLControls::renderLabelField("m_admin_side", Text::_("Type of side").":"); ?></div>
				<div class="col-md-6"><select class="singleSelect form-control" onchange="setListVisioModules();" id="m_admin_side" name="m_admin_side"><option selected="selected" value="-1"><?php echo Text::_("Select view side"); ?></option><option value="1"><?php echo Text::_("Admin side"); ?></option><option value="0"><?php echo Text::_("Front side"); ?></option></select></div></div>
				<div class="row"><div class="col-md-6"><?php echo HTMLControls::renderLabelField("m_module", Text::_("List of modules").":"); ?></div>
				<div class="col-md-6"><select class="singleSelect form-control" onchange="setListVisioViews();" id="m_module" name="m_module"><option><?php echo Text::_("Select module"); ?></option></select></div></div>
				<div class="row"><div class="col-md-6"><?php echo HTMLControls::renderLabelField("m_view", Text::_("List of views").":"); ?></div>
				<div class="col-md-6"><select class="singleSelect form-control" onchange="setListVisioLayout();" id="m_view" name="m_view"><option><?php echo Text::_("Select view"); ?></option></select></div></div>
				<div class="row"><div class="col-md-6"><?php echo HTMLControls::renderLabelField("m_layout", Text::_("List of layout").":"); ?></div>
				<div class="col-md-6"><select class="singleSelect form-control" id="m_layout" name="m_layout"><option><?php echo Text::_("Select layout"); ?></option></select></div></div>
				<div class="buttons">
					<input class="commonButton btn btn-info" type="submit" name="save" value="<?php echo Text::_("Edit"); ?>" />
					<input type="hidden" name="module" value="conf" />
					<input type="hidden" name="task" value="prepareVisioForm" />
					<input type="hidden" name="view" value="fields" />
				</div>
			</div>
		</form>
	</div>
</div></div></div></div>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="acl-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_('Blog rights')." (".Text::_('Blog')."&nbsp;".$this->blogName.")&nbsp;".Text::_('for')."&nbsp;".$this->subjectName; ?></h4>
	<form action="index.php" method="post">
		<input type="hidden" name="module" value="blog" />
		<input type="hidden" name="task" value="updateRights" />
		<input type="hidden" name="subject" value="<?php echo $this->subject; ?>" />
		<input type="hidden" name="blogId" value="<?php echo $this->blogId; ?>" />
		<?php if ($this->subject == 'role') { ?>
		<input type="hidden" name="roleId" value="<?php echo $this->roleId; ?>" />
		<?php } else { ?>
		<input type="hidden" name="userId" value="<?php echo $this->userId; ?>" />
		<?php }?>
		<?php
		foreach ($this->actions as $action) {
			$checked = ""; $vl = 0;
			if (isset($this->rules[$action])) {
				if (intval($this->rules[$action]->flag) == 1) {
					$checked = " checked=\"checked\"";
					$vl = 1;
				}
			}
		?>
			<div class="row">
				<div class="col-xs-8">
					<input type="hidden" name="oldAccess[<?php echo $action; ?>]" value="<?php echo $vl; ?>" />
					<?php echo HTMLControls::renderLabelField("access_".$action, Text::_("blog_".$action)); ?>
				</div>
				<div class="col-xs-4">
					<input type="checkbox" id="access_<?php echo $action; ?>" name="access[<?php echo $action; ?>]"<?php echo $checked; ?> />
				</div>
			</div>
		<?php } ?>
		<div class="buttons">
			<input type="submit" class="commonButtonbtn btn btn-info" name="save" value="<?php echo Text::_('Save'); ?>" />
			<input type="submit" class="commonButton btn btn-info" name="apply" value="<?php echo Text::_('Apply'); ?>" />
			<input type="button" class="commonButton btn btn-info" onclick="javascript:document.location.href='<?php echo Router::_("index.php?module=blog&view=rights&blogid=".$this->blogId); ?>'; return true;" value="<?php echo Text::_('Cancel'); ?>" name="cancel" />
		</div>
	</form>
</div></div></div></div>
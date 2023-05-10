<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<form action="<?php echo Router::_("index.php"); ?>" method="post">
	<input type="hidden" name="module" value="mail" />
	<input type="hidden" name="task" value="send" />
	<div id="mailEditorHeader">
		<?php echo HTMLControls::renderLabelField("recvUser",Text::_('Reciever name').":"); ?>
		<input class="commonEdit form-control" size="30" type="text" id="recvUser" name="recvUser" value="<?php echo $this->recvNickname; ?>" />
		<?php echo HTMLControls::renderLabelField("letterTheme",Text::_('Theme').":"); ?>
		<input class="commonEdit form-control" size="100" type="text" id="letterTheme" name="letterTheme" value="<?php echo $this->theme; ?>" />
	</div>
	<div id="mailEditorData">
		<?php Event::raise("bbcode.editor",array("element_id"=>"letterText")); ?>
		<?php echo HTMLControls::renderBBCodeEditor('letterText','',$this->letterText); ?>
	</div>
	<div id="mailEditorCaptcha">
		<?php echo Event::raise("captcha.renderForm",array("module"=>"mail"))?>
	</div>
	<div id="mailFooter">
		<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Send'); ?>" />
	</div>
</form>
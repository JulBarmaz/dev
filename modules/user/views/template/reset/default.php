<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div id="resetForm">
	<form id="regForm" action="<?php echo Router::_("index.php"); ?>" method="post">
		<input type="hidden" name="module" value="user" />
		<input type="hidden" name="code" value="<?php echo $this->vcode;?>" />
		<input type="hidden" name="task" value="resetPassword" />
<?php if (!User::getInstance()->isLoggedIn()) { ?>
		<?php echo HTMLControls::renderLabelField("rLogin", Text::_('Login name')); ?>
		<div class="registrationFormField">
		<input class="commonEdit required form-control" type="text" name="rLogin" id="rLogin" value="" />
		</div>
<?php } ?>
		<?php echo HTMLControls::renderLabelField("rPassword", Text::_('New password')); ?>
		<div class="registrationFormField">
		<input class="commonEdit required form-control" type="password" name="rPassword" name="rPassword" value="" />
		</div>

		<?php echo HTMLControls::renderLabelField("rPasswordRetype", Text::_('Confirm password')); ?>
		<div class="registrationFormField">
		<input class="commonEdit required form-control" type="password" name="rPasswordRetype" id="rPasswordRetype" value="" />
		</div>
<?php if (!User::getInstance()->isLoggedIn()) { ?>
		<div class="row-for-captcha"><?php echo Event::raise("captcha.renderForm",array("module"=>"user"))?></div>
<?php } ?>
		<div id="mySettingsFooter" class="buttons">
			<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Change password'); ?>" />
		</div>
	</form>
</div>

<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$js="$(document).ready(function() {
	$('#email').bind('blur', function() {checkFieldUnique('user',this,".User::getInstance()->getID().");});
});
";
Portal::getInstance()->addScriptDeclaration($js); 
?>
<div id="resetForm">
	<form id="regForm" action="<?php echo Router::_("index.php"); ?>" method="post">
		<input type="hidden" name="module" value="user" />
		<input type="hidden" name="task" value="resetEmail" />
		<?php if ($this->email) $button_text="Change e-mail"; else $button_text="Save e-mail"; ?>
		<?php if (!$this->email) {?><h2><?php echo Text::_('Finishing registration')?>.</h2><?php }?>
		<h4><?php echo Text::_('For usage all possibilities of our site enter e-mail')?>.</h4>
		<?php if ($this->email) { ?><div class="registrationFormLabel old_email"><?php echo Text::_('Old e-mail')?>: <?php echo User::getInstance()->getEmail(); ?></div><?php }?>
		<?php echo  HTMLControls::renderLabelField("email", Text::_('New e-mail')); ?>
		<div class="registrationFormField"><input class="commonEdit required email form-control" required type="text" name="rEmail" id="email" value="" /></div>
		<div id="mySettingsFooter" class="buttons"><input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_($button_text); ?>" /></div>
	</form>
</div>

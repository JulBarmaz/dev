<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$js="$(document).ready(function() {
	$('#login').bind('blur', function() {checkFieldUnique('user',this,0);});
	$('#nickname').bind('blur', function() {checkFieldUnique('user',this,0);});
	$('#email').bind('blur', function() {checkFieldUnique('user',this,0);});
});
";
Portal::getInstance()->addScriptDeclaration($js); 
$rules_art=Module::getHelper('article','article')->getArticle(intval(siteConfig::$site_rules_article));
$pp_art=Module::getHelper('article','article')->getArticle(intval(siteConfig::$privacy_policy_article));
if(is_object($rules_art) && is_object($pp_art)) { ?>
<div id="registrationForm">
	<form id="regForm" action="<?php echo Router::_("index.php"); ?>" method="post">
		<input type="hidden" name="module" value="user" />
		<input type="hidden" name="return_url" value="<?php echo $this->return_url; ?>" />
		<input type="hidden" name="task" value="registerUser" />
		<h1 class="title"><?php echo Text::_("New user registration")?></h1>
		<b><?php echo Text::_('If you are already registered'); ?>, <a href="<?php echo Router::_("index.php?module=user&view=login"); ?>"  rel="nofollow" class="relpopup"><?php echo Text::_('then sign in'); ?>.</a></b>
		<br /><br />
		<?php if (backofficeConfig::$regConfirmation==1) { ?>
			<b><?php echo Text::_('If you do not recieved confirmation letter'); ?>, <a href="<?php echo Router::_("index.php?module=user&view=confirm"); ?>"  rel="nofollow" class="relpopup"><?php echo Text::_('click here'); ?>.</a></b><br /><br />
		<?php } ?>
		<?php echo HTMLControls::renderLabelField("nickname", Text::_('Nickname')); ?>
		<div class="registrationFormField">
		<input class="commonEdit required form-control" required type="text" id="nickname" name="rNickname" value="" />
		</div>
<?php if(!backofficeConfig::$allowEmailLogin) { ?>
		<?php echo HTMLControls::renderLabelField("login", Text::_('Login name')); ?>
		<div class="registrationFormField">
			<input class="commonEdit required form-control" required type="text" id="login" name="rLogin" value="" />
		</div>
<?php } ?>
		<?php echo HTMLControls::renderLabelField("email", Text::_('E-mail')); ?>
		<div class="registrationFormField">
			<input class="commonEdit required email form-control" required type="text" id="email" name="rEmail" value="" />
		</div>
		
		<?php echo HTMLControls::renderLabelField("rPassword", Text::_('Password')); ?>
		<div class="registrationFormField">
			<input class="commonEdit required form-control" required type="password" name="rPassword" id="rPassword" value="" />
		</div>

		<?php echo HTMLControls::renderLabelField("rPasswordRetype", Text::_('Confirm password')); ?>
		<div class="registrationFormField">
			<input class="commonEdit required form-control" type="password" required name="rPasswordRetype" id="rPasswordRetype" value="" />
		</div>
<?php if (siteConfig::$use_referral_system) {?>		
	<?php if (isset($_COOKIE['referral'])) { ?>
		<input type="hidden" name="codeassign" value="<?php echo $_COOKIE['referral']; ?>" />
		<label><?php echo Text::_('You were invited by'); ?>&nbsp;<?php echo User::getNicknameByAffCode($_COOKIE['referral']); ?></label><br />
	<?php } else {?>
		<?php echo HTMLControls::renderLabelField("codeassign", Text::_('assign code')); ?>
		<div class="registrationFormField">
			<input class="commonEdit form-control" type="text" name="codeassign" id="codeassign" value="" />
		</div>
	<?php } ?>
<?php } ?>
<?php 
/* privacy policy start */
echo"<div class=\"privacy_policy_block row\"><div class=\"col-md-12\">";
echo "<input class=\"commonEdit required\" type=\"checkbox\" id=\"privacy_policy_agree\" name=\"privacy_policy_agree\" value=\"1\" required=\"required\" />"
	." ".Text::_('I have read and agreed with')
	." <a rel=\"nofollow\" class=\"relpopuptext\" href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$pp_art->a_id."&amp;alias=".$pp_art->a_alias."&amp;notmpl=1")."\">".Text::_('privacy policy')."</a>";
echo"</div></div>";
/* privacy policy end */

echo"<div class=\"site_policy_block row\"><div class=\"col-md-12\">";
echo "<input class=\"commonEdit required\" type=\"checkbox\" id=\"agree\" name=\"rAgree\" value=\"1\" required />"
	." ".Text::_('I have read and agreed with')
	." <a rel=\"nofollow\" class=\"relpopuptext\" href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$rules_art->a_id."&amp;alias=".$rules_art->a_alias."&amp;notmpl=1")."\">".Text::_('site rules')."</a>";
echo"</div></div>";
		
?>
		<?php echo Event::raise("register.renderForm",array("module"=>"user"))?>
		<div class="row-for-captcha"><?php echo Event::raise("captcha.renderForm",array("module"=>"user"))?></div>
		<div id="mySettingsFooter" class="buttons">
			<input id="apply" type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Register'); ?>" />
		</div>
	</form>
</div>
<?php } else { ?>
<?php echo Text::_('Registration disabled'); ?>
<?php }?>

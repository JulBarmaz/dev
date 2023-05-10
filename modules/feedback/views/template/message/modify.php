<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$spr_tmpl_overrided=1;
?>
<div class="content">
<?php if (is_object($this->article)) { ?>
<h1 class="title"><?php echo $this->article->a_title; ?></h1>
<div class="contactsArticleText"><?php 
	Event::raise('content.prepare', array(), $this->article->a_text);
	echo $this->article->a_text; 
?></div>
<?php } ?>
<?php if (soConfig::$showOnFeedbackPage) { ?>
<div class="contact_info row">
	<?php if (is_object($this->article)) { ?>
		<h2 class="contact_info col-md-12"><?php echo Text::_("Contact information"); ?></h2>
	<?php } else { ?>
		<h1 class="contact_info col-md-12"><?php echo Text::_("Contact information"); ?></h1>
	<?php }?>
	<div itemscope itemtype="http://schema.org/Organization" class="col-md-12">
		<table class="contact_info table table-bordered table-hover table-condensed sprav-table">
			<?php 
			if (soConfig::$fullFirmName) echo "<tr><th>".Text::_("Firm name").":</th><td><span itemprop=\"name\">".soConfig::$fullFirmName."</span></td></tr>";
			if (soConfig::$OGRN) echo "<tr><th>".Text::_("OGRN").":</th><td><span itemprop=\"name\">".soConfig::$OGRN."</span></td></tr>";
			if (soConfig::$INN) echo "<tr><th>".Text::_("INN").":</th><td><span itemprop=\"name\">".soConfig::$INN."</span></td></tr>";
			if (soConfig::$KPP) echo "<tr><th>".Text::_("KPP").":</th><td><span itemprop=\"name\">".soConfig::$KPP."</span></td></tr>";
			if (soConfig::$Address) {
				echo "<tr><th>".Text::_("Address").":</th><td><span itemprop=\"address\">";
				echo soConfig::$Address;
				if (soConfig::$Addressadd) echo ", ".soConfig::$Addressadd;
				echo "</span></td></tr>";
			}
			if (soConfig::$Timework) echo "<tr><th>".Text::_("Timework").":</th><td><span itemprop=\"address\">".soConfig::$Timework."</span></td></tr>";
			if (soConfig::$Phone) echo "<tr><th>".Text::_("Phone").":</th><td><span itemprop=\"telephone\">".soConfig::$Phone."</span></td></tr>";
			if (soConfig::$Phone2) echo "<tr><th>".Text::_("Phone").":</th><td><span itemprop=\"telephone\">".soConfig::$Phone2."</span></td></tr>";
			if (soConfig::$Fax) echo "<tr><th>".Text::_("Fax").":</th><td><span itemprop=\"faxNumber\">".soConfig::$Fax."</span></td></tr>";
			if (soConfig::$siteEmail) echo "<tr><th>".Text::_("Contact e-mail").":</th><td><span itemprop=\"email\">".HTMLControls::hideEmail(soConfig::$siteEmail,"mymylo")."</span></td></tr>";
			if (soConfig::$contactName) echo "<tr><th>".Text::_("Contact name").":</th><td>".soConfig::$contactName."</td></tr>";
			if (soConfig::$Viber) echo "<tr><th class=\"social-label\">".Text::_("Viber").":</th><td>".soConfig::$Viber."</td></tr>";
			if (soConfig::$WhatsApp) echo "<tr><th class=\"social-label\">".Text::_("WhatsApp").":</th><td>".soConfig::$WhatsApp."</td></tr>";
			if (soConfig::$Telegram) echo "<tr><th class=\"social-label\">".Text::_("Telegram").":</th><td>".soConfig::$Telegram."</td></tr>";
			if (soConfig::$Skype) echo "<tr><th class=\"social-label\">".Text::_("Skype").":</th><td>".soConfig::$Skype."</td></tr>";
			if (soConfig::$ICQ) echo "<tr><th class=\"social-label\">".Text::_("ICQ").":</th><td>".soConfig::$ICQ."</td></tr>";
			if (soConfig::$Jabber) echo "<tr><th class=\"social-label\">".Text::_("Jabber").":</th><td>".HTMLControls::hideEmail(soConfig::$Jabber,"",false)."</td></tr>";
			?>
		</table>
	</div>
</div>
<?php } 
Debugger::getInstance()->milestone('Before form StartLayout');
$frm->startLayout();
if (is_object($this->article) || soConfig::$showOnFeedbackPage) {
	echo "<h2 class=\"contact_info contact_form\">".Text::_("You may write us from here")."</h2>";
} else {
	echo "<h1 class=\"contact_info contact_form\">".Text::_("You may write us from here")."</h1>";
}
echo "<div id=\"feedbackSender\" class=\"feedbackSender\">";
foreach ($input_type as $index=>$value)	{
	if ($meta->input_view[$index]==0 && $value!="hidden") continue;
	if (($meta->field[$index]=="f_sender" || $meta->field[$index]=="f_mail") && $this->authorized)  continue;
	switch ($value)	{
		case "hidden": continue 2; 
		break;
		default:
			echo "<div id=\"row_for_".$meta->field[$index]."\" class=\"row row-label\"><div class=\"col-md-12\">";
			$frm->renderLabelFor($meta->field[$index]);
			echo ":&nbsp;";
			$frm->renderBalloonFor($meta->field[$index],false);
			echo "</div></div><div class=\"row\"><div class=\"col-md-12\">";
			$frm->renderInputPart($meta->field[$index]);
			echo "</div></div>";
		break;
	}
}

/* privacy policy start */
echo"<div class=\"privacy_policy_block row\"><div class=\"col-md-12\">";
$pp_art=Module::getHelper('article','article')->getArticle(intval(siteConfig::$privacy_policy_article));
echo "<input class=\"commonEdit required\" type=\"checkbox\" id=\"privacy_policy_agree\" name=\"privacy_policy_agree\" value=\"1\" required=\"required\" />"
	."&nbsp;".Text::_('I have read and agreed with')
	." <a rel=\"nofollow\" class=\"relpopuptext\" href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$pp_art->a_id."&amp;alias=".$pp_art->a_alias."&amp;notmpl=1")."\">".Text::_('privacy policy')."</a>";
echo"</div></div>";
/* privacy policy end */

if(!$this->disableCaptcha) {
	echo"<div class=\"row row-for-captcha\"><div class=\"col-md-12\">";
	echo Event::raise("captcha.renderForm",array("module"=>"feedback"));
	echo"</div></div>";
}
echo"<div class=\"buttons row\"><div class=\"col-md-12\">";
echo"<input type=\"submit\" class=\"commonButton btn btn-info\" name=\"apply\" value=\"".Text::_("Send")."\" id=\"apply\">";
echo"</div></div>";
echo "</div>";
Debugger::getInstance()->milestone('After form DisplayOutput');
$frm->endLayout();
?>
</div>

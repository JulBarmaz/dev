<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"moduleShortcuts\">";
echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"answerLetter\" href=\"".Router::_("index.php?module=mail&amp;view=write&amp;recvuid=".$this->letter->l_sender_id."&amp;theme=".urlencode("Re: ".$this->letter->l_theme))."\">".Text::_('Answer')."</a>";
echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"deleteLetter\" onclick=\"javascript: if (confirm('".Text::_("Delete letter")."?')) return true; else return false;\" href=\"".Router::_("index.php?module=mail&amp;task=delete&amp;inbox=".$this->inbox."&amp;letterid=".$this->letter->l_id)."\">".Text::_('Delete')."</a>";
echo "</div>";
?>
<div class="letterFullBlock">
	<div class="letterTheme"><h3><?php echo $this->letter->l_theme; ?></h3></div>
	<div class="letterProps">
		<div class="letterProp"><?php echo HTMLControls::renderLabelField("", $this->userType.":"); ?><?php echo $this->user; ?></div>
		<div class="letterProp"><?php echo HTMLControls::renderLabelField("",Text::_('Date').":"); ?><?php echo $this->letter->l_date; ?></div>
	</div>
	<div class="letterText"><?php echo $this->letter->l_text; ?></div>
</div>
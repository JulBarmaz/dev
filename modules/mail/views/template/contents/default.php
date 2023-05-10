<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"moduleShortcuts\">";
echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"newLetter\" href=\"".Router::_("index.php?module=mail&amp;view=write")."\">".Text::_('New letter')."</a>";
if ($this->mode == 'all') {
	echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"unreadletters\" href=\"".Router::_("index.php?module=mail&amp;view=contents&amp;mode=unread&amp;inbox=".$this->inbox)."\">".Text::_('Unread')."</a>";
}	else {
	echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"allLetters\" href=\"".Router::_("index.php?module=mail&amp;view=contents&amp;inbox=".$this->inbox)."\">".Text::_('All')."</a>";
}
echo "</div>";
?>
<div id="folderSelector">
<form action="<?php echo Router::_("index.php"); ?>" method="post">
	<input type="hidden" name="option" value="module" />
	<input type="hidden" name="module" value="mail" />
	<input type="hidden" name="view" value="contents" />
	<?php echo HTMLControls::renderSelect("inbox", false, false, false, $this->boxes,$this->inbox, 0, "form.submit()", 0, "commonSelect"); ?>
</form>
</div>
<?php
if(count($this->letters)){
	foreach ($this->letters as $letter) {	
		echo "<div class=\"letterBlock\" id=\"letterBlock_".$letter->l_id."\">";
		$ltrLink = Router::_("index.php?module=mail&amp;view=read&amp;ltrid=".$letter->l_id);
		echo "<div class=\"letterTheme\"><h3><a class=\"letterTheme\" href=\"".$ltrLink."\">".$letter->l_theme."</a><h3></div>";
		echo "<div class=\"letterData\">";
		if ($this->inbox == true) {
			$userLink = "<a href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$letter->l_sender_id)."\">".$letter->sender."</a>";
			echo "<div class=\"letterProp\">".HTMLControls::renderLabelField("",Text::_('Sender').":").$userLink."</div>";
		}
		else {
			$userLink = "<a href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$letter->l_reciever_id)."\">".$letter->reciever."</a>";
			echo "<div class=\"letterProp\">".HTMLControls::renderLabelField("",Text::_('Reciever').":").$userLink."</div>";
		}
		echo "<div class=\"letterProp\">".HTMLControls::renderLabelField("",Text::_('Date').":").$letter->l_date."</div>";
		$status = "<span class=\"error\">".HTMLControls::renderLabelField("",Text::_('Status unread'))."</span>";
		if ($letter->l_read == '1') $status = Text::_('Status read');
		echo "<div class=\"letterProp\">".HTMLControls::renderLabelField("",Text::_('Status').":").$status."</div>";
		echo "</div>";
		echo "<div class=\"letterFooterLinks\">";
		echo "<a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=mail&amp;task=delete&amp;inbox=".intval($this->inbox)."&amp;letterid=".$letter->l_id)."\">".Text::_('Delete')."</a>";
		echo "</div>";
		echo "</div>";
	}
} else {
	echo "<div class=\"letterBlock\"><b>".Text::_('Letters are absent')."</b></div>";
}
?>

<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->errMessage) { echo "<h4 class=\"invalid\">".$this->errMessage."</h4>"; }
if ($this->section) { // выводим информацию о родительском форуме
	echo "<h1 class=\"title\">".$this->section->f_name."</h1>";
	//echo "<div class=\"main_forum_body\">".$this->section->f_description."</div>";
}
if($this->premoderated) echo "<p class=\"red_warning\">".Text::_("The message will be published after consideration by moderator")."</p>";
?>
<form action="<?php echo Router::_("index.php"); ?>" method="post" id="ThemeForm">
	<input type="hidden" name="module" value="forum" />
	<input type="hidden" name="task" value="saveTheme" />
	<input type="hidden" name="psid" value="<?php echo $this->section->f_id; ?>" />
	<input type="hidden" name="tid" value="<?php echo $this->theme->t_id; ?>" />
	<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
	<div id="forumTheme" class="row"><div class="col-md-12">
		<?php
		echo HTMLControls::renderLabelField("themeTitle", Text::_('Theme'));
		echo HTMLControls::renderInputText("themeTitle",$this->theme->t_theme, 100, 100, "themeTitle", "commonEdit");
		?>
	</div></div>
	<div class="themeText row"><div class="col-md-12"><?php 
		echo HTMLControls::renderLabelField("themeText", Text::_('Text'));
		Event::raise("bbcode.editor",array("element_id"=>"themeText"));
		echo HTMLControls::renderBBCodeEditor("themeText","themeText",$this->theme->t_text,80,15,"editorArea required"); 
	?></div></div>
	<?php 
		if($this->canModerate){
			echo "<div class=\"themeRow row\"><div class=\"col-md-12\">";
			echo HTMLControls::renderCheckbox("t_fixed",$this->theme->t_fixed,1,"","checkbox").HTMLControls::renderLabelField("t_fixed", Text::_('Fixed theme'));
			echo "</div></div>";
		}
		echo "<div class=\"themeRow row\"><div class=\"col-md-12\">";
		echo HTMLControls::renderCheckbox("t_closed",$this->theme->t_closed,1,"","checkbox").HTMLControls::renderLabelField("t_closed", Text::_('Closed theme'));
		echo "</div></div>";
		if(!$this->theme->t_id || ($this->theme->t_author_id && User::getInstance()->getID()==$this->theme->t_author_id)) {
			echo "<div class=\"themeRow row\"><div class=\"col-md-12\">";
			echo HTMLControls::renderCheckbox("t_subscribe",$this->userSubscribed,1,"","checkbox").HTMLControls::renderLabelField("t_subscribe", Text::_('Subscribe to theme'));
			echo "</div></div>";
		}
	?>
	<div class="postTagsEditor row" id="postTagsBlock"><div class="col-md-12"><?php 
		echo HTMLControls::renderLabelField("themeTags", Text::_('Tags')." (".Text::_('comma separated').")");
		echo HTMLControls::renderInputText("themeTags",$this->theme->t_tags, 80, 80, "themeTags", "commonEdit")
		?>
	</div></div>
	<div id="forumCaptcha" class="row"><div class="col-md-12">
		<?php if(!$this->disableCaptcha) echo Event::raise("captcha.renderForm",array("module"=>"forum"))?>
	</div></div>
	<div id="themeFooter" class="buttons">
		<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Send'); ?>" />
	</div>
	
</form>
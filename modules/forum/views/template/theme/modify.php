<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"forumPost\">";
echo "<h3 class=\"title\">".$this->theme->t_theme."</h3>";
Event::raise("bbcode.parse",array(),$this->theme->t_text);
echo "<p class=\"postMessage\">".$this->theme->t_text."</p>";
if($this->premoderated) echo "<p class=\"red_warning\">".Text::_("The message will be published after consideration by moderator")."</p>";
echo "</div>";
if ($this->errMessage) {	echo "<h4 class=\"invalid\">".$this->errMessage."</h4>"; }
?>
<form action="<?php echo Router::_("index.php"); ?>" method="post" id="PostForm">
	<input type="hidden" name="module" value="forum" />
	<input type="hidden" name="task" value="savePost" />
	<input type="hidden" name="psid" value="<?php echo $this->theme->t_id; ?>" />
	<input type="hidden" name="pid" value="<?php echo $this->post->p_id; ?>" />
	<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
	<div id="forumTheme" class="row"><div class="col-md-12">
		<?php echo HTMLControls::renderLabelField("postTitle", Text::_('Theme')); ?>:
		<input size="100" type="text" class="commonEdit form-control required" id="postTitle" name="postTitle" value="<?php echo $this->post->p_theme; ?>" />
	</div></div>
	<div class="themeText row"><div class="col-md-12">
		<?php echo HTMLControls::renderLabelField("postText", Text::_('Text')); ?>:
		<?php 
			Event::raise("bbcode.editor",array("element_id"=>"postText"));
			echo HTMLControls::renderBBCodeEditor("postText","postText",$this->post->p_text,80,15,"editorArea form-control required"); 
		?>
	</div></div>
	<?php if(!$this->post->p_id || ($this->post->p_author_id && User::getInstance()->getID()==$this->post->p_author_id)) { ?>
	<div class="themeRow row"><div class="col-md-12">
		<?php echo HTMLControls::renderCheckbox("p_subscribe",$this->userSubscribed,1,"","checkbox").HTMLControls::renderLabelField("p_subscribe", Text::_('Subscribe to theme')); ?>
	</div></div>
	<?php } ?>
	<div id="forumCaptcha" class="row"><div class="col-md-12">
		<?php if(!$this->disableCaptcha) echo Event::raise("captcha.renderForm",array("module"=>"forum"))?>
	</div></div>
	<div id="themeFooter" class="buttons">
		<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Send'); ?>" />
	</div>
</form>
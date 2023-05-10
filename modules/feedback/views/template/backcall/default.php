<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
?>

<?php 
if($this->is_ajax){
	$onsubmit=" onsubmit=\"".(siteConfig::$debugMode ? "console.log('onsubmit in ' + $(this).attr('id'));" : "")."
			var isFD=false;
			if(typeof FormData=='function'){
				var formData=new FormData(this);
				isFD=true;
			} else {
				var formData = $(this).serializeArray();
			}
			ajaxShowActivity();
			$.ajax({
				url : siteConfig['siteUrl'],
				type: 'POST',
				data:formData,
				processData:(isFD ? false : true),
				contentType:(isFD ? false : 'application/x-www-form-urlencoded;charset=UTF-8'),
				dataType:'json',
				success: function (data, textStatus) {
					ajaxHideActivity();
					if(data.status=='OK'){
						$('#fancybox-outer .backcallSender').html('<h4 class=\'title response\'>' +data.message + '</h4>');
					} else {
						alert(data.message);
					} 
				},
				error: function () {
					alert('".Text::mapjsAddSlashes(Text::_("Feedback not sent"))."');
					ajaxHideActivity();
					return false;
				}
			});
			return false;
			\"";		
} else {
	$onsubmit="";
}
?>
<div class="backcallSender container" style="margin-top: 5px;">
	<h1 class="backcall_form title"><?php echo Text::_("Order backcall"); ?></h1>
	<form name="<?php echo ($this->is_ajax ? "ajax" :""); ?>frmEdit" id="<?php echo ($this->is_ajax ? "ajax" :""); ?>frmEdit" method="post" action="index.php" data-target="backcall"<?php echo $onsubmit; ?>>
		<div class="row">
			<div class="row-label col-md-5"><label class="label" for="f_sender"><?php echo Text::_("Your name"); ?></label><span class="label_required">*</span></div>
			<div class="row-label col-md-7"><input value="<?php echo Request::getSafe('f_sender',''); ?>" name="f_sender" class="form-control" required="required" onchange="" type="text"></div>
		</div>
		<div class="row">
			<div class="row-label col-md-5"><label class="label" for="f_phone"><?php echo Text::_("Your phone"); ?></label><span class="label_required">*</span></div>
			<div class="row-label col-md-7"><input value="<?php echo Request::getSafe('f_phone',''); ?>" name="f_phone" class="form-control phone" required="required" onchange="" type="text"></div>
		</div>
		<div class="row">
			<div class="row-label col-md-5"><label class="label" for="f_mail"><?php echo Text::_("Your e-mail"); ?></label></div>
			<div class="row-label col-md-7"><input value="<?php echo Request::getSafe('f_mail',''); ?>" name="f_mail" class="form-control" onchange="" type="text"></div>
		</div>
<?php 
		/* privacy policy start */
		echo"<div class=\"privacy_policy_block row\"><div class=\"col-md-12\">";
		$pp_art=Module::getHelper('article','article')->getArticle(intval(siteConfig::$privacy_policy_article));
		echo "<input class=\"commonEdit required\" type=\"checkbox\" name=\"privacy_policy_agree\" value=\"1\" required=\"required\" />"
			."&nbsp;".Text::_('I have read and agreed with')
			." <a".($this->is_ajax ? " target=\"_blank\"" : " rel=\"nofollow\" class=\"relpopuptext\"")." href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$pp_art->a_id."&amp;alias=".$pp_art->a_alias.($this->is_ajax ? "" : "&amp;notmpl=1"))."\">".Text::_('privacy policy')."</a>";
		echo"</div></div>";
		/* privacy policy end */
		if(!$this->disableCaptcha) {
			echo"<div class=\"row row-for-captcha\"><div class=\"col-md-12\">";
			echo Event::raise("captcha.renderForm",array("module"=>"feedback"));
			echo"</div></div>";
		}
?>	
		<div class="buttons row">
			<div class="col-md-12">
				<input class="commonButton btn btn-info" name="apply" value="<?php echo Text::_("Send"); ?>" id="apply" type="submit">
			</div>
		</div>
		<input id="module" value="feedback" name="module" type="hidden">
		<input id="task" value="saveBackcall" name="task" type="hidden">
		<input id="is_ajax" value="<?php echo $this->is_ajax; ?>" name="is_ajax" type="hidden">
		<input id="view" value="backcall" name="view" type="hidden">
		<input id="parent_module" value="feedback" name="parent_module" type="hidden">
	</form>
</div>

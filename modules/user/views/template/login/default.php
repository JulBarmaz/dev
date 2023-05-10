<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$data_sn=array();
$suffix="";
if (backofficeConfig::$allowSNLogin) {
	Event::raise("user.login_form",array("module"=>"user"), $data_sn);
	if (count($data_sn)) $suffix="_sn";
}
$html ="
<div class=\"authorizeBlock".$suffix." float-fix\">
	<div id=\"loginForm\" class=\"commonPopup\">
		<form action=\"".Router::_("index.php")."\" method=\"post\">
			<input type=\"hidden\" name=\"option\" value=\"login\" />
			<input type=\"hidden\" name=\"return_url\" value=\"".((isset($this->return_url) && $this->return_url) ? base64_encode($this->return_url) : Util::getRefererUrl())."\" />
			<p class=\"login_label\">".(backofficeConfig::$allowEmailLogin ? HTMLControls::renderLabelField(false,'Email',true) : HTMLControls::renderLabelField(false, 'Login name', true)).":</p>
			<p class=\"login_input\"><input type=\"text\" required=\"required\" class=\"commonEdit loginEdit form-control\" name=\"username\" value=\"\" /></p>
			<p class=\"pwd_label\">".HTMLControls::renderLabelField(false,'Password',true).":</p>
			<p class=\"pwd_input\"><input type=\"password\" required=\"required\" class=\"commonEdit loginEdit form-control\" name=\"userpass\" value=\"\" /></p>
			<p class=\"remember_label\"><input type=\"checkbox\" id=\"remember_me\" name=\"remember\" />&nbsp;".HTMLControls::renderLabelField('remember_me', 'Remember me', true)."</p>
			<div id=\"loginButtons\" class=\"buttons\">"
			.HTMLControls::renderButton("login",Text::_('Log in'),"submit","","btn btn-info")
			.(!backofficeConfig::$noRegistration && !User::getInstance()->isLoggedIn() ? "&nbsp;<a rel=\"nofollow\" class=\"regButton linkButton btn btn-info\" href=\"".Router::_("index.php?module=user&amp;view=register")."\"><span>".Text::_("Register")."</span></a>" : "")
			."</div>
		</form>
	</div>";
if (count($data_sn)) {
	$html.="<div id=\"snBlock\"><h4 class=\"title\">".Text::_("You may login as").":</h4>";
	$html.="<div id=\"snButtons\" class=\"sn_buttons\">";
	$html.=implode($data_sn);
	$html.="</div>";
	$html.="</div>";
}
$html.="</div>";
echo $html;
?>
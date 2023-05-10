<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$_HTML="<div id=\"sign_in\">";
// $_HTML.="<h4 class=\"not_authorized\">".Text::_("You are not authorized").".</h4>";
$_HTML.="<h4 class=\"please_login\">".Text::_(backofficeConfig::$noRegistration ? "Please log in" : "Please log in or register").".</h4>";
$_HTML.="<div class=\"buttons\">";
$_HTML.="<a rel=\"nofollow\" class=\"linkButton relpopup btn btn-info\" href=\"".Router::_("index.php?module=user&amp;view=login")."\">".Text::_("Log in")."</a>";
if (!backofficeConfig::$noRegistration) $_HTML.="<a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=user&amp;view=register")."\">".Text::_("Register")."</a>";
$_HTML.="</div>";
if(catalogConfig::$ordersWithoutRegistration && !backofficeConfig::$cryptoUserData){
	$_HTML.="<div class=\"buttons\">";
	$_HTML.="	<form action=\"".Router::_(Util::getReturnUrl(false))."\" method=\"post\">";
	$_HTML.="		<input class=\"linkButton btn btn-info\" type=\"submit\" value=\"".Text::_("Continue without registration")."\" />";
	$_HTML.="		<input type=\"hidden\" name=\"without_registration\" value=\"1\" />";
	$_HTML.="	</form>";
	$_HTML.="</div>";
}
$_HTML.="</div>";
echo $_HTML; 

?>
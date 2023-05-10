<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$loginHTML = "
	<div id=\"resetForm\" class=\"commonPopup\">
		<form action=\"".Router::_("index.php")."\" method=\"post\">
			<input type=\"hidden\" name=\"module\" value=\"user\" />
			<input type=\"hidden\" name=\"task\" value=\"reactivate\" />"
			.HTMLControls::renderLabelField(false,Text::_("Your e-mail"))
			.":<div class=\"registrationFormField\"><input type=\"text\"  class=\"commonEdit loginEdit form-control\" name=\"r_email\" value=\"\" /></div>"
			."<div id=\"loginButtons\" class=\"buttons\">".HTMLControls::renderButton("login",Text::_('Request activation'),"submit", "", "btn btn-info")."</div>
		</form>
	</div>";
echo $loginHTML;
?>
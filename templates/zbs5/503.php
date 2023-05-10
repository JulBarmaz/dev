<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta content="no-cache" http-equiv="Pragma" />
	<meta content="no-cache" http-equiv="cache-control" />
	<title>Critical error (503)</title>
	<meta content="" name="description" />
	<meta content="" name="keywords" />
	<link type="text/css" href="/css/debug.css" rel="stylesheet" />
	<link type="text/css" href="/css/errors.css" rel="stylesheet" />
</head>
<body>
	<div id="error_body">
		<div id="error_header">
			<div id="logo" class="logo503" onclick="javascript: document.getElementById('loginForm').style.display='block';"></div>
			<div id="headLine"></div>
		</div>
		<div id="loginForm" class="commonPopup">
			<form action="/index.php" method="post">
				<input name="option" value="login" type="hidden" />
				User:&nbsp;
				<input style="width: 75px;" class="commonEdit loginEdit" id="username" name="username" value="" type="text" />
				&nbsp;&nbsp;Password:&nbsp;
				<input style="width: 75px; font-weight: 600; font-style: italic; color: rgb(0, 0, 153);" class="commonEdit loginEdit" name="userpass" value="" type="password" />
				&nbsp;
				<input class="commonButton" style="width: 75px;" value="Войти" onclick="javascript: $('#loginForm').hide('fast'); return true;" type="submit" />
			</form>
		</div>
		<div class="fatal-error-text"><?php echo Text::_("Fatal error"); ?>: <?php echo $message; ?></div>
		<div class="fatal-error-link"><a class="go_back" href="/"><?php echo Text::_('Go main page');?></a></div>
	</div>
	<?php if ((siteConfig::$debugMode && User::getInstance()->isAdmin()) || siteConfig::$debugMode>100) {	echo Debugger::getInstance()->dump(); } ?>
</body>
</html>
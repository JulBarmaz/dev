<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModelbackcall extends Model {
	public function send($theme, $sender, $phone, $mail, $ip, $CopyAdresses=false) {
		$to = soConfig::$siteEmail;
		if ($CopyAdresses) $to.=",".$CopyAdresses;
		$text=sprintf(Text::_("backcall mail text"), Portal::getURI(), $sender, $phone, $mail, $ip);
		return aNotifier::addToMailQueue($to, $theme, $text);
	}
}
?>
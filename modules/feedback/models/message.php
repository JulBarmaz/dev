<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModelmessage extends SpravModel {
	public function send($msgid,$theme,$sender,$mail,$ip,$feedbacktheme,$feedbacktext,$CopyAdresses=false) {
		if (!$msgid) return false;
		$to=soConfig::$siteEmail;
		if($CopyAdresses) $to.=",".$CopyAdresses;		
		$link=Router::_("/administrator/index.php?module=feedback&view=messages&task=modify&psid=".$msgid, false, true, 1, 2);
		$text=sprintf(Text::_("feedback mail text"),
		Portal::getURI(),	$sender,$mail,$ip,$feedbacktheme,$feedbacktext,$link);
		if (aNotifier::addToMailQueue($to, $theme, $text)) {
			$query="UPDATE #__feedback SET f_sent=1 WHERE f_id=".intval($msgid);
			$this->_db->setQuery($query);
			if ($this->_db->query()) return true;
			else return false;
		}
	}
}
?>
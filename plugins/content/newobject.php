<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined ( '_BARMAZ_VALID' ) or die ( "Access denied" );
class contentPluginnewobject extends Plugin {
	protected $_events = array ("rating.new");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("article_cost", "integer", 1);
		$this->addParam("blog_cost", "integer", 1);
		$this->addParam("comment_cost", "integer", 1);
		$this->addParam("user_cost", "integer", 0);
	}
	protected function onRaise($event, &$data) {
		$result = "";
		$module = $this->getParam("module", false);
		if (! $module)
			return $result;
		$view = $this->getParam("view", false);
		$element = $this->getParam("element", false);
		switch ($event) {
			case "rating.new" :
				if (! User::getInstance ()->isLoggedIn ())
					$result = false;
				else {
					$uid = User::getInstance ()->getID ();
					$result = $this->updateUserRating ( $module, $view, $element, $uid, "+" );
				}
				break;
			case "rating.delete" :
				if (! User::getInstance ()->isLoggedIn ())
					$result = false;
				else {
					$uid = User::getInstance ()->getID ();
					$result = $this->updateUserRating ( $module, $view, $element, $uid, "-" );
				}
				break;
			default :
				break;
		}
		return $result;
	}
	// Эта функция меняет рейтинг пользователя (автора или самого пользователя, смотря как вызвать)
	private function updateUserRating($module, $view, $element, $uid, $znak) {
		if (! $uid)
			return false;
		if ($element == "object") { // это объект
			$delta = $this->getParam($module . "_cost");
		} elseif ($element == "comment") { // это обычный комментарий
			$delta = $this->getParam("comment_cost");
		} else
			return false;
		if (! $delta)
			return false;
		$sql = "UPDATE #__users SET u_rating=u_rating" . $znak . $delta . " WHERE u_id=" . $uid;
		Database::getInstance ()->setQuery ( $sql );
		return Database::getInstance ()->query ();
	}
}
?>
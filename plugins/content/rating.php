<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class contentPluginrating extends Plugin {

	private $_vote_types=array("comment"=>1, "article.object"=>2, "blog.object"=>3, "user.object"=>4);	
	protected $_events=array("rating.check", "rating.vote","rating.rendervotepanel");	
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("article_cost", "integer", 1);
		$this->addParam("blog_cost", "integer", 1);
		$this->addParam("user_cost", "integer", 1);
		$this->addParam("comment_cost", "integer", 1);
	}
	protected function onRaise($event, &$data) {
		$result = "";
		$psid = $this->getParam("psid", false);
		if (! $psid) return $result;
		$module = $this->getParam("module", false);
		if (! $module) return $result;
		$view = $this->getParam("view", false);
		$element = $this->getParam("element", false);
		$znak = ($this->getParam("direction", false) == "up" ? "+" : "-" );
		switch ($event) {
			case "rating.rendervotepanel" :
				$mess = $this->getParam("mess", false, Text::_ ( "Vote this" ));
				$result = $this->renderVotePanel( $module, $view, $element, $psid, $mess );
				break;
			case "rating.check" :
				$result = $this->checkVoted($module, $element, $psid);
				break;
			case "rating.vote" :
				$author = $this->getParam( "author", false, 0);
				$uid = User::getInstance ()->getID ();
				// if ($author!=$uid)
				$result = $this->updateObjectRating ( $module, $view, $element, $psid, $author, $znak );
				break;
			default :
				break;
		}
		return $result;
	}
	public function checkVoted($module,$element,$psid){
		if ($element=="object"){
			$place=$module.".object";
		} elseif ($element=="comment") {
			$place=$element;
		} else return true;
		if (!array_key_exists($place, $this->_vote_types)) return false;
		$sql="SELECT COUNT(v_uid) FROM #__votes WHERE v_uid=".User::getInstance()->getID()." AND v_type=".$this->_vote_types[$place]." AND v_eid=".$psid;
		Database::getInstance()->setQuery($sql); 
		return intval(Database::getInstance()->LoadResult());
	}
	// Эта функция меняет рейтинг пользователя (автора или самого пользователя, смотря как вызвать)
	private function updateUserRating($module, $view, $element,$uid, $znak){ 
		if (!$uid) return false;
		if ($element=="object"){ // это объект
			$delta=$this->getParam($module."_cost");
		} elseif ($element=="comment"){ // это обычный комментарий
			$delta=$this->getParam("comment_cost");
		} else return false;
		if (!$delta) return false;
		$sql="UPDATE #__users SET u_rating=u_rating".$znak.$delta." WHERE u_id=".$uid;
		Database::getInstance()->setQuery($sql); 
		return Database::getInstance()->query();
	}
	private function updateObjectRating($module, $view, $element, $psid, $uid, $znak){ 
		if (!$this->updateUserRating($module, $view, $element,$uid, $znak)) return false;
		if ($element=="object"){ // это объект
			$delta=$this->getParam($module."_cost");
			if (!$delta) return false;
			$place=$module.".object";
			if ($module=="article"){
				$sql="UPDATE #__articles SET a_rating=a_rating".$znak.$delta." WHERE a_id=".$psid;
			}	elseif ($module=="blog"){
				$sql="UPDATE #__blogs_posts SET p_rating=p_rating".$znak.$delta." WHERE p_id=".$psid;
			} else return false;
		} elseif ($element=="comment"){ // это обычный комментарий
			$place=$element;
			$delta=$this->getParam("comment_cost");
			if (!$delta) return false;
			//@FIXME Now only base tables
			$sql="UPDATE #__comms SET cm_rating=cm_rating".$znak.$delta." WHERE cm_id=".$psid." AND cm_uid=".$uid;
		} else return false;
		Database::getInstance()->setQuery($sql); 
		if (!Database::getInstance()->query()) return false;
		$votesql="INSERT INTO #__votes VALUES (".User::getInstance()->getID().",".$this->_vote_types[$place].",".$psid.")";
		Database::getInstance()->setQuery($votesql);
		return Database::getInstance()->query();
	}
	private function renderVotePanel($module, $view, $element, $psid, $mess){
		$html = "<div class=\"vote_panel\">";
		$html.= "<a class=\"vote_down\" onclick=\"voteRating(this,'".$module."','".$view."','".$element."','".$psid."','down');\"><img alt=\"\" title=\"".Text::_("Bad")."\" width=\"1\" height=\"1\" src=\"/images/blank.gif\" /></a>";
		$html .= "<span>".$mess."</span>";
		$html.= "<a class=\"vote_up\" onclick=\"voteRating(this,'".$module."','".$view."','".$element."','".$psid."','up');\"><img alt=\"\" title=\"".Text::_("Good")."\" width=\"1\" height=\"1\" src=\"/images/blank.gif\" /></a>";
		$html.= "</div>";
		return $html;
	}
}
?>
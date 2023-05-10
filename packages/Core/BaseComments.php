<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

Class BaseComments extends BaseObject{
	private static $_instance = null;
	private $obj_id=0;
	private $list_limit=20;
	private $text_limit=1000;
	private $premoderate=0;
	private $mailmoder=0;
	private $tablename="comms";
	private $group_id=0;
	private $enabled=0;
	private $vote_obj=0;
	private $vote_comms=0;
	private $bbcode=0;
	private $module="";
	private $view="";
	private $layout="";
	private $title="";
	private $_data=array();
	private $_types=array();
	private $_categories=array();
	private $actions = array("read","write","moderate","vote");
	private $current_count=0;
	
	public function __construct() {
		$this->initObj();
	}

	public static function getInstance() {
		if (self::$_instance == null) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function getActions() {
		return $this->actions;
	}
	
	public function commentsEnabled() {
		return $this->enabled;
	}
	
	public function premoderateEnabled() {
		return $this->premoderate;
	}
	
	public function cutCommentByLimit($text) {
		return mb_substr($text,0,$this->text_limit,DEF_CP);
	}
	public function cleanComments($obj_arr){
		$sql="DELETE FROM #__".$this->tablename." WHERE cm_grp_id=".$this->group_id." AND cm_obj_id NOT IN (".implode(",",$obj_arr).")";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public function init($module,$view,$psid=0,$layout="") {
		$sql="SELECT * FROM #__comms_grp WHERE cg_module='".$module."' AND cg_view='".$view."'";
		Database::getInstance()->setQuery($sql);
		$group=false;
		Database::getInstance()->loadObject($group);
		if ($group) {
			$this->group_id=$group->cg_id;
			if ($group->cg_tablename) $this->tablename=$group->cg_tablename;
			$this->premoderate=$group->cg_premoderate;
			$this->mailmoder=$group->cg_mailmoder;
			$this->title=$group->cg_title;
			$this->enabled=$group->cg_enabled;
			$this->bbcode=$group->cg_bbcode;
			if ($group->cg_list_limit) $this->list_limit=$group->cg_list_limit;
			if ($group->cg_text_limit) $this->text_limit=$group->cg_text_limit;
			$this->obj_id=$psid;
			$this->module=$module;			
			$this->view=$view;
			$this->layout=$layout;
			if ($group->cg_vote_comms) $this->vote_comms=$group->cg_vote_comms;
			if ($group->cg_vote_obj) $this->vote_obj=$group->cg_vote_obj;
			$this->vote_obj=0; // @TODO убрать когда заработает
			if ($this->group_id) { 
				$this->loadACL();
				$this->getCommCategories();
				$this->getCommTypes();
			}
			return $this->group_id;
		}	else return false;
	}
	
	public function checkACL($action) {
		if (array_key_exists($action,$this->_data)){
			return intval($this->_data[$action]->ca_flag);
		} else return 0;
	}
	private function loadACL(){
		if (!$this->group_id) return false;
		$r_id=User::getInstance()->getRole();
		if (!$r_id) return false;
		$sql="SELECT ca_action, ca_flag FROM #__comms_acl WHERE ca_grp_id=".$this->group_id." AND ca_r_id=".$r_id;
		Database::getInstance()->setQuery($sql);
		$this->_data=Database::getInstance()->loadObjectList("ca_action");
		Event::raise("comments.loadACL",array("module"=>$this->module,"view"=>$this->view,"psid"=>$this->obj_id),$this->_data);
	}
	
	public function getGIDsForUser($action){
		if (!in_array($action,$this->actions)) return array();
		$r_id=User::getInstance()->getRole();	
		if (!$r_id) return array();
		$sql="SELECT ca_grp_id FROM #__comms_acl WHERE ca_flag=1 AND ca_action='".$action."' AND ca_r_id=".$r_id;
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList("ca_grp_id");
	}
	
	private function getCommTypes() {
		if (!$this->group_id) return false;
		$sql="select ct_id as id,ct_title as title,ct_marker as marker from	#__comms_types WHERE ct_cgrp_id=".$this->group_id." AND ct_enabled=1 AND ct_deleted=0";
		Database::getInstance()->setQuery($sql);
		$this->_types=Database::getInstance()->loadObjectList("id");		
	}
	
	private function getCommCategories() {
		if (!$this->group_id) return false;
		$sql="select 	cc_id as id,cc_title as title,cc_marker  as marker from	#__comms_cat WHERE cc_cgrp_id=".$this->group_id." AND cc_enabled=1 AND cc_deleted=0";
		Database::getInstance()->setQuery($sql);
		$this->_categories=Database::getInstance()->loadObjectList("id");		
	}
	
	private function renderTypeMessage($id, $icon=false) {
		if(isset($this->_types[$id]))	{
			if($icon) {
				$html=HTMLControls::renderImage($this->_types[$id]->marker,false);
			} else {
				$html=Text::_("Comment type").":".$this->_types[$id]->title;	
			}
			return $html;
		}	else return "";
	}
	
	private function renderCatMessage($id, $icon=false) {
		if(isset($this->_categories[$id])){
			if ($icon){
				$html=HTMLControls::renderImage($this->_categories[$id]->marker,false);
			} else {
				$html=Text::_("Category").":".$this->_categories[$id]->title;
			}
			return $html;
		}	else return "";
	}
		
	public function countComments($parent_id=0, $_start_with=0) {
		if (!$this->checkACL("read")) return 0;
		$uid=User::getInstance()->getID();
		$sql="SELECT COUNT(cm_id) FROM #__".$this->tablename;
		$sql.=" WHERE cm_grp_id=".$this->group_id." AND cm_obj_id=".$this->obj_id." AND cm_parent_id=".$parent_id." AND cm_deleted=0";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (cm_published=1 OR cm_uid=".$uid.")";
			else $sql.=" AND cm_published=1";
		}	else {
			$sql.=" AND cm_published=1";
		}
		Database::getInstance()->setQuery($sql);
		$this->current_count=Database::getInstance()->loadResult();
	}
	public function getComment($comm_id) {
		if (!$this->checkACL("read")) return false;
		$uid=User::getInstance()->getID();
		$sql="SELECT c.*";
/**/
		$sql.=",(SELECT COUNT(d.cm_id) FROM #__".$this->tablename." AS d WHERE d.cm_parent_id=c.cm_id ";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (d.cm_published=1 OR d.cm_uid=".$uid.")";
			else $sql.=" AND d.cm_published=1";
		}	else {
			$sql.=" AND d.cm_published=1";
		}
		$sql.=" AND d.cm_deleted=0) AS cm_children";
/**/	
		$sql.=" FROM #__".$this->tablename." AS c";
		$sql.=" WHERE c.cm_grp_id=".$this->group_id." AND c.cm_id=".$comm_id;
		if ($this->obj_id) $sql.=" AND c.cm_obj_id=".$this->obj_id;
		$sql.=" AND c.cm_deleted=0";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (c.cm_published=1 OR c.cm_uid=".$uid.")";
			else $sql.=" AND c.cm_published=1";
		}	else {
			$sql.=" AND c.cm_published=1";
		} 
		Database::getInstance()->setQuery($sql);
//		echo Database::getInstance()->getQuery();
		$res=false;	Database::getInstance()->loadObject($res);
		return $res;
	}
	
	public function getComments($parent_id=0, $_start_with=0) {
		if (!$this->checkACL("read")) return false;
		$this->countComments($parent_id, $_start_with);
		if ($this->current_count < $_start_with) return false;
		$uid=User::getInstance()->getID();
		$sql="SELECT c.*,";
		$sql.="(SELECT COUNT(d.cm_id) FROM #__".$this->tablename." AS d WHERE d.cm_parent_id=c.cm_id ";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (d.cm_published=1 OR d.cm_uid=".$uid.")";
			else $sql.=" AND d.cm_published=1";
		}	else {
			$sql.=" AND d.cm_published=1";
		}
		$sql.=" AND d.cm_deleted=0) AS cm_children";
		$sql.=" FROM #__".$this->tablename." AS c";
		$sql.=" WHERE c.cm_grp_id=".$this->group_id." AND c.cm_obj_id=".$this->obj_id." AND c.cm_parent_id=".$parent_id." AND c.cm_deleted=0";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (c.cm_published=1 OR c.cm_uid=".$uid.")";
			else $sql.=" AND c.cm_published=1";
		}	else {
			$sql.=" AND c.cm_published=1";
		}
		if ($parent_id) $sql.=" ORDER BY cm_date"; else $sql.=" ORDER BY cm_date"; 
		$sql.=" LIMIT ".$_start_with.",".$this->list_limit;
		Database::getInstance()->setQuery($sql);
		$res=Database::getInstance()->loadObjectList();
		return $res;
	}
	public function getLastComment() {
		if (!$this->checkACL("read")) return false;
		$uid=User::getInstance()->getID();
		$sql="SELECT c.*,";
		$sql.="(SELECT COUNT(d.cm_id) FROM #__".$this->tablename." AS d WHERE d.cm_parent_id=c.cm_id ";
		if ($this->checkACL("moderate")){
			/***/
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (d.cm_published=1 OR d.cm_uid=".$uid.")";
			else $sql.=" AND d.cm_published=1";
		}	else {
			$sql.=" AND d.cm_published=1";
		}
		$sql.=" AND d.cm_deleted=0) AS cm_children";
		$sql.=" FROM #__".$this->tablename." AS c";
		$sql.=" WHERE c.cm_grp_id=".$this->group_id." AND c.cm_obj_id=".$this->obj_id." AND c.cm_deleted=0";
		if ($this->checkACL("moderate")){
		}	elseif ($this->premoderate) {
			if ($uid) $sql.=" AND (c.cm_published=1 OR c.cm_uid=".$uid.")";
			else $sql.=" AND c.cm_published=1";
		}	else {
			$sql.=" AND c.cm_published=1";
		}
		$sql.=" ORDER BY cm_date DESC"; 
		$sql.=" LIMIT 0,1";
		Database::getInstance()->setQuery($sql);
// echo Database::getInstance()->getQuery();
		Database::getInstance()->loadObject($res);
		return $res;
	}
	public function checkParentComment($cm_id){
		$sql="SELECT cm_id FROM #__".$this->tablename;
		$sql.=" WHERE cm_grp_id=".$this->group_id." AND cm_id=".$cm_id." AND cm_obj_id=".$this->obj_id." AND cm_deleted=0 AND cm_published=1";
		Database::getInstance()->setQuery($sql);
		$res=(int)Database::getInstance()->loadResult();
		return $res;
	}
	public function saveComment($parent_id,$title,$text,$nickname,$email,$cm_cat=0,$cm_type=0) {
		if ($this->checkACL("write")) {
			if($parent_id) { $parent_id=$this->checkParentComment($parent_id); $cm_cat=0; $cm_type=0;}
			$uid=User::getInstance()->getID();
			$ip_addr = User::getInstance()->getIP();
			if ($this->premoderate) $published=0; else $published=1;
			$sql="INSERT INTO #__".$this->tablename."
			(cm_grp_id,cm_obj_id,cm_parent_id,cm_uid,cm_nickname,cm_email,cm_date,cm_ip,cm_title,cm_text,cm_rating,cm_published,cm_deleted,cm_cat,cm_type)
			VALUES 
			(".$this->group_id.",".$this->obj_id.",".$parent_id.",".$uid.",'".$nickname."','".$email."',NOW(),'".$ip_addr."','".$title."','".$text."',0,".$published.",0,".$cm_cat.",".$cm_type.")";
			Database::getInstance()->setQuery($sql);
			Database::getInstance()->query();
			$cid=Database::getInstance()->insertid();
			Event::raise("rating.new",array("module"=>$this->module,"element"=>"comment"));  // content.newobject
			$author=0; // @TODO проблемы с автором 
			if ($this->vote_obj&&$this->checkACL("vote")){
				$direction=Request::getInt("vote_obj","up");
				$params = array("module"=>$this->module,"view"=>$this->view,"element"=>"object","psid"=>$this->obj_id,"direction"=>$direction, "author"=>$author);
				$data_voted=Event::raise("rating.check",$params);  // content.rating
				if (!$data_voted) {
					Event::raise("rating.vote",$params); // content.rating
				}
			}
			if ($this->premoderate && $this->mailmoder) { $this->reminderForModerator($cid,$title,$text); }
			return $cid;
		} else { return false; }
	}
	
	public function renderComments($parent_id=0,$_start_with=0) {
		$list=$this->getComments($parent_id, $_start_with);
		$html=""; $intro=false;
		if ($list && count($list)){
			$canModerate=$this->checkACL("moderate");
			$canWrite=$this->checkACL("write");
			$canVote=$this->checkACL("vote");
			$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS."html".DS.'comment.php';
			foreach ($list as $mess){ 
				$a_path=""; $a_link="";
				if ($mess->cm_published) $published=""; else $published=" unpublished";
				if ($mess->cm_uid) {
					$profile=Profile::getInstance($mess->cm_uid)->getProfile();
					$avatar=$profile["pf_img"];
					if (isset($avatar["val"]) && $avatar["val"]){
						$a_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.Files::splitAppendix($avatar["val"],true);
						if ($a_path && is_file($a_path)){
							$a_link=BARMAZ_UF."/user/i/avatars/".Files::splitAppendix($avatar["val"]);
						}
					}
				}
				$layout=Request::getSafe("layout","");
				if ($layout) $layout="&layout=".$layout;
				$comment_id=$mess->cm_id;
				$cm_link=Router::_("index.php?module=".$this->module."&view=".$this->view.$layout."&psid=".$mess->cm_obj_id."&task=getComment&comm_id=".$comment_id);
				$user_link=""; if ($mess->cm_uid) $user_link = Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid);
				$publishJS="javascript: toggleCommentEnabled(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");";
				$deleteJS="javascript: if(confirm('".Text::_("Proceed")." ?')) toggleCommentDeleted(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");";
				if ($mess->cm_children)	$childrenJS="javascript: getCommentChildren(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");"; else $childrenJS="";
				if ($canWrite) $answerJS="javascript: setCommentAnswerTo(".$comment_id.");"; else $answerJS="";
				$uplink="";
				
				$html.="<div id=\"cm_body_".$comment_id."\" class=\"single_comment".$published."\">";
				$html.="<a name=\"comment".$comment_id."\"></a>";
				if (is_file($lPath)) {
					ob_start();
					include $lPath;
					$html .= ob_get_contents();
					ob_end_clean();
				} else {
					if ($a_link) $html.="<div class=\"comment_avatar\"><a rel=\"nofollow\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid)."\"><img src=\"".$a_link."\" /></a></div>";
					else $html.="<div class=\"comment_no_avatar\"></div>";
					$html.="<div class=\"comment_title\">";
					$html.="<span id=\"comment_date_".$comment_id."\" class=\"comment_date\"><a href=\"".$cm_link."\">".Date::fromSQL($mess->cm_date)."</a></span>";
					if ($mess->cm_uid) $html.="<a rel=\"nofollow\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid)."\"><span id=\"comment_author_".$comment_id."\" class=\"comment_author\">".$mess->cm_nickname."</span></a>";
					else $html.="<span id=\"comment_author_".$comment_id."\" class=\"comment_author\">".$mess->cm_nickname."</span>";
					$html.="<span class=\"comment_title\">".$mess->cm_title."</span>";
					$html.="</div>";
					if (($mess->cm_cat)||($mess->cm_type)){
				 		$html.="	<div class=\"comment_tps\">";	
						$html.=$this->renderTypeMessage($mess->cm_type);	
						$html.="&nbsp;";
						$html.=$this->renderCatMessage($mess->cm_cat);
				 		$html.="	</div>";	
					}				
					$html.="<div class=\"comment_text\">";
					if ($this->bbcode) { Event::raise("bbcode.parse",array(),$mess->cm_text); $html.=$mess->cm_text;}
					else $html.=Text::toHtml($mess->cm_text);
					$html.="</div>";
					if ($answerJS || $childrenJS || $this->vote_comms || $canModerate) {
						if ($this->vote_comms) {
							$html.="	<div class=\"comment_controls comment_rating\">";
							$html.="<div class=\"rating\">".Text::_("Rating")." : ".$mess->cm_rating."</div>";
							if($canVote) $html.=Event::raise("rating.rendervotepanel",array("module"=>$this->module,"view"=>$this->view,"element"=>"comment","psid"=>$comment_id, "mess"=>Text::_("Vote this comment"))); // content.rating
							$html.="	</div>";
						}
						$html.="	<div class=\"comment_controls\">";
						if ($canModerate){
							$html.=HTMLControls::renderHiddenField("comm_published_".$comment_id,$mess->cm_published);
							$html.="<a onclick=\"".$publishJS."\" class=\"commentFooterAdminLink btn btn-warning\" rel=\"nofollow\">";
							if ($mess->cm_published) $html.=Text::_("Disable"); else $html.=Text::_("Enable");
							$html.="</a>";
							$html.=HTMLControls::renderHiddenField("comm_deleted_".$comment_id,$mess->cm_deleted);
							$html.="<a onclick=\"".$deleteJS."\" class=\"commentFooterAdminLink btn btn-warning\" rel=\"nofollow\">";
							if ($mess->cm_deleted) $html.=Text::_("Undelete"); else $html.=Text::_("Delete");
							$html.="</a>";
						}
						if ($childrenJS) $html.="<a id=\"expander".$comment_id."\" onclick=\"".$childrenJS."\" class=\"commentFooterLink btn btn-info\" rel=\"nofollow\">".Text::_("Answers")." (".$mess->cm_children.")</a>";
						if ($answerJS) $html.="<a onclick=\"".$answerJS."\" href=\"#commentEditor\" class=\"commentFooterButton btn btn-info\" rel=\"nofollow\">".Text::_("Answer")."</a>";
						$html.="	</div>";
					}
				}
				$html.="</div>";
				$html.="<div id=\"subcomments_".$comment_id."\" class=\"subcomments\"></div>";
			}
			$delta=$this->current_count - $_start_with - $this->list_limit;
			if ($delta>0){ 
				$html.="<div id=\"morecomments_".$parent_id."\" class=\"morecomments\">";
				// возиожен отказ JS
				$html.="<a id=\"expander_more\" onclick=\"javascript: getMoreComments(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$parent_id.",".($_start_with + $this->list_limit).");\" class=\"commentFooterLink\" rel=\"nofollow\">".Text::_("More comments")."</a>";
				$html.="</div>";
			}
		}
		return $html;
	}
	
	public function renderComment($comm_id,$intro=true){
		$html="";
		$mess=$this->getComment($comm_id);
		$canModerate=$this->checkACL("moderate");
		$canWrite=$this->checkACL("write");
		$canVote=$this->checkACL("vote");
		$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS."html".DS.'comment.php';
		if ($mess){
			$a_path=""; $a_link="";
			if ($mess->cm_published) $published=""; else $published=" unpublished";
			if ($mess->cm_uid) {
				$profile=Profile::getInstance($mess->cm_uid)->getProfile();
				$avatar=$profile["pf_img"];
				if (isset($avatar["val"]) && $avatar["val"]){
					$a_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.Files::splitAppendix($avatar["val"],true);
					if ($a_path && is_file($a_path)){
						$a_link=BARMAZ_UF."/user/i/avatars/".Files::splitAppendix($avatar["val"]);
					}
				}
			}
			$layout=Request::getSafe("layout","");
			if ($layout) $layout="&layout=".$layout;
			$comment_id=$mess->cm_id;
			$cm_link=Router::_("index.php?module=".$this->module."&view=".$this->view.$layout."&psid=".$mess->cm_obj_id."&task=getComment&comm_id=".$comment_id);
			$user_link=""; if ($mess->cm_uid) $user_link = Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid);
			$publishJS="javascript: toggleCommentEnabled(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");";
			$deleteJS="javascript: if(confirm('".Text::_("Proceed")." ?')) toggleCommentDeleted(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");";
			if ($mess->cm_children)	$childrenJS="javascript: getCommentChildren(this,'".$this->module."','".$this->view."',".$this->obj_id.",".$comment_id.");"; else $childrenJS="";
			if ($canWrite) $answerJS="javascript: setCommentAnswerTo(".$comment_id.");"; else $answerJS="";
			$uplink="index.php?module=".$this->module."&view=".$this->view."&psid=".$mess->cm_obj_id;
			if ($mess->cm_parent_id) {
				$uplink.="&task=getComment&comm_id=".$mess->cm_parent_id;
				$uplink_text=Text::_("View parent thread");
			} else {
				$uplink_text=Text::_("View all comments");
			}
			$uplink=Router::_($uplink);
				
			$html.="<div id=\"cm_body_".$comment_id."\" class=\"single_comment".$published."\">";
			if (is_file($lPath)) {
				ob_start();
				include $lPath;
				$html .= ob_get_contents();
				ob_end_clean();
			} else {
				if ($intro) {
					$html.="	<div class=\"comment_text\">";
					$html.=Text::_("Comments group")." : ".$this->title." <a href=\"".Router::_("index.php?module=".$this->module."&view=".$this->view."&psid=".$this->obj_id)."\">".Text::_("for object")."</a>";
					$html.="	</div>";
				}
				$html.="<a name=\"comment".$comment_id."\"></a>";
				if ($a_link) $html.="<div class=\"comment_avatar\"><a rel=\"nofollow\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid)."\"><img src=\"".$a_link."\" /></a></div>";
				else $html.="<div class=\"comment_no_avatar\"></div>";
				$html.="	<div class=\"comment_title\">";
				$html.="<span id=\"comment_date_".$comment_id."\" class=\"comment_date\">".Date::fromSQL($mess->cm_date)."</span>";
				if ($mess->cm_uid)	$html.="<a rel=\"nofollow\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$mess->cm_uid)."\"><span id=\"comment_author_".$comment_id."\" class=\"comment_author\">".$mess->cm_nickname."</span></a>";
				else  $html.="<span id=\"comment_author_".$comment_id."\" class=\"comment_author\">".$mess->cm_nickname."</span>";
				$html.="<span class=\"comment_title\">".$mess->cm_title."</span>";
				$html.="	</div>";
	
				if (($mess->cm_cat)||($mess->cm_type)){
					$html.="	<div class=\"comment_tps\">";	
					$html.=$this->renderTypeMessage($mess->cm_type);	
					$html.="&nbsp;";
					$html.=$this->renderCatMessage($mess->cm_cat);
					$html.="	</div>";	
				}				
							
				$html.="	<div class=\"comment_text\">";
				if ($this->bbcode) { Event::raise("bbcode.parse",array(),$mess->cm_text); $html.=$mess->cm_text;}
				else $html.=Text::toHtml($mess->cm_text);
				//				$html.=Text::toHtml($mess->cm_text);
				$html.="	</div>";
				if ($this->vote_comms) {
					$html.="	<div class=\"comment_controls comment_rating\">";
					$html.="<div class=\"rating\">".Text::_("Rating")." : ".$mess->cm_rating."</div>";
					if($canVote) $html.=Event::raise("rating.rendervotepanel",array("module"=>$this->module,"view"=>$this->view,"element"=>"comment","psid"=>$comment_id, "mess"=>Text::_("Vote this comment"))); // content.rating
					$html.="</div>";
				}
				$html.="	<div class=\"comment_controls\">";
				if ($canModerate){
					$html.=HTMLControls::renderHiddenField("comm_published_".$comment_id,$mess->cm_published);
					$html.="<a onclick=\"".$publishJS."\" class=\"commentFooterAdminLink btn btn-warning\" rel=\"nofollow\">";
					if ($mess->cm_published) $html.=Text::_("Disable"); else $html.=Text::_("Enable");
					$html.="</a>";
					$html.=HTMLControls::renderHiddenField("comm_deleted_".$comment_id,$mess->cm_deleted);
					$html.="<a onclick=\"".$deleteJS."\" class=\"commentFooterAdminLink btn btn-warning\" rel=\"nofollow\">";
					if ($mess->cm_deleted) $html.=Text::_("Undelete"); else $html.=Text::_("Delete");
					$html.="</a>";
				}
				if ($childrenJS) $html.="<a id=\"expander".$comment_id."\" onclick=\"".$childrenJS."\" class=\"commentFooterLink btn btn-info\" rel=\"nofollow\">".Text::_("Answers")." (".$mess->cm_children.")</a>";
				if ($answerJS) $html.="<a onclick=\"".$answerJS."\" href=\"#commentEditor\" class=\"commentFooterButton btn btn-info\" rel=\"nofollow\">".Text::_("Answer")."</a>";
				$html.="<a class=\"commentFooterButton\" href=\"".$uplink."\">".$uplink_text."</a>";
				$html.="	</div>";
			}
			$html.="</div>";
			$html.="<div id=\"subcomments_".$mess->cm_id."\" class=\"subcomments_visible\">".$this->renderComments($mess->cm_id)."</div>";
		}
		return $html;
	}
	public function renderCommentForm($parent_id=0, $_start_with=0) {
		$html="";
		if ($this->checkACL("write")) {
			$parent_id=Request::getInt("parent_id",0);
			$title=Request::getSafe("comm_title","");
			$text=Request::getSafe("comm_text","");
			$nickname=Request::getSafe("comm_nickname","");
			$email=Request::getSafe("comm_email","");
			$cm_cat	= Request::getInt('cm_cat',0);
			$cm_type= Request::getInt('cm_type',0);
//			$renderSelect=true;
			if ($parent_id)	{
				$pcomm=$this->getComment($parent_id);
				if ($pcomm) {
					$opponent=User::getNicknameFor($pcomm->cm_uid);
					if (!$opponent) $opponent=Text::_("Anonymous");
					$parent_text="<img width=\"1\" height=\"1\" onclick=\"clearCommentAnswerTo()\" src=\"/images/blank.gif\" alt=\"\" title=\"\" class=\"clearAnswerTo\">".Text::_("Answer to comment")."<a href=\"#comment".$pcomm->cm_id."\"> ".$opponent." (".Date::fromSQL($pcomm->cm_date).")</a>:";
				}	else {
					$parent_id=0;
					$parent_text=Text::_("Leave comment").":";
				}
//				$renderSelect=false;
			} else $parent_text=Text::_("Leave comment").":";
			$html.="
			<div id=\"newCommentForm\"><a name=\"commentEditor\"></a>
				<div id=\"commentLabel\" class=\"title\">".$parent_text."</div>
				<form action=\"".Router::_("index.php")."\" method=\"post\">
					<input type=\"hidden\" id=\"answerCommentLabel\" value=\"".Text::_("Answer to comment")."\" /> 
					<input type=\"hidden\" id=\"cleanCommentLabel\" value=\"".Text::_("Leave comment")."\" /> 
					<input type=\"hidden\" name=\"module\" value=\"".$this->module."\" /> 
					<input type=\"hidden\" name=\"view\" value=\"".$this->view."\" />
					<input type=\"hidden\" name=\"layout\" value=\"".$this->layout."\" /> 
					<input type=\"hidden\" name=\"task\" value=\"saveComment\" /> 
					<input type=\"hidden\" name=\"psid\"	value=\"".$this->obj_id."\" /> 
					<input type=\"hidden\" id=\"parent_id\" name=\"parent_id\" value=\"".$parent_id."\" />";
			if (!User::getInstance()->isLoggedIn()) {
				$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("Nickname").": ", 0, "", "col-md-3"); 
				$html.="	<div class=\"col-md-9\">".HTMLControls::renderInputText("comm_nickname",$nickname,50,50)."</div></div>"; 
				$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("E-mail").": ", 0, "", "col-md-3"); 
				$html.="	<div class=\"col-md-9\">".HTMLControls::renderInputText("comm_email",$email,50,50)."</div></div>"; 
			}
			if ($this->vote_obj&&$this->checkACL("vote")){
				$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("Vote this comment").": ", 0, "", "col-md-3"); 
				$html.="	<div class=\"col-md-9\">".HTMLControls::renderSelect("vote_obj", "", false, false, SpravStatic::getCKArray("vote_vals"),false,false)."</div></div>"; 
			}
			$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("Theme").": ", 0, "", "col-md-3"); 
			$html.="	<div class=\"col-md-9\">".HTMLControls::renderInputText("comm_title",$title,50,250)."</div></div>";
			if(!$parent_id)	{
//				$html.="	<div id=\"com_selectors\">"; 
				if(is_array($this->_types) && count($this->_types)) {
					$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("Comment type").": ", 0, "", "col-md-3");
					$html.="	<div class=\"col-md-9\">".HTMLControls::renderSelect('cm_type', 'cm_type', 'id', 'title', $this->_types,$cm_type)."</div></div>";
				}
				if(is_array($this->_categories)&&count($this->_categories)) {				
					$html.="	<div class=\"form_row row\">".HTMLControls::renderLabelField(false,Text::_("Comment categories").": ", 0, "", "col-md-3");
					$html.="	<div class=\"col-md-9\">".HTMLControls::renderSelect('cm_cat', 'cm_cat', 'id', 'title', $this->_categories,$cm_cat)."</div></div>";
				}				
//				$html.="	</div>";
			}
			if ($this->bbcode) Event::raise("bbcode.editor",array("element_id"=>"comm_text"));
			$html.="	<div class=\"form_row row\"><div class=\"col-md-12\">".HTMLControls::renderBBCodeEditor("comm_text", "comm_text", $text, 65, 10, "form-control")."</div></div>"; 

			if(!ACLObject::getInstance('commentsDisableCaptcha', false)->canAccess()) $html.=Event::raise("captcha.renderForm",array("module"=>$this->module));
			$html.="	<div class=\"buttons\"><input type=\"submit\" class=\"btn btn-info commonButton\" value=\"".Text::_('Send')."\" /></div>
				</form>
			</div>";
		}
		return $html;
	}
	public function togglePublished($comm_id){
		if ($this->checkACL("moderate")) {
			$sql="UPDATE #__".$this->tablename." SET cm_published=ABS(cm_published-1) WHERE cm_id='".$comm_id."'";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->query();
		} else return false;
	}
	public function toggleDeleted($comm_id){
		if ($this->checkACL("moderate")) {
			$sql="UPDATE #__".$this->tablename." SET cm_deleted=ABS(cm_deleted-1) WHERE cm_id='".$comm_id."'";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->query();
		} else return false;
	}
	public function updateRating($comm_id){
		if (!$this->checkACL("vote")) return false;
		$comm=$this->getComment($comm_id);
		if ($comm) {
			$author=$comm->cm_uid;
			if ($author) { 
				$direction = Request::getSafe('dir',"up");
				$params = array("module"=>$this->module,"view"=>$this->view,"element"=>"comment","psid"=>$comm_id,"direction"=>$direction, "author"=>$author);
				$data_voted=Event::raise("rating.check",$params);  // content.rating
				if (!siteConfig::$useMultiVote && $data_voted) {
					echo Text::_("Already voted");
				} else {
					if (User::checkFloodPoint()){
						echo Event::raise("rating.rendervotepanel",array("module"=>$this->module,"view"=>$this->view,"element"=>"comment","psid"=>$comm_id, "mess"=>Text::_("Flood found")));
					} else {
						Event::raise("rating.vote",$params); // content.rating
						if ($direction=="up") echo Text::_("Good"); else echo Text::_("Bad");
					}
				}
			}
		}	
	}
	private function getModerators() {
		$db=Database::getInstance();
		$sql="SELECT ca_r_id FROM #__comms_acl WHERE ca_grp_id=".$this->group_id." AND ca_flag=1 AND ca_action='moderate'";
		$db->setQuery($sql);
		$roles=$db->loadObjectList("ca_r_id");
		$emails="";
		if (count($roles)){
			$roles_str=implode(",",array_keys($roles));
			$sql="SELECT u_email FROM #__users WHERE u_role IN (".$roles_str.")";
			$db->setQuery($sql);
			$emails_arr=$db->loadObjectList("u_email");
			if (count($emails_arr)) $emails = implode(",",array_keys($emails_arr));
		} else
		Event::raise("comments.getModerators",array("module"=>$this->module,"view"=>$this->view,"psid"=>$this->obj_id), $emails);
		return $emails;
	}
	private function reminderForModerator($cid, $title, $text){
		$text=strip_tags($text);
		$to=$this->getModerators();
		if (!$to) $to=soConfig::$siteEmail;
		$link=Router::_("index.php?module=".$this->module."&view=".$this->view."&task=getComment&psid=".$this->obj_id."&comm_id=".$cid, false, true, 1, 2);
		$theme=Text::_("Message for moderator");
		$text=sprintf(Text::_("comment mail text"), Portal::getURI(), $title, $text, $link);
		aNotifier::addToQueue($to, $theme, $text); 
	}
}
?>
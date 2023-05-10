<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsControllerdefault extends SpravController {

	public function showPolls() {
		$psid	= $this->getPsid();
		$layout = $this->get('layout');
		$model = $this->getModel();
		$view = $this->getView();
		$view->addBreadcrumb(Text::_('Polls'),"index.php?module=polls");
		$this->checkACL("viewPollsAllResults");

		$pollsCount = $model->getPollsCount();
		$paginator = $model->createPaginator($view, $pollsCount);
		$polls = $model->getPolls();
		$items = $model->getItems($polls);
		$view->assign("setVoteButton",false);
		$view->assign("polls",$polls);
		$view->assign("items",$items);
		$pg_link="index.php?module=polls&amp;psid=".$psid;
		$paginator->buildPagePanel($pg_link,"");
	}
	public function showPoll() {
		$psid	= $this->getPsid();
		if($psid) {
			$layout = $this->get('layout');
			$model = $this->getModel("polls");
			$view = $this->getView();
			$view->addBreadcrumb(Text::_('Polls'),"index.php?module=polls");
			$this->checkACL("viewPollsAllResults");
			$poll = $model->getPoll($psid);
			if(is_object($poll)){
				$items = $model->getItems(array($poll));
				$view->assign("setVoteButton",false);
				$view->assign("poll",$poll);
				$view->assign("items",$items);
			} else $this->setRedirect("index.php?module=polls", Text::_("Page not found"), 404);
		} else $this->setRedirect("index.php?module=polls", Text::_("Page not found"), 404);
	}
	public function ajaxgetVotePanel() {
		$psid=Request::getSafe('psid',0);
		$model = new Polls();
		$poll = $model->getPollByItem($psid);	
		
	}
	
	public function ajaxPollVote() {
		$psid=Request::getSafe('psid',0);
		$model = new Polls();
		$poll = $model->getPollByItem($psid);	
		
		if ($poll) {
			if ($model->allreadyVoted($poll)) {
				$html = "<p class=\"poll_error\">".Text::_("You have already voted in this poll")."</p>";
			} else {
				if ($this->checkACL("pollsModuleVote",0)) {
					if ($model->votePoll($psid)) {
						$model->setCookieLag($poll);
						$html = "<p class=\"poll_message\">".Text::_("Thank you for your vote")."</p>";
					} else $html = "<p class=\"poll_error\">".Text::_("Error while polling")."</p>";
				} else $html = "<p class=\"poll_error\">".Text::_("You cannot vote in polls")."</p>";
			}
			$items= $model->getItems($poll->p_id);	
			$html.=Widget::getInstance("polls")->renderPoll($poll,$items);
		} else $html = "<p class=\"poll_error\">".Text::_("Server Request error")."</p>";
		echo $html;
	}
}

?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsWidget extends Widget {
	protected $_requiredModules = array("polls");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Poll_ID", "table_select", "0", false, "SELECT p_id AS fld_id, p_title AS fld_name FROM #__polls ORDER BY fld_name");
		$this->addParam("Poll_results", "boolean", 1);
		$this->addParam("All_polls_button", "boolean", 1);
		$this->addParam("Show_bars", "boolean", 1);
		$this->addParam("Bar_color_hex", "string", "#0000FF");
	}
	public function render() {
		$widgetHTML="";
		$poll_id = intval($this->getParam("Poll_ID"));
		$model = new Polls();
		$poll = $model->getPoll($poll_id);
		$items = $model->getItems($poll_id);
		if (($poll)&&($items)&&(count($items))) {
			$widgetHTML.=$this->renderPoll($poll, $items);
			$color=$this->getParam("Bar_color_hex");
			$widgetHTML.=HTMLControls::renderHiddenField("barcolor_".$poll_id,$color);
		}
		return $widgetHTML;
	}
	public function renderPoll($poll,$items){
		$url=Router::_("index.php");
		$all_polls_button = intval($this->getParam("All_polls_button"));
		$show_bars = intval($this->getParam("Show_bars"));
		$poll_results = intval($this->getParam("Poll_results"));
		$total=0;
		$color="background:".$this->getParam("Bar_color_hex").";";
		$_html="<div class=\"poll\" id=\"wpoll_".$poll->p_id."\">";
		$_html.="<p class=\"poll_title\">".$poll->p_title."</p>";
		$_html.="<form name=\"wpoll\" method=\"post\" action=\"".$url."\">";
		$_html.="<input type=\"hidden\" value=\"polls\" name=\"module\" />";
		foreach($items as $item) {
			$total=$total+$item->pi_hits;
		}
		foreach($items as $item) {
			$percentage=0;	if ($total) $percentage=round(100*($item->pi_hits)/$total, 2);
			$_html.="<div><input class=\"vote_radio\" type=\"radio\" name=\"voteid[]\" id=\"voteid".$item->pi_id."\" value=\"".$item->pi_id."\" />";
			$_html.="<label class=\"vote_label\" for=\"voteid".$item->pi_id."\">".htmlspecialchars($item->pi_text)."</label></div>";
			if($show_bars) {
				$_html.="<div class=\"pollbar\">";
				$_html.="<div class=\"percentage\" style=\"".$color.";width:".round((0.8*$percentage),0)."%\"></div>&nbsp;".$percentage."%";
				$_html.="</div>";
			}
		}
		$_html.="</form>";
		if($poll_results) $_html.="<p class=\"total_votes\">".Text::_("Total voted").": ".$total."</p>";
		$_html.="<div class=\"vote_button btn btn-group clearfix\">";
		if($all_polls_button) $_html.="<input style=\"float:left;\" type=\"button\" class=\"commonButton btn btn-info\" value=\"".Text::_("All polls")."\"  onclick=\"javascript:document.location.href='".Router::_("index.php?module=polls")."'; return false;\" />";
		$_html.="<input type=\"button\" class=\"commonButton btn btn-info\" value=\"".Text::_("Vote")."\" onclick=\"votePoll(".$poll->p_id."); return false;\" />";
		$_html.="</div>";
		$_html.="</div>";
		return $_html;
	}
	
}
?>
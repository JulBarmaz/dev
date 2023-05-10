<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("articleId", "table_select", 0, false, "SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY fld_name");
	}
	public function render() {
		$psid = $this->getParam('articleId');
		$model = $this->getModel('article', 'article');
		$art = false;
		$art = $model->getArticle($psid); 
		if ($art) { 
			Event::raise('content.prepare', array("clean_all"=>"1"), $art->a_text);
			$articleHTML = $art->a_text; 
		}
		else { $articleHTML = "<b>".Text::_('Article not found: ').$alias."(".$psid.")</b>";	}
		return $articleHTML;
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


defined ( '_BARMAZ_VALID' ) or die ( "Access denied" );

class defaultViewitems extends SpravView {
	public function renderChangeForm($psid, $data) {
		$html = '';
		if ($psid) {
			$html .= "<div><form action=\"index.php\" method=\"post\">";
			$html .= HTMLControls::renderHiddenField ( 'module', 'article' );
			$html .= HTMLControls::renderHiddenField ( 'view', 'items' );
			$html .= HTMLControls::renderHiddenField ( 'task', 'changeDate' );
			$html .= HTMLControls::renderHiddenField ( 'psid', $psid );
			$html .= "<p>" . Text::_ ( "Change date publicity article" ) . "  " . $data->a_title . "</p>";
			$html .= HTMLControls::renderLabelField ( 'a_date', Text::_ ( "Set new date" ) );
			$html .= HTMLControls::renderDateTimeSelector ( 'a_date', $data->a_date );
			$html .= HTMLControls::renderButton ( 'save', 'apply', 'submit' );
			$html .= "</form></div>";
		}
		echo $html;
		parent::render ();
	}
}
?>
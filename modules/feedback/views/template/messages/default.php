<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (count($this->list)){
	foreach($this->list as $k=>$v) {
	echo "<div class=\"fb_message row\">
			<div class=\"fb_title col-md-12\">
				<div class=\"row\">
					<div class=\"fb_theme col-md-9\">".$v->f_theme."</div>
					<div class=\"fb_date  col-md-3\">".Date::fromSQL($v->f_date,false,true)."</div>
				</div>
			</div>";
	 echo " <div class=\"fb_text col-md-12\">".$v->f_text."</div>";

	 if($v->f_read){
	 	echo "<div class=\"fb_date2 col-md-12\">".Text::_("Date of read")." ".Date::fromSQL($v->f_readdate,false,true)."</div>";
	 	if (strlen($v->f_comments)>20) {
	 		echo "<div class=\"row\">";
	 		echo "<div class=\"fb_title\">".Text::_("Comment")."</div>";
	 		echo "<div class=\"fb_text\">".$v->f_comments."</div>";
	 		echo "</div>";
	 	}
	 } else {
		 echo "<div class=\"fb_date col-md-12\">".Text::_("Unread")."</div>";
	 }
	 echo "</div>";
	}
} else {
	echo Text::_("Messages not found");
}
?>

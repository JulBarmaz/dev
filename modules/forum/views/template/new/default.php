<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<h1 class=\"title\">".Text::_("New messages in themes")."</h1>";
echo "<div class=\"row\"><div class=\"col-md-12\"><div class=\"forums\"><div><table class=\"table table-bordered table-striped\">";
echo "<tr><th class=\"forum_title\">".Text::_("Theme")."</th>";
echo "<th width=\"20%\" class=\"forum_title\">".Text::_("Message")."</th>";
echo "</tr>";
if (count($this->themes)) {// выводим информацию о темах
	foreach($this->themes as $theme){
		if ($theme->t_deleted) $dclass=" deletedTheme"; 
		elseif (!$theme->t_enabled) $dclass=" disabledTheme";
		else $dclass="";
		echo "<tr class=\"rowTheme".$dclass."\"><td><a class=\"theme_title\" href=\"".Router::_("index.php?module=forum&view=theme&layout=lastpage&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : ""))."\">".$theme->t_theme."</a></td>";
		echo "<td align=\"center\">";
		echo "<a  title=\"".Text::_("View theme")."\" href=\"".Router::_("index.php?module=forum&view=theme&layout=lastpage&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : ""))."\">".Date::fromSQL($theme->p_date)."</a>";
		echo "<br />".Text::_("from")." "
			."<a title=\"".Text::_("Profile")."\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$theme->p_author_id)."\">".$theme->u_nickname."</a>"
			."</td>";
	}
}
echo "</table></div></div></div></div>";
?>
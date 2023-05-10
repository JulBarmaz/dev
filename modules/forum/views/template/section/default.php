<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"main_forum_panel\">";
if (User::getInstance()->isLoggedIn()) echo "<a class=\"btn btn-info\" href=\"".Router::_("index.php?module=forum&view=new")."\">".Text::_("New messages")." : ".$this->newposts."</a>"; 
echo"</div>";
if ($this->section) { // выводим информацию о родительском форуме
	// Если нужно то можно оверрайдить метатеги
//	Portal::getInstance()->setTitle( Text::_("Forum")." - ".($this->section->f_meta_title ? $this->section->f_meta_title : $this->section->f_name).($this->page > 1 ? " - ".Text::_("Page")." ".$this->page : "") );
	echo "<h1 class=\"title\">".$this->section->f_name."</h1>";
	echo "<div class=\"main_forum_body\">".$this->section->f_description."</div>";
} else {
	echo "<h1 class=\"title\">".Text::_("Forums")."</h1>";
}
if(!User::getInstance()->isLoggedIn())	echo "<p class=\"warning\">".Text::_("For posting log in or register")."</p>";

if (count($this->sections)) { // выводим информацию о форумах
	echo "<div class=\"row\"><div class=\"col-md-12\"><div class=\"forums\"><div><table class=\"table table-bordered table-striped\">";
	if ($this->section)	echo "<tr><th colspan=\"4\" class=\"sub_forum_title\">".Text::_("Subforums")."</th></tr>";
	echo "<tr><th colspan=\"2\" class=\"forum_title\">".Text::_("Name")."/".Text::_("Description")."</th>";
	echo "<th width=\"15%\" class=\"forum_title\">".Text::_("Subforums")."</th>";
	echo "<th width=\"15%\" class=\"forum_title\">".Text::_("Themes")."</th>";
	echo "</tr>";
	foreach($this->sections as $forum){
		if (in_array($forum->f_id,$this->allowed_ids)) {
			echo "<tr><td colspan=\"2\"><a class=\"forum_title\" href=\"".Router::_("index.php?module=forum&view=section&psid=".$forum->f_id.($forum->f_alias ? "&alias=".$forum->f_alias : ""))."\">".$forum->f_name."</a><br />";
			echo $forum->f_description."</td>";
			echo "<td align=\"center\">".$forum->f_forums."</td>";
			echo "<td align=\"center\">".$forum->f_themes."</td>";
			echo "</tr>";
		}
	}
	echo "</table></div></div></div></div>";
}
if ($this->section) {
	echo "<div class=\"row\"><div class=\"col-md-12\"><div class=\"forum_panel btn-group\">";
	if ($this->canWrite) {
		echo "<a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=forum&task=modifyTheme&psid=".$this->section->f_id."&page=".$this->page)."\">".Text::_("Create theme")."</a>";
	}
	echo "<a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=forum")."\">".Text::_("All forums")."</a>";
	echo "</div></div></div>";
}

if ($this->section) {
	echo "<div class=\"row\"><div class=\"col-md-12\"><div class=\"forums\"><div><table class=\"table table-bordered table-striped\">";
	echo "<tr><th colspan=\"4\" class=\"sub_forum_title\">".Text::_("Themes")."</th></tr>";
	echo "<tr><th class=\"forum_title\">".Text::_("Theme")."</th>";
	echo "<th width=\"20%\" class=\"forum_title\">".Text::_("Last post")."</th>";
	echo "<th class=\"forum_title\">".Text::_("Answers")."</th>";
	echo "<th class=\"forum_title\">".Text::_("Views")."</th>";
	echo "</tr>";
	if (count($this->themes)) {// выводим информацию о темах
		foreach($this->themes as $theme){
			if ($theme->t_deleted) $dclass=" deletedTheme"; 
			elseif (!$theme->t_enabled) $dclass=" disabledTheme";
			else $dclass="";
			echo "<tr class=\"rowTheme".$dclass."\"><td><a class=\"theme_title\" href=\"".Router::_("index.php?module=forum&view=theme&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : ""))."\">".$theme->t_theme."</a></td>";
//			echo "<td align=\"center\">".Date::fromSQL($theme->t_date)."<br />".Text::_("from")." ".$theme->u_nickname."</td>";
			echo "<td align=\"center\">";
			echo "<a title=\"".Text::_("View theme")."\" href=\"".Router::_("index.php?module=forum&view=theme&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : "").($theme->t_posts ? "&layout=lastpage" : ""))."\">".Date::fromSQL($theme->post_date)."</a>";
			echo "<br />".Text::_("from")." "
			."<a title=\"".Text::_("Profile")."\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$theme->post_author_id)."\">".$theme->post_author."</a>"
			."</td>";
			echo "<td align=\"center\">".$theme->t_posts."</td>";
			echo "<td align=\"center\">".$theme->t_views."</td></tr>";
		}
	}
	echo "</table></div></div></div></div>";
}
?>
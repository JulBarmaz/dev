<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<h1 id="articleTitleRead"><?php echo Text::_("Tree"); ?></h1>
<?php if (count($this->brokenParents)>0) { ?>
<div class="articleAchtungRead">
	<div class="articleAchtungTitle"><?php echo Text::_("Articles with broke parent links"); ?></div>
	<ul>
<?php 
	foreach($this->brokenParents as $brokenLink) {
		$href=Router::_("index.php?module=article&amp;view=read&amp;psid=".$brokenLink->a_id);
		echo "<li><a href=\"\">".$brokenLink->a_title."</a></li>";
	}
?>
</ul></div>	
<?php } ?>
<div id="articleTextRead"><?php echo $this->tree; ?></div>
<?php 
Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
Portal::getInstance()->AddScriptDeclaration('$(document).ready(function(){ $("#article_tree").treeview({  animated: "fast", collapsed: false, unique: true }); });'); 
?>
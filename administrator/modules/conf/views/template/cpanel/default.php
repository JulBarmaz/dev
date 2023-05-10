<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$uri = Portal::getInstance()->getTemplateURI();
?>
<div class="cpanel_ext container">
	<?php foreach ($this->icons_arr as $rk=>$_row) : ?>
	<div class="row">
		<?php foreach ($_row as $_k=>$_cell) : ?>
		<?php if(isset($this->disabled_modules[$_cell["module"]])) {
			$_cell["link"] = "#";
			$_cell["class"].= ($_cell["class"] ? " " : "")."disabled-icon";
		}
		?>
		<div class="col-xss-6 col-xs-4 col-sm-2 col-md-1"><div class="cpanel">
			<a title="<?php echo Text::_($_cell["title"]); ?>" class="<?php echo $_cell["class"]; ?>" href="<?php echo Router::_($_cell["link"])?>">
				<img alt="<?php echo Text::_($_cell["title"]); ?>" src="<?php echo $uri."images/cpanel/".$_cell["icon"]; ?>" />
				<br />
				<?php echo Text::_($_cell["title"]); ?>
			</a>
		</div></div>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
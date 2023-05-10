<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="install-manager rounded-pan rounded-pan-medium">
	<h4 class="title">
	<?php 
		if ($this->mode=="install") echo Text::_("Package instalation results"); 
		if ($this->mode=="uninstall") echo Text::_("Package uninstall results"); 
		?>
	</h4>
	<div class="installer">
		<?php
		if (count($this->log)) {
			foreach($this->log as $key=>$msg){
				echo "<p class=\"log_".$msg["type"]."\">".$msg["text"]."</p>";
			}		
		}
		if ($this->result)	echo "<p class=\"log_message\">".$this->msg."</p>";
		else echo "<p class=\"log_error\">".$this->msg."</p>";
		?>
	</div>
	<div class="buttons"><a class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=installer"); ?>"><?php echo Text::_("Installer"); ?></a></div>
</div></div></div></div>
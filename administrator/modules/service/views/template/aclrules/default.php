<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");?>

<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Check ACL objects"); ?></h4>
	<div class="row">
		<div class="col-md-12"><?php 
		if(count($this->message)){
			foreach($this->message as $message) {
		 		echo "<p class=\"error\">".$message."</p>";
			}
		} else {
			echo "<p class=\"ok\">".Text::_("Everything is ОК")."</p>";
		}	
		?></div>
	</div>
</div></div></div></div>
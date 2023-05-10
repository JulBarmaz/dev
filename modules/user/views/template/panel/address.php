<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$address=$this->address;
if ($address["psid"]) $title=Text::_("Changing data"); else  $title=Text::_("Adding data"); 
?>
<form action="<?php echo Router::_("index.php"); ?>" method="post" name="addressForm" id="addressForm">
	<input type="hidden"  name="task" value="saveAddress" />
	<input type="hidden"  name="module" value="user" />
	<input type="hidden"  name="view" value="panel" />
	<input type="hidden"  name="psid" value="<?php echo $address["psid"];?>" />
	<div class="userAddress">
		<h3 class="title" colspan="3"><?php echo $title;?></h3>
		<div class="addressPanel"><div class="row"><div class="col-sm-5"><?php echo HTMLControls::renderLabelField("", Text::_("Address type").":");?></div><div class="col-sm-7"><?php echo Address::renderTypeSelector($address['type_id'])?></div></div></div>
		<?php echo Address::renderEditor($address, false, true);?>
		<div class="buttons">
			<input class="commonButton btn btn-info" onclick="submitAddress();" type="submit" name="submit"  value="<?php echo Text::_("Save"); ?>" />
		</div>
	</div>
</form>

<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");?>

<?php
if(!$this->error_message) {
	$script="
	function processResize(next_start) {
		ajaxShowActivity();
		$.ajax({
			url : siteConfig['siteUrl']+'index.php',
	 		type: 'POST',
			data:({
				type:'module',
				option:'ajax',
				module:'service',
				view:'imageprocessor',
				task:'processResize',
				enabled_only:".$this->enabled_only.",
				skip_deleted:".$this->skip_deleted.",
				force_from_source:".$this->force_from_source.",
				i_understand:".$this->i_understand.",
				start: next_start,
				field_key:'".$this->field_key."'
			}),
			dataType:'json',
			success: function (response) {
				$('#mass_resize_result').append('<span class=\"'+response.status+'\">'+response.message+'</span>');
				if (response.status==='processing' && response.next_start > 0) {
					if(response.error_message !='') $('#mass_resize_result').append('<span class=\"error\">'+response.error_message+'</span>');
					setTimeout('processResize(\''+response.next_start+'\')', 1000);
				} else if (response.status==='aborted'){
					ajaxHideActivity();
				} else if (response.status==='finished'){
					ajaxHideActivity();
				} else{
					alert('Error');
					ajaxHideActivity();
				}
			}
		});
	}
	$(document).ready(function(){
		processResize(1);
	});
	";
	Portal::getInstance()->addScriptDeclaration($script);
}
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager image-processor rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Image processor"); ?></h4>
	<?php if($this->image_object) { ?><h4 class="title"><?php echo $this->image_object["title"]." => ".$this->image_object["field_title"]." (".$this->image_object["width"]."x".$this->image_object["height"].")"; ?></h4><?php } ?>
	<p><?php echo Text::_("Skip deleted");?>: <?php echo $this->skip_deleted ? Text::_("Y") : Text::_("N");?></p>
	<p><?php echo Text::_("Enabled only");?>: <?php echo $this->enabled_only ? Text::_("Y") : Text::_("N");?></p>
	<p><?php echo Text::_("Force resize from source");?>: <?php echo $this->force_from_source ? Text::_("Y") : Text::_("N");?></p>
	<?php if($this->error_message) { ?>
	<div class="row">
		<div class="col-md-12">
			<div class="message_error">
			<?php echo $this->error_message; ?>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<h4 class="title"><?php echo Text::_("Results"); ?>:</h4>
	<div class="row">
		<div class="col-md-12">
			<div id="mass_resize_result"></div>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-md-12">
			<div class="buttons">
				<a href="/administrator/index.php" class="commonButtonbtn btn btn-info"><?php echo Text::_("Admin panel"); ?></a>
				<a href="/administrator/index.php?module=service&view=imageprocessor" class="commonButtonbtn btn btn-info"><?php echo Text::_("Image processor"); ?></a>
			</div> 
		</div>
	</div>
</div></div></div></div>

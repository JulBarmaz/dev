<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");?>

<?php
$script="
function massResizeOnSubmit(){
	if($('#i_understand_1').prop('checked')) return true;
	else return false;
}
function massOptimizeOnSubmit(){
	if($('#i_understand_2').prop('checked')) return true;
	else return false;
}
";

Portal::getInstance()->addScriptDeclaration($script);
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager image-processor rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Image processor"); ?></h4>
	<p align="center" class="red"><?php echo Text::_("Beta version of module is for testing ONLY").".<br />".Text::_("We dont quarantee the correct working of your CMS while using it")."."; ?></p>
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#mass_resize"><?php echo Text::_("Mass resize"); ?></a></li>
		<li><a data-toggle="tab" href="#mass_optimize"><?php echo Text::_("Mass optimize"); ?></a></li>
		<li><a data-toggle="tab" href="#mass_webp"><?php echo Text::_("Optimize image webp"); ?></a></li>
	</ul>
	<div class="tab-content">
		<div id="mass_resize" class="tab-pane fade in active">
			<form id="frmImageProcessor1" name="frmImageProcessor" action="index.php" method="post" onsubmit="return massResizeOnSubmit();">
				<h4 class="title"><?php echo Text::_("Select field for processing"); ?></h4>
				<div class="row">
					<div class="col-md-12"><?php 
					$i=0;
					foreach($this->objects as $k=>$o){
						$i++;
						echo "<div class=\"radio\"><label><input id=\"field_".$i."\" name=\"field_key\" type=\"radio\" value=\"".$k."\" required />".$o["title"]." => ".$o["field_title"]." (".$o["width"]."x".$o["height"].")</label></div>";
					}
					?></div>
				</div>
				<h4 class="title"><?php echo Text::_("Options"); ?></h4>
				<div class="row">
					<div class="col-md-12">
						<div class="checkbox"><input type="checkbox" id="skip_deleted" name="skip_deleted" value="1"><label for="skip_deleted"><?php echo Text::_("Skip deleted");?></label></div>
						<div class="checkbox"><input type="checkbox" id="enabled_only" name="enabled_only" value="1"><label for="enabled_only"><?php echo Text::_("Enabled only");?></label></div>
						<div class="checkbox"><input type="checkbox" id="force_from_source" name="force_from_source" value="1"><label for="force_from_source"><?php echo Text::_("Force resize from source");?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="checkbox"><input type="checkbox" id="i_understand_1" name="i_understand" value="1" /><label class="red" for="i_understand_1"><?php echo Text::_("I understand that this operation may cause damage to files and database");?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="buttons">
							<input type="submit" class="commonButtonbtn btn btn-info" value="<?php echo Text::_("Start"); ?>">
							<input type="hidden" name="module" value="service">
							<input type="hidden" name="view" value="imageprocessor">
							<input type="hidden" name="task" value="processResize">
						</div> 
					</div>
				</div>
			</form>
		</div>
		<div id="mass_optimize" class="tab-pane fade">
			<form id="frmImageProcessor2" name="frmImageProcessor" action="index.php" method="post" onsubmit="return massOptimizeOnSubmit();">
				<div class="row">
					<div class="col-md-12">
						
					</div>
				</div>
				<h4 class="title"><?php echo Text::_("Under construction"); ?></h4>
				<div class="row">
					<div class="col-md-12">
						<div class="checkbox"><input type="checkbox" id="i_understand_2" name="i_understand" value="1" /><label class="red" for="i_understand_2"><?php echo Text::_("I understand that this operation may cause damage to files and database");?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="buttons">
							<input type="submit" class="commonButtonbtn btn btn-info" value="<?php echo Text::_("Start"); ?>">
							<input type="hidden" name="module" value="service">
							<input type="hidden" name="view" value="imageprocessor">
							<input type="hidden" name="task" value="startOptinmize">
						</div> 
					</div>
				</div>
			</form> 
		</div>

	<div id="mass_webp" class="tab-pane fade in active">
			<form id="frmImageProcessor3" name="frmImageProcessor" action="index.php" method="post" onsubmit="return massWebpOnSubmit();">
				<h4 class="title"><?php echo Text::_("Select object for processing"); ?></h4>
				<div class="row">
					<div class="col-md-12"><?php 
					//$i=0;
					//Util::showArray($this->objects);
					foreach($this->objects as $k=>$o){
						$_arr[$k]=$o["title"]." => ".$o["field_title"]." (".$o["width"]."x".$o["height"].")";
						//$i++;
						//echo "<div class=\"radio\"><label><input id=\"field_".$i."\" name=\"field_key\" type=\"radio\" value=\"".$k."\" required />".$o["title"]." => ".$o["field_title"]." (".$o["width"]."x".$o["height"].")</label></div>";
					}
					echo HTMLControls::renderSelect("field_key", "field_1", false, false, $_arr,0,false,'setWebpProc(this)');
					?></div>
				</div>
				<h4 class="title"><?php echo Text::_("Options"); ?></h4>
				<div class="row">
					<div class="col-md-12">
					  <div id="selected_item"></div>
						<div class="checkbox"><input type="checkbox" id="w_skip_deleted" name="w_skip_deleted" value="1"><label for="skip_deleted"><?php echo Text::_("Skip deleted");?></label></div>
						<div class="checkbox"><input type="checkbox" id="w_enabled_only" name="w_enabled_only" value="1"><label for="enabled_only"><?php echo Text::_("Enabled only");?></label></div>
						<div class="checkbox"><input type="checkbox" id="w_force_from_source" name="w_force_from_source" value="1"><label for="force_from_source"><?php echo Text::_("Force resize from source");?></label></div>
						<div class="checkbox"><input type="checkbox" id="w_delete_source" name="w_delete_source" value="1"><label for="force_from_source"><?php echo Text::_("Delete source file");?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="checkbox"><input type="checkbox" id="i_understand_w" name="i_understand_w" value="1" /><label class="red" for="i_understand_1"><?php echo Text::_("I understand that this operation may cause damage to files and database");?></label></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="buttons">
							<input type="submit" class="commonButtonbtn btn btn-info" value="<?php echo Text::_("Start"); ?>">
							<input type="hidden" name="module" value="service">
							<input type="hidden" name="view" value="imageprocessor">
							<input type="hidden" name="task" value="processResize">
						</div> 
					</div>
				</div>
			</form>
		</div>
	</div>
</div></div></div></div>
<?php 
//Util::showArray($this->objects); 
?>
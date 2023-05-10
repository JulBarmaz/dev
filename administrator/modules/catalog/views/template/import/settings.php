<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$headers=Session::getVar("CATIMPHDR");
$fields=$this->fields;
$fields_data=$this->fields_data;
$af_wlists=$this->af_wlists;
$form=new aForm("import_form", "post", "index.php?module=catalog&view=import",false);
$form->addInput(array("ID"=>"step", "TYPE"=>"hidden", 	"NAME"=>"step", "VAL"=>2));
$form->addInput(array("ID"=>"portions", "TYPE"=>"select", "NAME"=>"portions", "VAL"=>"500", "OPTIONS"=>array("100"=>"100","500"=>"500","1000"=>"1000","5000"=>"5000")));
$form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit",	"VAL"=>Text::_("Continue"),	"ID"=>"doit", "NAME"=>"doit","ONCLICK"=>"return csv_import_check_fields();"));
$form->StartLayout();
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Import catalog data");?></h4>
	<div id="modify-wrapper">
		<div class="row"><div class="col-md-12">
			<?php echo HTMLControls::renderLabelField(false, Text::_("Total records in temporary table").": ".$this->count_records); ?>
		</div></div>
		<h4 class="title"><?php echo Text::_("Import settings");?></h4>
		<div class="row">
			<div class=" col-sm-4 col-xs-9"><?php echo HTMLControls::renderLabelField("clean_tables", Text::_("Clean tables before import").":");?></div>
			<div class=" col-sm-8 col-xs-3"><?php echo HTMLControls::renderCheckbox("clean_tables", 0, 1); ?></div>
		</div>
		<div class="row">
			<div class=" col-sm-4 col-xs-9"><?php echo HTMLControls::renderLabelField("overwrite_data", Text::_("Overwrite data").":");?></div>
			<div class=" col-sm-8 col-xs-3"><?php echo HTMLControls::renderCheckbox("overwrite_data", 1, 1); ?></div>
		</div>
		<div class="row">
			<div class=" col-sm-4 col-xs-9"><?php echo HTMLControls::renderLabelField("insert_new_data", Text::_("Insert new data").":");?></div>
			<div class=" col-sm-8 col-xs-3"><?php echo HTMLControls::renderCheckbox("insert_new_data",1,1); ?></div>
		</div>
		<div class="row">
			<div class=" col-sm-4"><?php echo HTMLControls::renderLabelField("portions", Text::_("Import portions").":");?></div>
			<div class=" col-sm-8"><?php $form->renderInputPart("portions"); ?></div>
		</div>
		<div class="row">
			<div class=" col-sm-4"><?php echo HTMLControls::renderLabelField("parent_group_id", Text::_("Use parent group").":");?></div>
			<div class=" col-sm-8"><?php echo $this->groupsTree->getTreeHTML(0, "select", "parent_group_id", "parent_group_id", 0, "", "singleSelect form-control"); ?></div>
		</div>
		<div class="row">
			<div class=" col-sm-4 col-xs-9"><?php echo HTMLControls::renderLabelField("disable_all_goods", Text::_("Disable all goods first").":");?></div>
			<div class=" col-sm-8 col-xs-3"><?php echo HTMLControls::renderCheckbox("disable_all_goods", 0, 1); ?></div>
		</div>
		<h4 class="title"><?php echo Text::_("Make fields compliance");?></h4>
		<div class="row"><div class=" col-sm-12"><div class="table-scroller-wrapper">
			<table class="table table-bordered table-hover table-condensed">
				<tr>
					<th width="35%" align="center"><?php echo Text::_("Field in CSV");?> : </th>
					<th width="35%" align="center"><?php echo Text::_("Field in base");?> : </th>
					<th width="15%" align="center"><?php echo Text::_("Update field");?> : </th>
					<th width="15%" align="center"><?php echo Text::_("Update field source list");?> : </th>
				</tr>
				<?php 
				foreach($headers as $key=>$val){
					if(in_array($val, $this->hidden_fields)) continue;
					echo "<tr>";	
					echo "	<td align=\"right\">".$val." : </td>";
					if(array_key_exists($val, $fields)) $sel_val=$val; else $sel_val=0;
					echo "	<td class=\"fcell\">".HTMLControls::renderSelect($key, $key, false, false, $fields,$sel_val)."</td>";
					echo "	<td align=\"center\">";
					echo HTMLControls::renderCheckbox("update_field_".$key,1,1);
					echo "	</td>";
					echo "	<td align=\"center\">";
					if(array_key_exists($val, $af_wlists) && $af_wlists[$val]) echo HTMLControls::renderCheckbox("update_list_".$key,1,1); else echo "&nbsp;";
					echo "	</td>";
					echo "</tr>";	
				}
				?>
			</table>
		</div></div></div>
		<h4 class="title"><?php echo Text::_("Make fields defs values");?></h4>
		<div class="row"><div class=" col-sm-12"><div class="table-scroller-wrapper">
			<table class="table table-bordered table-hover table-condensed">
				<tr>
					<th align="center" width="40%"><?php echo Text::_("Field in base");?> : </th>
					<th align="center" width="40%"><?php echo Text::_("Value");?> : </th>
					<th align="center" width="20%"><?php echo Text::_("Update field");?> : </th>
				</tr>
				<?php 
				foreach($fields_data as $key=>$val){
					echo "<tr>";	
					echo "	<td align=\"right\">".$val["title"]." : </td>";
					switch ($val["type"]){
						case "ck_select":
							echo "	<td class=\"tcell\" align=\"left\">".HTMLControls::renderSelect($key, $key, false, false, $val["data"], $val["val"])."</td>";
						break;
						case "ch_select":
							echo "	<td class=\"gcell\" align=\"left\">".HTMLControls::renderSelect($key, $key, "id", "name", $val["data"], $val["val"])."</td>";
						break;
						case "text":
						default:
							echo "	<td class=\"gcell\" align=\"left\">".HTMLControls::renderInputText($key, $val["val"],40,200, $key)."</td>";
						break;
					}
					echo "	<td align=\"center\">";
					echo HTMLControls::renderCheckbox("update_field_".$key,1,1);
					echo "	</td>";
					echo "</tr>";	
				}
				?>
			</table>
		</div></div></div>
		<div class="buttons">
			<?php $form->renderInputPart("step"); ?>
			<?php $form->renderInputPart("doit"); ?>
		</div>
	</div>
</div></div></div></div>
<?php $form->endLayout(); ?>
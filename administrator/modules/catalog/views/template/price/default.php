<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan">
	<h4 class="title"><?php echo Text::_("Price list parameters")?></h4>
	<form name="adminForm" action="index.php" method="post" target="_blank">
	<?php 
		$grpTree=$this->grpTree;
		$grp_html=$grpTree->getTreeHTML(0, "select", "parent_group", "", 0, "", "singleSelect");
		echo HTMLControls::renderHiddenField("option","print");
		echo HTMLControls::renderHiddenField("module","catalog");
		echo HTMLControls::renderHiddenField("view","price");
		echo HTMLControls::renderHiddenField("layout","list");
	?>
	<div class="row"><div class="col-sm-5">
		<?php echo HTMLControls::renderLabelField("price_setting","Price setting",1) ?>
	</div><div class="col-sm-7">
		<?php 
		if(count($this->listsets)){
			// выводим специализированный селект - может его потом засунуть в HTMLControls как отдельную фичу
			/*<select title="select">
			<option onmouseover="this.parentNode.title='aaa'" title="aaa">aaa</option>
			<option onmouseover="this.parentNode.title='bbb';">bbb</option>
			<option title="ccc">ccc</option>
			</select>
			class=\"singleSelect\"
			*/
			echo "<td><select id=\"p_id\" name=\"p_id\">";
			foreach($this->listsets as $set){
			//	echo "<option onmouseover=\"this.parentNode.title='".$set->p_comment."'\" title=\"'".$set->p_comment."'\" value=\"".$set->id."\">".$set->title."</option>";
				echo "<option title=\"'".$set->p_comment."'\" value=\"".$set->id."\">".$set->title."</option>";
			}
			echo "</select><span class=\"commonButton\" onclick=\"getPriceSettings('p_id');\">".Text::_("Set fields")."</span></td>";                 
			//echo "<td>".HTMLControls::renderSelect("price_setting", "price_setting", 'id', 'title', $this->listsets,1,0)."</td>";
		} else {
			echo HTMLControls::renderHiddenField('p_id',"");
		}
		?>	
	</div></div>
	<div class="row"><div class="col-sm-5">
		<?php echo HTMLControls::renderLabelField(false, Text::_("new name set")); ?>
	</div><div class="col-sm-7">
		<?php echo HTMLControls::renderInputText("p_name","",80); ?>
	</div></div>
	<div class="row"><div class="col-sm-5">
		<?php echo HTMLControls::renderLabelField(false, Text::_("Comment new set")); ?>
	</div><div class="col-sm-7">
		<?php echo HTMLControls::renderInputText("p_comment","",120); ?>
	</div></div>
	<div class="row"><div class="col-xs-3 col-sm-5">
		<?php echo HTMLControls::renderLabelField(false, Text::_("new set create")); ?>
	</div><div class="col-xs-9 col-sm-7">
		<?php echo HTMLControls::renderCheckbox("p_new"); ?>
	</div></div>
	<div class="row"><div class="col-sm-5"><?php echo HTMLControls::renderLabelField("p_template","price_template",1) ?></div>
	<div class="col-sm-7"><?php echo HTMLControls::renderInputText("p_template","",80);?></div></div>
	<div class="row"><div class="col-sm-5"><?php echo HTMLControls::renderLabelField("price_type","Price type",1) ?></div>
	<div class="col-sm-7"><?php echo HTMLControls::renderSelect("price_type", "price_type", false, false, SpravStatic::getCKArray("price_type"),1,0,"",0,""); ?></div></div>
	<div class="row"><div class="col-sm-5"><?php echo HTMLControls::renderLabelField("p_discount","Price discount",1) ?></div>
	<div class="col-sm-7"><?php echo HTMLControls::renderInputText("p_discount"); ?></div></div>
	<div class="row"><div class="col-sm-5"><?php echo HTMLControls::renderLabelField("parent_group","Begin from",1) ?></div>
	<div class="col-sm-7"><?php echo $grp_html; ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("enabled_only",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("enabled_only","Enabled only",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("break_by_groups",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("break_by_groups","Break by groups",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_thumbs",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_thumbs","Show thumbs",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_dimensions",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_dimensions","Show dimensions",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_pack_price",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_pack_price","Show pack price",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_volume_price",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_volume_price","Show volume price",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_weight",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_weight","Show weight",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_weight_price",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_weight_price","Show weight price",1) ?></div></div>
	<div class="row"><div class="col-xs-3 col-sm-5"><?php echo HTMLControls::renderCheckbox("show_company_info",1,1); ?></div>
	<div class="col-xs-9 col-sm-7"><?php echo HTMLControls::renderLabelField("show_company_info","Show company info",1) ?></div></div>
	<div class="row"><div class="col-sm-12">
		<?php echo HTMLControls::renderLabelField("p_head_colon","Add header",1) ?>
	</div></div>
	<div class="row"><div class="col-sm-12">
		<?php echo HTMLControls::renderBBCodeEditor("p_head_colon","p_head_colon"); ?>
	</div></div>
	<div class="row"><div class="col-sm-12">
		<?php echo HTMLControls::renderLabelField("p_foot_colon","Add footer",1) ?>
	</div></div>
	<div class="row"><div class="col-sm-12">
		<?php echo HTMLControls::renderBBCodeEditor("p_foot_colon","p_foot_colon"); ?>
	</div></div>
	<div class="buttons">			
		<?php	
			echo HTMLControls::renderButton("go",Text::_("Proceed"),"submit");
			echo HTMLControls::renderButton("saveparam",Text::_("Save sets"),"button","saveparam","commonButton btn btn-info","savePriceSettings();");
		?>
	</div>
	</form>
	
</div></div></div></div>
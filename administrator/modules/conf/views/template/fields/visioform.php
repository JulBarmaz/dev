<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$m_admin_side=$this->m_admin_side;
$m_module=$this->m_module;
$m_view=$this->m_view;
$m_layout=$this->m_layout;
if($m_admin_side==0) $texttype=Text::_("Front side");
else $texttype=Text::_("Admin side");
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="conf-manager rounded-pan">
	<h4 class="title"><?php echo Text::_("Editing parametres").' : '.$texttype.'. '.Text::_("module").': '.$m_module.', '.Text::_("view").': '.$m_view.', '.Text::_("layout").': '.$m_layout; ?></h4>
	<div id="modify-wrapper">
		<form method="post" action="index.php">
			<?php if($this->res) { ?>
			<div class="table-scroller-wrapper editMetaFields">
				<table class="table table-bordered table-hover table-condensed">
					<tr><th><?php echo Text::_("field"); ?></th>
					<th width="10%"><?php echo Text::_("Ordering"); ?></th>
					<th width="10%"><a onclick="toggleCheckboxes('m_show', '<?php echo Text::_("Change status"); ?>: <?php echo Text::_("m_show"); ?>');"><?php echo Text::_("m_show"); ?></a></th>
					<th width="10%"><?php echo Text::_("m_width"); ?></th>
					<th width="8%"><a onclick="toggleCheckboxes('m_input_view', '<?php echo Text::_("Change status"); ?>: <?php echo Text::_("m_input_view"); ?>');"><?php echo Text::_("m_input_view"); ?></a></th>
					<th width="8%"><?php echo Text::_("m_input_page"); ?><br /><?php echo HTMLControls::renderBalloonButton("Used in custom templates by admins and developers"); ?></th>
					<th width="8%"><a onclick="toggleCheckboxes('m_show_in_filter', '<?php echo Text::_("Change status"); ?>: <?php echo Text::_("m_show_in_filter"); ?>');"><?php echo Text::_("m_show_in_filter"); ?></a></th>
					<th width="8%"><a onclick="toggleCheckboxes('m_show_in_filter_ext', '<?php echo Text::_("Change status"); ?>: <?php echo Text::_("m_show_in_filter_ext"); ?>');"><?php echo Text::_("m_show_in_filter_ext"); ?></a><?php echo HTMLControls::renderBalloonButton("Used in custom templates by admins and developers"); ?></th>
					<th width="8%"><a onclick="toggleCheckboxes('m_strict_filter', '<?php echo Text::_("Change status"); ?>: <?php echo Text::_("m_strict_filter"); ?>');"><?php echo Text::_("m_strict_filter"); ?></a></th>
					<?php if(defined("_BARMAZ_TRANSLATE")){?>
					<th width="8%"><a onclick="toggleCheckboxes('m_translate_value', '<?php echo Text::_("Demand translate value"); ?>: <?php echo Text::_("Demand translate value"); ?>');"><?php echo Text::_("Demand translate value"); ?></a></th></tr>
					<?php } ?>
					<?php 
					$_arr_10=array();
					for($i10=0;$i10<11;$i10++) $_arr_10[$i10]=$i10; 
					foreach($this->meta->field as $id=>$fld){
						if (array_key_exists($fld, $this->res)) { 
							$val=$this->res[$fld];							
							$checked =" checked=\"checked\"";
							if($val->m_show==1) $m_viewlistCheck =" checked=\"checked\""; else $m_viewlistCheck ="";
							if($val->m_input_view==1) $m_viewmodCheck =" checked=\"checked\""; else $m_viewmodCheck ="";
							if(defined("_BARMAZ_TRANSLATE")){
							  if($val->m_translate_value==1) $m_viewTranslate =" checked=\"checked\""; else $m_viewTranslate="";
							}  
							?>
							<tr><td<?php echo ($this->meta->input_type[$id]=="hidden" ? " class=\"hidden_field\"" : ""); ?>><?php echo Text::_($this->m_meta_name[$this->m_meta_field[$val->m_field]]); ?><input type="hidden" name="m_id[<?php echo $val->m_id; ?>]" value="<?php echo $val->m_id; ?>" /></td>
							<th><input class="form-control" size="3" type="text" name="m_field_order[<?php echo $val->m_id; ?>]" value="<?php echo ((int)$this->meta->field_order[$id]*10); ?>" /></th>
							<th><input type="checkbox" class="m_show" name="m_show[<?php echo $val->m_id; ?>]" id="m_show_<?php echo $val->m_id; ?>" value="1" <?php echo ($val->m_show ? $checked : ""); ?> /></th>
							<th><input class="form-control" size="3" type="text" name="m_width[<?php echo $val->m_id; ?>]" value="<?php echo $val->m_width; ?>" /></th>
							<?php if($this->meta->input_type[$id]=="hidden"){ ?>
								<th>&nbsp;</th><th>&nbsp;</th>
							<?php } else { ?>
								<th><input type="checkbox" class="m_input_view" name="m_input_view[<?php echo $val->m_id; ?>]" id="m_input_view_<?php echo $val->m_id; ?>" value="1" <?php echo ($val->m_input_view ? $checked : ""); ?> /></th>
								<th><?php echo HTMLControls::renderSelect('m_input_page['.$val->m_id.']', "", "", "", $_arr_10,$val->m_input_page,false); ?></th>
							<?php } ?>
							<?php if($this->meta->field_is_method[$id] ||$this->meta->input_type[$id]=="hidden"){ ?>
								<th>&nbsp;</th><th>&nbsp;</th>
							<?php } else { ?>
								<th><input type="checkbox" class="m_show_in_filter" name="m_show_in_filter[<?php echo $val->m_id; ?>]" id="m_show_in_filter_<?php echo $val->m_id; ?>" value="1" <?php echo ($val->m_show_in_filter ? $checked : ""); ?> /></th>
								<th><input type="checkbox" class="m_show_in_filter_ext" name="m_show_in_filter_ext[<?php echo $val->m_id; ?>]" id="m_show_in_filter_ext_<?php echo $val->m_id; ?>" value="1" <?php echo($val->m_show_in_filter_ext ? $checked : ""); ?> /></th>
								<th>
								<?php if(
										($this->meta->input_type[$this->m_meta_field[$val->m_field]]=="select" && $this->meta->ch_table[$this->m_meta_field[$val->m_field]])
										|| 
										($this->meta->val_type[$this->m_meta_field[$val->m_field]]=="string" && $this->meta->input_type[$this->m_meta_field[$val->m_field]]=="text" && !$this->meta->ch_table[$this->m_meta_field[$val->m_field]])
										){ 
								?> 
									<input type="checkbox" class="m_strict_filter" name="m_strict_filter[<?php echo $val->m_id; ?>]" id="m_strict_filter_<?php echo $val->m_id; ?>" value="1" <?php echo($val->m_strict_filter ? $checked : ""); ?> />
								<?php }?>
								</th>
							<?php } ?>
							<?php if(defined("_BARMAZ_TRANSLATE")){?>
							<!-- тут можно по типам исключить , или наоборот определить  какие поля точно не требуют перевода -->
							<th><input type="checkbox" class="m_translate_value" name="m_translate_value[<?php echo $val->m_id; ?>]" id="m_translate_value_<?php echo $val->m_id; ?>" value="1" <?php echo($val->m_translate_value ? $checked : ""); ?> /></th>
							<?php } ?>
							</tr>
						<?php } else  return false;  ?>
					<?php } ?>
				</table>
			</div>
			<?php } ?>
			<div class="buttons">
				<input class="commonButton btn btn-info" type="submit" name="save" value="<?php echo Text::_("Save"); ?>" />
				<input class="commonButton btn btn-info" type="submit" name="apply" value="<?php echo Text::_("Apply"); ?>" />
				<input class="commonButton btn btn-info" type="button" name="reset" value="<?php echo Text::_("Cancel"); ?>" onclick="javascript:document.location.href='<?php echo Router::_("index.php?module=conf&task=selectVisio",false,false); ?>'" />
				<input type="hidden" name="module" value="conf" />
				<input type="hidden" name="task" value="saveVisio" />
				<input type="hidden" name="m_module" value="<?php echo $m_module; ?>" />
				<input type="hidden" name="m_view" value="<?php echo $m_view; ?>" />
				<input type="hidden" name="m_layout" value="<?php echo $m_layout; ?>" />
				<input type="hidden" name="m_admin_side" value="<?php echo $m_admin_side; ?>" />
			</div>
		</form>
	</div>
</div></div></div></div>
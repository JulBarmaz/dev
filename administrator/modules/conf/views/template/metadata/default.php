<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<form method="post" action="index.php" name="frmEdit">
	<input type="hidden" value="0" name="return" />
	<input type="hidden" value="conf" name="module" />
	<input type="hidden" value="metadata" name="view" />
	<input type="hidden" value="default" name="layout" />
	<input type="hidden" value="" name="task" />
	<input type="hidden" value="" name="sort" />
	<input type="hidden" value="" name="orderby" />
	<input type="hidden" value="" name="page" />
	<input type="hidden" value="" name="multy_code" />
	<input type="hidden" value="" name="psid" />
	<input type="hidden" value="0" name="trash" />
	<div class="nmb" id="nmb">
		<div id="sprav_mnu_left">
			<div class="picto_left">	
				<a onclick="return true;" href="index.php?module=conf&amp;view=metadata&amp;task=modifyMetadata">		
					<img class="sprav-button-new" title="Добавить" alt="I" src="/images/blank.gif" />	
				</a>
			</div>
			<div class="picto_left">
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{submitbutton('conf','metadata','','modifyMetadata');return false;}">		
					<img class="sprav-button-modify" title="<?php echo Text::_("Edit"); ?>"  alt="I" src="/images/blank.gif" />	
				</a>
			</div>
			<div class="picto_left">
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{submitbutton('conf','metadata','','modifyFields');return false;}">		
					<img class="sprav-button" title="<?php echo Text::_("Edit fields"); ?>"  alt="I" src="/images/blank.gif" />	
				</a>
			</div>
			<div class="picto_left">
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{submitbutton('conf','metadata','','modifyButtons');return false;}">		
					<img class="sprav-button" title="<?php echo Text::_("Edit standart buttons"); ?>"  alt="I" src="/images/blank.gif" />	
				</a>
			</div>
			<div class="picto_left">
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{submitbutton('conf','metadata','','modifyUButtons');return false;}">		
					<img class="sprav-button" title="<?php echo Text::_("Edit unique buttons"); ?>"  alt="I" src="/images/blank.gif" />	
				</a>
			</div>
			<div class="picto_left">
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{submitbutton('conf','metadata','','modifyUButtons');return false;}">		
					<img class="sprav-button" title="<?php echo Text::_("Fields visibility"); ?>"  alt="I" src="/images/blank.gif" />	
				</a>
			</div>
		</div>
		<div id="sprav_mnu_center" style="width: 982px;">
			<table class="sprav_title_text">
				<tbody>
					<tr>
						<td>
							<span class="spravtitle"><?php echo Text::_("Metadata editor");?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="sprav_mnu_right">
			<div class="picto_right">	
				<a onclick="javascript:if(isChecked()!=1){alert('<?php echo Text::_("Please select element from list"); ?>');  return false; }else{if(confirm('<?php echo Text::_("Do you want to delete"); ?> ?'))submitbutton('conf','metadata','','deleteMetadata');return false;}">		
					<img class="sprav-button-delete" title="<?php echo Text::_("Delete"); ?>" alt="I" src="/images/blank.gif" />	
				</a>
			</div>
		</div>
	</div>
	<div class="sprav_telo">
	<?php if (count($this->result)) { ?>
		<table class="sprav">
			<thead>
				<tr>
					<th class="checkbox"><div class="inner-checkbox"><input onclick="checkAll(this);" type="checkbox" value="" name="toggle" /></div></th>
					<th class="grid"><div class="inner-grid"><?php echo Text::_("Metadata side"); ?></div></th>
					<th class="grid" width="24%"><div class="inner-grid"><?php echo Text::_("module"); ?></div></th>
					<th class="grid" width="24%"><div class="inner-grid"><?php echo Text::_("view"); ?></div></th>
					<th class="grid" width="24%"><div class="inner-grid"><?php echo Text::_("layout"); ?></div></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($this->result as $row) {	?>
				<tr>
					<td class="checkbox">
						<div class="inner-grid checkbox">
							<input type="checkbox" id="cb<?php echo $row->h_id; ?>" value="<?php echo $row->h_id; ?>" name="cps_id[]" />
						</div>
					</td>
					<td style="white-space: nowrap;" class="choice_aa grid">
						<div style="white-space: nowrap;" class="inner-grid choice_aa grid">
							<?php if ($row->h_side) echo Text::_("FRONT"); else echo Text::_("Admin zone"); ?>
						</div>
					</td>
					<td style="white-space: nowrap;" class="choice_aa grid">
						<div style="white-space: nowrap;" class="inner-grid choice_aa grid"><?php echo $row->h_module; ?></div>
					</td>
					<td style="white-space: nowrap;" class="choice_aa grid">
						<div style="white-space: nowrap;" class="inner-grid choice_aa grid"><?php echo $row->h_view; ?></div>
					</td>
					<td style="white-space: nowrap;" class="choice_aa grid">
						<div style="white-space: nowrap;" class="inner-grid choice_aa grid">
							<a href="index.php?module=conf&amp;view=metadata&amp;layout=fields&amp;psid=<?php echo $row->h_id; ?>"><?php echo $row->h_layout?></a>
						</div>
					</td>
				</tr>
			<?php 	} ?>
			</tbody>
		</table>
	<?php } ?>
	</div>
</form>

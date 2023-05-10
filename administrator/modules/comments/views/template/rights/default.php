<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$cols=count($this->actions)+1;
$rules=array();
if (count($this->rights)) {
	foreach($this->rights as $right){ $rules[$right->ca_grp_id][$right->ca_r_id][$right->ca_action]=$right->ca_flag; }
}
?>
<div class="br_map-wrapper">
	<h4 class="title"><?php echo Text::_('Comments rights map by roles'); ?></h4>
	<table class="br_map table table-bordered table-hover table-condensed sprav-table">
	<?php foreach ($this->grps as $grp) { 
		if ($grp->cg_deleted) $subclass=" deleted"; else $subclass=""	?>
		<tr class="header"><th class="br_title<?php echo $subclass;?>" colspan="<?php echo $cols; ?>"><?php echo Text::_('Group')." : ".$grp->cg_title; ?></td></tr>
		<tr class="subheader">
			<td>&nbsp;</td>
			<?php foreach ($this->actions as $act) {?>
				<td class="br_title"><?php echo Text::_("grp_".$act);?></td>
			<?php } ?>
		</tr>
		<?php foreach ($this->roles as $role) {?>
		<tr class="rules">
			<th class="br_title"><?php echo $role->ar_title?></td>
			<?php foreach ($this->actions as $act) {?>
				<td class="br_action"><?php 
				if(isset($rules[$grp->cg_id][$role->ar_id][$act])){
					if ($rules[$grp->cg_id][$role->ar_id][$act]) $flag=1; else $flag=0;
				}	else $flag=0;
				echo "<a onclick=\"updateCommentRoleRule(this,'".$grp->cg_id."','".$role->ar_id."','".$act."','".abs($flag-1)."')\"><img width=\"1\" height=\"1\" class=\"enabled_".$flag."\" alt=\"\" src=\"/images/blank.gif\" /></a>"; 
				?></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr class="br_separator"><td colspan="<?php echo $cols; ?>">&nbsp;</td></tr>
	<?php } ?>
	</table>
</div>
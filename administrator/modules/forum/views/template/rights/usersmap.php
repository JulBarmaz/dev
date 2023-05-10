<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$cols=count($this->actions)+1;
$rules=array();
if (count($this->rights)) {
	foreach($this->rights as $right){ $rules[$right->f_id][$right->u_id][$right->action]=$right->flag; }
}
?>
<div class="br_map-wrapper">
	<h4 class="title"><?php echo Text::_('Forum rights map by users'); ?></h4>
	<table class="br_map table table-bordered table-hover table-condensed sprav-table">
	<?php foreach ($this->forums as $forum) { 
		if ($forum->f_deleted) $subclass=" deleted"; else $subclass=""	?>
		<tr class="header"><th class="br_title<?php echo $subclass;?>" colspan="<?php echo $cols; ?>"><?php echo Text::_('Forum')." : ".$forum->f_name; ?></td></tr>
		<tr class="subheader">
			<td>&nbsp;</td>
			<?php foreach ($this->actions as $act) {?>
				<td class="br_title"><?php echo Text::_("forum_".$act);?></td>
			<?php } ?>
		</tr>
		<?php foreach ($this->users as $user) {?>
		<tr class="rules">
			<th class="br_title"><?php echo $user->u_login?> (<?php echo $user->u_nickname?>)</td>
			<?php foreach ($this->actions as $act) {?>
				<td class="br_action"><?php 
				if(isset($rules[$forum->f_id][$user->u_id][$act])){
					if ($rules[$forum->f_id][$user->u_id][$act]) {
						$newflag=2;
						$state=1;
					} else {
						$newflag=1;
						$state=0;
					}
				}	else {
					$newflag=1; 
					$state=2; 
				}
				echo "<a onclick=\"updateUserRuleFF(this,'".$forum->f_id."','".$user->u_id."','".$act."','".$newflag."')\"><img width=\"1\" height=\"1\" class=\"enabled_".$state."\" alt=\"\" src=\"/images/blank.gif\" /></a>"; 
				?></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr class="br_separator"><td colspan="<?php echo $cols; ?>">&nbsp;</td></tr>
	<?php } ?>
	</table>
</div>
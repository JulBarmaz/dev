<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$feCurrent = ""; $beCurrent = "";
$feClass=""; $beClass="";
if ($this->tabId == "backend") {
	$beCurrent = " active";	$beClass = " active";
} else {
	$feCurrent = " active";	$feClass = " active";
}
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="acl-manager rounded-pan">
	<h4 class="title"><?php echo Text::_('ACL manager')." (".$this->roleName.")"; ?></h4>
	<?php if(count($this->message)){ ?>
	<div class="row">
		<div class="col-md-12"><?php 
		foreach($this->message as $message) {
	 		echo "<p class=\"error\">".$message."</p>";
		}
		?></div>
	</div>
	<?php } ?>
	<ul class="nav nav-tabs" id="tabs">
		<li class="switcher<?php echo $feCurrent; ?>">
			<a aria-expanded="false" href="#tab_frontend" data-toggle="tab"><?php echo Text::_('Frontend'); ?></a>
		</li>
		<li class="switcher<?php echo $beCurrent; ?>">
			<a aria-expanded="false" href="#tab_backend" data-toggle="tab"><?php echo Text::_('Backend'); ?></a>
		</li>
	</ul>
	<div class="tab-content float-fix">
		<div class="tab-pane<?php echo $feClass; ?>" id="tab_frontend">
		<form id="formFrontendRules" action="index.php" method="post">
			<input type="hidden" name="module" value="aclmgr" />
			<input type="hidden" name="task" value="update" />
			<input type="hidden" name="roleId" value="<?php echo $this->roleId; ?>" />
			<input type="hidden" name="tabId" value="frontend" />
			
			<?php $module = ""; $_side=array("","","",""); $html=""; $rcount=0; $r_part=ceil(count($this->objectsFE)/3); $side_n=1;
			foreach ($this->objectsFE as $objectf) {
				if ($module != $objectf->ao_module_name) {
					if ($module) { 
						$html.="</table></fieldset>";
						$_side[$side_n].=$html;
						if ($rcount>$r_part*$side_n) $side_n++;
						$html=""; 
					}
					$module = $objectf->ao_module_name;
					$html.="<fieldset>";
					$html.="<legend>".Text::_("Module rules for ".$module)."</legend>";
					$html.="<table class=\"rules\">";
				}
				$checked = ""; $canAccess = 0;
				if (isset($objectf->canAccess)) if ($objectf->canAccess) {
					$checked="checked=\"checked\"";
					$canAccess = 1;
				}
				$objectText = $objectf->ao_name;
				if ($objectf->ao_description != "") $objectText = $objectf->ao_description;
				$html.="<tr>";
				$html.="<td style=\"text-align:left;\">".HTMLControls::renderLabelField("access_".$objectf->ao_id, Text::_($objectText))."</td>";
				$html.="<td style=\"text-align:left;width:10px;\">";
				$html.="<input type=\"hidden\" name=\"oldAccess[".$objectf->ao_id."]\" value=\"".$canAccess."\" />";
				$html.="<input id=\"access_".$objectf->ao_id."\" type=\"checkbox\" name=\"access[".$objectf->ao_id."]\" ".$checked." />";
				$html.="</td>";
				$html.="</tr>";
				$rcount++;
			}
			$html.="</table></fieldset>";
			$_side[$side_n].=$html;
			echo "<div class=\"row\"><div class=\"part_acl col-sm-4\">".$_side[1]."</div><div class=\"part_acl col-sm-4\">".$_side[2]."</div><div class=\"part_acl col-sm-4\">".$_side[3]."</div></div>";
			?>
			<div class="buttons">
				<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Apply'); ?>" />
				<input type="button" class="commonButton btn btn-info" onclick="javascript:document.location.href='index.php?module=aclmgr&amp;view=roles&amp;return=1'; return true;" value="<?php echo Text::_('Cancel'); ?>" name="cancel" />
			</div>
		</form>
		</div>
		
		<div class="tab-pane<?php echo $beClass; ?>" id="tab_backend">
		<form id="formBackendRules" action="index.php" method="post">
			<input type="hidden" name="module" value="aclmgr" />
			<input type="hidden" name="task" value="update" />
			<input type="hidden" name="roleId" value="<?php echo $this->roleId; ?>" />
			<input type="hidden" name="tabId" value="backend" />
			
			<?php $module = ""; $_side=array("","","",""); $html=""; $rcount=0; $r_part=ceil(count($this->objectsBE)/3); $side_n=1;
			foreach ($this->objectsBE as $objectb) {
				if ($module != $objectb->ao_module_name) {
					if ($module) { 
						$html.="</table></fieldset>";	
						$_side[$side_n].=$html;
						if ($rcount>$r_part*$side_n) $side_n++;
						$html=""; 
					}
					$module = $objectb->ao_module_name;
					$html.="<fieldset>";
					$html.="<legend>".Text::_("Module rules for ".$module)."</legend>";
					$html.="<table class=\"rules\">";
				}
				$checked = ""; $canAccess = 0;
				if (isset($objectb->canAccess)) if ($objectb->canAccess) {
					$checked="checked=\"checked\"";
					$canAccess = 1;
				}
				$objectText = $objectb->ao_name;
				if ($objectb->ao_description != "") $objectText = $objectb->ao_description;
			
				$html.="<tr>";
				$html.="<td style=\"text-align:left;\">".HTMLControls::renderLabelField("access_".$objectb->ao_id, Text::_($objectText))."</td>";
				$html.="<td style=\"text-align:left;width:10px;\">";
				$html.="<input type=\"hidden\" name=\"oldAccess[".$objectb->ao_id."]\" value=\"".$canAccess."\" />";
				$html.="<input id=\"access_".$objectb->ao_id."\" type=\"checkbox\" name=\"access[".$objectb->ao_id."]\" ".$checked." />";
				$html.="</td>";
				$html.="</tr>";
				$rcount++;
			}
			$html.="</table></fieldset>";
			$_side[$side_n].=$html;
			echo "<div class=\"row\"><div class=\"part_acl col-sm-4\">".$_side[1]."</div><div class=\"part_acl col-sm-4\">".$_side[2]."</div><div class=\"part_acl col-sm-4\">".$_side[3]."</div></div>";
			?>
			<div class="buttons">
				<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Apply'); ?>" />
				<input type="button" class="commonButton btn btn-info" onclick="javascript:document.location.href='index.php?module=aclmgr&amp;view=roles'; return true;" value="<?php echo Text::_('Cancel'); ?>" name="cancel" />
			</div>
		</form>
		</div>
	</div>
</div></div></div></div>

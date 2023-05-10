<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$ecols=($this->params["show_pack_price"]+$this->params["show_weight_price"]+$this->params["show_volume_price"])*2+$this->params["show_dimensions"]*3;
if ($ecols) $hrowspan=2; else $hrowspan=1;
$colspan=4+$ecols;
if ($this->params["show_thumbs"]) $colspan++;
if ($this->params["show_weight"]) $colspan++;
$this->params["colspan"]=$colspan;
echo "<h3 align=\"center\">".Text::_("Price list")." ".Text::_("from")." ".Date::GetdateRus(time(),1)."</h3>";
if($this->params["show_company_info"]){
	echo "<h4>".soConfig::$firmName.", ".soConfig::$INN."</h4>";
	echo "<h4>".soConfig::$Address."</h4>";
	echo "<h4>".soConfig::$Phone.", ".soConfig::$Fax."</h4>";
}	
if ($this->params["add_header"]) echo "<h4>".Text::toHtml($this->params["add_header"])."</h4>";
?>
<table class="price_list" border="1">
	<thead>
	<tr>
		<?php if ($this->params["show_thumbs"]) { ?> <th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("Thumb"); ?></th><?php } ?>
		<th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("SKU"); ?></th>
		<th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("Name"); ?></th>
		<th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("Measure"); ?></th>
		<?php 
		if ($ecols){
			if($this->params["show_dimensions"]){
				echo "<th colspan=\"3\">".Text::_("Dimensions").", ",Measure::getInstance()->getShortName(catalogConfig::$default_size_measure)."</th>";
			}
		}
		?>
		<?php if ($this->params["show_weight"]) { ?><th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("Weight"); ?></th><?php } ?>
		<th rowspan="<?php echo $hrowspan;?>"><?php echo Text::_("Price"); ?></th>
		<?php 
		if ($ecols){
			if($this->params["show_pack_price"]){
				echo "<th colspan=\"2\">".Text::_("Pack")."</th>";
			}
			if($this->params["show_volume_price"]){
				echo "<th colspan=\"2\">".Text::_("Volume")."</th>";
			}
			if($this->params["show_weight_price"]){
				echo "<th colspan=\"2\">".Text::_("Weight")."</th>";
			}
		}
		?>
	</tr>
	<?php
	if ($ecols){
		echo "<tr>";
			if($this->params["show_dimensions"]){
				echo "<th>".Text::_("Length")."</th>";
				echo "<th>".Text::_("Width")."</th>";
				echo "<th>".Text::_("Height")."</th>";
			}
			if($this->params["show_pack_price"]){
				echo "<th>".Text::_("Measure")."</th>";
				echo "<th>".Text::_("Price")."</th>";
			}
			if($this->params["show_volume_price"]){
				echo "<th>".Text::_("Measure")."</th>";
				echo "<th>".Text::_("Price")."</th>";
			}
			if($this->params["show_weight_price"]){
				echo "<th>".Text::_("Measure")."</th>";
				echo "<th>".Text::_("Price")."</th>";
			}
		echo "</tr>";
	}
	echo "</thead>";
	if($this->params["break_by_groups"]){
		if($this->params["parent_group"]&&$this->mainGrp) {
			echo "<tr><td colspan=\"".$colspan."\" class=\"price_group_0\">".$this->mainGrp."</td></tr>";
			$this->renderGroupGoods($this->params["parent_group"],$this->params);
		}
		if(count($this->grpArr)){
			foreach($this->grpArr as $grp){
				echo "<tr><td colspan=\"".$colspan."\" class=\"price_group_".$grp->level."\">".$grp->title."</td></tr>";
				$this->renderGroupGoods($grp->id,$this->params);
			}
		}
	} else {
		if($this->params["parent_group"]&&$this->mainGrp) {
			echo "<tr><td colspan=\"".$colspan."\" class=\"price_group_0\">".$this->mainGrp."</td></tr>";
		}
		// здесь выводим просто по алфавиту
		$this->renderGroupGoods($this->params["parent_group"],$this->params);
	}
	?>
</table>
<?php 
if ($this->params["add_footer"]) echo "<h4>".Text::toHtml($this->params["add_footer"])."</h4>";
?>
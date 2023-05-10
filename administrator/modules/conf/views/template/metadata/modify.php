<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$res=$this->res; ?>
<form name="edit" action="index.php" method="post">
	<div class="tablHeader"><?php echo Text::_("Editing table string"); ?></div>
	<div class="clear"></div>
<div class="tablBody">
	<table class="">
	<tbody>
		<?php 
		$fld="h_side"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=0;
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Metadata side",1).":</th>";
		echo "<td>".HTMLControls::renderSelect($fld, "", 0, 0, array(0=>Text::_("Admin side"),1=>Text::_("Front side")), $val, 0)."</td></tr>";

		$fld="h_module"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "module",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_view"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "view",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_layout"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "layout",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_title"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Title",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val,50)."</td></tr>";
		
		$fld="h_table"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Table",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_keystring"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Key field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_namestring"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Name field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_keycurrency"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Currency field", 1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_enabled"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Enabled status field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_deleted"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Deleted status field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_keysort"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Def.sort field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_ordering_fld"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Ordering field", 1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

//		$fld="h_ordering_parent"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
//		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Ordering parent field",1).":</th>";
//		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_show_cb"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=0; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Show checkbox",1).":</th>";
		echo "<td>".HTMLControls::renderCheckbox($fld,$val)."</td></tr>";
		
		$fld="h_selector"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=0; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Selector mode",1).":</th>";
		echo "<td>".HTMLControls::renderCheckbox($fld,$val)."</td></tr>";
		
		$fld="h_multy_field"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Multy field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
				
		$fld="h_l_tablename"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Links table",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_p_tablename"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Parent table",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_p_keystring"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Parent key field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_p_namestring"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Parent name field",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";

		$fld="h_p_view"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Parent view",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_tmpl_new"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "New element template",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_tmpl_modify"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Modify element template",1).":</th>";
		echo "<td>".HTMLControls::renderInputText($fld,$val)."</td></tr>";
		
		$fld="h_custom_sql"; if (isset($res->{$fld})) $val=$res->{$fld}; else $val=""; 
		echo "<tr><th>".HTMLControls::renderLabelField($fld, "Custom SQL",1).":</th>";
		echo "<td>".HTMLControls::renderBBCodeEditor($fld,"",$val)."</td></tr>";
		
		?>
		<tr>
			<th colspan="2" style="text-align: center;">
				<input type="hidden" id="psid" value="<?php echo $this->psid; ?>" name="psid" />
				<input type="hidden" id="task" value="saveMetadata" name="task" />
				<input type="hidden" id="module" value="conf" name="module" />
				<input type="hidden" id="view" value="metadata" name="view" />
				<input type="hidden" id="layout" value="default" name="layout" />
				<input type="submit" class="commonButton" value="Сохранить" name="save" />
				<input type="submit" class="commonButton" value="Применить" name="apply" />
				<input type="button" class="commonButton"	onclick="javascript:document.location.href='index.php?module=conf&amp;view=metadata'; return true;" value="Закрыть" name="cancel" />
			</th>
		</tr>
	</tbody>
</table>
</div>
</form>

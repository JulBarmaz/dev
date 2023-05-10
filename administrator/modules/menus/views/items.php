<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewitems extends SpravView {
	private $flds = array(
			0 => array('mi_module', 'mi_view', 'mi_layout', 'mi_alias', 'mi_psid', 'mi_controller', 'mi_task'),
			1 => array('mi_link'),
			2 => array('mi_canonical_id')
	);
	
	public function modify($row) {
//		Util::pre("var flds = ". json_encode($this->flds) . ";\n");
		$func_name = "updateFieldsVisibilityOnModify_".time();
		$script="";
		$script.= "function ".$func_name."(id){";
/*	
		$script.= "
	var flds = new Array();
	flds[0] = ['mi_module', 'mi_view', 'mi_layout', 'mi_alias', 'mi_psid', 'mi_controller', 'mi_task'];
	flds[1] = ['mi_link'];
	flds[2] = ['mi_canonical_id'];
				";
*/
		$script.= "var flds = ". json_encode($this->flds) . ";";
//		$script.= "console.dir(flds);";
		$script.="
	flds.forEach(function callback(curVal, ind, arr) {
		curVal.forEach(function callback(curVal2, ind2, arr2) {
			$('#wrapper-' + curVal2).hide();
		});
	});
	flds.forEach(function callback(curVal, ind, arr) {
		if(ind != id){
			curVal.forEach(function callback(curVal2, ind2, arr2) {
				// console.log(curVal2);
				$('#' + curVal2).val('');
			});
		}
	});
	flds[id].forEach(function callback(curVal, ind, arr) {
		$('#wrapper-' + curVal).show();
	})
				";
		$script.= "}";
		$script.= "
			$(document).ready(function(){
				$('#mi_type').change(function() {
					".$func_name."($(this).val());
				});
				".$func_name."($('#mi_type').val());
			});";

		if($script) Portal::getInstance()->addScriptDeclaration($script);
		parent::modify($row);
	}
	
	public function renderViewsSelector(){
		$arr = array();
		$arr[""] = "[ ".Text::_("Not selected")." ]";
		if ($this->modname) {
			$pathr=$this->getpath_name($this->modname);
			$path = PATH_FRONT_MODULES.$pathr.DS."views";
			$files_arr = Files::getFiles($path,false,false);
		} else $files_arr = array();
		if (count($files_arr)){
			foreach($files_arr as $key=>$val){
				$item = substr($key,0,strpos($key, "."));
				$arr[$item] = $item;
			}
		}
		return HTMLControls::renderSelect("mi_view", "mi_view", "", "", $arr, $this->vname, false);
	}
	public function renderControllersSelector(){
		$arr = array();
		$arr[""] = "[ ".Text::_("Default value")." ]";
		if ($this->modname) {
			$pathr=$this->getpath_name($this->modname);
			$path = PATH_FRONT_MODULES.$pathr.DS."controllers";
			$files_arr = Files::getFiles($path,false,false);
		} else $files_arr = array();
		if (count($files_arr)){
			foreach($files_arr as $key=>$val){
				if($key == "default.php") continue;
				$item = substr($key,0,strpos($key, "."));
				$arr[$item] = $item;
			}
		}
		return HTMLControls::renderSelect("mi_controller", "mi_controller", "", "", $arr, $this->vcontroller, false);
	}
	public function renderMenuSelector(){
		$tree=new simpleTreeTable();
		$tree->table = "menus";
		$tree->fld_id = "mi_id";
		$tree->fld_parent_id = "mi_parent_id";
		$tree->fld_title = "mi_name";
		$tree->fld_deleted = "mi_deleted";
		$tree->fld_enabled = "mi_enabled";
		$tree->select_top_level_text = "[ ".Text::_("Not selected")." ]";
		$tree->buildTreeArrays($this->mi_id, 0, 1, 0);
		return $tree->getTreeHTML(0, $type="select", "mi_canonical_id", "mi_canonical_id", $this->mi_canonical_id, "", "singleSelect form-control form-control");
	}
	public function getViewsList(){
		$html="<option value=\"\">[ ".Text::_("Not selected")." ]</option>";
		if ($this->modname) {
			$pathr=$this->getpath_name($this->modname);
			$path = PATH_FRONT_MODULES.$pathr.DS."views";
			$files_arr = Files::getFiles($path,false,false);
		} else $files_arr = array();
		if (count($files_arr)){
			foreach($files_arr as $key=>$val){
				$item = substr($key,0,strpos($key, "."));
				$html.= "<option value=\"".$item."\">".$item."</option>";
				
			}
		}
		return $html;
	}	
	public function getControllersList(){
		$html="<option value=\"\">[ ".Text::_("Default value")." ]</option>";
		if ($this->modname) {
			$pathr=$this->getpath_name($this->modname);
			$path = PATH_FRONT_MODULES.$pathr.DS."controllers";
			$files_arr = Files::getFiles($path,false,false);
		} else $files_arr = array();
		if (count($files_arr)){
			foreach($files_arr as $key=>$val){
				if($key == "default.php") continue;
				$item = substr($key, 0, strpos($key, "."));
				$html.= "<option value=\"".$item."\">".$item."</option>";
				
			}
		}
		return $html;
	}
	public function renderModulesSelector($_cur_val=""){
		$inst_modules = Module::getInstalledModules();
		$mdirs = Files::getFolders(PATH_FRONT_MODULES,false,false);
		$_mdls[""] = "[ ".Text::_("Not selected")." ]";
		foreach($inst_modules as $_module) { 
			if (array_key_exists($_module, $mdirs)) $_mdls[$_module] = $_module;	
		}
		$js = "getViewsForModule(this, 'mi_view');";
		$js.= "getControllersForModule(this, 'mi_controller');";
		return HTMLControls::renderSelect("mi_module", "mi_module", "", "", $_mdls, $_cur_val, false, $js);
		
	}
}
?>
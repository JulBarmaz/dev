<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");


$_activeTab=Request::getSafe("config","site"); ?>

<div class="container">
   <div class="row">  
    <div class="col-md-12">
       <div class="cpanel_conf rounded-pan"> 
       <h4 class="title"><?php echo Text::_("System settings") ?></h4>
	   <ul class="nav nav-tabs" id="conf_tabs" role="tablist">
<?php foreach(ConfigTMPL::$_tabs as $_tab_key=>$_tab_val) {
			if ($_tab_val[0]==$_activeTab) $_class=' active'; else $_class="";
		?>
		<li class="nav-item" role="presentation">
			<button class="nav-link <?php echo $_class; ?>"	id="tb<?php echo $_tab_key; ?>-tab" 
			data-bs-toggle="tab" data-bs-target="#tb<?php echo $_tab_key; ?>-tab-pane" 
			type="button" role="tab" aria-controls="tb<?php echo $_tab_key; ?>-pane" 
			
			>
			<?php echo Text::_($_tab_val[1]); ?>
			</button>
		</li>
		<?php }?>
	</ul>
	<div class="tab-content clearfix" id="TabContent">
		<?php foreach(ConfigTMPL::$_tabs as $_tab_key=>$_tab_val) {
			if ($_tab_val[0]==$_activeTab) $_class=' active'; else $_class="";
		?>
		<div id="tb<?php echo $_tab_key; ?>-tab-pane" class="tab-pane fade show <?php echo $_class; ?>" role="tabpanel"
		aria-labelledby="tb<?php echo $_tab_key; ?>-tab" tabindex="0">
			
  		<form name="<?php echo $_tab_val[0]?>Form" action="index.php" method="post">
				<input type="hidden" name="config" value="<?php echo $_tab_val[0]?>" /> 
				<input type="hidden" name="module" value="conf" /> <input type="hidden" name="task" value="saveConfig" />
				<?php
				$confTMPL_name=$_tab_val[0]."ConfigTMPL";
				$confTMPL= new $confTMPL_name;
				$conf_name=$_tab_val[0]."Config";
				foreach ($confTMPL->props as $_key=>$_val ) {
					$_cur_val = $confTMPL->getConfigVar($conf_name,$_key);
					if(!$_cur_val && in_array($_val[0], array("string")) && (isset($_val[1]) && $_val[1]!=false)) $_cur_val=$_val[1];
					$_source=false;
					$fld_id=$conf_name.$_key;
					echo '<div class="row">';
					echo '<div class="col-sm-5">';
					echo HTMLControls::renderLabelField($fld_id, Text::_($_key));
					if(isset($_val[3]) && $_val[3]) echo HTMLControls::renderBalloonButton($_key." description");
					echo '</div>';
					echo '<div class="col-sm-7">';
					switch($_val[0]) {
						case 'boolean':
							if ($_cur_val) $checked="checked=\"checked\""; else $checked="";
							echo '<input type="checkbox" id="'.$fld_id.'" value="1" name="'.$_key.'" '.$checked.' />';
							break;
						case 'integer':
						case 'float':
							echo '<input type="text" id="'.$fld_id.'" class="form-control numeric" size="19" value="'.$_cur_val.'" name="'.$_key.'" />';
							break;
						case 'text':
							echo '<textarea id="'.$fld_id.'" name="'.$_key.'" cols="45" rows="6" class="form-control">'.$_cur_val.'</textarea>';
							break;
						case 'string':
							echo '<input type="text" id="'.$fld_id.'" size="200" class="form-control" value="'.$_cur_val.'" name="'.$_key.'" />';
							break;
						case 'password':
							echo '<div class="show-config-pass" title="'.Text::_("Show").'/'.Text::_("Hide").'"><input type="checkbox" id="show_check'.$fld_id.'" value="1" name="show_check'.$fld_id.'" onchange="$(\'#'.$fld_id.'\').attr(\'type\', $(this).prop(\'checked\') ? \'text\' : \'password\')" /></div>';
							echo '<input type="password" id="'.$fld_id.'" size="60" class="form-control" value="'.$_cur_val.'" name="'.$_key.'" />';
							break;
						case 'select':
							if (isset($_val[2])) {
								foreach($_val[2] as $key=>$val){
									if (!isset($_val[4]) || $_val[4]!=false){
										$_val[2][$key]=Text::_($val);
									}
								}
								echo HTMLControls::renderSelect($_key,$fld_id, "", "", $_val[2], $_cur_val, 0,"",0);
							}
							break;
						case 'select_method':
							if (isset($_val[2])) {
								$method_array=explode("!!",$_val[2]);
								if (is_array($method_array) && count($method_array)){
									$class_func=explode("::",$method_array[0]);
									if (is_array($class_func) && count($class_func)>1){
										if (count($method_array)>1){
											$arr=call_user_func($class_func, $method_array[1]);
										} else {
											$arr=call_user_func($class_func);
										}
										echo HTMLControls::renderSelect($_key,$fld_id, false, false,  $arr, $_cur_val, 0,"",0);
									}
								}
							}
							break;
						case 'multiselect':
							if (isset($_val[2])) {
								echo HTMLControls::renderSelect($_key,$fld_id, "", "", $_val[2], $_cur_val, 0,"",5);
							}
							break;
						case 'multiselect_method':
							if (isset($_val[2])) {
								$method_array=explode("!!",$_val[2]);
								if (is_array($method_array) && count($method_array)){
									$class_func=explode("::",$method_array[0]);
									if (is_array($class_func) && count($class_func)>1){
										if (count($method_array)>1){
											$arr=call_user_func($class_func, $method_array[1]);
										} else {
											$arr=call_user_func($class_func);
										}
										echo HTMLControls::renderSelect($_key,$fld_id, false, false,  $arr, $_cur_val, 0,"",5);
									}
								}
							}
							break;
						case 'table_multiselect':
							if (isset($_val[2])) {
								$db=Database::getInstance();
								$db->setQuery($_val[2]);
								$_source=$db->loadObjectList();
								echo HTMLControls::renderSelect($_key,$fld_id, "fld_id", "fld_name", $_source, $_cur_val, 0,"",5);
							}
							break;
						case 'table_select':
							if (isset($_val[2])) {
								$db=Database::getInstance();
								$db->setQuery($_val[2]);
								$_source=$db->loadObjectList();
								echo HTMLControls::renderSelect($_key,$fld_id, "fld_id", "fld_name", $_source, $_cur_val, 0);
							}
							break;
						case 'folder':
							if (isset($_val[2])) {
								$_source=Files::getFolders(PATH_FRONT.DS.$_val[2],array(".svn",".",".."));
								echo HTMLControls::renderSelect($_key,$fld_id, 0, 'filename', $_source, $_cur_val, 0);
							}
							break;
						case 'files':
							if (isset($_val[2])) {
								$_source=Files::getFiles(PATH_FRONT.DS.$_val[2],array(".svn",".","..","index.html"),false);
								echo HTMLControls::renderSelect($_key,$fld_id, 0, 'filename', $_source, $_cur_val, 1);
							}
							break;
						case 'filenames':
							if (isset($_val[2])) {
								$_source=Files::getFiles(PATH_FRONT.DS.$_val[2],array(".svn",".","..","index.html"),false);
								foreach($_source as $_src_key=>$_src_val){
									$_source[$_src_key]["filename"]=str_replace(".php", "", $_src_val["filename"]);
								}
								echo HTMLControls::renderSelect($_key,$fld_id, 'filename', 'filename', $_source, $_cur_val, 1);
							}
							break;
						default:
							echo '&nbsp;';
					}
					echo '</div>';
					echo '</div>';
				}
				?>
				<div class="buttons">
					<input type="submit" class="commonButtonbtn btn btn-info" value="<?php echo Text::_('Apply'); ?>" /> 
					<input type="submit" name="cancel_but" class="commonButton btn btn-info" value="<?php echo Text::_('Cancel'); ?>" />
				</div>
			</form>
				
		</div>
	<?php } ?>
	</div>
</div></div>
 </div>
</div>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class aForm extends BaseObject {
	
	private $_name="";
	private $_id="";
	private $_is_ajax=false;
	private $_action="index.php";
	private $_method="POST";
	private $_enctype="";
	private $_inputs=array();
	private $_errors=array();
	private $_capturing=false;
	private $_directOutput=false;
	
	public function __construct($_name,$_method,$_action,$_is_ajax,$_id="") {
		$this->_name = $_name;
		if($_id) $this->_id=$_id;
		$this->_action = $_action;
		$this->_method = $_method;
		$this->_is_ajax = $_is_ajax;
	}
	private function setEncType($datatype){
		$this->_enctype=$datatype;
	}
	
	public function appendTag($tag,$name,$val)
	{
		if(isset($this->_inputs[$name])){
			$this->_inputs[$name][$tag]=$val;
		}
	}
	
	public function addInput($arr){
		if (!isset($arr["VAL"])) $arr["VAL"]="";
		if (!isset($arr["NAME"])) $this->logError("Preparing.No NAME specified");
		elseif (!isset($arr["TYPE"]))	$this->logError("Preparing.No TYPE specified");
		else {
			if ($arr["TYPE"]=="file" || $arr["TYPE"]=="fileselector" || $arr["TYPE"]=="imageselector"  )	$this->setEncType("multipart/form-data");
			if ($arr["TYPE"]!="hidden") {
				if (!isset($arr["CLASS"])) $arr["CLASS"]="";
				if (!isset($arr["ID"])) $arr["ID"]=$arr["NAME"];
				if (!isset($arr["REQUIRED"]["FLAG"])) { $arr["REQUIRED"]["FLAG"]=false; $arr["REQUIRED"]["MESSAGE"]="";}
				if (!isset($arr["UNIQUE"]["FLAG"])) { $arr["UNIQUE"]["FLAG"]=false; $arr["UNIQUE"]["MESSAGE"]="";}
			}
			switch($arr["TYPE"]) {
				case "checkbox":
					if (!isset($arr["CHECKED"])) $arr["CHECKED"]="";
					if (!isset($arr["ONCLICK"])) $arr["ONCLICK"]="";
					if (!isset($arr["ONCHANGE"])) $arr["ONCHANGE"]="";
					break;
				case "button":
				case "submit":
					if (!isset($arr["ONCLICK"])) $arr["ONCLICK"]="";
					break;
				default:
					if (!isset($arr["ONCLICK"])) $arr["ONCLICK"]="";
					if (!isset($arr["ONCHANGE"])) $arr["ONCHANGE"]="";
					break;
			}
			if (!isset($arr["READONLY"])) $arr["READONLY"]=false;
			$this->_inputs[$arr["NAME"]]=$arr;
		}
	}
	public function startLayoutCapture()	{
		if(!$this->_capturing) $this->_capturing=ob_start();
		else $this->logError("Already in capturing");
	}
	public function stopLayoutCapture()	{
		if($this->_capturing) {
			$data=ob_get_contents();
			ob_end_clean();
			return $data;
		} else $this->logError("Not in capturing");
	}
	public function startLayout($directOutput=true)	{
		$this->_directOutput=$directOutput;
		$html="<form name=\"".$this->_name."\" method=\"".$this->_method."\" action=\"".$this->_action."\"";
		if ($this->_enctype) $html.=" enctype=\"".$this->_enctype."\"";
		if ($this->_id) $html.=" id=\"".$this->_id."\"";
		$html.=" >";
		if ($this->_capturing||$this->_directOutput) { echo $html; }
		else return $html;
	}
	public function endLayout()	{
		$html=$this->renderHiddenFields();
		$html.="</form>";
		if ($this->_capturing||$this->_directOutput) { echo $html; }
		else return $html;
	}
	private function logError($msg)	{
		$this->_errors[]=$msg;
	}
	public function dump(){
		Util::showCollapsedArray($this);
	}
	private function renderHiddenFields(){
		$html="";
		foreach ($this->_inputs as $key=>$input){
			if ($input["TYPE"]=="hidden"){
				$html.=HTMLControls::renderHiddenField($input["NAME"], $input["VAL"], $input["ID"]);
			}
		}
		if ($this->_capturing||$this->_directOutput) { echo $html; }
		else return $html;
	}
	public function getInput($name){
		if (!isset($this->_inputs[$name])) $this->logError("Rendering.No input found for ".$name);
		else return $this->_inputs[$name];
	}
	public function getInputValue($name){
		if (!isset($this->_inputs[$name])) $this->logError("Rendering.No input found for ".$name);
		elseif (!isset($this->_inputs[$name]["VAL"])) $this->logError("Rendering.No VAL specified for ".$name);
		else return $this->_inputs[$name]["VAL"];
	}
	public function getSelectValue($name){
		if (!isset($this->_inputs[$name])) $this->logError("Rendering.No input found for ".$name);
		elseif (!isset($this->_inputs[$name]["OPTIONS"])) $this->logError("Rendering.No OPTION specified for ".$name);
		else return $this->_inputs[$name]["OPTIONS"];
	}
	public function setInputValue($name,$value){
		if (!isset($this->_inputs[$name])) $this->logError("Rendering.No input found for ".$name);
		else $this->_inputs[$name]["VAL"]=$value;
	}
	public function unsetInput($name){
		if (!isset($this->_inputs[$name])) $this->logError("Rendering.No input found for ".$name);
		else unset($this->_inputs[$name]);
	}
	public function renderLabelFor($name){
		if (!isset($this->_inputs[$name]["LABEL"])) $this->logError("Rendering.No LABEL found for ".$name);
		else {
			$html=HTMLControls::renderLabelField($name, $this->_inputs[$name]["LABEL"]);
			if(isset($this->_inputs[$name]["REQUIRED"]) && isset($this->_inputs[$name]["REQUIRED"]['FLAG']) && $this->_inputs[$name]["REQUIRED"]['FLAG']){
				$html.= "<span class=\"label_required\">*</span>";
			} elseif(isset($this->_inputs[$name]["UNIQUE"]) && isset($this->_inputs[$name]["UNIQUE"]['FLAG']) && $this->_inputs[$name]["UNIQUE"]['FLAG']){
				$html.= "<span class=\"label_unique\">*</span>";
			}
			if ($this->_capturing||$this->_directOutput) { echo $html; }
			else return $html;
		}
		
	}
	public function renderBalloonFor($name){
		if (isset($this->_inputs[$name]["DESCRIPTION"])&&$this->_inputs[$name]["DESCRIPTION"]) {
			$html=HTMLControls::renderBalloonButton($this->_inputs[$name]["DESCRIPTION"], false);
			if ($this->_capturing||$this->_directOutput) { echo $html; }
			else return $html;
		}
		
	}
	public function renderInputPart($name){
		// @FIXME не везде обрабатывается READONLY (только дата и поле ввода) и REQUIRED
		if (!isset($this->_inputs[$name])) { $html="Rendering.No input found for ".$name; $this->logError("Rendering.No input found for ".$name);}
		elseif (!isset($this->_inputs[$name]["TYPE"])) {$html="Rendering.No TYPE specified for ".$name; $this->logError("Rendering.No TYPE specified for ".$name);}
		else {
			$html="";
			switch($this->_inputs[$name]["TYPE"]){
				case "textarea":
					if(isset($this->_inputs[$name]["COLS"])) $cols=$this->_inputs[$name]["COLS"]; else $cols=0;
					if(isset($this->_inputs[$name]["ROWS"])) $rows=$this->_inputs[$name]["ROWS"]; else $rows=0;
					if(isset($this->_inputs[$name]["TITLE"])) $taTitle=$this->_inputs[$name]["TITLE"]; else $taTitle='';
					if(isset($this->_inputs[$name]["PLACEHOLDER"])) $placeholder=$this->_inputs[$name]["PLACEHOLDER"]; else $placeholder='';
					$html=HTMLControls::renderBBCodeEditor($this->_inputs[$name]["ID"], $this->_inputs[$name]["NAME"],$this->_inputs[$name]["VAL"], $cols, $rows, $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["READONLY"], $this->_inputs[$name]["REQUIRED"]["FLAG"],$taTitle,$placeholder);
					break;
				case "texteditor":
					$params=array();
					if(isset($this->_inputs[$name]["TOOLBAR"])) $toolbar=$this->_inputs[$name]["TOOLBAR"]; else $toolbar="";
					if(isset($this->_inputs[$name]["WIDTH"])) $width=$this->_inputs[$name]["WIDTH"]; else $width=0;
					if(isset($this->_inputs[$name]["HEIGHT"])) $height=$this->_inputs[$name]["HEIGHT"]; else $height=0;
					if(isset($this->_inputs[$name]["COLS"])) $params["cols"]=$this->_inputs[$name]["COLS"];
					if(isset($this->_inputs[$name]["ROWS"])) $params["rows"]=$this->_inputs[$name]["ROWS"];
					if(isset($this->_inputs[$name]["TITLE"])) $taTitle=$this->_inputs[$name]["TITLE"]; else $taTitle='';
					if(isset($this->_inputs[$name]["PLACEHOLDER"])) $placeholder=$this->_inputs[$name]["PLACEHOLDER"]; else $placeholder='';
					$html=HTMLControls::renderEditor($this->_inputs[$name]["ID"],$this->_inputs[$name]["NAME"],$this->_inputs[$name]["VAL"],$toolbar,$height,$width,$params, $this->_inputs[$name]["CLASS"],$taTitle,$placeholder);
					break;
				case "select":
				case "multiselect":
					if(isset($this->_inputs[$name]["ZEROFILL"])) $zerofill=$this->_inputs[$name]["ZEROFILL"]; else $zerofill=0;
					if(isset($this->_inputs[$name]["MULTIPLE"])) $multiple=$this->_inputs[$name]["MULTIPLE"]; else $multiple=0;
					// $html=HTMLControls::renderSelect($this->_inputs[$name]["NAME"], $this->_inputs[$name]["ID"], "", "", $this->_inputs[$name]["OPTIONS"], $this->_inputs[$name]["VAL"], $zerofill, $this->_inputs[$name]["ONCHANGE"], $multiple, $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["REQUIRED"]["FLAG"]);
					$html=HTMLControls::renderSelectColored($this->_inputs[$name]["NAME"], $this->_inputs[$name]["ID"], "", "", $this->_inputs[$name]["OPTIONS"], (isset($this->_inputs[$name]["OPTIONS_CLASSES"]) ? $this->_inputs[$name]["OPTIONS_CLASSES"] : array()), $this->_inputs[$name]["VAL"], $zerofill, $this->_inputs[$name]["ONCHANGE"], $multiple, $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["REQUIRED"]["FLAG"]);
					break;
				case "checkbox":
					$html=HTMLControls::renderCheckbox($this->_inputs[$name]["NAME"], $this->_inputs[$name]["CHECKED"], $this->_inputs[$name]["VAL"], $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"]);
					break;
				case "file":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					$html=HTMLControls::renderInputFile($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"]);
					break;
				case "fileselector":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					$js="clearfieldVal('".$this->_inputs[$name]["ID"]."')";
					$html="<div class=\"fileselector\">";
					$html.=HTMLControls::renderHiddenField($this->_inputs[$name]["NAME"]."_oldfile",$this->_inputs[$name]["VAL"]);
					$html.=HTMLControls::renderButton($this->_inputs[$name]["NAME"]."_clear", "", "button", "", "clrfile",$js, Text::_("Clear"));
					$html.=HTMLControls::renderInputFile($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"]);
					if ($this->_inputs[$name]["VAL"]) {
						$html.=HTMLControls::renderLabelField(false,$this->_inputs[$name]["VAL"]);
						$html.="<div class=\"delete-file\">";
						$html.=HTMLControls::renderCheckbox($this->_inputs[$name]["NAME"]."_delete", "0","1");
						$html.=HTMLControls::renderLabelField($this->_inputs[$name]["NAME"]."_delete",Text::_('Mark for delete'));
						$html.="</div>";
					}
					$html.="</div>";
					break;
				case "datetimeselector":
				case "dateselector":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					if(isset($this->_inputs[$name]["MAXLENGTH"])) $length=$this->_inputs[$name]["MAXLENGTH"]; else $length="";
					$html="<div class=\"wrapper-datetimeselector\">".HTMLControls::renderInputText($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size,$length, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["READONLY"], $this->_inputs[$name]["REQUIRED"]["FLAG"]);
					$html.="<img width=\"1\" height=\"1\" class=\"date_selector\" src=\"/images/blank.gif\" title=\"".Text::_("Select date")."\"	alt=\"D\" /></div>";
					if ($this->_inputs[$name]["TYPE"]=="datetimeselector") $script='jQuery(document).ready(function() { appendDTPicker(jQuery("#'.$this->_inputs[$name]["ID"].'"),true); });';
					else $script='jQuery(document).ready(function() { appendDTPicker(jQuery("#'.$this->_inputs[$name]["ID"].'")); });';
					if($this->_is_ajax) $html.='<script type="text/javascript">'.$script.'</script>';
					else Portal::getInstance()->addScriptDeclaration($script);
//					if ($this->_inputs[$name]["TYPE"]=="datetimeselector") $script='<script type="text/javascript">jQuery(document).ready(function() { appendDTPicker(jQuery("#'.$this->_inputs[$name]["ID"].'"),true); });</script>';
//					else $script='<script type="text/javascript">jQuery(document).ready(function() { appendDTPicker(jQuery("#'.$this->_inputs[$name]["ID"].'")); });</script>';
//					$html.=$script;
					break;
				case "imageselector":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					if(isset($this->_inputs[$name]["URL"])) $tmpl_img=$this->_inputs[$name]["URL"]; else $tmpl_img="";
					if(isset($this->_inputs[$name]["PATH"])) $tmpl_img_path=$this->_inputs[$name]["PATH"]; else $tmpl_img_path="";
					$js="clearfieldVal('".$this->_inputs[$name]["ID"]."')";
					$html="<div class=\"fileselector\">";
					$html.=HTMLControls::renderButton($this->_inputs[$name]["NAME"]."_clear", "", "button", "", "clrfile",$js, Text::_("Clear"));
					$html.=HTMLControls::renderInputFile($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"]);
					$html.="</div>";
					$html.="<div class=\"modify-image\">";
					if ((file_exists($tmpl_img_path))&&(is_file($tmpl_img_path))) {
						if (Files::mime_content_type($tmpl_img_path)=="application/x-shockwave-flash") {
							$html.='<object type="application/x-shockwave-flash" data="'.$tmpl_img.'" width="100" height="100">
										<param name="movie" value="'.$tmpl_img.'">
										<param name="quality" value="high">
										<param name="wmode" value="transparent">
									</object>';
						} else {
							if ($this->_is_ajax) {
								$html.="<img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" />";
							} else {
								$html.="<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" /></a>";
							}
						}
					} else  {
						$tmpl_img="/images/nophoto.png";
						$html.="<img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" />";
					}
					$html.="</div>";
					$html.="<div class=\"delete-image\">";
					$html.=HTMLControls::renderHiddenField($this->_inputs[$name]["NAME"]."_oldfile",$this->_inputs[$name]["VAL"]);
					$html.=HTMLControls::renderCheckbox($this->_inputs[$name]["NAME"]."_delete", "0","1");
					$html.=HTMLControls::renderLabelField($this->_inputs[$name]["NAME"]."_delete",Text::_('Mark for delete'));
					$html.="</div>";
					break;
				case "text":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					if(isset($this->_inputs[$name]["MAXLENGTH"])&&$this->_inputs[$name]["MAXLENGTH"]) $length=$this->_inputs[$name]["MAXLENGTH"]; else $length="";
					if(isset($this->_inputs[$name]["TITLE"])) $taTitle=$this->_inputs[$name]["TITLE"]; else $taTitle='';
					if(isset($this->_inputs[$name]["PLACEHOLDER"])) $placeholder=$this->_inputs[$name]["PLACEHOLDER"]; else $placeholder='';
					$html=HTMLControls::renderInputText($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size,$length, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["READONLY"], $this->_inputs[$name]["REQUIRED"]['FLAG'],$taTitle,array('onchange'=>$this->_inputs[$name]["ONCHANGE"]),$placeholder);
					break;
				case "html":
					if(isset($this->_inputs[$name]["TITLE"])) $taTitle=$this->_inputs[$name]["TITLE"]; else $taTitle='';
					$html.="<div id=\"".$this->_inputs[$name]["ID"]."\" class=\"".$this->_inputs[$name]["CLASS"]."\" title=\"".$taTitle."\">".$this->_inputs[$name]["VAL"]."</div>";
					break;
				case "filepath":
					if(isset($this->_inputs[$name]["SIZE"])) $size=$this->_inputs[$name]["SIZE"]; else $size="";
					if(isset($this->_inputs[$name]["MAXLENGTH"])&&$this->_inputs[$name]["MAXLENGTH"]) $length=$this->_inputs[$name]["MAXLENGTH"]; else $length="";
					$html=HTMLControls::renderFileSelector($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],$size,$length, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["READONLY"], $this->_inputs[$name]["REQUIRED"]['FLAG']);
					// Restrict filepath show on frontend
					if(!defined("_ADMIN_MODE")) $html="";
					break;
				case "button":
					$html=HTMLControls::renderButton($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],  "button",  $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"],$this->_inputs[$name]["ONCLICK"]);
					break;
				case "submit":
					$html=HTMLControls::renderButton($this->_inputs[$name]["NAME"], $this->_inputs[$name]["VAL"],  "submit",  $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"],$this->_inputs[$name]["ONCLICK"]);
					break;
				case "addressselector":
					$address = Address::getTmpl();
					if ($this->_inputs[$name]["VAL"]) {
						$data=json_decode(base64_decode($this->_inputs[$name]["VAL"]),true);
						if (is_array($data)){
							foreach($address as $key=>$val){
								if (isset($data[$key])) $current[$key]=$data[$key]; else $current[$key]=$val;
							}
						} else $current=$address;
					} else $current=$address;
					$html=HTMLControls::renderAddressPanel($this->_inputs[$name]["NAME"], $current, $this->_inputs[$name]["ID"], $this->_inputs[$name]["CLASS"], $this->_inputs[$name]["READONLY"], ($this->_inputs[$name]["MODULE"] ? $this->_inputs[$name]["MODULE"] : ""));
					break;
			}
		}
		if ($this->_capturing||$this->_directOutput) { echo $html; }
		else return $html;
	}
	public function displayOutput(){
		// fake function
	}
}

?>
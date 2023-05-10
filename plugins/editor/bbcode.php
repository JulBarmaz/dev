<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class editorPluginbbcode extends Plugin {
	protected $_events=array("bbcode.editor","bbcode.parse");
	private $bbtags=array( "tag_p","tag_br","tag_b","tag_i","tag_s","tag_u","tag_url","tag_img","tag_size","tag_color","tag_ul","tag_ol","tag_code","tag_h");
	private $disabled_tags=array();
	protected function setParamsMask(){
		parent::setParamsMask();
		foreach($this->bbtags as $tag){
			$this->addParam($tag, "boolean", 0);
		}
		$this->addParam("links_as_nofollow", "boolean", 1);
		$this->addParam("image_as_thumb", "boolean", 0);
		$this->addParam("max_thumb_width", "string", "100%");
	}
	protected function onRaise($event, &$data) {
		foreach($this->bbtags as $tag){
			if (!$this->get($tag,0)) $this->disabled_tags[$tag]=true;
		}
		switch($event){
			case "bbcode.editor":
				$editor_id=$this->get("element_id");
				if ($editor_id){
					$dts=""; $dts_arr=array();
					foreach($this->disabled_tags as $dt=>$val) {
						if ($dt=="tag_h") $dts_arr[]="tag_h1:false,tag_h2:false,tag_h3:false,tag_h4:false,tag_h5:false,tag_h6:false";
						else $dts_arr[]=$dt.":false";
					}
					if (count($dts_arr)) $dts="{".implode(",",$dts_arr)."}";
					Portal::getInstance()->addScriptDeclaration("$(document).ready(function() { $('#".$editor_id."').bbcode_editor(".$dts."); });");
					Portal::getInstance()->addScript("/redistribution/bbcode/jquery.bbcode_editor.min.js");
					Portal::getInstance()->addStylesheet("/redistribution/bbcode/jquery.bbcode_editor.min.css");
				}
				break;
			case "bbcode.parse":
				$this->parseBBCode($data);
				break;
		}
	}

	private function parseBBCode(&$text) {
		$patterns=array();
		$replacements=array();
		if (!array_key_exists("tag_br", $this->disabled_tags)){
			array_push($patterns, "#\[br\]#is");
			array_push($replacements, "<br />");
		}
		if (!array_key_exists("tag_p", $this->disabled_tags)){
			array_push($patterns, "#\[p\](.+?)\[\/p\]#is");
			array_push($replacements, "<p>\\1</p>");
		}
		if (!array_key_exists("tag_b", $this->disabled_tags)){
			array_push($patterns, "#\[b\](.+?)\[\/b\]#is");
			array_push($replacements, "<strong>\\1</strong>");
		}
		if (!array_key_exists("tag_i", $this->disabled_tags)){
			array_push($patterns, "#\[i\](.+?)\[\/i\]#is");
			array_push($replacements, "<span style='font-style:italic'>\\1</span>");
		}
		if (!array_key_exists("tag_s", $this->disabled_tags)){
			array_push($patterns, "#\[s\](.+?)\[\/s\]#is");
			array_push($replacements, "<span style='text-decoration:line-through'>\\1</span>");
		}
		if (!array_key_exists("tag_u", $this->disabled_tags)){
			array_push($patterns, "#\[u\](.+?)\[\/u\]#is");
			array_push($replacements, "<span style='text-decoration:underline'>\\1</span>");
		}
		if (!array_key_exists("tag_url", $this->disabled_tags)){
			if($this->get("links_as_nofollow",1)) $links_nofollow=" rel=\"nofollow\""; 
			else $links_nofollow="";
			
			array_push($patterns, "#\[url=http\:\/\/(.+?)\](.+?)\[\/url\]#is");
			array_push($replacements, "<a".$links_nofollow." target=\"_blank\" href='http://\\1'>\\2</a>");
	
			array_push($patterns, "#\[url=http\:\/\/(.+?)\]\[\/url\]#is");
			array_push($replacements, "<a".$links_nofollow." target=\"_blank\" href='http://\\1'>\\1</a>");
	
			array_push($patterns, "#\[url=(.+?)\](.+?)\[\/url\]#is");
			array_push($replacements, "<a".$links_nofollow." target=\"_blank\" href='\\1'>\\2</a>");
	
			array_push($patterns, "#\[url=(.+?)\]\[\/url\]#is");
			array_push($replacements, "<a".$links_nofollow." target=\"_blank\" href='\\1'>\\1</a>");
		}
		if (!array_key_exists("tag_img", $this->disabled_tags)){
			array_push($patterns, "#\[img\](.+?)\[\/img\]#is");
			if ($this->get("image_as_thumb",0)){
				$max_width=$this->get("max_thumb_width","100%");
				array_push($replacements, "<a style='max-width:".$max_width.";display: block;' class='relpopup' href='\\1'><img style='max-width:100%;display: block;' alt=\"\" src='\\1' /></a>");
			} else {
				array_push($replacements, "<img alt=\"\" src='\\1' />");
			}
		}
		if (!array_key_exists("tag_size", $this->disabled_tags)){
			array_push($patterns, "#\[size=(.+?)\](.+?)\[\/size\]#is");
			array_push($replacements, "<span style='font-size:\\1pt'>\\2</span>");
		}
		if (!array_key_exists("tag_color", $this->disabled_tags)){
			array_push($patterns, "#\[color=(.+?)\](.+?)\[\/color\]#is");
			array_push($replacements, "<span style='color:\\1'>\\2</span>");
		}
		if (!array_key_exists("tag_ul", $this->disabled_tags)){
			array_push($patterns, "#\[list\](.+?)\[\/list\]#is");
			array_push($replacements, "<ul>\\1</ul>");
		}
		if (!array_key_exists("tag_ol", $this->disabled_tags)){
			array_push($patterns, "#\[list=(1|a|I)\](.+?)\[\/list\]#is");
			array_push($replacements, "<ol type='\\1'>\\2</ol>");
		}
		if ((!array_key_exists("tag_ol", $this->disabled_tags)) || (!array_key_exists("tag_ul", $this->disabled_tags))) {
			array_push($patterns, "#\[\*\](.*)#");
			array_push($replacements, "<li>\\1</li>");
		}
		if (!array_key_exists("tag_code", $this->disabled_tags)){
			array_push($patterns, "#\[code\](.+?)\[\/code\]#is");
			array_push($replacements, "<pre class='program_code'>\\1</pre>");
		}
		if (!array_key_exists("tag_h", $this->disabled_tags)){
			array_push($patterns, "#\[h(1|2|3|4|5|6)\](.+?)\[/h\\1\]#is");
			array_push($replacements, "<h\\1>\\2</h\\1>");
		}
		if (count($patterns)) $text = preg_replace($patterns,$replacements,$text);
		$text = str_replace("\n","<br />",$text);
	}
}
?>
<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class simpleTreeTable extends BaseObject {
	protected $_db = null;
	
	private $max_levels = 999;
	private $_parents = array();
	protected $processed_elements = array();
	protected $all_res = array();
	private $tree_level = 0;
	private $html = "";
	private $arr = array();
	private $arr_count = 0;
	private $_unique_id = "";
	
	public $table="";
	public $fld_id="";
	public $fld_parent_id="";
	public $fld_title="";
	public $fld_alias="";
	public $split_title=0;
	public $fld_deleted="";
	public $fld_enabled="";
	public $fld_orderby="";
	public $ch_table="";
	public $ch_id="";
	public $ch_field="";
	public $element_link="";
	public $element_js="";
	public $select_show_levels=false;
	public $select_top_level_id="";
	public $select_top_level_text="";
	public $select_level_padding="&mdash;";
	public $select_level_suffix="&nbsp;";
	
	public function __construct() {
		$this->initObj();
		$this->_db = Database::getInstance();
		$this->select_top_level_text = Text::_('Top level');
	}
	// получаем всех родителей для массива id 
	public function getWholeTreeUp($arr) {
		$this->_parents=array();
		if (is_array($arr)&&count($arr)) {
			foreach($arr as $id){
				$this->addParentToList($id);
			}
		} 
		return $this->_parents;
	}

	public function removeElementsFromList($_array = array(), $flatten=0) {
		$excl_counter=0;
		if($flatten){
			// соберем связь групп и родителей
			$al=array();
			foreach($this->all_res as $key=>$val) {
				$al[$val->id]=$val->parent_id;
			}
			foreach($this->all_res as $key=>$val) {
				if(in_array($val->id, $_array)){
					$ale=array();
					$ale[$val->id]=$val->parent_id;
					unset($this->all_res[$key]);
					$excl_counter++;
				}
			}
			foreach($this->all_res as $key=>$val) {
				if(in_array($val->parent_id, $_array)){
					$val->parent_id=$al[$val->parent_id];
				}
			}
		}
		return $excl_counter;
	}
	public function countElements() {
		return count($this->all_res);
	}
	private function addParentToList($id){
		$this->_parents[$id]=$id;
		foreach($this->all_res as $row) {
			if ($row->id==$id) {
				if ($row->parent_id) $this->addParentToList($row->parent_id);
			}
		}
	}
	private function is_processed($elements) {
		$result=false;
		if (count($elements)>0) {
			foreach($elements as $key=>$elem) {
				if (array_key_exists($elem, $this->processed_elements)){
					Debugger::getInstance()->warning("Tree element repeats ".$elem);	$result=true;
				} else $this->processed_elements[$elem]=1;
			}			
		}
		return $result;		
	}
	
	private function drawTreeLevelSelect($parent_id, $selected_id=0) {
		if ($this->is_processed($parent_id)) return;
		$this->tree_level++; reset($this->all_res);
		foreach($this->all_res as $res_row) {
			if (in_array($res_row->parent_id,$parent_id)) {
				if ($selected_id==$res_row->id) $selected=" selected=\"selected\""; else $selected="";
				if ($res_row->deleted) $class=" class=\"deleted\"";
				elseif($this->fld_enabled && !$res_row->enabled) $class=" class=\"disabled\"";
				else $class="";
				$this->html.= '<option value="'.$res_row->id.'"'.$selected.$class.'>';
				$current_title=$res_row->title;
				if (($this->split_title)&&(mb_strlen($current_title,DEF_CP)>$this->split_title)) {
					$current_title=htmlspecialchars(mb_substr(htmlspecialchars_decode($current_title), 0,$this->split_title,DEF_CP),ENT_QUOTES,DEF_CP)."...";
				}
				//$this->html.= "(".($this->tree_level).")".str_repeat($this->select_level_padding, $this->tree_level).$this->select_level_suffix.$current_title;
				$this->html.= ($this->select_show_levels ? "(".$this->tree_level.")".$this->select_level_suffix : "").str_repeat($this->select_level_padding, $this->tree_level).$this->select_level_suffix.$current_title;
				$this->html.= '</option>';
				$parents_arr=preg_split("/(\,)/", $res_row->id);
				if ($this->tree_level<$this->max_levels) $this->drawTreeLevelSelect($parents_arr, $selected_id);
			}
		}
		$this->tree_level--;
	}
	private function drawTreeLevelUL($parent_id, $ul_id="",$li_id_pref="", $tree_class="") {
		if ($this->is_processed($parent_id)) return;
		$item_drawed=0; $this->tree_level++; reset($this->all_res);
		foreach($this->all_res as $res_row) {
			if (in_array($res_row->parent_id,$parent_id)) {
				if (!$item_drawed) {
					if($tree_class) $tree_class=" ".$tree_class;
					if ($ul_id) $this->html.= '<ul id="'.$ul_id.'" class="tree_level_'.$this->tree_level.$tree_class.'">';
					else $this->html.= '<ul class="tree_level_'.$this->tree_level.$tree_class.'">';
					$item_drawed=1;
				}
				if ($res_row->deleted) $class=" class=\"deleted\""; 
				elseif($this->fld_enabled && !$res_row->enabled) $class=" class=\"disabled\"";
				else $class="";
				if ($li_id_pref) $li_id=" id=\"".$li_id_pref."_".urlencode($res_row->id)."\""; else $li_id=" id=\"branch_".$this->_unique_id."_".urlencode($res_row->id)."\"";
				$this->html.= "<li data-row-id=\"".$res_row->id."\"".$li_id.$class.">";
				$href = $this->element_link.$res_row->id;
				if ($this->fld_alias) $href .= "&amp;alias=".$res_row->alias; 
				if ($this->element_link) $href=" href=\"".Router::_($href)."\""; else $href="";
				if ($this->element_js) $js=" onclick=\"".sprintf($this->element_js,$res_row->id)."\""; else $js="";
				$current_title=$res_row->title;
				if (($this->split_title)&&(mb_strlen(htmlspecialchars_decode($current_title),DEF_CP)>$this->split_title)) {
					$href_title=" title=\"".$res_row->title."\"";
					$current_title=htmlspecialchars(mb_substr(htmlspecialchars_decode($current_title), 0,$this->split_title,DEF_CP),ENT_QUOTES,DEF_CP)."...";
				} else {
					$href_title='';
				}
				$this->html.= "<a".$href_title.$js.$href.">".$current_title."</a>";
				$parents_arr=preg_split("/(\,)/", $res_row->id);
				if ($this->tree_level<$this->max_levels) $this->drawTreeLevelUL ( $parents_arr, 'tree_'.$this->_unique_id."_".$res_row->id,$li_id_pref);
				$this->html.= "</li>";
			}
		}
		$this->tree_level--;
		if ($item_drawed) $this->html.=  '</ul>';
	}

	public function getTreeHTML($start_id=0, $type="ul", $id_sfx="", $name_sfx="", $selected_id=0, $li_id_pref="", $tree_class="") {
		if(!$name_sfx) $name_sfx=$id_sfx;
//		if (siteConfig::$treeDepth) $this->max_levels=(int)siteConfig::$treeDepth;
		
		$this->html=""; $this->tree_level=0;
		$parents_arr=preg_split("/(\,)/", $start_id);
		if ($type=="ul") {
			if($id_sfx) $this->_unique_id=$id_sfx; else $this->_unique_id=md5("tree");
			if (count($this->all_res)) $this->drawTreeLevelUL ($parents_arr, $id_sfx, $li_id_pref, $tree_class);
		} elseif ($type==="select") {
			if (!$selected_id) $selected=' selected="selected"'; else $selected='';
			if ($tree_class) $tree_class=" class=\"".$tree_class."\"";
			$this->html.= '<option value="'.$selected_id.'"'.$selected.'>';
			// $this->html.= "(0) ".$this->select_top_level_text;
			$this->html.= ($this->select_show_levels ? "(0)".$this->select_level_suffix : "").$this->select_top_level_text;
			$this->html.= '</option>';
			if (count($this->all_res)) $this->drawTreeLevelSelect ($parents_arr, $selected_id);
			if ($this->element_js) $js=" onchange=\"".$this->element_js."\""; else $js="";
			$this->html='<select '.$js.' id="'.$id_sfx.'" name="'.$name_sfx.'"'.$tree_class.'>'.$this->html.'</select>';
		}
		return $this->html;
	}
	private function makeTreeLevelArr($parent_id) {
		if ($this->is_processed($parent_id)) return;
		$this->tree_level++; reset($this->all_res);
		foreach($this->all_res as $res_row) {
			if (in_array($res_row->parent_id,$parent_id)) {
				$this->arr_count++;
				$this->arr[$this->arr_count]= $res_row;
				$this->arr[$this->arr_count]->level= $this->tree_level;
				$parents_arr=preg_split("/(\,)/", $res_row->id);
				if ($this->tree_level<$this->max_levels) $this->makeTreeLevelArr($parents_arr);
			}
		}
		$this->tree_level--;
	}
	public function getTreeArr($start_id=0) {
//		if (siteConfig::$treeDepth) $this->max_levels=(int)siteConfig::$treeDepth;
		$this->arr=array(); $this->tree_level=0; $this->arr_count=0;
		$parents_arr=preg_split("/(\,)/", $start_id);
		if (count($this->all_res)) $this->makeTreeLevelArr($parents_arr);
		return $this->arr;
	}
	/**
	 *
	 * @param string $exc_ids  массив исключаемых ид
	 * @param number $exc_only_childs :    Варианты состояния : исключать всех , по дереву вниз - 0,
	 * 									   исключать только детей - 1,
	 * 									   исключать только указанные ид, не затрагивая "родственные отношения" , сын за отца не ответчик.
	 * @param number $no_deleted  : показывать только не удаленные позиции(1), иначе все(0)
	 * @param number $enabled_only : показывать только включенные, опубликованные и т.п.(1) , иначе все (0)
	 * @param number $max_levels  : уровень погружения в дерево, иначе будет использоваться настройка из конфигурации сайта (20)
	 */
	public function buildTreeArrays( $exc_ids="", $exc_only_childs=0, $no_deleted=0, $enabled_only=0, $max_levels=0) {
		$this->processed_elements=array();
		if ($max_levels) $this->max_levels=(int)$max_levels;
		else { if (siteConfig::$treeDepth) $this->max_levels=(int)siteConfig::$treeDepth; }
		$sql = "SELECT a.".$this->fld_id." as id, a.".$this->fld_parent_id." as parent_id";
		if ($this->ch_table && $this->ch_field && $this->ch_id){
			$sql.= ", a.".$this->fld_title." as ch_id, b.".$this->ch_field." as title";
		} else {
			$sql.= ", a.".$this->fld_title." as title";
		}
		if ($this->fld_alias) $sql.=", a.".$this->fld_alias." as alias";
		$sql .=	", a.".$this->fld_deleted." as deleted";
		if ($this->fld_enabled) $sql .=", a.".$this->fld_enabled." as enabled";
		$sql .=" FROM #__".$this->table." AS a";
		if ($this->ch_table && $this->ch_field && $this->ch_id){
			$sql .=" LEFT JOIN #__".$this->ch_table." AS b ON b.".$this->ch_id."=a.".$this->fld_title;
		}
		$sql .=" WHERE a.".$this->fld_id."<>'0' AND a.".$this->fld_id."<>'' AND a.".$this->fld_id." IS NOT NULL";
		if ($exc_ids){
			if($exc_only_childs==1) $sql.= " AND a.".$this->fld_parent_id." NOT IN (".$exc_ids.")";
			elseif($exc_only_childs==2) $sql.= " AND a.".$this->fld_id." NOT IN (".$exc_ids.")";
			else $sql.= " AND a.".$this->fld_id." NOT IN (".$exc_ids.") AND a.".$this->fld_parent_id." NOT IN (".$exc_ids.")";
		}
		if ($no_deleted)  $sql.= " AND a.".$this->fld_deleted."=0";
		if ($enabled_only&&$this->fld_enabled)  $sql.= " AND a.".$this->fld_enabled."=1";
		if ($this->fld_orderby) $sql.= " ORDER BY a.".$this->fld_orderby.", a.".$this->fld_title;
		else $sql.= " ORDER BY a.".$this->fld_title;
		$this->_db->setQuery($sql);
		$this->all_res=$this->_db->loadObjectList();
	}
	
	
	public function getTreeAllRes() {
		return $this->all_res; 
	}
	public function setTreeAllRes($arr) {
		$this->all_res=$arr;
	}

	
	public function getBrokenParents() {
		$sql="SELECT ".$this->fld_id." as id, ".$this->fld_parent_id." as parent_id, ".$this->fld_title." as title, ".$this->fld_deleted." as deleted 
			FROM #__".$this->table." 
			WHERE ".$this->fld_parent_id." NOT IN (SELECT ".$this->fld_id." from #__".$this->table.") 
			AND ".$this->fld_parent_id."<>'0' AND ".$this->fld_parent_id."<>'' AND ".$this->fld_parent_id." IS NOT NULL";
		$this->_db->setQuery($sql);
		$brokenLinks = $this->_db->loadObjectList();
		return $brokenLinks;
	}

	public function getDeletedChildren(&$childs,$parent_id,$wholeBranch=0) {
		$this->tree_level++; reset($this->all_res);
		if ($this->is_processed($parent_id)) return;
		foreach($this->all_res as $res_row) {
			if (in_array($res_row->parent_id,$parent_id)) {
				if ($wholeBranch || $res_row->deleted) {
					$childs[$res_row->id]=$res_row->id;
					$parents_arr=preg_split("/(\,)/", $res_row->id);
					if ($this->tree_level<$this->max_levels) $this->getDeletedChildren ( $childs, $parents_arr, 1);
				} else {
					$parents_arr=preg_split("/(\,)/", $res_row->id);
					if ($this->tree_level<$this->max_levels) $this->getDeletedChildren ( $childs, $parents_arr, 0);
				}
			}
		}
		$this->tree_level--;
	}
	public function itemsForDelete(){
		$itemsForDelete=array();
		if (count($this->all_res)) $this->getDeletedChildren ($itemsForDelete, array(0));
		return $itemsForDelete;
	}
}

?>
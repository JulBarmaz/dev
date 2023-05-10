<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModelstructure extends Model {
	function getStructure($affiliate_code) {
		$tree = new simpleTreeTable;
  	$tree->table="users";
    $tree->fld_id="u_affiliate_code";
    $tree->fld_parent_id="u_referral";
    $tree->fld_title="u_nickname";
    $tree->fld_deleted="u_deleted";
    $tree->fld_orderby="u_nickname";
    $tree->element_link="index.php?module=user&amp;view=info&amp;affiliate=";
		$tree->buildTreeArrays('',0,1);
  	return $tree->getTreeHTML($affiliate_code,'ul','users_tree');
  }
	
}
?>
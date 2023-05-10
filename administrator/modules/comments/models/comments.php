<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class commentsModelcomments extends SpravModel {
	public function updateMeta() {
		if($this->meta->module=="comments" && $this->meta->tablename=="comms"){
			$reestr = Module::getInstance()->get('reestr');
			// Util::showArray($reestr);
			$grp = $this->getParentElement($reestr->get('multy_code'), 0);
			if($grp && $grp->cg_tablename) $this->meta->tablename = $grp->cg_tablename;
		}
	}
}

?>
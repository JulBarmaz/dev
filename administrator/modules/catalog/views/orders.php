<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultVieworders extends SpravView {

	public function getOrderFiles($psid){
		$sql="SELECT * FROM #__orders_files WHERE f_item_id=".$psid;
		Database::getInstance()->setQuery($sql);
		$res=Database::getInstance()->loadObjectList();
		$html_arr=array();
		if(count($res)) {
			foreach($res as $file){
				$path=BARMAZ_UF_PATH."catalog".DS."orders".DS.Files::splitAppendix($file->f_opt_file, true);
				if(Files::isImage($path)){
					$href=BARMAZ_UF."/catalog/orders/".Files::splitAppendix($file->f_opt_file);
					$html_arr[]="<a class=\"relpopup\" href=\"".$href."\">".$file->f_opt_title.": ".$file->f_opt_file."</a>";
				} else {
					$href=Router::_("index.php?module=catalog&task=downloadFile&folder=".urlencode("orders/".Files::getAppendix($file->f_opt_file))."&filename=".urlencode($file->f_opt_file));
					$html_arr[]="<a target=\"_blank\" href=\"".$href."\">".$file->f_opt_title.": ".$file->f_opt_file."</a>";
				}
			}
		} else {
			$html_arr[]=Text::_("Files absent");
		}
		return implode("<br />", $html_arr);	
	}
	
}
?>
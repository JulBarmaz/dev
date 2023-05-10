<?php
/*!
 * BARMAZ-CMS
 * Copyright (c) BARMAZ Group
 * Web: https://BARMAZ.ru
 * Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
 * Revision: 1975 (2020-04-23 17:36:30)
 */
defined('_BARMAZ_VALID') or die("Access denied");
//Event::raise("sprav_view.modify_form.prepared", array("module"=>$module, "class_name"=>__CLASS__, "func_name"=>__FUNCTION__, "meta"=>$meta, "row"=>$row), $frm);
class sprav_viewPluginmodify_form extends Plugin {
	protected $_events=array("sprav_view.modify_form.prepared");
	protected function setParamsMask(){
		parent::setParamsMask();
	}
	protected function onRaise($event,&$data) {
		// это у нас объект формы
		$module=$this->getParam("module");
		$mdl = Module::getInstance($module);
		$reestr = $mdl->get('reestr');
		//var_dump($reestr->get('meta'));
		//var_dump($reestr);
		switch($module){
			case 'objects':
				$view=$mdl->get('view');
				switch($view){
					case 'snt_counters':						
						$l_zam=$reestr->get("arr_zam");
						if(is_array($l_zam)&&count($l_zam))
						{
							foreach($l_zam as $kf=>$vf)
							{
								$data->setInputValue($kf,$vf);
							}	
						}	
					break;	
				}
				break;
		}
		
		
		//var_dump($data);
	}

}
?>
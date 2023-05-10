<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Translator extends BaseObject {
	
	protected	$_db			= null;
	protected	$arr_lang		=array();
	protected	$list_lang      =array();
	protected   $translatedFieldTypes=array(); // val_type полей
	protected   $translatedInputTypes=array(); // input_type полей
	protected   $exclude_modules=array();  // перечень исключаемых модулей
	protected   $exclude_tables=array();  // перечень исключаемых модулей
	
	public function __construct() {
		$this->initObj();
		$this->_db = Database::getInstance();
		$this->list_lang=$this->getAnotherLang();
		$this->translatedFieldTypes=array('string','text');
		$this->translatedInputTypes=array('label','text','formated','textarea','texteditor');
		$this->exclude_modules=array('aclmgr','acrm','catalog','comments','conf','feedback','forum',
		'help','installer','mail','polls','site','service','forum','user');
		//'menus',
		$this->exclude_tables=array('modules');
		//echo $this->fields4check;
	}
	/**
	 * Запись в базу переводов
	 * @param int $t_psid  - ид элемента
	 * @param array $doplang - массив дополнительных языков
	 * @param string $t_module  - модуль
	 * @param string $t_table  - таблица
	 * @param array $langinfo - масив значений для записи в формате двумерного массива  nazv[lang][field]=value;
	 * return bool 
	 */
	public function saveData($t_psid,$lang,$t_module,$t_table,$langinfo,$meta=array())
	{
		//echo Util::traceStack();
		//Util::showArray($meta);
		//Util::showArray($_REQUEST);
		//Util::showArray($_FILES,"f  ".time());
		// повторная проверка на наличие таблицы - но имхо место этому не тут 
		$this->checkTranslateTable($t_table);
		$t_table=$t_table."_trans";
		$arr_fld=Request::getSafe('dest_fld');// список названий полей
		$arr_fld=array_flip($arr_fld);
		$main_list=array_flip($meta->field);
		$type_fld=Request::getSafe('type_fld');// список типов полей
 	    if(count($langinfo)){
				// в этом языке что-то есть , начинаем формровать запросы
				foreach($langinfo as $t_field=>$t_value){
					$t_image=''; // пока всегда пустой , но вообще сюда бы надо передавать картинку, проверять ее по типу
					// основной ключ -  поле, язык , псид
					// тут проверим поле на тип и если это картинка  - то сохраним ее в путях сведений
					//echo $lang."  ".$t_field." ".$type_fld[$arr_fld[$t_field."_".$lang]]."<br />";
					$index=$main_list[$t_field];
					switch($type_fld[$arr_fld[$t_field."_".$lang]])
					{
						case 'image':	// пишем картинку
							$temp="";
							
							if (isset($_FILES['transl_'.$lang]) && $_FILES['transl_'.$lang]['name'][$t_field]) {
								//echo "<pre>";var_dump('img',$t_field,$lang,$_FILES['transl_'.$lang]['name'][$t_field]);echo "</pre>";
								
								if(isset($meta->upload_path[$index])) $subdir=$meta->upload_path[$index];
								else $subdir='';
								// дополним массив сведений о файле в стандартной структуре во избежание ошибок в базовом классе
								$_FILES[$t_field."_".$lang]['name']=$_FILES['transl_'.$lang]['name'][$t_field];
								$_FILES[$t_field."_".$lang]['type']=$_FILES['transl_'.$lang]['type'][$t_field];
								$_FILES[$t_field."_".$lang]['size']=$_FILES['transl_'.$lang]['size'][$t_field];
								$_FILES[$t_field."_".$lang]['tmp_name']=$_FILES['transl_'.$lang]['tmp_name'][$t_field];
								$_FILES[$t_field."_".$lang]['error']=$_FILES['transl_'.$lang]['error'][$t_field];
								// тут еще можно очистить массив от прежней нотации - но вроде как это не критично
								
								
								$result=Files::uploadUserFile($t_field."_".$lang, $meta->module, $subdir);
								if (isset($result['filename'])) $temp=$result['filename'];
								else {									
									//echo "<pre>";var_dump('img File upload error',$t_field,$lang);echo "</pre>";
									$temp="";
								}
							} else {
								//echo "<pre>";var_dump('imgno',$t_field,$lang);echo "</pre>";
								
								$temp="";
							}
							// удаляем предыдущую
							
							$old_file=Request::getSafe($t_field."_".$lang."_oldfile","");
							$delete_file=Request::getInt($t_field."_".$lang."_delete",0);
							//echo "<pre>";var_dump('delete img conditions :',$temp,$lang,$delete_file);echo "</pre>";
							
							if ((!$temp)&&(!$delete_file))  continue 2; // ни загрузка ни удаление
							else {
								if ($old_file) {	// вот только в этом случае удаляем
									if(isset($meta->upload_path[$index])) $subdir=$meta->upload_path[$index];
									else $subdir='';
									$file=BARMAZ_UF_PATH.$meta->module.DS.$subdir.DS.Files::splitAppendix($old_file,true);
									Files::delete($file, true);
									$res_delete=$this->deleteValuefield($t_table,$t_field,$lang,$t_psid);
									//echo "<pre>";var_dump('delete img ',$t_field,$lang,$file,$res_delete);echo "</pre>";
									
								}
							}
							if($temp) $t_value=$temp;						
							break;
						default:
							break;
					}
					// добавим сюда постфикс от языка к алиасу при сохранении, если в списке изменяемых полей есть алиас
					// определяем его по метаданным
					if(isset($meta->alias_field)&&$meta->alias_field&&$t_value&&$t_field==$meta->alias_field)
					{
						
						// первое - проверим его на наличие алиаса уже - попутно сравним его с сохраняемым языком
						$al_lang=$this->getLangByString($t_value);
						$t_value=str_replace(" ","-",trim($t_value));
						if($al_lang)							
						{ //язык есть и он не совпадает с текущим сохраняемым
							if($al_lang!=$lang)
							{ // заменяем его в алиасе 
								$t_value=substr($t_value,0,strrpos($alias, '-')+1).$lang;
							}	
						}
						else 
						{
							$t_value=$t_value."-".$lang;
						}	
						//echo $t_value;
					}	
					
					
					
					
					if($t_value){
						$sql="insert into `#__".$t_table."`
						  (t_id,t_field,t_lang,t_psid,t_value,t_deleted,t_enabled)
							values
						  (NULL,'".$t_field."','".$lang."',".$t_psid.",'".$t_value."',0,1)
						  ON DUPLICATE KEY UPDATE 	t_value='".$t_value."'";
						$this->_db->setQuery($sql);
						$this->_db->query(); // сохранили данные этого перевода					
					}
					else{
						// тут условие , если данных нет - значит нам оно не надо и надо удалить из базы значение если оно там есть
						// вместо того, чтобы держать его пустым
						// однако есть проблемы что удаление происходит после записи - поскольку у нас идет цикл по языкам
						// надо думать как правильно организовать очистку
						// на текущую очистку нормально отрабатывает -  ON DUPLICATE KEY UPDATE 
						// возможно достаточно будет после записи - сделать очистку по этому посту по ключам и пустому значению t_value
						
						// этот вариант -косячит
						/*
						$sql="delete from `#__".$t_table."` where t_field='".$t_field."' and t_lang='".$lang."' and t_psid=".$t_psid;
						$this->_db->setQuery($sql);
						$this->_db->query(); // почистили
						*/
					}
			}// окончание пробежки по языку
		} // был ли язык вообще - может тут сделать масовое удаление по индексу модуль ,таблица, язык ???
		return true; // возвращаем пока так
	}
	
	public function deleteValuefield($t_table,$t_field,$lang,$t_psid)
	{
		$sql_delete="delete from `#__".$t_table."` where t_field='".$t_field."' and t_lang='".$lang."' and t_psid=".$t_psid;
		$this->_db->setQuery($sql_delete);
		//echo $this->_db->getQuery();
		return $this->_db->query(); // почистили
	}
	/**
	 * возвращает нормализованный массив по выбранному языку из указанной таблицы
	 * $arr_data[$val->t_psid][$val->t_field]=$val->t_value;
	 */
	public function getListTranslateData($tablename,$lang,$arr_field=array())
	{
		
		$arrTranslateVal=array();
		$table_transl=$tablename."_trans";
		if (!$this->_db->checkTableExists($this->_db->getPrefix().$table_transl)) return $arrTranslateVal;
		$dop_sql='';
		if(count($arr_field))
		{
			$dop_sql=" and t_field in ('".implode("','",$arr_field)."')";
		}	
		$sql="select t_psid,t_id,t_field,t_lang,t_value from `#__".$table_transl."` where t_lang ='".$lang."'".$dop_sql;
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObjectList();
		if($res&&count($res))
		{
			foreach($res as $val)
			{
				$arrTranslateVal[$val->t_psid][$val->t_field]=$val->t_value;
			}	
		}	
		
		return $arrTranslateVal;
		
		
	}
	
	/**
	 * 
	 * @param int $psid
	 * @param string $module
	 * @param string $tablename - имя основной таблицы
	 * @param string $translateList  - поля разделенные запятыми
	 * @param string $list_lang  - языки разделенные запятыми
	 * @return boolean|array|string
	 */
	 
	public function getlistElementTranslate($psid,$module,$tablename,$translateList)
	{
		$arrTranslateVal=array();
		$table_transl=$tablename."_trans";
		// вообще место этому не тут - нефиг каждый раз дергать проверку и создание таблицы
		// но если нет таблицы - просто выходим, нет данных на сейве она создастся
		if (!$this->_db->checkTableExists($this->_db->getPrefix().$table_transl)) return false;
		
		$list_lang=implode("','", array_keys($this->list_lang));
		$sql="select t_id,t_field,t_lang,t_value from `#__".$table_transl."` 
		where t_psid=".$psid." and t_field in ('".$translateList."') and t_lang in ('".$list_lang."')";
		
		$this->_db->setQuery($sql);
		$result=$this->_db->loadObjectList();
		if(!$result) return false;
		foreach($result as $val)
		{
			$arrTranslateVal[$val->t_lang][$val->t_field]=html_entity_decode($val->t_value);
		}
		return $arrTranslateVal;
	}
	/**
	 * получаем список языков кроме основного
	 * @return array
	 */
	public function getAnotherLang()
	{
		// добавляем сведения по встроенным языкам
		// язык сайта по умолчанию // язык на котром содержатся основные записи
		$def_lang=siteConfig::$defaultLanguage;
		// доступные языки
		$list_lang=Text::getAllLanguages();
		unset($list_lang[$def_lang]);
		return $list_lang;
	}
	/**
	 * возвращаем массив языков ключ язык - значение номер вкладки
	 * @return array|number[]
	 */
	public function getArrLang()
	{
		return $this->arr_lang;
	}
	
	/**
	 * Получаем поля текущей метадаты которые предназначены к переводу
	 * @param array $meta
	 * @return array массив полей : ключ - имя поля => значение - тип ввода
	 */
	public function getTranslateList($meta)
	{
		$translateList=array();
		foreach($meta->field as $key=>$fld)
		{
			if($meta->translate_value[$key]==1)
				$translateList[$fld]=$meta->input_type[$key];
		}
		return  $translateList;
	}
	/**
	 * Подготовка к выводу на экран заголовоков панелей переводов в карточках модификации сущностей
	 * выводит сверстанные дополнительные панели
	 * @param int $_activeTab - активная вкладка
	 * @param int  $last_tab  - номер последней вкладки на входе
	 * возвращает верстку
	 * @return   $dop_tab - количество добавленных вкладок    
	 */
	public function prepareTranslatorPanelHead($_activeTab,$last_tab,&$dop_tab)
	{
		$html='';		
		$nom_lang=0;
		$arr_lang=array();
		foreach ($this->list_lang as $lang=>$langrow)
		{
			$nom_lang++;
			$_akey=(int)$last_tab+$nom_lang;
			$arr_lang[$lang]=$_akey;
			if ($_akey==$_activeTab) $_class=' active'; else $_class="";
			$html.="<li class=\"switcher".$_class."\">";		
			$html.="<a href=\"#tab_".$_akey."\" style=\"background-image: url(/images/flags/".$lang.".png); background-repeat: no-repeat; background-size: contain; background-position: 8px center; padding-left: 58px;\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Translate to")." ".$lang."</a>";			
			$html.="</li>";
		}
		$this->arr_lang=$arr_lang;
		$last_tab=$_akey; // изменяем номер последней вкладки после языков
		$dop_tab=$nom_lang; // добавлено вкладок
		return $html;
	}
	
	/**
	 * Подготовка к выводу на экран панелей переводов в карточках модификации сущностей
	 * выводит сверстанные дополнительные панели   
	 * @param array $meta - метадата таблицы
	 * @param array $list_lang - список языков для которых нужны переводы
	 */
	public function prepareTranslatorPanel($_activeTab,$meta,$frm=false)
	{
		//Util::showArray($frm);
		// возможно для единообразия имеет смысл тут дополнить штатный aform - возможно придется переделывать
		// или переводить(переносить) еще описания к полям, если они будут к остальным
		// но тогда надо сюда передавать форму 
		$el_name=$frm->getInputValue($meta->namestring);
		//var_dump($el_name);
		$html_tr='';		
		$ar_fldname=array_flip($meta->field);		
		$mdl = Module::getInstance($meta->module);
		$psid = $mdl->get('reestr')->get('psid');
		$arr_lang=$this->arr_lang;
		
		$alias_field=$meta->alias_field;
		//$list_lang=$this->getAnotherLang();
		$list_lang=$this->list_lang;
		Portal::getInstance()->addScript('/js/BARMAZ.translator.js');
		$css_transl="<style>
		div.pan-buttons {text-align:left;padding:4px;}
		div.pan-buttons span{display:inline-block;margin:2px 10px;}
		div.pan-buttons span#tr_but_restore{background-color:silver; cursor:none;}
		</style>";
		Portal::getInstance()->addStyle($css_transl);
		//Util::showArray($list_lang,'list_lang');
		//Util::showArray($arr_lang,'arr_lang');
		$translate_list=$this->getTranslateList($meta);
		//Util::showArray($translate_list,'translate_list');
		$translate_data=$this->getlistElementTranslate($psid, $meta->module, $meta->tablename, implode("','", array_keys($translate_list)));
		// таблица относительно которой пишем
		$html_tr.=HTMLControls::renderHiddenField('table',$meta->tablename);// таблица , относительно которой пишем
		foreach ($list_lang as $lang=>$langrow)
		{
			$nom_pan=$arr_lang[$lang];
			$html_tr.=HTMLControls::renderHiddenField("doplang[]",$lang,"doplang_".$lang); // список дополнительных языков , которые мы добавляем тут
			if ($nom_pan==$_activeTab) $_class=" active"; else $_class="";
			$html_tr.="<div class=\"tab-pane".$_class."\" id=\"tab_".$nom_pan."\">";
			$html_tr.="<h4 class=\"title\">".$el_name."</h4>";
			$html_tr.="<div class=\"modify-wrapper row\"><fieldset>";
			$html_tr.="<legend>".Text::_("Translator")." - ".$lang.":</legend>";
			// тут место для инструментов связанных с переводом - например копирование содержания полей ввода, подлежащих переводу из основных в переводные поля
			// или для иврита транслятор в латиницу			
			$html_tr.="<div class=\"pan-buttons\"><span id=\"tr_but_copy\" class=\"commonButton btn btn-info\" onclick=copyDataForTranslate('tab_".$nom_pan."','".$lang."')>".Text::_("Copy fields data")."</span>";
			// это должно быть активным только если есть что сохранять
			$html_tr.="<span id=\"tr_but_restore\" class=\"btn\">".Text::_("Restore fields data")."</span>";
			$html_tr.="</div>";
			
			foreach($translate_list as $tdkey=>$vdkey)
			{
				// тут заполняем значения
				if(isset($translate_data[$lang][$tdkey])){
				  $dat_fld=$translate_data[$lang][$tdkey];}
				else $dat_fld="";

				//$html_tr.="<div class=\"modify-editor-wrapper\" id=\"wrapper-".$tdkey."_".$lang."\">";
				$html_tr.=HTMLControls::renderHiddenField('dest_fld[]',$tdkey."_".$lang,"dest_fld_".$tdkey."_".$lang);// поле в котором указатель откуда копировать
				$html_tr.=HTMLControls::renderHiddenField('backup_fld['.$tdkey.'_'.$lang.']','',"backup_fld_".$tdkey."_".$lang);// поле куда сохраним данные после очистки ( на всякий случай )
				$html_tr.=HTMLControls::renderHiddenField('type_fld[]',$vdkey,"type_fld_".$tdkey."_".$lang);// указатель с типом поля
				// тип поля определяем по значению ключа - умолчание - строка
				switch($vdkey){
					case "textarea":
					case "formated":
					$html_tr.="<div class=\"modify-editor-wrapper\" id=\"wrapper-".$tdkey."_".$lang."\">";
					$html_tr.="<div class=\"row\"><div class=\"modify-label col-sm-12\">".HTMLControls::renderLabelField($tdkey."_".$lang,Text::_($meta->field_title[$ar_fldname[$tdkey]]))."</div></div>";
					$html_tr.="<div class=\"row\"><div class=\"modify-input col-sm-12\">".HTMLControls::renderBBCodeEditor($tdkey."_".$lang,"transl_".$lang."[".$tdkey."]",$dat_fld)."</div></div>";
					$html_tr.="</div>";
					
					break;
					case "texteditor":
						$html_tr.="<div class=\"modify-editor-wrapper\" id=\"wrapper-".$tdkey."_".$lang."\">";
						$html_tr.="<div class=\"row\"><div class=\"modify-label col-sm-12\">".HTMLControls::renderLabelField($tdkey."_".$lang,Text::_($meta->field_title[$ar_fldname[$tdkey]]))."</div></div>";
						$html_tr.="<div class=\"row\"><div class=\"modify-input col-sm-12\">".HTMLControls::renderEditor($tdkey."_".$lang,"transl_".$lang."[".$tdkey."]",$dat_fld)."</div></div>";
						$html_tr.="</div>";
						
					break;
					case 'image':  // вот надо ли  -
						
						$img_val=$dat_fld; // тут надо достать изображение из $this->_inputs[$name]["VAL"] но местное
						$tmpl_img_path="";
						$tmpl_img='';
						$index=$ar_fldname[$tdkey];
						if(isset($meta->img_module[$index])&&$meta->img_module[$index]!="")
						{
							$tmpl_img=BARMAZ_UF."/".$meta->img_module[$index]."/".$meta->upload_path[$index]."/".Files::splitAppendix($img_val);
							$tmpl_img_path=BARMAZ_UF_PATH.$meta->img_module[$index].DS.str_replace("/",DS,$meta->upload_path[$index]).DS.Files::splitAppendix($img_val, true);							
						}
						else
						{
							$tmpl_img=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$index]."/".Files::splitAppendix($img_val);
							$tmpl_img_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/",DS,$meta->upload_path[$index]).DS.Files::splitAppendix($img_val, true);
						}
						//var_dump($dat_fld,$tmpl_img,$tmpl_img_path);
						$html_tr.="<div class=\"modify-image-wrapper\" id=\"wrapper-".$tdkey."_".$lang."\"><div class=\"row\">";
						
						$html_tr.="<div class=\"modify-label col-sm-4\">".HTMLControls::renderLabelField($tdkey."_".$lang,Text::_($meta->field_title[$ar_fldname[$tdkey]]))."</div>";
						$js="clearfieldVal('".$tdkey."_".$lang."')";
						$html_tr.="<div class=\"fileselector\">";
						
						$html_tr.=HTMLControls::renderButton($tdkey."_".$lang."_clear", "", "button", "", "clrfile",$js, Text::_("Clear"));
						//$html_tr.=HTMLControls::renderInputFile($tdkey."_".$lang, $img_val,20,'file_'.$tdkey."_".$lang, '');
						$html_tr.=HTMLControls::renderInputFile("transl_".$lang."[".$tdkey."]", $img_val,20,'file_'.$tdkey."_".$lang, '');
						$html_tr.=HTMLControls::renderHiddenField("transl_".$lang."[".$tdkey."]",$img_val);
						$html_tr.="</div>";
						
						$html_tr.="<div class=\"modify-image\">";
						if ((file_exists($tmpl_img_path))&&(is_file($tmpl_img_path))) {
							if (Files::mime_content_type($tmpl_img_path)=="application/x-shockwave-flash") {
								$html_tr.='<object type="application/x-shockwave-flash" data="'.$tmpl_img.'" width="100" height="100">
										<param name="movie" value="'.$tmpl_img.'">
										<param name="quality" value="high">
										<param name="wmode" value="transparent">
									</object>';
							} else {
								//if ($this->_is_ajax) {
								//	$html_tr.="<img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" />";
								//} else {
									$html_tr.="<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" /></a>";
								//}
							}
						} else  {
							$tmpl_img="/images/nophoto.png";
							$html_tr.="<img src=\"".$tmpl_img."\" width=\"100\" alt=\"\" />";
						}
						
						$html_tr.="</div>";
						
						$html_tr.="<div class=\"delete-image\">";
						$html_tr.=HTMLControls::renderHiddenField($tdkey."_".$lang."_oldfile",$img_val);
						$html_tr.=HTMLControls::renderCheckbox($tdkey."_".$lang."_delete", "0","1");
						$html_tr.=HTMLControls::renderLabelField($tdkey."_".$lang."_delete",Text::_('Mark for delete'));
						$html_tr.="</div>";
						
						$html_tr.="</div></div>";
						
					break;
					default:
						
						$html_tr.="<div class=\"modify-wrapper row\" id=\"wrapper-".$tdkey."_".$lang."\">";
						$html_tr.="<div class=\"modify-label col-sm-4\">".HTMLControls::renderLabelField($tdkey."_".$lang,Text::_($meta->field_title[$ar_fldname[$tdkey]]))."&nbsp;".$frm->renderBalloonFor($meta->field[$ar_fldname[$tdkey]],false);
						if($tdkey==$alias_field){  // формируем балон по алиасу
							$text="The alias string will be automatically converted into a line with hyphens between words and supplemented with a postfix of the current language";
							$html_tr.=HTMLControls::renderBalloonButton($text);							
						}
						$html_tr.="</div>";
						$html_tr.="<div class=\"modify-input col-sm-8\">".HTMLControls::renderInputText("transl_".$lang."[".$tdkey."]",$dat_fld,"","",$tdkey."_".$lang)."</div>";
						$html_tr.="</div>";
						
					break;
					
				}
			}
			$html_tr.="</fieldset></div>";
			$html_tr.="</div>";
		}
		return $html_tr;
	}
	/**
	 * (@TODO) Сервисная функция , которая вызывает модуль, проверяет необходимость переводов в таблице модулей 
	 * если да,то 
	 * 1. Поднимает все его метадаты.
	 * 2. Проверяет для таблиц модуля(по метадатам) наличие таблиц перевода. Если не найдены - создает их.
	 * 2.1. Если есть апдейты структур таблиц перевода - проводит их. 
	 * 3. Смотрит в общей системной таблицы метадаты наличие флагов " требуется перевод " по этим
	 * таблицам. Если ни одного флага не установлено - запускает механизм простановки по критериям 
	 * функции setFieldForTranslate. 
	 * Если хоть один флаг установлен - считается что администратор поработал - и механизм простановки не запускается.
	 * Таким образом будут обработаны вновь появляющиеся таблицы модулей.
	 *   
	 * @param string $module_name
	 */
	public function checkModuleForTranslate($module_name){
		
		/* есть модули для которых перевод по справочнику не нужен*/
		$html='<div class="str_module">';
		$html.="<p class=\"title_module\">".Text::_("Being ready to translate check ").':<span>'.$module_name."</span></p>";
		if(in_array($module_name,$this->exclude_modules)) {
			$html.="<p >".Text::_("Module excluded for translate").':'.$module_name."</p>";
			return '';
		}
		
		/* получаем список наборов по модулю из таблицы 
		 * ( те метадаты которых в таблице нет - побоку, хотя линковочные тут тоже пролетят мимо)
		 * может правильнее будет считать файлы прям из папок - увидим
		 * */
		  
		$sql="select distinct m_view,m_layout,m_admin_side from #__metadata where m_module='".$module_name."'";
		$this->_db->setQuery($sql);
		$listForMeta=$this->_db->loadObjectList();
		// есть список описанных метадат
		// достаем имена таблиц из метадат
		//Util::showArray($listForMeta);
		// пути для файлов
		$adm_path = PATH_SITE.'modules'.DS.$module_name.DS.'metadata';
		$site_path = PATH_FRONT.'modules'.DS.$module_name.DS.'metadata';
		
		if(is_array($listForMeta)&&count($listForMeta))
		{
			$usesTable=array();
			foreach($listForMeta as $val){
				if($val->m_admin_side)  $meta_file=$adm_path.DS.$val->m_view.".".$val->m_layout.".php";
				else $meta_file=$site_path.DS.$val->m_view.".".$val->m_layout.".php";
				if(is_file($meta_file)){
					$html.="<p >".Text::_("File meta").':'.$meta_file."</p>";
					// странная инверсия обращения стороны - зачем???
					$meta=new SpravMetadata($module_name,$val->m_view,$val->m_layout,false,false,abs($val->m_admin_side-1));
					$res=$this->checkTranslateTable($meta->tablename);
					$html.="<p>".Text::_("checking for a translation table").':'.$meta->tablename."</p>";
					// признак обработки метадаты 
					$sumtranslate=array_sum($meta->translate_value);
					$html.="<p>".Text::_("checking for signs of field translation").':'.$sumtranslate."</p>";
					// попытка многократно не насиловать одну и ту же таблицу
					$tableFldList=array(); // список полей таблицы для перевода
					if($sumtranslate==0){
						// нет ни одного поля для перевода , тестим поля на входимые типы данных, формируем массив
						// 
						foreach($meta->val_type as $kt=>$vt)
						{
							//var_dump($val,in_array($val,$this->translatedFieldTypes));
							if(in_array($vt,$this->translatedFieldTypes)
								&& in_array($meta->input_type[$kt],$this->translatedInputTypes))
							{
								
								$tableFldList[$meta->field[$kt]]=$vt;
							}
						}	
						
					}
					//$usesTable[$meta->tablename]['counter']=$sumtranslate;
					// тут возможна опасность - когда в разных метадатах по одной таблице будут разные типы - перезапишется последними данными из обработанных
					//$usesTable[$meta->tablename]['fields']=$tableFldList;
					if(count($tableFldList)){
					//	var_dump($module_name,$val->m_view,$val->m_layout);
					$restranslate=$this->setFieldForTranslate($module_name,$val->m_view,$val->m_layout,array_keys($tableFldList));
					// проставим в таблицу признаки перевода по полям 
					$html.="<p>".Text::_("set signs of field translation for ").':'.$restranslate."</p>";
					}else {
						$html.="<p>".Text::_("For translation table").':'.$meta->tablename."  ".Text::_(" translation fields not found")."</p>";
					}
					
					
				}
				else 
				{
					$html.="<p class=\"invalid\">".Text::_("File not found").':'.$meta_file."</p>";
				}	
		
			}
			
		}	
		$html.='</div>';
		return $html;
		
	}
	
	/**
	 * возвращает список полей, необходимых для перевода
	 */
	public function getListFieldsForTranslation($meta)
	{
		$tableFldList=array(); // список полей таблицы для перевода
		if($sumtranslate==0){
			// нет ни одного поля для перевода , тестим поля на входимые типы данных, формируем массив
			//
			foreach($meta->val_type as $kt=>$vt)
			{
				//var_dump($val,in_array($val,$this->translatedFieldTypes));
				if(in_array($vt,$this->translatedFieldTypes)
						&& in_array($meta->input_type[$kt],$this->translatedInputTypes))
				{
					
					$tableFldList[$meta->field[$kt]]=$vt;
				}
			}
		}
		return $tableFldList;
		
	}
	
	/**
	 * устанавливаем признаки полей которые требуется переводить(заменять содержимое)
	 * по умолчанию это текстовые поля и поля текстареа, еще может быть картинки
	 * устанавливаем в таблицу метататы эти признаки, если какие то поля не нужно переводить,
	 * то можно зайти в панель управления видимостью полей и там снять эти галочки
	 */ 	 
	public function setFieldForTranslate($m_module,$m_view,$m_layout,$arr_field)
	{
		$list_fld=implode("','",$arr_field);	
		$sql="update `#__metadata` set m_translate_value=1 where
		m_module = '".$m_module."' and	m_view = '".$m_view."' and m_layout = '".$m_layout."' and m_field in('".$list_fld."')";
		$this->_db->setQuery($sql);
		//echo $this->_db->getQuery();
		$this->_db->query();
		return $this->_db->getAffectedRows();
	}
	
	public function checkTranslateTable($tablename)
	{
		if(in_array($tablename,$this->exclude_tables)) return true;
		$result=true;
		$table="#__".$tablename."_trans";
		if(!$this->_db->checkTableExists($table))
		{
			$result=$this->createTranslateTable($table);
		}	
		return $result;
	}
	public function createTranslateTable($table)
	{
		$sql="CREATE TABLE `".$table."` (
				`t_id` int(11) NOT NULL AUTO_INCREMENT,
				`t_field` varchar(80) NOT NULL,
				`t_lang` varchar(10) DEFAULT NULL COMMENT 'сокращение языка для которого переводим',
				`t_value` text COMMENT 'значение перевода',
				`t_deleted` tinyint(1) DEFAULT '0',
				`t_enabled` tinyint(1) DEFAULT '1',
				`t_psid` int(11) DEFAULT NULL COMMENT 'ид ключевого поля в таблице',
				PRIMARY KEY (`t_id`),
				UNIQUE KEY `main` (`t_field`,`t_lang`,`t_psid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	/**
	 * 
	 * @param array $arr_tables
	 * @param array $arr_data
	 * @param array $arr_psid
	 * @param array $arr_keys
	 * @param string $lang  - язык по которому подбираем переводы
	 * return updated array of objects
	 * пример подготовки данных в модуле класса
	 * $arr_tables=array('blogs_posts'); - таблицы по которым надо достать переводы 
	   $arr_data=array('blogs_posts'=>$posts); - данные которые надо апдейтить если у нас список
	   или
	   $arr_data=array('blogs_posts'=>array($post)); - данные которые надо апдейтить если у нас один объект
	   $arr_psid['blogs_posts'][$post->p_id]=$post->p_id;             - списки ид записей по которым надо провести апдейт по указанной в ключе таблице	  
	   $arr_psid['users'][$post->p_author_id]=$post->p_author_id;
	   $arr_key['blogs_posts']='p_id'; - имя поля ключа таблицы
	   $arr_key['users']='u_id';
	 * 
	 */
	public function updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_keys,$lang)
	{
		// сначала выясняем по какиим таблицам работаем
		foreach($arr_tables as $table)
		{
			// перечень задействованных ид
			$list_psid=implode(",",array_keys($arr_psid[$table]));
			$sql_tdata="select * from `#__".$table."_trans` where t_lang='".$lang."' and t_enabled=1 and t_deleted=0 and t_psid in (".$list_psid.")";
			$this->_db->setQuery($sql_tdata);
			$translate_data=$this->_db->loadObjectList();
			$t_data=array();
			if($translate_data)
			{
				// готовим массив для преобразования
				/*[t_id]	====> 	85 (typeof string)
				[t_field]	====> 	p_theme (typeof string)
				[t_lang]	====> 	en (typeof string)
				[t_value]	====> 	What are the signs of pregnancy? (typeof string)
				[t_deleted]	====> 	0 (typeof string)
				[t_enabled]	====> 	1 (typeof string)
				[t_psid]	====> 	24 (typeof string)
				*/
				foreach($translate_data as $v_tdata)
				{
					$t_data[$v_tdata->t_psid][$v_tdata->t_field]=$v_tdata->t_value;
				}	
			
			}
			// производим подмены
			if(isset($arr_data[$table]) && count($arr_data[$table]))
			{	
				foreach($arr_data[$table] as $val_data) // по всему набору данных
				{
					// находим значение ид текущей записи
					$rec_id=$val_data->$arr_keys[$table];
					//var_dump($rec_id);
					foreach($val_data as $e_field=>$e_data) // по экземпляру
					{
						if(isset($t_data[$rec_id][$e_field]))
						{
							$val_data->$e_field=html_entity_decode($t_data[$rec_id][$e_field]);
						}	
					  	
					}
				}	
				//Util::showArray($arr_data[$table],'inner replacement');
			}			//Util::showArray($t_data);
		}							
		return $arr_data;
	}
	/**
	 * Возвращаем ид записи по алиасу на языке перевода 
	 * @param string $module
	 * @param string $view
	 * @param string $alias
	 * @param string $lang
	 * @return int id alias
	 * @NB! алиасы дергаются постоянно с фронтенда - так что проверку тут точно делать ни к чему - все должно существовать 
	 * 
	 */
	public function getIdByAlias($module,$view,$alias,$lang){
		switch($module){
			case "blog":
				switch($view){
				case "category":
					$sql="SELECT t_psid FROM #__blogs_cats_trans WHERE t_lang='".$lang."' and t_field='bc_alias' and t_value='".$alias."'";
				break;
				case "list":
					$sql="SELECT t_psid FROM #__blogs_trans WHERE t_lang='".$lang."' and t_field='b_alias' and t_value='".$alias."'";					
				break;
				case "post":
					$sql="SELECT t_psid FROM #__blogs_posts_trans WHERE t_lang='".$lang."' and t_field='p_alias' and t_value='".$alias."'";
				break;
				default:
				return 0;
				break;				
			}
			break;
			case "articles":
				$sql="SELECT t_psid FROM #__articles_trans WHERE t_lang='".$lang."' and t_field='a_alias' and t_value='".$alias."'";
			break;
			default:
			 return 0;
			break;
		}
		$this->_db->setQuery($sql);
		return intval($this->_db->loadResult());
	}
	public function getAliasByPsid($module,$view,$lang,$psid)
	{
		switch($module){
			case "blog":
				
				switch($view){
					case "category":
						if($lang==siteConfig::$defaultLanguage)	$sql="SELECT bc_alias FROM #__blogs_cats WHERE bc_id=".$psid;
						else $sql="SELECT t_value FROM #__blogs_cats_trans WHERE t_lang='".$lang."' and t_field='bc_alias' and t_psid=".$psid;
							
						break;
					case "list":
						if($lang==siteConfig::$defaultLanguage) $sql="SELECT b_alias FROM #__blogs WHERE b_id=".$psid;
						else $sql="SELECT t_value FROM #__blogs_trans WHERE t_lang='".$lang."' and t_field='b_alias' and t_psid=".$psid;
						break;
					case "post":
						if($lang==siteConfig::$defaultLanguage) $sql="SELECT p_alias FROM #__blogs_posts WHERE p_id=".$psid;
						else $sql="SELECT t_value FROM #__blogs_posts_trans WHERE t_lang='".$lang."' and t_field='p_alias' and t_psid=".$psid;
						break;
					default:
						return 0;
						break;
				}
				break;
			default:
				return 0;
				break;
		}
		$this->_db->setQuery($sql);
		//echo $this->_db->getQuery();
		return strval($this->_db->loadResult());
	}
	
	/**
	 * в переводном алиасе(не с основным языком) должен быть постфикс -[lang]
	 * @param string $alias
	 * @return string - lang
	 */
	public function getLangByString($alias)
	{
		// тут на входе должна быть всегда латиница		
		$check_lang=substr($alias,strrpos($alias, '-')+1);		
		if($check_lang&&array_key_exists($check_lang,Text::getAllLanguages())) return $check_lang;
		else return '';
	}
	
}
?>
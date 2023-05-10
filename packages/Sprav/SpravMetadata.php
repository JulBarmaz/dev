<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SpravMetadata extends BaseObject {
	protected $store_type	= 1;		// Тип хранения данных в файлах
	protected $_prepView = true;		// апдейт полей из таблицы
	public $success			= false;	// Флаг удачной загрузки
	public $constants		= array();	// Константы
	public $classname		= "";		// Имя класса
	public $module			= "";		// Имя модуля (совпадает с именем каталога модуля)
	public $tablename		= "";		// Основная таблица БД без префикса
	public $linktable		= "";		// Линковочная таблица БД без префикса
	public $link_with_childs= false;	// При линковке если родитель дерево взять всех детей
	public $parent_table = '';  // родительская  таблица, актуальна при линковке, а так равна ch_table поля multy_field
	public $parent_name	= '';		// поле наименования родительской таблицы, актуально при линковке
	public $use_view_rights	= '';	// имя view права которой можно использовать
	public $title				= "";		// Заголовок таблицы
	public $namestring	= "";		// Имя поля с названием в таблице
	public $selector_string	= "";		// Список полей разделенных запятыми для подстановки из селектора
	public $alias_field	= "";		// Имя поля с псевдонимом в таблице для
	public $parent_subordination	= true;		// учитыватьподчиненность в списках
	public $parent_code	= '';		// ключевое поле родительского представления
	public $parent_view = '';   // родительское представление, куда перейдем по go_up
	public $parent_layout = ''; // родительское отображение, куда перейдем по go_up
	public $keystring		= "";		// Основное Ключевое поле таблицы
	public $nofilter		= false;// Принудительное отключение фильтрации
	public $show_nopages = true; //  Показать флажок "Не разбивать на страницы"
	public $show_woparents = true; //  Показать флажок "Без учета родителей"
	public $enabled			= "";		// Поле пометки вкл/выкл
	public $deleted			= "";		// Поле пометки на удаление
	public $keysort			= "";		// Поле сортировки по умолчанию при показе
	public $keysort_dest	= "ASC";		// Направление сортировки по умолчанию при показе
	public $ordering_field	= "";		// Поле порядка отображения
	public $keycurrency	= "";		// Поле в котором содержится валюта хранения(опц.)
	public $checkbox		= true;		// Надо ли выводить первой колонкой checkbox для выбора элементов
	public $selector		= false;		// Надо ли выводить первой колонкой кнопку для подстановки элементов
	public $tree_index	= 0;		// если является деревом, тогда здесь индекс поля
	public $tree_skip_deleted	= 0;	// если является деревом, тогда можно скрыть удаленные в дереве
	public $tree_skip_disabled	= 0;	// если является деревом, тогда можно скрыть отключенные в дереве
	public $multy_field	= "";		// Поле таблицы для связи с родительской таблицей
	public $custom_sql	= "";		// Дополнительный текст SQL запроса в условии WHERE
	public $custom_ordering_sql	= "";		// Дополнительный текст SQL запроса в условии ORDER BY
	public $pans_titles		= array(1=>"Main data", 2=>"Additional"); // Названия вкладок при редактировании
	public $templates		= array();	// Шаблоны
	public $buttons			= array();	// Общие для всех кнопки
	public $uni_buttons	= array();	// Кнопки уникальные для данного справочника
	public $field				= array();	// Поля таблицы
	public $filter			= array();	// Наличие фильтрации по полю
	public $filter_ext		= array();	// Наличие фильтрации по полю
	public $filter_strict	= array();	// Строгая фильтрация по полю
	public $filter_ch_list	= array();	// Фильтрация ch поля по списку
	public $field_orderby	= array();	// Направление сортировки поля таблицы
	public $field_title		= array();	// Заголовки полей таблицы
	public $field_descr		= array();	// Описание (всплывающий balloon) полей таблицы
	public $field_on_change	= array();	// Javascript на изменение поля при редактировании
	public $field_is_method	= array();	// Значение в поле является результатом вычислений и не полем таблицы
	public $size				= array();	// Ширина колонки при просмотре
	public $view				= array();	// Показывать колонку
	public $link				= array();	// Ссылка для значения ячейки
	public $link_vars		= array();	// Параметры для подстановки в link
	public $link_types	= array();	// Параметры для подстановки в link ( например popup)
	public $link_picture	= array();	// Картинка для  подстановки в ячейку
	public $img_module	= array();	// модуль из которого брать картинки( например в статьи из галереи, в блоги из товаров)	
	public $upload_path	= array();	// хвост пути, куда сохранять этот файл (userfiles/имя модуля/$upload_path)
	public $val_type		= array();	// Тип данных поля
	public $val_size		= array();	// Длина данных поля
	public $input_size	= array();	// Размер контрола при редактировании
	public $input_type	= array();	// Тип контрола при редактировании
	public $input_view	= array();	// Показывать колонку при редактировании
	public $input_page	= array();	// Номер вкладки при редактировании
	public $input_last_page	= 1;	// Номер последней вкладки при редактировании
	public $field_no_update	= array();	// Не изменять при редактировании
	public $check_value	= array();	// Проверять значение при редактировании элемента (0 - не проверяет, 1 - проверяет, 2 - проверяет на уникальность все, 3 - проверяет на уникальность непустые)
	public $translate_value	= array();	// Переводить ли полученное значение
	public $default_value	= array();	// Значение по умолчанию
	public $ck_reestr		= array();	// Подстановка фиксированного набора значений
	public $ch_table		= array();	// Таблицы подстановки для зависимых полей
	public $ch_field		= array();	// Поле подстановки из таблицы подстановки
	public $ch_parent_field		= array();	// Поле родителя таблицы подстановки (если таблица подстановки дерево и не равна текущей таблице)
	public $ch_id				= array();	// Ключевое поле таблицы подстановки
	public $ch_deleted	= array();	// Поле пометки на удаление таблицы подстановки
	public $ch_enabled	= array();	// Поле пометки на включенность в таблице подстановки
	public $ch_skip_deleted	= array();	// Поле исключения удаленных для таблицы подстановки
	public $ch_skip_disabled	= array();	// Поле исключения отключенных для таблицы подстановки
	public $ch_sort			= array();	// Поле сортировки таблицы подстановки
	public $ch_sp_field	= array();	// Дополнительное условие для выборки из таблицы подстановки (Приравнивается к psid редактируемого элемента если не указан ch_sp_field_val)
	public $ch_sp_field_val	= array();	// Значение дополнительного условия для выборки из таблицы подстановки
	public $is_add			= array();	// поле является дополнительным и живет в другой таблице [tablename]_data
	public $is_add_custom	= array();	// если (is_add), то 1 - используется для объекта от группы объектов, 2- является опцией (множественна и подчинена объекту)
	public $classTable    = '';
	public $viewName		= '';
	public $layoutName  = '';
	public $field_order	= array();	// Номер по порядку в массиве полей. Для пересортировки массива метаданных
	public $metaside =0;						// принудительная указание что метадата на фронтенде (при вызове из админки)
	public function __construct($moduleName='', $viewName='',$layoutName='',$critical=true,$autoPrepView=true,$metaside=0) {
		$this->_prepView=$autoPrepView;
		$this->initObj($moduleName.'Metadata');
		if ($moduleName == '') { $this->module = $this->get('module'); }
		else { $this->module = $moduleName; }
		if ($viewName == '') { $this->viewName = $this->get('view'); 	}
		else { $this->viewName = $viewName; }
		if ($layoutName == '') { $this->layoutName = $this->get('layout'); }
		else { $this->layoutName = $layoutName; }		    
		$this->metaside=$metaside;
		$this->defaultButtons();
		$this->defaultConstants();
		$this->read($critical);
		if ($this->_prepView) $this->prepareView();
	}

	private function prepareView() {
		// вызов простановки из базы
		$this->updateViewFromTable();
	}

	private function defaultButtons(){
		$this->buttons["go_up"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["new"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["clone"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["new_string"]	= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["modify"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["info"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["filter"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["refresh"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["reorder"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["print"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["datagramm"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["modify_links"]	= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["delete"]		= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["undelete"]		= array("module"=>'',"show"=>1,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["trash"]			= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["list"]			= array("module"=>'',"show"=>1,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
		$this->buttons["clean_trash"]	= array("module"=>'',"show"=>0,"view"=>"","task"=>"","link"=>false,"layout"=>'',"controller"=>'',"title"=>'');
	}

	private function defaultConstants(){
		$this->constants["AUTHOR"]	= User::getInstance()->getId();
		$this->constants["AUTHOR_MAIL"]	= User::getInstance()->getEmail();
		$this->constants["AUTHOR_NAME"]	= User::getInstance()->getNickname();
		$this->constants["AUTHOR_IP"]	= User::getInstance()->getIP();
		$this->constants["NOW"]		= date("d.m.Y H:i:s");
		$this->constants["TODAY"]	= date("d.m.Y");
	}

	public function getpath_name($module_name='')
	{
		if($module_name){
			Database::getInstance()->setQuery("select m_replace_name from #__modules where m_name='".$module_name."'");
			$pathl=Database::getInstance()->loadResult();
			if($pathl) return $pathl;
		}
		return $module_name;
	}
	
	public function getDefaultConstant($name) {
		if(array_key_exists($name, $this->constants)) return $this->constants[$name];
		return null;
	}

	public function read($critical) {
		$_module	= $this->module;
		$_view		= $this->viewName;
		$_task		= $this->get('task');
		$_layout  = $this->layoutName;
		
		if($_layout == '') $_layout	= $this->get('layout');

		$_meta=$_view.".".$_layout;
		if($_layout=='default') $_meta=$_view.".defl";
		$pathr=$this->getpath_name($_module);
		
		if($this->metaside==1) {
			$metaPath = PATH_FRONT_MODULES.$pathr.DS.'metadata'.DS.$_meta.'.php';
		} else {
			$metaPath = PATH_MODULES.$pathr.DS.'metadata'.DS.$_meta.'.php';
		}
		$this->milestone('Meta: &quot;'.$_meta.'&quot; Metadata file: '.$metaPath, __FUNCTION__);
		if (file_exists($metaPath)) {
			$arr_parms[$_view]['view'] = array();
			$arr_parms[$_view]['size'] = array();
			include $metaPath;			
			if (isset($tablname)) $this->tablename = $tablname;
			if (isset($parent_table)) $this->parent_table = $parent_table; // else $this->linktable = "";
			if (isset($parent_code)) $this->parent_code = $parent_code;
			if (isset($parent_subordination)) $this->parent_subordination = $parent_subordination;
			if (isset($parent_name)) $this->parent_name = $parent_name;
			if (isset($parent_view)) {
				$parent_meta_name=preg_split("/[.]/", $parent_view);
				if (count($parent_meta_name)==2) { // указана мета вместе с layout пробуем загрузить метадату родителя
					$this->parent_view = $parent_meta_name[0];
					$this->parent_layout = $parent_meta_name[1];
				} else {// указана без layout? грузим дефолтную !
					$this->parent_view = $parent_view;
					$this->parent_layout = "default";
				}
			}
			if (isset($nametabl)) $this->title = $nametabl;
			if (isset($classname)) $this->classname = $classname;
			if (isset($classTable)) $this->classTable = $classTable;
			if (isset($keystring)) $this->keystring = $keystring;
			if (isset($nofilter)) $this->nofilter = $nofilter;
			if (isset($show_nopages)) $this->show_nopages= $show_nopages;
			if (isset($show_woparents)) $this->show_woparents= $show_woparents;
			if (isset($dopkeyid)) $this->dopkeyid = $dopkeyid;
			if (isset($enabled)) $this->enabled = $enabled;
			if (isset($deleted)) $this->deleted = $deleted;
			if (isset($templates)) $this->templates = $templates;
			if (isset($multy_field)) $this->multy_field = $multy_field;
			if (isset($keysort)) $this->keysort = $keysort;
			if (isset($keysort_dest)) $this->keysort_dest = $keysort_dest;
			if (isset($keycurrency)) $this->keycurrency = $keycurrency;
			if (isset($ordering_field)) $this->ordering_field = $ordering_field;
			if (isset($checkbox)) $this->checkbox = $checkbox;
			if (isset($selector)) $this->selector = $selector;
			if (isset($custom_sql)) $this->custom_sql = $custom_sql;
			if (isset($custom_ordering_sql)) $this->custom_ordering_sql = $custom_ordering_sql;
			if (isset($pans_titles)) $this->pans_titles = $pans_titles;
			if (isset($namestring)) $this->namestring = $namestring; else $this->namestring = $this->keystring;
			if (isset($selector_string)) $this->selector_string = $selector_string; else $this->selector_string = $this->namestring;
			
			if (isset($alias_field)) $this->alias_field = $alias_field; else $this->alias_field = "";
			if (isset($use_view_rights) && $use_view_rights) $this->use_view_rights = $use_view_rights;
			
			// у нас есть все необходимые сведения для формирвоания дополинтельных полей
			// хотя можно вставить проверку на наличие необходимых реквизитов
			// достаем список дополнительных полей
			$cur_table_arr=$this->appendBaseAddFields($tablname,$cur_table_arr);
			// значение линковочной таблицы имеет смысл только тогда когда выполняется ряд условий
			if ($this->multy_field==$this->keystring) {
				if (($this->parent_table)&&($this->parent_code)) {
					if (isset($linktable)) $this->linktable = $linktable;
					else $this->linktable = $this->tablename."_links";
					if (isset($link_with_childs)) $this->link_with_childs = $link_with_childs;
				}
			}
			foreach ($this->buttons as $b_key=>$b_vals) {
				if (isset($buttons[$b_key]['module'])) $this->buttons[$b_key]['module']	= $buttons[$b_key]['module'];
				else {$this->buttons[$b_key]['module']	= $this->module;}
				if (isset($buttons[$b_key]['show'])) $this->buttons[$b_key]['show']	= $buttons[$b_key]['show'];
				if (isset($buttons[$b_key]['view'])) $this->buttons[$b_key]['view']	= $buttons[$b_key]['view']; // else $this->buttons[$b_key]['view']=$this->viewName;
				if (isset($buttons[$b_key]['task'])) $this->buttons[$b_key]['task']	= $buttons[$b_key]['task'];
				if (isset($buttons[$b_key]['layout'])) $this->buttons[$b_key]['layout']	= $buttons[$b_key]['layout']; else $this->buttons[$b_key]['layout']=$this->layoutName;
				if (isset($buttons[$b_key]['link'])) $this->buttons[$b_key]['link']	= $buttons[$b_key]['link'];
				if (isset($buttons[$b_key]['controller'])) $this->buttons[$b_key]['controller']	= $buttons[$b_key]['controller'];
				if (isset($buttons[$b_key]['title'])) $this->buttons[$b_key]['title']	= $buttons[$b_key]['title'];
			}
			if (isset($uni_buttons)) $this->uni_buttons = $uni_buttons;
			// Тут можно обработать метадату, например добавить поля
			Event::raise('system.metadata.prepare_fields', array("metadata"=>$this, "metadata_module"=>$_module, "metadata_file"=>$_meta, "metadata_side"=>((defined("_ADMIN_MODE") && $this->metaside) || !defined("_ADMIN_MODE")) ? 1 : 0), $cur_table_arr);
			// if (isset($cur_table_arr['multy_field'])) $this->multy_field = $cur_table_arr['multy_field']; // Нет ведь таких полей ????
			if (isset($cur_table_arr['field'])) $this->field = $cur_table_arr['field'];
			if (count($this->field) > 0) {
				foreach ($this->field as $key=>$data) {
					if (isset($cur_table_arr['filter'][$key])) $this->filter[$key] = $cur_table_arr['filter'][$key]; else $this->filter[$key]=(defined("_ADMIN_MODE") && !$this->metaside ? 1 : 0);
					if (isset($cur_table_arr['filter_ext'][$key])) $this->filter_ext[$key] = $cur_table_arr['filter_ext'][$key]; else $this->filter_ext[$key]=(defined("_ADMIN_MODE") && !$this->metaside ? 1 : 0);;
					if (isset($cur_table_arr['filter_strict'][$key])) $this->filter_strict[$key] = $cur_table_arr['filter_strict'][$key]; else $this->filter_strict[$key]=0;
					if (isset($cur_table_arr['filter_ch_list'][$key]) && $cur_table_arr['filter_ch_list'][$key]) $this->filter_ch_list[$key] = 1; else $this->filter_ch_list[$key]=0;
					if (isset($cur_table_arr['sort_order'][$key])) $this->field_orderby[$key] = $cur_table_arr['sort_order'][$key]; else $this->field_orderby[$key]="";
					if (isset($cur_table_arr['fim'][$key])) {
						$this->field_orderby[$key]="NONE";
						$this->field_is_method[$key] = $cur_table_arr['fim'][$key];
					} else $this->field_is_method[$key]=false;
					if (isset($cur_table_arr['field_on_change'][$key])) $this->field_on_change[$key] = $cur_table_arr['field_on_change'][$key]; else $this->field_on_change[$key]="";
					if (isset($cur_table_arr['update_type'][$key])&&($cur_table_arr['update_type'][$key]==0)) $this->field_no_update[$key]=1; else $this->field_no_update[$key]= 0;
					if (isset($cur_table_arr['name'][$key])) $this->field_title[$key] = $cur_table_arr['name'][$key]; else $this->field_title[$key]="";
					if (isset($cur_table_arr['descr'][$key])) $this->field_descr[$key] = $cur_table_arr['descr'][$key]; else $this->field_descr[$key]="";
					if (isset($cur_table_arr['size'][$key])) $this->size[$key] = str_replace('"', "", $cur_table_arr['size'][$key]); else $this->size[$key]="";
					if (isset($cur_table_arr['view'][$key])) $this->view[$key] = $cur_table_arr['view'][$key]; else $this->view[$key]=0;
					if (isset($cur_table_arr['link'][$key])) $this->link[$key] = $cur_table_arr['link'][$key]; else $this->link[$key]="";
					if (isset($cur_table_arr['is_add'][$key])) $this->is_add[$key] = $cur_table_arr['is_add'][$key]; else $this->is_add[$key]=false;
					if (isset($cur_table_arr['is_add_custom'][$key])) $this->is_add_custom[$key] = $cur_table_arr['is_add_custom'][$key]; else $this->is_add_custom[$key]=false;
					// для совместимости используем str_replace
					if (isset($cur_table_arr['link_key'][$key])) $this->link_vars[$key] = str_replace("$", "", $cur_table_arr['link_key'][$key]); else $this->link_vars[$key]="";
					if (isset($cur_table_arr['link_type'][$key])) $this->link_types[$key] = $cur_table_arr['link_type'][$key]; else $this->link_types[$key]="";
					if (isset($cur_table_arr['link_picture'][$key])) $this->link_picture[$key] = $cur_table_arr['link_picture'][$key]; else $this->link_picture[$key]="";
					if (isset($cur_table_arr['img_module'][$key])) $this->img_module[$key] = $cur_table_arr['img_module'][$key]; else $this->img_module[$key]="";					
					if (isset($cur_table_arr['val_type'][$key])) $this->val_type[$key] = $cur_table_arr['val_type'][$key]; else $this->val_type[$key]="";
					if (isset($cur_table_arr['val_size'][$key])) $this->val_size[$key] = (int)$cur_table_arr['val_size'][$key]; 
					else {
						if ($this->_prepView) $this->val_size[$key]=-1; else $this->val_size[$key]=0;
					}
					if (isset($cur_table_arr['val_const'][$key])) $this->val_type[$key] = "constanta";
					if (isset($cur_table_arr['input_view'][$key])) $this->input_view[$key] = $cur_table_arr['input_view'][$key]; else $this->input_view[$key]=(defined("_ADMIN_MODE") && !$this->metaside ? 1 : 0);
					if (isset($cur_table_arr['input_size'][$key])) $this->input_size[$key] = str_replace('"', "", $cur_table_arr['input_size'][$key]); else $this->input_size[$key]="";
					if (isset($cur_table_arr['input_type'][$key])) $this->input_type[$key] = $cur_table_arr['input_type'][$key]; else $this->input_type[$key]="";
					if (isset($cur_table_arr['input_page'][$key])) $this->input_page[$key] = intval($cur_table_arr['input_page'][$key]); else $this->input_page[$key]=0;
					if($this->input_page[$key] > $this->input_last_page) $this->input_last_page=$this->input_page[$key];
					$this->field_order[$key]=0;
//					if (isset($cur_table_arr['upload_path'][$key])) $this->upload_path[$key] = $cur_table_arr['upload_path'][$key]; else $this->upload_path[$key]="";
					if (isset($cur_table_arr['upload_path'][$key])) $this->upload_path[$key] = $cur_table_arr['upload_path'][$key];
					else {
						if (($this->input_type[$key]=='image') ||($this->input_type[$key]=='file')) {
							if($this->is_add[$key]) $subdir='addfields/'.$this->field[$key];
							else $subdir='mainfields/'.$this->field[$key];
						} else $this->upload_path[$key]="";
					}
					if (isset($cur_table_arr['check_value'][$key])) $this->check_value[$key] = $cur_table_arr['check_value'][$key]; else $this->check_value[$key]="";
					if (isset($cur_table_arr['translate_value'][$key])) $this->translate_value[$key] = $cur_table_arr['translate_value'][$key]; else $this->translate_value[$key]=false;
					if (isset($cur_table_arr['default_value'][$key])) $this->default_value[$key]= $cur_table_arr['default_value'][$key]; else $this->default_value[$key]="";
					if (isset($cur_table_arr['ch_table'][$key])) $this->ch_table[$key] = $cur_table_arr['ch_table'][$key]; else $this->ch_table[$key]="";
					if ($this->ch_table[$key]==$this->tablename) $this->tree_index=$key;
					if (isset($cur_table_arr['ch_field'][$key])) $this->ch_field[$key] = $cur_table_arr['ch_field'][$key]; else $this->ch_field[$key]="";
					if (isset($cur_table_arr['ch_parent_field'][$key])) $this->ch_parent_field[$key] = $cur_table_arr['ch_parent_field'][$key];	else $this->ch_parent_field[$key]="";
					if (isset($cur_table_arr['ch_id'][$key])) $this->ch_id[$key] = $cur_table_arr['ch_id'][$key]; else $this->ch_id[$key]="";
					if (isset($cur_table_arr['ch_sort'][$key])) $this->ch_sort[$key] = $cur_table_arr['ch_sort'][$key]; else $this->ch_sort[$key]="";
					if (isset($cur_table_arr['ch_sp_field'][$key])) $this->ch_sp_field[$key] = $cur_table_arr['ch_sp_field'][$key]; else $this->ch_sp_field[$key]="";
					if (isset($cur_table_arr['ch_sp_field_val'][$key])) $this->ch_sp_field_val[$key] = $cur_table_arr['ch_sp_field_val'][$key]; else $this->ch_sp_field_val[$key]="";
					if (isset($cur_table_arr['ch_deleted'][$key])) $this->ch_deleted[$key] = $cur_table_arr['ch_deleted'][$key]; else $this->ch_deleted[$key]="";					
					if (isset($cur_table_arr['ch_enabled'][$key])) $this->ch_enabled[$key] = $cur_table_arr['ch_enabled'][$key]; else $this->ch_enabled[$key]="";
					if (isset($cur_table_arr['ch_skip_deleted'][$key])) $this->ch_skip_deleted[$key] = $cur_table_arr['ch_skip_deleted'][$key]; else $this->ch_skip_deleted[$key]=1;
					if (isset($cur_table_arr['ch_skip_disabled'][$key])) $this->ch_skip_disabled[$key] = $cur_table_arr['ch_skip_disabled'][$key]; else $this->ch_skip_disabled[$key]=1;
					if (isset($cur_table_arr['ck_reestr'][$key])) $this->ck_reestr[$key] = $cur_table_arr['ck_reestr'][$key]; else $this->ck_reestr[$key]="";
				}
				$this->success=true;
				if($this->tree_index){
					if (isset($tree_skip_deleted)) $this->tree_skip_deleted = $tree_skip_deleted;
					if (isset($tree_skip_disabled)) $this->tree_skip_disabled = $tree_skip_disabled;
				}
				
			}
		} else {
			if ($critical) $this->fatalError('Metadata file not found: '.$metaPath); // Realy fatal, if critical flag true.
		}
	}
	/**
	* дополнием описатели таблиц динамической частью
	* @param string $tablname имя таблицы к кторой подбираем дополнительные поля
	* @param array $cur_table_arr предварительно собранный массив который будем наращивать
	*/
	private function appendBaseAddFields($tablname,$cur_table_arr) {
		$add_list=$this->getListAdditionalField($tablname);
		$add_list_values = $this->getAdditionalFieldsValues($tablname);
		$_tps=$this->getArrayFieldsType();
		if(is_array($add_list)&&count($add_list)>0) {
			$count=count($cur_table_arr["field"]);
			$cur_table_arr["is_add"]=array();
			$cur_table_arr["is_add_custom"]=array();
			foreach($add_list as $list) {
				$count++;
				$cur_table_arr["field"][$count]=$list->f_name;
				$cur_table_arr["name"][$count]=$list->f_descr;
				$cur_table_arr["check_value"][$count]=$list->f_required;
				if(!isset($_tps[$list->f_type])) {
					$cur_table_arr["val_type"][$count]='string';
					$cur_table_arr["input_type"][$count]='text';
				}	else {
					$cur_table_arr["val_type"][$count]=$_tps[$list->f_type]->t_val_type;
					$cur_table_arr["input_type"][$count]=$_tps[$list->f_type]->t_input_type;
					if ($cur_table_arr["input_type"][$count]=="select" || $cur_table_arr["input_type"][$count]=="multiselect") {
						if(isset($add_list_values[$list->f_name]) && count($add_list_values[$list->f_name])){
							$ck_array=array();
							foreach($add_list_values[$list->f_name] as $field_choice){
								$ck_array[$field_choice->fc_id]=$field_choice->fc_value;
							}
							$cur_table_arr["ck_reestr"][$count]=$ck_array;
						} else { // если массив значений пустой проверим может по имени поля у нас есть предустановленный список
							$cur_table_arr["ck_reestr"][$count]=SpravStatic::getCKArray($list->f_name);
						}
					}
				}
				$cur_table_arr["is_add"][$count]=$list->f_id;
				$cur_table_arr["default_value"][$count]=$list->f_default;
				$cur_table_arr["is_add_custom"][$count]=$list->f_custom;
				$cur_table_arr["input_view"][$count]=0;
				$cur_table_arr["view"][$count]=0;
				if (($cur_table_arr["input_type"][$count]=='image') ||($cur_table_arr["input_type"][$count]=='file')) {
					$cur_table_arr["upload_path"][$count]="_add/".$this->viewName;
				}
			}
		}
		return $cur_table_arr;
	}
	/**
	* Список дополнительных полей к таблице,
	* @param string $table имя таблицы к кторой подбираем дополнительные поля
	* return массив объектов с ключом по ид поля
	*/
	public function getListAdditionalField($table='') {
		$db=Database::getInstance();
		$query="SELECT ff.* FROM #__fields_list AS ff WHERE ff.f_deleted=0 AND ff.f_table='".$table."' AND ff.f_custom<2";
		$db->setQuery($query);
		return $db->loadObjectList('f_name');
	}
	private function getAdditionalFieldsValues($table='', $enabled_only=true) {
		$this->milestone('Metadata started loading additional fields values: ', __FUNCTION__);
		$result = array();
		$db=Database::getInstance();
		// $query="SELECT fc.* FROM `#__fields_choices` AS fc WHERE fc.fc_deleted=0 AND fc.fc_field_id IN (SELECT ff.f_id FROM #__fields_list AS ff WHERE ff.f_deleted=0 AND ff.f_table='".$table."' AND ff.f_custom<2)";
		$query = "SELECT fc.*, fl.f_name, fl.f_table FROM `#__fields_choices` AS fc, `#__fields_list` as fl";
		$query.= " WHERE fc.fc_field_id=fl.f_id AND fc.fc_deleted=0";
		if($enabled_only) $query.= " AND fc.fc_enabled=1";
		$query.= " AND fl.f_deleted=0 AND fl.f_table='".$table."'";
		$query.= " ORDER BY fl.f_name, fc.fc_ordering";
		$db->setQuery($query);
		$choices = $db->loadObjectList();
		if(count($choices)){
			foreach($choices as $choice){
				$result[$choice->f_name][$choice->fc_id]=$choice;
			}
		}
		$this->milestone('Metadata finished loading additional fields values: ', __FUNCTION__);
		return $result;
	}
	private function getArrayFieldsType() {
		$db=Database::getInstance();
		$sql = "SELECT * FROM #__fields_type";
		$db->setQuery($sql);
		return $db->loadObjectList("t_id");
	}
	private function updateViewFromTable() {
		$_module	= $this->module;
		$_view	= $this->viewName;
		$_layout	= $this->layoutName;
		if ($_layout=='' || is_null($_layout)) $_layout='default';
		if($_layout=='default') $layout_base='defl'; else $layout_base=$_layout;
		$field     = $this->field;
		$field_obr = array_flip($field);
		$db=Database::getInstance();
		if (defined('_ADMIN_MODE')){
			if ($this->metaside) $m_admin_side=0; else $m_admin_side=1;
		} else {
			$m_admin_side=0;
		}
		$query=" SELECT * FROM #__metadata WHERE `m_module`='".$_module."' AND `m_view`='".$_view."' AND `m_layout`='".$layout_base."' AND `m_admin_side`=".$m_admin_side;
		$db->setQuery($query);
		$cur = $db->loadAssocList();
		if (!$cur)  return false;
		foreach($cur as $fld_dop) {
			$f_key=$fld_dop["m_field"];
			if(isset($field_obr[$f_key])) {
				$key=$field_obr[$f_key];
				$this->size[$key]=$fld_dop["m_width"];
				$this->view[$key]=$fld_dop["m_show"];
				$this->input_size[$key]=$fld_dop["m_input_size"];
				$this->input_view[$key]=$fld_dop["m_input_view"];
				$this->input_page[$key]=$fld_dop["m_input_page"];
				if($this->input_page[$key] > $this->input_last_page) $this->input_last_page=$this->input_page[$key];
				if($this->val_size[$key]<0)	$this->val_size[$key]=intval($this->input_size[$key]);
				$this->filter[$key]=$fld_dop["m_show_in_filter"];
				$this->filter_ext[$key]=$fld_dop["m_show_in_filter_ext"];
				$this->filter_strict[$key]=$fld_dop["m_strict_filter"];
				if(defined("_BARMAZ_TRANSLATE")){
					$this->translate_value[$key]=$fld_dop["m_translate_value"];
				}
				if($fld_dop["m_field_order"]) $this->field_order[$key]=$fld_dop["m_field_order"]; else $this->field_order[$key]=$key;
			}
		}
		$this->reorderFields();
		return true;
	}
	private function reorderFields(){
		Debugger::getInstance()->milestone("Start Metadata sorting");
		$array_for_sort=array("field",	"filter", "filter_ext", "filter_strict", "filter_ch_list", "field_orderby",	"field_title",	"field_descr",
							"field_on_change",	"field_is_method",	"size",	"view",	"link",
							"link_vars",	"link_types",	"link_picture",	"upload_path","img_module","val_type",
							"val_size",	"input_size",	"input_type", "input_view", "input_page",	"field_no_update",
							"check_value",	"translate_value",	"default_value",	"ck_reestr",	"ch_table",
							"ch_field",	"ch_parent_field",	"ch_id",	"ch_deleted",	"ch_enabled",
							"ch_sort",	"ch_sp_field",	"ch_sp_field_val",	"is_add",	"is_add_custom", 
							"field_order");
		// формируем порядковые номера
		$counter=1;
		asort($this->field_order);
		foreach($this->field_order as $fok=>$fov){
			$this->field_order[$fok]=$counter;
			$counter++;
		}
		// меняем индекс поля дерева
		if ($this->tree_index){ $this->tree_index=$this->field_order[$this->tree_index]; }
		// пересортировываем остальные элементы
		$new_order=$this->field_order;
		foreach($array_for_sort as $array_name){
			$new_array = array();
			foreach($this->{$array_name} as  $key=>$val){
				$new_array[$new_order[$key]]=$val;
			}
			ksort($new_array);
			$this->{$array_name}=$new_array;
		}
		Debugger::getInstance()->milestone("Finish Metadata sorting");
	}
	public function getFieldIndex($fieldname) {
		if (count($this->field)>0) {
			foreach ($this->field as $key=>$val) {
				if ($val==$fieldname) return $key;
			}
		}
		return 0;
	}
	public function updateArrayField($arrayname, $fieldname, $value) {
		$key=$this->getFieldIndex($fieldname);
		if ($key && isset($this->{$arrayname}) && array_key_exists($key, $this->{$arrayname}))	{
			$this->{$arrayname}[$key]=$value;
			return true;
		} else return false;
	}
	public function cleanAddFields($fields) {
		if($this->success && count($this->field)) {
			foreach ($this->field as $key=>$data) {
				if($this->is_add_custom[$key] && !array_key_exists($this->field[$key], $fields)){
					unset($this->field[$key]);
					unset($this->filter[$key]);
					unset($this->filter_ext[$key]);
					unset($this->filter_strict[$key]);
					unset($this->filter_ch_list[$key]);
					unset($this->field_orderby[$key]);
					unset($this->field_is_method[$key]);
					unset($this->field_on_change[$key]);
					unset($this->field_no_update[$key]);
					unset($this->field_title[$key]);
					unset($this->field_descr[$key]);
					unset($this->size[$key]);
					unset($this->view[$key]);
					unset($this->link[$key]);
					unset($this->is_add[$key]);
					unset($this->is_add_custom[$key]);
					unset($this->link_vars[$key]);
					unset($this->link_types[$key]);
					unset($this->link_picture[$key]);
					unset($this->img_module[$key]);
					unset($this->val_type[$key]);
					unset($this->val_size[$key]);
					unset($this->val_type[$key]);
					unset($this->input_view[$key]);
					unset($this->input_size[$key]);
					unset($this->input_type[$key]);
					unset($this->input_page[$key]);
					unset($this->field_order[$key]);
					unset($this->upload_path[$key]);
					unset($this->check_value[$key]);
					unset($this->translate_value[$key]);
					unset($this->default_value[$key]);
					unset($this->ch_table[$key]);
					unset($this->ch_field[$key]);
					unset($this->ch_parent_field[$key]);
					unset($this->ch_id[$key]);
					unset($this->ch_sort[$key]);
					unset($this->ch_sp_field[$key]);
					unset($this->ch_sp_field_val[$key]);
					unset($this->ch_deleted[$key]);
					unset($this->ch_enabled[$key]);
					unset($this->ch_skip_deleted[$key]);
					unset($this->ch_skip_disabled[$key]);
					unset($this->ck_reestr[$key]);
				}
			}
		}
	}
}
?>
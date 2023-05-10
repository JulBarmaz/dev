<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class BaseCML extends BaseXML{
	public static function getRoot($cml_version) {
		$root_xml = new SimpleXMLElement(self::getRootText($cml_version));
		return $root_xml;
	}
	public static function getRootText($cml_version) {
		$root = '<?xml version="1.0" encoding="utf-8"?>';
		switch($cml_version){
			case "2.04":
			default:
				$root.= '<'.self::_("CommerceInformation").' xmlns="urn:1C.ru:commerceml_2" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.self::_("SchemaVersion").'="'.$cml_version.'" '.self::_("CreationDate").'="' . date('c', time()) . '" />';
			break;
		}
		return $root;
	}
	public static function nodeHasChilds($node, $subnodeName) {
		return parent::nodeHasChilds($node, self::_($subnodeName));
	}
	public static function checkNode($node, $subnodeName) {
		return parent::checkNode($node, self::_($subnodeName));
	}
	public static function getNode($node, $subnodeName, $defaultValue="") {
		return parent::getNode($node, self::_($subnodeName));
	}
	public static function dropNode($node, $subnodeName) {
		return parent::dropNode($node, self::_($subnodeName));
	}
	public static function checkAttr($node, $attrName) {
		return parent::checkAttr($node, self::_($attrName));
	}
	public static function getAttr($node, $attrName, $defaultValue="") {
		return parent::getAttr($node, self::_($attrName));
	}
	public static function _($id){
		if(Text::getLanguage()=="en") return $id;
		switch($id){
			case "testCML_Accounts": $name="РасчетныеСчета"; break;
			case "testCML_Account": $name="РасчетныйСчет"; break;
			case "testCML_AccountNumber": $name="НомерСчета"; break;
			case "testCML_Actual_address": $name="Фактический адрес"; break;
			case "testCML_Address_field": $name="АдресноеПоле"; break;
			case "testCML_Address_post_code": $name="Почтовый индекс"; break;
			case "testCML_Address_country": $name="Страна"; break;
			case "testCML_Address_region": $name="Регион"; break;
			case "testCML_Address_state": $name="Район"; break;
			case "testCML_Address_small_city": $name="Населенный пункт"; break;
			case "testCML_Address_city": $name="Город"; break;
			case "testCML_Address_street": $name="Улица"; break;
			case "testCML_Address_house": $name="Дом"; break;
			case "testCML_Address_building": $name="Корпус"; break;
			case "testCML_Address_flat": $name="Квартира"; break;
			case "testCML_Bank": $name="Банк"; break;
			case "testCML_BankAddress": $name="АдресБанка"; break;
			case "testCML_BIK": $name="БИК"; break;
			case "testCML_BusinessOperation": $name="ХозОперация"; break;
			case "testCML_BusinessOperationDefault": $name="Заказ товара"; break;
			case "testCML_Categories": $name="Категории"; break;
			case "testCML_Category": $name="Категория"; break;
			case "testCML_Contact_person": $name="Контактное лицо"; break;
			case "testCML_Contractor": $name="Контрагент"; break;
			case "testCML_Contractors": $name="Контрагенты"; break;
			case "testCML_CorrespondentAccount": $name="СчетКорреспондентский"; break;
			case "testCML_CurrencyRate": $name="Курс"; break;
			case "testCML_Date": $name="Дата"; break;
			case "testCML_DateTime": $name="ДатаВремя"; break;
			case "testCML_Delegate": $name="Представитель"; break;
			case "testCML_Delegates": $name="Представители"; break;
			case "testCML_Discount": $name="Скидка"; break;
			case "testCML_Discounts": $name="Скидки"; break;
			case "testCML_Document": $name="Документ"; break;
			case "testCML_Email": $name="Email"; break;
			case "testCML_FirstName": $name="Имя"; break;
			case "testCML_forProducts": $name="ДляТоваров"; break;
			case "testCML_forOffers": $name="ДляПредложений"; break;
			case "testCML_forDocuments": $name="ДляДокументов"; break;
			case "testCML_Full_title": $name="Полное наименование"; break;
			case "testCML_Height": $name="Высота"; break;
			case "testCML_INN": $name="ИНН"; break;
			case "testCML_KPP": $name="КПП"; break;
			case "testCML_LastName": $name="Фамилия"; break;
			case "testCML_LegalAddress": $name="ЮридическийАдрес"; break;
			case "testCML_Length": $name="Длина"; break;
			case "testCML_Loaded_from_site": $name="Загружено с сайта"; break;
			case "testCML_Mail": $name="Почта"; break;
			case "testCML_Nomer": $name="Номер"; break;
			case "testCML_OKPO": $name="ОКПО"; break;
			case "testCML_Order_status": $name="Статус заказа"; break;
			case "testCML_Phone": $name="Телефон"; break;
			case "testCML_Private_person": $name="Частное лицо"; break;
			case "testCML_PropertyNomenclatures": $name="СвойствоНоменклатуры"; break;
			case "testCML_PropertyUseFor": $name="ИспользованиеСвойства"; break;
			case "testCML_Rate": $name="Ставка"; break;
			case "testCML_Ratio": $name="Пересчет"; break;
			case "testCML_RegistrationAddress": $name="АдресРегистрации"; break;
			case "testCML_Relationship": $name="Отношение"; break;
			case "testCML_Required": $name="Обязательное"; break;
			case "testCML_Reserve": $name="Резерв"; break;
			case "testCML_Role": $name="Роль"; break;
			case "testCML_RoleCustomerDefault": $name="Покупатель"; break;
			case "testCML_RoleVendorDefault": $name="Продавец"; break;
			case "testCML_Shipping_date": $name="Дата отгрузки"; break;
			case "testCML_Sum": $name="Сумма"; break;
			case "testCML_Taxes": $name="Налоги"; break;
			case "testCML_TaxNDS": $name="НДС"; break;
			case "testCML_TaxRate": $name="СтавкаНалога"; break;
			case "testCML_TaxRates": $name="СтавкиНалогов"; break;
			case "testCML_Time": $name="Время"; break;
			case "testCML_TraitValue": $name="ЗначениеРеквизита"; break;
			case "testCML_TypeOfNomenclature": $name="ТипНоменклатуры"; break;
			case "testCML_UnitOfMeasurement": $name="ЕдиницаИзмерения"; break;
			case "testCML_VidOfNomenclature": $name="ВидНоменклатуры"; break;
			case "testCML_Width": $name="Ширина"; break;
			case "testCML_Without_tax": $name="Без налога"; break;
			case "testCML_Work_phone": $name="Телефон рабочий"; break;
			/*********************************/
			case "Address": $name="Адрес"; break;
			case "Amount": $name="Количество"; break;
			case "Anons": $name="Анонс"; break;
			case "Article": $name="Артикул"; break;
			case "Attributes": $name="Характеристики"; break;
			case "Balance": $name="Остаток"; break;
			case "Balances": $name="Остатки"; break;
			case "BarCode": $name="ШтрихКод"; break;
			case "Barcode": $name="Штрихкод"; break;
			case "BaseUnit": $name="БазоваяЕдиница"; break;
			case "ByDefault": $name="ПоУмолчанию"; break;
			case "Catalog": $name="Каталог"; break;
			case "CatalogId": $name="ИдКаталога"; break;
			case "ChangesOnly": $name="СодержитТолькоИзменения"; break;
			case "Choice": $name="Вариант"; break;
			case "ChoiceValue": $name="ВариантЗначения"; break;
			case "ChoiceValues": $name="ВариантыЗначений"; break;
			case "Code": $name="Код"; break;
			case "Comment": $name="Комментарий"; break;
			case "CommerceInformation": $name="КоммерческаяИнформация"; break;
			case "Condition": $name="Условие"; break;
			case "Contact": $name="Контакт"; break;
			case "Contacts": $name="Контакты"; break;
			case "CreationDate": $name="ДатаФормирования"; break;
			case "Currency": $name="Валюта"; break;
			case "Deleted": $name="Удален"; break;
			case "Description": $name="Описание"; break;
			case "DiscountMarkup": $name="СкидкиНаценки"; break;
			case "DisplayType": $name="ТипОтображения"; break;
			case "Element": $name="Товар"; break;
			case "ElementProperties": $name="СвойстваЭлементов"; break;
			case "Elements": $name="Товары"; break;
			case "External": $name="Внешний"; break;
			case "File": $name="Файл"; break;
			case "FileDescription": $name="ОписаниеФайла"; break;
			case "Files": $name="Файлы"; break;
			case "FullName": $name="НаименованиеПолное"; break;
			case "FullTitle": $name="ПолноеНаименование"; break;
			case "GroupProperties": $name="СвойстваГрупп"; break;
			case "HTMLDescription": $name="ОписаниеВФорматеHTML"; break;
			case "Hint": $name="Подсказка"; break;
			case "Id": $name="Ид"; break;
			case "InSum": $name="УчтеноВСумме"; break;
			case "InheritedTemlpates": $name="НаследуемыеШаблоны"; break;
			case "IntlAbbreviation": $name="МеждународноеСокращение"; break;
			case "ItemAttribute": $name="ХарактеристикаТовара"; break;
			case "ItemAttributes": $name="ХарактеристикиТовара"; break;
			case "List": $name="Справочник"; break;
			case "Manufacturer": $name="Изготовитель"; break;
			case "ManufacturerProperty": $name="Производитель"; break;
			case "MarkedForDeletion": $name="ПометкаУдаления"; break;
			case "Measure": $name="Единица"; break;
			case "Metadata": $name="Классификатор"; break;
			case "MetadataId": $name="ИдКлассификатора"; break;
			case "Multiple": $name="Множественное"; break;
			case "Number": $name="Число"; break;
			case "Offer": $name="Предложение"; break;
			case "OfferChange": $name="ИзмененияПакетаПредложений"; break;
			case "Offers": $name="Предложения"; break;
			case "OffersList": $name="ПакетПредложений"; break;
			case "OfficialTitle": $name="ОфициальноеНаименование"; break;
			case "Owner": $name="Владелец"; break;
			case "Percent": $name="Процент"; break;
			case "Picture": $name="Картинка"; break;
			case "Pictures": $name="Картинки"; break;
			case "Price": $name="Цена"; break;
			case "PriceForOne": $name="ЦенаЗаЕдиницу"; break;
			case "PriceType": $name="ТипЦены"; break;
			case "PriceTypeId": $name="ИдТипаЦены"; break;
			case "PriceTypes": $name="ТипыЦен"; break;
			case "Prices": $name="Цены"; break;
			case "ProductSets": $name="НаборыТовара"; break;
			case "ProductsSets": $name="НаборыТоваров"; break;
			case "Properties": $name="Свойства"; break;
			case "PropertiesValues": $name="ЗначенияСвойств"; break;
			case "Property": $name="Свойство"; break;
			case "PropertyValue": $name="ЗначениеСвойства"; break;
			case "PropertyValues": $name="ЗначенияСвойства"; break;
			case "Rate": $name="Коэффициент"; break;
			case "SDP": $name="ЧРД"; break;
			case "SchemaVersion": $name="ВерсияСхемы"; break;
			case "Section": $name="Группа"; break;
			case "Sections": $name="Группы"; break;
			case "Serialized": $name="Сериализовано"; break;
			case "Set": $name="Набор"; break;
			case "SetElement": $name="ЭлементНабора"; break;
			case "ShortName": $name="НаименованиеКраткое"; break;
			case "ShowExpanded": $name="ПоказатьРазвёрнутым"; break;
			case "SmartFilter": $name="УмныйФильтр"; break;
			case "Sort": $name="Сортировка"; break;
			case "Status": $name="Статус"; break;
			case "String": $name="Строка"; break;
			case "SumFormat": $name="ФорматСуммы"; break;
			case "Tax": $name="Налог"; break;
			case "Template": $name="Шаблон"; break;
			case "Title": $name="Наименование"; break;
			case "Traits": $name="Реквизиты"; break;
			case "TraitsValues": $name="ЗначенияРеквизитов"; break;
			case "Type": $name="Тип"; break;
			case "UnitsOfMeasurement": $name="ЕдиницыИзмерения"; break;
			case "Value": $name="Значение"; break;
			case "ValueCondition": $name="ЗначениеУсловия"; break;
			case "ValueId": $name="ИдЗначения"; break;
			case "ValuesType": $name="ТипЗначений"; break;
			case "ValuesTypes": $name="ТипыЗначений"; break;
			case "VersionNumber": $name="НомерВерсии"; break;
			case "View": $name="Представление"; break;
			case "Warehouse": $name="Склад"; break;
			case "WarehouseID": $name="ИдСклада"; break;
			case "WarehouseStock": $name="КоличествоНаСкладе"; break;
			case "Warehouses": $name="Склады"; break;
			case "WarehousesStock": $name="КоличествоНаСкладах"; break;
			case "Weight": $name="Вес"; break;
			default: $name=$id; break;
		}
		return $name;
	}
}
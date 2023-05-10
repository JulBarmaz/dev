/*функции подсистемы переводов*/
function copyDataForTranslate(tab,tablang){
	//получаем названия полей в массиве
	let is_data=false;
	$("div#"+tab+" input[name$='dest_fld[]']").each(function( index ) {
		// находим ид техничского поля   
		let fid=$(this).attr('id');
		//console.log(fid);
		//let tid=fid.replace("dest_fld","type_fld");
		let type_fld=$("#"+fid.replace("dest_fld","type_fld")).val();
		// смотрим тип значения
		let destid=$(this).val(); // в это поле будем складывать результат
		// предварительно скопируем прежнее значение значение и, когда оно у нас будет(из базы придет - текущие не сохраняем) - сделаем активной кнопку восстановления
		// ну или повесим на нее обработчик - что будет культурнее   
		let old_data=$("#"+destid).val(); // тут у нас только те данные, что поднялись из базы, текущих нет ( работает ck editor )
		//console.log(old_data); 
		// теперь сохраняем в это поле данные поля источника
		$("#"+fid.replace("dest_fld","backup_fld")).val(old_data);
		if(old_data!='') is_data=true;
		// затрем постфикс языка
		let srcdata=$("#"+destid.replace('_'+tablang,'')).val();	 
		//console.log('srcdata --  '+$("#"+destid).val());
		$("#"+destid).val(srcdata);
		//console.log('resdata --  '+$("#"+destid).val());
		// пихнем наши изменения в ckeditor а то не видать их там
		// если у нас тип соответствующий то нам нужен ckeditor
		if(type_fld=='texteditor'){
			CKEDITOR.instances[destid].setData(srcdata);
		}
		//CKEDITOR.instances[destid].setData(srcdata, {internal: true});
		// сначала определяем тип поля - хотя у нас работают только строки и текстареа , в обоих случаях работает val
		// но мало ли что понадобится в будущем
		 // console.log( index + ": " +this.id+" - "+fid+' - '+ destid + ' data '+ old_data);
		});
	if(is_data) // есть что восстанавливать
	{
		// биндим к кнопке функцию
		// делаем ее активной
		$("#tr_but_restore").css('cursor','pointer');
		$("#tr_but_restore").css('backgroup-color','#2aabd2');
		$("#tr_but_restore").addClass('commonButton btn-info');
		$("#tr_but_restore").bind( "click", function() {
			restoreDataFromBackup(tab,tablang);
		});
	}	
}

function restoreDataFromBackup(tab,tablang){
	$("div#"+tab+" input[name$='backup_fld[]']").each(function( index ) {
		let fid=$(this).attr('id');
		let backup_data=$(this).val(); // это сохраненные данные перед копированием
		$(this).val(''); // очищаем состояние полей бекапа
		// теперь возвращаем их в поля
		$("#"+fid.replace("backup_fld","dest_fld")).val(backup_data);
		CKEDITOR.instances[fid].setData(backup_data);
	});
	// восстанавливааем состояние кнопки до обычного спана 
	$("#tr_but_restore").css('cursor','none');
	$("#tr_but_restore").css('backgroup-color','silver');
	$("#tr_but_restore").removeClass('commonButton btn-info');
	$("#tr_but_restore").unbind("click");
	
}
<?
	$add_error=array();
	
	/*Настройки поля чекбоксы*/
	$dt_settings_checkbox=array('important'=>'Обязательно для заполнения', 'nospan'=>'Nospan (div clear)', 'show_search'=>'Выводить в поиске раздела', 'show_list'=>'Выводить в таблице списка');
	
	/*Настройки персональные раздела*/
	$sec_settings_checkbox=array('onoff'=>'Вкл\Откл записей', 'precedence'=>'Порядок записей', 'show_id'=>'Показывать id', 'no_paging'=>'Без пейджинга', 'reklama'=>'С коммерческим размещением');
	
	/*Данные шаблона*/
	$SectionPattern = new $section['pattern'];
	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
	$all_types=$CDDataType->getSetting('all_types');
	
	$help=$SectionPattern->getSetting('help');
	
	/*Данные колонок*/
	$columns=$CDDataType->get_column_info($SectionPattern->getSetting('table'),$section['id']);
	
	/*Получаем все типы данных*/
	$type_array=array(''=>'');
	foreach ($all_types as $at)
	{
		$type=new $at;
		$type->init(array());
		$type_array[$at]['description']=$type->getSetting('descr');
		$type_array[$at]['default_type']=$SectionPattern->get_default_type(array('type'=>$at));
	
	}
	asort($type_array);
	
	
	$dataset=$CDDataSet->get($SectionPattern->getSetting('dataset'), $section['id']);

		

?>
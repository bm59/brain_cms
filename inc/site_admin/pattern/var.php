<?
	$add_error=array();
	
	/*��������� ���� ��������*/
	$dt_settings_checkbox=array('important'=>'����������� ��� ����������', 'nospan'=>'Nospan (div clear)', 'show_search'=>'�������� � ������ �������', 'show_list'=>'�������� � ������� ������');
	
	/*��������� ������������ �������*/
	$sec_settings_checkbox=array('onoff'=>'���\���� �������', 'precedence'=>'������� �������', 'show_id'=>'���������� id', 'no_paging'=>'��� ���������', 'reklama'=>'� ������������ �����������');
	
	/*������ �������*/
	$SectionPattern = new $section['pattern'];
	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
	$all_types=$CDDataType->getSetting('all_types');
	
	$help=$SectionPattern->getSetting('help');
	
	/*������ �������*/
	$columns=$CDDataType->get_column_info($SectionPattern->getSetting('table'),$section['id']);
	
	/*�������� ��� ���� ������*/
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
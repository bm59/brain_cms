<div class="clear"></div><br/><br/>
<h2>Добавить шаблонное поле</h2>
<?
$add_patterns=
array(
	
		array('description'=>'Имя', 'name'=>'name', 'type'=>'CDText'),
		array('description'=>'Текст', 'name'=>'text', 'type'=>'CDTextEditor', 'pre_settings'=>'|texttype=full|'),
		array('description'=>'Картинка', 'name'=>'image', 'type'=>'CDImage'),
		array('description'=>'Галерея изображений', 'name'=>'gallery', 'type'=>'CDGallery'),
		array('description'=>'Счетчик', 'name'=>'spinner', 'type'=>'CDSpinner','pre_settings'=>'|default=5|min=1|max=10|important|'),
		array('description'=>'Файл', 'name'=>'file', 'type'=>'CDFile'),
		
		array('description'=>'Title', 'name'=>'ptitle', 'type'=>'CDText', 'setting_style_edit'=>'width:32%; margin-right:2%;'),
		array('description'=>'Description', 'name'=>'pdescription', 'type'=>'CDText', 'setting_style_edit'=>'width:32%; margin-right:2%;'),
		array('description'=>'Псевдоним ссылки', 'name'=>'pseudolink', 'type'=>'CDText', 'setting_style_edit'=>'width:32%;'),
		
		
);
?>
<table class="table-content stat pattern">
<tbody id="sortable2" class="connectedSortable">
<?

$i=0;
foreach ($add_patterns as $ap)
if (!in_array($ap['name'], $dt_names))
{
	
	$ds=$ap;
	$ds['id']='new_'.(100+$i);
	$datatype['type']=$ap['type'];
	$ds['table_type']=$VirtualPattern->get_default_type($datatype);
	print_dt($ds);
	$i++;
}
?>
</tbody>
</table>
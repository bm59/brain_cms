<div class="clear"></div><br/><br/>
<h2>�������� ��������� ����</h2>
<?
$add_patterns=
array(
	
		array('description'=>'���', 'name'=>'name', 'type'=>'CDText'),
		array('description'=>'�����', 'name'=>'text', 'type'=>'CDTextEditor', 'pre_settings'=>'|texttype=full|'),
		array('description'=>'��������', 'name'=>'image', 'type'=>'CDImage'),
		array('description'=>'������� �����������', 'name'=>'gallery', 'type'=>'CDGallery'),
		array('description'=>'�������', 'name'=>'spinner', 'type'=>'CDSpinner','pre_settings'=>'|default=5|min=1|max=10|important|'),
		array('description'=>'����', 'name'=>'file', 'type'=>'CDFile'),
		
		array('description'=>'Title', 'name'=>'ptitle', 'type'=>'CDText', 'setting_style_edit'=>'width:32%; margin-right:2%;'),
		array('description'=>'Description', 'name'=>'pdescription', 'type'=>'CDText', 'setting_style_edit'=>'width:32%; margin-right:2%;'),
		array('description'=>'��������� ������', 'name'=>'pseudolink', 'type'=>'CDText', 'setting_style_edit'=>'width:32%;'),
		
		
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
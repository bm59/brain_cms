<? 
	include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/pattern/var.php";
	include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/pattern/functions.php";

	$i=0;
	

	
	
	/*Удаление типа данных*/
	if ($_GET['delete']>0)
	{
		$CDDataType->delete($_GET['delete'], $SectionPattern->getSetting('table'));
	}

	
	/*Сохранение и добавление*/
	$save_fields=array('name','description','type','settings','setting_style_edit');
	
	
if (floor($_POST['action'])==1)
{
	/* print_r($_POST); */	
	$SiteSections->update_personal_settings($section['id'], $_POST['settings_section']);

	
	/* Применяем настройки только к текущему разделу или ко всем в шаблоне */
/* 	if ($_POST['apply_all']=='on')
	{
		$ret=$CDDataType->get_dataset_tables($SectionPattern->getSetting('name'));
		$table_array=$ret['tables'];
		$sections_array=$ret['ids'];
	} */

	$table_array[]=$SectionPattern->getsetting('table');
	$sections_array[]=$section['id'];
	

	
	/* print_r($table_array); */

	
		foreach($_POST as $k=>$v){
		if (preg_match('|^id\_[a-z_A-Z_0-9]+$|',$k))
		{
			
			$id=preg_replace('|^id\_([a-z_A-Z_0-9]+)$|','\\1',$k);
						 
			
			$update['precedence']=$i;
			$table_type_options=$_POST['table_type_'.$id];
			
			foreach ($save_fields as $sf)
			{
				if (isset($_POST[$sf.'_'.$id])) $update[$sf]=$_POST[$sf.'_'.$id];
			}


			
			
			/* Проверяем на ошибки */
			$errors=array();
			if (!$dataset['id']>0) 						$errors[]='Не передан dataset id';
			if ($update['name']=='') 					$errors[]='Не задано имя колонки';
			if ($update['description']=='') 			$errors[]='Не задано описание колонки';
			if ($update['type']=='') 					$errors[]='Не задан тип колонки';
			if ($table_type_options=='') 				$errors[]='Не задано свойство колонки';
			
			if (count($errors)) $error_ids[]=$id;
			
			for ($j = 0; $j < count($errors); $j++) $_SESSION['global_alert'].='<i><span style="color: #CC0000">Ошибка:</span></i> '.$errors[$i].' | '.$id.'<br/>';

			if (count($errors)==0)
			{
				if (stripos($id, 'new')===false)
				{
					$result=$CDDataType->update(
						$id, 
						$update, 
						$columns,
						$table_type_options, 
						$table_array,
						$section['id']);
						
						if ($result!='' && $result!=true)			$error_ids[]=$id;
						elseif ($result==true)						$ok_ids[]=$id;
						
				}
				else
				{
						$update['dataset']=$dataset['id'];
						$update['table_type']=$_POST['table_type_'.$id];
						$result=$CDDataType->add($update, true, $table_array);
						if ($result===false) 
						{
							$error_ids[]=$id; 
						}
						elseif($result>0) {$ok_ids[]=$result; print $result.'!';} 
				}
			}
			
			$i++;
		}
	}
	
	$CDDataSet->update_prec($dataset['id'], $section['id']);
}	
	/*Обновляем данные*/
	$columns=$CDDataType->get_column_info($SectionPattern->getSetting('table'));
	$dataset=$CDDataSet->get($SectionPattern->getSetting('dataset'));

	
	/* Если были ошибки при добавлении, добавляем колонку к полям данных с ошибкой */
	foreach ($dataset['types'] as $tp)
	{
		$store_ids[]=$tp['id'];
		$store_names[]=$tp['name'];
	}
	foreach($_POST as $k=>$v){
		if (preg_match('|^id\_[a-z_A-Z_0-9]+$|',$k))
		{
			$id=preg_replace('|^id\_([a-z_A-Z_0-9]+)$|','\\1',$k);
			if (stripos($id, 'new')!==false)
			{
			if (!in_array($id, $store_ids) && !in_array($_POST['name_'.$id], $store_names)) $add_error[]=array('id'=>$id, 'description'=>$_POST['description_'.$id],
					'name'=>$_POST['name_'.$id],'type'=>$_POST['type_'.$id],'settings'=>$_POST['settings_'.$id], 'setting_style_edit'=>$_POST['setting_style_edit_'.$id]);
			}
		}
	}
	


	
?>
<script>
  $(function() {

	new_count=$("input[type=hidden][name*=id_new]").length+1;
	
    $( "#sortable" ).sortable({
      handle: ".drag_icon"
    });

    $(document).on('change','select', function() {

     	cur_id=$(this).attr('name');
     	cur_id=cur_id.split('_');
     	alert(cur_id[1]);
		if (cur_id[1]!='')
		$('[name=table_type_'+cur_id[1]+']').val($(this).find(':selected').attr('data-default'));
		
        return false;
    });

    
    $(document).on('change',".dt_settings input[type='checkbox']", function() {

		var inp_str=$(this).parents('.dt_settings').find('.setting_text').val();
		if ($(this).is(":checked")==false)
		{
			inp_str=inp_str.replace($(this).attr('data-name')+'|','');
			if (inp_str=='|') inp_str='';
		}
		else 
		inp_str+=(inp_str=='' ? '|':'')+$(this).attr('data-name')+'|';

		$(this).parents('.dt_settings').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
    });

    $(document).on('change',".section_settings input[type='checkbox']", function() {

		var inp_str=$(this).parents('.section_settings').find('.setting_text').val();
		if ($(this).is(":checked")==false)
		{
			inp_str=inp_str.replace($(this).attr('data-name')+'|','');
			if (inp_str=='|') inp_str='';
		}
		else 
		inp_str+=(inp_str=='' ? '|':'')+$(this).attr('data-name')+'|';

		$(this).parents('.section_settings').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
    });

    
    $(document).on('click',"#save_form .show_settings", function() {


		var parent=$(this).parents('td').prev();
		if (!parent.find('.dt_settings').is(':visible'))
		{
			$('.dt_settings').show();
		}
		else
		{
			$('.dt_settings').hide();
		}
		var elem=$(this).parents('td');
      	destination = elem.offset().top-50;
        $("html, body").animate({scrollTop:destination},"slow");
        return false;
    });

    $(document).on('click',"#save_form .on_off", function() {

		cur_val=$('[name='+$(this).attr('data-inp')+']');
    	var inp_str=$(this).parents('tr').find('.setting_text').val();
		if (cur_val.val()==1)
		{
			inp_str=inp_str.replace('off|','');
			cur_val.val(0);
			$(this).css('backgroundImage', 'url("/pics/editor/on.png")');
		}
		else
		{ 
			inp_str+=(inp_str=='' ? '|':'')+'off|';
			cur_val.val(1);
			$(this).css('backgroundImage', 'url("/pics/editor/off.png")');
		}

		$(this).parents('tr').find('.setting_text').val(inp_str);
		//alert($(this).attr('data-name'));
        return false;
        
    });


		
	
  });

  var new_count=1;
  function add_empty() {
  	$.ajax({
          type: "POST",
          url: "/inc/site_admin/pattern/ajax.php",
          data: "action=add_empty&section_id=<?=$_GET['section']?>&new_count="+new_count,
          success: function(html){
        	  $('#sortable').append(html);
        	  new_count++;
          }
      });
  }
</script>
<div id="content">
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
<?
if ($_SESSION['global_alert']) print '<div class="alert"><div>'.$_SESSION['global_alert'].'</div></div>';
?>
	<h2>Настройки шаблона:</h2>
		<div>id: <?=$SectionPattern->getSetting('dataset')?></div>	
		<div>Название: <?=$dataset['name']?></div>	
		<div>Описание: <?=$dataset['description']?></div>		
		<div>Таблица: <?=$SectionPattern->getSetting('table');?></div>
		<div>Patern: <?=$SectionPattern->getSetting('name'); ?></div>
		<div>Class: <?=get_class($SectionPattern->getSetting('cclass')); ?></div>
	<div class="clear"></div>
	
	
	
<div class="hr"></div>
<h2>Шаблон:</h2> 
<form id="save_form" name="save_form" action="" method="POST" enctype="multipart/form-data">
<div class="stat section_settings">
<?
$section = $SiteSections->get($section['id']);
$str_settings=$section['settings_personal'];
foreach($sec_settings_checkbox as $k=>$v)
{
	/* Включена ли настройка */
	$set_on=false;
	if (stripos($str_settings, "|$k|")!==false) $set_on=true;

	?><input data-name="<?=$k?>"  id="section_<?=$k?>" type="checkbox" <?=$set_on ? 'checked="checked"' : '' ?>><label for="section_<?=$k?>"><?=$v?></label><?
}
?>
	<div class="clear"></div>
	<br/>
	<!-- Настройки раздела -->
	<label>Настройки раздела (разделитель |):</label>
	<span class="input">
		<input type="text" value="<?=$str_settings?>"  class="setting_text" name="settings_section">
	</span>
</div>
<div class="clear"></div>

<input type="hidden" name="action" value="1">
	<table class="table-content stat pattern">
	<tbody id="sortable">
	<? 
	$dataset=$CDDataSet->get($SectionPattern->getSetting('dataset'), $section['id']);
	 $dataset['types']=array_merge ($dataset['types'],$add_error); 
	foreach ($dataset['types'] as $ds)
	{
	 	if (in_array($ds['id'], $error_ids)) 	$ds['tr_type']='tab_alert';
	 	if (in_array($ds['id'], $ok_ids)) 		$ds['tr_type']='tab_ok';
		print_dt($ds);

	}
	?>
	</tbody>
	</table>
		<div class="place">
		 	<span style="float: left;">
		        <input class="button big" onclick="add_empty(); return false;" type="submit" name="save_form" value="Добавить поле"/>
		    </span>
		    <span style="float: right;">
<!-- 		    	<div class="stat">
		    		<input id="apply_all" name="apply_all" type="checkbox"><label for="apply_all">Применить ко всем разделам шаблона</label>
		    	</div> -->
		        <input class="button big" type="submit" name="save_form" value="Сохранить изменения"/>
		    </span>
		</div>	
	                   
</form>
</div>
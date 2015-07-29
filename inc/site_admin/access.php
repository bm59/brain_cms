<?


$path='/manage/control/contents/';
if ($_GET['section']>0) $path.='?section='.$_GET['section'];

/*Добавление записей*/
if ($mode!='development' && !in_array('add', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( ".button:contains('Добавить')").addClass("disabled");
       $( ".button:contains('Добавить')").click(function(){return false;});
       $( "input[value*='Добавить']").addClass("disabled");
       $( "input[value*='Добавить']").click(function(){return false;});

       <?if (!$_GET['pub']>0){?>
       	$( ".button:contains('Сохранить изменения')").addClass("disabled");
       	$( ".button:contains('Сохранить изменения')").click(function(){return false;});
       	$( "input[value*='Сохранить изменения']").addClass("disabled");
       	$( "input[value*='Сохранить изменения']").click(function(){return false;});
       <?}?>
  	});
  </script>
<?
	if ($_GET['pub']=='new' || $_GET['pub']=='add')
	{
 		header('Location: '.$path);
 		die();
 	}
}
?>
<?
/*Редактирование*/
if ($mode!='development' && !in_array('edit', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( "a[title*='Редактировать'] IMG").attr("src","/pics/editor/prefs-disabled.gif");
       $( "a[title*='Редактировать']").attr("href","");
       $( "a[title*='Редактировать']").click(function(){return false;});

       $( "a:contains('Редактировать')").click(function(){return false;});
       $( "a:contains('Редактировать')").attr("href","");

       $( ".button:contains('Сохранить')").addClass("disabled");
       $( ".button:contains('Сохранить')").click(function(){return false;});
       $( "input[value*='Сохранить']").addClass("disabled");
       $( "input[value*='Сохранить']").click(function(){return false;});


  	});
  </script>



<?
 if (floor($_GET['pub'])>0 || floor($_POST['editformpost'])==1)
 {
 	header('Location: '.$path);
 	die();
 }
}

/*Удаление*/
if ($mode!='development' && !in_array('delete', $group['new_settings'][$cid]))
{
?>
	<script>
  	$(function() {
       $( "a[title*='Удалить'] IMG").attr("src","/pics/editor/delete-disabled.gif");
       $( "a[title*='Удалить']").attr("href","");
       $( "a[title*='Удалить']").click(function(){return false;});

       $( "a:contains('Удалить')").click(function(){return false;});
       $( "a:contains('Удалить')").attr("href","");

       $( "input[title*='Удалить']").css("backgroundImage", "url('/pics/editor/delete-disabled.gif')");
       $( "input[title*='Удалить']").click(function(){return false;});
  	});
  </script>



<?
 if (floor($_GET['delete'])>0)
 { 	header('Location: '.$path);
 	die();
 }

}
?>
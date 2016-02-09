<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";

if (!$activeccid>0)
$activeccid=$Content->getIdByPath(configGet("AskUrl"));

if (!@in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['edit']>0)
header("Location: /manage/control/contents/");
if (!@in_array('add',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['edit']=='')
header("Location: /manage/control/contents/");


$data = array('name'=>'','access'=>array(),'settings'=>array());
$errors = array();
$editid = $VisitorType->checkTypePresence($_GET['edit']);
if ($editid>0)
{	$data = $VisitorType->getOne($editid);
	$new_settings=$data['new_settings'];
}
if (isset($data['settings']['noedit'])){
	$data = array('name'=>'','access'=>array(),'settings'=>array());
	$editid = 0;
}
if (isset($_POST['addeditgroup'])){
	$editid = floor($_POST['addeditgroup']);
	if (!isset($data['settings']['norename'])) $data['name'] = htmlspecialchars(trim($_POST['name']));
	$data['access'] = array();

	$access_settings='';
	foreach ($_POST as $k=>$v){
		if (preg_match('|^section\_[0-9]+$|',$k)){

			$section_id=preg_replace('|^section\_([0-9]+)$|','\\1',$k);

			if ($section_id>0)
			{
				$action_text='';
				foreach ($_POST as $k=>$v)
				if (preg_match('|^action\_'.$section_id.'\_[a-z]+$|',$k))
				{
	            	$action=preg_replace('|^action\_'.$section_id.'\_([a-z]+)$|','\\1',$k);
	            	if ($action!='') $action_text.=(($action_text!='') ? ',':'').$action;

				}

				$access_settings.=(($access_settings=='') ? '|':'').$section_id.'='.$action_text.'|';

			}

		}
	}


	if ($editid>0)
	{		$errors = $VisitorType->edit($editid,$data['name'],$access_settings);
		WriteLog($editid, 'редактирование группы', $data['name']);
	}
	else
	{		$errors = $VisitorType->add($data['name'],$access_settings);
		WriteLog(0, 'добавление группы', $data['name']);
	}


	if (count($errors)==0) header("Location: ../");
}
$settings = ($editid>0)?array('title'=>'Редактирование группы пользователей','button'=>'Сохранить'):array('title'=>'Добавление группы пользователей','button'=>'Создать группу');
?>
<? include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php"; ?>
<script type="text/javascript">
	$(function()
	{		/*check_access();*/

		$(document).on("click", "input[type='checkbox'][id*='section_']",  function()
		{/*			if ($(this).prop("checked")==true)
			$(this).parent().parent().find('input').prop("checked", "checked");
			else
			$(this).parent().parent().find('input').prop("checked", "");

			check_access();
			check_access();*/
			if ($(this).prop("checked")==true)
			$(this).parents('LI:first').find('input').prop("checked", "checked");
			else $(this).parents('LI:first').find('input').prop("checked", "");
		});

		$(document).on("click", ".actions input[type='checkbox']",  function()
		{
				if ($(this).parents('LI:first').find(".actions input:checked").length>0)
				$(this).parents('LI:first').find('input:first').prop("checked", "checked");
				else $(this).parents('LI:first').find('input:first').prop("checked", "");
		});


		function check_access ()
		{
					$('.foraccess > UL > LI').has('UL').each(function()
					{
						if ($(this).find(' LI > label > input:checked ').length>0)
						$(this).find('input:first').prop("checked", "checked");
                        else
                        $(this).find('input:first').prop("checked", "");

					});

		}	});
</script>
<div id="content">
	<? include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php"; ?>
	<div id="content" class="forms">
        <div class="hr"></div>
  		<h1><a href="/manage/">Панель управления</a> &rarr; Доступ &rarr; <a href="/manage/access/groups/">Группы</a> &rarr; Редактирование</h1>
        <br/>
		<?
		if (count($errors)>0){
			print '
			<p><strong>'.$settings['title'].' не выполнено по следующим причинам:</strong></p>
			<ul class="errors">';
				foreach ($errors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}
		?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
			<input type="hidden" name="addeditgroup" value="<?=$editid?>">
			<div class="place" style="width: 300px;">
				<label>Название</label>
				<span class="input">
					<input name="name" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['name']?>" />
				</span>
			</div>
			<span class="clear"></span>
			<h1>Разделы для доступа</h1>
			<div class="place" style="z-index: 10;">
				<div class="forckecks foraccess">
					<?
					$VisitorType->drawAccessCallback(0,-1,$data['access']);
					?>
				</div>
			</div>
			<span class="clear"></span>
			<div class="place">
				<span style="float: right;">
					<input class="button big" type="submit" name="editgroup" value="<?=$settings['button']?>"/>
				</span>
			</div>
			<span class="clear"></span>
		</form>
		<div class="hr"><hr /></div>
			<a href="/manage/access/groups/" class="button">Перейти к списку групп</a>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>

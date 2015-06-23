<?
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";

if (!$activeccid>0)
$activeccid=$Content->getIdByPath(configGet("AskUrl"));

if (!in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['edit']>0)
header("Location: /manage/control/contents/");
if (!in_array('add',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['edit']=='')
header("Location: /manage/control/contents/");


$data = array('name'=>'','access'=>array(),'settings'=>array());
$errors = array();
$editid = $VisitorType->checkTypePresence($_GET['edit']);
if ($editid>0)
{	$data = $VisitorType->getOne($editid);
	$new_settings=$data['new_settings'];
	print_r($new_settings);
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


	if ($editid>0) $errors = $VisitorType->edit($editid,$data['name'],$access_settings);
	else $errors = $VisitorType->add($data['name'],$access_settings);
	if (count($errors)==0) header("Location: ../");
}
$settings = ($editid>0)?array('title'=>'Редактирование группы пользователей','button'=>'Сохранить'):array('title'=>'Добавление группы пользователей','button'=>'Создать группу');
?>
<? include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php"; ?>
<script type="text/javascript">
	jQuery(function()
	{		check_access();

		jQuery(document).on("click", "input[type='checkbox']",  function()
		{			if (jQuery(this).prop("checked")==true)
			jQuery(this).parent().parent().find('input').prop("checked", "checked");
			else
			jQuery(this).parent().parent().find('input').prop("checked", "");

			check_access();
			check_access();
		});

		jQuery(document).on("click", ".actions input[type='checkbox']",  function()
		{
				var count_all=0;
				var count_check=0;
				jQuery(this).parent().parent().parent().find("input").each(function()
				{	               if (jQuery(this).prop("checked")==true)
                   count_check++;

                   count_all++;
				});

				if (count_check>0)
				jQuery(this).parents('LI').find('input:first').prop("checked", "checked");
				else jQuery(this).parents('LI').find('input:first').prop("checked", "");

		});


		function check_access ()
		{
					//alert(jQuery(elem).attr('id'));
					jQuery('.foraccess LI').has('UL').each(function()
					{
						var count_all=0;
						var count_check=0;
						var parent_ul=jQuery(this).attr('id');
						jQuery('#'+parent_ul+' LI').each(function()
						{                          //alert(jQuery(this).find('input').prop("checked"));
                          if (jQuery(this).find('input').prop("checked")==true)
                          count_check++;

                          count_all++;

						});

						//alert('Всего '+count_all+'; отмечено '+count_check);
						if (count_check>0)
						jQuery('#'+parent_ul).find('input:first').prop("checked", "checked");
                        else
                        jQuery('#'+parent_ul).find('input:first').prop("checked", "");

					});

		}	});
</script>
<style>
.foraccess > UL > LI > LABEL {font-size: 18px; background-color: #CCC; padding: 15px}
.foraccess > UL > LI {width: 100%; border-bottom: none;}
.foraccess UL UL {margin: 10px 0 0 10px;}
.foraccess LI {background: none; padding: 10px 0; border-bottom: 1px solid #CCC;}
.foraccess LABEL INPUT {position: relative !important; margin: 3px;}

.foraccess .actions {float: right; position: absolute; top: 18px;}
.foraccess UL UL .actions {float: right; position: absolute; top: 4px; right: 10px;}
.foraccess .actions DIV {float: left;}
</style>

<div id="zbody">
	<? include $_SERVER['DOCUMENT_ROOT']."/inc/content/header.php"; ?>
	<div id="content" class="forms">
		<h1><a href="../">Группы пользователей</a> &rarr; <?=$settings['title']?></h1>
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
					<span class="bl"></span>
					<span class="bc"><input name="name" maxlength="25" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['name']?>" /></span>
					<span class="br"></span>
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
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc"><?=$settings['button']?></span>
					<span class="br"></span>
					<input type="submit" name="editgroup" value=""/>
				</span>
			</div>
			<span class="clear"></span>
		</form>
		<div class="hr"><hr /></div>
		<div id="paging" class="nopad">
			<a href="./" class="button">
				<span class="bl"></span>
				<span class="bc">Перейти к списку групп</span>
				<span class="br"></span>
			</a>
		</div>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>

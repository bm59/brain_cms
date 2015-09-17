<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/auth.php");?>
<?

if (!isset($_POST['edit_image']))
{
	if ($_GET['file']=='') die('No image');
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$_GET['file'])) die('File not exist');

}

if (isset($_POST['edit_image']))
{

	print 'OK';
	
	$image['path']=$_POST['path'];
	$image['x1']=$_POST['x1'];
	$image['y1']=$_POST['y1'];
	$image['x2']=$_POST['x2'];
	$image['y2']=$_POST['y2'];

	/* if ($_POST['type_edit']=='mini') */
	{
		if ($image['x1']>0 || $image['x2']>0 || $image['y2']>0)
		{
			
			$mini_image=crop_editor($_SERVER['DOCUMENT_ROOT'].$image['path'], $image['x1'], $image['y1'], $image['x2']-$image['x1'], $image['y2']-$image['y1'], $_POST['editor_minw'], $_POST['editor_minh'], $_POST['editor_as_min']);
		}


	}
}


$datatype=msr(msq("SELECT * FROM `site_site_data_types` WHERE `section_id`=".$_GET['section']." and  `name`='".$_GET['rubric']."'"));

$settings=$VirtualPattern->explode($datatype['settings']);


$calc_w=$settings['editor_imgw']>0 ? $settings['editor_imgw'] : $settings['editor_minw'];
$calc_h=$settings['editor_imgh']>0 ? $settings['editor_imgh'] : $settings['editor_minh'];


if ($settings['editor_proport']=='auto')
{
	
	$settings['editor_proport']=$calc_w.':'.$calc_h;
}

configSet('contenttitle', 'Редактор фото');
configSet('contentdescription', 'Редактор фото');
?>
<?
include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php");

$def_x1=$calc_w>0 ? '10': '10';
$def_y1=$calc_h>0 ? '10': '10';

$def_x2=$calc_w>0 ? $calc_w+10: 150;
$def_y2=$calc_h>0 ? $calc_h+10: 150;

if ($_POST['edit_image']!='')
{

	?>
	<script type="text/javascript">
	$(document).ready(function () {
		$.cookie('change_photo', '1', {path: '/'});
		<?if ($mini_image!=''){?>$.cookie('add_mini', '<?=$mini_image?>', {path: '/'});<?}?>
		window.close();
	});
	</script>
	<?
}
?>

<script src="/js/jquery.imgareaselect.js" language="JavaScript" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/css/imgareaselect-default.css" media="all" />

<script type="text/javascript">

$(document).ready(function () {


var ias = $('img#photo').imgAreaSelect({
            enable:true,
<? if ($settings['editor_proport']){?>  aspectRatio: '<?=$settings['editor_proport']?>',	<?} ?>
            							x1: <?=(($_POST['x1']>0) ? $_POST['x1'] : 10)?>,
            							y1: <?=(($_POST['y1']>0) ? $_POST['y1'] : 10)?>,
					            		x2: <?=(($_POST['x2']>0) ? $_POST['x2'] : $def_x2)?>,
					            		y2: <?=(($_POST['y2']>0) ? $_POST['y2'] : $def_y2)?>,
					            		handles: true,
					            		keys: true,
					            		instance: true,

<?if ($calc_w>0){?>						minWidth: '<?=$calc_w?>',<?} ?>
<?if ($calc_h>0){?>						minHeight: '<?=$calc_h?>',<?} ?>					            	

            onSelectEnd: function (img, selection) {
                $('input[name="x1"]').val(selection.x1);
                $('input[name="y1"]').val(selection.y1);
                $('input[name="x2"]').val(selection.x2);
                $('input[name="y2"]').val(selection.y2);
            }
    });

		$('button#rectangle').click(function () {
/* 			selection=ias.getSelection();
			if ((selection.x1+selection.x2)<(selection.y1+selection.y2))
			{
				new_x1=selection.x1;
				new_x2=selection.x2;
				new_y1=selection.y1;
				new_y2=selection.x2;
			}

			ias.setOptions({ x1: new_x1, y1: new_y1, x2: new_x2, y2: new_y2 });
			ias.update(); */ 
			/* ias.setOptions({ aspectRatio: '1:1' });
			ias.update();
			alert(ias.getOptions().aspectRatio); */
			
			
	    });
		$('.ratio').click(function () {
			ias.setOptions({ aspectRatio: '1:1'});
			ias.update();
						
						
		});
		$('.ratio_off').click(function () {
			ias.setOptions({ aspectRatio: ''});
			ias.update();
						
						
		});


});

	function check_size()
	{
		
		var error=false;
		<? if ($calc_w>0) {?>
		if (($('input[name="x2"]').val()-$('input[name="x1"]').val())<<?=$calc_w?>) 
		{
			alert('Ширина выделеной области '+($('input[name="x2"]').val()-$('input[name="x1"]').val())+' меньше допустимой <?=$calc_w?>');
			error=true;
		}
		<?} ?>
		<? if ($calc_h>0) {?>
		if (($('input[name="y2"]').val()-$('input[name="y1"]').val())<<?=$calc_h?>)
		{
			alert('Высота выделеной области '+($('input[name="y2"]').val()-$('input[name="y1"]').val())+' меньше допустимой <?=$calc_h?>');
			error=true;
		}
		<?} ?>

		if (error==false)
		$('#save_form').submit();

	}

</script>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
	<div id="content">
	
		<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
		
		<?
		foreach ($settings as $k=>$v)
		print $k.'='.$v.'<br/>';
		print '<a href="'.$_GET['file'].'" target="_blank">'.$_GET['file'].'</a>';
		
		$image = getimagesize ($_SERVER['DOCUMENT_ROOT'].$_GET['file']);
		if (($calc_w>0 && $calc_h>0) && $image[0]==$calc_w && $image[1]==$calc_h) $error.='<h2>Изображение соответствует размеру</h2>';
		
		if (($calc_w>0 && $calc_h>0) && $image[0]<$calc_w || $image[1]<$calc_h) $error.='<h2>Изображение меньше минимальных размеров</h2>';
		

		if ($error!='')
		{
			print $error;
			print '<br/>ширина: '.$image[0].'; высота: '.$image[1];
		}
		else
		{
		?>

<form id="save_form" name="save_form"  action="" enctype="multipart/form-data" method="POST">
	<div style="padding: 10px  0 10px 0px; margin-left: -5px">
		<input type="submit" value="Сохранить изменения" onclick="check_size(); return false;" name="editform" class="button big">
	</div>
	
	<!-- <button id="rectangle" type="button">Rectangle</button> -->
	<a href="#" class="ratio">Ratio 1:1</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="#" class="ratio_off">Сбросить Ratio</a>		

	<div>
		<img id="photo" src="<?=$_GET['file']?>?<?=time()?>" style="border: 1px solid #CCCCCC;"/>
	</div>

	<input type="hidden" name="x1" value="<?=(($_POST['x1']>0) ? $_POST['x1'] : $def_x1)?>" />
	<input type="hidden" name="y1" value="<?=(($_POST['y1']>0) ? $_POST['y1'] : $def_y1)?>" />
	<input type="hidden" name="x2" value="<?=(($_POST['x2']>0) ? $_POST['x2'] : $def_x2)?>" />
	<input type="hidden" name="y2" value="<?=(($_POST['y2']>0) ? $_POST['y2'] : $def_y2)?>" />
	<input type="hidden" name="editor_minw" value="<?=$settings['editor_minw']?>" />
	<input type="hidden" name="editor_minh" value="<?=$settings['editor_minh']?>" />
	<input type="hidden" name="editor_as_min" value="<?=$settings['editor_as_min']?>" />
	<input type="hidden" name="edit_image" value="1" />
	<input type="hidden" name="path" value="<?=$_GET['file']?>" />
	<input type="hidden" name="type_edit" value="<?=$_GET['type']?>" />
</form>
<?} ?>

</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>
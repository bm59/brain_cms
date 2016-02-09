<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/auth.php");?>
<?

if (!isset($_POST['edit_image']))
{
	if ($_GET['file']=='') die('No image');
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$_GET['file'])) die('File not exist');

}

$datatype=msr(msq("SELECT * FROM `site_site_data_types` WHERE `section_id`=".$_GET['section']." and  `name`='".$_GET['rubric']."'"));

if ($_GET['str_settings']!='')
$datatype['settings']=$_GET['str_settings'];

$settings=$VirtualPattern->explode($datatype['settings']);

if (isset($_POST['edit_image']))
{
	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$_POST['path']);
	
	
	$image['path']=$_POST['path'];
	$image['x1']=$_POST['x1'];
	$image['y1']=$_POST['y1'];
	$image['x2']=$_POST['x2'];
	$image['y2']=$_POST['y2'];
	
	if ($image['x2']<$size[0]-1 || $image['y2']<$size[1]-1)
	{
		if ($image['x1']>0 || $image['x2']>0 || $image['y2']>0)
		{

			$mini_image=crop_editor($_SERVER['DOCUMENT_ROOT'].$image['path'], $image['x1'], $image['y1'], $image['x2']-$image['x1'], $image['y2']-$image['y1'], $settings);
			/* print 'режем';	 */
		
		}


	}
}





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

$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$_GET['file']);

$def_x1=$calc_w>0 ? '10': '10';
$def_y1=$calc_h>0 ? '10': '10';

$def_x2=$calc_w>0 ? $calc_w+10: (($size[0]-10<=150) ? $size[0]-10 : 150);
$def_y2=$calc_h>0 ? $calc_h+10: (($size[1]-10<=150) ? $size[1]-10 : 150);


if ($_POST['edit_image']!='')
{

	?>
	<script type="text/javascript">
	$(document).ready(function () {
		$.cookie('change_photo_<?=$_GET['section']?>_<?=$_GET['rubric'] ?><?=(($_GET['image_id']>0) ? '_'.$_GET['image_id']:'') ?>', '1', {path: '/'});
		window.close();
	});
	</script>
	<?
}
?>

<script src="/js/inputs/jquery.imgareaselect.js" language="JavaScript" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/css/inputs/imgareaselect-default.css" media="all" />

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
					            		onSelectChange: preview, 

<?if ($calc_w>0 && (!$settings['editor_min_more'] || $settings['editor_minw']!='')){?>						minWidth: '<?=$calc_w?>',<?} ?>
<?if ($calc_h>0 && (!$settings['editor_min_more'] || $settings['editor_minh']!='')){?>						minHeight: '<?=$calc_h?>',<?} ?>

            onSelectEnd: function (img, selection) {
                $('input[name="x1"]').val(selection.x1);
                $('input[name="y1"]').val(selection.y1);
                $('input[name="x2"]').val(selection.x2);
                $('input[name="y2"]').val(selection.y2);
            }
    });

		preview('', ias.getSelection()); 

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

		function preview (img, selection)
		{

			$('#x1').html('x<sub>1</sub>: '+selection.x1);
			$('#x2').html('x<sub>2</sub>: '+selection.x2);
			$('#y1').html('y<sub>1</sub>: '+selection.y1);
			$('#y2').html('y<sub>2</sub>: '+selection.y2);
		    $('#w').html('Ширина: '+selection.width);
		    $('#h').html('Высота: '+selection.height); 	
		}
		
		$('#change_ratio').click(function () {
			ias.setOptions({ aspectRatio: $('[name=ratio_val]').val()});
			ias.update();
			return false;


		});

		$('#change_point').click(function () {

			var val=$('[name=point_val]').val();
			val=val.split(',');
			
			ias.setSelection(val[0], val[1], val[2], val[3], true);
			preview('', ias.getSelection());
			ias.update();

			selection=ias.getSelection();

            $('input[name="x1"]').val(selection.x1);
            $('input[name="y1"]').val(selection.y1);
            $('input[name="x2"]').val(selection.x2);
            $('input[name="y2"]').val(selection.y2);

			
			return false;


		});

		$('#center_gorizontal, #center_vertical').click(function () {

			var img_w=<?=floor($size[0]) ?>;
			var img_h=<?=floor($size[1]) ?>;

			selection=ias.getSelection();

			if ($(this).attr('id')=='center_gorizontal')
			{

				var select_width=selection.x2-selection.x1;

				new_x1=Math.round((img_w-select_width)/2);
				new_x2=new_x1+select_width;
				ias.setSelection(new_x1, selection.y1, new_x2, selection.y2, true);
			}

			if ($(this).attr('id')=='center_vertical')
			{

				var select_height=selection.y2-selection.y1;

				new_y1=Math.round((img_h-select_height)/2);
				new_y2=new_y1+select_height;
				ias.setSelection(selection.x1, new_y1, selection.x2, new_y2, true);
			}

			
			ias.update();

		});



});

	function check_size()
	{

		var error=false;
		<? if ($calc_w>0 && !$settings['editor_min_more']) {?>
		if (($('input[name="x2"]').val()-$('input[name="x1"]').val())<<?=$calc_w?>)
		{
			alert('Ширина выделеной области '+($('input[name="x2"]').val()-$('input[name="x1"]').val())+' меньше допустимой <?=$calc_w?>');
			error=true;
		}
		<?} ?>
		<? if ($calc_h>0 && !$settings['editor_min_more']) {?>
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

		$image = getimagesize ($_SERVER['DOCUMENT_ROOT'].$_GET['file']);
		if (($calc_w>0 && $calc_h>0) && $image[0]==$calc_w && $image[1]==$calc_h) $error.='<h2 style="color: #FF0000">Изображение соответствует размеру</h2>';

		if (($calc_w>0 && $calc_h>0) && $image[0]<$calc_w || $image[1]<$calc_h) $error.='<h2 style="color: #FF0000">Изображение меньше минимальных размеров</h2>';


		if ($error!='')
		{
			print $error;
			print '<br/>ширина: '.$image[0].'; высота: '.$image[1];
		}

		?>

<form id="save_form" name="save_form"  action="" enctype="multipart/form-data" method="POST">
	<div style="padding: 10px  0 10px 0px; margin-left: -5px">
		<input type="submit" value="Сохранить изменения" onclick="check_size(); return false;" name="editform" class="button big">
	</div>

	<!-- <button id="rectangle" type="button">Rectangle</button> -->
	<div class="editor_container">
		<img id="photo" src="<?=$_GET['file']?>?<?=time()?>" style="border: 1px solid #CCCCCC; display: block; float: left"/>
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



	<div class="clear"></div>
	<br>
		<div class="prew" style="float: left; background: #eee none repeat scroll 0 0; border: 2px solid #ddd; padding: 0.6em;">
			<div id="w">Ширина: </div>
			<div id="h">Высота: </div>
			<div id="x1">x<sub>1</sub>: </div>
			<div id="x2">x<sub>2</sub>: </div>
			<div id="y1">y<sub>1</sub>: </div>
			<div id="y2">y<sub>2</sub>: </div>
		</div>
		
		<div style="float: left; margin-left: 15px; background: #eee none repeat scroll 0 0; border: 2px solid #ddd; padding: 0.6em;">
		<?if (count($settings)>0) {?>
		<div>Базовые настройки:</div>
		<?
			foreach ($settings as $k=>$v)
			print $k.'='.$v.'<br/>';
		}
		print '<a href="'.$_GET['file'].'" target="_blank">'.$_GET['file'].'</a>';
		?>
		</div>

	<div class="clear"></div>		
		
		
		
<span style="width: 400px; float: left;" class="input">
	<input type="text" placeholder="Пророрция: 1:1" name="ratio_val" maxlength="255" value="<?=$settings['editor_proport']>0 ? $settings['editor_proport'] : '1:1'?>">
</span>
<a style="display: block; float:left; margin: 13px 0 0 10px;" class="button" href="#" id="change_ratio">Установить ratio</a>
<div class="clear"></div>
<span style="width: 400px; float: left;" class="input">
	<input type="text" placeholder="Точки: 0,0,150,150" name="point_val" maxlength="255" value="0,0,<?=$size[0]-1?>,<?=$size[1]-1?> ">
</span>
<a style="display: block; float:left; margin: 13px 0 0 10px;" class="button" href="#" id="change_point" onclick="return false">Установить точки</a>
<div class="clear"></div>
<a style="display: block; float:left;" class="button" href="#" id="center_gorizontal" onclick="return false">Центрировать по горизонтали</a><a style="display: block; float:left; padding-left: 20px;" class="button" href="#" id="center_vertical" onclick="return false">Центрировать по вертикали</a>
<div class="clear"></div>

</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php");?>
<?include_once("ImportClass.php");?>

<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
<? 
$exts='xls, xlsx';
?>
<style type="text/css">
#add_file {padding-top: 10px;}
#add_file img {display: inline-block; padding-left: 18px;}
#upl_error {color: red; padding-top: 10px;}
#loading {padding-top: 10px;}
.step {border: 2px solid #CCCCCC; padding: 10px 20px; margin: 10px 0;}
table {width: 100%}
table td {width: 48%; padding-right: 10px;}
table td.onoff_container {width: 4%; }
</style>
<script>
function get_sql()
{
	var error=0;
	var ins_fields='';
	
	$(".header_result table tr .input input").css('border','2px solid rgba(220, 220, 220, 1)');
	$(".header_result table tr").has(".on_off[title='Включено']").each(function()
	{
		if ($(this).find('.mysql_name input').val()=='')
		{
			$(this).find('.mysql_name input').css('border','2px solid #CC0000');
			error=1;
		}
		else ins_fields+=(ins_fields!='' ? ',' : '')+'`'+$(this).find('.mysql_name input').val()+'`';
	});

	if (ins_fields=='') {error=1; alert('Не найдены имена полей MySQL');}

	

	if (error==0)
	{
		var result_sql='';
		var vals='';

		
		$(".insert_values div").each(function(){
			vals=$(this).html();
			vals=vals.split('|');

			var cur_values='';
			var unique_sql=';';
			

			for(var i=0;i<vals.length;i++)
			{
				if ($(".header_result table").find('.mysql_name input').eq(i).prop('disabled')==false)
				cur_values+=(cur_values=='' ? '' : ',')+'\''+vals[i]+'\'';	
			}

				var j=0;
				var unique_usl='';
				$('.unique_container input').each(function()
				{
					if ($(this).prop('title')=='Включено' && vals[j]!='')
					unique_usl+=(unique_usl!='' ? ' or ':'')+'`'+$(this).parents('tr').find('.mysql_name input').val()+'`=\''+vals[j]+'\'';

					j++;
							
				});
				if (unique_usl!='')
				unique_sql=' WHERE NOT EXISTS (SELECT `id` FROM `'+$('[name=table_name]').val()+'` WHERE '+unique_usl+')';


			result_sql+='INSERT INTO `'+$('[name=table_name]').val()+'` ('+ins_fields+$('[name=const_fields]').val()+') SELECT '+cur_values+$('[name=const_values]').val()+' FROM DUAL'+unique_sql+';';
			
		});
		
		//alert(result_sql);
		$('.result_sql textarea').val(result_sql);
	}
}
$(function(){

	 /* Включение\отключение колонок */
    $(document).on('click',".on_off", function() {
    	$(".header_result table tr .input input").css('border','2px solid rgba(220, 220, 220, 1)');

		if ($(this).css('backgroundImage')=='url("http://mailer/pics/editor/off.png")')
		{
			$(this).css('backgroundImage', 'url("/pics/editor/on.png")');
			$(this).parents('tr').find('.input input').css("background-color", "#FFFFFF");
			$(this).prop('title','Включено');
			$(this).parents('tr').find('.input input').prop("disabled",false);
		}
		else
		{
			$(this).css('backgroundImage', 'url("/pics/editor/off.png")');
			$(this).parents('tr').find('.input input').css("background-color", "#CCCCCC");
			$(this).prop('title','Отключено');
			$(this).parents('tr').find('.input input').prop("disabled",true);
		}
		


		
        return false;

    });

    $(document).on('click',".unique", function() {

		if ($(this).prop('title')=='Отключено')
		{
			$(this).css('backgroundImage', 'url("/pics/editor/on.png")');
			$(this).prop('title','Включено');
		}
		else
		{
			$(this).css('backgroundImage', 'url("/pics/editor/off.png")');
			$(this).prop('title','Отключено');
		}
		


		
        return false;

    });

    
					        var btnUpload=$('#upl_button');
					        var status=$('#upl_status');
					        var error=$('#upl_error');
					        var old_file='';
					        var upload_me=new AjaxUpload(btnUpload, {
					            action: '/manage/excel_import/ajax.php', 
					            responseType: 'html',
					            name: 'upl_file',
					            data: {sid : '<?=session_id()?>', first_header: $('[name=first_header]').val()},
					            onSubmit: function(file, ext){
					            	error.html('');
					            	status.html('');
					            	$('.result_sql textarea').val('');
					            	$('.header_result').html('');
					            	 
						            if ($('[name=table_name]').val()=='')
						            {
						            	error.html('<nobr>Введите имя таблицы</nobr>');
						            	return false;	
						            }
					                <?if ($exts!=''){?>
					                if (! (ext && /^(<?=strtolower(str_replace(', ', '|', $exts))?>)$/.test(ext))){
					                    error.html('<nobr>Допустимые расширения: <?=strtolower($exts)?></nobr>');
					                    return false;
					                }
					                <?}?>
					                $('#file').fadeOut(0);
					                $('#loading').fadeIn(0);
					            },
					            onComplete: function(file, response){
					                status.html(response);
					                $('.header_result').html($('.header_html_ajax').html());
					                $('#step_3').show();
					                $('#loading').fadeOut(0);

					                $('.header_html_ajax').html('')

					                $('.onoff_container').html('<span class="button txtstyle"><input type="button" style="background-image: url(/pics/editor/on.png)" onclick="return false" class="on_off" title="Включено"></span>');
					                $('.unique_container').html('<span class="button txtstyle"><input type="button" style="background-image: url(/pics/editor/off.png)" onclick="return false" class="unique" title="Отключено"></span>');
					                $('.header_result').append('<div style="float: right;"><input type="submit" value="Сформировать SQL запрос" class="button big" onclick="get_sql(); return false;"></div><div class="clear"></div>');
					            }
					        });
});
						</script>							
<div id="content" class="forms">
<div class="hr"></div>
<h1><a href="/manage/">Панель управления</a>  &rarr; Импорт xls</h1>

<div class="step">
	<h2>1. Настройки импорта</h2>
		<div>
			<label>Таблица для импорта</label>
			<span class="input">
				<input name="table_name" value="<?=($_GET['table']!='' ? $_GET['table'] : 'test_table')?>" />
			</span>
		</div>
		
		<div>
			<div class="styled">
				<input type="checkbox" name="first_header" id="checkbox" class="checkbox" checked="checked">
				<label for="checkbox">Первая строка - заголовки</label>
			</div>
		</div>
	<div class="clear"></div>					
</div>

<div class="step">
	<h2>2. Выберите файл xls</h2>
						
						<div id="add_file" style="float: left;">
						        <a id="upl_button" class="button">Загрузить файл</a>
						        <div class="clear"></div>
						        <img id="loading" src="/pics/loader.gif" style="display: none;" />
						        <div id="upl_error"></div>
						        <div id="upl_status"></div>
						 </div>
						 <div class="clear"></div>
</div>
<div class="step" style="display: none;" id="step_3">
	<h2>3. Проверьте настройки полей и нажмите сформировать запрос</h2>
	<div class="header_result"></div>
	<div>
	<table>
		<tr>
			<td><div class="input"><input value="" name="const_fields" placeholder="константы: ,`base_id`"></div></td>
			<td><div class="input"><input value="" name="const_values" placeholder="константы: ,'1'"></div></td>
		</tr>
	</table>
	</div>	
	<div class="result_sql">
		<textarea rows="20" style="width: 100%;"></textarea>
	</div>
					
</div>
<? 
$ImportClass=new ImportClass();
$ImportClass->init();

?>
</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>

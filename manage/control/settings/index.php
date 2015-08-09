<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";
/*print_r($_SERVER);*/
$iface = new SiteSettings;
$iface->init();
$adderrors = $updateerrors = $data = array();
if (isset($_POST['setadd'])){
	foreach ($_POST as $k=>$v) $data[$k] = trim($v);
	$adderrors = $iface->add($data['name'],$data['description'],$data['value'],array('type'=>$data['type']));
	if (count($adderrors)==0)
	{		$data = array();
		WriteLog(0, 'добавление настройки', $_POST['name'].'|'.$_POST['description']);
	}
}
if (isset($_GET['setdel'])){
	 $iface->delete($_GET['setdel']);
	 WriteLog($_GET['setdel'], 'удаление настройки');
}

$list = $iface->getList();
if (isset($_POST['setupdate'])){	$i=0;
	foreach ($_POST as $k=>$v){

		$set = $iface->getOne($iface->getIdByName(str_replace('_value','',$k)));
		if ($set['id']>0)
		{
			$i++;
			if (isset($_POST[$set['name'].'_value'])) $err = $iface->update($set['id'],$_POST[$set['name'].'_value'],$i);
			if ($err!='') $updateerrors[] = $err;
			if ($err=='' && $set['value']!=$_POST[$set['name'].'_value']) WriteLog($set['id'], 'редактирование настройки', $set['name'].'|'.$set['value'].'|'.$_POST[$set['name'].'_value']);
	    }
	}
}
$list = $iface->getList();
?>

<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";;?>
	<div id="content" class="forms">
		<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
		<?
		if (count($updateerrors)>0){
			print '
			<p><strong>Не все настройки обновлены:</strong></p>
			<ul class="errors">';
				foreach ($updateerrors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}
		if (count($list)>0){
		?>
  <script>
  $(function() {
    $( "#sortable" ).sortable({
      handle: ".drag_icon"
    });
    //$( "#sortable" ).disableSelection();
  });
function selectText(elem) {
  var range = document.createRange();
  range.selectNode(elem);
  window.getSelection().addRange(range);
};
  </script>
		<form name="setupdate" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
		<UL id="sortable">
			<?
			foreach ($list as $setid){
				$set = $iface->getOne($setid);
				?>
				<LI>
				<div class="place" id="item_<?=$set['id']?>">
					<table style="width: 100%;">
					<tr>
					<td style="width: 20px; padding-top: 20px;"><div class="drag_icon"><img src="/pics/editor/up_down.png"></div></td>
					<td>
					<label><?=stripslashes(htmlspecialchars($set['description']))?> <small class="setdesc" onclick="selectText(this)"><?=stripslashes(htmlspecialchars($set['name']))?></small></label>
					<?
					if ($set['settings']['type']=='int')
					{
                        $values = array('0'=>'Нет', '1'=>'Да');
                        print getSelectSinonim($set['name'].'_value',$values,$set['value']);
					}
                    elseif ($set['settings']['type']=='text')
                    {
                    ?>
						<div class="ta_big"><textarea style="width: 100%; padding: 10px;" name="<?=htmlspecialchars($set['name'])?>_value"><?=stripslashes(htmlspecialchars($set['value']))?></textarea></div>
                    <?
                    }
                     elseif ($set['settings']['type']=='image')
                    {
                    ?>
        <script>
        $(function(){
        var btnUpload<?='_'.$set['name']?>=$('#upl_button<?='_'.$set['name']?>');
        var status=$('.contentdesc<?='_'.$set['name']?>');
        var upload_me=new AjaxUpload(btnUpload<?='_'.$set['name']?>, {
            action: '/uploader_image.php',
            responseType: 'json',
            name: 'upl_file',
            data: {},
            onSubmit: function(file, ext){
                this.setData({sid : '<?=session_id()?>', theme: '0', rubric: '0', stid: '2', uid: <?=floor($iface->getSetting('uid'))?>});
            },
            onComplete: function(file, response){
                status.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#delete_container<?='_'.$set['name']?>').fadeIn(0);
                    status.html('<a  href="'+response.path+'" target="_blank">ссылка на изображение</a>');
                    $('#uploadfilehidden<?=htmlspecialchars($set['name'])?>').val(response.id);
                }else{
                    $('#loading').fadeOut(0);
                    if (response.error!='') alert(response.error);


                }
            }
        });


    });
    	function delete_file<?='_'.$set['name']?> ()
        {
       		if (!confirm('Вы уверены, что хотите удалить этот файл?')) return false;

			    $.ajax({
			        url: "/uploader_image.php",
			        type: "post",
			        data: {action: 'delete',old_id : $('#uploadfilehidden<?=htmlspecialchars($set['name'])?>').val()},
			        success: function(result){
			            var arr_resp = result.split("#%#");
			            if(arr_resp[0]==="true")
			            {
                         	 $('#delete_container<?='_'.$set['name']?>').fadeOut(0);
                         	 $('.contentdesc<?='_'.$set['name']?>').html('');
			            }
			            else
			            {
			            	alert(result);
			            }
			        }
			    });
			    return false;
        }


			</script>
                    <div class="settings_image">
					<div class="place forimage">
					<span class="button" id="upl_button<?='_'.$set['name']?>" style="display:block; float: left;">
						Загрузить изображение
					</span>
					<?
					$image='';
					if ($set['value']>0)
					{						$image=$Storage->getFile($set['value']);
					}
					?>

                    <input type="hidden" id="uploadfilehidden<?=$set['name']?>" name="<?=$set['name']?>_value" value="<?=stripslashes(htmlspecialchars($set['value']))?>">

					<span style="display:<?=$image['path']!='' ? 'block':'none'?>; float: left;padding: 12px 0 0 0 " id="delete_container<?='_'.$set['name']?>" class="button txtstyle">
						<input type="button" onclick="delete_file<?='_'.$set['name']?>(); return false" id="delete_button_image" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение">
					</span>
					<div class="contentdesc<?='_'.$set['name']?>" style="float: left; padding: 10px 0 0 20px;"><?=$image['path']!='' ? '<a  href="'.$image['path'].'" target="_blank">ссылка на изображение</a>':''?></div>
					</div>
					<div class="clear"></div>
					</div>
                    <?
                    }
					else
					{
					?>
					<span class="input">
						<input name="<?=htmlspecialchars($set['name'])?>_value" value="<?=stripslashes(htmlspecialchars($set['value']))?>"/>
					</span>
					<?
					}
					?>
					</td><td style="width: 32px;">
					<label>&nbsp;</label>
					<?
					if ((isset($set['settings']['undeletable']) || !in_array('delete',$group['new_settings'][$activeccid])) && $mode!='development'){
						?>
						<span class="button txtstyle disabled">
							<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" onclick="return false;" />
						</span>
						<?
					}
					else{
						?>
						<a class="button txtstyle" href="#" onclick="if (confirm('Вы уверены, что хотите удалить эту настройку?')) Gotopage('<?=$_SERVER['PHP_SELF'].'?setdel='.$set['id']?>'); return false;" >
							<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить настройку" />
						</a>
						<?
					}
					?>
					</td></tr></table>
				</div>
				<span class="clear"></span>
				</LI>
				<?
			}

			if (in_array('edit',$group['new_settings'][$activeccid]) || $mode=='development')
			{
			?>
			<div class="place">
				<span style="float: right;">
					<input class="button big" type="submit" name="setupdate" value="Сохранить" />
				</span>
			</div>
			<?}?>
		</UL>
		</form>
		<?
		}
		?>
		<span class="clear"></span>
		<?
		if (count($adderrors)>0){
			print '
			<p><strong>Добавление настройки не выполнено по следующим причинам:</strong></p>
			<ul class="errors">';
				foreach ($adderrors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}

		if (in_array('add',$group['new_settings'][$activeccid]) || $mode=='development')
		{
		?>
		<div class="hr"></div>
		<form name="setadd" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
		<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
			<div class="place">
				<table style="width: 100%; table-layout: fixed;"><tr><td>
				<label>Описание</label>
				<span class="input">
					<input name="description" maxlength="250" value="<?=htmlspecialchars($data['description'])?>" />
				</span>
				</td></tr></table>
			</div>
			<span class="clear"></span>
			<div class="place">
				<table style="width: 100%; table-layout: fixed;"><tr><td>
				<tr><td>
				<label>Название</label>
				<span class="input">
					<input name="name" maxlength="20" value="<?=htmlspecialchars($data['name'])?>" />
				</span>
				</td><td>
				<label>Значение</label>
				<span class="input">
					<input name="value" value="<?=stripslashes(htmlspecialchars($data['value']))?>" />
				</span>
				</td><td>
				<label>Тип</label>
				<?
				$values = array();
				foreach ($iface->getSetting('types') as $k=>$v) $values[$k] = $v;
				print getSelectSinonim('type',$values,$data['type']);
				?>
				</td></tr></table>
			</div>
			<span class="clear"></span>
			<div class="place">
				<span style="float: right;">
					<input class="button big" type="submit" name="setadd" value="Добавить" />
				</span>
			</div>
		</form>
		<?}?>
		<span class="clear"></span>
	</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>
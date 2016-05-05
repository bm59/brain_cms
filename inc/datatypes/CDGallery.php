<?
class CDGallery extends VirtualType
{
	function init($settings){
		$settings['descr']='Картинки (галерея)';
		$settings['help']=array(
				'auto_resize=true|auto_resize_if_more=true|auto_width=187|auto_height=120|'=>'Автоматическая обрезка изображений',
				'imgw=245|imgwtype=1|imgh=400|imghtype=1'=>'Проверка на размер изображений',
				'comment=комментарий'=>'Комментарий',
				'auto_mini=true|auto_mini_width=210|auto_mini_height=220'=>'Автоматическое создание миниатюр',
				'exts=jpg,gif,jpeg,png'=>'Расширения',
				'editor_imgh=200|editor_imgw=150'=>'Мин. размер в редакторе',
				'editor_minh=200|editor_minw=100'=>'Минимизировать к размеру',
				'editor_min_more=1'=>'Минимизирует если область выделенная область больше',
				'editor_as_min=1'=>'Сохранять изображение из редактора как миниатюру',

		);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');

		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		if (!floor($st['id'])>0)
		$st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Картинки','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));



		$f = 0;
		if (floor($st['id'])>0){

			?>
   			<script>
        $(function(){
        var btnUpload<?='_'.$this->getSetting('name')?>=$('#upl_button<?='_'.$this->getSetting('name')?>');
        var status=$('.sortable<?='_'.$this->getSetting('name')?>');
        var upload_me=new AjaxUpload(btnUpload<?='_'.$this->getSetting('name')?>, {
            action: '/uploader_image.php',
            responseType: 'json',
            name: 'upl_file',
            data: {},
            onSubmit: function(file, ext){
            	$('#loading').show();
                this.setData({sid : '<?=session_id()?>', theme: '<?=trim($this->getSetting('theme'))?>', rubric: '<?=trim($this->getSetting('rubric'))?>', stid: '<?=$st['id']?>', uid: <?=floor($settings['uid'])?>});
            },
            onComplete: function(file, response){
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading<?='_'.$this->getSetting('name')?>').fadeOut(0);
                    var editor_link='<span class="button txtstyle"><a onclick="wait_editor_gallery('+response.id+');" href="/inc/datatypes/photo_editor/?file='+response.path+'&rubric=<?=htmlspecialchars($this->getSetting('name'))?>&section=<?=$_GET['section']?>&image_id='+response.id+'" title="Редактор фото" target="_blank" class="editor_gallery" id="editor_image"><img style="margin-top: -4px;" src="/pics/editor/photo_editor.png" alt="Редактор фото"></a></span>';
                    status.html(status.html()+'<LI id="'+response.id+'" style="height: 170px;float: left;"><div class="gallery_container"><input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>[]" value="'+response.id+'"><span class="button txtstyle"><input type="button" onclick="delete_file_image<?=htmlspecialchars($this->getSetting('name'))?>(this); return false" id="delete_button_image" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение"></span><span class="button txtstyle"><a id="link_image" class="link<?='_'.$this->getSetting('name')?>" target="_blank" title="Ссылка на картинку" href="'+response.path+'"><img alt="Ссылка на картинку" src="/pics/editor/link.png"></a></span>'+editor_link+'<img style="height: 170px" class="contentimg" src="'+response.path+'" class="contentimg"></div></LI>');
                    //$('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val(response.id);
                    $('#loading').hide();

             }else{
                    $('#loading').hide();
                    if (response.error!='') alert(response.error);


                }
            }
        });


    });
    	function delete_file_image<?=htmlspecialchars($this->getSetting('name'))?> (elem)
        {
       		if (!confirm('Вы уверены, что хотите удалить этот файл?')) return false;

			$(elem).parents('LI').remove();
			$( "#sortable" ).sortable();

			return false;
        }

    	function wait_editor_gallery(image_id)
    	{
    		/*alert('start_wait'+'change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>_'+image_id);*/
    		var interval = setInterval(function()
    		{


    			if ($.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>_'+image_id)=='1' && $.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>_'+image_id)!='null')
    			{

    				/*alert('Есть изменения');*/
    	            $.cookie('change_photo<?='_'.$_GET['section']?><?='_'.$this->getSetting('name')?>_'+image_id, null, {path: '/'});

    	            var src=$('#'+image_id+' .contentimg').attr("src").split("?")[0] + "?" + Math.random();
    	            $('#'+image_id+' .contentimg').attr('src','');
    	            $('#'+image_id+' .contentimg').attr('src',src);


    	            clearInterval(interval);
    			}



    		}, 1000);


    	}


			</script>
			  <script>
  $(function() {
    $( "#sortable" ).sortable();
    //$( "#sortable" ).disableSelection();
  });
  </script>

			<div class="place gallery" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>
			<?
			/*if ($this->getSetting('name')=='image' && $_GET['section']==7) 	print '<div style="padding: 0 5px; color: #ff0000">Рекомендуемый размер:  1040x450 пикселей</div>';*/
			?>
                <div class="place forimage ">
				<span id="upl_button<?='_'.$this->getSetting('name')?>" class="button">
					Загрузить изображение
				</span>
				<img src="/pics/inputs/loading2.gif" id="loading" style="position: absolute; top: 0; left: 170px; display: none;">
				</div>
               <span class="clear"></span>
				<div id="upl_error<?='_'.$this->getSetting('name')?>"></div>
			    <div id="upl_status<?='_'.$this->getSetting('name')?>"></div>

				<span class="clear"></span>
				<?
					$desc = '';
					$exts = upper(str_replace(',',', ',$settings['exts']));
					if ($exts!='') $desc.= ' формата '.$exts;
					$wh = '';
					if (floor($settings['imgw'])>0){
						$imgw = floor($settings['imgw']);
						if (floor($settings['imgwtype'])==1) $wh.= 'ширина должна быть равна '.$imgw.'px';
						if (floor($settings['imgwtype'])==2) $wh.= 'ширина должна быть меньше или равна '.$imgw.'px';
						if (floor($settings['imgwtype'])==3) $wh.= 'ширина должна быть больше или равна '.$imgw.'px';
					}
					if (floor($settings['imgh'])>0){
						$imgh = floor($settings['imgh']);
						if (floor($settings['imghtype'])==1) $wh.= (($wh=='')?'':', а ').'высота должна быть равна '.$imgh.'px';
						if (floor($settings['imghtype'])==2) $wh.= (($wh=='')?'':', а ').'высота должна быть меньше или равна '.$imgh.'px';
						if (floor($settings['imghtype'])==3) $wh.= (($wh=='')?'':', а ').'высота должна быть больше или равна '.$imgh.'px';
					}
					if ($wh!='') $wh = ' '.$wh.'.';
					$desc.='.'.$wh;
				?>
				<?
				/*|auto_resize=true|auto_width=187|auto_height=120|*/
				if ($settings['auto_resize'] && ($settings['auto_width']>0 || $settings['auto_height']>0))
				{
					$img_descr='<small>Рекомендуемый размер изображения:';
					$img_descr.=(($settings['auto_width']>0) ? ' ширина '.$settings['auto_width'].'пикселей;' : '');
					$img_descr.=(($settings['auto_height']>0) ? ' высота '.$settings['auto_height'].'пикселей;' : '');
					print $img_descr.' Если размеры загружаемого изображения больше - его размеры будут автоматически изменены</small>';
				}
				?>

				<div><small><?=$desc?></small></div>
				<div id="<?=htmlspecialchars($this->getSetting('name'))?>imagecontent">
				<div class="contentdesc<?='_'.$this->getSetting('name')?>">
				<UL id="sortable" class="sortable<?='_'.$this->getSetting('name')?>">
				<?
				$images=$this->getSetting('value');
				$images=explode('|',$images);
				foreach ($images as $img)
				if ($img>0)
				{                 	$image = $Storage->getFile($img);
					$f = floor($image['id']);
					?>
					<LI style="height: 170px;float: left;" id="<?=$image['id'] ?>">
					<div class="gallery_container">
					<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>[]" value="<?=$f?>"/>
					<span class="button txtstyle"><input type="button" onclick="delete_file_image<?=htmlspecialchars($this->getSetting('name'))?>(this); return false" id="delete_button_image" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение"/></span>
					<span class="button txtstyle"><a id="link_image" class="link<?='_'.$this->getSetting('name')?>" target="_blank" title="Ссылка на картинку" href="<?=$image['path'] ?>"><img alt="Ссылка на картинку" src="/pics/editor/link.png"></a></span>
					<span class="button txtstyle"><a id="editor_image" class="editor<?='_'.$this->getSetting('name')?>" target="_blank" title="Редактор фото" href="/inc/datatypes/photo_editor/?file=<?=$image['path'] ?>&rubric=<?=$this->getSetting('rubric') ?>&section=<?=$_GET['section'] ?>&image_id=<?=$image['id']?>" onclick="wait_editor_gallery(<?=$image['id']?>);"><img alt="Редактор фото" src="/pics/editor/photo_editor.png" style="margin-top: -4px;"></a></span>
					<img style="height: 170px" class="contentimg" src="<?=$image['path']?>" class="contentimg"/>
					</div>
					</LI>
					<?
				}
				?>
				</UL>
				</div>
				</div>
			</div>
		<?
				if ($span) print '
			<span class="clear"></span>';
		}
	}
	function preSave(){
		$newvalue='';
		if (count($_POST[$this->getSetting('name')])>0)
		{			foreach ($_POST[$this->getSetting('name')] as $img)
			$newvalue.=($newvalue!='' ? '|':'').$img;
		}
		$errors = array();
		$settings = $this->getSetting('settings');
		if ((isset($settings['important'])) && ($newvalue<1)) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){
		$settings = $this->getSetting('settings');
		global $Storage;

		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
			if (!$st['id']>0) $st = $Storage->getStorage(0,array('path'=>'/storage/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
			if (floor($st['id'])>0){

				$files=explode('|',$this->getSetting('value'));
				foreach ($files as $fi)
				{
					$f = $Storage->getFile($fi);
					if (floor($f['id'])>0){
						if (substr($f['name'],0,5)=='temp_'){
							if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
								$nf = $Storage->getFile($f['id']);

							}
						}

						if ($settings['auto_mini'] && ($settings['auto_mini_width']>0 || $settings['auto_mini_height']>0))
						{

							$nf = $Storage->getFile($f['id']);
							$mini_fname=str_replace('.'.$nf['ext'], '_mini.'.$nf['ext'], $nf['fullpath']);
							copy($nf['fullpath'], $mini_fname);
							crop($mini_fname, $settings['auto_mini_width'], $settings['auto_mini_height']);
							/*Crop($mini_fname, 210, 220);*/
						}
					}
					$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
					foreach ($flist as $f){
						if (!$f['id']==$this->getSetting('value')) $Storage->deleteFile($f['id']);
					}
				}
			}
		}
	}
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".$this->getSetting('value')."'"; }
	function delete(){
		global $Storage;
		$images=explode('|',$this->getSetting('value'));
		foreach ($images as $img)
		$Storage->deleteFile($img);
	}
}
?>
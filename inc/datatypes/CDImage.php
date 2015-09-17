<?
/*
Класс, описывающий тип «Изображение»
*/
class CDImage extends VirtualType
{
	function init($settings){
		$settings['descr']='Картинка';
		$settings['help']=array(
				'auto_resize=true|auto_width=187|auto_height=120'=>'Автоматическая обрезка изображений',
				'imgw=245|imgwtype=1|imgh=400|imghtype=1'=>'Проверка на размер изображений',
				'comment=комментарий'=>'Комментарий',
				'auto_mini=true|auto_mini_width=210|auto_mini_height=220'=>'Автоматическое создание миниатюр', 
				'exts=jpg,gif,jpeg,png'=>'Расширения',
				'editor_proport=auto'=>'Пропорция 3:4 или auto',
				'editor_imgh=200|editor_imgw=150'=>'Мин. размер в редакторе',
				'editor_minh=200|editor_minw=100'=>'Минимизировать к размеру',
				'editor_as_min=1'=>'Сохранять изображение из редактора как миниатюру',
		
		);
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		global $Storage;
		
		$settings = $this->getSetting('settings');
		
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		
		if (!floor($st['id'])>0)
		$st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Картинки','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
		
		$f = 0;
		if (floor($st['id'])>0){
			$image = $Storage->getFile($this->getSetting('value'));
			$f = floor($image['id']);
			$this->setSetting('value',$f);
			?>
   			<script>
        $(function(){
        var btnUpload<?='_'.$this->getSetting('name')?>=$('#upl_button<?='_'.$this->getSetting('name')?>');
        var status=$('.contentdesc<?='_'.$this->getSetting('name')?>');
        var upload_me=new AjaxUpload(btnUpload<?='_'.$this->getSetting('name')?>, {
            action: '/uploader_image.php',
            responseType: 'json',
            name: 'upl_file',
            data: {},
            onSubmit: function(file, ext){
                $('#loading').show();
                this.setData({sid : '<?=session_id()?>', theme: '<?=trim($this->getSetting('theme'))?>', rubric: '<?=trim($this->getSetting('rubric'))?>', stid: '<?=$st['id']?>', uid: <?=floor($this->getSetting('uid'))?>,old_id : $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val()});
            },
            onComplete: function(file, response){
                status.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading<?='_'.$this->getSetting('name')?>').fadeOut(0);
                    $('#delete_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    $('#editor_container<?='_'.$this->getSetting('name')?>').fadeIn(0);
                    $('#link_container<?='_'.$this->getSetting('name')?>').fadeIn(0);

                    $('.editor<?='_'.$this->getSetting('name')?>').attr('href', '/inc/datatypes/photo_editor/?file='+response.path+'&rubric=<?=$this->getSetting('rubric') ?>&section=<?=$_GET['section']?>');
                    
                    $('.link<?='_'.$this->getSetting('name')?>').attr('href', response.path);
                    status.html('<img style="max-width: 170px" class="contentimg" id="contentimg<?='_'.$this->getSetting('name')?>" src="'+response.path+'">');
                    $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val(response.id);
                    $('#loading').hide();
                }
                else{
                    $('#loading').hide();
                    if (response.error!='') alert(response.error);


                }
            }
        });


    });
    	function delete_file<?='_'.$this->getSetting('name')?> ()
        {
       		if (!confirm('Вы уверены, что хотите удалить этот файл?')) return false;

			    $.ajax({
			        url: "/uploader_image.php",
			        type: "post",
			        data: {action: 'delete',old_id : $('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val()},
			        success: function(result){
			            var arr_resp = result.split("#%#");
			            if(arr_resp[0]==="true")
			            {
                         	 $('#delete_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
                         	 $('#link_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
                         	 $('#editor_container<?='_'.$this->getSetting('name')?>').fadeOut(0);
                         	 $('.contentdesc<?='_'.$this->getSetting('name')?>').html('');
			            }
			            else
			            {
			            	alert(result);
			            }
			        }
			    });
			    return false;
        }
    	function wait_editor()
    	{
			/* alert('start_wait'); */
    		var interval = setInterval(function()
    		{

    			
    			if ($.cookie('change_photo')=='1' && $.cookie('change_photo')!='null')
    			{
    	            
					/* alert('Есть изменения'); */
    	            $.cookie('change_photo', null, {path: '/'});
    	            $('#contentimg<?='_'.$this->getSetting('name')?>').attr('src',$('#contentimg<?='_'.$this->getSetting('name')?>').attr("src").split("?")[0] + "?" + Math.random());
    	            clearInterval(interval);
    			}



    		}, 1000);


    	}

			</script>

			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<?if ($settings['comment']!=''){?><small><?=$settings['comment']?></small><?}?>
			<?
			/*if ($this->getSetting('name')=='image' && $_GET['section']==7) 	print '<div style="padding: 0 5px; color: #ff0000">Рекомендуемый размер:  1040x450 пикселей</div>';*/
			?>
				<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">
                <div class="place forimage">
				<span id="upl_button<?='_'.$this->getSetting('name')?>" class="button">
					Загрузить изображение
				</span>
				<img src="/pics/inputs/loading2.gif" id="loading" style="position: absolute; top: 0; left: 170px; display: none;">

				<span class="button txtstyle" id="delete_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>>
					<input type="button" title="Удалить изображение" style="background-image: url(/pics/editor/delete.gif)" id="delete_button<?='_'.$this->getSetting('name')?>" onclick="delete_file<?='_'.$this->getSetting('name')?>(); return false">
				</span>
				<span class="button txtstyle" id="link_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>><a class="link<?='_'.$this->getSetting('name')?>" target="_blank" title="Ссылка на картинку" href="<?=$image['path'] ?>"><img alt="Ссылка на картинку" src="/pics/editor/link.png" style="margin-top: -4px;"></a></span>
				<span class="button txtstyle" id="editor_container<?='_'.$this->getSetting('name')?>" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>><a class="editor<?='_'.$this->getSetting('name')?>" target="_blank" title="Редактор фото" href="/inc/datatypes/photo_editor/?file=<?=$image['path'] ?>&rubric=<?=$this->getSetting('rubric') ?>&section=<?=$_GET['section'] ?>" onclick="wait_editor();"><img alt="Редактор фото" src="/pics/editor/photo_editor.png" style="margin-top: -4px;"></a></span>
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
					if ($wh!='') $wh = ' Кроме того, '.$wh.'.';
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
				<?
				if (floor($image['id'])>0){
					$wh = @getimagesize($image['fullpath']);
					/*if ((floor($wh[0])<=600) && (floor($wh[1])<=200))*/
					{
						if ($image['ext']=='swf'){
							$imgw = (floor($st['settings']['imgwtype'])==1)?floor($st['settings']['imgw']):'100%';
							$imgh = (floor($st['settings']['imghtype'])==1)?floor($st['settings']['imgh']):'100%';
							$iinner = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="'.$imgw.'" height="'.$imgh.'">';
							$iinner.= '<param name="allowScriptAccess" value="sameDomain">';
							$iinner.= '<param name="movie" value="'.$image['path'].'">';
							$iinner.= '<param name="quality" value="high">';
							$iinner.= '<param name="salign" value="lt">';
							$iinner.= '<param name="wmode" value="transparent">';
							$iinner.= '<param name="menu" value="false">';
							$iinner.= '<param name="scale" value="noborder">';
							$iinner.= '<embed src="'.$image['path'].'" width="'.$imgw.'" height="'.$imgh.'" quality="high" scale="noborder" salign="lt" wmode="transperent" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" menu="false"></embed>';
							$iinner.= '</object>';
							print $iinner;
						}
						else print '
						<img class="contentimg" id="contentimg'.$this->getSetting('name').'" src="'.$image['path'].'" style="width: 170px"/>';

					}

				}
				?>
				</div>
				</div>
			</div>
		<?
				if ($span) print '
			<span class="clear"></span>';
		}
	}
	function preSave(){
		$errors = array();
		$settings = $this->getSetting('settings');
		$newvalue = floor($_POST[$this->getSetting('name')]);
		if ((isset($settings['important'])) && ($newvalue<1)) $errors[] = 'Заполните поле «'.$this->getSetting('description').'»';
		$this->setSetting('value',$newvalue);
		return $errors;
	}
	function postSave(){
		$settings = $this->getSetting('settings');
		global $Storage;
		
		if (floor($this->getSetting('uid'))>0){
			
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
			if (!$st['id']>0) $st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
			if (floor($st['id'])>0){
				$f = $Storage->getFile($this->getSetting('value'));
				if (floor($f['id'])>0){
					if (substr($f['name'],0,5)=='temp_'){
						
						if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
							$nf = $Storage->getFile($f['id']);

                            /*|auto_mini=true|auto_mini_width=210|auto_mini_height=220|*/
			                if ($settings['auto_mini'] && ($settings['auto_mini_width']>0 || $settings['auto_mini_height']>0))
							{
			              				$mini_fname=str_replace('.'.$nf['ext'], '_mini.'.$nf['ext'], $nf['fullpath']);
										copy($nf['fullpath'], $mini_fname);
			       						ResizeFrameMaxSide($mini_fname, 210,220);
			       						/*Crop($mini_fname, 210, 220);*/
							}
							
							/* Сохранять данные из редактора в миниатюру */
							if ($settings['editor_as_min'])
							{
								
								
								$mini_fname_old=str_replace('.'.$f['ext'], '_mini.'.$f['ext'], $f['path']);
								$mini_fname=str_replace('.'.$nf['ext'], '_mini.'.$nf['ext'], $nf['path']);
								rename($_SERVER['DOCUMENT_ROOT'].$mini_fname_old, $_SERVER['DOCUMENT_ROOT'].$mini_fname);
							}
						}
					}
				}
				$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
				foreach ($flist as $f){
					if (!$f['id']==$this->getSetting('value')) $Storage->deleteFile($f['id']);
				}
			}
		}
	}
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".floor($this->getSetting('value'))."'"; }
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		if (!$st['id']>0) $st = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>
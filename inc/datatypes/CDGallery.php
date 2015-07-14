<?
/*
Класс, описывающий тип «Изображение»
*/
class CDGallery extends VirtualType
{
	function init($settings){
		VirtualType::init($settings);
	}
	function drawEditor($divstyle = '',$span = true){
		$settings = $this->getSetting('settings');
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		if (!floor($st['id'])>0)
		$st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
		$f = 0;
		if (floor($st['id'])>0){

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
                $('#loading').attr('src', '/pics/loading.gif').fadeIn(0);
                this.setData({sid : '<?=session_id()?>', theme: '<?=trim($this->getSetting('theme'))?>', rubric: '<?=trim($this->getSetting('rubric'))?>', stid: '<?=$st['id']?>', uid: <?=floor($this->getSetting('uid'))?>});
            },
            onComplete: function(file, response){
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading<?='_'.$this->getSetting('name')?>').fadeOut(0);
                    status.html(status.html()+'<LI style="height: 170px;float: left;"><div class="gallery_container"><input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>[]" value="'+response.id+'"><span class="button txtstyle"><input type="button" onclick="delete_file_image(this); return false" id="delete_button_image" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение"></span><img style="height: 170px" class="contentimg" src="'+response.path+'" class="contentimg"></div></LI>');
                    //$('#uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>').val(response.id);
                }else{
                    $('#loading').fadeOut(0);
                    if (response.error!='') alert(response.error);


                }
            }
        });


    });
    	function delete_file_image (elem)
        {
       		if (!confirm('Вы уверены, что хотите удалить этот файл?')) return false;

			$(elem).parents('.gallery_container').remove();
			return false;
        }


			</script>
			  <script>
  $(function() {
    $( "#sortable" ).sortable();
    //$( "#sortable" ).disableSelection();
  });
  </script>

			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
			<?
			/*if ($this->getSetting('name')=='image' && $_GET['section']==7) 	print '<div style="padding: 0 5px; color: #ff0000">Рекомендуемый размер:  1040x450 пикселей</div>';*/
			?>
                <div class="place forimage">
				<span id="upl_button<?='_'.$this->getSetting('name')?>" class="button">
					Загрузить изображение
				</span>
				</div>
               <span class="clear"></span>
				<div id="upl_error<?='_'.$this->getSetting('name')?>"></div>
			    <div id="upl_status<?='_'.$this->getSetting('name')?>"></div>

				<span class="clear"></span>
				<?
					$desc = '';
					$exts = upper(str_replace(',',', ',$st['settings']['exts']));
					if ($exts!='') $desc.= ' формата '.$exts;
					$wh = '';
					if (floor($st['settings']['imgw'])>0){
						$imgw = floor($st['settings']['imgw']);
						if (floor($st['settings']['imgwtype'])==1) $wh.= 'ширина должна быть равна '.$imgw.'px';
						if (floor($st['settings']['imgwtype'])==2) $wh.= 'ширина должна быть меньше или равна '.$imgw.'px';
						if (floor($st['settings']['imgwtype'])==3) $wh.= 'ширина должна быть больше или равна '.$imgw.'px';
					}
					if (floor($st['settings']['imgh'])>0){
						$imgh = floor($st['settings']['imgh']);
						if (floor($st['settings']['imghtype'])==1) $wh.= (($wh=='')?'':', а ').'высота должна быть равна '.$imgh.'px';
						if (floor($st['settings']['imghtype'])==2) $wh.= (($wh=='')?'':', а ').'высота должна быть меньше или равна '.$imgh.'px';
						if (floor($st['settings']['imghtype'])==3) $wh.= (($wh=='')?'':', а ').'высота должна быть больше или равна '.$imgh.'px';
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
				<UL id="sortable">
				<?
				$images=$this->getSetting('value');
				$images=explode('|',$images);
				foreach ($images as $img)
				if ($img>0)
				{                 	$image = $Storage->getFile($img);
					$f = floor($image['id']);
					?>
					<LI style="height: 170px;float: left;">
					<div class="gallery_container">
					<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>[]" value="<?=$f?>"><span class="button txtstyle">
					<input type="button" onclick="delete_file_image(this); return false" id="delete_button_image" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение"></span>
					<img style="height: 170px" class="contentimg" src="<?=$image['path']?>" class="contentimg">
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
			if (!$st['id']>0) $st = $Storage->getStorage(floor($this->getSetting('imagestorage')));
			if (floor($st['id'])>0){

				$files=explode('|',$this->getSetting('value'));
				foreach ($files as $fi)
				{
					$f = $Storage->getFile($fi);
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
	}
	function getValue(){ return $this->getSetting('value'); }
	function getUpdateSQL(){ return "`".$this->getSetting('name')."`='".$this->getSetting('value')."'"; }
	function delete(){
		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>
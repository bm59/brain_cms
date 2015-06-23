<?
/*
Класс, описывающий тип «Изображение»
*/
class CDImage extends VirtualType
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
			$image = $Storage->getFile($this->getSetting('value'));
			$f = floor($image['id']);
			$this->setSetting('value',$f);
			?>
			<script>
				function showHideDeletingButton<?=htmlspecialchars($this->getSetting('name'))?>(mode){
					var span = $('<?=htmlspecialchars($this->getSetting('name'))?>uploadimagedeletebutton');
					span.style.display = (mode>0)?'':'none';
				}
				function on<?=htmlspecialchars($this->getSetting('name'))?>StartLoading(){
					var img = $('<?=htmlspecialchars($this->getSetting('name'))?>imagecontent');
					img.innerHTML = '<div class="contenttxt"><br>Идет загрузка...</div>';
				}
				function on<?=htmlspecialchars($this->getSetting('name'))?>FinishDeleting(){
					var file = $('uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>');
					var img = $('<?=htmlspecialchars($this->getSetting('name'))?>imagecontent');
					img.innerHTML = '';
					file.value = 0;
					showHideDeletingButton<?=htmlspecialchars($this->getSetting('name'))?>(0);
				}
				function on<?=htmlspecialchars($this->getSetting('name'))?>FinishLoading(uploadimage,uploadimageid,uploadimagewidth,uploadimageheight){
					var file = $('uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>');
					var img = $('<?=htmlspecialchars($this->getSetting('name'))?>imagecontent');
					img.innerHTML = '';
					if (uploadimageid>0){
						file.value = uploadimageid;
						if (parseInt(uploadimagewidth,10)<=600 && parseInt(uploadimageheight,10)<=200){
							img.innerHTML = '<img class="contentimg" src="'+uploadimage+'" />';
							if (uploadimage.replace(/[^.]*\./,"")=='swf'){
								var imgHTMLCode = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="'+uploadimagewidth+'" height="'+uploadimageheight+'">';
								imgHTMLCode+= '<param name="allowScriptAccess" value="sameDomain">';
								imgHTMLCode+= '<param name="movie" value="'+uploadimage+'">';
								imgHTMLCode+= '<param name="quality" value="high">';
								imgHTMLCode+= '<param name="salign" value="lt">';
								imgHTMLCode+= '<param name="wmode" value="transparent">';
								imgHTMLCode+= '<param name="menu" value="false">';
								imgHTMLCode+= '<param name="scale" value="noborder">';
								imgHTMLCode+= '<embed src="'+uploadimage+'" width="'+uploadimagewidth+'" height="'+uploadimageheight+'" quality="high" scale="noborder" salign="lt" wmode="transperent" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" menu="false"></embed>';
								imgHTMLCode+= '</object>';
								img.innerHTML = imgHTMLCode;
							}
						}
						else{
							//img.innerHTML = '<div class="contenttxt"><a href="'+uploadimage+'">Ссылка на файл</a></div>';
							img.innerHTML = '<img class="contentimg" src="'+uploadimage+'" style="width: 170px"/><div class="contenttxt"><a href="'+uploadimage+'" target="_blank" style="width: 170px;" target="_blank">Ссылка на файл</a></div>';

						}
						showHideDeletingButton<?=htmlspecialchars($this->getSetting('name'))?>(1);
					}
				}
			</script>
			<iframe id="fileuploadframe" name="fileuploadframe" width="1px" height="1px" src="" style="display:none;"></iframe>
			<?
			if ($this->getSetting('name')=='image' && $_GET['section']==14) 	print '<div style="padding: 0 5px; color: #ff0000">Рекомендуемый размер:  80x80 пикселей (или пропорция)</div>';
			?>
			<div class="place" <?=($divstyle!='')?$divstyle:''?>>
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">
				<span class="button">
					<span class="bl"></span>
					<span class="bc">Загрузить изображение</span>
					<span class="br"></span>
					<div class="fileselect">
												<input type="file" name="<?=htmlspecialchars($this->getSetting('name'))?>2" onchange="uploadFileAjax(this,'<?=htmlspecialchars($this->getSetting('editformid'))?>','fileuploadframe','uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>',<?=$st['id']?>,'<?=trim($this->getSetting('theme'))?>','<?=trim($this->getSetting('rubric'))?>',<?=floor($this->getSetting('uid'))?>,'on<?=htmlspecialchars($this->getSetting('name'))?>StartLoading();','on<?=htmlspecialchars($this->getSetting('name'))?>FinishLoading(uploadimage,uploadimageid,uploadimagewidth,uploadimageheight);','onready');"/>
					</div>
				</span>
				<span id="<?=htmlspecialchars($this->getSetting('name'))?>uploadimagedeletebutton" class="button txtstyle" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>>
					<span class="bl"></span>
					<span class="bc"></span>
					<span class="br"></span>
					<input type="button" onclick="uploadFileAjax(this,'<?=htmlspecialchars($this->getSetting('editformid'))?>','fileuploadframe','uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>',<?=$st['id']?>,'<?=trim($this->getSetting('theme'))?>','<?=trim($this->getSetting('rubric'))?>',<?=floor($this->getSetting('uid'))?>,'on<?=htmlspecialchars($this->getSetting('name'))?>StartLoading();','on<?=htmlspecialchars($this->getSetting('name'))?>FinishDeleting();','anytime');return false;" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение" />
				</span>
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
				<div class="contentdesc"><small><?=$desc?></small></div>
				<div id="<?=htmlspecialchars($this->getSetting('name'))?>imagecontent">
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
						<img class="contentimg" src="'.$image['path'].'" style="width: 170px"/>';

						if ((floor($wh[0])>=200) && (floor($wh[1])>=200))
						print '
						<div class="contenttxt"><a href="'.$image['path'].'" target="_blank">Ссылка на файл</a></div>';
					}

					/*else print '
						<div class="contenttxt"><a href="'.$image['path'].'">Ссылка на файл</a></div>';*/
				}
				?>
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
		global $Storage;
		if (floor($this->getSetting('uid'))>0){
			$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));
			if (floor($st['id'])>0){
				$f = $Storage->getFile($this->getSetting('value'));
				if (floor($f['id'])>0){
					if (substr($f['name'],0,5)=='temp_'){
						if ($Storage->renameFile($f['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')))){
							$nf = $Storage->getFile($f['id']);
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
		$flist = $Storage->getListByUID($st['id'],$this->getSetting('theme'),$this->getSetting('rubric'),floor($this->getSetting('uid')));
		foreach ($flist as $f) $Storage->deleteFile($f['id']);
	}
}
?>
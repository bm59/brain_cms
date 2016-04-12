<?
class CDVideo extends VirtualType
{
	function init($settings){
		$settings['descr']='Видео файл';
		VirtualType::init($settings);
	}
	function drawEditor(){
		$settings = $this->getSetting('settings');

		global $Storage;
		$st = $Storage->getStorage(floor($this->getSetting($this->getSetting('name').'storage')));

		$f = 0;
		if (floor($st['id'])>0){
			$videofile = $Storage->getFile($this->getSetting('value'));
			$f = floor($videofile['id']);
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
						img.innerHTML = '<div class="contenttxt"><a href="'+uploadimage+'">Ссылка на файл</a></div><div class="contenttxt"><a href="/site/video.php?file='+uploadimage+'" onclick="PopUp(\'/site/video.php?file='+uploadimage+'\',\'Video\',420,350); return false;">Просмотр видео</a></div>';
						showHideDeletingButton<?=htmlspecialchars($this->getSetting('name'))?>(1);

					}
				}
			</script>
			<iframe id="fileuploadframe" name="fileuploadframe" width="1px" height="1px" src="" style="display:none;"></iframe>
			<div class="place">
				<label><?=htmlspecialchars($this->getSetting('description'))?><?=((isset($settings['important']))?' <span class="important">*</span>':'')?></label>
				<input type="hidden" id="uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>" name="<?=htmlspecialchars($this->getSetting('name'))?>" value="<?=$this->getSetting('value')?>">
				<div class="button">
					Загрузить файл
					<div class="fileselect">
					<input type="file" name="<?=htmlspecialchars($this->getSetting('name'))?>2" id="<?=htmlspecialchars($this->getSetting('name'))?>2" onchange="uploadFileAjax(this,'<?=htmlspecialchars($this->getSetting('editformid'))?>','fileuploadframe','uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>',<?=$st['id']?>,'<?=trim($this->getSetting('theme'))?>','<?=trim($this->getSetting('rubric'))?>',<?=floor($this->getSetting('uid'))?>,'on<?=htmlspecialchars($this->getSetting('name'))?>StartLoading();','on<?=htmlspecialchars($this->getSetting('name'))?>FinishLoading(uploadimage,uploadimageid,uploadimagewidth,uploadimageheight);','onready');"/>
					</div>
				</div>
				<span id="<?=htmlspecialchars($this->getSetting('name'))?>uploadimagedeletebutton" class="button txtstyle" <?=(floor($this->getSetting('value'))<1)?'style="display:none;"':''?>>
					<input type="button" onclick="uploadFileAjax(this,'<?=htmlspecialchars($this->getSetting('editformid'))?>','fileuploadframe','uploadfilehidden<?=htmlspecialchars($this->getSetting('name'))?>',<?=$st['id']?>,'<?=trim($this->getSetting('theme'))?>','<?=trim($this->getSetting('rubric'))?>',<?=floor($this->getSetting('uid'))?>,'on<?=htmlspecialchars($this->getSetting('name'))?>StartLoading();','on<?=htmlspecialchars($this->getSetting('name'))?>FinishDeleting();','anytime');return false;" style="background-image: url(/pics/editor/delete.gif)" title="Удалить ролик" />
				</span>
				<span class="clear"></span>
				<?
					$desc = 'Видеофайл в формате flv'.$this->getSetting($this->getSetting('name'));;
					$exts = upper(str_replace(',',', ',$st['settings']['exts']));
					if ($exts!='') $desc.= ' формата '.$exts;
				?>
				<div class="contentdesc"><small><?=$desc?></small></div>
				<div id="<?=htmlspecialchars($this->getSetting('name'))?>imagecontent" name="<?=htmlspecialchars($this->getSetting('name'))?>imagecontent">
				<?
				if (floor($videofile['id'])>0){
				print '<div class="contenttxt"><a href="'.$videofile['path'].'">Ссылка на файл</a></div>';
				print '<div class="contenttxt"><a href="/site/video.php?file='.$videofile['path'].'" onclick="PopUp(\'/site/video.php?file='.$videofile['path'].'\',\'Video\',420,350); return false;">Просмотр видео</a></div>';
				}
				?>
				</div>
			</div>
			<span class="clear"></span>
		<?
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
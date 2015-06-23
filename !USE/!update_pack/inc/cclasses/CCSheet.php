<?
/*
Функционал Листа
*/
class CCSheet extends VirtualContent
{
	function init($settings){
		VirtualContent::init($settings);
	}

	function get(){
		global $CDDataSet;
		$retval = array();
		$r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$this->getSetting('section')."'"));
		$dataset = $CDDataSet->get($this->getSetting('dataset'));
		foreach ($dataset['types'] as $k=>$dt){
			$retval[$dt['name']] = $r[$dt['name']];
		}
		return $retval;
	}

	function start(){
		global $CDDataSet;
		if (!msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$this->getSetting('section')."'")))
			msq("INSERT INTO `".$this->getSetting('table')."`(`section_id`) VALUES ('".$this->getSetting('section')."')");
		$dataset = $CDDataSet->get($this->getSetting('dataset'));
		$imagestorage = $this->getSetting('imagestorage');
		$filestorage = $this->getSetting('filestorage');
		foreach ($dataset['types'] as $k=>$dt){
			$tface = new $dt['type'];
			$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
			$dataset['types'][$k]['face'] = $tface;
		}
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$this->getSetting('section')."'"))){
			foreach ($dataset['types'] as $k=>$dt){
				$tface = $dt['face'];
				$tface->init(array('value'=>$r[$dt['name']],'uid'=>floor($r['id'])));
				$dataset['types'][$k]['face'] = $tface;
			}
		}
		$this->setSetting('dataface',$dataset);
		if (floor($_POST['editformpost'])==1) $this->setSetting('saveerrors',$this->save());
		$this->drawEdit();
	}
	function save(){		global $SiteVisitor;
		$errors = array();
		$dataset = $this->getSetting('dataface');
		foreach ($dataset['types'] as $k=>$dt){
			$tface = $dt['face'];
			$err = $tface->preSave(); foreach ($err as $v) $errors[] = $v;
			$dataset['types'][$k]['face'] = $tface;
		}
		if (count($errors)==0){
			$update = '';
			foreach ($dataset['types'] as $dt){
				$tface = $dt['face'];
				$tface->postSave();
				$update.= (($update!='')?',':'').$tface->getUpdateSQL();
				$dataset['types'][$k]['face'] = $tface;
			}

			msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `section_id`='".$this->getSetting('section')."'");
            $SiteVisitor->SaveLog($this->getSetting('section'), 'Изменена страница', '');
		}
		$this->setSetting('dataface',$dataset);
		return $errors;
	}
	function drawEdit(){
		global $CDDataSet,$SiteSections;
		$section = $SiteSections->get($this->getSetting('section'));
		?>
		<div id="content" class="forms">
			<h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <?=$section['name']?></h1>
			<?
			$saveerrors = $this->getSetting('saveerrors');
			if (!is_array($saveerrors)) $saveerrors = array();
			if (count($saveerrors)>0){
				print '
				<p><strong>Сохранение не выполнено по следующим причинам:</strong></p>
				<ul class="errors">';
					foreach ($saveerrors as $v) print '
					<li>'.$v.'</li>';
				print '
				</ul>
				<div class="hr"><hr /></div>';
			}
			?>
			<p class="impfields">Поля, отмеченные знаком «<span class="important">*</span>», обязательные для заполнения.</p>
			<form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="editformpost" value="1">
				<?
				$dataset = $this->getSetting('dataface');
				foreach ($dataset['types'] as $dt){
					$tface = $dt['face'];
					$tface->drawEditor();
				}
			?>
			<div class="place">
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc">Сохранить</span>
					<span class="br"></span>
					<input type="submit" name="editform" value=""/>
				</span>
			</div>
			<span class="clear"></span>
			</form>
		</div>
		<?
	}
	function delete(){
		global $CDDataSet;
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `section_id`='".$this->getSetting('section')."'"))){
			$dataset = $CDDataSet->get($this->getSetting('dataset'));
			$imagestorage = $this->getSetting('imagestorage');
			$filestorage = $this->getSetting('filestorage');
			foreach ($dataset['types'] as $dt){
				$tface = new $dt['type'];
				$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
				$tface->delete();
			}
			msq("DELETE FROM `".$this->getSetting('table')."` WHERE `section_id`='".$this->getSetting('section')."'");
			return true;
		}
		return true;
	}
}
?>
<?
class CCPhotoalbum extends VirtualContent
{
	function init($settings){
		VirtualContent::init($settings);
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('photoalbum_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;
	}

	function getList($page = 1, $album= -1){
		$retval = array();
		if ($album>-1) $conditions.= (($conditions!='')?" AND ":"")."`album`='$album'";
		if ($conditions!='') $conditions = ' WHERE '.$conditions;
		$count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`".$conditions.""));
		$count = floor($count['c']);
		$page = floor($page);
		if ($page==-1) $this->setSetting('onpage',10000);
		if ($page<1) $page = 1;
		$this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
		if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
		$this->setSetting('page',$page);
		$q = msq("SELECT `id` FROM `".$this->getSetting('table')."`".$conditions." ORDER BY `id` DESC LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));
		while ($r = msr($q)) $retval[] = $this->getOne($r['id']);
		return $retval;
	}
	function getOne($id){
		global $CDDataSet;
		$retval = array();
		$id = floor($id);
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
			$retval['id'] = $r['id'];
			$retval['album'] = $r['album'];
			$dataset = $CDDataSet->get($this->getSetting('dataset'));
			foreach ($dataset['types'] as $k=>$dt){
				$retval[$dt['name']] = $r[$dt['name']];
			}
		}
		return $retval;
	}
	function getRubricList(){
		$answered = floor($answered);
		$retval = array();
		$q = msq("SELECT * FROM `".$this->getSetting('albums')."` ORDER BY `name` ASC");

        $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
		$count = floor($count['c']);
        $retval[-1]=array('id'=> -1, 'name'=> 'Все', 'count'=>$count);


        $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."` WHERE `album`='".$r['id']."'"));
		$count = floor($count['c']);
        $retval[0]=array('id'=> 0, 'name'=> 'Без фотоальбома', 'count'=>$count);

		if ($conditions!="") $conditions = ' WHERE '.$conditions;
		while ($r = msr($q))
		{
			$count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."` WHERE `album`='".$r['id']."'"));
			$count = floor($count['c']);

			$rub = $this->getRubric($r['id']);
			$rub['count'] = $count;
			$retval[] = $rub;
		}
		return $retval;
	}
	function getRubric($id){
		$id = floor($id);
		$retval = array();
		if ($id==-1)
		{			$retval['id'] = -1;
			$retval['name'] ='Все';
		}
		elseif ($id==0)
		{
			$retval['id'] = 0;
			$retval['name'] ='Без фотоальбома';
		}
		elseif ($r = msr(msq("SELECT * FROM `".$this->getSetting('albums')."` WHERE `id`='$id'"))){
			$retval['id'] = $id;
			$retval['name'] = $r['name'];
			$retval['date'] = $r['date'];
		}
		return $retval;
	}
	function addQuestion($name,$email,$rubric,$question){
		$rublist = $this->getRubricList(0);
		$realrub = 0;
		$email = checkEmail($email); if (!$email) $email = '';
		foreach ($rublist as $rub) if ($rub['id']==$rubric) $realrub = $rub['id'];
		$question = addslashes(substr(trim($question),0,2000));
		msq("INSERT INTO `".$this->getSetting('table')."` (`rubric`,`date`,`author`,`authormail`,`question`,`answer`) VALUES ('$realrub','".date("Y-m-d")."','".substr(addslashes($name),0,100)."','$email','$question','')");
	}

	function start(){
		if (floor($_GET['function'])==0){
			if (isset($_GET['element'])){
				global $CDDataSet;
				$dataset = $CDDataSet->get($this->getSetting('dataset'));
				$imagestorage = $this->getSetting('imagestorage');
				$filestorage = $this->getSetting('filestorage');
				$iconstorage = $this->getSetting('iconstorage');
				foreach ($dataset['types'] as $k=>$dt){
					$tface = new $dt['type'];
					$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'iconstorage'=>floor($iconstorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
					$dataset['types'][$k]['face'] = $tface;
				}
				$element = $this->getOne(floor($_GET['element']));
				if (floor($element['id'])>0){
					foreach ($dataset['types'] as $k=>$dt){
						$tface = $dt['face'];
						$tface->init(array('value'=>$element[$dt['name']],'uid'=>floor($element['id'])));
						$dataset['types'][$k]['face'] = $tface;
					}
				}
				$this->setSetting('dataface',$dataset);
				if (floor($_POST['editformpost'])==1){
					$this->setSetting('saveerrors',$this->save());
					if (count($this->getSetting('saveerrors'))==0){
						unset($_GET['element']);
						$this->start();
						return;
					}
				}
				$this->drawAddEdit();
			}
			else{
				if (floor($_GET['delete'])>0) $this->deleteElement($_GET['delete']);
				$this->drawList();
			}
		}
		elseif(floor($_GET['function'])==1){
			if (isset($_GET['element'])){
				$element = $this->getRubric($_GET['element']); $element['id'] = floor($element['id']);
				$this->setSetting('data',$element);
				if (floor($_POST['editformpost'])==1){
					$this->setSetting('saveerrors',$this->saveRubric());
					if (count($this->getSetting('saveerrors'))==0){
						unset($_GET['element']);
						$this->start();
						return;
					}
				}
				$this->drawAddEditRubric();
			}
			else{
				if (floor($_GET['delete'])>0) $this->deleteRubric($_GET['delete']);
				$this->drawRubricList();
			}
		}
	}
	function save(){		global $SiteVisitor;
		$errors = array();
		$album = $this->getRubric($_POST['album']); $album['id'] = floor($album['id']);
		$dataset = $this->getSetting('dataface');
		foreach ($dataset['types'] as $k=>$dt){
			$tface = $dt['face'];
			$err = $tface->preSave(); foreach ($err as $v) $errors[] = $v;
			$dataset['types'][$k]['face'] = $tface;
		}
		if (trim($_POST['authormail'])!=''){
			if (!checkEmail($_POST['authormail'])) $errors[] = 'Электронная почта автора: указан некорректный адрес';
		}
		if (count($errors)==0){
			$element = $this->getOne($_GET['element']); $element['id'] = floor($element['id']);
			$update = "`album`='".$album['id']."'";
			if ($element['id']<1){
				msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
				$element['id'] = mslastid();
				$comment='Добавлено фото';
			}
			else $comment='Изменено фото';
			foreach ($dataset['types'] as $dt){
				$tface = $dt['face'];
				$tface->init(array('uid'=>floor($element['id'])));
				$tface->postSave();
				$update.= (($update!='')?',':'').$tface->getUpdateSQL();
				$dataset['types'][$k]['face'] = $tface;
			}
			$SiteVisitor->SaveLog($this->getSetting('section'), $comment, '?photo='.$element['id']);

			msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$element['id']."'");
		}
		$this->setSetting('dataface',$dataset);
		return $errors;
	}
	function deleteElement($id){
		$id = floor($id);
		global $CDDataSet;
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
			$dataset = $CDDataSet->get($this->getSetting('dataset'));
			$imagestorage = $this->getSetting('imagestorage');
			$filestorage = $this->getSetting('filestorage');
			foreach ($dataset['types'] as $dt){
				$tface = new $dt['type'];
				$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
				$tface->delete();
			}
			msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
			return true;
		}
		return false;
	}
	function drawAddEdit(){
		global $CDDataSet,$SiteSections;
		$section = $SiteSections->get($this->getSetting('section'));
		$element = $this->getOne($_GET['element']); $element['id'] = floor($element['id']);
		if (isset($_POST['rubric'])) $element['rubric'] = floor($_POST['rubric']);
		$rubric = $this->getRubric($element['rubric']); $element['rubric'] = floor($rubric['id']);
		$rubriclist = $this->getRubricList();
		?>
		<div id="content" class="forms">
			<h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <a href="./?section=<?=$section['id']?>"><?=$section['name']?></a> &rarr; <?=($element['id']>0)?'Редактирование':'Добавление'?></h1>
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
					if ($dt['name']=='date') $tface->drawEditor();
				}
				?>
				<div class="place" style="z-index: 10;">
					<label>Фотоальбом</label>
					<?
					$values = array('0'=>'Без фотоальбома');
					foreach ($rubriclist as $r) $values[$r['id']] = $r['name'];
					print getSelectSinonim('album',$values,floor($element['album']));
					?>
				</div>
				<?
				foreach ($dataset['types'] as $dt){
					$tface = $dt['face'];
					if ($dt['name']!='date') $tface->drawEditor();
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
	function drawList(){
		global $SiteSections;
		$section = $SiteSections->get($this->getSetting('section'));
		$searchfrom = isset($_POST['searchfrom'])?$_POST['searchfrom']:$_GET['searchfrom'];
		$searchto = isset($_POST['searchto'])?$_POST['searchto']:$_GET['searchto'];
		if (isset($_POST['searchalbum']) || isset($_GET['searchalbum']))
		{
			$searchalbum = isset($_POST['searchalbum'])?$_POST['searchalbum']:$_GET['searchalbum'];
			$searchalbum = $this->getRubric($searchalbum); $searchalbum['id'] = $searchalbum['id'];
		}
		$rubriclist = $this->getRubricList();
		?>
		<div id="content" class="forms">
			<h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <?=$section['name']?></h1>
<!--//			<form id="searchform" name="searchform" action="./?section=<?=$section['id'].(($searchrubric['id']>0)?'&searchrubric='.$searchrubric['id']:'')?>" method="POST">
				<div class="place" id="searchfrom_calendar" style="z-index: 10; width: 158px; margin-right: 2%;">
					<script>
						CalendarInit('searchfrom','Начальная дата','<?=$searchfrom?>');
					</script>
				</div>
				<div class="place" id="searchto_calendar" style="z-index: 10; width: 158px; margin-right: 2%;">
					<script>
						CalendarInit('searchto','Конечная дата','<?=$searchto?>');
					</script>
				</div>
				<div class="place" style="width: auto; margin-right: 20px;">
					<label>&nbsp;</label>
					<span class="forbutton">
						<span class="button">
							<span class="bl"></span>
							<span class="bc">Показать вопросы за выбранный период</span>
							<span class="br"></span>
							<input type="submit" value=""/>
						</span>
					</span>
				</div>
				<?
				if (($searchfrom.$searchto!='') || ($searchrubric['id']>0)){
				?>
				<div class="place" style="width: auto;">
					<label>&nbsp;</label>
					<span class="forbutton">
						<a href="./?section=<?=$section['id']?>" class="button">
							<span class="bl"></span>
							<span class="bc">Показать все вопросы</span>
							<span class="br"></span>
						</a>
					</span>
				</div>
				<?
				}
				?>
			</form>//-->
			<span class="clear"></span>
			<?
			if (count($rubriclist)>0){
				?>
				<form id="searchform" name="searchform" action="./?section=<?=$section['id'].(($searchfrom!='')?'&searchfrom='.$searchfrom:'').(($searchto!='')?'&searchto='.$searchto:'')?>" method="POST">
					<div class="place" style="width: 336px; margin-right: 20px; z-index: 9;">
						<label>Фотоальбом</label>
						<?
						/*$values = array('0'=>'Без фотоальбома');*/
						foreach ($rubriclist as $r) $values[$r['id']] = $r['name'];
						print getSelectSinonim('searchalbum',$values,$searchalbum['id']);
						?>
					</div>
					<div class="place" style="width: auto; margin-right: 20px;">
						<label>&nbsp;</label>
						<span class="forbutton">
							<span class="button">
								<span class="bl"></span>
								<span class="bc">Показать фото из выбранного фотоальбома</span>
								<span class="br"></span>
								<input type="submit" value=""/>
							</span>
						</span>
					</div>
					<div class="place" style="width: auto;">
						<label>&nbsp;</label>
						<span class="forbutton">
							<a href="./?section=<?=$section['id']?>&function=1" class="button" style="display: block;">
								<span class="bl"></span>
								<span class="bc">Управление фотоальбомами</span>
								<span class="br"></span>
							</a>
						</span>
					</div>
					<span class="clear"></span>
				</form>
				<?
			}
			else{
			?>
			<div class="place" style="width: auto;">
				<label>&nbsp;</label>
				<span class="forbutton">
					<a href="./?section=<?=$section['id']?>&function=1" class="button" style="display: block;">
						<span class="bl"></span>
						<span class="bc">Управление фотоальбомами</span>
						<span class="br"></span>
					</a>
				</span>
			</div>
			<span class="clear"></span>
			<?
			}
			?>
			<div class="hr"><hr /></div>
			<?
			$list = $this->getList(floor($_GET['page']),$searchalbum['id']);
			if (count($list)==0){
				?>
				<p>Отсутствуют записи, удовлетворяющие заданным условиям</p>
				<span class="clear"></span>
				<div class="place">
					<a href="./?section=<?=$section['id']?>&element=new" class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc">Добавить</span>
						<span class="br"></span>
					</span>
				</div>
				<?
			}
			else{
				?>
				<table class="table-content stat">
					<tr>
						<th class="t_minwidth t_nowrap">Фото</th>
						<th class="t_nowrap">Фотоальбом</th>
						<th class="t_nowrap">Заголовок</th>
						<th class="t_nowrap">Комментарий</th>
						<th class="t_32width"></th>
					</tr>
					<?
					$Storage = new Storage;
                    $Storage ->init();
                    $St =$Storage->getStorage('iconstorage');

					foreach ($list as $el){
                        $image=$Storage->getFile($el['image']);
						$rubric = $this->getRubric($el['album']);
						?>
						<tr>
							<td class="t_minwidth t_nowrap"><img src="<?=$image['path']?>" height="120px"></td>
							<td class="t_left"><?=$rubric['name']?></td>
							<td class="t_left"><a href="./?section=<?=$section['id']?>&element=<?=$el['id']?>"><?=htmlspecialchars(stripslashes($el['header']))?></a></td>
							<td class="t_left"><?=$el['comment']?></td>
							<td class="t_32width">
								<a href="./?section=<?=$section['id']?>&delete=<?=$el['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить вопрос')) return false;">
									<span class="bl"></span>
									<span class="bc"></span>
									<span class="br"></span>
									<span class="icon" style="background-image: url(/pics/editor/delete.gif)" title="Удалить вопрос" />
								</a>
							</td>
						</tr>
						<?
					}
					?>
				</table>
				<span class="clear"></span>
				<div class="place">
					<a href="./?section=<?=$section['id']?>&element=new" class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc">Добавить</span>
						<span class="br"></span>
					</a>
				</div>
				<span class="clear"></span>
				<?
				if ($this->getSetting('pagescount')>1){
					?>
					<div class="hr"><hr /></div>
					<div id="paging" class="nopad">
						<?
						$href = '?section='.$section['id'];
						if ($searchfrom!='') $href.= '&searchfrom='.$searchfrom;
						if ($searchto!='') $href.= '&searchto='.$searchto;
						if ($searchrubric['id']>0) $href.= '&searchrubric='.$searchrubric['id'];
						for ($i=1; $i<=$this->getSetting('pagescount'); $i++){
							$inner = '';
							$block = array('<a href="./'.$href.'&page='.$i.'" class="button">','</a>');
							if ($i==($this->getSetting('page')-5)){
								$inner = ($i>1)?'<strong>&hellip;</strong>':$i;
							}
							if (($i>($this->getSetting('page')-5)) && ($i<($this->getSetting('page')+5))){
								$inner = $i;
								if ($i==$this->getSetting('page')) $block = array('<span class="button">','</span>');
							}
							if ($i==($this->getSetting('page')+5)){
								$inner = ($i<$this->getSetting('pagescount'))?'<strong>&hellip;</strong>':$i;
							}
							if ($inner!='') print '
							'.$block[0].'
								<span class="bl"></span>
								<span class="bc">'.$inner.'</span>
								<span class="br"></span>
							'.$block[1];
						}
						?>
					</div>
					<?
				}
			}
			?>
		</div>
		<?
	}
	function saveRubric(){
		$errors = array();
		$name = trim($_POST['name']);
		$date = trim($_POST['date']);
		if ($name=='') $errors[] = 'Не указано название фотоальбома';
		if (count($errors)==0){
			$element = $this->getRubric($_GET['element']); $element['id'] = floor($element['id']);
			if ($element['id']<1){
				msq("INSERT INTO `".$this->getSetting('albums')."` (`name`, `date`) VALUES ('".htmlspecialchars($name)."', '".msdtodb($date)."')");
				$element['id'] = mslastid();
			}
			else msq("UPDATE `".$this->getSetting('albums')."` SET `name`='".htmlspecialchars($name)."' WHERE `id`='".$element['id']."'");
		}
		$this->setSetting('data',$this->getRubric($element['id']));
		return $errors;
	}
	function deleteRubric($id){
		$id = floor($id);
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('albums')."` WHERE `id`='$id'"))){
			msq("UPDATE `".$this->getSetting('table')."` SET `rubric`='0' WHERE `rubric`='$id'");
			msq("DELETE FROM `".$this->getSetting('albums')."` WHERE `id`='$id'");
			return true;
		}
		return false;
	}
	function drawAddEditRubric(){
		global $SiteSections;
		$section = $SiteSections->get($this->getSetting('section'));
		$element = $this->getSetting('data');
		?>
		<div id="content" class="forms">
			<h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <a href="./?section=<?=$section['id']?>"><?=$section['name']?></a> &rarr; <a href="./?section=<?=$section['id']?>&function=1">Фотоальбомы</a> &rarr; <?=($element['id']>0)?'Редактирование':'Добавление'?></h1>
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
			<form id="editform" name="editform" action="./?section=<?=$section['id']?>&function=1&element=<?=($element['id']>0)?$element['id']:'new'?>" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="editformpost" value="1">
				<?
				$CDDate=new CDDate;
				$CDDate->setsetting('name', 'date');
				$CDDate->setsetting('description', 'Дата');
                $CDDate->drawEditor();
				?>
				<div class="place">
					<label>Название фотоальбома</label>
					<span class="input">
						<span class="bl"></span>
						<span class="bc"><input type="text" name="name" value="<?=$element['name']?>" maxlength="250"></span>
						<span class="br"></span>
					</span>
				</div>
				<span class="clear"></span>
				<div class="place">
					<span class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc"><?=($element['id']>0)?'Сохранить':'Добавить'?></span>
						<span class="br"></span>
						<input type="submit" name="editform" value=""/>
					</span>
				</div>
				<span class="clear"></span>
			</form>
		</div>
		<?
	}
	function drawRubricList(){
		global $SiteSections;
		$section = $SiteSections->get($this->getSetting('section'));
		?>
		<div id="content" class="forms">
			<h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <a href="./?section=<?=$section['id']?>"><?=$section['name']?></a> &rarr; Список</h1>
			<?
			$list = $this->getRubricList();
			if (count($list)==0){
				?>
				<p>Элементы отсутствуют</p>
				<span class="clear"></span>
				<div class="place">
					<a href="./?section=<?=$section['id']?>&function=1&element=new" class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc">Добавить</span>
						<span class="br"></span>
					</span>
				</div>
				<?
			}
			else{
				?>
				<table class="table-content stat">
					<tr>
						<th class="t_32width">Дата</th>
						<th class="t_nowrap">Название</th>
						<th class="t_32width">Фотографий</th>
						<th class="t_32width"></th>
					</tr>
					<?
					foreach ($list as $el){
						?>
						<tr>
							<td class="t_32width"><?=msdfromdb($el['date'])?></td>
							<td class="t_left"><a href="./?section=<?=$section['id']?>&function=1&element=<?=$el['id']?>"><?=($el['name']!='')?$el['name']:'Без названия'?></a></td>
                            <td class="t_32width"><?=$el['count']?></td>
							<td class="t_32width">
							<?if ($el['id']>0) {?>
								<a href="./?section=<?=$section['id']?>&function=1&delete=<?=$el['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить фотоальбом? Все фотографии перейдут в раздел «Без фотоальбома».')) return false;">
									<span class="bl"></span>
									<span class="bc"></span>
									<span class="br"></span>
									<span class="icon" style="background-image: url(/pics/editor/delete.gif)" title="Удалить рубрику" />
								</a>
								<?}?>
							</td>
						</tr>
						<?
					}
					?>
				</table>
				<span class="clear"></span>
				<div class="place">
					<a href="./?section=<?=$section['id']?>&function=1&element=new" class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc">Добавить</span>
						<span class="br"></span>
					</a>
				</div>
				<span class="clear"></span>
				<?
			}
			?>
		</div>
		<?
	}
	function delete(){
		global $CDDataSet;
		$q = msq("SELECT * FROM `".$this->getSetting('table')."`");
		while ($r = msr($q)) $this->deleteElement($r['id']);
		msq("DROP TABLE `".$this->getSetting('table')."`");
		msq("DROP TABLE `".$this->getSetting('albums')."`");
		return true;
	}
}
?>
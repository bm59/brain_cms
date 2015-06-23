<?
/*
Функционал Новостей
*/
class CCRubricator extends VirtualContent
{
  	public $urlstr;
	public $sqlstr;

		function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;

                $this->search_text = isset($_POST['search_text']) ? $_POST['search_text']: (isset($_GET['search_text']) ? $_GET['search_text'] : '');

                if ($this->search_text!='')
                {
                	$this->sqlstr.=" and `name` like '%".$this->search_text."%'";
                	$this->urlstr.="&text=".$this->search_text;
                }

     	}
        function getChild($parent, $search_usl=''){
                $retval = array();
                $parent=floor($parent);

                $dop="
                or (`parent`=".$parent." and `id` in (select `parent` as id FROM `".$this->getSetting('table')."` WHERE `show`=1 ".$this->sqlstr."))
                or (`parent`=".$parent." and `parent` in (SELECT `id` FROM `".$this->getSetting('table')."` WHERE `show`=1 and `parent`=0 ".$this->sqlstr."))";

                $q = msq("SELECT `id` FROM `".$this->getSetting('table')."` WHERE (`parent`='".$parent."' ".$search_usl.")".$dop." ORDER by `precedence` ASC");

                while ($r = msr($q)) $retval[] = $this->getPub($r['id']);
                return $retval;
        }
        function getPub($id){
                $retval = array();
                $id = floor($id);
                if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'")))

				foreach ($r as $k=>$v)
                $r[$k]=html_entity_decode(stripslashes($v));
                $retval= $r;
                return $retval;
        }
        function getAllSprList($show = 0,$sprnam, $fieldnam){
                $retval = array();
                $SiteSections= new SiteSections;
         		$SiteSections->init();
         		$Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
         		if ($Section['id']>0)
         		{
		 			$Pattern = new $Section['pattern'];
		 			$Iface = $Pattern->init(array('section'=>$Section['id']));
                	if ($show!=0) $conditions.= (($conditions!='')?" AND ":"")."`show`>'0'";
                	if ($conditions!='') $conditions = ' WHERE '.$conditions;
                	$q = msq("SELECT `".$fieldnam."` FROM `".$Iface ->getSetting('table')."`".$conditions." ORDER BY precedence");
                	while ($r = msr($q))
                	$retval[] =$r["$fieldnam"];
                }
                return $retval;
        }
        function start(){
                if (isset($_GET['pub'])){
                        global $CDDataSet;
                        $dataset = $CDDataSet->get($this->getSetting('dataset'));
                        $imagestorage = $this->getSetting('imagestorage');
                        $filestorage = $this->getSetting('filestorage');
                        foreach ($dataset['types'] as $k=>$dt){
                                $tface = new $dt['type'];
                                $tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
                                $dataset['types'][$k]['face'] = $tface;
                        }
                        $pub = $this->getPub(floor($_GET['pub']));
                        if (floor($pub['id'])>0){
                                foreach ($dataset['types'] as $k=>$dt){
                                        $tface = $dt['face'];
                                        $tface->init(array('value'=>$pub[$dt['name']],'uid'=>floor($pub['id'])));
                                        $dataset['types'][$k]['face'] = $tface;
                                }
                        }
                        $this->setSetting('dataface',$dataset);
                        if (floor($_POST['editformpost'])==1){
                                $this->setSetting('saveerrors',$this->save());
                                if (count($this->getSetting('saveerrors'))==0){
                                        unset($_GET['pub']);
                                        $this->start();
                                        return;
                                }
                        }
                        $this->drawAddEdit();
                }
                else{
                        if (floor($_GET['delete'])>0) $this->deletePub($_GET['delete']);
                        $this->drawPubsList();
                }
        }

        function save(){
                $errors = array();
                $dataset = $this->getSetting('dataface');
                foreach ($dataset['types'] as $k=>$dt){
                        $tface = $dt['face'];
                        $err = $tface->preSave(); foreach ($err as $v) $errors[] = $v;
                        $dataset['types'][$k]['face'] = $tface;
                }
                if (count($errors)==0){                		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                         if ($pub['id']<1){
                                msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
                                $pub['id'] = mslastid();
                        }
                        foreach ($dataset['types'] as $dt){
                                $tface = $dt['face'];
                                $tface->init(array('uid'=>floor($pub['id'])));
                                $tface->postSave();
                                if ($tface->getSetting('name')=='tags') $tagss = trim($tface->getSetting('value'));
                                else $update.= (($update!='')?',':'').$tface->getUpdateSQL();
                                $dataset['types'][$k]['face'] = $tface;

                                if ($_REQUEST['add_child']>0)
                                $update.= (($update!='')?',':'').'`parent`="'.$_REQUEST['add_child'].'"';
                        }
                        msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
                }
                $this->setSetting('dataface',$dataset);
                $this->updatePrecedence();
                return $errors;
        }
        function updatePrecedence(){
			$precedence = 0;

			$max_prec = msr(msq("SELECT max( precedence ) AS max FROM `".$this->getSetting('table')."`"));


			$q = msq("SELECT `id`,`precedence` FROM `".$this->getSetting('table')."` WHERE `parent`=0 ORDER BY `precedence` ASC");
			while ($r = msr($q)){

				if (is_null($r['precedence'])) $prec=$max_prec['max']+1;
				else $prec=$precedence;

				msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$prec' WHERE `id`='".$r['id']."'");
				if (!is_null($r['precedence'])) $precedence++;

				$child_precedence = 0;
				$max_prec_parent = msr(msq("SELECT max( precedence ) AS max FROM `".$this->getSetting('table')."` WHERE `parent`=".$r['id']));
				$child_q = msq("SELECT `id`,`precedence` FROM `".$this->getSetting('table')."` WHERE `parent`=".$r['id']." ORDER BY `precedence` ASC");
				while ($child_r = msr($child_q))
				{                	if (is_null($child_r['precedence'])) $prec_child=$max_prec_parent['max']+1;
					else $prec_child=$child_precedence;

                	msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$prec_child' WHERE `id`='".$child_r['id']."'");
					if (!is_null($child_r['precedence'])) $child_precedence++;
				}
			}
		}
        function drawAddEdit(){
                global $CDDataSet,$SiteSections;
                $section = $SiteSections->get($this->getSetting('section'));
                $pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                ?>
                <div id="content" class="forms">
                        <h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <a href="./?section=<?=$section['id']?>"><?=$section['name']?></a> &rarr; <?=($pub['id']>0)?'Редактирование':'Добавление'?></h1>
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
                                <input type="hidden" name="add_child" value="<?=$_REQUEST['add_child']?>">
                                <?
                                $dataset = $this->getSetting('dataface');
                                foreach ($dataset['types'] as $dt){
                                        $tface = $dt['face'];
                                        $stylearray = array(
                                                "ptitle"=>'style="width:32%; margin-right:2%;"',
                                                "pdescription"=>'style="width:32%; margin-right:2%;"',
                                                "pseudolink"=>'style="width:32%;"'
                                        );

                                        if ($dt['name']=='name' && $_GET['add_child']>0)
                                        {                                        	$parent=$this->getPub($_GET['add_child']);
                                        	print '<strong>Добавить раздел в рубрике: '.$parent['name'].'</strong>';
                                        }


                                        $nospans = array("ptitle","pdescription");
                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));
                                }
                        ?>
                        <div class="place">
                                <span class="button big" style="float: right;">
                                        <span class="bl"></span>
                                        <span class="bc"><?=($pub['id']>0)?'Сохранить изменения':'Добавить'?></span>
                                        <span class="br"></span>
                                        <input type="submit" name="editform" value=""/>
                                </span>
                        </div>
                        <span class="clear"></span>
                        </form>
                </div>
                <?
        }
        function SetPseudolink()
		{
			$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE 	pseudolink=''");
			while ($r = msr($q))
			{
					$num=0;
					$i=0;
					while ($num==0)
					{
                        $i++;
						$double=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `pseudolink`='".get_url_text($r['name']).(($i>1) ? "_".$i:"")."' LIMIT 1"));

						if (!$double['id']>0)
						{
							msq("UPDATE `".$this->getSetting('table')."` SET pseudolink='".get_url_text($r['name']).(($i>1) ? "_".$i:"")."' WHERE id=".$r['id']);
							$num=$i;
						}

					}

			}

		}
        function drawPubsList(){
                global $SiteSections;
                $this->SetPseudolink();
                $section = $SiteSections->get($this->getSetting('section'));
                $searchfrom = $searchto = '';
                if (isset($_POST['showsave'])){
					foreach ($_POST as $k=>$v){
						if (preg_match('|^prec\_[0-9]+$|',$k)){
							$p = preg_replace('|^prec\_([0-9]+)$|','\\1',$k);
							msq("UPDATE `".$this->getSetting('table')."` SET `show`='".(($_POST['checkshow'.$p]=='on')?'1':'0')."',`precedence`='".floor($_POST['prec_'.$p])."' WHERE `id`='$p'");

						}
					}
					$this->updatePrecedence();
                }
                $MySqlConnect = new MySqlConnect;
                ?>
                <script>
                </script>
                <div id="content" class="forms">
                        <h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <?=$section['name']?></h1>
                        <form id="searchform" name="searchform" action="./?section=<?=$section['id']?>" method="POST">
							<div class="place" style="z-index: 10; width: 90%; margin-right: 2%;">
								<label>Наименование</label>
								<span class="input">
								<span class="bl"></span>
								<span class="bc"><input type="text" name="search_text" maxlength="100" value="<?=$this->search_text?>"/></span>
								<span class="br"></span>
								</span>
							</div>
							<div class="place" style="width: 8%">
								<label>&nbsp;</label>
								<span class="forbutton">
								<span class="button">
								<span class="bl"></span>
								<span class="bc">Найти</span>
								<span class="br"></span>
								<input type="submit" value="" >
								</span>
								</span>
							</div>
                                <span class="clear"></span>
                        </form>
                        <div class="hr"><hr /></div>
                        <?
                        $Storage = new Storage;
                        $Storage ->init();
                        $St =$Storage->getStorage('iconstorage');

                        $list = $this->getChild('', $this->sqlstr);
                        if (count($list)==0){
                                ?>
                                <p>Отсутствуют публикации, удовлетворяющие заданным условиям</p>
                                <span class="clear"></span>
                                <div class="place">
                                        <a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">
                                                <span class="bl"></span>
                                                <span class="bc">Добавить</span>
                                                <span class="br"></span>
                                        </span>
                                </div>
                                <?
                        }
                        else{
                                ?>
                                <form id="showsave" class="showsave" name="showsave" action="./?section=<?=$section['id']?><?=($this->getSetting('page')>1)?'&page='.$this->getSetting('page'):''?>" method="POST">
                                        <table class="table-content stat">
                                                <tr>
                                                        <th class="t_32width">&nbsp;</th>
                                                        <th class="t_32width">&nbsp;</th>
                                                        <th class="t_nowrap">Наименование</th>
                                                        <th class="t_32width"></th>
                                                </tr>
                                        <?

                                        foreach ($list as $pub){

                                                $image=$Storage->getFile($pub['image']);
                                                ?>
                                                <tr>
                                                        <td class="t_32width"><input type="hidden" name="pubshow_<?=$pub['id']?>" value="1">
                                                        	<input type="checkbox" name="checkshow<?=$pub['id']?>" <?=($pub['show']>0)?'checked':''?> />
                                                            <?if ($image['path']!='') {?><img src="<?=$image['path']?>" width="50px"><?}?>
                                                        </td>
                                                        <td class="t_32width"><input class="posid" name="prec_<?=$pub['id']?>" value="<?=floor($pub['precedence'])?>"/></td>
                                                        <td class="t_left">
                                                        	<a href="./?section=<?=$section['id']?>&pub=<?=$pub['id']?>"><?=htmlspecialchars($pub['name'])?></a>
                                                        	<div style="color:#666666"><?=$child_pub['pseudolink']?></div>
                                                        	<div>
                                                        		<table class="table-content stat">
                                                        		<?
                                                        		$child_list = $this->getChild($pub['id'], $this->sqlstr);
                                                        		foreach ($child_list as $child_pub)
                                                        		{                                                        			?>

					                                                <tr>
					                                                        <td class="t_32width"><input class="posid" name="prec_<?=$child_pub['id']?>" value="<?=floor($child_pub['precedence'])?>"/></td>
					                                                        <td class="t_left">
					                                                        	<a href="./?section=<?=$section['id']?>&pub=<?=$child_pub['id']?>"><?=$child_pub['name']?></a>
					                                                        	<div style="color:#666666"><?=$child_pub['pseudolink']?></div>
					                                                        </td>
					                                                        <td class="t_32width">
									                                            <a href="./?section=<?=$section['id']?>&delete=<?=$child_pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить публикацию')) return false;">
				                                                                        <span class="bl"></span>
				                                                                        <span class="bc"></span>
				                                                                        <span class="br"></span>
				                                                                        <input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить публикацию"/>
				                                                                </a>
					                                                        </td>
					                                                </tr>



                                                        			<?
                                                        		}
                                                        		?>
                                                        		</table>
                                                        	</div>

                                                        </td>
                                                        <td class="t_32width">
                                                        <a href="./?section=<?=$section['id']?>&add_child=<?=$pub['id']?>&pub=new">add</a>
		                                        <span class="button txtstyle">
		                                                <span class="bl"></span>
		                                                <span class="bc"></span>
		                                                <span class="br"></span>
		                                                <input type="button" style="background-image: url(/pics/editor/plus.gif)" title="Добавить подрубрику" onclick="window.location.href = './?section=<?=$section['id']?>&add_child=<?=$pub['id']?>&pub=new';" />
		                                        </span>
		                                       <span class="button txtstyle disabled">
		                                                <span class="bl"></span>
		                                                <span class="bc"></span>
		                                                <span class="br"></span>
		                                                <input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" onclick="return false;" />
		                                        </span>
                                                        </td>
                                                </tr>
                                                <?
                                        }
                                        ?>
                                        </table>
                                        <span class="clear"></span>
                                        <div class="place">
                                                <span class="button big">
                                                        <span class="bl"></span>
                                                        <span class="bc">Сохранить изменения</span>
                                                        <span class="br"></span>
                                                        <input type="submit" name="showsave" value="" />
                                                </span>
                                                <a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">
                                                        <span class="bl"></span>
                                                        <span class="bc">Добавить</span>
                                                        <span class="br"></span>
                                                </a>
                                        </div>
                                        <span class="clear"></span>
                                </form>
                                <span class="clear"></span>
                                <?
                                if ($this->getSetting('pagescount')>1){
                                ?>
                                <div class="hr"><hr /></div>
                                <div id="paging" class="nopad">
                                        <?
                                        $href = '?section='.$section['id'].$this->urlstr;
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
        function deletePub($id){
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
        function delete(){
                global $CDDataSet;
                $q = msq("SELECT * FROM `".$this->getSetting('table')."`");
                while ($r = msr($q)) $this->deletePub($r['id']);
                msq("DROP TABLE `".$this->getSetting('table')."`");
                return true;
        }
}
?>
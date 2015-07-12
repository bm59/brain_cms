<?
/*
Функционал Справочника
*/
class CCSprOrder extends VirtualContent
{
  	public $urlstr;
	public $sqlstr;

		function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;

                if ($_POST['searchtext']!='')
				{
					$this->searchtext = $_POST['searchtext'];
					$this->urlstr.="&searchtext=".$_POST['searchtext'];
				}

				elseif ($_GET['searchtext']!='')
				{
					$this->searchtext = $_GET['searchtext'];
    				$this->urlstr.="&searchtext=".$_GET['searchtext'];
    			}

                if ($this->searchtext!='')
				($this->sqlstr =='') ? $this->sqlstr.=" where `name` LIKE '%".addslashes($this->searchtext)."%'" : $this->sqlstr.=" and `name` LIKE '%".addslashes($this->searchtext)."%'";
     	}
        function getList($searchtext, $page = 1, $orderby=''){
                $retval = array();
                $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`".$this->sqlstr));
                $count = floor($count['c']);
                $page = floor($page);
                if ($orderby=='') $orderby='`precedence` ASC';
                if ($page==-1) $this->setSetting('onpage',10000);
                if ($page<1) $page = 1;
                $this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
                if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
                $this->setSetting('page',$page);
                $q = msq("SELECT `id` FROM `".$this->getSetting('table')."`".$this->sqlstr." ORDER BY ".$orderby." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));
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
        function getAllSprListIdName($show = 0,$sprnam, $fieldnam, $order='',$first=''){
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
                	if ($order!='') $orderby=" ORDER BY $order";
                	if ($first!='') $retval['0'] =$first;
                	$q = msq("SELECT `id`,`".$fieldnam."` FROM `".$Iface ->getSetting('table')."`".$conditions.$orderby);
                	while ($r = msr($q))
                	$retval[$r['id']] =$r["$fieldnam"];
                }
                return $retval;
        }
        function getIdByName($id,$sprnam, $fieldnam, $fieldval){
                $retval = array();
                $SiteSections= new SiteSections;
         		$SiteSections->init();
         		$Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
         		if ($Section['id']>0)
         		{
		 			$Pattern = new $Section['pattern'];
		 			$Iface = $Pattern->init(array('section'=>$Section['id']));
		 			$conditions= "`$fieldnam`='$fieldval'";
                	if ($show!=0) $conditions.= (($conditions!='')?" AND ":"")."`show`>'0'";
                	if ($conditions!='') $conditions = ' WHERE '.$conditions;
                	$q = msr(msq("SELECT `id` FROM `".$Iface ->getSetting('table')."`".$conditions));
                	return $q['id'];
                }
                return $retval;
        }
        function start(){
                // $q = msq("SELECT * FROM `".$this->getSetting('table')."`");
                // while ($r = msr($q)) $this->deletePub($r['id']);
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
                                $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
								$count = floor($count['c']);
								$update.= (($update!='')?',':'')."`precedence`='$count'";
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
                        }
                        msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");
                }
                $this->setSetting('dataface',$dataset);
                return $errors;
        }
        function drawAddEdit(){
                global $CDDataSet,$SiteSections;
                $section = $SiteSections->get($this->getSetting('section'));
                $pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                ?>
                <div id="content" class="forms">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
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
                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));
                                }
                        ?>
                        <div class="place">
                                <span style="float: right;">
                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
                                </span>
                        </div>
                        <span class="clear"></span>
                        </form>
                </div>
                <?
        }
        function updatePrecedence(){
			$precedence = 0;
			$q = msq("SELECT `id` FROM `".$this->getSetting('table')."` ORDER BY `precedence` ASC");
			while ($r = msr($q)){
				msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$precedence' WHERE `id`='".$r['id']."'");
				$precedence++;
			}
		}
        function drawPubsList(){
                global $SiteSections;
                $section = $SiteSections->get($this->getSetting('section'));
                $searchfrom = $searchto = '';
                if (isset($_POST['showsave'])){
                        foreach ($_POST as $k=>$v){
                                if (preg_match('|^pubshow\_[0-9]+$|',$k)){
                                        $p = preg_replace('|^pubshow\_([0-9]+)$|','\\1',$k);
                                        msq("UPDATE `".$this->getSetting('table')."` SET `show`='".(($_POST['checkshow'.$p]=='on')?'1':'0')."', `precedence`='".floor($_POST['prec_'.$p])."' WHERE `id`='$p'");
                                }
                        }
                        $this->updatePrecedence();
                }
                ?>
                <script>
                </script>
                <div id="content" class="forms">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
                        <form id="searchform" name="searchform" action="./?section=<?=$section['id']?>" method="POST">
							<div class="place" style="z-index: 10; width: 90%; margin-right: 2%;">
								<label>Наименование</label>
								<span class="input">
									<input type="text" name="searchtext" maxlength="100" value="<?=$this->searchtext?>"/>
								</span>
							</div>
							<div class="place" style="width: 8%">
								<label>&nbsp;</label>
								<span class="forbutton">
									<span>
										<input class="button" type="submit" value="Найти" >
									</span>
								</span>
							</div>
                                <span class="clear"></span>
                        </form>
                        <div class="hr"><hr /></div>
                        <?
                        $list = $this->getList('',$_GET['page']);
                        if (count($list)==0){
                                ?>
                                <p>Отсутствуют публикации, удовлетворяющие заданным условиям</p>
                                <span class="clear"></span>
                                <div class="place">
                                	<a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
                                </div>
                                <?
                        }
                        else{
                                ?>
                                <form id="showsave" class="showsave" name="showsave" action="./?section=<?=$section['id']?><?=($this->getSetting('page')>1)?'&page='.$this->getSetting('page'):''?>" method="POST">
                                        <?
                                        if ($searchfrom!='') print '
                                                <input type="hidden" name="searchfrom" value="'.htmlspecialchars($searchfrom).'">';
                                        if ($searchto!='') print '
                                                <input type="hidden" name="searchto" value="'.htmlspecialchars($searchto).'">';
                                        ?>
                                        <table class="table-content stat">
                                                <tr>
                                                        <th class="t_32width">&nbsp;</th>
                                                        <th class="t_32width">&nbsp;</th>
                                                        <th class="t_nowrap">Заголовок</th>
                                                        <th class="t_32width"></th>
                                                </tr>
                                        <?
                                        foreach ($list as $pub){
                                                ?>
                                                <tr>
                                                        <td class="t_32width"><input type="hidden" name="pubshow_<?=$pub['id']?>" value="1"><input type="checkbox" name="checkshow<?=$pub['id']?>" <?=($pub['show']>0)?'checked':''?> /></td>
                                                        <td class="t_32width"><input class="posid" name="prec_<?=$pub['id']?>" value="<?=floor($pub['precedence'])?>"/></td>
                                                        <td class="t_left"><a href="./?section=<?=$section['id']?>&pub=<?=$pub['id']?>"><?=htmlspecialchars($pub['name'])?></a></td>
                                                        <td class="t_32width">
                                                                <a href="./?section=<?=$section['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить публикацию')) return false;">
                                                                	<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить публикацию"/>
                                                                </a>
                                                        </td>
                                                </tr>
                                                <?
                                        }
                                        ?>
                                        </table>
                                        <span class="clear"></span>
                                        <div class="place">
                                                <span>
                                                	<input class="button big" type="submit" name="showsave" value="Сохранить изменения" />
                                                </span>
                                                <a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
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
                                                '.$block[0].$inner.$block[1];
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
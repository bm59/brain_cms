<?
class CCLog extends VirtualContent
{
  	public $urlstr;
	public $sqlstr;

		function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;
                $MySqlConnect = new MySqlConnect;

                $like_array=array('search_descr','search_comment','search_changes', 'search_user_name');/* Где нет в названии "name", но нужен поиск по like*/
                $not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $field_tr=array('search_'=>'','_from'=>'','_to'=>'');
                /*подмена названий*/
                $field_change=array(/*'date'=>'time', 'anketa_name'=>'t3.`name`', 'name'=>'t2.`name`'*/);

                foreach ($_REQUEST as $k=>$v)
                if (stripos($k,'search')!==false && $v!='')
                if (!in_array($k,$no_auto))
                {
                	$this->$k=$v;

                	$mysql_k=strtr($k,$field_tr);
                    $mysql_k=strtr($mysql_k,$field_change);

                    $this->urlstr.='&'.$k.'='.$v;

                    if (!in_array($mysql_k,$field_change)) $mysql_k='`'.$mysql_k.'`';

                	if ((stripos($k,'name')!==false || in_array($k,$like_array)) and !in_array($k,$not_like_array))
                	$this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ').$mysql_k." like '%".$v."%'";
                	elseif ($k=='search_date_from')
                	$this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ').'date('.$mysql_k.")>='".$MySqlConnect->dateToDB($v)."'";
                	elseif ($k=='search_date_to')
                	$this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ').'date('.$mysql_k.")<='".$MySqlConnect->dateToDB($v)."'";
                	else $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ').$mysql_k."='".$v."'";

                }

     	}
        function getList($searchtext, $page = 1, $orderby=''){
                $q="SELECT * FROM `".ConfigGet('pr_name')."_log` ".$this->sqlstr;
                /*print  $q;*/
                $count = msq($q);
                $count = mysql_num_rows($count);

                $page = floor($page);
                if ($page==-1) $this->setSetting('onpage',10000);
                if ($page<1) $page = 1;

                $this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
                $this->setSetting('count',ceil($count));

                if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
                $this->setSetting('page',$page);
                /*print $q." ORDER BY `id` DESC LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage');*/
 				$q = msq($q." ORDER BY `id` DESC LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));


 				while ($r = msr($q)) $retval[] = $r;
                return $retval;
        }
        function getPub($id){
                $retval = array();
                $id = floor($id);

                if ($r = msr(msq("SELECT * FROM `fact` WHERE `id`='".$id."'")))

				foreach ($r as $k=>$v)
                $r[$k]=stripslashes($v);
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
                // $q = msq("SELECT * FROM `fact`");
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
                if (count($errors)==0){                		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                         if ($pub['id']<1){
                                msq("INSERT INTO `fact` (`inuse`) VALUES ('1')");
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
                        msq("UPDATE `fact` SET ".$update." WHERE `id`='".$pub['id']."'");
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
                        <span class="clear"></span>
                        </form>
                </div>
                <?
        }
        function drawPubsList(){
                global $SiteSections;
                $section = $SiteSections->get($this->getSetting('section'));
                $MySqlConnect = new MySqlConnect;
                $searchfrom = $searchto = '';
                if (isset($_POST['showsave'])){
                        foreach ($_POST as $k=>$v){
                                if (preg_match('|^pubshow\_[0-9]+$|',$k)){
                                        $p = preg_replace('|^pubshow\_([0-9]+)$|','\\1',$k);
                                        msq("UPDATE `fact` SET `show`='".(($_POST['checkshow'.$p]=='on')?'1':'0')."' WHERE `id`='$p'");
                                }
                        }
                }
                ?>
               <script>
                       	$(function()
                       	{
                        	$('#toggle_search').click(function(){$('#searchform').slideToggle(500); });

                        		$.datepicker.regional['ru'] =
								{
									closeText: 'Закрыть',
									prevText: '&#x3c;Пред',
									nextText: 'След&#x3e;',
									currentText: 'Сегодня',
									monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
									'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
									monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
									'Июл','Авг','Сен','Окт','Ноя','Дек'],
									dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
									dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
									dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
									dateFormat: 'dd.mm.yy',
									firstDay: 1,
									isRTL: false
								};

								$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));

								$( "#from" ).datepicker({
									defaultDate: "+1w",
									changeMonth: true,
									numberOfMonths: 3,
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true,
									onClose: function( selectedDate ) {
									$( "#to" ).datepicker( "option", "minDate", selectedDate );
								}
								});
								$( "#to" ).datepicker({
									defaultDate: "+1w",
									changeMonth: true,
									numberOfMonths: 3,
									showOn: "both",
									buttonImage: "/pics/editor/calendar.gif",
									buttonImageOnly: true,
									onClose: function( selectedDate ) {
									$( "#from" ).datepicker( "option", "maxDate", selectedDate );
								}
								});
                       	});
                </script>
                <div id="content" class="forms">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/admin/total_tab.php")?>
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
                        <form id="searchform" name="searchform" action="./?section=<?=$section['id']?>" method="POST">
							<div class="place" style="z-index: 10; width: 10%;">
								<label>id записи</label>
								<span class="input">
									<input type="text" name="search_item_id" maxlength="100" value="<?=$this->search_item_id?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 10%;">
								<label>user id</label>
								<span class="input">
									<input type="text" name="search_user_id" maxlength="100" value="<?=$this->search_user_id?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 10%;">
								<label>user name</label>
								<span class="input">
									<input type="text" name="search_user_name" maxlength="100" value="<?=$this->search_user_name?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 10%;">
								<label>действие</label>
								<span class="input">
									<input type="text" name="search_descr" maxlength="100" value="<?=$this->search_descr?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 10%;">
								<label>комментарий</label>
								<span class="input">
									<input type="text" name="search_comment" maxlength="100" value="<?=$this->search_comment?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 10%;">
								<label>Изменения</label>
								<span class="input">
									<input type="text" name="search_changes" maxlength="100" value="<?=$this->search_changes?>"/>
								</span>
							</div>
							<div class="place" style="z-index: 10; width: 165px;">
								<label>Дата с</label>
								<div><input id="from" name="search_date_from" type="text" style="width: 100px; float: left;" value="<?=htmlspecialchars($this->search_date_from)?>"/></div>
							</div>

							<div class="place" style="z-index: 10; width: 165px; margin-right: 2%;">
								<label>Дата по</label>
								<div><input id="to" name="search_date_to" type="text" style="width: 100px; float: left;" value="<?=htmlspecialchars($this->search_date_to)?>"/></div>
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
                        <?
                        $href = '?section='.$section['id'].$this->urlstr;
                        ?>

                        </form>
                        <div class="hr"><hr /></div>
                        <?
                        $list = $this->getList('',$_GET['page']);
                        if (count($list)==0){
                                ?>
                                <p>Отсутствуют записи, удовлетворяющие заданным условиям</p>
                                <span class="clear"></span>
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
                                                        <th class="t_32width">id</th>
                                                        <th class="t_32width">дата</th>
                                                        <th class="t_minwidth">id записи</th>
                                                        <th class="t_minwidth">user id</th>
                                                        <th class="t_minwidth">user name</th>
                                                        <th>действие</th>
                                                        <th>комментарий</th>
                                                        <th>ip</th>
                                                </tr>
                                        <?
                                        foreach ($list as $pub){
                                                ?>
                                                <tr>
                                                        <!--//<td class="t_32width"><input type="hidden" name="pubshow_<?=$pub['id']?>" value="1"><input type="checkbox" name="checkshow<?=$pub['id']?>" <?=($pub['show']>0)?'checked':''?> /></td>//-->
                                                        <td class="t_32width"><?=$pub['id']?></td>
                                                        <td class="t_32width"><nobr><?=$MySqlConnect->dateFromDBDot($pub['date'])?> <?=$MySqlConnect->TimeFromDB($pub['date'])?></nobr></td>
                                                        <td class="t_32width"><?=$pub['item_id']?></td>
                                                        <td class="t_32width"><?=$pub['user_id']?></td>
                                                        <td class="t_32width">
                                                        	<div><?=$pub['user_name']?></div>
                                                        	<div><?=$pub['ip']?></div>
                                                        </td>

                                                         <td class="t_left">
                                                        		<?=htmlspecialchars($pub['descr'])?>
                                                        		<?
                                                        		if ($pub['changes']!='')
                                                        		{                                                        			?>
                                                        				<div style="color: #888">
                                                        				<?=$pub['changes']?>
                                                        				</div>
                                                        			<?
                                                        		}
                                                        		?>
                                                         </td>
                                                          <td class="t_left"><?=htmlspecialchars($pub['comment'])?></td>
                                                          <td class="t_center"><?=htmlspecialchars($pub['ip'])?></td>
                                                </tr>
                                                <?
                                        }
                                        ?>
                                        </table>
                                        <span class="clear"></span>
                                </form>
                                <span class="clear"></span>
                                <?
                                if ($this->getSetting('pagescount')>1){
                                ?>
                                <div class="hr"></div>
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
                if ($r = msr(msq("SELECT * FROM `fact` WHERE `id`='".$id."'"))){
                        $dataset = $CDDataSet->get($this->getSetting('dataset'));
                        $imagestorage = $this->getSetting('imagestorage');
                        $filestorage = $this->getSetting('filestorage');
                        foreach ($dataset['types'] as $dt){
                                $tface = new $dt['type'];
                                $tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'filestorage'=>floor($filestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
                                $tface->delete();
                        }
                        msq("DELETE FROM `fact` WHERE `id`='".$id."'");
                        return true;
                }
                return false;
        }
        function delete(){
                return true;
        }
}
?>
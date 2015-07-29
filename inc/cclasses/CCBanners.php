<?
/*
Функционал Справочника
*/
class CCBanners extends VirtualContent
{
  	public $urlstr;
	public $sqlstr;

		function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;

				$search_name 	= isset($_POST['search_name']) ? $_POST['search_name']: (isset($_GET['search_name']) ? $_GET['search_name'] : '');


                if ($search_name!='')
                $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ')."`name` like '%".$search_name."%'";


     	}
        function getList($usl, $page = 1, $orderby=''){
                $retval = array();
                $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`".$this->sqlstr));
                $count = floor($count['c']);
                $page = floor($page);
                if ($orderby=='') $orderby='`id` DESC';
                if ($page==-1) $this->setSetting('onpage',10000);
                if ($page<1) $page = 1;
                $this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
                if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
                $this->setSetting('page',$page);
                $q = msq("SELECT `id` FROM `".$this->getSetting('table')."` ".$this->sqlstr.(($usl!='') ? $usl : '' )." ORDER BY ".$orderby." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));
                while ($r = msr($q)) $retval[] = $this->getPub($r['id']);
                return $retval;
        }
        function show_banner($section_id, $banner_id){

                $today_show=msr(msq("SELECT * FROM `pr_banners_shows` WHERE date(`date`)=date(NOW()) and `section_id`='".$section_id."' and `banner_id`='".$banner_id."'"));
                if ($today_show['num']>0)
                msq("UPDATE `pr_banners_shows` SET `num`=`num`+1 WHERE id=".$today_show['id']);
                else
                msq("INSERT INTO `pr_banners_shows` (`section_id`, `banner_id`, `date`, `num`) VALUES ('".$section_id."','".$banner_id."', date(NOW()), 1)");


                return;
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
        function getPlaces(){
                $retval = array();
                $retval[-1]='&nbsp';
				$q = msq("SELECT * FROM `site_site_pplaces_places_11` ORDER BY `floor_id`, `num`");
				while ($r = msr($q))
				{
			            $floor=msr(msq("SELECT * FROM `site_site_psprext_sprext_10` WHERE id=".$r['floor_id']));
			            $retval[$r['id']] = $floor['name'].' этаж: № '.$r['num'];

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
                        $err = $tface->preSave();
                        foreach ($err as $v) $errors[] = $v;
                        $dataset['types'][$k]['face'] = $tface;
                }


                if (count($errors)==0){                		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                         if ($pub['id']<1){
                                msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
                                $pub['id'] = mslastid();
                        }
                        foreach ($dataset['types'] as $dt){
                                if ($dt['name']=='place_id')
                        		{
                                 	$tface = $dt['face'];
	                                $tface->init(array('uid'=>floor($pub['id'])));
                                 	$place_floor=msr(msq("SELECT * FROM `site_site_pplaces_places_11` WHERE id=".$tface->getSetting('value')));
                                 	$floor=msr(msq("SELECT * FROM `site_site_psprext_sprext_10` WHERE id=".$place_floor['floor_id']));

                                 	$update.= (($update!='')?',':'')."`floor_id`='".$place_floor['floor_id']."', `place_num`='".$place_floor['num']."', floor_num='".$floor['dop1']."'";
                        		}

                        		if ($dt['name']!='floor_id')
                        		{

	                                $tface = $dt['face'];
	                                $tface->init(array('uid'=>floor($pub['id'])));
	                                $tface->postSave();
									$update.= (($update!='')?',':'').$tface->getUpdateSQL();
	                                $dataset['types'][$k]['face'] = $tface;
                                }
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

	                        <div class="place" style="margin-top: -50px;">
	                                <span style="float: right;">
	                                        <input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
	                                </span>
	                        </div>
                                <input type="hidden" name="editformpost" value="1">
                                <?
                                $places=$this->getPlaces();


                                $dataset = $this->getSetting('dataface');
                                foreach ($dataset['types'] as $dt){
                                        $tface = $dt['face'];

                                        if ($dt['name']=='place_id')
                                        {
                                        	print
		                                          '<div class="place" id="place_series_id" style="z-index: 15; width:50%;">
														<label>'.$dt['description'].(($dt['settings']['important']==1) ? ' <span class="important">*</span>' : '').'</label>';
														print getSelectSinonim('place_id',$places,$tface->getSetting('value'));
											print '</div>';
                                        }
                                        elseif ($dt['name']=='cat_id')
                                        {
                                        	$categs=getSprValuesOrder('/admin/shop_cat/', ' ORDER BY `id`');
                                        	print
		                                          '<div class="place" id="place_series_id" style="z-index: 15; width:50%;">
														<label>'.$dt['description'].(($dt['settings']['important']==1) ? ' <span class="important">*</span>' : '').'</label>';
														print getSelectSinonim('cat_id',$categs,$tface->getSetting('value'));
											print '</div>';
                                        }
                                        elseif ($dt['name']!='floor_id')
                                        {
	                                        $stylearray = array(
	                                            	"cat_id"=>'style="width:50%;"',
	                                            	"floor_id"=>'style="width:50%;"',
	                                            	"site"=>'style="width:33%;"',
	                                            	"tel"=>'style="width:33%;"',
	                                            	"email"=>'style="width:34%;"',
	                                            	"ptitle"=>'style="width:33%;"',
	                                                "pdescription"=>'style="width:33%; "',
	                                                "pseudolink"=>'style="width:34%;"'
	                                        );

	                                        $nospans = array("dop1","dop2","dop3","name", "ptitle", "pseudolink", "cat_id", "site", "email", "tel");
	                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));
                                        }
                                }
                        ?>
                        <div class="place">
                                <span style="float: right;">
                                        <input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
                                </span>
                        </div>
                        <span class="clear"></span>
                        </form>

		                <?
		                if ($pub['id']>0)
		                {

			                $MySqlConnect = new MySqlConnect;

			                $from = isset($_POST['from']) ? $_POST['from']: (isset($_GET['from']) ? $_GET['from'] : '');
                			$to = isset($_POST['to']) ? $_POST['to']: (isset($_GET['to']) ? $_GET['to'] : '');

                			$sqlstr='';

			              	if ($from!='')
                			$sqlstr.=" and `date`>='".$MySqlConnect->dateToDB($from)."'";

						    if ($to!='')
						    $sqlstr.=" and `date`<='".$MySqlConnect->dateToDB($to)."'";

						    if ($sqlstr=='') $limit=" LIMIT 30";

			                $show_kol=msr(msq("SELECT count(*) as cnt FROM `pr_banners_shows` WHERE `section_id`=".$section['id']." and `banner_id`=".$pub['id'].$sqlstr." ORDER BY ID DESC".$limit));


			                if ($show_kol['cnt']>0)
			                {


			                		?>
			                		<H1>Статистика:</H1>


			                		<form id="statistic" name="statistic" method="POST" enctype="multipart/form-data">
			                      		<div class="place" style="z-index: 10; width: 165px;">
											<label>Дата с</label>
											<div style="margin-top: 3px;"><input id="from" name="from" type="text" style="width: 80px; padding: 14px; border: 1px solid #D8D8D8; background-color:none; margin: 0 2px; float: left;" value="<?=htmlspecialchars($from)?>"/></div>
										</div>

										<div class="place" style="z-index: 10; width: 165px; margin-right: 2%;">
											<label>Дата по</label>
											<div style="margin-top: 3px;"><input id="to" name="to" type="text" style="width: 80px; padding: 14px; border: 1px solid #D8D8D8; background-color:none; margin: 0 2px; float: left;" value="<?=htmlspecialchars($to)?>"/></div>
										</div>


										<div class="place" style="width: 20%">
											<label>&nbsp;</label>
											<span class="forbutton">
												<input class="button" type="submit" value="Показать статистику за период" >
											</span>
										</div>
			                		</form>



			                		<table class="table-content stat">
			                			<tr>
			                				<th>Дата</th>
			                				<th>Просмотров</th>
			                				<th>Кликов</th>
			                			</tr>
			                		<?

			                		$stat_show=msq("SELECT * FROM `pr_banners_shows` WHERE `section_id`=".$section['id']." and `banner_id`=".$pub['id'].$sqlstr." ORDER BY ID DESC".$limit);
                                    $show_summ=0;
                                    $click_summ=0;

			                		while ($ss=msr($stat_show))
			                		{
                                    	$click_cnt=msr(msq("SELECT * FROM `pr_banners_clicks` WHERE `section_id`=".$section['id']." and `banner_id`=".$pub['id']." and `date`='".$ss['date']."'"));
                                    	?>
                                    	<tr>
			                				<td><?=$MySqlConnect->dateFromDBDot($ss['date'])?></td>
			                				<td><?=$ss['num']?></td>
			                				<td><?=floor($click_cnt['num'])?></td>
			                			</tr>
                                    	<?
                                    	$show_summ+=$ss['num'];
                                    	$click_summ+=$click_cnt['num'];
			                		}
			                		?>
			                			<tr>
			                				<td>ИТОГО</td>
			                				<td><?=$show_summ?></td>
			                				<td><?=$click_summ?></td>
			                			</tr>
			                		</table>
<script>
$(function() {
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

			                		<?
			                }

						}?>
                </div>

                <?
        }
        function drawPubsList(){
                global $SiteSections;
                $MySqlConnect = new MySqlConnect;
                $section = $SiteSections->get($this->getSetting('section'));
				if (isset($_POST['showsave'])){
					foreach ($_POST as $k=>$v){
						if (preg_match('|^pubshow\_[0-9]+$|',$k)){
							$p = preg_replace('|^pubshow\_([0-9]+)$|','\\1',$k);
							msq("UPDATE `".$this->getSetting('table')."` SET `show`='".(($_POST['checkshow'.$p]=='on')?'1':'0')."' WHERE `id`='$p'");
						}
					}
				}

				$search_name	= isset($_POST['search_name']) ? $_POST['search_name']: (isset($_GET['search_name']) ? $_GET['search_name'] : '');

                ?>
                <script>
                </script>
                <div id="content" class="forms">
                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
                        <form id="searchform" name="searchform" action="./?section=<?=$section['id']?>" method="POST">
							<div class="place" style="z-index: 10; width: 20%;">
								<label>Название или часть</label>
								<span class="input">
									<input type="text" name="search_name" maxlength="100" value="<?=$search_name?>"/>
								</span>
							</div>
							<div class="place" style="width: 8%;float: right;">
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
                                <p>Отсутствуют записи, удовлетворяющие заданным условиям</p>
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
                                                        <th class="t_32width">id</th>
                                                        <th class="t_nowrap">Наименование</th>
                                                        <th class="t_nowrap">Ссылка</th>
                                                        <th class="t_nowrap">Дата с</th>
                                                        <th class="t_nowrap">Дата по</th>
                                                        <th class="t_32width"></th>
                                                </tr>
                                        <?
                                        foreach ($list as $pub){
                                                ?>
                                                <tr>
                                                        <td class="t_32width"><input type="hidden" name="pubshow_<?=$pub['id']?>" value="1"><input type="checkbox" name="checkshow<?=$pub['id']?>" <?=($pub['show']>0)?'checked':''?> /></td>
                                                        <td class="t_32width"><?=$pub['id']?></td>
                                                        <td class="t_left"><a href="./?section=<?=$section['id']?>&pub=<?=$pub['id']?>"><?=htmlspecialchars($pub['name'])?></a></td>
                                                        <td class="t_left"><?=$pub['href']?></td>
                                                        <td class="t_left"><?=$MySqlConnect->dateFromDBDot($pub['startdate'])?></td>
                                                        <td class="t_left"><?=$MySqlConnect->dateFromDBDot($pub['enddate'])?></td>
                                                        <td class="t_32width">
                                                                <a href="./?section=<?=$section['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить запись')) return false;">
                                                                	<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить запись"/>
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
<?
/*ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);*/
class CCOrder extends VirtualContent
{
        public $urlstr;
		public $sqlstr;

        function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;

                $MySqlConnect = new MySqlConnect;

                $search_id = floor(isset($_POST['search_id'])?$_POST['search_id']:$_GET['search_id']);
                $search_phone = isset($_POST['search_phone']) ? $_POST['search_phone']: $_GET['search_phone'];
                $from = isset($_POST['from']) ? $_POST['from']: (isset($_GET['from']) ? $_GET['from'] : '');
                $to = isset($_POST['to']) ? $_POST['to']: (isset($_GET['to']) ? $_GET['to'] : '');

                if ($search_id>0)
                $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ')."`id`=".$search_id;


                if ($search_phone!='')
                $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ')."`tel` like '%".$search_phone."%'";

                if ($from!='')
                $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ')."`date`>='".$MySqlConnect->dateToDB($from)."'";

                if ($to!='')
                $this->sqlstr.=(($this->sqlstr =='') ? ' where ': ' and ')."`date`<='".$MySqlConnect->dateToDB($to)."'";

        }
        function getList($page=0){
                $retval = array();


                $page = floor($page);

                if ($page==-1) $this->setSetting('onpage',10000);

                if ($page<1) $page = 1;

                $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`".$this->sqlstr.""));
                $count = floor($count['c']);

                $this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));

                if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');

                $this->setSetting('page',$page);

                if (!$nolimit)
                $limit=" LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage');

                $q = msq("SELECT `id` FROM `".$this->getSetting('table')."`".$this->sqlstr." ORDER BY `date` DESC, `id` DESC ".$limit);
                while ($r = msr($q)) $retval[] = $this->getPub($r['id']);
                return $retval;
        }
        function GetOrderGoods($order)
        {
            $retval = array();
            $q = msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_goods` where `order_id`=$order");
            while ($r = msr($q)) $retval[] = $r;
            return $retval;
        }
        function getPub($id){
                $retval = array();
                $id = floor($id);
                if ($r = msr(msq("SELECT *, hour(`date`) as h, minute(`date`) m FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'")))
				foreach ($r as $k=>$v)
                $r[$k]=html_entity_decode(stripslashes($v));
                $retval= $r;
                return $retval;
        }
        function start(){
                // $q = msq("SELECT * FROM `".$this->getSetting('table')."`");
                // while ($r = msr($q)) $this->deletePub($r['id']);
                if (isset($_GET['pub'])){
                        global $CDDataSet;
                        $dataset = $CDDataSet->get($this->getSetting('dataset'));
                        $pub = $this->getPub(floor($_GET['pub']));
                        $this->drawAddEdit();
                }
                elseif (isset($_GET['showdouble'])){
                        global $CDDataSet;
                        $dataset = $CDDataSet->get($this->getSetting('dataset'));
                        $this->drawPubsList();
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
                if (count($errors)==0){
                        $pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
                        $rss = ($_POST['rss']=='on')?1:0;
                        if ($pub['id']<1){
                                msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
                                $pub['id'] = mslastid();
                        }
                        $update = '';
                        $tagss = '';
                        foreach ($dataset['types'] as $dt){
                                $tface = $dt['face'];
                                $tface->init(array('uid'=>floor($pub['id'])));
                                $tface->postSave();
                                $update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
                                $dataset['types'][$k]['face'] = $tface;
                        }

                        $tags = explode(',',$tagss);
                        $tagss = '|';
                        if (is_array($tags)) foreach ($tags as $t) if (trim($t)!='') $tagss.= trim($t).'|';
                        msq("UPDATE `".$this->getSetting('table')."` SET ".$update.",`tags`='$tagss',`rss`='".$rss."'".$tmpstr." WHERE `id`='".$pub['id']."'");
                }
                $this->setSetting('dataface',&$dataset);


                return $errors;
        }
        function drawAddEdit(){
                global $CDDataSet,$SiteSections;
                $MySqlConnect = new MySqlConnect;
                $section = $SiteSections->get($this->getSetting('section'));
                $pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);

                $list=$this->GetOrderGoods($pub['id']);
                ?>
                <div id="content" class="forms">
                        <h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <a href="./?section=<?=$section['id']?>"><?=$section['name']?></a> &rarr; <?=($pub['id']>0)?'Просмотр заказа':'Добавление'?></h1>
                        <strong>Заказ № <?=$pub['id']?> от <?=$MySqlConnect->dateFromDBDot($pub['date'])?>, <?=$MySqlConnect->TimeFromDB($pub['date'])?></strong>

	               		<div><?=$pub['client_address']?></div>
	               		<div><?=$pub['client_tel']?></div>
	               		<div><?=$pub['client_email']?></div>
	                    <div><?=(($pub['client_comment']!='') ? 'Дополнительно: '.$pub['client_comment'] : '')?></div>

                        <table class="table-content stat">
                                                <tr>
                                                        <th class="t_minwidth t_nowrap">№ блюда</th>
                                                        <th class="t_minwidth t_nowrap">Наименование</th>
                                                        <th class="t_minwidth t_nowrap">Количество</th>
                                                        <th class="t_minwidth t_nowrap">Цена</th>
                                                        <th class="t_minwidth t_nowrap">Сумма</th>
                                                </tr>
                                                <?
                                                foreach ($list as $ord)
                                                {
                                                ?>
                                                	<tr>
                                                		<td><?=$ord['good_num']?></td>
                                                		<td><?=$ord['good_name']?></td>
                                                		<td><?=$ord['kol']?></td>
                                                		<td><?=$ord['price']?></td>
                                                		<td><?=$ord['summ']?></td>
                                                	</tr>

                                                <?
                                                }
                                                ?>
                                                <tr>
                                                	<td colspan="4" style="text-align: left;"><strong>ИТОГО:</strong></td>
                                                	<td><strong><?=$pub['summ']?></strong></td>
                                                </tr>

                           </table>


           </div>
                <?

        }
        function drawPubsList(){

                global $SiteSections;
                $MySqlConnect = new MySqlConnect;

                $section = $SiteSections->get($this->getSetting('section'));

                $search_id = floor(isset($_POST['search_id'])?$_POST['search_id']:$_GET['search_id']);
                $search_phone = isset($_POST['search_phone']) ? $_POST['search_phone']: $_GET['search_phone'];
                $from = isset($_POST['from']) ? $_POST['from']: (isset($_GET['from']) ? $_GET['from'] : '');
                $to = isset($_POST['to']) ? $_POST['to']: (isset($_GET['to']) ? $_GET['to'] : '');



                $q = msq("SELECT * FROM `".$this->getSetting('table')."`".$conditions.((($conditions!='')?" AND ":" WHERE ")."(`tel` is NULL or `tel`='')"));
                while ($r = msr($q))
                if ($r['client_tel']!='')
         		{
                	$clear_tel=strtr(trim($r['client_tel']), array("-" => '', " " => '', "+7" => '8', "+" => '8', "342" => '', "(" => '', ")" => '', "[" => '', "]" => '', "{" => '', "}" => ''));
                	msq("UPDATE `".$this->getSetting('table')."` set `tel`='".$clear_tel."' where `id`=".$r['id']);
                }

                msq("UPDATE `".$this->getSetting('table')."` set `source`='телефон' where `source` is NULL and `summ`>0 and `accept`=1");



                if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$|",$searchfrom)) $searchfrom = '';
                if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$|",$searchto)) $searchto = '';


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
                        <h1><a href="./">Список <?=($this->getSetting('isservice')>0)?'сервисов':'разделов'?></a> &rarr; <?=$section['name']?></h1>

                        <form id="searchform" name="searchform" action="./?section=<?=$section['id']?>" method="POST">
                                			<div class="place" style="z-index: 10; width: 150px; ">
												<label>Номер заказа</label>
												<span class="input">
													<span class="bl"></span>
													<span class="bc"><input type="text" name="search_id" maxlength="20" value="<?=($search_id>0) ? $search_id : ''; ?>"/></span>
													<span class="br"></span>
												</span>
											</div>

											<div class="place" style="z-index: 10; width: 150px; ">
												<label>Телефон или часть</label>
												<span class="input">
													<span class="bl"></span>
													<span class="bc"><input type="text" name="search_phone" maxlength="20" value="<?=$search_phone;?>"/></span>
													<span class="br"></span>
												</span>
											</div>
											<div class="place" style="z-index: 10; width: 165px;">
												<label>Дата с</label>
												<div style="margin-top: 3px;"><input id="from" name="from" type="text" style="width: 80px; padding: 14px; border: 1px solid #D8D8D8; background-color:none; margin: 0 2px; float: left;" value="<?=htmlspecialchars($from)?>"/></div>
											</div>

											<div class="place" style="z-index: 10; width: 165px; margin-right: 2%;">
												<label>Дата по</label>
												<div style="margin-top: 3px;"><input id="to" name="to" type="text" style="width: 80px; padding: 14px; border: 1px solid #D8D8D8; background-color:none; margin: 0 2px; float: left;" value="<?=htmlspecialchars($to)?>"/></div>
											</div>

                                <div class="place" style="width: auto; margin-right: 20px;">
                                        <label>&nbsp;</label>
                                        <span class="forbutton">
                                                <span class="button">
                                                        <span class="bl"></span>
                                                        <span class="bc">Найти</span>
                                                        <span class="br"></span>
                                                        <input type="submit" value=""/>
                                                </span>
                                        </span>
                                </div>
                                <span class="clear"></span>
                                <input type="hidden" name="searchaction" value="1">
                        </form>
                        <br/>

                        <?
                        $list = $this->getList($_GET['page']);

                        if (count($list)==0){
                                ?>
                                <p>Отсутствуют записи, удовлетворяющие заданным условиям</p>
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
                                        <?
                                        if ($searchfrom!='') print '
                                                <input type="hidden" name="searchfrom" value="'.htmlspecialchars($searchfrom).'">';
                                        if ($searchto!='') print '
                                                <input type="hidden" name="searchto" value="'.htmlspecialchars($searchto).'">';
                                        ?>
                                        <table class="table-content stat">
                                                <tr>
                                                        <th class="t_32width">&nbsp;</th>
                                                        <th class="t_minwidth t_nowrap">№</th>
                                                        <th class="t_minwidth t_nowrap">Дата</th>
                                                        <th class="t_minwidth t_nowrap">Время</th>
                                                        <th class="t_minwidth t_nowrap">Сумма</th>
                                                        <th class="t_nowrap">Телефон</th>
                                                        <th class="t_nowrap">Заказ</th>
                                                        <th class="t_32width"></th>
                                                </tr>
                                        <?
                                        foreach ($list as $pub){
                                                ?>
                                                <tr>
                                                        <td class="t_left">
                                                        	<a href="#" onclick="accept_order('<?=$pub['id']?>', 'accept_order'); return false;">
	                                                        	<img id="onoff_<?=$pub['id']?>" src="/pics/editor/<?=($pub['accept']==0) ? 'status-disabled.gif' : (($pub['show']==0) ? 'off.gif' : (($pub['deliv_h']>0 && $pub['deliv_m']>0) ? 'valid.png' : 'on.gif' ))?>" title="Изменить статус заказа">
                                                        	</a>
                                                        </td>

                                                        <td class="t_left"><nobr><?=$pub['id']?></nobr></td>
                                                        <td class="t_minwidth t_nowrap">
                                                        	<?=$MySqlConnect->dateFromDBDot($pub['date'])?>
                                                        	<?
                                                        	if ($pub['date_delivery']!='')
                                                        	print '<div><strong>предзаказ</strong></div>';
                                                        	?>
                                                        </td>
                                                        <td class="t_minwidth t_nowrap"><?=$MySqlConnect->TimeFromDB($pub['date'])?></td>
                                                        <td class="t_left">
                                                        	<div><?=htmlspecialchars($pub['summ'])?><?=($pub['discount_summ']>0) ? " [-".$pub['discount_summ']."]" : ""?></div>
                                                        </td>
                                                        <td class="t_left">
                                                       		<div>Имя: <?=$pub['name']?></div>
                                                       		<div>Адрес: <?=$pub['address']?></div>
                                                       		<div>Телефон: <?=$pub['tel']?></div>
                                                       		<div>email: <?=$pub['email']?></div>
                                                       		<div>Комментарий: <?=$pub['comment']?></div>
                                                        </td>
                                                        <td class="t_left">


                                                        	<?
                                                        	$goods=$this->GetOrderGoods($pub['id']);
                                                        	foreach ($goods as $g)
                                                        	{                                                        		?><div><?=$g['good_name']?>&nbsp;&nbsp;|&nbsp;&nbsp;<?=$g['price']?> руб.</div><?
                                                        	}
                                                        	?>
                                                        </td>
                                                        <td class="t_32width">
                                                                <a href="./?section=<?=$section['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Вы действительно хотите удалить заказ? Все данные по этому заказу будут утеряны')) return false;">
                                                                        <span class="bl"></span>
                                                                        <span class="bc"></span>
                                                                        <span class="br"></span>
                                                                        <input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить заказ"/>
                                                                </a>
                                                        </td>
                                                </tr>
                                                <?
                                        }
                                        ?>
                                        </table>
                                        <span class="clear"></span>
                                        <?
                                         $href = '?section='.$section['id'].$this->urlstr;
                                        if ($search_source!='') $href.= '&search_source='.$search_source;
                                        if ($search_phone!='') $href.= '&search_phone='.$search_phone;
                                        if ($search_status!='') $href.= '&search_status='.$search_status;

                                        ?>
                                        <div class="place">
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
                        msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
                        return true;
                }
                return false;
        }
        function delete(){
/*                global $CDDataSet;
                $q = msq("SELECT * FROM `".$this->getSetting('table')."`");
                while ($r = msr($q)) $this->deletePub($r['id']);
                msq("DROP TABLE `".$this->getSetting('table')."`");*/
                return true;
        }
}
?>
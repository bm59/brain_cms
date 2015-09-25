<?

class CCBlogs extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array();/* Где нет в названии "name", но нужен поиск по like*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();


   }
   function getList($page=0){

   	$retval = array();

   	$q = "SELECT tab.*, blogers.`name` as bloger_name FROM `".$this->getSetting('table')."` tab, `site_site_universal_universal_27` blogers ".$this->sqlstr.(($this->sqlstr=='') ? ' WHERE ':' and ').' tab.bloger_id=blogers.id';

   	$count = msq($q);
   	$count = mysql_num_rows($count);

   	$page = floor($page);
   	if ($page==-1 || isset($this->Settings['settings_personal']['no_paging'])) $this->setSetting('onpage',10000);
   	if ($page<1) $page = 1;


   	$this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
   	$this->setSetting('count',ceil($count));

   	if ($this->getSetting('pagescount')>0 && $page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
   	$this->setSetting('page',$page);


   	if ($_GET['sort']!='')
   	{
   		$order_by="ORDER BY `".$_GET['sort']."` ".$_GET['sort_type'];
   	}
   	else
   		$order_by=$this->Settings['settings_personal']['default_order']!='' ? $this->Settings['settings_personal']['default_order'] : "ORDER BY `id` DESC";


   	$this->order_by=$order_by;
   	$q = msq($q." ".$order_by." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));


   	while ($r = msr($q)) $retval[] = $r;

   	return $retval;
   }
   function drawPubsList(){
   	global $SiteSections, $CDDataSet, $CDDataType;

   	$this->generateMeta('name');

   	$dataset = $this->getSetting('dataface');

   	$section = $SiteSections->get($this->getSetting('section'));

   	if (isset($_POST['showsave'])){
   		foreach ($_POST as $k=>$v){
   			if (preg_match('|^prec\_[0-9]+$|',$k)){
   				$p = preg_replace('|^prec\_([0-9]+)$|','\\1',$k);
   				msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='".floor($_POST['prec_'.$p])."' WHERE `id`='$p'");
   			}
   		}
   		$this->updatePrecedence();
   	}
   	?>
   		    	<div id="content" class="forms">
   		    	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		        <form name="searchform" action="" method="POST">
   					<?
   					$search_fields_cnt=0;
   					?>
   					<!-- Влючен\Отключен -->
   					<?if (isset($this->Settings['settings_personal']['onoff'])){?>
   					<div class="place" style="z-index: 10; width: 10%;">
   						<label>Включен</label>
   						<?
   						if (!isset($this->search_show)) $this->search_show='-1';
   						$vals=array('-1'=>'','0'=>'Отключен', '1'=>'Включен');
   						print getSelectSinonim('search_show',$vals,$_POST['search_show'],true);

   						$search_fields_cnt++;
   						?>
   					</div>
   					<?}?>

   					<!-- Поля для поиска -->
   					<?
   					$search_fields=array();
   					foreach ($dataset['types'] as $dt)
   					{
   						if (isset($dt['settings']['show_search']) && !isset($dt['settings']['off']))
   						{
   							$search_fields[]=$dt['name'];
   							$search_fields_cnt++;
   						}
   					}

   					foreach ($search_fields as $sf){
   						$CDDataType->get_search_field($dataset['types'][$sf],$search_fields_cnt);
   					}

   					if ($search_fields_cnt>0)
   					{
   					?>
   					 <div class="place" style="width: 8%;margin-left: 2%;">
   						<label>&nbsp;</label>
   						<span class="forbutton">
   							<span>
   								<input class="button" type="submit" value="Найти" >
   							</span>
   						</span>
   					</div>
   					<span class="clear"></span>
   					<?
   					}

   					?>
   				</form>
   				<div class="hr"><hr/></div>
   		                        <?
   		                        $list = $this->getList($_GET['page'], array(), '', $searchnumgood, $searchtextgood);
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
   		                         print 'Всего записей: '.$this->getSetting('count');
   		                         $Storage = new Storage;
   		                         $Storage ->init();
   		                                ?>
   		                                <form id="showsave" class="showsave" name="showsave" action="./?section=<?=$section['id']?><?=($this->getSetting('page')>1)?'&page='.$this->getSetting('page'):''?>" method="POST">
   		                                        <?
   		                                        /* Поля отображаемые в таблице */
   		                                        $show_fields=array();

   		                                        foreach ($dataset['types'] as $dt)
   		                                        {
   		                                        	if (isset($dt['settings']['show_list']) && !isset($dt['settings']['off']))
   		                                        	$show_fields[]=$dt['name'];
   		                                        }




   		                                        ?>
   		<script>
   		var session_id = '<?php echo session_id(); ?>';
   		$(function() {
   		    $(document).on('click','.onoff', function() {
   		        var id=$(this).attr("data-id");
   		        var elem=$(this);
   				if (id>0)
   				{
   					$.ajax({
   			            type: "POST",
   			            url: "/inc/site_admin/pattern/ajax_class.php",
   			            data: "action=onoff&id="+id+"&table=<?=$this->getSetting('table')?>&session_id="+session_id,
   			            dataType: 'json',
   			            success: function(data){
   			            	elem.children('img').attr('src', '/pics/editor/'+data.signal);
   			            }
   			        });
   				}

   		        return false;
   		    });
   		});
   		</script>
   		<table class="table-content stat">
   		<tr class="template">

   		<?
   		$table_th=array();

   		if (isset($this->Settings['settings_personal']['onoff'])) 		$table_th[]=array('name'=>'show', 'description'=>'Вкл', 'class'=>'t_minwidth t_center');
   		if (isset($this->Settings['settings_personal']['show_id']))		$table_th[]=array('name'=>'id', 'description'=>'№', 'class'=>'t_minwidth  t_center');
   		if (isset($this->Settings['settings_personal']['precedence']))	$table_th[]=array('name'=>'precedence', 'description'=>'Порядок', 'class'=>'t_32width');

   		foreach($show_fields as $sf){
   			$set=$dataset['types'][$sf]['face']->Settings;
   			$table_th[]=array('name'=>$set['name'], 'description'=>$set['description'], 'class'=>$set['settings']['list_class']);
   		}



   		$table_th[]=array('name'=>'bloger_name', 'description'=>'Блогер', 'class'=>'t_minwidth');

   		/* Редактирование и удаление */
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');
   		$table_th[]=array('name'=>'', 'description'=>'', 'class'=>'t_minwidth');




   			foreach($table_th as $th){
   				$sort_button='';
   				$active_sort='';


   				if ($th['name']!='')
   				{
   					$type_sort='down';
   					if (stripos($this->order_by, '`'.$th['name'].'`')!==false)
   					{
   						$active_sort=' active';
   						$type_sort=stripos($this->order_by,'DESC') ? 'down':'up';
   					}

   					$sort_button='<a class="sort '.$type_sort.$active_sort.'" href="?section='.$section['id'].(($_GET['page']>1) ? '&page='.$_GET['page']:'').$this->urlstr.'&sort='.$th['name'].'&sort_type='.(($type_sort=='down') ? 'ASC':'DESC').'"></a>';
   				}


   				?>
   				<th <?=$th['class']!='' ? 'class="'.$th['class'].'"' :'' ?>>
   					<div><div><?=$th['description']?></div><div style="height: 8px;"><?=$sort_button?></div></div>
   				</th>
   				<?
   			}

   		?>
   		</tr>
   		<?
   		/* Поля ктр. дублируют ссылку на редактирование */
   		$editlink_double=array('name');
   		$blogers=getSprValuesOrder('/sitecontent/blogs/bloggers/',' ORDER by `name`');

   		foreach ($list as $pub)
   		{
   			?>
   		<tr>

   			<!-- Вкл. Откл -->
   			<?if (isset($this->Settings['settings_personal']['onoff'])){?>
   				<td class="t_minwidth  t_center">
   					<a href="#" onclick="return false;" class="onoff" data-id="<?=$pub['id']?>">
   						<img id="onoff_<?=$pub['id']?>" src="/pics/editor/<?=$pub['show']==0 ? 'off.png' : 'on.png'?>" title="<?=$pub['show']==0 ? 'Отключена' : 'Включена'?>" style="display: inline;">
   					</a>
   				</td>
   			<?}?>


   			<!-- ID, порядок -->
   			<?if (isset($this->Settings['settings_personal']['show_id'])){?>		<td class="t_minwidth  t_center"><?=$pub['id'] ?></td><?}?>
   			<?if (isset($this->Settings['settings_personal']['precedence'])){?>		<td class="t_32width  t_center"><input type="text" name="prec_<?=$pub['id']?>" value="<?=floor($pub['precedence'])?>"/></td><?}?>


   			<!-- Видимые поля -->
   			<?
   			$custom_settings=array();


   			if (array_search('bloger_id',$show_fields)!==false)
   			unset($show_fields[array_search('bloger_id',$show_fields)]);
   			$show_fields[]='bloger_id';
   			$custom_settings['bloger_id']=array('settings'=>array('name'=>'bloger_id', 'val'=>$blogers[$pub['bloger_id']]));

   			foreach($show_fields as $sf)
   			{
   				$set=$dataset['types'][$sf]['face']->Settings;

   				if (!@is_array($set) && @is_array($custom_settings[$sf])) $set=$custom_settings[$sf];

   				if (isset($set['settings']['val'])) $pub[$sf]=$set['settings']['val'];

   				$href=array();
   				if (in_array($sf,$editlink_double)) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
   				?>
   				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?>>
   					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf]);?><?=$href[1]?>
   				</td>
   			<?}?>


   			<!-- Редактировать, Удалить -->
   			<td class="t_minwidth">
   				<a class="button txtstyle" href="/manage/control/contents/?section=<?=$section['id']?>&pub=<?=$pub['id']?>" title="Редактировать"><img src="/pics/editor/prefs.gif" alt="Редактировать"></a>
   			</td>
   			<td class="t_minwidth">
   				<a href="./?section=<?=$section['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить запись')) return false;">
   				<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить запись"/>
   				</a>
   			</td>
   		</tr>
   		<?}?>
   		                                        </table>
   		                                        <span class="clear"></span>
   		                                        <div class="place">
   		                                        <?if (isset($this->Settings['settings_personal']['precedence'])){?>
   		                                                <span>
   		                                                	<input class="button big" type="submit" name="showsave" value="Сохранить порядок" />
   		                                                </span>
   		                                        <?} ?>
   		                                                <a href="./?section=<?=$section['id']?>&pub=new" class="button big" style="float: right;">Добавить</a>
   		                                        </div>
   		                                        <span class="clear"></span>
   		                                </form>
   		                                <span class="clear"></span>
   		                                <?
   		                                $pagescount=$this->getSetting('pagescount');
   		                                if(!$_GET['page']>0) $_GET['page']=1;

   		                                if ($pagescount>1 && $_GET['id']==''){
   		                                	?>
   												<div class="hr"><hr/></div>
   												<div id="paging" class="nopad">
   													<?
   													$dif=5;

   													$href = '?section='.$section['id'].$this->urlstr;
   													if ($_REQUEST['sort']!='') $href .='&sort='.$_REQUEST['sort'];
   													if ($_REQUEST['sort_type']!='') $href .='&sort_type='.$_REQUEST['sort_type'];

   													if ($_GET['page']>$dif/2+1) print '<a href="'.$href.'">В начало</a>';

   													for ($i=1; $i<=$pagescount; $i++)
   													{
   														$inner = '';
   					        							$block = array('<a href="'.$href.'&page='.$i.'">','</a>');


   					        							if (
   					        									($i>($_GET['page']-($dif/2))) && ($i<($_GET['page']+($dif/2)))

   					        									|| ($i<=$dif && $_GET['page']<=$dif-$dif/2)
   					        									|| ($i>$pagescount-$dif && $_GET['page']>$pagescount-$dif/2+1)

   					        								)
   					        							{
   					        								$inner = $i;
   					        								if ($i==$_GET['page']) $block = array('<span>','</span>');
   					        							}

   					        							if ($inner!='') print $block[0].$inner.$block[1];
   													}

   													if ($_GET['page']!=$pagescount && $pagescount>1) print '<a href="'.$href."&page=".($_GET['page']+1).'">Следующая</a>';
   				        							if ($_GET['page']<$pagescount && $pagescount>$dif) print '<a href="'.$href."&page=".($_GET['page']+1).'">Последняя</a>';
   													?>
   												</div>
   		                                	<?
   		                                }
   		                        }
   		                        ?>
   		                </div>
   		  			</div>
   		                <?
   		        }
   function save(){
   	$errors = array();
   	$dataset = $this->getSetting('dataface');
   	foreach ($dataset['types'] as $k=>$dt){

   		if (!isset($dt['settings']['off']))
   		{
   			$tface = $dt['face'];
   			$err = $tface->preSave();
   			foreach ($err as $v) $errors[] = $v;
   			$dataset['types'][$k]['face'] = $tface;
   		}

   	}

   	if ($_POST['bloger_id']<=0) $errors[]='Заполните поле «Блогер»';

   	if (count($errors)==0){
   		$update = '';
   		$pub = $this->getPub($_GET['pub']); $pub['id'] = floor($pub['id']);
   		if ($pub['id']<1){
   			$count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
   			$count = floor($count['c']);
   			$update.= (($update!='')?',':'')."`precedence`='$count'";
   			msq("INSERT INTO `".$this->getSetting('table')."` (`show`) VALUES ('1')");
   			$pub['id'] = mslastid();
   		}

   		foreach ($dataset['types'] as $dt)
   		{
   			$tface = $dt['face'];
   			$tface->init(array('uid'=>floor($pub['id'])));
   			$tface->postSave();
   			$update.= (($update!='')?',':'').$tface->getUpdateSQL((int)$_GET['section']);
   			$dataset['types'][$k]['face'] = $tface;
   		}


   		$update.=",`bloger_id`='".$_POST['bloger_id']."'";

   		if ($rub_ids!='') $update.=', `rubrics`="'.$rub_ids.'"';
   		msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");

   		WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
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
   		                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		                        <p class="impfields">Поля, отмеченные знаком «<span class="important">*</span>», обязательные для заполнения.</p>
   		                        <form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
   		                                <input type="hidden" name="editformpost" value="1">
   		                                <?
   		                                $stylearray = array(
   		                                		"ptitle"=>'style="width:32%; margin-right:2%;"',
   		                                		"pdescription"=>'style="width:32%; margin-right:2%;"',
   		                                		"pseudolink"=>'style="width:32%;"'
   		                                );
   		                                $nospans = array("ptitle","pdescription");


   		                                $dataset = $this->getSetting('dataface');
   		                                foreach ($dataset['types'] as $dt)
   		                                {
   		                                		$tface = $dt['face'];

   		                                		if ($dt['name']=='name' && !$bloger['id']>0)
   		                                		{
   		                                			$blogers=getSprValuesOrder('/sitecontent/blogs/bloggers/',' ORDER by `name`');
   		                                			$val=((isset($_POST['bloger_id'])) ? $_POST['bloger_id'] : $pub['bloger_id']);
   		                                			?>
   		                                		<div class="place" style="width:48%; margin-right:2%;">
   		                                			<label>Блогер <span class="important">*</span></label>
   		                                			<?print getSelectSinonim('bloger_id',$blogers,$val);?>
   		                                		</div>
   		                                		<?
   		                                		}



   		                                        if (isset($dt['setting_style_edit']['css'])) $stylearray[$dt['name']]='style="'.$dt['setting_style_edit']['css'].'"';
   		                                        if (isset($dt['settings']['nospan'])) $nospans[]=$dt['name'];

   		    									if (!isset($dt['settings']['off']))
   		                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));

   		                                }
   		                        ?>


   		                        </table>
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


}
?>
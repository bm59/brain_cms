<?
/*
Базовый класс функционала разделов
*/
class VirtualContent
{
	var $Settings = array();
	public $urlstr;
	public $sqlstr;
	public $editor_cnt=0;

	function init($settings = array()){
		if (is_array($settings)){
			foreach ($settings as $name=>$value) $this->setSetting($name,$value);
		}
		if ($dir = @opendir($_SERVER['DOCUMENT_ROOT']."/inc/cclasses/")){
			while ($file = readdir($dir)){
				if ($file && $file!=".." && $file!="."){
					if ((preg_match('|.*\.php$|',$file)) && ($file!='VirtualContent.php')) include_once($_SERVER['DOCUMENT_ROOT']."/inc/cclasses/".$file);
				}
			}
			closedir($dir);
		}
	}
	function ExportStatXls($query){
		global $MySqlObject;

		deleteTempFiles('/storage/xls/');

		include_once($_SERVER['DOCUMENT_ROOT']."/inc/excel/PHPExcel.php");
		include_once($_SERVER['DOCUMENT_ROOT']."/inc/excel/PHPExcel/Writer/Excel2007.php");

		// Create new PHPExcel object

		$objPHPExcel = new PHPExcel();



		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B2', iconv('windows-1251', 'utf-8', 'Дата'))
		->setCellValue('C2', iconv('windows-1251', 'utf-8', 'Показов'))
		->setCellValue('D2', iconv('windows-1251', 'utf-8', 'Кликов'))
		->setCellValue('E2', iconv('windows-1251', 'utf-8', 'Уникальных'));


		$styleHeader = array('font'=> array('bold'=>true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		foreach(range('B','E') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($styleHeader);
		}

		foreach(range('B','E') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
			->setAutoSize(true);
		}

		$objPHPExcel->getActiveSheet()->setTitle('statistics');

		$i=2;
		while ($r=msr($query))
		{
			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$i, iconv('windows-1251', 'utf-8', $MySqlObject->dateFromDBDot($r['date'])))
			->setCellValue('C'.$i, iconv('windows-1251', 'utf-8', $r['show']))
			->setCellValue('D'.$i, iconv('windows-1251', 'utf-8', $r['click']))
			->setCellValue('E'.$i, iconv('windows-1251', 'utf-8', $r['unique']));
		}

		$styleArray = array(
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);

		$objPHPExcel->getActiveSheet()->getStyle('B2:E'.$i)->applyFromArray($styleArray);


		// Save Excel 2007 file
		$file_name="temp_".time().".xlsx";
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($_SERVER['DOCUMENT_ROOT']."/storage/xls/".$file_name);

		?>
			<script>
			window.location.href = "<?="/storage/xls/".$file_name?>";
			</script>
			<?
	}
	function drawPubsList($param=''){
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
		        	<input type="hidden" name="searchaction">
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
		                        $list = $this->getList($_GET['page']);
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
			foreach($show_fields as $sf)
			{
				$set=$dataset['types'][$sf]['face']->Settings;
				$href=array();
				if (in_array($sf,$editlink_double) && !isset($set['settings']['editable'])) $href=array('<a href="/manage/control/contents/?section='.$section['id'].'&pub='.$pub['id'].'" title="Редактировать">', '</a>');
				?>
				<td <?=$set['settings']['list_class']!='' ? 'class="'.$set['settings']['list_class'].'"' : ''?> <?=$set['settings']['list_style']!='' ? 'style="'.$set['settings']['list_style'].'"' : ''?>>
					<?=$href[0]?><?=$CDDataType->get_view_field($dataset['types'][$sf],$pub[$sf], $pub);?><?=$href[1]?>
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
		                        
		                        if ($param)
		                        $this->get_txt_export();
		                        ?>
		                </div>
		  			</div>
		                <?
		}
		function get_txt_export(){
			$exts='txt';
			?>
			<style type="text/css">
#add_file {padding-top: 10px;}
#add_file img {display: inline-block; padding-left: 30px;}
#upl_error {color: red; padding-top: 10px;}
#loading {padding-top: 10px;}
			</style>
			<script>
        $(function(){
        var btnUpload=$('#upl_button');
        var status=$('#upl_status');
        var error=$('#upl_error');
        var old_file='';
        var upload_me=new AjaxUpload(btnUpload, {
            action: '/uploader_txt.php',
            responseType: 'json',
            name: 'upl_file',
            data: {sid : '<?=session_id()?>', section_id: '<?=$_GET['section'] ?>'},
            onSubmit: function(file, ext){
            	status.hide();
                <?if ($exts!=''){?>
                if (! (ext && /^(<?=strtolower(str_replace(', ', '|', $exts))?>)$/.test(ext))){
                    error.html('<nobr>Допустимые форматы: <?=strtolower($exts)?></nobr>');
                    return false;
                }
                <?}?>
                $('#file').fadeOut(0);
                $('#loading').attr('src', '/pics/loading.gif').fadeIn(0);
            },
            onComplete: function(file, response){
                status.html('');
                error.html('');
                $('#file').html('');
                if(response.result==="ok"){
                    $('#loading').fadeOut(0);
                    upload_me.setData({sid : '<?=session_id()?>'});
                    status.html(response.comment+'&nbsp;&nbsp;&nbsp;<a href="/manage/control/contents/?section=<?=$_GET['section'] ?>">Обновить страницу</a>');
                    status.show();
                }else{
                    status.html(response.error);
                    $('#loading').fadeOut(0);

                }
            }
        });
    });
			</script>
			<div class="hr"><hr/></div>
			<div class="place">
			<label>Импорт из txt файла</label>
			<small>Каждое значение должно быть в отдельной строке</small>
			<input type="hidden" id="uploadfilehidden_txt" name="upload_txt" value="upload_txt">

             	<span class="clear"></span>
				<div class="contentdesc"></div>

			<div class="clear"></div>
			<div id="add_file" style="float: left;">
			        <a id="upl_button" class="button">загрузить файл</a>
			        <div class="clear"></div>
			        <img id="loading" src="/pics/inputs/loading2.gif" height="28" style="display: none;" />
			         <div id="upl_error"></div>
			         <div class="clear"></div>
			         <div id="upl_status"></div>
			         <input type="hidden" name="<?=$this->getSetting('name')?>" id="<?=$this->getSetting('name')?>" value="<?=stripslashes($this->getSetting('value'))?>">
			    </div>
		    </div>
		    <div class="clear"></div>
			<?
		}
		function drawAddEdit(){
			global $CDDataSet,$SiteSections, $multiple_editor;
			$section = $SiteSections->get($this->getSetting('section'));

			$SectionPattern = new $section['pattern'];
			$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));

			$init_pattern=$Iface->getSetting('pattern');

			if ($this->editor_cnt>1)
			{
				$multiple_editor=true;
				?><script type="text/javascript" src="/js/tinymce/tinymce.js"></script><?
			}

			$pub = $this->getPub($_GET['pub']);
			$pub['id'] = floor($pub['id']);
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

		                                        if (isset($dt['setting_style_edit']['css'])) $stylearray[$dt['name']]='style="'.$dt['setting_style_edit']['css'].'"';
		                                        if (isset($dt['settings']['nospan'])) $nospans[]=$dt['name'];

		    									if (!isset($dt['settings']['off']))
		                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));


		    									/* Подсказки у поля в паттерне: $this->setSetting('type_settings_settings', array('min_w|'=>'Минимальная ширина')); */
		    									if (count($init_pattern->Settings['type_settings_'.$dt['name']])>0)
		    									{
		    										foreach ($init_pattern->Settings['type_settings_'.$dt['name']] as $k=>$v)
		    										print $k.' - '.$v.'<br/>';

		    										?><div class="clear"></div><?
		    									}


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
		                if (isset($this->Settings['settings_personal']['reklama']))
		                include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/reklama/add_pattern.php");
	}
	function start(){
		global $CDDataSet;

		$dataset = $CDDataSet->get($this->getSetting('dataset'));
		$imagestorage = $this->getSetting('imagestorage');

		if ($_GET['pub']>0)
		$pub = $this->getPub(floor($_GET['pub']));
		foreach ($dataset['types'] as $k=>$dt){

			if ($dt['type']=='CDTextEditor')
			$this->editor_cnt++;
			$tface = new $dt['type'];
			$tface->init(array('name'=>$dt['name'],'value'=>$pub[$dt['name']], 'uid'=>floor($pub['id']),'imagestorage'=>floor($imagestorage['id']),'description'=>$dt['description'],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'), 'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'settings'=>$dt['settings']));
			$dataset['types'][$k]['face'] = $tface;
		}


		if (isset($_GET['pub'])){

			$this->setSetting('dataface',$dataset);
			if (floor($_POST['editformpost'])==1){
				$this->setSetting('saveerrors',$this->save());
				if (count($this->getSetting('saveerrors'))==0){
					unset($_GET['pub']);
					$this->start();
					return;
				}
			}
			if (!isset($_POST['searchaction']))
				$this->drawAddEdit();
			else $this->drawPubsList();
		}
		else{
			if (floor($_GET['delete'])>0) $this->deletePub($_GET['delete']);
			$this->setSetting('dataface',$dataset);
			$this->drawPubsList();
		}
	}
	function getSearch(){
		global $MySqlObject;

		foreach ($_REQUEST as $k=>$v)
			if (stripos($k,'search')!==false && $v!='' && $v!='-1')
				if (!in_array($k,$this->no_auto))
				if (strpos($k, 'nouse_')===false)
				{
					$this->$k=$v;

					$mysql_k=strtr($k,$this->field_tr);
					$mysql_k=strtr($mysql_k,$this->field_change);

					$this->urlstr.='&'.$k.'='.$v;

					if (!in_array($mysql_k,$this->field_change)) $mysql_k='`'.$mysql_k.'`';

					if ($this->sqlstr =='') $sql_pref=' WHERE ';  /*!!!Заменить на WHERE если нет других условий*/
					else $sql_pref=' and ';


					if ($_REQUEST['nouse_'.$k.'_type']=='CDCHOICE')
					$this->sqlstr.=$sql_pref.$mysql_k." like '%,".$v.",%'";
					elseif ((stripos($k,'name')!==false || in_array($k,$this->like_array)) and !in_array($k,$this->not_like_array))
					$this->sqlstr.=$sql_pref.$mysql_k." like '%".$v."%'";
					elseif (stripos($k, '_from') && $v!='')
					$this->sqlstr.=$sql_pref.$mysql_k.">='".$MySqlObject->dateToDB($v)."'";
					elseif (stripos($k, '_to') && $v!='')
					$this->sqlstr.=$sql_pref.$mysql_k."<='".$MySqlObject->dateToDB($v)."'";
					else $this->sqlstr.=$sql_pref.$mysql_k."='".$v."'";

				}
	}
	function generateMeta($field='', $dop_title='')
	{
		$pub=msr(msq("SELECT * FROM `".$this->getSetting('table')."` LIMIT 1"));
		if (@array_key_exists('pseudolink',$pub))
		{
			$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE 	`pseudolink`='' or `pseudolink` is NULL");
			while ($r = msr($q))
			{
				$num=$i=0;
				while ($num==0)
				{
					$i++;
					$double=msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `pseudolink`='".get_url_text($r[$field]).(($i>1) ? "_".$i:"")."' LIMIT 1"));
					if (!$double['id']>0)
					{
						msq("UPDATE `".$this->getSetting('table')."` SET `pseudolink`='".get_url_text($r[$field]).(($i>1) ? "_".$i:"")."' WHERE id=".$r['id']);
						$num=$i;
					}

				}
			}

		}

		if (@array_key_exists('ptitle',$pub))
		{
			$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE 	`ptitle`='' or `ptitle` is NULL");
			while ($r = msr($q))
			{
				msq("UPDATE `".$this->getSetting('table')."` SET `ptitle`='".$r[$field].$dop_title."' WHERE id=".$r['id']);
			}
		}

		if (@array_key_exists('pdescription',$pub))
		{
			$q = msq("SELECT * FROM `".$this->getSetting('table')."` WHERE 	`pdescription`='' or `pdescription` is NULL");
			while ($r = msr($q))
			{
				msq("UPDATE `".$this->getSetting('table')."` SET `pdescription`='".$r[$field].$dop_title."' WHERE id=".$r['id']);
			}
		}

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
	function incField($field_name='', $id=0){
		if ($field_name=='' || !$id>0) return false;
		$r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"));

		if ($r['id']>0)
		msq("UPDATE `".$this->getSetting('table')."` SET `$field_name`='".(floor($r[$field_name])+1)."' WHERE id=".$r['id']." LIMIT 1");

		return $retval;
	}
	function getPubByField($field,$value){
		$retval = array();
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `".$field."`='".$value."'")))

		foreach ($r as $k=>$v)
		$r[$k]=html_entity_decode(stripslashes($v));

		$retval= $r;
		return $retval;
	}
	function updatePrecedence(){
		$precedence = 0;
		$q = msq("SELECT `id` FROM `".$this->getSetting('table')."` ORDER BY `precedence` ASC");
		while ($r = msr($q)){
			msq("UPDATE `".$this->getSetting('table')."` SET `precedence`='$precedence' WHERE `id`='".$r['id']."'");
			$precedence++;
		}
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


			msq("UPDATE `".$this->getSetting('table')."` SET ".$update." WHERE `id`='".$pub['id']."'");



			WriteLog($pub['id'], (($_GET['pub']=='new') ? 'добавление':'редактирование').' записи', '','','',$this->getSetting('section'));
		}

		$this->setSetting('dataface',$dataset);
		return $errors;
	}
    function getList($page=0, $str_usl='', $str_order_by=''){

        	$retval = array();

        	$q = "SELECT * FROM `".$this->getSetting('table')."`".$this->sqlstr.$str_usl;

        	$count = msq($q);
        	$count = @mysql_num_rows($count);

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

        	if ($str_order_by) $order_by=$str_order_by;


        	$this->order_by=$order_by;
        	$q = msq($q." ".$order_by." LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));

        	while ($r = msr($q)) $retval[] = $r;

        	return $retval;
    }
	function delete(){
		global $CDDataSet;
		$pattern=$this->getSetting('pattern');

		$q = msq("SELECT * FROM `".$this->getSetting('table')."`");
		while ($r = msr($q)) $this->deletePub($r['id']);
		msq("DROP TABLE `".$this->getSetting('table')."`");



		$CDDataSet->delete($pattern->getSetting('dataset'), $this->getSetting('section'));
		return true;
	}
	function deletePub($id,$updateprec = true){
		$id = floor($id);
		global $CDDataSet;
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'"))){
			$dataset = $CDDataSet->get($this->getSetting('dataset'));
			$imagestorage = $this->getSetting('imagestorage');
			foreach ($dataset['types'] as $dt){
				$tface = new $dt['type'];
				$tface->init(array('name'=>$dt['name'],'description'=>$dt['description'],'value'=>$r[$dt['name']],'imagestorage'=>floor($imagestorage['id']),'theme'=>$dataset['name'].'_'.$this->getSetting('section'),'rubric'=>$dt['name'],'uid'=>floor($r['id']),'settings'=>$dt['settings']));
				$tface->delete();
			}
			msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");

			WriteLog($id, 'удаление записи', '','','',$this->getSetting('section'));

			if ($updateprec) $this->updatePrecedence();
			return true;
		}
		return false;
	}
	function getSetting($name){ return $this->Settings[$name]; } // Получение значения, хранящегося в $Settings
	function setSetting($name,$value){ $this->Settings[$name] = $value; }
	function implode($settings = array()){ // Формирует строку вида |name|name=value|name|name|...
		if (!is_array($settings)) $settings = array();
		$retval = '|';
		$doubles = array();
		foreach ($settings as $k=>$v){
			$k = lower(trim($k));
			$v = trim($v);
			if (($k!='') && (!in_array($k,$doubles))){
				$doubles[] = $k;
				if ($v!='') $retval.= $k.'='.$v.'|';
				else $retval.= $k.'|';
			}
		}
		if ($retval=='|') $retval = '';
		return $retval;
	}
	function explode($settings = ''){ // Формирует массив из строки вида |name|name=value|name|name|...
		$settings = explode('|',trim($settings));
		$retval = array();
		foreach ($settings as $v){
			$v = trim($v);
			if ($v!=''){
				$values = explode('=',$v);
				$retval[lower(trim($values[0]))] = trim($values[1]);
			}
		}
		return $retval;
	}
}
?>
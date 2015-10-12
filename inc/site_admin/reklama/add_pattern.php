<div id="content" class="forms">
<div class="hr"><hr/></div>
<h2>Коммерческое размещение</h2>
<?
$MySqlConnect = new MySqlConnect;

$reklama_section=msr(msq("SELECT * FROM `site_site_sections` WHERE `pattern`='PReklama'"));


if (!$reklama_section['id']>0) $error='<h2>Необходимо создать раздел шаблона Реклама</h2><br/>';

if (floor($_GET['pub'])=='')  $error.='<h2>Опции коммерческого размещения будут доступны после сохранения записи</h2><br/>';

if ($error!='') print $error;
else 
{
	
	$RkSection = $SiteSections->get($reklama_section['id']);
	$RkSection['id'] = floor($RkSection['id']);
	
	if ($RkSection['id']>0)
	{
		
		$RkPattern = new $RkSection['pattern'];
		$RkIface = $RkPattern->init(array('section'=>$RkSection['id']));
	}
	
	$RkIface->StuctureTable($_GET['section']);
	
	$cur_pub=$RkIface->getItem($_GET['section'], $_GET['pub']);

	if (!$cur_reklama['id']>0) 
	$cur_reklama=$RkIface->getPubBySection($_GET['section'], $_GET['pub']);
	
	$active=false;
	if ($cur_reklama['id']>0)
	{
		
		
		if ($cur_reklama['start']>=0 && $cur_reklama['end']<=0)
		{
			print '<div><h2 style="color: #00cc00">Реклама активна</h2></div>';
			$active=true;
		}
		else  print '<div><h2 style="color: #cc0000">Реклама не активна</h2></div>';
		
		?>
		<table>
			<tr>
				<td>Начало:</td><td><?=$MySqlConnect->dateFromDBDot($cur_reklama['date_start']) ?></td>
			</tr>
			<tr>
				<td>Конец:</td><td><?=$MySqlConnect->dateFromDBDot($cur_reklama['date_end']) ?></td>
			</tr>
		</table>
		<?
	}
	
	?>
	<br/>
	<a class="button" href="/manage/control/contents/?section=<?=$reklama_section['id']?>&pub=<?=$cur_reklama['id']>0 ? $cur_reklama['id']: 'new' ?>&section_id=<?=$_GET['section'] ?>&item_id=<?=$_GET['pub'] ?>"><?=!$active ? 'Добавить':'Изменить'?> рекламу</a>
	<?
	 
	if (!isset($_POST['stat_end']))
	{
		$default_date_end=msr(msq("SELECT max(date) as dt FROM `site_site_reklama_stat` WHERE `item_id`=".$cur_reklama['id']));
		$default_date_end=msdfromdb($default_date_end['dt']);
	}
	else $default_date_end=$_POST['stat_end'];
	 
	if (!isset($_POST['stat_start']))
	{
		$default_date_start=msr(msq("SELECT min(date) as dt FROM `site_site_reklama_stat` WHERE `item_id`=".$cur_reklama['id']));
		$default_date_start=msdfromdb($default_date_start['dt']);
	}
	else $default_date_start=$_POST['stat_start'];
	 
	
	
	if ($_POST['search_stat']==1)
	{
		
		$default_limit=0;
		if ($_POST['stat_start']!='') $usl.=" and `date`>='".$MySqlConnect->dateToDB($_POST['stat_start'])."'";
		if ($_POST['stat_end']!='') $usl.=" and `date`<='".$MySqlConnect->dateToDB($_POST['stat_end'])."'";
		 
		?>
	   		                        	 <script>
										$(function() {
											var elem=$('#statistic');
											destination = elem.offset().top-50;
											$("html, body").animate({scrollTop:destination},"slow");
										});
										
										</script>
	   		                        	 <?
	   		                        }
		
		$stat=msr(msq("SELECT count(*) as cnt FROM `site_site_reklama_stat` WHERE `item_id`=".$cur_reklama['id']));
		
		if ($stat['cnt']>0)
		{
		?>
	   		                        		<div class="hr"><hr/></div>
	   		                        		<h2>Статистика</h2>
	   		
					<script type="text/javascript">
								$(function(){
	
									$("#stat_start").datepicker({
										showOn: "both",
										buttonImage: "/pics/editor/calendar.gif",
										buttonImageOnly: true
									});
	
									$("#stat_end").datepicker({
											showOn: "both",
											buttonImage: "/pics/editor/calendar.gif",
											buttonImageOnly: true
									});
								});
								</script>
								
								<form id="statistic" name="statistic" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="search_stat" value="1">
									<div class="place" id="stat_start_calendar" style="width: 158px;">
										<label>Дата с</label>
										<div><input  id="stat_start" name="stat_start" type="text" style="width: 100px; float: left;" value="<?=$default_date_start ?>"/></div>
									</div>
									<div class="place" id="stat_end_calendar" style="width: 158px;"> 
										<label>Дата по</label>
										<div><input  id="stat_end" name="stat_end" type="text" style="width: 100px; float: left;" value="<?=$default_date_end ?>"/></div>
									</div>
									<div style="width: 200px;margin-left: 2%;" class="place">
				   						<label>&nbsp;</label>
				   						<span class="forbutton">
				   							<span>
				   								<input type="submit" value="Показать за выбранный период" class="button">
				   							</span>
				   						</span>
				   					</div>
				   					<div style="width: 8%;margin-left: 2%;" class="place">
				   						<label>&nbsp;</label>
				   						<span class="forbutton">
				   							<span>
				   								<input type="submit" value="Выгрузить в xls" class="button" name="export">
				   							</span>
				   						</span>
				   					</div>
								</form>
			
	   		                        		<table class="table-content stat">
	   		                        		<tr>
	   		                        			<th class="t_center">Дата</th>
	   		                        			<th>Показов</th>
	   		                        			<th>Кликов</th>
	   		                        			<th>Уникальных посетителей</th>
	   		                        		</tr>
	   		                        		<?	
	   		                        		$stat=msq("SELECT * FROM `site_site_reklama_stat` WHERE `item_id`=".$cur_reklama['id'].$usl." ORDER BY `date` DESC ".(($_POST['search_stat']=='')? ' LIMIT 0, 200':''));
	   		                        		
	   		                        		while ($st=msr($stat))
	   		                        		{
	   		                        			?>
	   		                        			<tr>
	   		                        				<td class="t_center"><?=$MySqlConnect->dateFromDBDot($st['date'])?></td>
	   		                        				<td><?=$st['show']?></td>
	   		                        				<td><?=$st['click']?></td>
	   		                        				<td><?=$st['unique']?></td>
	   		                        			</tr>
	   		                        			<?	
	   		                        		}
	   		                        		?>
	   		                        		</table>
	   		                        		<?
	   		                        		
	   		                        	/* 	if (isset($_POST['export']))
	   		                        		$this->ExportXls($stat); */
	   		                        	
		   		                        	if (isset($_POST['export']))
		   		                        	{
		   		                        		mysql_data_seek($stat, 0);
		   		                        		$this->ExportStatXls($stat);
		   		                        	}
	   		                        	
	   		                        	}
	
} ?>
</div>
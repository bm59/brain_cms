<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";
$order = floor($_GET['order']);
if (floor($_GET['switch_on'])>0)
{	$SiteVisitor->switchOnOff($_GET['switch_on'],'on');
	WriteLog($_GET['switch_on'], 'включение пользователя' );
}
if (floor($_GET['switch_off'])>0)
{	$SiteVisitor->switchOnOff($_GET['switch_off'],'off');
	WriteLog($_GET['switch_off'], 'отключение пользователя');
}

if (!$activeccid>0) $activeccid=$SiteSections->getIdByPath('/access/users/');

if ($_GET['delete']>0)
if (@in_array('delete',$group['new_settings'][$activeccid]) || $mode=='development')
{
	$SiteVisitor->delete($_GET['delete']);
	WriteLog($_GET['delete'], 'удаление пользователя');

	header("Location: ".configGet("AskUrl"));
}
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";
?>

	<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
	<script>
	$(function() {
		$.expr[':'].contains = function( elem, i, match, array ) {
		    return (elem.textContent || elem.innerText || jQuery.text( elem ) || "").toLowerCase().indexOf(match[3].toLowerCase()) >= 0;
		}

		$('[name=search_group]').change(function()
		{


				if ($(this).val()=='-1') $('.table-content td').show();
				else
				{
					 $('.table-content td').hide();
					 $(".table-content .group_name:contains('"+$(this).val()+"')").parents('tr').find('td').show();
				}



		});
		function search_name (s_text)
		{
			if (s_text!='')
			{
				$('.table-content td').hide();
				$(".table-content .user_name:contains("+s_text+")").parents('tr').find('td').show();
			}
			else
			$('.table-content td').show();
		}

		$('[name=search_name]').keyup(function()
		{
			search_name($(this).val());

		});
	});
	</script>
	<div id="content">
	<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
		<?
		$userslist = $SiteVisitor->getList(0,$order);
		if (count($userslist)<1){
			?>
			<h2>Пользователи отсутствуют</h2>
			<?
		}
		else{
		?>
		<div>
			<table>
			<tr>
				<td>
					<span class="input">
						<input value="" maxlength="40" name="search_name" placeholder="Имя пользователя">
					</span>
				</td>
				<td>
					<?
					$values['-1']='';
					$q = msq("SELECT * FROM `site_bk_users_types` ORDER BY `name`");
					while ($r = msr($q))
					{
				            $values[stripslashes(htmlspecialchars($r['name']))] = stripslashes(htmlspecialchars($r['name']));

				    }
					?>

						<?print getSelectSinonim('search_group',$values,$_REQUEST['search_group']);?>

				</td>
			</tr>
			</table>
		</div>
		<table class="table-content stat tusers">
			<tr>
				<th class="t_nowrap">Пользователь<a href="<?=configGet("AskUrl").'?order=1'?>" title="Сортировать по убыванию" class="sort"><img src="/pics/arrows/down_sort_blue.gif" width="5" height="10" /></a><a href="<?=configGet("AskUrl")?>" title="Сортировать по возрастанию" class="sort"><img src="/pics/arrows/up_sort_blue.gif" width="5" height="10" /></a></th>
				<th class="t_nowrap">Последний вход</th>
				<th class="t_nowrap">Группа<a href="<?=configGet("AskUrl").'?order=3'?>" title="Сортировать по убыванию" class="sort"><img src="/pics/arrows/down_sort_blue.gif" width="5" height="10" /></a><a href="<?=configGet("AskUrl").'?order=2'?>" title="Сортировать по возрастанию" class="sort"><img src="/pics/arrows/up_sort_blue.gif" width="5" height="10" /></a></th>
				<th class="t_32width"></th>
				<th class="t_32width"></th>
			</tr>
			<?
			foreach ($userslist as $userid){
				$user = $SiteVisitor->getOne($userid);
				$usertype = $VisitorType->getOne($user['type']);
				$lasttime = '&mdash;';
				if (preg_match("|^[0-9]+$|",$user['settings']['lasttime'])) if ($time = @date("d.m.Y H:i",$user['settings']['lasttime'])) $lasttime = $time;

				$href=array('<a href="'.$_SERVER['REDIRECT_URL'].'profile/?user='.$user['id'].'">','</a>');

				if (!@in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development')
				$href=array();

				?>
				<tr id="item_<?=$user['id']?>">
					<td class="t_left user_name">
					<?=$href[0].$user['secondname'].' '.$user['firstname'].' '.$user['parentname'].$href[1]?>
					<?=$help?></td>
					<td class="t_left"><?=$lasttime?></td>
					<td class="t_left group_name"><?=$usertype['name']?></td>
					<td class="t_32width">
						<?
						if ((isset($user['settings']['noswitch']) || !@in_array('onoff',$group['new_settings'][$activeccid])) && $mode!='development'){
							?>
							<a class="button txtstyle disabled">
								<span class="icon" style="background-image: url(/pics/editor/disabled.png)" title="Нельзя выключить" />
							</a>
							<?
						}
						else{
							?>
							<a href="<?=configGet("AskUrl").'?switch_'.((isset($user['settings']['engage']))?'off':'on').'='.$user['id']?>" class="button txtstyle">
								<span class="icon" style="background-image: url(/pics/editor/<?=(isset($user['settings']['engage']))?'on':'off'?>.png)" title="Включен" />
							</a>
							<?
						}
						?>
					</td>
					<td class="t_32width">
						<?
						if ((isset($group['settings']['undeletable']) || !@in_array('delete',$group['new_settings'][$activeccid])) && $mode!='development'){
							?>
							<span class="button txtstyle disabled">
								<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" />
							</span>
							<?
						}
						else{
							?>
							<a href="./?delete=<?=$user['id']?>" class="button txtstyle" onclick="if (confirm('Вы уверены, что хотите удалить этого пользователя?')) bkAjaxDeleteItem('users',<?=$user['id']?>,'item_<?=$user['id']?>'); return false;">
								<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить пользователя" />
							</a>
							<?
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
		}

		if (@in_array('add',$group['new_settings'][$activeccid]) || $mode=='development')
		{
		?>
		<span class="clear"></span>
		<div class="place">
			<a href="edit/" class="button big" style="float: right;">Новый пользователь</a>
		</div>
		<?}?>
		<span class="clear"></span>
	</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>
<?
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";
$order = floor($_GET['order']);
if (floor($_GET['switch_on'])>0) $SiteVisitor->switchOnOff($_GET['switch_on'],'on');
if (floor($_GET['switch_off'])>0) $SiteVisitor->switchOnOff($_GET['switch_off'],'off');
include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php";
?>
<div id="zbody">
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/content/header.php";?>
	<div id="content">
		<?
		$userslist = $SiteVisitor->getList(0,$order);
		if (count($userslist)<1){
			?>
			<h2>������������ �����������</h2>
			<?
		}
		else{
		?>
		<table class="table-content stat tusers">
			<tr>
				<th class="avatar"></th>
				<th class="t_nowrap">������������<a href="<?=configGet("AskUrl").'?order=1'?>" title="����������� �� ��������" class="sort"><img src="/pics/i/down.gif" width="5" height="10" /></a><a href="<?=configGet("AskUrl")?>" title="����������� �� �����������" class="sort"><img src="/pics/i/up.gif" width="5" height="10" /></a></th>
				<th class="t_nowrap">��������� ����</th>
				<th class="t_nowrap">������<a href="<?=configGet("AskUrl").'?order=3'?>" title="����������� �� ��������" class="sort"><img src="/pics/i/down.gif" width="5" height="10" /></a><a href="<?=configGet("AskUrl").'?order=2'?>" title="����������� �� �����������" class="sort"><img src="/pics/i/up.gif" width="5" height="10" /></a></th>
				<th class="t_32width"></th>
				<th class="t_32width"></th>
			</tr>
			<?
			foreach ($userslist as $userid){
				$user = $SiteVisitor->getOne($userid);
				$usertype = $VisitorType->getOne($user['type']);
				$lasttime = '&mdash;';
				if (preg_match("|^[0-9]+$|",$user['settings']['lasttime'])) if ($time = @date("d.m.Y H:i",$user['settings']['lasttime'])) $lasttime = $time;
				?>
				<tr id="item_<?=$user['id']?>">
					<td class="avatar"><div><a href="edit/?edit=<?=$user['id']?>"><img src="<?=($user['picture']['path'])?$user['picture']['path']:'/pics/i/empty_user.gif'?>" width="60" height="60" alt="<?=$user['secondname'].' '.$user['firstname'].' '.$user['parentname']?>" /></a></div></td>
					<td class="t_left"><a href="<?=$_SERVER['REDIRECT_URL'].'profile/?user='.$user['id']?>"><?=$user['secondname'].' '.$user['firstname'].' '.$user['parentname']?></a><?=$help?></td>
					<td class="t_left"><?=$lasttime?></td>
					<td class="t_left"><?=$usertype['name']?></td>
					<td class="t_32width">
						<?
						if (isset($user['settings']['noswitch'])){
							?>
							<a class="button txtstyle disabled">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<span class="icon" style="background-image: url(/pics/editor/status-disabled.gif)" title="������ ���������" />
							</a>
							<?
						}
						else{
							?>
							<a href="<?=configGet("AskUrl").'?switch_'.((isset($user['settings']['engage']))?'off':'on').'='.$user['id']?>" class="button txtstyle">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<span class="icon" style="background-image: url(/pics/editor/<?=(isset($user['settings']['engage']))?'on':'off'?>.gif)" title="�������" />
							</a>
							<?
						}
						?>
					</td>
					<td class="t_32width">
						<?
						if (isset($user['settings']['undeletable'])){
							?>
							<span class="button txtstyle disabled">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="���������� �������" />
							</span>
							<?
						}
						else{
							?>
							<a href="��������" class="button txtstyle" onclick="if (confirm('�� �������, ��� ������ ������� ����� ������������?')) bkAjaxDeleteItem('users',<?=$user['id']?>,'item_<?=$user['id']?>'); return false;">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="������� ������������" />
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
		?>
		<span class="clear"></span>
		<div class="place">
			<a href="edit/" class="button big">
				<span class="bl"></span>
				<span class="bc">����� ������������</span>
				<span class="br"></span>
			</a>
		</div>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>
<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";

if ($_GET['delete']>0)
if (in_array('delete',$group['new_settings'][$activeccid]) || $mode=='development')
{	$VisitorType->delete($_GET['delete']);
	WriteLog($_GET['delete'], '�������� ������');
	header("Location: ".configGet("AskUrl"));
}
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";
?>
	<div id="zbody">
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
	<div id="content">
		<?
		$groupslist = $VisitorType->getList();
		if (count($groupslist)<1){
			?>
			<h2>������ ������������� �����������</h2>
			<?
		}
		else{
		?>
		<table class="table-content stat tusers">
			<tr>
				<th class="t_nowrap">��������</th>
				<th class="t_nowrap">���������� �������������</th>
				<th class="t_32width"></th>
			</tr>
			<?
			foreach ($groupslist as $groupid){
				$group = $VisitorType->getOne($groupid);
				$href = (isset($group['settings']['noedit']))?array('',''):array('<a href="edit/?edit='.$group['id'].'">','</a>');

				if (!in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development')
				$href=array();
				?>
				<tr id="item_<?=$group['id']?>">
					<td class="t_left"><?=$href[0]?><?=$group['name']?><?=$href[1]?><?=$help?></td>
					<td class="t_left"><?=$group['userscount']?></td>
					<td class="t_32width">
						<?
						if ((isset($group['settings']['undeletable']) || !in_array('delete',$group['new_settings'][$activeccid])) && $mode!='development'){
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
							<a href="./?delete=<?=$group['id']?>" class="button txtstyle" onclick="if (!confirm('�� �������, ��� ������ ������� ��� ������?')) return false;">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="�������" />
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

		if (in_array('add',$group['new_settings'][$activeccid]) || $mode=='development')
		{
		?>
		<span class="clear"></span>
		<div class="place">
			<a href="edit/" class="button big">
				<span class="bl"></span>
				<span class="bc">����� ������</span>
				<span class="br"></span>
			</a>
		</div>
		<?}?>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>

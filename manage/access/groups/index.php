<?
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";
include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php";
?>
	<div id="zbody">
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/content/header.php";?>
	<div id="content">
		<?
		$groupslist = $VisitorType->getList();
		if (count($groupslist)<1){
			?>
			<h2>Группы пользователей отсутствуют</h2>
			<?
		}
		else{
		?>
		<table class="table-content stat tusers">
			<tr>
				<th class="t_nowrap">Название</th>
				<th class="t_nowrap">Количество пользователей</th>
				<th class="t_32width"></th>
			</tr>
			<?
			foreach ($groupslist as $groupid){
				$group = $VisitorType->getOne($groupid);
				$href = (isset($group['settings']['noedit']))?array('',''):array('<a href="edit/?edit='.$group['id'].'">','</a>');
				?>
				<tr id="item_<?=$group['id']?>">
					<td class="t_left"><?=$href[0]?><?=$group['name']?><?=$href[1]?><?=$help?></td>
					<td class="t_left"><?=$group['userscount']?></td>
					<td class="t_32width">
						<?
						if (isset($group['settings']['undeletable'])){
							?>
							<span class="button txtstyle disabled">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" />
							</span>
							<?
						}
						else{
							?>
							<a href="Удаление" class="button txtstyle" onclick="if (confirm('Вы уверены, что хотите удалить эту группу?')) bkAjaxDeleteItem('usergroups',<?=$group['id']?>,'item_<?=$group['id']?>'); return false;">
								<span class="bl"></span>
								<span class="bc"></span>
								<span class="br"></span>
								<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить" />
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
				<span class="bc">Новая группа</span>
				<span class="br"></span>
			</a>
		</div>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>

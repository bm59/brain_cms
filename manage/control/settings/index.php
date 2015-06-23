<?
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";
/*print_r($_SERVER);*/
$iface = new SiteSettings;
$iface->init();
$adderrors = $updateerrors = $data = array();
if (isset($_POST['setadd'])){
	foreach ($_POST as $k=>$v) $data[$k] = trim($v);
	$adderrors = $iface->add($data['name'],$data['description'],$data['value'],array('type'=>$data['type']));
	if (count($adderrors)==0) $data = array();
}
if (isset($_GET['setdel'])){
	 $iface->delete($_GET['setdel']);
}
$list = $iface->getList();
if (isset($_POST['setupdate'])){
	foreach ($list as $setid){
		$set = $iface->getOne($setid);
		if (isset($_POST[$set['name'].'_value'])) $err = $iface->update($set['id'],$_POST[$set['name'].'_value']);
		if ($err!='') $updateerrors[] = $err;
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php";?>
	<title>Настройки сайта</title>
</head>
<body>
<script type="text/javascript">
function Gotopage(link)
{

 document.location=link
}
</script>
<div id="zbody">
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/content/header.php";;?>
	<div id="content" class="forms">
		<h1>Настройки сайта</h1>
		<?
		if (count($updateerrors)>0){
			print '
			<p><strong>Не все настройки обновлены:</strong></p>
			<ul class="errors">';
				foreach ($updateerrors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}
		if (count($list)>0){
		?>
		<form name="setupdate" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
			<?
			foreach ($list as $setid){
				$set = $iface->getOne($setid);
				?>
				<div class="place" id="item_<?=$set['id']?>">
					<table style="width: 100%;"><tr><td>
					<label><?=stripslashes(htmlspecialchars($set['description']))?> <small class="setdesc"><?=stripslashes(htmlspecialchars($set['name']))?></small></label>
					<?
					if ($set['settings']['type']!='text')
					{
					?>
					<span class="input">
						<span class="bl"></span>
						<span class="bc"><input name="<?=htmlspecialchars($set['name'])?>_value" value="<?=stripslashes(htmlspecialchars($set['value']))?>"/></span>
						<span class="br"></span>
					</span>
					<?
					}
                    else
                    {                    ?>
						<div class="ta_big"><textarea style="width: 100%; padding: 10px;" name="<?=htmlspecialchars($set['name'])?>_value"><?=stripslashes(htmlspecialchars($set['value']))?></textarea></div>
                    <?
                    }
					?>
					</td><td style="width: 32px;">
					<label>&nbsp;</label>
					<?
					if (isset($set['settings']['undeletable'])){
						?>
						<span class="button txtstyle disabled">
							<span class="bl"></span>
							<span class="bc"></span>
							<span class="br"></span>
							<input type="button" style="background-image: url(/pics/editor/delete-disabled.gif)" title="Невозможно удалить" onclick="return false;" />
						</span>
						<?
					}
					else{
						?>
						<a class="button txtstyle" href="#" onclick="if (confirm('Вы уверены, что хотите удалить эту настройку?')) Gotopage('<?=$_SERVER['PHP_SELF'].'?setdel='.$set['id']?>'); return false;" >
							<span class="bl"></span>
							<span class="bc"></span>
							<span class="br"></span>
							<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить настройку" />
						</a>
						<?
					}
					?>
					</td></tr></table>
				</div>
				<span class="clear"></span>
				<?
			}
			?>
			<div class="place">
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc">Сохранить</span>
					<span class="br"></span>
					<input type="submit" name="setupdate" value="" />
				</span>
			</div>
		</form>
		<?
		}
		?>
		<span class="clear"></span>
		<?
		if (count($adderrors)>0){
			print '
			<p><strong>Добавление настройки не выполнено по следующим причинам:</strong></p>
			<ul class="errors">';
				foreach ($adderrors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}
		?>
		<form name="setadd" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
			<div class="place">
				<table style="width: 100%; table-layout: fixed;"><tr><td>
				<label>Описание</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="description" maxlength="250" value="<?=htmlspecialchars($data['description'])?>" /></span>
					<span class="br"></span>
				</span>
				</td></tr></table>
			</div>
			<span class="clear"></span>
			<div class="place">
				<table style="width: 100%; table-layout: fixed;"><tr><td>
				<tr><td>
				<label>Название</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="name" maxlength="20" value="<?=htmlspecialchars($data['name'])?>" /></span>
					<span class="br"></span>
				</span>
				</td><td>
				<label>Значение</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="value" value="<?=stripslashes(htmlspecialchars($data['value']))?>" /></span>
					<span class="br"></span>
				</span>
				</td><td>
				<label>Тип</label>
				<?
				$values = array();
				foreach ($iface->getSetting('types') as $k=>$v) $values[$k] = $v;
				print getSelectSinonim('type',$values,$data['type']);
				?>
				</td></tr></table>
			</div>
			<span class="clear"></span>
			<div class="place">
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc">Добавить</span>
					<span class="br"></span>
					<input type="submit" name="setadd" value="" />
				</span>
			</div>
		</form>
		<span class="clear"></span>
	</div>
	<?/*include $_SERVER['DOCUMENT_ROOT']."/inc/footer.php";*/?>
</div>
</body>
</html>
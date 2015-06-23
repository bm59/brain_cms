<?
include "$DOCUMENT_ROOT/inc/include.php";
$data = array('name'=>'','access'=>array(),'settings'=>array());
$errors = array();
$editid = $VisitorType->checkTypePresence($_GET['edit']);
if ($editid>0) $data = $VisitorType->getOne($editid);
if (isset($data['settings']['noedit'])){
	$data = array('name'=>'','access'=>array(),'settings'=>array());
	$editid = 0;
}
if (isset($_POST['addeditgroup'])){
	$editid = floor($_POST['addeditgroup']);
	if (!isset($data['settings']['norename'])) $data['name'] = htmlspecialchars(trim($_POST['name']));
	$data['access'] = array();
	foreach ($_POST as $k=>$v){
		if (preg_match("|^access_([0-9]{1,3})$|",$k)){
			$access = floor(substr($k,7));
			if ($v=='on') $data['access'][] = $access;
		}
	}
	if ($editid>0) $errors = $VisitorType->edit($editid,$data['name'],$data['access']);
	else $errors = $VisitorType->add($data['name'],$data['access']);
	if (count($errors)==0) header("Location: ../");
}
$settings = ($editid>0)?array('title'=>'Редактирование группы пользователей','button'=>'Сохранить'):array('title'=>'Добавление группы пользователей','button'=>'Создать группу');
?>
<? include "$DOCUMENT_ROOT/inc/content/meta.php"; ?>

<div id="zbody">
	<? include "$DOCUMENT_ROOT/inc/content/header.php"; ?>
	<div id="content" class="forms">
		<h1><a href="../">Группы пользователей</a> &rarr; <?=$settings['title']?></h1>
		<?
		if (count($errors)>0){
			print '
			<p><strong>'.$settings['title'].' не выполнено по следующим причинам:</strong></p>
			<ul class="errors">';
				foreach ($errors as $v) print '
				<li>'.$v.'</li>';
			print '
			</ul>';
		}
		?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
			<input type="hidden" name="addeditgroup" value="<?=$editid?>">
			<div class="place" style="width: 300px;">
				<label>Название</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="name" maxlength="25" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['name']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<span class="clear"></span>
			<h1>Разделы для доступа</h1>
			<div class="place" style="z-index: 10;">
				<div class="forckecks foraccess">
					<?
					$VisitorType->drawAccessCallback(0,-1,$data['access']);
					?>
				</div>
			</div>
			<span class="clear"></span>
			<div class="place">
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc"><?=$settings['button']?></span>
					<span class="br"></span>
					<input type="submit" name="editgroup" value=""/>
				</span>
			</div>
			<span class="clear"></span>
		</form>
		<div class="hr"><hr /></div>
		<div id="paging" class="nopad">
			<a href="./" class="button">
				<span class="bl"></span>
				<span class="bc">Перейти к списку групп</span>
				<span class="br"></span>
			</a>
		</div>
		<span class="clear"></span>
	</div>
	<?/*include "$DOCUMENT_ROOT/inc/footer.php";*/?>
</div>
</body>
</html>

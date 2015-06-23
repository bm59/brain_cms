<?
include "$DOCUMENT_ROOT/inc/include.php";
$data = array('settings'=>array(),'picture'=>array(),'type'=>$VisitorType->getSetting('guestsId'));
$errors = array();
$editid = $SiteVisitor->checkUserPresence($_GET['edit']);
if ($editid>0){
	$data = $SiteVisitor->getOne($editid);
	$data['pswd'] = '';
}
if (isset($_POST['addedituser'])){
	$editid = floor($_POST['addedituser']);
	$data['picture']['id'] = floor($_POST['picture']);
	if (!isset($data['settings']['nologinchange'])) $data['login'] = trim($_POST['login']);
	if (!isset($data['settings']['norename'])){
		$data['secondname'] = htmlspecialchars(trim($_POST['secondname']));
		$data['firstname'] = htmlspecialchars(trim($_POST['firstname']));
		$data['parentname'] = htmlspecialchars(trim($_POST['parentname']));
	}
	$data['pswd'] = trim($_POST['pswd']);
	if (!isset($data['settings']['notypechange'])) $data['type'] = floor($_POST['type']);
	$data['email'] = trim($_POST['email']);
	if ($editid>0) $errors = $SiteVisitor->edit($editid,$data);
	else $errors = $SiteVisitor->add($data);
	if (count($errors)==0) header("Location: ../\n");
}
$settings = ($editid>0)?array('title'=>'Редактирование пользователя','button'=>'Сохранить изменения','pswd'=>' (если Вы не хотите менять пароль — оставьте поле пустым)'):array('title'=>'Добавление пользователя','button'=>'Создать пользователя','pswd'=>' (не менее 4-х символов)');
include "$DOCUMENT_ROOT/inc/content/meta.php";?>
<div id="zbody">
	<?include "$DOCUMENT_ROOT/inc/content/header.php";?>
	<div id="content" class="forms">
		<h1><a href="../">Пользователи</a> &rarr; <?=$settings['title']?></h1>
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
		<form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="addedituser" value="<?=$editid?>">
			<div class="place" style="width: 250px; margin-right: 2%;">
				<label>Фамилия</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="secondname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['secondname']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<div class="place" style="width: 250px; margin-right: 2%;">
				<label>Имя</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="firstname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['firstname']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<div class="place" style="width: 250px;">
				<label>Отчество</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="parentname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['parentname']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<span class="clear"></span>
			<div class="place" style="width: 300px; margin-right: 2%;">
				<label>Логин</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="login" maxlength="30" <?=(isset($data['settings']['nologinchange']))?'disabled="disabled"':''?> value="<?=$data['login']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<div class="place" style="width: 450px;">
				<label>Пароль<?=$settings['pswd']?></label>
				<span class="input" style="width: 250px;">
					<span class="bl"></span>
					<span class="bc"><input <?=($editid>0)?'type="password"':'type="text"'?> name="pswd" maxlength="30" value="<?=$data['pswd']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<span class="clear"></span>
			<div class="place" style="width: 300px; margin-right: 2%;">
				<label>Электронная почта</label>
				<span class="input">
					<span class="bl"></span>
					<span class="bc"><input name="email" maxlength="40" value="<?=$data['email']?>" /></span>
					<span class="br"></span>
				</span>
			</div>
			<?
			if (!isset($data['settings']['notypechange'])){
			?>
			<div class="place" style="width: 300px; z-index: 10;">
				<label>Группа пользователей</label>
				<?
				$values = array();
				$types = $VisitorType->getList();
				foreach ($types as $typeid){
					$type = $VisitorType->getOne($typeid);
					$values[$typeid] = $type['name'];
				}
				print getSelectSinonim('type',$values,$data['type']);
				?>
			</div>
			<?
			}
			?>
			<span class="clear"></span>
			<?
			$data['picture'] = $Storage->getFile($data['picture']['id']);
			if (!($data['picture']['imgsize'] = @getimagesize($data['picture']['fullpath']))) $data['picture']['id'] = 0;
			if ($data['picture']['id']==0) $data['picture']['path'] = '/pics/i/empty_user.gif';
			?>
			<input type="hidden" id="uploadfilehidden" name="picture" value="<?=$data['picture']['id']?>">
			<div class="place">
				<label>Фотография</label>
				<div class="avatar" id="iconavatar"><img id="iconavatarimg" src="<?=$data['picture']['path']?>" width="60" height="60" alt="" /></div>
				<div class="avatar" id="waitavatar" style="display:none;"><div class="loading"></div></div>
				<span class="input" style="width: 250px; margin: 12px 0px 12px 10px;">
					<span class="bl"></span>
					<span class="bc"><input value="" id="iconfakeinput" disabled /></span>
					<span class="br"></span>
				</span>
				<span class="button big" style="margin: 20px 0px 20px 10px;">
					<span class="bl"></span>
					<span class="bc">Обзор</span>
					<span class="br"></span>
					<div class="fileselect">
						<input type="file" name="uploadfile" onchange="$('iconfakeinput').value=this.value; uploadFileAjax(this,'editform','fileuploadframe','uploadfilehidden',<?=($stid = $SiteVisitor->getSetting('iconsstorage'))?$stid['id']:0?>,'bk_users','icon',<?=$editid?>,'onUserIconStartLoading();','onUserIconFinishLoading(uploadimage,uploadimageid);','onready');"/>
					</div>
				</span>
				<span id="uploadfiledeletebutton" class="button txtstyle" style="margin: 20px 0px 20px 10px;<?=($data['picture']['id']>0)?'':'display:none;'?>">
					<span class="bl"></span>
					<span class="bc"></span>
					<span class="br"></span>
					<input type="button" onclick="uploadFileAjax(this,'editform','fileuploadframe','uploadfilehidden',<?=($stid = $SiteVisitor->getSetting('iconsstorage'))?$stid['id']:0?>,'bk_users','icon',<?=$editid?>,'onUserIconStartLoading();','onUserIconFinishDeleting();','anytime');return false;" style="background-image: url(/pics/editor/delete.gif)" title="Удалить изображение" />
				</span>
			</div>
			<span class="clear"></span>
			<div class="place">
				<span class="button big" style="float: right;">
					<span class="bl"></span>
					<span class="bc"><?=$settings['button']?></span>
					<span class="br"></span>
					<input type="submit" name="edituser" value="" />
				</span>
			</div>
			<span class="clear"></span>
		</form>
		<script id="imageuploadscript">
			function showHideDeletingButton(mode){
				var span = $('uploadfiledeletebutton');
				span.style.display = (mode>0)?'':'none';
			}
			function onUserIconStartLoading(){
				var icon = $('iconavatar');
				var wait = $('waitavatar');
				icon.style.display = "none";
				wait.style.display = "";
			}
			function onUserIconFinishDeleting(){
				var icon = $('iconavatar');
				var wait = $('waitavatar');
				var file = $('uploadfilehidden');
				var img = $('iconavatarimg');
				file.value = 0;
				img.src = '/pics/i/empty_user.gif';
				showHideDeletingButton(0);
				icon.style.display = "";
				wait.style.display = "none";
			}
			function onUserIconFinishLoading(uploadfile,uploadfileid){
				var icon = $('iconavatar');
				var wait = $('waitavatar');
				var file = $('uploadfilehidden');
				var img = $('iconavatarimg');
				if (uploadfileid>0){
					file.value = uploadfileid;
					img.src = uploadfile;
					showHideDeletingButton(1);
				}
				wait.style.display = "none";
				icon.style.display = "";
			}
		</script>
        <iframe id="fileuploadframe" name="fileuploadframe" width="900px" height="200px" style="display:none"></iframe>
		<div class="hr"><hr /></div>
		<div id="paging" class="nopad">
			<a href="../" class="button">
				<span class="bl"></span>
				<span class="bc">Перейти к списку пользователей</span>
				<span class="br"></span>
			</a>
		</div>
		<span class="clear"></span>
	</div>
	<?/*include "$DOCUMENT_ROOT/inc/footer.php";*/?>
</div>
</body>
</html>

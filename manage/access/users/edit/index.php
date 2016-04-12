<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";

if (!$activeccid>0)
$activeccid=$Content->getIdByPath(configGet("AskUrl"));

if (!@in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['user']>0)
header("Location: /manage/control/contents/");


if (!@in_array('add',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['user']=='')
header("Location: /manage/control/contents/");

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
	if ($editid>0)
	{		$errors = $SiteVisitor->edit($editid,$data);
		WriteLog($editid, 'редактирование пользователя');
	}
	else
	{		$errors = $SiteVisitor->add($data);
		WriteLog(0, 'добавление пользователя', $data['login']);
	}
	if (count($errors)==0) header("Location: ../\n");
}
$settings = ($editid>0)?array('title'=>'Редактирование пользователя','button'=>'Сохранить изменения','pswd'=>' (если Вы не хотите менять пароль — оставьте поле пустым)'):array('title'=>'Добавление пользователя','button'=>'Создать пользователя','pswd'=>' (не менее 4-х символов)');
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>
	<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
	<div id="content" class="forms">
		<div class="hr"></div>
		<h1><a href="/manage/">Панель управления</a> &rarr; Доступ &rarr; <a href="/manage/access/users/">Пользователи</a> &rarr; <?=$settings['title']?></h1>
		<br/>
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
					<input name="secondname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['secondname']?>" />
				</span>
			</div>
			<div class="place" style="width: 250px; margin-right: 2%;">
				<label>Имя</label>
				<span class="input">
					<input name="firstname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['firstname']?>" />
				</span>
			</div>
			<div class="place" style="width: 250px;">
				<label>Отчество</label>
				<span class="input">
					<input name="parentname" maxlength="30" <?=(isset($data['settings']['norename']))?'disabled="disabled"':''?> value="<?=$data['parentname']?>" />
				</span>
			</div>
			<span class="clear"></span>
			<div class="place" style="width: 300px; margin-right: 2%;">
				<label>Логин</label>
				<span class="input">
					<input name="login" maxlength="30" <?=(isset($data['settings']['nologinchange']))?'disabled="disabled"':''?> value="<?=$data['login']?>" />
				</span>
			</div>
			<div class="place" style="width: 450px;">
				<label>Пароль<?=$settings['pswd']?></label>
				<span class="input" style="width: 250px;">
					<input <?=($editid>0)?'type="password"':'type="text"'?> name="pswd" maxlength="30" value="<?=$data['pswd']?>" />
				</span>
			</div>
			<span class="clear"></span>
			<div class="place" style="width: 300px; margin-right: 2%;">
				<label>Электронная почта</label>
				<span class="input">
					<input name="email" maxlength="40" value="<?=$data['email']?>" />
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
			<span class="clear"></span>
			<div class="place">
				<span  style="float: right;">
					<input class="button big" type="submit" name="edituser" value="<?=$settings['button']?>" />
				</span>
			</div>
			<span class="clear"></span>
		</form>
		<div class="hr"><hr /></div>
		<div class="nopad">
			<a href="../" class="button">Перейти к списку пользователей</a>
		</div>
		<span class="clear"></span>
	</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>

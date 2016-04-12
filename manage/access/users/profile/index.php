<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";

if (!$activeccid>0)
$activeccid=$Content->getIdByPath(configGet("AskUrl"));

if (!@in_array('edit',$group['new_settings'][$activeccid]) && $mode!='development' && $_GET['user']>0)
header("Location: /manage/control/contents/");

$requestUserId = $_SESSION['visitorID'];
$user = $SiteVisitor->getOne($_SESSION['visitorID']);
$group = $VisitorType->getOne($user['type']);
$requestUserId = floor($_GET['user']); /* Поставить седующие условие вместо этой строки, чтобы непривилигированные пользователи не могли заходить на страницу чужого профиля
	if ((@in_array('610',$group['access'])) || (isset($group['settings']['superaccess']))) $requestUserId = floor($_GET['user']);
*/
if (!$SiteVisitor->checkUserPresence($requestUserId)) $requestUserId = $_SESSION['visitorID'];
configSet('profileID',$requestUserId);
$requestUser = $SiteVisitor->getOne($requestUserId);
$requestUserGroup = $VisitorType->getOne($requestUser['type']);
$errors = array();
if (isset($_POST['profilepswd'])) $errors = $SiteVisitor->changePassword($_SESSION['visitorID'],trim($_POST['oldpswd']),trim($_POST['newpswd']));
?>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/header.php";?>
	<div id="content" class="forms">
       	<div class="hr"></div>
  		<h1><a href="/manage/">Панель управления</a> &rarr; Доступ &rarr; <a href="/manage/access/users/">Пользователи</a> &rarr; Профиль</h1>
        <br/>
		<div class="pub user">
			<div class="info">
				<div class="avatar" style="margin: -4px 0px;"><img src="<?=($requestUser['picture']['path'])?$requestUser['picture']['path']:'/pics/inputs/empty_user.gif'?>" width="60" height="60" alt="" /></div>
			</div>
			<h1><?=$requestUser['secondname'].' '.$requestUser['firstname'].' '.$requestUser['parentname']?></h1>
			<h2><?=$requestUserGroup['name']?></h2>
			<div class="userinfo">
				<span><strong>Дата регистрации</strong><?=$requestUser['regdate']?></span>
				<span><strong>Дата последнего входа</strong><?=date("d.m.Y H:i",$requestUser['settings']['lasttime'])?></span>
				<span><strong>Электронная почта</strong><a href="mailto:<?=$requestUser['email']?>"><?=$requestUser['email']?></a></span>
			</div>
			<?
			if ((@in_array('410',$group['access'])) || (isset($group['settings']['superaccess']))){
				?>
				<a href="/manage/access/users/edit/?edit=<?=$requestUser['id']?>" class="button">Редактировать</a>
				<?
			}
			?>
		</div>
		<?
		if ($_SESSION['visitorID']==configGet('profileID')){
			if ((!@in_array('611',$group['access'])) && (!isset($group['settings']['superaccess']))){
				?>
				<div class="hr"><hr /></div>
				<span class="clear"></span>
				<?
				if (count($errors)>0){
					print '
					<p><strong>Смена пароля не выполнена по следующим причинам:</strong></p>
					<ul class="errors">';
						foreach ($errors as $v) print '
						<li>'.$v.'</li>';
					print '
					</ul><div class="hr"><hr /></div>';
				}
				elseif (isset($_POST['profilepswd'])) print '
					<p><strong>Смена пароля успешно выполнена</strong></p>';
				?>
				<span class="clear"></span>
				<form action="/manage/access/users/profile/" method="POST">
					<div class="place" style="width: 200px; margin-right: 2%;">
						<label>Старый пароль</label>
						<span class="input">
							<input type="password" name="oldpswd" maxlength="30" value=""/>
						</span>
					</div>
					<div class="place" style="width: 200px; margin-right: 2%;">
						<label>Новый пароль</label>
						<span class="input">
							<input type="password" name="newpswd" maxlength="30" value=""/>
						</span>
					</div>
					<div class="place" style="width: 200px;">
						<label>&nbsp;</label>
						<span class="forbutton">
							<span>
								<input class="button big" type="submit" name="profilepswd" value="Сохранить"/>
							</span>
						</span>
					</div>
				</form>
				<span class="clear"></span>
				<?
			}
		}
		?>
		<span class="clear"></span>
		<?
		if ((@in_array('610',$group['access'])) || (isset($group['settings']['superaccess']))){
		?>
		<div class="hr"><hr /></div>
		<a href="/manage/access/users/" class="button">Перейти к списку пользователей</a>
		<?
		}
		?>
	</div>
<?include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/footer.php";?>

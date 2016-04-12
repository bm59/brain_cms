<?
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/meta.php";?>

<body id="login">
	<div id="loginform">
		<form name="enter" action="/manage/" method="POST" class="forms">
            <?
            if (setting('admin_logo')>0)
            $image=$Storage->getfile(setting('admin_logo'));
            if ($image['path']!='')
            {            ?><a href="/manage/"><img src="<?=$image['path']?>" width="206px;"></a><?
            }
            else{?><a href="/manage/"><img src="/pics/logo_cms.png"></a><?}?>

			<br/></span>
			<!--//<div><p>Для входа в панель управления<br />вам необходимо авторизироваться</p></div>//-->
			<div class="formmargin">
				<div class="place" style="float: left; width: 49%;">
					<label>Логин</label>
					<input name="login" value="<?=$_POST['login']?>" autocomplete="off" type="text"/>
				</div>
				<div class="place" style="float: right; width: 49%;">
					<label>Пароль</label>
					<input name="password" class="inp-pass" type="password" value="" /></span>
				</div>
				<span class="clear"></span>
				<div class="place">
					<div class="styled">
						<input type="checkbox" name="saveme" id="checkbox" class="checkbox">
						<label for="checkbox">запомнить</label>
					</div>

					<div style="float: right; padding-right: -2px;">
						<input type="submit" style="float: right;" value="Вход" class="button big" name="enter">
					</div>
				</div>
			</div>
		</form>
	</div>
</body>
<script>
	$(document).ready(function(){		 $( "input[name='login']" ).focus();	});
</script>
</html>
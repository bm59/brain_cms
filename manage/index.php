<?
include $_SERVER['DOCUMENT_ROOT']."/inc/include.php";
include $_SERVER['DOCUMENT_ROOT']."/inc/content/meta.php";?>

<body id="login">
	<div id="loginform">
		<div class="bg png"></div>
		<form name="enter" action="/manage/" method="POST" class="forms">
			<span class="logo"><img src="/pics/logo.jpg" alt=""  /><br/></span>
			<!--//<div><p>Для входа в панель управления<br />вам необходимо авторизироваться</p></div>//-->
			<div class="formmargin">
				<div class="place" style="float: left; width: 49%;">
					<label>Логин</label>
					<span class="input">
						<span class="bl"></span>
						<span class="bc"><input name="login" value="" autocomplete="off"/></span>
						<span class="br"></span>
					</span>
				</div>
				<div class="place" style="float: right; width: 49%;">
					<label>Пароль</label>
					<span class="input">
						<span class="bl"></span>
						<span class="bc"><input name="password" class="inp-pass" type="password" value="" /></span>
						<span class="br"></span>
					</span>
				</div>
				<span class="clear"></span>
				<div class="place">
					<span class="forckecks" style="float: left;">
						<label><input name="saveme" type="checkbox" />Запомнить меня</label>
					</span>
					<span class="button big" style="float: right;">
						<span class="bl"></span>
						<span class="bc">Войти</span>
						<span class="br"></span>
						<input type="submit" name="enter" value="" />
					</span>
				</div>
			</div>
		</form>
	</div>
</body>
<script>
	addEvent(window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null, "load", function(){ if (form = document.forms['enter']) form.login.focus(); });
</script>
</html>
<?
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=windows-1251" />
		<meta http-equiv="content-language" content="ru" />
		<meta name="title" content="Страница не найдена" />
		<meta name="description" content="Страница не найдена" />
		<meta name="keywords" content="Страница не найдена" />
		<link rel="icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<title>Страница не найдена. Ошибка 404</title>
	</head>
<body id="error404" style="background-color: #F9F9F6;">
	<div style="text-align: center;">
			<div style="text-align: center; margin: 0px auto 30px; width: 600px; padding: 30px 0;">

  				<div><a href="/"><img src="<?=((file_exists($_SERVER['DOCUMENT_ROOT'].'/pics/logo.png')) ? '/pics/logo.png' : '/pics/404.png')?>"></a></div>
			<h1>Страница не найдена!</h1>
			<p>Возможно, вы ошиблись, набирая адрес, либо данная страница была удалена.</p>
			<div><a href="/">Перейти на главную страницу</a></div>
			<div><a href="javascript:history.go(-1)">Вернуться к предыдущей странице</a></div>
		</div>
	</div>
</body>
</html>
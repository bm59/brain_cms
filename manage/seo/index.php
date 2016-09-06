<!DOCTYPE html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Language" content="ru">

<link rel="stylesheet" href="/css/manage.css" type="text/css" media="screen" />

<script src="/js/jquery.js" type="text/javascript"></script>

<script type="text/javascript">
$(function() {
	$("#makelist").click(function() {
		$("#form1").submit();
	});
	
});
</script>
</head>

<body style="padding: 40px;">
<form id="form1" name="form1" method="post" action="">
	<label>Список запросов:</label>
  	<textarea name="list" id="list" rows="10" style="width: 100%;"></textarea>
  	
  	 <a href="#" class="button" id="makelist">Сформировать список</a>
</form>
<?
function clear_digits ($input)
{
	$return=$input;
	
	
 	if (strpos($input, '	')>0)
 	$return=substr($input, 0, strpos($input, '	'));
 	
 	/* print $input.'= '.$return; */
 	
 	return $return;
}

if (isset($_POST['list']))
{
    $array=explode("\n", $_POST['list']);
    
    foreach ($array as $item)
    {
    	print clear_digits($item).'<br/>';
    }	
}
?>
</body>
</html>
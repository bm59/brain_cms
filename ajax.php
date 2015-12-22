<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");?>
<?
mysql_query("SET NAMES cp1251"); // для mysql
header("Content-type: text/html; charset=windows-1251");


if (session_id()!=$_POST['session_id']) die('Ошибка!!!');



if ($_POST['action']=='send_vote' && $_POST['answer_id']>0 && $_POST['vote_id']>0 && $_POST['pt']>0 && $_POST['pt']<date('U'))
{
	$voting_iface=getiface('/sitecontent/voting/');
	$voting_Section = $SiteSections->get($SiteSections->getIdByPath('/sitecontent/voting/'));
	
	$voting=$voting_iface->get();
	$answer=$voting_iface->getAnswer($_POST['answer_id']);
	if ($voting['id']>0 && $answer['id']>0)
	{
		$_SERVER['time_page']=$_POST['pt'];
		$_SERVER['page']=$_POST['page'];
		
		
		$add_vote=$voting_iface->addVote($answer['id'], $voting['id'], $_SERVER);
		if ($add_vote==false) $alert='Ошибка. С вашего компьютера или IP адреса уже голосовали';
		
		/* Если уже голосовали или добавили голос устанавливаем куку */
		if ($add_vote==false) $add_vote=1;
		cookieSet('vid'.$_POST['vote_id'], $add_vote, 30);
		
		$show_total=$voting_Section['settings_personal']['show_vote_count'];
			
		$vote=str_replace('"', '\'', $voting_iface->getTotalHtml($voting['id'], $show_total));
			print
			'{
				"result": '.json_encode(iconv('windows-1251', 'UTF-8',$vote)).',
				"alert": "'.$alert.'",
				"ok": "ok"';
				$ok=true;
		
			
	}
	
		
}

if ($ok)
{
	print '
				}';
}

?>
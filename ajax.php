<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?
mysql_query("SET NAMES utf8"); // для mysql
header("Content-type: text/html; charset=utf-8");


if (session_id()!=$_POST['session_id']) die('Ошибка!!!');

$section_basket=$SiteSections->getByPattern('PBasket');
$basket=getIface($SiteSections->getPath($section_basket['id']));


if ($_POST['action']=='get_price_search')
{
	
	if ($_POST['param']!='')
	{
		$sql='';
		$params=explode('|', $_POST['param']);
		foreach ($params as $par)
		{
			$from_to=explode(',', $par);
			
			if ($from_to[0]>=0 && $from_to[1]>0)
			$sql.=($sql=='' ? '':' or ')."(`price`>='".$from_to[0]."' and `price`<='".$from_to[1]."')";
		}
	}
	
	if ($_POST['cat_id']>0)
	{
		if ($sql!='') $sql='('.$sql.')';
		$sql.=($sql!='' ? ' and ':'')."`categs` like '%,".$_POST['cat_id'].",%'";
	}
		
	if ($sql!='') $sql='WHERE '.$sql;	
	
	$good_iface=getIface('/sitecontent/goods/');
	
	$list=$good_iface->getList(1, $sql);
	foreach ($list as $r)
	{
		$good_iface->PrintGood($r);
	}

}

if ($_POST['action']=='basket_add' && $_POST['tmp_order_id']>0 && $_POST['good_id']>0)
{
	
	$return=$basket->addGood($_POST['tmp_order_id'], $_POST['good_id'], $_POST['kol'], $_POST['price_field'], $_POST['size_id'], $_POST['color_id']);
	
	
	if (is_array($return) && $return['error']!='') print json_encode(array('error'=>$return['error']));
	else print json_encode(array('ok'=>'ok'));
}

if ($_POST['action']=='basket_comment' && $_POST['tmp_order_id']>0)
{
	$return=$basket->GetTotalBasketComment($_POST['tmp_order_id']);
	print json_encode(array('comment'=>$return));
	
}

if ($_POST['action']=='basket_update_summ' && $_POST['tmp_order_id']>0 && $_POST['good_id']>0)
{
	$return=$basket->GetGoodBasket($_POST['tmp_order_id'], $_POST['good_id']);

	print json_encode(array('summ'=>$return['summ']));

}

if ($_POST['action']=='get_discount' && $_POST['discount_id']>0)
{
	$section_discount=$SiteSections->get($SiteSections->getIdByPath('/sitecontent/basket/discount/'));
	$discount_iface=getIface($SiteSections->getPath($section_discount['id']));
	
	$discount=$discount_iface->getPub($_POST['discount_id']);
	print json_encode(array('discount'=>$discount['procent']));

}

/* if ($_POST['action']=='send_vote' && $_POST['answer_id']>0 && $_POST['vote_id']>0 && $_POST['pt']>0 && $_POST['pt']<date('U'))
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

		if ($add_vote==false) $add_vote=1;
		cookieSet('vid'.$_POST['vote_id'], $add_vote, 30);

		$show_total=$voting_Section['settings_personal']['show_vote_count'];

		$vote=str_replace('"', '\'', $voting_iface->getTotalHtml($voting['id'], $show_total));
			print
			'{
				"result": '.json_encode($vote).',
				"alert": "'.$alert.'",
				"ok": "ok"';
				$ok=true;


	}


} */



?>
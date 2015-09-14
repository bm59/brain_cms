<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include_noauth.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");?>
<?
         mysql_query("SET NAMES cp1251"); // для mysql
		 header("Content-type: text/html; charset=windows-1251");
		 
		 
		 if (session_id()!=$_POST['session_id']) die('Ошибка!!!');
		 
		 if ($_POST['action']=='onoff' && $_POST['id']>0 && $_POST['table']!='')
		 {
		 	$cur_item=msr(msq("SELECT * FROM `".$_POST['table']."` WHERE `id`=".$_POST['id']));
		 	
		 	if ($cur_item['id']>0)
		 	{
			 	
		 		msq("UPDATE `".$_POST['table']."` SET `show`='".(($cur_item['show']==1) ? 0 : 1)."' WHERE `id`=".$cur_item['id']);
		 		$signal=$cur_item['show']==1 ? 'off.png':'on.png';
			 	print
			 	'{
				   "signal": "'.$signal.'",
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
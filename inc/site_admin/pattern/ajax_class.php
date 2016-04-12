<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include_noauth.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/ajax_securuty.php");?>
<?
         mysql_query("SET NAMES uft8"); // для mysql
		 header("Content-type: text/html; charset=utf-8");


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

		 if ($_POST['action']=='delitem' && $_POST['id']>0 && $_POST['table']!='')
		 {
		 	$cur_item=msr(msq("SELECT * FROM `".$_POST['table']."` WHERE `id`=".$_POST['id']));

		 	if ($cur_item['id']>0)
		 	{

		 		msq("DELETE FROM `".$_POST['table']."` WHERE `id`=".$cur_item['id']." LIMIT 1");
		 		print
		 		'{
				   "ok": "ok"';
		 		$ok=true;
		 	}

		 }

		 if ($_POST['action']=='edit_field' && $_POST['section_id']>0 && $_POST['id']>0 && isset($_POST['value']) && $_POST['field_name']!='')
		 {
		 		$tab=getTableById($_POST['section_id']);
		 		

		 		if ($tab!='')
				msq("UPDATE `$tab` SET `".$_POST['field_name']."`='".htmlspecialchars( $_POST['value'])."' WHERE id='".$_POST['id']."' LIMIT 1");

		 }

		 if ($ok)
		 {
		 	print '
				}';
		 }

?>
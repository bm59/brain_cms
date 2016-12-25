<?php
include $_SERVER['DOCUMENT_ROOT']."/inc/site_admin/include.php";

session_start();
header("Content-type: text/html; charset=uft-8");

		$item_array=array();
		$comment='';

		$f=fopen($_FILES['upl_file']['tmp_name'], 'r') or die("Невозможно открыть файл!");
		while(!feof($f))
		{
			$text = fgets($f,999);

			if (trim($text)!='') $item_array[]=trim($text);

		}
		fclose($f);


		$double_count=0;
		$add_count=0;
		if (count($item_array)>0)
		{
			$SiteSettings = new SiteSettings;
			$SiteSettings->init();

			$Section = $SiteSections->get($_POST['section_id']);

			$Section['id'] = floor($Section['id']);
			if ($Section['id']>0)
			{
				$Pattern = new $Section['pattern'];
				$Iface = $Pattern->init(array('section'=>$Section['id']));
			}

			if ($Section['id']>0)
			foreach ($item_array as $item)
			{
				$store_item=msr(msq("SELECT * FROM `".$Iface->getSetting('table')."` WHERE `name`='".$item."'"));
				if ($store_item['id']>0)
				$double_count++;
				else
				{
					msq("INSERT INTO `".$Iface->getSetting('table')."` (`show`,`name`) VALUES ('1','".$item."')");
					$add_count++;
				}
			}
		}

		$comment='Всего записей: '.count($item_array).'; добавлено: '.floor($add_count).'; дублей: '.floor($double_count);
		print json_encode(array('result'=>'ok','count'=>count($item_array), 'double_count'=>floor($double_count), 'comment'=>$comment));
?>
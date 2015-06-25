<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?
		 /*Подтверждение заказа*/
		 if ($_GET['action']=='get_files' && $_GET['folder']!='')
		 {

			    $i=0;
			    $result='';

			    $folders=explode('|',$_GET['folder']);
			   /* print_r($folders);*/
			    foreach ($folders as $fo)
			    if ($fo!='')
			    {
				    $d = dir($_SERVER['DOCUMENT_ROOT'].$fo);
				    while (($e = $d->read()) !== false)
				    if (!is_dir($_SERVER['DOCUMENT_ROOT'].$fo.$e))
				    {
	                    $result.=$fo.$e.'|';
				   		$i++;
				   	}
			   	}




           	print
			'{
			   "count": "'.$i.'",
			   "result" : "'.$result.'",
			   "ok": "ok",';
			   $ok=true;



		 }

		if ($ok)
		{
			print '

				}';
		}
?>
<?

	$prec=0;
	$saved_id=array();

	foreach ($_POST as $k=>$v)
	{

	   if (preg_match('|^image_file[0-9]+$|',$k) && $v!='')
	   {
	           $prec++;

	           $num=preg_replace('|^image_file([0-9]+)$|','\\1',$k);

	           $file_name=basename($_POST['image_file'.$num]);
	           $new_filename=$file_name;
	           $file_path=str_replace($file_name,'',$_POST['image_file'.$num]);
	           if (stripos($file_name, '_temp')>0)
	           {	           		$new_filename=str_replace('_temp', '', $file_name);
	           		rename($_SERVER['DOCUMENT_ROOT'].$_POST['image_file'.$num], $_SERVER['DOCUMENT_ROOT'].$file_path.$new_filename);
	           }

	           $cur_id=msr(msq("SELECT * FROM `dop_image` WHERE `id`='".$num."'"));

	           $data=array ('prec'=>$prec, 'good_id'=>$pub['id'], 'image_file'=>$new_filename, 'image_name'=>$_POST['image_name'.$num], 'image_descr'=>$_POST['image_descr'.$num]);

	           if (!$cur_id['id']>0)
	           {
	           		msq(get_insert_sql($data, 'dop_image'));
	           		$saved_id[]=mslastid;
	           }
	           else
	           {
	           		msq(get_update_sql($data, 'dop_image', 'WHERE `id`='.$cur_id['id']." LIMIT 1"));
	           		$saved_id[]=$cur_id['id'];
	           }

	   }
	}

	/*Удаляем файлы которые были удалены пользователем*/
	$deleted_images=msq("SELECT * FROM `dop_image` WHERE `good_id`='".$pub['id']."'".((count($saved_id)>0) ?  " and id not in (".implode(',',$saved_id).")" : ""));
	if (mysql_num_rows($deleted_images)>0)
	while ($del=msr($deleted_images))
	{		msq("DELETE FROM `dop_image` WHERE `id`=".$del['id']." LIMIT 1");
		$filename = $_SERVER['DOCUMENT_ROOT'].'/storage/image_ajax/'.$del['image_file'];
		@unlink($filename);
	}



    /*Очищаем папку от старых временных файлов*/
    $dir = $_SERVER['DOCUMENT_ROOT']."/storage/image_ajax/";
    $d=dir($dir);
    while (($e = $d->read()) !== false)
    if (!is_dir("$dirname/$e"))
    {
   		if (stripos($e, '_temp')!==false)
   		{
   			$hour = time()-filemtime($dir.$e) ;
   			$hour=round( $hour / 3600 );

   			if ($hour>24)
   			{            	@unlink($dir.$e);
   			}
   		}
   	}


?>
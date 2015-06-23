<?
	$prec=0;

	foreach ($_POST as $k=>$v)
	{
	   if (preg_match('|^size_val[0-9]+$|',$k) && $v!='')
	   {
	   		 $prec++;
	   		 $num=preg_replace('|^size_val([0-9]+)$|','\\1',$k);

	   		   $cur_id=msr(msq("SELECT * FROM `dop_size` WHERE `id`='".$num."'"));

	           $data=array ('prec'=>$prec, 'good_id'=>$pub['id'], 'size_val'=>$_POST['size_val'.$num], 'size_price'=>$_POST['size_price'.$num], 'size_descr'=>$_POST['size_descr'.$num]);

	           if (!$cur_id['id']>0)
	           {
	           		msq(get_insert_sql($data, 'dop_size'));
	           		$saved_id[]=mslastid;
	           }
	           else
	           {
	           		msq(get_update_sql($data, 'dop_size', 'WHERE `id`='.$cur_id['id']." LIMIT 1"));
	           		$saved_id[]=$cur_id['id'];
	           }


	   }
	}


	msq("DELETE FROM `dop_size` WHERE `good_id`='".$pub['id']."'".((count($saved_id)>0) ?  " and id not in (".implode(',',$saved_id).")" : ""));


?>
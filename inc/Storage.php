<?
/*
Класс хранилища данных (изображения и файлы)
В каждом хранилище можно устанавливать ограничение на типы и размеры загружаемых файлов
*/
class Storage extends VirtualClass
{
	function init(){
		$this->Settings['table'] = mstable(ConfigGet('pr_name').'_storages','info','',array(
			"path"=>"TEXT",
			"name"=>"TEXT",
			"settings"=>"TEXT"
		));
		$this->Settings['files'] = mstable(ConfigGet('pr_name').'_storages','files','',array(
			"stid"=>"TEXT",
			"name"=>"VARCHAR(255)",
			"theme"=>"VARCHAR(255)",
			"rubric"=>"VARCHAR(255)",
			"uid"=>"BIGINT(20)",
			"settings"=>"TEXT"
		));
		$this->Settings['maxfilesize'] = 10*1024; // Максимальный размер загружаемых файлов по умолчанию (в Кб)
		$this->Settings['imgsizestypes'] = array("0"=>"Не имеет значения","1"=>"Равно","2"=>"Меньше или равно"); // Типы ограничений на размеры изображения

		$this->getAllStrorages();
	}
	function renameFile($id,$theme,$rubric,$uid){
		if ($file = $this->getFile($id)){
			if ($storageobj = $this->getStorage($file['storage'])){
				$name = $file['id'].getUniqueStr(12).'.'.$file['ext'];
				if (@copy($file['fullpath'],$storageobj['fullpath'].$name)){
					@unlink($file['fullpath']);
					$theme = trim($theme);
					if (!preg_match("|^[a-zA-Z_0-9]+$|",$theme)) $theme = '';
					$rubric = trim($rubric);
					if (!preg_match("|^[a-zA-Z_0-9]+$|",$rubric)) $rubric = '';
					$uid = floor($uid);
					msq("UPDATE `".$this->getSetting('files')."` SET `name`='$name',`theme`='$theme',`rubric`='$rubric',`uid`='$uid' WHERE `id`='".$file['id']."'");
					return true;
				}
			}
		}
		return false;
	}
	function deleteFile($id){
		if ($file = $this->getFile($id)){
			@unlink($file['fullpath']);
			@unlink(str_replace('.'.$file['ext'], '_mini.'.$file['ext'], $file['fullpath']));
			msq("DELETE FROM `".$this->getSetting('files')."` WHERE `id`='".$file['id']."'");
		}
		return false;
	}
	function uploadFile($stid,$theme,$rubric,$uid,$file,$str_settings=''){
		global $CDDataSet;
		$retval = array('errors'=>array());
		$storageobj = $this->getStorage($stid);
		if (floor($storageobj['id'])<1) $retval['errors'][] = 'Не удается открыть хранилище файлов';
		$theme = trim($theme);

		$rubric = trim($rubric);
		if (!preg_match("|^[a-zA-Z_0-9]+$|",$rubric)) $rubric = '';
		$uid = floor($uid);
		$ext = lower(trim(preg_replace("/.*?\./","",basename($file['name']))));
		if (($ext=='') || (floor($file['size'])==0)) $retval['errors'][] = 'Не удается загрузить файл на сервер';
		$filename = lower("temp_".getUniqueStr(10).".".$ext);
		if (floor($file['size']/1024)>$storageobj['settings']['maxsize']) $retval['errors'][] = 'Размер загружаемого файла ('.getFileSizeString($file['size']).') больше максимально допустимого ('.getFileSizeString($storageobj['settings']['maxsize']*1024).')';
		$imgsize = @getimagesize($file['tmp_name']);
		$exts = array();


        /*Проверяем ограничения на размер изображения*/
		$section_id=preg_replace('|^([a-z]+)\_([0-9]+)$|','\\2',$theme);
        $dataset_name=preg_replace('|^([a-z]+)\_([0-9]+)$|','\\1',$theme);
		{			$data_set=msr(msq("SELECT * FROM `site_site_data_sets` WHERE `name`='".$dataset_name."'"));
			$data_type=msr(msq("SELECT * FROM `site_site_data_types` WHERE `dataset`=".$data_set['id']." and `section_id`=$section_id and `name`='".$rubric."'"));

			if ($str_settings!='') $data_type['settings']=$str_settings;

			if ($data_type['settings']!='')
			{				$mysql_settings=$this->explode($data_type['settings']);
			}


			if ($mysql_settings['imgw']!='') $storageobj['settings']['imgw']=$mysql_settings['imgw'];
            if ($mysql_settings['imgwtype']!='') $storageobj['settings']['imgwtype']=$mysql_settings['imgwtype'];
            if ($mysql_settings['imgh']!='') $storageobj['settings']['imgh']=$mysql_settings['imgh'];
            if ($mysql_settings['imghtype']!='') $storageobj['settings']['imghtype']=$mysql_settings['imghtype'];

            /*При автоматической обрезке, исходное изображение не должно быть меньшего размера*/
            if ($mysql_settings['auto_resize']=='true')
            {            	if ($imgsize[0]<$mysql_settings['auto_width'])  $retval['errors'][] = 'Ширина загружаемого изображения '.floor($imgsize[0]).'px, а должна быть больше или равна '.$mysql_settings['auto_width'].'px';
                if ($imgsize[1]<$mysql_settings['auto_height'])  $retval['errors'][] = 'Высота загружаемого изображения '.floor($imgsize[1]).'px, а должна быть больше или равна '.$mysql_settings['auto_height'].'px';
            }

		}


		foreach(explode(',',$mysql_settings['exts']) as $v) if (trim($v)!='') $exts[] = lower(trim($v));

		if (count($exts)>0) if (!in_array($ext,$exts)) $retval['errors'][] = 'Допустимые расширения файла: '.implode(', ',$exts);
		if (isset($storageobj['settings']['images'])) if (!$imgsize) $retval['errors'][] = 'Файл не является корректным изображением';


		if ($ext!='swf'){
			if (floor($storageobj['settings']['imgw'])>0){
				$imgw = floor($storageobj['settings']['imgw']);
				if (floor($storageobj['settings']['imgwtype'])==1) if (floor($imgsize[0])!=$imgw) $retval['errors'][] = 'Ширина загружаемого изображения '.floor($imgsize[0]).'px, а должна быть равной '.$imgw.'px';
				if (floor($storageobj['settings']['imgwtype'])==2) if (floor($imgsize[0])>$imgw) $retval['errors'][] = 'Ширина загружаемого изображения '.floor($imgsize[0]).'px, а должна быть меньше или равна '.$imgw.'px';
				if (floor($storageobj['settings']['imgwtype'])==3) if (floor($imgsize[0])<$imgw) $retval['errors'][] = 'Ширина загружаемого изображения '.floor($imgsize[0]).'px, а должна быть больше или равна '.$imgw.'px';
			}
			if (floor($storageobj['settings']['imgh'])>0){
				$imgh = floor($storageobj['settings']['imgh']);
				if (floor($storageobj['settings']['imghtype'])==1) if (floor($imgsize[1])!=$imgh) $retval['errors'][] = 'Высота загружаемого изображения '.floor($imgsize[1]).'px, а должна быть равной '.$imgh.'px';
				if (floor($storageobj['settings']['imghtype'])==2) if (floor($imgsize[1])>$imgh) $retval['errors'][] = 'Высота загружаемого изображения '.floor($imgsize[1]).'px, а должна быть меньше или равна '.$imgh.'px';
			    if (floor($storageobj['settings']['imghtype'])==3) if (floor($imgsize[1])<$imgh) $retval['errors'][] = 'Высота загружаемого изображения '.floor($imgsize[1]).'px, а должна быть больше или равна '.$imgh.'px';
			}
		}
		if (count($retval['errors'])==0){
			if ((@move_uploaded_file($file['tmp_name'],$storageobj['fullpath'].$filename)) || (@rename($file['tmp_name'],$storageobj['fullpath'].$filename))){
				msq("INSERT INTO `".$this->getSetting('files')."` (`stid`,`name`,`theme`,`rubric`,`uid`,`date`) VALUES ('".$storageobj['id']."','$filename','$theme','$rubric','$uid', NOW())");


				/*Обрезка изображений*/

				if ($mysql_settings['auto_resize']==true)
				{							if ($mysql_settings['auto_width']>0 && $mysql_settings['auto_height']>0)
							{
								$this->ResizeFrame($storageobj['fullpath'].$filename, $mysql_settings['auto_width'],$mysql_settings['auto_height']);
								$this->Crop($storageobj['fullpath'].$filename, $mysql_settings['auto_width'],$mysql_settings['auto_height']);
							}
				}



				$uniqueid = mslastid();
				$retval['id'] = $uniqueid;
				$retval['name'] = $filename;
				$retval['ext'] = preg_replace("/.*?\./","",$retval['name']);
				$retval['path'] = $storageobj['path'].$retval['name'];
				$retval['fullpath'] = $_SERVER['DOCUMENT_ROOT'].$retval['path'];
				$retval['size'] = floor(@filesize($retval['fullpath']));
				$retval['sizestr'] = getFileSizeString($retval['size']);
				$imgsize = @getimagesize($retval['fullpath']);
				if (floor($storageobj['settings']['imgwtype'])==1) $retval['width'] = floor($storageobj['settings']['imgw']);
				elseif ($ext=='swf') $retval['width'] = '100%';
				else $retval['width'] = floor($imgsize[0]);
				if (floor($storageobj['settings']['imghtype'])==1) $retval['height'] = floor($storageobj['settings']['imgh']);
				elseif ($ext=='swf') $retval['height'] = '100%';
				else $retval['height'] = floor($imgsize[1]);
			}
			else $retval['errors'][] = 'Не удается скопировать файл в хранилище, возможно установлен запрет на запись';
		}
		return $retval;
	}
    function ResizeFrame($src=null,$maxWidth=null,$maxHeight=null) {


	    list($w_i, $h_i, $type) = getimagesize($src);


	    $types = array("", "gif", "jpeg", "png");
		$ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа

	    if ($ext) {
	      $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
	      $save_func='image'.$ext;
	      $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
	    } else {
	      echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
	      return false;
	    }


	    $srcimg = $func($src);
	    $srcsize = getimagesize($src);

	    if ($srcsize[0]<=$maxWidth && $srcsize[1]<=$maxHeight) return true;


	    /*Если пропорция по X больше*/
	    if (($srcsize[0]/$maxWidth) > ($srcsize[1]/$maxHeight))
	    {
	    	$dest_y = $maxHeight;
	    	$dest_x = ($maxHeight / $srcsize[1]) * $srcsize[0];
	    	$thumbimg = imagecreatetruecolor($dest_x, $dest_y);
	    	imageAlphaBlending($thumbimg, false);
			imageSaveAlpha($thumbimg,true);
	    	imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    	$resultimg= $save_func($thumbimg,$src);
	    }
	    else
	    {
	    	$dest_x = $maxWidth;
	    	$dest_y = ($maxWidth / $srcsize[0]) * $srcsize[1];
	    	$thumbimg = imagecreatetruecolor($dest_x, $dest_y);
     		imageAlphaBlending($thumbimg, false);
			imageSaveAlpha($thumbimg,true);
	    	imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    	$resultimg = $save_func($thumbimg,$src);
	    }

    }
    function crop($src=null,$maxWidth=null,$maxHeight=null) {


	    list($w_i, $h_i, $type) = getimagesize($src);


	    $types = array("", "gif", "jpeg", "png");
		$ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа

	    if ($ext) {
	      $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
	      $save_func='image'.$ext;
	      $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
	    } else {
	      echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
	      return false;
	    }


	    $srcimg = $func($src);
	    $srcsize = getimagesize($src);

        /*Если обрезка по высоте*/
        if ($srcsize[1]>$maxHeight)
        {
        	$y_pos=($srcsize[1]-$maxHeight)/2;
        	$srcsize[1]=($srcsize[1]-$y_pos*2);
        }
        else
        {        	$x_pos=($srcsize[0]-$maxWidth)/2;
        	$srcsize[0]=($srcsize[0]-$x_pos*2);
        }

	    /*Копируем центр обрезанного изображения*/
	    $thumbimg = imagecreatetruecolor($maxWidth, $maxHeight);
	    imageAlphaBlending($thumbimg, false);
		imageSaveAlpha($thumbimg,true);
	    imagecopyresampled($thumbimg,$srcimg,0,0,floor($x_pos),floor($y_pos),$maxWidth,$maxHeight, $srcsize[0], $srcsize[1]);
	    $thumbimg = $save_func($thumbimg,$src);

    }
	function getFile($id){
		$id = floor($id);

		/*if ($retval = $this->getCacheValue('storage_file_'.$id)) return $retval;*/
		//echo "SELECT * FROM `".$this->getSetting('files')."` WHERE `id`='$id'";
		$retval = array();
		if ($r = msr(msq("SELECT * FROM `".$this->getSetting('files')."` WHERE `id`='$id'"))){
			if ($st = $this->getStorage($r['stid'])){
				$retval['id'] = $id;
				$retval['storage'] = $st['id'];
				$retval['name'] = lower($r['name']);
				$retval['ext'] = preg_replace("/.*?\./","",$retval['name']);
				$retval['path'] = $st['path'].$retval['name'];
				$retval['fullpath'] = $_SERVER['DOCUMENT_ROOT'].$retval['path'];
				if (floor($st['settings']['imgwtype'])==1) $retval['width'] = floor($st['settings']['imgw']);
				elseif ($retval['ext']=='swf') $retval['width'] = '100%';
				elseif ($imgsize = @getimagesize($retval['fullpath'])) $retval['width'] = floor($imgsize[0]);
				if (floor($st['settings']['imghtype'])==1) $retval['height'] = floor($st['settings']['imgh']);
				elseif ($retval['ext']=='swf') $retval['height'] = '100%';
				elseif ($imgsize = @getimagesize($retval['fullpath'])) $retval['height'] = floor($imgsize[1]);


				if (!file_exists($retval['fullpath'] ))
				$retval['path'] = '';

				$this->setCacheValue('storage_file_'.$id, $retval) ;
			}

			//
		}
		return $retval;
	}
	function getIdByName($name){
		$retval = 0;
		$name = trim($name);
		if (preg_match("|^[a-z\._0-9]+$|",$name)) if ($r = msr(msq("SELECT * FROM `".$this->getSetting('files')."` WHERE `name`='$name'"))) $retval = $r['id'];
		return $retval;
	}

	function getAllStrorages() {
        $res =(msq("SELECT * FROM `".$this->getSetting('table')."` "));


        while($row = msr($res)) {
				$row ['fullpath'] = $_SERVER['DOCUMENT_ROOT'].$row ['path'];
				$row ['settings'] = $this->explode($row ['settings']);
				$this->setCacheValue('storage_'.md5($row ['path']),$row );
				$this->setCacheValue('storage_'.$row ['id'],$row );
        }


	}
	function delete_tmp_files() {
		$res =msq("SELECT * FROM `site_storages_files` WHERE `name` like '%temp_%' and datediff( now( ) , `date`  )>1");

		while($row = msr($res)) {
			$this->deleteFile($row['id']);
			msq("DELETE FROM `site_storages_files` WHERE id=".$row['id']);
		}


	}

	function getStorage($id,$create = array()){ // Добавляет новое хранилище, либо возвращает уже созданное
		$imgsizestypes = $this->getSetting('imgsizestypes');
		$id = floor($id);

		$path = '/storage/';
		if(!empty($id)) {
            if ($retval = $this->getCacheValue('storage_'.$id)) return $retval; // Если нашли в кэше
		} else {
            $path=$path.trim($create['path'],"/")."/";
            if ($retval = $this->getCacheValue('storage_'.md5($path))) return $retval; // Если нашли в кэше

		}
		$retval = array();
		$id = $this->checkPresence($id);
		$checkedid = ($id>0)?$id:0;
		if ($checkedid==0){ // Если не обнаружили по ID
			$errors = array();
			if (!is_array($create)) $create = array();
			$path = '/storage/';
			foreach (explode('/',$create['path']) as $v)
                if ((trim($v)!='') && (preg_match('|[a-z_0-9]+|',$v))) $path.= $v.'/';

			if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `path`='$path'"))) $checkedid = $r['id'];
			if ($checkedid==0){ // Если не обнаружили по требуемому пути
				if ($path=='/storage/') $errors['path'] = 'Неверный путь';
				$create['name'] = htmlspecialchars(trim($create['name']));
				if (count($errors)==0){ // Создание хранилища
					$settings = array();
					if ($create['images']==1) $settings['images'] = '';
					$settings['maxsize'] = (floor($create['size'])>0)?floor($create['size']):$this->getSetting('maxfilesize');
					if (floor($create['imgw'])>0){
						$settings['imgw'] = floor($create['imgw']);
						if ($imgsizestypes[floor($create['imgwtype'])]) $settings['imgwtype'] = floor($create['imgwtype']);
					}
					if (floor($create['imgh'])>0){
						$settings['imgh'] = floor($create['imgh']);
						if ($imgsizestypes[floor($create['imghtype'])]) $settings['imghtype'] = floor($create['imghtype']);
					}
					$exts = array();
					if (is_array($create['exts'])){
						foreach ($create['exts'] as $v) if ((trim($v!='')) && (preg_match('|[a-z0-9]+|',$v))) $exts[] = lower(trim($v));
						if (count($exts)>0) $settings['exts'] = implode(',',$exts);
					}
					if (msq("INSERT INTO `".$this->getSetting('table')."` (`path`,`name`,`settings`) VALUES ('$path','".$create['name']."','".$this->implode($settings)."')")){
						$checkedid = floor(mslastid());
						$temppath = $_SERVER['DOCUMENT_ROOT'].'/';
						foreach (explode('/',$path) as $v){
							if ((trim($v)!='') && (preg_match('|[a-z_0-9]+|',$v))){
								$temppath.= $v.'/';
								@mkdir($temppath,0777);
							}
						}
					}
				}
			}
		}
		if ($checkedid>0){
			if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$checkedid'"))){
				$retval['id'] = $checkedid;
				$retval['name'] = $r['name'];
				$retval['path'] = $r['path'];
				$retval['fullpath'] = $_SERVER['DOCUMENT_ROOT'].$retval['path'];
				$retval['settings'] = $this->explode($r['settings']);
				$this->setCacheValue('storage_'.$checkedid,$retval);
			}
		}
		return $retval;
	}
	function checkPresence($id){ // Провека на существование по ID
		$id = floor($id);
		if (msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='$id'"))) return $id;
		return 0;
	}
	function getListByUID($stid,$theme,$rubric,$uid){
		$retval = array();
		$stid = floor($stid);
		$theme = trim($theme);
		$rubric = trim($rubric);
		$uid = floor($uid);
		$st = $this->getStorage($stid);
		if (floor($st['id'])>0){
			$q = msq("SELECT * FROM `".$this->getSetting('files')."` WHERE `stid`='".floor($st['id'])."' AND `theme`='".addslashes($theme)."' AND `rubric`='".addslashes($rubric)."' AND `uid`='$uid'");
			while ($r = msr($q)) $retval[] = $this->getFile($r['id']);
		}
		return $retval;
	}
}
?>
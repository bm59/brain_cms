<?php
include('config/config.php');
function get_file_name($str)
{
	$ret='';

	$str=html_entity_decode($str);

	$tr = array(
			"А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
			"Д"=>"d","Е"=>"e","Ё"=>"e", "Ж"=>"j","З"=>"z","И"=>"i",
			"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
			"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
			"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"c","Ч"=>"ch",
			"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
			"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"e", "ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
			"!"=>'',":"=>''," - "=>'_'," -"=>'_',"- "=>'_'," - "=>'_',"-"=>'_'," "=> "_", "."=> "", '\\"'=>'', "\\'"=>'', "«"=>'', "»"=>'', "("=>'', ")"=>'', ","=>'', "+"=>'', "\""=>'',
			"/"=>'_', "№"=>'', "#"=>'', "@"=>'', "&"=>'', "%"=>'', "~"=>'',
			"A"=>"a", "B"=>"b", "C"=>"c", "D"=>"d", "E"=>"e", "F"=>"f", "G"=>"g", "H"=>"h", "I"=>"i", "J"=>"j", "K"=>"k", "L"=>"l", "M"=>"m", "N"=>"n",
			"O"=>"o", "P"=>"p", "Q"=>"q", "R"=>"r", "S"=>"s", "T"=>"t", "U"=>"u", "V"=>"v", "W"=>"w", "X"=>"x", "Y"=>"y", "Z"=>"z"
	);
	$ret=strtr($str,$tr);
	
	$ret = preg_replace( "/[^a-zA-Z0-9\.\[\]_| -]/", '', $ret);
	
	return $ret;

}

if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
include('include/utils.php');


$storeFolder = $_POST['path'];
$storeFolderThumb = $_POST['path_thumb'];

$path_pos=strpos($storeFolder,$current_path);
$thumb_pos=strpos($_POST['path_thumb'],$thumbs_base_path);
if($path_pos!==0
    || $thumb_pos !==0
    || strpos($storeFolderThumb,'../',strlen($thumbs_base_path))!==FALSE
    || strpos($storeFolderThumb,'./',strlen($thumbs_base_path))!==FALSE
    || strpos($storeFolder,'../',strlen($current_path))!==FALSE
    || strpos($storeFolder,'./',strlen($current_path))!==FALSE )
    die('wrong path');


$path=$storeFolder;
$cycle=true;
$max_cycles=50;
$i=0;
while($cycle && $i<$max_cycles){
    $i++;
    if($path==$current_path)  $cycle=false;
    if(file_exists($path."config.php")){
	require_once($path."config.php");
	$cycle=false;
    }
    $path=fix_dirname($path).'/';
}


if (!empty($_FILES)) {
    $info=pathinfo($_FILES['file']['name']);
    if(in_array(fix_strtolower($info['extension']), $ext)){
	$tempFile = $_FILES['file']['tmp_name'];

	$targetPath = $storeFolder;
	$targetPathThumb = $storeFolderThumb;
	$_FILES['file']['name'] = fix_filename($_FILES['file']['name'],$transliteration);
	$file_prefix='some_file';
	if (in_array($info['extension'], $ext_img)) 	$file_prefix='image';
	if (in_array($info['extension'], $ext_file)) 	$file_prefix='file';
	if (in_array($info['extension'], $ext_video)) 	$file_prefix='video';
	if (in_array($info['extension'], $ext_music)) 	$file_prefix='music';
	if (in_array($info['extension'], $ext_misc)) 	$file_prefix='archives';

	/* $_FILES['file']['name'] = $file_prefix.'.'.$info['extension']; */
	
	$_FILES['file']['name'] = get_file_name($info['filename']).'.'.$info['extension'];

	
	if(file_exists($targetPath.$_FILES['file']['name'])){
	    $i = 1;
	    $info=pathinfo($_FILES['file']['name']);
	    while(file_exists($targetPath.$info['filename']."_".$i.".".$info['extension'])) {
		    $i++;
	    }
	    $_FILES['file']['name']=$info['filename']."_".$i.".".$info['extension'];
	}
	$targetFile =  $targetPath. $_FILES['file']['name'];
	$targetFileThumb =  $targetPathThumb. $_FILES['file']['name'];

	if(in_array(fix_strtolower($info['extension']),$ext_img)) $is_img=true;
	else $is_img=false;


	move_uploaded_file($tempFile,$targetFile);
	chmod($targetFile, 0755);

	if($is_img){
	    $memory_error=false;
	    if(!create_img_gd($targetFile, $targetFileThumb, 122, 91)){
		$memory_error=false;
	    }else{
		if(!new_thumbnails_creation($targetPath,$targetFile,$_FILES['file']['name'],$current_path,$relative_image_creation,$relative_path_from_current_pos,$relative_image_creation_name_to_prepend,$relative_image_creation_name_to_append,$relative_image_creation_width,$relative_image_creation_height,$fixed_image_creation,$fixed_path_from_filemanager,$fixed_image_creation_name_to_prepend,$fixed_image_creation_to_append,$fixed_image_creation_width,$fixed_image_creation_height)){
		    $memory_error=false;
		}else{
		    $imginfo =getimagesize($targetFile);
		    $srcWidth = $imginfo[0];
		    $srcHeight = $imginfo[1];

		    if($image_resizing){
			if($image_resizing_width==0){
			    if($image_resizing_height==0){
				$image_resizing_width=$srcWidth;
				$image_resizing_height =$srcHeight;
			    }else{
				$image_resizing_width=$image_resizing_height*$srcWidth/$srcHeight;
			}
			}elseif($image_resizing_height==0){
			    $image_resizing_height =$image_resizing_width*$srcHeight/$srcWidth;
			}
			$srcWidth=$image_resizing_width;
			$srcHeight=$image_resizing_height;
			create_img_gd($targetFile, $targetFile, $image_resizing_width, $image_resizing_height);
		    }
		    //max resizing limit control
		    $resize=false;
		    if($image_max_width!=0 && $srcWidth >$image_max_width){
			$resize=true;
			$srcHeight=$image_max_width*$srcHeight/$srcWidth;
			$srcWidth=$image_max_width;
		    }
		    if($image_max_height!=0 && $srcHeight >$image_max_height){
			$resize=true;
			$srcWidth =$image_max_height*$srcWidth/$srcHeight;
			$srcHeight =$image_max_height;
		    }
		    if($resize)
			create_img_gd($targetFile, $targetFile, $srcWidth, $srcHeight);
		}
	    }
	    if($memory_error){
		//error
		unlink($targetFile);
		header('HTTP/1.1 406 Not enought Memory',true,406);
		exit();
	    }
	}
    }else{
	header('HTTP/1.1 406 file not permitted',true,406);
	exit();
    }
}else{
    header('HTTP/1.1 405 Bad Request', true, 405);
    exit();
}
if(isset($_POST['submit'])){
    $query = http_build_query(array(
        'type'      => $_POST['type'],
        'lang'      => $_POST['lang'],
        'popup'     => $_POST['popup'],
        'field_id'  => $_POST['field_id'],
        'fldr'      => $_POST['fldr'],
    ));
    header("location: dialog.php?" . $query);
}

?>

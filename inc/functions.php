<?
function upper($str){ // ������ strtoupper �� �� ���� �������� ��������� �������� :)
	$lower = "abcdefghijklmnopqrstuvwxyz��������������������������������";
	$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ�����Ũ��������������������������";
	$lowerlen = strlen($lower);
	for ($i=0; $i<$lowerlen; $i++) $str = str_replace(substr($lower,$i,1),substr($upper,$i,1),$str);
	return $str;
}
function lower($str){ // ������ strtolower �� �� ���� �������� ��������� �������� :)
	$lower = "abcdefghijklmnopqrstuvwxyz��������������������������������";
	$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ�����Ũ��������������������������";
	$upperlen = strlen($upper);
	for ($i=0; $i<$upperlen; $i++) $str = str_replace(substr($upper,$i,1),substr($lower,$i,1),$str);
	return $str;
}

/* ������� ��������� �������� ������ */
function getMonthRusNameUpper($num){ $months = array("","������","�������","����","������","���","����","����","������","��������","�������","������","�������"); return $months[floor($num)]; }
function getMonthRusNameLower($num){ $months = array("","������","�������","����","������","���","����","����","������","��������","�������","������","�������"); return $months[floor($num)]; }
function getMonthRusNameLowerRP($num){ $months = array("","������","�������","�����","������","���","����","����","�������","��������","�������","������","�������"); return $months[floor($num)]; }
function getMonthRusNameUpperRP($num){ $months = array("","������","�������","�����","������","���","����","����","�������","��������","�������","������","�������"); return $months[floor($num)]; }

function pregtrim($str){ return preg_replace("/[^\x20-\xFF]/","",@strval($str)); } // ������� �������� ������� �������
function checkUrl($url){ // �������� ������������ url, ���������� ���������� URL ��� FALSE
	$url = trim(pregtrim($url));
	if (strlen($url)==0) return false; // ���� ������ url
/*	if (!preg_match("~^(?:(?:https?://|ftp://|telnet://|mailto:)(?:[a-z0-9_-]{1,32}".
					"(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
					"org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
					"!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&".
					"?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i",$url,$ok))
	return false; // ���� url �����������*/
	if (!strstr($url,"://") && !strstr($url,"mailto:")) $url = "http://".$url; // ���� �� ������ �������� � ��������� http://
	$url = preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url); // �������� �������� �� ������ �������: hTtP -> http
	return $url;
}
function checkEmail($mail){ // �������� ������������ email, ���������� ���������� E-mail ��� FALSE
   $mail = trim(pregtrim($mail));
   if (strlen($mail)==0) return false; // ���� ������ email
   if (!preg_match("/^[a-z0-9\._-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|".
   "edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-".
   "9]{1,3}\.[0-9]{1,3})$/is",$mail))
   return false; // ���� email �����������
   return $mail;
}
function checkValidDate($date){ // �������� ������������ ����, ���������� true ��� false
	$date = explode('.',$date);
	$d = floor($date[0]);
	$m = floor($date[1]);
	$y = floor($date[2]);
	if ($y<1) return false;
	if (($m<1) || ($m>12)) return false;
	if (($d<1) || ($d>getMonthDaysCount($m,$y))) return false;
	return true;
}
function getMonthDaysCount($m,$y){ // ���������� ���-�� ���� � ������
	$m = floor($m)-1; if ($m<0) return 31;
	$y = floor($y);
	$VVMonths = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$NVMonths = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($y%4)==0){
		if (($y%100)==0 && ($y%400)!=0) return $VVMonths[$m];
		return $NVMonths[$m];
	}
	else return $VVMonths[$m];
}
function getUniqueStr($length = 0){ // ���������� ��������� ����� �������� �������� �����
	$retval = '';
	$length = floor($length);
	if ($length<1) return '';
	$symbols = "abcdefghijklmnopqrstuvwxyz1234567890";
	for ($i=0; $i<$length; $i++) $retval.= substr($symbols,rand(1,strlen($symbols)),1);
	return $retval;
}
function defaultEmail($email,$text,$theme,$fromemail = ''){ // ����������� ������������ ������
	$email = checkEmail($email);
	if (!$email) $email = configGet("SiteDefaultToEmail");
	$fromemail = checkEmail($email);
	if (!$fromemail) $fromemail = configGet("SiteDefaultFromEmail");
	$text = trim($text);
	if (!$text) return false;
	$theme = htmlspecialchars(trim($theme));
	if (!$theme) return false;
	$header = "From: $fromemail\r\nReply-To: $fromemail\r\n";
	$body = '
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=windows-1251">
			<style>
				body { font-family: tahoma, verdana, helvetica, sans-serif; font-size: 12px; line-height: 1.3; color: #555; }
				h3 { font: bold 15px Arial, sans-serif; color: #004195; margin: 1.3em 0 }
				p { margin: 10px 0 10px 30px }
			</style>
		</head>
		<body>
			<h3>'.$theme.'</h3>
			'.$text.'
		</body>
		</html>';
	return mail($email,$theme,$body,$header. "Content-type: text/html; charset=windows-1251");
}
function sendsmtp ($to, $message,  $subject, $from)
    {
    	    $address = 'smtp.beget.ru'; // ����� smtp-�������
    		$port    = 2525;          // ���� (����������� smtp - 25)

    		$login   = 'sender@nadosushi.ru';    // ����� � �����
    		$pwd     = '1qazxsw';    // ������ � �����

    		/*$subject=iconv('windows-1251', 'utf-8', $subject);*/
    	$message = '
		<html>
		<head>
		</head>
		<body>
			'.$message.'
		</body>
		</html>';

	    	try {

	        // ������� �����
	        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	        if ($socket < 0) {
	            throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
	        }

	        // ��������� ����� � �������
	        /*echo 'Connect to \''.$address.':'.$port.'\' ... ';*/
	        $result = socket_connect($socket, $address, $port);
	        if ($result === false) {
	            throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
	        } else {
	            /*echo "OK\n";*/
	        }

	        // ������ ���������� � �������
	        read_smtp_answer($socket);

	        // ������������ ������
	        write_smtp_response($socket, 'EHLO '.$login);
	        read_smtp_answer($socket); // ����� �������

	        /*echo 'Authentication ... ';*/

	        // ������ ������ �����������
	        write_smtp_response($socket, 'AUTH LOGIN');
	        read_smtp_answer($socket); // ����� �������

	        // ��������� �����
	        write_smtp_response($socket, base64_encode($login));
	        read_smtp_answer($socket); // ����� �������

	        // ��������� ������
	        write_smtp_response($socket, base64_encode($pwd));
	        read_smtp_answer($socket); // ����� �������

	        /*echo "OK\n";
	        echo "Check sender address ... ";*/

	        // ������ ����� �����������
	        write_smtp_response($socket, 'MAIL FROM:<'.$from.'>');
	        read_smtp_answer($socket); // ����� �������

	        /*echo "OK\n";
	        echo "Check recipient address ... ";*/

	        // ������ ����� ����������
	        write_smtp_response($socket, 'RCPT TO:<'.$to.'>');
	        read_smtp_answer($socket); // ����� �������

	        /*echo "OK\n";
	        echo "Send message text ... ";*/

	        // ������� ������ � ������ ������
	        write_smtp_response($socket, 'DATA');
	        read_smtp_answer($socket); // ����� �������

	        // ���������� ������
            $headers  = "Content-Type: text/html; charset=windows-1251\r\n";
	        $headers .= "To: $to\r\n"; // ��������� ��������� ��������� "����� ����������"
	        $headers .= "Subject: $subject\r\n"; // ��������� "���� ���������"
	        write_smtp_response($socket, $headers.$message."\r\n.");
	        read_smtp_answer($socket); // ����� �������

	       /* echo "OK\n";
	        echo 'Close connection ... ';*/

	        // ������������� �� �������
	        write_smtp_response($socket, 'QUIT');
	        read_smtp_answer($socket); // ����� �������

	        /*echo "OK\n";*/

	    }
	    catch (Exception $e) {
	        /*echo "\nError: ".$e->getMessage();*/
	    }

	    if (isset($socket)) {
	        socket_close($socket);
	    }


    }
    // ������� ��� ������ ������ �������. ����������� ���������� � ������ ������
    function read_smtp_answer($socket) {
        $read = socket_read($socket, 1024);

        if ($read{0} != '2' && $read{0} != '3') {
            if (!empty($read)) {
                throw new Exception('SMTP failed: '.$read."\n");
            } else {
                throw new Exception('Unknown error'."\n");
            }
        }
    }

    // ������� ��� �������� ������� �������
    function write_smtp_response($socket, $msg) {
        $msg = $msg."\r\n";
        socket_write($socket, $msg, strlen($msg));
    }
function getFileSizeString($size){ // ���������� �������� �������� ������� (size - � ������)
	$size = floor($size);
	$names = array('�','��','��','��');
	$end = '';
	foreach ($names as $v) if ($end=='') { if ($size>800) $size = $size/1024; else $end = $v; }
	$size = round($size,1);
	return str_replace('.',',',$size).' '.$end;
}
function myspecialchars($s){
	$i = array('|"|','|<|','|>|');
	$r = array('&quot;','&lt;','&gt;');
	return preg_replace($i,$r,security($s));
}
function security ($refers){
	$refers = @eregi_replace("UNION|OUTFILE|FROM|SELECT|WHERE|SHUTDOWN|UPDATE|DELETE|CHANGE|MODIFY|RENAME|RELOAD|ALTER|GRANT|DROP|INSERT|CONCAT|cmd|exec",'',$refers);
	$refers = htmlspecialchars($refers);
	return $refers;
}
function countWords($text){
	$text = preg_replace('#(\w)\-(\w)#','',$text);
	$text = preg_replace('#[\W]#',' ',$text);
	$text = preg_replace('#[\s]+#',' ',$text);
	return count(explode(' ',$text));
}
function phcmp($a,$b){ return ($a['href']==$b['href'])?0:(($a['href']>$b['href'])?-1:1); }
function get_url_text($str)
{
    $ret='';

    $str=html_entity_decode($str);

    $tr = array(
        "�"=>"a","�"=>"b","�"=>"v","�"=>"g",
        "�"=>"d","�"=>"e","�"=>"e", "�"=>"j","�"=>"z","�"=>"i",
        "�"=>"y","�"=>"k","�"=>"l","�"=>"m","�"=>"n",
        "�"=>"o","�"=>"p","�"=>"r","�"=>"s","�"=>"t",
        "�"=>"u","�"=>"f","�"=>"h","�"=>"c","�"=>"ch",
        "�"=>"sh","�"=>"sch","�"=>"","�"=>"yi","�"=>"",
        "�"=>"e","�"=>"yu","�"=>"ya","�"=>"a","�"=>"b",
        "�"=>"v","�"=>"g","�"=>"d","�"=>"e","�"=>"e", "�"=>"j",
        "�"=>"z","�"=>"i","�"=>"y","�"=>"k","�"=>"l",
        "�"=>"m","�"=>"n","�"=>"o","�"=>"p","�"=>"r",
        "�"=>"s","�"=>"t","�"=>"u","�"=>"f","�"=>"h",
        "�"=>"c","�"=>"ch","�"=>"sh","�"=>"sch","�"=>"y",
        "�"=>"yi","�"=>"","�"=>"e","�"=>"yu","�"=>"ya",
        "!"=>'',":"=>''," - "=>'_'," -"=>'_',"- "=>'_'," - "=>'_',"-"=>'_'," "=> "_", "."=> "", '\\"'=>'', "\\'"=>'', "�"=>'', "�"=>'', "("=>'', ")"=>'', ","=>'', "+"=>'', "\""=>'',
        "/"=>'_', "�"=>'', "#"=>'', "@"=>'', "&"=>'', "%"=>'', "~"=>'',
        "A"=>"a", "B"=>"b", "C"=>"c", "D"=>"d", "E"=>"e", "F"=>"f", "G"=>"g", "H"=>"h", "I"=>"i", "J"=>"j", "K"=>"k", "L"=>"l", "M"=>"m", "N"=>"n",
        "O"=>"o", "P"=>"p", "Q"=>"q", "R"=>"r", "S"=>"s", "T"=>"t", "U"=>"u", "V"=>"v", "W"=>"w", "X"=>"x", "Y"=>"y", "Z"=>"z"
    );
    $ret=strtr($str,$tr);

    if (strlen($ret)>100)
    {
    	$ret=substr($ret,0,100);
    	if (strrpos($ret,'_')>0)
    	$ret=substr($ret,0,strrpos($ret,'_'));
    }
    return $ret;

}
/*error_reporting(-1);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);*/

function getSprValues($sprnam,$id=0,$add_empty=true)
{
	global $CDDataSet,$SiteSections;
    $SiteSections= new SiteSections;
    $SiteSections->init();
    $Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
    if ($Section['id']>0)
    {
	    $Section['id'] = floor($Section['id']);
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
		if ($add_empty) $retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` ORDER BY `name`");
		while ($r = msr($q))
		{
	            $retval[$r['id']] = stripslashes(htmlspecialchars($r['name']));

	    }
    }
    return $retval;

}
function getSprValuesEx($sprnam,$id=0,$add_empty=true,$order='')
{
	global $CDDataSet,$SiteSections;
    $SiteSections= new SiteSections;
    $SiteSections->init();
    $Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
    if ($Section['id']>0)
    {
	    $Section['id'] = floor($Section['id']);
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
		if ($add_empty) $retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` ".(($order=='') ? 'ORDER BY `name`': $order));
		while ($r = msr($q))
		{
	            $retval[$r['id']] = $r;

	    }
    }
    return $retval;

}

function getSprValuesOrder($sprnam,$order,$id=0,$add_empty=true)
{
	global $CDDataSet,$SiteSections;
    $SiteSections= new SiteSections;
    $SiteSections->init();
    $Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
    if ($Section['id']>0)
    {
	    $Section['id'] = floor($Section['id']);
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
		if ($add_empty) $retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` ".$order);
		while ($r = msr($q))
		{
	            $retval[$r['id']] = stripslashes($r['name']);

	    }
    }
    return $retval;

}
function getSprValuesShow($sprnam,$order,$id=0,$add_empty=true)
{
	global $CDDataSet,$SiteSections;
	$SiteSections= new SiteSections;
	$SiteSections->init();
	$Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
	if ($Section['id']>0)
	{
		$Section['id'] = floor($Section['id']);
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
		if ($add_empty) $retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` WHERE `show`=1 ".$order);
		while ($r = msr($q))
		{
			$retval[$r['id']] = stripslashes($r['name']);

		}
	}
	return $retval;

}

function abc_new($var){
	$var = str_replace(" ","",$var);
	$var = str_split($var);
	return array($var);
}
function detectlanguage($sub_text)
{


	$_langs = array(
	'en'=>array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'),
	'ru'=>array('�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�','�')
	);


	$sub_text = trim($sub_text);
	$sub_text = strtolower($sub_text);
	$sub_text = strip_tags($sub_text);

	$text = abc_new($sub_text);

	$rus = array_intersect($_langs[ru],$text[0]);
	$eng = array_intersect($_langs[en],$text[0]);
	if(count($rus) > count($eng)){

	return "ru";
	}else{

	return "end";
	}


	return "none";


}
/*                	$descr_iface=getiface('/sitecontent/about/');
                	$sheet = $descr_iface->get();
					print $sheet['text'].'<br/>';
*/
function getIface($path)
{
	global $CDDataSet,$SiteSections;
    $SiteSections= new SiteSections;
    $SiteSections->init();

	$Section = $SiteSections->get($SiteSections->getIdByPath($path));
	$Section['id'] = floor($Section['id']);

	if ($Section['id']>0)
	{
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
	}

	return $Iface;

}
function get_text($path)
{
    $Iface=getIface($path);
    $sheet = $Iface->get();
	return $sheet['text'];

}
function get_filesize($file)
{
    // ���� ����
    if(!file_exists($file)) return "����  �� ������: ".$file;
   // ������ ���������� ������ ����� � ��������� �����
  $filesize = filesize($file);
   // ���� ������ ������ 1 ��
   if($filesize > 1024)
   {
       $filesize = ($filesize/1024);
       // ���� ������ ����� ������ ���������
       // �� ����� ���������� ��� � ����������. ������������� � ��
       if($filesize > 1024)
       {
            $filesize = ($filesize/1024);
           // � �� ���� ���� ������ 1 ���������, �� ���������
           // �� ������ �� �� 1 ���������
           if($filesize > 1024)
           {
               $filesize = ($filesize/1024);
               $filesize = round($filesize, 1);
               return $filesize." ��";
           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." M�";
           }
       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." ��";
       }
   }
   else
   {
       $filesize = round($filesize, 1);
       return $filesize." ����";
   }
}
function clear_array_empty($array)
{
$ret_arr = array();
foreach($array as $val)
{
    if (!empty($val))
    {
        $ret_arr[] = trim($val);
    }
}
return $ret_arr;
}
function get_update_sql($data, $table, $where)
{
	$sql='';

	foreach ($data as $k=>$v)
	$sql.=(($sql!='') ? ', ':'')."`$k`='$v'";

	$sql="UPDATE `$table` SET $sql $where";
	return $sql;

}
function get_insert_sql($data, $table)
{
	$sql='';

	foreach ($data as $k=>$v)
	{
		$keys.=(($keys!='') ? ',':'')."`$k`";
		$values.=(($values!='') ? ',':'')."'$v'";
	}

	$sql="INSERT INTO `$table` ($keys) VALUES ($values)";
	return $sql;

}
function get_array_sql($q)
{
	$return=array();
	
	while ($r=msr($q))
	$return[]=$r;
	
	return $return;

}

    function ResizeFrame($src=null,$maxWidth=null,$maxHeight=null) {

	    list($w_i, $h_i, $type) = getimagesize($src);


	    $types = array("", "gif", "jpeg", "png");
		$ext = $types[$type]; // ���� "��������" ��� �����������, ����� �������� ����

	    if ($ext) {
	      $func = 'imagecreatefrom'.$ext; // �������� �������� �������, ��������������� ����, ��� �������� �����������
	      $save_func='image'.$ext;
	      $img_i = $func($image); // ������ ���������� ��� ������ � �������� ������������
	    } else {
	      echo '������������ �����������'; // ������� ������, ���� ������ ����������� ������������
	      return false;
	    }


	    $srcimg = $func($src);
	    $srcsize = getimagesize($src);

	    if ($srcsize[0]<=$maxWidth && $srcsize[1]<=$maxHeight) return true;
	    
	    
	    /* ���� ����� �������� ������ ����� �� ������ */
	    if ($maxWidth>0 && $maxHeight>0)
	    {
	    	 
	    	$dest_y = $maxHeight;
	    	$dest_x = $maxWidth;
	    }
	    else 
	    {
	    	/*���� ��������� �� X ������*/
	    	if (($srcsize[0]/$maxWidth) > ($srcsize[1]/$maxHeight))
	    	{
	    		$dest_y = $maxHeight;
	    		$dest_x = ($maxHeight / $srcsize[1]) * $srcsize[0];
	    	}
	    	else
	    	{
	    		$dest_x = $maxWidth;
	    		$dest_y = ($maxWidth / $srcsize[0]) * $srcsize[1];
	    	}
	    }
	    

	    $thumbimg = imagecreatetruecolor($dest_x, $dest_y);
	    imageAlphaBlending($thumbimg, false);
		imageSaveAlpha($thumbimg,true);
	    imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    $resultimg= $save_func($thumbimg,$src);
	    

    }
    function  image_block_position ($path, $res_width, $res_height)
    {
    	$size = getimagesize ($_SERVER['DOCUMENT_ROOT'].$path);
    	
    	$width_dif=$size[0]-$res_width;
    	$height_dif=$size[1]-$res_height;
    	
    	
    	if ($width_dif>$height_dif)
    	{
    		$procent=$height_dif/$size[1]*100;
    			
    		$new_size=$size[0]-($size[0]/100*$procent);
    			
    		$margin=floor(($new_size-$res_width)/2);
    			
    			
    		$style="max-height: ".$res_height."px;".($margin>0 ? "margin-left: -".$margin."px":"");
    	}
    	elseif ($width_dif<$height_dif)
    	{
    		$procent=$width_dif/$size[0]*100;
    		$new_size=$size[1]-($size[1]/100*$procent);
    		$margin=floor(($new_size-$res_height)/2);
    			
    		$style="max-width: ".$res_width."px;".($margin>0 ? "margin-top: -".$margin."px":"");
    	}	
    	
    	return '<img src="'.$path.'" alt="" style="'.$style.'"/>';
    }
    function ResizeFrameMaxSide($src=null,$maxWidth=null,$maxHeight=null) {


	    list($w_i, $h_i, $type) = getimagesize($src);


	    $types = array("", "gif", "jpeg", "png");
		$ext = $types[$type]; // ���� "��������" ��� �����������, ����� �������� ����

	    if ($ext) {
	      $func = 'imagecreatefrom'.$ext; // �������� �������� �������, ��������������� ����, ��� �������� �����������
	      $save_func='image'.$ext;
	      $img_i = $func($image); // ������ ���������� ��� ������ � �������� ������������
	    } else {
	      echo '������������ �����������'; // ������� ������, ���� ������ ����������� ������������
	      return false;
	    }


	    $srcimg = $func($src);
	    $srcsize = getimagesize($src);
	    /*if ($ext=='png') imagefill($srcimg , 0, 0, 0xFFFFFF);*/

	    if ($srcsize[0]<=$maxWidth && $srcsize[1]<=$maxHeight) return true;


	    /*���� ��������� �� X ������*/
	    if (($srcsize[0]/$maxWidth) < ($srcsize[1]/$maxHeight))
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
	    	$white = imagecolorallocate($thumbimg, 255, 255, 255);
	    	imagefill($thumbimg, 0, 0, $white);
	    	imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    	$resultimg = $save_func($thumbimg,$src);
	    }

    }
/*     function crop($src=null,$maxWidth=null,$maxHeight=null) {
    
    
    	list($w_i, $h_i, $type) = getimagesize($src);
    
    
    	$types = array("", "gif", "jpeg", "png");
    	$ext = $types[$type]; // ���� "��������" ��� �����������, ����� �������� ����
    
    	if ($ext) {
    		$func = 'imagecreatefrom'.$ext; // �������� �������� �������, ��������������� ����, ��� �������� �����������
    		$save_func='image'.$ext;
    		$img_i = $func($image); // ������ ���������� ��� ������ � �������� ������������
    	} else {
    		echo '������������ �����������'; // ������� ������, ���� ������ ����������� ������������
    		return false;
    	}
    
    
    	$srcimg = $func($src);
    	$srcsize = getimagesize($src);
    
    	if (($srcsize[0]/$maxWidth) < ($srcsize[1]/$maxHeight))
    	{
    		$y_pos=($srcsize[1]-$maxHeight)/2;
    		$srcsize[1]=($srcsize[1]-$y_pos*2);
    	}
    	else
    	{
    		$x_pos=($srcsize[0]-$maxWidth)/2;
    		$srcsize[0]=($srcsize[0]-$x_pos*2);
    	}
    
    	$thumbimg = imagecreatetruecolor($maxWidth, $maxHeight);
    	imagecopyresampled($thumbimg,$srcimg,0,0,floor($x_pos),floor($y_pos),$maxWidth,$maxHeight, $srcsize[0], $srcsize[1]);
    	$thumbimg = $save_func($thumbimg,$src);
    
    } */
    function crop($src=null,$maxWidth=null,$maxHeight=null) {


	    list($w_i, $h_i, $type) = getimagesize($src);


	    $types = array("", "gif", "jpeg", "png");
		$ext = $types[$type]; // ���� "��������" ��� �����������, ����� �������� ����

	    if ($ext) {
	      $func = 'imagecreatefrom'.$ext; // �������� �������� �������, ��������������� ����, ��� �������� �����������
	      $save_func='image'.$ext;
	      $img_i = $func($image); // ������ ���������� ��� ������ � �������� ������������
	    } else {
	      echo '������������ �����������'; // ������� ������, ���� ������ ����������� ������������
	      return false;
	    }


	    $srcimg = $func($src);
	    $srcsize = getimagesize($src);
	    
	    $width_dif=$srcsize[0]-$maxWidth;
	    $height_dif=$srcsize[1]-$maxHeight;
	    
	     
	    $change_width=$srcsize[0]/$maxWidth;
	    $change_height=$srcsize[1]/$maxHeight;
	    
	    
	    /*�������� ����� ����������� �����������*/
	    $thumbimg = imagecreatetruecolor($maxWidth, $maxHeight);
	    

        /*���� ������� �� ������*/
        if ($change_width<$change_height)
        {
        	$y_pos=($srcsize[1]-($maxHeight*$change_width)) / 2;
        	imagecopyresampled($thumbimg,$srcimg,0,0,floor($x_pos),floor($y_pos),$maxWidth,$maxHeight, $srcsize[0], $srcsize[1]-$y_pos*2);
        }
        else 
        {
        	$x_pos=($srcsize[0]-($maxWidth*$change_height)) / 2;
        	imagecopyresampled($thumbimg,$srcimg,0,0,floor($x_pos),floor($y_pos),$maxWidth,$maxHeight, $srcsize[0]-$x_pos*2, $srcsize[1]);
        }

	    $thumbimg = $save_func($thumbimg,$src);

    }
    function crop_editor($image, $x_o, $y_o, $w_o, $h_o, $settings) {
    
    
    	if (($x_o < 0) || ($y_o < 0) || ($w_o < 0) || ($h_o < 0)) {
    		echo "������������ ������� ���������";
    		return false;
    	}
    	list($w_i, $h_i, $type) = getimagesize($image); // �������� ������� � ��� ����������� (�����)
    	$types = array("", "gif", "jpeg", "png"); // ������ � ������ �����������
    	$ext = $types[$type]; // ���� "��������" ��� �����������, ����� �������� ����
    	if ($ext) {
    		$func = 'imagecreatefrom'.$ext; // �������� �������� �������, ��������������� ����, ��� �������� �����������
    		$img_i = $func($image); // ������ ���������� ��� ������ � �������� ������������
    	} else {
    		echo '������������ �����������'; // ������� ������, ���� ������ ����������� ������������
    		return false;
    	}
    	if ($x_o + $w_o > $w_i) $w_o = $w_i - $x_o; // ���� ������ ��������� ����������� ������ ��������� (� ������ x_o), �� ��������� �
    	if ($y_o + $h_o > $h_i) $h_o = $h_i - $y_o; // ���� ������ ��������� ����������� ������ ��������� (� ������ y_o), �� ��������� �
    	$img_o = imagecreatetruecolor($w_o, $h_o); // ������ ���������� ��� ��������� �����������
    	imageAlphaBlending($img_o, false);
    	imageSaveAlpha($img_o,true);
    	imagecopy($img_o, $img_i, 0, 0, $x_o, $y_o, $w_o, $h_o); // ��������� ����� ����������� �� ��������� � ��������
    	$func = 'image'.$ext; // �������� ������� ��� ���������� ����������
    
    	
    	if ($settings['editor_as_min'])
    	$image=str_replace('.','_mini.',$image);
    	
    	$new_i=$func($img_o, $image);  // ��������� �����������
    	
    	
    	/* ���� ���� ��������� �� �������� */
    	if ($settings['editor_minw']>0 && $settings['editor_minh']>0)
    	{
    		if (!$settings['editor_min_more'] || ($settings['editor_minw']<$x_o + $w_o || $settings['editor_minh']<$y_o + $h_o))
    		
    		ResizeFrame($image, $settings['editor_minw'], $settings['editor_minh']);  
    	}
    	
    	return basename($image);
    
    
    }
    function setting($name)
	{
		global $SiteSettings;
		$set=$SiteSettings->getOne($SiteSettings->getIdByName($name));
		return html_entity_decode(stripslashes($set['value']));
	}
	function alert_mysql($comment='')
	{
		if (mysql_error()!='')
		$_SESSION['global_alert'].=(($_SESSION['global_alert']!='') ? '<br/>':'').'<i><span style="color: #CC0000">������ SQL:'.$comment.'</span></i> '.mysql_error();
	}
	function WriteLog($item_id, $descr, $comment, $user_id='', $changes='', $section_id=0)
	{
 		global $user;
        if (setting('log_enable'))
   		{
 			if ($user_id>0) $user['id']=$user_id;
 			$user_name=$user['login'];


 			msq("INSERT INTO `".ConfigGet('pr_name')."_log` (`date`,`item_id`,`descr`,`comment`, `user_id`, `changes`, `user_name`, `ip`, `section_id`) VALUES ( NOW(), $item_id, '".$descr."', '".$comment."',  '".$user['id']."', '".$changes."', '".$user_name."', '".$_SERVER['REMOTE_ADDR']."', '".$section_id."')");
 			
	    }
	}
	function deleteTempFiles($path=''){
		
		
		$d = dir($_SERVER['DOCUMENT_ROOT'].$path);
		while (($e = $d->read()) !== false)
		if (!is_dir("$dirname/$e") && stripos($e, 'temp_')!==false)
		{
			@unlink($_SERVER['DOCUMENT_ROOT'].$path.$e);
		}
		
	}
	function print_tag ($ids, $tag_array, $url)
	{
		if ($ids=='') return false;
		
		$return='';
		
		$ids=explode(',', $ids);
		
		$ids=clear_array_empty($ids);
		
		foreach ($ids as $id)
		{
			if (isset($tag_array[$id]))
			$return.=($return=='' ? '':'<div>,&nbsp;</div>').'<a href="'.$url.'?tag='.$id.'">'.$tag_array[$id]['name'].'</a>';
		}
		
		return '<div class="tags"><div class="tag_header">����:&nbsp;&nbsp;&nbsp;</div>'.$return.'</div>';
	}
	function getTableById($section_id)
	{
		global $CDDataSet,$SiteSections;
		$SiteSections= new SiteSections;
		$SiteSections->init();
	
		$Section = $SiteSections->get($section_id);
		$Section['id'] = floor($Section['id']);
	
		if ($Section['id']>0)
		{
			$Pattern = new $Section['pattern'];
			$Iface = $Pattern->init(array('section'=>$Section['id']));
		}
	
		return $Iface->getSetting('table');
	
	}
?>
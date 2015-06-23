<?
function upper($str){ // Просто strtoupper не на всех серверах корректно работает :)
	$lower = "abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя";
	$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
	$lowerlen = strlen($lower);
	for ($i=0; $i<$lowerlen; $i++) $str = str_replace(substr($lower,$i,1),substr($upper,$i,1),$str);
	return $str;
}
function lower($str){ // Просто strtolower не на всех серверах корректно работает :)
	$lower = "abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя";
	$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
	$upperlen = strlen($upper);
	for ($i=0; $i<$upperlen; $i++) $str = str_replace(substr($upper,$i,1),substr($lower,$i,1),$str);
	return $str;
}

/* Функции получения названия месяца */
function getMonthRusNameUpper($num){ $months = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"); return $months[floor($num)]; }
function getMonthRusNameLower($num){ $months = array("","январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь"); return $months[floor($num)]; }
function getMonthRusNameLowerRP($num){ $months = array("","января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"); return $months[floor($num)]; }
function getMonthRusNameUpperRP($num){ $months = array("","Января","Февраля","Марта","Апреля","Мая","Июня","Июля","Августа","Сентября","Октября","Ноября","Декабря"); return $months[floor($num)]; }

function pregtrim($str){ return preg_replace("/[^\x20-\xFF]/","",@strval($str)); } // Функция удаления опасных сиволов
function checkUrl($url){ // Проверка корректности url, возвращает корректный URL или FALSE
	$url = trim(pregtrim($url));
	if (strlen($url)==0) return false; // если пустой url
/*	if (!preg_match("~^(?:(?:https?://|ftp://|telnet://|mailto:)(?:[a-z0-9_-]{1,32}".
					"(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
					"org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
					"!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&".
					"?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i",$url,$ok))
	return false; // если url некорректен*/
	if (!strstr($url,"://") && !strstr($url,"mailto:")) $url = "http://".$url; // если не указан протокол — добавляем http://
	$url = preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url); // заменяем протокол на нижний регистр: hTtP -> http
	return $url;
}
function checkEmail($mail){ // Проверка корректности email, возвращает корректный E-mail или FALSE
   $mail = trim(pregtrim($mail));
   if (strlen($mail)==0) return false; // если пустой email
   if (!preg_match("/^[a-z0-9\._-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|".
   "edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-".
   "9]{1,3}\.[0-9]{1,3})$/is",$mail))
   return false; // если email некорректен
   return $mail;
}
function checkValidDate($date){ // Проверка корректности даты, возвращает true или false
	$date = explode('.',$date);
	$d = floor($date[0]);
	$m = floor($date[1]);
	$y = floor($date[2]);
	if ($y<1) return false;
	if (($m<1) || ($m>12)) return false;
	if (($d<1) || ($d>getMonthDaysCount($m,$y))) return false;
	return true;
}
function getMonthDaysCount($m,$y){ // Возвращает кол-во дней в месяце
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
function getUniqueStr($length = 0){ // Возвращает случайный набор символов заданной длины
	$retval = '';
	$length = floor($length);
	if ($length<1) return '';
	$symbols = "abcdefghijklmnopqrstuvwxyz1234567890";
	for ($i=0; $i<$length; $i++) $retval.= substr($symbols,rand(1,strlen($symbols)),1);
	return $retval;
}
function defaultEmail($email,$text,$theme,$fromemail = ''){ // Отправление электронного письма
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
    	    $address = 'smtp.beget.ru'; // адрес smtp-сервера
    		$port    = 2525;          // порт (стандартный smtp - 25)

    		$login   = 'sender@nadosushi.ru';    // логин к ящику
    		$pwd     = '1qazxsw';    // пароль к ящику

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

	        // Создаем сокет
	        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	        if ($socket < 0) {
	            throw new Exception('socket_create() failed: '.socket_strerror(socket_last_error())."\n");
	        }

	        // Соединяем сокет к серверу
	        /*echo 'Connect to \''.$address.':'.$port.'\' ... ';*/
	        $result = socket_connect($socket, $address, $port);
	        if ($result === false) {
	            throw new Exception('socket_connect() failed: '.socket_strerror(socket_last_error())."\n");
	        } else {
	            /*echo "OK\n";*/
	        }

	        // Читаем информацию о сервере
	        read_smtp_answer($socket);

	        // Приветствуем сервер
	        write_smtp_response($socket, 'EHLO '.$login);
	        read_smtp_answer($socket); // ответ сервера

	        /*echo 'Authentication ... ';*/

	        // Делаем запрос авторизации
	        write_smtp_response($socket, 'AUTH LOGIN');
	        read_smtp_answer($socket); // ответ сервера

	        // Отравляем логин
	        write_smtp_response($socket, base64_encode($login));
	        read_smtp_answer($socket); // ответ сервера

	        // Отравляем пароль
	        write_smtp_response($socket, base64_encode($pwd));
	        read_smtp_answer($socket); // ответ сервера

	        /*echo "OK\n";
	        echo "Check sender address ... ";*/

	        // Задаем адрес отправителя
	        write_smtp_response($socket, 'MAIL FROM:<'.$from.'>');
	        read_smtp_answer($socket); // ответ сервера

	        /*echo "OK\n";
	        echo "Check recipient address ... ";*/

	        // Задаем адрес получателя
	        write_smtp_response($socket, 'RCPT TO:<'.$to.'>');
	        read_smtp_answer($socket); // ответ сервера

	        /*echo "OK\n";
	        echo "Send message text ... ";*/

	        // Готовим сервер к приему данных
	        write_smtp_response($socket, 'DATA');
	        read_smtp_answer($socket); // ответ сервера

	        // Отправляем данные
            $headers  = "Content-Type: text/html; charset=windows-1251\r\n";
	        $headers .= "To: $to\r\n"; // добавляем заголовок сообщения "адрес получателя"
	        $headers .= "Subject: $subject\r\n"; // заголовок "тема сообщения"
	        write_smtp_response($socket, $headers.$message."\r\n.");
	        read_smtp_answer($socket); // ответ сервера

	       /* echo "OK\n";
	        echo 'Close connection ... ';*/

	        // Отсоединяемся от сервера
	        write_smtp_response($socket, 'QUIT');
	        read_smtp_answer($socket); // ответ сервера

	        /*echo "OK\n";*/

	    }
	    catch (Exception $e) {
	        /*echo "\nError: ".$e->getMessage();*/
	    }

	    if (isset($socket)) {
	        socket_close($socket);
	    }


    }
    // Функция для чтения ответа сервера. Выбрасывает исключение в случае ошибки
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

    // Функция для отправки запроса серверу
    function write_smtp_response($socket, $msg) {
        $msg = $msg."\r\n";
        socket_write($socket, $msg, strlen($msg));
    }
function getFileSizeString($size){ // Возвращает тектовое описание размера (size - в байтах)
	$size = floor($size);
	$names = array('б','Кб','Мб','Гб');
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
	$refers = @eregi_replace("UNION|OUTFILE|FROM|SELECT|WHERE|SHUTDOWN|UPDATE|DELETE|CHANGE|MODIFY|RENAME|RELOAD|ALTER|GRANT|DROP|INSERT|CONCAT",'',$refers);
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

function getSprValues($sprnam,$id=0)
{	global $CDDataSet,$SiteSections;
    $SiteSections= new SiteSections;
    $SiteSections->init();
    $Section = $SiteSections->get($SiteSections->getIdByPath($sprnam));
    if ($Section['id']>0)
    {
	    $Section['id'] = floor($Section['id']);
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
		$retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` ORDER BY `name`");
		while ($r = msr($q))
		{
	            $retval[$r['id']] = $r['name'];

	    }
    }
    return $retval;

}
function getSprValuesEx($sprnam,$id=0)
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
		$retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."` ORDER BY `name`");
		while ($r = msr($q))
		{
	            $retval[$r['id']] = $r;

	    }
    }
    return $retval;

}

function getSprValuesOrder($sprnam,$order,$id=0)
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
		$retval[-1]='&nbsp';
		$q = msq("SELECT * FROM `".$Iface ->getSetting('table')."`".$order);
		while ($r = msr($q))
		{
	            $retval[$r['id']] = $r['name'];

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
	'ru'=>array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я')
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
function get_filesize($file)
{
    // идем файл
    if(!file_exists($file)) return "Файл  не найден: ".$file;
   // теперь определяем размер файла в несколько шагов
  $filesize = filesize($file);
   // Если размер больше 1 Кб
   if($filesize > 1024)
   {
       $filesize = ($filesize/1024);
       // Если размер файла больше Килобайта
       // то лучше отобразить его в Мегабайтах. Пересчитываем в Мб
       if($filesize > 1024)
       {
            $filesize = ($filesize/1024);
           // А уж если файл больше 1 Мегабайта, то проверяем
           // Не больше ли он 1 Гигабайта
           if($filesize > 1024)
           {
               $filesize = ($filesize/1024);
               $filesize = round($filesize, 1);
               return $filesize." ГБ";
           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." MБ";
           }
       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." Кб";
       }
   }
   else
   {
       $filesize = round($filesize, 1);
       return $filesize." байт";
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
	    	imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    	$resultimg= $save_func($thumbimg,$src);
	    }
	    else
	    {
	    	$dest_x = $maxWidth;
	    	$dest_y = ($maxWidth / $srcsize[0]) * $srcsize[1];
	    	$thumbimg = imagecreatetruecolor($dest_x, $dest_y);
	    	imagecopyresampled($thumbimg,$srcimg,0,0,0,0,$dest_x,$dest_y, $srcsize[0], $srcsize[1]);
	    	$resultimg = $save_func($thumbimg,$src);
	    }

    }
    function ResizeFrameMaxSide($src=null,$maxWidth=null,$maxHeight=null) {


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
	    /*if ($ext=='png') imagefill($srcimg , 0, 0, 0xFFFFFF);*/

	    if ($srcsize[0]<=$maxWidth && $srcsize[1]<=$maxHeight) return true;


	    /*Если пропорция по X больше*/
	    if (($srcsize[0]/$maxWidth) < ($srcsize[1]/$maxHeight))
	    {
	    	$dest_y = $maxHeight;
	    	$dest_x = ($maxHeight / $srcsize[1]) * $srcsize[0];
	    	$thumbimg = imagecreatetruecolor($dest_x, $dest_y);
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
        {
        	$x_pos=($srcsize[0]-$maxWidth)/2;
        	$srcsize[0]=($srcsize[0]-$x_pos*2);
        }

	    /*Копируем центр обрезанного изображения*/
	    $thumbimg = imagecreatetruecolor($maxWidth, $maxHeight);
	    imagecopyresampled($thumbimg,$srcimg,0,0,floor($x_pos),floor($y_pos),$maxWidth,$maxHeight, $srcsize[0], $srcsize[1]);
	    $thumbimg = $save_func($thumbimg,$src);

    }
?>
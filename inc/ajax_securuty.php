<?
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']!="-"){
	define('USER_AGENT',$_SERVER['HTTP_USER_AGENT']);
}else{ die(); }

if($_SERVER['REQUEST_METHOD']=='TRACE'){ die(); }
if(isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])){ die(); }
if(!is_array($GLOBALS)){ die(); }

$badcount = 0;
$baddata = array("UNION",
		"OUTFILE",
		"FROM",
		"SELECT",
		"WHERE",
		"SHUTDOWN",
		"UPDATE",
		"DELETE",
		"CHANGE",
		"MODIFY",
		"RENAME",
		"RELOAD",
		"ALTER",
		"GRANT",
		"DROP",
		"INSERT",
		"CONCAT",
		"cmd",
		"exec",
		"--",
		// HTML LINE
		"\([^>]*\"?[^)]*\)",
		"<[^>]*body*\"?[^>]*>",
		"<[^>]*script*\"?[^>]*>",
		"<[^>]*object*\"?[^>]*>",
		"<[^>]*iframe*\"?[^>]*>",
		"<[^>]*img*\"?[^>]*>",
		"<[^>]*frame*\"?[^>]*>",
		"<[^>]*applet*\"?[^>]*>",
		"<[^>]*meta*\"?[^>]*>",
		"<[^>]*style*\"?[^>]*>",
		"<[^>]*form*\"?[^>]*>",
		"<[^>]*div*\"?[^>]*>");
if(!isset($_REQUEST)) return;
foreach($_REQUEST as $params => $inputdata){
foreach($baddata as $badkey => $badvalue){
if(is_string($inputdata) && eregi($badvalue,$inputdata)){ $badcount=1; }
}
}

if($badcount==1){
exit();
}

$array = array( "\x27", "\x22", "\x60", "\t",'\n','\r', '\\', "'",
"¬","#",";","~","[","]","{","}","=","+",")","(",
"*","&","^","%","$","<",">","?","!",".pl", ".php",'"' );

$_GET = str_replace($array, '', $_GET);
$_POST = str_replace($array, '', $_POST);
$_SESSION = str_replace($array, '', $_SESSION);
$_COOKIE = str_replace($array, '', $_COOKIE);
$_ENV = str_replace($array, '', $_ENV);
$_FILES = str_replace($array, '', $_FILES);
$_REQUEST = str_replace($array, '', $_REQUEST);
$_SERVER = str_replace($array, '', $_SERVER);
?>
<?
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']!="-"){
	define('USER_AGENT',$_SERVER['HTTP_USER_AGENT']);
}else{ die(); }

if($_SERVER['REQUEST_METHOD']=='TRACE'){ die(); }
if(isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])){ die(); }
if(!is_array($GLOBALS)){ die(); }

$badcount = 0;
$baddata = array("/UNION/i",
		"/OUTFILE/i",
		"/FROM/i",
		"/SELECT/i",
		"/WHERE/i",
		"/SHUTDOWN/i",
		"/UPDATE/i",
		"/DELETE/i",
		"/CHANGE/i",
		"/MODIFY/i",
		"/RENAME/i",
		"/RELOAD/i",
		"/ALTER/i",
		"/GRANT/i",
		"/DROP/i",
		"/INSERT/i",
		"/CONCAT/i",
		"/OR/i",
		"/TRUNCATE/i",
		"/ALTER/i",
		"/cmd/i",
		"/exec/i",
		// HTML LINE
		"/<(.*?)script(.*?)>/si",
		"/<(.*?)object(.*?)>/si",
		"/<(.*?)frame(.*?)>/si",
		"/<(.*?)iframe(.*?)>/si",
		"/<(.*?)form(.*?)>/si",
		"/<(.*?)meta(.*?)>/si",
		"/<(.*?)applet(.*?)>/si",
		"/<(.*?)body(.*?)>/si"
		
		
);
if(!isset($_REQUEST)) return;
foreach($_REQUEST as $params => $inputdata){
	foreach($baddata as $badkey => $badvalue){
		if(is_string($inputdata) && preg_match($badvalue, $inputdata))
		{ 
			$badcount=1; 
			/* exit('ERROR: '.$params.'='.$inputdata."|".$badvalue); */
			exit('ERROR: BADDATA'); 
		}
	}
}



$array = array( "\x27", "\x22", "\x60", "\t",'\n','\r', '\\', "'",
"¬","#",";","~","[","]","{","}","=","+",")","(",
"*","&","^","%","$","<",">","?","!",".pl", '"' );

$_GET = str_replace($array, '', $_GET);
$_POST = str_replace($array, '', $_POST);
$_SESSION = str_replace($array, '', $_SESSION);
$_COOKIE = str_replace($array, '', $_COOKIE);
$_ENV = str_replace($array, '', $_ENV);
$_FILES = str_replace($array, '', $_FILES);
$_REQUEST = str_replace($array, '', $_REQUEST);
$_SERVER = str_replace($array, '', $_SERVER);
?>
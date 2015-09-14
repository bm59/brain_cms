<?
/*Глобальные настройки сайта*/
$GlobalConfiguration = array();
function configSet($param,$value){ global $GlobalConfiguration; $GlobalConfiguration[$param] = $value; }
function configGet($param){ global $GlobalConfiguration; return $GlobalConfiguration[$param]; }

/*Переменные сессии*/
session_start();


/*Cookies*/
function cookieSet($name,$value,$days = 0){
	$days = floor($days);
	$time = ($days>0)?time()+$days*24*60*60:0;
	setcookie($name,$value,$time,"/");
}
function cookieGet($name){ return $_COOKIE[$name]; }
?>
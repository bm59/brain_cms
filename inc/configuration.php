<?
/*Глобальные настройки сайта*/
$GlobalConfiguration = array();
function configSet($param,$value){ global $GlobalConfiguration; $GlobalConfiguration[$param] = $value; }
function configGet($param){ global $GlobalConfiguration; return $GlobalConfiguration[$param]; }

/*Переменные сессии*/
$SessionVariables = array();
session_start();
session_register("SessionVariables");
$SessionVariables = $_SESSION["SessionVariables"];
/*function sessionSet($name,$value){ global $SessionVariables; $SessionVariables[$name] = $value; }
function sessionGet($name){ global $SessionVariables; return $SessionVariables[$name]; }*/

/*Cookies*/
function cookieSet($name,$value,$days = 0){
	$days = floor($days);
	$time = ($days>0)?time()+$days*24*60*60:0;
	setcookie($name,$value,$time,"/");
}
function cookieGet($name){ return $_COOKIE[$name]; }
?>
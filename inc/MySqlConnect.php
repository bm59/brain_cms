<?
/*
Класс работы с БД MySQL
*/
class MySqlConnect
{
	var $Settings = array(); /* Настройки класса */
	var $Errors = array( /* Описание ошибок подключения */
		'ServerConnect'=>'Сервер базы данных недоступен в данный момент. Попробуйте обратиться позднее.',
		'BaseConnect'=>'Ошибка подключения к базе данных. Попробуйте обратиться позднее.'
	);

	function error($code){ if ($this->Errors[$code]) return $this->Errors[$code]; else return 'Ошибка БД'; }
	function connect(){ /* Подключение к БД */
		$this->Settings['QuerriesNumber'] = 0;
		$this->Settings['Server'] = @mysql_connect(configGet("DBHost"),configGet("DBUser"),configGet("DBPassword")) or die($this->error("ServerConnect"));
		$this->Settings['Connected'] = @mysql_select_db(configGet("DBBase"),$this->Settings['Server']) or die($this->error("BaseConnect"));
		@mysql_query("SET NAMES utf8",$this->Settings['Server']);
	}
	function querriesNumberPlus() { $this->Settings['QuerriesNumber']++; }
	function getQuerriesNumber() { return $this->Settings['QuerriesNumber']; }
	function query($query){ $this->querriesNumberPlus(); return mysql_query($query,$this->Settings['Server']);} /* Запрос к БД */
	function row($result) { return @mysql_fetch_assoc($result); }
	function rowsNum() { return	@mysql_affected_rows($this->Settings['Server']); }
	function lastInsertId() { $res = $this->query("SELECT LAST_INSERT_ID() as idd"); return @mysql_result($res,0); } /* Получение последнего значения auto_increment */
	function dateToDB($date) { if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$|",$date)) return ''; return substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2); } /* Преобразование даты из формата ДД.ММ.ГГГГ в формат ГГГГ-ММ-ДД */
	function dateFromDBDot($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,0,4); } /* Преобразование даты из формата ГГГГ-ММ-ДД в формат ДД.ММ.ГГГГ */
	function dateTimeFromDB($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,0,4)." ".substr($date,11,5); }
	function TimeFromDB($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,11,5); }
	function dateTimeFromDBShort($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,2,2)." ".substr($date,11,5); }
	function dateTimeToDB($date) { if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}(?:\:[0-9]{2})$|",$date)) return ''; if (strlen($date)==16) $date.= ':00'; return substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2)." ".substr($date,11,8); }
	function GetWeekDay($date)
	{
		$retval='';

		if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}|",$date)) return '';

		$date = mktime(0, 0, 0, substr($date,5,2), substr($date,8,2), substr($date,0,4));

		$now_weekday=date('w', $date);
		switch ($now_weekday)
		{
        	case '0': $retval='Вс'; break;
        	case '1': $retval='Пн'; break;
        	case '2': $retval='Вт'; break;
        	case '3': $retval='Ср'; break;
        	case '4': $retval='Чт'; break;
        	case '5': $retval='Пт'; break;
        	case '6': $retval='Сб'; break;
       	}

        return $retval;
	}
}

/* Чтобы не писать каждый раз обращение к классу — глобальные сокращенные функции: */
function msq($query){ global $MySqlObject; return $MySqlObject->query($query); }
function msr($result){ global $MySqlObject; return $MySqlObject->row($result); }
function mstable($module,$theme,$block,$fields){ // Создает таблицу в БД, или возвращает название уже созданной таблицы
	$module = trim($module);
	$theme = trim($theme);
	$block = trim($block);
	$tabname = $module.(($theme!='')?'_'.$theme.(($block!='')?'_'.$block:''):'');
	if (!msq("DESCRIBE `".$tabname."`")){
		$query = "";
		foreach($fields as $k=>$v) $query.= ", `$k` $v ";
		msq("CREATE TABLE `".$tabname."` (id BIGINT auto_increment PRIMARY KEY ".$query.")");
	}
	return $tabname;
}
function mslastid() { global $MySqlObject; return $MySqlObject->lastInsertId(); }
function msdtodb($date) { global $MySqlObject; return $MySqlObject->dateToDB($date); }
function msdfromdb($date) { global $MySqlObject; return $MySqlObject->dateFromDBDot($date); }
function msdttodb($date) { global $MySqlObject; return $MySqlObject->dateTimeToDB($date); }
function msdtfromdb($date) { global $MySqlObject; return $MySqlObject->dateTimeFromDB($date); }

$MySqlObject = new MySqlConnect; /* Создание и инициализация объекта */
$MySqlObject->connect();
?>
<?
/*
����� ������ � �� MySQL
*/
class MySqlConnect
{
	var $Settings = array(); /* ��������� ������ */
	var $Errors = array( /* �������� ������ ����������� */
		'ServerConnect'=>'������ ���� ������ ���������� � ������ ������. ���������� ���������� �������.',
		'BaseConnect'=>'������ ����������� � ���� ������. ���������� ���������� �������.'
	);

	function error($code){ if ($this->Errors[$code]) return $this->Errors[$code]; else return '������ ��'; }
	function connect(){ /* ����������� � �� */
		$this->Settings['QuerriesNumber'] = 0;
		$this->Settings['Server'] = @mysql_connect(configGet("DBHost"),configGet("DBUser"),configGet("DBPassword")) or die($this->error("ServerConnect"));
		$this->Settings['Connected'] = @mysql_select_db(configGet("DBBase"),$this->Settings['Server']) or die($this->error("BaseConnect"));
		@mysql_query("SET NAMES cp1251",$this->Settings['Server']);
	}
	function querriesNumberPlus() { $this->Settings['QuerriesNumber']++; }
	function getQuerriesNumber() { return $this->Settings['QuerriesNumber']; }
	function query($query){ $this->querriesNumberPlus(); return mysql_query($query,$this->Settings['Server']);} /* ������ � �� */
	function row($result) { return @mysql_fetch_assoc($result); }
	function rowsNum() { return	@mysql_affected_rows($this->Settings['Server']); }
	function lastInsertId() { $res = $this->query("SELECT LAST_INSERT_ID() as idd"); return @mysql_result($res,0); } /* ��������� ���������� �������� auto_increment */
	function dateToDB($date) { if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$|",$date)) return ''; return substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2); } /* �������������� ���� �� ������� ��.��.���� � ������ ����-��-�� */
	function dateFromDBDot($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,0,4); } /* �������������� ���� �� ������� ����-��-�� � ������ ��.��.���� */
	function dateTimeFromDB($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,0,4)." ".substr($date,11,5); }
	function TimeFromDB($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,11,5); }
	function dateTimeFromDBShort($date) { if (!preg_match("|^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$|",$date)) return ''; return substr($date,8,2).".".substr($date,5,2).".".substr($date,2,2)." ".substr($date,11,5); }
	function dateTimeToDB($date) { if (!preg_match("|^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}(?:\:[0-9]{2})$|",$date)) return ''; if (strlen($date)==16) $date.= ':00'; return substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2)." ".substr($date,11,8); }
}

/* ����� �� ������ ������ ��� ��������� � ������ � ���������� ����������� �������: */
function msq($query){ global $MySqlObject; return $MySqlObject->query($query); }
function msr($result){ global $MySqlObject; return $MySqlObject->row($result); }
function mstable($module,$theme,$block,$fields){ // ������� ������� � ��, ��� ���������� �������� ��� ��������� �������
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

$MySqlObject = new MySqlConnect; /* �������� � ������������� ������� */
$MySqlObject->connect();
?>
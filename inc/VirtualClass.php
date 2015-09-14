<?
/*
������� �����
*/
class VirtualClass
{
	var $Settings = array(); /* ��������� ������ */
	var $Cache = array(); /* ��� ������, ����� ���������� �� ������ �������� � ���� */
	
	function getCacheValue($name){ if (isset($this->Cache[$name])) return $this->Cache[$name]; return false; } /* ��������� �������� �� ���� */
	function setCacheValue($name,$value){ $this->Cache[$name] = $value; } /* ��������� �������� � ��� */
	function getSetting($name){ return $this->Settings[$name]; } // ��������� ��������, ����������� � $Settings
	function setSetting($name,$value){ $this->Settings[$name] = $value; }
	function implode($settings = array()){ // ��������� ������ ���� |name|name=value|name|name|...
		if (!is_array($settings)) $settings = array();
		$retval = '|';
		$doubles = array();
		foreach ($settings as $k=>$v){
			$k = lower(trim($k));
			$v = trim($v);
			if (($k!='') && (!in_array($k,$doubles))){
				$doubles[] = $k;
				if ($v!='') $retval.= $k.'='.$v.'|';
				else $retval.= $k.'|';
			}
		}
		if ($retval=='|') $retval = '';
		return $retval;
	}
	function explode($settings = ''){ // ��������� ������ �� ������ ���� |name|name=value|name|name|...
		$settings = explode('|',trim($settings));
		$retval = array();

		foreach ($settings as $v){
			$v = trim($v);
			if ($v!=''){
				$values = explode('=',$v);
				/* $retval[lower(trim($values[0]))] = trim($values[1]); */
				if (isset($values[1]) && $values[1]!='')
				$retval[lower(trim($values[0]))] = str_replace($values[0].'=', '', $v);
				else $retval[lower(trim($values[0]))] = '';
			}
		}
		return $retval;
	}
}
?>
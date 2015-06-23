<?
/*
Шаблон закладки
*/
class PFolder extends VirtualPattern
{
	function init($settings){
		$settings['name'] = 'PFolder';
		$settings['noedit'] = 1;
		VirtualPattern::init($settings);
		return false;
	}

	function start(){
		return false;
	}
	function delete(){
		return true;
	}
}

$registeredPatterns = configGet('registeredPatterns');
if (!is_array($registeredPatterns)) $registeredPatterns = array();
$registeredPatterns[] = array('name'=>'PFolder','description'=>'Закладка','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
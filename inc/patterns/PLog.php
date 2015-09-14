<?
$source_descr='Логи действий';
$source_name='PLog';
/* Имя класса */
class PLog extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		
		/* Описание класса */
		$source_descr='Логи действий';
		
		$source_name=substr(get_class(), 1, strlen(get_class()));
		$class_name='CC'.$source_name;
		$settings['name'] = get_class();
		$settings['dataset'] = $CDDataSet->checkPresence(0, mb_strtolower($source_name));
		
		
		
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)')));
		$iface = new $class_name;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>&$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);




		return $iface;
	}

	function start(){
		$iface = $this->getSetting('cclass');
		$iface->start();
	}
	function delete(){
		$iface = $this->getSetting('cclass');
		if ($iface->delete()) return true;
		return false;
	}
}

$registeredPatterns = configGet('registeredPatterns');
if (!is_array($registeredPatterns)) $registeredPatterns = array();
$registeredPatterns[] = array('name'=>$source_name,'description'=>$source_descr,'useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
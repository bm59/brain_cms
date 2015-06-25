<?
class PLog extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PLog';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'log');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)')));
		$iface = new CCLog;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>&$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);

/*		$CDDataSet->add
		(
			array
			(
					'name'=>'ticket',
					'description'=>'Тикеты',
					'types'=>array
					(
						array('name'=>'hover', 'type'=>'CDTextArea', 'description'=>'Заголовок', 'settings'=>array('important'=>1)),
						array('name'=>'text', 'type'=>'CDTextEditor', 'description'=>'Содержание', 'settings'=>array('texttype'=>'full')),
						array('name'=>'file', 'type'=>'CDImage', 'description'=>'Картинка','settings'=>array())

					)

			)
		);*/


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
$registeredPatterns[] = array('name'=>'PLog','description'=>'Логи действий','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
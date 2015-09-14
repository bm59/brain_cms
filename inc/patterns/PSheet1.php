<?
/*
Шаблон обычной страницы (Лист)
*/
class PSheet1 extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$settings['name'] = 'PSheet1';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'sheet1');
		$CDDataSet->add
		(
				array
				(
						'name'=>'sheet1',
						'description'=>'Лист (1 колонка)',
						'types'=>array
						(
								array('name'=>'text', 'description'=>'Текст', 'type'=>'CDTextEditor',  'settings'=>array('texttype'=>'full')),
						)
		
				),
				$settings['section']
		);
		
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),0,array('section_id'=>'BIGINT(20)')));
		$iface = new CCSheet;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
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
$registeredPatterns[] = array('name'=>'PSheet1','description'=>'Страница с 1 колонкой','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
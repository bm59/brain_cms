<?
class PVideo extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PVideo';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'video');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/banners/200_290/','name'=>'Баннеры','exts'=>array('jpg','gif','jpeg', 'png', 'swf'),'imgw'=>300,'imgwtype'=>1,'imgh'=>200,'imghtype'=>1,'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'show_main'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCVideo;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'video',
					'description'=>'Видео',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'Наименование', 'settings'=>array()),
                        array('name'=>'image', 'type'=>'CDImage', 'description'=>'Изображение', 'settings'=>array('important'=>1)),
                        array('name'=>'code', 'type'=>'CDTextEditor', 'description'=>'Код видеоролика', 'settings'=>array('important'=>1)),
                        array('name'=>'descr', 'type'=>'CDTextEditor', 'description'=>'Описание видеоролика', 'settings'=>array('texttype'=>'full')),
                        array('name'=>'url', 'type'=>'CDText', 'description'=>'Ссылка', 'settings'=>array()),
                        array('name'=>'dopinfo', 'type'=>'CDText', 'description'=>'Дополнительная информация', 'settings'=>array())

					)

			)
		);

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
$registeredPatterns[] = array('name'=>'PVideo','description'=>'Видео','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
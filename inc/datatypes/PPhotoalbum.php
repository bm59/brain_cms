<?

class PPhotoalbum extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('photoalbum_page_count','Количество элементов на странице типа «Фото»',18,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PPhotoalbum';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'photoalbum');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));
		$settings['iconstorage'] = $Storage->getStorage(0,array('path'=>'/site/images/smallicons/','name'=>'Иконки проектов','exts'=>array('jpg','gif','jpeg'),'imgwtype'=>1,'imghtype'=>1,'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('album'=>'BIGINT(20)', 'show'=>'INT(1)')));
		$this->setSetting('albums',mstable('site',lower($this->getSetting('name')),'albums_'.$this->getSetting('section'),array('date'=>'DATE', 'name'=>'VARCHAR(255)')));
		$iface = new CCPhotoalbum;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'albums'=>$this->getSetting('albums'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage'),'iconstorage'=>$this->getSetting('iconstorage')));
		$this->setSetting('cclass',$iface);
		$CDDataSet->add
		(
			array
			(
					'name'=>'photoalbum',
					'description'=>'Фотоальбомы',
					'types'=>array
					(
                        array('name'=>'image', 'type'=>'CDImage', 'description'=>'Изображение', 'settings'=>array()),
                        array('name'=>'header', 'type'=>'CDText', 'description'=>'Заголовок', 'settings'=>array()),
                        array('name'=>'comment', 'type'=>'CDText', 'description'=>'Комментарий', 'settings'=>array())

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
$registeredPatterns[] = array('name'=>'PPhotoalbum','description'=>'Фотоальбомы','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
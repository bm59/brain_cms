<?
class PGoods extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'Pgoods';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'goods');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'date_add'=>'DATETIME', 'date_edit'=>'DATETIME','view_count'=>'BIGINT(20)', 'cat_ids'=>'VARCHAR(1000)')));
		$iface = new CCGoods;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>&$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',&$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'goods',
					'description'=>'Товары',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'Наименование', 'settings'=>array('important'=>1)),
						array('name'=>'price', 'type'=>'CDFloat', 'description'=>'Цена', 'settings'=>array()),
                        array('name'=>'descr', 'type'=>'CDTextEditor', 'description'=>'Описание', 'settings'=>array('texttype'=>'full')),
                        array('name'=>'image', 'type'=>'CDImage',  'description'=>'Изображение', 'settings'=>array()),
                        array('name'=>'image2', 'type'=>'CDImage', 'description'=>'Изображение 2', 'settings'=>array()),
                        array('name'=>'image3', 'type'=>'CDImage', 'description'=>'Изображение 3', 'settings'=>array()),
                        array('name'=>'image4', 'type'=>'CDImage', 'description'=>'Изображение 4', 'settings'=>array()),
                        array('name'=>'image5', 'type'=>'CDImage', 'description'=>'Изображение 5', 'settings'=>array()),
                        array('name'=>'dopinfo', 'type'=>'CDText', 'description'=>'Дополнительная информация', 'settings'=>array()),
                        array('name'=>'ptitle', 'type'=>'CDText','description'=>'Title страницы','settings'=>array()),
                        array('name'=>'pdescription', 'type'=>'CDText', 'description'=>'Description страницы','settings'=>array()),
                        array('name'=>'pseudolink', 'type'=>'CDText', 'description'=>'Псеводоним ссылки','settings'=>array())

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
$registeredPatterns[] = array('name'=>'PGoods','description'=>'Товары','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
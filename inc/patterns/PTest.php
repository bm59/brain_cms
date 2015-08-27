<?
class PTest extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PTest';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'test');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Баннеры','exts'=>array('jpg','gif','jpeg', 'png', 'swf'),'imgw'=>300,'imgwtype'=>1,'imgh'=>200,'imghtype'=>1,'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'show_main'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCTest;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',$iface);
	

		 $CDDataSet->add
		( 
			
			array
			(
					'name'=>'test',
					'description'=>'Тестовый раздел',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'Наименование', 'settings'=>array()),
                        array('name'=>'enabled', 'type'=>'CDBoolean', 'description'=>'Показывать', 'settings'=>array('default'=>1)),
                        array('name'=>'spinner', 'type'=>'CDSpinner', 'description'=>'Счетчик', 'settings'=>array('default'=>5)),
                        array('name'=>'slider', 'type'=>'CDSlider', 'description'=>'Слайдер', 'settings'=>array('min'=>1, 'max'=>5, 'default'=>3)),
                        /*'range'=>'true', 'values'=>'[ 75, 300 ]' - для выбора интервала*/
                        /*'default'=>3*/
                        /*'comment'=>'маленький комментарий'*/
                        array('name'=>'choice', 'type'=>'CDChoice', 'description'=>'Выбор', 'settings'=>array('type'=>'radio')),
                        array('name'=>'choice_milti', 'type'=>'CDChoice', 'description'=>'Множ. выбор', 'settings'=>array('type'=>'multi', 'values'=>'1#первый, 2#второй, 3#третий, 4#четвертый, 5#пятый', 'comment'=>'выберите одно или несколько значений')),
                        array('name'=>'choice_milti2', 'type'=>'CDChoice', 'description'=>'Множ. выбор - внешний массив', 'settings'=>array('type'=>'multi')),
                        /*values=>'первый, второй, третий' - выбор без ид*/
                        /*values=>'2#первый, 3#второй, 4#третий' - выбор с ид*/
                        /*'comment'=>'выберите значение'*/
					)

			),
			$settings['section']
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
$registeredPatterns[] = array('name'=>'PTest','description'=>'Тестовый раздел','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
<?
$source_descr='Тестовый раздел';
$source_name='PTest';
/* Имя класса */
class PTest extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;
		
		$descr='Тестовый раздел';
		
		if ($CDDataSet->checkDatatypes($settings['section'])==0)
		$SiteSections->update_personal_settings($settings['section'], '|onoff|show_id|default_order=ORDER BY `name`|');
		
		$settings['name']=substr(get_class(), 1, strlen(get_class()));

		$class_name='CC'.$settings['name'];
		$settings['dataset'] = $CDDataSet->checkPresence(0, mb_strtolower($settings['name']));
		
		$CDDataSet->add
		(
		
				array
				(
						'name'=>mb_strtolower($settings['name']),
						'description'=>$descr,
						'types'=>array
						(
								array('name'=>'name', 'type'=>'CDText', 'description'=>'Наименование', 'settings'=>array()),
								array('name'=>'enabled', 'type'=>'CDBoolean', 'description'=>'Показывать', 'settings'=>array('default'=>1)),
								array('name'=>'spinner', 'type'=>'CDSpinner', 'description'=>'Счетчик', 'settings'=>array('default'=>5)),
								array('name'=>'slider', 'type'=>'CDSlider', 'description'=>'Слайдер', 'settings'=>array('min'=>1, 'max'=>5, 'default'=>3)),
								array('name'=>'choice', 'type'=>'CDChoice', 'description'=>'Выбор', 'settings'=>array('type'=>'radio')),
								array('name'=>'choice_milti', 'type'=>'CDChoice', 'description'=>'Множ. выбор', 'settings'=>array('type'=>'multi', 'values'=>'1#первый, 2#второй, 3#третий, 4#четвертый, 5#пятый', 'comment'=>'выберите одно или несколько значений')),
								array('name'=>'choice_milti2', 'type'=>'CDChoice', 'description'=>'Множ. выбор - внешний массив', 'settings'=>array('type'=>'multi')),
						)
		
				),
				$settings['section']
		);

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		VirtualPattern::init($settings);

		$iface = new $class_name;
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset')));
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
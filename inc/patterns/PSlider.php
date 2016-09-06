<?
$source_descr='Слайдер';
$source_name='PSlider';
/* Имя класса */
class PSlider extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Слайдер';

		if ($CDDataSet->checkDatatypes($settings['section'])==0)
		$SiteSections->update_personal_settings($settings['section'], '|precedence|onoff|show_id|default_order=ORDER BY `name`|');


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
								array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage', 'settings'=>array('show_search'=>'', 'show_list'=>'', 'exts'=>array('jpg','gif','jpeg','png'))),
								array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText',  'settings'=>array('show_search'=>'', 'show_list'=>'')),
								array('name'=>'href', 'description'=>'Ссылка', 'type'=>'CDText',  'settings'=>array())							
						)

				),
				$settings['section']
		);

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();


		VirtualPattern::init($settings);

		$iface = new $class_name;
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)')));

		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
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
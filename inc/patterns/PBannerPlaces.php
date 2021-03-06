<?
$source_descr='Баннеры - места';
$source_name='PBannerPlaces';
/* Имя класса */
class PBannerPlaces extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Универсальный';

		if ($CDDataSet->checkDatatypes($settings['section'])==0)
		$SiteSections->update_personal_settings($settings['section'], '|onoff|show_id|');

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
								array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('name'=>'description', 'description'=>'Описание', 'type'=>'CDText', 'settings'=>array()),
								array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage', 'settings'=>array('exts'=>'jpg,gif,jpeg,png')),
								array('name'=>'settings', 'description'=>'Настройки', 'type'=>'CDTextArea', 'settings'=>array())
						)

				),
				$settings['section']
		);

		$this->setSetting('type_settings_settings',
			array(
				'|imgw=200|imgh=200|'=>'Минимальная ширина',
				'|imgwtype=3|imghtype=3|'=>'1-равна; 2-меньше или равна; 3-больше или равна',
				'|editor_proport=auto|'=>'Пропорция 3:4 или auto',
				'|editor_imgh=200|editor_imgw=150|'=>'Мин. размер в редакторе',
				'|editor_minh=200|editor_minw=100|'=>'Минимизировать к размеру',
				'|editor_min_more=1|'=>'Минимизирует если область выделенная область больше'
			)
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
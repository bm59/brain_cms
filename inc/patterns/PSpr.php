<?
$source_descr='Справочник';
$source_name='PSpr';
/* Имя класса */
class PSpr extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Справочник';

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
								array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('name'=>'description', 'description'=>'Описание', 'type'=>'CDText',  'settings'=>array('off'=>'')),
								array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage',   'settings'=>array('off'=>'', 'exts'=>array('jpg','gif','jpeg','png'))),
								array('name'=>'ptitle', 'description'=>'Title страницы', 'type'=>'CDText','settings'=>array('off'=>'')),
								array('name'=>'pdescription', 'description'=>'Description страницы', 'type'=>'CDText', 'settings'=>array('off'=>'')),
								array('name'=>'pseudolink', 'description'=>'Псеводоним ссылки', 'type'=>'CDText', 'settings'=>array('off'=>''))
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
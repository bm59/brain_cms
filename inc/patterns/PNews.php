<?
$source_descr='Новости';
$source_name='PNews';
/* Имя класса */
class PNews extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Новости';

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
								array('description'=>'Дата', 'name'=>'date', 'type'=>'CDDate',  'settings'=>array('show_search'=>'', 'show_list'=>'')),
								array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('description'=>'Текст', 'name'=>'text', 'type'=>'CDTextEditor', 'settings'=>'|texttype=full|'),
								array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage',   'settings'=>array('exts'=>'jpg,gif,jpeg,png')),
								array('name'=>'tag_id', 'description'=>'Теги', 'type'=>'CDChoice',   'settings'=>'|source=#source_type=spr#spr_path=%SOURCE_PATH%#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `name`#name_only=0|type=multi|off|'),
								array('name'=>'ptitle', 'description'=>'Title страницы', 'type'=>'CDText','settings'=>array()),
								array('name'=>'pdescription', 'description'=>'Description страницы', 'type'=>'CDText', 'settings'=>array()),
								array('name'=>'pseudolink', 'description'=>'Псеводоним ссылки', 'type'=>'CDText', 'settings'=>array()),
								array('name'=>'show_count', 'description'=>'Показов', 'type'=>'CDInteger','settings'=>array('off'=>''))
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
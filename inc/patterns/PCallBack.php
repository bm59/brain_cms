<?
$source_descr='Заказ звонка - история';
$source_name='PCallBack';
/* Имя класса */
class PCallBack extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Заказ звонка - история';

		if ($CDDataSet->checkDatatypes($settings['section'])==0)
		$SiteSections->update_personal_settings($settings['section'], '|show_id|');

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
								array('description'=>'Статус', 'name'=>'status_id', 'type'=>'CDColorStatus',  'settings'=>array('default'=>1, 'source'=>'#values=1#не принят#666666, 2#в обработке#CC9900, 3#завершен#009933, 4#отменен#CC0000', 'show_list'=>'', 'list_style'=>'width: 200px', 'show_search'=>'')),
								array('description'=>'Телефон', 'name'=>'phone', 'type'=>'CDText',  'settings'=>array('show_search'=>'', 'show_list'=>'')),
								array('description'=>'Комментарий', 'name'=>'comment', 'type'=>'CDText',  'settings'=>array('show_search'=>'', 'show_list'=>'')),
								array('description'=>'Заметка', 'name'=>'note', 'type'=>'CDText',  'settings'=>array('show_search'=>''))
						)

				),
				$settings['section']
		);

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		VirtualPattern::init($settings);

		$iface = new $class_name;
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'date'=>'DATETIME', 'status_history'=>'TEXT')));

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
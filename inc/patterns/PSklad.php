<?
$source_descr='Склад';
$source_name='PSklad';
/* Имя класса */
class PSklad extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Склад';

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
								array('name'=>'kol', 'description'=>'Количество', 'type'=>'CDSpinner',  'settings'=>array('show_list'=>'')),
								array('name'=>'comment', 'description'=>'Комментарий', 'type'=>'CDText',  'settings'=>array()),
						)

				),
				$settings['section']
		);



		/* Подсказка для поля
		$this->setSetting('type_settings_%FIELD_NAME%',array('|imgw=200|imgh=200|'=>'Минимальная ширина'));*/

		/* Подсказка раздела для редактора шаблонов
		$help=array('show_vote_count'=>'Показывать сколько человек проголосовали');
		$this->setSetting('help', $help);*/

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		VirtualPattern::init($settings);

		$iface = new $class_name;
		
		/* $types=array('1'=>'Приход', '2'=>'Списание'); */
		
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array(/* 'type_id'=>'BIGINT(20)', */ 'show'=>'INT(1)', 'user_id'=>'BIGINT(20)', 'good_id'=>'BIGINT(20)', 'size_id'=>'BIGINT(20)', 'color_id'=>'BIGINT(20)', 'date'=>'DATETIME')));

		$iface->init(array('mode'=>$this->getSetting('mode'), 'types'=>$types, 'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this, 'table'=>$this->getSetting('table'), 'dataset'=>$this->getSetting('dataset')));
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
<?
$source_descr='Заказы (корзина)';
$source_name='PBasket';
/* Имя класса */
class PBasket extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Заказы';

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
								array('name'=>'status_id', 'description'=>'Статус', 'type'=>'CDColorStatus',  'settings'=>array('default'=>1, 'source'=>'#source_type=spr#spr_path=/sitecontent/basket/status/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
								array('name'=>'discount_id', 'description'=>'Скидка', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/discount/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
								array('name'=>'paytype_id', 'description'=>'Способ оплаты', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/paytype/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
								array('name'=>'delivery_id', 'description'=>'Способ доставки', 'type'=>'CDSelect',  'settings'=>array('off'=>'')),
								array('name'=>'name', 'description'=>'ФИО', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('name'=>'phone', 'description'=>'Телефон', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('name'=>'address', 'description'=>'Адрес', 'type'=>'CDText',  'settings'=>array()),
								array('name'=>'email', 'description'=>'Email', 'type'=>'CDText',  'settings'=>array()),
								array('name'=>'description', 'description'=>'Описание', 'type'=>'CDText',  'settings'=>array()),
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
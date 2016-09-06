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
								array('name'=>'status_id', 'description'=>'Статус', 'type'=>'CDColorStatus',  'settings'=>array('default'=>1, 'source'=>'#source_type=spr#spr_path=/sitecontent/basket/status/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`', 'show_list'=>'', 'list_style'=>'width: 150px')),
								array('name'=>'name', 'description'=>'ФИО', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'')),
								array('name'=>'phone', 'description'=>'Телефон', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'')),
								array('name'=>'address', 'description'=>'Адрес', 'type'=>'CDText',  'settings'=>array('show_search'=>'')),
								array('name'=>'discount_id', 'description'=>'Скидка', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/discount/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
								array('name'=>'paytype_id', 'description'=>'Способ оплаты', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/paytype/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
								array('name'=>'delivery_id', 'description'=>'Способ доставки', 'type'=>'CDSelect',  'settings'=>array('off'=>'')),
								array('name'=>'email', 'description'=>'Email', 'type'=>'CDText',  'settings'=>array()),
								array('name'=>'summ', 'description'=>'Сумма', 'type'=>'CDSpinner',  'settings'=>array('show_list'=>'')),
								array('name'=>'summ_discount', 'description'=>'Сумма скидки', 'type'=>'CDSpinner',  'settings'=>array('off'=>'')),
								array('name'=>'summ_clear', 'description'=>'Сумма без скидки', 'type'=>'CDSpinner',  'settings'=>array('off'=>'')),
								
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
		
		$this->setSetting('table_order_tmp',mstable(ConfigGet('pr_name').'_site','order','tmp',array('date'=>'DATETIME')));
		$this->setSetting('table_order_tmp_goods',mstable(ConfigGet('pr_name').'_site','order','tmp_goods',array('tmp_order_id'=>'BIGINT(20)', 'good_id'=>'BIGINT(20)', 'kol'=>'BIGINT(20)', 'price'=>'BIGINT(20)', 'summ'=>'BIGINT(20)')));
		$this->setSetting('table_order_goods',mstable(ConfigGet('pr_name').'_site','order','goods',array('order_id'=>'BIGINT(20)', 'good_id'=>'BIGINT(20)', 'kol'=>'BIGINT(20)', 'price'=>'BIGINT(20)', 'summ'=>'BIGINT(20)')));
		
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'paid'=>'INT(1) DEFAULT 0', 'accept'=>'INT(1) DEFAULT 0', 'source_id'=>'INT(1) DEFAULT 0', 'pay_type'=>'INT(1) DEFAULT 0', 'pay_date'=>'DATETIME', 'date'=>'DATETIME', 'date_accept'=>'DATETIME', 'precedence'=>'BIGINT(20)')));

		
		
		
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,
				'table'=>$this->getSetting('table'), 			
				'table_order_tmp'=>$this->getSetting('table_order_tmp'),
				'table_order_tmp_goods'=>$this->getSetting('table_order_tmp_goods'),
				'table_order_goods'=>$this->getSetting('table_order_goods'),
				'dataset'=>$this->getSetting('dataset')));
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
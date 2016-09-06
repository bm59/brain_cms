<?
$source_descr='Товары';
$source_name='PGoods';
/* Имя класса */
class PGoods extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;
		$descr='Товары';

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
								array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('name'=>'price', 'description'=>'Цена', 'type'=>'CDSpinner',  'settings'=>array( 'show_search'=>'', 'important'=>'', 'show_list'=>'')),
								array('name'=>'gallery', 'description'=>'Галерея изображений', 'type'=>'CDGallery',  'settings'=>array()),
								array('name'=>'is_size', 'description'=>'Размерный ряд', 'type'=>'CDBoolean',  'settings'=>array()),
								array('name'=>'is_color', 'description'=>'Цветовая гамма', 'type'=>'CDBoolean',  'settings'=>array()),
								
								array('name'=>'new', 'description'=>'Новинка', 'type'=>'CDBoolean',  'settings'=>array('off'=>'', 'editable'=>'', 'list_class'=>'t_minwidth')),
								array('name'=>'hit', 'description'=>'Хит продаж', 'type'=>'CDBoolean',  'settings'=>array('off'=>'', 'editable'=>'', 'list_class'=>'t_minwidth')),
								
								array('name'=>'description', 'description'=>'Описание', 'type'=>'CDText',  'settings'=>array('off'=>'')),
								array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage',   'settings'=>array('off'=>'', 'exts'=>'jpg,gif,jpeg,png')),
								array('name'=>'show_count', 'description'=>'Показов', 'type'=>'CDInteger','settings'=>array('off'=>'')),
								array('name'=>'ptitle', 'description'=>'Title страницы', 'type'=>'CDText','settings'=>array('off'=>'')),
								array('name'=>'pdescription', 'description'=>'Description страницы', 'type'=>'CDText', 'settings'=>array('off'=>'')),
								array('name'=>'pseudolink', 'description'=>'Псеводоним ссылки', 'type'=>'CDText', 'settings'=>array('off'=>''))
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
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'clear_price'=>'BIGINT(20)', 'popular'=>'BIGINT(20)', 'kol'=>'BIGINT(20)', 'size_id'=>'BIGINT(20)', 'color_id'=>'BIGINT(20)', 'categs'=>'VARCHAR(250)')));

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
<?
$source_descr='Баннеры';
$source_name='PBanners';
/* Имя класса */
class PBanners extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Баннеры';

		if ($CDDataSet->checkDatatypes($settings['section'])==0)
		$SiteSections->update_personal_settings($settings['section'], '|onoff|show_id|precedence|');

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
							array('name'=>'date_start', 'description'=>'Дата начала', 'type'=>'CDDate',  'settings'=>array('nodefault'=>'','changemonth'=>'','changeyear'=>'','numberofmonths'=>'3','important'=>'', 'show_list'=>'', 'list_class'=>'t_minwidth')),
							array('name'=>'date_end', 'description'=>'Дата окончания', 'type'=>'CDDate',  'settings'=>array('nodefault'=>'','changemonth'=>'','changeyear'=>'','numberofmonths'=>'3','important'=>'', 'show_list'=>'', 'list_class'=>'t_minwidth')),
							array('name'=>'name', 'description'=>'Наименование', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'', 'show_list'=>'')),
							array('name'=>'href', 'description'=>'Ссылка', 'type'=>'CDText',  'settings'=>array('show_search'=>'', 'show_list'=>'')),
							array('name'=>'image', 'description'=>'Изображение', 'type'=>'CDImage',   'settings'=>array('exts'=>'jpg,gif,jpeg,png,swf')),
							array('name'=>'code', 'description'=>'Код баннера', 'type'=>'CDTextArea', 'settings'=>array()),
							array('name'=>'border', 'description'=>'Обводить рамкой', 'type'=>'CDBoolean', 'settings'=>array()),
							array('name'=>'inner_href', 'description'=>'Вшитая ссылка', 'type'=>'CDBoolean', 'settings'=>array()),
						)

				),
				$settings['section']
		);

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/banners/','name'=>'Баннеры','exts'=>array('swf','jpg','gif','jpeg', 'png'),'images'=>1));

		VirtualPattern::init($settings);

		$iface = new $class_name;

		$help=array(
			/* 'show_vote_count'=>'Показывать сколько человек проголосовали' */
		);

		$this->setSetting('help', $help);



		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'place_id'=>'BIGINT(20) DEFAULT 0')));
		$this->setSetting('table_stat',mstable(ConfigGet('pr_name').'_site','banners','stat',array('banner_id'=>'BIGINT(20)', 'date'=>'DATE', 'show'=>'BIGINT(20) DEFAULT 0', 'click'=>'BIGINT(20) DEFAULT 0', 'unique'=>'BIGINT(20) DEFAULT 0')));

		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'table_stat'=>$this->getSetting('table_stat'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage')));
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
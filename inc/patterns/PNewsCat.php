<?
class PNewsCat extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PNewsCat';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'newscat');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg','png'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCNewsCat;
		$iface->init(array('catspr'=>'/spr/categs/', 'mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'newscat',
					'description'=>'Лента с категориями и порядком',
					'types'=>array
					(
						array
						(
							'description'=>'Дата',
							'name'=>'date',
							'type'=>'CDDate',
							'settings'=>array()
						),
						array
						(
							'description'=>'Категория',
							'name'=>'cat_id',
							'type'=>'CDInteger',
							'settings'=>array()
						),
						array
						(
							'description'=>'Картинка',
							'name'=>'image',
							'type'=>'CDImage',
							'settings'=>array()
						),
						array(
							'description'=>'Заголовок',
							'name'=>'header',
							'type'=>'CDText',
							'settings'=>array('important'=>1)
						),
                        array(
	                        'description'=>'Анонс',
	                        'name'=>'short',
	                        'type'=>'CDTextArea',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'Текст',
	                        'name'=>'text',
	                        'type'=>'CDTextEditor',
	                        'settings'=>array('texttype'=>'full')
                        ),
                        array(
	                        'description'=>'Title страницы',
	                        'name'=>'ptitle',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'Description страницы',
	                        'name'=>'pdescription',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'Псеводоним ссылки',
	                        'name'=>'pseudolink',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        )

					)

			)
		);

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
$registeredPatterns[] = array('name'=>'PNewsCat','description'=>'Лента с категориями и порядком','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
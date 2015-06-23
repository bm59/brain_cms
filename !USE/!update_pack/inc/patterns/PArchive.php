<?
/*
Шаблон публикаций (Ленты)
*/
class PArchive extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','Количество элементов на странице типа «Лента»',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PArchive';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'archive');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'Изображения сайта (общее)','exts'=>array('jpg','gif','jpeg','png'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'Файлы сайта (общее)'));

		$settings['pdf_filestorage'] = $Storage->getStorage(0,array('path'=>'/site/pdf/','name'=>'Файлы сайта (общее)','exts'=>array('pdf')));

		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCArchive;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'pdf_filestorage'=>$this->getSetting('pdf_filestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'archive',
					'description'=>'Архив газеты',
					'types'=>array
					(
						array
						(
							'description'=>'Дата выхода',
							'name'=>'date',
							'type'=>'CDDate',
							'settings'=>array()
						),
						array
						(
							'description'=>'Номер',
							'name'=>'num',
							'type'=>'CDText',
							'settings'=>array('|important|')
						),
						array(
	                        'description'=>'PDF файл',
	                        'name'=>'pdf',
	                        'type'=>'CDFile',
	                        'settings'=>array('texttype'=>'full')
                        ),
                        array(
	                        'description'=>'Год',
	                        'name'=>'year',
	                        'type'=>'CDText',
	                        'settings'=>array('|important|')
                        ),
                        array(
	                        'description'=>'Описание',
	                        'name'=>'text',
	                        'type'=>'CDTextEditor',
	                        'settings'=>array('texttype'=>'full')
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
$registeredPatterns[] = array('name'=>'PArchive','description'=>'Архив газеты','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
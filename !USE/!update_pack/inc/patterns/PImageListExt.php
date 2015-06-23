<?
class PImageListExt extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','���������� ��������� �� �������� ���� ������',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PImageListExt';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'imagelistext');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'show_main'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCImageListExt;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'imagelistext',
					'description'=>'������ � ���������� �����������',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'������������', 'settings'=>array()),
                        array('name'=>'image', 'type'=>'CDImage', 'description'=>'�����������', 'settings'=>array()),
                        array('name'=>'descr', 'type'=>'CDTextEditor', 'description'=>'��������', 'settings'=>array('texttype'=>'full')),
                        array('name'=>'url', 'type'=>'CDText', 'description'=>'������', 'settings'=>array()),
                        array('name'=>'dopinfo', 'type'=>'CDText', 'description'=>'�������������� ����������', 'settings'=>array())

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
$registeredPatterns[] = array('name'=>'PImageListExt','description'=>'������ � ���������� �����������','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
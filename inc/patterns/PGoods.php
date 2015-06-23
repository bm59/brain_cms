<?
class PGoods extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','���������� ��������� �� �������� ���� ������',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'Pgoods';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'goods');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'date_add'=>'DATETIME', 'date_edit'=>'DATETIME','view_count'=>'BIGINT(20)', 'cat_ids'=>'VARCHAR(1000)')));
		$iface = new CCGoods;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>&$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',&$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'goods',
					'description'=>'������',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'������������', 'settings'=>array('important'=>1)),
						array('name'=>'price', 'type'=>'CDFloat', 'description'=>'����', 'settings'=>array()),
                        array('name'=>'descr', 'type'=>'CDTextEditor', 'description'=>'��������', 'settings'=>array('texttype'=>'full')),
                        array('name'=>'image', 'type'=>'CDImage',  'description'=>'�����������', 'settings'=>array()),
                        array('name'=>'image2', 'type'=>'CDImage', 'description'=>'����������� 2', 'settings'=>array()),
                        array('name'=>'image3', 'type'=>'CDImage', 'description'=>'����������� 3', 'settings'=>array()),
                        array('name'=>'image4', 'type'=>'CDImage', 'description'=>'����������� 4', 'settings'=>array()),
                        array('name'=>'image5', 'type'=>'CDImage', 'description'=>'����������� 5', 'settings'=>array()),
                        array('name'=>'dopinfo', 'type'=>'CDText', 'description'=>'�������������� ����������', 'settings'=>array()),
                        array('name'=>'ptitle', 'type'=>'CDText','description'=>'Title ��������','settings'=>array()),
                        array('name'=>'pdescription', 'type'=>'CDText', 'description'=>'Description ��������','settings'=>array()),
                        array('name'=>'pseudolink', 'type'=>'CDText', 'description'=>'���������� ������','settings'=>array())

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
$registeredPatterns[] = array('name'=>'PGoods','description'=>'������','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
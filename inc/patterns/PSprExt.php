<?
/*
������ �����������
*/
class PSprExt extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','���������� ��������� �� �������� ���� ������',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PSprExt';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'sprext');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'����� ����� (�����)'));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCSprExt;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'sprext',
					'description'=>'���������� �����������',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'������������', 'settings'=>array('important'=>1)),
						array('name'=>'dop1', 'type'=>'CDText', 'description'=>'��� 1', 'settings'=>array()),
						array('name'=>'dop2', 'type'=>'CDText', 'description'=>'��� 2', 'settings'=>array()),
						array('name'=>'dop3', 'type'=>'CDText', 'description'=>'��� 3', 'settings'=>array()),
                        array(
	                        'description'=>'������� ��������',
	                        'name'=>'short',
	                        'type'=>'CDTextArea',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'��������',
	                        'name'=>'text',
	                        'type'=>'CDTextEditor',
	                        'settings'=>array('texttype'=>'full')
                        ),
                        array(
	                        'description'=>'���������� ������',
	                        'name'=>'pseudolink',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'Title ��������',
	                        'name'=>'ptitle',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        ),
                        array(
	                        'description'=>'Description ��������',
	                        'name'=>'pdescription',
	                        'type'=>'CDText',
	                        'settings'=>array()
                        ),
                        array
						(
							'description'=>'��������',
							'name'=>'image',
							'type'=>'CDImage',
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
$registeredPatterns[] = array('name'=>'PSprExt','description'=>'���������� �����������','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
<?
class PTest extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','���������� ��������� �� �������� ���� ������',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PTest';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'test');
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'�������','exts'=>array('jpg','gif','jpeg', 'png', 'swf'),'imgw'=>300,'imgwtype'=>1,'imgh'=>200,'imghtype'=>1,'images'=>1));
		$settings['smallimagestorage'] = $Storage->getStorage(0,array('path'=>'/site/images/','name'=>'����������� ����� (�����)','exts'=>array('jpg','gif','jpeg', 'png'),'images'=>1));
		VirtualPattern::init($settings);
		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'show_main'=>'INT(1)', 'precedence'=>'BIGINT(20)')));
		$iface = new CCTest;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
		$this->setSetting('cclass',$iface);
	

		 $CDDataSet->add
		( 
			
			array
			(
					'name'=>'test',
					'description'=>'�������� ������',
					'types'=>array
					(
						array('name'=>'name', 'type'=>'CDText', 'description'=>'������������', 'settings'=>array()),
                        array('name'=>'enabled', 'type'=>'CDBoolean', 'description'=>'����������', 'settings'=>array('default'=>1)),
                        array('name'=>'spinner', 'type'=>'CDSpinner', 'description'=>'�������', 'settings'=>array('default'=>5)),
                        array('name'=>'slider', 'type'=>'CDSlider', 'description'=>'�������', 'settings'=>array('min'=>1, 'max'=>5, 'default'=>3)),
                        /*'range'=>'true', 'values'=>'[ 75, 300 ]' - ��� ������ ���������*/
                        /*'default'=>3*/
                        /*'comment'=>'��������� �����������'*/
                        array('name'=>'choice', 'type'=>'CDChoice', 'description'=>'�����', 'settings'=>array('type'=>'radio')),
                        array('name'=>'choice_milti', 'type'=>'CDChoice', 'description'=>'����. �����', 'settings'=>array('type'=>'multi', 'values'=>'1#������, 2#������, 3#������, 4#���������, 5#�����', 'comment'=>'�������� ���� ��� ��������� ��������')),
                        array('name'=>'choice_milti2', 'type'=>'CDChoice', 'description'=>'����. ����� - ������� ������', 'settings'=>array('type'=>'multi')),
                        /*values=>'������, ������, ������' - ����� ��� ��*/
                        /*values=>'2#������, 3#������, 4#������' - ����� � ��*/
                        /*'comment'=>'�������� ��������'*/
					)

			),
			$settings['section']
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
$registeredPatterns[] = array('name'=>'PTest','description'=>'�������� ������','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
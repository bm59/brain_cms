<?
/*
������ �����������
*/
class PBanners extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage;
		$SiteSettings = new SiteSettings;
		$SiteSettings->init();
		$SiteSettings->add('pub_page_count','���������� ��������� �� �������� ���� ������',20,array('type'=>'integer','notnull'=>'','undeletable'=>''));
		$settings['name'] = 'PBanners';
		$settings['dataset'] = $CDDataSet->checkPresence(0,'banners');

		$img_params=msr(msq("SELECT * FROM `site_site_sections` WHERE id=".$settings['section']));


        if ($settings['section']==12)
		$settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/banners/245_400/','name'=>'�������','exts'=>array('jpg','gif','jpeg', 'png', 'swf'),'imgw'=>245,'imgwtype'=>1,'imgh'=>400,'imghtype'=>1,'images'=>1));
        else
        $settings['imagestorage'] = $Storage->getStorage(0,array('path'=>'/site/banners/','name'=>'�������','exts'=>array('jpg','gif','jpeg', 'png', 'swf')));


		$settings['filestorage'] = $Storage->getStorage(0,array('path'=>'/site/files/','name'=>'����� ����� (�����)'));
		$settings['shows'] = 'pr_banners_shows';
        $settings['clicks'] = 'pr_banners_clicks';

		VirtualPattern::init($settings);



		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)')));
		$iface = new CCBanners;
		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'filestorage'=>$this->getSetting('filestorage')));
		$this->setSetting('cclass',$iface);

		$CDDataSet->add
		(
			array
			(
					'name'=>'banners',
					'description'=>'�������',
					'types'=>array
					(
						array
						(
							'description'=>'�������� �������',
							'name'=>'name',
							'type'=>'CDText',
							'settings'=>array('important'=>1)
						),
						array
						(
							'description'=>'������',
							'name'=>'href',
							'type'=>'CDText',
							'settings'=>array()
						),
						array
						(
							'description'=>'������',
							'name'=>'image',
							'type'=>'CDImage',
							'settings'=>array()
						),
                        array('name'=>'startdate', 'type'=>'CDDate', 'description'=>'���� �', 'settings'=>array()),
						array('name'=>'enddate', 'type'=>'CDDate', 'description'=>'���� ��', 'settings'=>array())

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
$registeredPatterns[] = array('name'=>'PBanners','description'=>'�������','useradd'=>1);
configSet('registeredPatterns',$registeredPatterns);
?>
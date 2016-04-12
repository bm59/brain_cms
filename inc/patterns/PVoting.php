<?
$source_descr='Голосования';
$source_name='PVoting';
/* Имя класса */
class PVoting extends VirtualPattern
{
	function init($settings){
		global $CDDataSet,$Storage,$SiteSections;

		$descr='Голосования';

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
								array('name'=>'name', 'description'=>'Вопрос', 'type'=>'CDText',  'settings'=>array('important'=>'', 'show_search'=>'', 'show_list'=>'')),
								array('description'=>'Комментарий', 'name'=>'text', 'type'=>'CDTextEditor', 'settings'=>'|texttype=full|'),
						)

				),
				$settings['section']
		);

		$SiteSettings = new SiteSettings;
		$SiteSettings->init();

		VirtualPattern::init($settings);

		$iface = new $class_name;

		$help=array(
			'show_vote_count'=>'Показывать сколько человек проголосовали'
		);

		$this->setSetting('help', $help);

		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)', 'result'=>'BIGINT(20) DEFAULT 0')));
		$this->setSetting('table_answers',mstable(ConfigGet('pr_name').'_site','voting','answers',array('show'=>'INT(1) DEFAULT 0', 'precedence'=>'BIGINT(20)', 'text'=>'VARCHAR(255)', 'voting_id'=>'BIGINT(20)', 'result'=>'BIGINT(20) DEFAULT 0', 'image'=>'BIGINT(20)')));
		$this->setSetting('table_log',mstable(ConfigGet('pr_name').'_site','voting','log',array('voting_id'=>'BIGINT(20)', 'question_id'=>'BIGINT(20)', 'ip'=>'VARCHAR(20)', 'real_ip'=>'VARCHAR(20)', 'time_page'=>'BIGINT(20)', 'time_vote'=>'BIGINT(20)', 'time_diff'=>'BIGINT(20)', 'server_info'=>'TEXT', 'page'=>'VARCHAR(255)')));
/* 		$this->setSetting('table',$this->createDataSetTable($this->getSetting('dataset'),$this->getSetting('section'),array('show'=>'INT(1)', 'precedence'=>'BIGINT(20)'))); */

		$iface->init(array('mode'=>$this->getSetting('mode'),'isservice'=>$this->getSetting('isservice'),'section'=>$this->getSetting('section'),'pattern'=>$this,'table'=>$this->getSetting('table'),'dataset'=>$this->getSetting('dataset'),'imagestorage'=>$this->getSetting('imagestorage'),'smallimagestorage'=>$this->getSetting('smallimagestorage')));
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
<?

class CCUniversal extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);
                
                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];
                
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;
        		
                
                
                $this->like_array=array();/* ��� ��� � �������� "name", �� ����� ����� �� like - search_href*/
                $this->not_like_array=array();/* ��� ���� � �������� "name", �� �� ����� ����� �� like*/
                $this->no_auto=array(); /*�� ������������ ����������, ������ ���������*/
                
                /*�������� �� ������ � ��������� ��������� ��� ������*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');
                
                /*������� ��������*/
                $this->field_change=array();
                
                
 				$this->getSearch();

        
   }


}
?>
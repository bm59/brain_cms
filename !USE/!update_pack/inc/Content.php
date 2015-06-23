<?
/*
�����, ����������� ��������� �������� ��� ���������� ����, ����������� ������� � �.�.
*/
class Content extends VirtualClass
{
	function init(){
		$this->Settings['structure'] = array(
			  "0"=>array("path"=>'manage',"childs"=>array("100","200")),
              "100"=>array("name"=>'���������� ������',"path"=>'control',"childs"=>array("110","120")),
              	"110"=>array("name"=>'���������',"path"=>'settings',"childs"=>array()),
              	"120"=>array("name"=>'�������',"path"=>'contents',"childs"=>array()),
              "200"=>array("name"=>'���������� ��������������',"path"=>'access',"childs"=>array("210","220")),
              	"210"=>array("name"=>'������������',"path"=>'users',"childs"=>array()),
              	"220"=>array("name"=>'������',"path"=>'groups',"childs"=>array())
		);
	}
	function getList($id = 0){ /* ��������� ������ �������� */
		$id = floor($id);
		$retval = array();
		$childs = $this->Settings['structure'][$id]['childs'];
		if (is_array($childs)){
			foreach ($childs as $v){
				if (is_array($this->Settings['structure'][$v])) $retval[$v] = $this->Settings['structure'][$v];
			}
		}
		return $retval;
	}
	function getOne($id){ /* ��������� ���������� � ���������� ������� */
		$id = floor($id);
		$retval = false;
		if (is_array($this->Settings['structure'][$id])) $retval = $this->Settings['structure'][$id];
		$retval['id'] = $id;
		return $retval;
	}
	function getIdByPath($path){
		$path = explode('/',preg_replace('|\/$|','',preg_replace('|^\/|','',trim($path))));
		$retval = 0;
		if (is_array($path)){
			foreach ($path as $p){
				$childs = $this->Settings['structure'][$retval]['childs'];
				foreach ($childs as $k){
					$c = $this->getOne($k);
					if ($c['path']==$p) $retval = $k;
				}
			}
		}
		return $retval;
	}
	function getParent($id){
		$id = floor($id);
		$retval = 0;
		foreach ($this->getSetting('structure') as $k=>$v) if (in_array($id,$v['childs'])) $retval = $k;
		return $retval;
	}
	function getPath($id){
		$retval = '/';
		while ($id>0){
			$c = $this->getOne($id);
			$id = $this->getParent($id);
			$retval = '/'.$c['path'].$retval;
		}
		return $retval;
	}
}
?>
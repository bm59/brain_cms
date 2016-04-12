<?

class CCBasket extends VirtualContent
{

	function init($settings){
        		global $SiteSections;
                VirtualContent::init($settings);

                $section = $SiteSections->get($this->getSetting('section'));
                $this->Settings['settings_personal']=$section['settings_personal'];

                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $this->Settings['settings_personal']['on_page']>0 ? $section['settings_personal']['on_page'] : 20;



                $this->like_array=array('search_href');/* Где нет в названии "name", но нужен поиск по like - search_href*/
                $this->not_like_array=array();/* Где есть в названии "name", но не нужен поиск по like*/
                $this->no_auto=array(); /*Не обрабатывать переменные, ручная обработка*/

                /*заменить на пустое в названиях переменны при поиске*/
                $this->field_tr=array('search_'=>'','_from'=>'','_to'=>'');

                /*подмена названий*/
                $this->field_change=array();


 				$this->getSearch();


   }
   function createSubSection ($sub_path='', $sub_name='', $add_fields=array(), $add_values=array(), $settings_personal)
   {
   		global $CDDataSet, $SiteSections, $DataType;
   			
   		$parent = $SiteSections->get($this->getSetting('section'));
   		$parent_path = $SiteSections->getPath($this->getSetting('section'));
   		
   		$sub_section = $SiteSections->get($SiteSections->getIdByPath($parent_path.$sub_path.'/'));
   		
   		if (!$sub_section['id']>0)
   		{
   			$SiteSections->add(array(
   					'name'=>$sub_name, 
   					'path'=>$sub_path, 
   					'pattern'=>'PUniversal',
   					'settings_personal'=>array($settings_personal)
   					
   			), $this->getSetting('section'));
   			
   			$sub_section = $SiteSections->get($SiteSections->getIdByPath($parent_path.$sub_path.'/'));
   			 
   			$dt=new DataType;
   			$dt->init();
   			 
   			/* Добавляем поля в раздел */
   			foreach ($add_fields as $add)
   			{
   				$add['section_id']=$sub_section['id'];
   				$dt->add($add, true, '', $sub_section['id']);
   			}
   			
   			 
   			/* Вставляем начальные данные в таблицу */
   			if ($sub_section['id']>0 && count($add_values)>0)
   			{
   				$Pattern = new $sub_section['pattern'];
   				$Iface = $Pattern->init(array('section'=>$sub_section['id']));
   				foreach ($add_values as $add)
   				{
   					$this->insertNotDouble($add, $Iface->getSetting('table'));
   				}
   			}
   		
   		}

   		
   		
   		
   		
   		
   		
   }
   function drawAddEdit(){
   	global $CDDataSet,$SiteSections, $multiple_editor;
   	$section = $SiteSections->get($this->getSetting('section'));
   
   	$SectionPattern = new $section['pattern'];
   	$Iface = $SectionPattern->init(array('section'=>$section['id'],'mode'=>$delepmentmode,'isservice'=>0));
   	
   	/* Статусы заказа */
   	$this->createSubSection(
   			'status', 
   			'Статусы заказа',
   			/* Добавляем поля */
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'color', 'description'=>'Цвет', 'type'=>'CDText', 'settings'=>'|show_search|show_list|', 'table_type'=>'VARCHAR(255)')	
   			),
   			/* Добавляем значения */
   			array
   			(
   				array('name'=>'В обработке', 'color'=>'#666666', 'show'=>1),
   				array('name'=>'Принят', 'color'=>'#CC9900', 'show'=>1),
   				array('name'=>'Завершен', 'color'=>'#009933', 'show'=>1),
   				array('name'=>'Отменен', 'color'=>'#CC0000', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);
   	
   	/* Способы доставки */
   	$this->createSubSection(
   			'paytype',
   			'Способы оплаты',
   			/* Добавляем поля */
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			/* Добавляем значения */
   			array
   			(
   					array('name'=>'Наличные', 'show'=>1),
   					array('name'=>'Безнал', 'show'=>1),
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);
   	
   	/* Скидки */
   	$this->createSubSection(
   			'discount',
   			'Скидки',
   			/* Добавляем поля */
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'procent', 'description'=>'Процент', 'type'=>'CDSpinner', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   					
   			),
   			/* Добавляем значения */
   			array
   			(
   					array('name'=>'3 процента', 	'procent'=>'3', 'show'=>1),
   					array('name'=>'5 процентов', 	'procent'=>'5', 'show'=>1),
   					array('name'=>'10 процентов',	'procent'=>'10', 'show'=>1),
   					array('name'=>'15 процентов', 	'procent'=>'15', 'show'=>1)
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);
   	
   	/* Категории товара */
   	$this->createSubSection(
   			'categories',
   			'Категории',
   			/* Добавляем поля */
   			array
   			(
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)')
   			),
   			/* Добавляем значения */
   			array
   			(		array('name'=>'Категория 1', 'show'=>1),
   					array('name'=>'Категория 2', 'show'=>1)
   					
   			),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|precedence|'
   	);
   	
   	/* Товары */
   	$this->createSubSection(
   			'goods',
   			'Товары',
   			/* Добавляем поля */
   			array
   			(
   					array('dataset'=>2, 'name'=>'cat_id', 'description'=>'Категория товара', 'type'=>'CDSelect',  'settings'=>array('source'=>'#source_type=spr#spr_path=/sitecontent/basket/categories/#spr_field=name#spr_usl=WHERE `show`=1#spr_order=ORDER BY `id`')),
   					array('dataset'=>2, 'name'=>'name', 'description'=>'Наименование', 'type'=>'CDText', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(255)'),
   					array('dataset'=>2, 'name'=>'images', 'description'=>'Картинки', 'type'=>'CDGallery', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'VARCHAR(1000)'),
   					array('dataset'=>2, 'name'=>'price', 'description'=>'Цена',  'type'=>'CDSpinner', 'settings'=>'|show_search|show_list|important|', 'table_type'=>'BIGINT(20)'),
   					array('dataset'=>2, 'name'=>'description', 'description'=>'Описание', 'type'=>'CDText',  'settings'=>array()),
   					array('dataset'=>2, 'name'=>'ptitle', 'description'=>'Title страницы', 'type'=>'CDText','settings'=>array()),
   					array('dataset'=>2, 'name'=>'pdescription', 'description'=>'Description страницы', 'type'=>'CDText', 'settings'=>array()),
   					array('dataset'=>2, 'name'=>'pseudolink', 'description'=>'Псеводоним ссылки', 'type'=>'CDText', 'settings'=>array())
   					
   			),
   			/* Добавляем значения */
   			array
   			(),
   			'|onoff|show_id|default_order=ORDER BY `id` ASC|'
   	);
   	
   	$init_pattern=$Iface->getSetting('pattern');
   
   	if ($this->editor_cnt>1)
   	{
   		$multiple_editor=true;
   		?><script type="text/javascript" src="/js/tinymce/tinymce.js"></script><?
   			}
   
   			$pub = $this->getPub($_GET['pub']);
   			$pub['id'] = floor($pub['id']);
   			?>
   		                <div id="content" class="forms">
   		                        <?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/nav.php");?>
   		                        <?
   		                        $saveerrors = $this->getSetting('saveerrors');
   		                        if (!is_array($saveerrors)) $saveerrors = array();
   		                        if (count($saveerrors)>0){
   		                                print '
   		                                <p><strong>Сохранение не выполнено по следующим причинам:</strong></p>
   		                                <ul class="errors">';
   		                                        foreach ($saveerrors as $v) print '
   		                                        <li>'.$v.'</li>';
   		                                print '
   		                                </ul>
   		                                <div class="hr"><hr /></div>';
   		                        }
   		                        ?>
   		                        <p class="impfields">Поля, отмеченные знаком «<span class="important">*</span>», обязательные для заполнения.</p>
   		                        <form id="editform" name="editform" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
   		                                <input type="hidden" name="editformpost" value="1">
   		                                <?
   		                                $stylearray = array(
   		                                		"ptitle"=>'style="width:32%; margin-right:2%;"',
   		                                		"pdescription"=>'style="width:32%; margin-right:2%;"',
   		                                		"pseudolink"=>'style="width:32%;"'
   		                                );
   		                                $nospans = array("ptitle","pdescription");
   
   
   		                                $dataset = $this->getSetting('dataface');
   		                                foreach ($dataset['types'] as $dt)
   		                                {
   		                                		$tface = $dt['face'];
   
   		                                        if (isset($dt['setting_style_edit']['css'])) $stylearray[$dt['name']]='style="'.$dt['setting_style_edit']['css'].'"';
   		                                        if (isset($dt['settings']['nospan'])) $nospans[]=$dt['name'];
   
   		    									if (!isset($dt['settings']['off']))
   		                                        $tface->drawEditor($stylearray[$tface->getSetting('name')],((in_array($tface->getSetting('name'),$nospans))?false:true));
   
   
   		    									/* Подсказки у поля в паттерне: $this->setSetting('type_settings_settings', array('min_w|'=>'Минимальная ширина')); */
   		    									if (count($init_pattern->Settings['type_settings_'.$dt['name']])>0)
   		    									{
   		    										foreach ($init_pattern->Settings['type_settings_'.$dt['name']] as $k=>$v)
   		    										print $k.' - '.$v.'<br/>';
   
   		    										?><div class="clear"></div><?
   		    									}
   
   
   		                                }
   
   
   		                        ?>
   
   
   		                        </table>
   		                        <div class="place">
   		                                <span style="float: right;">
   		                                	<input class="button big" type="submit" name="editform" value="<?=($pub['id']>0)?'Сохранить изменения':'Добавить'?>"/>
   		                                </span>
   		                        </div>
   
   		                        <span class="clear"></span>
   		                        </form>
   		                </div>
   		                <?
   		                if (isset($this->Settings['settings_personal']['reklama']))
   		                include_once($_SERVER['DOCUMENT_ROOT']."/inc/site_admin/reklama/add_pattern.php");
   	}
  

}
?>
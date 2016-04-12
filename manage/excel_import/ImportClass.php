<? 
class ImportClass 
{
	var $name_comment=array(
			'имя'=>'name',
			'фио'=>'name',	
			'email'=>'email',
			'почта'=>'email',
			'дата'=>'date',
			'телефон'=>'phone',
			'мобильный'=>'phone',
			'контакты'=>'phone'
			
	);
	
	function init($settings=array())
	{
		if (is_array($settings['name_comment'])) $this->name_comment=array_merge($this->name_comment, $settings['name_comment']);
	}
	function getNameTemplate($val)
	{
		foreach ($this->name_comment as $k=>$v)
		{
			if (stripos(strtolower($val), $k)!== false)
			return $v;
		}
		
	}
	function FileAnalize($file)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].'/inc/excel/PHPExcel/IOFactory.php';
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		$all_row=0;
		$values='';
		$headers_html='';
		
		for ($i=0;  $i<=$objPHPExcel->getSheetCount()-1; $i++)
		{
			
			$objPHPExcel->setActiveSheetIndex($i);
			$aSheet = $objPHPExcel->getActiveSheet();
			

			$highestRow         = $aSheet->getHighestRow();
			$highestColumn      = $aSheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

			
			$first_noempty_row=0;
			
			
			if ($highestRow>1 && $highestColumnIndex>0)
			for ($row = 1; $row <= $highestRow; $row++) 
			{
				$cur_val='';
				
				for ($col = 0; $col < $highestColumnIndex; $col++)
				{
					
					$val=$aSheet->getCellByColumnAndRow($col, $row);
					
					if ($val!='')
					{
						$val=trim(iconv('utf-8', 'cp1251', $val));
						
 						if(PHPExcel_Shared_Date::isDateTime($aSheet->getCellByColumnAndRow($col, $row))) {
							$val = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($val));
						}
						
						if (!$first_noempty_row>0) $first_noempty_row=$row;
						
					}
					
					$cur_val.=($col>0 ? '|':'').$val;
					
					
				}
				
				if (str_replace('|', '', $cur_val)!='' && ($_POST['first_header']!='on' || $row>$first_noempty_row)) 
				$values.="<div>$cur_val</div>";
				
				$all_row++;
			}
			
			
			/* Получаем заголовки если первая строка - заголовок */
			if ($first_noempty_row>0 && $_POST['first_header']=='on')
			{
				for ($col = 0; $col < $highestColumnIndex; $col++)
				{
					$val=$aSheet->getCellByColumnAndRow($col, $first_noempty_row);
					$val=trim(iconv('utf-8', 'cp1251', $val));

					$headers_html.='<tr><td class="onoff_container"></td><td><div class="input"><input value="'.$val.'" /></div></td><td><div class="input mysql_name"><input value="'.$this->getNameTemplate($val).'" /></div></td><td class="unique_container"></td></tr>';
				}
			}
			
			
			
			
		}
		
		$result.="<div>Всего строк: $all_row</div>";
		
		if ($headers_html!='')
		$result.="<div class='header_html_ajax' style='display: none;'><table><tr><th>Импорт</th><th>Описание</th><th>Имя в БД</th><th>Уникальное</th></tr>$headers_html</table></div>";
		
		if ($values!='')
		$result.="<div class='insert_values' style='display: none;'>$values</div>";
		print $result;
	}
}
?>
<?
class CCImag extends VirtualContent
{

        function init($settings){
                VirtualContent::init($settings);
                $SiteSettings = new SiteSettings;
                $SiteSettings->init();
                $this->Settings['onpage'] = $SiteSettings->getOne($SiteSettings->getIdByName('pub_page_count')); $this->Settings['onpage'] = (floor($this->Settings['onpage']['value'])>0)?floor($this->Settings['onpage']['value']):20;
 		}
        function GetTmpOrdernum()
        {
            $retval=0;
/*            msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_order` WHERE datediff( date, now())<0");
            msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_goods` WHERE `order` not in (select `number` from `".ConfigGet('pr_name')."_imag_tmp_order`)");*/
            $q = msq("SELECT max(number) as num FROM `".ConfigGet('pr_name')."_imag_tmp_order`");
            $r = msr($q);
            $retval=$r['num']+1;

            msq("INSERT INTO `".ConfigGet('pr_name')."_imag_tmp_order` (`date`,`number`) VALUES (NOW(),'".$retval."')");

            return $retval;
        }
        function CheckTmpOrdernum($num)
        {
            $retval=0;
            $q = msr(msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_order` WHERE `number`=".$num));
            return $q;
        }
        function AddGood($add=array())
        {
	         	$keys = $values = "";
	            foreach ($add as $key=>$value)
	            {
	                    if ($key!=''){
	                            $keys.= (($keys=="")?"":", ")."`".$key."`";
	                            $values.= (($values=="")?"":", ")."'".myspecialchars($value)."'";
	                    }
	            }

            	msq("INSERT INTO `".ConfigGet('pr_name')."_imag_tmp_goods` (".$keys.") VALUES (".$values.")");

            	return $retval;
        }
        function SaveOrder($add=array(), $order=0, $summ=0, $time_dif=0)
        {
            $keys = $values = "";
            foreach ($add as $key=>$value){
                    if ($key!=''){
                            $keys.= (($keys=="")?"":", ")."`".$key."`";
                            $values.= (($values=="")?"":", ")."'".myspecialchars($value['value'])."'";
                    }
            }
           print "INSERT INTO `".ConfigGet('pr_name')."_imag_order` (`show`, ".$keys.", `date`) VALUES ('0',".$values.", TIMESTAMPADD(HOUR,".$time_dif.",NOW()))";
           msq("INSERT INTO `".ConfigGet('pr_name')."_imag_order` (`show`, ".$keys.", `date`) VALUES ('0',".$values.", TIMESTAMPADD(HOUR,".$time_dif.",NOW()))");
           $id=mslastid();

			$goods_key_txt='';
			$goods_key = msq("SHOW COLUMNS FROM `site_imag_tmp_goods`");
			while($col = msr($goods_key))
			if ($col['Field']!='id' && $col['Field']!='order_id')
			{
			    $goods_key_txt.= (($goods_key_txt=="")?"":", ")."`".$col['Field']."`";
			}
			print "INSERT INTO `".ConfigGet('pr_name')."_imag_goods` (`order_id`,".$goods_key_txt.")  SELECT '".$id."', ".$goods_key_txt." FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=".$order;

            msq("INSERT INTO `".ConfigGet('pr_name')."_imag_goods` (`order_id`,".$goods_key_txt.")  SELECT '".$id."', ".$goods_key_txt." FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=".$order);
            return $id;
        }
        function CheckinOrder($order, $good)
        {
            $ret=false;
            $q = msr(msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order and `good_num`=$good"));
            if ($q['id']>0)
            $ret=true;

            return $ret;
        }
        function KolinOrder($order, $good, $half_type='')
        {
            if ($half_type!=='') $dop_sql=" and `half_type`=".$half_type;
            $q = msr(msq("SELECT sum(`kol`) as kol FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order and `good_num`=$good".$dop_sql));
            $last_half_type=msr(msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order and `good_num`=$good ORDER BY `id` DESC LIMIT 1"));
            $q['half_type']=$last_half_type['half_type'];

            return $q;
        }
        function IteminOrder($order)
        {
            $q = msr(msq("SELECT count(distinct(good_num)) as `cnt` FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order"));

            return $q['cnt'];
        }
        function UpdateKolSum($ord, $kol, $summ)
        {
            msq("UPDATE `".ConfigGet('pr_name')."_imag_tmp_goods` SET `kol`=$kol, `summ`=$summ WHERE `id`=$ord");

            return;
        }
        function GoodinOrder($id)
        {
            $q = msr(msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `id`=$id"));

            return $q;
        }
        function SendOrder($order,$contacts=array(), $summ=0, $emails, $globnum, $type='', $comment='')
        {
            global $msc_diff;
            $retval = array();
            $tab='';
            $msg='';

            $menu=array();
            $qmenu=msq("SELECT * FROM `sushi_site_pmenu_menu_26`");
            while ($m = msr($qmenu))
            $menu[$m['id']]=$m['name'];

            if ($type=='')
            $q = msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=".$order);
            elseif ($type=='after')
            $q = msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_goods` where `order_id`=".$order);

            while ($r = msr($q))
            {
            	$qmenu_id=msr(msq("SELECT * FROM `sushi_site_pgood_good_27` WHERE `number`=".$r['good_num']));
                $menu_id=$qmenu_id['section_id'];

            	$tab.='<tr>
            		<td>'.$r['good_num'].'</td>
            		<td>'.$r['good_name'].'</td>
            		<td>'.$menu[$menu_id].'</td>
            		<td>'.$r['kol'].'</td>
            		<td>'.$r['price'].' руб.</td>
            		<td>'.$r['summ'].' руб.</td>
            	</tr>';
            }
            $time=msr(msq("SELECT TIMESTAMPADD(HOUR,".$msc_diff.",NOW()) as dt"));
            $MySqlConnect = new MySqlConnect;
            $tab='<strong><p>Время заказа: '.$MySqlConnect->dateTimeFromDB($time['dt']).'</p><p>Номер заказа: '.$globnum.'</p></strong><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
            	<tr>
            		<th>№ блюда</th>
            		<th>Наименование</th>
            		<th>Раздел</th>
            		<th>Количество</th>
            		<th>Цена</th>
            		<th>Сумма</th>
            	</tr>'.$tab.'</table><strong><p>ИТОГО: '.$summ.' руб.</p></strong><br/>';


             $contactstxt='';
   			 foreach ($contacts as $k=>$v)
   			 if ($v!='')
   			 {
              	$contactstxt.="<p>$k: $v</p>";
   			 }

             $msg=$tab.$contactstxt.$comment;
             $em_ar=explode('|', $emails);
   			 foreach ($em_ar as $em)
   			 {
   			 	if ($em!='')
				/*defaultEmail($em,$msg,'заказ № '.$globnum.' с autosushi-perm.ru','noreply@autosushi-perm.ru');*/
				sendsmtp($em,$msg,'заказ № '.$globnum.' с autosushi-perm.ru','sender@nadosushi.ru');
   			 }

/*             msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_order` WHERE `number`=".$order);
             msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_goods` WHERE `order`=".$order);*/
             return $retval;

        }
        function GetOrderInfoSum($order)
        {
            $q = msr(msq("SELECT sum(`summ`) as summ , sum(`kol`) as cnt FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order"));
            return $q;
        }
        function GetOrderInfoSum_nodrink($order)
        {
            $q = msr(msq("SELECT sum(`summ`) as summ , count(*) as cnt FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order
            and `good_num` not IN (SELECT `number` FROM `sushi_site_pgood_good_27` WHERE `section_id` in(13,14,18))"));
            return $q;
        }
        function GetOrderInfoSum_After($order)
        {
            $q = msr(msq("SELECT sum(`summ`) as summ , sum(`kol`) as cnt FROM `".ConfigGet('pr_name')."_imag_goods` where `order_id`=$order"));
            return $q;
        }
        function GetOrderGoods($order)
        {
            $retval = array();
            $q = msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order");
            while ($r = msr($q)) $retval[] = $r;
            return $retval;
        }
        function GetGroupOrderGoods($order)
        {
            $retval = array();
            $q = msq("SELECT *, sum(`kol`) as kol, sum(`summ`) as summ FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order GROUP BY `good_id`");
            while ($r = msr($q)) $retval[] = $r;
            return $retval;
        }
        function GetOrderGoods_After($order)
        {
            $retval = array();
            $q = msq("SELECT * FROM `".ConfigGet('pr_name')."_imag_goods` where `order_id`=$order");
            while ($r = msr($q)) $retval[] = $r;
            return $retval;
        }
        function GetGroupOrderGoods_After($order)
        {
            $retval = array();
            $q = msq("SELECT *, sum(`kol`) as kol, sum(`summ`) as summ FROM `".ConfigGet('pr_name')."_imag_goods` where `order_id`=$order GROUP BY `good_num`");
            while ($r = msr($q)) $retval[] = $r;
            return $retval;
        }
        function DelOrderGoods($id)
        {
            $q = msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `id`=$id");
            return true;
        }
        function DelGroupOrderGoods($good_id, $order)
        {
            $q = msq("DELETE FROM `".ConfigGet('pr_name')."_imag_tmp_goods` where `order_id`=$order and `good_id`=$good_id");
            return true;
        }
        function AddOrderGoods($order, $good_id, $kol, $half_type=0)
        {
            if ($order>0)
            {
            	$good = msr(msq("SELECT * FROM `".ConfigGet('pr_name')."_site_pgood_good_27` where `id`=$good_id"));

            	if ($half_type==1)  $good['price']=$good['half_price'];

            	if ($good['price']>0)
            	{                   msq("INSERT INTO `".ConfigGet('pr_name')."_imag_tmp_goods`
                   (`order_id`, `good_num`, `good_name`, `kol`, `price`, `summ`, `half_type`) VALUES
                   ('".$order."', '".$good['number']."', '".$good['name']."', '".$kol."', '".$good['price']."', '".($good['price']*$kol)."', '".$half_type."')");
            	}
            }
        }
        function start()
        {
          		$this->drawPubsList();
          		if (floor($_GET['delete'])>0) $this->deletePub($_GET['delete']);
        }
        function getList($page = 1){
                $retval = array();
                $count = msr(msq("SELECT COUNT(id) AS c FROM `".$this->getSetting('table')."`"));
                $count = floor($count['c']);
                $this->setSetting('count',$count);
                $page = floor($page);
                if ($page==-1) $this->setSetting('onpage',10000);
                if ($page<1) $page = 1;
                $this->setSetting('pagescount',ceil($count/$this->getSetting('onpage')));
                if ($page>$this->getSetting('pagescount')) $page = $this->getSetting('pagescount');
                $this->setSetting('page',$page);
                $q = msq("SELECT * FROM `".$this->getSetting('table')."` ORDER BY `id` desc LIMIT ".(($page-1)*$this->getSetting('onpage')).",".$this->getSetting('onpage'));
                while ($r = msr($q)) $retval[] = $r;
                return $retval;
        }
         function save($values){         	if (count($values>0))
          	{          		$set='';
          		foreach ($values as $k=>$v)
          		$set.=(($set!='') ? ', ' : '')."`".$k."`='".htmlspecialchars($v)."'";

          	    msq("INSERT INTO `".$this->getSetting('table')."` (`date`) VALUES (NOW())");
                $pub['id'] = mslastid();
                msq("UPDATE `".$this->getSetting('table')."` SET $set WHERE `id`='".$pub['id']."'");
          	}
        }
        function deletePub($id){
                $id = floor($id);
                global $CDDataSet;
                if ($r = msr(msq("SELECT * FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'")))
                {
                   msq("DELETE FROM `".$this->getSetting('table')."` WHERE `id`='".$id."'");
                   return true;
                }
                return false;
        }
        function Sendmail($emails,$values,$header){

   			 $txt='';
   			 foreach ($values as $k=>$v)
   			 {              	$txt.="$k: $v<br/>";
   			 }

   			 $em_ar=explode('|', $emails);
   			 foreach ($em_ar as $em)
   			 {   			 	if ($em!='' && count($values))
				defaultEmail($em,$txt,$header,'noreply@permplatok.ru');

   			 }

		}
        function drawPubsList()
        {
            global $SiteSections;
            $section = $SiteSections->get($this->getSetting('section'));
			$data=$this->getList($_GET['page']);
			$MySqlConnect = new MySqlConnect;
        	?>
        	<div id="content" class="forms">
                        <p>Всего записей: <?=$this->getSetting('count')?></p>

        		                <table class="table-content stat">
                                                <tr>
                                                        <th>Дата</th>
                                                        <th>Имя</th>
                                                        <th>Обращение</th>
                                                        <th>Контакты</th>
                                                        <!--//<th class="t_32width"></th>//-->
                                                </tr>
                                        <?
                                        foreach ($data as $pub){
                                                ?>
                                                <tr>
                                                       <td><?=$MySqlConnect->dateFromDBDot($pub['date'])?></td>
                                                       <td><?=$pub['name']?></td>
                                                       <td><?=$pub['comment']?></td>
                                                       <td><?=$pub['contacts']?></td>
<!--//						                               <td class="t_32width">
															<a href="./?section=<?=$section['id']?>&rubric=<?=$rubric['id']?>&delete=<?=$pub['id']?>" class="button txtstyle" onclick="if (!confirm('Удалить элемент')) return false;">
																<span class="bl"></span>
																<span class="bc"></span>
																<span class="br"></span>
																<input type="button" style="background-image: url(/pics/editor/delete.gif)" title="Удалить элемент" />
															</a>
														</td>//-->
                                                </tr>
                                                <?
                                        }
                                        ?>
                                        </table>


                                <span class="clear"></span>
                                <?if ($this->getSetting('pagescount')>1){
                                ?>
                                <div class="hr"><hr /></div>
                                <div id="paging" class="nopad">
                                        <?
                                        $href = '?section='.$section['id'];
                                        for ($i=1; $i<=$this->getSetting('pagescount'); $i++){
                                                $inner = '';
                                                $block = array('<a href="./'.$href.'&page='.$i.'" class="button">','</a>');
                                                if ($i==($this->getSetting('page')-5)){
                                                        $inner = ($i>1)?'<strong>&hellip;</strong>':$i;
                                                }
                                                if (($i>($this->getSetting('page')-5)) && ($i<($this->getSetting('page')+5))){
                                                        $inner = $i;
                                                        if ($i==$this->getSetting('page')) $block = array('<span class="button">','</span>');
                                                }
                                                if ($i==($this->getSetting('page')+5)){
                                                        $inner = ($i<$this->getSetting('pagescount'))?'<strong>&hellip;</strong>':$i;
                                                }
                                                if ($inner!='') print '
                                                '.$block[0].'
                                                        <span class="bl"></span>
                                                        <span class="bc">'.$inner.'</span>
                                                        <span class="br"></span>
                                                '.$block[1];
                                        }
                                        ?>
                                </div>
                                <?
                                }

         print '</div>';        }
        function delete(){
                return true;
        }

}
?>
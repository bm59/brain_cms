<?
$section_slider=$SiteSections->getByPattern('PSlider');
if ($section_slider['id']>0){
?>
<link rel="stylesheet" type="text/css" href="/css/jcarousel.basic.css"  media="all" />
<script type="text/javascript" src="/js/jquery.jcarousel.js"></script>
<script type="text/javascript" src="/js/jcarousel_basic.js"></script>
<div class="clear"></div>
<div class="slider">
		<!-- Wrapper -->
		<div class="wrapper">
		    <!-- Carousel -->
		    <div class="jcarousel">
		        <ul>
				<?
				$slider=getIface($SiteSections->getPath($section_slider['id']));
				
				$sliders=$slider->getList(0, ' WHERE `show`=1', 'ORDER BY `precedence`');
		        foreach ($sliders as $sl)
		        {         	
		            $lnk=array();
		            if ($sl['href']!='') $lnk=array('<a href="'.$sl['href'].'" '.(stripos($sl['href'],'http')!==false ? 'target="_blank"':'').'>', '</a>');
		
		            $image=$Storage->getFile($sl['image']);
		            
		            ?>
		           <li>
		            	<?=$lnk[0]?><img src="<?=$image['path']?>" alt="<?=$sl['name'] ?>"/><?=$lnk[1]?>
		           </li>
		          <?
		        }
		        ?>
		        </ul>
		    </div>
		
		    <!-- Pagination -->
		    <div class="jcarousel-pagination">
		        <!-- Pagination items will be generated in here -->
		    </div>
		    <a href="#" class="jcarousel-control-prev"><div class="container"><span class="bg"></span><span class="arrow"></span></div></a>
		    <a href="#" class="jcarousel-control-next"><div class="container"><span class="bg"></span><span class="arrow"></span></div></a>
		                
		</div>
</div>
<?} ?>

<link rel="stylesheet" type="text/css" href="/css/jcarousel.basic.css"  media="all" />
<script type="text/javascript" src="/js/jquery.jcarousel.js"></script>
<script type="text/javascript" src="/js/jcarousel_basic.js"></script>

<div class="slider">
		<!-- Wrapper -->
		<div class="wrapper">
		    <!-- Carousel -->
		    <div class="jcarousel">
		        <ul>
				<?
				$section_slider=$SiteSections->getByPattern('PSlider');
				$slider=getIface($SiteSections->getPath($section_slider['id']));
				
				$sliders=$slider->getList(0, ' WHERE `show`=1', 'ORDER BY `precedence`');
		        foreach ($sliders as $sl)
		        {         	
		            $lnk=array();
		            if ($sl['href']!='') $lnk=array('<a href="'.$sl['href'].'" '.(stripos($sl['href'],'http')!==false ? 'target="_blank"':'').'>', '</a>');
		
		            $image=$Storage->getFile($sl['image']);
		            
		            ?>
		           <li>
		           		<div class="bg-container1">
		            		<?=$lnk[0]?><img src="<?=$image['path']?>" alt="<?=$sl['name'] ?>"/><?=$lnk[1]?>
		            	</div>
		           </li>
		          <?
		        }
		        ?>
		        </ul>
		    </div>

			  <!-- Pagination -->
			  <div class="jcarousel-pagination"></div> 
			 
			  <a href="#" class="jcarousel-control-prev"></a>
			  <a href="#" class="jcarousel-control-next"></a> 
			         
		</div>
</div>

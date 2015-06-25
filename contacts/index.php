<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/include.php");?>
<?

	$SiteSettings = new SiteSettings;
	$SiteSettings->init();

	$Section = $SiteSections->get($SiteSections->getIdByPath('/sitecontent'.configGet("AskUrl")));

	$Section['id'] = floor($Section['id']);
	if ($Section['id']>0)
	{
		$Pattern = new $Section['pattern'];
		$Iface = $Pattern->init(array('section'=>$Section['id']));
	}

	if ($Section['description']!='') $headerh1=$Section['description'];
	else $headerh1=$Section['name'];

	$nav_text='<div>'.$Section['name'].'</div>';


?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/meta.php");?>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/header.php");?>


<div class="content_container">
<div class="mininav"><a href="/">�������</a><img src="/pics/nav_arrow.png"><?=$nav_text?></div>
<H1><?=$headerh1?></H1>
<?
	$sheet = $Iface->get();
	print $sheet['text'];
?>
<?
						$map_address=$SiteSettings->getOne($SiteSettings->getIdByName('map_address'));

						if ($map_address['value']!='')
						{
							?>
							<div class="clear"></div>
                              <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript">
        // ��� ������ ����� �������� API � ����� DOM, ��������� �������������
        ymaps.ready(init);

        function init () {
            var myMap = new ymaps.Map("map", {
                    center: [58.0000, 56.2111],
                    zoom: 18,
                    controls: ['smallMapDefaultSet'],
                    behaviors: ['drag']
                });

            var myGeocoder = ymaps.geocode('<?=$map_address['value']?>', {results: 1});
			myGeocoder.then(
			    function (res)
			    {
		            // �������� ������ ��������� ��������������.
		            var firstGeoObject = res.geoObjects.get(0),
		                // ���������� ����������.
		                coords = firstGeoObject.geometry.getCoordinates(),
		                // ������� ��������� ����������.
		                bounds = firstGeoObject.properties.get('boundedBy');

		            // ��������� ������ ��������� ��������� �� �����.
		            myMap.geoObjects.add(firstGeoObject);
		                      myMap.zoomRange.get(coords).then(function (range) {
       myMap.setCenter(coords, 15);
    });
		            // ������������ ����� �� ������� ��������� ����������.
		            //myMap.setBounds(bounds, {
		            //    checkZoomRange: true // ��������� ������� ������ �� ������ ��������.
		            //});
			    }
			);
		}

    </script>

							<div id="map" style="height: 300px; border: 3px solid #CCCCCC;margin-top: 20px;"></div>

							<?
						}
					?>
</div>
<?include_once($_SERVER['DOCUMENT_ROOT']."/inc/site/footer.php");?>
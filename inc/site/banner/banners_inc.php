<?
$banner_iface=getiface('/products/banners/');
$banner_Section = $SiteSections->get($SiteSections->getIdByPath('/products/banners/'));

$place_iface=getiface('/products/'.$banner_Section['path'].'/places/');
$place_Section = $SiteSections->get($SiteSections->getIdByPath('/products/'.$banner_Section['path'].'/places/'));

?>
<script>
var session_id = '<?php echo session_id(); ?>';
$(function() {
	$(".banner_code, .inner_href").click(function() {
		
		if ($(this).attr('id')>0)
		$.ajax({
            type: "POST",
            url: "/redirect.php",
            data: "action=add_click&banner_id="+$(this).attr('id')+"&session_id="+session_id,
            dataType: 'json'
        });	
	});

	$(".built_href").click(function() {
		return false;
	
	});
});
</script>
<?

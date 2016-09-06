 <script type="text/javascript">
 $(function() {

	 function showSize(){
		 var info='';

		 info+='$(window).width='+$(window).width()+'<br/>';
		 info+='$(window).height='+$(window).height()+'<br/>';
		 $('.adapt_info').html(info);
	 }

	 $(window).on('resize', function(){
		 showSize();
	 });
	 
	 showSize();
	
});
 </script>	
 <div class="adapt_info" style="line-height:35px; position: fixed; bottom: 40px; right: 40px; background-color: #FFFFFF; border: 1px solid #CCCCCC; padding: 20px; z-index: 999999999999">
&nbsp;
 </div>	
	
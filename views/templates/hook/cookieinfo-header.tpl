{*
*  @author Vicente García <v@vicentegarcia.com>
*  @copyright  Copyright (c) 2014, Vicente García
*  @version    1.2
*}
{literal}
<script type="text/javascript">
$(function()
{
	$.getJSON('/modules/cookieinfo/texts.php',{ajax: 'true', cookieinfo_token: '{/literal}{$cookieinfo_token|escape:'htmlall':'UTF-8'}{literal}'})
	.done (function(data){
		$( 'body' ).prepend( '<div class="wrapperCookie"><div class="inner"><div class="textCookie"><p><strong>'+ data.title +'</strong></p><p>'+ data.text +' - <a  id="cookieinline" href="#cookiedata">'+ data.linkpolicy +'</a></p></div></div></div>' );
		$( 'body' ).prepend( '<div style="display:none"><div id="cookiedata"><div style="width: 650px; height: 425px; padding: 30px;">'+ data.policy +'</div></div></div>' );
	});

	$("a#cookieinline").livequery(function(){
	    $(this).fancybox();
	});
});
</script>
{/literal}
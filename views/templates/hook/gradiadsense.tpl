{if $check_activo == 1}
<style>


</style>
<div id="gradiAdsense">

	<div class="container">
		<div class="left">
			<a href="{$cta_url}">
	    		<img src="./modules/gradiadsense/views/img/{$imagen}" width="100%" heigth="auto"></img>
	    	</a>
		</div>
		<div class="right">
			<br><br>
			<h2>{$texto|escape:'html':'UTF-8'}</h2>
			<br>
	    	<p class="lead text-muted">{$descripcion|escape:'html':'UTF-8'}<p>
    	</div>
	</div>
</div>
{/if}
<?php
//print $_SERVER['HTTP_REFERER'];
if ($_GET['lang']=='es') $_SESSION['select_idioma'] = 'es';
else if ($_GET['lang']=='vl') $_SESSION['select_idioma'] = 'va';
$idioma = $_SESSION['select_idioma'];
if ($idioma == 'es')
{
	$array_meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
}
else if ($idioma == 'en')
{
	$array_meses = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
}
else if ($idioma == 'va')
{
	$array_meses = array("Gen","Feb","Mar","Abr","Mai","Jun","Jul","Ago","Sep","Oct","Nov","Des");
}
//print '<li><a href="https://www.masymas.com"><i class="fa fa-home"></i> <span class="nav-label">Ir a web</span></a></li>';
print '<li';
if ($seccion == "dashboard") print ' class="active"';
print '>';
	print '<a id="dashboard" href="dashboard.php"><i class="fa fa-home"></i> <span class="nav-label" tkey="Inicio">Inicio</span></a>';
print '</li>';
print '<li';
if ($seccion == "perfil") print ' class="active"';
print '>';
	print '<a id="perfil" href="perfil.php"><img class="menu_izquierda" src="img/svg/menu/m_ajustes.svg" alt="Perfil"> <span class="nav-label" tkey="Perfil">Perfil</span></a>';
print '</li>';
print '<li';
if ($seccion == "compras") print ' class="active"';
print '>';
	print '<a href="compras.php"><img class="menu_izquierda" src="img/svg/shopping_cart.svg" alt="app movil"> <span class="nav-label" tkey="Historico1">Hist&oacute;rico</span></a>';
print '</li>';
print '<li';
if ($seccion == "ahorro") print ' class="active"';
print '>';
	print '<a href="ahorro.php"><img class="menu_izquierda" src="img/svg/menu/m_ahorro.svg" alt="Mi ChequeAhorro"> <span class="nav-label" tkey="MiChequeAhorro">Mi ChequeAhorro</span></a>';
print '</li>';
print '<li';
if ($seccion == "megacupon") print ' class="active"';
print '>';
	print '<a href="megacupones.php"><img class="menu_izquierda" src="img/svg/tarjeta/multicupon.svg" alt="Multicupón"> <span class="nav-label" tkey="Cupones">Cupones</span></a>';
print '</li>';
print '<li';
if ($seccion == "folleto") print ' class="active"';
print '>';
	print '<a href="folletos.php"><img class="menu_izquierda" src="img/svg/portada/folleto2.svg" alt="Folletos"> <span class="nav-label" tkey="cab_folletos_vista">Folletos</span></a>';
print '</li>';
/*
print '<li';
if ($seccion == "catalogos") print ' class="active"';
print '>';
	print '<a href="catalogos.php"><img class="menu_izquierda" src="img/svg/portada/folleto2.svg" alt="Folletos"> <span class="nav-label">Folletos</span></a>';
print '</li>';
*/

?>
<script>
	var temp_lang = {};
	var id_home = <?=$_SESSION['id_home']?>;
 	function obtener_mensajes(todas,vistas)
    {
		var mensajes_no_leidos = 0;
		/*<?php
		//if (isset($_GET['mensajes']) && $_GET['mensajes']!='')
		//{
		?>
			console.log('1');
			mensajes_no_leidos = <?php //echo $_GET['mensajes']; ?>;
			$('.notif').html(mensajes_no_leidos);
				$('#perfil').attr('href', 'perfil.php?mesajes='+ mensajes_no_leidos);
				$('#dashboard').attr('href', 'dashboard.php?mesajes='+ mensajes_no_leidos);
				if (mensajes_no_leidos>0) 
				{
					$('.notif').show();
					//window.FirebasePlugin.setBadgeNumber(mensajes_no_leidos);
				}
				else $('.notif').hide();
		<?php	
		//}
		//else 
		//{		
		?>*/
		console.log('2');
	    $.ajax({
        type:'GET',
        timeout: 20000,
        dataType: 'json',
        url:'https://www.appfornes.es/servicios-web/obtener_mensajes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&vistas='+vistas+'&idioma=es&anticache='+(new Date()).getTime(),
        success:function(res, textStatus, XMLHttpRequest)
        {
        	
        	html_result = '';
        	if (res.validacion == 'ok')
        	{				
				$.each(res.mensajes, function(key, value) 
				{
					if (value.leido==0) 
					{
						mensajes_no_leidos++;
					}
				});								
				$('.notif').html(mensajes_no_leidos);
				//$('#perfil').attr('href', 'perfil.php?mensajes='+ mensajes_no_leidos);
				//$('#dashboard').attr('href', 'dashboard.php?mensajes='+ mensajes_no_leidos);
				if (mensajes_no_leidos>0) 
				{
					$('.notif').show();
					//window.FirebasePlugin.setBadgeNumber(mensajes_no_leidos);
				}
				else $('.notif').hide();
				//else window.FirebasePlugin.setBadgeNumber(0)
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
		});
		/*<?php
		//}
		?>*/
	}
	$(document).ready(function(){
		var idioma = <?php echo "'".$idioma."'"; ?> ;
		
		console.log('idioma', idioma);
		if (idioma!=''&& idioma!='es')
		{
			if (idioma=='va') 
			{
				translate(lang_va);
				temp_lang = lang_va;
				idioma = 'va';				
			}
			else if (idioma=='en') 
			{
				translate(lang_en);
				temp_lang = lang_en;
				idioma = 'en';
			}
			else 
			{
				translate(lang_es);
				temp_lang = lang_es;
				idioma = 'es';
			}						
		}
		else 
		{
			translate(lang_es);
			temp_lang = lang_es;
			idioma = 'es';			
		}
		obtener_mensajes(0, 1);
 	});
</script>

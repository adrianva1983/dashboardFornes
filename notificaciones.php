<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/conexion.php");
//Comprobamos el acceso
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/aut_verifica.inc.php");
if (!isset($_SESSION['cod_cliente'])||$_SESSION['cod_cliente']=='')
{
	header ("Location: $redir?error_login=5");
	exit;
}
/*
if ($_SERVER['HTTP_REFERER'] == "")
{
	Header ("Location: index.php?error_login=1");
	die ("Error cod.:1 - Acceso incorrecto!");
	exit;
}
*/
function url_exists($url)
{		
	$ch = @curl_init($url);
	@curl_setopt($ch, CURLOPT_HEADER, TRUE);
	@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$status = array();
	preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);	
	return ($status[1] == 200);
}
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notificaciones masymas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	
	<!-- FooTable -->
    <link href="css/plugins/footable/footable.core.css" rel="stylesheet">
	<!-- Sweet Alert -->
    <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
	<link href="css/plugins/select2/select2.min.css" rel="stylesheet">
	
	<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<?php
	require "herramientas/favicon.php";
	?>

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	
	<!-- Chartjs -->
	<script src="js/plugins/chartJs/Chart.min.js"></script>
	<!-- FooTable -->
    <script src="js/plugins/footable/footable.all.min.js"></script>
	<!-- Sweet alert -->
	<script src="js/plugins/sweetalert/sweetalert.min.js"></script>
	<!-- Select2 -->
    <script src="js/plugins/select2/select2.full.min.js"></script>	
    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- Data picker -->
    <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
	<script src="js/plugins/pace/pace.min.js"></script>
	<script src="js/app.js" type="text/javascript"></script>
	
</head>

<body class="no-skin-config seccion_notificaciones">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> 
                            <span>
                                <img alt="image" src="/Plantillas/Imagenes/logo.svg" />
                            </span>
                        </div>
                        <div class="logo-element">
                            +
                        </div>
                    </li>
				<?php
					//$seccion = "compras";
					require($_SERVER['DOCUMENT_ROOT']."/dashboard/herramientas/menu.php");
				?>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
		<?php
		require($_SERVER['DOCUMENT_ROOT']."/dashboard/herramientas/menu-top.php");		
		?>

                </nav>
            </div>
            <div class="unidad_familiar_perfil" style="display: none">
	            <h2 tkey="cab_notificaciones">Notificaciones</h2>
            </div>
            <div class="container-fluid top sin_padd_r">
				<div class="row listas" id="cont_lista1">
					<div id="lista1" class="container-fluid" ></div>
				</div>
			</div>
            <div class="footer">
				<div class="pull-right">
					<strong tkey="masymas">masymas</strong>
				</div>
				<div>
					<strong>Copyright</strong> <a href="http://www.semillaproyectos.com" tkey="Supermercados_masymas">Semilla Proyectos Internet &copy;</a>
				</div>
        	</div>	
        </div>
        
	</div>	
	<script src="/dashboard/lang/lang.js" type="text/javascript"></script>
	<script src="/dashboard/lang/es.js" type="text/javascript"></script>
	<script src="/dashboard/lang/en.js" type="text/javascript"></script>
	<script src="/dashboard/lang/va.js" type="text/javascript"></script>
    <script>
	var cupon_resaltar = '';
    function obtener_mensajes(todas,vistas)
    {
		console.log('id_card',<?=$_SESSION['id_card']?>);
	    $.ajax({
        type:'GET',
        timeout: 20000,
        dataType: 'json',
        url:'https://www.appfornes.es/servicios-web/obtener_mensajes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&vistas='+vistas+'&idioma=<?=$idioma?>&anticache='+(new Date()).getTime(),
        success:function(res, textStatus, XMLHttpRequest)
        {
			console.log('33333333333333', res, <?=$_SESSION['id_home']?>, <?=$_SESSION['id_card']?>, vistas, todas);
        	var mensajes_no_leidos = 0;
        	html_result = '';
        	if (res.validacion == 'ok')
        	{				
				$.each(res.mensajes, function(key, value) 
				{
					if (value.leido==0) 
					{
						mensajes_no_leidos++;
					}
					if (1==1||value.leido==0||todas==1)
					{
						html_result += '<div class="row lista_prod" id="mensaje_'+value.id+'_'+value.cod_barras+'"><div class="col-xs-1 nuevo">';
						if (value.leido==0) html_result+= '<i class="fa fa-circle"></i>';
						html_result += '</div>';
						html_result += '<div class="col-xs-8 prod_data"><p href="folletos.php" class="prod_env"';
						var tmp = value.id.split('-');
						if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\''+value.cod_barras+'\',\'pagina-folletos\');return false;"';
						else if (tmp[0]=='cupon') html_result+=' onclick="cupon_resaltar=\''+value.cod_barras+'\';marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\''+value.cod_barras+'\',\'pagina-cupones\');return false;"';
						else if (tmp[0]=='multicupon') 
						{
							html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\''+value.cod_barras+'\',\'pagina-cupones\');';
							if (value.siguiente==1) html_result+='$(\'.pagina-multi-cupon .grup_cupones\').hide();$(\'.pagina-multi-cupon .grup_cupones_siguiente\').fadeIn();';
							else html_result+='$(\'.pagina-multi-cupon .grup_cupones_siguiente\').hide();$(\'.pagina-multi-cupon .grup_cupones\').fadeIn();';
							html_result+='return false;"';
						}
						else if (tmp[0]=='chequeahorro') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\''+value.cod_barras+'\',\'pagina-cupones\');return false;"';
						else if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\''+value.cod_barras+'\',\'pagina-folletos\');return false;"';
						else html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');"';
						html_result +='>'+value.texto+'</p></div><div class="col-xs-3 prod_activo">';
						//html_result +='<input type="checkbox" class="styleMe" id="input_'+value.id+'"/>';
						if (value.tipo!=''&&value.tipo!=undefined) html_result+=value.tipo;						
						html_result+='</div>';
						html_result+='<a href="#" class="btn btn-primary borrar_mensaje" onclick="marcar_mensaje_borrado(\''+value.id+'\',\''+value.cod_barras+'\');$(\'#mensaje_'+value.id+'\').fadeOut();return false;"><i class="fa fa-trash"></i></a></div>';
                        //html_result+='</div>';
                        //html_result+='<hr/>';					
					}
				});								
				$('.notif').html(mensajes_no_leidos);
				$('.unidad_familiar_perfil').show();
				if (mensajes_no_leidos>0) 
				{
					$('.notif').show();
					//window.FirebasePlugin.setBadgeNumber(mensajes_no_leidos);
				}
				else $('.notif').hide();
				//else window.FirebasePlugin.setBadgeNumber(0);
				if (html_result!='')
				{					
					$('#lista1').html(html_result);
					/*$('.styleMe').iCheck({
						checkboxClass: 'icheckbox_square-green',
						radioClass: 'iradio_minimal',
						labelHover: false,
						cursor: true
					});*/
					$('.styleMe').on('ifChecked', function(event){
						var pulsado = $(this).attr("id").split('_');
						marcar_mensaje_leido(pulsado[1],pulsado[2]);
						$('#mensaje_'+pulsado[1]).fadeOut();
					});
				}
				else
				{
					$('#lista1').html('<div class="row lista_prod"><div class="col-xs-8 col-xs-offset-1 prod_data"><p class="prod_env">No tiene ningún mensaje nuevo.</p></div><div class="col-xs-2 prod_activo">&nbsp;</div></div>');					
				}		
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
		});
	}
	function marcar_mensaje_borrado(id_mensaje,cod_barras)
	{
		if ($('#mensaje_'+id_mensaje+' .nuevo').html()!='')
		{
			$('.notif').html(parseInt($('.notif').html())-1);
			if (parseInt($('.notif').html())<=0) $('.notif').hide();
		}
		$('#mensaje_'+id_mensaje+'_'+cod_barras).fadeOut();
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/marcar_mensaje_borrado.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&id_mensaje='+id_mensaje+'&cod_barras='+cod_barras+'&idioma=es&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});		
	}
	function marcar_mensaje_leido(id_mensaje,cod_barras)
	{
		$('.notif').html(parseInt($('.notif').html())-1);
		if (parseInt($('.notif').html())<=0) $('.notif').hide();
		$('#mensaje_'+id_mensaje+'_'+cod_barras+' .nuevo i').fadeOut();	
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/marcar_mensaje_leido.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&id_mensaje='+id_mensaje+'&cod_barras='+cod_barras+'&idioma=es&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				if (res.es_multicupon==1)
				{				
					if ($('.js-switch_'+cod_barras).is(':checked'))
					{
					}
					else				
					{								
						$('.js-switch_'+cod_barras).trigger('click');						
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});		
	}
    $(document).ready(function(){
        obtener_mensajes(0, 1);
    });
    </script>           	
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
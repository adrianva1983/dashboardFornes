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

    <title tkey="Ahorro_masymas">Ahorro masymas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	
	<!-- FooTable -->
    <link href="css/plugins/footable/footable.core.css" rel="stylesheet">
	<!-- Sweet Alert -->
    <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
	<link href="css/plugins/select2/select2.min.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	
	<!-- FooTable -->
    <script src="js/plugins/footable/footable.all.min.js"></script>
	<!-- Sweet alert -->
	<script src="js/plugins/sweetalert/sweetalert.min.js"></script>
	<!-- Select2 -->
    <script src="js/plugins/select2/select2.full.min.js"></script>	
	
	<!-- ChartJS-->
    <script src="js/plugins/chartJs/Chart.min.js"></script>


    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
	<?php
	require "herramientas/favicon.php";
	?>
</head>

<body class="no-skin-config">
    <div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <img alt="image" src="/Plantillas/Imagenes/logo.svg" />
                             </span>                        
                    </div>
                    <div class="logo-element">
                        +
                    </div>
                </li>
				<?php
					$seccion = "ahorro";
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
			<!--<div class="wrapper wrapper-content wrapper-content-cupones animated fadeInRight"></div>-->
            <div class="wrapper wrapper-content wrapper-content-ahorro animated fadeInRight pagina-ahorro">
            </div>
			<!--<div class="pagina pagina-ahorro">
				<div class="container-fluid top">
					<div class="bloques_historicos_chequeahorro">
					</div>
				</div>
			</div>-->
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
	function mostrar_tickets(indice)
	{
		window.location = 'https://tarjeta.masymas.com/dashboard/compras.php?mes_previo='+indice+'&solapa=tickets';
	}
	function obtener_chequeahorro()	
	{
		$.ajax({
			type:'GET',
			timeout: 3000,
			dataType: 'json',
			url:'https://tarjeta.masymas.com/servicios-web/obtener_chequeahorro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?php print $_SESSION['id_home'];?>&cod_cliente=<?php print $_SESSION['cod_cliente'];?>&idioma=<?=$idioma?>&dos_meses=1&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				console.log('res1', res);
				var resultado_ajax ='';
				if (res.validacion=='ok')
				{
					localStorage.setItem('puntos_cheque',res.puntos);
					localStorage.setItem('fecha_cheque',res.fecha);
					$('.fecha_cheque').html(res.fecha);
					var ahorro_total_actual = parseFloat(res.ahorro_total_actual);
					var ahorro_total_anterior = parseFloat(res.ahorro_total_anterior);
					if (ahorro_total_actual>0)
					{
						$('.ahorrar').hide();
						$('.puntos_cheque').show();
						$('.puntos_cheque').html(ahorro_total_actual.toFixed(2).replace('.',',')+'<span>&euro;</span>');
					}
					else if (ahorro_total_anterior<=0)
					{
						$('.puntos_cheque').hide();
						$('.ahorrar').show();
					}
					if (ahorro_total_anterior>0)
					{
						$('#acum .saldo').html(ahorro_total_anterior.toFixed(2).replace('.',',')+' &euro;');
						$('#acum').show();
						$('.puntos_cheque').addClass('precio_mas_pequeno');
					}
					var html_result = '';
					var total_acumulado = 0;
					var ahorro_total_acumulado_pendiente = 0;										
					var date_actual = new Date();
					var algun_otros = false;
					ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
					$.each(res.ahorro_historico, function(key, value) 
					{	
						if (parseFloat(value.AhorroOtros).toFixed(2)>0) algun_otros = true;
						if ((parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual) + parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior))>=0)
						{
							var total_mes = (key == 0? (parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual)): (parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior)));
							var porcentaje_cheque = (total_mes * 35)/2;
							var quedan = total_mes - 1;
							if (porcentaje_cheque>100) porcentaje_cheque = 100;
							html_result+='<div class="row compras"><div class="col-xs-12';
							if (quedan<0) html_result+=' no_alcanzado';
							else if (res.ahorro_fecha_redencion!='') html_result+=' no_alcanzado'; //lo canjeado también lo sacamos en gris
							if ((parseInt(value.mes)!=parseInt(date_actual.getMonth() +1)||parseInt(value.ano)!=parseInt(date_actual.getFullYear()))&&(key == 1))
							{
								html_result+=' mes_anterior_ahorro';
							}								
							html_result+='"><h4 class="more_left">'+(key == 0 ? res.fecha_actual : res.fecha_anterior)+'</h4>';	
							html_result+= '<span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span>';													
							html_result+='<p id="mostrar_tickets" class="more_right" onclick="mostrar_tickets('+key+');">'+temp_lang["ver_tickets"]+' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p>';
							if (quedan<0 && key == 0)
							{
								var fecha_actual = new Date();
								var time_actual = fecha_actual.getTime();							
								var ultimo_dia_mes = new Date(fecha_actual.getFullYear(),fecha_actual.getMonth()+1,0);
								var time_comparacion = ultimo_dia_mes.getTime()-(7*24*60*60*1000);

								if (time_comparacion<=time_actual) html_result+='<p class="more_right">'+temp_lang["Va_caducar"]+'</p>';
								if (quedan.toFixed(2)<=-1 && key == 0) html_result+='<p class="more_right">'+temp_lang["empieza_acumular"]+'</p>';
							}
							else if (key == 0)
							{
								html_result+='<p class="more_right">'+temp_lang["sique_acumulando"]+'</p>';
							}
							if (quedan<0 && key == 1)
							{
								html_result+='<p class="more_right">'+temp_lang["no_llegaste"]+'</p>';
							}
							else if (key == 1) 
							{
								console.log('key', res);
								if (res.ahorro_fecha_caducidad!='' && res.ahorro_fecha_redencion == '') html_result+='<p class="more_right">'+temp_lang["cheque_canjeable"]+' '+res.ahorro_fecha_caducidad+'</p>';
								else if (key == 0) html_result+='<p class="more_right">'+temp_lang["disponible_cheque_apartir"]+'</p>';
							}
							if (res.ahorro_fecha_redencion!='' && key == 1) html_result+='<p class="more_right">'+temp_lang["cheque_canjeado"]+' '+res.ahorro_fecha_redencion+'</p>';
							if (1 - res.ahorro_total_actual > 0 && key == 0 && res.ahorro_total_actual != 0) html_result+='<p class="more_right">'+temp_lang["faltan_delante"]+' '+(Math.round((1 - res.ahorro_total_actual) * 100)/100)+'&euro; '+temp_lang["faltan_detras"]+'</p>';
							if (parseInt(value.mes)==parseInt(date_actual.getMonth() +1)&&parseInt(value.ano)==parseInt(date_actual.getFullYear())) 
							{
								html_result+='<p class="more_right">'+temp_lang["Acumulado"]+' '+res.fecha+'</p>';
							}
							html_result+='</div></div>';						
							if (quedan<0) 
							{
								//html_result+='<div class="row leyenda"><div class="col-xs-2 anyo"><p></p></div><div class="col-xs-7 mes"><p>Para conseguir tu cheque ahorro</p> </div>';
								//html_result+='<div class="col-xs-3 ahorro"><p>Quedan</p> </div></div>';
								//html_result+='<div class="row leyenda hist_datos totales"><div class="col-xs-8 col-xs-offset-1 anyo gasto"><p style="width: '+porcentaje_cheque+'%;background: #333;color:#FFF;">'+total_mes.toFixed(2).replace('.',',')+'&euro;</p> </div>';
								//html_result+='<div class="col-xs-2 ahorro cifra_ahorro"><p>'+quedan.toFixed(2).replace('.',',')+'&euro;</p> </div>';
							}
							else 
							{
								//html_result+='<div class="row leyenda"><div class="col-xs-2 anyo"><p></p></div><div class="col-xs-7 mes"><p></p> </div>';
								//html_result+='<div class="col-xs-3 ahorro"><p></p> </div></div>';
								//html_result+='<div class="row leyenda hist_datos totales"><div class="col-xs-10 col-xs-offset-1 anyo gasto" style="border-top-right-radius: 46px;border-bottom-right-radius: 46px;"><p style="width: '+porcentaje_cheque+'%">'+total_mes.toFixed(2).replace('.',',')+'&euro;</p> </div>';							
							}												
							//html_result+='<div class="marcador_gasto"><div class="linea_vertical_verde"></div><p class="marcador_cantidad"></p> </div></div>';
							html_result+='<div class="row subtotales';
							if (quedan<0) html_result+=' no_alcanzado';
							else if (res.ahorro_fecha_redencion!='') html_result+=' no_alcanzado'; //lo canjeado también lo sacamos en gris
							html_result+='"><div class="col-md-3 col-xs-12 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>'+temp_lang["1detuscompras"]+'</h5><p>'+(key == 0? parseFloat(res.ahorro_compra_actual).toFixed(2).replace('.',','):parseFloat(res.ahorro_compra_anterior).toFixed(2).replace('.',','))+' <span>&euro;</span></p><span class="simbolo-separacion-ahorro pull-right">+</span></div>';
							html_result+='<div class="col-md-3 col-xs-12 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>'+temp_lang["ProductosChequeAhorro"]+'</h5><p>'+(key == 0? parseFloat(res.ahorro_productos_actual).toFixed(2).replace('.',','):parseFloat(res.ahorro_productos_anterior).toFixed(2).replace('.',','))+' <span>&euro;</span></p><span class="simbolo-separacion-ahorro pull-right">+</span></div>';
							html_result+='<div class="col-md-3 col-xs-12 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang["Otros"]+'</h5> <p>'+(key == 0? parseFloat(res.ahorro_otros_actual).toFixed(2).replace('.',','):parseFloat(res.ahorro_otros_anterior).toFixed(2).replace('.',','))+' <span>&euro;</span></p> <span class="simbolo-separacion-ahorro pull-right">=</span></div>';
							html_result+='<div class="col-md-3 col-xs-12 subtotal" ><img style="width:0px;height:57px;" class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang["TotalMes"]+'</h5> <p>'+(key == 0? (parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual)).toFixed(2).replace('.',','): (parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior)).toFixed(2).replace('.',','))+' <span>&euro;</span></p> </div></div>';
							total_acumulado+=(key == 0? (parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual)): (parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior)));
							if (key==1&&quedan<0) {}
							else if (key==1&&res.ahorro_fecha_redencion!='') {}
							else if (key==1)						
							{
								ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
							}
						}
					});					
					if (!algun_otros)
					{
						$('.otros').fadeOut();
					}
					if (total_acumulado>0)
					{
						html_result+='<div class="row ahorro_total"><div class="col-xs-12" ><img class="responsive" src="img/svg/ahorro/ahorro_total.svg"><p class="big_tot">'+total_acumulado.toFixed(2).replace('.',',')+' <span>&euro;</span></p> </div></div>';
					}
					if (html_result!='') $('.wrapper-content-ahorro').append(html_result);

				}
				else
				{
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}	
	/*var temp_lang = {};
	function obtener_chequeahorro3()
	{
	$.ajax({
		type:'GET',
		timeout: 3000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_chequeahorro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?php print $_SESSION['id_home'];?>&cod_cliente=<?php print $_SESSION['cod_cliente'];?>&idioma=es&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('mirar a aca', res);
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				$('.fecha_cheque').html(res.fecha);
				var ahorro_total_actual = parseFloat(res.ahorro_total_actual);
				var ahorro_total_anterior = parseFloat(res.ahorro_total_anterior);
				var ahorro_canjeable = parseFloat(res.home_canjeable);
				var ahorro_acumulado = parseFloat(res.home_acumulado);
				var ahorro_quedan = parseFloat(res.home_no_perder);
				var fecha_acumulado = res.fecha;
				pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado);
				
				var html_result = '';
				var html_result_mostrar = '';
				var total_acumulado = 0;
				var total_acumulado_2meses = 0;
				var num_meses = 0;
				var ahorro_total_acumulado_pendiente = 0;
				$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').html('');
				//Historico actual y anterior
				//Historico actual
				html_result+='<div class="row compras titulo_ahorro_historico';
				var total_mes = 0;
				total_mes = parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				var quedan = total_mes - 1;
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_actual+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(0);">ver_tickets<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';
				if (quedan<0)
				{
					var fecha_actual = new Date();
					var time_actual = fecha_actual.getTime();							
					var ultimo_dia_mes = new Date(fecha_actual.getFullYear(),fecha_actual.getMonth()+1,0);
					var time_comparacion = ultimo_dia_mes.getTime()-(7*24*60*60*1000);
					
					html_result+='<div class="col-xs-12"><div class="explicacion_cheque';					
					if (time_comparacion<=time_actual) html_result+=' va_a_caducar';							
					if (quedan.toFixed(2)<=-1) html_result+='">empieza_acumular</div></div>';
					else html_result+='">faltan_delante '+(-quedan.toFixed(2))+'&euro; faltan_detras</div></div>';
				}
				else html_result+='<div class="col-xs-12"><div class="explicacion_cheque">sique_acumulando</div></div>';
				html_result+='</div>';
				html_result+='<div class="row subtotales';
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>1detuscompras</h5><p>'+parseFloat(res.ahorro_compra_actual).toFixed(2).replace('.',',')+' <span>?</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>ProductosChequeAhorro</h5><p>'+parseFloat(res.ahorro_productos_actual).toFixed(2).replace('.',',')+' <span>?</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>Otros</h5> <p>'+parseFloat(res.ahorro_otros_actual).toFixed(2).replace('.',',')+' <span>?</span></p> </div></div>';
				total_acumulado+=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				total_acumulado_2meses +=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				//Historico anterior
				html_result+='<div class="row compras titulo_ahorro_historico';
				var total_mes = 0;
				total_mes = parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				var quedan = total_mes - 1;
				if (quedan<0) html_result+=' no_alcanzado';
				else if (res.ahorro_fecha_redencion!='') html_result+=' no_alcanzado'; //lo canjeado también lo sacamos en gris
				html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_anterior+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(1);">ver_tickets <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';						
				if (quedan<0) html_result+='<div class="col-xs-12"><div class="explicacion_cheque">no_llegaste</div></div>';						
				else if (res.ahorro_fecha_redencion!='') html_result+='<div class="col-xs-12"><div class="explicacion_cheque">Canjeado '+res.ahorro_fecha_redencion+'</div></div>';
				else 
				{
					html_result+='<div class="col-xs-12"><div class="explicacion_cheque';
					var fecha_actual = new Date();
					var time_actual = fecha_actual.getTime();							
					var array_caducidad = res.ahorro_fecha_caducidad.split('/');
					var time_caducidad = new Date(array_caducidad[2],array_caducidad[1],array_caducidad[0]);
					var time_comparacion = time_caducidad-(7*24*60*60*1000);
					
					if (time_actual>=time_comparacion) html_result+=' va_a_caducar';
					html_result+='">';
					if (res.ahorro_fecha_caducidad!='') html_result+='cheque_canjeable '+res.ahorro_fecha_caducidad;
					else html_result+=disponible_cheque_apartir;
					html_result+='</div></div>';				
					ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				}
				html_result+='</div>';
				html_result+='<div class="row subtotales';
				if (quedan<0) html_result+=' no_alcanzado';
				else if (res.ahorro_fecha_redencion!='') html_result+=' no_alcanzado'; //lo canjeado también lo sacamos en gris
				html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5 tkey="1detuscompras">1detuscompras</h5><p>'+parseFloat(res.ahorro_compra_anterior).toFixed(2).replace('.',',')+' <span>?</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>ProductosChequeAhorro</h5><p>'+parseFloat(res.ahorro_productos_anterior).toFixed(2).replace('.',',')+' <span>?</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>Otros</h5> <p>'+parseFloat(res.ahorro_otros_anterior).toFixed(2).replace('.',',')+' <span>?</span></p> </div></div>';
				total_acumulado+=parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				total_acumulado_2meses +=parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				html_result_mostrar = html_result;						
				data_ahorro = [];
				data_ahorro_ofertas = [];
				labels_ahorro = [];
				labels_ahorro_ofertas = [];
				$.each(res.ahorro_historico, function(key, value) 
				{						
					var total_mes = 0;
					total_mes = parseFloat(value.AhorroCompra)+parseFloat(value.AhorroProductos)+parseFloat(value.AhorroOtros);
					labels_ahorro.push(value.mes_letra);
					labels_ahorro_ofertas.push(value.mes_letra);
					data_ahorro.push(total_mes.toFixed(2));	
					data_ahorro_ofertas.push((parseFloat(value.AhorroTotalPorOfertas)+total_mes).toFixed(2));
						var porcentaje_cheque = (total_mes * 35)/2;
						var quedan = total_mes - 1;
				
					num_meses++;
				});	
				data_ahorro = data_ahorro.reverse();
				data_ahorro_ofertas = data_ahorro_ofertas.reverse();
				labels_ahorro = labels_ahorro.reverse();
				labels_ahorro_ofertas = labels_ahorro_ofertas.reverse();
				if (ahorro_acumulado+ahorro_canjeable>0)
				{
					html_result_mostrar+='<div class="row ahorro_total"><div class="col-xs-12" ><span class="TOTALChequeHistorico">Total</span><span class="AHORROChequeHistorico">Ahorro</span><p class="big_tot">'+(ahorro_acumulado+ahorro_canjeable).toFixed(2).replace('.',',')+'<span> ?</span></p> </div></div>';
					//localStorage.setItem('puntos_cheque',ahorro_total_acumulado_pendiente);
					$('.puntos_cheque').html(ahorro_total_acumulado_pendiente.toFixed(2).replace('.',',')+'<span> ?</span>');
				}
				localStorage.setItem('labels_ahorro',JSON.stringify(labels_ahorro));
				localStorage.setItem('data_ahorro',JSON.stringify(data_ahorro));
				localStorage.setItem('data_ahorro_ofertas',JSON.stringify(data_ahorro_ofertas));
				localStorage.setItem('labels_ahorro_ofertas',JSON.stringify(labels_ahorro_ofertas));
				localStorage.setItem('bloques_historicos_chequeahorro',html_result_mostrar);
				if (html_result_mostrar!='') $('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(html_result_mostrar);
			}	 
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			labels_ahorro = JSON.parse(localStorage.getItem('labels_ahorro'));
			data_ahorro = JSON.parse(localStorage.getItem('data_ahorro'));
			data_ahorro_ofertas = JSON.parse(localStorage.getItem('data_ahorro_ofertas'));
			labels_ahorro_ofertas = JSON.parse(localStorage.getItem('labels_ahorro_ofertas'));
			var ahorro_acumulado = parseFloat(localStorage.getItem('ahorro_acumulado'));
			var ahorro_quedan = parseFloat(localStorage.getItem('ahorro_quedan'));
			var ahorro_canjeable = parseFloat(localStorage.getItem('ahorro_canjeable'));
			var ahorro_total_actual = parseFloat(localStorage.getItem('ahorro_total_actual'));
			var fecha_acumulado = localStorage.getItem('fecha_acumulado');
			pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado);
			$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').html('');		
			$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(localStorage.getItem('bloques_historicos_chequeahorro'));
			
			$('.fecha_cheque').html(localStorage.getItem('fecha_cheque'));
			$('.cheque_ahorro_acumulando .saldo_fecha').html(localStorage.getItem('fecha_cheque'));
			$('.puntos_cheque').html(localStorage.getItem('puntos_cheque'));
			$('.tot_desde').html(localStorage.getItem('fecha_cheque'));
		}
	});
	}*/
function pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado)
{		
	if (ahorro_acumulado>0)
	{
		if (ahorro_quedan>0)
		{			
			$('.ahorrar').html('faltan_delante '+ahorro_quedan.toFixed(2).replace('.',',')+'<span> &euro;</span>');
			$('.ahorrar').show();						
			$('.ahorrar').addClass('ahorrar2_animation');
			$('.ahorrar2').html('Llevas '+ahorro_acumulado.toFixed(2).replace('.',',')+'<span> &euro;</span>');
			$('.ahorrar2').addClass('ahorrar2_animation2');
			$('.ahorrar2').show();							
			$('.puntos_cheque_acumulado').hide();
		}
		else
		{
			$('.puntos_cheque_acumulado').show();
			$('.puntos_cheque_acumulado').html(ahorro_acumulado.toFixed(2).replace('.',',')+'<span> &euro;</span>');
			localStorage.setItem('puntos_cheque_acumulado',ahorro_acumulado.toFixed(2).replace('.',',')+'<span> &euro;</span>');					
			$('.ahorrar').hide();
			$('.ahorrar2').hide();				
		}
	}
	else
	{		
		$('.puntos_cheque_acumulado').hide();
		$('.ahorrar').show();					
	}
	if (ahorro_canjeable>0)
	{		
		$('.ahorrar').css({'padding':'7px','font-size':'18px','line-height':'18px','float':'left'});	
		$('.ahorrar2').css({'font-size':'18px','line-height':'18px','padding':'7px'});
		$('.puntos_cheque_canjeable').show();
		$('.puntos_cheque_canjeable').html(ahorro_canjeable.toFixed(2).replace('.',',')+'<span> &euro;</span>');
		localStorage.setItem('puntos_cheque_canjeable',ahorro_canjeable.toFixed(2).replace('.',',')+'<span> &euro;</span>');					
		if (ahorro_total_actual<1)
		{ 
			//Aun necesita para generar cheque ahorro
			var quedan = 1-ahorro_total_actual;							
			$('#acum_dentro .saldo').html(quedan.toFixed(2).replace('.',',')+'<span>&euro;</span>');
			$('.cheque_ahorro_acumulando .precio').html(ahorro_total_actual.toFixed(2).replace('.',',')+'<span>&euro;</span>');
			$('.cheque_ahorro_acumulando .saldo_fecha').html(fecha_acumulado);			
			$('.empieza_acumular').hide();
		}
		else
		{
			$('.cheque_ahorro_acumulando').hide();
			$('#acum_dentro').hide();
			$('.empieza_acumular').hide();
		}
	}
	else
	{
		$('.ahorro_canjeable_nuevo').hide();
		$('.ahorro_acumulado_nuevo').css('width','100%');					
	}
}
	function obtener_vales()
	{
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://tarjeta.masymas.com/servicios-web/obtener_vales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?php print $_SESSION['id_home'];?>&cod_cliente=<?php print $_SESSION['cod_cliente'];?>	&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				console.log('res11', res);
				 if (res.validacion == 'ok')
				 {
					var resultado_ajax = '';
					$.each(res.vales, function(key, value) 
					{
						
						if (value.es_cheque_ahorro==1)
						{
							resultado_ajax += '<p class="more_right">Canjeable hasta el '+value.valido+'</p>';
						}
					});
					if (resultado_ajax!='') $('.mes_anterior_ahorro').append(resultado_ajax);
					else $('.mes_anterior_ahorro').append('<p class="more_right"></p>');
				 }
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}	
        $(document).ready(function() {
				obtener_chequeahorro();
				//obtener_vales();
				
			<?php			
			if ($errores!="")
			{
				echo '
					swal({
						title: "'.$lang['problema_encontrado'].'",
						text: "'.$errores.'",
						html: true,
						type: "error"
					});	';
			}
			if ($mensaje!="")
			{
				echo '
					swal({
						title: "'.$lang['accion_correcta'].'",
						text: "'.$mensaje.'",
						html: true,
						type: "success"
					});	';
			}
			?>
			});
	
    </script>	
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
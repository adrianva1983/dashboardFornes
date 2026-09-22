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
//$idioma = "es-es";
$idioma = $_SESSION['select_idioma'];
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title tkey="Cuadro_mandos_masymas">Cuadro de mandos masymas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	
	<!-- FooTable -->
    <link href="css/plugins/footable/footable.core.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

	<!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="js/plugins/flot/jquery.flot.time.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>
	
	<!-- Chartjs -->
	<script src="js/plugins/chartJs/Chart.min.js"></script>
	
	<!-- FooTable -->
    <script src="js/plugins/footable/footable.all.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
	<?php
	require "herramientas/favicon.php";
	?>
	
</head>

<body class="no-skin-config dashboard-page" id="body">
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
					$seccion = "dashboard";
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
            <div class="wrapper wrapper-content animated fadeInRight dash_min">
				<div class="row">
					<div class="col-lg-7 col-xs-12">
						<a href="/dashboard/ahorro.php" id="bloque-cheque-ahorro-dashboard">
						<div class="col-lg-12">
							<div class="widget style1 navy-bg">
								<div class="row" id="id_cheque_ahorro" style="display: none">
									
									<div class="col-xs-12text-right">	
										<div class="MiChequeAhorro">
											<h2 tkey="MiChequeAhorro">Mi ChequeAhorro</h2>											
										</div>
										<div class="ahorro_acumulado_nuevo">
										 	
											<img src="img/hucha.svg" alt="Cheque ahorro">
											<p class="fecha_cheque"></p>
											<p class="precio puntos_cheque_acumulado">0<span> €</span></p>	
											<h3 class="ahorrar" id="com_ahorrar" style="display:none;" tkey="ComienzaAhorrar">Comienza a Ahorrar</h3>
											<h3 class="ahorrar2" style="display:none"></h3>
											
										</div>
										
										<div id="acum" style="display:none">
											
											<p class="saldo"></p>
										</div>
										<div class="ahorro_canjeable_nuevo">											
											<img src="img/svg/cupon_euro.svg" alt="Cheque ahorro">
											<p tkey="SaldoCanjeable">Saldo canjeable</p>
											<p class="precio puntos_cheque_canjeable">0<span> €</span></p>	
										</div>										
																				
																				
									</div>
								</div>
							</div>
						</div>
						</a>
						<div class="col-lg-12" id="grafico_30">
							<div class="ibox-content text-center dash_min1">
								<h1 tkey="Compras_inicio">Compras en 30 días</h1>
								<div class="compra_30_dias"><canvas id="barChart" height="140"></canvas></div>
								<?php
				
								$datos_barchar = "[";
								$labels_barchar = "[";
								for ($i=31;$i>0;$i--)
								{
									if ($i<31)
									{
										$datos_barchar .= ",";	
										$labels_barchar .= ",";	
									}									
									$requete = "SELECT SUM(`IMPORTE_TOTAL_NETO`) AS TOTAL FROM `Cabeceras` WHERE (";
									$primero = true;
									$requete2 = "SELECT `id_card` FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home']." AND `is_erased`=0";
									$result2 = mysqli_query($db,$requete2);	
									if (($result2) && (mysqli_num_rows($result2)>0))
									{
										while ($listado2 = mysqli_fetch_object($result2))
										{
											if ($primero) $primero = false;
											else $requete.=" OR ";											
											$requete.= "`COD_TARJETA`=".$listado2->id_card;
										}
									}
									$requete.=") AND `FECHA`='".date('Y-m-d',strtotime("-".$i." days"))."'";										
									$result = mysqli_query($db,$requete);								
									$labels_barchar .= '"'.date('d',strtotime("-".$i." days")).' '.$array_meses[intval(date('m',strtotime("-".$i." days")))-1].'"';
									if (($result) && (mysqli_num_rows($result)>0))
									{
										$listado = mysqli_fetch_object($result);
										if ($listado->TOTAL!='') $datos_barchar .= number_format($listado->TOTAL,2,".","");
										else $datos_barchar .= 0;
									}
									else $datos_barchar .=0;
								}
								$datos_barchar .= "]";
								$labels_barchar .= "]";
								?>
							</div>
						</div>
						<div class="col-lg-12" id="grafico_15" style="display:none;">
							<div class="ibox-content text-center dash_min1">
								<h1 tkey="Compras_inicio_15">Compras en 15 días</h1>
								<div class="compra_30_dias"><canvas id="barChart_15" height="140"></canvas></div>
								<?php
				
								$datos_barchar1 = "[";
								$labels_barchar1 = "[";
								for ($i=15;$i>0;$i--)
								{
									if ($i<15)
									{
										$datos_barchar1 .= ",";	
										$labels_barchar1 .= ",";	
									}									
									$requete = "SELECT SUM(`IMPORTE_TOTAL_NETO`) AS TOTAL FROM `Cabeceras` WHERE (";
									$primero = true;
									$requete2 = "SELECT `id_card` FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home']." AND `is_erased`=0";
									$result2 = mysqli_query($db,$requete2);	
									if (($result2) && (mysqli_num_rows($result2)>0))
									{
										while ($listado2 = mysqli_fetch_object($result2))
										{
											if ($primero) $primero = false;
											else $requete.=" OR ";											
											$requete.= "`COD_TARJETA`=".$listado2->id_card;
										}
									}
									$requete.=") AND `FECHA`='".date('Y-m-d',strtotime("-".$i." days"))."'";										
									$result = mysqli_query($db,$requete);								
									$labels_barchar1 .= '"'.date('d',strtotime("-".$i." days")).' '.$array_meses[intval(date('m',strtotime("-".$i." days")))-1].'"';
									if (($result) && (mysqli_num_rows($result)>0))
									{
										$listado = mysqli_fetch_object($result);
										if ($listado->TOTAL!='') $datos_barchar1 .= number_format($listado->TOTAL,2,".","");
										else $datos_barchar1 .= 0;
									}
									else $datos_barchar1 .=0;
								}
								$datos_barchar1 .= "]";
								$labels_barchar1 .= "]";
								?>
							</div>
						</div>
					</div>
					<div class="col-lg-5 col-xs-12">												
						<div class="col-lg-12">
							<div class="widget style1 navy-bg">
								<div class="row">
									<div class="col-xs-3">
										<img src="img/svg/movil.svg" alt="app movil">
									</div>
									<div class="col-xs-9 text-right">							
										<span tkey="Descargate_nuestra">Desc&aacutergate nuestra</span>
										<h2 tkey="APP_movil">APP móvil</h2>
										<div class="btn-group botonera-app">
											<a class="btn btn-white" target="_blank" href="https://itunes.com/apps/semillaproyectos/com.semillaproyectos.masymas.levante"><i class="fa fa-apple"></i> App Store</a>
											<a class="btn btn-white" target="_blank" href="https://play.google.com/store/apps/details?id=com.semillaproyectos.masymas.fornes"><i class="fa fa-android"></i> Google Play</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-lg-12">
							<div id="fb-root"></div>
							<script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v3.2"></script>
							<div class="fb-page" data-href="http://www.facebook.com/masymasfornes" data-tabs="timeline" data-width="500" data-height="315" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="false"><blockquote cite="http://www.facebook.com/masymasfornes" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/masymasfornes" tkey="Supermercados_masymas">Supermercados masymas Fornés</a></blockquote></div>						
						</div>
					</div>
				</div>				
			</div>
			<div class="footer">
				<div class="pull-right">
					<strong>masymas</strong>
				</div>
				<div>
					<strong>Copyright</strong> <a href="http://www.semillaproyectos.com" tkey="Supermercados_masymas">Semilla Proyectos Internet &copy;</a>
				</div>
			</div>
        </div>

    </div>
	<div class="modal inmodal" id="myModal3" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content animated flipInY">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
					<i class="fa fa-mobile modal-icon"></i>
					<h4 class="modal-title">Inst&aacute;late nuestra app m&oacute;vil</h4>					
				</div>
				<div class="modal-body">
					<p>Disponible para iOS y Android.</p>				
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
					<a class="btn btn-primary" target="_blank" href="#"><i class="fa fa-apple"></i> Versi&oacute;n iOS</a>
					<a class="btn btn-primary" target="_blank" href="#"><i class="fa fa-android"></i> Versi&oacute;n Android</a>
				</div>
			</div>
		</div>
	</div>
	<script src="/dashboard/lang/lang.js" type="text/javascript"></script>
	<script src="/dashboard/lang/es.js" type="text/javascript"></script>
	<script src="/dashboard/lang/en.js" type="text/javascript"></script>
	<script src="/dashboard/lang/va.js" type="text/javascript"></script>
    <script>
	
	function abre_dialogo_app()
	{
		$('#myModal3').modal();
	}
	function pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado)
	{		
		if (ahorro_acumulado>0)
		{
			if (ahorro_quedan>0)
			{			
				$('.ahorrar').html(temp_lang['faltan_delante']+' '+ahorro_quedan.toFixed(2).replace('.',',')+'<span> €</span>');
				$('.ahorrar').show();						
				$('.ahorrar').addClass('ahorrar2_animation');
				$('.ahorrar2').html(temp_lang['Llevas']+' '+ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');
				$('.ahorrar2').addClass('ahorrar2_animation2');
				$('.ahorrar2').css('margin-top','18%');
				$('.ahorrar2').css('width','40%');
				$('.ahorrar').css('margin-top','18%');
				$('.ahorrar').css('width','40%');
				$('.ahorrar').css('left','30%');
				$('.ahorrar2').show();							
				$('.puntos_cheque_acumulado').hide();
			}
			else
			{
				$('.puntos_cheque_acumulado').show();
				$('.puntos_cheque_acumulado').html(ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');
				localStorage.setItem('puntos_cheque_acumulado',ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');					
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
			$('.ahorrar').css({'padding':'20px','font-size':'18px','line-height':'18px','float':'left'});	
			$('.ahorrar2').css({'font-size':'18px','line-height':'18px','padding':'7px'});
			$('.puntos_cheque_canjeable').show();
			$('.puntos_cheque_canjeable').html(ahorro_canjeable.toFixed(2).replace('.',',')+'<span> €</span>');
			localStorage.setItem('puntos_cheque_canjeable',ahorro_canjeable.toFixed(2).replace('.',',')+'<span> €</span>');					
			if (ahorro_total_actual<1)
			{ 
				//Aun necesita para generar cheque ahorro
				var quedan = 1-ahorro_total_actual;							
				$('#acum_dentro .saldo').html(quedan.toFixed(2).replace('.',',')+'<span>€</span>');
				$('.cheque_ahorro_acumulando .precio').html(ahorro_total_actual.toFixed(2).replace('.',',')+'<span>€</span>');
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
			$('.ahorrar').css('margin-top','18%');
			$('.ahorrar').css('width','40%');
			$('.ahorrar').addClass('ahorrar2_animation');
			$('.ahorrar').css('left','30%');
			$('.ahorrar2').css('margin-top','18%');			
			$('.ahorro_acumulado_nuevo').css('width','100%');
			$('#com_ahorrar').css('margin-left','0%');					
		}
		$('#id_cheque_ahorro').show();
	}
	function obtener_chequeahorro()
	{
		$.ajax({
			type:'GET',
			timeout: 3000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/obtener_chequeahorro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['cod_cliente']?>&idioma=<?=$idioma?>&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				var resultado_ajax ='';
				if (res.validacion=='ok')
				{
					console.log('res.fecha', res);
					//localStorage.setItem('puntos_cheque',res.puntos);					
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
					html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_actual+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(0);">'+temp_lang['ver_tickets']+' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';
					if (quedan<0)
					{
						var fecha_actual = new Date();
						var time_actual = fecha_actual.getTime();							
						var ultimo_dia_mes = new Date(fecha_actual.getFullYear(),fecha_actual.getMonth()+1,0);
						var time_comparacion = ultimo_dia_mes.getTime()-(7*24*60*60*1000);
						
						html_result+='<div class="col-xs-12"><div class="explicacion_cheque';					
						if (time_comparacion<=time_actual) html_result+=' va_a_caducar';							
						if (quedan.toFixed(2)<=-1) html_result+='">'+temp_lang['empieza_acumular']+'</div></div>';
						else html_result+='">'+temp_lang['faltan_delante']+' '+(-quedan.toFixed(2))+'&euro; '+temp_lang['faltan_detras']+'</div></div>';
					}
					else html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['sique_acumulando']+'</div></div>';
					html_result+='</div>';
					html_result+='<div class="row subtotales';
					if (quedan<0) html_result+=' no_alcanzado';
					html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>'+temp_lang['1detuscompras']+'</h5><p>'+parseFloat(res.ahorro_compra_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
					html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>'+temp_lang['ProductosChequeAhorro']+'</h5><p>'+parseFloat(res.ahorro_productos_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
					html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang['Otros']+'</h5> <p>'+parseFloat(res.ahorro_otros_actual).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
					total_acumulado+=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
					total_acumulado_2meses +=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
					ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
					var ahorro_total_acumulado_pendiente1 = parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
					//Historico anterior
					html_result+='<div class="row compras titulo_ahorro_historico';
					var total_mes = 0;
					total_mes = parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
					var quedan = total_mes - 1;
					if (quedan<0) html_result+=' no_alcanzado';
					html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_anterior+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(1);">'+temp_lang['ver_tickets']+' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';						
					if (quedan<0) html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['no_llegaste']+'</div></div>';						
					else if (res.ahorro_fecha_redencion!='') html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['cheque_canjeado']+' '+res.ahorro_fecha_redencion+'</div></div>';
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
						if (res.ahorro_fecha_caducidad!='') html_result+=temp_lang['cheque_canjeable']+' '+res.ahorro_fecha_caducidad;
						else html_result+=temp_lang['disponible_cheque_apartir'];
						html_result+='</div></div>';				
						ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
						ahorro_total_acumulado_pendiente2 = parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
					}
					html_result+='</div>';
					html_result+='<div class="row subtotales';
					if (quedan<0) html_result+=' no_alcanzado';
					html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>'+temp_lang['1detuscompras']+'</h5><p>'+parseFloat(res.ahorro_compra_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
					html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>'+temp_lang['ProductosChequeAhorro']+'</h5><p>'+parseFloat(res.ahorro_productos_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
					html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang['Otros']+'</h5> <p>'+parseFloat(res.ahorro_otros_anterior).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
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
					if (total_acumulado>0)
					{
						html_result_mostrar+='<div class="row ahorro_total"><div class="col-xs-12" ><span class="TOTALChequeHistorico">'+temp_lang['Total']+'</span><span class="AHORROChequeHistorico">'+temp_lang['Ahorro']+'</span><p class="big_tot">'+total_acumulado_2meses.toFixed(2).replace('.',',')+'<span> €</span></p> </div></div>';
						//localStorage.setItem('puntos_cheque',ahorro_total_acumulado_pendiente);
						console.log('ahorro_total_acumulado_pendiente1', ahorro_total_acumulado_pendiente1 + ' - ' + <?=$_SESSION['cod_cliente']?>);
						//console.log('ahorro_total_acumulado_pendiente2', ahorro_total_acumulado_pendiente2);
						$('.puntos_cheque').html(ahorro_total_acumulado_pendiente.toFixed(2).replace('.',',')+'<span> €</span>');
					}
					if (html_result_mostrar!='') $('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(html_result_mostrar);
				}		 
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{			
			}
		});
	}
	function obtener_mensajes(todas,vistas)
    {
	    $.ajax({
        type:'GET',
        timeout: 20000,
        dataType: 'json',
        url:'https://www.appfornes.es/servicios-web/obtener_mensajes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&vistas='+vistas+'&idioma=es&anticache='+(new Date()).getTime(),
        success:function(res, textStatus, XMLHttpRequest)
        {
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
						html_result += '<div class="row ibox-content lista_prod" id="mensaje_'+value.id+'_'+value.cod_barras+'"><div class="col-xs-1 nuevo">';
						if (value.leido==0) html_result+= '<i class="fa fa-circle"></i>';
						html_result += '</div>';
						html_result += '<div class="col-xs-8 prod_data"><p class="prod_env"';
						var tmp = value.id.split('-');
						if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-folletos\');return false;"';
						else if (tmp[0]=='cupon') html_result+=' onclick="cupon_resaltar=\''+value.cod_barras+'\';marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-tarjeta\');return false;"';
						else if (tmp[0]=='multicupon') 
						{
							html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-multi-cupon\');';
							if (value.siguiente==1) html_result+='$(\'.pagina-multi-cupon .grup_cupones\').hide();$(\'.pagina-multi-cupon .grup_cupones_siguiente\').fadeIn();';
							else html_result+='$(\'.pagina-multi-cupon .grup_cupones_siguiente\').hide();$(\'.pagina-multi-cupon .grup_cupones\').fadeIn();';
							html_result+='return false;"';
						}
						else if (tmp[0]=='chequeahorro') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-tarjeta\');return false;"';
						else if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-folletos\');return false;"';
						else html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');"';
						html_result +='>'+value.texto+'</p></div><div class="col-xs-3 prod_activo">';
						//html_result +='<input type="checkbox" class="styleMe" id="input_'+value.id+'"/>';
						//if (value.tipo!=''&&value.tipo!=undefined) html_result+=value.tipo;						
						html_result+='</div>';
						html_result+='<a href="#" class="btn btn-primary borrar_mensaje" onclick="marcar_mensaje_borrado(\''+value.id+'\',\''+value.cod_barras+'\');$(\'#mensaje_'+value.id+'\').fadeOut();return false;"><i class="fa fa-trash"></i></a></div>';
                        //html_result+='</div>';
                        //html_result+='<hr/>';					
					}
				});								
				$('.notif').html(mensajes_no_leidos);
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
	$(document).ready(function() {
		
		
		//obtener_mensajes(0, 1);
		obtener_chequeahorro();
		var ancho_pantalla = $("#body").width();
		var labels = '';
		var data = '';
		if (ancho_pantalla > 768)
		{
			labels = <?=$labels_barchar?>;
			data = <?=$datos_barchar?>;
			$('#grafico_30').show();
			$('#grafico_15').hide();
		}
		else
		{
			labels = <?=$labels_barchar1?>;
			data = <?=$datos_barchar1?>;
			$('#grafico_15').show();
			$('#grafico_30').hide();
		}
		$('.footable').footable();
		var barData = {				
			labels: labels,
			datasets: [
						{
						label: "Consumo",
						fillColor: "rgba(2,108,80,1)",
						strokeColor: "rgba(220,220,220,0.8)",
						highlightFill: "rgba(2,108,80,0.75)",
						highlightStroke: "rgba(220,220,220,1)",
						data: data
						}
					]
				};
		var barOptions = {
			scaleBeginAtZero: true,
			scaleShowGridLines: true,
			scaleGridLineColor: "rgba(0,0,0,.05)",
			scaleGridLineWidth: 1,
			barShowStroke: true,
			barStrokeWidth: 2,
			barValueSpacing: ancho_pantalla > 768 ? 15:5,
			barDatasetSpacing: 1,
			responsive: true,
			maintainAspectRatio: ancho_pantalla > 768 ? true:false,
		}

		var ctx = document.getElementById("barChart").getContext("2d");
		var ctx1 = document.getElementById("barChart_15").getContext("2d");
		if (ancho_pantalla<=768)
		{
			ctx.canvas.parentNode.style.height = '30vh';
			ctx.canvas.parentNode.style.width = '90vw';
		}
		var myNewChart = new Chart(ctx).Bar(barData, barOptions);
		var myNewChart = new Chart(ctx1).Bar(barData, barOptions);
		//myNewChart.resize(600, 600);
		
	});
    </script>	
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
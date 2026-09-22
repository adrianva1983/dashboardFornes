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

    <title tkey="Compras_masymas">Compras masymas</title>

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
	<?php
	require "herramientas/favicon.php";
	?>
	
</head>

<body class="no-skin-config" id="body">
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
						$seccion = "compras";
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
            <div class="wrapper wrapper-content animated fadeInRight">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="tabla1">				
					<div class="ibox">						
						<ul class="nav nav-tabs">
							<li><a class="nav-link botonera-ahorro activo boton-ahorro" onclick="cargar_ahorro();return false;" href="#" tkey="ChequeAhorro">ChequeAhorro</a></li>
							<li><a class="nav-link botonera-ahorro boton-ahorro-ofertas" onclick="cargar_ahorro_ofertas();return false;" href="#" tkey="Ahorro">Ahorro</a></li>
							<!--<li><a class="nav-link botonera-ahorro boton-compras" onclick="cargar_compras();return false;" href="#">Compras</a></li>-->
							<li><a class="nav-link botonera-ahorro boton-tickets" onclick="cargar_tickets();return false;" href="#" tkey="Tickets">Tickets</a></li>
						</ul>
						<div class="tab-content">
                            <div id="tab-1" class="tab-panel">
								<div class="ahorro_6_meses"><canvas id="barChart2" height="100"></canvas></div>
								<div class="explicacion-historico"><strong tkey="TituloGraficaChequeAhorro">ChequesAhorro</strong> <span tkey="ExplicacionGraficaChequeAhorro">obtenidos por utilizar tu Tarjeta Cliente acumulando el 1% de tus compras, Productos ChequeAhorro y otros descuentos.</span></div>
							</div>
							<div id="tab-2" class="tab-panel">
								<div class="compra_6_meses"><canvas id="barChart" height="100"></canvas></div>
								<div class="explicacion-historico"><strong tkey="TituloGraficaAhorro">Ahorro Total</strong> <span tkey="ExplicacionGraficaAhorro">obtenido por haber realizado tus compras en masymas.</span></div>
							</div>
							<?php
							//$array_meses = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");							
							if ($idioma == 'en')
							{
								$array_meses = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
							}
							else if ($idioma == 'va')
							{
								$array_meses = array("","Gen","Feb","Mar","Abr","Mai","Jun","Jul","Ago","Sep","Oct","Nov","Des");
							}
							else
							{
								$array_meses = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
							}
							$datos_barchar = "[";
							$datos_barchar2 = "[";
							$labels_barchar = "[";
							for ($i=11;$i>-1;$i--)
							{
								if ($i<11) 
								{
									$datos_barchar .= ",";	
									$datos_barchar2 .= ",";	
									$labels_barchar .= ",";	
								}
								$requete = "SELECT SUM(`IMPORTE_TOTAL_NETO`) AS TOTAL FROM `Cabeceras` WHERE (";								
								$requete2= "SELECT `id_card` FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home']." AND `is_erased`=0";
								$result2 = mysqli_query($db,$requete2);	
								$primero = true;
								if (($result2) && (mysqli_num_rows($result2)>0))
								{
									while ($listado2 = mysqli_fetch_object($result2))
									{
										if ($primero) $primero = false;
										else $requete.=" OR ";
										$requete.= "`COD_TARJETA`=".$listado2->id_card;
									}
								}
								$requete.= ") AND `FECHA`>='".date('Y-m',strtotime("-".$i." months"))."-01' AND `FECHA`<'".date('Y-m',strtotime("-".($i-1)." months"))."-01'";	
								$result = mysqli_query($db,$requete);								
								$labels_barchar .= '"'.$array_meses[intval(date('m',strtotime("-".($i+1)." months")))].' - '.date('Y',strtotime("-".($i+1)." months")).'"';
								if (($result) && (mysqli_num_rows($result)>0))
								{
									$listado = mysqli_fetch_object($result);
									if ($listado->TOTAL!='') $datos_barchar .= number_format($listado->TOTAL,2,".","");
									else $datos_barchar .= 0;
								}
								else $datos_barchar .=0;
								$requete = "SELECT * FROM `APP_cheque_ahorro_historico` WHERE `id_familia`=".$_SESSION['id_home']." AND `Ano`='".date('Y',strtotime("-".($i+1)." months"))."' AND `Mes`='".date('m',strtotime("-".($i+1)." months"))."'";
								$result = mysqli_query($db,$requete);
								$tmp_ahorro = 0;
								if (($result) && (mysqli_num_rows($result)>0))
								{
									$listado = mysqli_fetch_object($result);
									$tmp_ahorro = $listado->AhorroCompra+$listado->AhorroProductos+$listado->AhorroOtros;
								}
								$datos_barchar2 .=$tmp_ahorro;
							}
							$datos_barchar2 .= "]";
							$datos_barchar .= "]";
							$labels_barchar .= "]";
							?>
							<div id="tab-3" class="tab-panel">
								<?php
								if ($_GET['ver_ticket']!='')
								{
									echo '
									<div class="row"><div class="col-lg-12"><div class="ibox"><div class="ibox-title">
									<h5>Detalle de Ticket de compra '.$_GET['ver_ticket'].'</h5>
									<div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a><a class="close-link"><i class="fa fa-times"></i></a></div>
									</div><div class="ibox-content"><table class="table table-hover"><thead><tr><th>REF</th><th>Nombre</th><th>Unidades</th><th>Precio unitario</th><th>Importe</th></tr></thead>';			
									$requete = "SELECT * FROM `Cabeceras` WHERE `Id`=".$_GET['ver_ticket']." AND (";									
									$requete2= "SELECT `id_card` FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home']." AND `is_erased`=0";
									$result2 = mysqli_query($db,$requete2);	
									$primero = true;
									if (($result2) && (mysqli_num_rows($result2)>0))
									{
										while ($listado2 = mysqli_fetch_object($result2))
										{
											if ($primero) $primero = false;
											else $requete.=" OR ";
											$requete.= "`COD_TARJETA`=".$listado2->id_card;
										}
									}
									$requete.=")";
									$result = mysqli_query($db,$requete);			
									if (($result) && (mysqli_num_rows($result)>0))
									{
										$listado = mysqli_fetch_object($result);
										$total_descuento = $listado->IMPORTE_TOTAL_BRUTO - $listado->IMPORTE_TOTAL_NETO;
										$total_ticket = $listado->IMPORTE_TOTAL_NETO;
										$requete = "SELECT * FROM `Lineas` WHERE `IdCabecera`=".$_GET['ver_ticket']." AND `Anulado`=0;";					
										$result = mysqli_query($db,$requete);			
										if (($result) && (mysqli_num_rows($result)>0))
										{
											print '<tbody>';
											while ($listado = mysqli_fetch_object($result))
											{
												print '<tr>';							
												$requete2 = "SELECT * FROM `Productos` WHERE `id_product`='".$listado->Producto."'";
												$result2 = mysqli_query($db,$requete2);			
												if (($result2) && (mysqli_num_rows($result2)>0))
												{
													$listado2 = mysqli_fetch_object($result2);
												}
												print '<td>'.$listado->Producto.'</td>';
												print '<td>';										
												if ($listado->oTipo==2) print '<span class="label label-info">Promo</span> '.utf8_encode($listado2->ds_product);
												else if ($listado->bDevol==1) print '<span class="label label-danger">Devolución</span> '.utf8_encode($listado2->ds_product);
												else print utf8_encode($listado2->ds_product);
												print '</td>';
												print '<td>'.$listado->Cantidad.'</td>';
												if ($listado2->bDevol==1) print '<td>-'.$listado->Precio.'</td>';
												else print '<td>'.number_format($listado->Precio,2,",",".").'&euro;</td>';
												print '<td>'.number_format($listado->Importe,2,",",".").'&euro;</td>';
												print '</tr>';									
											}
											print '</tbody><tfoot>';
											print '<tr><th colspan="4">Total antes de descuentos:</th><td>'.number_format(($total_ticket+$total_descuento),2,",",".").'&euro;</td></tr>';
											print '<tr><th colspan="4">Total descuento:</th><td>'.number_format($total_descuento,2,",",".").'&euro;</td></tr>';
											print '<tr><th colspan="4">Total ticket:</th><th>'.number_format($total_ticket,2,",",".").'&euro;</th></tr>';
											print '</tfoot></table>';	
										}
									}
									else
									{					
										print '<p class="label label-danger">Error. Ticket no existe o no tiene permisos para consultarlo.</p>';
									}					
									print '</table></div></div></div></div>';
								}
								?>
								<div class="row"><div class="col-lg-12"><div class="ibox">		
									<!--<div class="ibox-content"><table id="table_tickets" class="table table-hover"><thead><tr><th tkey="Tienda">Tienda</th><th class="hidden-xs">TPV</th><th>Ticket</th><th class="hidden-xs" tkey="Operador">Operador</th><th tkey="Fecha">Fecha</th><th class="hidden-xs" tkey="Hora">Hora</th><th tkey="Importe">Importe</th><th tkey="Detalle">Detalle</th></tr></thead><tbody>-->
									<?php
									$array_valores = array();
									$requete = "SELECT * FROM `Cabeceras` WHERE (";
									$requete2= "SELECT * FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home']." AND `is_erased`=0";
									$result2 = mysqli_query($db,$requete2);	
									$primero = true;
									if (($result2) && (mysqli_num_rows($result2)>0))
									{
										while ($listado2 = mysqli_fetch_object($result2))
										{
											if ($primero) $primero = false;
											else $requete.=" OR ";
											$requete.= "`COD_TARJETA`=".$listado2->id_card;
										}
									}
									$requete.=")";
									if ($_GET['fecha']!='') 
									{
										$tmp_fecha = explode('/',$_GET['fecha']);
										$tmp_fecha2 = $tmp_fecha[2]."-".$tmp_fecha[1]."-".$tmp_fecha[0];
										if ($_GET['mes_completo']==1) $requete.= " AND `FECHA`>='".$tmp_fecha2."' AND `FECHA`<'".date('Y-m-d',strtotime($tmp_fecha2.' +1 month'))."'";
										else $requete.= " AND `FECHA`='".$tmp_fecha2."'";	
									}
									if ($_GET['importe']!='') 
									{
										$importe = str_replace(',','.',$_GET['importe']);
										$requete.= " AND `IMPORTE_TOTAL_NETO`>".$importe;				
									}
									if ($_GET['importe2']!='') 
									{
										$importe2 = str_replace(',','.',$_GET['importe2']);
										$requete.= " AND `IMPORTE_TOTAL_NETO`<".$importe2;				
									}
									if ($_GET['mes_previo']>0)
									{
										$fecha_rango1 = date('Y-m',strtotime(date('Y-m').'-01 -'.($_GET['mes_previo']).' months')).'-01';
										$fecha_rango2 = date('Y-m',strtotime(date('Y-m').'-01 -'.($_GET['mes_previo']-1).' months')).'-01';
										$requete.= " AND `FECHA`>='".$fecha_rango1."' AND `FECHA`<'".$fecha_rango2."'";
									}
									else
									{
										$fecha_rango1 = date('Y-m').'-01';
										$fecha_rango2 = date('Y-m',strtotime('+1 months')).'-01';
										$requete.= " AND `FECHA`>='".$fecha_rango1."' AND `FECHA`<'".$fecha_rango2."'";										
									}
									$requete.=" ORDER BY `FECHA`";																											
									$result = mysqli_query($db,$requete);			
									if (($result) && (mysqli_num_rows($result)>0))
									{
										print '<div class="ibox-content"><table id="table_tickets" class="table table-hover"><thead><tr><th tkey="Tienda">Tienda</th><th class="hidden-xs">TPV</th><th>Ticket</th><th class="hidden-xs" tkey="Operador">Operador</th><th tkey="Fecha">Fecha</th><th class="hidden-xs" tkey="Hora">Hora</th><th tkey="Importe">Importe</th><th tkey="Detalle">Detalle</th></tr></thead><tbody>';
										while ($listado = mysqli_fetch_object($result))
										{
											print '<tr>';
											print '<td>'.$listado->TIENDA.'</td>';
											print '<td class="hidden-xs">'.$listado->CAJA.'</td>';
											print '<td>'.$listado->TICKET.'</td>';
											print '<td class="hidden-xs">'.$listado->OPERADOR.'</td>';
											$tmp_fecha = explode('-',$listado->FECHA);
											print '<td><i class="fa fa-calendar"></i> '.$tmp_fecha[2].'/'.$tmp_fecha[1].'/'.$tmp_fecha[0].'</td>';
											print '<td class="hidden-xs"><i class="fa fa-clock-o"></i> '.$listado->HORA.'</td>';
											print '<td style="text-align:right;">'.number_format($listado->IMPORTE_TOTAL_NETO,2,',','').'&euro;</td>';
											print '<td><a onclick="cargar_ticket_texto(\''.$listado->Id.'\');return false;" title="Ver detalle ticket"><i class="fa fa-file-text-o"></i></a></td>';
											print '</tr>';									
										}
									}
									else
									{
										print '<div class="ibox-content"><h3 tkey="No_tickets_mes">No existen tickets para este mes</h3>';
									}
									?>
									</tbody></table>
									<div class="btn-group">
										<?php
										for ($i=0;$i<12;$i++)
										{
											print '<a href="?mes_previo='.$i.'&solapa=tickets" class="btn';
											if ($_GET['mes_previo']==$i) print ' btn-primary';
											else print ' btn-white';
											print '">'.$array_meses[intval(date('m',strtotime(date('Y-m').'-01 -'.$i.' months')))].' '.date('Y',strtotime('-'.$i.' months')).'</a>';
										}
										?>
									</div>
								</div></div></div></div>
							</div>
						</div>
					</div>
				</div>
				<div class="detalle_ticket_texto">
					<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12" id="tabla2" style="display: none">
						<div class="ibox">
							<div class="ibox-content">
								<div class="contenido_ticket"></div>
							</div>
						</div>
					</div>
				</div>
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
		var data_ahorro_ofertas = [];
		function cargar_ahorro()
		{
			var ancho_pantalla = $("#body").width();
			
			$('#tabla2').hide();
			$('#tabla1').removeClass('col-lg-7');
			$('#tabla1').addClass('col-lg-12');
			console.log('$datos_barchar2', <?php print $datos_barchar2;?>);
			$('.tab-panel').hide();
			$('#tab-1').show();
			$('.botonera-ahorro').removeClass('activo');
			$('.boton-ahorro').addClass('activo');			
			var barData2 = {				
				labels: <?php print $labels_barchar;?>,
				datasets: [
					{
					label: "Cheque ahorro",
					fillColor: "rgba(255,213,17,1)",
					strokeColor: "rgba(220,220,220,0.8)",
					highlightFill: "rgba(255,213,17,0.75)",
					highlightStroke: "rgba(220,220,220,1)",
					data: <?php print $datos_barchar2;?>
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
				barValueSpacing: 25,
				barDatasetSpacing: 1,
				responsive: true,
				maintainAspectRatio: ancho_pantalla > 768 ? true:false,
			}
			var ctx = document.getElementById("barChart2").getContext("2d");
			if (ancho_pantalla<=768)
			{
				ctx.canvas.parentNode.style.height = '40vh';
				ctx.canvas.parentNode.style.width = '95vw';
			}
			var myNewChart = new Chart(ctx).Bar(barData2, barOptions);
			
		}
		function obtener_chequeahorro()
		{
			$.ajax({
				type:'GET',
				timeout: 3000,
				dataType: 'json',
				url:'https://www.appfornes.es/servicios-web/obtener_chequeahorro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['cod_cliente']?>&dos_meses=100&idioma=es&anticache='+(new Date()).getTime(),
				success:function(res, textStatus, XMLHttpRequest)
				{
					var resultado_ajax ='';
					if (res.validacion=='ok')
					{
						//localStorage.setItem('puntos_cheque',res.puntos);
						localStorage.setItem('fecha_cheque',res.fecha);
						$('.fecha_cheque').html(res.fecha);
						var ahorro_total_actual = parseFloat(res.ahorro_total_actual);
						var ahorro_total_anterior = parseFloat(res.ahorro_total_anterior);
						var ahorro_canjeable = parseFloat(res.home_canjeable);
						var ahorro_acumulado = parseFloat(res.home_acumulado);
						var ahorro_quedan = parseFloat(res.home_no_perder);
						var fecha_acumulado = res.fecha;
						localStorage.setItem('ahorro_total_actual',ahorro_total_actual);
						localStorage.setItem('ahorro_canjeable',ahorro_canjeable);
						localStorage.setItem('ahorro_acumulado',ahorro_acumulado);
						localStorage.setItem('ahorro_quedan',ahorro_quedan);
						localStorage.setItem('fecha_acumulado',fecha_acumulado);
						
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
						html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_actual+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(0);"> <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';
						if (quedan<0)
						{
							var fecha_actual = new Date();
							var time_actual = fecha_actual.getTime();							
							var ultimo_dia_mes = new Date(fecha_actual.getFullYear(),fecha_actual.getMonth()+1,0);
							var time_comparacion = ultimo_dia_mes.getTime()-(7*24*60*60*1000);
							
							html_result+='<div class="col-xs-12"><div class="explicacion_cheque';					
							if (time_comparacion<=time_actual) html_result+=' va_a_caducar';							
							if (quedan.toFixed(2)<=-1) html_result+='"></div></div>';
							else html_result+='"> '+(-quedan.toFixed(2))+'&euro; </div></div>';
						}
						else html_result+='<div class="col-xs-12"><div class="explicacion_cheque"></div></div>';
						html_result+='</div>';
						html_result+='<div class="row subtotales';
						if (quedan<0) html_result+=' no_alcanzado';
						html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5></h5><p>'+parseFloat(res.ahorro_compra_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
						html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5></h5><p>'+parseFloat(res.ahorro_productos_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
						html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5></h5> <p>'+parseFloat(res.ahorro_otros_actual).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
						total_acumulado+=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
						total_acumulado_2meses +=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
						ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
						//Historico anterior
						html_result+='<div class="row compras titulo_ahorro_historico';
						var total_mes = 0;
						total_mes = parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
						var quedan = total_mes - 1;
						if (quedan<0) html_result+=' no_alcanzado';
						html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_anterior+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(1);"> <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';						
						if (quedan<0) html_result+='<div class="col-xs-12"><div class="explicacion_cheque"></div></div>';						
						else if (res.ahorro_fecha_redencion!='') html_result+='<div class="col-xs-12"><div class="explicacion_cheque"> '+res.ahorro_fecha_redencion+'</div></div>';
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
							if (res.ahorro_fecha_caducidad!='') html_result+= res.ahorro_fecha_caducidad;
							else html_result+='disponible_cheque_apartir';
							html_result+='</div></div>';				
							ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
						}
						html_result+='</div>';
						html_result+='<div class="row subtotales';
						if (quedan<0) html_result+=' no_alcanzado';
						html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5></h5><p>'+parseFloat(res.ahorro_compra_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
						html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5></h5><p>'+parseFloat(res.ahorro_productos_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
						html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5></h5> <p>'+parseFloat(res.ahorro_otros_anterior).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
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
							html_result_mostrar+='<div class="row ahorro_total"><div class="col-xs-12" ><span class="TOTALChequeHistorico"></span><span class="AHORROChequeHistorico"></span><p class="big_tot">'+total_acumulado_2meses.toFixed(2).replace('.',',')+'<span> €</span></p> </div></div>';
							//localStorage.setItem('puntos_cheque',ahorro_total_acumulado_pendiente);
							$('.puntos_cheque').html(ahorro_total_acumulado_pendiente.toFixed(2).replace('.',',')+'<span> €</span>');
						}
						localStorage.setItem('labels_ahorro',JSON.stringify(labels_ahorro));
						localStorage.setItem('data_ahorro',JSON.stringify(data_ahorro));
						localStorage.setItem('data_ahorro_ofertas',JSON.stringify(data_ahorro_ofertas));
						localStorage.setItem('labels_ahorro_ofertas',JSON.stringify(labels_ahorro_ofertas));
						localStorage.setItem('bloques_historicos_chequeahorro',html_result_mostrar);
						if (html_result_mostrar!='') $('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(html_result_mostrar);
					}
					console.log('data_ahorro_ofertas', data_ahorro_ofertas);
					console.log('labels_ahorro_ofertas', labels_ahorro_ofertas);		 
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
					$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').html('');		
					$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(localStorage.getItem('bloques_historicos_chequeahorro'));
					
					$('.fecha_cheque').html(localStorage.getItem('fecha_cheque'));
					$('.cheque_ahorro_acumulando .saldo_fecha').html(localStorage.getItem('fecha_cheque'));
					$('.puntos_cheque').html(localStorage.getItem('puntos_cheque'));
					$('.tot_desde').html(localStorage.getItem('fecha_cheque'));
				}
			});
		}
		function cargar_ahorro_ofertas(registrar_navegacion = true)
		{
			/*if (registrar_navegacion)
			{
				if (origen_array[origen_array.length-1]=='pagina-ahorro-historico'&&origen_subseccion[origen_subseccion.length-1]=='cargar_ahorro_ofertas(false);')
				{
					//No repetir item en navegación
				}
				else
				{
					origen_subseccion.push('cargar_ahorro_ofertas(false);');		
					origen_array.push('pagina-ahorro-historico');	
					origen_altura.push(0);
				}
			}*/
			var ancho_pantalla = $("#body").width();
			$('#tabla2').hide();
			$('#tabla1').removeClass('col-lg-7');
			$('#tabla1').addClass('col-lg-12');
			
			$('.tab-panel').hide();
			$('#tab-2').show();
			$('.botonera-ahorro').removeClass('activo');
			$('.boton-ahorro-ofertas').addClass('activo');

			var barData = {				
			labels: <?php print $labels_barchar;?>,
			datasets: [
				{
				label: "Ahorro",
				fillColor: "rgba(2,108,80,1)",
				strokeColor: "rgba(220,220,220,0.8)",
				highlightFill: "rgba(2,108,80,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data: data_ahorro_ofertas
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
				barValueSpacing: 25,
				barDatasetSpacing: 1,
				responsive: true,
				maintainAspectRatio: ancho_pantalla > 768 ? true:false,
			}
			//var ctx4 = document.getElementById("barChart4").getContext("2d");	
			//var myNewChart4 = new Chart(ctx4).Bar(barData, barOptions);	

			var ctx = document.getElementById("barChart").getContext("2d");
			if (ancho_pantalla<=768)
			{
				console.log('Entro222222');
				ctx.canvas.parentNode.style.height = '40vh';
				ctx.canvas.parentNode.style.width = '95vw';
			}
			var myNewChart = new Chart(ctx).Bar(barData, barOptions);
	
		}
		function cargar_tickets()
		{
			$('.botonera-ahorro').removeClass('activo');
			$('.boton-tickets').addClass('activo');
			$('.tab-panel').hide();
			$('#tab-3').fadeIn();
		}
		function cargar_compras()
		{
			var ancho_pantalla = $("#body").width();
			$('.tab-panel').hide();
			$('#tab-2').show();
			$('.botonera-ahorro').removeClass('activo');
			$('.boton-compras').addClass('activo');
			var barData = {				
				labels: <?php print $labels_barchar;?>,
				datasets: [
					{
					label: "Consumo",
					fillColor: "rgba(2,108,80,1)",
					strokeColor: "rgba(220,220,220,0.8)",
					highlightFill: "rgba(2,108,80,0.75)",
					highlightStroke: "rgba(220,220,220,1)",
					data: <?php print $datos_barchar;?>
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
				barValueSpacing: 25,
				barDatasetSpacing: 1,
				responsive: true,
				maintainAspectRatio: ancho_pantalla > 768 ? true:false,
			}
			var ctx = document.getElementById("barChart").getContext("2d");
			if (ancho_pantalla<=768)
			{
				console.log('Entro11111');
				ctx.canvas.parentNode.style.height = '40vh';
				ctx.canvas.parentNode.style.width = '95vw';
			}
			var myNewChart = new Chart(ctx).Bar(barData, barOptions);
		}
		function cargar_ticket_texto(id_ticket)
		{
			$('.detalle_ticket_texto').fadeIn();
			$.ajax({
				type:'GET',
				timeout: 10000,
				dataType: 'json',
				url:'https://www.appfornes.es/servicios-web/obtener_ticket_texto.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['cod_cliente']?>&id_ticket='+id_ticket+'&idioma=es&anticache='+(new Date()).getTime(),
				success:function(res, textStatus, XMLHttpRequest)
				{
					var resultado_ajax ='';
					if (res.validacion=='ok')
					{
						console.log('12345', res);
						//resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.ticket_detalle\').fadeOut();$(\'.tickets_listado\').fadeIn();$(\'.tickets_navegacion\').show();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';						
						//resultado_ajax+='<div class="ticket_formato_texto"><xmp>'+res.texto_ticket+'</xmp></div>';
						resultado_ajax+='<div class="ticket_formato_texto">'+res.texto_ticket+'</div>';
						//resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.ticket_detalle\').fadeOut();$(\'.tickets_listado\').fadeIn();$(\'.tickets_navegacion\').show();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
						//resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.detalle_ticket_texto\').fadeOut();return false;" class="btn btn-primary">CERRAR</a></div></div>';
						$('.detalle_ticket_texto .contenido_ticket').html(resultado_ajax);
						$('#tabla2').show();
						$('#tabla1').addClass('col-lg-7');
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
				}
			});	
		}

		$(document).ready(function() {
			
			obtener_chequeahorro();
			$('.footable').footable();
			$(".select2").select2({
				placeholder: "Selecciona municipio",
				allowClear: true
			});
			$.fn.datepicker.dates['es'] = {
				days: ["Domingo", "Lunes", "Martes", "Mi�rcoles", "Jueves", "Viernes", "S�bado", "Domingo"],
				daysShort: ["Dom", "Lun", "Mar", "Mi�", "Jue", "Vie", "S�b", "Dom"],
				daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
				months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
				monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
				today: "Hoy"
			};
			$('#fecha').datepicker({
				todayBtn: "linked",
				keyboardNavigation: false,
				forceParse: false,                				
				format: "dd/mm/yyyy",
				autoclose: true,
				language: 'es'
			});
				<?php
				if ($_GET['solapa']=='') print 'cargar_ahorro();';
				else if ($_GET['solapa']=='tickets') print 'cargar_tickets();';
				else if ($_GET['solapa']=='compras') print 'cargar_compras();';
				?>
				
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
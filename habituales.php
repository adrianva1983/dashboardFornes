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

    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Habituales</title>

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
	<?php
	require "herramientas/favicon.php";
	?>
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
    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>


    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
	
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
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                             <span class="text-muted text-xs block">Área privada <b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="?desconectar=si">Desconectar</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        +
                    </div>
                </li>
				<?php
					$seccion = "habituales";
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
					<div class="col-lg-12">
						<div class="ibox">
							<div class="ibox-title">
								<h5>Productos comprados habitualmente en los últimos 6 meses</h5>
								<div class="ibox-tools">
									<a class="collapse-link">
										<i class="fa fa-chevron-up"></i>
									</a>
									<a class="close-link">
										<i class="fa fa-times"></i>
									</a>
								</div>
							</div>
							<div class="ibox-content">								
								<table class="table table-hover">
								<thead>
									<tr>
										<th>Imagen</th>
										<th>EAN13</th>
										<th>REF</th>
										<th>Nombre</th>			
										<th>Comprado en 6 meses</th>											
										<th>Última compra</th>											
										<th>Evolución precios</th>
										<th>Precio medio</th>
									</tr>
								</thead>
								<tbody>
								<?php
								$array_valores = array();
								$requete = "SELECT * FROM `APP_productos_habituales`,`Productos` WHERE `APP_productos_habituales`.CODIGO_ARTICULO=`Productos`.COD_PRODUCTO AND `APP_productos_habituales`.CODIGO_TARJETA=".$_SESSION['cod_cliente']." AND `APP_productos_habituales`.ULTIMA_COMPRA>='".date('Y-m-d',strtotime("-12 months"))."' ORDER BY `APP_productos_habituales`.COUNT DESC LIMIT 0,30";
								$result = mysqli_query($db,$requete);
								$i = 0;
								if (($result) && (mysqli_num_rows($result)>0))
								{
									while ($listado = mysqli_fetch_object($result))
									{
										print '<tr>';
											if (url_exists('https://masymas-services.supermasymas.com/fotos/'.$listado->CODIGO_ARTICULO.'.jpg')) print '<td><img class="producto_imagen_listado" src="https://masymas-services.supermasymas.com/fotos/'.$listado->CODIGO_ARTICULO.'.jpg"></td>';
											else print '<td><img class="producto_imagen_listado" src="/Plantillas/Imagenes/globo-masymas.svg"></td>';
											print '<td>'.$listado->EAN13.'</td>';
											print '<td>'.$listado->CODIGO_ARTICULO.'</td>';											
											print '<td>'.$listado->DESCRIPCION.'</td>';
											print '<td><span class="label label-primary">'.$listado->COUNT.'</span></td>';
											$tmp_fecha = $listado->ULTIMA_COMPRA;
											$tmp_fecha =explode('-',$tmp_fecha);
											print '<td><span class="label label-default">'.$tmp_fecha[2].'/'.$tmp_fecha[1].'/'.$tmp_fecha[0].'</span></td>';
											print '<td>';
											print '<span id="sparkline_'.$i.'"></span>';
											$array_valores[$i] = '[5,6,7,9,9,5,3,2,2,4,6,7]';
											$requete2 = "SELECT * FROM `APP_evolucion_precio_articulo` WHERE `CODIGO_ARTICULO`='".$listado->CODIGO_ARTICULO."' ORDER BY Ano,Mes DESC LIMIT 0,6";
											$result2 = mysqli_query($db,$requete2);
											$array_valores[$i] = '[';
											$primero_array = true;
											$precio_medio = 0;
											$k = 1;
											if (($result2) && (mysqli_num_rows($result2)>0))
											{
												while ($listado2 = mysqli_fetch_object($result2))
												{
													if ($primero_array) $primero_array = false;
													else $array_valores[$i] .= ',';													
													$array_valores[$i] .= number_format($listado2->PrecioMedio,2,".","");
													$precio_medio += $listado2->PrecioMedio;
													$k++;
												}
											}
											$array_valores[$i] .= ']';
											print '</td>';
											print '<td>';											
											if ($precio_medio>0) 
											{
												$precio_medio = $precio_medio / ($k-1);
												print number_format($precio_medio,2,",","")."&euro;";
											}
											print '</td>';
										print '</tr>';
										$i++;
									}
								}
								?>
								</tbody>
								</table>
							</div>
						</div>
					</div>					
				</div>
            </div>
        <div class="footer">
            <div class="pull-right">
                <strong>masymas</strong>
            </div>
            <div>
                <strong>Copyright</strong> <a href="http://www.semillaproyectos.com">Semilla Proyectos Internet &copy;</a>
            </div>
        </div>
        </div>

    </div>

    <script>
        $(document).ready(function() {
			$(document).ready(function() {
				$('.footable').footable();
				$(".select2").select2({
					placeholder: "Selecciona municipio",
					allowClear: true
				});
			<?php			
			for ($i=0;$i<count($array_valores);$i++)
			{
				print '$("#sparkline_'.$i.'").sparkline('.$array_valores[$i].', {type: "bar",barColor:"#75AC1B",barWidth: 10,chartRangeMin: 0});';
			}			
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
        });
    </script>	
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-29717040-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
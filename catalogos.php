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

    <title>Catalogos masymas</title>

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
					$seccion = "catalogos";
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

			<iframe src="https://cdn.flipsnack.com/widget/v2/widget.html?hash=fvxpeupnr&bgcolor=2f4050&t=1548848045" width="100%" height="485" seamless="seamless" scrolling="no" frameBorder="0" allowFullScreen></iframe>
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
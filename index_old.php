<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>masymas área clientes</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	<!-- Sweet Alert -->
    <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
    <?php
	require "herramientas/favicon.php";
	?>

</head>

<body class="gray-bg">

    <div class="middle-box text-center lockscreen animated fadeInDown">
        <div>
            <div class="m-b-md">
            <img class="image logo-dashboard" src="/Plantillas/Imagenes/logo.svg">
            </div>
            <h3 tkey="masymas">masymas</h3>
            <p tkey="Cuadro_mandos" tkey="Cuadro_mandos">Cuadro de mandos para clientes.</p>
            <form class="m-t" role="form" action="dashboard.php" method="post">
                <div class="form-group">
					<input type="number" name="cod_cliente" class="form-control" placeholder="Número tarjeta" required="">
				</div>
				<div class="form-group">
                    <input type="text" name="dni" class="form-control" placeholder="DNI" required="">
                </div>
                <div class="form-group">
                    <select class="form-control select_idioma" name="select_idioma">
                        <option value="es">Castellano</option>
                        <option value="va">Valencià</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary block full-width">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<!-- Sweet alert -->
	<script src="js/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="lang/lang.js" type="text/javascript"></script>
	<script src="lang/es.js" type="text/javascript"></script>
	<script src="lang/en.js" type="text/javascript"></script>
	<script src="lang/va.js" type="text/javascript"></script>
	<script type='text/javascript'>	
	$(document).ready(function(){		
	<?php
	require($_SERVER['DOCUMENT_ROOT']."/dashboard/herramientas/aut_mensaje_error.inc.php");
	if ($_GET["error_login"]!="") $errores = $error_login_ms[$_GET["error_login"]];
	if ($errores!="")
	{
		echo '
				swal({
					title: "Fallo de acceso",
					text: "'.$errores.'",
					html: true,
					type: "error"
				});	';
	}
	?>
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

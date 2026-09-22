<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/conexion.php");
//Comprobamos el acceso
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/aut_verifica.inc.php");
if (!isset($_SESSION['cod_cliente'])||$_SESSION['cod_cliente']=='')
{
	header ("Location: $redir?error_login=5");
	exit;
}
if ($_SERVER['HTTP_REFERER'] == "")
{
	Header ("Location: index.php?error_login=1");
	die ("Error cod.:1 - Acceso incorrecto!");
	exit;
}
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
function ean13_check_digit($digits)
{
	//PROCEDIMIENTO PARA CALCULAR EL ÚLTIMO DÍGITO DEL EAN13
	//first change digits to a string so that we can access individual numbers
	$digits =(string)$digits;
	// 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
	$even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
	// 2. Multiply this result by 3.
	$even_sum_three = $even_sum * 3;
	// 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
	$odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
	// 4. Sum the results of steps 2 and 3.
	$total_sum = $even_sum_three + $odd_sum;
	// 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
	$next_ten = (ceil($total_sum/10))*10;
	$check_digit = $next_ten - $total_sum;
	return $digits . $check_digit;
}
function bigintval($value) 
{
	$value = trim($value);
	if (ctype_digit($value)) return $value;
	$value = preg_replace("/[^0-9](.*)$/", '', $value);
	if (ctype_digit($value)) return $value;
	return 0;
}
if ($_POST['compartida_tarjeta_fisica']==1||$_POST['compartida_tarjeta_online']==1)
{
	if (isset($_POST['formulario'])&&$_POST['DNI']=="") $errores.="El <strong>DNI</strong> es un campo obligatorio.<br/>";
	if (isset($_POST['formulario'])&&$_POST['Nombre']=="") $errores.="El <strong>nombre</strong> es un campo obligatorio.<br/>";
	if (isset($_POST['formulario'])&&$_POST['Apellidos']=="") $errores.="Los <strong>apellidos</strong> es un campo obligatorio.<br/>";
	if (isset($_POST['formulario'])&&$_POST['Telefono']=="") $errores.="El <strong>Tel&eacute;fono</strong> es un campo obligatorio.<br/>";
	if (isset($_POST['formulario'])&&$_POST['Provincia']=="") $errores.="El <strong>Provincia</strong> es un campo obligatorio.<br/>";	
	if (isset($_POST['formulario'])&&$_POST['tipo_direccion']=="") $errores.="El <strong>tipo de v&iacute;</strong> es un campo obligatorio.<br/>";	
	if (isset($_POST['formulario'])&&$_POST['Direccion']=="") $errores.="La <strong>direcci&oacute;n</strong> es un campo obligatorio.<br/>";	

	if ($errores =='')
	{
		$requete = "SELECT * FROM `Clientes` WHERE `DNI_CLIENTE`='".trim($_POST['DNI'])."'";
		$result = mysqli_query($db,$requete);	
		if (($result) && (mysqli_num_rows($result)>0))
		{
			$listado = mysqli_fetch_object($result);
			$requete = "UPDATE `Clientes` SET `COD_TARJETA_REFERIDO`='".$_SESSION['cod_cliente']."'";
			if ($_POST['tipo_direccion']!=''&&$_POST['tipo_direccion']!='Seleccionar') $requete.=", `TIPO_DIRECCION`='".$_POST['tipo_direccion']."'";
			else $requete.=", `TIPO_DIRECCION`=NULL";
			if ($_POST['Sexo']!=''&&$_POST['Sexo']!='Seleccionar') $requete.=", `SEXO`='".$_POST['Sexo']."'";
			else $requete.=", `SEXO`=NULL";		
			if ($_POST['NHogar']!=''&&$_POST['NHijos']!='Seleccionar') $requete.=", `PERS_UNIDAD_FAM`='".$_POST['NHogar']."'";
			else $requete.=", `PERS_UNIDAD_FAM`=NULL";
			if ($_POST['NHijos']!=''&&$_POST['NHijos']!='Seleccionar') $requete.=", `HIJOS_HOGAR`='".$_POST['NHijos']."'";
			else $requete.=", `HIJOS_HOGAR`=NULL";		
			if ($_POST['FechaNaciomiento'])
			{			
				$tmp_fecha = explode("/",$_POST['FechaNaciomiento']);
				$requete.=",`FECHA_NACIMIENTO`='".$tmp_fecha[2]."-".$tmp_fecha[1]."-".$tmp_fecha[0]."'";
			}
			$requete.=",`FECHA_MODIFICACION`='".date('Y-m-d')."'";
			$requete.=",`FECHA_MODIFICACION_APP`='".date('Y-m-d H:i:s')."'";
			$requete.=",`ORIGEN`='REFERENCIADO'";
			$requete.= " WHERE `Id`=".$listado->Id;			
			$id_cliente = $listado->Id;
			mysqli_query($db,$requete);	
		}
		else
		{
			$requete = "INSERT INTO `Clientes` (`COD_TARJETA_REFERIDO`,`DNI_CLIENTE`,`TIPO_DOCUMENTO`,`NOMBRE`,`APELLIDOS`,`NACIONALIDAD`,`DIRECCION`,`NUMERO`,`PISO_LETRA_OTROS`,`POBLACION`,`COD_POSTAL`,`PROVINCIA`,`TELEFONO`,`MOVIL`,`EMAIL`,`ENVIO_PUBLICIDAD`,`SEXO`,`ESTADO_CIVIL`,`PERS_UNIDAD_FAM`,`HIJOS_HOGAR`,`FECHA_NACIMIENTO`,`FECHA_MODIFICACION`,`FECHA_MODIFICACION_APP`,`FECHA_ALTA`,`ORIGEN`,`CODIGO_CAJERA`,`ALMACEN`) VALUES (";
			$requete.="'".$_SESSION['cod_cliente']."','".addslashes(trim($_POST['DNI']))."','".$_POST['tipo_documento']."','".addslashes(trim($_POST['Nombre']))."','".addslashes(trim($_POST['Apellidos']))."','".addslashes(trim($_POST['Nacionalidad']))."','".addslashes(trim($_POST['Direccion']))."','".addslashes(trim($_POST['Numero']))."','".addslashes(trim($_POST['PisoLetra']))."','".addslashes(trim($_POST['Municipio']))."','".addslashes(trim($_POST['CP']))."','".addslashes(trim($_POST['Provincia']))."','".addslashes(trim($_POST['Telefono']))."','".addslashes(trim($_POST['Movil']))."','".addslashes(trim($_POST['Email']))."'";
			$requete.=",NULL";
			if ($_POST['Sexo']!=''&&$_POST['Sexo']!='Seleccionar') $requete.=",'".$_POST['Sexo']."'";
			else $requete.=",NULL";		
			$requete.=",NULL"; //ESTADO_CIVIL
			if ($_POST['NHogar']!=''&&$_POST['NHogar']!='Seleccionar') $requete.=",'".$_POST['NHogar']."'";
			else $requete.=",NULL";
			if ($_POST['NHijos']!=''&&$_POST['NHijos']!='Seleccionar') $requete.=",'".$_POST['NHijos']."'";
			else $requete.=",NULL";
			if ($_POST['FechaNaciomiento'])
			{			
				$tmp_fecha = explode("/",$_POST['FechaNaciomiento']);
				$requete.=",'".$tmp_fecha[2]."-".$tmp_fecha[1]."-".$tmp_fecha[0]."'";
			}
			else $requete.=",NULL";
			$requete.=",'".date('Y-m-d')."'";
			$requete.=",'".date('Y-m-d H:i:s')."'";
			$requete.=",'".date('Y-m-d H:i:s')."'";
			$requete.=",'REFERENCIADO'";
			$requete.=",NULL"; //CAJERA
			$requete.=",NULL"; //TIENDA
			$requete.=");";			
			if (mysqli_query($db,$requete))
			{
				//CREADO CLIENTE
				$id_cliente = mysqli_insert_id($db);
			}
		}		
		//Cargamos los encabezados del mail
		$dominio = $_SERVER['SERVER_NAME'];
		$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= "From: $dominio <info@supermasymas.com>\r\n";
		if ($_POST['compartida_tarjeta_fisica']==1)
		{
			$headers .= "Reply-To: ".$_POST['Email']."\r\n";
			//Enviamos la confirmación de la solicitud de reserva al mail del usuario
			$msg = "<p>Solicitud de Tarjeta F&iacute;sica</p>";
			$msg .= "<p>";
			//$msg .= "<br/><strong>TARJETA QUE REFERENCIA</strong><hr><br>\n";
			//$msg .= "<strong>".$_SESSION['cod_cliente']."</strong>";
			$msg .= "<br/><strong>TITULAR TARJETA 1</strong><hr><br>\n";
			$msg .= "<strong>TIPO DOCUMENTO:</strong> ".$_POST['tipo_documento']."<br/>\n";	
			$msg .= "<strong>DNI:</strong> ".$_POST['DNI']."<br/>\n";	
			$msg .= "<strong>NOMBRE:</strong> ".$_POST['Nombre']."<br/>\n";
			$msg .= "<strong>APELLIDOS:</strong> ".$_POST['Apellidos']."<br/>\n";	
			$msg .= "<strong>FECHA DE NACIMIENTO:</strong> ".$_POST['FechaNaciomiento']."<br/>\n";
			$msg .= "<strong>SEXO:</strong> ".$_POST['Sexo']."<br/>\n";		
			$msg .= "<strong>NACIONALIDAD:</strong> ".$_POST['Nacionalidad']."<br/>\n";	
			$msg .= "<strong>VIA:</strong> ".$_POST['tipo_direccion']."<br/>\n";
			$msg .= "<strong>DIRECCI&Oacute;N:</strong> ".$_POST['Direccion']."<br/>\n";	
			$msg .= "<strong>NUMERO:</strong> ".$_POST['Numero']."<br/>\n";	
			$msg .= "<strong>PISO,LETRA,ETC:</strong> ".$_POST['PisoLetra']."<br/>\n";	
			$msg .= "<strong>POBLACI&Oacute;N / MUNICIPIO:</strong> ".$_POST['Municipio']."<br/>\n";	
			$msg .= "<strong>PROVINCIA:</strong> ".$_POST['Provincia']."<br/>\n";	
			$msg .= "<strong>CP:</strong> ".$_POST['CP']."<br/>\n";	
			$msg .= "<strong>M&Oacute;VIL:</strong> ".$_POST['Movil']."<br/>\n";
			$msg .= "<strong>TEL&Eacute;FONO:</strong> ".$_POST['Telefono']."<br/>\n";
			$msg .= "<strong>EMAIL:</strong> ".$_POST['Email']."<br/>\n";
			$msg .= "<strong>N&Uacute;MERO PERSONAS EN EL HOGAR:</strong> ".$_POST['NHogar']."<br/>\n";		
			$msg .= "<strong>N&Uacute;MERO HIJOS:</strong> ".$_POST['NHijos']."<br/>\n";		
			$msg .= "<strong>TIENDA HABITUAL:</strong> ".$_POST['TiendaHabitual']."<br/>\n";		
			$msg .= "</p>";	
			include($_SERVER['DOCUMENT_ROOT']."/Plantillas/alerta_correo.php");
			mail("susanag@supermasymas.com", "Solicitud de tarjeta referencia cliente condiciones especiales", $mensaje, $headers);			
			mail("christianp@supermasymas.com", "Solicitud de tarjeta referencia cliente condiciones especiales", $mensaje, $headers);			
			//mail("victor.estrada@semillaproyectos.com", "Solicitud de tarjeta referencia cliente condiciones especiales", $mensaje, $headers);				
			$mensaje.="Tarjeta correctamente socilitada.";
			if ($_POST['Email']!='')			
			{
				//MANDAR MAIL TARJETA FÍSICA A CLIENTE
			}
		}
		else if ($_POST['compartida_tarjeta_online']==1)
		{
			$maximo = '';
			$requete = "SELECT * FROM `APP_rango_tarjetas_nuevas`";
			$result = mysqli_query($db,$requete);
			if (($result) && (mysqli_num_rows($result)>0))
			{
				$listado = mysqli_fetch_object($result);
			}
			$requete2 = "SELECT MAX(CODIGO_TARJETA) AS Maximo FROM `ClientesTarjetas` WHERE `CODIGO_TARJETA`>=".$listado->Inicio." AND `CODIGO_TARJETA`<=".$listado->Fin;
			$result2 = mysqli_query($db,$requete2);			
			if (($result2) && (mysqli_num_rows($result2)>0))
			{
				$listado2 = mysqli_fetch_object($result2);		
				if ($listado2->Maximo!=NULL && $listado2->Maximo!='') $maximo = $listado2->Maximo;
				else $maximo = $listado->Inicio;
				$maximo = bigintval($maximo/10);
				$maximo++;
				$maximo = ean13_check_digit($maximo);
			}
			if ($id_cliente!='')
			{
				$cod_tarjeta = $maximo;
				$requete2 = "INSERT INTO ClientesTarjetas (`CODIGO_TARJETA`,`DNI_CLIENTE`,`FECHA_ALTA`,`IdCliente`) VALUES (".$cod_tarjeta.",'".trim($_POST['DNI'])."','".date('Y-m-d')."',".$id_cliente.");";
				if (mysqli_query($db,$requete2))
				{
					$mensaje.="Tarjeta correctamente asociada. N&uacute;mero de tarjeta asociada: ".$maximo;
				}
				else
				{
					$errores.="Problemas asociando la nueva tarjeta";
				}
				$requete2 = "INSERT INTO ClientesTarjetasGrupos (`CODIGO_TARJETA`,`CODIGO_GRUPO`,`FECHA_ALTA`,`FechaUltimaModificacion`) VALUES (".$maximo.",'1581','".date('Y-m-d')."','".date('Y-m-d')."');";
				mysqli_query($db,$requete2);
			}
			//MANDAR MAIL TARJETA ONLINE
			$headers .= "Reply-To: christianp@supermasymas.com\r\n";
			//Enviamos la confirmación de la solicitud de reserva al mail del usuario
			$msg = "<p>Estimado cliente,</p>";
			$msg .= "<p>";
			$msg .= "le hacemos llegar los datos relativos a su Tarjeta de Cliente de masymas supermercados con el prop&oacute;sito de disfrutar de todas sus ventajas a trav&eacute;s de nuestra aplicaci&oacute;n m&oacute;vil, la cual podr&aacute; descargar desde App Store para iOS y también desde Google Play para Android.</p>\n";
			$msg .= "<p>Enlace para tel&eacute;fonos iPhone: <a href='https://itunes.apple.com/es/app/masymas-supermercados/id1135172868?mt=8'>https://itunes.apple.com/es/app/masymas-supermercados/id1135172868?mt=8</a></p>\n";
			$msg .= "<p>Enlace para tel&eacute;fonos Android: <a href='https://play.google.com/store/apps/details?id=com.semillaproyectos.masymas'>https://play.google.com/store/apps/details?id=com.semillaproyectos.masymas</a></p>\n";
			$msg .= "<p><strong>DNI:</strong> ".$_POST['DNI']."<br/>\n";
			$msg .= "<p><strong>TARJETA:</strong> ".$maximo."<br/>\n";
			$msg .= "<p>Tenga en cuenta que la asociaci&oacute;n de su nueva tarjeta de cliente en nuestra App deber&aacute; ser realizada en &oacute;ptimas condiciones de conectividad, ya sea v&iacute;a Wi-Fi o datos 3G/4G, para que &eacute;sta sea llevada a cabo con &eacute;xito, introduciendo los datos a vincular tal y como le mostramos previamente.</p>\n";	
			$msg .= "<p>Sin otro particular y agradeciendo la confianza depositada en nosotros, reciba un afectuoso saludo.</p>\n";					
			include($_SERVER['DOCUMENT_ROOT']."/Plantillas/alerta_correo.php");
			//mail("susanag@supermasymas.com", "Solicitud de Tarjeta Family", $mensaje, $headers);
			mail("christianp@supermasymas.com", "Tarjeta app masymas", $mensaje, $headers);			
			mail($_POST['Email'], "Tarjeta app masymas", $mensaje, $headers);	
			//mail("victor.estrada@semillaproyectos.com", "Tarjeta app masymas", $mensaje, $headers);
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>masymas</title>

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
	<script>
	function nif_correcto(dni)
	{
		var numero;
		var letr;
		var letra;
		var expresion_regular_dni;
		expresion_regular_dni = /^\d{8}[a-zA-Z]$/;
		if(expresion_regular_dni.test (dni) == true)
		{
			numero = dni.substr(0,dni.length-1);
			letr = dni.substr(dni.length-1,1);
			numero = numero % 23;
			letra='TRWAGMYFPDXBNJZSQVHLCKET';
			letra=letra.substring(numero,numero+1);
			if (letra!=letr.toUpperCase()) 
			{				
				return false;				
			}
			else
			{
				
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	function dni_cargado()
	{
		var correcto = true;
		if ($('#tipo_documento').val()!='Otro')
		{
			correcto = nif_correcto($('#DNI').val());
			if (correcto){}
			else 
			{
				swal({
					title: "Problemas encontrados!",
					text: "DNI con formato incorrecto.",
					html: true,
					type: "error"
				});
			}			
		}
	}
	</script>
	<style>label{display:block;}</style>
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
					$seccion = "comparte";
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
			<div class="ibox">
			<?php
			$requete = "SELECT * FROM `Clientes` WHERE `COD_TARJETA_REFERIDO`=".$_SESSION['cod_cliente'];
			$result = mysqli_query($db,$requete);	
			if (($result) && (mysqli_num_rows($result)>0))
			{
				$listado = mysqli_fetch_object($result);
				echo '
				<div class="ibox-title">
					<h5>Condiciones promocionales ya compartidas.</h5>
				</div>
				<div class="ibox-content m-b-sm border-bottom">
					<p>Datos b&aacute;sicos de la persona con la que has compartido las promociones:</p>
					<span class="label label-info">'.$listado->NOMBRE.' '.$listado->APELLIDOS.'</span> <span class="label label-info">DNI: '.$listado->DNI_CLIENTE.'</span> ';
					/*
					$requete2 = "SELECT * FROM `ClientesTarjetas` WHERE `DNI_CLIENTE`='".$listado->DNI_CLIENTE."'";
					$result2 = mysql_query($requete2,$db);	
					if (($result2) && (mysql_num_rows($result2)>0))
					{
						$listado2 = mysql_fetch_object($result2);
						echo '<span class="label label-info">N&Uacute;MERO TARJETA: '.$listado2->CODIGO_TARJETA.'</span> ';
					}
					*/
				echo '</div>';
			}
			else
			{
				if ($_GET['tarjeta_fisica']==1||$_GET['tarjeta_online']==1)
				{
					echo '<form id="sky-form4" action="/dashboard/comparte.php" method="post" enctype="multipart/form-data"><div class="ibox-title">';
					if ($_GET['tarjeta_fisica']==1) echo '<h5>Enviar tarjeta f&iacute;sica</h5>';
					if ($_GET['tarjeta_online']==1) echo '<h5>Enviar tarjeta online</h5>';
					echo '</div>';
					if ($_GET['tarjeta_fisica']==1) echo '<input type="hidden" name="compartida_tarjeta_fisica" value=1/>';
					if ($_GET['tarjeta_online']==1) echo '<input type="hidden" name="compartida_tarjeta_online" value=1/>';
					echo '
					<div class="ibox-content m-b-sm border-bottom">
					<p><br/>Por favor, comprueba que los datos introducidos son correctos. En caso contrario es posible que el alta no se realice de forma efectiva.<br/></p>
					<div class="log-reg-block" id="bloque_datos_usuario">							
					<div class="login-input reg-input">
						<div class="row">
							<div class="col-sm-6">
								<section>
									<label class="input"> Nombre:
										<input required type="text" name="Nombre" value="'.$_POST['Nombre'].'" id="Nombre" placeholder="Nombre" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-6">
								<section>
									<label class="input"> Apellidos:
										<input required type="text" name="Apellidos"  value="'.$_POST['Apellidos'].'" id="Apellidos" placeholder="Apellidos" class="form-control">
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2"><label for="tipo_documento">TIPO DOCUMENTO<select name="tipo_documento" id="tipo_documento" class="form-control"><option value="NIF" selected>NIF</option><option value="Otro">Otro</option></select></label></div>
							<div class="col-sm-5">
								<section>
									<label class="input"> DNI:
										<input required type="text" name="DNI" value="'.$_POST['DNI'].'" id="DNI" placeholder="DNI" class="form-control" onblur="dni_cargado();">
									</label>
								</section>
							</div>
							<div class="col-sm-5">
								<section>
									<label class="input"> Nacionalidad:
										<input type="text" name="Nacionalidad"  value="'.$_POST['Nacionalidad'].'" id="Nacionalidad" placeholder="Nacionalidad" class="form-control">
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<section>
									<label class="input"> Fecha de nacimiento:
										<input required type="text" name="FechaNaciomiento" data-mask="99/99/9999" value="'.$_POST['FechaNaciomiento'].'" id="FechaNaciomiento" placeholder="dd/mm/yyyy" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-6">
								<section>
									<label class="input"> Sexo:						
										<select name="Sexo" id="Sexo" class="form-control"><option>Sexo</option><option value="Hombre"';							
										if ($_POST['Sexo']=='Hombre') print ' selected';
										echo '>Hombre</option><option value="Mujer"';
										if ($_POST['Sexo']=='Mujer') print ' selected';
										echo '>Mujer</option>
										</select>
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<section>
									<label class="input"> Teléfono
										<input required type="text" name="Telefono" value="'.$_POST['Telefono'].'" placeholder="Teléfono de contacto" class="form-control">
									</label>
								</section>									
							</div>
							<div class="col-sm-4">
								<section>
									<label class="input"> Móvil
										<input type="text" name="Movil" value="'.$_POST['Movil'].'" placeholder="Móvil de contacto" class="form-control">
									</label>
								</section>						
							</div>
							<div class="col-sm-4">
								<section>
									<label class="input"> Email:
										<input required type="email" id="Email" name="Email" value="'.$_POST['Email'].'" placeholder="Email" class="form-control">
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-2">
								<section>
									<label class="input"> Vía:
										<select required name="tipo_direccion" id="tipo_direccion" class="form-control"><option>Seleccionar</option>
										<option value="ALDEA">ALDEA</option><option value="ARRBL">ARRABAL</option><option value="AUT">AUTOPISTA o AUTOVIA</option><option value="AVDA">AVENIDA o AVINGUDA</option><option value="BARRI">BARRI</option><option value="BDA">BARRIADA</option><option value="BJDA">BAJADA, ILLA</option><option value="BLQ">BLOQUE</option><option value="BO">BARRIO</option>
										<option value="BRNCO">BARRANCO</option><option value="BXDA">BAIXADA</option><option value="CALLE">CALLE o CARRER</option><option value="CAMI">CAMI</option><option value="CAS">CASA, CASETA</option><option value="CC">CENTRO COMERCIAL</option><option value="CJA">CALLEJA, UELA</option><option value="CJON">CALLEJON</option><option value="CJTO">CONJUNTO</option>
										<option value="CLLZO">CALLIZO</option><option value="CMNO">CAMINO</option><option value="CMÑO">CAMIÑO</option><option value="CNIA">COLONIA</option><option value="CRA">CARRETERA, CARRERA o CORREDERA</option><option value="CRO">CARRERO</option><option value="CRRIL">CARRIL</option><option value="CSRIO">CASERIO</option><option value="CSTA">CUESTA</option>
										<option value="CTJO">CORTIJADA o CORTIJO</option><option value="CXON">CALLEXON</option><option value="EDIF">EDIFICIO</option><option value="ESTDA">ESTRADA</option><option value="EXTR">DESPOBLADO o DISEMINADO</option><option value="EXTR">EXTRAMUROS o EXTRARRADIO</option><option value="FINCA">FINCA</option><option value="GLTA">GLORIETA</option><option value="GRUP">GRUP</option>
										<option value="GRUPO">GRUPO</option><option value="KALE">KALE</option><option value="LLOC">LLOC</option><option value="LUGAR">LUGAR</option><option value="MASIA">MASIA</option><option value="MASO">MANSO</option><option value="PAGO">PAGO</option><option value="PARC">PARC</option><option value="PASEO">PASEO</option><option value="PBDO">POBLADO</option>
										<option value="PCTA">PLACETA</option><option value="PJDA">PUJADA</option><option value="PJE">PASAJE</option><option value="PLAÇA">PLAÇA</option><option value="PLAZA">PLAZA</option><option value="POLIG">POLIGONO</option><option value="PQUE">PARQUE</option><option value="PRAZA">PRAZA</option><option value="PRJE">PARAJE</option><option value="PROL">PROLONGACION</option>
										<option value="PSAXE">PASAXE</option><option value="PSEIG">PASSEIG</option><option value="PTDA">PARTIDA</option><option value="PTGE">PARATGE</option><option value="PTGE">PASSATGE</option><option value="PZLA">PLAZUELA</option><option value="PZTA">PLAZOLETA</option><option value="RAVAL">RAVAL</option><option value="RBLA">RAMBLA</option><option value="RESID">RESIDENCIAL</option>
										<option value="RIERA">RIERA</option><option value="RONDA">RONDA</option><option value="RUA">RUA</option><option value="RUELA">RUELA</option><option value="SBIDA">SUBIDA</option><option value="SENDA">SENDA</option><option value="TRANS">TRANSVERSAL</option><option value="TRAV">TRAVESA, TRAVESIA, TRAVESSERA o TRAVESSIA</option><option value="TSRA">TRASERA</option>
										<option value="URB">URBANIZACION</option><option value="VENAT">VEINAT</option><option value="VIA">VIA</option><option value="VREDA">VEREDA</option><option value="ZONA">ZONA</option>
										</select>
									</label>
								</section>
							</div>
							<div class="col-sm-4">
								<section>
									<label class="input"> Dirección:
										<input required type="text" id="Direccion" name="Direccion" value="'.$_POST['Direccion'].'" placeholder="Dirección" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-3">
								<section>
									<label class="input"> Número:
										<input type="text" id="Numero" name="Numero" value="'.$_POST['Numero'].'" placeholder="Numero" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-3">
								<section>
									<label class="input"> Piso, Letra, etc.:
										<input type="text" id="PisoLetra" name="PisoLetra" value="'.$_POST['PisoLetra'].'" placeholder="Piso, Letra, etc." class="form-control">
									</label>
								</section>
							</div>				
						</div>
						<div class="row">
							<div class="col-sm-4">
								<section>
									<label class="input"> Provincia:
										<input required type="text" id="Provincia" name="Provincia" value="'.$_POST['Provincia'].'" placeholder="Provincia" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-4">
								<section>
									<label class="input"> Municipio:
										<input required type="text" id="Municipio" name="Municipio" value="'.$_POST['Municipio'].'" placeholder="Municipio" class="form-control">
									</label>
								</section>
							</div>
							<div class="col-sm-4">
								<section>
									<label class="input"> CP:
										<input required type="text" id="CP" name="CP" value="'.$_POST['CP'].'" placeholder="CP" class="form-control">
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<section>
									<label class="input"> Nº de personas en el hogar:
										<select name="NHogar" id="NHogar" class="form-control"><option>Seleccionar</option>';
										for ($kkk=1;$kkk<11;$kkk++)
										{
											echo '<option value="'.$kkk.'"';
											if ($_POST['NHogar']==$kkk) print ' selected';
											echo '>'.$kkk.'</option>';
										}
										echo '
										</select>
									</label>
								</section>
							</div>
							<div class="col-sm-6">
								<section>
									<label class="input"> Nº de hijos que viven en el hogar:
										<select name="NHijos" id="NHijos" class="form-control"><option>Seleccionar</option>';
										for ($kkk=0;$kkk<11;$kkk++)
										{
											echo '<option value="'.$kkk.'"';
											if ($_POST['NHijos']==$kkk) print ' selected';
											echo '>'.$kkk.'</option>';
										}
										echo '
										</select>
									</label>
								</section>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<section>
									<label class="input">Tienda habitual compra:
									<input type="text" id="TiendaHabitual" name="TiendaHabitual" value="'.$_POST['TiendaHabitual'].'" placeholder="Tienda habitual" class="form-control">
									</label>
								</section>
							</div>
						</div>														
					</div>
					</div>
					</div>
					<div class="ibox-footer">
						<input type="submit" class="btn btn-primary btn-block" name="formulario" value="Enviar tarjeta">
					</div>
					</form>				
					';				
				}
				else
				{
					echo '
					<div class="ibox-title">
						<h5>Condiciones del servicio</h5>			
					</div>
					<div class="ibox-content m-b-sm border-bottom">
						<h4>Solicitud de nuevas tarjetas</h4>
						<p>Una vez realizada el alta de la nueva tarjeta, podrás realizar una invitación a otra unidad familiar de tu elección, respetando el parentesco de familiar en primer grado.</p>
						<p>Según lo estimes, el destinatario podrá utilizar una Tarjeta Virtual, a utilizar a través de nuestra App, o bien de una Tarjeta Física al uso.</p>
						<h4>Tarjeta Virtual</h4>
						<p>En el caso de que se realice una solicitud de una Tarjeta Virtual, te solicitaremos mediante un breve formulario los datos básicos para la asignación de la numeración de la nueva tarjeta. Al email designado llegará una invitación con los datos de acceso para su vinculación a la App.</p>
						<h4>Tarjeta Física (NO DISPONIBLE)</h4>
						<p>En el caso de que se realice una solicitud de Tarjeta Física, deberás registrar todos los datos solicitados en el formulario siguiente con el fin de hacer llegar dicha tarjeta mediante correo postal la dirección que se nos indique.</p>
					</div>
					<div class="ibox-footer">
						<div class="row">
						<div class="col-md-6">
							<a class="btn btn-lg btn-default btn-block">ENVIAR TARJETA FÍSICA<br/><small>NO DISPONIBLE</small></a>
						</div>
						<div class="col-md-6">
							<a href="?tarjeta_online=1" class="btn btn-lg btn-primary btn-block">ENVIAR TARJETA ONLINE<br/><small>Plazo 24 horas</small></a>
						</div>
						</div>
					</div>
					';
				}
			}
			?>
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
				$.fn.datepicker.dates['es'] = {
					days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
					daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
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
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
$requete = "SELECT * FROM `ClientesTarjetas` WHERE `id_card`='".$_SESSION['id_card']."'";			
$result = mysqli_query($db,$requete);
if (($result) && (mysqli_num_rows($result)>0))
{						
	$listado = mysqli_fetch_object($result);
	$_SESSION['nombre_origen'] = utf8_encode($listado->na_name);
}		
if (count($_POST)>2)
{
	if (!$_POST['na_name'])
	{		
		$errores .= "El nombre es un campo obligatorio.<br/>";
	}
	if(!$_POST['na_surname_1'] && !$_POST['na_surname_2'])
	{
		$errores .= "Los apellidos son un campo obligatorio.<br/>";
	}
	if(!$_POST['co_sex'])
	{			
		$errores .= "El sexo es un campo obligatorio.<br/>";
	}
	if(!$_POST['na_email'])
	{			
		$errores .= "El mail es un campo obligatorio.<br/>";
	}
	if(!$_POST['nu_mobile'] && $_POST['TelefonoAlternativo']=='')
	{			
		$errores .= "El móvil es un campo obligatorio.<br/>";
	}
	if(!$_POST['na_country'])
	{			
		$errores .= "La nacionalidad es un campo obligatorio.<br/>";
	}
	if(!$_POST['dt_birth'])
	{			
		$errores .= "La fecha de nacimiento es un campo obligatorio.<br/>";
	}
	if ($_POST['password']!=''&&$_POST['password']!=$_POST['password2'])
	{
		$errores .= "Las contraseñas deben ser coincidentes.<br/>";
	}
	if ($errores=='')
	{		
		$requete2 = "UPDATE `ClientesTarjetas` SET `na_name`='".strtoupper(utf8_decode($_POST['na_name']))."', `na_surname_1`='".strtoupper(utf8_decode($_POST['na_surname_1']))."', `na_surname_2`='".strtoupper(utf8_decode($_POST['na_surname_2']))."'";
		$requete2 .= ", `co_sex`='".$_POST['co_sex']."', `na_email`='".$_POST['na_email']."', `nu_mobile`='".$_POST['nu_mobile']."', `TelefonoAlternativo`='".$_POST['TelefonoAlternativo']."', `na_country`='".utf8_decode($_POST['na_country'])."', `dt_birth`='".$_POST['dt_birth']."'";
		$requete2 .= "WHERE `id_card`='".$_SESSION['id_card']."'";
		$errores_sql='';		
		if (mysqli_query($db,$requete2))
		{
			$momento_actual = date('Y-m-d H:i:s');
			$requete3 = "UPDATE `ClientesHogar` SET `co_street_type`='".addslashes(utf8_decode($_POST['tipo_direccion']))."',`na_street`='".addslashes(utf8_decode($_POST['direccion']))."',`nu_street`='".addslashes(utf8_decode($_POST['numero_registro']))."',`nu_floor`='".addslashes(utf8_decode($_POST['planta_registro']))."',`co_door`='".addslashes(utf8_decode($_POST['puerta_registro']))."',";
			$requete3 .= "`co_block`='".addslashes(utf8_decode($_POST['bloque_registro']))."',`co_stairs`='".addslashes(utf8_decode($_POST['escalera_registro']))."',`nu_postal_code`='".addslashes(utf8_decode($_POST['CP']))."',`na_province`='".addslashes(utf8_decode($_POST['provincia_registro']))."',`na_city`='".addslashes(utf8_decode($_POST['ciudad_registro']))."',`dt_updated`='".$momento_actual."',`nu_mobile`='".addslashes($_POST['nu_mobile'])."',`na_email`='".addslashes(utf8_decode($_POST['na_email']))."'";
			$requete3 .= " WHERE `id_home`=".$_SESSION['id_home'];
			if($_GET['desa']==1) mysqli_query($db,$requete3);
		}
		else
		{
			$errores_sql.=$requete2."<br/>";		
		}
		if ($_POST['password']!='')
		{
			$requete2 = "UPDATE `APP_login` SET `pass`='".hash('sha256',$_POST['password'])."' WHERE `nif`='".strtoupper($listado->co_nif)."'";					
			mysqli_query($db,$requete2);		
		}
		if ($errores_sql=='')
		{			
				$mensaje = "Tus datos de cliente han sido actualizados correctamente.";
		}
		else
		{
			$errores .= "Error actualizando base de datos de clientes.";			
		}
	}
}

?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title tkey="Perfil_masymas">Perfil masymas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	
	<!-- FooTable -->
    <link href="css/plugins/footable/footable.core.css" rel="stylesheet">
	
	<link href="css/plugins/select2/select2.min.css" rel="stylesheet">
	<link href="css/plugins/chosen/chosen.css" rel="stylesheet">	

	<!-- Sweet Alert -->
    <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">


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
	<script src="http://malsup.github.com/jquery.form.js"></script>
	
	<!-- Chosen -->
	<script src="js/plugins/chosen/chosen.jquery.js"></script>
	
	<!-- Select2 -->
	<script src="js/plugins/select2/select2.full.min.js"></script>	
	
	<!-- Sweet alert -->
	<script src="js/plugins/sweetalert/sweetalert.min.js"></script>

		<!-- FooTable -->
    <script src="js/plugins/footable/footable.all.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
	<script src="js/app.js" type="text/javascript"></script>

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
					$seccion = "perfil";
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
				
	<form class="" action="#" method="post" name="signForm" id="registroform" enctype="multipart/form-data">	
		<fieldset>		
			<div class="unidad_familiar_perfil"><h2 tkey="Datos_perfil">Datos de su perfil</h2></div>
			<div class="row">
				<div class="col-md-12">
					<div class="ibox">
						<div class="ibox-content datos_perfil">	
							<div class="input-group m-b"><label for="co_nif" tkey="DNI">DNI</label><input value="<?php print $listado->co_nif;?>" name="co_nif" id="co_nif" type="hidden" placeholder="Tu DNI" class="form-control"><input value="<?php print $listado->co_nif;?>" disabled name="co_nif2" id="co_nif2" type="text" placeholder="Tu DNI" class="form-control"></div>
							<div class="input-group m-b"><label for="na_name" tkey="Nombre">Nombre</label><input value="<?php print utf8_encode($listado->na_name);?>" required name="na_name" id="na_name" type="text" placeholder="Nombre" class="form-control"></div>
							<div class="input-group m-b"><label for="na_surname_1" tkey="PrimerApellido">Primer apellido</label><input value="<?php print utf8_encode($listado->na_surname_1);?>" required name="na_surname_1" id="na_surname_1" type="text" placeholder="Apellido 1" class="form-control"></div>
							<div class="input-group m-b"><label for="na_surname_2" tkey="SegundoApellido">Segundo apellido</label><input required  value="<?php print utf8_encode($listado->na_surname_2);?>" name="na_surname_2" id="na_surname_2" type="text" placeholder="Apellido 2" class="form-control"></div>
							<div class="input-group m-b"><label for="na_email" tkey="Mail">E-mail</label><input required value="<?php print utf8_encode($listado->na_email);?>" onchange="cambios_hechos_perfil = 1;" name="na_email" id="na_email" type="email" tkeyholder="Mail" placeholder="E-mail" class="form-control"></div>
							<div class="input-group m-b"><label for="nu_mobile" tkey="Movil">Móvil</label><input required value="<?php print utf8_encode($listado->nu_mobile);?>" onchange="cambios_hechos_perfil = 1;" name="nu_mobile" id="nu_mobile" type="number" tkeyholder="Moviñ" placeholder="Móvil" class="form-control"></div>

							<div class="input-group m-b"><label for="TelefonoAlternativo" tkey="TelefonoAlternativo">Teléfono Alternativo</label><input value="<?php print utf8_encode($listado->TelefonoAlternativo);?>" onchange="cambios_hechos_perfil = 1;" name="TelefonoAlternativo" id="TelefonoAlternativo" type="number" tkeyholder="TelefonoAlternativo" placeholder="Teléfono Alternativo" class="form-control"></div>

							<div class="input-group m-b"><label for="co_sex" tkey="Sexo">Sexo</label><select required  name="co_sex" id="co_sex" class="form-control"><option tkey="Seleccionar">Seleccionar</option><option value="M" tkey="Mujer" <?php if ($listado->co_sex=='M') print ' selected';?>>Femenino</option><option value="H" tkey="Hombre" <?php if ($listado->co_sex=='H') print ' selected';?>>Masculino</option></select></div>
							<div class="input-group m-b"><label for="na_country" tkey="Nacionalidad">Nacionalidad</label><input required maxlength="20"  value="<?php print utf8_encode($listado->na_country);?>" name="na_country" id="na_country" type="text" placeholder="Nacionalidad" class="form-control"></div>
							<?php
								if($_GET['desa']==1)
								{									
									$requete1 = "SELECT * FROM `ClientesHogar` WHERE `id_home` = ".$listado->id_home;			
									$result1 = mysqli_query($db,$requete1);
									if (($result1) && (mysqli_num_rows($result1)>0))
									{						
										$listado1 = mysqli_fetch_object($result1);
									}
									?>
									<div class="col-md-6">
										<div class="input-group m-b">
											<label for="tipo_direccion" tkey="TipoCalle">Tipo de vía</label>
											<select required name="tipo_direccion" id="tipo_direccion" class="form-control">
												<option value="" tkey="Seleccionar">Seleccionar</option>
												<option value="AGREGADO" <?php if ($listado1->co_street_type=='AGREGADO') print ' selected';?>>Agregado</option>
												<option value="ALAMEDA" <?php if ($listado1->co_street_type=='ALAMEDA') print ' selected';?>>Alameda</option>
												<option value="ALDEA" <?php if ($listado1->co_street_type=='ALDEA') print ' selected';?>>Aldea</option>
												<option value="APARTADO DE CORREOS" <?php if ($listado1->co_street_type=='APARTADO DE CORREOS') print ' selected';?>>Apartado de Correos</option>
												<option value="APARTAMENTO"	<?php if ($listado1->co_street_type=='APARTAMENTO') print ' selected';?>>Apartamento</option>
												<option value="ARRABAL" <?php if ($listado1->co_street_type=='ARRABAL') print ' selected';?>>Arrabal</option>
												<option value="ARROYO" <?php if ($listado1->co_street_type=='ARROYO') print ' selected';?>>Arroyo</option>
												<option value="AUTOVIA" <?php if ($listado1->co_street_type=='AUTOVIA') print ' selected';?>>Autovía</option>
												<option value="AVENIDA" <?php if ($listado1->co_street_type=='AVENIDA') print ' selected';?>>Avenida</option>
												<option value="AVINGUDA" <?php if ($listado1->co_street_type=='AVINGUDA') print ' selected';?>>Avinguda</option>
												<option value="BAIXADA" <?php if ($listado1->co_street_type=='BAIXADA') print ' selected';?>>Baixada</option>
												<option value="BAJADA" <?php if ($listado1->co_street_type=='BAJADA') print ' selected';?>>Bajada</option>
												<option value="BARRANCO" <?php if ($listado1->co_street_type=='BARRANCO') print ' selected';?>>Barranco</option>
												<option value="BARRI" <?php if ($listado1->co_street_type=='BARRI') print ' selected';?>>Barri</option>
												<option value="BARRIADA" <?php if ($listado1->co_street_type=='BARRIADA') print ' selected';?>>Barriada</option>
												<option value="BARRIO" <?php if ($listado1->co_street_type=='BARRIO') print ' selected';?>>Barrio</option>
												<option value="BLOQUE" <?php if ($listado1->co_street_type=='BLOQUE') print ' selected';?>>Bloque</option>
												<option value="CALLE" <?php if ($listado1->co_street_type=='CALLE') print ' selected';?>>Calle</option>
												<option value="CALLEJA" <?php if ($listado1->co_street_type=='CALLEJA') print ' selected';?>>Calleja</option>
												<option value="CALLEJON" <?php if ($listado1->co_street_type=='CALLEJON') print ' selected';?>>Callejón</option>
												<option value="CAMI" <?php if ($listado1->co_street_type=='CAMI') print ' selected';?>>Cami</option>
												<option value="CAMINO" <?php if ($listado1->co_street_type=='CAMINO') print ' selected';?>>Camino</option>
												<option value="CAMPA" <?php if ($listado1->co_street_type=='CAMPA') print ' selected';?>>Campa</option>
												<option value="CARRER" <?php if ($listado1->co_street_type=='CARRER') print ' selected';?>>Carrer</option>
												<option value="CARRERO" <?php if ($listado1->co_street_type=='CARRERO') print ' selected';?>>Carreró</option>
												<option value="CARRETERA" <?php if ($listado1->co_street_type=='CARRETERA') print ' selected';?>>Carretera</option>
												<option value="CARRIL" <?php if ($listado1->co_street_type=='CARRIL') print ' selected';?>>Carril</option>
												<option value="CASA" <?php if ($listado1->co_street_type=='CASA') print ' selected';?>>Casa</option>
												<option value="CASERIO" <?php if ($listado1->co_street_type=='CASERIO') print ' selected';?>>Caserío</option>
												<option value="CASETA" <?php if ($listado1->co_street_type=='CASETA') print ' selected';?>>Caseta</option>
												<option value="CHALET" <?php if ($listado1->co_street_type=='CHALET') print ' selected';?>>Chalet</option>
												<option value="COLEGIO" <?php if ($listado1->co_street_type=='COLEGIO') print ' selected';?>>Colegio</option>
												<option value="COLONIA" <?php if ($listado1->co_street_type=='COLONIA') print ' selected';?>>Colonia</option>
												<option value="CONJUNTO" <?php if ($listado1->co_street_type=='CONJUNTO') print ' selected';?>>Conjunto</option>
												<option value="CORREGIDOR" <?php if ($listado1->co_street_type=='CORREGIDOR') print ' selected';?>>Corregidor</option>
												<option value="CORTIJO" <?php if ($listado1->co_street_type=='CORTIJO') print ' selected';?>>Cortijo</option>
												<option value="CUESTA" <?php if ($listado1->co_street_type=='CUESTA') print ' selected';?>>Cuesta</option>
												<option value="DIPUTACION" <?php if ($listado1->co_street_type=='DIPUTACION') print ' selected';?>>Diputación</option>
												<option value="DISEMINADOR" <?php if ($listado1->co_street_type=='DISEMINADOR') print ' selected';?>>Diseminador</option>
												<option value="EDIFICIO" <?php if ($listado1->co_street_type=='EDIFICIO') print ' selected';?>>Edificio</option>
												<option value="ENTRADA" <?php if ($listado1->co_street_type=='ENTRADA') print ' selected';?>>Entrada</option>
												<option value="ESCALINATA" <?php if ($listado1->co_street_type=='ESCALINATA') print ' selected';?>>Escalinata</option>
												<option value="EXTRAMUROS" <?php if ($listado1->co_street_type=='EXTRAMUROS') print ' selected';?>>Extramuros</option>
												<option value="EXTRARRADIO" <?php if ($listado1->co_street_type=='EXTRARRADIO') print ' selected';?>>Extrarradio</option>
												<option value="FERROCARRIL" <?php if ($listado1->co_street_type=='FERROCARRIL') print ' selected';?>>Ferrocarril</option>
												<option value="FINCA" <?php if ($listado1->co_street_type=='FINCA') print ' selected';?>>Finca</option>
												<option value="GLORIETA" <?php if ($listado1->co_street_type=='GLORIETA') print ' selected';?>>Glorieta</option>
												<option value="GRAN VIA" <?php if ($listado1->co_street_type=='GRAN VIA') print ' selected';?>>Gran Vía</option>
												<option value="GRUP" <?php if ($listado1->co_street_type=='GRUP') print ' selected';?>>Grup</option>
												<option value="GRUPO" <?php if ($listado1->co_street_type=='GRUPO') print ' selected';?>>Grupo</option>
												<option value="HUERTA" <?php if ($listado1->co_street_type=='HUERTA') print ' selected';?>>Huerta</option>
												<option value="JARDINES" <?php if ($listado1->co_street_type=='JARDINES') print ' selected';?>>Jardines</option>
												<option value="LADO" <?php if ($listado1->co_street_type=='LADO') print ' selected';?>>Lado</option>
												<option value="LLOC" <?php if ($listado1->co_street_type=='LLOC') print ' selected';?>>Lloc</option>
												<option value="LUGAR" <?php if ($listado1->co_street_type=='LUGAR') print ' selected';?>>Lugar</option>
												<option value="MANZANA" <?php if ($listado1->co_street_type=='MANZANA') print ' selected';?>>Manzana</option>
												<option value="MASIA" <?php if ($listado1->co_street_type=='MASIA') print ' selected';?>>Masía</option>
												<option value="MERCADO" <?php if ($listado1->co_street_type=='MERCADO') print ' selected';?>>Mercado</option>
												<option value="MONTE" <?php if ($listado1->co_street_type=='MONTE') print ' selected';?>>Monte</option>
												<option value="MUELLE" <?php if ($listado1->co_street_type=='MUELLE') print ' selected';?>>Muelle</option>
												<option value="MUNICIPIO" <?php if ($listado1->co_street_type=='MUNICIPIO') print ' selected';?>>Municipio</option>
												<option value="PAGO" <?php if ($listado1->co_street_type=='PAGO') print ' selected';?>>Pago</option>
												<option value="PARAJE" <?php if ($listado1->co_street_type=='PARAJE') print ' selected';?>>Paraje</option>
												<option value="PARATGE" <?php if ($listado1->co_street_type=='PARATGE') print ' selected';?>>Paratge</option>
												<option value="PARC" <?php if ($listado1->co_street_type=='PARC') print ' selected';?>>Parc</option>
												<option value="PARCELA" <?php if ($listado1->co_street_type=='PARCELA') print ' selected';?>>Parcela</option>
												<option value="PARQUE" <?php if ($listado1->co_street_type=='PARQUE') print ' selected';?>>Parque</option>
												<option value="PARROQUIA" <?php if ($listado1->co_street_type=='PARROQUIA') print ' selected';?>>Parroquia</option>
												<option value="PARTIDA" <?php if ($listado1->co_street_type=='PARTIDA') print ' selected';?>>Partida</option>
												<option value="PASAJE" <?php if ($listado1->co_street_type=='PASAJE') print ' selected';?>>Pasaje</option>
												<option value="PASEO" <?php if ($listado1->co_street_type=='PASEO') print ' selected';?>>Paseo</option>
												<option value="PASSATGE" <?php if ($listado1->co_street_type=='PASSATGE') print ' selected';?>>Passatge</option>
												<option value="PASSEIG" <?php if ($listado1->co_street_type=='PASSEIG') print ' selected';?>>Passeig</option>
												<option value="PLAÇA" <?php if ($listado1->co_street_type=='PLAÇA') print ' selected';?>>Plaça</option>
												<option value="PLACETA" <?php if ($listado1->co_street_type=='PLACETA') print ' selected';?>>Placeta</option>
												<option value="PLAZA" <?php if ($listado1->co_street_type=='PLAZA') print ' selected';?>>Plaza</option>
												<option value="PLAZOLETA" <?php if ($listado1->co_street_type=='PLAZOLETA') print ' selected';?>>Plazoleta</option>
												<option value="PLAZUELA" <?php if ($listado1->co_street_type=='PLAZUELA') print ' selected';?>>Plazuela</option>
												<option value="POBLADO" <?php if ($listado1->co_street_type=='POBLADO') print ' selected';?>>Poblado</option>
												<option value="POLIGONO" <?php if ($listado1->co_street_type=='POLIGONO') print ' selected';?>>Polígono</option>
												<option value="PROLONGACION" <?php if ($listado1->co_street_type=='PROLONGACION') print ' selected';?>>Prolongación</option>
												<option value="PUENTE" <?php if ($listado1->co_street_type=='PUENTE') print ' selected';?>>Puente</option>
												<option value="PUERTA" <?php if ($listado1->co_street_type=='PUERTA') print ' selected';?>>Puerta</option>
												<option value="PUJADA" <?php if ($listado1->co_street_type=='PUJADA') print ' selected';?>>Pujada</option>
												<option value="QUINTA" <?php if ($listado1->co_street_type=='QUINTA') print ' selected';?>>Quinta</option>
												<option value="RAMAL" <?php if ($listado1->co_street_type=='RAMAL') print ' selected';?>>Ramal</option>
												<option value="RAMBLA" <?php if ($listado1->co_street_type=='RAMBLA') print ' selected';?>>Rambla</option>
												<option value="RAMPA" <?php if ($listado1->co_street_type=='RAMPA') print ' selected';?>>Rampa</option>
												<option value="RAVAL" <?php if ($listado1->co_street_type=='RAVAL') print ' selected';?>>Raval</option>
												<option value="RESIDENCIAL" <?php if ($listado1->co_street_type=='RESIDENCIAL') print ' selected';?>>Residencial</option>
												<option value="RIERA" <?php if ($listado1->co_street_type=='RIERA') print ' selected';?>>Riera</option>
												<option value="RINCON" <?php if ($listado1->co_street_type=='RINCON') print ' selected';?>>Rincón</option>
												<option value="RONDA" <?php if ($listado1->co_street_type=='RONDA') print ' selected';?>>Ronda</option>
												<option value="RUA" <?php if ($listado1->co_street_type=='RUA') print ' selected';?>>Rúa</option>
												<option value="SALIDA" <?php if ($listado1->co_street_type=='SALIDA') print ' selected';?>>Salida</option>
												<option value="SENDA" <?php if ($listado1->co_street_type=='SENDA') print ' selected';?>>Senda</option>
												<option value="SOLAR" <?php if ($listado1->co_street_type=='SOLAR') print ' selected';?>>Solar</option>
												<option value="SUBIDA" <?php if ($listado1->co_street_type=='SUBIDA') print ' selected';?>>Subida</option>
												<option value="TERRENOS" <?php if ($listado1->co_street_type=='TERRENOS') print ' selected';?>>Terrenos</option>
												<option value="TORRENTE" <?php if ($listado1->co_street_type=='TORRENTE') print ' selected';?>>Torrente</option>
												<option value="TRAVESA" <?php if ($listado1->co_street_type=='TRAVESA') print ' selected';?>>Travesa</option>
												<option value="TRAVESIA" <?php if ($listado1->co_street_type=='TRAVESIA') print ' selected';?>>Travesía</option>
												<option value="TRAVESSERA" <?php if ($listado1->co_street_type=='TRAVESSERA') print ' selected';?>>Travessera</option>
												<option value="TRAVESSIA" <?php if ($listado1->co_street_type=='TRAVESSIA') print ' selected';?>>Travessia</option>
												<option value="URBANIZACION" <?php if ($listado1->co_street_type=='URBANIZACION') print ' selected';?>>Urbanización</option>
												<option value="VEINAT" <?php if ($listado1->co_street_type=='VEINAT') print ' selected';?>>Veinat</option>
												<option value="VEREDA" <?php if ($listado1->co_street_type=='VEREDA') print ' selected';?>>Vereda</option>
												<option value="VIA" <?php if ($listado1->co_street_type=='VIA') print ' selected';?>>Vía</option>
												<option value="VIA PUBLICA" <?php if ($listado1->co_street_type=='VIA PUBLICA') print ' selected';?>>Vía Pública</option>
												<option value="ZONA" <?php if ($listado1->co_street_type=='ZONA') print ' selected';?>>Zona</option>
											</select>
										</div>
									</div>
									<div class="col-md-6 padd_l">
										<div class="input-group m-b">
											<label for="direccion" tkey="Direccion">Dirección</label>
											<input value="<?php print utf8_encode($listado1->na_street);?>" name="direccion" id="direccion" type="text" tkeyholder="Direccion" placeholder="Dirección" class="form-control">
										</div>
									</div>
									<div class="col-md-3">
										<div class="input-group m-b">
											<label for="numero_registro" tkey="Numero">Número</label>
											<input value="<?php print utf8_encode($listado1->nu_street);?>" name="numero_registro" id="numero_registro" type="text" tkeyholder="Numero" placeholder="Número" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="planta_registro" tkey="Planta">Planta</label>
											<input value="<?php print utf8_encode($listado1->nu_floor);?>" name="planta_registro" id="planta_registro" type="text" tkeyholder="Planta" placeholder="Planta" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="puerta_registro" tkey="Puerta">Puerta</label>
											<input value="<?php print utf8_encode($listado1->co_door);?>" name="puerta_registro" id="puerta_registro" type="text" tkeyholder="Puerta" placeholder="Puerta" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="bloque_registro" tkey="Bloque">Bloque</label>
											<input value="<?php print utf8_encode($listado1->co_block);?>" name="bloque_registro" id="bloque_registro" type="text" tkeyholder="Bloque" placeholder="Bloque" class="form-control">
										</div>
									</div>

									<div class="col-md-3">
										<div class="input-group m-b">
											<label for="escalera_registro" tkey="Escalera">Escalera</label>
											<input value="<?php print utf8_encode($listado1->co_stairs);?>" name="escalera_registro" id="escalera_registro" type="text" tkeyholder="Escalera" placeholder="Escalera" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="CP" tkey="CodigoPostal">CP</label>
											<input value="<?php print utf8_encode($listado1->nu_postal_code);?>" name="CP" id="CP" type="text" tkeyholder="CodigoPostal" placeholder="Planta" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="provincia_registro" tkey="Provincia">Provincia</label>
											<input value="<?php print utf8_encode($listado1->na_province);?>" name="provincia_registro" id="provincia_registro" type="text" tkeyholder="Provincia" placeholder="Provincia" class="form-control">
										</div>
									</div>
									<div class="col-md-3 padd_l">
										<div class="input-group m-b">
											<label for="ciudad_registro" tkey="Ciudad">Ciudad</label>
											<input value="<?php print utf8_encode($listado1->na_city);?>" name="ciudad_registro" id="ciudad_registro" type="text" tkeyholder="Ciudad" placeholder="Ciudad" class="form-control">
										</div>
									</div>
									
									<?php
								}
								print '<div class="input-group m-b"><label for="dt_birth" tkey="Nacimiento">Fecha nacimiento</label>';					
								print '<input required  value="'.$listado->dt_birth.'" name="dt_birth" id="dt_birth" type="date" placeholder="dd/mm/aaaa" class="form-control"></div>';					
							?>				
							<div class="input-group m-b"><label for="password" tkey="Contrasena">Contraseña</label><input  value="" name="password" id="password" type="password" placeholder="Cambiar contraseña" class="form-control"></div>
							<div class="input-group m-b"><label for="password" tkey="RepetirContrasena">Repetir Contraseña</label><input  value="" name="password2" id="password2" type="password" placeholder="Repetir contraseña" class="form-control"></div>

							
								<div class="input-group m-b">
									<label for="Idioma" tkey="IdiomaApp">IdiomaApp</label>
									<select required  name="Idioma" id="Idioma" class="form-control" onchange="cambiar_idioma($('#Idioma option:selected').val(), <?=$_SESSION['cod_cliente']?>);return false;">
										<option tkey="Seleccionar">Seleccionar</option>
										<option value="es" <?php if ($listado->Idioma=='es'||$listado->Idioma=='ES') print ' selected';?>>Castellano</option>
										<option value="va" <?php if ($listado->Idioma=='va'||$listado->Idioma=='VA') print ' selected';?>>Valencià</option>
										<option value="en" <?php if ($listado->Idioma=='en'||$listado->Idioma=='EN') print ' selected';?>>English</option> 
									</select>
								</div>

							
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<p class="comunicaciones" tkey="Comunicaciones">Comunicaciones</p>
					<div class="input_comunicaciones input-group m-b ibox-content">
						<div tkey="AceptoNotificaciones" class="col-xs-9">Acepto recibir notificaciones en la App</div>
						<div class="col-xs-3 notificaciones_app">
							<input type="checkbox" class="js-switch_ajustes_1" data-switchery="true" ">
							<span class="switchery switchery-default" style="box-shadow: rgb(204, 204, 204) 0px 0px 0px 0px inset; border-color: rgb(204, 204, 204); background-color: rgb(204, 204, 204); transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;">
								<small style="left: 0px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small>
							</span>
						</div>
					</div>
					<div class="input_comunicaciones input-group  m-b ibox-content">
						<div tkey="AceptoComunicacionesMail" class="col-xs-9">Acepto recibir comunicaciones por E-mail</div>
						<div class="col-xs-3 comunicaciones_mail">
							<input type="checkbox" checked="" class="js-switch_ajustes_2" data-switchery="true" ">
							<span class="switchery switchery-default" style="background-color: rgb(255, 202, 0); border-color: rgb(255, 202, 0); box-shadow: rgb(255, 202, 0) 0px 0px 0px 0px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;">
								<small style="left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small>
							</span>
						</div>
					</div>
					
					

					<div class="input_comunicaciones input-group  m-b ibox-content">
						<div tkey="ticket_sinPapel" class="col-xs-9">Recibir ticket en la App</div>
						<div class="col-xs-3 ticket_sinPapel">
							<input type="checkbox" checked="" class="js-switch_ajustes_3" data-switchery="true" ">
							<span class="switchery switchery-default" style="background-color: rgb(255, 202, 0); border-color: rgb(255, 202, 0); box-shadow: rgb(255, 202, 0) 0px 0px 0px 0px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;">
								<small style="left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small>
							</span>
						</div>
					</div>
					<div class="input_comunicaciones input-group  m-b ibox-content">
						<div tkey="ticket_inmediato" class="col-xs-9">Recibir el ticket inmediatamente</div>
						<div class="col-xs-3 ticket_inmediato">
							<input type="checkbox" checked="" class="js-switch_ajustes_4" data-switchery="true" ">
							<span class="switchery switchery-default" style="background-color: rgb(255, 202, 0); border-color: rgb(255, 202, 0); box-shadow: rgb(255, 202, 0) 0px 0px 0px 0px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;">
								<small style="left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small>
							</span>
						</div>
					</div>
					<div class="input_comunicaciones input-group  m-b ibox-content">
						<div tkey="soporte_remoto" class="col-xs-9">Permitir soporte remoto</div>
						<div class="col-xs-3 soporte_remoto">
							<input type="checkbox" checked="" class="js-switch_ajustes_5" data-switchery="true" ">
							<span class="switchery switchery-default" style="background-color: rgb(255, 202, 0); border-color: rgb(255, 202, 0); box-shadow: rgb(255, 202, 0) 0px 0px 0px 0px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;">
								<small style="left: 20px; background-color: rgb(255, 255, 255); transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small>
							</span>
						</div>
					</div>

					 
				</div>
				<div class="col-md-12">
					<div class="ibox">
						<div class="ibox-content br_25">			
							<div class="caja-boton bloque-boton-modificar-estilos">				
								<a onclick="comprobar_fecha();" name="guardarcambios" class="btn btn-primary" type="submit" tkey="GUARDARCAMBIOS">Guardar cambios</a>
							</div>
						</div>
					</div>
				</div>
			</div>		
		</fieldset>
	</form>			
	<?php
	
		//print_r($_SESSION);
		$requete = "SELECT * FROM `APP_tarjetas_asociadas` WHERE `id_card1`=".$_SESSION['cod_cliente'];
		$result = mysqli_query($db,$requete);
		print '<div class="unidad_familiar"><div class="row"><div class="col-md-10"><h2 tkey="Tarjetas_asociadas">Tarjetas asociadas</h2></div><div class="col-md-2"><h2><a class="btn btn-primary" onclick="asociar_tarjeta();return false" tkey="ASOCIARTARJETA">ASOCIAR TARJETA</h2></a></div></div><div class="row">';
		if (($result) && (mysqli_num_rows($result)>0))
		{
			while ($listado = mysqli_fetch_object($result))
			{
				print '<div class="col-md-4"><div class="tarjetas_asociadas tarjeta_asociada_num_'.$listado->id_card2.'"><div class="row">';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card2.'"><img src="img/svg/portada/tarjeta.svg"></div>';
					print '<div class="col-xs-6 tarjeta_asociada_num_'.$listado->id_card2.'"><h3>'.$listado->solonombre_tarjeta2.'</h3></div>';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card2.'"><a class="btn btn-primary" onclick="cambiar_tarjeta_asociada(\''.$listado->id_card2.'\');return false;"><i class="fa fa-refresh"></i></a></div>';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card2.'"><a class="btn btn-danger" onclick="borrar_tarjeta_asociada(\''.$listado->id_card2.'\', \''.$_SESSION['cod_cliente'].'\');return false;"><i class="fa fa-trash"></i></a></div>';
				print '</div></div></div>';
			}		
								
		}
		$requete = "SELECT * FROM `APP_tarjetas_asociadas` WHERE `id_card2`=".$_SESSION['cod_cliente'];
		$result = mysqli_query($db,$requete);
		if (($result) && (mysqli_num_rows($result)>0))
		{
			while ($listado = mysqli_fetch_object($result))
			{
				print '<div class="col-md-4"><div class="tarjetas_asociadas tarjeta_asociada_num_'.$listado->id_card1.'"><div class="row">';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card1.'"><img src="img/svg/portada/tarjeta.svg"></div>';
					print '<div class="col-xs-6 tarjeta_asociada_num_'.$listado->id_card1.'"><h3>'.$listado->solonombre_tarjeta1.'</h3></div>';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card1.'"><a class="btn btn-primary" onclick="cambiar_tarjeta_asociada(\''.$listado->id_card1.'\');return false;"><i class="fa fa-refresh"></i></a></div>';
					print '<div class="col-xs-2 tarjeta_asociada_num_'.$listado->id_card1.'"><a class="btn btn-danger" onclick="borrar_tarjeta_asociada(\''.$listado->id_card1.'\', \''.$_SESSION['cod_cliente'].'\');return false;"><i class="fa fa-trash"></i></a></div>';
				print '</div></div></div>';
			}		
								
		}
		print '</div></div>';
		print '<form method="POST" id="id_cambiar_tarjeta_asociada" action="dashboard.php">';
			print '<input type="hidden" id="cod_cliente" name="cod_cliente"/>';
			print '<input type="hidden" id="dni" name="dni"/>';
		print '</form>';
	
	?>	
			<?php
			$requete = "SELECT * FROM `ClientesTarjetas` WHERE `id_home`=".$_SESSION['id_home'];
			$result = mysqli_query($db,$requete);
			if (($result) && (mysqli_num_rows($result)>0))
			{
				$cont = 0;
				
				while ($listado = mysqli_fetch_object($result))
				{
					if ($_SESSION['cod_cliente']!=$listado->id_card&&$_SESSION['dni']!=$listado->co_nif&&$listado->co_nif!=''&&$listado->co_nif!=NULL)
					{
						if ($cont==0)
						{
							print '<div class="unidad_familiar"><h2 tkey="MIEMBROS_ASOCIADOS">Miembros familiares asociados</h2><div class="row">';
							$cont = 1;
						}
						print '<div class="col-md-4"><div class="miembro_familia"><h3>'.utf8_encode($listado->na_name.' '.$listado->na_surname_1.' '.$listado->na_surname_2).'</h3><p><span tkey="DNI">NIF:</span>: '.$listado->co_nif.'</p><p><span tkey="NUMTARJETA">Nº TARJETA:</span> '.$listado->id_card.'</p></div></div>';
						
					}
				}
				if ($cont==1)
				{
					print '</div></div>';
					
				}
			}
			?>			
				
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
	
	<div class="modal fade" id="miModal_dni_vincular" tabindex="-1" role="dialog" aria-labelledby="miModal_dni_vincularLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<div class="registrate center_text">
					<h2 tkey="Vincular">Vincular</h2>
					<p tkey="explicacion-tarjeta-a-mano">Si vinculas otra tarjeta podrás cambiar de tarjeta utilizando el mismo terminal móvil con un botón directo en la barra superior.</p>
				</div>
			</div>
			<div class="modal-body">
			<div class="datos_tabla" id="datos_cliente_nuevo">            		
			<div class="row 111">			
				<div class="col-xs-10 col-xs-offset-1 data">
					<input tkeyholder="introduceDNI" id="dni_vinculacion" onblur="$('#dni_vinculacion').val($('#dni_vinculacion').val().toUpperCase());" type="text" placeholder="Introduce tu DNI o NIE" class="form-control">
				</div>
				<div class="col-xs-10 col-xs-offset-1 data">
					<input tkeyholder="numero_de_tarjeta" onfocus="convertir_en_solo_numero('numero_tarjeta_asociacion');" onkeyup="convertir_en_solo_numero('numero_tarjeta_asociacion');" id="numero_tarjeta_asociacion" pattern="\d*" inputmode="numeric" type="text" placeholder="Tu número de tarjeta" class="form-control input_numero_tarjeta">
				</div>
			</div>
		</div>
		<div class="row aviso_legal_registro_nuevo">
			<div class="col-xs-10 col-xs-offset-1 boton">
				<a tkey="EMPEZARPROCESOVINCULACION" class="btn btn-primary btn-block" href="#" onclick="ya_tengo_tarjeta1('<?=$_SESSION['cod_cliente']?>', '<?=$_SESSION['dni']?>', '<?=$_SESSION['nombre_origen']?>', '<?=$_SESSION['cod_cliente_digital']?>');return false;">
					EMPEZAR PROCESO VINCULACIÓN
				</a>
			</div>
			<div class="col-xs-10 col-xs-offset-1 boton">
				<a tkey="CANCELAR" class="btn btn-white btn-block" href="#" onclick="volver_perfil();asociando_tarjeta = 0;return false;">
					CANCELAR
				</a>
			</div>
			</div>
			<!--<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Send message</button>
			</div>-->
			</div>
		</div>
	</div>

	<script src="/dashboard/lang/lang.js" type="text/javascript"></script>
	<script src="/dashboard/lang/es.js" type="text/javascript"></script>
	<script src="/dashboard/lang/en.js" type="text/javascript"></script>
	<script src="/dashboard/lang/va.js" type="text/javascript"></script>
	<script src="js/plugins/switchery/switchery.js"></script>
    <script>
		function cargar_datos_personales()
		{
			$.ajax({
				type:'GET',
				timeout: 10000,
				dataType: 'json',
				url:'https://www.appfornes.es/servicios-web/cargar_datos_personales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+<?=$_SESSION['id_card']?>+'&idioma=es&anticache='+(new Date()).getTime(),
				success:function(res, textStatus, XMLHttpRequest)
				{	
					if (res.validacion=='ok')
					{
						$('#dni_perfil').val(res.NIF);
						$('#nombre').val(res.NOMBRE);
						$('#apellido1').val(res.APELLIDO1);
						$('#apellido2').val(res.APELLIDO2);
						$('#movil').val(res.MOVIL);
						$('#email').val(res.EMAIL);
						$('#fecha_nacimiento').val(res.FECHA_NACIMIENTO);
						if (res.FECHA_NACIMIENTO!='')
						{
							var tmp_birth = res.FECHA_NACIMIENTO.split('-');					
							$('select[name="birthday[day]"]').val(parseInt(tmp_birth[2]));
							$('select[name="birthday[month]"]').val(parseInt(tmp_birth[1]));
							$('select[name="birthday[year]"]').val(parseInt(tmp_birth[0]));
						}
						else
						{
							$('select[name="birthday[day]"]').val('');
							$('select[name="birthday[month]"]').val('');
							$('select[name="birthday[year]"]').val('');					
						}
						console.log('cargar_datos_personales', res);
						localStorage.setItem('idioma',res.Idioma);
						if (localStorage.getItem('idioma')!=''&&localStorage.getItem('idioma')!='es')
						{
							console.log('11111111111', localStorage.getItem('idioma'));
							if (localStorage.getItem('idioma')=='va') 
							{
								translate(lang_va);
								temp_lang = lang_va;
								idioma = 'va';				
							}
							else if (localStorage.getItem('idioma')=='en') 
							{
								console.log('2222222222222')
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
						$('#nacionalidad').val(res.NACIONALIDAD);
						$('#sexo').val('');				
						$('#sexo option[value="'+res.SEXO+'"]').prop('selected','selected');
						var html_result = '';
						if (res.Digital==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_3"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_3"/>';
						$('.todo_online').html(html_result);
						html_result = '';
						if (res.NotificacionesAPP==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_1"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_1"/>';
						$('.notificaciones_app').html(html_result);
						html_result = '';				
						if (res.ComunicacionesMail==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_2"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_2"/>';
						$('.comunicaciones_mail').html(html_result);
						html_result = '';				
						if (res.TicketSinPapel==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_3"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_3"/>';
						$('.ticket_sinPapel').html(html_result);
						html_result = '';				
						if (res.TicketInmediato==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_4"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_4"/>';
						$('.ticket_inmediato').html(html_result);
						html_result = '';				
						if (res.PermiteSuplantareCommerce==1) html_result = '<input type="checkbox" checked class="js-switch_ajustes_5"/>';
						else html_result = '<input type="checkbox" class="js-switch_ajustes_5"/>';
						$('.soporte_remoto').html(html_result);


						var elem_ajustes1=document.querySelector('.js-switch_ajustes_1');
						var switchery_ajustes1 = new Switchery(elem_ajustes1, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes1.onchange = function(){
							if (elem_ajustes1.checked) activar_ajuste('ajuste_notificaciones');
							else desactivar_ajuste('ajuste_notificaciones');			
						};
						var elem_ajustes2=document.querySelector('.js-switch_ajustes_2');
						var switchery_ajustes2 = new Switchery(elem_ajustes2, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes2.onchange = function(){
							if (elem_ajustes2.checked) activar_ajuste('ajuste_mail');
							else desactivar_ajuste('ajuste_mail');
						};
						var elem_ajustes3=document.querySelector('.js-switch_ajustes_3');
						var switchery_ajustes3 = new Switchery(elem_ajustes3, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes3.onchange = function(){
							console.log('Entro33333333333333333')
							if (elem_ajustes3.checked) activar_ajuste('ajuste_TicketSinPapel');
							else desactivar_ajuste('ajuste_TicketSinPapel');
						};
						var elem_ajustes4=document.querySelector('.js-switch_ajustes_4');
						var switchery_ajustes4 = new Switchery(elem_ajustes4, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes4.onchange = function(){
							console.log('Entro33333333333333333')
							if (elem_ajustes4.checked) activar_ajuste('ajuste_TicketInmediato');
							else desactivar_ajuste('ajuste_TicketInmediato');
						};
						var elem_ajustes5=document.querySelector('.js-switch_ajustes_5');
						var switchery_ajustes5 = new Switchery(elem_ajustes5, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes5.onchange = function(){
							console.log('Entro33333333333333333')
							if (elem_ajustes5.checked) activar_ajuste('ajuste_PermiteSuplantareCommerce');
							else desactivar_ajuste('ajuste_PermiteSuplantareCommerce');
						};
						/*
						var elem_ajustes3=document.querySelector('.js-switch_ajustes_3');
						var switchery_ajustes3 = new Switchery(elem_ajustes3, { color: '#FFCA00', secondaryColor    : '#cccccc', });	
						elem_ajustes3.onchange = function(){
							if (elem_ajustes3.checked) activar_ajuste('ajuste_online');
							else desactivar_ajuste('ajuste_online');
						};
						*/
						//Recargo el componente de desplegables de fecha de nacimiento para que coja los nuevos valores
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					$('.pagina-perfil .principal').html('<div class="alert alert-danger">'+temp_lang['sin_conexion_perfil']+'</div>');
				}
			});	
		}
		function activar_ajuste(item)
		{
			idioma = $('#Idioma option:selected').val();
			var url='https://www.appfornes.es/servicios-web/ajustes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&item='+item+'&activar=1&cod_cliente='+cod_cliente_digital+'&idioma='+idioma+'&anticache='+(new Date());
			console.log('url00000', url);
			$.ajax({
				type:'GET',
				timeout: 3000,
				dataType: 'json',
				url:'https://www.appfornes.es/servicios-web/ajustes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&item='+item+'&activar=1&cod_cliente='+<?=$_SESSION['id_card']?>+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
				success:function(res, textStatus, XMLHttpRequest)
				{
					console.log('activar_ajuste', res);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
				}
			});		
		}
		function desactivar_ajuste(item)
		{
			$.ajax({
				type:'GET',
				timeout: 3000,
				dataType: 'json',
				url:'https://www.appfornes.es/servicios-web/ajustes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&item='+item+'&activar=0&cod_cliente='+<?=$_SESSION['id_card']?>+'&anticache='+(new Date()).getTime(),
				success:function(res, textStatus, XMLHttpRequest)
				{
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
				}
			});		
		}
		function comprobar_fecha()
		{
			var fecha_inferior = new Date('1910-01-01');
			var fecha_superior = new Date();
			var fecha_analizar = new Date($('#dt_birth').val());
			if (fecha_analizar<fecha_inferior||fecha_analizar>fecha_superior)
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang['error_nacimiento_rango'],
					html: true
				});
			} 
			else $('#registroform').submit();
		}
        $(document).ready(function() {
			cargar_datos_personales();
			if (localStorage.getItem('idioma')!=''&&localStorage.getItem('idioma')!='es')
			{
				console.log('11111111111', localStorage.getItem('idioma'));
				if (localStorage.getItem('idioma')=='va') 
				{
					translate(lang_va);
					temp_lang = lang_va;
					idioma = 'va';				
				}
				else if (localStorage.getItem('idioma')=='en') 
				{
					console.log('2222222222222')
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
			
				$('.footable').footable();
				var config = {
					'.chosen-select'           : {},
					'.chosen-select-deselect'  : {allow_single_deselect:true},
					'.chosen-select-no-single' : {disable_search_threshold:10},
					'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
					'.chosen-select-width'     : {width:"95%"}
					}
				for (var selector in config) {
					$(selector).chosen(config[selector]);
				}
			<?php
			if ($mensaje!="")
			{
				echo '
						swal({
							title: "Operación realizada con éxito",
							text: "'.$mensaje.'",
							html: true,
							type: "success"
						});	';
			}
			if ($errores!="")
			{
				echo '
						swal({
							title: "Problemas encontrados!",
							text: "'.$errores.'",
							html: true,
							type: "error"
						});	';
			}
			?>
        });

		function cambiar_tarjeta_asociada(id_card)
		{
			if (id_card=='')
			{
				if (num_tarjetas_asociadas>1)
				{
					var resultado_ajax = '';
					$.each(tarjetas_asociadas, function(key, value)
					{
						temp_num_tarjeta = value.id_card;
						//resultado_ajax+='<div class="col-xs-2 tarjeta_asociada_num_'+value.id_card+'"><img src="img/svg/portada/tarjeta.svg"></div><div class="col-xs-8 tarjeta_asociada_num_'+value.id_card+'">'+value.solonombre+'</div><div class="col-xs-2 tarjeta_asociada_num_'+value.id_card+'"><a class="btn btn-primary" onclick="cambiar_tarjeta_asociada(\''+value.id_card+'\');return false;"><i class="fa fa-refresh"></i></a></div>';				
						resultado_ajax+='<div class="row" style="background: #EFEFEF;margin: 10px;line-height: 32px;border-radius: 10px;"><div class="col-xs-2 tarjeta_asociada_num_'+value.id_card+'"></div><div class="col-xs-8 tarjeta_asociada_num_'+value.id_card+'">'+value.solonombre+'</div><div class="col-xs-2 tarjeta_asociada_num_'+value.id_card+'"><a class="btn btn-primary" onclick="cambiar_tarjeta_asociada(\''+value.id_card+'\');return false;"><i class="fa fa-refresh"></i></a></div></div>';				
						
					});	
					swal({
						title: temp_lang["Selecciona tarjeta"],
						text: resultado_ajax,
						html: true,
						type: "warning"
					});
				}
			}
			else
			{
				var numeroAperturas = localStorage.getItem('numeroAperturas');
				var time_vinculacion = localStorage.getItem('time_vinculacion');
				localStorage.clear();
				if (db!=null) resetea_sqlite();
				cod_cliente = id_card;	
				localStorage.setItem('numeroAperturas',numeroAperturas);
				localStorage.setItem('time_vinculacion',time_vinculacion);
				$.each(tarjetas_asociadas, function(key, value)
				{
					if (value.id_card==id_card)
					{
						console.log('9999999999999999', value);
						$('#cod_cliente').val(id_card);
						$('#dni').val(value.dni_cliente);
						localStorage.setItem('numero_tarjeta',id_card);
						localStorage.setItem('solo_nombre',value.solonombre);
						localStorage.setItem('cod_cliente_digital',value.id_card_digital);
						localStorage.setItem('cod_cliente',id_card);
						localStorage.setItem('dni_cliente',value.dni_cliente);
						localStorage.setItem('id_home',value.id_home);
					}
				});
				if (idioma!='') localStorage.setItem('idioma',idioma);
				//window.location.reload(true);
				$('#id_cambiar_tarjeta_asociada').submit();
			}
		}

		obtener_tarjetas_asociadas('<?=$_SESSION['cod_cliente']?>', '<?=$_SESSION['select_idioma']?>');
    </script>	
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
<?php
if (isset($_GET["desconectar"])) $desconectar = $_GET["desconectar"];
else $desconectar = "";
if (isset($_GET["cerrar_sesion"])) $cerrar_sesion = $_GET["cerrar_sesion"];
else $cerrar_sesion = "";
// Motor autentificación usuarios.

// Cargar datos conexion.
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/conexion.php");


// chequear página que lo llama para devolver errores a dicha página.

$url = explode("?",$_SERVER['HTTP_REFERER']);
$pag_referida=$url[0];
$redir="http://fornes.semillaproyectos.com/dashboard/";

if ($_GET['token_oauth']!='')
{
	$requete = "SELECT * FROM `APP_login` WHERE `token_oauth`='".addslashes($_GET['token_oauth'])."' AND `token_caducidad`>='".date('Y-m-d H:i:s')."'";	
	$result = mysqli_query($db,$requete);
	if (($result) && (mysqli_num_rows($result)>0))
	{		
		$listado = mysqli_fetch_object($result);	
		$_POST['dni'] = $listado->nif;
		$requete2 = "SELECT * FROM `ClientesTarjetas` WHERE `co_nif`='".$listado->nif."' AND `is_erased`=0";				
		$result2 = mysqli_query($db,$requete2);
		$tarjeta_descuento = false;
		$tarjeta_dni = false;		
		$id_home = '';
		$cod_cliente = '';
		$cod_home = '';
		if (($result2) && (mysqli_num_rows($result2)>0))
		{
			while ($listado2 = mysqli_fetch_object($result2))
			{				
				if (!$tarjeta_descuento)
				{
					if ($listado2->nu_discount!='0'&&$listado2->nu_discount!='')
					{
						$tarjeta_descuento = true;						
					}
					$cod_cliente = $listado2->id_card;
					$cod_home = $listado2->id_home;
				}
			}
		}
		$_POST['cod_cliente'] = $cod_cliente;
	}		
}
if ($_POST['cod_cliente']!='') $cod_cliente = $_POST['cod_cliente'];
if (strlen($cod_cliente)>9)
{
	$cod_cliente = substr($cod_cliente,3,9);
}

if ($_POST['dni']!='') $dni = $_POST['dni'];
if ($_POST['select_idioma']!='') $select_idioma = $_POST['select_idioma'];

if ($_POST['userName']!='') $userName = $_POST['userName'];
if ($_POST['password']!='') $password = $_POST['password'];
if ($_POST['select_idioma']!='') $select_idioma = $_POST['select_idioma'];
// Chequeamos si se está autentificandose un usuario por medio del formulario
if (isset($dni) && isset($cod_cliente) && $dni!='' && $cod_cliente!='') 
{	
	// realizamos la consulta a la BD para chequear datos del Usuario.
	$usuario_consulta = mysqli_query($db,"SELECT * FROM ClientesTarjetas WHERE id_card='".$cod_cliente."' AND `co_nif`='".$dni."' ORDER BY `ClientesTarjetas`.`dt_registered` DESC") or die(header ("Location:  $redir?error_login=1"));
	// miramos el total de resultado de la consulta (si es distinto de 0 es que existe el usuario)
	if (mysqli_num_rows($usuario_consulta) != 0) 
	{
		// eliminamos barras invertidas y dobles en sencillas
		// En este punto, el usuario ya esta validado.
		// Grabamos los datos del usuario en una sesion.

		 // le damos un mobre a la sesion.
		session_name("AutenticadorPanel");
		// Paranoia: decimos al navegador que no "cachee" esta página.
		session_cache_limiter('nocache,private');
		 // incia sessiones
		session_start();

		

		// Asignamos variables de sesión con datos del Usuario para el uso en el
		// resto de páginas autentificadas.

		// definimos usuarios_id como IDentificador del usuario en nuestra BD de usuarios
		
		$listado = mysqli_fetch_object($usuario_consulta);
		$_SESSION['token_oauth']=$_GET['token_oauth'];
		$_SESSION['id_home']= $listado->id_home;
		$usuario_consulta1 = mysqli_query($db,"SELECT * FROM ClientesTarjetas WHERE `co_nif`='".$dni."' AND `is_erased`=0 ORDER BY `ClientesTarjetas`.`dt_registered` DESC");
		$listado1 = mysqli_fetch_object($usuario_consulta1);
		$cod_cliente= $listado1->id_card;
		$_SESSION['cod_cliente']=$cod_cliente;
		$_SESSION['cod_cliente_digital']= $listado1->id_card;
		$_SESSION['id_card']= $cod_cliente;
		if ($select_idioma!='') $_SESSION['select_idioma'] = $select_idioma;
		else $_SEESSION['select_idioma'] = 'es';
		// definimos usuario_nivel con el Nivel de acceso del usuario de nuestra BD de usuarios
		$_SESSION['dni']=$dni;
		$requete = "SELECT * FROM `APP_tarjetas_activas` WHERE `CODIGO_TARJETA`=".$cod_cliente;
		$result = mysqli_query($db,$requete);
		if (($result) && (mysqli_num_rows($result)>0))
		{
			$requete = "UPDATE `APP_tarjetas_activas` SET `FechaUltimoAcceso`='".date('Y-m-d')."' WHERE `CODIGO_TARJETA`=".$cod_cliente;
			mysqli_query($db,$requete);
		}
		else 
		{
			$requete = "INSERT INTO `APP_tarjetas_activas` (`CODIGO_TARJETA`,`FechaUltimoAcceso`) VALUES (".$cod_cliente.",'".date('Y-m-d')."');";
			mysqli_query($db,$requete);
			$ch = curl_init("https://fornes.supermasymas.com/generar_top_productos.php");
		}
		// Hacemos una llamada a si mismo (scritp) para que queden disponibles
		// las variables de session en el array asociado $HTTP_...
		$pag=$_SERVER['PHP_SELF'];
		//Header ("Location: $pag?");		
		//exit;
	}
	else 
	{
		// si no esta el nombre de usuario en la BD o el password ..
		// se devuelve a pagina q lo llamo con error
		Header ("Location: $redir?error_login=2");
		exit;
	}
} 
else if (isset($userName) && isset($password) && $userName!='' && $password!='')
{
	// realizamos la consulta a la BD para chequear datos del Usuario.
	$usuario_consulta = mysqli_query($db,"SELECT * FROM APP_login WHERE `nif`='".$userName."'") or die(header ("Location:  $redir?error_login=1"));
	if (mysqli_num_rows($usuario_consulta) != 0)
	{
		$listado = mysqli_fetch_object($usuario_consulta);
		if (hash('sha256',$password) == $listado->pass)
		//if ($password == $listado->pass)
		{
			// le damos un mobre a la sesion.
			session_name("AutenticadorPanel");
			// Paranoia: decimos al navegador que no "cachee" esta página.
			session_cache_limiter('nocache,private');
			// incia sessiones
			session_start();
			

			$_SESSION['token_oauth']=$_GET['token_oauth'];
			$usuario_consulta1 = mysqli_query($db,"SELECT * FROM ClientesTarjetas WHERE `co_nif`='".$userName."' AND `is_erased`=0 ORDER BY `ClientesTarjetas`.`dt_registered` DESC");
			$listado1 = mysqli_fetch_object($usuario_consulta1);
			$cod_cliente= $listado1->id_card;
			
			$_SESSION['id_home']= $listado1->id_home;
			$_SESSION['cod_cliente']=$cod_cliente;
			$_SESSION['cod_cliente_digital']= $listado1->id_card;
			$_SESSION['id_card']= $cod_cliente;
			if ($select_idioma!='') $_SESSION['select_idioma'] = $select_idioma;
			else $_SEESSION['select_idioma'] = 'es';
			// definimos usuario_nivel con el Nivel de acceso del usuario de nuestra BD de usuarios
			$_SESSION['dni']=$userName;

			
			$requete = "SELECT * FROM `APP_tarjetas_activas` WHERE `CODIGO_TARJETA`=".$cod_cliente;
			$result = mysqli_query($db,$requete);
			if (($result) && (mysqli_num_rows($result)>0))
			{
				$requete = "UPDATE `APP_tarjetas_activas` SET `FechaUltimoAcceso`='".date('Y-m-d')."' WHERE `CODIGO_TARJETA`=".$cod_cliente;
				mysqli_query($db,$requete);
			}
			else 
			{
				$requete = "INSERT INTO `APP_tarjetas_activas` (`CODIGO_TARJETA`,`FechaUltimoAcceso`) VALUES (".$cod_cliente.",'".date('Y-m-d')."');";
				mysqli_query($db,$requete);
				$ch = curl_init("https://fornes.supermasymas.com/generar_top_productos.php");
			}
			// Hacemos una llamada a si mismo (scritp) para que queden disponibles
			// las variables de session en el array asociado $HTTP_...
			$pag=$_SERVER['PHP_SELF'];
			//Header ("Location: $pag?");		
			//exit;
		}
		else
		{
			// si no esta el nombre de usuario en la BD o el password ..
			// se devuelve a pagina q lo llamo con error
			Header ("Location: $redir?error_login=2");
			exit;
		}
	}
	else 
	{
		// si no esta el nombre de usuario en la BD o el password ..
		// se devuelve a pagina q lo llamo con error
		Header ("Location: $redir?error_login=2");
		exit;
	}
} 
else 
{	
	// -------- Chequear sesión existe -------
	// usamos la sesion de nombre definido.
	session_name("AutenticadorPanel");
	// Iniciamos el uso de sesiones
	if(!isset($_SESSION)) 
	{ 
		session_start(); 
	}
}
if ((isset($desconectar)&&($desconectar=="si"))||(isset($cerrar_sesion)&&$cerrar_sesion=='1'))
{
	session_name("Autenticador");
	session_destroy();
	header("Location:https://".$_SERVER['SERVER_NAME']."/dashboard/");
	exit;
}
?>

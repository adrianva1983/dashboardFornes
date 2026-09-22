<?php
	include "../includes/config.inc.php";
	$serverurl = "http://".$_SERVER["SERVER_NAME"]."/";
	
	//Comprobamos el acceso
	require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/aut_verifica.inc.php");
	$nivel_acceso=5; // Nivel de acceso para esta página.
	if ($nivel_acceso <= $_SESSION['usuario_nivel'])
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
	if ($Idioma=="") $Idioma="es";

	switch ($_POST['provincia'])
	{
		case "A Coruña":
			$id_zona =496;
			break;
		case "Álava":
			$id_zona =523;
			break;		
		case "Albacete":
			$id_zona =510;
			break;
		case "Alicante":
			$id_zona =490;
			break;
		case "Almería":
			$id_zona =499;
			break;
		case "Asturias":
			$id_zona =481;
			break;
		case "Ávila":
			$id_zona =513;
			break;
		case "Badajoz":
			$id_zona =524;
			break;
		case "Barcelona":
			$id_zona =525;
			break;
		case "Burgos":
			$id_zona =514;
			break;
		case "Cáceres":
			$id_zona =526;
			break;
		case "Cádiz":
			$id_zona =500;
			break;
		case "Cantabria":
			$id_zona =509;
			break;
		case "Castellón":
			$id_zona =527;
			break;
		case "Ciudad Real":
			$id_zona =511;
			break;
		case "Córdoba":
			$id_zona =501;
			break;
		case "Cuenca":
			$id_zona =512;
			break;
		case "Girona":
			$id_zona =528;
			break;
		case "Granada":
			$id_zona =502;
			break;
		case "Guadalajara":
			$id_zona =529;
			break;
		case "Guipúzcoa":
			$id_zona =530;
			break;
		case "Huelva":
			$id_zona =531;
			break;
		case "Huesca":
			$id_zona =506;
			break;
		case "Islas Baleares":
			$id_zona =491;
			break;
		case "Jaén":
			$id_zona =503;
			break;
		case "La Rioja":
			$id_zona =532;
			break;
		case "Las Palmas":
			$id_zona =533;
			break;
		case "León":
			$id_zona =515;
			break;
		case "Lleida":
			$id_zona =534;
			break;
		case "Lugo":
			$id_zona =498;
			break;
		case "Madrid":
			$id_zona =535;
			break;
		case "Málaga":
			$id_zona =504;
			break;
		case "Murcia":
			$id_zona =536;
			break;
		case "Navarra":
			$id_zona =537;
			break;
		case "Ourense":
			$id_zona =495;
			break;
		case "Palencia":
			$id_zona =516;
			break;
		case "Pontevedra":
			$id_zona =497;
			break;
		case "Salamanca":
			$id_zona =493;
			break;
		case "Santa Cruz de Tenerife":
			$id_zona =538;
			break;
		case "Segovia":
			$id_zona =517;
			break;
		case "Sevilla":
			$id_zona =505;
			break;
		case "Soria":
			$id_zona =518;
			break;
		case "Tarragona":
			$id_zona =539;
			break;
		case "Teruel":
			$id_zona =507;
			break;
		case "Toledo":
			$id_zona =540;
			break;
		case "Valencia":
			$id_zona =492;
			break;
		case "Valladolid":
			$id_zona =519;
			break;
		case "Vizcaya":
			$id_zona =541;
			break;
		case "Zamora":
			$id_zona =520;
			break;
		case "Zaragoza":
			$id_zona =508;
			break;
		default:
			if ($_GET["id_zona"]!="") $id_zona = $_GET["id_zona"];
			else $id_zona = $_POST["id_zona"];			
	}

	//mysql_query("SET NAMES utf8");
	$is_user = false;
	
	//if (isset($_GET['user']))$is_user = $_GET['user'] == "1"; else $is_user = false;
	$query_con_user = "SELECT * FROM " . $DBPrefix . "conciertos WHERE id = '" . $_GET['id'] . "'";
	$res_con_user = mysql_query($query_con_user);
	$system->check_mysql($res_con_user, $query_con_user, __LINE__, __FILE__);
	$user_folder_name = mysql_result($res_con_user, 0, 'user');
	
	$query_description_names = "SELECT distinct nombre FROM " . $DBPrefix . "conciertos";
	$res_description_names = mysql_query($query_description_names);
	$system->check_mysql($res_description_names, $query_description_names, __LINE__, __FILE__);
	$num_names = mysql_num_rows($res_description_names);
	$i = 0;
	$datasoruce_names = "[&quot;";
	while ($i < $num_names) {
		$description_name = mysql_result($res_description_names, $i, 'nombre');
		$datasoruce_names = $datasoruce_names . "&quot;,&quot;" . $description_name;
		$i++;
	}

	$datasoruce_names = $datasoruce_names . "&quot;]";
	$datasoruce_names = htmlspecialchars($datasoruce_names, ENT_QUOTES);
	
	$query_description_groups = "SELECT distinct nombre FROM " . $DBPrefix . "usuarios WHERE tipo=2";
	$res_description_groups = mysql_query($query_description_groups);
	$system->check_mysql($res_description_groups, $query_description_groups, __LINE__, __FILE__);
	$num_groups = mysql_num_rows($res_description_groups);
	$j = 0;
	$datasource_groups = "[&quot;";
	while ($j < $num_groups) {
		$datasource_group = mysql_result($res_description_groups, $j, 'nombre');
		
		if ($datasource_group!="")$datasource_groups = $datasource_groups . "&quot;,&quot;" . $datasource_group;
		$j++;
	}

	$datasource_groups = $datasource_groups . "&quot;]";
	$query_description_locals = "SELECT distinct nombre FROM " . $DBPrefix . "usuarios WHERE tipo=3";
	$res_description_locals = mysql_query($query_description_locals);
	$system->check_mysql($res_description_locals, $query_description_locals, __LINE__, __FILE__);
	$num_locals = mysql_num_rows($res_description_locals);
	$k = 0;
	$datasource_locals = "[&quot;";
	while ($k < $num_locals) {
		$datasource_local = mysql_result($res_description_locals, $k, 'nombre');
		
		if ($datasource_local!="")$datasource_locals = $datasource_locals . "&quot;,&quot;" . $datasource_local;
		$k++;
	}

	$datasource_locals = $datasource_locals . "&quot;]";
	
	
	if (isset($_POST['action']) && ($_POST['action'] == "addconcert")){
		
		if ($_FILES['file']['size'] > 0)list($width, $height) = getimagesize($_FILES['file']['tmp_name']);
		
		if ($_FILES['file_slide']['size'] > 0)list($width_slide, $height_slide) = getimagesize($_FILES['file_slide']['tmp_name']);
		$error_add_number = 0;
		
		if(!$_POST['N_nombre']){
			$error_add_number = 1;
			$error_add_msg = "Es necesario un nombre descriptivo";
		} else		
		if($_POST['N_youtube']&&(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['N_youtube']))){
			$error_add_number = 6;
			$error_add_msg = "El formato de url es incorrecto (recuerde comenzar con http:// o https://)";
		} else
		if($_POST['N_puntos_venta_url']&&(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['N_puntos_venta_url']))){
			$error_add_number = 12;
			$error_add_msg = "El formato de url es incorrecto (recuerde comenzar con http:// o https://)";
		} else {
			$isGroup = false;
			for ($j = 1; $j <= $_POST['groups-index']; $j++) {
				
				if ($_POST['nombre_grupo'.$j]){
					$isGroup = true;
				}

			}

			
			if (!$isGroup || $_POST['groups-number'] == 0){
				$error_add_number = 8;
				$error_add_msg = "Debe agregar al menos un grupo y una fecha";
			} 

				
				if ($error_add_msg == "")
				{
					$concert_id = $_GET['id'];
					$keywords = $system->cleanvars($_POST['N_nombre']);
						$id_local = "";
						$local_lugar = "";
						if ($_POST['N_lugar']!="")
						{
							$local_lugar = $_POST['N_lugar'];
							$requete = "SELECT * FROM cast_usuarios WHERE nombre = '".$_POST['N_lugar']."' AND tipo=3";
							if ($id_zona!="") $requete.=" AND id_zona=".$id_zona;
							$result = mysql_query($requete,$db);
							if (($result) && (mysql_num_rows($result)>0))
							{
								$listado = mysql_fetch_object($result);
								$id_local = $listado->id;
							}
							else
							{
								$requete = "INSERT INTO cast_usuarios (nombre,tipo,municipio,dedonde,DV_Activo,CreadoDesdeConcierto";
								if ($id_zona!="") $requete.=",id_zona";
								$requete.= ") VALUES ('".$_POST['N_lugar']."',3,'".$municipio."','".$_POST["N_localidad"]."',0,1";
								if ($id_zona!="") $requete.=",".$id_zona;
								$requete.=");";
								mysql_query($requete,$db);
								$id_local = mysql_insert_id();
							}
						}
						else 
						{
							$id_local = $_POST["local"];
							$requete = "SELECT nombre FROM cast_usuarios WHERE id=".$id_local;
							$result = mysql_query($requete,$db);
							if (($result) && (mysql_num_rows($result)>0))
							{
								$listado = mysql_fetch_object($result);
								$local_lugar = $listado->nombre;
							}
						}

					
					if (!$is_user)
					{
						$query_update = "UPDATE " . $DBPrefix . "conciertos SET nombre = '" . $system->cleanvars($_POST['N_nombre']) . "', provincia = '" . $system->cleanvars($_POST['provincia']) . "', municipio = '" . $system->cleanvars($_POST['municipio']) . "', nombre_slide = '" . $_POST['N_nombre_slide'] . "', localidad = '" . $_POST['N_localidad'] . "', id_local= ".$id_local.", lugar = '" . $system->cleanvars($local_lugar) ."', descripcion = '" . $_POST['N_descripcion'] ."', descripcion_slide = '" . $system->cleanvars($_POST['N_descripcion_slide']) ."'";
						$query_update .= ", imagen = '";
						if ($_POST["N_image"]!="") $query_update .= $_POST["N_image"];
						$indice = 0;
						while ($_POST["N_image".$indice]!=""&&$_POST["N_image".$indice]!= null&&$_POST["N_image".$indice]!= 'undefined')
						{
							if ($indice!=0) $query_update .="||";
							$query_update .=$_POST["N_image".$indice];
							$indice++;
						}
						$query_update .="'";
						$query_update .= ", video = '" . $_POST['N_youtube'] ."', precio = '" . $_POST['N_precio'] ."', precio1 = '" . $_POST['N_precio1'] ."', concepto1 = '" . $_POST['N_concepto1'] . "', precio2 = '" . $_POST['N_precio2'] . "', concepto2 = '" . $_POST['N_concepto2'] ."', precio3 = '" . $_POST['N_precio3'] ."', concepto3 = '" . $_POST['N_concepto3'] ."', precio4 = '" . $_POST['N_precio4']."', concepto4 = '" . $_POST['N_concepto4']."', puntos_de_venta = '" . $_POST['N_puntos_venta']."', puntos_de_venta_url = '" . $_POST['N_puntos_venta_url']."' WHERE id = '" . $concert_id."'";
					}		
					$system->check_mysql(mysql_query($query_update), $query_update, __LINE__, __FILE__);					
					$conId = $_GET['id'];
					$query_con_remove_groups = "DELETE FROM " . $DBPrefix . "grupos_conciertos WHERE concierto='". $conId  ."'";
					$system->check_mysql(mysql_query($query_con_remove_groups), $query_con_remove_groups, __LINE__, __FILE__);
					$number_groups = $_POST['groups-number'];
					$groups_index = $_POST['groups-index'];
					$min_timestamp = 999999999999999999999999999;
					$estilo_evento = $_POST['estilo_grupo0'];
					$SinHoraEvento = 0;
					for ($j = 1; $j <= $groups_index; $j++) 
					{
						
						if ($_POST['nombre_grupo'.$j])
						{
							$keywords = $keywords. "/" . $system->cleanvars($_POST['nombre_grupo'.$j]);
							$timestamp = strtotime($system->cleanvars($_POST['fecha_grupo'.$j]));
							if ($estilo_evento != $_POST['estilo_grupo'.$j]) $estilo_evento = "Varios";
							if ($timestamp < $min_timestamp)$min_timestamp = $timestamp;
							//Miramos si el estilo existe para añadirlo a la base de datos
							if ($_POST['estilo_grupo'.$j]!="")
							{
								$requete = "SELECT * FROM cast_estilos WHERE estilo='".$_POST['estilo_grupo'.$j]."'";
								$result = mysql_query($requete,$db);
								if (($result) && (mysql_num_rows($result)>0))
								{
								}
								else
								{
									$requete = "INSERT INTO cast_estilos (estilo) VALUES ('".$_POST['estilo_grupo'.$j]."')";
									mysql_query($requete,$db);
								}
							}
							if ($_POST["id_grupo".$j]=="")							
							{ //No existe el grupo, valoramos si lo metemos o no en la base de datos
								$requete = "SELECT * FROM cast_usuarios WHERE nombre = '".$_POST['nombre_grupo'.$j]."' AND tipo=2";
								//if ($id_zona!="") $requete.=" AND id_zona=".$id_zona;
								$result = mysql_query($requete,$db);
								if (($result) && (mysql_num_rows($result)>0)) {}
								else
								{
									$requete = "INSERT INTO cast_usuarios (nombre,tipo,DV_Activo,estilo,CreadoDesdeConcierto";
									$requete.= ") VALUES ('".$_POST['nombre_grupo'.$j]."',2,0,'".$_POST['estilo_grupo'.$j]."',1";
									$requete.=");";
									mysql_query($requete,$db);
									$id_grupo_actual = mysql_insert_id();
								}							
							}
							else
							{
								$requete = "SELECT * FROM cast_usuarios WHERE id = '".$_POST["id_grupo".$j]."' AND tipo=2";
								$result = mysql_query($requete,$db);
								if (($result) && (mysql_num_rows($result)>0))
								{
									$listado = mysql_fetch_object($result);
									if ($listado->estilo!=""||$_POST['estilo_grupo'.$j]==""){}
									else
									{
										$requete = "UPDATE cast_usuarios SET estilo='".$_POST['estilo_grupo'.$j]."' WHERE id=".$_POST["id_grupo".$j];
										mysql_query($requete,$db);
									}
								}
							}
							$SinHora=1;
							if (strpos($_POST['fecha_grupo'.$j],":")) 
							{
								$SinHora=0;
							}
							else $SinHoraEvento=1;
							$query_addconcert = "INSERT INTO " . $DBPrefix . "grupos_conciertos (nombre, fecha_hora, estilo, concierto,SinHora";
							if ($_POST["id_grupo".$j]!="") $query_addconcert .=",id_usuario_grupo";
							if ($id_grupo_actual!="") $query_addconcert .=",id_usuario_grupo";
							$query_addconcert .= ") VALUES ('" . $system->cleanvars($_POST['nombre_grupo'.$j]) . "','" . $timestamp . "','" . $system->cleanvars($_POST['estilo_grupo'.$j]) . "','" . $concert_id . "',".$SinHora;
							if ($_POST["id_grupo".$j]!="") $query_addconcert .=",".$_POST["id_grupo".$j];
							if ($id_grupo_actual!="") $query_addconcert .=",".$id_grupo_actual;
							$query_addconcert .= ")";
							$res_add_concert = mysql_query ($query_addconcert);
							$system->check_mysql($res_add_concert, $query_add_concert, __LINE__, __FILE__);
						}

					}					

					if ($min_timestamp!=$timestamp) $SinHoraEvento=1;
					$query_update_time = "UPDATE " . $DBPrefix . "conciertos SET fecha_hora = '" . $min_timestamp ."', SinHora=".$SinHoraEvento.", keywords = '" . $keywords . "', estilo='".$estilo_evento."' WHERE id = '" . $concert_id."'";					
					$system->check_mysql(mysql_query($query_update_time), $query_update_time, __LINE__, __FILE__);
					if ($is_user)header('Location: panel-control-usuario.php'); else header('Location: panel-control.php');
					exit;
				}

			}

		}
	

	
	$query_country_names = "SELECT distinct concejo FROM " . $DBPrefix . "concejos";
	$res_country_names = mysql_query($query_country_names);
	$system->check_mysql($res_country_names, $query_country_names, __LINE__, __FILE__);
	$num_country_names = mysql_num_rows($res_country_names);
	$i = 0;
	$datasoruce_country_names = "[&quot;";
	while ($i < $num_country_names) {
		$country_name = mysql_result($res_country_names, $i, 'concejo');
		$datasoruce_country_names = $datasoruce_country_names . "&quot;,&quot;" . $country_name;
		$i++;
	}

	$datasoruce_country_names = $datasoruce_country_names . "&quot;]";
	
	if (isset($_POST['action']) && ($_POST['action'] == "addconcert")){
		$number_groups = $_POST['groups-number'];
		$groups_index = $_POST['groups-index'];
		$appendStr = "";
		for ($k = 1; $k <= $groups_index; $k++) {
			
			if ($_POST['nombre_grupo'.$k]){
				$timestamp = strtotime($system->cleanvars($_POST['fecha_grupo'.$k]) ." ".$system->cleanvars($_POST['hora_grupo'.$k]));
				$hora = date("H:i", intval($timestamp));
				$fecha = date("d-m-Y", intval($timestamp));
				$appendStr = $appendStr . "<div class=\"form-label\" id=\"group-block-" .$k. "\"><a  id=\"close".$k."\" class=\"close\" data-dismiss=\"alert\" href=\"javascript:void(0);\">x</a><input readonly=\"readonly\" class=\"input-btn xxlarge\" id=\"nombre_grupo".$k."\" name=\"nombre_grupo".$k."\" size=\"20\" value=\"" . $_POST['nombre_grupo'.$k] . "\">";				
				$appendStr = $appendStr . "&nbsp;<input class=\"input-btn medium\" id=\"estilo_grupo".$k."\" name=\"estilo_grupo".$k."\" size=\"20\" class=\"exit-detect\" value=\"". $_POST['estilo_grupo'.$k] ."\">";
				$appendStr = $appendStr . "&nbsp;<input class=\"input-btn small\" id=\"fecha_grupo".$k."\" name=\"fecha_grupo".$k."\" size=\"20\" value=\"".$fecha ." ".$hora."\"></div>";				
			}

		}

		$val_nombre = $_POST['N_nombre'];
		$val_nombre_slide = $_POST['N_nombre_slide'];
		$val_provincia =$_POST['provincia'];
		$val_municipio =$_POST['municipio'];
		$val_localidad =$_POST['N_localidad'];
		$val_lugar = $_POST['N_lugar'];
		$val_descripcion =  $_POST['N_descripcion'];
		$val_descripcion_slide =  $_POST['N_descripcion_slide'];		
		$val_youtube = $_POST['N_youtube'];
		$val_precio =$_POST['N_precio'];
		$val_precio1 =$_POST['N_precio1'];
		$val_concepto1 =$_POST['N_concepto1'];
		$val_precio2 =$_POST['N_precio2'];
		$val_concepto2 =$_POST['N_concepto2'];
		$val_precio3 =$_POST['N_precio3'];
		$val_concepto3 =$_POST['N_concepto3'];
		$val_precio4 =$_POST['N_precio4'];
		$val_concepto4 =$_POST['N_concepto4'];
		$val_puntos_venta =$_POST['N_puntos_venta'];
		$val_puntos_venta_url =$_POST['N_puntos_venta_url'];
		$val_imagen =$_POST['N_image'];
		
		$nombre_archivo = $_SESSION['WEBID_LOGGED_IN'];
		
		if ($_FILES['file']['size'] != 0&&($width > 599)){
			
			if (isset($_POST['nombre_imagen'])){
				
				if (!file_exists("upload/".$nombre_archivo)) {
					mkdir("upload/".$nombre_archivo,0777);
				}

				$image_for_update = time(). $_FILES["file"]["name"];
				move_uploaded_file($_FILES["file"]["tmp_name"],"upload/". $nombre_archivo . "/" .$image_for_update);
			}

		}

		elseif ($_FILES['file']['size'] > 0&&$width < 600){
			$error_add_number = 5;
			$error_add_msg = "La imagen debe tener una ancho mayor o igual a 600 px";
			$image_for_update = "";
		}

		elseif (isset($_POST['nombre_imagen'])){
			$image_for_update = $_POST['nombre_imagen'];
		}

		
		if ($_FILES['file_slide']['size'] != 0 && ($width_slide == 960)&& ($height_slide == 349)){
			
			if (isset($_POST['nombre_imagen_slide'])){
				
				if (!file_exists("upload/".$nombre_archivo)) {
					mkdir("upload/".$nombre_archivo,0777);
				}

				$image_for_update_slide = time(). $_FILES["file_slide"]["name"];
				move_uploaded_file($_FILES["file_slide"]["tmp_name"],"upload/". $nombre_archivo . "/" .$image_for_update_slide);
			}

		}

		elseif ($_FILES['file_slide']['size'] > 0&&(($width_slide != 960)||($height_slide != 349))){
			$error_add_number = 9;
			$error_add_msg = "La imagen debe tener una ancho de 960 px y un alto de 349";
			$image_for_update_slide = "";
		}

		elseif (isset($_POST['nombre_imagen_slide'])){
			$image_for_update_slide = $_POST['nombre_imagen_slide'];
		}

		
		
		if (isset($_POST['nombre_imagen']) && ($_POST['nombre_imagen'] != "")&&$image_for_update!=""){
			$appendStrImg = '<div class="form-label" id="group-block-image"><input readonly="readonly" class="input-btn large" id="nombre_imagen" name="nombre_imagen" size="20" value="'.$image_for_update.'"></div>';
		}

		
		if (isset($_POST['nombre_imagen_slide']) && ($_POST['nombre_imagen_slide'] != "")&&$image_for_update_slide!=""){
			$appendStrImgSlide = '<div class="form-label" id="group-block-image-slide"><input readonly="readonly" class="input-btn large" id="nombre_imagen_slide" name="nombre_imagen_slide" size="20" value="'.$image_for_update_slide.'"></div>';
		}

	} else {
		$conId = $_GET['id'];
		$query_con = "SELECT * FROM " . $DBPrefix . "conciertos WHERE id = '" . $conId . "'";
		$res_con = mysql_query($query_con);
		$system->check_mysql($res_con, $query_con, __LINE__, __FILE__);
		$val_nombre = mysql_result($res_con, 0, 'nombre');
		$val_nombre_slide = mysql_result($res_con, 0, 'nombre_slide');
		$val_provincia = mysql_result($res_con, 0, 'provincia');
		$val_municipio = mysql_result($res_con, 0, 'municipio');
		$val_localidad = mysql_result($res_con, 0, 'localidad');
		$val_lugar = mysql_result($res_con, 0, 'lugar');
		$val_descripcion = str_replace( '<br />', "\n",  mysql_result($res_con, 0, 'descripcion'));	
	
		$val_descripcion_slide = mysql_result($res_con, 0, 'descripcion_slide');
		$val_youtube = mysql_result($res_con, 0, 'video');
		$val_precio = mysql_result($res_con, 0, 'precio');
		$val_precio1 = mysql_result($res_con, 0, 'precio1');
		$val_concepto1 = mysql_result($res_con, 0, 'concepto1');
		$val_precio2 = mysql_result($res_con, 0, 'precio2');
		$val_concepto2 = mysql_result($res_con, 0, 'concepto2');
		$val_precio3 = mysql_result($res_con, 0, 'precio3');
		$val_concepto3 = mysql_result($res_con, 0, 'concepto3');
		$val_precio4 = mysql_result($res_con, 0, 'precio4');
		$val_concepto4 = mysql_result($res_con, 0, 'concepto4');
		$val_puntos_venta = mysql_result($res_con, 0, 'puntos_de_venta');
		$val_puntos_venta_url = mysql_result($res_con, 0, 'puntos_de_venta_url');
		$val_estilo = mysql_result($res_con, 0, 'estilo');
		$val_estilo2 = mysql_result($res_con, 0, 'estilo2');
		$val_imagen = mysql_result($res_con, 0, 'imagen');
		$val_imagen_slide = mysql_result($res_con, 0, 'imagen_slide');
		$val_user = mysql_result($res_con, 0, 'user');
		
		$query_grupos_conciertos = "SELECT * FROM " . $DBPrefix . "grupos_conciertos WHERE concierto = '" . $conId . "'";
		$res_grupos_conciertos = mysql_query($query_grupos_conciertos);
		$system->check_mysql($res_grupos_conciertos, $query_grupos_conciertos, __LINE__, __FILE__);
		$number_groups = mysql_num_rows($res_grupos_conciertos);
		$groups_index = mysql_num_rows($res_grupos_conciertos);
		$k = 1;
		$appendStr = "";
		
		
		if ($val_imagen_slide != "")$appendStrImgSlide = '<div class="form-label" id="group-block-image-slide"><input readonly="readonly" class="input-btn large" id="nombre_imagen_slide" name="nombre_imagen_slide" size="20" value="'.$val_imagen_slide.'"></div>';
		$consulta = "SELECT * from `cast_estilos` WHERE `Activo`=1 ORDER BY `estilo`";
		$result = mysql_query($consulta,$db);		
		if (($result) && (mysql_num_rows($result)>0))
		{
			$listado_estilos = '[&quot;&quot;';	
			while ($listado = mysql_fetch_object($result))
			{
				$listado_estilos .= ",&quot;".$listado->estilo."&quot;";	
			}
			$listado_estilos .= ']';
		}
		while ($k <= $number_groups) {
			$hora = date("H:i", intval(mysql_result($res_grupos_conciertos, $k-1, 'fecha_hora')));
			$fecha = date("d-m-Y", intval(mysql_result($res_grupos_conciertos, $k-1, 'fecha_hora')));
			$appendStr = $appendStr . "<div class=\"form-label\" id=\"group-block-" .$k. "\"><a  id=\"close".$k."\" class=\"close\" data-dismiss=\"alert\" href=\"javascript:void(0);\">x</a><input readonly=\"readonly\" class=\"input-btn xxlarge\" id=\"nombre_grupo".$k."\" name=\"nombre_grupo".$k."\" size=\"20\" value=\"" . mysql_result($res_grupos_conciertos, $k-1, 'nombre') . "\">";
			if (mysql_result($res_grupos_conciertos, $k-1, 'estilo')!="") $appendStr = $appendStr . "&nbsp;<input class=\"input-btn medium\" id=\"estilo_grupo".$k."\" name=\"estilo_grupo".$k."\" size=\"20\" class=\"exit-detect\" value=\"". mysql_result($res_grupos_conciertos, $k-1, 'estilo') ."\">";
			else $appendStr = $appendStr . '&nbsp;<input type="text" data-source="'.$listado_estilos.'" class="input-btn medium" data-items="5" data-provide="typeahead" class="input-btn small" data-original-title="Si no esta, se agregará" data-content="Escribe el género e iremos buscando coincidencias en nuestra base de datos" placeholder="Indícanos su estilo"  id="estilo_grupo'.$k.'" name="estilo_grupo'.$k.'" size="20" class="exit-detect">';
			if (mysql_result($res_grupos_conciertos, $k-1, 'SinHora')==1) $appendStr = $appendStr . "&nbsp;<input class=\"input-btn small\" id=\"fecha_grupo".$k."\" name=\"fecha_grupo".$k."\" size=\"20\" value=\"".$fecha."\"></div>";
			else $appendStr = $appendStr . "&nbsp;<input class=\"input-btn small\" id=\"fecha_grupo".$k."\" name=\"fecha_grupo".$k."\" size=\"20\" value=\"".$fecha ." ".$hora."\"></div>";
			$k++;
		}
	}

	
	if ($val_imagen!=''){
		$query_user_id = "SELECT * FROM " . $DBPrefix . "conciertos WHERE id = '" . $conId . "'";
		$res_user_id = mysql_query($query_user_id);
		$system->check_mysql($res_user_id, $query_user_id, __LINE__, __FILE__);
		$val_imagen_tmp = explode("||",$val_imagen);
		$thumb_imagen = "";
		for ($i=0;$i<count($val_imagen_tmp);$i++)
		{
			$thumb_imagen .= '<img src="upload/' . $val_user . '/thumb_'.$val_imagen_tmp[$i] .'" alt="Resized Image" style="margin-right:10px;">';	
		}
	} else $thumb_imagen = '';
	
	if ($is_user)$parameter_get = "&user=1";
	
	require($_SERVER['DOCUMENT_ROOT']."/herramientas/libreria_front_office.php");	
	require($_SERVER['DOCUMENT_ROOT']."/Plantillas/idiomas/portada_general-".$Idioma.".conf");
	
	$data = array('SITEURL' => $serverurl,'PROVINCIA' => $val_provincia,'MUNICIPIO' => $val_municipio,'V_NOMBRE' => $val_nombre,'V_NOMBRE_SLIDE' => $val_nombre_slide,'V_LOCAL' => $val_lugar,'V_ESTILO' => $val_estilo,'V_ESTILO2' => $val_estilo2,'V_LOCALIDAD' => $val_localidad,'V_DESCRIPCION' => $val_descripcion,'V_DESCRIPCION_SLIDE' => $val_descripcion_slide,'V_IMAGEN' => $_POST['N_imagen'],'V_IMAGE' => $item_image,'IMAGE_VAL' => $appendStrImg,'IMAGE_VAL_SLIDE' => $appendStrImgSlide,'V_YOUTUBE' => $val_youtube,'V_PRECIO1' => $val_precio1,'V_CONCEPTO1' => $val_concepto1,'V_PRECIO2' => $val_precio2,'V_CONCEPTO2' => $val_concepto2,'V_PRECIO3' => $val_precio3,'V_CONCEPTO3' => $val_concepto3,'V_PRECIO4' => $val_precio4,'V_CONCEPTO4' => $val_concepto4,'V_PUNTOS_VENTA' => $val_puntos_venta,'V_PUNTOS_VENTA_URL' => $val_puntos_venta_url,'ERR_ADD_NBR_MSG' => $error_add_number,'F_N' => $_FILES["file"]["name"],'F_T' => $_FILES["file"]["type"],'F_S' => $_FILES["file"]["size"],'F_D' => $_FILES["file"]["tmp_name"],'ERR_FILE' => $error_add_msg,'ERR_ADD_MSG' => $error_add_msg,'USERNAME' => $_SESSION['WEBID_LOGGED_IN'],'USERFOLDERNAME' => $user_folder_name,'DATASOURCE_NAMES' => $datasoruce_names,'DATASOURCE_GROUPS' => $datasource_groups,'DATASOURCE_LOCALS' => $datasource_locals,'DATASOURCE_COUNTRY_NAMES' => $datasoruce_country_names,'COUNTRY_NAMES' => $datasoruce_country_names,'ERR_ADDING' => $error_add_number > 0,'V_NO_PRECIO' => ($val_precio == 1) ? 'checked=true' :
	'','V_LIBRE' => ($val_precio == 2) ? 'checked=true' :
	'','V_PRECIO' => ($val_precio == 3) ? 'checked=true' :
	'','DISABLE_PRICES' => ($val_precio != 3) ? 'disabled="disabled"' :
	'','DATOS' => $_POST['N_fecha']."--".$_POST['N_time'],'ERRORA1' => ($error_add_number == 1) ? 'error' :
	'','ERRORA2' => ($error_add_number == 2) ? 'error' :
	'','ERRORA3' => ($error_add_number == 3) ? 'error' :
	'','ERRORA4' => ($error_add_number == 4) ? 'error' :
	'','ERRORA5' => ($error_add_number == 5) ? 'error' :
	'','ERRORA6' => ($error_add_number == 6) ? 'error' :
	'','ERRORA8' => ($error_add_number == 8) ? 'error' :
	'','ERRORA9' => ($error_add_number == 9) ? 'error' :
	'','ERRORA10' => ($error_add_number == 10) ? 'error' :
	'','ERRORA11' => ($error_add_number == 11) ? 'error' :
	'','ERRORA12' => ($error_add_number == 12) ? 'error' :
	'','HEADER_CLASS' => 'cabecera-nuevo-concierto','DISPLAYADDERR' => ($error_add_number > 0) ? 'display' :
	'nondisplay','NG' => $timestamp = strtotime("31-01-2012 09:45 PM"),'JAVI' => $javi,'ESTILO_CABECERA' => 'estrecho','ID_UPDATED' => $_GET['id'],'BODY_PARAM' => true,'NUMERO_GRUPOS' => $number_groups,'INDICE_GRUPOS' => $groups_index,'GROUPS_DIVS' => $appendStr,'IS_ADMIN' => !$is_user,'PARAMETER_GET' => $parameter_get,'HEAD_TITLE' => "Modificar concierto - Conciertos en Asturias",'HEAD_KEYWORDS' => "concierto, modificar, Asturias",'HEAD_DESCRIPTION' => "Modificar concierto en Asturias: publica tu concierto o de cualquier grupo en nuestro portal",'IMG_THUMB' =>  $thumb_imagen  ,'IMG_NAME' => $val_imagen,'USER_FOLDER' => $_SESSION['usuario_id'] );

	echo $data['GROUPS_DIVS'];
?>
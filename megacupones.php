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

    <title tkey="Megacupones_masymas">Megacupones masymas</title>

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
                    </div>
                    <div class="logo-element">
                        +
                    </div>
                </li>
				<?php
					$seccion = "megacupon";
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
			<div class="wrapper wrapper-content-cupones animated fadeInRight"></div>
			<?php
			require "tarjetas.php";
			?>			
            <div class="wrapper wrapper-content animated fadeInRight">
			<?php
			$array_valores = array();
			$requete = "SELECT * FROM `Cupones` WHERE `FECHA_HASTA`>='".date('Y-m-d')."' AND `BAJA`='N'";
			$result = mysqli_query($db,$requete);
			$i = 0;
			if (($result) && (mysqli_num_rows($result)>0))
			{
				while ($listado = mysqli_fetch_object($result))
				{
					$campo_fecha = (384*(intval(date('y'))-11)) + (32*(intval(date('m'))-1)) + intval(date('d'));
					$texto_megacupon = '';
					$texto_megacupon .=  '<div class="row"><div class="col-lg-12"><div class="ibox"><div class="ibox-title">';
					$texto_megacupon .=  '<h5>Megacupón: 98'.str_pad($listado->Id, 5, "0", STR_PAD_LEFT).'009907'.$campo_fecha.'000000000</h5>';
					$texto_megacupon .=  '<div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a><a class="close-link"><i class="fa fa-times"></i></a></div>';
					$texto_megacupon .=  '</div><div class="ibox-content"><table class="table table-hover"><thead><tr><th tkey="Cod_Barras">Cod.Barras</th><th tkey="Codigo">Código</th><th tkey="Vigente_desde">Vigente desde</th><th tkey="Vigente_hasta">Vigenta hasta</th></tr></thead><tbody>';
					$texto_megacupon .=  '<tr>';
						$texto_megacupon .=  '<td><img class="producto_imagen_listado" src="https://masymas-services.supermasymas.com/generador-barcode.php?code=98'.str_pad($listado->Id, 5, "0", STR_PAD_LEFT).'009907'.$campo_fecha.'000000000&type=code128"></td>';
						$texto_megacupon .=  '<td>98'.str_pad($listado->Id, 5, "0", STR_PAD_LEFT).'009907'.$campo_fecha.'000000000</td>';
						$tmp_fecha = explode('-',$listado->FECHA_DESDE);
						$texto_megacupon .=  '<td>'.$tmp_fecha[2].'/'.$tmp_fecha[1].'/'.$tmp_fecha[0].'</td>';
						$tmp_fecha = explode('-',$listado->FECHA_HASTA);
						$texto_megacupon .=  '<td>'.$tmp_fecha[2].'/'.$tmp_fecha[1].'/'.$tmp_fecha[0].'</td>';
					$texto_megacupon .=  '</tr>';
					$texto_megacupon .=  '</tbody></table>';
					$requete2 = "SELECT * FROM `CuponesBloques` WHERE `IdCupon`=".$listado->Id." ORDER BY Id DESC";					
					$result2 = mysqli_query($db,$requete2);
					$encontrado = false;
					if (($result2) && (mysqli_num_rows($result2)>0))
					{						
						$texto_megacupon.= '<table class="table table-hover"><thead><tr><th>Cod.</th><th tkey="Titulo">Titulo</th><th tkey="Promocion">Promoción</th><th tkey="Descripción">Descripción</th></tr></thead><tbody>';
						while ($listado2 = mysqli_fetch_object($result2))
						{
							$requete3 = "SELECT * FROM `CuponesRelacion`,`ClientesTarjetasGrupos` WHERE `ClientesTarjetasGrupos`.CODIGO_TARJETA = ".$_SESSION['id_card']." AND `ClientesTarjetasGrupos`.CODIGO_GRUPO = `CuponesRelacion`.CODIGO_GRUPO AND (`ClientesTarjetasGrupos`.BAJA IS NULL OR `ClientesTarjetasGrupos`.BAJA!='S') AND `CuponesRelacion`.CODIGO_PROMOCION=".$listado2->Id;							
							$result3 = mysqli_query($db,$requete3);
							if (($result3) && (mysqli_num_rows($result3)>0))
							{
								$encontrado = true;
								$texto_megacupon.= '<tr>';
									$texto_megacupon.= '<td>'.$listado2->Id.'</td>';
									$texto_megacupon.= '<td>'.$listado2->Titulo.'</td>';
									$texto_megacupon.= '<td>'.$listado2->Tipo.'</td>';
									$texto_megacupon.= '<td>'.$listado2->Texto.'</td>';
								$texto_megacupon.= '</tr>';
							}
						}
						$texto_megacupon.= '</tbody></table>';						
					}	
					$texto_megacupon.= '</div></div></div></div>';
					if ($encontrado) print $texto_megacupon;					
				}
			}

			?>						 
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
	<script src="/dashboard/lang/lang.js" type="text/javascript"></script>
	<script src="/dashboard/lang/es.js" type="text/javascript"></script>
	<script src="/dashboard/lang/en.js" type="text/javascript"></script>
	<script src="/dashboard/lang/va.js" type="text/javascript"></script>		
    <script>
	var resaltar = "<?=$_GET['cupon_resaltar']?>";
	function obtener_vales()
	{
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://tarjeta.masymas.com/servicios-web/obtener_vales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?php print $_SESSION['id_home'];?>&cod_cliente=<?php print $_SESSION['id_card'];?>	&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				console.log('res01010101010', res);
				 if (res.validacion == 'ok')
				 {
					var resultado_ajax = '';
					if (res.vales.length>0)
					{
						resultado_ajax += '<div class="grup_cupones container-fluid" style="margin-top:20px;">';												
						//resultado_ajax += '<div class="grup_cupones container-fluid"><div class="row tabler multi"><div class="col-md-4 col-xs-12 cup_qty cabecera-ticket-multicupon"></div><div class="col-md-4 col-xs-12 cup_name multi"><h3 class="titulo_seccion_multi">Cupones</h3></div><div class="col-md-4 col-xs-12 cup_name multi fecha_seccion_multi"><p>';
						//resultado_ajax += '</p></div></div>';
						resultado_ajax +='<div class="row tabler">';
					}
					var num_elto = 1;
					$.each(res.vales, function(key, value) 
					{
						if (value.es_cheque_ahorro==1)
						{
						}
						else
						{
						if (num_elto==1) resultado_ajax +='<div class="row tam_cupon_row">';
						resultado_ajax +='<div class="col-md-4 col-xs-12 tam_cupon_col"><div class="recorte';
						if (resaltar == value.cod_vale) resultado_ajax += ' resaltar_cupon';
						resultado_ajax += '"><div class="col-xs-3 cup_qty">';
						if (value.Img!=undefined&&value.Img!='')
						{
							resultado_ajax += '<img class="imagen_multicupon" src="'+value.Img+'">';
						}
						if (value.tipo.indexOf('<img')<0)
						{
							resultado_ajax += '<h1>'+value.tipo+'</h1>';
						}
						resultado_ajax +='</div>';
						if (value.es_cheque_ahorro==1){
							resultado_ajax +='<div class="col-xs-9 cup_name"><h3';
							if (resaltar == value.cod_vale) resultado_ajax += ' class="va_a_caducar"';
							resultado_ajax += '>'+temp_lang["MiChequeAhorro"]+'</h3>';
						}
						else{
							resultado_ajax +='<div class="col-xs-9 cup_name"><h3';
							if (resaltar == value.cod_vale) resultado_ajax += ' class="va_a_caducar"';
							resultado_ajax += '>'+value.titulo+'</h3><p>'+value.texto+'</p>';
							if (value.diferido==1) resultado_ajax += '<span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
						}
						resultado_ajax +='<p class="otros_cupones_valido';						
						if (value.proximo_vencimiento==1) resultado_ajax+=' va_a_caducar';
						resultado_ajax+='">'+temp_lang["cheque_canjeable"]+' '+value.valido+'</p></div>';
						resultado_ajax +='<div class="col-xs-2 cup_state"></div></div></div>';
						if (num_elto == 3) 
						{
							num_elto = 1;
							resultado_ajax+='</div>';
						}
						else num_elto++;
						}
					});
					if (res.vales.length>0)
					{
						if (num_elto!=1) resultado_ajax+='</div>';
						resultado_ajax +='</div></div></div></div>';
					}					
					$('.wrapper-content-cupones').html(resultado_ajax);
				 }
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}

	function obtener_multicupon()
	{
		
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://tarjeta.masymas.com/servicios-web/obtener_multicupon.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?php print $_SESSION['id_home'];?>&cod_cliente=<?php print $_SESSION['id_card'];?>&idioma=<?=$idioma?>&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				 if (res.validacion == 'ok')
				 {
					 var primero = true;
					 var resultado_ajax ='';	
					 $.each(res.multicupones, function(key, value) 
					 {
						 console.log('resaltar', resaltar);
						 console.log('value.cod_multicupon', value.cod_multicupon);
						if (primero) resultado_ajax += '<div class="grup_cupones container-fluid primer_multicupon_bloque">';
						else resultado_ajax += '<div class="grup_cupones container-fluid siguiente_multicupon_bloque">';
						if (res.multicupones.length>1){
							resultado_ajax += '<div class="grup_cupones"><div class="row tabler multi"><div class="col-md-3 col-xs-12 cup_qty cabecera-ticket-multicupon"></div><div class="col-md-3 col-xs-12 cup_name multi"><h3 class="titulo_seccion_multi';
							if (resaltar == value.cod_multicupon) resultado_ajax += ' va_a_caducar';
							resultado_ajax += '">'+temp_lang['Multicupon']+'</h3></div><div class="col-md-3 col-xs-12 cup_name multi fecha_seccion_multi"><p>';
						} else{
							resultado_ajax += '<div class="grup_cupones"><div class="row tabler multi"><div class="col-md-4 col-xs-12 cup_qty cabecera-ticket-multicupon"></div><div class="col-md-4 col-xs-12 cup_name multi"><h3 class="titulo_seccion_multi';
							if (resaltar == value.cod_multicupon) resultado_ajax += ' va_a_caducar';
							resultado_ajax += '">'+temp_lang['Multicupon']+'</h3></div><div class="col-md-4 col-xs-12 cup_name multi fecha_seccion_multi"><p>';
						} 
						resultado_ajax += ''+temp_lang['ValidoDel']+'<br/>'+value.fecha_ini_redencion;
						resultado_ajax += ' al '+value.fecha_fin_redencion;
						if (res.multicupones.length>1) 
						{
							if (primero) resultado_ajax += '</p></div><div class="col-md-3 col-xs-12"><a href="#" onclick="$(\'.primer_multicupon_bloque\').hide();$(\'.siguiente_multicupon_bloque\').fadeIn();return false;" class="btn btn-primary btn-block siguiente_multicupon">Ver siquiente</a></div>';
							else resultado_ajax += '</p></div><div class="col-md-3 col-xs-12"><a href="#" onclick="$(\'.siguiente_multicupon_bloque\').hide();$(\'.primer_multicupon_bloque\').fadeIn();return false;" class="btn btn-primary btn-block siguiente_multicupon">Ver anterior</a></div>';
						}
						else resultado_ajax += '</p></div></div>';
						resultado_ajax +='</div><div class="row tabler">';
						var num_elto = 1;
						$.each(value.promos, function(key2, value2)
						{
							if (num_elto==1) resultado_ajax +='<div class="row tam_cupon_row">';
							resultado_ajax +='<div class="col-md-4 col-xs-12 tam_cupon_col"><div class="recorte"><div class="col-xs-4 cup_qty">';
							if (value2.Img!=undefined&&value2.Img!='')
							{
								resultado_ajax +='<img class="imagen_multicupon" src="'+value2.Img+'">';
							}
							if (value2.tipo.indexOf('<img')<0)
							{
								resultado_ajax +='<h1>'+value2.tipo.replace(/EUR/g,'€')+'</h1>';
							}
							resultado_ajax +='</div><div class="col-xs-6 cup_name"><h3>';
							resultado_ajax += value2.titulo.replace(/EUR/g,'€');;
							resultado_ajax +='</h3><p>';
							resultado_ajax += value2.texto.replace(/EUR/g,'€');;
							resultado_ajax +='</p>';
							if (value2.diferido==1) resultado_ajax +='<span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							resultado_ajax +='</div><div class="col-xs-2 cup_state"></div></div></div>';
							if (num_elto == 3)
							{
								num_elto = 1;
								resultado_ajax+='</div>';
							}
							else num_elto++;
						});
						if (num_elto!=1) resultado_ajax+='</div>';
						resultado_ajax +='</div></div></div></div>';
						if (primero)
						{
							primero = false;
						}
						else
						{
							//$('.pagina-multi-cupon').html(resultado_ajax);				
						}
					 });
					$('.wrapper-content').html(resultado_ajax);						 
				 }
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}
        $(document).ready(function() {
				obtener_multicupon();
				obtener_vales();
				obtener_vales1(0);
				$('.footable').footable();				
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
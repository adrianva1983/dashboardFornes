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

    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Folletos masymas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
	
	<!-- FooTable -->
    <link href="css/plugins/footable/footable.core.css" rel="stylesheet">

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

    <!-- Libro -->
    <script type="text/javascript" src="../include/libro/turn.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
    <script src="js/plugins/swiper/swiper.min.js"></script>
    <script src="js/app.js" type="text/javascript"></script>

    
    <script>
        
    </script>
	
</head>

<body class="no-skin-config dashboard-page" onload="">
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
                        $seccion = "folleto";
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
            
               
                <div class="pagina pagina-folletos">
                    <div class="barra_cabecera container">
                        <div class="row">
                            <div class="unidad_familiar_perfil">
                                <h2 tkey="cab_folletos_vista">Folletos</h2>
                            </div>
                        </div>
                    </div>       
                    <!--<div class="slider-nav" id="slider-folletos"></div>-->
                    <div class="slider-nav" id="slider-folletos-sinslider"></div>

                </div>

                <div class="pagina pagina-folletos-vista" style="display: none">

                    <div class="barra_cabecera container">
                        <div class="row">
                            <div class="unidad_familiar_perfil">
                                <h2 tkey="cab_folletos_vista">Folleto</h2>
                            </div>
                        </div>
                    </div> 
                    <div class="swiper-container" style="height:0;">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper" id="folleto_nav_paginas" style="display: none">
                            <!-- Slides -->
                        </div>
                        <!-- If we need navigation buttons 
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        -->
                    </div>
                    <div id="plus_ext_box">
                        
                    </div>
                    <div class="but_container container-fluid">
                        <div class="row folletos-botones">                
                            <div class="col-xs-5ths but_prev swiper-button-prev-to">
                                <a href="#" id="prev" onclick="ir_a_pagina_ant();return false;">
                                    <div class="folletos-nav-boton"><img class="folletos_bar_ant" src="img/svg/folletos/anterior.svg" alt="image"  draggable="false"/> <span tkey="Anterior">Anterior</span></div>
                                </a>
                            </div> 				
                            <div class="col-xs-5ths">
                            <a href="#" onclick="cambiar_pagina('','pagina-folletos-grid');return false;">
                                <div class="folletos-nav-boton"><img class="folletos_bar_pag" src="img/svg/folletos/paginas.svg" alt="image"  draggable="false"/> <span tkey="Paginas">Paacuteginas</span></div>
                            </a>				
                            </div>				
                            <div class="col-xs-5ths but_next swiper-button-next-to">
                                <a href="#" id="next" onclick="ir_a_pagina_sgte();return false;">
                                    <div class="folletos-nav-boton"><span tkey="Siguiente">Siguiente</span> <img class="folletos_bar_sig" src="img/svg/folletos/sig.svg" alt="image"  draggable="false"/></div>
                                </a>
                            </div>                
                        </div>
                    </div>
                </div>

                <div class="pagina pagina-folletos-grid" style="display: none">

                    <div class="barra_cabecera container">
                        <div class="row">
                            <div class="unidad_familiar_perfil">
                                <h2 tkey="cab_folletos_vista">Folleto</h2>
                            </div>
                        </div>
                    </div> 
                    <div class="container-fluid grid">
                        <div class="row" id="grid_paginas">
                        </div>
                    </div>

                </div>
                <div class="pagina pagina-folletos-swipers-test" style="display: none">

                    <div class="barra_cabecera container-fluid">
                        <div class="row">
                            <nav class="navbar" role="navigation" style="margin-bottom: 0">
                                <div class="col-xs-2 notificaciones">

                            <a  href="#" onclick="cambiar_pagina('','pagina-folletos');return false;">
                                        <img class="responsive navback" src="img/svg/volver_blanco.svg">
                                    </a>
                                </div>
                                <div class="navbar_logo secun col-xs-8">
                                </div>
                                <div class="col-xs-2">
                            <a  href="#" onclick="cambiar_pagina('','pagina-folletos-grid');return false;">
                                        <img class="responsive navmenu" src="img/svg/folletos/paginas_up.svg">
                                    </a>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="swiper-container">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper">
                            <!-- Slides -->
                            <div class="swiper-slide">
                                <div class="swiper-zoom-container">
                                    <img src="img/png/folletos/folleto_sample.png" alt="image"  draggable="false"/>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-zoom-container">
                                <img src="img/png/folletos/folleto_sample.png" alt="image"  draggable="false"/>
                            </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="swiper-zoom-container">
                                <img src="img/png/folletos/folleto_sample.png" alt="image"  draggable="false"/>
                            </div>
                            </div>
                        </div>
                        <!-- If we need navigation buttons -->
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>

                        <!-- If we need scrollbar -->
                        <div class="swiper-scrollbar"></div>
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
	<script src="/dashboard/lang/lang.js" type="text/javascript"></script>
	<script src="/dashboard/lang/es.js" type="text/javascript"></script>
	<script src="/dashboard/lang/en.js" type="text/javascript"></script>
    <script src="/dashboard/lang/va.js" type="text/javascript"></script>
    
    
    <script>
       // var paginas_folletos = [];       
       //var folleto_atras = '<?=$_GET['folleto_atras']?>';
        function prueba()
        {
            console.log('folleto_atras1111',folleto_atras);
            window.location.hash="no-back-button";
            window.location.hash="Again-No-back-button" //chrome
            window.onhashchange=function(){window.location.hash="no-back-button";}
            /*if (folleto_atras==2)
            {
                window.location.hash="";
                window.location.hash=""
                window.onhashchange=function(){
                    window.location.hash="";
                    console.log('Entro1111111111111');
                }
            }
            else
            {
                window.location.hash="no-back-button";
                window.location.hash="Again-No-back-button"
                window.onhashchange=function(){
                    window.location.hash="no-back-button";
                }
            }*/
        }
        $(document).ready(function() {
            
            obtener_promo_folletos();
			obtener_folletos('<?=$idioma?>');
            
        });
        /*window.addEventListener("hashchange", function(e) 
        {
            console.log('TTTTTTTTTTTT', e);
            if (folleto_atras==1)
            {
                folleto_atras = 2;
                console.log('listado_folletos', listado_folletos);
                //window.location = "folletos.php?";
            }
        })*/
        
            window.addEventListener('popstate', function(event) 
            {   
                if (numero_folletos>1)
                {             
                    console.log('numero_folletos', numero_folletos);
                    console.log('comp', comp);
                    if (comp==1)
                    {
                        comp = 0;
                        history.pushState(null, null, window.location.pathname);
                        history.pushState(null, null, window.location.pathname);
                        window.location.reload();
                    }
                    if (folleto_activo>=0)
                    {
                        comp = 1;
                    }
                    else
                    {
                        history.back();
                    }
                    console.log('comp1', comp);
                }
            }, false);
        
       
    </script>	
</body>
</html>
<?php
require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/cierre.php");
?>
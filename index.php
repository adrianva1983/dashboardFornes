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
    <script src="js/app.js"></script>
    <?php
	require "herramientas/favicon.php";
	?>

</head>

<body class="gray-bg">

    <div class="middle-box text-center lockscreen animated fadeInDown">
        <div class="card_width">
            <div>
                <?php
                    if($_REQUEST['enlace']==1)
                    { 
                        print '<div class="alert alert-success" role="alert">Su contraseña ha sido cambiada correctamente</div>';
                    }
                    else if ($_REQUEST['enlace']==2)
                    {
                        print '<div class="alert alert-danger" role="alert">Enlace de cambio de contraseña no valido</div>';
                    }
                ?>
            </div>
            <div class="m-b-md">
                <img class="image logo-dashboard" src="/Plantillas/Imagenes/logo.svg">
            </div>
            <h3 tkey="masymas">masymas</h3>
            <h3 id="zona_cliente_titulo" class="col-12 mt-4 pt-3 text-center" tkey="Zona_cliente">Zona cliente.</h3>
            <form class="m-t" role="form" action="dashboard.php" method="post" id="form_tarjeta">
                <div class="form-group">					
                    <input type="text" name="dni" class="form-control input_home" placeholder="DNI" required="" tkeyholder="introduceDNI">
				</div>
				<div class="form-group">
                    <input type="number" name="cod_cliente" class="form-control input_home" placeholder="Número tarjeta" required="" tkeyholder="numero_de_tarjeta">
                </div>
                <div class="form-group">
                    <select class="form-control select_idioma input_home" name="select_idioma" onchange="cambiar_idioma($('.select_idioma option:selected').val());return false;">
                        <option value="es">Castellano</option>
                        <option value="va">Valencià</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit"  class="btn btn-primary block full-width" tkey="Entrar">Entrar</button>
                </div>
                
                <div class="form-group my-3 u-size--18" id="form-login--remember">
                    <span class="u-text--regular" tkey="usuario_contraseña">Si prefiere entrar con usuario y contraseña</span>
                    <span class="u-text--semi-bold ml-1 u-link" tkey="pincha" onclick="cambiar_metodo(1);return false;">Pincha aquí.</span>
                </div>
                
            </form>
            <form class="m-t" role="form" action="dashboard.php" method="post" id="form_pass" style="display:none;">
                <div class="form-group">
                    <input autofocus="" class="form-control input_home" id="userName" name="userName" placeholder="DNI" type="text" tkeyholder="introduceDNI">
				</div>
				<div class="form-group">
                    <input class="form-control input_home" id="password" name="password" type="password" placeholder="Contraseña" tkeyholder="Contrasena">
                </div>
                <!--<div class="form-group">
                    <select class="form-control select_idioma input_home" name="select_idioma" id="id_select_idioma_rec" onchange="cambiar_idioma($('.select_idioma option:selected').val());return false;">
                        <option value="es">Castellano</option>
                        <option value="va">Valencià</option>
                        <option value="en">English</option>
                    </select>
                </div>-->
                <div class="form-group">
                    <div class="my-3 u-size--18" id="form-login--remember">
                        <span class="u-text--regular" tkey="recordar_pass">¿Has olvidado la contraseña?</span>
                        <span class="u-text--semi-bold ml-1 u-link" data-toggle="modal" href="#mi_modal" tkey="Recordar">Recordar.</span>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary block full-width" tkey="Entrar">Entrar</button>
                </div>
                <div class="form-group">
                    <a href="https://tarjeta.masymas.com/servicios-web/alta_dasboard.php" class="btn btn-primary block full-width" id="form-login--btn-register" translate="" type="submit" tkey="Registrarse_ahora">Registrarse ahora</a>
                </div>
                <div class="form-group my-3 u-size--18" id="form-login--remember">
                    <span class="u-text--semi-bold ml-1 u-link" translate="" onclick="cambiar_metodo(2);return false;" tkey="Regresar">Regresar.</span>
                </div>
                
            </form>
        </div>
    </div>

    <div class="modal fade" id="mi_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                    <img class="image-component__image ng-star-inserted" src="https://cdn.aktiosdigitalservices.com/tol/fornes/shop-ang/6.5.0-FINAL/css/assets/images/logo/es/logo.png" alt="">
                </div>
                <div class="modal-body">
                    <div class="row" style="padding:15px">
                        <div class="dynamicForm-base d-flex flex-column ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword">
                            <div class="dynamicForm-base-title d-flex flex-row justify-content-center ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword-title">
                                <h2 class="ng-tns-c18-68" tkey="recordar_pass">¿Has olvidado la contraseña?</h2>
                            </div>
                            <div class="dynamicForm-base-container ng-tns-c18-68 ng-star-inserted">
                                <div class="dynamicForm-base-container-step ng-tns-c18-68 ng-trigger ng-trigger-horizontalSlide ng-star-inserted">
                                    
                                    <cmp-dynamic-step-base class="ng-tns-c18-68">
                                        <div class="step-base" id="dynamicStep-form-recoveryPassword-step-email">
                                            <cmp-error-step class="ng-star-inserted">

                                            </cmp-error-step>
                                            <cmp-dynamic-group-base class="mb-2 ng-star-inserted" style="">
                                                <div class="groups-base d-flex flex-wrap" id="dynamicGroup-form-recoveryPassword-step-email-group1">
                                                    <cmp-dynamic-field-base class="field-base col-12 ng-star-inserted" id="form-recoveryPassword-field-email-text">
                                                        <div id="form-recoveryPassword-field-email-text-base" class="ng-star-inserted">
                                                            <cmp-html-field class="ng-tns-c19-69 ng-star-inserted">
                                                                <div class="field-html" id="html-form-recoveryPassword-field-email-text">
                                                                    <div class="u-rounded-8 field-html--neutral">
                                                                        <div class="field-html-container--neutral ng-star-inserted" tkey="recordar_pass_exp">Si no recuerdas tu contraseña, introduce tu DNI y te enviaremos un SMS o un email para que puedas recuperarla.</div>
                                                                    </div>
                                                                </div>
                                                            </cmp-html-field>
                                                        </div>
                                                    </cmp-dynamic-field-base>
                                                    <cmp-dynamic-field-base class="field-base col-12 ng-star-inserted" id="form-recoveryPassword-field-username">
                                                        <div id="form-recoveryPassword-field-username-base" class="ng-star-inserted">
                                                            <cmp-short-text-field class="ng-star-inserted">
                                                                <div class="field-input-label ng-star-inserted" id="form-recoveryPassword-field-username-label"><span tkey="Documento">Documento</span>
                                                                    <label class="ml-1 ng-star-inserted">*</label>
                                                                </div>
                                                                <div class="field-shortText-container" id="">
                                                                    <input id="id_nif_rec" autofocus="" class="form-control input_home" type="text" placeholder="DNI / NIE" maxlength="50" tkeyholder="introduceDNI">
                                                                </div>
                                                                <cmp-error-field>                                                    
                                                                </cmp-error-field>
                                                            </cmp-short-text-field>
                                                        </div>
                                                    </cmp-dynamic-field-base>
                                                </div>
                                            </cmp-dynamic-group-base>
                                            <div class="d-flex flex-column col-6 align-items-center mt-5" id="dynamicStep-footer-row">
                                                <div class="row">
                                                    <div class="col-md-6 col-xs-12">
                                                        <button onclick="recuperar_password();return false;" class="btn btn-primary block full-width" id="dynamicStep-button-main" tkey="mandamelo-por-sms">Aceptar</button>
                                                    </div>
                                                    <div class="col-md-6 col-xs-12">
                                                        <button onclick="recuperar_password_mail();return false;" class="btn btn-primary block full-width" id="dynamicStep-button-main" tkey="solicitar-soporte-mail">Aceptar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </cmp-dynamic-step-base>
                                </div>
                            </div>
                        </div>                 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mi_modal_enviado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span onclick="cerrar_modals_recuperar();return false;" class="sr-only">Cerrar</span>
                    </button>
                    <img class="image-component__image ng-star-inserted" src="https://cdn.aktiosdigitalservices.com/tol/fornes/shop-ang/6.5.0-FINAL/css/assets/images/logo/es/logo.png" alt="">
                </div>
                <div class="modal-body">
                    <div class="row" style="padding:15px">
                        <div class="dynamicForm-base d-flex flex-column ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword">
                            <div class="dynamicForm-base-title d-flex flex-row justify-content-center ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword-title">
                                <h2 class="ng-tns-c18-68" tkey="recordar_pass">¿Has olvidado la contraseña?</h2>
                            </div>
                        </div>                 
                    </div>
                    <cmp-dynamic-group-base class="mb-2 ng-star-inserted" style="">
                        <div class="groups-base d-flex flex-wrap" id="dynamicGroup-">
                            <cmp-dynamic-field-base class="field-base col-12 ng-star-inserted" id="group-end">
                                <div id="group-end-base" class="ng-star-inserted">
                                    <cmp-end-field class="ng-star-inserted">
                                        <div class="field-html" id="html-group-end">
                                            <div class="smile">
                                                <img class="image-component__image ng-star-inserted" src="img/mail-sent.svg" alt="">
                                            </div>
                                            <h4 class="u-text--bold u-size--18 text-center mt-4" tkey="SMS_enviado">Te hemos enviado un SMS</h4>
                                            <h5 class="u-text--regular u-size--16 text-center mt-4" tkey="SMS_enviado_exp">Si los datos que has introducido son correctos recibirás un SMS con los siguientes pasos a realizar para recuperar tu contraseña. <br> <br> En caso de no recibir el sms con la contraseña, ponte en contacto con nosotros a través del teléfono de atención al cliente 900 777 000 o envíanos un correo a contacta.masymas@fornes.net</h5>
                                        </div>
                                    </cmp-end-field>
                                </div>
                            </cmp-dynamic-field-base>
                        </div>
                    </cmp-dynamic-group-base>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mi_modal_enviado_mail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span onclick="cerrar_modals_recuperar();return false;" class="sr-only">Cerrar</span>
                    </button>
                    <img class="image-component__image ng-star-inserted" src="https://cdn.aktiosdigitalservices.com/tol/fornes/shop-ang/6.5.0-FINAL/css/assets/images/logo/es/logo.png" alt="">
                </div>
                <div class="modal-body">
                    <div class="row" style="padding:15px">
                        <div class="dynamicForm-base d-flex flex-column ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword">
                            <div class="dynamicForm-base-title d-flex flex-row justify-content-center ng-tns-c18-68 ng-star-inserted" id="dynamicForm-form-desktop-recoveryPassword-title">
                                <h2 class="ng-tns-c18-68" tkey="recordar_pass">¿Has olvidado la contraseña?</h2>
                            </div>
                        </div>                 
                    </div>
                    <cmp-dynamic-group-base class="mb-2 ng-star-inserted" style="">
                        <div class="groups-base d-flex flex-wrap" id="dynamicGroup-">
                            <cmp-dynamic-field-base class="field-base col-12 ng-star-inserted" id="group-end">
                                <div id="group-end-base" class="ng-star-inserted">
                                    <cmp-end-field class="ng-star-inserted">
                                        <div class="field-html" id="html-group-end">
                                            <div class="smile">
                                                <img class="image-component__image ng-star-inserted" src="img/mail-sent.svg" alt="">
                                            </div>
                                            <h4 class="u-text--bold u-size--18 text-center mt-4" tkey="Email_enviado">Te hemos enviado un Email</h4>
                                            <h5 class="u-text--regular u-size--16 text-center mt-4" tkey="Email_enviado_exp">Si los datos que has introducido son correctos recibirás un email con los siguientes pasos a realizar para recuperar tu contraseña. <br> <br> En caso de no recibir el email, ponte en contacto con nosotros a través del teléfono de atención al cliente 900 777 000 o envíanos un correo a contacta.masymas@fornes.net</h5>
                                        </div>
                                    </cmp-end-field>
                                </div>
                            </cmp-dynamic-field-base>
                        </div>
                    </cmp-dynamic-group-base>
                </div>
            </div>
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
    if (isset($_REQUEST['enlace']))
    {
        echo 'cambiar_metodo(1);';
    }
	?>

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
		if (idioma!='')
		{
			$('.select_idioma').val(idioma);
			$('.select_idioma2').val(idioma);
		}
	});
	</script>	
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-29717040-1', 'auto');
  ga('send', 'pageview');
  function cambiar_metodo(value)
  {
    console.log('0000000000', value);
    if(value==1)
    {
        $('#form_tarjeta').hide();
        $('#form_pass').show();
        $('#userName').val('');
        $('#password').val('');
    }
    else
    {
        $('#form_tarjeta').show();
        $('#form_pass').hide();
        $('#userName').val('');
        $('#password').val('');
    }
  }
  function on_submit()
  {
    $('#form_tarjeta').submit();
  }

</script>
</body>

</html>

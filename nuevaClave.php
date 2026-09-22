<?php if($_REQUEST['tokenUser'] !="" && $_REQUEST['id'] !="") 
{ 
    require($_SERVER['DOCUMENT_ROOT']."/dashboard/Scripts-Gestion/conexion.php");
    $requete = "SELECT * FROM `APP_token_enlace` WHERE `nif`=".$_REQUEST['id']." AND `token_oauth`=".$_REQUEST['tokenUser']." AND `token_caducidad`>='".date("Y-m-d H:i:s", strtotime('+2 hours'))."'";
    $result = mysqli_query($db,$requete);
    //print $requete;
	if (($result) && (mysqli_num_rows($result)>0))
    {
        $listado = mysqli_fetch_object($result);
?>
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
        <div>
            <div class="m-b-md">
            <img class="image logo-dashboard" src="/Plantillas/Imagenes/logo.svg">
            </div>
            <h3 tkey="masymas">masymas</h3>
            <h3 id="zona_cliente_titulo" class="col-12 mt-4 pt-3 text-center" tkey="">Hola Bienvenido, escribe tu nueva clave aquí</h3>
            <h3 class="col-12 mt-4 pt-3 text-center">Enlace válido hasta: <?=date('H:i:s',strtotime($listado->token_caducidad))?></h3>
            <form class="m-t" role="form" action="updateClave.php" method="post" id="form_nuevaClave">
                <input type="text" name="id" value="<?php echo $_REQUEST['id']; ?>" hidden="true">
                <input type="text" name="tokenUser" value="<?php echo $_REQUEST['tokenUser']; ?>" hidden="true">
                <div class="form-group">
					<input type="password" name="nueva_pass" id="nueva_pass" class="form-control input_home" placeholder="Nueva contraseña" required="">
				</div>
				<div class="form-group">
                    <input type="password" name="nueva_pass1" id="nueva_pass1" class="form-control input_home" placeholder="Repetir contraseña" required="">
                </div>
                <div class="form-group">
                    <button onclick="comprobar_pass();return false;" class="btn btn-primary block full-width">Recuperar Ahora</button>
                </div>
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
    <script>
        function comprobar_pass()
        {
            var errores = '';
            if ($('#nueva_pass').val()=='') errores+= temp_lang['error_password']+'<br/>';
		    if ($('#nueva_pass1').val()!=$('#nueva_pass').val()) errores+= temp_lang['error_password2']+'<br/>';
            if ($('#nueva_pass').val()!='') errores += checkStrength2($('#nueva_pass').val());
            if (errores=='')
            {
                $('#form_nuevaClave').submit();
            }
            else
            {
                swal({
                    title: temp_lang["Error"],
                    text: errores,
                    html: true,
                    type: "error"
                });
            }
        }
        function checkStrength2(password) {  
            console.log(password); 
            var strength = 0  
            if (password.length < 6) {  
                return '<span tkey="pssshort">Demasiado corta</span>'  
            }  
            if (password.length > 7) strength += 1  
            // If password contains both lower and uppercase characters, increase strength value.  
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1  
            // If it has numbers and characters, increase strength value.  
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1  
            // If it has one special character, increase strength value.  
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1  
            // If it has two special characters, increase strength value.  
            if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1  
            // Calculated strength value, we can return messages  
            // If value is less than 2  
            if (strength < 2) {   
                return '<span tkey="pssweak">Poco segura</span>';  
            } else if (strength == 2) {   
                return '';  
            } else {   
                return '';  
            }  
        }  
        $(document).ready(function(){
            if (localStorage.getItem('idioma')!=''&&localStorage.getItem('idioma')!='es')
            {
                if (localStorage.getItem('idioma')=='va') 
                {
                    translate(lang_va);
                    temp_lang = lang_va;
                    idioma = 'va';				
                }
                else if (localStorage.getItem('idioma')=='en') 
                {
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
        });
    </script>
</body>
</html>
<?php 
    }
    else
    {
        
        print '<meta http-equiv="refresh" content="0;url=index.php?enlace=2"/>';
        
    }
}else{ ?>
    <meta http-equiv="refresh" content="0;url=index.php?enlace=2"/>
<?php } ?>
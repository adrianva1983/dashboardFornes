/*var sistema_operativo = 'android';
//var sistema_operativo = "ios";
if (sistema_operativo=='android')
{
	//Android
	var enlace_valoracion = 'market://details?id=com.semillaproyectos.masymas.fornes';
	var enlace_valoracion_onclick = '';
}
else
{
	//iPhone
	var enlace_valoracion = '';
	//var enlace_valoracion = 'itms-apps:apps.apple.com/us/app/id1480506414';
	var enlace_valoracion_onclick = "cordova.InAppBrowser.open(\'https://apps.apple.com/us/app/id1480506414\',\'_blank\',\'location=no\');return false;";
}*/
var cod_cliente = '';
var cod_cliente_digital = '';
var codigo_sms = '';
var fidelizado = false;
var escaneado = false;
var necesario_chequeo = true;
var cupon_resaltar = '';
var hogar_origen = '';
var tarjeta_origen = '';
var relacion_tarjetas_hogares;
var respuesta_necesario_chequeo;
var usuario_id = 0;
var origen = 'pagina-portada';
var origen_array = [];
var origen_subseccion = [];
var origen_altura = [];
var origen_nav_deshacer = false;
var pagina_actual = '';
var folleto_activo= -1;
var num_listas = 0;
var num_productos_listas = 0;
var lista_activa = '';
var latitud = 39.4416473;
var longitud = -0.4452905;
var map;
var test=false;
let paginas_folletos = [];
var paginas_descargas = [];
var elems = [];
var elems2 = [];
var switchery = [];
var switchery2 = [];
var num_cupones_activados = 0;
var cupones_activados = [];
var cupones_descargados = [];
var qr_ampliado = false;
var labels_ahorro = [];
var labels_compras = [];
var labels_ahorro_ofertas = [];
var data_ahorro = [];
var data_ahorro_ofertas = [];
var data_compras = [];
var deniedCount = 0;
var listado_tiendas_abierto = 0;
var gps_apagado = 0;
var cambios_hechos_perfil = 0;
var numero_folletos = 0;
var modo_sandbox = '0';
var refrescado_centro = 0;
var db = null;
var folleto_atras = 0;
var tarjetas_asociadas = [];
var num_tarjetas_asociadas = 0;
var pagina_flipbook = 1;
var comp = 0;

if (localStorage.getItem('idioma')!=''&&localStorage.getItem('idioma')!=null&&localStorage.getItem('idioma')!='null'&&localStorage.getItem('idioma')!=undefined&&localStorage.getItem('idioma')!='undefined') 
{		
	var idioma = localStorage.getItem('idioma');
}
else var idioma = 'es';
if (localStorage.getItem('bloquear_en_perfil')!='') var bloquear_en_perfil = localStorage.getItem('bloquear_en_perfil');
else var bloquear_en_perfil = 0;
if (localStorage.getItem('id_multicupon')!='') var id_multicupon = localStorage.getItem('id_multicupon');
else var id_multicupon = '';
if (localStorage.getItem('id_multicupon_siguiente')!='') var id_multicupon_siguiente = localStorage.getItem('id_megacupon_siguiente');
else var id_multicupon_siguiente = '';
if (localStorage.getItem('cod_multicupon')!='') var cod_multicupon = localStorage.getItem('cod_multicupon');
else var cod_multicupon = '';
if (localStorage.getItem('cod_multicupon_siguiente')!='') var cod_multicupon_siguiente = localStorage.getItem('cod_megacupon_siguiente');
else var cod_multicupon_siguiente = '';
if (localStorage.getItem('cupon_desde')!='') var cupon_desde = localStorage.getItem('cupon_desde');
else var cupon_desde = '';
if (localStorage.getItem('cupon_desde_siguiente')!='') var cupon_desde_siguiente = localStorage.getItem('cupon_desde_siguiente');
else var cupon_desde_siguiente = '';
if (localStorage.getItem('cupon_hasta')!='') var cupon_hasta = localStorage.getItem('cupon_hasta');
else var cupon_hasta = '';
if (localStorage.getItem('cupon_hasta_siguiente')!='') var cupon_hasta_siguiente = localStorage.getItem('cupon_hasta_siguiente');
else var cupon_hasta_siguiente = '';
if (localStorage.getItem('numeroAperturas')!=null&&localStorage.getItem('numeroAperturas')!='')
{	
	if (localStorage.getItem('numeroAperturas')<10)
	{
		localStorage.setItem('numeroAperturas',parseInt(localStorage.getItem('numeroAperturas'))+1);
	}
}
else localStorage.setItem('numeroAperturas',1);
var mi_lista = new Object();
var mi_lista_unidades = new Object();
var temp_lang = {};
//added
var listado_tiendas=null;
var cheque_ahorro_cerca_caducar = false;
var watchID;
var marker_ubicacion_actual;

var options = 
{
	zoom: 9,		 
	mapTypeId: 'Styled',
	streetViewControl: false,
	disableDefaultUI: true,
	mapTypeControl:false,
	zoomControl: false,
	fullscreenControl:false,
	mapTypeControlOptions: 
	{
		mapTypeIds: ['Styled']
	}
};
var styles = 
[
	{
		featureType: "road",
		elementType: "geometry",
		stylers: [{lightness: 100}, {visibility: "simplified"}]
	},
	{
		featureType: "road",
		elementType: "labels",
		stylers: [{visibility: "on"}]
	},
	{
		featureType: "poi",
		stylers: [{visibility: "on"}]
	}
];
var newMarker = null;
var markers = [];

var calculadas_coordenadas = false;

//added
document.addEventListener("deviceready", onDeviceReady, false);

function onDeviceReady() {
	db = window.sqlitePlugin.openDatabase({
		name: 'masymasfornes.db',
		location: 'default',
	});		
	db.transaction(function(tx) 
	{			
		tx.executeSql('CREATE TABLE IF NOT EXISTS DatosApp (campo PRIMARY KEY, valor)');
	}, function(error) {			
		console.log('Transaction ERROR: ' + error.message);
	}, function() {			
		console.log('Populated database OK');
	});
	if (localStorage.getItem('cod_cliente')!=''&&localStorage.getItem('cod_cliente')!=null&&localStorage.getItem('cod_cliente')!='undefined')
	{
		LocalStorage2SQLLite();			
	}
	else
	{
		SQLLite2LocalStorage();
	}
    var onSuccess = function(position) {		
       	//seteamos las coordenadas actuales	
       		if(test===false){
	         	latitud=position.coords.latitude ;
	         	longitud= position.coords.longitude ;
	        }
         console.log("nuevas coord:"+latitud+" ** "+longitud);
		 centrar_posicion();		 
		 obtener_tiendas(1);		 
		 refrescarMapa(0);
    };
	var onSuccessRefresco = function(position)
	{	
		
		/*	alert(	'Latitude: '			+ position.coords.latitude			+ '\n' +
					'Longitude: '			+ position.coords.longitude			+ '\n' +
					'Altitude: '			+ position.coords.altitude			+ '\n' +
					'Accuracy: '			+ position.coords.accuracy			+ '\n' +
					'Altitude Accuracy:'	+ position.coords.altitudeAccuracy	+ '\n' +
					'Heading: '				+ position.coords.heading			+ '\n' +
					'Speed: '				+ position.coords.speed				+ '\n' +
					'Timestamp: '			+ position.coords.timestamp			+ '\n');			*/
		latitud=position.coords.latitude ;
		longitud= position.coords.longitude ;		
		$('#latitudGPS_centro').val(latitud);
		$('#longitudGPS_centro').val(longitud);
		$('#latitudGPS2').val(latitud);
		$('#longitudGPS2').val(longitud);
		if ($('#latitudGPS').val()=='') $('#latitudGPS').val(latitud);
		if ($('#longitudGPS').val()=='') $('#longitudGPS').val(longitud);		
		refrescar_centro_gps();
		if (refrescado_centro>0) {}
		else
		{
			refrescado_centro = 1;
			obtener_tiendas(1);
		}
	};


    // onError Callback receives a PositionError object
    //
    function onError(error) {
        console.log('code: '    + error.code    + '\n' +     'message: ' + error.message + '\n');
    }

    navigator.geolocation.getCurrentPosition(onSuccess, onError);
	watchID = navigator.geolocation.watchPosition(onSuccessRefresco, onError, { maximumAge: 30000, enableHighAccuracy: true });					
}
//end added 


function validateEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}
String.prototype.reverse =
function()
{
    splitext = this.split("");
    revertext = splitext.reverse();
    reversed = revertext.join("");
    return reversed;
}
function eanCheckDigit(s)
{
    var result = 0;
    var rs = s.reverse();
    for (counter = 0; counter < rs.length; counter++)
    {
        result = result + parseInt(rs.charAt(counter)) * Math.pow(3, ((counter+1) % 2));
    }
    return (10 - (result % 10)) % 10;
}
function LocalStorage2SQLLite()
{
	var campos_sync = ['numero_tarjeta','fecha_cheque','solo_nombre','cod_cliente_digital','cod_multicupon','dni_cliente','cod_cliente','time_vinculacion','id_home','numeroAperturas','array_vales','ahorro_total_actual','ahorro_canjeable','ahorro_acumulado','ahorro_quedan','data_comprar','labels_compras','labels_ahorro','data_ahorro','fecha_acumulado','labels_ahorro_ofertas','data_ahorro_ofertas','bloques_historicos_chequeahorro'];
	keys = Object.keys(localStorage),
	i = keys.length;
	var sqlstring = [];
	while ( i-- ) 
	{
		if (campos_sync.indexOf(keys[i])>=0)
		{
			var valor_registro = localStorage.getItem(keys[i]);
			valor_registro = valor_registro.replace(/'/g, "\\'");			
			sqlstring.push('INSERT INTO DatosApp (campo,valor) VALUES (\''+keys[i]+'\',\''+valor_registro+'\') ON CONFLICT(campo) DO UPDATE SET valor = \''+valor_registro+'\';');	
		}
	}
	db.transaction(function(tx) 
	{
		//alert(keys[i]+'|'+localStorage.getItem(keys[i]));
		//alert('dentro transaction');
		for (var i=0;i<sqlstring.length;i++)
		{			
			console.log(sqlstring[i]);
			tx.executeSql(sqlstring[i], [], function(tx,rs) 
			{
				console.log('Populated database OK');								
			}, function(tx, error) 
			{
				console.log(error);				
				console.log('SELECT error: ' + error.message);				
			});
		}
	}, function(error) {
		//alert('Transaction ERROR LocalStorage2SQLLite: ' + error.message)
		console.log('Transaction ERROR: ' + error.message);
	}, function() {
		//console.log('Populated database OK ');
		//alert('Populated database OK LocalStorage2SQLLite');
	});	
}
function SQLLite2LocalStorage()
{	
	db.transaction(function(tx) {		
		tx.executeSql('SELECT * FROM DatosApp', [], function(tx, rs) 
		{
			var len = rs.rows.length;
			//alert('registros:'+len);			
			for (var i=0;i<len;i++)
			{
				localStorage.setItem(rs.rows.item(i).campo,rs.rows.item(i).valor);
			}
			if (i>0) window.location.reload(true); //Se ha restaurado de sqllite	
		}, function(tx, error) 
		{
			console.log('SELECT error: ' + error.message);		
		});
	}, function(error) {		
        console.log('transaction error: ' + error.message);
    }, function() {		
        console.log('transaction ok');
    });			
}
function resetea_sqlite()
{
	db.transaction(function(tx) 
	{			
		tx.executeSql('DROP TABLE DatosApp');
	}, function(error) {			
		console.log('Transaction ERROR: ' + error.message);
	}, function() {			
		console.log('Populated database OK');
	});	
}
function validarFormatoFecha(campo) {
	var RegExPattern = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
	if ((campo.match(RegExPattern)) && (campo!='')) return true;
	else return false;
}
function cerrar_sesion(){	
	localStorage.clear();
	resetea_sqlite();
	if (idioma!='') localStorage.setItem('idioma',idioma);
	cambiar_pagina('','pagina-dni_registro');
}
function cambiar_idioma(idioma_seleccionado, cod_cliente_tmp)
{	
	console.log('temp_lang');
	idioma = idioma_seleccionado;
	
	cod_cliente_digital = cod_cliente_tmp;
	console.log('cod_cliente', cod_cliente_digital);
	if (cod_cliente_digital!='') activar_ajuste('ajuste_idioma');
	localStorage.setItem('idioma',idioma);
	//window.location.reload(true);
}

var onSuccessPaginaTiendas = function(position)
{		
		/*
		alert(	'Latitude: '			+ position.coords.latitude			+ '\n' +
				'Longitude: '			+ position.coords.longitude			+ '\n' +
				'Altitude: '			+ position.coords.altitude			+ '\n' +
				'Accuracy: '			+ position.coords.accuracy			+ '\n' +
				'Altitude Accuracy:'	+ position.coords.altitudeAccuracy	+ '\n' +
				'Heading: '				+ position.coords.heading			+ '\n' +
				'Speed: '				+ position.coords.speed				+ '\n' +
				'Timestamp: '			+ position.coords.timestamp			+ '\n');			
		*/	
	$('#latitudGPS_centro').val(position.coords.latitude);
	$('#longitudGPS_centro').val(position.coords.longitude);
	if ($('#latitudGPS').val()==''||$('#longitudGPS').val()==''||($('#latitudGPS').val()==0&&$('#longitudGPS').val()==0))
	{
		window.location.reload(true);
	}
	else
	{
		latitud = position.coords.latitude;
		longitud = position.coords.longitude;
	}
	centrar_posicion();
};
function onErrorAuthorization(error){
    console.error("The following error occurred: "+error);
}
function onErrorPaginaTiendas(error){
	//alert('Houston, tenemos un problema');
}
function evaluateAuthorizationStatus(status){	
    switch(status)
	{
		case cordova.plugins.diagnostic.permissionStatus.NOT_REQUESTED:
		   console.log("Permission not requested");			   			  
		   requestAuthorization();
		   break;
		case cordova.plugins.diagnostic.permissionStatus.DENIED:
		case cordova.plugins.diagnostic.permissionStatus.DENIED_ONCE:
			console.log("Permission denied");			 
			if(deniedCount < 3)
			{
			  deniedCount++;
			  requestAuthorization();
			}
			else
			{
				// Are we sure we want to hassle the user more than 3 times?
			}
			break;
		case cordova.plugins.diagnostic.permissionStatus.DENIED_ALWAYS:
		   console.log("Permission permanently denied");		   
		   deniedCount++;		   
		   navigator.notification.confirm(
				"Esta aplicación necesita acceos a tu ubicación para ofrecer los supermercados cercanos pero ha sido denegado el acceso. ¿Deseas dar acceso a la aplicación?", 
				function (i) {
					if (i === 1) {
						cordova.plugins.diagnostic.switchToSettings();
					}
				}, "Acceso denegado a la ubicación", ['Si', 'No']);
		   break;
		case cordova.plugins.diagnostic.permissionStatus.GRANTED:
		   console.log("Permission granted always");		   
		   navigator.geolocation.getCurrentPosition(onSuccessPaginaTiendas, onErrorPaginaTiendas);		  
		   // Yay! use location
		   break;        
	}	
}
function requestAuthorization(){
    cordova.plugins.diagnostic.requestLocationAuthorization(evaluateAuthorizationStatus, onErrorAuthorization);
}
function checkAuthorization(){
    cordova.plugins.diagnostic.getLocationAuthorizationStatus(evaluateAuthorizationStatus, onErrorAuthorization);
}
function checkGPSON(){
	cordova.plugins.diagnostic.isGpsLocationEnabled(function(enabled)
	{
		if (!enabled)
		{
			gps_apagado = 1;
			swal({
				title:temp_lang['problema_gps'],
				html: true,
				text:"<p>"+temp_lang['gps_apagado']+"</p>",					
				confirmButtonText:'OK',
				showCancelButton:false
			});
		}
		else gps_apagado = 0;
	}, function(error){
		console.error("The following error occurred: "+error);
	});
}
function continuar_cambiar_pagina(origen_accion,destino,registrar_navegacion = true)
{
	if (bloquear_en_perfil==1)
	{
		swal({
			title: temp_lang["NECESITAMOSTUSDATOS"],
			text: temp_lang["necesitamos_datos_texto"],
			html: true
		});		
		if ($('#menu_navegacion').is(":visible")) $('#menu_navegacion').toggle();
		$("html, body").animate({ scrollTop: 0 }, "fast");
		cargar_datos_personales();//Refrescamos la info del perfil
		pagina_actual = 'pagina-perfil';
		origen = '';
		$('.pagina').hide();
		$('.'+destino).fadeIn();

	}
	else
	{
		if (destino!='pagina-tarjeta')
		{
			if (cupon_resaltar!='')
			{
				$('.pagina-tarjeta .cupon_'+cupon_resaltar).removeClass('resaltar_cupon');
				cupon_resaltar = '';
			}
		}
		if (registrar_navegacion)
		{		
			if (origen_array.length>0){}
			else
			{
				origen_array.push('pagina-portada');
				origen_subseccion.push('');
				origen_altura.push(0);
			}
			var subseccion = '';
			if (pagina_actual=='pagina-ahorro-historico') subseccion ='cargar_ahorro();';		
			if (destino==origen_array[origen_array.length-1]&&subseccion==origen_subseccion[origen_subseccion.length-1])
			{
				//No metemos al array de navegación mismo item
			}
			else
			{
				origen_array.push(destino);
				if (destino=='pagina-lista_individual') origen_subseccion.push('activar_lista(\'listas\',\'opcion_mi_lista\',false);');
				else origen_subseccion.push(subseccion);		
				if (pagina_actual!='')
				{
					if (origen_altura.length>0) origen_altura[origen_altura.length-1] = $('.'+pagina_actual+' .container-fluid').scrollTop();
				}
				origen_altura.push(0);
				console.log('CAMBIAR PAGINA:');
				console.log(origen_array);
				console.log(origen_subseccion);
				console.log(origen_altura);
			}
		}		
		//Para que refresque listado tickets y no se quede en el último abierto
		$('.ticket_detalle').fadeOut();$('.tickets_listado').fadeIn();$('.tickets_navegacion').show();
		
		if ($('#menu_navegacion').is(":visible")) $('#menu_navegacion').toggle();
		$("html, body").animate({ scrollTop: 0 }, "fast");
		pagina_actual = destino;
		origen = origen_accion;
		$('.pagina').hide();
		$('.'+destino).fadeIn();
		if (destino=='pagina-home') $('.volver').fadeOut();
		else $('.volver').fadeIn();
		if (destino!='pagina-tarjeta_cargada'&&destino!='pagina-tarjeta_tengo'&&destino!='pagina-tarjeta_chequear'&&destino!='pagina-tarjeta_no_tengo'&&destino!='pagina-tienes_tarjeta'&&destino!='pagina-dni_registro'&&destino!='pagina-tarjeta_generar'&&destino!='pagina-aviso_legal') obtener_chequeahorro();
		switch (destino)
		{
			case 'pagina-tarjeta_generar':
				$('select[name="birthday[day]"]').val('');
				$('select[name="birthday[month]"]').val('');
				$('select[name="birthday[year]"]').val('');
				break;
			case 'pagina-tarjeta_chequear':
				$('.enviar_codigo_sms').show();
				$('.movil_para_verificacion').show();
				$('.codigo_numerico_sms').hide();
				$('.verificar_codigo_sms').hide();
				break;
			case 'pagina-perfil':
				cargar_datos_personales();
				break;
			case 'pagina-home':
				obtener_chequeahorro();
				break;
			case 'pagina-tarjeta':			
				if (cupon_resaltar!='')
				{
					$('.pagina-tarjeta .cupon_'+cupon_resaltar).addClass('resaltar_cupon');												
					//var scroll_cupon_resaltar = $('.pagina-tarjeta .cupon_'+cupon_resaltar).offset().top - $('.codigos_texto_clicados').offset().top;				
					//$("html, body").animate({ scrollTop: scroll_cupon_resaltar });								
				}
				obtener_multicupon();
				if (origen_accion == 'pagina-notificaciones') {
					window.location.href = 'tarjetas.php';
				}			
				break;
				case 'pagina-cupones':
					cupon_resaltar = origen_accion;
					obtener_multicupon('cupon_resaltar', cupon_resaltar);
					if (origen_accion == '') {
						
						window.location.href = 'megacupones.php?cupon_resaltar=1';
					} else {
						window.location.href = 'megacupones.php?cupon_resaltar='+origen_accion;	
					}
						
					
				break;
			case 'pagina-folletos':
				
				//obtener_promo_folletos();
				//obtener_folletos();
				if (origen_accion != '') {
					window.location.href = 'folletos.php';
				}
				//$('.slider-nav').slick("getSlick").refresh();
				//$('.slider-for').slick("getSlick").refresh();
				//$('.single-item').slick("getSlick").refresh();
				//$('.single-item').slick("getSlick").refresh();
				break;
			case 'pagina-ahorro-historico':			
				if (registrar_navegacion) cargar_ahorro();
				break;
			case 'pagina-folletos-vista':			
				cambiar_folleto(folleto_activo);
				//$('.slider-nav').slick("getSlick").refresh();
				//$('.slider-for').slick("getSlick").refresh();
				//$('.single-item').slick("getSlick").refresh();
				//$('.single-item').slick("getSlick").refresh();
				break;
			case 'pagina-tiendas':			
				//if ($('#latitudGPS').val()==''||$('#longitudGPS').val()==''||($('#latitudGPS').val()==0&&$('#longitudGPS').val()==0))
				//{	
					//$('#latitudGPS').show();
					//$('#longitudGPS').show();
					//$('#latitudGPS2').show();
					//$('#longitudGPS2').show();					
					if (gps_apagado==0)
					{												
						console.log('whatchID');
						console.log(watchID);
						if (deniedCount<=0)
						{							
							swal({
								title:temp_lang['espere'],
								html: true,
								text:"<p>"+temp_lang['centrando_ubicacion']+"</p>",					
								confirmButtonText:'OK',
								showCancelButton:false
							});
						}
						else
						{
							swal({
								title:temp_lang['problema_gps'],
								html: true,
								text:"<p>"+temp_lang['permiso_gps_denegado']+"</p>",					
								confirmButtonText:'OK',
								showCancelButton:false
							});						
						}						
					}
					else
					{
						checkAuthorization();
						checkGPSON();
					}
					/*
					alert('sin coordenadas');
					navigator.geolocation.getCurrentPosition(onSuccessPaginaTiendas, onError);
					alert('tenemos_coordenadas');
					*/
				//}
				//obtener_tiendas();				
				$('#lista-tiendas').show();	
				$('#lista-tiendas').addClass('lista_mapa_fixed');
				centrar_posicion();
				//refrescarMapa();
				//ver_mapa();			
				break;
			case 'pagina-notificaciones':
				obtener_mensajes(0,1);
				break;
			case 'pagina-lista-individual':
				activar_lista('listas','opcion_mi_lista',false);
				break;
		}	
	}
}
function cambiar_pagina(origen_accion,destino,registrar_navegacion = true)
{	
	if (bloquear_en_perfil) cambios_hechos_perfil = 0;
	if (cambios_hechos_perfil)
	{
		permitir_navegar = false;
		swal({
			title: temp_lang['perderas_cambios'],
			text: temp_lang['has_hecho_cambios'],
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: temp_lang['confirmar_sin_guardar'],
			cancelButtonText: temp_lang['no_quiero_seguir'],
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm)
		{
			if (isConfirm)
			{
				cambios_hechos_perfil = 0;
				continuar_cambiar_pagina(origen_accion,destino,registrar_navegacion);
			} 
			else 
			{			
			}
		});	
	}
	else continuar_cambiar_pagina(origen_accion,destino,registrar_navegacion);
}
function eliminar_lista(id_lista)
{
	/*Obsoleta, ya no es multilista*/
	$('#'+id_lista+'_menu').remove();
	$('#'+id_lista).remove();
	localStorage.setItem("menu_listas",$('#listado_listas_menu').html());
	localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
}
function ocultar_bloques_lista()
{
	$('.pagina-lista_individual .footer_content').hide();
	$('.pagina-lista_individual .bloque_pie_mi_lista_resto').hide();
}
function mostrar_bloques_lista()
{
	$('.pagina-lista_individual .footer_content').show();
	$('.pagina-lista_individual .bloque_pie_mi_lista_resto').show();
}
function ocultar_bloques_login()
{
	$('.escanear').hide();
	$('.texto_bienvenido').hide();	
	$('.introduce_tarjeta_opciones').css('padding','16vh 0px 20px 0px');
}
function mostrar_bloques_login()
{
	$('.escanear').show();
	$('.texto_bienvenido').show();
	$('.introduce_tarjeta_opciones').css('padding','8px 0px 20px 0px');	
}
function ocultar_bloques_login_nuevo()
{
	$('.registrate').hide();
	$('#datos_cliente_nuevo').css('margin-top','16vh');
}
function mostrar_bloques_login_nuevo()
{
	$('#datos_cliente_nuevo').css('margin-top','2vh');
	$('.registrate').show();
}
function tengo_tarjeta_a_mano()
{
	$('.introduce_tarjeta_opciones').fadeIn();
	$('.boton_escanear_tarjeta').show();
	$('.escanear').show();		
}
function no_tengo_tarjeta_a_mano()
{	
	$('.introduce_tarjeta_opciones').fadeIn();
	$('.boton_escanear_tarjeta').hide();	
}
function verificar_privilegios()
{
	var url = 'https://www.appfornes.es/servicios-web/verificar_privilegios.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente+'&anticache='+(new Date()).getTime();
	url+='&idioma='+idioma;
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url: url,
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			if (res.validacion=='ok'&&res.perdidos_privilegios==1)
			{
				cod_cliente = cod_cliente_digital;				
				localStorage.setItem('cod_cliente',res.cod_cliente);
				localStorage.setItem('numero_tarjeta',res.cod_cliente);
				window.location.reload(true);				
			}
			if (res.validacion=='ok'&&res.id_card_privilegios!=''&&res.id_card_privilegios>0&&res.id_card_privilegios!=cod_cliente)
			{
				cod_cliente = res.id_card_privilegios;
				localStorage.setItem('cod_cliente',res.cod_cliente);
				localStorage.setItem('numero_tarjeta',res.cod_cliente);
				window.location.reload(true);				
			}			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});			
}
function ya_tengo_tarjeta()
{
	var errores = '';
	if ($('#checkBoxLegalRegistro_nuevo:checked').val()==undefined) errores+= temp_lang['error_aviso_legal']+'<br/>';	
	if ($('#dni').val()=='') errores+= temp_lang['error_dni']+'<br/>';	
	if ($('#dni').val()!=''&&!validar_dni($('#dni').val()))
	{		
		errores+=temp_lang['error_formato_DNI'];	
	}
	if (errores=='')
	{
		fidelizado = true;
		var url = 'https://www.appfornes.es/servicios-web/nuevo_registro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&dni_registro='+$('#dni').val()+'&anticache='+(new Date()).getTime();		
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url: url,
			success:function(res, textStatus, XMLHttpRequest)
			{
				var resultado_ajax ='';				
				$.each(res, function(key, value) 
				{
					if (res.validacion=='ok')
					{
						relacion_tarjetas_hogares = res;						
							console.log('0000000000000000',res);
						console.log(res.datos_cliente.nu_mobile);

						if (res.datos_cliente.nu_mobile!=''&&res.tarjeta_recuperable!='') 
						{							
							$('.boton_validar_tarjeta').removeClass('col-xs-12');
							$('.boton_validar_tarjeta').addClass('col-xs-6');
							$('.mandar_sms').show();
						}
						else 
						{
							$('.mandar_sms').hide();
							$('.boton_validar_tarjeta').removeClass('col-xs-6');
							$('.boton_validar_tarjeta').addClass('col-xs-12');
						}
						cambiar_pagina('','pagina-tarjeta_tengo');		
					}
					else
					{
						swal({
							title: temp_lang["Error"],
							text: decodeURIComponent(unescape(res.mensaje)),
							html: true,
							type: "error"
						});
					}
				});			 
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});		
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
function verificar_codigo_sms()
{
	if ($('#codigo_sms').val()!=codigo_sms)
	{
		swal({
			title: temp_lang["Error"],
			text: temp_lang['error_codigo_sms'],
			html: true,
			type: "error"
		});
	}
	else
	{
		vincular_tarjeta_consolidado();
	}
}
function enviar_sms_verificacion()
{
	$('.enviar_codigo_sms').hide();
	var errores = '';
	if ($('#movil_chequeo').val()=='') errores+= temp_lang['error_movil_registro']+'<br/>';	
	if (errores=='')
	{
		var url = 'https://www.appfornes.es/servicios-web/solicitar_codigo.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+$('#numero_tarjeta').val()+'&texto_sms='+temp_lang['texto_sms_codigo']+'&movil='+$('#movil_chequeo').val()+'&anticache='+(new Date()).getTime();
		url+='&idioma='+idioma;
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url: url,
			success:function(res, textStatus, XMLHttpRequest)
			{
				var resultado_ajax ='';
				if (res.validacion=='ok')
				{
					$('.movil_para_verificacion').hide();
					$('.codigo_numerico_sms').show();
					$('.verificar_codigo_sms').show();
					codigo_sms = res.codigo;
				}
				else
				{
					$('.movil_para_verificacion').show();
					$('.codigo_numerico_sms').hide();
					$('.verificar_codigo_sms').hide();
					swal({
						title: temp_lang["Error"],
						text: decodeURIComponent(unescape(res.mensaje)),
						html: true,
						type: "error"
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});	
	}
	else
	{
		$('.enviar_codigo_sms').show();
		$('.movil_para_verificacion').show();
		$('.codigo_numerico_sms').hide();
		$('.verificar_codigo_sms').hide();
		swal({
			title: temp_lang["Error"],
			text: errores,
			html: true,
			type: "error"
		});
	}
}
function check_sandbox()
{
	var url = 'https://www.appfornes.es/servicios-web/check_sandbox.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&anticache='+(new Date()).getTime();
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url: url,
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			modo_sandbox = res.modo_sandbox;
			if (modo_sandbox=='0'&&cod_cliente=='190000002')
			{
				swal({
					title: temp_lang["Error"],
					text: 'Ha finalizado su periodo de prueba. Para utilizar todas las funcionalidades de la APP debe volver a registrarse introduciendo su DNI/NIE.',
					html: true,
					type: "error"
				});
				cerrar_sesion();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});		
}
function solicitar_tarjeta()
{
	$('.registrate').show();
	var errores = '';	
	if ($('#checkBoxLegalRegistro_nuevo:checked').val()==undefined) errores+= temp_lang['error_aviso_legal']+'<br/>';	
	if (sistema_operativo!='android' && modo_sandbox=='1') {}
	else if ($('#dni').val()=='') errores+= temp_lang['error_dni']+'<br/>';	
	if ($('#dni').val()!=''&&!validar_dni($('#dni').val()))
	{		
		errores+=temp_lang['error_formato_DNI'];	
	}
	if (errores=='')
	{
		fidelizado = false;
		var url = 'https://www.appfornes.es/servicios-web/nuevo_registro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&dni_registro='+$('#dni').val()+'&modo_sandbox='+modo_sandbox+'&anticache='+(new Date()).getTime();
		url+='&idioma='+idioma;
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url: url,
			success:function(res, textStatus, XMLHttpRequest)
			{
				var resultado_ajax ='';
				$.each(res, function(key, value) 
				{
					if (res.validacion=='ok')
					{
						relacion_tarjetas_hogares = res;
						if (res.datos_cliente.nu_mobile!='') 
						{
							$('.boton_validar_tarjeta').removeClass('col-xs-12');
							$('.boton_validar_tarjeta').addClass('col-xs-6');
							$('.mandar_sms').show();
						}
						else 
						{
							$('.mandar_sms').hide();
							$('.boton_validar_tarjeta').removeClass('col-xs-6');
							$('.boton_validar_tarjeta').addClass('col-xs-12');
						}
						cambiar_pagina('','pagina-tarjeta_generar');						
					}
					else
					{
						swal({
							title: temp_lang["Error"],
							text: decodeURIComponent(unescape(res.mensaje)),
							html: true,
							type: "error"
						});
					}
				});			 
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});		
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

function vaciar_lista_confirmado()
{
	$('.anadido_en_lista').removeClass('anadido_en_lista');
	$('#listas_detalles_bloque').html('');
	localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
	localStorage.setItem("mi_lista","");
	localStorage.setItem("mi_lista_unidades","");
	mi_lista = new Object();
	mi_lista_unidades = new Object();
}
function vaciar_lista()
{	
	swal({
		title:temp_lang["VaciarLista"],
		html: true,
		text:"<p>"+temp_lang["ConfirmoVaciarLista"]+"</p>",
		cancelButtonText:temp_lang["VACIARLISTA"],
		confirmButtonText:temp_lang["CANCELAR"],
		showCancelButton:true
	},
	function (isConfirm)
	{
		if (!isConfirm) 
		{			
			vaciar_lista_confirmado();
		}
	});
}
function eliminar_item_lista(item,id_item,cupones)
{
	//$('.anadido_en_lista').removeClass('anadido_en_lista');
	if (id_item!='') 
	{			
		if (cupones==1) 
		{
			$('#cupones_bloque_'+id_item).removeClass('anadido_en_lista');
			$('#cupones_seccion_'+id_item).removeClass('anadido_en_lista');	
			$('#cupones_seccion_'+id_item+' .cup_state').html('<a href="#" onclick="anadir_lista(\'cupones_bloque_'+id_item+'\',\''+id_item+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a>');
			$('#cupones_bloque_'+id_item+' .cup_state').html('<a href="#" onclick="anadir_lista(\'cupones_bloque_'+id_item+'\',\''+id_item+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a>');
			delete mi_lista['cupon_'+id_item];
			delete mi_lista_unidades['cupon_'+id_item];
		}
		else 
		{
			$('#habituales_bloque_'+id_item).removeClass('anadido_en_lista');
			$('#habituales_bloque_'+id_item+' .cup_state').html('<a href="#" onclick="anadir_lista(\'habituales_bloque_'+id_item+'\',\''+id_item+'\',\'\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a>');
			delete mi_lista[id_item];
			delete mi_lista_unidades[id_item];
		}		
		console.log(mi_lista);
		localStorage.setItem('mi_lista',JSON.stringify(mi_lista));	
		localStorage.setItem('mi_lista_unidades',JSON.stringify(mi_lista_unidades));
	}
	else
	{		
		var tmp_id_texto = item.split('_');
		delete mi_lista['texto_'+tmp_id_texto[2]];
		console.log(mi_lista);
	}
	$('#'+item).remove();
	localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
}
function marcar_comprado(item,id_item,cupones)
{
	if ($('#'+item+' .prod_activo .icheckbox_square-green').hasClass('checked'))
	{		
		$('#'+item+' .prod_activo .icheckbox_square-green').removeClass('checked');		
		$('#'+item).removeClass('marcado_completado');
		var html_result = $('#'+item).clone();		
		$('#'+item).remove();
		$(html_result).prependTo('#listas_detalles_bloque');		
		//$('#eliminar_'+item).remove();
		$(html_result).show("slow",3000);
	}
	else
	{
		$('#'+item+' .prod_activo .icheckbox_square-green').addClass('checked');
		$('#'+item).addClass('marcado_completado');
		var html_result = $('#'+item).clone();
		$('#'+item).remove();
		$(html_result).appendTo('#listas_detalles_bloque');
		//$('#'+item+' .prod_activo').append('<a href="#" class="eliminar_definitiva_lista" id="eliminar_'+item+'" onclick="eliminar_item_lista(\''+item+'\',\''+id_item+'\',\''+cupones+'\');return false;"><i class="fa fa-trash"></i></a>');
		$(html_result).show("slow",3000);
	}
	localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
}
function crear_lista()
{
	/*Obsoleta, ya no hay multilista*/
	num_listas++;
	localStorage.setItem("num_listas",num_listas);
	var html_tmp = '<div class="row listas" id="lista'+num_listas+'_menu"><div class="lista_cab colbutton">';
	html_tmp+='<a class="more_left remove" onclick="eliminar_lista(\'lista'+num_listas+'\');return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
	html_tmp+='<a class="divclicker" href="#" onclick="lista_activa=\'lista'+num_listas+'\';$(\'.listas_detalles\').hide();$(\'#lista'+num_listas+'\').show();cambiar_pagina(\'pagina-lista\',\'pagina-lista_individual\');return false;">';
	html_tmp+='</a>';
	html_tmp+='<h2 class="">'+$('#nueva_lista_nombre').val()+'</h2><p class="more_right flecha"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p>';
	html_tmp+='</div></div>';	
	var html_tmp_detalle ='<div id="lista'+num_listas+'" class="container-fluid listas_detalles"><p style="padding: 20px;text-align: center;">Añade productos a la lista de la compra.</p></div>';
	$('#listado_listas_menu').append(html_tmp);
	$('#listas_detalles_bloque').append(html_tmp_detalle);
	localStorage.setItem("menu_listas",$('#listado_listas_menu').html());
	localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
	$('.cont_lista_nueva').hide();	
}
function scan_barcode(reintento)
{
	cordova.plugins.barcodeScanner.scan(
		function (result) {
			$('#numero_tarjeta').val(result.text);
			tarjeta_origen = result.text;
			escaneado = true;
			
			vincular_tarjeta();						
			/*alert ("We got a barcode\n" +
				"Result: " + result.text + "\n" +
				"Format: " + result.format + "\n" +
				"Cancelled: " + result.cancelled);*/
		}, 
		function (error) 
		{			
		},
		{
			preferFrontCamera : false, // iOS and Android
			showFlipCameraButton : false, // iOS and Android
			showTorchButton : false, // iOS and Android
			torchOn: false, // Android, launch with the torch switched on (if available)
			saveHistory: true, // Android, save scan history (default false) prompt : "Coloca Place a barcode inside the scan area", // Android
			resultDisplayDuration: 500, // Android, display scanned text for X ms. 0 suppresses it entirely, default 1500 //formats : "EAN_13", // default: all but PDF_417 and RSS_EXPANDED
			orientation : "landscape", // Android only (portrait|landscape), default unset so it rotates with the device
			disableAnimations : true, // iOS
			disableSuccessBeep: false // iOS and Android
		}
	);
}
function activar_lista(bloque,solapa,registrar_navegacion = true)
{
	if (registrar_navegacion)
	{
		if (origen_array[origen_array.length-1]=='pagina-lista_individual'&&origen_subseccion[origen_subseccion.length-1]=='activar_lista(\''+bloque+'\',\''+solapa+'\',false);')
		{
			//No repetir item
		}
		else
		{
			origen_subseccion.push('activar_lista(\''+bloque+'\',\''+solapa+'\',false);');		
			origen_array.push('pagina-lista_individual');	
			if (origen_altura.length>0) origen_altura[origen_altura.length-1] = $('.pagina-lista_individual .container-fluid').scrollTop();
			origen_altura.push(0);
			console.log('ACTIVAR_LISTA');
			console.log(origen_array);
			console.log(origen_subseccion);
			console.log(origen_altura);
		}
	}
	//$("html, body").animate({ scrollTop: 0 }, "fast");
	$('.bloque-lista').hide();
	$('.opciones_mi_lista').removeClass('seccion_lista_activa');		
	if (bloque=='listas')
	{
		$('.pagina-lista_individual .footer_content').show();		
		$('.bloque_pie_mi_lista_resto').hide();				
	}	
	else
	{
		$('.pagina-lista_individual .footer_content').hide();
		$('.bloque_pie_mi_lista_resto').show();		
	}
	$('.'+bloque).fadeIn();
	$('#'+solapa).addClass('seccion_lista_activa');
}
function cargar_top_habituales()
{
	$.ajax({
		type:'GET',
		timeout: 3000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/habituales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente+'&id_home='+id_home+'&idioma='+idioma,
		success:function(res, textStatus, XMLHttpRequest)
		{			
			var resultado_ajax ='';			
			$.each(res.habituales, function(key, value) {
				resultado_ajax += '<div class="row';
				if (mi_lista[value.CODIGO_ARTICULO]!=undefined&&mi_lista[value.CODIGO_ARTICULO]!='') resultado_ajax += ' anadido_en_lista';
				resultado_ajax += '" id="habituales_bloque_'+value.CODIGO_ARTICULO+'"><div class="col-xs-12 listado-productos">';
				if (value.DESCRIPCION!=null&&value.DESCRIPCION!='') resultado_ajax += '<div class="col-xs-10"><span class="habitual_nombre">'+value.DESCRIPCION+'</span></div>';
				else resultado_ajax+= '<div class="col-xs-10"<span class="habitual_nombre">COD:'+value.CODIGO_ARTICULO+'</span></div>';								
				resultado_ajax += '<div style="display:none;" class="col-xs-2"><div class="botonera"><input id="habituales_valor_'+value.CODIGO_ARTICULO+'" type="text" size=2 value="1"> Unds.</div></div>';
				resultado_ajax += '<div class="col-xs-2 cup_state">';
				if (mi_lista[value.CODIGO_ARTICULO]!=undefined&&mi_lista[value.CODIGO_ARTICULO]!='') resultado_ajax+='<a href="#" onclick="eliminar_item_lista(\'habituales_lista_prod_'+value.CODIGO_ARTICULO+'\',\''+value.CODIGO_ARTICULO+'\',\'0\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a></div>';			
				else resultado_ajax+='<a href="#" onclick="anadir_lista(\'habituales_bloque_'+value.CODIGO_ARTICULO+'\',\''+value.CODIGO_ARTICULO+'\',\'\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a></div>';				
				resultado_ajax += '</div></div>';
																					
			});
			if (res.habituales_texto!='') $('.opcion_mi_lista_habituales').html('<p class="alert alert-danger">'+res.habituales_texto+'</p>');
			$('.opcion_mi_lista_habituales').html(resultado_ajax);
			if (res.habituales_texto!='') $('.opcion_mi_lista_habituales').html('<p class="alert alert-danger">'+res.habituales_texto+'</p>');
			localStorage.setItem('habituales',resultado_ajax);
			var resultado_ajax ='';
			$.each(res.ocasionales, function(key, value) {
				resultado_ajax += '<div class="row';
				if (mi_lista[value.CODIGO_ARTICULO]!=undefined&&mi_lista[value.CODIGO_ARTICULO]!='') resultado_ajax += ' anadido_en_lista';
				resultado_ajax += '" id="habituales_bloque_'+value.CODIGO_ARTICULO+'"><div class="col-xs-12 listado-productos">';
				if (value.DESCRIPCION!=null&&value.DESCRIPCION!='') resultado_ajax += '<div class="col-xs-10"><span class="habitual_nombre">'+value.DESCRIPCION+'</span></div>';
				else resultado_ajax+= '<div class="col-xs-10"<span class="habitual_nombre">COD:'+value.CODIGO_ARTICULO+'</span></div>';								
				resultado_ajax += '<div style="display:none;" class="col-xs-2"><div class="botonera"><input id="habituales_valor_'+value.CODIGO_ARTICULO+'" type="text" size=2 value="1"> Unds.</div></div>';
				resultado_ajax += '<div class="col-xs-2 cup_state"><a href="#" onclick="anadir_lista(\'habituales_bloque_'+value.CODIGO_ARTICULO+'\',\''+value.CODIGO_ARTICULO+'\',\'\',\'\');return false;" ';				
				resultado_ajax += 'class="marcar';
				resultado_ajax += ' boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a></div>';
				resultado_ajax += '</div></div>';
																					
			});
			$('.opcion_mi_lista_ocasionales').html(resultado_ajax);			
			if (res.ocasionales_texto!='') $('.opcion_mi_lista_ocasionales').html('<p class="alert alert-danger">'+res.ocasionales_texto+'</p>');
			localStorage.setItem('ocasionales',resultado_ajax);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			$('.opcion_mi_lista_ocasionales').html(localStorage.getItem('ocasionales'));
			$('.opcion_mi_lista_habituales').html(localStorage.getItem('habituales'));			
		}
	});
}
function incrementar_unidades(id_item,tipo)
{
	var tmp_html = $('#listas_detalles_bloque').html();
	if (tipo=='texto')
	{
		mi_lista_unidades['texto_'+id_item] = parseInt($('#count'+id_item).val())+1;	
		var valor_anterior = parseInt($('#count'+id_item).val());
		$('#count'+id_item).val(parseInt($('#count'+id_item).val())+1);
		tmp_html = tmp_html.replace('<input class="prod_qty" value="'+valor_anterior+'" type="text" id="count'+id_item+'">','<input class="prod_qty" value="'+(parseInt($('#count'+id_item).val()))+'" type="text" id="count'+id_item+'">');			
	}
	else
	{
		mi_lista_unidades[id_item] = parseInt($('#count_habituales_'+id_item).val())+1;	
		var valor_anterior = parseInt($('#count_habituales_'+id_item).val());
		$('#count_habituales_'+id_item).val(parseInt($('#count_habituales_'+id_item).val())+1);
		$('#habituales_valor_'+id_item).val(parseInt($('#count_habituales_'+id_item).val()));
		tmp_html = tmp_html.replace('<input class="prod_qty" value="'+valor_anterior+'" type="text" id="count_habituales_'+id_item+'">','<input class="prod_qty" value="'+(parseInt($('#count_habituales_'+id_item).val()))+'" type="text" id="count_habituales_'+id_item+'">');		
	}	
	localStorage.setItem('mi_lista_unidades',JSON.stringify(mi_lista_unidades));
	localStorage.setItem("listas_detalles_bloque",tmp_html);	
}
function decrementar_unidades(id_item,tipo)
{
	var tmp_html = $('#listas_detalles_bloque').html();
	if (tipo=='texto')
	{
		var valor_anterior = parseInt($('#count'+id_item).val());		
		if ((parseInt($('#count'+id_item).val())-1)>0)
		{
			mi_lista_unidades['texto_'+id_item] = parseInt($('#count'+id_item).val())-1;	
			$('#count'+id_item).val(parseInt($('#count'+id_item).val())-1);
			tmp_html = tmp_html.replace('<input class="prod_qty" value="'+valor_anterior+'" type="text" id="count'+id_item+'">','<input class="prod_qty" value="'+(parseInt($('#count'+id_item).val()))+'" type="text" id="count_habituales_'+id_item+'">');		
		}
		else
		{
			//marcar_comprado('lista_prod_'+id_item,id_item,0);
		}
	}
	else
	{
		var valor_anterior = parseInt($('#count_habituales_'+id_item).val());
		if ((parseInt($('#count_habituales_'+id_item).val())-1)>0)
		{
			mi_lista_unidades[id_item] = parseInt($('#count_habituales_'+id_item).val())-1;	
			$('#count_habituales_'+id_item).val(parseInt($('#count_habituales_'+id_item).val())-1);
			$('#habituales_valor_'+id_item).val(parseInt($('#count_habituales_'+id_item).val()));
			tmp_html = tmp_html.replace('<input class="prod_qty" value="'+valor_anterior+'" type="text" id="count_habituales_'+id_item+'">','<input class="prod_qty" value="'+(parseInt($('#count_habituales_'+id_item).val()))+'" type="text" id="count_habituales_'+id_item+'">');		
		}
		else
		{
			//marcar_comprado('habituales_lista_prod_'+id_item,id_item,0);
		}
	}
	localStorage.setItem("listas_detalles_bloque",tmp_html);
	localStorage.setItem('mi_lista_unidades',JSON.stringify(mi_lista_unidades));
}
function anadir_lista(id_bloque,id_item,time_caducidad,tmp2)
{		
	var tmp = id_bloque.split('_');
	var cantidad_anadida = 0;
	if (tmp[0]=='cupones')
	{		
		cantidad_anadida = parseInt($('#cupones_lista_prod_'+id_item+' input').val());
		if (cantidad_anadida>0)
		{
			//No se hace nada si el cupón ya está añadido			
		}
		else
		{			
			$('#cupones_bloque_'+id_item).addClass('anadido_en_lista');	
			$('#cupones_bloque_'+id_item+' .cup_state').html('<a href="#" onclick="eliminar_item_lista(\'cupones_lista_prod_'+id_item+'\',\''+id_item+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a>');
			$('#cupones_seccion_'+id_item+' .cup_state').html('<a href="#" onclick="eliminar_item_lista(\'cupones_lista_prod_'+id_item+'\',\''+id_item+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a>');
			//$('#cupones_seccion_'+id_item).addClass('anadido_en_lista');
			var html_tmp ='';
			html_tmp += '<div class="row lista_prod" id="cupones_lista_prod_'+id_item+'"><input type="hidden" class="time_caducidad" value="'+time_caducidad+'"/><div class="col-xs-8 col-xs-offset-1 prod_data">';
			html_tmp += $('#'+id_bloque+' .cup_name').html();
			html_tmp += '</div><div class="col-xs-2 prod_activo"><div class="icheckbox_square-green" onclick="marcar_comprado(\'cupones_lista_prod_'+id_item+'\',\''+id_item+'\',1);return false;" id="inputP_cupones_'+id_item+'"></div>';
			html_tmp += '<a href="#" class="eliminar_definitiva_lista" id="eliminar_cupones_lista_prod_'+id_item+'" onclick="eliminar_item_lista(\'cupones_lista_prod_'+id_item+'\',\''+id_item+'\',\'1\');return false;"><i class="fa fa-trash"></i></a>';
			html_tmp += '</div></div>';			
			$('#listas_detalles_bloque').prepend(html_tmp);
			localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());	
		}
		mi_lista['cupon_'+id_item] = $('#'+id_bloque+' .cup_name').text();
		mi_lista_unidades['cupon_'+id_item] = 1;
	}
	else
	{		
		$('#habituales_bloque_'+id_item+' .cup_state').html('<a href="#" onclick="eliminar_item_lista(\'habituales_lista_prod_'+id_item+'\',\''+id_item+'\',\'0\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a>');
		cantidad_anadida = parseInt($('#habituales_lista_prod_'+id_item+' input').val());
		if (cantidad_anadida>0)
		{
			var cantidad = parseInt($('#habituales_lista_prod_'+id_item+' input').val()) + parseInt($('#habituales_valor_'+id_item).val());
			$('#habituales_lista_prod_'+id_item+' input').val(cantidad);
		}
		else
		{
			cantidad_anadida = parseInt($('#habituales_valor_'+id_item).val());
			$('#habituales_bloque_'+id_item).addClass('anadido_en_lista');
			var html_tmp ='';
			html_tmp += '<div class="row lista_prod" id="habituales_lista_prod_'+id_item+'"><div class="col-xs-8 col-xs-offset-1 prod_data">';
			html_tmp += '<p class="prod_name">'+$('#'+id_bloque+' .listado-productos .habitual_nombre').html()+'</p><p class="prod_env"></p>';
			html_tmp += '<input type="button" class="inputpm sub" onclick="decrementar_unidades(\''+id_item+'\');return false;" value="-"><input class="prod_qty" value="'+parseInt($('#habituales_valor_'+id_item).val())+'" type="text" id="count_habituales_'+id_item+'"><input type="button" value="+" onclick="incrementar_unidades(\''+id_item+'\');return false;" class="inputpm add"> <p class="uds">'+temp_lang['uds']+'</p>';
			html_tmp += '</div><div class="col-xs-2 prod_activo"><div class="icheckbox_square-green" onclick="marcar_comprado(\'habituales_lista_prod_'+id_item+'\',\''+id_item+'\',0);return false;" id="inputP_habituales_'+id_item+'"></div>';
			html_tmp += '<a href="#" class="eliminar_definitiva_lista" id="eliminar_'+id_item+'" onclick="eliminar_item_lista(\'habituales_lista_prod_'+id_item+'\',\''+id_item+'\',\'0\');return false;"><i class="fa fa-trash"></i></a>';
			html_tmp += '</div></div>';
			$('#listas_detalles_bloque').prepend(html_tmp);
			localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());	
		}
		mi_lista[id_item] = $('#'+id_bloque+' .listado-productos .habitual_nombre').text();
		mi_lista_unidades[id_item] = cantidad_anadida;
	}
	localStorage.setItem('mi_lista',JSON.stringify(mi_lista));	
	localStorage.setItem('mi_lista_unidades',JSON.stringify(mi_lista_unidades));
}
function anadir_producto_lista()
{
	if ($('#nom_prod_nuevo').val()=='')
	{
		swal({
			title: temp_lang["Error"],
			text: temp_lang["ErrorProducto"],
			html: true,
			type: "error"
		});
	}
	else
	{
		$('.cortina-lista').hide();
		if ($('#'+lista_activa).html()=='<p style="padding: 20px;text-align: center;">Añade productos a la lista de la compra.</p>') $('#'+lista_activa).html('');
		num_productos_listas++;
		var html_tmp ='';
		html_tmp += '<div class="row lista_prod" id="lista_prod_'+num_productos_listas+'"><div class="col-xs-8 col-xs-offset-1 prod_data">';
		html_tmp += '<p class="prod_name">'+$('#nom_prod_nuevo').val()+'</p><p class="prod_env"></p>';
		html_tmp += '<input type="button" class="inputpm sub" onclick="decrementar_unidades(\''+num_productos_listas+'\',\'texto\');return false;" value="-"><input class="prod_qty" value="'+$('#prod_qyy_nuevo').val()+'" type="text" id="count'+num_productos_listas+'"><input type="button" value="+" onclick="incrementar_unidades(\''+num_productos_listas+'\',\'texto\');return false;" class="inputpm add"> <p class="uds">'+temp_lang['uds']+'</p>';
		html_tmp += '</div><div class="col-xs-2 prod_activo"><div class="icheckbox_square-green" onclick="marcar_comprado(\'lista_prod_'+num_productos_listas+'\',\'\',0);return false;" id="inputP'+num_productos_listas+'"></div>';
		html_tmp += '<a href="#" class="eliminar_definitiva_lista" id="eliminar_lista_prod_'+num_productos_listas+'" onclick="eliminar_item_lista(\'lista_prod_'+num_productos_listas+'\',\'\',\'0\');return false;"><i class="fa fa-trash"></i></a>';
		html_tmp += '</div></div>';
		mi_lista['texto_'+num_productos_listas] = $('#nom_prod_nuevo').val();
		localStorage.setItem('mi_lista',JSON.stringify(mi_lista));
		mi_lista_unidades['texto_'+num_productos_listas] = $('#prod_qyy_nuevo').val();
		localStorage.setItem('mi_lista_unidades',JSON.stringify(mi_lista_unidades));
		/*
		Obsoleto, para multilista
		$('#'+lista_activa).append(html_tmp);
		*/
		$('#listas_detalles_bloque').prepend(html_tmp);
		localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());	
		$('.add_prod').toggle();
		$('#nom_prod_nuevo').val('');
		$('#prod_qyy_nuevo').val('1');
	}
}
function cargar_ticket(id_ticket)
{
	$('.tickets_listado').fadeOut();
	$('.tickets_navegacion').hide();	
	$('.ticket_detalle').fadeIn();
	$("html, body").animate({ scrollTop: 0 }, "fast");	
	$('#tab-3-historico .ticket_detalle').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_ticket.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&id_ticket='+id_ticket+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				resultado_ajax+='<div class="row linea-tickets encabezado_detalle_ticket"><div class="col-xs-8">'+temp_lang['Fecha']+'</div><div class="col-xs-4">'+temp_lang['TotalTicket']+'</div><div class="col-xs-8 valor_cabecera_ticket"><i class="fa fa-calendar"></i> '+res.fecha+' '+res.hora+'</div><div class="col-xs-4 valor_cabecera_ticket"><span class="label">'+res.total_ticket+'</span></div><div class="col-xs-1"></div><div class="col-xs-9 numero_ticket_detalle">'+temp_lang['NUMEROTICKET']+'<br>'+res.num_ticket+'</div><div class="col-xs-1"></div></div>';
				$.each(res.lineas, function(key, value) {
					resultado_ajax+='<div class="row linea-ticket"><div class="col-xs-9">'+value.producto+'<br><span class="label">'+value.precio+'</span> <span class="label">'+value.cantidad+temp_lang['uds']+'</span></div><div class="col-xs-3"><span class="label">'+value.importe+'</span></div></div>';
				});
				resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.ticket_detalle\').fadeOut();$(\'.tickets_listado\').fadeIn();$(\'.tickets_navegacion\').show();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
				$('#tab-3-historico .ticket_detalle').html(resultado_ajax);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});	
}
document.addEventListener('scroll', function (event) {
	//console.log(event.target);
    if (event.target.id === 'idOfUl') { // or any other filtering condition        
        console.log('scrolling', event.target);
    }
}, true /*Capture event*/);
function cargar_ticket_texto(id_ticket,registrar_navegacion = true)
{
	if (registrar_navegacion)
	{
		if (origen_array[origen_array.length-1]=='pagina-ahorro-historico'&&origen_subseccion[origen_subseccion.length-1]=='cargar_ticket_texto(\''+id_ticket+'\',false);')
		{
			//No repetir item en navegación
		}
		else
		{
			origen_subseccion.push('cargar_ticket_texto(\''+id_ticket+'\',false);');		
			origen_array.push('pagina-ahorro-historico');
			if (origen_altura.length>0) origen_altura[origen_altura.length-1] = $('.pagina-ahorro-historico .container-fluid').scrollTop();
			origen_altura.push(0);		
		}
	}
	$('.tickets_listado').fadeOut();
	$('.tickets_navegacion').hide();	
	$('.ticket_detalle').fadeIn();
	$('#tab-3-historico .ticket_detalle').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_ticket_texto.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&id_ticket='+id_ticket+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('res', res);
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				//resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.ticket_detalle\').fadeOut();$(\'.tickets_listado\').fadeIn();$(\'.tickets_navegacion\').show();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
				resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="volver();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
				//resultado_ajax+='<div class="ticket_formato_texto"><xmp>'+res.texto_ticket+'</xmp></div>';
				resultado_ajax+='<div class="ticket_formato_texto">'+res.texto_ticket+'</div>';
				//resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="$(\'.ticket_detalle\').fadeOut();$(\'.tickets_listado\').fadeIn();$(\'.tickets_navegacion\').show();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
				resultado_ajax+='<div class="botonera_detalle_ticket"><div class="btn-group"><a href="#" onclick="volver();return false;" class="btn btn-primary">'+temp_lang['VOLVER']+'</a></div></div>';
				$('#tab-3-historico .ticket_detalle').html(resultado_ajax);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});	
}
function empezar_navegacion()
{
	var script_anterior = '';
	origen_subseccion = [];
	var scroll_inicial = 0;
	origen_altura = [];
	origen_array = [];	
	cambiar_pagina('pagina-portada','pagina-portada');
}
function cargar_tickets(indice,registrar_navegacion = true)
{
	$('.ticket_detalle').fadeOut();
	$('.tickets_listado').fadeIn();
	$('.tickets_navegacion').show();
	if (registrar_navegacion)
	{
		if (origen_array[origen_array.length-1]=='pagina-ahorro-historico'&&origen_subseccion[origen_subseccion.lenght-1])
		{
			//No repetir item en navegación
		}
		else
		{			
			origen_subseccion.push('cargar_tickets(\''+indice+'\',false);');		
			origen_array.push('pagina-ahorro-historico');	
			if (origen_altura.length>0) origen_altura[origen_altura.length-1] = $('.pagina-ahorro-historico .container-fluid').scrollTop();
			origen_altura.push(0);		
			console.log('CARGAR_TICKETS:');
			console.log(origen_array);
			console.log(origen_subseccion);
			console.log(origen_altura);	
		}
	}
	$('.tab-panel').hide();
	$('#tab-3-historico').show();
	$('.botonera-ahorro').removeClass('activo');
	$('.boton-tickets').addClass('activo');
	$('.tickets_navegacion .btn-group').html('');
	$('#tab-3-historico .tickets_listado').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_tickets.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&indice='+indice+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				$.each(res.tickets, function(key, value) {
					resultado_ajax+='<div class="row linea-tickets"><div class="col-xs-5"><i class="fa fa-calendar"></i> '+value.fecha+' '+value.hora+'</div><div class="col-xs-4"><span class="label">'+value.importe+'</span></div><div class="col-xs-3"><a href="#" class="btn btn-primary" onclick="cargar_ticket_texto(\''+value.id+'\');return false;">VER ></a></div></div>';
				});
				$('#tab-3-historico .tickets_listado').html(resultado_ajax);
				var html_botones = '';
				if (res.mes_anterior!='') html_botones += '<a href="#" class="btn btn-primary" onclick="cargar_tickets('+(parseInt(indice)+1)+');return false">'+res.mes_anterior+'</a>';
				html_botones += '<a href="#" class="btn btn-primary" onclick="empezar_navegacion();return false"><i class="fa fa-home"></i></a>';
				if (res.mes_siguiente!='') html_botones += '<a href="#" class="btn btn-primary" onclick="cargar_tickets('+(parseInt(indice)-1)+');return false">'+res.mes_siguiente+'</a>';
				/*
				if (indice>0) html_botones += '<a href="#" class="btn btn-primary" onclick="cargar_tickets('+(indice-1)+');return false">MES SIGUIENTE</a>';
				if (indice<5) html_botones += '<a href="#" class="btn btn-primary" onclick="cargar_tickets('+(indice+1)+');return false">MES ANTERIOR</a>';
				*/				
				$('.tickets_navegacion').html(html_botones);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});
}
function cargar_compras()
{
	$('.tab-panel').hide();
	$('#tab-2-historico').show();
	$('.botonera-ahorro').removeClass('activo');
	$('.boton-compras').addClass('activo');

	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_compras.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				data_compras = [];
				labels_compras = [];
				$.each(res.compras, function(key, value) {
					labels_compras.push(value.fecha);
					data_compras.push(value.valor);
				});
				localStorage.setItem('data_compras',JSON.stringify(data_compras));
				localStorage.setItem('lavels_compras',JSON.stringify(labels_compras));
				var tmp1 = data_compras.reverse();
				var tmp2 = labels_compras.reverse();
				var barData = {				
					labels: tmp2,
					datasets: [
						{
						label: "Consumo",
						fillColor: "rgba(2,108,80,1)",
						strokeColor: "rgba(220,220,220,0.8)",
						highlightFill: "rgba(2,108,80,0.75)",
						highlightStroke: "rgba(220,220,220,1)",
						data: tmp1
						}						
					]
				};
				var barOptions = {
					scaleBeginAtZero: true,
					scaleShowGridLines: true,
					scaleGridLineColor: "rgba(0,0,0,.05)",
					scaleGridLineWidth: 1,
					barShowStroke: true,
					barStrokeWidth: 2,
					barValueSpacing: 10,
					barDatasetSpacing: 1,
					responsive: true
				}
				var ctx2 = document.getElementById("barChart2").getContext("2d");
				var myNewChart2 = new Chart(ctx2).Bar(barData, barOptions);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			data_compras = [];
			labels_compras = [];
			if (localStorage.getItem('data_compras')!=''&&localStorage.getItem('data_compras')!=undefined&&localStorage.getItem('data_compras')!='undefined') data_compras = JSON.parse(localStorage.getItem('data_compras'));
			if (localStorage.getItem('labels_compras')!=''&&localStorage.getItem('labels_compras')!=undefined&&localStorage.getItem('labels_compras')!='undefined') labels_compras = JSON.parse(localStorage.getItem('labels_compras'));
			var tmp1 = data_compras.reverse();
			var tmp2 = labels_compras.reverse();
			var barData = {
				labels: tmp2,
				datasets: [
					{
					label: "Consumo",
					fillColor: "rgba(2,108,80,1)",
					strokeColor: "rgba(220,220,220,0.8)",
					highlightFill: "rgba(2,108,80,0.75)",
					highlightStroke: "rgba(220,220,220,1)",
					data: tmp1
					}						
				]
			};
			var barOptions = {
				scaleBeginAtZero: true,
				scaleShowGridLines: true,
				scaleGridLineColor: "rgba(0,0,0,.05)",
				scaleGridLineWidth: 1,
				barShowStroke: true,
				barStrokeWidth: 2,
				barValueSpacing: 10,
				barDatasetSpacing: 1,
				responsive: true
			}
			var ctx2 = document.getElementById("barChart2").getContext("2d");
			var myNewChart2 = new Chart(ctx2).Bar(barData, barOptions);
		}
	});
}
function cargar_ahorro_ofertas(registrar_navegacion = true)
{
	if (registrar_navegacion)
	{
		if (origen_array[origen_array.length-1]=='pagina-ahorro-historico'&&origen_subseccion[origen_subseccion.length-1]=='cargar_ahorro_ofertas(false);')
		{
			//No repetir item en navegación
		}
		else
		{
			origen_subseccion.push('cargar_ahorro_ofertas(false);');		
			origen_array.push('pagina-ahorro-historico');	
			origen_altura.push(0);
		}
	}
	$('.tab-panel').hide();
	$('#tab-4-historico').show();
	$('.botonera-ahorro').removeClass('activo');
	$('.boton-ahorro-ofertas').addClass('activo');

	var barData = {				
		labels: labels_ahorro_ofertas,
		datasets: [
			{
			label: "Ahorro",
			fillColor: "rgba(2,108,80,1)",
			strokeColor: "rgba(220,220,220,0.8)",
			highlightFill: "rgba(2,108,80,0.75)",
			highlightStroke: "rgba(220,220,220,1)",
			data: data_ahorro_ofertas
			}
		]
	};		
	var barOptions = {
		scaleBeginAtZero: true,
		scaleShowGridLines: true,
		scaleGridLineColor: "rgba(0,0,0,.05)",
		scaleGridLineWidth: 1,
		barShowStroke: true,
		barStrokeWidth: 2,
		barValueSpacing: 10,
		barDatasetSpacing: 1,
		responsive: true
	}
	var ctx4 = document.getElementById("barChart4").getContext("2d");	
	var myNewChart4 = new Chart(ctx4).Bar(barData, barOptions);	
	$('.tickets_listado').fadeIn();
	$('.tickets_navegacion').show();	
	$('.ticket_detalle').fadeOut();
	
}
function cargar_ahorro(registrar_navegacion = true)
{
	if (registrar_navegacion)
	{
		if (origen_array[origen_array.length-1]=='pagina-ahorro-historico'&&origen_subseccion[origen_subseccion.length-1]=='cargar_ahorro(false);')
		{
			//No repetir item
		}
		else
		{
			origen_subseccion.push('cargar_ahorro(false);');		
			origen_array.push('pagina-ahorro-historico');	
			origen_altura.push(0);
		}
	}
	$('.tab-panel').hide();
	$('#tab-1-historico').show();
	$('.botonera-ahorro').removeClass('activo');
	$('.boton-ahorro').addClass('activo');		
	var barData = {				
		labels: labels_ahorro,
		datasets: [
			{
			label: "Cheque ahorro",
			fillColor: "rgba(255,213,17,1)",
			strokeColor: "rgba(220,220,220,0.8)",
			highlightFill: "rgba(255,213,17,0.75)",
			highlightStroke: "rgba(220,220,220,1)",
			data: data_ahorro
			}
		]
	};		
	var barOptions = {
		scaleBeginAtZero: true,
		scaleShowGridLines: true,
		scaleGridLineColor: "rgba(0,0,0,.05)",
		scaleGridLineWidth: 1,
		barShowStroke: true,
		barStrokeWidth: 2,
		barValueSpacing: 10,
		barDatasetSpacing: 1,
		responsive: true		
	}	
	var ctx = document.getElementById("barChart").getContext("2d");	
	var myNewChart = new Chart(ctx).Bar(barData, barOptions);	
}

function asignar_tarjeta()
{
	var uuid_telefono ='';
	var model = '';
	if (typeof(device)!= 'undefined') 
	{
		uuid_telefono = device.uuid;
		model = device.platform+' '+device.model;
	}
	var errores = '';
	var token = $('#token_fcm').val();	
	if (sistema_operativo!='android'&&modo_sandbox=='1'&&$('#dni').val()=='')
	{
		if ($('#checkBoxLegalRegistro').attr('checked')) errores+= temp_lang['error_aviso_legal']+'<br/>';	
		if (errores=='')
		{
			escaneado = true;
			$('.nombre_tarjeta').html($('#nombre_registro').val());
			$('.numero_tarjeta').html(relacion_tarjetas_hogares.tarjeta_digital);
			$('.numero_tarjeta_scan').html(relacion_tarjetas_hogares.tarjeta_digital);						
			$('.nombre_en_home').html($('#nombre_registro').val());
			localStorage.setItem('solo_nombre',$('#nombre_registro').val());						
			localStorage.setItem('cod_cliente',relacion_tarjetas_hogares.tarjeta_digital);
			localStorage.setItem('cod_cliente_digital',relacion_tarjetas_hogares.tarjeta_digital);
			cod_cliente_digital = relacion_tarjetas_hogares.tarjeta_digital;
			localStorage.setItem('dni_cliente',$('#dni_registro').val());					 
			localStorage.setItem('numero_tarjeta',relacion_tarjetas_hogares.tarjeta_digital);
			var ahora = new Date();
			localStorage.setItem('time_vinculacion',ahora.getTime());
			id_home = relacion_tarjetas_hogares.tarjeta_digital;
			localStorage.setItem('id_home',relacion_tarjetas_hogares.tarjeta_digital);
			obtener_chequeahorro();
			obtener_multicupon();
			obtener_vales(0);
			obtener_mensajes(0,0);
			cargar_top_habituales();
			cargar_datos_locales();			
			swal({
				title: '',
					text: decodeURIComponent('Bienvenido'),
					html: true,
					type: "success"
			});									
			cod_cliente = relacion_tarjetas_hogares.tarjeta_digital;	
			location.reload();						
			//cambiar_pagina('pagina-home','pagina-home');
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
	else
	{
		if (modo_sandbox=='1') {}
		else
		{
			if ($('#nombre_registro').val()=='') errores+= temp_lang['error_nombre']+'<br/>';
			if ($('#apellido1_registro').val()=='') errores+= temp_lang['error_apellidos']+'<br/>';	
			if ($('#dni').val()=='') errores+= temp_lang['error_dni']+'<br/>';	
			if ($('#basic').val()=='') errores+= temp_lang['error_nacimiento']+'<br/>';
			if ($('#tipo_direccion').val()==''||$('#tipo_direccion').val()=='Seleccionar') errores+= temp_lang['error_tipovia']+'<br/>';
			if ($('#direccion').val()=='') errores+= temp_lang['error_direccion']+'<br/>';
			if ($('#numero_registro').val()=='') errores+= temp_lang['error_numero_registro']+'<br/>';
			if ($('#CP').val()=='') errores+= temp_lang['error_CP']+'<br/>';
			if ($('#provincia_registro').val()=='') errores+= temp_lang['error_provincia']+'<br/>';
			if ($('#ciudad_registro').val()=='') errores+= temp_lang['error_ciudad']+'<br/>';
			//if ($('#sexo_registro').val()==''||$('#sexo_registro').val()=='Seleccionar') errores+= temp_lang['error_sexo']+'<br/>';
			if ($('#movil_registro').val()=='') errores+= temp_lang['error_movil_registro']+'<br/>';
			if ($('#movil_registro').val()!=''&&(parseInt($('#movil_registro').val())<=99999999||parseInt($('#movil_registro').val())>999999999)) errores+= temp_lang['formato_telefono_obligatorio']+"<br/>";
			//if ($('#email_registro').val()=='') errores+= temp_lang['error_mail_registro']+'<br/>';
			//if ($('#nacionalidad_registro').val()=='') errores+= temp_lang['error_nacionalidad_registro']+'<br/>';
		}
		if ($('#checkBoxLegalRegistro').attr('checked')) errores+= temp_lang['error_aviso_legal']+'<br/>';	
		if (errores=='')
		{
			var url = 'https://www.appfornes.es/servicios-web/asignar_tarjeta_nueva.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&uuid_telefono='+uuid_telefono+'&nombre='+$('#nombre_registro').val()+'&apellido1='+$('#apellido1_registro').val()+'&apellido2='+$('#apellido2_registro').val();
			url+='&dni_registro='+$('#dni').val()+'&fecha_nacimiento='+$('#basic').val()+'&tipo_direccion='+$('#tipo_direccion').val()+'&direccion='+$('#direccion').val()+'&numero_registro='+$('#numero_registro').val()+'&puerta_registro='+$('#puerta_registro').val()+'&bloque_registro='+$('#bloque_registro').val()+'&escalera_registro='+$('#escalera_registro').val();
			url+='&CP='+$('#CP').val()+'&planta_registro='+$('#planta_registro').val()+'&provincia_registro='+$('#provincia_registro').val()+'&ciudad_registro='+$('#ciudad_registro').val()+'&sexo_registro='+$('#sexo_registro').val()+'&movil_registro='+$('#movil_registro').val()+'&email_registro='+$('#email_registro').val()+'&nacionalidad_registro='+$('#nacionalidad_registro').val();
			url+='&modelo='+model+'&sistema_operativo='+sistema_operativo+'&token_fcm='+token;
			url+='&idioma='+idioma;
			$.ajax({
				type:'GET',
				timeout: 10000,
				dataType: 'json',
				url: url,
				success:function(res, textStatus, XMLHttpRequest)
				{
					var resultado_ajax ='';
					$.each(res, function(key, value) 
					{
						if (res.validacion=='ok')
						{
							escaneado = true;
							$('.nombre_tarjeta').html($('#nombre_registro').val());
							$('.numero_tarjeta').html(res.cod_cliente);
							$('.numero_tarjeta_scan').html(res.cod_cliente);						
							$('.nombre_en_home').html($('#nombre_registro').val());
							localStorage.setItem('solo_nombre',$('#nombre_registro').val());						
							localStorage.setItem('cod_cliente',res.cod_cliente);
							localStorage.setItem('cod_cliente_digital',res.cod_cliente_digital);
							cod_cliente_digital = res.cod_cliente_digital;
							localStorage.setItem('dni_cliente',$('#dni_registro').val());					 
							localStorage.setItem('numero_tarjeta',res.cod_cliente);
							var ahora = new Date();
							localStorage.setItem('time_vinculacion',ahora.getTime());
							id_home = res.id_home;
							localStorage.setItem('id_home',id_home);
							obtener_chequeahorro();
							obtener_multicupon();
							obtener_vales(0);
							obtener_mensajes(0,0);
							cargar_top_habituales();
							cargar_datos_locales();						
							
							swal({
								title: '',
									text: decodeURIComponent(unescape(res.mensaje)),
									html: true,
									type: "success"
							});									
							cod_cliente = res.cod_tarjeta;	
							location.reload();						
							//cambiar_pagina('pagina-home','pagina-home');
						}
						else
						{
							swal({
								title: temp_lang["Error"],
								text: decodeURIComponent(unescape(res.mensaje)),
								html: true,
								type: "error"
							});
						}
					});			 
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					swal({
						title: temp_lang["Error"],
						text: temp_lang["ErrorConexion"],
						html: true,
						type: "error"
					});
				}
			});
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
}
function pintar_codigo_qr(data)
{
	var qrcode = new QRCode("barcode_codigo_tarjeta_qr",{						
		width:256,
		height:256,
		colorDark: '#000000',
		colorLight: '#FFFFFF',
		useSVG: true,
		correctLevel : QRCode.CorrectLevel.H
	});
	qrcode.clear();
	qrcode.makeCode(data);
}
function anadir_codigo_activo(codigo)
{
	if (cupones_activados.indexOf(codigo)>=0)
	{
		console.log('Código ya estaba encolado, se ignora',codigo);
	}
	else
	{
		num_cupones_activados++;
		cupones_activados.push(codigo);
	}
}
function generar_data_code(temp)
{
	var cupones_activados_string = '';
	var temp_tarjeta = temp;
	if (temp_tarjeta.length<10) 
	{
		temp_tarjeta = '282'+temp_tarjeta;
		temp_tarjeta = temp_tarjeta+eanCheckDigit(temp_tarjeta);
	}
	if (num_cupones_activados==0) var num_cupones_string = '01';
	else var num_cupones_string = num_cupones_activados.toString().padStart(2,'0');
	$.each(cupones_activados, function(key, value) 
	{
		cupones_activados_string+=value;		
	});						
	console.log('Data code generado','001|'+temp_tarjeta+'|'+num_cupones_string+'|'+cupones_activados_string);
	return '001'+temp_tarjeta+num_cupones_string+cupones_activados_string;
}
function obtener_multicupon()
{
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_multicupon.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('cojone3', res);
			if (res.validacion == 'ok')
			{				
				if (cupones_activados.indexOf(cod_multicupon)>=0)
				{
					console.log('PRUEBA1',cupones_activados);
					//Ya se había metido, por lo que no hacemos nada
				}
				else
				{
					var primero = true;
					//Reseteo valores del mega que pudiese estar guardado de antes
					$('.numero_multicupon_scan').html('');					
					$('.valided_multicupon').html('');
					cod_multicupon = '';										
					pintar_codigo_qr(generar_data_code());						
					$('#bloque_activar_multicupon').html('');
					localStorage.removeItem('cod_multicupon');
					localStorage.setItem('cod_multicupon',cod_multicupon);				
					$('.pagina-multi-cupon .grup_cupones').html('');
					$('.opcion_mi_lista_cupones').html('');						
					localStorage.setItem('multicupon_html','');						
					localStorage.setItem('multicupon_html_lista','');												
					$('.pagina-multi-cupon .grup_cupones_siguiente').html('');			 
					localStorage.setItem('multicupon_html_siguiente','');
					$('.pagina-multi-cupon .grup_cupones .nav-multicupones').html('');
					$('.pagina-multi-cupon .grup_cupones_siguiente .nav-multicupones').html('');
					//Fin de reseteo
										
					if (res.multicupones.length>0)
					{
						$('.pagina-tarjeta .multi .cupon_no_disponible_text').html('');
						$.each(res.multicupones, function(key, value) 
						{
							var resultado_ajax ='';
							var	resultado_ajax_lista = '';
							if (primero)
							{
								//$('.valided_multicupon').html('<i class="fa fa-calendar"></i> Canjeable hasta '+value.fecha_fin_redencion);
								$('.valided_multicupon').html('');															
								cod_multicupon = value.cod_multicupon;
								$('.numero_multicupon_scan').html(cod_multicupon);
								//if (value.visto==1) anadir_codigo_activo(cod_multicupon);																	
								anadir_codigo_activo(cod_multicupon); //Como si estuviera visto																 //
								pintar_codigo_qr(generar_data_code());									
								var html_tmp = '<div class="col-xs-3 cup_state contenedor_switchery_'+cod_multicupon+'"><input value="'+cod_multicupon+'" type="checkbox" class="js-switch_'+cod_multicupon+' btn_activar"';
								//if (value.visto==1) html_tmp+=' checked'; //PARA AUTO ACTIVARLO SÓLO SI SE HA VISTO PREVIAMENTE
								html_tmp+=' checked'; // AHORA SE AUTOACTIVA SIEMPRE SE HAYA VISTO O NO
								html_tmp+='/></div>';
								$('#bloque_activar_multicupon').html(html_tmp);						
								var tmp_elem = document.querySelector('.js-switch_'+cod_multicupon);
								elems[cod_multicupon] = tmp_elem;
								switchery[cod_multicupon] = new Switchery(elems[cod_multicupon], { color: '#FFCA00', secondaryColor    : '#cccccc', });
								elems[cod_multicupon].onchange = function()
								{							
									if (this.checked) 
									{									
										var resultado = activar_cupon(this.value,cod_multicupon);																
									}
									else 
									{								
										desactivar_cupon(this.value);											
									}																																			
								};

								localStorage.removeItem('cod_multicupon');
								localStorage.setItem('cod_multicupon',cod_multicupon);
							
							}
							else
							{
								cod_multicupon_siguiente = value.cod_multicupon;
								localStorage.removeItem('cod_multicupon_siguiente');
								localStorage.setItem('cod_multicupon_siguiente',cod_multicupon_siguiente);
							}
							//resultado_ajax += '<div class="barra_cabecera container"><div class="row"><nav class="navbar" role="navigation" style="margin-bottom: 0"><div class="col-xs-2 notificaciones"><a class="" href="#" onclick="volver();return false;"><img class="responsive navback" src="img/svg/volver.svg"></a></div><div class="navbar_logo secun col-xs-8"><a href="#" onclick="cambiar_pagina(\'\',\'pagina-portada\');return false;"><img src="img/svg/cupones/cab_cupones.svg"></a></div><div class="col-xs-2"><a href="#" class="show_menu" onclick="menushow();return false;"><img class="responsive navmenu" src="img/svg/portada/menu.svg"></a></div></nav></div></div>';
							resultado_ajax += '<div class="row tabler multi"><div class="col-xs-4 cup_qty"></div><div class="col-xs-5 cup_name multi"><h3>'+temp_lang['Multicupon']+'</h3><p>';
							resultado_ajax += temp_lang['ValidoDel']+'<br/>'+value.fecha_ini_redencion;
							resultado_ajax += ' '+temp_lang['al']+' '+value.fecha_fin_redencion;
							resultado_ajax += '</p></div><div class="col-xs-3 nav-multicupones"></div></div>';
							var reseteo_caducidad = new Date(value.date_fin_redencion);					
							var time_caducidad = reseteo_caducidad.getTime();					
							$.each(value.promos, function(key2, value2) 
							{						
								resultado_ajax +='<div class="row tabler';
								//if (mi_lista['cupon_'+value2.id]!=undefined&&mi_lista['cupon_'+value2.id]!='') resultado_ajax+= ' anadido_en_lista'; 
								resultado_ajax +='" id="cupones_seccion_'+value2.id+'"><div class="col-xs-3 cup_qty"><h1>';
								resultado_ajax_lista += '<div class="row tabler';
								if (mi_lista['cupon_'+value2.id]!=undefined&&mi_lista['cupon_'+value2.id]!='') resultado_ajax_lista += ' anadido_en_lista';
								resultado_ajax_lista += '" id="cupones_bloque_'+value2.id+'">';
								resultado_ajax += value2.tipo.replace(/EUR/g,'€');
								//resultado_ajax +='</h1></div><div class="col-xs-7 cup_name"><h3>';//Espacio para añadir a la lista cuando se haga
								resultado_ajax +='</h1></div><div class="col-xs-10 cup_name"><h3>';//Espacio para añadir a la lista cuando se haga
								resultado_ajax_lista += '<div class="col-xs-10 cup_name"><h3><span class="tipo_cupon">'+value2.tipo.replace(/EUR/g,'€')+'</span>';
								//resultado_ajax +='</h1></div><div class="col-xs-8 cup_name"><h3>';//Quitar cuando se añada a la lista
								resultado_ajax += value2.titulo.replace(/EUR/g,'€');
								resultado_ajax_lista += value2.titulo.replace(/EUR/g,'€');
								resultado_ajax +='</h3><p>';
								resultado_ajax_lista +='</h3><p>';
								resultado_ajax += value2.texto.replace(/EUR/g,'€');
								resultado_ajax_lista +=value2.texto.replace(/EUR/g,'€');
								resultado_ajax +='</p></div>';
		/* 						resultado_ajax += '<div class="col-xs-2 cup_state">';
								if (primero) 
								{							
									if (mi_lista['cupon_'+value2.id]!=undefined&&mi_lista['cupon_'+value2.id]!='') resultado_ajax += '<a href="#" onclick="eliminar_item_lista(\'cupones_lista_prod_'+value2.id+'\',\''+value2.id+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a>';
									else resultado_ajax += '<a href="#" onclick="anadir_lista(\'cupones_bloque_'+value2.id+'\',\''+value2.id+'\',\''+time_caducidad+'\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a>'; //Sólo permito añadir a la lista el actual
								}
								resultado_ajax+='</div>'; */
								resultado_ajax+='</div>'; //Espacio para añadir a la lista cuando se haga						
								if (mi_lista['cupon_'+value2.id]!=undefined&&mi_lista['cupon_'+value2.id]!='') resultado_ajax_lista +='</p></div><div class="col-xs-2 cup_state"><a href="#" onclick="eliminar_item_lista(\'cupones_lista_prod_'+value2.id+'\',\''+value2.id+'\',\'1\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-trash"></i></a></div></div>';
								else resultado_ajax_lista +='</p></div><div class="col-xs-2 cup_state"><a href="#" onclick="anadir_lista(\'cupones_bloque_'+value2.id+'\',\''+value2.id+'\',\''+time_caducidad+'\',\'\');return false;" class="marcar boton_anadir_mi_lista pull-right"><img src="img/svg/portada/lista2.svg" class="icono_lista_compra"><i class="fa fa-plus"></i></a></div></div>';
								//resultado_ajax +='</p></div></div>'; //Quitar cuando se añada a la lista
							});
							//resultado_ajax +='</div>';
							if (primero)
							{
								$('.pagina-multi-cupon .grup_cupones').html(resultado_ajax);
								$('.opcion_mi_lista_cupones').html(resultado_ajax_lista);						
								localStorage.setItem('multicupon_html',resultado_ajax);						
								localStorage.setItem('multicupon_html_lista',resultado_ajax_lista);						
								primero = false;						
							}
							else
							{
								$('.pagina-multi-cupon .grup_cupones_siguiente').html(resultado_ajax);			 
								localStorage.setItem('multicupon_html_siguiente',resultado_ajax);
								$('.pagina-multi-cupon .grup_cupones .nav-multicupones').html('<a href="#" class="btn btn-primary" onclick="$(\'.pagina-multi-cupon .grup_cupones\').hide();$(\'.pagina-multi-cupon .grup_cupones_siguiente\').fadeIn();return false;">'+temp_lang['Siguiente']+'</a>');
								$('.pagina-multi-cupon .grup_cupones_siguiente .nav-multicupones').html('<a href="#" class="btn btn-primary" onclick="$(\'.pagina-multi-cupon .grup_cupones_siguiente\').hide();$(\'.pagina-multi-cupon .grup_cupones\').fadeIn();return false;">'+temp_lang['Actual']+'</a>');
							}
						});
					}
					else
					{					
						$('.pagina-tarjeta .multi .cupon_no_disponible_text').html(temp_lang['NoDisponible']);
						$('.pagina-multi-cupon .grup_cupones').html('<p class="alert alert-danger">'+temp_lang['NoDisponible']+'</p>');
					}
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			$('.valided_multicupon').html('');
			$('.pagina-multi-cupon .grup_cupones').html(localStorage.getItem('multicupon_html'));			
			//num_cupones_activados++; No se usa el multicupon
			cod_multicupon = localStorage.getItem('cod_multicupon');
			$('.numero_multicupon_scan').html(cod_multicupon);			
			var html_tmp = '<div class="col-xs-3 cup_state contenedor_switchery_'+cod_multicupon+'"><input value="'+cod_multicupon+'" type="checkbox" class="js-switch_'+cod_multicupon+' btn_activar"';
			html_tmp+=' checked'; // AHORA SE AUTOACTIVA SIEMPRE SE HAYA VISTO O NO			
			anadir_codigo_activo(cod_multicupon);				
			$('.cupones_activados_html').remove();
			pintar_codigo_qr(generar_data_code());
			html_tmp+='/></div>';
			$('#bloque_activar_multicupon').html(html_tmp);						
			var tmp_elem = document.querySelector('.js-switch_'+cod_multicupon);
			elems[cod_multicupon] = tmp_elem;
			switchery[cod_multicupon] = new Switchery(elems[cod_multicupon], { color: '#FFCA00', secondaryColor    : '#cccccc', });
			elems[cod_multicupon].onchange = function()
			{							
				if (this.checked) 
				{									
					var resultado = activar_cupon(this.value,cod_multicupon);																
				}
				else 
				{								
					desactivar_cupon(this.value);											
				}																																			
			};				
			pintar_codigo_qr(generar_data_code());			
			$('.opcion_mi_lista_cupones').html(localStorage.getItem('multicupon_html_lista'));
		}
	});

	//Reseteamos cupones anadidos a la lista caducados
	var fecha_actual = new Date();
	var time_actual = fecha_actual.getTime();							
	var alguno_reseteado = false;
	$('#listas_detalles_bloque .lista_prod').each(function()
	{
		if (parseInt($(this).find('.time_caducidad').val())<time_actual)
		{
			//Cupón ha caducado, lo saco de la lista
			$(this).remove();
			alguno_reseteado = true;
		}
	});
	if (alguno_reseteado)
	{
		localStorage.setItem("listas_detalles_bloque",$('#listas_detalles_bloque').html());
	}
}
function refrescar_cupones_1_uso()
{
	//Para asegurarnos de que no vuelva a pasar por casa se mira si los cupones seleccionados no están entre los disponibles porque se hayan usado
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_vales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{						
			if (res.validacion == 'ok')
			{		
				var filteredItems = cupones_descargados;
				$.each(res.vales, function(key, value) 
				{
					filteredItems = filteredItems.filter(item => item !== value.cod_vale);
				});
				if (filteredItems.length>0)
				{					
					obtener_vales(1); //Hay algun cupon que ya no está disponible. Refresco.
					swal({
						title: temp_lang["SeleccionReseteada"],
						text: temp_lang["SeleccionReseteadaTexto"],
						html: true,
						type: "warning"
					});
				}				
			}			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});
}
function activar_cupon(codigo_cupon,cod_vale)
{
	//Bloqueamos el cupón
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/bloquear_cupon.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente+'&codigo_cupon='+codigo_cupon+'&id_home='+id_home+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('Cupones activados antes marcado:('+num_cupones_activados+')',cupones_activados);				
			if (res.validacion=='ok')
			{
				anadir_codigo_activo(codigo_cupon);	
				console.log('permitido');
				$('.cupones_activados_html').remove();
				pintar_codigo_qr(generar_data_code());				
				console.log('Cupones activados despues marcado:('+num_cupones_activados+')',cupones_activados);
				return true;
			}
			else
			{
				console.log('Cupones activados despues marcado error A:('+num_cupones_activados+')',cupones_activados);
				var nuevo_cupones_activados = [];
				$.each(cupones_activados, function(key, value) 
				{
					if (codigo_cupon!=value) 
					{												
						nuevo_cupones_activados.push(value);
					}
				});
				cupones_activados = nuevo_cupones_activados;

				console.log('no permitido');
				//$('.cupon_'+cod_vale+' .texto_activar').html('Activar');	
				if ($('.js-switch_1').val()==codigo_cupon)
				{
					//Se activó el cheque ahorro destacado
					$('.js-switch_1').trigger('click');				
					swal({
						title: temp_lang["Error"],
						text: decodeURIComponent(unescape(res.mensaje)),
						html: true,
						type: "error"
					});
				}
				else
				{
					$('.js-switch_'+codigo_cupon).trigger('click');				
					swal({
						title: temp_lang["Error"],
						text: decodeURIComponent(unescape(res.mensaje)),
						html: true,
						type: "error"
					});
				}
				console.log('Cupones activados despues marcado error:('+num_cupones_activados+')',cupones_activados);
				return false;
			}			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			anadir_codigo_activo(codigo_cupon);	
			console.log('permitido');
			$('.cupones_activados_html').remove();
			pintar_codigo_qr(generar_data_code());				
			console.log('Cupones activados despues marcado:('+num_cupones_activados+')',cupones_activados);
		}
	});	
}
function desactivar_cupon(codigo_cupon)
{
	console.log('Cupones activados antes desactivacion:('+num_cupones_activados+')('+codigo_cupon+')',cupones_activados);
	if (num_cupones_activados>0)
	{		
		var cupones_activados_string = '';
		var cupones_activados_html = '';
		var nuevo_cupones_activados = [];
		$.each(cupones_activados, function(key, value) 
		{
			if (codigo_cupon!=value) 
			{
				cupones_activados_string+=value;
				cupones_activados_html+='<span>'+value+'</span><br/>';
				nuevo_cupones_activados.push(value);
			}
			else
			{
				num_cupones_activados--;
			}
		});
		cupones_activados = nuevo_cupones_activados;

		
		$('.cupones_activados_html').remove();		
		pintar_codigo_qr(generar_data_code());
		//Descloqueamos el cupón
		$.ajax({
			type:'GET',
			timeout: 20000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/desbloquear_cupon.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente+'&codigo_cupon='+codigo_cupon+'&id_home='+id_home+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});	

	}
	console.log('Cupones activados despues desactivacion:('+num_cupones_activados+')('+codigo_cupon+')',cupones_activados);
}
function marcar_mensaje_leido(id_mensaje,cod_barras)
{
	$('.notif').html(parseInt($('.notif').html())-1);
	if (parseInt($('.notif').html())<=0) $('.notif').hide();
	$('#mensaje_'+id_mensaje+'_'+cod_barras+' .nuevo i').fadeOut();	
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/marcar_mensaje_leido.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&id_mensaje='+id_mensaje+'&cod_barras='+cod_barras+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			if (res.es_multicupon==1)
			{				
				if ($('.js-switch_'+cod_barras).is(':checked'))
				{
				}
				else				
				{								
					$('.js-switch_'+cod_barras).trigger('click');						
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});		
}
function marcar_mensaje_borrado(id_mensaje,cod_barras)
{
	if ($('#mensaje_'+id_mensaje+' .nuevo').html()!='')
	{
		$('.notif').html(parseInt($('.notif').html())-1);
		if (parseInt($('.notif').html())<=0) $('.notif').hide();
	}
	$('#mensaje_'+id_mensaje+'_'+cod_barras).fadeOut();
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/marcar_mensaje_borrado.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&id_mensaje='+id_mensaje+'&cod_barras='+cod_barras+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});		
}
function obtener_mensajes(todas,vistas)
{
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_mensajes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&vistas='+vistas+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var mensajes_no_leidos = 0;
			html_result = '';
			if (res.validacion == 'ok')
			{				
				$.each(res.mensajes, function(key, value) 
				{
					if (value.leido==0) 
					{
						mensajes_no_leidos++;
					}
					if (1==1||value.leido==0||todas==1)
					{
						html_result += '<div class="row lista_prod" id="mensaje_'+value.id+'_'+value.cod_barras+'"><div class="col-xs-1 nuevo">';
						if (value.leido==0) html_result+= '<i class="fa fa-circle"></i>';
						html_result += '</div>';
						html_result += '<div class="col-xs-8 prod_data"><p class="prod_env"';
						var tmp = value.id.split('-');
						if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-folletos\');return false;"';
						else if (tmp[0]=='cupon') html_result+=' onclick="cupon_resaltar=\''+value.cod_barras+'\';marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-tarjeta\');return false;"';
						else if (tmp[0]=='multicupon') 
						{
							html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-multi-cupon\');';
							if (value.siguiente==1) html_result+='$(\'.pagina-multi-cupon .grup_cupones\').hide();$(\'.pagina-multi-cupon .grup_cupones_siguiente\').fadeIn();';
							else html_result+='$(\'.pagina-multi-cupon .grup_cupones_siguiente\').hide();$(\'.pagina-multi-cupon .grup_cupones\').fadeIn();';
							html_result+='return false;"';
						}
						else if (tmp[0]=='chequeahorro') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-tarjeta\');return false;"';
						else if (tmp[0]=='folleto') html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');cambiar_pagina(\'pagina-notificaciones\',\'pagina-folletos\');return false;"';
						else html_result+=' onclick="marcar_mensaje_leido(\''+value.id+'\',\''+value.cod_barras+'\');"';
						html_result +='>'+value.texto+'</p></div><div class="col-xs-3 prod_activo">';
						//html_result +='<input type="checkbox" class="styleMe" id="input_'+value.id+'"/>';
						if (value.tipo!=''&&value.tipo!=undefined) html_result+=value.tipo;						
						html_result+='</div>';
						html_result+='<a href="#" class="btn btn-primary borrar_mensaje" onclick="marcar_mensaje_borrado(\''+value.id+'\',\''+value.cod_barras+'\');$(\'#mensaje_'+value.id+'\').fadeOut();return false;"><i class="fa fa-trash"></i></a>';
						html_result+='</div>';					
					}
				});								
				$('.notif').html(mensajes_no_leidos);
				if (mensajes_no_leidos>0) 
				{
					$('.notif').show();
					//window.FirebasePlugin.setBadgeNumber(mensajes_no_leidos);
				}
				else $('.notif').hide();
				//else window.FirebasePlugin.setBadgeNumber(0);
				if (html_result!='')
				{					
					$('#lista1').html(html_result);
					$('.styleMe').iCheck({
						checkboxClass: 'icheckbox_square-green',
						radioClass: 'iradio_minimal',
						labelHover: false,
						cursor: true
					});
					$('.styleMe').on('ifChecked', function(event){
						var pulsado = $(this).attr("id").split('_');
						marcar_mensaje_leido(pulsado[1],pulsado[2]);
						$('#mensaje_'+pulsado[1]).fadeOut();
					});
				}
				else
				{
					$('#lista1').html('<div class="row lista_prod"><div class="col-xs-8 col-xs-offset-1 prod_data"><p class="prod_env">'+temp_lang['sin_notificaciones']+'</p></div><div class="col-xs-2 prod_activo">&nbsp;</div></div>');
				}		
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});
}
function obtener_vales(recargado)
{
	console.log('cojone1', recargado);
	$.ajax({
		type:'GET',
		timeout: 20000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_vales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var encontrado_cheque_ahorro = false;			
			var algun_vale = false;
			if (res.validacion == 'ok')
			{
				console.log('000000000000000000000', res);
				cupones_descargados = [];				
				var i=3;
				if (recargado) 
				{
					$('.pagina-tarjeta .grup_cupones .otros_cupones').remove();
					$('.pagina-multi-cupon .otros-cupones-seccion-cupones .row').remove();
				}
				localStorage.setItem('array_vales',JSON.stringify(res.vales));
				$.each(res.vales, function(key, value) 
				{
					cupones_descargados.push(value.cod_vale);
					if (value.es_cheque_ahorro_principal==1 && !encontrado_cheque_ahorro)
					{
						$('.pagina-tarjeta .cheque_ahorro').show();
						$('.cheque_ahorro .bloque_centro').show();
						encontrado_cheque_ahorro = true;
						$('.js-switch_1').val(value.cod_vale);
						$('.tot_cheque').html(value.importe_vale+'<span>€</span>');
						$('.codigo_cheque_ahorro').html(value.cod_vale);
						$('.tot_desde').html('<i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido);
						if (value.proximo_vencimiento==1)
						{
							cheque_ahorro_cerca_caducar = true;
							$('.cheque_ahorro .bloque_centro').addClass('va_a_caducar_bloque');
						}
						else
						{
							cheque_ahorro_cerca_caducar = false;
							$('.cheque_ahorro .bloque_centro').removeClass('va_a_caducar_bloque');
						}
					}
					else
					{
						console.log('2');
						algun_vale = true;
						var html_result = '';
						if (value.es_cheque_ahorro==1)
						{
							//Tarjeta propia para el ChequeAhorro dentro del listado de vales (portado de la app; clase renombrada
							//a "cheque_ahorro_vale" para no chocar con el widget #mcheque_ahorro1 de esta misma página).
							html_result+='<div class="row cheque_ahorro_vale tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1><img src="img/svg/cupon_euro.svg" style="width:80%;">';
							if (value.diferido==1) html_result+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result+='</h1></div>';
							html_result+='<div class="col-xs-12 cup_name"><h3 tkey="MiChequeAhorro">'+temp_lang['MiChequeAhorro']+'</h3>';
							var texto_explicativo = value.texto.split('<br>')[0].replace('&euro;','<span class="euro">&euro;</span>');
							html_result+='<p>'+texto_explicativo+'</p>';
							html_result+='<div class="cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
							html_result+='<p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
							html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p>';
							html_result+='<p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';
							html_result+='</div>';
						}
						else
						{
							//Imagen propia del cupón si la API la da; si no, se mantiene el texto de siempre.
							html_result+='<div class="row tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty">';
							if (value.Img!=undefined&&value.Img!='')
							{
								html_result+='<img class="imagen_multicupon" src="'+value.Img+'">';
								if (value.tipo.indexOf('<img')<0) html_result+='<h1>'+value.tipo;
							}
							else html_result+='<h1>'+value.tipo;
							if (value.diferido==1) html_result+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result+='</h1></div>';
							html_result+='<div class="col-xs-6"><p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
							html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
							html_result+='<div class="col-xs-3 cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
							html_result+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
							html_result+='<p>'+value.texto+'</p><p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';
							html_result+='</div>';
						}
						$('.pagina-tarjeta .grup_cupones').append(html_result);
						if (value.es_cheque_ahorro!=1)
						{
							var html_result2 = '';
							html_result2+='<div class="row tabler_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty">';
							if (value.Img!=undefined&&value.Img!='')
							{
								html_result2+='<img class="imagen_multicupon" src="'+value.Img+'">';
								if (value.tipo.indexOf('<img')<0) html_result2+='<h1>'+value.tipo;
							}
							else html_result2+='<h1>'+value.tipo;
							if (value.diferido==1) html_result2+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result2+='</h1></div>';
							html_result2+='<div class="col-xs-9"><p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result2+=' va_a_caducar';
							html_result2+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
							//html_result2+='<div class="col-xs-3 cup_state contenedor_switchery2_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_seccion_cupones_'+value.cod_vale+' btn_activar"/></div>';
							html_result2+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
							html_result2+='<p>'+value.texto+'</p>';
							//html_result2+='<p class="otros_cupones_codigo">'+value.cod_vale+'</p>';
							html_result2+='</div>';
							html_result2+='</div>';
							$('.pagina-multi-cupon .otros-cupones-seccion-cupones').append(html_result2);
						}
						$('.pagina-multi-cupon .otros-cupones-seccion-cupones').show();
						if (recargado)
						{
							//Reseteamos la selección en el QR previa
							desactivar_cupon(value.cod_vale);
							//Resetamos los switchery anteriormente cargados
							//switchery2[value.cod_vale].destroy();
							//$('.contenedor_switchery2_'+value.cod_vale+' .switchery').remove();
							switchery[value.cod_vale].destroy();
							$('.contenedor_switchery_'+value.cod_vale+' .switchery').remove();
						}
						var tmp_elem = document.querySelector('.js-switch_'+value.cod_vale);					
						elems[value.cod_vale] = tmp_elem;
						switchery[value.cod_vale] = new Switchery(elems[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
						//var tmp_elem2 = document.querySelector('.js-switch_seccion_cupones_'+value.cod_vale);
						//elems2[value.cod_vale] = tmp_elem2;
						//switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
						elems[value.cod_vale].onchange = function()
						{
							//switchery2[value.cod_vale].destroy();
							//$('.contenedor_switchery2_'+value.cod_vale+' .switchery').remove();
							if (this.checked) 
							{									
								var resultado = activar_cupon(this.value,value.cod_vale);								
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activado');								
								//$('.js-switch_seccion_cupones_'+value.cod_vale).prop('checked', true);
							}
							else 
							{
								desactivar_cupon(this.value);			
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activar');								
								//$('.js-switch_seccion_cupones_'+value.cod_vale).prop('checked', false);
							}																												
							//switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
							
						};
						/*
						elems2[value.cod_vale].onchange = function()
						{							
							switchery[value.cod_vale].destroy();
							$('.contenedor_switchery_'+value.cod_vale+' .switchery').remove();
							if (this.checked) 
							{								
								var resultado = activar_cupon(this.value,value.cod_vale);								
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activado');
								$('.js-switch_'+value.cod_vale).prop('checked', true);
							}
							else 
							{
								desactivar_cupon(this.value);			
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activar');								
								$('.js-switch_'+value.cod_vale).prop('checked', false);
							}
							switchery[value.cod_vale] = new Switchery(elems[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
						};
						*/
					}
					i++;
				});
				if (!algun_vale)
				{
					var html_result = '';					
					$('.pagina-tarjeta .grup_cupones').append(html_result);
				}				
			}
			if (!encontrado_cheque_ahorro) $('.cheque_ahorro .bloque_centro').hide();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			var vales = JSON.parse(localStorage.getItem('array_vales'));
			var i=3;
			$.each(vales, function(key, value) 
				{
					cupones_descargados.push(value.cod_vale);
					if (value.es_cheque_ahorro_principal==1 && !encontrado_cheque_ahorro)
					{
						$('.pagina-tarjeta .cheque_ahorro').show();
						$('.cheque_ahorro .bloque_centro').show();
						encontrado_cheque_ahorro = true;
						$('.js-switch_1').val(value.cod_vale);
						$('.tot_cheque').html(value.importe_vale+'<span>€</span>');
						$('.codigo_cheque_ahorro').html(value.cod_vale);
						$('.tot_desde').html('<i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido);
						if (value.proximo_vencimiento==1)
						{
							cheque_ahorro_cerca_caducar = true;
							$('.cheque_ahorro .bloque_centro').addClass('va_a_caducar_bloque');
						}
						else
						{
							cheque_ahorro_cerca_caducar = false;
							$('.cheque_ahorro .bloque_centro').removeClass('va_a_caducar_bloque');
						}
					}
					else
					{
						algun_vale = true;
						var html_result = '';
						if (value.es_cheque_ahorro==1)
						{
							html_result+='<div class="row cheque_ahorro_vale tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1><img src="img/svg/cupon_euro.svg" style="width:80%;">';
							if (value.diferido==1) html_result+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result+='</h1></div>';
							html_result+='<div class="col-xs-12 cup_name"><h3 tkey="MiChequeAhorro">'+temp_lang['MiChequeAhorro']+'</h3>';
							var texto_explicativo = value.texto.split('<br>')[0].replace('&euro;','<span class="euro">&euro;</span>');
							html_result+='<p>'+texto_explicativo+'</p>';
							html_result+='<div class="cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
							html_result+='<p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
							html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p>';
							html_result+='<p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';
							html_result+='</div>';
						}
						else
						{
							html_result+='<div class="row tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty">';
							if (value.Img!=undefined&&value.Img!='')
							{
								html_result+='<img class="imagen_multicupon" src="'+value.Img+'">';
								if (value.tipo.indexOf('<img')<0) html_result+='<h1>'+value.tipo;
							}
							else html_result+='<h1>'+value.tipo;
							if (value.diferido==1) html_result+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result+='</h1></div>';
							html_result+='<div class="col-xs-6"><p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
							html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
							html_result+='<div class="col-xs-3 cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
							html_result+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
							html_result+='<p>'+value.texto+'</p><p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';
							html_result+='</div>';
						}
						$('.pagina-tarjeta .grup_cupones').append(html_result);
						if (value.es_cheque_ahorro!=1)
						{
							var html_result2 = '';
							html_result2+='<div class="row tabler_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty">';
							if (value.Img!=undefined&&value.Img!='')
							{
								html_result2+='<img class="imagen_multicupon" src="'+value.Img+'">';
								if (value.tipo.indexOf('<img')<0) html_result2+='<h1>'+value.tipo;
							}
							else html_result2+='<h1>'+value.tipo;
							if (value.diferido==1) html_result2+='<br><span class="bloque_cupon_acumula">'+temp_lang['DescuentoACUMULA']+'</span>';
							html_result2+='</h1></div>';
							html_result2+='<div class="col-xs-9"><p class="otros_cupones_valido';
							if (value.proximo_vencimiento==1) html_result2+=' va_a_caducar';
							html_result2+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
							html_result2+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
							html_result2+='<p>'+value.texto+'</p>';
							html_result2+='</div>';
							html_result2+='</div>';
							$('.pagina-multi-cupon .otros-cupones-seccion-cupones').append(html_result2);
						}
						$('.pagina-multi-cupon .otros-cupones-seccion-cupones').show();
						if (recargado)
						{
							//Reseteamos la selección en el QR previa
							desactivar_cupon(value.cod_vale);
							//Resetamos los switchery anteriormente cargados
							//switchery2[value.cod_vale].destroy();
							//$('.contenedor_switchery2_'+value.cod_vale+' .switchery').remove();
							switchery[value.cod_vale].destroy();
							$('.contenedor_switchery_'+value.cod_vale+' .switchery').remove();
						}
						var tmp_elem = document.querySelector('.js-switch_'+value.cod_vale);					
						elems[value.cod_vale] = tmp_elem;
						switchery[value.cod_vale] = new Switchery(elems[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
						//var tmp_elem2 = document.querySelector('.js-switch_seccion_cupones_'+value.cod_vale);
						//elems2[value.cod_vale] = tmp_elem2;
						//switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
						elems[value.cod_vale].onchange = function()
						{
							//switchery2[value.cod_vale].destroy();
							//$('.contenedor_switchery2_'+value.cod_vale+' .switchery').remove();
							if (this.checked) 
							{									
								var resultado = activar_cupon(this.value,value.cod_vale);								
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activado');								
								//$('.js-switch_seccion_cupones_'+value.cod_vale).prop('checked', true);
							}
							else 
							{
								desactivar_cupon(this.value);			
								//$('.cupon_'+value.cod_vale+' .texto_activar').html('Activar');								
								//$('.js-switch_seccion_cupones_'+value.cod_vale).prop('checked', false);
							}																												
							//switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
							
						};						
					}
					i++;
				});
				if (!algun_vale)
				{
					var html_result = '';					
					$('.pagina-tarjeta .grup_cupones').append(html_result);
				}				
		}
	});
}
function pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado)
{		
	if (ahorro_acumulado>0)
	{
		if (ahorro_quedan>0)
		{			
			$('.ahorrar').html(temp_lang['faltan_delante']+' '+ahorro_quedan.toFixed(2).replace('.',',')+'<span> €</span>');
			$('.ahorrar').show();						
			$('.ahorrar').addClass('ahorrar2_animation');
			$('.ahorrar2').html(temp_lang['Llevas']+' '+ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');
			$('.ahorrar2').addClass('ahorrar2_animation2');
			$('.ahorrar2').show();							
			$('.puntos_cheque_acumulado').hide();
		}
		else
		{
			$('.puntos_cheque_acumulado').show();
			$('.puntos_cheque_acumulado').html(ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');
			localStorage.setItem('puntos_cheque_acumulado',ahorro_acumulado.toFixed(2).replace('.',',')+'<span> €</span>');					
			$('.ahorrar').hide();
			$('.ahorrar2').hide();				
		}
	}
	else
	{		
		$('.puntos_cheque_acumulado').hide();
		$('.ahorrar').show();					
	}
	if (ahorro_canjeable>0)
	{		
		$('.ahorrar').css({'padding':'7px','font-size':'18px','line-height':'18px','float':'left'});	
		$('.ahorrar2').css({'font-size':'18px','line-height':'18px','padding':'7px'});
		$('.puntos_cheque_canjeable').show();
		$('.puntos_cheque_canjeable').html(ahorro_canjeable.toFixed(2).replace('.',',')+'<span> €</span>');
		localStorage.setItem('puntos_cheque_canjeable',ahorro_canjeable.toFixed(2).replace('.',',')+'<span> €</span>');					
		if (ahorro_total_actual<1)
		{ 
			//Aun necesita para generar cheque ahorro
			var quedan = 1-ahorro_total_actual;							
			$('#acum_dentro .saldo').html(quedan.toFixed(2).replace('.',',')+'<span>€</span>');
			$('.cheque_ahorro_acumulando .precio').html(ahorro_total_actual.toFixed(2).replace('.',',')+'<span>€</span>');
			$('.cheque_ahorro_acumulando .saldo_fecha').html(fecha_acumulado);			
			$('.empieza_acumular').hide();
		}
		else
		{
			$('.cheque_ahorro_acumulando').hide();
			$('#acum_dentro').hide();
			$('.empieza_acumular').hide();
		}
	}
	else
	{
		$('.ahorro_canjeable_nuevo').hide();
		$('.ahorro_acumulado_nuevo').css('width','100%');					
	}
}
function obtener_chequeahorro()
{
	$.ajax({
		type:'GET',
		timeout: 3000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/obtener_chequeahorro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home='+id_home+'&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			var resultado_ajax ='';
			if (res.validacion=='ok')
			{
				console.log('fecha_cheque',res);
				//localStorage.setItem('puntos_cheque',res.puntos);
				localStorage.setItem('fecha_cheque',res.fecha);
				$('.fecha_cheque').html(res.fecha);
				var ahorro_total_actual = parseFloat(res.ahorro_total_actual);
				var ahorro_total_anterior = parseFloat(res.ahorro_total_anterior);
				var ahorro_canjeable = parseFloat(res.home_canjeable);
				var ahorro_acumulado = parseFloat(res.home_acumulado);
				var ahorro_quedan = parseFloat(res.home_no_perder);
				var fecha_acumulado = res.fecha;
				localStorage.setItem('ahorro_total_actual',ahorro_total_actual);
				localStorage.setItem('ahorro_canjeable',ahorro_canjeable);
				localStorage.setItem('ahorro_acumulado',ahorro_acumulado);
				localStorage.setItem('ahorro_quedan',ahorro_quedan);
				localStorage.setItem('fecha_acumulado',fecha_acumulado);
				pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado);
				
				var html_result = '';
				var html_result_mostrar = '';
				var total_acumulado = 0;
				var total_acumulado_2meses = 0;
				var num_meses = 0;
				var ahorro_total_acumulado_pendiente = 0;
				$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').html('');
				//Historico actual y anterior
				//Historico actual
				html_result+='<div class="row compras titulo_ahorro_historico';
				var total_mes = 0;
				total_mes = parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				var quedan = total_mes - 1;
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_actual+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(0);">'+temp_lang['ver_tickets']+' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';
				if (quedan<0)
				{
					var fecha_actual = new Date();
					var time_actual = fecha_actual.getTime();							
					var ultimo_dia_mes = new Date(fecha_actual.getFullYear(),fecha_actual.getMonth()+1,0);
					var time_comparacion = ultimo_dia_mes.getTime()-(7*24*60*60*1000);
					
					html_result+='<div class="col-xs-12"><div class="explicacion_cheque';					
					if (time_comparacion<=time_actual) html_result+=' va_a_caducar';							
					if (quedan.toFixed(2)<=-1) html_result+='">'+temp_lang['empieza_acumular']+'</div></div>';
					else html_result+='">'+temp_lang['faltan_delante']+' '+(-quedan.toFixed(2))+'&euro; '+temp_lang['faltan_detras']+'</div></div>';
				}
				else html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['sique_acumulando']+'</div></div>';
				html_result+='</div>';
				html_result+='<div class="row subtotales';
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>'+temp_lang['1detuscompras']+'</h5><p>'+parseFloat(res.ahorro_compra_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>'+temp_lang['ProductosChequeAhorro']+'</h5><p>'+parseFloat(res.ahorro_productos_actual).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang['Otros']+'</h5> <p>'+parseFloat(res.ahorro_otros_actual).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
				total_acumulado+=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				total_acumulado_2meses +=parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_actual)+parseFloat(res.ahorro_productos_actual)+parseFloat(res.ahorro_otros_actual);
				//Historico anterior
				html_result+='<div class="row compras titulo_ahorro_historico';
				var total_mes = 0;
				total_mes = parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				var quedan = total_mes - 1;
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-12"><h4 class="more_left">'+res.fecha_anterior+'</h4><span class="label label_total_mes">'+total_mes.toFixed(2).replace('.',',')+'&euro;</span><p class="more_right" onclick="cambiar_pagina(\'pagina-ahorro\',\'pagina-ahorro-historico\',false);cargar_tickets(1);">'+temp_lang['ver_tickets']+' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></p></div>';						
				if (quedan<0) html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['no_llegaste']+'</div></div>';						
				else if (res.ahorro_fecha_redencion!='') html_result+='<div class="col-xs-12"><div class="explicacion_cheque">'+temp_lang['cheque_canjeado']+' '+res.ahorro_fecha_redencion+'</div></div>';
				else 
				{
					html_result+='<div class="col-xs-12"><div class="explicacion_cheque';
					var fecha_actual = new Date();
					var time_actual = fecha_actual.getTime();							
					var array_caducidad = res.ahorro_fecha_caducidad.split('/');
					var time_caducidad = new Date(array_caducidad[2],array_caducidad[1],array_caducidad[0]);
					var time_comparacion = time_caducidad-(7*24*60*60*1000);
					
					if (time_actual>=time_comparacion) html_result+=' va_a_caducar';
					html_result+='">';
					if (res.ahorro_fecha_caducidad!='') html_result+=temp_lang['cheque_canjeable']+' '+res.ahorro_fecha_caducidad;
					else html_result+=temp_lang['disponible_cheque_apartir'];
					html_result+='</div></div>';				
					ahorro_total_acumulado_pendiente += parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				}
				html_result+='</div>';
				html_result+='<div class="row subtotales';
				if (quedan<0) html_result+=' no_alcanzado';
				html_result+='"><div class="col-xs-4 sobre_compra" ><img class="responsive" src="img/svg/ahorro/icono_sobre_compra.svg"><h5>'+temp_lang['1detuscompras']+'</h5><p>'+parseFloat(res.ahorro_compra_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 senalizados" ><img class="responsive" src="img/svg/ahorro/icono_senalizados.svg"><h5>'+temp_lang['ProductosChequeAhorro']+'</h5><p>'+parseFloat(res.ahorro_productos_anterior).toFixed(2).replace('.',',')+' <span>€</span></p><div class="icono_mas">+</div></div>';
				html_result+='<div class="col-xs-4 otros" ><img class="responsive" src="img/svg/ahorro/icono_otros.svg"><h5>'+temp_lang['Otros']+'</h5> <p>'+parseFloat(res.ahorro_otros_anterior).toFixed(2).replace('.',',')+' <span>€</span></p> </div></div>';
				total_acumulado+=parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				total_acumulado_2meses +=parseFloat(res.ahorro_compra_anterior)+parseFloat(res.ahorro_productos_anterior)+parseFloat(res.ahorro_otros_anterior);
				html_result_mostrar = html_result;						
				data_ahorro = [];
				data_ahorro_ofertas = [];
				labels_ahorro = [];
				labels_ahorro_ofertas = [];
				$.each(res.ahorro_historico, function(key, value) 
				{						
					var total_mes = 0;
					total_mes = parseFloat(value.AhorroCompra)+parseFloat(value.AhorroProductos)+parseFloat(value.AhorroOtros);
					labels_ahorro.push(value.mes_letra);
					labels_ahorro_ofertas.push(value.mes_letra);
					data_ahorro.push(total_mes.toFixed(2));	
					data_ahorro_ofertas.push((parseFloat(value.AhorroTotalPorOfertas)+total_mes).toFixed(2));
						var porcentaje_cheque = (total_mes * 35)/2;
						var quedan = total_mes - 1;
				
					num_meses++;
				});	
				data_ahorro = data_ahorro.reverse();
				data_ahorro_ofertas = data_ahorro_ofertas.reverse();
				labels_ahorro = labels_ahorro.reverse();
				labels_ahorro_ofertas = labels_ahorro_ofertas.reverse();
				if (total_acumulado>0)
				{
					html_result_mostrar+='<div class="row ahorro_total"><div class="col-xs-12" ><span class="TOTALChequeHistorico">'+temp_lang['Total']+'</span><span class="AHORROChequeHistorico">'+temp_lang['Ahorro']+'</span><p class="big_tot">'+total_acumulado_2meses.toFixed(2).replace('.',',')+'<span> €</span></p> </div></div>';
					//localStorage.setItem('puntos_cheque',ahorro_total_acumulado_pendiente);
					$('.puntos_cheque').html(ahorro_total_acumulado_pendiente.toFixed(2).replace('.',',')+'<span> €</span>');
				}
				localStorage.setItem('labels_ahorro',JSON.stringify(labels_ahorro));
				localStorage.setItem('data_ahorro',JSON.stringify(data_ahorro));
				localStorage.setItem('data_ahorro_ofertas',JSON.stringify(data_ahorro_ofertas));
				localStorage.setItem('labels_ahorro_ofertas',JSON.stringify(labels_ahorro_ofertas));
				localStorage.setItem('bloques_historicos_chequeahorro',html_result_mostrar);
				if (html_result_mostrar!='') $('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(html_result_mostrar);
			}		 
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			labels_ahorro = JSON.parse(localStorage.getItem('labels_ahorro'));
			data_ahorro = JSON.parse(localStorage.getItem('data_ahorro'));
			data_ahorro_ofertas = JSON.parse(localStorage.getItem('data_ahorro_ofertas'));
			labels_ahorro_ofertas = JSON.parse(localStorage.getItem('labels_ahorro_ofertas'));
			var ahorro_acumulado = parseFloat(localStorage.getItem('ahorro_acumulado'));
			var ahorro_quedan = parseFloat(localStorage.getItem('ahorro_quedan'));
			var ahorro_canjeable = parseFloat(localStorage.getItem('ahorro_canjeable'));
			var ahorro_total_actual = parseFloat(localStorage.getItem('ahorro_total_actual'));
			var fecha_acumulado = localStorage.getItem('fecha_acumulado');
			pintar_numeros_home(ahorro_acumulado,ahorro_quedan,ahorro_canjeable,ahorro_total_actual,fecha_acumulado);
			$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').html('');		
			$('.pagina-ahorro .container-fluid .bloques_historicos_chequeahorro').append(localStorage.getItem('bloques_historicos_chequeahorro'));
			
			$('.fecha_cheque').html(localStorage.getItem('fecha_cheque'));
			$('.cheque_ahorro_acumulando .saldo_fecha').html(localStorage.getItem('fecha_cheque'));
			$('.puntos_cheque').html(localStorage.getItem('puntos_cheque'));
			$('.tot_desde').html(localStorage.getItem('fecha_cheque'));
		}
	});
}
function cargar_datos_locales()
{
	cargar_datos_personales();	
	if (localStorage.getItem('habituales')!=''&&localStorage.getItem('habituales')!=null)
	{
		$('.opcion_mi_lista_habituales').html(localStorage.getItem('habituales'));
	}	
	if (localStorage.getItem('ocasionales')!=''&&localStorage.getItem('ocasionales')!=null)
	{
		$('.opcion_mi_lista_ocasionales').html(localStorage.getItem('ocasionales'));
	}
	if (localStorage.getItem('mi_lista')!=''&&localStorage.getItem('mi_lista')!=null)
	{
		mi_lista = JSON.parse(localStorage.getItem('mi_lista'));	
		/*
		$.each(mi_lista, function(index,value){			
			$('.opcion_mi_lista').append(value);
			$('#habituales_bloque_'+index+' a.boton_anadir_mi_lista').addClass('marcado');
			$('#habituales_bloque_'+index+' a.boton_anadir_mi_lista').removeClass('marcar');			
		});
		*/
	}	
	if (localStorage.getItem('mi_lista_unidades')!=''&&localStorage.getItem('mi_lista_unidades')!=null)
	{
		mi_lista_unidades = JSON.parse(localStorage.getItem('mi_lista_unidades'));	
	}	
	if (localStorage.getItem('cupones')!=''&&localStorage.getItem('cupones')!=null)
	{
		$('.opcion_mi_lista_cupones').html(localStorage.getItem('cupones'));
		$('.pagina-megacupon .opcion_mi_lista_cupones2').html(localStorage.getItem('cupones'));	
		$('.desde span.valor_fecha').html(localStorage.getItem('cupon_desde'));		
		$('.hasta span.valor_fecha').html(localStorage.getItem('cupon_hasta'));
	}		
	if (localStorage.getItem('cupones_siguiente')!=''&&localStorage.getItem('cupones_siguiente')!=null)
	{
		$('.pagina-megacupon-siguiente .opcion_mi_lista_cupones2').html(localStorage.getItem('cupones_siguiente'));				
		$('.desde span.valor_fecha_siguiente').html(localStorage.getItem('cupon_desde_siguiente'));
		$('.hasta span.valor_fecha_siguiente').html(localStorage.getItem('cupon_hasta_siguiente'));
	}
	regenerar_token();
}
function charCode(caracter)
{
	return caracter.charCodeAt(0);
}
function LetraToPrefijo(chLetra)
{
	chLetra.toUpperCase();	
	if ((chLetra == 'I') || (chLetra == 'O') || (chLetra == 'T')) return false;
	if ((charCode(chLetra) >= charCode('0')) && (charCode(chLetra) <= charCode('9')))	
	{
		return true;
	}
	else if ((charCode(chLetra) >= charCode('A')) && (charCode(chLetra) <= charCode('Z')))
	{		
		return true;
	}
	else
	{		
		return false;
	}
}
function GetDC_DNI_NIE(pstrNIF) 
{
	var chCheck = "TRWAGMYFPDXBNJZSQVHLCKE";	
	if (pstrNIF.substr(0,1) == 'X') pstrNIF = '0'+pstrNIF.substr(1);
	else if (pstrNIF.substr(0,1) == 'Y') pstrNIF = '1'+pstrNIF.substr(1);
	else if (pstrNIF.substr(0,1) == 'Z') pstrNIF = '2'+pstrNIF.substr(1);
	
	var iNum = parseInt(pstrNIF);

	return (chCheck.substr((iNum % 23),1));
}
function GetDC_NIF_CIF(pstrNIF) 
{	
	var iCont = 0;
	var iVal = 0;
	var iDigit = 0;

	for (var i = 1 ; i <= 7 ; i++)
	{
  		iVal = parseInt(pstrNIF.substr(i,1));
	    if (i % 2 == 0) iCont+=iVal;
		else iCont+= parseInt((2 * iVal) / 10) + parseInt((2 * iVal) % 10) ;
	}
	iDigit = 10 - parseInt(iCont % 10) ;													//en el rango [1..10] ó ['A'..'J']	
	if (((charCode(pstrNIF.substr(0,1)) >= charCode('A')) && (charCode(pstrNIF.substr(0,1)) <= charCode('H'))) || (pstrNIF.substr(0,1) == 'J') || ((charCode(pstrNIF.substr(0,1)) >= charCode('U')) && (charCode(pstrNIF.substr(0,1)) <= charCode('V'))))
		return (parseInt(charCode('0')) + parseInt(iDigit % 10)) ;												//digito control numérico
	else																			
		return (parseInt(charCode('A')) + parseInt(iDigit - 1)) ;												//digito control letra
}
function GetDC_NIF(pstrNIF) 
{	
	var strAux = '' ;		
	if (pstrNIF.length != 8) return '' ;			
	if (!LetraToPrefijo(pstrNIF.substr(0,1))) return '';		
	for (var i = 1 ; i <= 7 ; i++)
	{
		if ((charCode(pstrNIF.substr(i,1)) < charCode('0')) || (charCode(pstrNIF.substr(i,1)) > charCode('9'))) return '';
	}
	strAux = pstrNIF;
	strAux.toUpperCase();

	if (((charCode(strAux.substr(0,1)) >= charCode('0')) && (charCode(strAux.substr(0,1)) <= charCode('9'))) || ((charCode(strAux.substr(0,1)) >= charCode('X')) && (charCode(strAux.substr(0,1)) <= charCode('Z'))))
	{		
		return charCode(GetDC_DNI_NIE(strAux)) ;
	}
	else
	{	
		return GetDC_NIF_CIF(strAux) ;
	}
}
function validar_dni(dni)
{
	if (dni.length != 9) return false ;
    var strAux = dni.substr(0,8);
	var chChkOld = dni.substr(8,1).toUpperCase();
    return (parseInt(GetDC_NIF(strAux))  == parseInt(charCode(chChkOld))) ;
}
function guardar_datos_personales()
{
	var errores_datos = '';	
	if ($('#nombre').val()=='') errores_datos+= temp_lang['nombre_obligatorio']+'<br/>';
	if ($('#apellido1').val()=='') errores_datos+= temp_lang['primer_apellido_obligatorio']+'<br/>';	
	//if ($('#sexo').val()==''||$('#sexo').val()=='Seleccionar') errores_datos+= 'Indicar el sexo es una campo obligatorio.<br/>';
	//if ($('#tipo_direccion').val()==''||$('#tipo_direccion').val()=='Seleccionar') errores_datos+= 'El tipo de dirección es una campo obligatorio.<br/>';
	if ($('#movil').val()!=''&&(parseInt($('#movil').val())<=99999999||parseInt($('#movil').val())>999999999)) errores_datos+= temp_lang['formato_telefono_obligatorio']+"<br/>";
	if ($('#movil').val()=='') errores_datos+= temp_lang['error_movil_registro']+'<br/>';
	if ($('#email').val()!=''&&!validateEmail($('#email').val())) errores_datos += temp_lang['formato_email_obligatorio']+"<br/>";	
	if ($('#fecha_nacimiento').val()=='') errores_datos+= temp_lang['fecha_nacimiento_obligatorio']+"<br/>";	
	if (errores_datos == '')
	{
		var url_datos = 'https://www.appfornes.es/servicios-web/enviar_datos_personales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf';
		var parametros = '&cod_cliente='+cod_cliente_digital+		
		'&nombre='+$('#nombre').val()+
		'&apellido1='+$('#apellido1').val()+
		'&apellido2='+$('#apellido2').val()+
		'&sexo='+$('#sexo').val()+
		'&nacionalidad='+$('#nacionalidad').val()+
		'&movil='+$('#movil').val()+
		'&email='+$('#email').val()+
		'&fecha_nacimiento='+$('#fecha_nacimiento').val()+
		'&idioma='+idioma;
		parametros = parametros.replace(/'/g, "\\'"); //Equivalente al addslashes
		url_datos = url_datos + parametros;
		$.ajax({
			type:'GET',
			timeout: 5000,
			dataType: 'json',
			url: url_datos,
			success:function(res, textStatus, XMLHttpRequest)
			{	
				if (res.validacion=='ok')
				{
					cambios_hechos_perfil = 0;
					if (bloquear_en_perfil==1)
					{
						bloquear_en_perfil = 0;
						localStorage.setItem('bloquear_en_perfil',0);
						cambiar_pagina('pagina-portada','pagina-portada');
					}
					$('.nombre_en_home').html($('#nombre').val());
					localStorage.setItem('solo_nombre',$('#nombre').val());					
					swal({
						title: temp_lang["ActualizacionDatos"],
						text: decodeURIComponent(unescape(res.mensaje)),
						html: true,
						type: "success"
					});
				}
				else
				{
					swal({
						title: temp_lang["Error"],
						text: decodeURIComponent(unescape(res.mensaje)),
						html: true,
						type: "error"
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});		
	}
	else
	{
		swal({
			title: temp_lang["Error"],
			text: errores_datos,
			html: true,
			type: "error"
		});
	}
}
function activar_ajuste(item)
{
	$.ajax({
		type:'GET',
		timeout: 3000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/ajustes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&item='+item+'&activar=1&cod_cliente='+cod_cliente_digital+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
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
		url:'https://www.appfornes.es/servicios-web/ajustes.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&item='+item+'&activar=0&cod_cliente='+cod_cliente_digital+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});		
}
function cargar_datos_personales()
{	
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/cargar_datos_personales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente_digital+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
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
function regenerar_token()
{
	var uuid_telefono = '';
	var model = '';	
	if (typeof(device)!= 'undefined') 
	{
		uuid_telefono = device.uuid;
		localStorage.setItem('device_id',uuid_telefono);
		model = device.platform+' '+device.model;
	}
	var parametros = {
		"token_fcm" : $('#token_fcm').val(),
		"key_acceso" : 'c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf',
		"cod_cliente" : cod_cliente,		
		"uuid_telefono" : uuid_telefono,
		"sistema_operativo" : sistema_operativo,
		"idioma" : idioma,
		"modelo" : model
	}
	if (cod_cliente!=''&&$('#token_fcm').val()!=''&&model!=''&&uuid_telefono!='')
	{
		$.ajax({
			data: parametros,
			type:'post',
			timeout: 3000,
			dataType: 'json',		
			url:'https://www.appfornes.es/servicios-web/regenerar_token.php',
			success:function(res, textStatus, XMLHttpRequest)
			{
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});	
	}
}
function resetear_todo()
{
	$('.otros-cupones-seccion-cupones').html('');
	$('.grup_cupones').html('');
	vaciar_lista_confirmado();
}
function chequear()
{
	if (relacion_tarjetas_hogares.tiene_descuentos==1)
	{
		swal({
			title: temp_lang["Error"],
			text: temp_lang['error_tarjeta_chequeo_especial'],
			html: true
		});
	}
	else
	{
		cambiar_pagina('pagina-tarjeta_no_tengo','pagina-tarjeta_chequear');
	}
}
function vincular_tarjeta()
{	
	resetear_todo();	
	var error_validar_datos = '';		
	if ($('#dni').val()=='') error_validar_datos+=temp_lang['error_dni']+'<br/>';
	if ($('#numero_tarjeta').val()=='') error_validar_datos+=temp_lang['error_tarjeta_obligatorio']+'<br/>';
	if (error_validar_datos!='')
	{
		swal({
			title: temp_lang["Error"],
			text: error_validar_datos,
			html: true
		});
	}
	else 
	{
		var uuid_telefono = '';
		var model = '';
		if (typeof(device)!= 'undefined') 
		{
			uuid_telefono = device.uuid;
			model = device.platform+' '+device.model;
		}
		if ($('#dni').val()!='') dni_cliente = $('#dni').val()
		else dni_cliente = localStorage.getItem('dni');
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/comprobacion_tarjeta.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+$('#numero_tarjeta').val()+'&dni_cliente='+dni_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				if (res.validacion=='ok')
				{
					respuesta_necesario_chequeo = res;
					cod_cliente = res.cod_cliente_normalizado;
					$('#numero_tarjeta').val(res.cod_cliente_normalizado);
					if (res.error_tarjeta)
					{
						swal({
							title: temp_lang["Error"],
							text: temp_lang["error_tarjeta_mal"],
							html: true,
							type: "error"
						});						
					}
					else
					{
						if (res.necesario_chequeo&&!escaneado) chequear();
						else vincular_tarjeta_consolidado();
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});	
	}
}
function vincular_tarjeta_consolidado()
{
	var uuid_telefono ='';
	var model = '';
	if (typeof(device)!= 'undefined') 
	{
		uuid_telefono = device.uuid;
		model = device.platform+' '+device.model;
	}
	if ($('#numero_tarjeta').val()!='') cod_cliente = $('#numero_tarjeta').val();
	else cod_cliente = localStorage.getItem('cod_cliente');
	if (cod_cliente.length>9)
	{
		cod_cliente = cod_cliente.substring(3,12);
	}
	var parametros = {
		"token_fcm" : $('#token_fcm').val(),
		"key_acceso" : 'c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf',
		"movil" : $('#movil_chequeo').val(),
		"fidelizado" : fidelizado,
		"escaneado" : escaneado,
		"necesario_chequeo" : necesario_chequeo,
		"cod_cliente" : cod_cliente,
		"dni_cliente" : dni_cliente,
		"uuid_telefono" : uuid_telefono,
		"sistema_operativo" : sistema_operativo,
		"modelo" : model,
		"idioma" : idioma,
		"anticache": (new Date()).getTime()
	}	
	$.ajax({
		data: parametros,
		type:'post',
		timeout: 3000,
		dataType: 'json',		
		url:'https://www.appfornes.es/servicios-web/vincular_tarjeta.php',
		success:function(res, textStatus, XMLHttpRequest)
		{			
			var resultado_ajax ='';			 
			 if (res.validacion=='ok')
			 {
				 $('.nombre_tarjeta').html(res.nombre_tarjeta);
				 $('.numero_tarjeta').html(res.cod_cliente);
				 $('.numero_tarjeta_scan').html(res.cod_cliente);
				 $('.nombre_en_home').html(res.solo_nombre);
				 localStorage.setItem('solo_nombre',res.solo_nombre);					 
				 localStorage.setItem('cod_cliente',res.cod_cliente);
				 localStorage.setItem('cod_cliente_digital',res.cod_cliente_digital);
				 cod_cliente = res.cod_cliente;
				 cod_cliente_digital = res.cod_cliente_digital;
				 localStorage.setItem('dni_cliente',$('#dni').val());				 
				 localStorage.setItem('numero_tarjeta',res.cod_cliente);
				 var ahora = new Date();
				 localStorage.setItem('time_vinculacion',ahora.getTime());
				 id_home = res.id_home;
				 localStorage.setItem('id_home',id_home);
				 obtener_mensajes();
				 obtener_chequeahorro();
				 obtener_multicupon();
				 obtener_vales(0);
				 obtener_mensajes(0,0);
				 cargar_top_habituales();
				 cargar_datos_locales();
				 cambiar_pagina('','pagina-tarjeta_cargada');
				 if (res.solo_nombre=='')
				 {										
					cambiar_pagina('','pagina-perfil');
					bloquear_en_perfil = 1;
					localStorage.setItem('bloquear_en_perfil',bloquear_en_perfil);
					swal({
						title: temp_lang["NECESITAMOSTUSDATOS"],
						text: temp_lang["necesitamos_datos_texto"],
						html: true
					});
				 }
			 }
			 else
			 {
				swal({
					title: temp_lang["Error"],
					text: decodeURIComponent(unescape(res.mensaje)),
					html: true
				});
			 }
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			swal({
				title: temp_lang["Error"],
				text: temp_lang["ErrorConexion"],
				html: true,
				type: "error"
			});
		}
	});	
}
function continuar_volver()
{
	if (bloquear_en_perfil==1)
	{
		swal({
			title: temp_lang["NECESITAMOSTUSDATOS"],
			text: temp_lang["necesitamos_datos_texto"],
			html: true
		});
	}
	else
	{
		if (pagina_actual!='pagina-dni_registro')
		{
			//No permitimos hacer back en el registro
			if (cupon_resaltar!='')
			{
				$('.pagina-tarjeta .cupon_'+cupon_resaltar).removeClass('resaltar_cupon');
				cupon_resaltar = '';
			}
			console.log('VOLVER:');
			console.log(origen_array);
			console.log(origen_subseccion);
			console.log(origen_altura);	
			if (origen_array.length>0)
			{		
				$('.pagina').hide();
				origen_array.pop();
				origen_subseccion.pop();
				origen_altura.pop();				
				var pagina_anterior = origen_array[origen_array.length-1];								
				var array_excepciones_home = ["activar_lista('opcion_mi_lista_cupones','opcion_cupones',false);","cargar_ahorro(false);","cargar_ahorro_ofertas(false);","activar_lista('listas','opcion_mi_lista',false);","activar_lista('opcion_mi_lista_ocasionales','opcion_ocasionales',false);","activar_lista('opcion_mi_lista_habituales','opcion_habituales',false);","activar_lista('opcion_mi_lista_cupones','opcion_cupones',false);"];				
				if (pagina_anterior==undefined) navigator.app.exitApp();
				//alert(pagina_anterior+'||'+pagina_actual);
				//alert(origen_subseccion[origen_subseccion.length-1]);	
				if (pagina_anterior=='pagina-folletos-vista'&&pagina_actual=='pagina-folletos-grid')
				{					
					if (numero_folletos>1)
					{						
						pagina_anterior = 'pagina-folletos';
						pagina_actual = 'pagina-portada';
						var script_anterior = '';
						origen_subseccion = ['',''];
						var scroll_inicial = 0;
						origen_altura = ['',''];
						origen_array = ['pagina-portada','pagina-folletos'];						
					}
					else
					{					
						pagina_anterior = 'pagina-portada';
						pagina_actual = 'pagina-portada';
						var script_anterior = '';
						origen_subseccion = [];
						var scroll_inicial = 0;
						origen_altura = [];
						origen_array = [];
						origen_array.push('pagina-portada');
					}
				}
				else if ((pagina_anterior == 'pagina-ahorro-historico'||pagina_anterior=='pagina-lista_individual') && (array_excepciones_home.includes(origen_subseccion[origen_subseccion.length-1])||origen_subseccion[origen_subseccion.length-1]==''))
				{
					pagina_anterior = 'pagina-portada';
					pagina_actual = 'pagina-portada';
					var script_anterior = '';
					origen_subseccion = [];
					var scroll_inicial = 0;
					origen_altura = [];
					origen_array = []
					origen_array.push('pagina-portada');
				}
				else
				{
					pagina_actual = pagina_anterior;
					var script_anterior = origen_subseccion[origen_subseccion.length-1];		
					if (script_anterior!='') eval(script_anterior);		
					var scroll_inicial = origen_altura[origen_altura.length-1];
				}
				$('.'+pagina_anterior).fadeIn();				
				$("."+pagina_anterior+" .container-fluid").animate({ scrollTop: scroll_inicial }, "fast");
				//window.scrollTo(scroll_inicial,0);
			}
			else navigator.app.exitApp();
			console.log('TRAS VOLVER:');
			console.log(origen_array);
			console.log(origen_subseccion);
			console.log(origen_altura);
		}
		else navigator.app.exitApp();
	}
}
function volver()
{
	if (bloquear_en_perfil) cambios_hechos_perfil = 0;
	if (cambios_hechos_perfil)
	{
		permitir_navegar = false;
		swal({
			title: temp_lang['perderas_cambios'],
			text: temp_lang['has_hecho_cambios'],
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: temp_lang['confirmar_sin_guardar'],
			cancelButtonText: temp_lang['no_quiero_seguir'],
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm)
		{
			if (isConfirm)
			{
				cambios_hechos_perfil = 0;
				continuar_volver();
			} 
			else 
			{
				
			}
		});	
	}
	else continuar_volver();
}
function ampliar_qr()
{	
	if (qr_ampliado) 
	{
		//$('#barcode_codigo_tarjeta_qr').css('padding','35vw 25vw 0vw');		
		qr_ampliado = false;
	}
	else 
	{
		//$('#barcode_codigo_tarjeta_qr').css('padding','35vw 15vw 0vw');		
		qr_ampliado = true;
	}
}
function abrir_contactar()
{	
	swal({title:temp_lang["Contactar"], html: true,text:"<p>"+temp_lang['text_contactar']+".</p><br/><a class='btn btn-primary' href='tel:+34900777000'><i class='fa fa-phone'></i> "+temp_lang['Llamar']+"</a> <a class='btn btn-primary' href='mailto:contacta.masymas@fornes.net'><i class='fa fa-envelope-o'></i> "+temp_lang['Mail']+"</a>", confirmButtonText:temp_lang['Cerrar']});
}
function menushow()
{
	if ($('.pagina-menu').is(':hidden')) 
	{
		pagina_actual= ($(".pagina:visible").attr('class').split(' ')[1]);
		$('.pagina-menu-cortina').show();
        $('.pagina-menu').show();		
		$('.pagina-menu').addClass('menu_visible');
		$('.pagina-menu').removeClass('menu_invisible');
	}
	else
	{
		$('.pagina-menu').addClass('menu_invisible');
		$('.pagina-menu').removeClass('menu_visible');
		$('.pagina-menu-cortina').hide();
		setTimeout(function (){
			$('.pagina-menu').hide();	    
			$('.pagina-menu-cortina').hide();					
		}, (500));
        $('.'+pagina_actual).fadeIn()
	}
		/*
        if ($('.pagina-menu').is(':hidden')) {
			$("html, body").animate({ scrollTop: 0 }, "fast");
                pagina_actual= ($(".pagina:visible").attr('class').split(' ')[1]);
                //$('.pagina').hide();
				//$('.show_menu img').attr('src','img/svg/menu/close_menu.svg');
				$('.pagina-menu-cortina').show();
                $('.pagina-menu').toggle('slide');
            }
        else {
               $('.pagina-menu').hide('slide');
			   //$('.show_menu img').attr('src','img/svg/portada/menu.svg');
			   $('.pagina-menu-cortina').hide();
               $('.'+pagina_actual).fadeIn()
            }
			*/
}
function convertir_en_solo_numero(id_elemento)
{	
	var cadena_salida = '';
	var cadena = $('#'+id_elemento).val();	
	if (cadena!=''&&cadena!=undefined)
	{
		for (var i=0;i<cadena.length;i++)
		{
			if (cadena.charAt(i)=='0'||cadena.charAt(i)=='1'||cadena.charAt(i)=='2'||cadena.charAt(i)=='3'||cadena.charAt(i)=='4'||cadena.charAt(i)=='5'||cadena.charAt(i)=='6'||cadena.charAt(i)=='7'||cadena.charAt(i)=='8'||cadena.charAt(i)=='9')
			{
				cadena_salida+= cadena.charAt(i);
			}
		}	
		cadena_salida = cadena_salida.trim();
		$('#'+id_elemento).val(cadena_salida);
	}
}
function recuperar_tarjeta()
{	
	var errores = '';
	if ($('#dni').val()=='') errores+= temp_lang['error_dni'];	
	if (errores=='')
	{		
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/recuperar_tarjeta_sms.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&movil='+relacion_tarjetas_hogares.datos_cliente.nu_mobile+'&tarjeta='+relacion_tarjetas_hogares.tarjeta_recuperable+'&texto_sms='+temp_lang['su_numero_tarjeta_es']+'&idioma='+idioma,
			success:function(res, textStatus, XMLHttpRequest)
			{
				swal({
					title: temp_lang['tarjeta_enviada_sms'],
						text: temp_lang['tarjeta_enviada_sms_texto']+' '+relacion_tarjetas_hogares.datos_cliente.nu_mobile,
						html: true,
						type: "success"
				});
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"					
				});
			}
		});				
	}
	else		
	{
		swal({
			title: temp_lang['HemosEncontradoProblema'],
			text: errores,
			html: true,
			type: "error"
		});
	}
}
function refrescar_centro_gps()
{
	if (typeof(google)==='object')
	{
		var latLong2 = new google.maps.LatLng($('#latitudGPS_centro').val(), $('#longitudGPS_centro').val());
		marker_ubicacion_actual.setMap(null);
		marker_ubicacion_actual = new google.maps.Marker({
			position: latLong2,
			icon: new google.maps.MarkerImage('img/svg/tiendas/ICON2.gif',null,null,null,new google.maps.Size(30, 30))
		});
		marker_ubicacion_actual.setMap(map);
	}
}
function refrescarMapa(origen_mapa)
{
	if (typeof(google)==='object')
	{
		var mapOptions = {
			center: new google.maps.LatLng(0, 0),
			zoom: 1,
			mapTypeControl:false,
			streetViewControl: false,
			zoomControl: false,
			fullscreenControl:false,
			/*
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL,
				position: google.maps.ControlPosition.RIGHT_CENTER
			},
			*/
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map"), mapOptions);	
		var latLong = new google.maps.LatLng(latitud, longitud);
		var latLong2 = new google.maps.LatLng($('#latitudGPS').val(), $('#longitudGPS').val());
		//added
		console.log(latitud+"--"+longitud);	
		//added recorrerr array    
		marker_ubicacion_actual = new google.maps.Marker({
			position: latLong2,
			icon: new google.maps.MarkerImage('img/svg/tiendas/ICON2.gif',null,null,null,new google.maps.Size(30, 30))
		});
		marker_ubicacion_actual.setMap(map);
		
		
		map.setZoom(10);
		if (origen_mapa) 
		{
			//alert('center1');
			map.setCenter(latLong2);
		}
		else 
		{
			//alert('center2');
			//alert(latitud+'|'+longitud);
			map.setCenter(latLong);
		}

		if(listado_tiendas===null){
			console.log("no hay tiendas");
		}
		else{
			$.each(listado_tiendas, function(key, value){
				//console.log(JSON.stringify(value));
				var latLong = new google.maps.LatLng(value.Latitud, value.Longitud);
				var marker = new google.maps.Marker({ position: latLong ,
				icon: new google.maps.MarkerImage('img/svg/tiendas/ICON1.svg',null,null,null,new google.maps.Size(57, 65))
				});
				marker.addListener('click', function() {
					resultado_ajax='<div class="row tienda_result tabler';
					resultado_ajax+='">';
					resultado_ajax+='<a class="cerrar_bloque_mas_cercana" href="" onclick="$(\'.lista_mapa_fixed\').hide(\'fast\');return false;"><i class="fa fa-close"></i></a>';
					resultado_ajax+='<div class="distancia tienda_bloque_1"><img src="img/svg/mari_logo.svg"></div>';
					resultado_ajax+='<div onclick="ver_ficha_comercio('+key+');" class="tienda_bloque_2"><p class="res_ciudad"><strong>'+value.Municipio+', '+value.Provincia+'</strong></p>';
					resultado_ajax+='<p class="res_direccion">'+value.CalleYNumero+'</p>';
					resultado_ajax+='<p class="res_codigo_postal">LUNES - S&Aacute;BADO: '+value.Horario;
					if (value.Parking==1) resultado_ajax+='<br/><span class="tiene_parking"><i class="fa fa-car"></i> parking</span>';
					resultado_ajax+='</p>';
					resultado_ajax+='<span class="btn btn-primary">'+temp_lang['ComoLlegar']+'</span>';				
					resultado_ajax+='</div>';
					resultado_ajax+='<div class="dist"><p class="">'+temp_lang['Distancia']+'</p>';
					if (value.distancia<1) resultado_ajax+='<p class="res_metros">'+parseInt(value.distancia*1000)+'m<p>';
					else resultado_ajax+='<p class="res_metros">'+(value.distancia).toFixed(2)+'Km<p>';                    
					resultado_ajax+='</div>';                			
					resultado_ajax+='</div>';
					
					$('#tienda-pinchada').html(resultado_ajax);
					$('#lista-tiendas').hide();
					$('.ver_listado').hide();
					$('#tienda-pinchada').show();		
					//alert('center3');					
					map.setCenter(marker.getPosition());
				});
				marker.setMap(map);
				map.setZoom(15);
				//map.setCenter(marker.getPosition());
			});
		}
	   
		/*
		setTimeout(function() {
			map = new google.maps.Map(document.getElementById('mapa'), options);
			var styledMapType = new google.maps.StyledMapType(styles, {
				name: 'Styled'
			});
			map.mapTypes.set('Styled', styledMapType);
			if ($('#latitudGPS').val()!='') latitud = $('#latitudGPS').val();
			if ($('#longitudGPS').val()!='') longitud = $('#longitudGPS').val();
			map.setCenter(new google.maps.LatLng(latitud, longitud));
			map.setZoom(9);	
		}, 300);
		*/
	}
}
function ver_mapa()
{
    $('.pagina-tiendas').removeClass('fondo_mari');
			$('.aviso_loc').hide();
			$('.ver_mapa').hide();
			$('.mapa_container').fadeIn();
			$('.ver_listado').fadeIn();
			$('.lista_results').fadeIn();
	//alert('precenter1');
	refrescarMapa(0);
	setTimeout(function() 
	{
		if (map) 
		{
			google.maps.event.trigger(map, 'resize');
		}			
	}, 300);
}
function ver_ficha_comercio(indice)
{
	console.log(listado_tiendas[indice]);
	if (sistema_operativo=='android') window.location = "https://www.google.com/maps?q="+listado_tiendas[indice]['Latitud']+","+listado_tiendas[indice]['Longitud'];
	else	
	{
		swal({title:temp_lang["ComoLlegar"], html: true,text:"<p>"+temp_lang["SeleccionTipoIndicaciones"]+".</p><a class='btn btn-primary' onclick='cordova.InAppBrowser.open(\"https://www.google.com/maps?q="+listado_tiendas[indice]['Latitud']+","+listado_tiendas[indice]['Longitud']+"\",\"_blank\",\"location=no\");return false;' href='#'>Google Maps</a> <a class='btn btn-primary' href='maps:?q="+listado_tiendas[indice]['Latitud']+","+listado_tiendas[indice]['Longitud']+"("+listado_tiendas[indice]['CalleYNumero']+")'>Apple Maps</a>", confirmButtonText:'Cerrar'});
		//swal({title:temp_lang["ComoLlegar"], html: true,text:"<p>"+temp_lang["SeleccionTipoIndicaciones"]+".</p><a class='btn btn-primary' href='geo:"+listado_tiendas[indice]['Latitud']+","+listado_tiendas[indice]['Longitud']+"("+listado_tiendas[indice]['CalleYNumero']+")'>Google Maps</a> <a class='btn btn-primary' href='maps:"+listado_tiendas[indice]['Latitud']+","+listado_tiendas[indice]['Longitud']+"("+listado_tiendas[indice]['CalleYNumero']+")'>Apple Maps</a>", confirmButtonText:'Cerrar'});
	}
}
function buscar_por_direccion()
{	
	var direccion = $('#BuscaPorDireccion').val();
	if (direccion=='Denia')
	{
		var latitud_antigua = latitud;
		var longitud_antigua = longitud;
		latitud = 38.839622;
		longitud = 0.103721;		
		obtener_tiendas(1);
		//alert('precenter2');
		refrescarMapa(0);
	}
	else if (direccion)
	{	
		var geocoder = new google.maps.Geocoder();
		var result = direccion.match(/Murcia/i);
		if (result)
		{
			direccion+='. Murcia';
		}
		else direccion+='. Comunidad Valenciana o Murcia';		
		geocoder.geocode( { 'address': direccion }, function(results, status) {    			
			//si el estado de la llamado es OK
			if (status == google.maps.GeocoderStatus.OK) 
			{
				var latitud_antigua = latitud;
				var longitud_antigua = longitud;
				latitud = parseFloat(results[0].geometry.location.lat());
				longitud = parseFloat(results[0].geometry.location.lng());
				if (latitud>parseFloat('37.37')&&latitud<parseFloat('40.78')&&longitud<parseFloat('0.1787')&&longitud>parseFloat('-2.35'))
				{
					//alert('tiendas1');
					obtener_tiendas(1);
					//alert('precenter3');
					refrescarMapa(0);
				}
				else
				{
					latitud = latitud_antigua;
					longitud = longitud_antigua;
					swal({
						title: temp_lang['HemosEncontradoProblema'],
						text: temp_lang['GeocoderErrorFuera'],
						html: true
					});
				}				
			/*
		                    if (entrar)	$("#long").focusout();
		                    else{
		                    	var lat = $("#lat").val();
		        				var long = $("#long").val();
		                    	$.getJSON("getDistancia.php", { lat: lat, long:long, latitud:latitud, longitud:longitud})
								.done(function(datos) {
									$('#dist'+concat).val(datos);									
								})
		                    }
			*/
			} 
			else
			{				
				//si no es OK devuelvo error
				$("#latitudGPS").val('');
				$("#longitudGPS").val('');					  	
				swal({
					title: temp_lang['HemosEncontradoProblema'],
					text: temp_lang['GeocoderError'],
					html: true,
					type: "error"
				});
			}
		});
	}
}
function cargar_pagina_tiendas()
{
	if ($('#latitudGPS').val()==''||$('#longitudGPS').val()==''||($('#latitudGPS').val()==0&&$('#longitudGPS').val()==0))
	{
		swal({
			title: temp_lang['HemosEncontradoProblema'],
			text: temp_lang['GPSNoactivadoEspere'],
			html: true,
			type: "error"
		});		
	}
	else
	{
		cambiar_pagina('pagina-portada','pagina-tiendas');
	}
}
function centrar_posicion()
{
	if ($('#latitudGPS').val()==''||$('#longitudGPS').val()==''||($('#latitudGPS').val()==0&&$('#longitudGPS').val()==0))
	{
		//Reiniciamos app
		//window.location.reload(true);
	}
	else refrescarMapa(1);
}
function obtener_tiendas(recalculo)
{	
	var url_tiendas = 'https://www.appfornes.es/servicios-web/tiendas_cercanas.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&latitud='+$('#latitudGPS').val()+'&longitud='+$('#longitudGPS').val()+'&idioma='+idioma;
	if (recalculo) url_tiendas+='&latitud2='+$('#latitudGPS2').val()+'&longitud2='+$('#longitudGPS2').val();
	console.log(url_tiendas);
	$.ajax({
	type:'GET',
	timeout: 10000,
	dataType: 'json',
	//url:'https://www.appfornes.es/servicios-web/tiendas_cercanas.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&latitud='+$('#latitudGPS').html()+'&longitud='+$('#longitudGPS').html(),
	url: url_tiendas,
	
	success:function(res, textStatus, XMLHttpRequest)
	{			
		//added
		listado_tiendas=[];
		console.log("listado:"+JSON.stringify(listado_tiendas));
		var resultado_ajax ='<a class="cerrar_bloque_mas_cercana" href="" onclick="$(\'.lista_mapa_fixed\').toggle(\'fast\');return false;"><i class="fa fa-close"></i></a><div class="lamas"><p>'+temp_lang['LaMasCercana']+'</p></div>';
		var primera = true;
		var indice=0;
		$.each(res, function(key, value) 
		{			
			if (primera) 
			{
				resultado_ajax+='<div class="row tienda_result tabler';
				primera = false;
				resultado_ajax+='">';
				resultado_ajax+='<div class="tienda_bloque_1 distancia"><img src="img/svg/mari_logo.svg"></div>';
				if (sistema_operativo=='android')
				{
					//En Android
					//resultado_ajax+='<div class="row"><div class="col-xs-12 tienda"><a href="geo:'+value.latitud+','+value.longitud+'('+value.nombre+')"><img class="globo-tienda-masymas" src="img/globo-masymas.svg"/></a>';				 
				}
				else
				{
					//En Iphone
					//resultado_ajax+='<div class="row"><div class="col-xs-12 tienda"><a href="maps:'+value.latitud+','+value.longitud+'('+value.nombre+')"><img class="globo-tienda-masymas" src="img/globo-masymas.svg"/></a>';
				}	
				
				resultado_ajax+='<div class="tienda_bloque_2 data"><p class="res_ciudad"><strong>'+value.Municipio+', '+value.Provincia+'</strong></p>';
					resultado_ajax+='<p class="res_direccion">'+value.CalleYNumero+'</p>';
					resultado_ajax+='<p class="res_codigo_postal">Lunes - S&aacute;bado: '+value.Horario;
					if (value.Parking==1) resultado_ajax+='<br/><span class="tiene_parking"><i class="fa fa-car"></i> parking</span>';
					resultado_ajax+='</p>';
					resultado_ajax+='<span onclick="ver_ficha_comercio('+indice+');" class="btn btn-primary">'+temp_lang['ComoLlegar']+'</span>';
					//resultado_ajax+='<p class="res_tel"><strong>T. </strong><a href="tel:+34'+value.Telefono+'">'+value.Telefono+'</a></p>';
				resultado_ajax+='</div>';
				resultado_ajax+='<div class="tienda_bloque_3 dist"><p class="">'+temp_lang['Distancia']+'</p>';
				if (value.distancia<1) resultado_ajax+='<p class="res_metros">'+parseInt(value.distancia*1000)+'m<p>';
				else resultado_ajax+='<p class="res_metros">'+(value.distancia).toFixed(2)+'Km<p>';                    
				resultado_ajax+='</div>';                
				
				if (sistema_operativo=='android')
				{
					//En Android
					//resultado_ajax+='<a href="geo:'+value.latitud+','+value.longitud+'?q='+value.latitud+','+value.longitud+'('+value.nombre+')"><strong class="titulo_tienda">'+value.nombre+"</strong></a><br/>";
				}
				else
				{
					//En Iphone
					//resultado_ajax+='<a href="maps:'+value.latitud+','+value.longitud+'?q='+value.latitud+','+value.longitud+'('+value.nombre+')"><strong class="titulo_tienda">'+value.nombre+"</strong></a><br/>";
				}			
				
				resultado_ajax+='</div>';
			}
			listado_tiendas.push(value);			
			indice++;
		});
		//added		
		localStorage.setItem('lista-tiendas',resultado_ajax);
		$('#lista-tiendas').html(resultado_ajax);
		$('.btn_text').toggle();
		$('.tienda_result.secun').toggle();
		
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) 
	{		
		/*
		swal({
			title: "Error",
			text: 'Error de conexión.',
			html: true,
			type: "error"
		});		
		*/
	}	
	});
}
function obtener_promo_home()
{
	$.ajax({
	type:'GET',
	timeout: 3000,
	dataType: 'json',	
	url:'https://www.appfornes.es/servicios-web/contenidos_seccion.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&id_seccion=3&idioma='+idioma,
	
	success:function(res, textStatus, XMLHttpRequest)
	{			
		//added		
		var resultado ='';				
		$.each(res, function(key, value) 
		{						
			resultado+='<img src="https://www.appfornes.es/Imagenes/'+value.Icono+'" alt="image"  draggable="false"/>';			
		});
		localStorage.setItem('banner_portada',resultado);
		$('#banner_portada').html(resultado);
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) 
	{	
		$('#banner_portada').html(localStorage.getItem('banner_portada'));		
	}	
	});
}
function obtener_promo_folletos()
{
	var mySwiper = new Swiper ('.swiper-container', {
		// Optional parameters
		direction: 'horizontal',
		zoom: true,
		loop: false,
		observer: true,
		observeParents: true,

		// If we need pagination
		pagination: {
		el: '.swiper-pagination',
		},

		// Navigation arrows
		navigation: {
		nextEl: '.swiper-button-next-to',
		prevEl: '.swiper-button-prev-to',
		},

		// And if we need scrollbar
		scrollbar: {
		el: '.swiper-scrollbar',
		},
	})
	$.ajax({
	type:'GET',
	timeout: 3000,
	dataType: 'json',	
	url:'https://www.appfornes.es/servicios-web/contenidos_seccion.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&id_seccion=2&idioma='+idioma,
	
	success:function(res, textStatus, XMLHttpRequest)
	{			
		//added
		listado_folletos=res;		
		var resultado ='';		
		var primera = true;
		$.each(res, function(key, value) 
		{
			//resultado+='<div class="item"><a  href="#" onclick="ver_folleto(\'pagina-folletos-vista\','+key+');return false;">';
			resultado+='<div class="item"><a>';
			resultado+='<img src="https://www.appfornes.es/Imagenes/'+value.Icono+'" alt="image"  draggable="false"/>';
			resultado+='</a></div>';
		});
		localStorage.setItem('folletos-listado',resultado);
		$('#folletos-listado').html(resultado);
		if (resultado=='') 
		{
			$('#folletos-listado').hide();
			$('#slider-folletos-h3').css('padding-top', '20vw');
		}
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) 
	{	
		$('#folletos-listado').html(localStorage.getItem('folletos-listado'));		
	}	
	});
}
function obtener_folletos(lang)
{
	idioma = lang;
	$.ajax({
	type:'GET',
	timeout: 3000,
	dataType: 'json',	
	url:'https://www.appfornes.es/servicios-web/folletos_activos.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&idioma='+idioma,
	
	success:function(res, textStatus, XMLHttpRequest)
	{			
		//added
		listado_folletos=res;				
		var resultado_slider='';
		var primera = true;
		var indice = 0;
		$.each(res, function(key, value) 
		{
			console.log('Portada', res);
			console.log('Portada', value.Portada);
			resultado_slider+='<div class="item center_text"><a  href="#" onclick="folleto_activo = '+indice+';cambiar_pagina(\'\',\'pagina-folletos-vista\');return false;">';
			resultado_slider+='<img src="https://www.appfornes.es/Imagenes/'+value.Portada+'" alt="image"  draggable="false"/>';
            resultado_slider+='<h5>'+value.Nombre+'</h5>';
            resultado_slider+='<p>'+value.Breve+'</p>';
            resultado_slider+='</a></div>';		
			var array_paginas = []
			$.each(value.Paginas, function(key2, value2)
			{
				var tmp = [];
				tmp['Titulo'] = value2.Titulo;
				tmp['Imagen'] = value2.Imagen;				
				array_paginas.push(tmp);				
			});
			paginas_folletos.push(array_paginas);
			var array_descargas = [];
			$.each(value.Descargas, function(key2,value2)
			{
				var tmp =[];
				tmp['Titulo'] = value2.Titulo;
				if (value2.Descarga!='') tmp['Descarga'] = 'https://www.supermasymas.com/Recursos/'+value2.Descarga;
				else tmp['Descarga'] = '';
				array_descargas.push(tmp);
			});
			paginas_descargas.push(array_descargas);
			indice++;
		});
		console.log(paginas_folletos);
		localStorage.setItem('slider-folletos-sinslider',resultado_slider);
		$('#slider-folletos-sinslider').html(resultado_slider);		
		numero_folletos = listado_folletos.length;
		console.log('numero_folletosnumero_folletos', numero_folletos);
		if (listado_folletos.length==1)
		{
			//Quitamos el último registro de navegación para que no vuelva al listado
			origen_array.pop();
			origen_subseccion.pop();
			origen_altura.pop();
			//solo un folleto activo
			//cambiar_pagina('pagina-portada','pagina-folletos-vista');
		}
		console.log('EEEEEEEE', indice);

		if (indice==1)
		{
			console.log('Entro1');
			folleto_activo = 0
			cambiar_pagina('','pagina-folletos-vista');
		}
	},
	error: function(XMLHttpRequest, textStatus, errorThrown) 
	{
		$('#folletos-listado').html('<div class="alert alert-danger">'+temp_lang['ErrorConexion']+'</div>');
		/*
		$('#folletos-listado').html(localStorage.getItem('folletos-listado'));		
		$('#slider-folletos-sinslider').html(localStorage.getItem('slider-folletos-sinslider'));		
		$('.slider-nav').slick({
          slidesToShow: 2,
          slidesToScroll: 1,
          asNavFor: '.slider-for',
          dots: false,
          centerMode: true,
          focusOnSelect: true,
          autoplay: false          
        });
		*/
	}	
	});
}
function ir_a_pagina(num_pagina)
{
	var mySwiper = new Swiper ('.swiper-container', {
		// Optional parameters
		direction: 'horizontal',
		zoom: true,
		loop: false,
		observer: true,
		observeParents: true,

		// If we need pagination
		pagination: {
		el: '.swiper-pagination',
		},

		// Navigation arrows
		navigation: {
		nextEl: '.swiper-button-next-to',
		prevEl: '.swiper-button-prev-to',
		},

		// And if we need scrollbar
		scrollbar: {
		el: '.swiper-scrollbar',
		},
	})
	pagina_flipbook = num_pagina+1;
	console.log('333333333',pagina_flipbook);
	cambiar_pagina('','pagina-folletos-vista');
	
	console.log('999909990', mySwiper);
	//$('.swiper-button-next-to').trigger();
	mySwiper[0].slideTo(num_pagina, 1000, null);
}
function cambiar_folleto(folleto_activo)
{
	var mySwiper = new Swiper ('.swiper-container', {
		// Optional parameters
		direction: 'horizontal',
		zoom: true,
		loop: false,
		observer: true,
		observeParents: true,

		// If we need pagination
		pagination: {
		el: '.swiper-pagination',
		},

		// Navigation arrows
		navigation: {
		nextEl: '.swiper-button-next-to',
		prevEl: '.swiper-button-prev-to',
		},

		// And if we need scrollbar
		scrollbar: {
		el: '.swiper-scrollbar',
		},
	})
	var html_result = '';
	var html_result2 = '';
	//obtener_folletos();
	
	
	console.log('cod_cliente', cod_cliente);
	for (var i=0;i<paginas_folletos[folleto_activo].length;i++)
	{		
		html_result+='<div class="swiper-slide"><div class="swiper-zoom-container">';
		html_result+='<img src="https://www.appfornes.es/Imagenes/'+paginas_folletos[folleto_activo][i]['Imagen']+'" alt="image"  draggable="false"/>';
		html_result+='</div></div>';
		//html_result2+='<div class="col-xs-3 item"><img src="https://www.appfornes.es/Imagenes/'+paginas_folletos[folleto_activo][i]['Imagen']+'" alt="image"  draggable="false"/></div>';
		html_result2+='<div class="col-md-3 col-xs-4 item"><img onclick="ir_a_pagina('+i+');return false;" src="https://www.appfornes.es/Imagenes/'+paginas_folletos[folleto_activo][i]['Imagen']+'" alt="image"  draggable="false"/></div>';
	}	
	console.log(paginas_descargas[folleto_activo]);
	if (paginas_descargas[folleto_activo].length>0) $('#descarga_catalogo').attr("onclick","location.href='"+paginas_descargas[folleto_activo][0]['Descarga']+"';");
	$('#folleto_nav_paginas').html(html_result);
	$('#grid_paginas').html(html_result2);
	mySwiper[0].slideTo(0, 1000, null);
	folleto_atras = 1;
	var html_result3 = '';
	html_result3 += '<div class="wrapper" id="flipbook">';
	for (var i=0;i<paginas_folletos[folleto_activo].length;i++)
	{
		if (i==0)
		{
			html_result3 += '<div class="hard portada" style="">';
				html_result3 += '<img src="https://www.appfornes.es/Imagenes/'+paginas_folletos[folleto_activo][i]['Imagen']+'" alt="image"  draggable="false"/>';
			html_result3 += '</div>';
		}
		else
		{
			html_result3 += '<div class="hard" style="">';
				html_result3 += '<img src="https://www.appfornes.es/Imagenes/'+paginas_folletos[folleto_activo][i]['Imagen']+'" alt="image"  draggable="false"/>';
			html_result3 += '</div>';
		}		
	}
	html_result3 += '</div>';
	console.log('444444445',pagina_flipbook);
	$('#plus_ext_box').html(html_result3);
	$('#flipbook').turn({		
		page:pagina_flipbook,
		acceleration: true,
		autoCenter: true,
    	next:true
	});
	$('#flipbook1').turn({		
		page:pagina_flipbook,
		acceleration: true,
		autoCenter: true,
    	next:true
	});
	/*const nicknames = document.querySelectorAll('[page="1"]');
	console.log('nicknames', nicknames);
	$(nicknames[0]).attr('class', 'prueba');*/
}
function ver_listado()
{
   if (listado_tiendas_abierto==1) 
   {	   
	   listado_tiendas_abierto = 0;
	   $('#lista-tiendas').addClass('lista_mapa_fixed');
   }
   else
   {
	   listado_tiendas_abierto=1;
	   $('#lista-tiendas').removeClass('lista_mapa_fixed');
   }
   $('.tienda_result.secun').slideToggle();
   $('.btn_text').toggle();   
}
function ver_folleto(destino, folleto_destino)
{	
    $("html, body").animate({ scrollTop: 0 }, "fast");
    pagina_actual = destino;
    $('.pagina').hide();
    $('.'+destino).fadeIn();

    $('.slider-nav').slick("getSlick").refresh();
    $('.slider-for').slick("getSlick").refresh();
    $('.single-item').slick("getSlick").refresh();
}

function lista_nueva()
{
	$('#nueva_lista_nombre').val('');
    $('.cont_lista_nueva').fadeIn();
}
function nuevo_prod()
{
	$('.cortina-lista').show();
	$('.add_prod').toggle();
	$('#nom_prod_nuevo').focus();
}

var onSuccessSocialSharing = function(result) {
  //alert("Share completed? " + result.completed); // On Android apps mostly return false even while it's true
  //alert("Shared to app: " + result.app); // On Android result.app since plugin version 5.4.0 this is no longer empty. On iOS it's empty when sharing is cancelled (result.completed=false)
};
var onErrorSocialSharing = function(msg) {
  //alert("Sharing failed with message: " + msg);
};
function compartir_lista()
{
	console.log('temp_lang', mi_lista_unidades);
	var texto_lista = temp_lang['Lista']+':\n';
	var primero = true;
	$.each(mi_lista, function(index,value){
		if (primero) primero = false;
		else texto_lista+='\n';
		texto_lista+='['+mi_lista_unidades[index]+' '+temp_lang['uds']+'] '+value;
	});
	var options = {
		message: texto_lista, // not supported on some apps (Facebook, Instagram)
		subject: 'Lista de la compra', // fi. for email		
		chooserTitle: 'Lista de la compra' // Android only, you can override the default share sheet title,
		//appPackageName: 'com.apple.social.facebook' // Android only, you can provide id of the App you want to share with
	};	
	window.plugins.socialsharing.shareWithOptions(options, onSuccessSocialSharing, onErrorSocialSharing);
	/*
	window.plugins.socialsharing.shareViaWhatsApp(texto_lista,null, null, function() {console.log('share ok')}, function(errormsg){console.log(errormsg)});
	*/
}
function borrar_tarjeta_asociada(id_card, cod_cliente)
{
	$.ajax({		
		type:'get',
		timeout: 5000,
		dataType: 'json',		
		url:'https://www.appfornes.es/servicios-web/borrar_tarjeta_asociada.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_card='+id_card+'&id_card2='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('borrar_tarjeta_asociada', res);			
			var resultado_ajax ='';			 
			var resultado_ajax2 = '';
			if (res.validacion=='ok')
			{
				$('.tarjeta_asociada_num_'+id_card).remove();
				swal({
					title: temp_lang["TarjetaDesvinculada"],
					text: temp_lang["TarjetaDesvinculada"],
					html: true
				});	
				setTimeout(function() { 
					location.reload();
				}, 1000);						
			 }
			 else
			 {
				swal({
					title: temp_lang["Error"],
					text: decodeURIComponent(unescape(res.mensaje)),
					html: true
				});
			 }
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			swal({
				title: temp_lang["Error"],
				text: temp_lang["ErrorConexion"],
				html: true,
				type: "error"
			});
		}
	});
}
var asociando_tarjeta = 0
function asociar_tarjeta()
{
	asociando_tarjeta = 1;
	$("#miModal_dni_vincular").modal("show");
}
function volver_perfil()
{
	asociando_tarjeta = 0;
	$("#miModal_dni_vincular").modal("hide");
}
function ya_tengo_tarjeta1(cod_cliente, dni_cliente_origen, nombre_origen, cod_cliente_digital)
{
	var errores = '';
		
	if ($('#dni_vinculacion').val()=='') errores+= temp_lang['error_dni']+'<br/>';	
	if ($('#dni_vinculacion').val()!=''&&!validar_dni($('#dni_vinculacion').val()))
	{		
		errores+=temp_lang['error_formato_DNI'];	
	}
	if (errores=='')
	{
		fidelizado = true;
		var url = 'https://www.appfornes.es/servicios-web/nuevo_registro.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&dni_registro='+$('#dni_vinculacion').val()+'&anticache='+(new Date()).getTime();		
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url: url,
			success:function(res, textStatus, XMLHttpRequest)
			{
				var resultado_ajax ='';	
				console.log('0000000000000000',res);			
				//$.each(res, function(key, value) 
				//{
					if (res.validacion=='ok')
					{
						relacion_tarjetas_hogares = res;						
						//console.log('0000000000000000',res);		
						console.log(res.datos_cliente.nu_mobile);

						if (res.datos_cliente.nu_mobile!=''&&res.tarjeta_recuperable!='') 
						{							
							vincular_tarjeta_asociacion(cod_cliente, dni_cliente_origen, nombre_origen, cod_cliente_digital);
						}
					}
					else
					{
						swal({
							title: temp_lang["Error"],
							text: decodeURIComponent(unescape(res.mensaje)),
							html: true,
							type: "error"
						});
					}
				//});			 
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});		
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
function vincular_tarjeta_asociacion(cod_cliente, dni_cliente_origen, nombre_origen, cod_cliente_digital)
{	
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/comprobacion_tarjeta.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+$('#numero_tarjeta_asociacion').val()+'&dni_cliente='+$('#dni_vinculacion').val()+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			if (res.validacion=='ok')
			{
				console.log('111111111111111111',res);
				respuesta_necesario_chequeo = res;
				cod_cliente_asociacion = res.cod_cliente_normalizado;						
				if (res.error_tarjeta)
				{
					swal({
						title: temp_lang["Error"],
						text: temp_lang["error_tarjeta_mal"],
						html: true,
						type: "error"
					});
				}
				else
				{
					if (res.necesario_chequeo&&!escaneado) chequear1();
					else asociar_tarjeta_consolidado(cod_cliente, dni_cliente_origen, nombre_origen, cod_cliente_digital);
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			swal({
				title: temp_lang["Error"],
				text: temp_lang["ErrorConexion"],
				html: true,
				type: "error"
			});
		}
	});	
}
function asociar_tarjeta_consolidado(cod_cliente, dni_cliente_origen, nombre_origen, cod_cliente_digital)
{
	var parametros = {		
		"key_acceso" : 'c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf',			
		"token_fcm" : $('#token_fcm').val(),
		"cod_cliente" : cod_cliente,
		"cod_cliente_digital" : cod_cliente_digital,
		"id_home" : id_home,
		"nombre_origen" : nombre_origen,
		"dni_cliente" : $('#dni_vinculacion').val(),
		"dni_cliente_origen" : dni_cliente_origen,
		"cod_cliente_asociacion" : cod_cliente_asociacion,		
		"idioma" : idioma,
		"anticache": (new Date()).getTime(),
		"desa": "1"
	}
	console.log('parametros', parametros);	
	$.ajax({
		data: parametros,
		type:'post',
		timeout: 3000,
		dataType: 'json',		
		url:'https://www.appfornes.es/servicios-web/asociar_tarjeta.php',
		success:function(res, textStatus, XMLHttpRequest)
		{	
			console.log('asociar_tarjeta_consolidado11', res);		
			var resultado_ajax ='';			 
			var resultado_ajax2 = '';
			if (res.validacion=='ok')
			{
				swal({
					title: temp_lang["TarjetaVinculada"],
					text: decodeURIComponent(res.mensaje),
					html: true,
				});
				setTimeout(function() { 
					location.reload();
				}, 1000);
					
				
			 }
			 else
			 {
				swal({
					title: temp_lang["Error"],
					text: decodeURIComponent(unescape(res.mensaje)),
					html: true
				});
			 }
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			swal({
				title: temp_lang["Error"],
				text: temp_lang["ErrorConexion"],
				html: true,
				type: "error"
			});
		}
	});	
}
function chequear1()
{
	swal({
		title: temp_lang["Error"],
		text: temp_lang['error_tarjeta_chequeo_especial_web'],
		html: true
	});
	
}

function obtener_tarjetas_asociadas(cod_cliente, idioma)
{
	$.ajax({
		type:'GET',
		timeout: 10000,
		dataType: 'json',
		url:'https://www.appfornes.es/servicios-web/tarjetas_asociadas.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&cod_cliente='+cod_cliente+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
		success:function(res, textStatus, XMLHttpRequest)
		{
			console.log('obtener_tarjetas_asociadas',res);
			if (res.validacion=='ok')
			{
				tarjetas_asociadas = res.tarjetas;				
				var resultado_ajax = '';
				var resultado_ajax2 = '';
				num_tarjetas_asociadas = res.num_tarjetas;
					
			}			
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});
}
function recuperar_password() 
{
	var errores = '';
	
	if ($('#id_nif_rec').val()=='') errores+= 'Necesitamos saber tu DNI/NIF/NIE.<br/>';
	$('#id_nif_rec').val($('#id_nif_rec').val().trim());	
	if ($('#id_nif_rec').val()!=''&&!validar_dni($('#id_nif_rec').val()))
	{
		errores+='El DNI/NIF/NIE tiene un formato incorrecto.';	
	}
	if (errores=='')
	{
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/recuperar_password.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&codigo_usuario='+$('#id_nif_rec').val()+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				console.log('recuperar_password', res);
				$('#mi_modal_enviado').modal('show');
			}
		});
		
	}
	else
	{
		swal({
			title: "Error",
			text: errores,
			html: true,
			type: "error"
		});
	}
}
function recuperar_password_mail() 
{
	var errores = '';
	
	if ($('#id_nif_rec').val()=='') errores+= 'Necesitamos saber tu DNI/NIF/NIE.<br/>';
	$('#id_nif_rec').val($('#id_nif_rec').val().trim());	
	if ($('#id_nif_rec').val()!=''&&!validar_dni($('#id_nif_rec').val()))
	{
		errores+='El DNI/NIF/NIE tiene un formato incorrecto.';	
	}
	if (errores=='')
	{
		console.log('Entro1');
		$.ajax({
			type:'GET',
			timeout: 10000,
			dataType: 'json',
			url:'https://www.appfornes.es/servicios-web/recuperar_password_mail.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&codigo_usuario='+$('#id_nif_rec').val()+'&idioma='+idioma+'&anticache='+(new Date()).getTime(),
			success:function(res, textStatus, XMLHttpRequest)
			{
				console.log('recuperar_password', res);
				$('#mi_modal_enviado_mail').modal('show');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				swal({
					title: temp_lang["Error"],
					text: temp_lang["ErrorConexion"],
					html: true,
					type: "error"
				});
			}
		});
		
	}
	else
	{
		swal({
			title: "Error",
			text: errores,
			html: true,
			type: "error"
		});
	}
}
function cerrar_modals_recuperar()
{
	$('#mi_modal').modal('hide');
	$('#mi_modal_enviado').modal('hide');
}
function ir_a_pagina_sgte()
{
	console.log("next");
	$("#flipbook").turn("next");
}
function ir_a_pagina_ant()
{
	console.log("previous");
	$("#flipbook").turn("previous");
}
function nobackbutton()
{
	window.addEventListener('popstate', function(event) 
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
		
	}, false);
}

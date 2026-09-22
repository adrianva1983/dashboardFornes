      
		        <div class="wrapper contenedor-tarjeta-megacupones">
                    <div class="container-fluid">
                        <div class="row">
                            <div id="mcheque_ahorro" class="cheque_ahorro col-md-4 col-xs-12" style="display: none">
                                <div id="mbloque_centro" class="text_center row bloque_centro recorte1">
                                    <h1 class="chequeAhorro" tkey="MiChequeAhorro">Mi ChequeAhorro</h1>
                                    <p class="tot_cheque"></p>				
                                    <div class="etiq">                    
                                        <p class="tot_desde"></p>
                                    </div>
                                    <!--<p class="codigo_cheque_ahorro"></p>-->
                                </div>
                            </div>
                            <div id="mcheque_ahorro1" class="cheque_ahorro1 col-md-4 col-xs-12" style="display: none">
                                <div id="mbloque_centro1" class="text_center row bloque_centro recorte1">
                                    <h1 class="chequeAhorro" tkey="MiChequeAhorro">Mi ChequeAhorro</h1>
                                    <p class="tot_cheque"></p>				
                                    <div class="etiq">                    
                                        <p class="tot_desde"></p>
                                    </div>
                                    <!--<p class="codigo_cheque_ahorro"></p>-->
                                </div>
                            </div> 
                        </div>                        
                    </div>
                    
                    <!--<div class="grup_cupones container-fluid">
                        <div class="tabler multi">				
                            <div class="col-xs-1"></div>
                                <div class="col-xs-8 cup_name multi">
                                    <h3 tkey="Multicupon">Multicupón</h3>
                                    <p class="valided_multicupon"></p>
                                    <p><span class="cupon_no_disponible_text"></span><span class="numero_multicupon_scan"></span></p>
                                </div>
                            <div id="bloque_activar_multicupon">
                            <div class="col-xs-3 cup_state">					
                            </div>
                            </div>
                        </div>
                    </div>-->
			    </div>
			
    
    <script>
        function obtener_folletos()
        {
            $.ajax({
            type:'GET',
            timeout: 3000,
            dataType: 'json',	
            url:'https://www.appfornes.es/servicios-web/folletos_activos.php?key_acceso=c5zwCl4e1vw5o21da2edvasUVx1Rf&idioma=es',
            
            success:function(res, textStatus, XMLHttpRequest)
            {			
                //added
                listado_folletos=res;				
                var resultado_slider='';
                var paginas_folletos = [];
                var paginas_descargas = [];
                var primera = true;
                var indice = 0;
                $.each(res, function(key, value) 
                {
                    resultado_slider+='<div class="item center_text col-12"><a  href="folleto_detalle.php?folleto_activo='+indice+'">';
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
                if (listado_folletos.length==1)
                {
                    //Quitamos el último registro de navegación para que no vuelva al listado
                    origen_array.pop();
                    origen_subseccion.pop();
                    origen_altura.pop();
                    //solo un folleto activo
                    cambiar_pagina('pagina-portada','pagina-folletos-vista');
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
        function obtener_multicupon()
        {
            $.ajax({
                type:'GET',
                timeout: 20000,
                dataType: 'json',
                url:'https://www.appfornes.es/servicios-web/obtener_multicupon.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['cod_cliente']?>&idioma=<?=$idioma?>&anticache='+(new Date()).getTime(),
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
                            pintar_codigo_qr(generar_data_code(<?=$_SESSION['cod_cliente']?>));						
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
                                        pintar_codigo_qr(generar_data_code(<?=$_SESSION['cod_cliente']?>));									
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
                    pintar_codigo_qr(generar_data_code(<?=$_SESSION['cod_cliente']?>));
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
                    pintar_codigo_qr(generar_data_code(<?=$_SESSION['cod_cliente']?>));			
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
        function obtener_vales1(recargado)
        {
            $.ajax({
                type:'GET',
                timeout: 20000,
                dataType: 'json',
                url:'https://www.appfornes.es/servicios-web/obtener_vales.php?key_acceso=c5zwCl4e1vw5oY609T5GX4rD71UVx1Rf&id_home=<?=$_SESSION['id_home']?>&cod_cliente=<?=$_SESSION['id_card']?>&idioma=<?=$idioma?>&anticache='+(new Date()).getTime(),
                success:function(res, textStatus, XMLHttpRequest)
                {
                    var encontrado_cheque_ahorro = false;			
                    var algun_vale = false;
                    if (res.validacion == 'ok')
                    {
                        cupones_descargados = [];				
                        var i=3;
                        if (recargado) 
                        {
                            $('.grup_cupones .otros_cupones').remove();
                            $('.pagina-multi-cupon .otros-cupones-seccion-cupones .row').remove();
                        }
                        localStorage.setItem('array_vales',JSON.stringify(res.vales));
                        $.each(res.vales, function(key, value) 
                        {
                            console.log('0000000000000000000001', value);
                            cupones_descargados.push(value.cod_vale);
                            if (value.es_cheque_ahorro_principal==1)
                            {
                                $('.cheque_ahorro').show();
                                $('.cheque_ahorro .bloque_centro').show();
                                if (resaltar == value.cod_vale) $('.cheque_ahorro .bloque_centro').addClass('resaltar_cupon1');
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
                            else if (value.es_cheque_ahorro==1)
                            {
                                $('.cheque_ahorro1').show();
                                $('.cheque_ahorro1 .bloque_centro').show();
                                if (resaltar == value.cod_vale) $('.cheque_ahorro1 .bloque_centro').addClass('resaltar_cupon1');
                                encontrado_cheque_ahorro = true;
                                $('.js-switch_1').val(value.cod_vale);
                                $('.cheque_ahorro1 .tot_cheque').html(value.importe_vale+'<span>€</span>');
                                $('.cheque_ahorro1 .codigo_cheque_ahorro').html(value.cod_vale);
                                $('.cheque_ahorro1 .tot_desde').html('<i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido);
                                if (value.proximo_vencimiento==1) 
                                {
                                    cheque_ahorro_cerca_caducar = true;
                                    $('.cheque_ahorro1 .bloque_centro').addClass('va_a_caducar_bloque');
                                }
                                else
                                {
                                    cheque_ahorro_cerca_caducar = false;
                                    $('.cheque_ahorro1 .bloque_centro').removeClass('va_a_caducar_bloque');
                                }
                            }
                            else
                            {
                                algun_vale = true;					
                                var html_result = '';
                                html_result+='<div class="row tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1>'+value.tipo+'</h1></div>';
                                html_result+='<div class="col-xs-6"><p class="otros_cupones_valido';
                                if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
                                html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
                                html_result+='<div class="col-xs-3 cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
                                html_result+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
                                html_result+='<p>'+value.texto+'</p><p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';						
                                html_result+='</div>';	
                                //$('.grup_cupones').append(html_result);					
                                if (value.es_cheque_ahorro!=1)
                                {
                                    var html_result2 = '';								
                                    html_result2+='<div class="row tabler_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1>'+value.tipo+'</h1></div>';						
                                    html_result2+='<div class="col-xs-9"><p class="otros_cupones_valido';
                                    if (value.proximo_vencimiento==1) html_result2+=' va_a_caducar';											
                                    html_result2+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';						
                                    //html_result2+='<div class="col-xs-3 cup_state contenedor_switchery2_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_seccion_cupones_'+value.cod_vale+' btn_activar"/></div>';						
                                    html_result2+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';				
                                    html_result2+='<p>'+value.texto+'</p>';
                                    //html_result2+='<p class="otros_cupones_codigo">'+value.cod_vale+'</p>';
                                    html_result2+='</div>';												
                                    html_result2+='</div>';						
                                    //$('.pagina-multi-cupon .otros-cupones-seccion-cupones').append(html_result2);
                                }
                                //$('.pagina-multi-cupon .otros-cupones-seccion-cupones').show();
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
                                //var tmp_elem = document.querySelector('.js-switch_'+value.cod_vale);					
                                //elems[value.cod_vale] = tmp_elem;
                                //switchery[value.cod_vale] = new Switchery(elems[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
                                //var tmp_elem2 = document.querySelector('.js-switch_seccion_cupones_'+value.cod_vale);
                                //elems2[value.cod_vale] = tmp_elem2;
                                //switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
                                /*elems[value.cod_vale].onchange = function()
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
                                    
                                };*/
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
                            console.log('corecore');
                            var html_result = '';					
                            $('.grup_cupones').append(html_result);
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
                            console.log('011111');
                            cupones_descargados.push(value.cod_vale);
                            if (value.es_cheque_ahorro_principal==1)
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
                                console.log('02222222');
                                algun_vale = true;					
                                var html_result = '';
                                html_result+='<div class="row tabler_cupones otros_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1>'+value.tipo+'</h1></div>';
                                html_result+='<div class="col-xs-6"><p class="otros_cupones_valido';
                                if (value.proximo_vencimiento==1) html_result+=' va_a_caducar';
                                html_result+='"><i class="fa fa-calendar"></i> '+temp_lang['cheque_canjeable']+' '+value.valido+'</p></div>';
                                html_result+='<div class="col-xs-3 cup_state contenedor_switchery_'+value.cod_vale+'"><input value="'+value.cod_vale+'" type="checkbox" class="js-switch_'+value.cod_vale+' btn_activar"/></div>';
                                html_result+='<div class="col-xs-9 cup_name"><h3>'+value.titulo+'</h3>';
                                html_result+='<p>'+value.texto+'</p><p class="otros_cupones_codigo">'+value.cod_vale+'</p></div>';						
                                html_result+='</div>';	
                                $('.pagina-tarjeta .grup_cupones').append(html_result);					
                                if (value.es_cheque_ahorro!=1)
                                {
                                    var html_result2 = '';								
                                    html_result2+='<div class="row tabler_cupones cupon_'+value.cod_vale+'"><div class="col-xs-3 cup_qty"><h1>'+value.tipo+'</h1></div>';						
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
                                //var tmp_elem = document.querySelector('.js-switch_'+value.cod_vale);					
                                //elems[value.cod_vale] = tmp_elem;
                                //switchery[value.cod_vale] = new Switchery(elems[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
                                //var tmp_elem2 = document.querySelector('.js-switch_seccion_cupones_'+value.cod_vale);
                                //elems2[value.cod_vale] = tmp_elem2;
                                //switchery2[value.cod_vale] = new Switchery(elems2[value.cod_vale], { color: '#FFCA00', secondaryColor    : '#cccccc', });
                                /*elems[value.cod_vale].onchange = function()
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
                                    
                                };*/						
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
	
        
    </script>	

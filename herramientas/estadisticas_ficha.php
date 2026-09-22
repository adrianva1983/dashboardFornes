
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php print $lang['estadisticas_visibilidad_ficha'];?></h5>
                            </div>
                            <div class="ibox-content">
								<div class="row">
                                <div class="col-lg-12">
									<div class="col-lg-3">
										<div class="widget style1 gray-bg visualizaciones">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-rss fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['total_visualizaciones'];?> </span>
													<h2 class="font-bold" id="total_visualizaciones">0</h2>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="widget style1 red-bg detalle">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-flash fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['total_detalle'];?> </span>
													<h2 class="font-bold" id="total_detalle">0</h2>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="widget style1 blue-bg detalle" data-container="body" data-toggle="popover" data-placement="top" data-content="<?php print $lang['mejora_ranking'];?>">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-trophy fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['ranking_general'];?> </span>
													<h2 class="font-bold" id="ranking_general">0</h2>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="widget style1 blue-bg detalle" data-container="body" data-toggle="popover" data-placement="top" data-content="<?php print $lang['mejora_ranking'];?>">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-trophy fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['ranking_provincia'];?> </span>
													<h2 class="font-bold" id="ranking_provincia">0</h2>
												</div>
											</div>
										</div>
									</div>
                                </div>
								<div class="col-lg-12">
									<div class="col-lg-6">
										<div class="widget style1 navy-bg detalle" data-container="body" data-toggle="popover" data-placement="top" data-content="<?php print $lang['explicacion_recomendado_estilo'];?>">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-tags fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['total_recomendado_estilo'];?> </span>
													<h2 class="font-bold" id="total_recomendado_estilo">0</h2>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="widget style1 yellow-bg detalle" data-container="body" data-toggle="popover" data-placement="top" data-content="<?php print $lang['explicacion_recomendado_gusto'];?>">
											<div class="row">
												<div class="col-xs-4">
													<i class="fa fa-heart fa-5x"></i>
												</div>
												<div class="col-xs-8 text-right">
													<span> <?php print $lang['total_recomendado_gusto'];?> </span>
													<h2 class="font-bold" id="total_recomendado_estilo">0</h2>
												</div>
											</div>
										</div>
									</div>
								</div>

								</div>
                                <div class="row">								
                                <div class="col-lg-12">
							    <div class="ibox-content">
									<?php print "<p>".$lang['explicacion_estadisticas_visibilidad_ficha']."</p>";?>
									<div>
										<canvas id="barChart" height="140"></canvas>
									</div>
								</div>

                                </div>
                                </div>
                            </div>
						</div>
<script>							
 $(document).ready(function() {            
<?php
	$fecha_actual = date("Y-m-d H:i:s");
	$requete = "SELECT * FROM `Contenidos` WHERE `IdPropietario`=".$_SESSION['usuario_id'];	
	$result = mysql_query($requete,$db);
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);
		$id_ficha = $listado->Id;
	}
	$mes = intval(date('m'));
	$ano =intval(date('Y'));
	if ($mes==12)
	{
		$mes = 1;
		$ano = $ano + 1;
	}
	else
	{
		$mes = $mes + 1;
		$ano = $ano;
	}
	$data2='];';
	$data3='];';
	$labels='];';
	$meses = array($lang['ene'],$lang['feb'],$lang['mar'],$lang['abr'],$lang['may'],$lang['jun'],$lang['jul'],$lang['ago'],$lang['sep'],$lang['oct'],$lang['nov'],$lang['dic']);
	$total_visualizaciones = 0;
	$total_detalle = 0;
	for ($i=0;$i<12;$i++)
	{
		if ($mes==1)
		{
			$ano_anterior = $ano-1;
			$mes_anterior = 12;
		}
		else 
		{
			$ano_anterior = $ano;
			$mes_anterior = $mes-1;
		}		
		$fecha_inferior = $ano_anterior.'-'.$mes_anterior.'-00';		
		$fecha_superior = $ano.'-'.$mes.'-00';
		
		$requete = "SELECT * FROM `EstadisticasContenidos` WHERE `IdContenido`=".$id_ficha." AND ((`FechaInicio`>='".$fecha_inferior."' AND `FechaFin`<='".$fecha_superior."') OR (`FechaInicio`='0000-00-00' AND `FechaFin`='".$fecha_superior."'))";					
		$result = mysql_query($requete,$db);
		$labels = "'".$meses[$mes_anterior-1].' '.$ano."'".$labels;
		if (($result) && (mysql_num_rows($result)>0))
		{
			$listado = mysql_fetch_object($result);
			if ($listado->Listados>$max1) $max1 = $listado->Listados;
			$total_visualizaciones += $listado->Listados;
			$total_detalle += $listado->Detalle;
			$data2= $listado->Listados.$data2;
			$data3= $listado->Detalle.$data3;
		}
		else 
		{
			$data2= '0'.$data2;
			$data3= '0'.$data3;
		}
		if ($i<11)
		{
			$data2=','.$data2;
			$data3=','.$data3;
			$labels=','.$labels;
		}
		$mes = $mes_anterior;
		$ano = $ano_anterior;
	}
	print "$('#total_visualizaciones').html('".$total_visualizaciones."');";
	print "$('#total_detalle').html('".$total_detalle."');";
	$data2 = 'var data2 = ['.$data2;
	$data3 = 'var data3 = ['.$data3;
	$labels = 'var etiquetas = ['.$labels;
	print $data2;
	print $data3;	
	print $labels;	
	//SACAMOS LA ESTADÍSTICA DE RECOMENDACIONES POR GUSTOS Y ESTILOS
	$requete = "SELECT * FROM `EstadisticasConzerto` WHERE `IdContenido`=".$id_ficha." AND `Campo`='recomendado-por-estilo'";
	$result = mysql_query($requete,$db);	
	$recomendado_por_estilo = 0;
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);
		$recomendado_por_estilo = $listado->Valor;
	}
	$requete = "SELECT * FROM `EstadisticasConzerto` WHERE `IdContenido`=".$id_ficha." AND `Campo`='recomendado-por-gusto'";
	$result = mysql_query($requete,$db);	
	$recomendado_por_gusto = 0;
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);
		$recomendado_por_gusto = $listado->Valor;
	}
	print "$('#total_recomendado_estilo').html('".$recomendado_por_estilo."');";
	print "$('#total_recomendado_gusto').html('".$total_recomendado_gusto."');";
	
	//CALCULAMOS RANKINGS
	//Miramos coeficiente
	$requete = "SELECT * FROM `Publicaciones` WHERE `IdContenido`=".$id_ficha;	
	$result = mysql_query($requete,$db);	
	$coeficiente = 0;
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);
		$coeficiente = $listado->Coeficiente;		
	}	
	$requete = "SELECT COUNT(*) AS posicion FROM `Contenidos`,`Publicaciones` WHERE `Contenidos`.Id=`Publicaciones`.IdContenido AND `Publicaciones`.Coeficiente >= ".$coeficiente;	
	$result = mysql_query($requete,$db);	
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);	
		$posicion = $listado->posicion+1;
		print "$('#ranking_general').html('".$posicion."');";
	}
	$requete = "SELECT * FROM `CamposAdicionales` WHERE `IdContenido`=".$id_ficha." AND `TituloCampo`='Provincia'";
	$result = mysql_query($requete,$db);	
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);	
		$provicia_estudio = $listado->Valor;
	}
	$requete = "SELECT COUNT(*) AS posicion FROM `Contenidos`,`Publicaciones`,`CamposAdicionales` WHERE `Contenidos`.Id=`Publicaciones`.IdContenido AND `CamposAdicionales`.IdContenido=`Contenidos`.Id AND `CamposAdicionales`.TituloCampo='Provincia' AND `CamposAdicionales`.Valor='".$provicia_estudio."' AND `Publicaciones`.Coeficiente >= ".$coeficiente;
	$result = mysql_query($requete,$db);	
	if (($result) && (mysql_num_rows($result)>0))
	{
		$listado = mysql_fetch_object($result);
		$posicion = $listado->posicion+1;
		print "$('#ranking_provincia').html('".$posicion."');";
	}
	//FIN CALCULO RANKINGS
?>

    var barData = {
        labels: etiquetas,
        datasets: [
            {
                label: "<?php print $lang['visualizacion'];?>",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: data2
            },
            {
                label: "<?php print $lang['detalle'];?>",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: data3
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
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        responsive: true,
    }
	var ctx = document.getElementById("barChart").getContext("2d");
	var myNewChart = new Chart(ctx).Bar(barData, barOptions);
 });
 </script>
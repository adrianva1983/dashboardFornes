
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php print $lang['estadisticas_visibilidad_web'];?></h5>
                            </div>
                            <div class="ibox-content">
                                <div class="row">								
                                <div class="col-lg-12">
							    <div class="ibox-content">
									<?php print "<p>".$lang['explicacion_estadisticas_visibilidad_web']."</p>";?>
									<div>
										<canvas id="lineChart" height="140"></canvas>
									</div>
								</div>

                                </div>
                                <div class="col-lg-12">
									<div class="widget style1 lazur-bg visualizaciones">
										<div class="row">
											<div class="col-xs-4">
												<i class="fa fa-eye fa-5x"></i>
											</div>
											<div class="col-xs-8 text-right">
												<span> <?php print $lang['total_valor'];?> </span>
												<h2 class="font-bold" id="total_valor_web">0</h2>
											</div>
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
	$data4='];';	
	$labels='];';
	$meses = array($lang['ene'],$lang['feb'],$lang['mar'],$lang['abr'],$lang['may'],$lang['jun'],$lang['jul'],$lang['ago'],$lang['sep'],$lang['oct'],$lang['nov'],$lang['dic']);	
	$total_valor = 0;	
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
		$fecha_inferior = $ano_anterior.'-'.$mes_anterior.'-01';		
		$fecha_superior = $ano.'-'.$mes.'-01';
		
		$requete = "SELECT SUM(`Valor`) AS Total FROM `EstadisticasSecciones` WHERE `IdSeccion`=".$id_seccion_principal." AND ((`Fecha`>='".$fecha_inferior."' AND `Fecha`<'".$fecha_superior."') OR (`Fecha`='0000-00-00' AND `Fecha`='".$fecha_superior."'))";					
		$result = mysql_query($requete,$db);
		$labels = "'".$meses[$mes_anterior-1].' '.$ano."'".$labels;
		if (($result) && (mysql_num_rows($result)>0))
		{
			$listado = mysql_fetch_object($result);
			if ($listado->Total!='') 
			{
				$total_valor += $listado->Total;
				$data4= $listado->Total.$data4;
			}
			else $data4= '0'.$data4;			
		}
		else 
		{
			$data4= '0'.$data4;			
		}
		if ($i<11)
		{
			$data4=','.$data4;			
			$labels=','.$labels;
		}
		$mes = $mes_anterior;
		$ano = $ano_anterior;
	}
	print "$('#total_valor_web').html('".$total_valor."');";
	$data4 = 'var data4 = ['.$data4;
	$labels = 'var etiquetas = ['.$labels;
	print $data4;	
	print $labels;	
?>

    var lineData = {
        labels: etiquetas,
        datasets: [
            {
                label: "<?php print $lang['visualizacion'];?>",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.7)",
                pointColor: "rgba(26,179,148,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(26,179,148,1)",
                data: data4
			}
        ]
    };

    var lineOptions = {
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        bezierCurve: true,
        bezierCurveTension: 0,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true,
	}
	var ctx = document.getElementById("lineChart").getContext("2d");
	var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
 });
 </script>
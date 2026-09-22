            <ul class="nav navbar-top-links navbar-left">
                <li>
					<a href="/dashboard/perfil.php">
                    <span class="m-r-sm text-muted welcome-message" ><span tkey="saludo">Hola </span>
					<?php
					$requete = "SELECT * FROM `ClientesTarjetas` WHERE `id_card`='".$_SESSION['id_card']."'";
					$result = mysqli_query($db,$requete);			
					if (($result) && (mysqli_num_rows($result)>0))
					{
						$listado = mysqli_fetch_object($result);
						print utf8_encode($listado->na_name);
					}
					?>
					</span>
					</a>
                </li>				
				<li class="notificaciones">
					<a href="/dashboard/notificaciones.php">
					<img src="/dashboard/img/svg/portada/notif.svg"/>
					<p class="notif"></p>
					</a>
				</li>
			</ul>
			<ul class="nav navbar-top-links navbar-right">                
			<?php				
				if ($_SESSION['token_oauth']!='')
				{
				?>
				<li>
					<?php					
					$idioma = $_SESSION['select_idioma'];
					if ($idioma == 'es')
					{
						print '<a href="https://tienda.masymas.com/es">';
					}
					else if ($idioma == 'en')
					{
						print '<a href="https://tienda.masymas.com/es">';
					}
					else if ($idioma == 'va')
					{
						print '<a href="https://tienda.masymas.com/vl">';
					}
					
					
					?>
                    <span class="m-r-sm text-muted welcome-message" ><img class="menu_izquierda" src="img/svg/shopping_cart.svg" alt="Tienda online"> Volver a tienda online</span>
					<span class="m-r-sm text-muted welcome-message-movil" ><img class="menu_izquierda" src="img/svg/shopping_cart.svg" alt="Tienda online"> Volver</span>
				 </a>
				</li>
				<?php
				}				
				?>
                <li>
                    <a href="?desconectar=si" tkey="Desconectar">
                        <i class="fa fa-power-off"></i> Desconectar
                    </a>
					<?php //print_r($_SESSION); ?>
                </li>
			</ul>

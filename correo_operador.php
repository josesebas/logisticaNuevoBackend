<?php 
class Correo_Operador {
		public static function getCorreo($operador, $cliente, $viaje){
		
		$correo =  '
			<!DOCTYPE html>
			<html lang="es-ES">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta property="og:locale" content"es_ES" />
			<meta http-equiv=Content-Type content="text/html; charset=unicode">
			<style type="text/css">
				  @media only screen and (max-width: 600px){
				    .politica{font-size:30px !important;}
				    .title_politica{font-size:30px !important;}
				    table.info {   width:100%;}
			       tr.info{   font-weight:bold;
			           display: block; }
			       td.info {   display: block;
			           text-align:justify;}
			       th.info {   display: block;
			           text-align:center!important;
			       }
			        img.icono{
			        	width:50px!important;	}
				  .numeros{ width:100%!important;}
		    	  .envianos{font-size:43px!important;}
				  .envianos1{font-size:41px!important;}
				  .envianos2{font-size:35px!important;}
				  .amarillo{padding-top: 60px!important;}
				  .amarillo2{padding-bottom:60px!important;}
				  .envianos3{font-size:28px!important;}
				  .envianos4{font-size:23px!important;}
				  .totales{ padding-left: 10px!important;}
				  .totales2{ padding-right: 10px!important;}		  
				  .gral{ width: 600px!important; height:600px!important;padding:100px!important; padding-top:100px!important; padding-bottom:100px!important;}
				  .img{width:100%!important; }
				}
				 @media only screen and (max-width: 500px){
				    .politica{font-size:25px !important;}
				    .title_politica{font-size:25px !important;}
				    table.info {   width:100%;}
			       tr.info{   font-weight:bold;
			           display: block; }
			       td.info {   display: block;
			           text-align:justify;}
			       th.info {   display: block;
			           text-align:center!important;
			       }
			        img.icono{
			        	width:50px!important;	}
				  .numeros{ width:100%!important;}
		    	  .envianos{font-size:38px!important;}
				  .envianos1{font-size:34px!important;}
				  .envianos2{font-size:28px!important;}
				  .amarillo{padding-top: 60px!important;}
				  .amarillo2{padding-bottom:60px!important;}
				  .envianos3{font-size:22px!important;}
				  .envianos4{font-size:18px!important;}
				  .totales{ padding-left: 10px!important;}
				  .totales2{ padding-right: 10px!important;}		  
				  gral{ width: 600px!important; hight:600px!important;padding:100px!important; padding-top:100px!important; padding-bottom:100px!important;}
				  .img{width:100%!important; }
				}
			</style>
		</head>
		<body style="margin: 0; padding: 0; font-family: sans-serif;">
		<table style="border-collapse:collapse!important; font-family:Helvetica; letter-spacing:normal; orphans:auto; text-indent:0px; text-transform:none; widows:auto; word-spacing:0px" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<table style="border-collapse:collapse!important; width:600px!important;" width="600px" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td>
							<table style="border-collapse:collapse!important; width:600px!important;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
								<tr style="background: #000; ">
									<td style="padding: 10px;">
										<table>
											<tr>
												<td style="width: 18%;">
													<img src="https://gdlvan.com.mx/logistica/logistica-backend/uploads/gdlvan/logo_gdlvan.png" alt="" style="width: 100%;">
												</td>
												<td>
													<h3 style="color: #fff; margin: 0; font-size: 25px; text-align: right; font-weight:normal" class="envianos">'.strtoupper($operador['nombre']).' tienes un viaje asignado</h3>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<img src="https://gdlvan.com.mx/promociones_back/images/correo_operador.jpg" alt="" style="width: 100%; height: 270px; object-fit: cover;">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table style="border-collapse:collapse!important; width:600px;" width="600px" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td>
							<p style="padding: 15px; font-family: Arial; font-size: 18px; margin: 0; color: #000;" class="envianos2">Hola '.strtoupper($operador['nombre']).' '.strtoupper($operador['apellidos']).', se te asigno un viaje, recuerda que nuestro objetivo es que nuestros pasajeros tengan una <b>EXPERIENCIA INCREIBLE</b>.
							</p>
						</td>
					</tr>
					
				</table>
				<table style="border-collapse:collapse!important; width:600px;" width="600px" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td style="text-align: left; padding-left:10px;">
							<h2 style="color: #fff; text-align: center; margin: 0; font-size: 30px; background: #000; margin: 15px 0; padding: 10px; font-weight: normal;" class="envianos"><span style="color: #F6D03D;">I</span>nformaci&oacute;n de tu <span style="color: #F6D03D	;">V</span>iaje</h2>
						</td>
					</tr>
					<tr>
						<td>
							<p>
								<b>Numero de contrato:</b> '.$viaje['num_contrato'].'<br>
								<b>Unidad:</b>'.$viaje['tipo_unidad'].' '.$viaje['num_economico'].'<br>
								<b>Pasajeros:</b>'.$viaje['pasajeros'].'<br>
								<b>Origen:</b>'.$viaje['salida_exacta'].'<br>
								<b>Destino:</b>'.$viaje['destino_exacto'].'<br>
								<b>Fechas:</b>Del '.$viaje['fecha1'].' al '.$viaje['fecha2'].'<br>
								<b>Tipo de viaje:</b>'.$viaje['tipo_viaje'].'<br>
							</p>
						</td>	
					</tr>
				</table>

				<table style="border-collapse: collapse; width: 600px!important; margin-bottom: 0px; margin-top:10px;" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td style="width: 100%;">
							<h4 class="envianos1" style="padding: 10px 0; background: #000; color: #fff; text-align: center; margin-bottom: 0px; font-family: Sans-Serif; margin-top: 0; font-size: 30px; font-weight: normal;"><span style="color: #F6D03D;">I</span>nformaci&oacute;n importante para tu <span style="color: #F6D03D;">V</span>iaje</h4>
						</td>
					</tr>
				</table>

				<table style="border-collapse: collapse!important; width: 600px!important;  margin-bottom: 15px; background:#F6D03D;" cellspacing="0" cellpadding="1" align="center">
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr >
							         <th width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img src="https://gdlvan.com.mx/ventas/images/iconos/abordarN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td>
							         	<p style="font-size: 17px; margin: 0; font-weight:bold"  class="title_politica">INICIO DEL VIAJE:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
										<p style="width: 100%; font-size: 15px; margin: 0; font-weight:normal" class="politica"> Es indispensable que el costo total de su viaje est&eacute; cubierto al 100% 24 horas antes de iniciar el mismo.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr>
							         <th width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img src="https://gdlvan.com.mx/ventas/images/iconos/incluirN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
							         	<p style="font-size: 17px; margin: 0; font-weight:bold"  class="title_politica">LO QUE INCLUYE SU VIAJE:</p>
							     	</td>
							    </tr>
							    <tr>
							     	<td colspan="2" align="justify">
										<p style="font-size: 15px; margin: 0; font-weight:normal" class="politica"> Todos nuestros servicios incluyen el servicio de Conductor, Combustible, Peajes, Vi&aacute;ticos de Conductor y Seguro de la Unidad y los pasajeros a bordo de la misma.
											 Los Movimientos que se especifiquen en su contrato.
										</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table>
							    <tr>
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img  src="https://gdlvan.com.mx/ventas/images/iconos/facturaN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
										<p style="font-size: 17px; margin: 0px; font-weight:bold" class="title_politica">FACTURACI&Oacute;N:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
										<p style="width: 100%; font-size: 15px; margin: 0; font-weight:normal" class="politica"> Es importante que al momento de realizar su pago especifique si necesita factura ya que est&aacute; le llegara por correo electr&oacute;nico de manera automatizada con la informaci&oacute;n que usted nos proporcione.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table>
							    <tr>
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img  src="https://gdlvan.com.mx/ventas/images/iconos/sobrecupoN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
										<p style="font-size: 17px; margin: 0; font-weight:bold" class="title_politica">CAPACIDAD DE LA UNIDAD:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
										<p style="width: 100%; font-size: 15px; margin: 0; font-weight:normal;" class="politica"> En su Cotizaci&oacute;n y Contrato se espec&iacute;fica la capacidad de la unidad que usted est&aacute; contratando. S&oacute;lo podr&aacute;n viajara el n&uacute;mero de personas que su contrato establece por cuestiones de seguridad, legales y de seguros.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr >
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr>
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img  src="https://gdlvan.com.mx/ventas/images/iconos/caminoN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
										<p style="font-size: 17px; margin: 0; font-weight:bold" class="title_politica">CAMINOS EN MAL ESTADO O EN RIESGO:</p>
									</td>
								</tr>
								<tr>
									<td  align="justify" colspan="2">
										<p style="width: 100%; font-size: 15px; margin: 0px; font-weight:normal;" class="politica"> Las unidades no recorrer&aacute;n caminos de terracer&iacute;a ni caminos en mal estado o con riesgo de inseguridad. El operador puede en todo momento detener el servicio si consider&aacute; que la unidad y/o los pasajero se encuentran en riesgo.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr >
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img  src="https://gdlvan.com.mx/ventas/images/iconos/perdidosN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
										<p style="font-size: 17px; margin: 0; font-weight:bold;" class="title_politica">USTED ES RESPONSABLE DE SU EQUIPAJE:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
									<p style="font-size: 15px; margin: 0; margin: 0px; font-weight:normal;" class="politica"> La empresa no se hace responsable por el equipaje de los pasajeros as&iacute; como objetos que sean olividados en la unidad, (por favor revise que no queden objetos en la unidad antes de que termine su servicio).</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr >
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img src="https://gdlvan.com.mx/ventas/images/iconos/accidentN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td  >
										<p style="font-size:17px; margin: 0; font-weight:bold" class="title_politica">NO DA&Ntilde;AR LA UNIDAD:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
										<p style="font-size: 15px; margin: 0; margin: 0px; font-weight:normal; width: 100%"  class="politica">La unidad est&aacute; a su servicio, en caso de que los pasajeros realicen alg&uacute;n da&ntilde;o a la unidad se generar&aacute;n cargos que puede revisar en nuestros terminos y condiciones.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:10px; padding-right:10px; padding-top:10px;padding-bottom:10px;">
							<table >
							    <tr >
							         <th  width="10%">
							         	<span style="color: #F6D03D; margin-right: 10px; text-align: center; padding:5px;"><img src="https://gdlvan.com.mx/ventas/images/iconos/toxicoN.png" style="width: 35px;" class="icono"></span>
							         </th>
							         <td >
										<p style="font-size: 17px; margin: 0; font-weight:bold" class="title_politica">PRODUCTOS QUE NO SE PUEDEN TRANSPORTAR:</p>
									</td>
								</tr>
								<tr>
									<td align="justify" colspan="2">
										<p style="font-size: 15px; margin: 0; font-weight:normal; width: 100%"  class="politica"> Por disposiciones oficiales queda estrictamente prohibido transportar, Armas de Fuego, cualquier tipo de drogas o estupefacientes, as&iacute; como materiales t&oacute;xicos e inflamables.</p>
							         </td>
							    </tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
	';
		return $correo;
		}
	}
 ?>